<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeatureToggle;
use Carbon\Carbon;

class FeatureToggleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'feature_name' => 'KYC Verification',
                'feature_key' => FeatureToggle::FEATURE_KYC_VERIFICATION,
                'is_enabled' => true,
                'rollout_percentage' => 100,
                'metadata' => json_encode([
                    'description' => 'Enable KYC verification for user accounts',
                    'category' => 'security',
                    'required_permissions' => ['admin', 'kyc_manager']
                ])
            ],
            [
                'feature_name' => 'Referral System',
                'feature_key' => FeatureToggle::FEATURE_REFERRAL_SYSTEM,
                'is_enabled' => true,
                'rollout_percentage' => 100,
                'metadata' => json_encode([
                    'description' => 'Enable user referral system with rewards',
                    'category' => 'marketing',
                    'reward_percentage' => 5
                ])
            ],
            [
                'feature_name' => 'Wallet to Wallet Transfer',
                'feature_key' => FeatureToggle::FEATURE_WALLET_TO_WALLET,
                'is_enabled' => false,
                'rollout_percentage' => 0,
                'metadata' => json_encode([
                    'description' => 'Enable wallet-to-wallet transfers between users',
                    'category' => 'payments',
                    'min_amount' => 100,
                    'max_amount' => 50000,
                    'daily_limit' => 200000
                ])
            ],
            [
                'feature_name' => 'API Monitoring',
                'feature_key' => FeatureToggle::FEATURE_API_MONITORING,
                'is_enabled' => true,
                'rollout_percentage' => 100,
                'metadata' => json_encode([
                    'description' => 'Monitor API performance and usage',
                    'category' => 'system',
                    'alert_threshold' => 95
                ])
            ],
            [
                'feature_name' => 'Real-time Notifications',
                'feature_key' => FeatureToggle::FEATURE_REAL_TIME_NOTIFICATIONS,
                'is_enabled' => true,
                'rollout_percentage' => 80,
                'metadata' => json_encode([
                    'description' => 'Enable real-time push notifications',
                    'category' => 'communication',
                    'channels' => ['push', 'email', 'sms']
                ])
            ],
            [
                'feature_name' => 'Advanced Analytics',
                'feature_key' => FeatureToggle::FEATURE_ADVANCED_ANALYTICS,
                'is_enabled' => true,
                'rollout_percentage' => 100,
                'metadata' => json_encode([
                    'description' => 'Enable advanced analytics and reporting',
                    'category' => 'analytics',
                    'retention_days' => 365
                ])
            ],
            [
                'feature_name' => 'Auto Reconciliation',
                'feature_key' => FeatureToggle::FEATURE_AUTO_RECONCILIATION,
                'is_enabled' => false,
                'rollout_percentage' => 0,
                'metadata' => json_encode([
                    'description' => 'Automatically reconcile transactions',
                    'category' => 'financial',
                    'reconcile_interval' => 'hourly'
                ])
            ],
            [
                'feature_name' => 'Bulk Operations',
                'feature_key' => FeatureToggle::FEATURE_BULK_OPERATIONS,
                'is_enabled' => true,
                'rollout_percentage' => 100,
                'metadata' => json_encode([
                    'description' => 'Enable bulk operations for admins',
                    'category' => 'admin',
                    'max_batch_size' => 1000
                ])
            ],
            [
                'feature_name' => 'Maintenance Mode',
                'feature_key' => FeatureToggle::FEATURE_MAINTENANCE_MODE,
                'is_enabled' => false,
                'rollout_percentage' => 0,
                'metadata' => json_encode([
                    'description' => 'Put system in maintenance mode',
                    'category' => 'system',
                    'bypass_ips' => ['127.0.0.1']
                ])
            ],
            [
                'feature_name' => 'Debug Mode',
                'feature_key' => FeatureToggle::FEATURE_DEBUG_MODE,
                'is_enabled' => false,
                'rollout_percentage' => 0,
                'metadata' => json_encode([
                    'description' => 'Enable debug mode for development',
                    'category' => 'development',
                    'log_level' => 'debug'
                ])
            ],
        ];

        foreach ($features as $feature) {
            FeatureToggle::updateOrCreate(
                ['feature_key' => $feature['feature_key']],
                array_merge($feature, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ])
            );
        }

        $this->command->info('Feature toggles seeded successfully!');
    }
}
