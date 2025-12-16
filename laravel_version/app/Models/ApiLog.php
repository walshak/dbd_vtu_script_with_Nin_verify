<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ApiLog extends Model
{
    use HasFactory;

    protected $table = 'api_logs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'service',
        'endpoint',
        'request_data',
        'response_data',
        'response_time',
        'status',
        'created_at'
    ];

    protected $casts = [
        'response_time' => 'float',
        'created_at' => 'datetime',
        'request_data' => 'json',
        'response_data' => 'json'
    ];

    /**
     * Check if the api_logs table exists
     */
    protected static function tableExists()
    {
        return Schema::hasTable('api_logs');
    }

    /**
     * Get logs by service
     */
    public static function getByService($service, $limit = 100)
    {
        if (!static::tableExists()) {
            return collect();
        }

        try {
            return static::where('service', $service)
                        ->orderBy('created_at', 'desc')
                        ->limit($limit)
                        ->get();
        } catch (\Exception $e) {
            Log::warning('ApiLog::getByService failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get recent failures for service
     */
    public static function getRecentFailures($service, $minutes = 60)
    {
        if (!static::tableExists()) {
            return collect();
        }

        try {
            $since = Carbon::now()->subMinutes($minutes);

            return static::where('service', $service)
                        ->where('status', '!=', 'success')
                        ->where('created_at', '>=', $since)
                        ->orderBy('created_at', 'desc')
                        ->get();
        } catch (\Exception $e) {
            Log::warning('ApiLog::getRecentFailures failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get success rate for service
     */
    public static function getSuccessRate($service, $hours = 24)
    {
        if (!static::tableExists()) {
            return 100; // No data means 100% success rate
        }

        try {
            $since = Carbon::now()->subHours($hours);

            $total = static::where('service', $service)
                          ->where('created_at', '>=', $since)
                          ->count();

            if ($total === 0) {
                return 100; // No data means 100% success rate
            }

            $successful = static::where('service', $service)
                               ->where('status', 'success')
                               ->where('created_at', '>=', $since)
                               ->count();

            return round(($successful / $total) * 100, 2);
        } catch (\Exception $e) {
            Log::warning('ApiLog::getSuccessRate failed: ' . $e->getMessage());
            return 100; // Default to success when can't query
        }
    }

    /**
     * Get average response time for service
     */
    public static function getAverageResponseTime($service, $hours = 24)
    {
        if (!static::tableExists()) {
            return 0;
        }

        try {
            $since = Carbon::now()->subHours($hours);

            return static::where('service', $service)
                        ->where('status', 'success')
                        ->where('created_at', '>=', $since)
                        ->avg('response_time') ?: 0;
        } catch (\Exception $e) {
            Log::warning('ApiLog::getAverageResponseTime failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Log API call (static method for convenience)
     */
    public static function logCall($service, $endpoint, $request, $response, $responseTime, $status = 'success')
    {
        if (!static::tableExists()) {
            Log::info('ApiLog table does not exist - skipping log entry', [
                'service' => $service,
                'endpoint' => $endpoint,
                'status' => $status
            ]);
            return null;
        }

        try {
            return static::create([
                'service' => $service,
                'endpoint' => $endpoint,
                'request_data' => $request,
                'response_data' => $response,
                'response_time' => $responseTime,
                'status' => $status,
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API call: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clean up old logs
     */
    public static function cleanup($days = 30)
    {
        if (!static::tableExists()) {
            return 0;
        }

        try {
            $cutoff = Carbon::now()->subDays($days);

            return static::where('created_at', '<', $cutoff)->delete();
        } catch (\Exception $e) {
            Log::warning('ApiLog::cleanup failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get hourly stats for service
     */
    public static function getHourlyStats($service, $hours = 24)
    {
        if (!static::tableExists()) {
            // Return empty stats structure
            $stats = [];
            for ($i = 0; $i < $hours; $i++) {
                $hourStart = Carbon::now()->subHours($i + 1)->startOfHour();
                $stats[] = [
                    'hour' => $hourStart->format('H:00'),
                    'timestamp' => $hourStart->timestamp,
                    'total_requests' => 0,
                    'successful_requests' => 0,
                    'failed_requests' => 0,
                    'avg_response_time' => 0,
                    'max_response_time' => 0
                ];
            }
            return array_reverse($stats);
        }

        try {
            $stats = [];

            for ($i = 0; $i < $hours; $i++) {
                $hourStart = Carbon::now()->subHours($i + 1)->startOfHour();
                $hourEnd = Carbon::now()->subHours($i)->startOfHour();

                $hourLogs = static::where('service', $service)
                                 ->whereBetween('created_at', [$hourStart, $hourEnd])
                                 ->get();

                $stats[] = [
                    'hour' => $hourStart->format('H:00'),
                    'timestamp' => $hourStart->timestamp,
                    'total_requests' => $hourLogs->count(),
                    'successful_requests' => $hourLogs->where('status', 'success')->count(),
                    'failed_requests' => $hourLogs->where('status', '!=', 'success')->count(),
                    'avg_response_time' => round($hourLogs->where('status', 'success')->avg('response_time') ?: 0, 2),
                    'max_response_time' => round($hourLogs->max('response_time') ?: 0, 2)
                ];
            }

            return array_reverse($stats);
        } catch (\Exception $e) {
            Log::warning('ApiLog::getHourlyStats failed: ' . $e->getMessage());
            // Return empty stats structure on error
            $stats = [];
            for ($i = 0; $i < $hours; $i++) {
                $hourStart = Carbon::now()->subHours($i + 1)->startOfHour();
                $stats[] = [
                    'hour' => $hourStart->format('H:00'),
                    'timestamp' => $hourStart->timestamp,
                    'total_requests' => 0,
                    'successful_requests' => 0,
                    'failed_requests' => 0,
                    'avg_response_time' => 0,
                    'max_response_time' => 0
                ];
            }
            return array_reverse($stats);
        }
    }

    /**
     * Get service status overview
     */
    public static function getServiceStatus($service)
    {
        if (!static::tableExists()) {
            return [
                'service' => $service,
                'status' => 'unknown',
                'recent_requests' => 0,
                'recent_failures' => 0,
                'recent_success_rate' => 0,
                'last_request' => null,
                'last_status' => null
            ];
        }

        try {
            $recent = Carbon::now()->subMinutes(30);
            $recentLogs = static::where('service', $service)
                               ->where('created_at', '>=', $recent)
                               ->get();

            $totalRecent = $recentLogs->count();
            $successfulRecent = $recentLogs->where('status', 'success')->count();
            $failedRecent = $recentLogs->where('status', '!=', 'success')->count();

            // Determine status
            $status = 'unknown';
            if ($totalRecent > 0) {
                $failureRate = ($failedRecent / $totalRecent) * 100;

                if ($failureRate > 50) {
                    $status = 'critical';
                } elseif ($failureRate > 20) {
                    $status = 'degraded';
                } else {
                    $status = 'healthy';
                }
            }

            $lastLog = static::where('service', $service)
                            ->orderBy('created_at', 'desc')
                            ->first();

            return [
                'service' => $service,
                'status' => $status,
                'recent_requests' => $totalRecent,
                'recent_failures' => $failedRecent,
                'recent_success_rate' => $totalRecent > 0 ? round(($successfulRecent / $totalRecent) * 100, 2) : 0,
                'last_request' => $lastLog ? $lastLog->created_at->toISOString() : null,
                'last_status' => $lastLog ? $lastLog->status : null
            ];
        } catch (\Exception $e) {
            Log::warning('ApiLog::getServiceStatus failed: ' . $e->getMessage());
            return [
                'service' => $service,
                'status' => 'unknown',
                'recent_requests' => 0,
                'recent_failures' => 0,
                'recent_success_rate' => 0,
                'last_request' => null,
                'last_status' => null
            ];
        }
    }

    /**
     * Get error summary for service
     */
    public static function getErrorSummary($service, $hours = 24)
    {
        if (!static::tableExists()) {
            return [
                'service' => $service,
                'period' => "{$hours} hours",
                'total_errors' => 0,
                'unique_error_types' => 0,
                'error_breakdown' => []
            ];
        }

        try {
            $since = Carbon::now()->subHours($hours);

            $errorLogs = static::where('service', $service)
                              ->where('status', '!=', 'success')
                              ->where('created_at', '>=', $since)
                              ->get();

            $errors = [];

            foreach ($errorLogs as $log) {
                $errorKey = $log->status;

                if (!isset($errors[$errorKey])) {
                    $errors[$errorKey] = [
                        'error_type' => $errorKey,
                        'count' => 0,
                        'first_occurrence' => $log->created_at,
                        'last_occurrence' => $log->created_at,
                        'sample_response' => $log->response_data
                    ];
                }

                $errors[$errorKey]['count']++;

                if ($log->created_at < $errors[$errorKey]['first_occurrence']) {
                    $errors[$errorKey]['first_occurrence'] = $log->created_at;
                }

                if ($log->created_at > $errors[$errorKey]['last_occurrence']) {
                    $errors[$errorKey]['last_occurrence'] = $log->created_at;
                    $errors[$errorKey]['sample_response'] = $log->response_data;
                }
            }

            // Sort by count
            uasort($errors, function($a, $b) {
                return $b['count'] - $a['count'];
            });

            return [
                'service' => $service,
                'period' => "{$hours} hours",
                'total_errors' => $errorLogs->count(),
                'unique_error_types' => count($errors),
                'error_breakdown' => array_values($errors)
            ];
        } catch (\Exception $e) {
            Log::warning('ApiLog::getErrorSummary failed: ' . $e->getMessage());
            return [
                'service' => $service,
                'period' => "{$hours} hours",
                'total_errors' => 0,
                'unique_error_types' => 0,
                'error_breakdown' => []
            ];
        }
    }

    /**
     * Check if service is having issues
     */
    public static function isServiceHavingIssues($service, $failureThreshold = 50, $timeWindow = 30)
    {
        if (!static::tableExists()) {
            return false; // No table means no issues detected
        }

        try {
            $since = Carbon::now()->subMinutes($timeWindow);

            $recentLogs = static::where('service', $service)
                               ->where('created_at', '>=', $since)
                               ->get();

            $total = $recentLogs->count();

            if ($total < 5) {
                return false; // Not enough data
            }

            $failures = $recentLogs->where('status', '!=', 'success')->count();
            $failureRate = ($failures / $total) * 100;

            return $failureRate >= $failureThreshold;
        } catch (\Exception $e) {
            Log::warning('ApiLog::isServiceHavingIssues failed: ' . $e->getMessage());
            return false; // Default to no issues when can't determine
        }
    }
}
