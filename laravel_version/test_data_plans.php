<?php

use Illuminate\Contracts\Console\Kernel;
use App\Services\ExternalApiService;
use App\Services\ConfigurationService;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

echo "Testing Uzobest Data Plans Endpoint...\n\n";

$externalApiService = app(ExternalApiService::class);

// Test 1: Get all data plans from /api/network/
echo "1. Fetching all data plans from /api/network/...\n";
$plans = $externalApiService->getDataPlans();

if ($plans['success']) {
    echo "SUCCESS: Fetched data plans\n";
    echo "Response structure:\n";
    print_r(array_slice($plans['plans'] ?? [], 0, 3)); // Show first 3 items
} else {
    echo "FAILED: " . ($plans['message'] ?? 'Unknown error') . "\n";
    if (isset($plans['response_body'])) {
        echo "Response Body: " . substr($plans['response_body'], 0, 500) . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n\n";

// Test 2: Check if we can filter by network
echo "2. Testing network filtering (if applicable)...\n";
$mtnPlans = $externalApiService->getDataPlans('MTN', 'SME');

if ($mtnPlans['success']) {
    echo "SUCCESS: Fetched MTN plans\n";
    echo "Number of plans: " . count($mtnPlans['plans'] ?? []) . "\n";
} else {
    echo "FAILED: " . ($mtnPlans['message'] ?? 'Unknown error') . "\n";
}

echo "\nTest Complete.\n";
