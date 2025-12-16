<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DataPlan;

class DataPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataPlans = [
            // MTN SME Plans
            ['dPlanId' => 'mtn-sme-500mb', 'nId' => 1, 'dPlan' => 'MTN 500MB', 'dAmount' => '500MB', 'dValidity' => '30 Days', 'userPrice' => 135, 'agentPrice' => 125, 'apiPrice' => 120, 'dGroup' => 'SME'],
            ['dPlanId' => 'mtn-sme-1gb', 'nId' => 1, 'dPlan' => 'MTN 1GB', 'dAmount' => '1GB', 'dValidity' => '30 Days', 'userPrice' => 270, 'agentPrice' => 250, 'apiPrice' => 240, 'dGroup' => 'SME'],
            ['dPlanId' => 'mtn-sme-2gb', 'nId' => 1, 'dPlan' => 'MTN 2GB', 'dAmount' => '2GB', 'dValidity' => '30 Days', 'userPrice' => 540, 'agentPrice' => 500, 'apiPrice' => 480, 'dGroup' => 'SME'],
            ['dPlanId' => 'mtn-sme-5gb', 'nId' => 1, 'dPlan' => 'MTN 5GB', 'dAmount' => '5GB', 'dValidity' => '30 Days', 'userPrice' => 1350, 'agentPrice' => 1250, 'apiPrice' => 1200, 'dGroup' => 'SME'],
            ['dPlanId' => 'mtn-sme-10gb', 'nId' => 1, 'dPlan' => 'MTN 10GB', 'dAmount' => '10GB', 'dValidity' => '30 Days', 'userPrice' => 2700, 'agentPrice' => 2500, 'apiPrice' => 2400, 'dGroup' => 'SME'],

            // MTN Gifting Plans
            ['dPlanId' => 'mtn-gift-500mb', 'nId' => 1, 'dPlan' => 'MTN 500MB Gifting', 'dAmount' => '500MB', 'dValidity' => '30 Days', 'userPrice' => 145, 'agentPrice' => 135, 'apiPrice' => 130, 'dGroup' => 'Gifting'],
            ['dPlanId' => 'mtn-gift-1gb', 'nId' => 1, 'dPlan' => 'MTN 1GB Gifting', 'dAmount' => '1GB', 'dValidity' => '30 Days', 'userPrice' => 290, 'agentPrice' => 270, 'apiPrice' => 260, 'dGroup' => 'Gifting'],
            ['dPlanId' => 'mtn-gift-2gb', 'nId' => 1, 'dPlan' => 'MTN 2GB Gifting', 'dAmount' => '2GB', 'dValidity' => '30 Days', 'userPrice' => 580, 'agentPrice' => 540, 'apiPrice' => 520, 'dGroup' => 'Gifting'],

            // Airtel SME Plans
            ['dPlanId' => 'airtel-sme-500mb', 'nId' => 2, 'dPlan' => 'Airtel 500MB', 'dAmount' => '500MB', 'dValidity' => '30 Days', 'userPrice' => 140, 'agentPrice' => 130, 'apiPrice' => 125, 'dGroup' => 'SME'],
            ['dPlanId' => 'airtel-sme-1gb', 'nId' => 2, 'dPlan' => 'Airtel 1GB', 'dAmount' => '1GB', 'dValidity' => '30 Days', 'userPrice' => 280, 'agentPrice' => 260, 'apiPrice' => 250, 'dGroup' => 'SME'],
            ['dPlanId' => 'airtel-sme-2gb', 'nId' => 2, 'dPlan' => 'Airtel 2GB', 'dAmount' => '2GB', 'dValidity' => '30 Days', 'userPrice' => 560, 'agentPrice' => 520, 'apiPrice' => 500, 'dGroup' => 'SME'],
            ['dPlanId' => 'airtel-sme-5gb', 'nId' => 2, 'dPlan' => 'Airtel 5GB', 'dAmount' => '5GB', 'dValidity' => '30 Days', 'userPrice' => 1400, 'agentPrice' => 1300, 'apiPrice' => 1250, 'dGroup' => 'SME'],

            // Glo SME Plans
            ['dPlanId' => 'glo-sme-500mb', 'nId' => 3, 'dPlan' => 'Glo 500MB', 'dAmount' => '500MB', 'dValidity' => '30 Days', 'userPrice' => 130, 'agentPrice' => 120, 'apiPrice' => 115, 'dGroup' => 'SME'],
            ['dPlanId' => 'glo-sme-1gb', 'nId' => 3, 'dPlan' => 'Glo 1GB', 'dAmount' => '1GB', 'dValidity' => '30 Days', 'userPrice' => 260, 'agentPrice' => 240, 'apiPrice' => 230, 'dGroup' => 'SME'],
            ['dPlanId' => 'glo-sme-2gb', 'nId' => 3, 'dPlan' => 'Glo 2GB', 'dAmount' => '2GB', 'dValidity' => '30 Days', 'userPrice' => 520, 'agentPrice' => 480, 'apiPrice' => 460, 'dGroup' => 'SME'],
            ['dPlanId' => 'glo-sme-5gb', 'nId' => 3, 'dPlan' => 'Glo 5GB', 'dAmount' => '5GB', 'dValidity' => '30 Days', 'userPrice' => 1300, 'agentPrice' => 1200, 'apiPrice' => 1150, 'dGroup' => 'SME'],

            // 9mobile SME Plans
            ['dPlanId' => '9mobile-sme-500mb', 'nId' => 4, 'dPlan' => '9mobile 500MB', 'dAmount' => '500MB', 'dValidity' => '30 Days', 'userPrice' => 135, 'agentPrice' => 125, 'apiPrice' => 120, 'dGroup' => 'SME'],
            ['dPlanId' => '9mobile-sme-1gb', 'nId' => 4, 'dPlan' => '9mobile 1GB', 'dAmount' => '1GB', 'dValidity' => '30 Days', 'userPrice' => 270, 'agentPrice' => 250, 'apiPrice' => 240, 'dGroup' => 'SME'],
            ['dPlanId' => '9mobile-sme-2gb', 'nId' => 4, 'dPlan' => '9mobile 2GB', 'dAmount' => '2GB', 'dValidity' => '30 Days', 'userPrice' => 540, 'agentPrice' => 500, 'apiPrice' => 480, 'dGroup' => 'SME'],
        ];

        foreach ($dataPlans as $plan) {
            DataPlan::updateOrCreate(
                ['dPlanId' => $plan['dPlanId']],
                $plan
            );
        }
    }
}
