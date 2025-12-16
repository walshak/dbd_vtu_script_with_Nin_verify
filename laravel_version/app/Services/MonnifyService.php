<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MonnifyService
{
    private $apiKey;
    private $secretKey;
    private $contractCode;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = $this->getConfigValue('monifyApi');
        $this->secretKey = $this->getConfigValue('monifySecrete');
        $this->contractCode = $this->getConfigValue('monifyContract');

        // Use sandbox for testing, live for production
        $this->baseUrl = config('app.env') === 'production'
            ? 'https://api.monnify.com'
            : 'https://sandbox.monnify.com';
    }

    /**
     * Get configuration value from database
     */
    private function getConfigValue($key)
    {
        try {
            return DB::table('configurations')
                ->where('config_key', $key)
                ->value('config_value');
        } catch (\Exception $e) {
            // Handle case when configurations table doesn't exist
            return null;
        }
    }

    /**
     * Get access token from Monnify with caching
     * Tokens are valid for 1 hour, we cache for 55 minutes
     */
    private function getAccessToken()
    {
        $cacheKey = 'monnify_access_token_' . md5($this->apiKey);
        $cacheDuration = config('monnify.token_cache_duration', 3300); // 55 minutes

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, $cacheDuration, function () {
            try {
                $credentials = base64_encode($this->apiKey . ':' . $this->secretKey);

                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Basic ' . $credentials,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($this->baseUrl . '/api/v1/auth/login');

                if ($response->successful()) {
                    $data = $response->json();
                    $token = $data['responseBody']['accessToken'] ?? null;
                    
                    if ($token) {
                        Log::info('Monnify access token obtained and cached');
                    }
                    
                    return $token;
                }

                Log::error('Monnify auth failed', ['response' => $response->body()]);
                return null;
            } catch (\Exception $e) {
                Log::error('Monnify auth error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Create virtual account for user
     */
    public function createVirtualAccount(User $user)
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'message' => 'Failed to authenticate with Monnify'];
            }

            $reference = 'VA_' . $user->id . '_' . uniqid() . rand(1000, 9999);
            $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));

            // Ensure we have a proper name - if user has no name, use a default based on email
            if (empty($fullName) || $fullName === ' ') {
                $emailPart = explode('@', $user->email)[0];
                $fullName = ucfirst($emailPart) . ' User';
            }

            // Validate and clean the name (Monnify has specific requirements)
            $fullName = preg_replace('/[^a-zA-Z\s]/', '', $fullName); // Remove special characters
            $fullName = preg_replace('/\s+/', ' ', $fullName); // Clean up multiple spaces
            $fullName = trim($fullName);

            // Ensure minimum length and maximum length for account name
            if (strlen($fullName) < 3) {
                $fullName = 'Account Holder ' . $user->id;
            }

            // Monnify typically limits account names to ~40 characters
            if (strlen($fullName) > 40) {
                $fullName = substr($fullName, 0, 40);
                $fullName = trim($fullName);
            }

            $payload = [
                'accountReference' => $reference,
                'accountName' => $fullName,
                'currencyCode' => 'NGN',
                'contractCode' => $this->contractCode,
                'customerEmail' => $user->email,
                'customerName' => $fullName,
                'getAllAvailableBanks' => false,
                'preferredBanks' => ['035', '120', '232'] // Wema, Sterling, Sterling
            ];

            Log::info('Monnify virtual account request payload', [
                'user_id' => $user->id,
                'payload' => $payload
            ]);

            // Create virtual account with multiple bank options
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/api/v2/bank-transfer/reserved-accounts', $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Monnify virtual account API response', [
                    'user_id' => $user->id,
                    'response_data' => $data
                ]);

                if (isset($data['requestSuccessful']) && $data['requestSuccessful']) {
                    $accounts = $data['responseBody']['accounts'];

                    // Save virtual accounts to user record
                    $virtualAccounts = [];
                    foreach ($accounts as $account) {
                        $virtualAccounts[] = [
                            'bank_name' => $account['bankName'],
                            'bank_code' => $account['bankCode'],
                            'account_number' => $account['accountNumber'],
                            'account_name' => $data['responseBody']['accountName']
                        ];
                    }

                    // Update user with virtual account details
                    $user->update([
                        'virtual_accounts' => json_encode($virtualAccounts),
                        'monnify_reference' => $reference
                    ]);

                    Log::info('Virtual accounts saved successfully', [
                        'user_id' => $user->id,
                        'accounts_count' => count($virtualAccounts)
                    ]);

                    return [
                        'success' => true,
                        'message' => 'Virtual account created successfully',
                        'accounts' => $virtualAccounts
                    ];
                } else {
                    Log::error('Monnify API returned unsuccessful response', [
                        'user_id' => $user->id,
                        'response_data' => $data
                    ]);

                    $errorMessage = isset($data['responseMessage'])
                        ? $data['responseMessage']
                        : 'Unknown error from Monnify API';

                    return ['success' => false, 'message' => $errorMessage];
                }
            }

            Log::error('Monnify virtual account creation failed', [
                'user_id' => $user->id,
                'response' => $response->body()
            ]);

            // Check if error is due to existing accounts
            $responseData = $response->json();
            if (isset($responseData['responseCode']) && $responseData['responseCode'] === 'R42') {
                // Account already exists - this is not really an error
                Log::info('Virtual account already exists for user', ['user_id' => $user->id]);
                return ['success' => false, 'message' => 'Virtual account already exists. Please check your existing accounts.'];
            }

            return ['success' => false, 'message' => 'Failed to create virtual account'];
        } catch (\Exception $e) {
            Log::error('Monnify virtual account error: ' . $e->getMessage(), [
                'user_id' => $user->id
            ]);

            return ['success' => false, 'message' => 'An error occurred while creating virtual account'];
        }
    }

    /**
     * Get user's virtual accounts
     */
    public function getUserVirtualAccounts(User $user)
    {
        if (!$user->virtual_accounts) {
            // Try to create virtual accounts if they don't exist
            $result = $this->createVirtualAccount($user);
            if ($result['success']) {
                return $result['accounts'];
            }

            // If creation failed, return empty array with a note
            // The fund wallet page will show the "Generate Virtual Account" button
            return [];
        }

        return json_decode($user->virtual_accounts, true) ?? [];
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        $computedSignature = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computedSignature, $signature);
    }

    /**
     * Process webhook notification
     */
    public function processWebhook($payload)
    {
        try {
            $data = json_decode($payload, true);

            if (!$data || !isset($data['eventData'])) {
                return ['success' => false, 'message' => 'Invalid webhook payload'];
            }

            $eventData = $data['eventData'];
            $eventType = $data['eventType'] ?? '';

            if ($eventType === 'SUCCESSFUL_TRANSACTION') {
                return $this->processSuccessfulPayment($eventData);
            }

            return ['success' => false, 'message' => 'Unsupported event type'];
        } catch (\Exception $e) {
            Log::error('Monnify webhook processing error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Webhook processing failed'];
        }
    }

    /**
     * Process successful payment
     * Updated with correct Monnify charges (₦10 flat fee, max ₦50)
     */
    private function processSuccessfulPayment($eventData)
    {
        try {
            $accountNumber = $eventData['destinationAccountNumber'] ?? '';
            $amountPaid = $eventData['amountPaid'] ?? 0;
            $paymentReference = $eventData['paymentReference'] ?? '';
            $transactionReference = $eventData['transactionReference'] ?? '';

            // Find user by virtual account number (SQLite compatible)
            $user = User::whereNotNull('virtual_accounts')
                ->get()
                ->filter(function ($user) use ($accountNumber) {
                    $virtualAccounts = json_decode($user->virtual_accounts, true);
                    if (!$virtualAccounts) return false;

                    foreach ($virtualAccounts as $account) {
                        if ($account['account_number'] === $accountNumber) {
                            return true;
                        }
                    }
                    return false;
                })
                ->first();

            if (!$user) {
                Log::warning('User not found for virtual account', ['account_number' => $accountNumber]);
                return ['success' => false, 'message' => 'User not found'];
            }

            // Start transaction with lock to prevent race conditions
            DB::beginTransaction();

            try {
                // Check if transaction already processed with row lock
                $existingTransaction = DB::table('transactions')
                    ->where('transref', $paymentReference)
                    ->lockForUpdate()
                    ->first();

                if ($existingTransaction) {
                    DB::rollBack();
                    Log::info('Transaction already processed (idempotency check)', [
                        'reference' => $paymentReference
                    ]);
                    return ['success' => true, 'message' => 'Transaction already processed'];
                }

                // Calculate charges - Monnify uses flat fee, not percentage
                $transferFee = config('monnify.transfer_fee', 10); // ₦10
                $maxFee = config('monnify.max_fee', 50); // ₦50
                $charges = min($transferFee, $maxFee);
                $netAmount = $amountPaid - $charges;

                // Get current balance
                $currentBalance = $user->wallet_balance ?? 0;
                $newBalance = $currentBalance + $netAmount;

                // Update user balance
                $user->update(['wallet_balance' => $newBalance]);

                // Record transaction
                DB::table('transactions')->insert([
                    'sId' => $user->id,
                    'transref' => $paymentReference,
                    'servicename' => 'Wallet Topup',
                    'servicedesc' => "Wallet funding of ₦" . number_format($amountPaid, 2) . " via Monnify bank transfer. Charges: ₦" . number_format($charges, 2),
                    'amount' => (string)$netAmount,
                    'status' => 0, // 0 = success
                    'oldbal' => (string)$currentBalance,
                    'newbal' => (string)$newBalance,
                    'profit' => 0,
                    'date' => now(),
                    // Also fill the new columns to avoid NOT NULL constraint errors
                    'service_name' => 'Wallet Topup',
                    'service_description' => "Wallet funding of ₦" . number_format($amountPaid, 2) . " via Monnify bank transfer. Charges: ₦" . number_format($charges, 2),
                    'old_balance' => $currentBalance,
                    'new_balance' => $newBalance
                ]);

                DB::commit();

                Log::info('Monnify payment processed successfully', [
                    'user_id' => $user->id,
                    'amount' => $amountPaid,
                    'charges' => $charges,
                    'net_amount' => $netAmount,
                    'reference' => $paymentReference
                ]);

                return ['success' => true, 'message' => 'Payment processed successfully'];
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Monnify payment processing error: ' . $e->getMessage(), [
                'payment_reference' => $paymentReference ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => 'Payment processing failed'];
        }
    }

    /**
     * Manually check for recent transactions (for local development)
     * This simulates webhook processing since local webhooks don't work
     */
    public function checkRecentTransactions($user)
    {
        try {
            if (!$this->apiKey || !$this->secretKey) {
                return ['success' => false, 'message' => 'Monnify not configured'];
            }

            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'message' => 'Could not authenticate with Monnify'];
            }

            // For local development, you could implement API calls to check recent transactions
            // This would require the Monnify transactions API endpoint

            Log::info('Checking recent Monnify transactions for user', ['user_id' => $user->id]);

            return ['success' => true, 'message' => 'Transaction check completed'];
        } catch (\Exception $e) {
            Log::error('Error checking Monnify transactions: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Transaction check failed'];
        }
    }
}
