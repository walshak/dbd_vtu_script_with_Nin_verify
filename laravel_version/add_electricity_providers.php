<?php

// Simple script to add test electricity providers
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adding test electricity providers...\n";

// Check if electricity_providers table exists and has columns
try {
    DB::table('electricity_providers')->insert([
        [
            'provider_name' => 'Abuja Electricity Distribution Company (AEDC)',
            'provider_code' => 'aedc',
            'minimum_amount' => 1000,
            'maximum_amount' => 50000,
            'service_charge' => 50,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'provider_name' => 'Eko Electricity Distribution Company (EKEDC)',
            'provider_code' => 'ekedc',
            'minimum_amount' => 1000,
            'maximum_amount' => 50000,
            'service_charge' => 50,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'provider_name' => 'Ikeja Electric Distribution Company (IKEDC)',
            'provider_code' => 'ikedc',
            'minimum_amount' => 1000,
            'maximum_amount' => 50000,
            'service_charge' => 50,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ]);
    echo "Electricity providers added successfully!\n";
} catch (Exception $e) {
    echo "Could not add electricity providers: " . $e->getMessage() . "\n";
    echo "This might be normal if the table doesn't exist or has different structure.\n";
}
