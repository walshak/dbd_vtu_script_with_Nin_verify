<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\Configuration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ApiMonitoringService
{
    /**
     * Log API request and response
     */
    public function logApiCall($service, $endpoint, $request, $response, $responseTime, $status = 'success')
    {
        try {
            ApiLog::create([
                'service' => $service,
                'endpoint' => $endpoint,
                'request_data' => is_array($request) ? json_encode($request) : $request,
                'response_data' => is_array($response) ? json_encode($response) : $response,
                'response_time' => $responseTime,
                'status' => $status,
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API call: ' . $e->getMessage());
        }
    }

    /**
     * Check API health status
     */
    public function checkApiHealth($service, $endpoint)
    {
        $cacheKey = "api_health_{$service}";
        
        return Cache::remember($cacheKey, 300, function () use ($service, $endpoint) {
            try {
                $startTime = microtime(true);
                
                $response = Http::timeout(10)->get($endpoint . '/health');
                
                $responseTime = (microtime(true) - $startTime) * 1000;
                
                $status = $response->successful() ? 'healthy' : 'unhealthy';
                
                $this->logApiCall($service, $endpoint . '/health', [], $response->body(), $responseTime, $status);
                
                return [
                    'status' => $status,
                    'response_time' => $responseTime,
                    'last_checked' => now()->toISOString()
                ];
                
            } catch (\Exception $e) {
                Log::error("API Health Check Failed for {$service}: " . $e->getMessage());
                
                $this->logApiCall($service, $endpoint, [], $e->getMessage(), 0, 'error');
                
                return [
                    'status' => 'error',
                    'response_time' => 0,
                    'error' => $e->getMessage(),
                    'last_checked' => now()->toISOString()
                ];
            }
        });
    }

    /**
     * Get API performance metrics
     */
    public function getApiMetrics($service, $hours = 24)
    {
        try {
            $since = Carbon::now()->subHours($hours);
            
            $logs = ApiLog::where('service', $service)
                         ->where('created_at', '>=', $since)
                         ->get();

            $totalRequests = $logs->count();
            $successfulRequests = $logs->where('status', 'success')->count();
            $failedRequests = $logs->where('status', '!=', 'success')->count();
            
            $avgResponseTime = $logs->where('response_time', '>', 0)->avg('response_time');
            $maxResponseTime = $logs->max('response_time');
            $minResponseTime = $logs->where('response_time', '>', 0)->min('response_time');
            
            $successRate = $totalRequests > 0 ? ($successfulRequests / $totalRequests) * 100 : 0;
            
            // Calculate hourly breakdown
            $hourlyBreakdown = [];
            for ($i = 0; $i < $hours; $i++) {
                $hourStart = Carbon::now()->subHours($i + 1);
                $hourEnd = Carbon::now()->subHours($i);
                
                $hourLogs = $logs->whereBetween('created_at', [$hourStart, $hourEnd]);
                
                $hourlyBreakdown[] = [
                    'hour' => $hourStart->format('H:00'),
                    'requests' => $hourLogs->count(),
                    'success' => $hourLogs->where('status', 'success')->count(),
                    'failed' => $hourLogs->where('status', '!=', 'success')->count(),
                    'avg_response_time' => $hourLogs->where('response_time', '>', 0)->avg('response_time') ?: 0
                ];
            }
            
            return [
                'service' => $service,
                'period' => "{$hours} hours",
                'total_requests' => $totalRequests,
                'successful_requests' => $successfulRequests,
                'failed_requests' => $failedRequests,
                'success_rate' => round($successRate, 2),
                'avg_response_time' => round($avgResponseTime ?: 0, 2),
                'max_response_time' => round($maxResponseTime ?: 0, 2),
                'min_response_time' => round($minResponseTime ?: 0, 2),
                'hourly_breakdown' => array_reverse($hourlyBreakdown)
            ];
            
        } catch (\Exception $e) {
            Log::error("Failed to get API metrics for {$service}: " . $e->getMessage());
            
            return [
                'service' => $service,
                'error' => 'Failed to retrieve metrics',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if API is currently down
     */
    public function isApiDown($service)
    {
        $cacheKey = "api_down_{$service}";
        
        return Cache::get($cacheKey, false);
    }

    /**
     * Mark API as down
     */
    public function markApiDown($service, $reason = 'Multiple failures detected')
    {
        $cacheKey = "api_down_{$service}";
        $duration = Configuration::getValue('api_downtime_cache', 600); // 10 minutes default
        
        Cache::put($cacheKey, true, $duration);
        
        Log::warning("API marked as down: {$service} - {$reason}");
        
        // Log the downtime event
        $this->logApiCall($service, 'system', ['event' => 'api_down'], $reason, 0, 'down');
    }

    /**
     * Mark API as up
     */
    public function markApiUp($service)
    {
        $cacheKey = "api_down_{$service}";
        
        Cache::forget($cacheKey);
        
        Log::info("API marked as up: {$service}");
        
        // Log the recovery event
        $this->logApiCall($service, 'system', ['event' => 'api_up'], 'API recovered', 0, 'up');
    }

    /**
     * Get current API status for all services
     */
    public function getAllApiStatus()
    {
        $services = ['airtime', 'data', 'cable_tv', 'electricity', 'exam_pin', 'recharge_pin'];
        $status = [];
        
        foreach ($services as $service) {
            $endpoint = Configuration::getValue("{$service}_api_url");
            
            if ($endpoint) {
                $health = $this->checkApiHealth($service, $endpoint);
                $status[$service] = [
                    'name' => ucfirst(str_replace('_', ' ', $service)),
                    'endpoint' => $endpoint,
                    'status' => $health['status'],
                    'response_time' => $health['response_time'] ?? 0,
                    'is_down' => $this->isApiDown($service),
                    'last_checked' => $health['last_checked'] ?? null
                ];
            } else {
                $status[$service] = [
                    'name' => ucfirst(str_replace('_', ' ', $service)),
                    'endpoint' => null,
                    'status' => 'not_configured',
                    'response_time' => 0,
                    'is_down' => false,
                    'last_checked' => null
                ];
            }
        }
        
        return $status;
    }

    /**
     * Check for API failures and auto-mark as down
     */
    public function checkForFailures($service, $threshold = 5, $timeWindow = 300)
    {
        try {
            $since = Carbon::now()->subSeconds($timeWindow);
            
            $recentFailures = ApiLog::where('service', $service)
                                   ->where('status', '!=', 'success')
                                   ->where('created_at', '>=', $since)
                                   ->count();
            
            if ($recentFailures >= $threshold && !$this->isApiDown($service)) {
                $this->markApiDown($service, "Multiple failures: {$recentFailures} in {$timeWindow} seconds");
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error("Failed to check API failures for {$service}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Auto-recover API if recent calls are successful
     */
    public function checkForRecovery($service, $threshold = 3)
    {
        try {
            if (!$this->isApiDown($service)) {
                return false;
            }
            
            $recentSuccesses = ApiLog::where('service', $service)
                                    ->where('status', 'success')
                                    ->orderBy('created_at', 'desc')
                                    ->limit($threshold)
                                    ->count();
            
            if ($recentSuccesses >= $threshold) {
                $this->markApiUp($service);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error("Failed to check API recovery for {$service}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cleanup old API logs
     */
    public function cleanupOldLogs($days = 30)
    {
        try {
            $cutoff = Carbon::now()->subDays($days);
            
            $deleted = ApiLog::where('created_at', '<', $cutoff)->delete();
            
            Log::info("Cleaned up {$deleted} old API logs older than {$days} days");
            
            return $deleted;
            
        } catch (\Exception $e) {
            Log::error("Failed to cleanup old API logs: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get API error patterns
     */
    public function getErrorPatterns($service, $hours = 24)
    {
        try {
            $since = Carbon::now()->subHours($hours);
            
            $errorLogs = ApiLog::where('service', $service)
                              ->where('status', '!=', 'success')
                              ->where('created_at', '>=', $since)
                              ->get();
            
            $patterns = [];
            
            foreach ($errorLogs as $log) {
                $responseData = is_string($log->response_data) ? $log->response_data : json_encode($log->response_data);
                
                // Extract common error patterns
                if (strpos($responseData, 'timeout') !== false) {
                    $patterns['timeout'] = ($patterns['timeout'] ?? 0) + 1;
                } elseif (strpos($responseData, 'connection') !== false) {
                    $patterns['connection'] = ($patterns['connection'] ?? 0) + 1;
                } elseif (strpos($responseData, 'authentication') !== false) {
                    $patterns['authentication'] = ($patterns['authentication'] ?? 0) + 1;
                } elseif (strpos($responseData, 'insufficient') !== false) {
                    $patterns['insufficient_balance'] = ($patterns['insufficient_balance'] ?? 0) + 1;
                } else {
                    $patterns['other'] = ($patterns['other'] ?? 0) + 1;
                }
            }
            
            // Sort by frequency
            arsort($patterns);
            
            return [
                'service' => $service,
                'period' => "{$hours} hours",
                'total_errors' => $errorLogs->count(),
                'error_patterns' => $patterns
            ];
            
        } catch (\Exception $e) {
            Log::error("Failed to get error patterns for {$service}: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}