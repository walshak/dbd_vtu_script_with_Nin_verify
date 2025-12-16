<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debug ConfigurationService getConfigValue ===\n";

$configService = app(App\Services\ConfigurationService::class);

$keys = ['cableVerificationApi', 'cableVerificationProvider', 'cableApi', 'cableProvider'];

foreach ($keys as $key) {
    $value = $configService->getConfigValue($key);
    echo "Key: $key | Value: $value\n";
}

echo "\n=== Direct DB Query ===\n";
$configs = App\Models\ApiConfiguration::whereIn('config_key', $keys)->get();
foreach ($configs as $config) {
    echo sprintf("Key: %s | Value: %s\n", $config->config_key, $config->config_value);
}
