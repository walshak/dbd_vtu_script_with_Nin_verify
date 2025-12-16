<?php
// Test Dashboard with User ID 2 (who has transactions)

use App\Models\User;
use App\Models\Transaction;

require_once __DIR__ . '/vendor/autoload.php';

echo "=== Testing Dashboard Data for User with Transactions ===\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get user ID 2 (who has transactions)
$user = User::find(2);

if (!$user) {
    echo "User ID 2 not found.\n";
    exit(1);
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n";
echo "Phone: {$user->phone}\n";
echo "Email: {$user->email}\n";
echo "Wallet Balance: ₦" . number_format($user->wallet_balance, 2) . "\n";
echo "Account Type: {$user->account_type_name}\n";
echo "Status: {$user->registration_status_name}\n\n";

// Test transaction summary
echo "Testing Transaction Summary:\n";
$transactionSummary = Transaction::getUserTransactionSummary($user->id);
print_r($transactionSummary);
echo "\n";

// Test recent transactions
echo "Testing Recent Transactions:\n";
$recentTransactions = Transaction::where('sId', $user->id)
    ->orderBy('date', 'desc')
    ->limit(5)
    ->get();

echo "Found {$recentTransactions->count()} recent transactions\n";
foreach ($recentTransactions as $transaction) {
    echo "- {$transaction->servicename}: ₦{$transaction->amount} ({$transaction->status_text}) on {$transaction->formatted_date}\n";
}

echo "\n=== Test Complete ===\n";