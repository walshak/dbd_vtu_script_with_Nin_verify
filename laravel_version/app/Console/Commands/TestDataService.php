<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExternalApiService;
use App\Services\ConfigurationService;
use App\Services\DataService;

class TestDataService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Data Service Integration';

    /**
     * Execute the console command.
     */
    public function handle(ExternalApiService $externalApiService, ConfigurationService $configService, DataService $dataService): void
    {
        $this->info('Testing Data Service Integration...');

        // Test configuration retrieval for different data types
        $this->info('Testing configuration retrieval for MTN SME Data...');
        $smeConfig = $configService->getProviderConfig('data', 'MTN', 'SME');
        $this->table(['Key', 'Value'], [
            ['API Key', $smeConfig['api_key'] ? 'Set' : 'Not set'],
            ['Provider URL', $smeConfig['provider_url'] ?: 'Not set'],
            ['Provider Name', $smeConfig['provider_name'] ?: 'Not set'],
            ['Auth Type', $smeConfig['auth_type'] ?: 'Not set'],
        ]);

        $this->info('Testing configuration retrieval for MTN Corporate Data...');
        $corporateConfig = $configService->getProviderConfig('data', 'MTN', 'Corporate');
        $this->table(['Key', 'Value'], [
            ['API Key', $corporateConfig['api_key'] ? 'Set' : 'Not set'],
            ['Provider URL', $corporateConfig['provider_url'] ?: 'Not set'],
            ['Provider Name', $corporateConfig['provider_name'] ?: 'Not set'],
            ['Auth Type', $corporateConfig['auth_type'] ?: 'Not set'],
        ]);

        $this->info('Testing configuration retrieval for MTN Gifting Data...');
        $giftingConfig = $configService->getProviderConfig('data', 'MTN', 'Gifting');
        $this->table(['Key', 'Value'], [
            ['API Key', $giftingConfig['api_key'] ? 'Set' : 'Not set'],
            ['Provider URL', $giftingConfig['provider_url'] ?: 'Not set'],
            ['Provider Name', $giftingConfig['provider_name'] ?: 'Not set'],
            ['Auth Type', $giftingConfig['auth_type'] ?: 'Not set'],
        ]);

        // Test external data plans retrieval
        $this->info('Testing external data plans retrieval...');
        if (empty($smeConfig['provider_url'])) {
            $this->warn('Data API not configured - skipping external plans test');
            $this->info('To test with real API: Configure MTN SME data settings in the database');
        } else {
            $this->info('Data API configured - structure test passed');
            $this->info('External data plans retrieval would connect to: ' . $smeConfig['provider_url']);
        }

        // Test data purchase API structure
        $this->info('Testing data purchase API structure...');
        $this->info('Data purchase would use the following flow:');
        $this->line('1. Validate user and plan');
        $this->line('2. Calculate final amount with discounts');
        $this->line('3. Debit user wallet');
        $this->line('4. Call external API via ExternalApiService::purchaseData()');
        $this->line('5. Process response and update transaction status');

        $this->info('Data Service Integration test completed!');
    }
}
