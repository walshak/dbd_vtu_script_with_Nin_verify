<?php
// Create Test Transactions for Dashboard

use App\Models\User;
use App\Models\Transaction;

require_once __DIR__ . '/vendor/autoload.php';

echo "=== Creating Test Transactions for Dashboard ===\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the first user
$user = User::first();

if (!$user) {
    echo "No users found in database.\n";
    exit(1);
}

echo "Creating test transactions for user: {$user->name}\n\n";

// Create some sample transactions
$transactions = [
    [
        'servicename' => 'Airtime',
        'description' => 'MTN Airtime Purchase - 08012345678',
        'amount' => '100.00',
        'old_balance' => '1047.00',
        'new_balance' => '947.00',
        'profit' => '2.50',
        'status' => Transaction::STATUS_SUCCESS
    ],
    [
        'servicename' => 'Data',
        'description' => 'MTN 1GB Data Bundle - 08012345678',
        'amount' => '250.00',
        'old_balance' => '947.00',
        'new_balance' => '697.00',
        'profit' => '5.00',
        'status' => Transaction::STATUS_SUCCESS
    ],
    [
        'servicename' => 'Cable Tv',
        'description' => 'DSTV Compact Plus Subscription',
        'amount' => '15700.00',
        'old_balance' => '697.00',
        'new_balance' => '697.00', // Failed transaction
        'profit' => '0.00',
        'status' => Transaction::STATUS_FAILED
    ],
    [
        'servicename' => 'Electricity',
        'description' => 'EEDC Electricity Bill Payment',
        'amount' => '5000.00',
        'old_balance' => '697.00',
        'new_balance' => '197.00',
        'profit' => '10.00',
        'status' => Transaction::STATUS_SUCCESS
    ],
    [
        'servicename' => 'Wallet Topup',
        'description' => 'Wallet funding of N5000 via Monnify bank transfer',
        'amount' => '5000.00',
        'old_balance' => '197.00',
        'new_balance' => '5197.00',
        'profit' => '0.00',
        'status' => Transaction::STATUS_SUCCESS
    ]
];

foreach ($transactions as $index => $transactionData) {
    // Add some variation in dates
    $date = now()->subHours(rand(1, 168)); // Random time in last week
    
    $transaction = new Transaction();
    $transaction->sId = $user->id;
    $transaction->transref = Transaction::generateReference();
    $transaction->servicename = $transactionData['servicename'];
    $transaction->servicedesc = $transactionData['description'];
    $transaction->amount = $transactionData['amount'];
    $transaction->status = $transactionData['status'];
    $transaction->oldbal = $transactionData['old_balance'];
    $transaction->newbal = $transactionData['new_balance'];
    $transaction->profit = $transactionData['profit'];
    $transaction->date = $date;
    
    $transaction->save();
    
    echo "✓ Created: {$transactionData['servicename']} - ₦{$transactionData['amount']} ({$transaction->status_text})\n";
}

// Update user's current wallet balance to match last transaction
$user->update(['wallet_balance' => 5197.00]);
echo "\n✓ Updated user wallet balance to ₦5,197.00\n";

// Test the updated data
echo "\nTesting updated transaction summary:\n";
$transactionSummary = Transaction::getUserTransactionSummary($user->id);
echo "Total Transactions: {$transactionSummary['total_transactions']}\n";
echo "Successful: {$transactionSummary['successful_transactions']}\n";
echo "Total Spent: ₦" . number_format($transactionSummary['total_spent'], 2) . "\n";
echo "Favorite Service: {$transactionSummary['favorite_service']}\n";

echo "\n=== Test Transactions Created Successfully ===\n";