<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiLink;
use App\Models\ApiConfiguration;
use Illuminate\Support\Facades\DB;

class DefaultApiProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $apiUrl = config('services.uzobest.url');
        $apiKey = config('services.uzobest.key');

        if (empty($apiUrl) || empty($apiKey)) {
            $this->command->error('Uzobest API credentials not found in config/services.php');
            return;
        }

        $this->command->info('Seeding Uzobest API settings...');

        DB::transaction(function () use ($apiUrl, $apiKey) {
            // Create or Update API Links for Uzobest as Priority #1 Provider
            // Uzobest is the primary/default API provider

            // Base URL Link for airtime
            ApiLink::updateOrCreate(
                ['value' => $apiUrl, 'type' => 'Airtime'],
                [
                    'name' => 'Uzobest',
                    'is_active' => true,
                    'priority' => 1,
                    'auth_type' => 'header',
                    'auth_params' => ['header_name' => 'Authorization', 'header_prefix' => 'Token '],
                    'success_rate' => 100.00,
                ]
            );

            // Data URL Link
            ApiLink::updateOrCreate(
                ['value' => $apiUrl . '/data/', 'type' => 'Data'],
                [
                    'name' => 'Uzobest',
                    'is_active' => true,
                    'priority' => 1,
                    'auth_type' => 'header',
                    'auth_params' => ['header_name' => 'Authorization', 'header_prefix' => 'Token '],
                    'success_rate' => 100.00,
                ]
            );

            // Cable URL Link
            ApiLink::updateOrCreate(
                ['value' => $apiUrl . '/cabletv/', 'type' => 'Cable'],
                [
                    'name' => 'Uzobest',
                    'is_active' => true,
                    'priority' => 1,
                    'auth_type' => 'header',
                    'auth_params' => ['header_name' => 'Authorization', 'header_prefix' => 'Token '],
                    'success_rate' => 100.00,
                ]
            );

            // Electricity URL Link
            ApiLink::updateOrCreate(
                ['value' => $apiUrl . '/electricity/', 'type' => 'Electricity'],
                [
                    'name' => 'Uzobest',
                    'is_active' => true,
                    'priority' => 1,
                    'auth_type' => 'header',
                    'auth_params' => ['header_name' => 'Authorization', 'header_prefix' => 'Token '],
                    'success_rate' => 100.00,
                ]
            );

            // Exam URL Link
            ApiLink::updateOrCreate(
                ['value' => $apiUrl . '/exam/', 'type' => 'Exam'],
                [
                    'name' => 'Uzobest',
                    'is_active' => true,
                    'priority' => 1,
                    'auth_type' => 'header',
                    'auth_params' => ['header_name' => 'Authorization', 'header_prefix' => 'Token '],
                    'success_rate' => 100.00,
                ]
            );

            // Cable Verification
            ApiLink::updateOrCreate(
                ['value' => $apiUrl . '/validate-customer/', 'type' => 'CableVer'],
                [
                    'name' => 'Uzobest',
                    'is_active' => true,
                    'priority' => 1,
                    'auth_type' => 'header',
                    'auth_params' => ['header_name' => 'Authorization', 'header_prefix' => 'Token '],
                    'success_rate' => 100.00,
                ]
            );

            // Electricity Verification
            ApiLink::updateOrCreate(
                ['value' => $apiUrl . '/validate-customer/', 'type' => 'ElectricityVer'],
                [
                    'name' => 'Uzobest',
                    'is_active' => true,
                    'priority' => 1,
                    'auth_type' => 'header',
                    'auth_params' => ['header_name' => 'Authorization', 'header_prefix' => 'Token '],
                    'success_rate' => 100.00,
                ]
            );            // Update ApiConfiguration with Uzobest API key and URLs
            $networks = ['MTN', 'AIRTEL', 'GLO', '9MOBILE'];
            $airtimeTypes = ['VTU', 'ShareSell'];
            $dataTypes = ['SME', 'Corporate', 'Gifting'];

            // Airtime configurations
            foreach ($networks as $network) {
                foreach ($airtimeTypes as $type) {
                    ApiConfiguration::updateOrCreate(
                        ['config_key' => strtolower($network) . $type . 'Key'],
                        ['config_value' => $apiKey]
                    );

                    ApiConfiguration::updateOrCreate(
                        ['config_key' => strtolower($network) . $type . 'Provider'],
                        ['config_value' => $apiUrl]
                    );
                }

                // Data configurations
                foreach ($dataTypes as $type) {
                    ApiConfiguration::updateOrCreate(
                        ['config_key' => strtolower($network) . $type . 'Api'],
                        ['config_value' => $apiKey]
                    );

                    ApiConfiguration::updateOrCreate(
                        ['config_key' => strtolower($network) . $type . 'Provider'],
                        ['config_value' => $apiUrl . '/data/']
                    );
                }
            }

            // Service-specific configurations
            $services = [
                ['key' => 'cableApi', 'value' => $apiKey],
                ['key' => 'cableProvider', 'value' => $apiUrl . '/cabletv/'],
                ['key' => 'cableVerificationApi', 'value' => $apiKey],
                ['key' => 'cableVerificationProvider', 'value' => $apiUrl . '/validate-customer/'],
                ['key' => 'meterApi', 'value' => $apiKey],
                ['key' => 'meterProvider', 'value' => $apiUrl . '/electricity/'],
                ['key' => 'meterVerificationApi', 'value' => $apiKey],
                ['key' => 'meterVerificationProvider', 'value' => $apiUrl . '/validate-customer/'],
                ['key' => 'examApi', 'value' => $apiKey],
                ['key' => 'examProvider', 'value' => $apiUrl . '/exam/'],
            ];

            foreach ($services as $service) {
                ApiConfiguration::updateOrCreate(
                    ['config_key' => $service['key']],
                    ['config_value' => $service['value']]
                );
            }
        });

        $this->command->info('✓ Uzobest API settings seeded successfully!');
    }
}
