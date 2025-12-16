<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Cable Configurations ===\n";

$configs = App\Models\ApiConfiguration::where('service', 'cable')->get();
foreach ($configs as $config) {
    echo sprintf("%s | %s | %s | %s\n",
        $config->service,
        $config->provider ?? 'N/A',
        $config->config_key,
        $config->config_value
    );
}

echo "\n=== Testing ConfigurationService ===\n";
$configService = app(App\Services\ConfigurationService::class);
$cableConfig = $configService->getServiceConfig('cable');
echo "Cable service config: " . json_encode($cableConfig, JSON_PRETTY_PRINT) . "\n";

echo "\n=== Testing ExternalApiService verifyCableIUC ===\n";
$externalApi = app(App\Services\ExternalApiService::class);
$result = $externalApi->verifyCableIUC('1234567890', 'dstv');
echo "IUC verification result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
