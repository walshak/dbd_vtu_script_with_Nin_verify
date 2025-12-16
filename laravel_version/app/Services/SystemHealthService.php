<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;

class SystemHealthService
{
    const CACHE_TTL = 300; // 5 minutes
    const HEALTH_CACHE_KEY = 'system_health_status';

    /**
     * Get comprehensive system health status
     */
    public function getHealthStatus(): array
    {
        return Cache::remember(self::HEALTH_CACHE_KEY, self::CACHE_TTL, function () {
            return [
                'overall_status' => $this->calculateOverallStatus(),
                'components' => [
                    'database' => $this->checkDatabaseHealth(),
                    'storage' => $this->checkStorageHealth(),
                    'cache' => $this->checkCacheHealth(),
                    'queue' => $this->checkQueueHealth(),
                    'api_endpoints' => $this->checkApiEndpointsHealth(),
                    'external_services' => $this->checkExternalServicesHealth(),
                    'application' => $this->checkApplicationHealth()
                ],
                'metrics' => [
                    'response_times' => $this->getResponseTimeMetrics(),
                    'error_rates' => $this->getErrorRateMetrics(),
                    'resource_usage' => $this->getResourceUsageMetrics(),
                    'transaction_health' => $this->getTransactionHealthMetrics()
                ],
                'alerts' => $this->getActiveAlerts(),
                'last_checked' => now(),
                'uptime' => $this->getSystemUptime()
            ];
        });
    }

    /**
     * Check database health
     */
    public function checkDatabaseHealth(): array
    {
        try {
            $startTime = microtime(true);
            
            // Test connection
            DB::connection()->getPdo();
            
            // Test query performance
            $queryStart = microtime(true);
            $userCount = User::count();
            $queryTime = microtime(true) - $queryStart;
            
            $connectionTime = microtime(true) - $startTime;
            
            // Check database size
            $dbSize = $this->getDatabaseSize();
            
            // Check slow queries
            $slowQueries = $this->getSlowQueryCount();
            
            $status = 'healthy';
            if ($connectionTime > 1.0 || $queryTime > 0.5) {
                $status = 'warning';
            }
            if ($connectionTime > 5.0 || $queryTime > 2.0) {
                $status = 'critical';
            }

            return [
                'status' => $status,
                'connection_time' => round($connectionTime * 1000, 2),
                'query_time' => round($queryTime * 1000, 2),
                'total_users' => $userCount,
                'database_size' => $this->formatBytes($dbSize),
                'slow_queries' => $slowQueries,
                'details' => [
                    'driver' => config('database.default'),
                    'host' => config('database.connections.sqlite.database') ? 'SQLite' : 'Unknown',
                    'last_backup' => SiteSettings::getSetting('last_backup_date'),
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Database health check failed: ' . $e->getMessage());
            return [
                'status' => 'critical',
                'error' => $e->getMessage(),
                'connection_time' => null,
                'query_time' => null
            ];
        }
    }

    /**
     * Check storage health
     */
    public function checkStorageHealth(): array
    {
        try {
            $storagePath = storage_path();
            $totalSpace = disk_total_space($storagePath);
            $freeSpace = disk_free_space($storagePath);
            $usedSpace = $totalSpace - $freeSpace;
            $usedPercentage = ($usedSpace / $totalSpace) * 100;

            // Check write permissions
            $testFile = $storagePath . '/health_check_' . time() . '.tmp';
            $writeTest = file_put_contents($testFile, 'test');
            $canWrite = $writeTest !== false;
            if ($canWrite) {
                unlink($testFile);
            }

            $status = 'healthy';
            if ($usedPercentage > 85) {
                $status = 'warning';
            }
            if ($usedPercentage > 95 || !$canWrite) {
                $status = 'critical';
            }

            return [
                'status' => $status,
                'total_space' => $this->formatBytes($totalSpace),
                'free_space' => $this->formatBytes($freeSpace),
                'used_space' => $this->formatBytes($usedSpace),
                'used_percentage' => round($usedPercentage, 2),
                'can_write' => $canWrite,
                'details' => [
                    'logs_size' => $this->getDirectorySize(storage_path('logs')),
                    'cache_size' => $this->getDirectorySize(storage_path('framework/cache')),
                    'temp_files' => $this->countTempFiles()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Storage health check failed: ' . $e->getMessage());
            return [
                'status' => 'critical',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check cache health
     */
    public function checkCacheHealth(): array
    {
        try {
            $driver = config('cache.default');
            $testKey = 'health_check_' . time();
            $testValue = 'test_value_' . uniqid();
            
            // Test write/read
            $writeStart = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $writeTime = microtime(true) - $writeStart;
            
            $readStart = microtime(true);
            $retrieved = Cache::get($testKey);
            $readTime = microtime(true) - $readStart;
            
            Cache::forget($testKey);
            
            $isWorking = $retrieved === $testValue;
            
            $status = 'healthy';
            if (!$isWorking || $writeTime > 0.1 || $readTime > 0.1) {
                $status = 'warning';
            }
            if (!$isWorking || $writeTime > 1.0 || $readTime > 1.0) {
                $status = 'critical';
            }

            return [
                'status' => $status,
                'driver' => $driver,
                'is_working' => $isWorking,
                'write_time' => round($writeTime * 1000, 2),
                'read_time' => round($readTime * 1000, 2),
                'details' => [
                    'hit_rate' => $this->getCacheHitRate(),
                    'memory_usage' => $this->getCacheMemoryUsage()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Cache health check failed: ' . $e->getMessage());
            return [
                'status' => 'critical',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check queue health
     */
    public function checkQueueHealth(): array
    {
        try {
            $connection = config('queue.default');
            
            // Get queue statistics
            $pendingJobs = $this->getPendingJobsCount();
            $failedJobs = $this->getFailedJobsCount();
            $processedToday = $this->getProcessedJobsToday();
            
            $status = 'healthy';
            if ($pendingJobs > 1000 || $failedJobs > 100) {
                $status = 'warning';
            }
            if ($pendingJobs > 5000 || $failedJobs > 500) {
                $status = 'critical';
            }

            return [
                'status' => $status,
                'connection' => $connection,
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
                'processed_today' => $processedToday,
                'details' => [
                    'workers_running' => $this->getRunningWorkersCount(),
                    'average_wait_time' => $this->getAverageWaitTime()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Queue health check failed: ' . $e->getMessage());
            return [
                'status' => 'critical',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check API endpoints health
     */
    public function checkApiEndpointsHealth(): array
    {
        try {
            $endpoints = [
                'airtime' => $this->testInternalEndpoint('/api/airtime/networks'),
                'data' => $this->testInternalEndpoint('/api/data/plans'),
                'cable_tv' => $this->testInternalEndpoint('/api/cable-tv/providers'),
                'electricity' => $this->testInternalEndpoint('/api/electricity/providers'),
                'user_auth' => $this->testInternalEndpoint('/api/user/profile')
            ];

            $healthyCount = collect($endpoints)->filter(fn($status) => $status['status'] === 'healthy')->count();
            $totalCount = count($endpoints);
            $healthPercentage = ($healthyCount / $totalCount) * 100;

            $status = 'healthy';
            if ($healthPercentage < 80) {
                $status = 'warning';
            }
            if ($healthPercentage < 50) {
                $status = 'critical';
            }

            return [
                'status' => $status,
                'healthy_endpoints' => $healthyCount,
                'total_endpoints' => $totalCount,
                'health_percentage' => round($healthPercentage, 2),
                'endpoints' => $endpoints,
                'last_api_error' => $this->getLastApiError()
            ];
        } catch (\Exception $e) {
            Log::error('API endpoints health check failed: ' . $e->getMessage());
            return [
                'status' => 'critical',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check external services health
     */
    public function checkExternalServicesHealth(): array
    {
        try {
            $services = [
                'payment_gateway' => $this->testPaymentGateway(),
                'sms_gateway' => $this->testSmsGateway(),
                'email_service' => $this->testEmailService(),
                'airtime_provider' => $this->testAirtimeProvider(),
                'data_provider' => $this->testDataProvider()
            ];

            $healthyCount = collect($services)->filter(fn($status) => $status['status'] === 'healthy')->count();
            $totalCount = count($services);
            $healthPercentage = ($healthyCount / $totalCount) * 100;

            $status = 'healthy';
            if ($healthPercentage < 80) {
                $status = 'warning';
            }
            if ($healthPercentage < 50) {
                $status = 'critical';
            }

            return [
                'status' => $status,
                'healthy_services' => $healthyCount,
                'total_services' => $totalCount,
                'health_percentage' => round($healthPercentage, 2),
                'services' => $services
            ];
        } catch (\Exception $e) {
            Log::error('External services health check failed: ' . $e->getMessage());
            return [
                'status' => 'critical',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check application health
     */
    public function checkApplicationHealth(): array
    {
        try {
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = $this->parseBytes(ini_get('memory_limit'));
            $memoryPercentage = ($memoryUsage / $memoryLimit) * 100;

            $cpuLoad = $this->getCpuLoad();
            
            $activeUsers = $this->getActiveUsersCount();
            $recentErrors = $this->getRecentErrorsCount();

            $status = 'healthy';
            if ($memoryPercentage > 80 || $cpuLoad > 80 || $recentErrors > 50) {
                $status = 'warning';
            }
            if ($memoryPercentage > 95 || $cpuLoad > 95 || $recentErrors > 200) {
                $status = 'critical';
            }

            return [
                'status' => $status,
                'memory_usage' => $this->formatBytes($memoryUsage),
                'memory_limit' => $this->formatBytes($memoryLimit),
                'memory_percentage' => round($memoryPercentage, 2),
                'cpu_load' => $cpuLoad,
                'active_users' => $activeUsers,
                'recent_errors' => $recentErrors,
                'details' => [
                    'php_version' => phpversion(),
                    'laravel_version' => app()->version(),
                    'environment' => app()->environment(),
                    'debug_mode' => config('app.debug')
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Application health check failed: ' . $e->getMessage());
            return [
                'status' => 'critical',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get active alerts
     */
    public function getActiveAlerts(): array
    {
        $alerts = [];

        // Check for high error rates
        $errorCount = $this->getRecentErrorsCount();
        if ($errorCount > 100) {
            $alerts[] = [
                'type' => 'error_rate',
                'severity' => $errorCount > 500 ? 'critical' : 'warning',
                'message' => "High error rate detected: {$errorCount} errors in the last hour",
                'timestamp' => now()
            ];
        }

        // Check for failed transactions
        $failedTransactions = Transaction::where('status', 'failed')
            ->where('created_at', '>', now()->subHour())
            ->count();
            
        if ($failedTransactions > 10) {
            $alerts[] = [
                'type' => 'transaction_failures',
                'severity' => $failedTransactions > 50 ? 'critical' : 'warning',
                'message' => "High transaction failure rate: {$failedTransactions} failed transactions in the last hour",
                'timestamp' => now()
            ];
        }

        // Check storage space
        $usedPercentage = $this->getStorageUsedPercentage();
        if ($usedPercentage > 85) {
            $alerts[] = [
                'type' => 'storage_space',
                'severity' => $usedPercentage > 95 ? 'critical' : 'warning',
                'message' => "Storage space running low: {$usedPercentage}% used",
                'timestamp' => now()
            ];
        }

        return $alerts;
    }

    /**
     * Calculate overall system status
     */
    private function calculateOverallStatus(): string
    {
        $components = [
            $this->checkDatabaseHealth(),
            $this->checkStorageHealth(),
            $this->checkCacheHealth(),
            $this->checkQueueHealth(),
            $this->checkApiEndpointsHealth(),
            $this->checkApplicationHealth()
        ];

        $statuses = collect($components)->pluck('status');

        if ($statuses->contains('critical')) {
            return 'critical';
        }

        if ($statuses->contains('warning')) {
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Additional helper methods for health checks...
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function parseBytes($value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;
        
        switch ($last) {
            case 'g':
                $value *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $value *= 1024 * 1024;
                break;
            case 'k':
                $value *= 1024;
                break;
        }
        
        return $value;
    }

    // Additional helper methods would be implemented here for:
    // - getDatabaseSize()
    // - getSlowQueryCount()
    // - getDirectorySize()
    // - countTempFiles()
    // - getCacheHitRate()
    // - getCacheMemoryUsage()
    // - getPendingJobsCount()
    // - getFailedJobsCount()
    // - testInternalEndpoint()
    // - testPaymentGateway()
    // - etc.
}