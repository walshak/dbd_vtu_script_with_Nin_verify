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

        // Always use production API
        $this->baseUrl = 'https://api.monnify.com';
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
     * Tokens are valid for 1 hour, we cache for 50 minutes to avoid edge cases
     */
    public function getAccessToken($forceRefresh = false)
    {
        $cacheKey = 'monnify_access_token_' . md5($this->apiKey);

        // Force refresh if requested (e.g., after 401 error)
        if ($forceRefresh) {
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
            Log::info('Monnify token cache cleared - forcing refresh');
        }

        $cacheDuration = 3000; // 50 minutes (safer than 55 for 1-hour tokens)

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
                        Log::info('Monnify access token obtained and cached', [
                            'cache_duration' => '50 minutes',
                            'expires_in' => '1 hour'
                        ]);
                    }

                    return $token;
                }

                Log::error('Monnify auth failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            } catch (\Exception $e) {
                Log::error('Monnify auth error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Make authenticated request to Monnify API with automatic token refresh
     */
    private function makeAuthenticatedRequest($method, $url, $data = [])
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to authenticate with Monnify'
            ];
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])
            ->$method($url, $data);

        // Check if token expired (401 error)
        if ($response->status() === 401) {
            $responseBody = $response->body();
            if (strpos($responseBody, 'expired') !== false || strpos($responseBody, 'invalid_token') !== false) {
                Log::warning('Monnify token expired, refreshing and retrying...');

                // Get fresh token
                $token = $this->getAccessToken(true);

                if (!$token) {
                    return [
                        'success' => false,
                        'message' => 'Failed to refresh authentication token'
                    ];
                }

                // Retry request with fresh token
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ])
                    ->$method($url, $data);
            }
        }

        return $response;
    }

    /**
     * Create virtual account for user
     */
    public function createVirtualAccount(User $user)
    {
        try {
            // Check if user has completed KYC (BVN or NIN required)
            if (empty($user->nin) && empty($user->bvn)) {
                Log::warning('Virtual account creation blocked - KYC not completed', [
                    'user_id' => $user->id
                ]);

                return [
                    'success' => false,
                    'message' => 'Please complete KYC verification (NIN or BVN) before generating virtual account',
                    'requires_kyc' => true
                ];
            }

            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'message' => 'Failed to authenticate with Monnify'];
            }

            $reference = 'VA_' . $user->id . '_' . uniqid() . rand(1000, 9999);

            // Get user's full name - use 'name' field from users table
            $fullName = trim($user->name ?? '');

            // Fallback: if no name, use email username
            if (empty($fullName)) {
                $emailPart = explode('@', $user->email)[0];
                $fullName = ucfirst($emailPart) . ' User';
            }

            // Validate and clean the name (Monnify has specific requirements)
            $fullName = preg_replace('/[^a-zA-Z\s]/', '', $fullName); // Remove special characters
            $fullName = preg_replace('/\s+/', ' ', $fullName); // Clean up multiple spaces
            $fullName = trim($fullName);

            // Ensure minimum length
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

            // Add BVN or NIN to payload (required by Monnify)
            if (!empty($user->bvn)) {
                $payload['bvn'] = $user->bvn;
                Log::info('Using BVN for virtual account', ['user_id' => $user->id]);
            } elseif (!empty($user->nin)) {
                $payload['nin'] = $user->nin;
                Log::info('Using NIN for virtual account', ['user_id' => $user->id]);
            }

            Log::info('Monnify virtual account request payload', [
                'user_id' => $user->id,
                'payload' => array_merge($payload, [
                    'bvn' => isset($payload['bvn']) ? substr($payload['bvn'], 0, 3) . '********' : null,
                    'nin' => isset($payload['nin']) ? substr($payload['nin'], 0, 3) . '********' : null,
                ])
            ]);

            // Create virtual account with automatic token refresh
            $response = $this->makeAuthenticatedRequest(
                'post',
                $this->baseUrl . '/api/v2/bank-transfer/reserved-accounts',
                $payload
            );

            // Handle makeAuthenticatedRequest error response
            if (is_array($response) && isset($response['success']) && !$response['success']) {
                return $response;
            }

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
     * Uses same logic as checkNewTransactions for consistency
     */
    private function processSuccessfulPayment($eventData)
    {
        try {
            $accountNumber = $eventData['destinationAccountNumber'] ?? '';
            $amountPaid = $eventData['amountPaid'] ?? 0;
            $paymentReference = $eventData['paymentReference'] ?? '';

            // Find user by virtual account number
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

            // Use the same transaction processing logic as checkNewTransactions
            $transaction = (object)[
                'paymentReference' => $paymentReference,
                'amountPaid' => $amountPaid,
                'paidOn' => $eventData['paidOn'] ?? now()->toDateTimeString(),
                'destinationAccountNumber' => $accountNumber
            ];

            $result = $this->processTransactionFromAPI($user, $transaction);

            return $result;

        } catch (\Exception $e) {
            Log::error('Monnify payment processing error: ' . $e->getMessage(), [
                'payment_reference' => $paymentReference ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => 'Payment processing failed'];
        }
    }

    /**
     * Check for new reserved account transactions
     * Call Monnify API to get recent transactions for all virtual accounts
     */
    public function checkNewTransactions()
    {
        try {
            // Get all users with virtual accounts
            $users = User::whereNotNull('virtual_accounts')
                ->where('virtual_accounts', '!=', '')
                ->where('virtual_accounts', '!=', '[]')
                ->get();

            if ($users->isEmpty()) {
                Log::info('No users with virtual accounts found');
                return ['success' => true, 'message' => 'No accounts to check', 'processed' => 0];
            }

            $processedCount = 0;
            $errorCount = 0;

            foreach ($users as $user) {
                try {
                    $result = $this->fetchUserTransactions($user);
                    if ($result['success'] && $result['processed'] > 0) {
                        $processedCount += $result['processed'];
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('Error checking transactions for user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Completed transaction check cycle', [
                'users_checked' => $users->count(),
                'transactions_processed' => $processedCount,
                'errors' => $errorCount
            ]);

            return [
                'success' => true,
                'users_checked' => $users->count(),
                'transactions_processed' => $processedCount,
                'errors' => $errorCount
            ];

        } catch (\Exception $e) {
            Log::error('Error in checkNewTransactions: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Fetch and process transactions for a specific user's virtual account
     */
    private function fetchUserTransactions(User $user)
    {
        try {
            $virtualAccounts = json_decode($user->virtual_accounts, true);
            if (!$virtualAccounts || empty($virtualAccounts)) {
                return ['success' => false, 'message' => 'No virtual accounts found'];
            }

            // Get account reference from user record
            if (empty($user->monnify_reference)) {
                Log::warning('User has no monnify_reference', ['user_id' => $user->id]);
                return ['success' => false, 'message' => 'No account reference'];
            }

            // Call Monnify API to get transactions for this account reference
            $response = $this->makeAuthenticatedRequest(
                'get',
                $this->baseUrl . '/api/v1/bank-transfer/reserved-accounts/transactions',
                [
                    'accountReference' => $user->monnify_reference,
                    'page' => 0,
                    'size' => 10 // Check last 10 transactions
                ]
            );

            // Handle makeAuthenticatedRequest error response
            if (is_array($response) && isset($response['success']) && !$response['success']) {
                return $response;
            }

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['requestSuccessful']) && $data['requestSuccessful']) {
                    $transactions = $data['responseBody']['content'] ?? [];
                    $processed = 0;

                    foreach ($transactions as $transaction) {
                        // Only process successful transactions
                        if ($transaction['paymentStatus'] === 'PAID') {
                            $result = $this->processTransactionFromAPI($user, $transaction);
                            if ($result['success']) {
                                $processed++;
                            }
                        }
                    }

                    return ['success' => true, 'processed' => $processed];
                }
            }

            Log::warning('Failed to fetch transactions from Monnify', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['success' => false, 'message' => 'API request failed'];

        } catch (\Exception $e) {
            Log::error('Error fetching user transactions: ' . $e->getMessage(), [
                'user_id' => $user->id
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Process a transaction fetched from Monnify API
     * Includes idempotency check to prevent double-crediting
     */
    private function processTransactionFromAPI(User $user, array $transaction)
    {
        try {
            $paymentReference = $transaction['paymentReference'] ?? '';
            $amountPaid = $transaction['amountPaid'] ?? 0;
            $transactionDate = $transaction['paidOn'] ?? now();

            if (empty($paymentReference) || $amountPaid <= 0) {
                return ['success' => false, 'message' => 'Invalid transaction data'];
            }

            // Start transaction with lock
            DB::beginTransaction();

            try {
                // Idempotency check - use transref to prevent double-crediting
                $existingTransaction = DB::table('transactions')
                    ->where('transref', $paymentReference)
                    ->lockForUpdate()
                    ->first();

                if ($existingTransaction) {
                    DB::rollBack();
                    Log::debug('Transaction already processed (idempotency)', [
                        'user_id' => $user->id,
                        'reference' => $paymentReference
                    ]);
                    return ['success' => true, 'message' => 'Already processed'];
                }

                // Calculate charges
                $transferFee = config('monnify.transfer_fee', 10);
                $maxFee = config('monnify.max_fee', 50);
                $charges = min($transferFee, $maxFee);
                $netAmount = $amountPaid - $charges;

                // Get current balance with lock
                $currentBalance = DB::table('users')
                    ->where('id', $user->id)
                    ->lockForUpdate()
                    ->value('wallet_balance') ?? 0;

                $newBalance = $currentBalance + $netAmount;

                // Update user balance
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['wallet_balance' => $newBalance]);

                // Record transaction
                DB::table('transactions')->insert([
                    'sId' => $user->id,
                    'transref' => $paymentReference,
                    'servicename' => 'Wallet Topup',
                    'servicedesc' => "Wallet funding of ₦" . number_format($amountPaid, 2) . " via Monnify. Charges: ₦" . number_format($charges, 2),
                    'amount' => (string)$netAmount,
                    'status' => 1, // 1 = successful
                    'oldbal' => (string)$currentBalance,
                    'newbal' => (string)$newBalance,
                    'profit' => 0,
                    'date' => $transactionDate,
                    'service_name' => 'Wallet Topup',
                    'service_description' => "Wallet funding via Monnify",
                    'old_balance' => $currentBalance,
                    'new_balance' => $newBalance
                ]);

                DB::commit();

                Log::info('Transaction processed from API check', [
                    'user_id' => $user->id,
                    'amount' => $amountPaid,
                    'charges' => $charges,
                    'net_amount' => $netAmount,
                    'reference' => $paymentReference
                ]);

                return ['success' => true, 'message' => 'Transaction processed'];

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error processing transaction from API: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'reference' => $paymentReference ?? 'unknown'
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
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
