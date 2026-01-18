<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Monnify Configuration Check ===\n\n";

$configs = DB::table('configurations')
    ->whereIn('config_key', ['monifyApi', 'monifySecrete', 'monifyContract'])
    ->get(['config_key', 'config_value']);

echo "Current Configuration:\n";
echo "=====================\n";
foreach ($configs as $config) {
    if ($config->config_key === 'monifySecrete') {
        echo "{$config->config_key}: " . substr($config->config_value, 0, 10) . "...\n";
    } else {
        echo "{$config->config_key}: {$config->config_value}\n";
    }
}

echo "\n\nDo you want to update these credentials? (yes/no): ";
$handle = fopen ("php://stdin","r");
$line = trim(fgets($handle));

if(strtolower($line) === 'yes'){
    echo "\nEnter Monnify API Key: ";
    $apiKey = trim(fgets($handle));

    echo "Enter Monnify Secret Key: ";
    $secretKey = trim(fgets($handle));

    echo "Enter Monnify Contract Code: ";
    $contractCode = trim(fgets($handle));

    DB::table('configurations')->updateOrInsert(
        ['config_key' => 'monifyApi'],
        ['config_value' => $apiKey, 'updated_at' => now()]
    );

    DB::table('configurations')->updateOrInsert(
        ['config_key' => 'monifySecrete'],
        ['config_value' => $secretKey, 'updated_at' => now()]
    );

    DB::table('configurations')->updateOrInsert(
        ['config_key' => 'monifyContract'],
        ['config_value' => $contractCode, 'updated_at' => now()]
    );

    echo "\n✓ Configuration updated successfully!\n";

    // Clear cache
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✓ Cache cleared\n";
} else {
    echo "\nConfiguration not updated.\n";
}

fclose($handle);
