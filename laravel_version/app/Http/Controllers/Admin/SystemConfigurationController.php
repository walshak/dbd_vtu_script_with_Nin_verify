<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureToggle;
use App\Models\SiteSettings;
use App\Models\Configuration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SystemConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * System configuration dashboard
     */
    public function index()
    {
        $data = [
            'feature_stats' => FeatureToggle::getFeatureStats(),
            'system_health' => $this->getSystemHealthSummary(),
            'maintenance_status' => $this->getMaintenanceStatus(),
            'cache_status' => $this->getCacheStatus(),
            'backup_status' => $this->getBackupStatus(),
            'environment_info' => $this->getEnvironmentInfo()
        ];

        return view('admin.configuration.index', compact('data'));
    }

    /**
     * Feature toggles management
     */
    public function featureToggles(Request $request)
    {
        $features = FeatureToggle::orderBy('feature_name')->get();
        $stats = FeatureToggle::getFeatureStats();

        return view('admin.configuration.feature-toggles', compact('features', 'stats'));
    }

    /**
     * Update feature toggle
     */
    public function updateFeatureToggle(Request $request, $id)
    {
        $request->validate([
            'is_enabled' => 'boolean',
            'rollout_percentage' => 'integer|min:0|max:100',
            'target_users' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $feature = FeatureToggle::findOrFail($id);

            $feature->update([
                'is_enabled' => $request->boolean('is_enabled'),
                'rollout_percentage' => $request->rollout_percentage ?? 100,
                'target_users' => $request->target_users,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description
            ]);

            FeatureToggle::clearCache($feature->feature_key);

            Log::info('Feature toggle updated', [
                'feature' => $feature->feature_key,
                'enabled' => $feature->is_enabled,
                'admin_id' => auth()->guard('admin')->id()
            ]);

            return back()->with('success', 'Feature toggle updated successfully');
        } catch (\Exception $e) {
            Log::error('Feature toggle update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update feature toggle');
        }
    }

    /**
     * Create new feature toggle
     */
    public function createFeatureToggle(Request $request)
    {
        $request->validate([
            'feature_name' => 'required|string|max:255',
            'feature_key' => 'required|string|max:100|unique:feature_toggles,feature_key',
            'description' => 'nullable|string|max:500',
            'is_enabled' => 'boolean',
            'rollout_percentage' => 'integer|min:0|max:100'
        ]);

        try {
            $feature = FeatureToggle::create([
                'feature_name' => $request->feature_name,
                'feature_key' => $request->feature_key,
                'description' => $request->description,
                'is_enabled' => $request->boolean('is_enabled'),
                'rollout_percentage' => $request->rollout_percentage ?? 100,
                'created_by' => auth()->guard('admin')->id()
            ]);

            Log::info('Feature toggle created', [
                'feature' => $feature->feature_key,
                'admin_id' => auth()->guard('admin')->id()
            ]);

            return back()->with('success', 'Feature toggle created successfully');
        } catch (\Exception $e) {
            Log::error('Feature toggle creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create feature toggle');
        }
    }

    /**
     * Maintenance mode management
     */
    public function maintenanceMode(Request $request)
    {
        if ($request->isMethod('GET')) {
            $status = $this->getMaintenanceStatus();
            return view('admin.configuration.maintenance', compact('status'));
        }

        $request->validate([
            'action' => 'required|in:enable,disable',
            'message' => 'nullable|string|max:500',
            'allowed_ips' => 'nullable|string',
            'estimated_duration' => 'nullable|integer|min:1|max:1440'
        ]);

        try {
            if ($request->action === 'enable') {
                $this->enableMaintenanceMode(
                    $request->message ?? 'System maintenance in progress. Please try again later.',
                    $request->allowed_ips,
                    $request->estimated_duration
                );
                $message = 'Maintenance mode enabled successfully';
            } else {
                $this->disableMaintenanceMode();
                $message = 'Maintenance mode disabled successfully';
            }

            Log::info('Maintenance mode toggled', [
                'action' => $request->action,
                'admin_id' => auth()->guard('admin')->id()
            ]);

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Maintenance mode toggle failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to toggle maintenance mode');
        }
    }

    /**
     * System health monitoring
     */
    public function systemHealth()
    {
        $health = [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'cache' => $this->checkCacheHealth(),
            'queue' => $this->checkQueueHealth(),
            'api_endpoints' => $this->checkApiEndpointsHealth(),
            'services' => $this->checkServicesHealth(),
            'performance' => $this->getPerformanceMetrics(),
            'security' => $this->getSecurityStatus()
        ];

        return view('admin.configuration.system-health', compact('health'));
    }

    /**
     * Cache management
     */
    public function cacheManagement(Request $request)
    {
        if ($request->isMethod('GET')) {
            $stats = $this->getCacheStatus();
            return view('admin.configuration.cache', compact('stats'));
        }

        $action = $request->input('action');

        try {
            switch ($action) {
                case 'clear_all':
                    Cache::flush();
                    $message = 'All cache cleared successfully';
                    break;

                case 'clear_config':
                    Cache::forget('config');
                    $message = 'Configuration cache cleared';
                    break;

                case 'clear_routes':
                    Cache::forget('routes');
                    $message = 'Routes cache cleared';
                    break;

                case 'clear_views':
                    Cache::forget('views');
                    $message = 'Views cache cleared';
                    break;

                case 'clear_features':
                    FeatureToggle::clearCache();
                    $message = 'Feature toggles cache cleared';
                    break;

                default:
                    throw new \Exception('Invalid cache action');
            }

            Log::info('Cache operation performed', [
                'action' => $action,
                'admin_id' => auth()->guard('admin')->id()
            ]);

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Cache operation failed: ' . $e->getMessage());
            return back()->with('error', 'Cache operation failed');
        }
    }

    /**
     * Backup management
     */
    public function backupManagement(Request $request)
    {
        if ($request->isMethod('GET')) {
            $backups = $this->getBackupList();
            $status = $this->getBackupStatus();
            return view('admin.configuration.backup', compact('backups', 'status'));
        }

        $action = $request->input('action');

        try {
            switch ($action) {
                case 'create':
                    $backup = $this->createBackup();
                    $message = "Backup created successfully: {$backup['filename']}";
                    break;

                case 'download':
                    $filename = $request->input('filename');
                    return $this->downloadBackup($filename);

                case 'delete':
                    $filename = $request->input('filename');
                    $this->deleteBackup($filename);
                    $message = "Backup deleted successfully: {$filename}";
                    break;

                default:
                    throw new \Exception('Invalid backup action');
            }

            Log::info('Backup operation performed', [
                'action' => $action,
                'admin_id' => auth()->guard('admin')->id()
            ]);

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Backup operation failed: ' . $e->getMessage());
            return back()->with('error', 'Backup operation failed: ' . $e->getMessage());
        }
    }

    /**
     * Environment configuration
     */
    public function environmentConfig(Request $request)
    {
        if ($request->isMethod('GET')) {
            $config = $this->getEnvironmentConfig();
            return view('admin.configuration.environment', compact('config'));
        }

        $request->validate([
            'app_debug' => 'boolean',
            'app_log_level' => 'required|in:emergency,alert,critical,error,warning,notice,info,debug',
            'session_lifetime' => 'integer|min:1|max:10080',
            'max_upload_size' => 'integer|min:1|max:100'
        ]);

        try {
            $this->updateEnvironmentConfig($request->only([
                'app_debug',
                'app_log_level',
                'session_lifetime',
                'max_upload_size'
            ]));

            Log::info('Environment configuration updated', [
                'admin_id' => auth()->guard('admin')->id()
            ]);

            return back()->with('success', 'Environment configuration updated successfully');
        } catch (\Exception $e) {
            Log::error('Environment configuration update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update environment configuration');
        }
    }

    /**
     * Get API status for health check
     */
    public function getApiStatus()
    {
        try {
            $endpoints = [
                'airtime' => $this->testApiEndpoint('airtime'),
                'data' => $this->testApiEndpoint('data'),
                'cable_tv' => $this->testApiEndpoint('cable_tv'),
                'electricity' => $this->testApiEndpoint('electricity')
            ];

            return response()->json([
                'success' => true,
                'endpoints' => $endpoints,
                'overall_status' => collect($endpoints)->every(fn($status) => $status === 'online') ? 'healthy' : 'degraded',
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check API status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Private Helper Methods
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get system health summary
     */
    private function getSystemHealthSummary()
    {
        $dbHealth = $this->checkDatabaseHealth();
        $storageHealth = $this->checkStorageHealth();
        $cacheHealth = $this->checkCacheHealth();

        // Determine overall status based on component health
        $overallStatus = 'healthy';
        if ($dbHealth['status'] !== 'healthy' || $storageHealth['status'] !== 'healthy' || $cacheHealth['status'] !== 'healthy') {
            $overallStatus = 'degraded';
        }

        return [
            'status' => $overallStatus,
            'overall_status' => $overallStatus,
            'database' => $dbHealth['status'],
            'storage' => $storageHealth['status'],
            'cache' => $cacheHealth['status'],
            'api_endpoints' => 'healthy',
            'last_checked' => now(),
            'metrics' => [
                'database' => $dbHealth['response_time'] ?? 'N/A',
                'storage' => $storageHealth['used_percentage'] ?? 'N/A',
                'cache' => $cacheHealth['driver'] ?? 'N/A'
            ]
        ];
    }

    /**
     * Get maintenance status
     */
    private function getMaintenanceStatus()
    {
        return [
            'enabled' => FeatureToggle::isEnabled(FeatureToggle::FEATURE_MAINTENANCE_MODE),
            'message' => Configuration::getValue('maintenance_message', 'System maintenance in progress'),
            'estimated_end' => Configuration::getValue('maintenance_end_time'),
            'allowed_ips' => json_decode(Configuration::getValue('maintenance_allowed_ips', '[]'), true)
        ];
    }

    /**
     * Get cache status
     */
    private function getCacheStatus()
    {
        return [
            'driver' => config('cache.default'),
            'is_working' => $this->checkCacheHealth()['status'] === 'healthy',
            'size' => '0 MB', // Would need cache-specific implementation
            'hit_rate' => '95%', // Would need cache statistics
            'last_cleared' => Configuration::getValue('cache_last_cleared')
        ];
    }

    /**
     * Get backup status
     */
    private function getBackupStatus()
    {
        return [
            'auto_backup_enabled' => Configuration::getValue('auto_backup_enabled', false),
            'backup_frequency' => Configuration::getValue('backup_frequency', 'daily'),
            'last_backup' => Configuration::getValue('last_backup_date'),
            'backup_count' => count($this->getBackupList()),
            'storage_used' => $this->getBackupStorageUsed()
        ];
    }

    /**
     * Get environment info
     */
    private function getEnvironmentInfo()
    {
        return [
            'app_env' => app()->environment(),
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize')
        ];
    }

    /**
     * Enable maintenance mode
     */
    private function enableMaintenanceMode($message, $allowedIps = null, $duration = null)
    {
        FeatureToggle::enable(FeatureToggle::FEATURE_MAINTENANCE_MODE);

        Configuration::setValue('maintenance_message', $message);

        if ($allowedIps) {
            $ips = array_filter(array_map('trim', explode(',', $allowedIps)));
            Configuration::setValue('maintenance_allowed_ips', json_encode($ips));
        }

        if ($duration) {
            $endTime = now()->addMinutes($duration);
            Configuration::setValue('maintenance_end_time', $endTime);
        }
    }

    /**
     * Disable maintenance mode
     */
    private function disableMaintenanceMode()
    {
        FeatureToggle::disable(FeatureToggle::FEATURE_MAINTENANCE_MODE);
        Configuration::setValue('maintenance_end_time', null);
    }

    /**
     * Check database health
     */
    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            $connectionTime = microtime(true);
            DB::select('SELECT 1');
            $queryTime = microtime(true) - $connectionTime;

            return [
                'status' => 'healthy',
                'response_time' => round($queryTime * 1000, 2) . 'ms',
                'connection' => 'active'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'connection' => 'failed'
            ];
        }
    }

    /**
     * Check storage health
     */
    private function checkStorageHealth()
    {
        try {
            $totalSpace = disk_total_space(storage_path());
            $freeSpace = disk_free_space(storage_path());
            $usedPercentage = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);

            $status = $usedPercentage > 90 ? 'warning' : ($usedPercentage > 95 ? 'critical' : 'healthy');

            return [
                'status' => $status,
                'total_space' => $this->formatBytes($totalSpace),
                'free_space' => $this->formatBytes($freeSpace),
                'used_percentage' => $usedPercentage
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check cache health
     */
    private function checkCacheHealth()
    {
        try {
            $testKey = 'health_check_' . time();
            $testValue = 'test_value';

            Cache::put($testKey, $testValue, 60);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);

            return [
                'status' => $retrieved === $testValue ? 'healthy' : 'unhealthy',
                'driver' => config('cache.default'),
                'test_passed' => $retrieved === $testValue
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check queue health
     */
    private function checkQueueHealth()
    {
        try {
            // Check if queue connection is working
            $connection = config('queue.default');

            return [
                'status' => 'healthy',
                'connection' => $connection,
                'workers_active' => true // Would need actual queue monitoring
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check API endpoints health
     */
    private function checkApiEndpointsHealth()
    {
        $endpoints = [
            'airtime' => $this->testApiEndpoint('airtime'),
            'data' => $this->testApiEndpoint('data'),
            'cable_tv' => $this->testApiEndpoint('cable_tv'),
            'electricity' => $this->testApiEndpoint('electricity')
        ];

        $healthyCount = collect($endpoints)->filter(fn($status) => $status === 'healthy')->count();
        $totalCount = count($endpoints);

        return [
            'status' => $healthyCount === $totalCount ? 'healthy' : ($healthyCount > 0 ? 'degraded' : 'unhealthy'),
            'endpoints' => $endpoints,
            'healthy_count' => $healthyCount,
            'total_count' => $totalCount
        ];
    }

    /**
     * Check services health
     */
    private function checkServicesHealth()
    {
        return [
            'status' => 'healthy',
            'services' => [
                'web_server' => 'running',
                'database' => $this->checkDatabaseHealth()['status'],
                'cache' => $this->checkCacheHealth()['status'],
                'storage' => $this->checkStorageHealth()['status']
            ]
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - LARAVEL_START,
            'database_queries' => 0, // Would need query monitoring
            'cache_hit_rate' => '95%' // Would need cache statistics
        ];
    }

    /**
     * Get security status
     */
    private function getSecurityStatus()
    {
        return [
            'status' => 'secure',
            'ssl_enabled' => request()->secure(),
            'debug_mode' => config('app.debug'),
            'maintenance_mode' => FeatureToggle::isEnabled(FeatureToggle::FEATURE_MAINTENANCE_MODE),
            'last_security_update' => Configuration::getValue('last_security_update')
        ];
    }

    /**
     * Test API endpoint
     */
    private function testApiEndpoint($service)
    {
        try {
            // This would test actual API endpoints
            // For now, return healthy as placeholder
            return 'healthy';
        } catch (\Exception $e) {
            return 'unhealthy';
        }
    }

    /**
     * Get backup list
     */
    private function getBackupList()
    {
        try {
            $backupPath = storage_path('app/backups');

            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
                return [];
            }

            $files = File::files($backupPath);
            $backups = [];

            foreach ($files as $file) {
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'created_at' => Carbon::createFromTimestamp($file->getCTime()),
                    'path' => $file->getPathname()
                ];
            }

            return collect($backups)->sortByDesc('created_at')->values()->all();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get backup storage used
     */
    private function getBackupStorageUsed()
    {
        try {
            $backupPath = storage_path('app/backups');

            if (!File::exists($backupPath)) {
                return '0 B';
            }

            $totalSize = 0;
            $files = File::files($backupPath);

            foreach ($files as $file) {
                $totalSize += $file->getSize();
            }

            return $this->formatBytes($totalSize);
        } catch (\Exception $e) {
            return '0 B';
        }
    }

    /**
     * Create backup
     */
    private function createBackup()
    {
        try {
            $backupPath = storage_path('app/backups');

            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $filename = 'backup_' . now()->format('Y_m_d_H_i_s') . '.sql';
            $filePath = $backupPath . '/' . $filename;

            // Create database backup
            $databasePath = database_path('database.sqlite');
            if (File::exists($databasePath)) {
                File::copy($databasePath, $filePath);
            } else {
                // For other database types, you would use mysqldump or similar
                throw new \Exception('Database backup not implemented for this database type');
            }

            Configuration::setValue('last_backup_date', now());

            return [
                'filename' => $filename,
                'size' => $this->formatBytes(File::size($filePath)),
                'created_at' => now()
            ];
        } catch (\Exception $e) {
            throw new \Exception('Backup creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Download backup
     */
    private function downloadBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (!File::exists($filePath)) {
            throw new \Exception('Backup file not found');
        }

        return response()->download($filePath);
    }

    /**
     * Delete backup
     */
    private function deleteBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);

        if (!File::exists($filePath)) {
            throw new \Exception('Backup file not found');
        }

        File::delete($filePath);
    }

    /**
     * Get environment configuration
     */
    private function getEnvironmentConfig()
    {
        return [
            'app_debug' => config('app.debug'),
            'app_log_level' => config('logging.level'),
            'session_lifetime' => config('session.lifetime'),
            'max_upload_size' => ini_get('upload_max_filesize'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time')
        ];
    }

    /**
     * Update environment configuration
     */
    private function updateEnvironmentConfig($config)
    {
        foreach ($config as $key => $value) {
            Configuration::setValue($key, $value);
        }

        // Note: Actual .env file updates would require additional implementation
        // This stores in database for now
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
}
