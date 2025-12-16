<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            // Monnify Settings
            ['config_key' => 'monifyCharges', 'config_value' => '1.075'],
            ['config_key' => 'monifyApi', 'config_value' => 'MK_PROD_JAJ'],
            ['config_key' => 'monifySecrete', 'config_value' => '4YBNAZ8XY1'],
            ['config_key' => 'monifyContract', 'config_value' => '5812563'],
            ['config_key' => 'monifyWeStatus', 'config_value' => 'On'],
            ['config_key' => 'monifyMoStatus', 'config_value' => 'On'],
            ['config_key' => 'monifyFeStatus', 'config_value' => 'Off'],
            ['config_key' => 'monifySaStatus', 'config_value' => 'On'],
            ['config_key' => 'monifyStatus', 'config_value' => 'On'],

            // Paystack Settings
            ['config_key' => 'paystackCharges', 'config_value' => '1.5'],
            ['config_key' => 'paystackApi', 'config_value' => ''],
            ['config_key' => 'paystackStatus', 'config_value' => 'Off'],

            // Wallet Provider Settings (Three wallets for API management)
            ['config_key' => 'walletOneProviderName', 'config_value' => 'Maskawasub'],
            ['config_key' => 'walletOneApi', 'config_value' => 'e5199989c9df406e8f78f9b255ab5620e131e2b4'],
            ['config_key' => 'walletOneProvider', 'config_value' => 'https://maskawasub.com/api/user/'],

            ['config_key' => 'walletTwoProviderName', 'config_value' => 'Topupmate'],
            ['config_key' => 'walletTwoApi', 'config_value' => ''],
            ['config_key' => 'walletTwoProvider', 'config_value' => 'https://topupmate.com/api/user/'],

            ['config_key' => 'walletThreeProviderName', 'config_value' => 'Aabaxztech'],
            ['config_key' => 'walletThreeApi', 'config_value' => ''],
            ['config_key' => 'walletThreeProvider', 'config_value' => 'https://aabaxztech.com/api/user/'],
        ];

        foreach ($configurations as $config) {
            \App\Models\Configuration::updateOrCreate(
                ['config_key' => $config['config_key']],
                ['config_value' => $config['config_value']]
            );
        }
    }
}
