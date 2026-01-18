<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use App\Services\MonnifyService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\ConfigurationService::class);
        $this->app->singleton(\App\Services\ExternalApiService::class);
        $this->app->singleton(\App\Services\LoggingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Check for new Monnify transactions on every request
        // But use 1-minute caching to avoid duplicate checks
        if (!$this->app->runningInConsole() && !$this->app->runningUnitTests()) {
            $this->checkMonnifyTransactionsWithCache();
        }
    }

    /**
     * Check Monnify transactions with 1-minute caching to avoid duplicates
     */
    private function checkMonnifyTransactionsWithCache(): void
    {
        // Use cache lock to ensure only one check runs at a time
        $lock = Cache::lock('monnify_transaction_check', 60);

        try {
            // Check if we've run this in the last minute
            $lastCheck = Cache::get('monnify_last_check_time');

            if ($lastCheck && now()->diffInSeconds($lastCheck) < 60) {
                // Already checked within the last minute, skip
                return;
            }

            // Try to acquire lock (non-blocking)
            if ($lock->get()) {
                try {
                    $monnifyService = app(MonnifyService::class);
                    $result = $monnifyService->checkNewTransactions();

                    // Cache the last check time for 1 minute
                    Cache::put('monnify_last_check_time', now(), 60);

                    \Illuminate\Support\Facades\Log::info('Monnify transaction check completed', [
                        'result' => $result,
                        'time' => now()->toDateTimeString()
                    ]);
                } finally {
                    $lock->release();
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Monnify transaction check failed', [
                'error' => $e->getMessage(),
                'time' => now()->toDateTimeString()
            ]);
        }
    }
}
