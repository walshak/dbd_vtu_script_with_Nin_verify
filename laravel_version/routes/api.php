<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\AirtimeController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\CableTVController;
use App\Http\Controllers\Api\ElectricityController;
use App\Http\Controllers\Api\ExamPinController;
use App\Http\Controllers\Api\RechargePinController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DeviceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {

    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('/social-login', [AuthController::class, 'socialLogin']);
        Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    });

    // Device Management Routes
    Route::prefix('device')->group(function () {
        Route::post('/register', [DeviceController::class, 'registerDevice']);
        Route::post('/update-fcm-token', [DeviceController::class, 'updateFcmToken']);
    });

    // Service Information Routes (Public)
    Route::prefix('services')->group(function () {
        Route::get('/status', [ServiceController::class, 'getServicesStatus']);
        Route::get('/networks', [ServiceController::class, 'getNetworks']);
        Route::get('/airtime/networks', [AirtimeController::class, 'getNetworks']);
        Route::get('/data/plans/{network}', [DataController::class, 'getDataPlans']);
        Route::get('/cable-tv/providers', [CableTVController::class, 'getProviders']);
        Route::get('/cable-tv/plans/{provider}', [CableTVController::class, 'getPlans']);
        Route::get('/electricity/providers', [ElectricityController::class, 'getProviders']);
        Route::get('/exam/providers', [ExamPinController::class, 'getProviders']);
        Route::get('/recharge-pin/networks', [RechargePinController::class, 'getNetworks']);
    });

    // Application info
    Route::get('/app-info', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'app_name' => config('app.name'),
                'app_version' => '2.0.0',
                'api_version' => 'v1',
                'maintenance_mode' => app()->isDownForMaintenance(),
                'features' => [
                    'airtime' => true,
                    'data' => true,
                    'cable_tv' => true,
                    'electricity' => true,
                    'exam_pins' => true,
                    'recharge_pins' => true,
                    'wallet_transfer' => true,
                    'referral_system' => true,
                ],
                'social_login' => [
                    'google' => true,
                    'facebook' => true
                ]
            ]
        ]);
    });
});

// Protected routes (require authentication)
Route::prefix('v1')->middleware(['auth:api', 'verified'])->group(function () {

    // User Profile Routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::post('/change-pin', [UserController::class, 'changePin']);
        Route::get('/api-key', [UserController::class, 'getApiKey']);
        Route::post('/api-key/regenerate', [UserController::class, 'regenerateApiKey']);
        Route::post('/upload-avatar', [UserController::class, 'uploadAvatar']);
        Route::get('/stats', [UserController::class, 'getUserStats']);
        Route::get('/activity', [UserController::class, 'getActivity']);

        // KYC Routes
        Route::prefix('kyc')->group(function () {
            Route::get('/status', [UserController::class, 'getKycStatus']);
            Route::post('/submit', [UserController::class, 'submitKyc']);
            Route::get('/documents', [UserController::class, 'getKycDocuments']);
            Route::post('/upload-document', [UserController::class, 'uploadKycDocument']);
        });

        // User Upgrade Routes
        Route::prefix('upgrade')->group(function () {
            Route::get('/options', [UserController::class, 'getUpgradeOptions']);
            Route::post('/agent', [UserController::class, 'upgradeToAgent']);
            Route::post('/vendor', [UserController::class, 'upgradeToVendor']);
        });
    });

    // Wallet Routes
    Route::prefix('wallet')->group(function () {
        Route::get('/balance', [WalletController::class, 'getBalance']);
        Route::get('/transactions', [WalletController::class, 'getTransactions']);
        Route::post('/fund', [WalletController::class, 'fundWallet']);
        Route::post('/transfer', [WalletController::class, 'transferFunds']);
        Route::get('/funding-options', [WalletController::class, 'getFundingOptions']);
        Route::get('/banks', [WalletController::class, 'getBanks']);
        Route::post('/verify-account', [WalletController::class, 'verifyBankAccount']);
        Route::get('/funding-history', [WalletController::class, 'getFundingHistory']);
    });

    // Transaction Routes
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/{transaction}', [TransactionController::class, 'show']);
        Route::post('/{transaction}/retry', [TransactionController::class, 'retry']);
        Route::get('/export/csv', [TransactionController::class, 'exportCsv']);
        Route::get('/stats', [TransactionController::class, 'getStats']);
        Route::get('/recent', [TransactionController::class, 'getRecent']);
    });

    // Service Purchase Routes
    Route::prefix('services')->group(function () {

        // Airtime Routes
        Route::prefix('airtime')->group(function () {
            Route::post('/purchase', [AirtimeController::class, 'purchase']);
            Route::get('/history', [AirtimeController::class, 'getHistory']);
            Route::post('/bulk-purchase', [AirtimeController::class, 'bulkPurchase']);
        });

        // Data Routes
        Route::prefix('data')->group(function () {
            Route::post('/purchase', [DataController::class, 'purchase']);
            Route::get('/history', [DataController::class, 'getHistory']);
            Route::post('/bulk-purchase', [DataController::class, 'bulkPurchase']);
            Route::get('/balance/{phone}', [DataController::class, 'checkBalance']);
        });

        // Cable TV Routes
        Route::prefix('cable-tv')->group(function () {
            Route::post('/purchase', [CableTVController::class, 'purchase']);
            Route::get('/history', [CableTVController::class, 'getHistory']);
            Route::post('/verify-decoder', [CableTVController::class, 'verifyDecoder']);
        });

        // Electricity Routes
        Route::prefix('electricity')->group(function () {
            Route::post('/purchase', [ElectricityController::class, 'purchase']);
            Route::get('/history', [ElectricityController::class, 'getHistory']);
            Route::post('/verify-meter', [ElectricityController::class, 'verifyMeter']);
        });

        // Exam Pin Routes
        Route::prefix('exam-pins')->group(function () {
            Route::post('/purchase', [ExamPinController::class, 'purchase']);
            Route::get('/history', [ExamPinController::class, 'getHistory']);
        });

        // Recharge Pin Routes
        Route::prefix('recharge-pins')->group(function () {
            Route::post('/purchase', [RechargePinController::class, 'purchase']);
            Route::get('/history', [RechargePinController::class, 'getHistory']);
        });
    });

    // Notification Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'delete']);
        Route::get('/preferences', [NotificationController::class, 'getPreferences']);
        Route::put('/preferences', [NotificationController::class, 'updatePreferences']);
    });

    // Device Management Routes (Protected)
    Route::prefix('device')->group(function () {
        Route::get('/info', [DeviceController::class, 'getDeviceInfo']);
        Route::put('/preferences', [DeviceController::class, 'updatePreferences']);
        Route::post('/biometric-setup', [DeviceController::class, 'setupBiometric']);
        Route::get('/sessions', [DeviceController::class, 'getActiveSessions']);
        Route::delete('/sessions/{session}', [DeviceController::class, 'revokeSession']);
    });

    // Referral Routes
    Route::prefix('referrals')->group(function () {
        Route::get('/stats', [UserController::class, 'getReferralStats']);
        Route::get('/link', [UserController::class, 'getReferralLink']);
        Route::get('/earnings', [UserController::class, 'getReferralEarnings']);
        Route::post('/transfer-earnings', [UserController::class, 'transferReferralEarnings']);
        Route::get('/leaderboard', [UserController::class, 'getReferralLeaderboard']);
    });

    // Support Routes
    Route::prefix('support')->group(function () {
        Route::get('/faq', [UserController::class, 'getFaq']);
        Route::post('/ticket', [UserController::class, 'createSupportTicket']);
        Route::get('/tickets', [UserController::class, 'getSupportTickets']);
        Route::get('/contact-info', [UserController::class, 'getContactInfo']);
    });
});

// Admin API Routes
Route::prefix('v1/admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/stats', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => \App\Models\User::count(),
                'active_users' => \App\Models\User::where('last_login_at', '>', now()->subDays(30))->count(),
                'total_transactions' => \App\Models\Transaction::count(),
                'today_transactions' => \App\Models\Transaction::whereDate('created_at', today())->count(),
            ]
        ]);
    });
});

// Fallback for API not found
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'error' => 'The requested API endpoint does not exist'
    ], 404);
});
