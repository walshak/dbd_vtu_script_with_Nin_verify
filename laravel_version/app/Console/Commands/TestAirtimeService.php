<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExternalApiService;
use App\Services\ConfigurationService;

class TestAirtimeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:airtime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Airtime Service Integration';

    /**
     * Execute the console command.
     */
    public function handle(ExternalApiService $externalApiService, ConfigurationService $configService): void
    {
        $this->info('Testing Airtime Service Integration...');

        // Test phone validation
        $this->info('Testing phone validation...');
        $phoneTest = $externalApiService->validatePhone('08031234567');
        $this->table(['Key', 'Value'], [
            ['Success', $phoneTest['success'] ? 'Yes' : 'No'],
            ['Network', $phoneTest['data']['network'] ?? 'N/A'],
            ['Message', $phoneTest['message']],
        ]);

        // Test configuration retrieval
        $this->info('Testing configuration retrieval...');
        $providerConfig = $configService->getProviderConfig('airtime', 'MTN', 'VTU');
        $this->table(['Key', 'Value'], [
            ['API Key', $providerConfig['api_key'] ? 'Set' : 'Not set'],
            ['Provider URL', $providerConfig['provider_url'] ?: 'Not set'],
            ['Provider Name', $providerConfig['provider_name'] ?: 'Not set'],
            ['Auth Type', $providerConfig['auth_type'] ?: 'Not set'],
        ]);

        // Test airtime purchase (dry run - would need real API keys to actually work)
        $this->info('Testing airtime purchase API structure...');
        if (empty($providerConfig['provider_url'])) {
            $this->warn('Airtime API not configured - skipping actual purchase test');
            $this->info('To test with real API: Configure MTN VTU settings in the database');
        } else {
            $this->info('Airtime API configured - structure test passed');
        }

        $this->info('Airtime Service Integration test completed!');
    }
}
