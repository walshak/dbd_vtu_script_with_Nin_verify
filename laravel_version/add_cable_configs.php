<?php

// Simple script to add cable TV configurations
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ApiConfiguration;
use App\Models\ApiLink;

echo "Adding cable TV configurations...\n";

// Cable verification provider configuration
ApiConfiguration::updateOrCreate([
    'config_key' => 'cableVerificationProvider'
], [
    'config_value' => 'https://n3tdata.com/api/validate-iuc/',
    'description' => 'Cable IUC verification endpoint'
]);

// Cable verification API key
ApiConfiguration::updateOrCreate([
    'config_key' => 'cableVerificationApi'
], [
    'config_value' => 'test-cable-verification-key',
    'description' => 'Cable IUC verification API key'
]);

// Cable purchase provider URL
ApiConfiguration::updateOrCreate([
    'config_key' => 'cableProvider'
], [
    'config_value' => 'https://n3tdata.com/api/cabletv/',
    'description' => 'Cable TV purchase endpoint'
]);

// Cable purchase API key
ApiConfiguration::updateOrCreate([
    'config_key' => 'cableApi'
], [
    'config_value' => 'test-cable-api-key',
    'description' => 'Cable TV API key'
]);

// Add provider link for n3tdata
ApiLink::updateOrCreate([
    'name' => 'n3tdata',
    'value' => 'https://n3tdata.com/api/cabletv/'
], [
    'type' => 'cable',
    'auth_type' => 'Basic',
    'is_active' => true,
    'description' => 'N3TData cable TV provider',
    'auth_params' => json_encode(['username' => 'api_user', 'password' => 'api_password'])
]);

echo "Cable TV configurations added successfully!\n";
