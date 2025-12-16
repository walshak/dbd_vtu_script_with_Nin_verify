<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Check is_active status ===\n";

$configs = App\Models\ApiConfiguration::whereIn('config_key', ['cableVerificationApi', 'cableVerificationProvider', 'cableApi', 'cableProvider'])->get();
foreach ($configs as $config) {
    echo sprintf("Key: %s | Value: %s | is_active: %s\n",
        $config->config_key,
        $config->config_value,
        $config->is_active ? 'true' : 'false'
    );
}

echo "\n=== Fixing is_active status ===\n";
foreach ($configs as $config) {
    $config->is_active = true;
    $config->save();
    echo "Fixed: {$config->config_key}\n";
}
