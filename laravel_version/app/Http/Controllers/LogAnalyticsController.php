<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\LoggingService;
use Carbon\Carbon;

class LogAnalyticsController extends Controller
{
    protected $loggingService;

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    /**
     * Get logging overview and statistics
     */
    public function getOverview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_range' => 'sometimes|string|in:hour,today,week,month'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $timeRange = $request->time_range ?? 'today';
            $stats = $this->loggingService->getLoggingStatistics($timeRange);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'time_range' => $timeRange,
                    'overview' => $stats,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get logging overview',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction logs with filtering
     */
    public function getTransactionLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'transaction_type' => 'sometimes|string',
            'status' => 'sometimes|string|in:success,error,pending',
            'user_id' => 'sometimes|integer',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid filter parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $page = $request->page ?? 1;
            $perPage = $request->per_page ?? 25;
            $offset = ($page - 1) * $perPage;

            $query = DB::table('transaction_logs');

            // Apply filters
            if ($request->transaction_type) {
                $query->where('transaction_type', $request->transaction_type);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->start_date) {
                $query->where('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
            }

            $total = $query->count();
            $logs = $query->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get()
                ->map(function ($log) {
                    $log->data = json_decode($log->data, true);
                    $log->metadata = json_decode($log->metadata, true);
                    return $log;
                });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'logs' => $logs,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => $total,
                        'last_page' => ceil($total / $perPage),
                        'from' => $offset + 1,
                        'to' => min($offset + $perPage, $total)
                    ],
                    'filters_applied' => array_filter([
                        'transaction_type' => $request->transaction_type,
                        'status' => $request->status,
                        'user_id' => $request->user_id,
                        'date_range' => $request->start_date || $request->end_date
                    ])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get transaction logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get API performance metrics
     */
    public function getApiMetrics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_range' => 'sometimes|string|in:hour,today,week,month',
            'endpoint' => 'sometimes|string',
            'provider' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $timeRange = $request->time_range ?? 'today';
            $startDate = $this->getStartDate($timeRange);

            $query = DB::table('api_metrics')
                ->where('created_at', '>=', $startDate);

            if ($request->endpoint) {
                $query->where('endpoint', 'like', '%' . $request->endpoint . '%');
            }

            if ($request->provider) {
                $query->where('provider', $request->provider);
            }

            // Get overall metrics
            $overallMetrics = $query->select(
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('AVG(response_time) as avg_response_time'),
                DB::raw('MAX(response_time) as max_response_time'),
                DB::raw('MIN(response_time) as min_response_time'),
                DB::raw('SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_requests'),
                DB::raw('SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed_requests')
            )->first();

            // Get metrics by endpoint
            $endpointMetrics = clone $query;
            $endpointStats = $endpointMetrics->select(
                'endpoint',
                'provider',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('AVG(response_time) as avg_response_time'),
                DB::raw('SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_requests')
            )
            ->groupBy('endpoint', 'provider')
            ->orderBy('total_requests', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($stat) {
                $stat->success_rate = $stat->total_requests > 0
                    ? round(($stat->successful_requests / $stat->total_requests) * 100, 2)
                    : 0;
                return $stat;
            });

            // Get hourly trends
            $hourlyTrends = clone $query;
            $trends = $hourlyTrends->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as requests'),
                DB::raw('AVG(response_time) as avg_response_time'),
                DB::raw('SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_requests')
            )
            ->groupBy(DB::raw('DATE(created_at), HOUR(created_at)'))
            ->orderBy('date', 'asc')
            ->orderBy('hour', 'asc')
            ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'time_range' => $timeRange,
                    'overall_metrics' => [
                        'total_requests' => $overallMetrics->total_requests ?? 0,
                        'avg_response_time' => round($overallMetrics->avg_response_time ?? 0, 2),
                        'max_response_time' => round($overallMetrics->max_response_time ?? 0, 2),
                        'min_response_time' => round($overallMetrics->min_response_time ?? 0, 2),
                        'success_rate' => $overallMetrics->total_requests > 0
                            ? round(($overallMetrics->successful_requests / $overallMetrics->total_requests) * 100, 2)
                            : 0,
                        'error_rate' => $overallMetrics->total_requests > 0
                            ? round(($overallMetrics->failed_requests / $overallMetrics->total_requests) * 100, 2)
                            : 0
                    ],
                    'endpoint_metrics' => $endpointStats,
                    'hourly_trends' => $trends,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get API metrics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get error analysis and patterns
     */
    public function getErrorAnalysis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_range' => 'sometimes|string|in:hour,today,week,month',
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $timeRange = $request->time_range ?? 'today';
            $limit = $request->limit ?? 20;
            $startDate = $this->getStartDate($timeRange);

            // Get error counts by transaction type
            $errorsByType = DB::table('transaction_logs')
                ->where('created_at', '>=', $startDate)
                ->where('status', 'error')
                ->select('transaction_type', DB::raw('COUNT(*) as error_count'))
                ->groupBy('transaction_type')
                ->orderBy('error_count', 'desc')
                ->limit($limit)
                ->get();

            // Get recent critical errors
            $criticalErrors = DB::table('transaction_logs')
                ->where('created_at', '>=', $startDate)
                ->where('status', 'error')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($error) {
                    $error->data = json_decode($error->data, true);
                    $error->metadata = json_decode($error->metadata, true);
                    return $error;
                });

            // Get API error rates by endpoint
            $apiErrors = DB::table('api_metrics')
                ->where('created_at', '>=', $startDate)
                ->where('success', 0)
                ->select(
                    'endpoint',
                    'provider',
                    DB::raw('COUNT(*) as error_count'),
                    DB::raw('AVG(response_time) as avg_response_time')
                )
                ->groupBy('endpoint', 'provider')
                ->orderBy('error_count', 'desc')
                ->limit($limit)
                ->get();

            // Calculate error trends
            $errorTrends = DB::table('transaction_logs')
                ->where('created_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as total_logs'),
                    DB::raw('SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as error_count')
                )
                ->groupBy(DB::raw('DATE(created_at), HOUR(created_at)'))
                ->orderBy('date', 'asc')
                ->orderBy('hour', 'asc')
                ->get()
                ->map(function ($trend) {
                    $trend->error_rate = $trend->total_logs > 0
                        ? round(($trend->error_count / $trend->total_logs) * 100, 2)
                        : 0;
                    return $trend;
                });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'time_range' => $timeRange,
                    'summary' => [
                        'total_errors' => $errorsByType->sum('error_count'),
                        'most_problematic_service' => $errorsByType->first()->transaction_type ?? 'none',
                        'avg_error_rate' => round($errorTrends->avg('error_rate') ?? 0, 2)
                    ],
                    'errors_by_type' => $errorsByType,
                    'critical_errors' => $criticalErrors,
                    'api_errors' => $apiErrors,
                    'error_trends' => $errorTrends,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get error analysis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get security events and audit logs
     */
    public function getSecurityLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_range' => 'sometimes|string|in:hour,today,week,month',
            'severity' => 'sometimes|string|in:low,medium,high,critical',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $timeRange = $request->time_range ?? 'today';
            $page = $request->page ?? 1;
            $perPage = $request->per_page ?? 25;
            $offset = ($page - 1) * $perPage;
            $startDate = $this->getStartDate($timeRange);

            // Get audit logs
            $auditQuery = DB::table('audit_logs')
                ->where('created_at', '>=', $startDate);

            $totalAuditLogs = $auditQuery->count();
            $auditLogs = $auditQuery->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get()
                ->map(function ($log) {
                    $log->changes = json_decode($log->changes, true);
                    return $log;
                });

            // Get failed login attempts
            $failedLogins = DB::table('transaction_logs')
                ->where('created_at', '>=', $startDate)
                ->where('transaction_type', 'authentication')
                ->where('status', 'error')
                ->select('ip_address', DB::raw('COUNT(*) as attempts'))
                ->groupBy('ip_address')
                ->orderBy('attempts', 'desc')
                ->limit(10)
                ->get();

            // Security summary
            $securitySummary = [
                'total_audit_events' => $totalAuditLogs,
                'failed_login_attempts' => $failedLogins->sum('attempts'),
                'unique_ips_with_failures' => $failedLogins->count(),
                'high_risk_ips' => $failedLogins->where('attempts', '>=', 5)->count()
            ];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'time_range' => $timeRange,
                    'security_summary' => $securitySummary,
                    'audit_logs' => $auditLogs,
                    'failed_logins' => $failedLogins,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => $totalAuditLogs,
                        'last_page' => ceil($totalAuditLogs / $perPage)
                    ],
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get security logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export logs to various formats
     */
    public function exportLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'format' => 'required|string|in:json,csv,excel',
            'type' => 'required|string|in:transaction,api,security,audit',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'filters' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid export parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $type = $request->type;
            $format = $request->format;
            $startDate = $request->start_date;
            $endDate = $request->end_date . ' 23:59:59';
            $filters = $request->filters ?? [];

            // Get data based on type
            $data = $this->getExportData($type, $startDate, $endDate, $filters);

            // Generate export file
            $filename = $this->generateExportFile($data, $type, $format);

            return response()->json([
                'status' => 'success',
                'message' => 'Export completed successfully',
                'data' => [
                    'filename' => $filename,
                    'download_url' => url("/downloads/{$filename}"),
                    'record_count' => count($data),
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get live log stream (for real-time monitoring)
     */
    public function getLiveStream(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'types' => 'sometimes|array',
            'types.*' => 'string|in:transaction,api,security,audit',
            'since' => 'sometimes|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid stream parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $since = $request->since ? Carbon::parse($request->since) : now()->subMinutes(5);
            $types = $request->types ?? ['transaction', 'api'];

            $recentLogs = [];

            if (in_array('transaction', $types)) {
                $transactionLogs = DB::table('transaction_logs')
                    ->where('created_at', '>=', $since)
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(function ($log) {
                        $log->type = 'transaction';
                        $log->data = json_decode($log->data, true);
                        return $log;
                    });

                $recentLogs = array_merge($recentLogs, $transactionLogs->toArray());
            }

            if (in_array('api', $types)) {
                $apiLogs = DB::table('api_metrics')
                    ->where('created_at', '>=', $since)
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(function ($log) {
                        $log->type = 'api';
                        return $log;
                    });

                $recentLogs = array_merge($recentLogs, $apiLogs->toArray());
            }

            // Sort by timestamp
            usort($recentLogs, function ($a, $b) {
                return strtotime($b->created_at) - strtotime($a->created_at);
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'logs' => array_slice($recentLogs, 0, 100),
                    'since' => $since->toISOString(),
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get live stream',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data for export based on type
     */
    protected function getExportData(string $type, string $startDate, string $endDate, array $filters): array
    {
        switch ($type) {
            case 'transaction':
                $query = DB::table('transaction_logs')
                    ->whereBetween('created_at', [$startDate, $endDate]);

                if (!empty($filters['transaction_type'])) {
                    $query->where('transaction_type', $filters['transaction_type']);
                }

                return $query->get()->toArray();

            case 'api':
                $query = DB::table('api_metrics')
                    ->whereBetween('created_at', [$startDate, $endDate]);

                if (!empty($filters['endpoint'])) {
                    $query->where('endpoint', 'like', '%' . $filters['endpoint'] . '%');
                }

                return $query->get()->toArray();

            case 'security':
            case 'audit':
                return DB::table('audit_logs')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->toArray();

            default:
                return [];
        }
    }

    /**
     * Generate export file
     */
    protected function generateExportFile(array $data, string $type, string $format): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "{$type}_logs_{$timestamp}.{$format}";
        $filePath = storage_path("app/exports/{$filename}");

        // Ensure directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        switch ($format) {
            case 'json':
                file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
                break;

            case 'csv':
                $this->generateCsvFile($data, $filePath);
                break;

            case 'excel':
                // Would implement Excel export if needed
                $this->generateCsvFile($data, $filePath);
                break;
        }

        return $filename;
    }

    /**
     * Generate CSV file from data
     */
    protected function generateCsvFile(array $data, string $filePath): void
    {
        $file = fopen($filePath, 'w');

        if (!empty($data)) {
            // Write headers
            $headers = array_keys((array) $data[0]);
            fputcsv($file, $headers);

            // Write data
            foreach ($data as $row) {
                fputcsv($file, array_values((array) $row));
            }
        }

        fclose($file);
    }

    /**
     * Get start date for time range
     */
    protected function getStartDate(string $timeRange): Carbon
    {
        switch ($timeRange) {
            case 'hour':
                return now()->subHour();
            case 'today':
                return now()->startOfDay();
            case 'week':
                return now()->startOfWeek();
            case 'month':
                return now()->startOfMonth();
            default:
                return now()->startOfDay();
        }
    }
}
