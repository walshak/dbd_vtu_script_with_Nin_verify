<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request
$request = Request::create('/register', 'POST', [
    'fname' => 'Test',
    'lname' => 'User New',
    'email' => 'newuser@example.com',
    'phone' => '08011111111',
    'password' => 'password123',
    'cpassword' => 'password123',
    'state' => 'Lagos',
    'transpin' => '1234',
    'account' => '1',
    'referal' => ''
]);

$request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
$request->headers->set('Accept', 'application/json, text/javascript, */*; q=0.01');
$request->headers->set('X-Requested-With', 'XMLHttpRequest'); // This makes it an AJAX request

// Process the request
$response = $kernel->handle($request);

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Content: " . $response->getContent() . "\n";

// Check current authentication status
echo "Is authenticated: " . (Auth::check() ? 'YES' : 'NO') . "\n";
if (Auth::check()) {
    echo "Current user: " . Auth::user()->name . " (" . Auth::user()->phone . ")\n";
}

$kernel->terminate($request, $response);