<?php

use Illuminate\Contracts\Console\Kernel;
use App\Services\DataService;
use App\Services\ExternalApiService;
use App\Services\ConfigurationService;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

echo "Starting Mobile Data Service Verification...\n";

$dataService = app(DataService::class);
$externalApiService = app(ExternalApiService::class);
$configService = app(ConfigurationService::class);

// 1. Verify Configuration
echo "\n1. Verifying Configuration...\n";
$mtnConfig = $configService->getProviderConfig('data', 'MTN', 'SME');
echo "MTN SME Provider: " . ($mtnConfig['provider_url'] ?? 'Not Set') . "\n";
echo "MTN SME Key: " . (substr($mtnConfig['api_key'] ?? '', 0, 5) . '...') . "\n";

if (strpos($mtnConfig['provider_url'], 'uzobestgsm.com/api/data/') === false) {
    echo "ERROR: Provider URL does not match Uzobest Data Endpoint.\n";
    exit(1);
}

// 2. Fetch Data Plans
echo "\n2. Fetching Data Plans for MTN SME...\n";
$plans = $externalApiService->getDataPlans('MTN', 'SME');

if ($plans['success']) {
    echo "SUCCESS: Fetched " . count($plans['plans'] ?? []) . " plans.\n";
    print_r(array_slice($plans['plans'] ?? [], 0, 2)); // Show first 2 plans
} else {
    echo "FAILED: " . ($plans['message'] ?? 'Unknown error') . "\n";
    if (isset($plans['response_body'])) {
        echo "Response Body: " . $plans['response_body'] . "\n";
    }
}

echo "\nVerification Complete.\n";
