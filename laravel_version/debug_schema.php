<?php
// Check Database Schema

require_once __DIR__ . '/vendor/autoload.php';

echo "=== Checking Database Schema ===\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Get table info
    $tableInfo = DB::select("PRAGMA table_info(transactions)");
    
    echo "Transactions table columns:\n";
    foreach ($tableInfo as $column) {
        echo "- {$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
    }
    
    // Check if table exists and get sample data
    $count = DB::table('transactions')->count();
    echo "\nTotal transactions: {$count}\n";
    
    if ($count > 0) {
        echo "\nFirst transaction (sample):\n";
        $sample = DB::table('transactions')->first();
        print_r($sample);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Schema Check Complete ===\n";