<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Services\MonnifyService;
use App\Services\KycService;
use Illuminate\Support\Facades\DB;

echo "=== Monnify Virtual Account Creation Test ===\n\n";

// Test user details
$testName = "Walshak Timothy Apollos";
$testNin = "29051238593";
$testEmail = "walshak@test.com";
$testPhone = "08012345678";

echo "Test User Details:\n";
echo "Name: $testName\n";
echo "NIN: $testNin\n";
echo "Email: $testEmail\n";
echo "Phone: $testPhone\n\n";

try {
    // Find or create test user
    echo "Step 1: Finding/Creating test user...\n";
    $user = User::where('email', $testEmail)->first();

    if (!$user) {
        echo "Creating new user...\n";
        $nameParts = explode(' ', $testName);
        $firstName = $nameParts[0];
        $lastName = implode(' ', array_slice($nameParts, 1));

        $user = User::create([
            'name' => $testName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $testEmail,
            'phone' => $testPhone,
            'password' => bcrypt('password123'),
            'balance' => 0,
            'status' => 'active'
        ]);
        echo "✓ User created successfully (ID: {$user->id})\n\n";
    } else {
        echo "✓ User found (ID: {$user->id})\n\n";
    }

    // Step 2: Skip NIN verification (requires wallet funding), assign NIN directly
    echo "Step 2: Assigning NIN directly (skipping verification - requires Monnify wallet funding)...\n";
    $user->nin = $testNin;
    $user->kyc_status = 'verified';
    $user->kyc_method = 'nin';
    $user->kyc_verified_at = now();
    $user->save();
    echo "✓ NIN assigned to user\n\n";

    // Step 4: Create Virtual Account
    echo "Step 3: Creating Virtual Account with Monnify...\n";
    $monnifyService = new MonnifyService();

    $result = $monnifyService->createVirtualAccount($user);

    if (isset($result['success']) && $result['success']) {
        echo "✓ Virtual Account Created Successfully!\n\n";
        echo "Account Details:\n";
        echo "================\n";

        if (isset($result['accounts']) && is_array($result['accounts'])) {
            foreach ($result['accounts'] as $account) {
                echo "Bank: {$account['bank_name']}\n";
                echo "Account Number: {$account['account_number']}\n";
                echo "Account Name: {$account['account_name']}\n";
                echo "----------------\n";
            }
        }

        echo "\nFull Response:\n";
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";

    } elseif (isset($result['requires_kyc']) && $result['requires_kyc']) {
        echo "✗ KYC Required: {$result['message']}\n";

    } else {
        echo "✗ Virtual Account Creation Failed\n";
        echo "Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        echo "Full Response:\n";
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    }

    echo "\n=== Test Completed ===\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
