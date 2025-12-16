<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExternalApiService;
use App\Services\ConfigurationService;

class TestElectricityService extends Command
{
    protected $signature = 'test:electricity';
    protected $description = 'Test Electricity service integration with real external APIs';

    public function handle()
    {
        $this->info('Testing Electricity Service Integration...');

        // Test configuration first
        $this->info("\n1. Testing Configuration:");
        $configService = app(ConfigurationService::class);
        $electricityConfig = $configService->getServiceConfig('electricity');
        $this->info("Electricity config: " . json_encode($electricityConfig, JSON_PRETTY_PRINT));

        // Test External API Service directly
        $this->info("\n2. Testing External API Service - Meter Verification:");
        $externalApiService = app(ExternalApiService::class);
        $meterResult = $externalApiService->verifyMeter('12345678901', 'prepaid', 'aedc');
        $this->info("Meter Verification: " . json_encode($meterResult, JSON_PRETTY_PRINT));

        // Test External API Service - Electricity Purchase
        $this->info("\n3. Testing External API Service - Electricity Purchase:");
        $purchaseResult = $externalApiService->purchaseElectricity('aedc', '12345678901', 'prepaid', 5000, 'TEST_REF_' . time());
        $this->info("Electricity Purchase: " . json_encode($purchaseResult, JSON_PRETTY_PRINT));

        $this->info("\nElectricity Service Test Complete!");
    }
}
