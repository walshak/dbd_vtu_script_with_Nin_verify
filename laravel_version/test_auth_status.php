<?php
// Test Authentication Script

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

require_once __DIR__ . '/vendor/autoload.php';

echo "=== VTU System Authentication Test ===\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "1. Current Authentication Status:\n";
echo "   Authenticated: " . (Auth::check() ? 'YES' : 'NO') . "\n";
if (Auth::check()) {
    echo "   Current User: " . Auth::user()->name . " (ID: " . Auth::user()->id . ")\n";
    echo "   Phone: " . Auth::user()->phone . "\n";
}
echo "\n";

echo "2. Users in Database:\n";
$users = DB::table('users')->select('id', 'name', 'phone', 'email', 'reg_status')->get();
foreach ($users as $user) {
    echo "   ID: {$user->id} | Name: {$user->name} | Phone: {$user->phone} | Email: {$user->email} | Status: {$user->reg_status}\n";
}
echo "\n";

echo "3. Test Registration Data Validation:\n";
$testData = [
    'fname' => 'Test',
    'lname' => 'NewUser',
    'email' => 'testnewuser@example.com',
    'phone' => '08099999999',
    'password' => 'password123',
    'state' => 'Lagos',
    'transpin' => '1234',
    'account' => '1'
];

echo "   Test Email: {$testData['email']}\n";
echo "   Test Phone: {$testData['phone']}\n";

// Check if test data would pass validation
$emailExists = DB::table('users')->where('email', $testData['email'])->exists();
$phoneExists = DB::table('users')->where('phone', $testData['phone'])->exists();

echo "   Email exists: " . ($emailExists ? 'YES' : 'NO') . "\n";
echo "   Phone exists: " . ($phoneExists ? 'YES' : 'NO') . "\n";
echo "   Would pass unique validation: " . (!$emailExists && !$phoneExists ? 'YES' : 'NO') . "\n";

echo "\n=== Test Complete ===\n";