<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ApiMonitoringService;
use App\Services\ApiFallbackService;
use App\Models\ApiLog;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApiMonitoringController extends Controller
{
    protected $monitoringService;
    protected $fallbackService;

    public function __construct(ApiMonitoringService $monitoringService, ApiFallbackService $fallbackService)
    {
        $this->middleware('admin');
        $this->monitoringService = $monitoringService;
        $this->fallbackService = $fallbackService;
    }

    /**
     * Show API monitoring dashboard
     */
    public function index()
    {
        try {
            $apiStatus = $this->monitoringService->getAllApiStatus();
            $healthSummary = $this->fallbackService->getHealthSummary();

            // Get recent critical alerts
            $criticalAlerts = $this->getCriticalAlerts();

            // Get performance overview
            $performanceOverview = $this->getPerformanceOverview();

            return view('admin.api-monitoring.index', compact(
                'apiStatus',
                'healthSummary',
                'criticalAlerts',
                'performanceOverview'
            ));
        } catch (\Exception $e) {
            Log::error('API Monitoring Dashboard Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to load API monitoring data.']);
        }
    }

    /**
     * Get detailed metrics for a specific service
     */
    public function getServiceMetrics(Request $request, $service)
    {
        $request->validate([
            'hours' => 'integer|min:1|max:168' // Max 7 days
        ]);

        $hours = $request->get('hours', 24);

        try {
            $metrics = $this->monitoringService->getApiMetrics($service, $hours);
            $errorPatterns = $this->monitoringService->getErrorPatterns($service, $hours);
            $hourlyStats = ApiLog::getHourlyStats($service, $hours);
            $errorSummary = ApiLog::getErrorSummary($service, $hours);

            return response()->json([
                'success' => true,
                'service' => $service,
                'metrics' => $metrics,
                'error_patterns' => $errorPatterns,
                'hourly_stats' => $hourlyStats,
                'error_summary' => $errorSummary
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to get metrics for {$service}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service metrics'
            ], 500);
        }
    }

    /**
     * Toggle API service status (force up/down)
     */
    public function toggleServiceStatus(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
            'action' => 'required|in:mark_up,mark_down,auto_recover'
        ]);

        try {
            $service = $request->service;
            $action = $request->action;

            switch ($action) {
                case 'mark_up':
                    $this->monitoringService->markApiUp($service);
                    $message = "Service {$service} marked as UP";
                    break;

                case 'mark_down':
                    $reason = $request->get('reason', 'Manually marked down by admin');
                    $this->monitoringService->markApiDown($service, $reason);
                    $message = "Service {$service} marked as DOWN";
                    break;

                case 'auto_recover':
                    $recovered = $this->monitoringService->checkForRecovery($service, 1);
                    $message = $recovered ?
                        "Service {$service} auto-recovery attempted" :
                        "Service {$service} does not meet recovery criteria";
                    break;
            }

            Log::info("API Service Status Changed", [
                'service' => $service,
                'action' => $action,
                'admin_id' => Auth::id() ?? 'unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Toggle Service Status Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update service status'
            ], 500);
        }
    }

    /**
     * Test API connectivity
     */
    public function testApiConnectivity(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
            'endpoint' => 'string',
            'test_data' => 'array'
        ]);

        try {
            $service = $request->service;
            $endpoint = $request->get('endpoint') ?: Configuration::getValue("{$service}_api_url");
            $testData = $request->get('test_data', []);

            if (!$endpoint) {
                return response()->json([
                    'success' => false,
                    'message' => 'No endpoint configured for this service'
                ]);
            }

            // Perform connectivity test
            $health = $this->monitoringService->checkApiHealth($service, $endpoint);

            return response()->json([
                'success' => true,
                'service' => $service,
                'endpoint' => $endpoint,
                'health_check' => $health,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error("API Connectivity Test Failed for {$service}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Connectivity test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get API logs with filtering
     */
    public function getLogs(Request $request)
    {
        $request->validate([
            'service' => 'string',
            'status' => 'string',
            'hours' => 'integer|min:1|max:168',
            'limit' => 'integer|min:10|max:1000'
        ]);

        try {
            $query = ApiLog::query();

            // Apply filters
            if ($request->filled('service')) {
                $query->where('service', $request->service);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('hours')) {
                $since = Carbon::now()->subHours($request->hours);
                $query->where('created_at', '>=', $since);
            }

            $logs = $query->orderBy('created_at', 'desc')
                ->limit($request->get('limit', 100))
                ->get();

            return response()->json([
                'success' => true,
                'logs' => $logs,
                'total' => $logs->count(),
                'filters' => $request->only(['service', 'status', 'hours'])
            ]);
        } catch (\Exception $e) {
            Log::error('Get API Logs Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve API logs'
            ], 500);
        }
    }

    /**
     * Export API logs
     */
    public function exportLogs(Request $request)
    {
        $request->validate([
            'service' => 'string',
            'hours' => 'integer|min:1|max:168',
            'format' => 'in:csv,json'
        ]);

        try {
            $query = ApiLog::query();

            if ($request->filled('service')) {
                $query->where('service', $request->service);
            }

            if ($request->filled('hours')) {
                $since = Carbon::now()->subHours($request->hours);
                $query->where('created_at', '>=', $since);
            }

            $logs = $query->orderBy('created_at', 'desc')
                ->limit(10000) // Reasonable limit for export
                ->get();

            $format = $request->get('format', 'csv');
            $filename = 'api_logs_' . date('Y-m-d_H-i-s') . '.' . $format;

            if ($format === 'json') {
                return response()->json($logs->toArray())
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            }

            // CSV export
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ];

            return response()->stream(function () use ($logs) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'ID',
                    'Service',
                    'Endpoint',
                    'Status',
                    'Response Time (ms)',
                    'Created At',
                    'Request Data',
                    'Response Data'
                ]);

                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->id,
                        $log->service,
                        $log->endpoint,
                        $log->status,
                        $log->response_time,
                        $log->created_at,
                        is_array($log->request_data) ? json_encode($log->request_data) : $log->request_data,
                        is_array($log->response_data) ? json_encode($log->response_data) : $log->response_data
                    ]);
                }

                fclose($file);
            }, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Export API Logs Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to export API logs.']);
        }
    }

    /**
     * Configure fallback APIs
     */
    public function configureFallback(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
            'fallbacks' => 'required|array',
            'fallbacks.*.name' => 'required|string',
            'fallbacks.*.url' => 'required|url',
            'fallbacks.*.api_key' => 'required|string',
            'fallbacks.*.auth_type' => 'in:bearer,basic,header',
            'fallbacks.*.enabled' => 'boolean'
        ]);

        try {
            $service = $request->service;
            $fallbacks = $request->fallbacks;

            // Clear existing fallback configurations
            $existingCount = Configuration::getValue("{$service}_fallback_count", 0);
            for ($i = 1; $i <= $existingCount; $i++) {
                Configuration::setValue("{$service}_fallback_{$i}_name", null);
                Configuration::setValue("{$service}_fallback_{$i}_url", null);
                Configuration::setValue("{$service}_fallback_{$i}_key", null);
                Configuration::setValue("{$service}_fallback_{$i}_auth_type", null);
                Configuration::setValue("{$service}_fallback_{$i}_enabled", null);
            }

            // Save new fallback configurations
            $enabledCount = 0;
            foreach ($fallbacks as $index => $fallback) {
                if (!isset($fallback['enabled']) || $fallback['enabled']) {
                    $enabledCount++;
                    $configIndex = $enabledCount;

                    Configuration::setValue("{$service}_fallback_{$configIndex}_name", $fallback['name']);
                    Configuration::setValue("{$service}_fallback_{$configIndex}_url", $fallback['url']);
                    Configuration::setValue("{$service}_fallback_{$configIndex}_key", $fallback['api_key']);
                    Configuration::setValue("{$service}_fallback_{$configIndex}_auth_type", $fallback['auth_type'] ?? 'bearer');
                    Configuration::setValue("{$service}_fallback_{$configIndex}_timeout", $fallback['timeout'] ?? 30);
                    Configuration::setValue("{$service}_fallback_{$configIndex}_method", $fallback['method'] ?? 'post');
                }
            }

            Configuration::setValue("{$service}_fallback_count", $enabledCount);

            Log::info("Fallback APIs configured for {$service}", [
                'fallback_count' => $enabledCount,
                'admin_id' => Auth::id() ?? 'unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Configured {$enabledCount} fallback APIs for {$service}"
            ]);
        } catch (\Exception $e) {
            Log::error('Configure Fallback Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to configure fallback APIs'
            ], 500);
        }
    }

    /**
     * Clean up old API logs
     */
    public function cleanupLogs(Request $request)
    {
        $request->validate([
            'days' => 'integer|min:1|max:365'
        ]);

        try {
            $days = $request->get('days', 30);
            $deleted = $this->monitoringService->cleanupOldLogs($days);

            Log::info("API logs cleanup completed", [
                'days' => $days,
                'deleted_count' => $deleted,
                'admin_id' => Auth::id() ?? 'unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deleted} old log entries",
                'deleted_count' => $deleted
            ]);
        } catch (\Exception $e) {
            Log::error('Cleanup Logs Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup logs'
            ], 500);
        }
    }

    /**
     * Get critical alerts
     */
    protected function getCriticalAlerts()
    {
        $alerts = [];
        $services = ['airtime', 'data', 'cable_tv', 'electricity', 'exam_pin', 'recharge_pin'];

        foreach ($services as $service) {
            // Check if service is down
            if ($this->monitoringService->isApiDown($service)) {
                $alerts[] = [
                    'type' => 'critical',
                    'service' => $service,
                    'message' => "Service {$service} is marked as DOWN",
                    'timestamp' => now()
                ];
            }

            // Check for high failure rates
            if (ApiLog::isServiceHavingIssues($service)) {
                $alerts[] = [
                    'type' => 'warning',
                    'service' => $service,
                    'message' => "Service {$service} has high failure rate",
                    'timestamp' => now()
                ];
            }

            // Check circuit breaker status
            $circuitBreaker = $this->fallbackService->getCircuitBreakerStatus($service);
            if ($circuitBreaker['state'] === 'open') {
                $alerts[] = [
                    'type' => 'warning',
                    'service' => $service,
                    'message' => "Circuit breaker is OPEN for {$service}",
                    'timestamp' => $circuitBreaker['last_failure']
                ];
            }
        }

        return array_slice($alerts, 0, 10); // Limit to 10 most recent alerts
    }

    /**
     * Get performance overview
     */
    protected function getPerformanceOverview()
    {
        $services = ['airtime', 'data', 'cable_tv', 'electricity', 'exam_pin', 'recharge_pin'];
        $overview = [];

        foreach ($services as $service) {
            $successRate = ApiLog::getSuccessRate($service, 24);
            $avgResponseTime = ApiLog::getAverageResponseTime($service, 24);
            $status = ApiLog::getServiceStatus($service);

            $overview[$service] = [
                'name' => ucfirst(str_replace('_', ' ', $service)),
                'success_rate' => $successRate,
                'avg_response_time' => round($avgResponseTime, 2),
                'status' => $status['status'],
                'recent_requests' => $status['recent_requests']
            ];
        }

        return $overview;
    }
}
