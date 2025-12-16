<?php
// Test ConfigurationService

use App\Services\ConfigurationService;
use Illuminate\Support\Facades\Log;

require_once __DIR__ . '/vendor/autoload.php';

echo "=== Testing ConfigurationService ===\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "1. Testing ConfigurationService instantiation:\n";

try {
    $configService = new ConfigurationService();
    echo "   ✓ ConfigurationService created successfully\n";
} catch (Exception $e) {
    echo "   ✗ Failed to create ConfigurationService: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n2. Testing getConfiguration method:\n";

try {
    $result = $configService->getConfiguration('logging');
    echo "   ✓ getConfiguration('logging') called successfully\n";
    echo "   Result type: " . gettype($result) . "\n";
    echo "   Result value: " . json_encode($result) . "\n";
} catch (Exception $e) {
    echo "   ✗ getConfiguration failed: " . $e->getMessage() . "\n";
}

echo "\n3. Testing getConfigValue method:\n";

try {
    $result = $configService->getConfigValue('logging');
    echo "   ✓ getConfigValue('logging') called successfully\n";
    echo "   Result type: " . gettype($result) . "\n";
    echo "   Result value: " . json_encode($result) . "\n";
} catch (Exception $e) {
    echo "   ✗ getConfigValue failed: " . $e->getMessage() . "\n";
}

echo "\n4. Testing class methods:\n";

$reflection = new ReflectionClass(ConfigurationService::class);
$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

foreach ($methods as $method) {
    echo "   - " . $method->getName() . "\n";
}

echo "\n=== Test Complete ===\n";