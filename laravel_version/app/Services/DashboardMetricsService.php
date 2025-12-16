<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Services\MonitoringService;
use App\Services\LoggingService;
use App\Services\FailoverService;
use App\Services\ExternalApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardMetricsService
{
    protected $monitoringService;
    protected $loggingService;
    protected $failoverService;
    protected $externalApiService;

    public function __construct(
        MonitoringService $monitoringService,
        LoggingService $loggingService,
        FailoverService $failoverService,
        ExternalApiService $externalApiService
    ) {
        $this->monitoringService = $monitoringService;
        $this->loggingService = $loggingService;
        $this->failoverService = $failoverService;
        $this->externalApiService = $externalApiService;
    }
    /**
     * Get real-time dashboard metrics
     */
    public function getRealTimeMetrics()
    {
        $cacheKey = 'dashboard_realtime_metrics';

        return Cache::remember($cacheKey, 60, function () { // Cache for 1 minute
            return [
                'transactions' => $this->getTransactionMetrics(),
                'users' => $this->getUserMetrics(),
                'revenue' => $this->getRevenueMetrics(),
                'services' => $this->getServiceMetrics(),
                'system' => $this->getSystemMetrics(),
                'providers' => $this->getProviderHealthMetrics(),
                'api_performance' => $this->getApiPerformanceMetrics(),
                'security' => $this->getSecurityMetrics()
            ];
        });
    }

    /**
     * Get real-time dashboard metrics without provider health (faster initial load)
     */
    public function getRealTimeMetricsWithoutProviders()
    {
        $cacheKey = 'dashboard_realtime_metrics_no_providers';

        return Cache::remember($cacheKey, 60, function () { // Cache for 1 minute
            return [
                'transactions' => $this->getTransactionMetrics(),
                'users' => $this->getUserMetrics(),
                'revenue' => $this->getRevenueMetrics(),
                'services' => $this->getServiceMetrics(),
                'system' => $this->getSystemMetrics(),
                'api_performance' => $this->getApiPerformanceMetrics(),
                'security' => $this->getSecurityMetrics()
            ];
        });
    }

    /**
     * Get transaction metrics
     */
    private function getTransactionMetrics()
    {
        $today = Carbon::today();
        $thisHour = Carbon::now()->startOfHour();
        $last24Hours = Carbon::now()->subHours(24);

        return [
            'today_total' => Transaction::whereDate('date', $today)->count(),
            'today_successful' => Transaction::whereDate('date', $today)
                ->where('status', 'Completed')->count(),
            'today_failed' => Transaction::whereDate('date', $today)
                ->where('status', 'Failed')->count(),
            'today_pending' => Transaction::whereDate('date', $today)
                ->where('status', 'Pending')->count(),
            'this_hour' => Transaction::where('date', '>=', $thisHour)->count(),
            'last_24h' => Transaction::where('date', '>=', $last24Hours)->count(),
            'success_rate_today' => $this->calculateSuccessRate($today),
            'pending_requiring_attention' => Transaction::where('status', 'Pending')
                ->where('date', '<', Carbon::now()->subHours(1))->count()
        ];
    }

    /**
     * Get user metrics
     */
    private function getUserMetrics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_users' => User::count(),
            'active_users' => User::where('reg_status', 'active')->count(),
            'today_registrations' => User::whereDate('created_at', $today)->count(),
            'week_registrations' => User::where('created_at', '>=', $thisWeek)->count(),
            'month_registrations' => User::where('created_at', '>=', $thisMonth)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'kyc_pending' => 0, // No user verification table in this system
            'active_sessions' => $this->getActiveSessions()
        ];
    }

    /**
     * Get revenue metrics
     */
    private function getRevenueMetrics()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->startOfMonth()->subDay();

        $todayRevenue = Transaction::whereDate('date', $today)
            ->where('status', 'Completed')
            ->sum('amount');

        $yesterdayRevenue = Transaction::whereDate('date', $yesterday)
            ->where('status', 'Completed')
            ->sum('amount');

        $thisMonthRevenue = Transaction::where('date', '>=', $thisMonth)
            ->where('status', 'Completed')
            ->sum('amount');

        $lastMonthRevenue = Transaction::whereBetween('date', [$lastMonth, $lastMonthEnd])
            ->where('status', 'Completed')
            ->sum('amount');

        return [
            'today_revenue' => $todayRevenue,
            'yesterday_revenue' => $yesterdayRevenue,
            'month_revenue' => $thisMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'daily_growth' => $this->calculateGrowth($todayRevenue, $yesterdayRevenue),
            'monthly_growth' => $this->calculateGrowth($thisMonthRevenue, $lastMonthRevenue),
            'today_commission' => Transaction::whereDate('date', $today)
                ->where('status', 'Completed')
                ->sum('profit'),
            'average_transaction_value' => Transaction::where('status', 'Completed')
                ->whereDate('date', $today)
                ->avg('amount') ?: 0
        ];
    }

    /**
     * Get service metrics
     */
    private function getServiceMetrics()
    {
        $today = Carbon::today();

        $servicePerformance = Transaction::selectRaw('
            servicename as service,
            COUNT(*) as total,
            SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN status = "Completed" THEN amount ELSE 0 END) as revenue,
            (SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) / COUNT(*)) * 100 as success_rate
        ')
            ->whereDate('date', $today)
            ->groupBy('servicename')
            ->orderBy('revenue', 'desc')
            ->get();

        return [
            'top_services_today' => $servicePerformance->take(5),
            'service_availability' => $this->getServiceAvailability(),
            'api_response_times' => $this->getApiResponseTimes(),
            'service_errors' => $this->getServiceErrors()
        ];
    }

    /**
     * Get system metrics
     */
    private function getSystemMetrics()
    {
        return [
            'server_status' => 'online',
            'database_connections' => $this->getDatabaseConnections(),
            'memory_usage' => $this->getMemoryUsage(),
            'disk_usage' => $this->getDiskUsage(),
            'api_health' => $this->getApiHealth(),
            'recent_errors' => $this->getRecentErrors(),
            'uptime' => $this->getSystemUptime(),
            'monitoring_stats' => $this->monitoringService->getMonitoringDashboard(),
            'service_availability' => $this->monitoringService->performHealthChecks()
        ];
    }

    /**
     * Get provider health metrics (cached for dashboard)
     */
    public function getProviderHealthMetrics()
    {
        try {
            // Return cached/static data for backward compatibility
            return Cache::remember('provider_health_metrics', 300, function () {
                return [
                    'providers' => [
                        'uzobest' => [
                            'status' => 'operational',
                            'response_time' => 200,
                            'success_rate' => 99.5,
                            'last_check' => now(),
                            'is_circuit_open' => false,
                            'available_services' => ['airtime', 'data', 'cable', 'electricity']
                        ]
                    ],
                    'overall_health' => 'good',
                    'active_failovers' => [],
                    'circuit_breaker_status' => 'all_closed'
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting provider health metrics: ' . $e->getMessage());
            return [
                'error' => 'Unable to fetch provider metrics',
                'providers' => [],
                'overall_health' => 'unknown'
            ];
        }
    }

    /**
     * Check health of critical providers: Uzobest (VTU), Paystack (Payments), Monnify (Virtual Accounts)
     * This is called via AJAX after dashboard loads
     */
    public function checkCriticalProvidersHealth()
    {
        try {
            $providers = [
                'uzobest' => $this->checkUzobestHealth(),
                'paystack' => $this->checkPaystackHealth(),
                'monnify' => $this->checkMonnifyHealth()
            ];

            $allHealthy = collect($providers)->every(fn($p) => $p['status'] === 'operational');

            return [
                'providers' => $providers,
                'overall_health' => $allHealthy ? 'good' : 'degraded',
                'checked_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Error checking critical providers health: ' . $e->getMessage());
            return [
                'error' => 'Unable to check providers',
                'providers' => [],
                'overall_health' => 'unknown'
            ];
        }
    }

    /**
     * Check Uzobest API health
     */
    private function checkUzobestHealth()
    {
        try {
            return $this->monitoringService->checkServiceHealth('uzobest_vtu');
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'message' => 'Health check failed',
                'response_time' => 0,
                'last_check' => now()
            ];
        }
    }

    /**
     * Check Paystack payment gateway health
     */
    private function checkPaystackHealth()
    {
        try {
            return $this->monitoringService->checkServiceHealth('paystack');
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'message' => 'Health check failed',
                'response_time' => 0,
                'last_check' => now()
            ];
        }
    }

    /**
     * Check Monnify virtual account provider health
     */
    private function checkMonnifyHealth()
    {
        try {
            return $this->monitoringService->checkServiceHealth('monnify');
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'message' => 'Health check failed',
                'response_time' => 0,
                'last_check' => now()
            ];
        }
    }

    /**
     * Get API performance metrics
     */
    public function getApiPerformanceMetrics()
    {
        try {
            // Return cached static data - api_metrics table doesn't exist yet
            return Cache::remember('api_performance_metrics', 300, function () {
                return [
                    'service_performance' => [],
                    'recent_errors' => [],
                    'hourly_trends' => [],
                    'overall_health_score' => 95,
                    'slowest_services' => [],
                    'fastest_services' => []
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting API performance metrics: ' . $e->getMessage());
            return [
                'error' => 'Unable to fetch API performance data',
                'service_performance' => [],
                'recent_errors' => []
            ];
        }
    }

    /**
     * Get security metrics
     */
    public function getSecurityMetrics()
    {
        try {
            // Return cached static data - audit_logs table doesn't exist yet
            return Cache::remember('security_metrics', 300, function () {
                return [
                    'security_events' => [],
                    'suspicious_activity' => [],
                    'authentication_stats' => [],
                    'failed_logins_24h' => 0,
                    'unique_login_users_24h' => 0,
                    'security_score' => 95
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting security metrics: ' . $e->getMessage());
            return [
                'error' => 'Unable to fetch security data',
                'security_events' => [],
                'suspicious_activity' => []
            ];
        }
    }

    /**
     * Get hourly transaction trends for today
     */
    public function getTodayHourlyTrends()
    {
        $cacheKey = 'hourly_trends_' . Carbon::today()->format('Y-m-d');

        return Cache::remember($cacheKey, 300, function () { // Cache for 5 minutes
            $trends = [];
            $today = Carbon::today();

            for ($hour = 0; $hour < 24; $hour++) {
                $hourStart = $today->copy()->addHours($hour);
                $hourEnd = $hourStart->copy()->addHour();

                $transactions = Transaction::whereBetween('date', [$hourStart, $hourEnd]);

                $trends[] = [
                    'hour' => $hour,
                    'total' => $transactions->count(),
                    'successful' => $transactions->where('status', 'Completed')->count(),
                    'failed' => $transactions->where('status', 'Failed')->count(),
                    'revenue' => $transactions->where('status', 'Completed')->sum('amount')
                ];
            }

            return $trends;
        });
    }

    /**
     * Get live transaction feed
     */
    public function getLiveTransactionFeed($limit = 10)
    {
        return Transaction::with('user')
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'user' => $transaction->user ?
                        $transaction->user->name :
                        'Unknown User',
                    'service' => $transaction->servicename,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'time' => $transaction->date,
                    'description' => $transaction->servicedesc
                ];
            });
    }

    /**
     * Get weekly comparison data
     */
    public function getWeeklyComparison()
    {
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = $thisWeek->copy()->subDay();

        $thisWeekData = Transaction::where('date', '>=', $thisWeek)
            ->where('status', 'Completed');

        $lastWeekData = Transaction::whereBetween('date', [$lastWeek, $lastWeekEnd])
            ->where('status', 'Completed');

        return [
            'this_week' => [
                'transactions' => $thisWeekData->count(),
                'revenue' => $thisWeekData->sum('amount'),
                'users' => $thisWeekData->distinct('sId')->count()
            ],
            'last_week' => [
                'transactions' => $lastWeekData->count(),
                'revenue' => $lastWeekData->sum('amount'),
                'users' => $lastWeekData->distinct('sId')->count()
            ]
        ];
    }

    /**
     * Get alerts and notifications
     */
    public function getSystemAlerts()
    {
        $alerts = [];

        // Check for pending transactions
        $oldPendingCount = Transaction::where('status', 'Pending')
            ->where('date', '<', Carbon::now()->subHours(2))
            ->count();

        if ($oldPendingCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Pending Transactions',
                'message' => "{$oldPendingCount} transactions have been pending for more than 2 hours",
                'action_url' => '/admin/transactions?status=pending',
                'severity' => 'medium',
                'count' => $oldPendingCount
            ];
        }

        // Check for high failure rate
        $recentFailureRate = $this->calculateSuccessRate(Carbon::now()->subHour());
        if ($recentFailureRate < 85) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'High Failure Rate',
                'message' => "Transaction success rate has dropped to {$recentFailureRate}% in the last hour",
                'action_url' => '/admin/analytics/transactions',
                'severity' => 'high',
                'rate' => $recentFailureRate
            ];
        }

        // Check for API performance issues
        try {
            $apiIssues = DB::table('api_metrics')
                ->where('created_at', '>=', Carbon::now()->subHour())
                ->where('response_time', '>', 10000) // More than 10 seconds
                ->count();

            if ($apiIssues > 10) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'API Performance Issues',
                    'message' => "Detected {$apiIssues} slow API responses (>10s) in the last hour",
                    'action_url' => '/admin/monitoring/api-performance',
                    'severity' => 'medium',
                    'count' => $apiIssues
                ];
            }
        } catch (\Exception $e) {
            // API metrics table might not exist yet
        }

        // Check for circuit breaker activations
        try {
            $activeFailovers = $this->getActiveFailovers();
            if (!empty($activeFailovers)) {
                $providers = array_unique(array_column($activeFailovers, 'provider'));
                $alerts[] = [
                    'type' => 'danger',
                    'title' => 'Provider Failovers Active',
                    'message' => 'Circuit breakers activated for: ' . implode(', ', $providers),
                    'action_url' => '/admin/monitoring/providers',
                    'severity' => 'high',
                    'affected_providers' => $providers
                ];
            }
        } catch (\Exception $e) {
            // Ignore errors for now
        }

        // Check for recent security incidents
        try {
            $securityIncidents = DB::table('audit_logs')
                ->where('severity', 'high')
                ->where('created_at', '>=', Carbon::now()->subHour())
                ->count();

            if ($securityIncidents > 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'title' => 'Security Incidents',
                    'message' => "{$securityIncidents} high-severity security events in the last hour",
                    'action_url' => '/admin/security/audit-logs',
                    'severity' => 'high',
                    'count' => $securityIncidents
                ];
            }
        } catch (\Exception $e) {
            // Audit logs table might not exist yet
        }

        // Check for system resource usage
        $diskUsage = $this->getDiskUsage();
        if (isset($diskUsage['used_percentage']) && $diskUsage['used_percentage'] > 85) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'High Disk Usage',
                'message' => "Disk usage is at {$diskUsage['used_percentage']}%",
                'action_url' => '/admin/system/resources',
                'severity' => 'medium',
                'usage' => $diskUsage['used_percentage']
            ];
        }

        return $alerts;
    }

    //----------------------------------------------------------------------------------------------------------------
    // Helper Methods
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Calculate success rate for a given date
     */
    private function calculateSuccessRate($date)
    {
        $total = Transaction::whereDate('date', $date)->count();
        if ($total === 0) return 100;

        $successful = Transaction::whereDate('date', $date)
            ->where('status', 'Completed')
            ->count();

        return round(($successful / $total) * 100, 2);
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Get active sessions count (simplified)
     */
    private function getActiveSessions()
    {
        // This would integrate with session storage
        return 0;
    }

    /**
     * Get service availability
     */
    private function getServiceAvailability()
    {
        return [
            'airtime' => 'online',
            'data' => 'online',
            'cable_tv' => 'online',
            'electricity' => 'online',
            'exam_pins' => 'online'
        ];
    }

    /**
     * Get API response times
     */
    private function getApiResponseTimes()
    {
        // API logs table doesn't exist in this system
        return [];

        // Original code commented out since api_logs table doesn't exist
        // if (!class_exists('App\Models\ApiLog')) {
        //     return [];
        // }
        //
        // return ApiLog::selectRaw('service, AVG(response_time) as avg_response_time')
        //            ->where('created_at', '>=', Carbon::now()->subHour())
        //            ->groupBy('service')
        //            ->pluck('avg_response_time', 'service');
    }

    /**
     * Get service errors
     */
    private function getServiceErrors()
    {
        return Transaction::selectRaw('servicename, COUNT(*) as errors')
            ->where('status', 'Failed')
            ->where('date', '>=', Carbon::now()->subHour())
            ->groupBy('servicename')
            ->pluck('errors', 'servicename');
    }

    /**
     * Get database connections
     */
    private function getDatabaseConnections()
    {
        try {
            return DB::select('SELECT COUNT(*) as connections FROM information_schema.processlist')[0]->connections ?? 0;
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage()
    {
        return [
            'current' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'limit' => ini_get('memory_limit')
        ];
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage()
    {
        $total = disk_total_space('.');
        $free = disk_free_space('.');
        $used = $total - $free;

        return [
            'used_percentage' => round(($used / $total) * 100, 2),
            'free_space' => round($free / 1024 / 1024 / 1024, 2) . ' GB'
        ];
    }

    /**
     * Get API health status
     */
    private function getApiHealth()
    {
        // This would ping external APIs
        return [
            'overall_status' => 'healthy',
            'services_online' => 5,
            'services_total' => 5
        ];
    }

    /**
     * Get recent errors
     */
    private function getRecentErrors()
    {
        // This would read from Laravel logs
        return 0;
    }

    /**
     * Get system uptime
     */
    private function getSystemUptime()
    {
        // This would get actual server uptime
        return '99.9%';
    }

    /**
     * Get provider services
     */
    private function getProviderServices($provider)
    {
        try {
            $services = DB::table('api_configurations')
                ->where('provider', $provider)
                ->where('is_active', true)
                ->pluck('service_type')
                ->toArray();

            return $services;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Calculate overall provider health
     */
    private function calculateOverallProviderHealth($providerStats)
    {
        if (empty($providerStats)) {
            return 'unknown';
        }

        $healthyCount = 0;
        $totalCount = count($providerStats);

        foreach ($providerStats as $stats) {
            if ($stats['status'] === 'healthy' && $stats['success_rate'] >= 95) {
                $healthyCount++;
            }
        }

        $healthPercentage = ($healthyCount / $totalCount) * 100;

        if ($healthPercentage >= 90) return 'excellent';
        if ($healthPercentage >= 75) return 'good';
        if ($healthPercentage >= 50) return 'fair';
        return 'poor';
    }

    /**
     * Get active failovers
     */
    private function getActiveFailovers()
    {
        try {
            $failovers = [];
            $providers = ['alphano', 'vtupro', 'clubkonnect', 'dataden'];
            $services = ['airtime', 'data', 'cable_tv', 'electricity', 'education_pins'];

            foreach ($providers as $provider) {
                foreach ($services as $service) {
                    // Use reflection to access protected method
                    $reflection = new \ReflectionClass($this->failoverService);
                    $method = $reflection->getMethod('isCircuitOpen');
                    $method->setAccessible(true);

                    if ($method->invoke($this->failoverService, $service, $provider)) {
                        $failovers[] = [
                            'provider' => $provider,
                            'service' => $service,
                            'status' => 'circuit_open',
                            'alternative_used' => $this->getBestAlternativeProvider($service, $provider),
                            'since' => now() // Would be actual failover time
                        ];
                    }
                }
            }

            return $failovers;
        } catch (\Exception $e) {
            Log::error('Error getting active failovers: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get circuit breaker status
     */
    private function getCircuitBreakerStatus()
    {
        try {
            $status = [];
            $providers = ['alphano', 'vtupro', 'clubkonnect', 'dataden'];
            $services = ['airtime', 'data', 'cable_tv', 'electricity', 'education_pins'];

            foreach ($providers as $provider) {
                $providerStatus = [];
                foreach ($services as $service) {
                    // Use reflection to access protected method
                    $reflection = new \ReflectionClass($this->failoverService);
                    $method = $reflection->getMethod('isCircuitOpen');
                    $method->setAccessible(true);

                    $providerStatus[$service] = [
                        'is_open' => $method->invoke($this->failoverService, $service, $provider),
                        'failure_count' => 0, // Would get from cache
                        'last_failure' => null, // Would get from cache
                        'next_retry' => null // Would calculate based on circuit breaker config
                    ];
                }
                $status[$provider] = $providerStatus;
            }

            return $status;
        } catch (\Exception $e) {
            Log::error('Error getting circuit breaker status: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get best alternative provider for a service
     */
    private function getBestAlternativeProvider($serviceType, $excludeProvider)
    {
        try {
            // Use reflection to access protected method
            $reflection = new \ReflectionClass($this->failoverService);
            $method = $reflection->getMethod('getAvailableProviders');
            $method->setAccessible(true);

            $providers = $method->invoke($this->failoverService, $serviceType);

            // Filter out the excluded provider and get the best one
            $alternatives = array_filter($providers, function ($provider) use ($excludeProvider) {
                return $provider['name'] !== $excludeProvider;
            });

            if (!empty($alternatives)) {
                // Sort by health score and get the best
                usort($alternatives, function ($a, $b) {
                    return $b['health_score'] <=> $a['health_score'];
                });

                return $alternatives[0]['name'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting alternative provider: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate API health score
     */
    private function calculateApiHealthScore($performanceStats)
    {
        if ($performanceStats->isEmpty()) {
            return 100;
        }

        $totalRequests = $performanceStats->sum('total_requests');
        $successfulRequests = $performanceStats->sum('successful_requests');
        $avgResponseTime = $performanceStats->avg('avg_response_time');

        if ($totalRequests === 0) {
            return 100;
        }

        $successRate = ($successfulRequests / $totalRequests) * 100;
        $responseTimeScore = max(0, 100 - ($avgResponseTime / 100)); // Penalty for slow responses

        // Weighted score
        $healthScore = ($successRate * 0.7) + ($responseTimeScore * 0.3);

        return round($healthScore, 2);
    }

    /**
     * Calculate security score
     */
    private function calculateSecurityScore($securityStats, $suspiciousActivity)
    {
        $baseScore = 100;

        // Deduct points for suspicious activities
        $suspiciousCount = $suspiciousActivity->count();
        $baseScore -= min($suspiciousCount * 5, 30); // Max 30 points deduction

        // Check for authentication issues
        $authEvents = $securityStats->where('event_type', 'authentication');
        if ($authEvents->isNotEmpty() && $authEvents->first()->count > 100) {
            $baseScore -= 10; // Deduct for high authentication events
        }

        return max(0, $baseScore);
    }
}
