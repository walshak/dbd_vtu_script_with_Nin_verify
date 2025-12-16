<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CableTVService;

class TestCableTvService extends Command
{
    protected $signature = 'test:cable';
    protected $description = 'Test Cable TV service integration with real external APIs';

    public function handle()
    {
        $this->info('Testing Cable TV Service Integration...');

        $cableTvService = app(CableTVService::class);

        // Test 1: Validate IUC Number
        $this->info("\n1. Testing IUC Validation:");
        $iucResult = $cableTvService->validateIUC('dstv', '1234567890');
        $this->info("IUC Validation: " . json_encode($iucResult, JSON_PRETTY_PRINT));

        // Test 2: Get Cable Plans
        $this->info("\n2. Testing Cable Plans Retrieval:");
        $userId = 1; // Assuming user ID 1 exists
        $plansResult = $cableTvService->getCablePlans($userId, 'dstv');
        $this->info("Cable Plans: " . json_encode($plansResult, JSON_PRETTY_PRINT));

        // Test 3: Get Available Decoders
        $this->info("\n3. Testing Available Decoders:");
        $decodersResult = $cableTvService->getAvailableDecoders();
        $this->info("Available Decoders: " . json_encode($decodersResult, JSON_PRETTY_PRINT));

        $this->info("\nCable TV Service Test Complete!");
    }
}
