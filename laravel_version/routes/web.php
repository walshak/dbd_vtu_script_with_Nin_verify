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
use App\Http\Controllers\PhoneValidationController;
use App\Http\Controllers\ServiceStatusController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionVerificationController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\EnhancedWalletController;
use App\Http\Controllers\ServiceParameterValidationController;
use App\Http\Controllers\ExternalServiceIntegrationController;
use App\Http\Controllers\AlphaTopupController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\WalletProviderController;
use App\Http\Controllers\Admin\ElectricityController as AdminElectricityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserUpgradeController;
use App\Http\Controllers\TransactionPinController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\User\KycVerificationController;
use App\Http\Controllers\Admin\AdminKycController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SystemConfigurationController;

// Home Route
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Password Reset Routes
Route::get('/password/reset', [AuthController::class, 'showPasswordResetForm'])->name('password.request');
Route::post('/password/reset-request', [AuthController::class, 'sendResetCode'])->name('password.reset.request');
Route::post('/password/verify-otp', [AuthController::class, 'verifyOTP'])->name('password.reset.verify');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/password/update', [AuthController::class, 'updatePassword'])->name('password.reset.update');

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/dashboard/realtime', [AdminDashboardController::class, 'getRealTimeData'])->name('admin.dashboard.realtime');
        Route::get('/dashboard/provider-health', [AdminDashboardController::class, 'getProviderHealth'])->name('admin.dashboard.provider-health');
        Route::get('/dashboard/api-performance', [AdminDashboardController::class, 'getApiPerformance'])->name('admin.dashboard.api-performance');
        Route::get('/dashboard/security-metrics', [AdminDashboardController::class, 'getSecurityMetrics'])->name('admin.dashboard.security-metrics');
        Route::get('/monitoring', [AdminDashboardController::class, 'monitoringOverview'])->name('admin.monitoring.overview');
        Route::get('/profile', [AdminAuthController::class, 'profile'])->name('admin.profile');
        Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');

        // User Management
        Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('admin.users.show');
        Route::put('/users/{user}/verify', [UserManagementController::class, 'verifyUser'])->name('admin.users.verify');
        Route::put('/users/{user}/suspend', [UserManagementController::class, 'suspendUser'])->name('admin.users.suspend');
        Route::put('/users/{user}/activate', [UserManagementController::class, 'activateUser'])->name('admin.users.activate');

        // Transaction Management
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('admin.transactions.index');
        Route::get('/transactions/{transaction}', [AdminTransactionController::class, 'show'])->name('admin.transactions.show');
        Route::put('/transactions/{transaction}/approve', [AdminTransactionController::class, 'approve'])->name('admin.transactions.approve');
        Route::put('/transactions/{transaction}/decline', [AdminTransactionController::class, 'decline'])->name('admin.transactions.decline');

        // Transaction Analysis Routes (compatible with old PHP app)
        Route::get('/transactions/analysis/general', [AdminTransactionController::class, 'generalSalesAnalysis'])->name('admin.transactions.general-analysis');
        Route::get('/transactions/analysis/airtime', [AdminTransactionController::class, 'airtimeSalesAnalysis'])->name('admin.transactions.airtime-analysis');
        Route::get('/transactions/analysis/data', [AdminTransactionController::class, 'dataSalesAnalysis'])->name('admin.transactions.data-analysis');
        Route::get('/transactions/{transactionId}/details', [AdminTransactionController::class, 'getTransactionDetails'])->name('admin.transactions.details');
        Route::post('/transactions/credit-user', [AdminTransactionController::class, 'creditUser'])->name('admin.transactions.credit-user');
        Route::post('/transactions/reverse', [AdminTransactionController::class, 'reverseTransaction'])->name('admin.transactions.reverse');
        Route::get('/transactions/export', [AdminTransactionController::class, 'export'])->name('admin.transactions.export');
        Route::get('/dashboard/stats', [AdminTransactionController::class, 'getDashboardStats'])->name('admin.dashboard.stats');

        // Wallet Provider Management Routes
        Route::prefix('wallet')->group(function () {
            Route::get('/dashboard', [WalletProviderController::class, 'dashboard'])->name('admin.wallet.dashboard');
            Route::get('/providers', [WalletProviderController::class, 'index'])->name('admin.wallet.providers.index');
            Route::get('/providers/create', [WalletProviderController::class, 'create'])->name('admin.wallet.providers.create');
            Route::post('/providers', [WalletProviderController::class, 'store'])->name('admin.wallet.providers.store');
            Route::get('/providers/{provider}/edit', [WalletProviderController::class, 'edit'])->name('admin.wallet.providers.edit');
            Route::put('/providers/{provider}', [WalletProviderController::class, 'update'])->name('admin.wallet.providers.update');
            Route::delete('/providers/{provider}', [WalletProviderController::class, 'destroy'])->name('admin.wallet.providers.destroy');
            Route::post('/providers/{provider}/toggle', [WalletProviderController::class, 'toggleStatus'])->name('admin.wallet.providers.toggle');
            Route::post('/providers/{provider}/update-balance', [WalletProviderController::class, 'updateBalance'])->name('admin.wallet.providers.update-balance');
            Route::post('/providers/{provider}/test', [WalletProviderController::class, 'testConnection'])->name('admin.wallet.providers.test');
            Route::get('/providers/status', [WalletProviderController::class, 'getStatus'])->name('admin.wallet.providers.status');
        });

        // API Configuration Routes
        Route::prefix('api-configuration')->name('admin.api-configuration.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'index'])->name('index');
            Route::get('/uzobest', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'uzobest'])->name('uzobest');
            Route::get('/airtime', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'airtime'])->name('airtime');
            Route::get('/data', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'data'])->name('data');
            Route::get('/wallet', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'wallet'])->name('wallet');

            Route::post('/update-general', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'updateGeneral'])->name('update-general');
            Route::post('/update-uzobest', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'updateUzobest'])->name('update-uzobest');
            Route::post('/update-airtime', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'updateAirtime'])->name('update-airtime');
            Route::post('/update-data', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'updateData'])->name('update-data');

            Route::get('/providers', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'providers'])->name('providers');
            Route::get('/providers/create', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'createProvider'])->name('providers.create');
            Route::post('/providers', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'storeProvider'])->name('providers.store');
            Route::get('/providers/{provider}/edit', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'editProvider'])->name('providers.edit');
            Route::put('/providers/{provider}', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'updateProvider'])->name('providers.update');
            Route::post('/providers/{provider}/toggle', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'toggleProvider'])->name('providers.toggle');
            Route::delete('/providers/{provider}', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'destroyProvider'])->name('providers.destroy');
            Route::post('/providers/{provider}/test', [App\Http\Controllers\Admin\ApiConfigurationController::class, 'testProvider'])->name('providers.test');
        });

        // Network Settings Routes
        Route::prefix('network-settings')->name('admin.network-settings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NetworkSettingsController::class, 'index'])->name('index');
            Route::put('/update', [App\Http\Controllers\Admin\NetworkSettingsController::class, 'update'])->name('update');
            Route::get('/status', [App\Http\Controllers\Admin\NetworkSettingsController::class, 'getNetworkStatus'])->name('status');
            Route::post('/toggle-service', [App\Http\Controllers\Admin\NetworkSettingsController::class, 'toggleService'])->name('toggle-service');
            Route::post('/bulk-toggle', [App\Http\Controllers\Admin\NetworkSettingsController::class, 'bulkToggle'])->name('bulk-toggle');
            Route::get('/analytics', [App\Http\Controllers\Admin\NetworkSettingsController::class, 'getServiceAnalytics'])->name('analytics');
        });

        // Data Plans Management Routes
        Route::prefix('data-plans')->name('admin.data-plans.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\DataPlanController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\DataPlanController::class, 'store'])->name('store');
            Route::get('/{plan}', [App\Http\Controllers\Admin\DataPlanController::class, 'show'])->name('show');
            Route::put('/{plan}', [App\Http\Controllers\Admin\DataPlanController::class, 'update'])->name('update');
            Route::delete('/{plan}', [App\Http\Controllers\Admin\DataPlanController::class, 'destroy'])->name('destroy');
            Route::post('/{plan}/toggle-status', [App\Http\Controllers\Admin\DataPlanController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-delete', [App\Http\Controllers\Admin\DataPlanController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/bulk-update-prices', [App\Http\Controllers\Admin\DataPlanController::class, 'bulkUpdatePrices'])->name('bulk-update-prices');
            Route::post('/sync', [App\Http\Controllers\Admin\DataPlanController::class, 'syncPlans'])->name('sync');
            Route::get('/export/csv', [App\Http\Controllers\Admin\DataPlanController::class, 'export'])->name('export');
            Route::get('/statistics/overview', [App\Http\Controllers\Admin\DataPlanController::class, 'getStatistics'])->name('statistics');
            Route::post('/sync-from-uzobest', [App\Http\Controllers\Admin\DataPlanController::class, 'syncFromUzobest'])->name('sync-from-uzobest');
        });

        // Airtime Pricing Management Routes
        Route::prefix('airtime')->name('admin.airtime.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\AirtimePricingController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\AirtimePricingController::class, 'store'])->name('store');
            Route::get('/{pricing}', [App\Http\Controllers\Admin\AirtimePricingController::class, 'show'])->name('show');
            Route::put('/{pricing}', [App\Http\Controllers\Admin\AirtimePricingController::class, 'update'])->name('update');
            Route::delete('/{pricing}', [App\Http\Controllers\Admin\AirtimePricingController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update', [App\Http\Controllers\Admin\AirtimePricingController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/export/csv', [App\Http\Controllers\Admin\AirtimePricingController::class, 'export'])->name('export');
            Route::get('/statistics/overview', [App\Http\Controllers\Admin\AirtimePricingController::class, 'getStatistics'])->name('statistics');
        });

        // Cable Plans Management Routes
        Route::prefix('cable-plans')->name('admin.cable-plans.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\CablePlanController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\CablePlanController::class, 'store'])->name('store');
            Route::get('/{plan}', [App\Http\Controllers\Admin\CablePlanController::class, 'show'])->name('show');
            Route::put('/{plan}', [App\Http\Controllers\Admin\CablePlanController::class, 'update'])->name('update');
            Route::delete('/{plan}', [App\Http\Controllers\Admin\CablePlanController::class, 'destroy'])->name('destroy');
            Route::post('/{plan}/toggle-status', [App\Http\Controllers\Admin\CablePlanController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-delete', [App\Http\Controllers\Admin\CablePlanController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/bulk-update-prices', [App\Http\Controllers\Admin\CablePlanController::class, 'bulkUpdatePrices'])->name('bulk-update-prices');
            Route::get('/export/csv', [App\Http\Controllers\Admin\CablePlanController::class, 'export'])->name('export');
            Route::get('/statistics/overview', [App\Http\Controllers\Admin\CablePlanController::class, 'getStatistics'])->name('statistics');
            Route::get('/by-provider', [App\Http\Controllers\Admin\CablePlanController::class, 'getPlansByProvider'])->name('by-provider');
            Route::post('/validate-iuc', [App\Http\Controllers\Admin\CablePlanController::class, 'validateIUC'])->name('validate-iuc');
            Route::post('/sync-from-uzobest', [App\Http\Controllers\Admin\CablePlanController::class, 'syncFromUzobest'])->name('sync-from-uzobest');
        });

        // Recharge Pin Management Routes
        Route::prefix('recharge-pins')->name('admin.recharge-pins.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\RechargePinController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\RechargePinController::class, 'store'])->name('store');
            Route::get('/{discount}', [App\Http\Controllers\Admin\RechargePinController::class, 'show'])->name('show');
            Route::put('/{discount}', [App\Http\Controllers\Admin\RechargePinController::class, 'update'])->name('update');
            Route::delete('/{discount}', [App\Http\Controllers\Admin\RechargePinController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update', [App\Http\Controllers\Admin\RechargePinController::class, 'bulkUpdate'])->name('bulk-update');
            Route::get('/statistics/overview', [App\Http\Controllers\Admin\RechargePinController::class, 'getStatistics'])->name('statistics');
            Route::get('/discount-by-network', [App\Http\Controllers\Admin\RechargePinController::class, 'getDiscountByNetwork'])->name('discount-by-network');
            Route::post('/calculate-pricing', [App\Http\Controllers\Admin\RechargePinController::class, 'calculatePricing'])->name('calculate-pricing');
            Route::get('/airtime-types', [App\Http\Controllers\Admin\RechargePinController::class, 'getAirtimeTypes'])->name('airtime-types');
        });

        // Exam Pin Management Routes
        Route::prefix('exam-pins')->name('admin.exam-pins.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ExamPinController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\ExamPinController::class, 'store'])->name('store');
            Route::get('/{examPin}', [App\Http\Controllers\Admin\ExamPinController::class, 'show'])->name('show');
            Route::put('/{examPin}', [App\Http\Controllers\Admin\ExamPinController::class, 'update'])->name('update');
            Route::delete('/{examPin}', [App\Http\Controllers\Admin\ExamPinController::class, 'destroy'])->name('destroy');
            Route::post('/{examPin}/toggle-status', [App\Http\Controllers\Admin\ExamPinController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-update-prices', [App\Http\Controllers\Admin\ExamPinController::class, 'bulkUpdatePrices'])->name('bulk-update-prices');
            Route::get('/export/csv', [App\Http\Controllers\Admin\ExamPinController::class, 'export'])->name('export');
            Route::get('/statistics/overview', [App\Http\Controllers\Admin\ExamPinController::class, 'getStatistics'])->name('statistics');
            Route::post('/calculate-pricing', [App\Http\Controllers\Admin\ExamPinController::class, 'calculatePricing'])->name('calculate-pricing');
        });

        // Alpha Topup Management Routes
        Route::prefix('alpha-topup')->name('admin.alpha-topup.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\AlphaTopupController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\AlphaTopupController::class, 'store'])->name('store');
            Route::get('/{alphaTopup}', [App\Http\Controllers\Admin\AlphaTopupController::class, 'show'])->name('show');
            Route::put('/{alphaTopup}', [App\Http\Controllers\Admin\AlphaTopupController::class, 'update'])->name('update');
            Route::delete('/{alphaTopup}', [App\Http\Controllers\Admin\AlphaTopupController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update-prices', [App\Http\Controllers\Admin\AlphaTopupController::class, 'bulkUpdatePrices'])->name('bulk-update-prices');
            Route::get('/export/csv', [App\Http\Controllers\Admin\AlphaTopupController::class, 'export'])->name('export');
            Route::get('/statistics/overview', [App\Http\Controllers\Admin\AlphaTopupController::class, 'getStatistics'])->name('statistics');
            Route::post('/calculate-pricing', [App\Http\Controllers\Admin\AlphaTopupController::class, 'calculatePricing'])->name('calculate-pricing');
        });

        // Electricity Management Routes
        Route::prefix('electricity')->name('admin.electricity.')->group(function () {
            Route::get('/', [AdminElectricityController::class, 'index'])->name('index');
            Route::post('/providers', [AdminElectricityController::class, 'store'])->name('providers.store');
            Route::put('/providers/{id}', [AdminElectricityController::class, 'update'])->name('providers.update');
            Route::delete('/providers/{id}', [AdminElectricityController::class, 'destroy'])->name('providers.destroy');
            Route::post('/providers/{id}/toggle-status', [AdminElectricityController::class, 'toggleStatus'])->name('providers.toggle-status');
            Route::get('/transactions', [AdminElectricityController::class, 'getTransactions'])->name('transactions');
            Route::post('/settings', [AdminElectricityController::class, 'updateSettings'])->name('settings.update');
            Route::post('/api-config', [AdminElectricityController::class, 'updateApiConfig'])->name('api-config.update');
            Route::post('/test-validation', [AdminElectricityController::class, 'testValidation'])->name('test-validation');
            Route::post('/sync-providers', [AdminElectricityController::class, 'syncProviders'])->name('sync-providers');
            Route::post('/sync-from-uzobest', [AdminElectricityController::class, 'syncFromUzobest'])->name('sync-from-uzobest');
            Route::post('/validate-meter', [AdminElectricityController::class, 'validateMeter'])->name('validate-meter');
        });

        // System Admin Management Routes (PHP AdminController Compatibility)
        Route::prefix('system')->name('admin.system.')->group(function () {
            // System Users Management
            Route::get('/accounts', [App\Http\Controllers\Admin\SystemAdminController::class, 'showAccountsPage'])->name('accounts.index');
            Route::get('/accounts/data', [App\Http\Controllers\Admin\SystemAdminController::class, 'getAccounts'])->name('accounts.data');
            Route::post('/accounts/create', [App\Http\Controllers\Admin\SystemAdminController::class, 'createAccount'])->name('accounts.create');
            Route::post('/accounts/create-subscriber', [App\Http\Controllers\Admin\SystemAdminController::class, 'createSubscriberAccount'])->name('accounts.create-subscriber');
            Route::get('/accounts/{id}', [App\Http\Controllers\Admin\SystemAdminController::class, 'getAccountById'])->name('accounts.show');
            Route::post('/accounts/update-status', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateAccountStatus'])->name('accounts.update-status');
            Route::post('/accounts/update', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateAdminAccount'])->name('accounts.update');
            Route::post('/logout', [App\Http\Controllers\Admin\SystemAdminController::class, 'logoutUser'])->name('logout');

            // Subscriber Management
            Route::get('/subscribers', [App\Http\Controllers\Admin\SystemAdminController::class, 'getSubscribers'])->name('subscribers');
            Route::get('/subscribers/{id}', [App\Http\Controllers\Admin\SystemAdminController::class, 'getSubscribersDetails'])->name('subscribers.show');
            Route::post('/subscribers/update', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateSubscriber'])->name('subscribers.update');
            Route::post('/subscribers/update-password', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateSubscriberPass'])->name('subscribers.update-password');
            Route::post('/subscribers/terminate', [App\Http\Controllers\Admin\SystemAdminController::class, 'terminateUserAccount'])->name('subscribers.terminate');
            Route::post('/subscribers/reset-api-key', [App\Http\Controllers\Admin\SystemAdminController::class, 'resetAccountApiKey'])->name('subscribers.reset-api-key');

            // Wallet Management
            Route::post('/wallet/credit-debit', [App\Http\Controllers\Admin\SystemAdminController::class, 'creditDebitUser'])->name('wallet.credit-debit');
            Route::get('/wallet/balance', [App\Http\Controllers\Admin\SystemAdminController::class, 'getWalletBalance'])->name('wallet.balance');

            // Wallet Provider Management Routes
            Route::prefix('wallet-providers')->name('wallet-providers.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\WalletProviderController::class, 'index'])->name('index');
                Route::get('/monnify', [App\Http\Controllers\Admin\WalletProviderController::class, 'showMonnifySettings'])->name('monnify');
                Route::get('/paystack', [App\Http\Controllers\Admin\WalletProviderController::class, 'showPaystackSettings'])->name('paystack');
                Route::get('/wallet-api', [App\Http\Controllers\Admin\WalletProviderController::class, 'showWalletApiSettings'])->name('wallet-api');
                Route::post('/monnify/update', [App\Http\Controllers\Admin\WalletProviderController::class, 'updateMonnifyConfig'])->name('monnify.update');
                Route::post('/paystack/update', [App\Http\Controllers\Admin\WalletProviderController::class, 'updatePaystackConfig'])->name('paystack.update');
                Route::post('/wallet-api/update', [App\Http\Controllers\Admin\WalletProviderController::class, 'updateWalletApiConfig'])->name('wallet-api.update');
                Route::get('/balances', [App\Http\Controllers\Admin\WalletProviderController::class, 'getWalletBalances'])->name('balances');
                Route::post('/test-connection', [App\Http\Controllers\Admin\WalletProviderController::class, 'testProviderConnection'])->name('test-connection');
                Route::post('/switch-provider', [App\Http\Controllers\Admin\WalletProviderController::class, 'switchPaymentProvider'])->name('switch-provider');
                Route::get('/transactions', [App\Http\Controllers\Admin\WalletProviderController::class, 'getPaymentTransactions'])->name('transactions');
                Route::post('/webhook-url', [App\Http\Controllers\Admin\WalletProviderController::class, 'generateWebhookUrl'])->name('webhook-url');
            });

            // Legacy route aliases for backward compatibility
            Route::get('/monnify-setting', [App\Http\Controllers\Admin\WalletProviderController::class, 'showMonnifySettings'])->name('monnify-setting');

            // Notification System Routes
            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
                Route::get('/messages', [App\Http\Controllers\Admin\NotificationController::class, 'messages'])->name('messages');
                Route::post('/status/update', [App\Http\Controllers\Admin\NotificationController::class, 'updateNotificationStatus'])->name('status.update');
                Route::post('/add', [App\Http\Controllers\Admin\NotificationController::class, 'addNotification'])->name('add');
                Route::delete('/delete', [App\Http\Controllers\Admin\NotificationController::class, 'deleteNotification'])->name('delete');
                Route::post('/send-email', [App\Http\Controllers\Admin\NotificationController::class, 'sendEmailToUser'])->name('send-email');
                Route::get('/contact-messages', [App\Http\Controllers\Admin\NotificationController::class, 'getContactMessages'])->name('contact-messages');
                Route::delete('/contact-message/delete', [App\Http\Controllers\Admin\NotificationController::class, 'deleteContactMessage'])->name('contact-message.delete');
                Route::post('/bulk-send', [App\Http\Controllers\Admin\NotificationController::class, 'sendBulkNotification'])->name('bulk-send');
                Route::get('/stats', [App\Http\Controllers\Admin\NotificationController::class, 'getNotificationStats'])->name('stats');
                Route::post('/preview', [App\Http\Controllers\Admin\NotificationController::class, 'previewNotification'])->name('preview');
            });

            // API Monitoring & Integration Routes
            Route::prefix('api-monitoring')->name('api-monitoring.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\ApiMonitoringController::class, 'index'])->name('index');
                Route::get('/service/{service}/metrics', [App\Http\Controllers\Admin\ApiMonitoringController::class, 'getServiceMetrics'])->name('service.metrics');
                Route::post('/service/toggle-status', [App\Http\Controllers\Admin\ApiMonitoringController::class, 'toggleServiceStatus'])->name('service.toggle-status');
                Route::post('/service/test-connectivity', [App\Http\Controllers\Admin\ApiMonitoringController::class, 'testApiConnectivity'])->name('service.test-connectivity');
                Route::get('/logs', [App\Http\Controllers\Admin\ApiMonitoringController::class, 'getLogs'])->name('logs');
                Route::get('/logs/export', [App\Http\Controllers\Admin\ApiMonitoringController::class, 'exportLogs'])->name('logs.export');
                Route::post('/fallback/configure', [App\Http\Controllers\Admin\ApiMonitoringController::class, 'configureFallback'])->name('fallback.configure');
                Route::post('/logs/cleanup', [App\Http\Controllers\Admin\ApiMonitoringController::class, 'cleanupLogs'])->name('logs.cleanup');
            });

            // Exam Pin Management
            Route::get('/exam-pins/{exam}', [App\Http\Controllers\Admin\SystemAdminController::class, 'getExamPinDetails'])->name('exam-pins.details');
            Route::post('/exam-pins/update', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateExamPin'])->name('exam-pins.update');

            // Electricity Management
            Route::get('/electricity/{electricity}', [App\Http\Controllers\Admin\SystemAdminController::class, 'getElectricityBillDetails'])->name('electricity.details');
            Route::post('/electricity/update', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateElectricityBill'])->name('electricity.update');

            // Site Settings
            Route::get('/settings', [App\Http\Controllers\Admin\SystemAdminController::class, 'getSiteSettings'])->name('settings');
            Route::post('/settings/network', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateNetworkSetting'])->name('settings.network');
            Route::post('/settings/contact', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateContactSetting'])->name('settings.contact');
            Route::post('/settings/site', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateSiteSetting'])->name('settings.site');
            Route::post('/settings/style', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateSiteStyleSetting'])->name('settings.style');

            // API Configuration
            Route::get('/api/config', [App\Http\Controllers\Admin\SystemAdminController::class, 'getApiConfiguration'])->name('api.config');
            Route::get('/api/links', [App\Http\Controllers\Admin\SystemAdminController::class, 'getApiConfigurationLinks'])->name('api.links');
            Route::post('/api/config/update', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateApiConfiguration'])->name('api.config.update');
            Route::post('/api/config/add', [App\Http\Controllers\Admin\SystemAdminController::class, 'addNewApiDetails'])->name('api.config.add');

            // Notification Management
            Route::get('/notifications/status', [App\Http\Controllers\Admin\SystemAdminController::class, 'getNotificationStatus'])->name('notifications.status');
            Route::post('/notifications/send-email', [App\Http\Controllers\Admin\SystemAdminController::class, 'sendEmailToUser'])->name('notifications.send-email');
            Route::post('/notifications/update-status', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateNotificationStatus'])->name('notifications.update-status');
            Route::get('/notifications', [App\Http\Controllers\Admin\SystemAdminController::class, 'getNotifications'])->name('notifications');
            Route::post('/notifications/add', [App\Http\Controllers\Admin\SystemAdminController::class, 'addNotification'])->name('notifications.add');
            Route::post('/notifications/delete', [App\Http\Controllers\Admin\SystemAdminController::class, 'deleteNotification'])->name('notifications.delete');

            // Network Management
            Route::get('/networks', [App\Http\Controllers\Admin\SystemAdminController::class, 'getNetworks'])->name('networks');

            // Airtime Discount Management
            Route::get('/airtime/discounts', [App\Http\Controllers\Admin\SystemAdminController::class, 'getAirtimeDiscount'])->name('airtime.discounts');
            Route::post('/airtime/discounts/add', [App\Http\Controllers\Admin\SystemAdminController::class, 'addAirtimeDiscount'])->name('airtime.discounts.add');
            Route::post('/airtime/discounts/update', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateAirtimeDiscount'])->name('airtime.discounts.update');

            // Alpha Topup Management
            Route::get('/alpha-topup', [App\Http\Controllers\Admin\SystemAdminController::class, 'getAlphaTopup'])->name('alpha-topup');
            Route::post('/alpha-topup/add', [App\Http\Controllers\Admin\SystemAdminController::class, 'addAlphaTopup'])->name('alpha-topup.add');
            Route::post('/alpha-topup/update', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateAlphaTopup'])->name('alpha-topup.update');
            Route::post('/alpha-topup/delete', [App\Http\Controllers\Admin\SystemAdminController::class, 'deleteAlphaTopup'])->name('alpha-topup.delete');
            Route::get('/alpha-topup/pending', [App\Http\Controllers\Admin\SystemAdminController::class, 'getPendingAlphaOrder'])->name('alpha-topup.pending');
            Route::post('/alpha-topup/complete', [App\Http\Controllers\Admin\SystemAdminController::class, 'completeAlphaTopupRequest'])->name('alpha-topup.complete');

            // Transaction Management
            Route::get('/transactions', [App\Http\Controllers\Admin\SystemAdminController::class, 'getTransactions'])->name('transactions');
            Route::get('/transactions/{id}', [App\Http\Controllers\Admin\SystemAdminController::class, 'getTransactionDetails'])->name('transactions.show');
            Route::post('/transactions/update-status', [App\Http\Controllers\Admin\SystemAdminController::class, 'updateTransactionStatus'])->name('transactions.update-status');

            // Analytics and Reports
            Route::get('/analytics/sales', [App\Http\Controllers\Admin\SystemAdminController::class, 'getSaleTransactions'])->name('analytics.sales');
            Route::get('/analytics/general', [App\Http\Controllers\Admin\SystemAdminController::class, 'getGeneralSalesAnalysis'])->name('analytics.general');
            Route::get('/analytics/airtime', [App\Http\Controllers\Admin\SystemAdminController::class, 'getAirtimeSalesAnalysis'])->name('analytics.airtime');
            Route::get('/analytics/data', [App\Http\Controllers\Admin\SystemAdminController::class, 'getDataSalesAnalysis'])->name('analytics.data');
            Route::get('/reports/site', [App\Http\Controllers\Admin\SystemAdminController::class, 'getGeneralSiteReports'])->name('reports.site');

            // Contact Management
            Route::get('/contacts', [App\Http\Controllers\Admin\SystemAdminController::class, 'getContact'])->name('contacts');
            Route::post('/contacts/delete', [App\Http\Controllers\Admin\SystemAdminController::class, 'deleteContact'])->name('contacts.delete');
        });

        // KYC Management Routes
        Route::prefix('kyc')->name('kyc.')->group(function () {
            Route::get('/', [AdminKycController::class, 'index'])->name('index');
            Route::get('/{verification}/show', [AdminKycController::class, 'show'])->name('show');
            Route::post('/{verification}/approve', [AdminKycController::class, 'approve'])->name('approve');
            Route::post('/{verification}/reject', [AdminKycController::class, 'reject'])->name('reject');
            Route::post('/{verification}/request-info', [AdminKycController::class, 'requestInfo'])->name('request-info');
            Route::get('/{verification}/download/{type}', [AdminKycController::class, 'downloadDocument'])->name('download');
            Route::get('/stats', [AdminKycController::class, 'getStats'])->name('stats');
            Route::post('/bulk-approve', [AdminKycController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/bulk-reject', [AdminKycController::class, 'bulkReject'])->name('bulk-reject');
            Route::get('/export', [AdminKycController::class, 'export'])->name('export');
            Route::post('/mark-expired', [AdminKycController::class, 'markExpired'])->name('mark-expired');
        });

        // Advanced Analytics Routes
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [AnalyticsController::class, 'index'])->name('index');
            Route::get('/transactions', [AnalyticsController::class, 'transactions'])->name('transactions');
            Route::get('/users', [AnalyticsController::class, 'users'])->name('users');
            Route::get('/services', [AnalyticsController::class, 'services'])->name('services');
            Route::get('/revenue', [AnalyticsController::class, 'revenue'])->name('revenue');
            Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
        });

        // Reports Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::post('/transaction', [ReportsController::class, 'transactionReport'])->name('transaction');
            Route::post('/user', [ReportsController::class, 'userReport'])->name('user');
            Route::post('/financial', [ReportsController::class, 'financialReport'])->name('financial');
            Route::post('/operational', [ReportsController::class, 'operationalReport'])->name('operational');
            Route::get('/custom', [ReportsController::class, 'customReport'])->name('custom');
            Route::post('/custom', [ReportsController::class, 'customReport'])->name('custom.generate');
            Route::post('/schedule', [ReportsController::class, 'scheduleReport'])->name('schedule');
        });

        // System Configuration Routes
        Route::prefix('system-configuration')->name('system-configuration.')->group(function () {
            Route::get('/', [SystemConfigurationController::class, 'index'])->name('index');

            // Feature Toggles
            Route::get('/feature-toggles', [SystemConfigurationController::class, 'featureToggles'])->name('feature-toggles');
            Route::post('/feature-toggles', [SystemConfigurationController::class, 'createFeatureToggle'])->name('feature-toggles.create');
            Route::put('/feature-toggles/{id}', [SystemConfigurationController::class, 'updateFeatureToggle'])->name('feature-toggles.update');

            // Maintenance Mode
            Route::get('/maintenance', [SystemConfigurationController::class, 'maintenanceMode'])->name('maintenance');
            Route::post('/maintenance', [SystemConfigurationController::class, 'maintenanceMode'])->name('maintenance.toggle');

            // System Health
            Route::get('/system-health', [SystemConfigurationController::class, 'systemHealth'])->name('system-health');
            Route::get('/api-status', [SystemConfigurationController::class, 'getApiStatus'])->name('api-status');

            // Cache Management
            Route::get('/cache', [SystemConfigurationController::class, 'cacheManagement'])->name('cache');
            Route::post('/cache', [SystemConfigurationController::class, 'cacheManagement'])->name('cache.action');

            // Backup Management
            Route::get('/backup', [SystemConfigurationController::class, 'backupManagement'])->name('backup');
            Route::post('/backup', [SystemConfigurationController::class, 'backupManagement'])->name('backup.action');

            // Environment Configuration
            Route::get('/environment', [SystemConfigurationController::class, 'environmentConfig'])->name('environment');
            Route::post('/environment', [SystemConfigurationController::class, 'environmentConfig'])->name('environment.update');
        });
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
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals');
    Route::post('/referrals/transfer', [ReferralController::class, 'transferEarnings'])->name('referrals.transfer');
    Route::get('/referrals/stats', [ReferralController::class, 'getStats'])->name('referrals.stats');
    Route::post('/referrals/calculate', [ReferralController::class, 'calculateEarnings'])->name('referrals.calculate');
    Route::get('/referrals/link', [ReferralController::class, 'getReferralLink'])->name('referrals.link');
    Route::get('/referrals/leaderboard', [ReferralController::class, 'getLeaderboard'])->name('referrals.leaderboard');

    // User Upgrade Routes
    Route::get('/upgrade/agent', [UserUpgradeController::class, 'showAgentUpgrade'])->name('upgrade.agent');
    Route::post('/upgrade/agent', [UserUpgradeController::class, 'upgradeToAgent'])->name('upgrade.agent.process');
    Route::get('/upgrade/vendor', [UserUpgradeController::class, 'showVendorUpgrade'])->name('upgrade.vendor');
    Route::post('/upgrade/vendor', [UserUpgradeController::class, 'upgradeToVendor'])->name('upgrade.vendor.process');
    Route::get('/upgrade/costs', [UserUpgradeController::class, 'getUpgradeCosts'])->name('upgrade.costs');

    // Transaction PIN Routes
    Route::get('/pin/setup', [TransactionPinController::class, 'showSetupForm'])->name('pin.setup');
    Route::post('/pin/setup', [TransactionPinController::class, 'setupPin'])->name('pin.setup.process');
    Route::get('/pin/change', [TransactionPinController::class, 'showChangeForm'])->name('pin.change');
    Route::post('/pin/change', [TransactionPinController::class, 'changePin'])->name('pin.change.process');
    Route::post('/pin/toggle', [TransactionPinController::class, 'togglePinStatus'])->name('pin.toggle');
    Route::post('/pin/reset', [TransactionPinController::class, 'resetPin'])->name('pin.reset');
    Route::post('/pin/validate', [TransactionPinController::class, 'validatePin'])->name('pin.validate');
    Route::get('/pin/status', [TransactionPinController::class, 'getPinStatus'])->name('pin.status');

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
    Route::post('/wallet/generate-virtual-account', [WalletController::class, 'generateVirtualAccount'])->name('wallet.generate-virtual-account');
    Route::get('/wallet/balance', [WalletController::class, 'getBalance'])->name('wallet.balance');
    Route::get('/wallet/transactions', [WalletController::class, 'getTransactionHistory'])->name('wallet.transactions');

    // Paystack Routes
    Route::post('/wallet/paystack/initialize', [WalletController::class, 'initializePaystackPayment'])->name('wallet.paystack.initialize');
    Route::get('/wallet/paystack/callback', [WalletController::class, 'handlePaystackCallback'])->name('wallet.paystack.callback');

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

    // Phone Validation Routes
    Route::post('/phone/validate', [PhoneValidationController::class, 'validatePhone'])->name('phone.validate');

    // Transaction Routes
    Route::get('/transactions/recent', [TransactionController::class, 'getRecent'])->name('transactions.recent');
    Route::get('/transactions/receipt/{reference}', [TransactionController::class, 'downloadReceipt'])->name('transactions.receipt');

    // Data API Routes
    Route::post('/data/plans', [DataController::class, 'getPlans'])->name('data.plans');
    Route::post('/data/purchase', [DataController::class, 'purchase'])->name('data.purchase');
    Route::post('/data/availability', [DataController::class, 'checkAvailability'])->name('data.availability');

    // Cable TV API Routes
    Route::get('/cable-tv/plans', [CableTVController::class, 'getPlans'])->name('cable-tv.plans');
    Route::post('/cable-tv/validate', [CableTVController::class, 'validateIUC'])->name('cable-tv.validate');
    Route::post('/cable-tv/purchase', [CableTVController::class, 'purchase'])->name('cable-tv.purchase');
    Route::get('/cable-tv/decoders', [CableTVController::class, 'getSupportedDecoders'])->name('cable-tv.decoders');

    // Electricity API Routes
    Route::get('/electricity/providers', [ElectricityController::class, 'getProviders'])->name('electricity.providers');
    Route::post('/electricity/validate-meter', [ElectricityController::class, 'validateMeter'])->name('electricity.validate-meter');
    Route::post('/electricity/pricing', [ElectricityController::class, 'getPricing'])->name('electricity.pricing');
    Route::post('/electricity/purchase', [ElectricityController::class, 'purchase'])->name('electricity.purchase');
    Route::get('/electricity/history', [ElectricityController::class, 'history'])->name('electricity.history');

    // Exam Pin API Routes
    Route::get('/exam-pins/available', [ExamPinController::class, 'getAvailableExams'])->name('exam-pins.available');
    Route::post('/exam-pins/purchase', [ExamPinController::class, 'purchase'])->name('exam-pins.purchase');
    Route::get('/exam-pins/history', [ExamPinController::class, 'history'])->name('exam-pins.history');

    // Recharge Pin API Routes
    Route::get('/recharge-pins/pricing', [RechargePinController::class, 'getPricing'])->name('recharge-pins.pricing');
    Route::post('/recharge-pins/purchase', [RechargePinController::class, 'purchase'])->name('recharge-pins.purchase');
    Route::get('/recharge-pins/history', [RechargePinController::class, 'history'])->name('recharge-pins.history');

    // Alpha Topup API Routes
    Route::post('/alpha-topup/pricing', [AlphaTopupController::class, 'getPricing'])->name('alpha-topup.pricing');
    Route::post('/alpha-topup/purchase', [AlphaTopupController::class, 'purchase'])->name('alpha-topup.purchase');
    Route::get('/alpha-topup/balance', [AlphaTopupController::class, 'checkBalance'])->name('alpha-topup.balance');

    // Phone Validation API Routes
    Route::post('/validate-phone', [PhoneValidationController::class, 'validatePhone'])->name('validate-phone');
    Route::post('/validate-phone/batch', [PhoneValidationController::class, 'validateBatch'])->name('validate-phone.batch');
    Route::get('/phone/networks', [PhoneValidationController::class, 'getSupportedNetworks'])->name('phone.networks');

    // Service Status API Routes
    Route::get('/service-status', [ServiceStatusController::class, 'getServiceStatus'])->name('service-status');
    Route::get('/service-status/{service}', [ServiceStatusController::class, 'getSpecificServiceStatus'])->name('service-status.specific');
    Route::get('/provider-status/{service}/{provider?}', [ServiceStatusController::class, 'checkProviderStatus'])->name('provider-status');

    // Transaction Verification API Routes
    Route::post('/verify-transaction-pin', [TransactionVerificationController::class, 'verifyTransactionPin'])->name('verify-transaction-pin');
    Route::post('/check-duplicate-transaction', [TransactionVerificationController::class, 'checkDuplicateTransaction'])->name('check-duplicate-transaction');
    Route::post('/verify-transaction-status', [TransactionVerificationController::class, 'verifyTransactionStatus'])->name('verify-transaction-status');
    Route::post('/verify-wallet-balance', [TransactionVerificationController::class, 'verifyWalletBalance'])->name('verify-wallet-balance');
    Route::post('/generate-transaction-reference', [TransactionVerificationController::class, 'generateTransactionReference'])->name('generate-transaction-reference');
    Route::post('/batch-verify-transactions', [TransactionVerificationController::class, 'batchVerifyTransactions'])->name('batch-verify-transactions');

    // Comprehensive Pricing API Routes
    Route::get('/pricing/all', [PricingController::class, 'getAllPricing'])->name('pricing.all');
    Route::get('/pricing/airtime', [PricingController::class, 'getAirtimePricing'])->name('pricing.airtime');
    Route::get('/pricing/data', [PricingController::class, 'getDataPricing'])->name('pricing.data');
    Route::get('/pricing/cable-tv', [PricingController::class, 'getCableTVPricing'])->name('pricing.cable-tv');
    Route::post('/pricing/specific', [PricingController::class, 'getSpecificPricing'])->name('pricing.specific');
    Route::post('/pricing/bulk-calculate', [PricingController::class, 'calculateBulkPricing'])->name('pricing.bulk-calculate');

    // Enhanced Wallet Management API Routes
    Route::get('/wallet/info', [EnhancedWalletController::class, 'getWalletInfo'])->name('wallet.info');
    Route::post('/wallet/credit', [EnhancedWalletController::class, 'creditWallet'])->name('wallet.credit');
    Route::post('/wallet/debit', [EnhancedWalletController::class, 'debitWallet'])->name('wallet.debit');
    Route::post('/wallet/transfer', [EnhancedWalletController::class, 'transferFunds'])->name('wallet.transfer');
    Route::post('/wallet/auto-funding/setup', [EnhancedWalletController::class, 'setupAutoFunding'])->name('wallet.auto-funding.setup');
    Route::get('/wallet/history', [EnhancedWalletController::class, 'getTransactionHistory'])->name('wallet.history');
    Route::post('/wallet/referral-bonus/apply', [EnhancedWalletController::class, 'applyReferralBonus'])->name('wallet.referral-bonus.apply');

    // Service Parameter Validation API Routes
    Route::post('/validate/meter-number', [ServiceParameterValidationController::class, 'validateMeterNumber'])->name('validate.meter-number');
    Route::post('/validate/cabletv-number', [ServiceParameterValidationController::class, 'validateCableTVNumber'])->name('validate.cabletv-number');
    Route::post('/validate/data-plan', [ServiceParameterValidationController::class, 'validateDataPlan'])->name('validate.data-plan');
    Route::post('/validate/exam-type', [ServiceParameterValidationController::class, 'validateExamType'])->name('validate.exam-type');
    Route::get('/validate/cabletv-plans', [ServiceParameterValidationController::class, 'getCableTVPlans'])->name('validate.cabletv-plans');
    Route::post('/validate/transaction-amount', [ServiceParameterValidationController::class, 'validateTransactionAmount'])->name('validate.transaction-amount');
    Route::post('/validate/batch', [ServiceParameterValidationController::class, 'batchValidateParameters'])->name('validate.batch');

    // External Service Integration API Routes
    Route::get('/external-services/providers', [ExternalServiceIntegrationController::class, 'getProviderConfigurations'])->name('external-services.providers');
    Route::post('/external-services/test/{provider}', [ExternalServiceIntegrationController::class, 'testProviderConnection'])->name('external-services.test');
    Route::post('/external-services/execute', [ExternalServiceIntegrationController::class, 'executeServiceTransaction'])->name('external-services.execute');
    Route::get('/external-services/stats', [ExternalServiceIntegrationController::class, 'getProviderStats'])->name('external-services.stats');
    Route::put('/external-services/providers/{provider}', [ExternalServiceIntegrationController::class, 'updateProviderConfiguration'])->name('external-services.providers.update');
    Route::get('/external-services/system-config', [ExternalServiceIntegrationController::class, 'getSystemConfiguration'])->name('external-services.system-config');

    // Other user routes
    Route::get('/pricing', function () {
        return view('pricing');
    })->name('pricing');

    // KYC Verification Routes
    Route::prefix('kyc')->group(function () {
        Route::get('/', [KycVerificationController::class, 'index'])->name('kyc.index');
        Route::get('/verify/{type}', [KycVerificationController::class, 'showVerificationForm'])->name('kyc.verify');
        Route::post('/verify/{type}', [KycVerificationController::class, 'submitVerification'])->name('kyc.submit');
        Route::post('/email/send', [KycVerificationController::class, 'sendEmailVerification'])->name('kyc.email.send');
        Route::post('/email/verify', [KycVerificationController::class, 'verifyEmail'])->name('kyc.email.verify');
        Route::post('/phone/send', [KycVerificationController::class, 'sendPhoneVerification'])->name('kyc.phone.send');
        Route::post('/phone/verify', [KycVerificationController::class, 'verifyPhone'])->name('kyc.phone.verify');
        Route::delete('/cancel/{verification}', [KycVerificationController::class, 'cancelVerification'])->name('kyc.cancel');
        Route::get('/progress', [KycVerificationController::class, 'getVerificationProgress'])->name('kyc.progress');
        Route::get('/download/{verification}/{type}', [KycVerificationController::class, 'downloadDocument'])->name('kyc.download');
    });
});

// API routes for mobile app
Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    Route::post('airtime/purchase', [AirtimeController::class, 'apiPurchase']);
    Route::post('data/purchase', [DataController::class, 'apiPurchase']);
    Route::post('cable-tv/purchase', [CableTVController::class, 'apiPurchase']);
    Route::get('wallet/balance', [WalletController::class, 'getBalance']);
    Route::post('wallet/fund', [WalletController::class, 'addFunds']);
});

// Webhook Routes (exclude from CSRF protection)
Route::post('/webhook/monnify', [App\Http\Controllers\MonnifyWebhookController::class, 'handleWebhook']);
Route::post('/webhook/paystack', [App\Http\Controllers\PaystackWebhookController::class, 'handleWebhook']);
