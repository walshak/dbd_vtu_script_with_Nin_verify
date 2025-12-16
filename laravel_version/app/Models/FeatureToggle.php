<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FeatureToggle extends Model
{
    use HasFactory;

    protected $table = 'feature_toggles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'feature_name',
        'feature_key',
        'is_enabled',
        'description',
        'environment',
        'rollout_percentage',
        'target_users',
        'start_date',
        'end_date',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'rollout_percentage' => 'integer',
        'target_users' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'metadata' => 'array'
    ];

    // Feature constants
    const FEATURE_KYC_VERIFICATION = 'kyc_verification';
    const FEATURE_REFERRAL_SYSTEM = 'referral_system';
    const FEATURE_WALLET_TO_WALLET = 'wallet_to_wallet';
    const FEATURE_API_MONITORING = 'api_monitoring';
    const FEATURE_REAL_TIME_NOTIFICATIONS = 'real_time_notifications';
    const FEATURE_ADVANCED_ANALYTICS = 'advanced_analytics';
    const FEATURE_AUTO_RECONCILIATION = 'auto_reconciliation';
    const FEATURE_BULK_OPERATIONS = 'bulk_operations';
    const FEATURE_MAINTENANCE_MODE = 'maintenance_mode';
    const FEATURE_DEBUG_MODE = 'debug_mode';

    /**
     * Check if a feature is enabled
     */
    public static function isEnabled($featureKey, $userId = null)
    {
        $cacheKey = "feature_toggle_{$featureKey}" . ($userId ? "_{$userId}" : '');
        
        return Cache::remember($cacheKey, 300, function () use ($featureKey, $userId) {
            $toggle = static::where('feature_key', $featureKey)->first();
            
            if (!$toggle) {
                return false; // Feature not found, disabled by default
            }

            // Check if feature is globally disabled
            if (!$toggle->is_enabled) {
                return false;
            }

            // Check environment
            if ($toggle->environment && $toggle->environment !== app()->environment()) {
                return false;
            }

            // Check date range
            $now = now();
            if ($toggle->start_date && $now->lt($toggle->start_date)) {
                return false;
            }
            if ($toggle->end_date && $now->gt($toggle->end_date)) {
                return false;
            }

            // Check rollout percentage
            if ($toggle->rollout_percentage < 100 && $userId) {
                $userHash = crc32($featureKey . $userId) % 100;
                if ($userHash >= $toggle->rollout_percentage) {
                    return false;
                }
            }

            // Check target users
            if ($toggle->target_users && $userId && !in_array($userId, $toggle->target_users)) {
                return false;
            }

            return true;
        });
    }

    /**
     * Enable a feature
     */
    public static function enable($featureKey, $options = [])
    {
        $toggle = static::updateOrCreate(
            ['feature_key' => $featureKey],
            array_merge([
                'is_enabled' => true,
                'rollout_percentage' => 100
            ], $options)
        );

        static::clearCache($featureKey);
        return $toggle;
    }

    /**
     * Disable a feature
     */
    public static function disable($featureKey)
    {
        $toggle = static::where('feature_key', $featureKey)->first();
        
        if ($toggle) {
            $toggle->update(['is_enabled' => false]);
            static::clearCache($featureKey);
        }

        return $toggle;
    }

    /**
     * Set rollout percentage for gradual feature rollout
     */
    public static function setRollout($featureKey, $percentage, $options = [])
    {
        $toggle = static::updateOrCreate(
            ['feature_key' => $featureKey],
            array_merge([
                'is_enabled' => true,
                'rollout_percentage' => max(0, min(100, $percentage))
            ], $options)
        );

        static::clearCache($featureKey);
        return $toggle;
    }

    /**
     * Set target users for feature
     */
    public static function setTargetUsers($featureKey, array $userIds, $options = [])
    {
        $toggle = static::updateOrCreate(
            ['feature_key' => $featureKey],
            array_merge([
                'is_enabled' => true,
                'target_users' => $userIds,
                'rollout_percentage' => 0 // Disable percentage when using target users
            ], $options)
        );

        static::clearCache($featureKey);
        return $toggle;
    }

    /**
     * Get all enabled features
     */
    public static function getEnabledFeatures()
    {
        return static::where('is_enabled', true)
                    ->where(function($query) {
                        $query->whereNull('start_date')
                              ->orWhere('start_date', '<=', now());
                    })
                    ->where(function($query) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', now());
                    })
                    ->pluck('feature_key')
                    ->toArray();
    }

    /**
     * Get feature status for user
     */
    public static function getUserFeatures($userId)
    {
        $features = static::all();
        $userFeatures = [];

        foreach ($features as $feature) {
            $userFeatures[$feature->feature_key] = static::isEnabled($feature->feature_key, $userId);
        }

        return $userFeatures;
    }

    /**
     * Clear feature cache
     */
    public static function clearCache($featureKey = null)
    {
        if ($featureKey) {
            Cache::forget("feature_toggle_{$featureKey}");
            // Clear user-specific caches (simplified)
            for ($i = 1; $i <= 1000; $i++) {
                Cache::forget("feature_toggle_{$featureKey}_{$i}");
            }
        } else {
            Cache::flush(); // Clear all cache (use cautiously)
        }
    }

    /**
     * Create default feature toggles
     */
    public static function createDefaults()
    {
        $defaults = [
            [
                'feature_name' => 'KYC Verification',
                'feature_key' => self::FEATURE_KYC_VERIFICATION,
                'description' => 'Enable KYC verification system for users',
                'is_enabled' => true,
                'rollout_percentage' => 100
            ],
            [
                'feature_name' => 'Referral System',
                'feature_key' => self::FEATURE_REFERRAL_SYSTEM,
                'description' => 'Enable referral bonuses and tracking',
                'is_enabled' => true,
                'rollout_percentage' => 100
            ],
            [
                'feature_name' => 'Wallet to Wallet Transfers',
                'feature_key' => self::FEATURE_WALLET_TO_WALLET,
                'description' => 'Allow users to transfer funds between wallets',
                'is_enabled' => true,
                'rollout_percentage' => 100
            ],
            [
                'feature_name' => 'API Monitoring',
                'feature_key' => self::FEATURE_API_MONITORING,
                'description' => 'Enable advanced API monitoring and fallback',
                'is_enabled' => true,
                'rollout_percentage' => 100
            ],
            [
                'feature_name' => 'Real-time Notifications',
                'feature_key' => self::FEATURE_REAL_TIME_NOTIFICATIONS,
                'description' => 'Enable real-time push notifications',
                'is_enabled' => false,
                'rollout_percentage' => 0
            ],
            [
                'feature_name' => 'Advanced Analytics',
                'feature_key' => self::FEATURE_ADVANCED_ANALYTICS,
                'description' => 'Enable advanced analytics and reporting',
                'is_enabled' => true,
                'rollout_percentage' => 100
            ],
            [
                'feature_name' => 'Auto Reconciliation',
                'feature_key' => self::FEATURE_AUTO_RECONCILIATION,
                'description' => 'Enable automatic transaction reconciliation',
                'is_enabled' => false,
                'rollout_percentage' => 0
            ],
            [
                'feature_name' => 'Bulk Operations',
                'feature_key' => self::FEATURE_BULK_OPERATIONS,
                'description' => 'Enable bulk transaction operations for admins',
                'is_enabled' => true,
                'rollout_percentage' => 100
            ],
            [
                'feature_name' => 'Maintenance Mode',
                'feature_key' => self::FEATURE_MAINTENANCE_MODE,
                'description' => 'Put system in maintenance mode',
                'is_enabled' => false,
                'rollout_percentage' => 0
            ],
            [
                'feature_name' => 'Debug Mode',
                'feature_key' => self::FEATURE_DEBUG_MODE,
                'description' => 'Enable debug logging and error display',
                'is_enabled' => false,
                'rollout_percentage' => 0
            ]
        ];

        foreach ($defaults as $default) {
            static::updateOrCreate(
                ['feature_key' => $default['feature_key']],
                $default
            );
        }
    }

    /**
     * Get feature statistics
     */
    public static function getFeatureStats()
    {
        return [
            'total_features' => static::count(),
            'enabled_features' => static::where('is_enabled', true)->count(),
            'disabled_features' => static::where('is_enabled', false)->count(),
            'rollout_features' => static::where('rollout_percentage', '<', 100)
                                       ->where('rollout_percentage', '>', 0)->count(),
            'targeted_features' => static::whereNotNull('target_users')->count(),
            'scheduled_features' => static::where(function($query) {
                $query->whereNotNull('start_date')
                      ->orWhereNotNull('end_date');
            })->count()
        ];
    }

    /**
     * Scope for enabled features
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope for disabled features
     */
    public function scopeDisabled($query)
    {
        return $query->where('is_enabled', false);
    }

    /**
     * Scope for active features (considering date range)
     */
    public function scopeActive($query)
    {
        return $query->where('is_enabled', true)
                    ->where(function($query) {
                        $query->whereNull('start_date')
                              ->orWhere('start_date', '<=', now());
                    })
                    ->where(function($query) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', now());
                    });
    }

    /**
     * Get creator information
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}