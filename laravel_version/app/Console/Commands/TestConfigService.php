<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ConfigurationService;

class TestConfigService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Configuration Service';

    /**
     * Execute the console command.
     */
    public function handle(ConfigurationService $configService): void
    {
        $this->info('Testing Configuration Service...');

        // Test getting API details
        $apiDetails = $configService->getApiDetails();
        $this->info('API Details count: ' . $apiDetails->count());

        // Test getting API links
        $apiLinks = $configService->getApiLinks();
        $this->info('API Links count: ' . $apiLinks->count());

        // Test getting specific config values
        $mtnVtuKey = $configService->getConfigValue('mtnVTUKey', 'default');
        $this->info('MTN VTU Key: ' . $mtnVtuKey);

        // Test provider config
        $providerConfig = $configService->getProviderConfig('airtime', 'MTN', 'VTU');
        $this->info('MTN VTU Provider Config:');
        $this->table(['Key', 'Value'], [
            ['API Key', $providerConfig['api_key'] ?? 'Not set'],
            ['Provider URL', $providerConfig['provider_url'] ?? 'Not set'],
            ['Provider Name', $providerConfig['provider_name'] ?? 'Not set'],
            ['Auth Type', $providerConfig['auth_type'] ?? 'Not set'],
        ]);

        // Test service config
        $cableConfig = $configService->getServiceConfig('cable');
        $this->info('Cable Service Config:');
        $this->table(['Key', 'Value'], [
            ['Verification API', $cableConfig['verification_api'] ?? 'Not set'],
            ['API Key', $cableConfig['api_key'] ?? 'Not set'],
            ['Provider', $cableConfig['provider'] ?? 'Not set'],
        ]);

        $this->info('Configuration Service test completed successfully!');
    }
}
