<?php
// Check User 2 Status Fields

use App\Models\User;

require_once __DIR__ . '/vendor/autoload.php';

echo "=== Checking User 2 Status Fields ===\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::find(2);

if ($user) {
    echo "User: {$user->name}\n";
    echo "Raw reg_status: '{$user->reg_status}'\n";
    echo "Raw user_type: '{$user->user_type}'\n";
    echo "Account Type Name: '{$user->account_type_name}'\n";
    echo "Registration Status Name: '{$user->registration_status_name}'\n";
    
    echo "\nDebugging:\n";
    echo "reg_status === 0: " . (($user->reg_status === 0) ? 'true' : 'false') . "\n";
    echo "reg_status == 'active': " . (($user->reg_status == 'active') ? 'true' : 'false') . "\n";
    echo "user_type === 1: " . (($user->user_type === 1) ? 'true' : 'false') . "\n";
    
    // Check all users
    echo "\nAll users:\n";
    $users = User::all();
    foreach ($users as $u) {
        echo "ID: {$u->id} | reg_status: '{$u->reg_status}' | user_type: '{$u->user_type}'\n";
    }
} else {
    echo "User 2 not found\n";
}

echo "\n=== Debug Complete ===\n";