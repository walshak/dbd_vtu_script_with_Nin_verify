<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CableTVController;
use App\Http\Controllers\ElectricityController;
use App\Http\Controllers\ExamPinController;
use App\Http\Controllers\RechargePinController;
use App\Http\Controllers\AlphaTopupController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Email Verification
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

// Password Reset Routes
Route::post('/password/reset-request', [AuthController::class, 'sendResetCode'])->name('password.reset.request');
Route::post('/password/verify-otp', [AuthController::class, 'verifyOTP'])->name('password.reset.verify');
Route::post('/password/update', [AuthController::class, 'updatePassword'])->name('password.reset.update');

// Password Reset Routes
Route::get('/password/reset', function () {
    return view('auth.reset-password');
})->name('password.reset');
Route::post('/password/reset-request', [PasswordResetController::class, 'sendResetCode'])->name('password.reset.request');
Route::post('/password/verify-otp', [PasswordResetController::class, 'verifyOTP'])->name('password.reset.verify');
Route::post('/password/update', [PasswordResetController::class, 'updatePassword'])->name('password.reset.update');

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('admin.profile');
        Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');
        
        // User Management
        Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('admin.users.show');
        Route::put('/users/{user}/verify', [UserManagementController::class, 'verifyUser'])->name('admin.users.verify');
        Route::put('/users/{user}/suspend', [UserManagementController::class, 'suspendUser'])->name('admin.users.suspend');
        Route::put('/users/{user}/activate', [UserManagementController::class, 'activateUser'])->name('admin.users.activate');
        
        // Transaction Management
        Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('admin.transactions.show');
        Route::put('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('admin.transactions.approve');
        Route::put('/transactions/{transaction}/decline', [TransactionController::class, 'decline'])->name('admin.transactions.decline');
    });
});

// Protected Routes
Route::middleware('auth')->group(function () {
    // User Profile Routes
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/profile/change-password', [UserController::class, 'changePassword'])->name('user.password.change');
    Route::post('/profile/change-pin', [UserController::class, 'changePin'])->name('user.pin.change');
    Route::get('/api-key', [UserController::class, 'getApiKey'])->name('user.api-key');
    Route::post('/api-key/regenerate', [UserController::class, 'regenerateApiKey'])->name('user.api-key.regenerate');
    Route::get('/referrals', [UserController::class, 'referrals'])->name('referrals');

    // Wallet Routes
    Route::get('/fund-wallet', [WalletController::class, 'fundWallet'])->name('fund-wallet');
    Route::get('/transfer-funds', [WalletController::class, 'transferFunds'])->name('transfer-funds');
    Route::get('/wallet-to-bank', [WalletController::class, 'withdrawFunds'])->name('wallet-to-bank');
    Route::get('/transactions', [WalletController::class, 'transactionHistory'])->name('transactions');

    // Wallet API Routes
    Route::post('/wallet/add-funds', [WalletController::class, 'addFunds'])->name('wallet.add-funds');
    Route::post('/wallet/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
    Route::post('/wallet/validate-bank', [WalletController::class, 'validateBankAccount'])->name('wallet.validate-bank');
    Route::get('/wallet/balance', [WalletController::class, 'getBalance'])->name('wallet.balance');
    Route::get('/wallet/transactions', [WalletController::class, 'getTransactionHistory'])->name('wallet.transactions');

    // Service Routes
    Route::get('/buy-airtime', [AirtimeController::class, 'index'])->name('buy-airtime');
    Route::get('/buy-data', [DataController::class, 'index'])->name('buy-data');
    Route::get('/cable-tv', [CableTVController::class, 'index'])->name('cable-tv');
    Route::get('/electricity', [ElectricityController::class, 'index'])->name('electricity');
    Route::get('/exam-pins', [ExamPinController::class, 'index'])->name('exam-pins');
    Route::get('/recharge-pins', [RechargePinController::class, 'index'])->name('recharge-pins');
    Route::get('/alpha-topup', [AlphaTopupController::class, 'index'])->name('alpha-topup');

    // Airtime API Routes
    Route::post('/airtime/pricing', [AirtimeController::class, 'getPricing'])->name('airtime.pricing');
    Route::post('/airtime/purchase', [AirtimeController::class, 'purchase'])->name('airtime.purchase');
    Route::get('/airtime/history', [AirtimeController::class, 'history'])->name('airtime.history');

    // Data API Routes
    Route::post('/data/plans', [DataController::class, 'getPlans'])->name('data.plans');
    Route::post('/data/purchase', [DataController::class, 'purchase'])->name('data.purchase');
    Route::post('/data/availability', [DataController::class, 'checkAvailability'])->name('data.availability');

    // Cable TV API Routes
    Route::post('/cable-tv/plans', [CableTVController::class, 'getPlans'])->name('cable-tv.plans');
    Route::post('/cable-tv/validate-iuc', [CableTVController::class, 'validateIUC'])->name('cable-tv.validate-iuc');
    Route::post('/cable-tv/purchase', [CableTVController::class, 'purchase'])->name('cable-tv.purchase');
    Route::get('/cable-tv/decoders', [CableTVController::class, 'getSupportedDecoders'])->name('cable-tv.decoders');

    // Electricity API Routes
    Route::get('/electricity/providers', [ElectricityController::class, 'getProviders'])->name('electricity.providers');
    Route::post('/electricity/validate-meter', [ElectricityController::class, 'validateMeter'])->name('electricity.validate-meter');
    Route::post('/electricity/pricing', [ElectricityController::class, 'getPricing'])->name('electricity.pricing');
    Route::post('/electricity/purchase', [ElectricityController::class, 'purchase'])->name('electricity.purchase');
    Route::get('/electricity/history', [ElectricityController::class, 'history'])->name('electricity.history');

    // Exam Pin API Routes
    Route::get('/exam-pins/providers', [ExamPinController::class, 'getProviders'])->name('exam-pins.providers');
    Route::post('/exam-pins/pricing', [ExamPinController::class, 'getPricing'])->name('exam-pins.pricing');
    Route::post('/exam-pins/purchase', [ExamPinController::class, 'purchase'])->name('exam-pins.purchase');
    Route::get('/exam-pins/history', [ExamPinController::class, 'history'])->name('exam-pins.history');

    // Recharge Pin API Routes
    Route::get('/recharge-pins/discounts', [RechargePinController::class, 'getDiscounts'])->name('recharge-pins.discounts');
    Route::post('/recharge-pins/pricing', [RechargePinController::class, 'getPricing'])->name('recharge-pins.pricing');
    Route::post('/recharge-pins/purchase', [RechargePinController::class, 'purchase'])->name('recharge-pins.purchase');
    Route::get('/recharge-pins/history', [RechargePinController::class, 'history'])->name('recharge-pins.history');
    Route::get('/recharge-pins/details/{reference}', [RechargePinController::class, 'getPinDetails'])->name('recharge-pins.details');

    // Alpha Topup API Routes
    Route::get('/alpha-topup/plans', [AlphaTopupController::class, 'getPlans'])->name('alpha-topup.plans');
    Route::get('/alpha-topup/denominations', [AlphaTopupController::class, 'getDenominations'])->name('alpha-topup.denominations');
    Route::post('/alpha-topup/pricing', [AlphaTopupController::class, 'getPricing'])->name('alpha-topup.pricing');
    Route::post('/alpha-topup/purchase', [AlphaTopupController::class, 'purchase'])->name('alpha-topup.purchase');
    Route::get('/alpha-topup/history', [AlphaTopupController::class, 'history'])->name('alpha-topup.history');
    Route::get('/alpha-topup/balance', [AlphaTopupController::class, 'checkBalance'])->name('alpha-topup.balance');



    // Account Routes
        
    // Other user routes can be added here
});

    Route::get('/referrals', [UserController::class, 'referrals'])->name('referrals');

    Route::get('/pricing', function () {
        return view('pricing');
    })->name('pricing');
});
});

// Admin Protected Routes
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});

// API routes for mobile app
Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    Route::post('airtime/purchase', [AirtimeController::class, 'apiPurchase']);
    Route::post('data/purchase', [DataController::class, 'apiPurchase']);
    Route::post('cable-tv/purchase', [CableTVController::class, 'apiPurchase']);

    Route::get('wallet/balance', [WalletController::class, 'getBalance']);
    Route::get('wallet/transactions', [WalletController::class, 'getTransactionHistory']);
});
