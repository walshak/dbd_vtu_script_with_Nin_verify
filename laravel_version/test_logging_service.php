<?php
// Test LoggingService with ConfigurationService

use App\Services\ConfigurationService;
use App\Services\LoggingService;

require_once __DIR__ . '/vendor/autoload.php';

echo "=== Testing LoggingService with ConfigurationService ===\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "1. Testing direct instantiation:\n";

try {
    $configService = new ConfigurationService();
    echo "   ✓ ConfigurationService created\n";
    
    $loggingService = new LoggingService($configService);
    echo "   ✓ LoggingService created with ConfigurationService\n";
} catch (Exception $e) {
    echo "   ✗ Failed: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n2. Testing through Laravel container:\n";

try {
    $loggingService = app(LoggingService::class);
    echo "   ✓ LoggingService resolved through container\n";
} catch (Exception $e) {
    echo "   ✗ Failed: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";