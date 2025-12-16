<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\SiteSettings;
use App\Models\Notification;
use App\Services\DashboardMetricsService;
use App\Services\MonitoringService;
use App\Services\LoggingService;
use App\Services\FailoverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    protected $metricsService;

    public function __construct(DashboardMetricsService $metricsService)
    {
        $this->middleware('admin');
        $this->metricsService = $metricsService;
    }

    /**
     * Show the admin dashboard with enhanced analytics
     */
    public function index()
    {
        try {
            // Get dashboard statistics
            $stats = $this->getDashboardStats();

            // Get enhanced dashboard data with metrics service (without provider health - loaded via AJAX)
            $dashboardData = [
                'real_time_metrics' => $this->metricsService->getRealTimeMetricsWithoutProviders(),
                'hourly_trends' => $this->metricsService->getTodayHourlyTrends(),
                'live_transactions' => $this->metricsService->getLiveTransactionFeed(),
                'weekly_comparison' => $this->metricsService->getWeeklyComparison(),
                'system_alerts' => $this->metricsService->getSystemAlerts(),
                'recent_transactions' => $this->getRecentTransactions(),
                'user_trends' => $this->getUserRegistrationTrends(),
                'transaction_trends' => $this->getTransactionTrends(),
                'top_services' => $this->getTopServices(),
                'pending_transactions' => $this->getPendingTransactions()
            ];

            return view('admin.dashboard.index', compact('stats', 'dashboardData'));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Get real-time dashboard data (AJAX endpoint)
     */
    public function getRealTimeData()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'metrics' => $this->metricsService->getRealTimeMetrics(),
                    'live_transactions' => $this->metricsService->getLiveTransactionFeed(5),
                    'alerts' => $this->metricsService->getSystemAlerts(),
                    'timestamp' => now()->format('H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch real-time data'
            ], 500);
        }
    }

    /**
     * Get provider health status (AJAX endpoint)
     * Only checks Uzobest, Paystack, and Monnify
     */
    public function getProviderHealth()
    {
        try {
            $healthMetrics = $this->metricsService->checkCriticalProvidersHealth();

            return response()->json([
                'success' => true,
                'data' => $healthMetrics,
                'timestamp' => now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Provider health check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provider health data'
            ], 500);
        }
    }

    /**
     * Get API performance metrics (AJAX endpoint)
     */
    public function getApiPerformance()
    {
        try {
            $performanceMetrics = $this->metricsService->getApiPerformanceMetrics();

            return response()->json([
                'success' => true,
                'data' => $performanceMetrics,
                'timestamp' => now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('API performance check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch API performance data'
            ], 500);
        }
    }

    /**
     * Get security metrics and events (AJAX endpoint)
     */
    public function getSecurityMetrics()
    {
        try {
            $securityMetrics = $this->metricsService->getSecurityMetrics();

            return response()->json([
                'success' => true,
                'data' => $securityMetrics,
                'timestamp' => now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Security metrics check failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch security metrics'
            ], 500);
        }
    }

    /**
     * Get comprehensive monitoring overview
     */
    public function monitoringOverview()
    {
        try {
            $monitoringData = [
                'provider_health' => $this->metricsService->getProviderHealthMetrics(),
                'api_performance' => $this->metricsService->getApiPerformanceMetrics(),
                'security_metrics' => $this->metricsService->getSecurityMetrics(),
                'system_alerts' => $this->metricsService->getSystemAlerts(),
                'real_time_metrics' => $this->metricsService->getRealTimeMetrics()
            ];

            return view('admin.monitoring.overview', compact('monitoringData'));
        } catch (\Exception $e) {
            Log::error('Monitoring overview error: ' . $e->getMessage());
            return back()->with('error', 'Error loading monitoring overview: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total_users' => User::count(),
            'active_users' => User::where('reg_status', 'active')->count(),
            'new_users_today' => User::whereDate('created_at', $today)->count(),
            'new_users_month' => User::whereDate('created_at', '>=', $thisMonth)->count(),

            'total_transactions' => Transaction::count(),
            'successful_transactions' => Transaction::where('status', 'Completed')->count(),
            'pending_transactions' => Transaction::where('status', 'Pending')->count(),
            'failed_transactions' => Transaction::where('status', 'Failed')->count(),

            'today_transactions' => Transaction::whereDate('date', $today)->count(),
            'today_revenue' => Transaction::whereDate('date', $today)
                ->where('status', 'Completed')
                ->sum(DB::raw('CAST(amount as DECIMAL(10,2))')),

            'month_transactions' => Transaction::whereDate('date', '>=', $thisMonth)->count(),
            'month_revenue' => Transaction::whereDate('date', '>=', $thisMonth)
                ->where('status', 'Completed')
                ->sum(DB::raw('CAST(amount as DECIMAL(10,2))')),

            'last_month_revenue' => Transaction::whereBetween('date', [
                $lastMonth,
                $thisMonth->copy()->subDay()
            ])->where('status', 'Completed')->sum(DB::raw('CAST(amount as DECIMAL(10,2))')),

            'total_wallet_balance' => User::sum('wallet_balance'),
            'average_transaction_value' => Transaction::where('status', 'Completed')
                ->avg(DB::raw('CAST(amount as DECIMAL(10,2))')),

            'commission_earned' => Transaction::where('status', 'Completed')
                ->sum('profit'),
        ];
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions($limit = 10)
    {
        return Transaction::with('user')
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'user' => $transaction->user ? $transaction->user->name : 'Unknown User',
                    'type' => $transaction->servicename,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'date' => $transaction->date,
                    'description' => $transaction->servicedesc
                ];
            });
    }

    /**
     * Get user registration trends (last 30 days)
     */
    private function getUserRegistrationTrends()
    {
        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', $date)->count();
            $days->push([
                'date' => $date,
                'count' => $count
            ]);
        }
        return $days;
    }

    /**
     * Get transaction trends (last 30 days)
     */
    private function getTransactionTrends()
    {
        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $transactions = Transaction::whereDate('date', $date);

            $days->push([
                'date' => $date,
                'count' => $transactions->count(),
                'revenue' => $transactions->where('status', 'Completed')->sum(DB::raw('CAST(amount as DECIMAL(10,2))')),
                'successful' => $transactions->where('status', 'Completed')->count(),
                'failed' => $transactions->where('status', 'Failed')->count()
            ]);
        }
        return $days;
    }

    /**
     * Get top performing services
     */
    private function getTopServices()
    {
        return Transaction::select('servicename', DB::raw('COUNT(*) as count'), DB::raw('SUM(CAST(amount as DECIMAL(10,2))) as revenue'))
            ->where('status', 'Completed')
            ->whereDate('date', '>=', Carbon::now()->subDays(30))
            ->groupBy('servicename')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($service) {
                return [
                    'type' => $service->servicename,
                    'count' => $service->count,
                    'revenue' => $service->revenue,
                    'average' => $service->count > 0 ? round($service->revenue / $service->count, 2) : 0
                ];
            });
    }

    /**
     * Get pending transactions requiring attention
     */
    private function getPendingTransactions()
    {
        return Transaction::where('status', 'Pending')
            ->where('date', '<', Carbon::now()->subHours(1)) // Pending for more than 1 hour
            ->orderBy('date', 'asc')
            ->limit(20)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'type' => $transaction->servicename,
                    'amount' => $transaction->amount,
                    'user' => $transaction->sId, // user ID
                    'date' => $transaction->date,
                    'hours_pending' => Carbon::parse($transaction->date)->diffInHours(Carbon::now())
                ];
            });
    }

    /**
     * Get system overview
     */
    public function systemOverview()
    {
        try {
            $overview = [
                'server_info' => [
                    'php_version' => phpversion(),
                    'laravel_version' => app()->version(),
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time')
                ],

                'database_info' => [
                    'total_users' => User::count(),
                    'total_transactions' => Transaction::count(),
                    'database_size' => $this->getDatabaseSize(),
                    'last_backup' => SiteSettings::getValue('lastBackup', 'Never')
                ],

                'api_status' => $this->checkApiStatus(),

                'system_health' => $this->getSystemHealth()
            ];

            return view('admin.dashboard.system-overview', compact('overview'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading system overview: ' . $e->getMessage());
        }
    }

    /**
     * Get database size (simplified)
     */
    private function getDatabaseSize()
    {
        try {
            $size = DB::select('SELECT SUM(LENGTH(column_default) + LENGTH(column_name)) as size FROM information_schema.columns')[0]->size ?? 0;
            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Check API status
     */
    private function checkApiStatus()
    {
        // This would check external API endpoints
        return [
            'airtime_api' => 'online',
            'data_api' => 'online',
            'electricity_api' => 'online',
            'cable_tv_api' => 'online'
        ];
    }

    /**
     * Get system health indicators
     */
    private function getSystemHealth()
    {
        $health = [
            'disk_space' => $this->getDiskSpaceUsage(),
            'memory_usage' => $this->getMemoryUsage(),
            'recent_errors' => $this->getRecentErrors(),
            'uptime' => $this->getUptime()
        ];

        return $health;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get disk space usage
     */
    private function getDiskSpaceUsage()
    {
        $total = disk_total_space('.');
        $free = disk_free_space('.');
        $used = $total - $free;

        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percentage' => round(($used / $total) * 100, 2)
        ];
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage()
    {
        return [
            'current' => $this->formatBytes(memory_get_usage(true)),
            'peak' => $this->formatBytes(memory_get_peak_usage(true)),
            'limit' => ini_get('memory_limit')
        ];
    }

    /**
     * Get recent errors from logs
     */
    private function getRecentErrors()
    {
        // This would read from Laravel logs
        return [];
    }

    /**
     * Get system uptime
     */
    private function getUptime()
    {
        // This would get actual server uptime
        return 'N/A';
    }

    /**
     * Send system notification
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,error,success',
            'target' => 'required|in:all,admins,users,specific',
            'user_ids' => 'required_if:target,specific|array'
        ]);

        try {
            $notification = Notification::create([
                'nTitle' => $request->title,
                'nMessage' => $request->message,
                'nType' => $request->type,
                'nTarget' => $request->target,
                'nUserIds' => $request->target === 'specific' ? json_encode($request->user_ids) : null,
                'nStatus' => 'active',
                'nDate' => now()
            ]);

            return back()->with('success', 'Notification sent successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error sending notification: ' . $e->getMessage());
        }
    }
}
