<?php

// Simple script to add electricity configurations
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ApiConfiguration;
use App\Models\ApiLink;

echo "Adding electricity configurations...\n";

// Meter verification provider configuration
ApiConfiguration::updateOrCreate([
    'config_key' => 'meterVerificationProvider'
], [
    'config_value' => 'https://n3tdata.com/api/validate-meter/',
    'description' => 'Meter number verification endpoint'
]);

// Meter verification API key
ApiConfiguration::updateOrCreate([
    'config_key' => 'meterVerificationApi'
], [
    'config_value' => 'test-meter-verification-key',
    'description' => 'Meter number verification API key'
]);

// Electricity purchase provider URL
ApiConfiguration::updateOrCreate([
    'config_key' => 'meterProvider'
], [
    'config_value' => 'https://n3tdata.com/api/electricity/',
    'description' => 'Electricity purchase endpoint'
]);

// Electricity purchase API key
ApiConfiguration::updateOrCreate([
    'config_key' => 'meterApi'
], [
    'config_value' => 'test-electricity-api-key',
    'description' => 'Electricity API key'
]);

// Add provider link for n3tdata electricity
ApiLink::updateOrCreate([
    'name' => 'n3tdata_electricity',
    'value' => 'https://n3tdata.com/api/electricity/'
], [
    'type' => 'electricity',
    'auth_type' => 'Basic',
    'is_active' => true,
    'description' => 'N3TData electricity provider',
    'auth_params' => json_encode(['username' => 'api_user', 'password' => 'api_password'])
]);

echo "Electricity configurations added successfully!\n";
