<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\MonnifyService;
use Illuminate\Support\Facades\Log;

echo "=== Monnify Transaction Check Test ===\n\n";

try {
    $monnifyService = app(MonnifyService::class);

    echo "Checking for new transactions...\n";
    echo "=================================\n\n";

    $result = $monnifyService->checkNewTransactions();

    if ($result['success']) {
        echo "✓ Check completed successfully!\n\n";
        echo "Results:\n";
        echo "--------\n";
        echo "Users checked: " . ($result['users_checked'] ?? 0) . "\n";
        echo "Transactions processed: " . ($result['transactions_processed'] ?? 0) . "\n";
        echo "Errors: " . ($result['errors'] ?? 0) . "\n\n";

        if (($result['transactions_processed'] ?? 0) > 0) {
            echo "🎉 " . $result['transactions_processed'] . " wallet(s) have been credited!\n";
        } else {
            echo "ℹ️  No new transactions found.\n";
        }
    } else {
        echo "✗ Check failed: " . ($result['message'] ?? 'Unknown error') . "\n";
    }

    echo "\n=== Check Complete ===\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
