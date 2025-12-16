<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ApiConfiguration;
use App\Models\ApiLink;

class ApiConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed API Links (provider URLs) - using only base columns from migration
        $apiLinks = [
            // Airtime providers
            ['name' => 'N3TDATA', 'type' => 'Airtime', 'value' => 'https://n3tdata.com/api/topup'],
            ['name' => 'BilalSadaSub', 'type' => 'Airtime', 'value' => 'https://bilalsadasub.com/api/topup'],

            // Data providers
            ['name' => 'N3TDATA', 'type' => 'Data', 'value' => 'https://n3tdata.com/api/data'],
            ['name' => 'BilalSadaSub', 'type' => 'Data', 'value' => 'https://bilalsadasub.com/api/data'],

            // Cable providers
            ['name' => 'N3TDATA', 'type' => 'Cable', 'value' => 'https://n3tdata.com/api/cablesub'],
            ['name' => 'BilalSadaSub', 'type' => 'Cable', 'value' => 'https://bilalsadasub.com/api/cable'],

            // Cable verification
            ['name' => 'N3TDATA', 'type' => 'CableVer', 'value' => 'https://n3tdata.com/api/validate-customer'],

            // Electricity providers
            ['name' => 'N3TDATA', 'type' => 'Electricity', 'value' => 'https://n3tdata.com/api/electricity'],
            ['name' => 'BilalSadaSub', 'type' => 'Electricity', 'value' => 'https://bilalsadasub.com/api/electricity'],

            // Electricity verification
            ['name' => 'N3TDATA', 'type' => 'ElectricityVer', 'value' => 'https://n3tdata.com/api/validate-customer'],

            // Exam providers
            ['name' => 'N3TDATA', 'type' => 'Exam', 'value' => 'https://n3tdata.com/api/exam'],

            // Data Pin providers
            ['name' => 'N3TDATA', 'type' => 'Data Pin', 'value' => 'https://n3tdata.com/api/datapin'],
        ];

        foreach ($apiLinks as $link) {
            ApiLink::create($link);
        }

        // Seed API Configurations
        $networks = ['MTN', 'AIRTEL', 'GLO', '9MOBILE'];

        // Network-specific configurations
        foreach ($networks as $network) {
            foreach (['VTU', 'ShareSell'] as $providerType) {
                ApiConfiguration::updateOrCreate(
                    ['config_key' => strtolower($network) . $providerType . 'Key'],
                    [
                        'config_value' => '',
                        'service_type' => 'airtime',
                        'network' => $network,
                        'provider_type' => $providerType,
                        'description' => "{$network} {$providerType} API Key"
                    ]
                );

                ApiConfiguration::updateOrCreate(
                    ['config_key' => strtolower($network) . $providerType . 'Provider'],
                    [
                        'config_value' => '',
                        'service_type' => 'airtime',
                        'network' => $network,
                        'provider_type' => $providerType,
                        'description' => "{$network} {$providerType} Provider URL"
                    ]
                );
            }

            foreach (['SME', 'Corporate', 'Gifting'] as $dataType) {
                ApiConfiguration::updateOrCreate(
                    ['config_key' => strtolower($network) . $dataType . 'Api'],
                    [
                        'config_value' => '',
                        'service_type' => 'data',
                        'network' => $network,
                        'provider_type' => $dataType,
                        'description' => "{$network} {$dataType} Data API Key"
                    ]
                );

                ApiConfiguration::updateOrCreate(
                    ['config_key' => strtolower($network) . $dataType . 'Provider'],
                    [
                        'config_value' => '',
                        'service_type' => 'data',
                        'network' => $network,
                        'provider_type' => $dataType,
                        'description' => "{$network} {$dataType} Data Provider URL"
                    ]
                );
            }
        }

        // Service-specific configurations
        $services = [
            'cable' => [
                ['key' => 'cableVerificationApi', 'desc' => 'Cable TV IUC Verification API Key'],
                ['key' => 'cableVerificationProvider', 'desc' => 'Cable TV IUC Verification Provider URL'],
                ['key' => 'cableApi', 'desc' => 'Cable TV API Key'],
                ['key' => 'cableProvider', 'desc' => 'Cable TV Provider URL'],
            ],
            'electricity' => [
                ['key' => 'meterVerificationApi', 'desc' => 'Electricity Meter Verification API Key'],
                ['key' => 'meterVerificationProvider', 'desc' => 'Electricity Meter Verification Provider URL'],
                ['key' => 'meterApi', 'desc' => 'Electricity API Key'],
                ['key' => 'meterProvider', 'desc' => 'Electricity Provider URL'],
            ],
            'exam' => [
                ['key' => 'examApi', 'desc' => 'Exam Checker API Key'],
                ['key' => 'examProvider', 'desc' => 'Exam Checker Provider URL'],
            ],
            'recharge_pin' => [
                ['key' => 'rechargePinApi', 'desc' => 'Recharge Card API Key'],
                ['key' => 'rechargePinProvider', 'desc' => 'Recharge Card Provider URL'],
            ],
            'data_pin' => [
                ['key' => 'dataPinApi', 'desc' => 'Data Pin API Key'],
                ['key' => 'dataPinProvider', 'desc' => 'Data Pin Provider URL'],
            ],
            'alpha_topup' => [
                ['key' => 'alphaApi', 'desc' => 'Alpha Topup API Key'],
                ['key' => 'alphaProvider', 'desc' => 'Alpha Topup Provider URL'],
            ],
        ];

        foreach ($services as $serviceType => $configs) {
            foreach ($configs as $config) {
                ApiConfiguration::updateOrCreate(
                    ['config_key' => $config['key']],
                    [
                        'config_value' => '',
                        'service_type' => $serviceType,
                        'description' => $config['desc']
                    ]
                );
            }
        }
    }
}
