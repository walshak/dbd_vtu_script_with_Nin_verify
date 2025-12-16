<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\ApiConfig;
use App\Models\WalletProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WalletService
{
    /**
     * Add funds to user wallet (compatible with old PHP app)
     */
    public function addFunds($userId, $amount, $paymentMethod = 'Monnify', $reference = null, $charges = 0)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            if ($amount <= 0) {
                return $this->errorResponse('Invalid amount');
            }

            $oldBalance = $user->wallet_balance;

            // Calculate new balance after charges
            $chargesAmount = $charges > 0 ? ($amount * $charges / 100) : 0;
            $creditAmount = $amount - $chargesAmount;
            $newBalance = $oldBalance + $creditAmount;

            // Update user wallet
            $user->wallet_balance = $newBalance;
            $user->save();

            // Record transaction (compatible with old system)
            $transaction = Transaction::recordWalletFunding(
                $userId,
                $amount,
                $oldBalance,
                $newBalance,
                $paymentMethod,
                $charges
            );

            return $this->successResponse('Wallet funded successfully', [
                'transaction_id' => $transaction->tId,
                'reference' => $transaction->transref,
                'amount' => $amount,
                'credit_amount' => $creditAmount,
                'charges' => $chargesAmount,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'payment_method' => $paymentMethod
            ]);
        } catch (Exception $e) {
            Log::error('Add Funds Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to add funds');
        }
    }

    /**
     * Credit user wallet by admin (compatible with old PHP app)
     */
    public function creditUser($userId, $amount, $reason = '', $adminEmail = '')
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            if ($amount <= 0) {
                return $this->errorResponse('Invalid amount');
            }

            $oldBalance = $user->wallet_balance;
            $newBalance = $oldBalance + $amount;

            // Update user wallet
            $user->wallet_balance = $newBalance;
            $user->save();

            // Record transaction (compatible with old system)
            $transaction = Transaction::recordWalletCredit(
                $userId,
                $amount,
                $oldBalance,
                $newBalance,
                $reason,
                $adminEmail ?: $user->email
            );

            return $this->successResponse('User wallet credited successfully', [
                'transaction_id' => $transaction->id ?? $transaction->tId,
                'reference' => $transaction->reference ?? $transaction->transref,
                'amount' => $amount,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'reason' => $reason
            ]);
        } catch (Exception $e) {
            Log::error('Credit User Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to credit user');
        }
    }

    /**
     * Debit user wallet for VTU purchase (compatible with old PHP app)
     */
    public function debitWallet($userId, $amount, $serviceName, $description, $profit = 0)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            if ($amount <= 0) {
                return $this->errorResponse('Invalid amount');
            }

            if ($user->wallet_balance < $amount) {
                return $this->errorResponse('Insufficient balance');
            }

            $oldBalance = $user->wallet_balance;
            $newBalance = $oldBalance - $amount;

            // Update user wallet
            $user->wallet_balance = $newBalance;
            $user->save();

            // Record transaction (compatible with old system)
            $transaction = Transaction::recordVtuTransaction(
                $userId,
                $serviceName,
                $description,
                $amount,
                $oldBalance,
                $newBalance,
                $profit
            );

            return $this->successResponse('Wallet debited successfully', [
                'transaction_id' => $transaction->tId,
                'reference' => $transaction->transref,
                'amount' => $amount,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'service' => $serviceName,
                'profit' => $profit
            ]);
        } catch (Exception $e) {
            Log::error('Debit Wallet Error: ' . $e->getMessage());

            // Refund user if error occurred after deduction
            if (isset($user) && isset($amount)) {
                $user->wallet_balance += $amount;
                $user->save();
            }

            return $this->errorResponse('Failed to debit wallet');
        }
    }

    /**
     * Transfer funds between users
     */
    public function transferFunds($fromUserId, $toUserEmail, $amount, $description = '')
    {
        try {
            // Validate sender
            $fromUser = User::find($fromUserId);
            if (!$fromUser) {
                return $this->errorResponse('Sender not found');
            }

            // Validate recipient
            $toUser = User::where('sEmail', $toUserEmail)->first();
            if (!$toUser) {
                return $this->errorResponse('Recipient not found');
            }

            if ($fromUser->sId === $toUser->sId) {
                return $this->errorResponse('Cannot transfer to yourself');
            }

            if ($amount <= 0) {
                return $this->errorResponse('Invalid amount');
            }

            if ($fromUser->wallet_balance < $amount) {
                return $this->errorResponse('Insufficient balance');
            }

            $reference = Transaction::generateReference();

            // Deduct from sender
            $fromOldBalance = $fromUser->wallet_balance;
            $fromNewBalance = $fromOldBalance - $amount;
            $fromUser->wallet_balance = $fromNewBalance;
            $fromUser->save();

            // Record sender transaction
            Transaction::recordTransaction([
                'user_id' => $fromUserId,
                'servicename' => 'Wallet Transfer',
                'description' => "Transfer of N{$amount} to {$toUser->sUserName}. " . ($description ?: ''),
                'amount' => $amount,
                'old_balance' => $fromOldBalance,
                'new_balance' => $fromNewBalance,
                'profit' => 0,
                'status' => Transaction::STATUS_SUCCESS,
                'reference' => $reference . '_OUT'
            ]);

            // Add to recipient
            $toOldBalance = $toUser->wallet_balance;
            $toNewBalance = $toOldBalance + $amount;
            $toUser->wallet_balance = $toNewBalance;
            $toUser->save();

            // Record recipient transaction
            Transaction::recordTransaction([
                'user_id' => $toUser->sId,
                'servicename' => 'Wallet Credit',
                'description' => "Transfer of N{$amount} from {$fromUser->sUserName}. " . ($description ?: ''),
                'amount' => $amount,
                'old_balance' => $toOldBalance,
                'new_balance' => $toNewBalance,
                'profit' => 0,
                'status' => Transaction::STATUS_SUCCESS,
                'reference' => $reference . '_IN'
            ]);

            return $this->successResponse('Transfer successful', [
                'reference' => $reference,
                'amount' => $amount,
                'recipient' => $toUser->sUserName,
                'sender_balance' => $fromNewBalance,
                'recipient_balance' => $toNewBalance
            ]);
        } catch (Exception $e) {
            Log::error('Transfer Funds Error: ' . $e->getMessage());

            // Refund sender if error occurred after deduction
            if (isset($fromUser) && isset($amount) && isset($fromOldBalance)) {
                $fromUser->wallet_balance = $fromOldBalance;
                $fromUser->save();
            }

            return $this->errorResponse('Transfer failed');
        }
    }

    /**
     * Reverse/refund a transaction (compatible with old PHP app)
     */
    public function reverseTransaction($transactionId, $reason = 'Transaction reversed')
    {
        try {
            $transaction = Transaction::find($transactionId);
            if (!$transaction) {
                return $this->errorResponse('Transaction not found');
            }

            if ($transaction->status !== Transaction::STATUS_SUCCESS) {
                return $this->errorResponse('Can only reverse successful transactions');
            }

            $user = $transaction->user;
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            $amount = floatval($transaction->amount);
            $oldBalance = $user->wallet_balance;
            $newBalance = $oldBalance + $amount;

            // Update user wallet
            $user->wallet_balance = $newBalance;
            $user->save();

            // Mark original transaction as failed
            $transaction->updateStatus(Transaction::STATUS_FAILED);

            // Record reversal transaction
            $reversalTransaction = Transaction::recordTransaction([
                'user_id' => $user->sId,
                'servicename' => 'Transaction Reversal',
                'description' => "Reversal of transaction {$transaction->transref}. Reason: {$reason}",
                'amount' => $amount,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'profit' => 0,
                'status' => Transaction::STATUS_SUCCESS
            ]);

            return $this->successResponse('Transaction reversed successfully', [
                'original_transaction' => $transaction->transref,
                'reversal_transaction' => $reversalTransaction->transref,
                'amount' => $amount,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'reason' => $reason
            ]);
        } catch (Exception $e) {
            Log::error('Reverse Transaction Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to reverse transaction');
        }
    }

    /**
     * Get user wallet balance
     */
    public function getBalance($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        return [
            'balance' => $user->wallet_balance,
            'formatted_balance' => '₦' . number_format($user->wallet_balance, 2)
        ];
    }

    /**
     * Get wallet transaction history (compatible with old PHP app)
     */
    public function getTransactionHistory($userId, $limit = 50, $serviceName = null)
    {
        $query = Transaction::where('sId', $userId)
            ->orderBy('date', 'desc');

        if ($serviceName) {
            $query->where('servicename', $serviceName);
        }

        $transactions = $query->limit($limit)->get();

        return $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->tId,
                'service' => $transaction->servicename,
                'description' => $transaction->servicedesc,
                'amount' => $transaction->amount,
                'formatted_amount' => '₦' . number_format(floatval($transaction->amount), 2),
                'status' => $transaction->status_text,
                'reference' => $transaction->transref,
                'old_balance' => $transaction->oldbal,
                'new_balance' => $transaction->newbal,
                'profit' => $transaction->profit,
                'date' => $transaction->date,
                'formatted_date' => $transaction->formatted_date
            ];
        });
    }

    /**
     * Get wallet statistics (compatible with old PHP app)
     */
    public function getWalletStats($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        $transactions = Transaction::where('sId', $userId)->get();
        $successfulTransactions = $transactions->where('status', Transaction::STATUS_SUCCESS);

        return [
            'current_balance' => $user->wallet_balance,
            'formatted_balance' => '₦' . number_format($user->wallet_balance, 2),
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $successfulTransactions->count(),
            'failed_transactions' => $transactions->where('status', Transaction::STATUS_FAILED)->count(),
            'total_spent' => $successfulTransactions->sum(function ($t) {
                return floatval($t->amount);
            }),
            'total_funded' => $successfulTransactions->where('servicename', Transaction::SERVICE_WALLET_TOPUP)->sum(function ($t) {
                return floatval($t->amount);
            }),
            'total_credited' => $successfulTransactions->where('servicename', Transaction::SERVICE_WALLET_CREDIT)->sum(function ($t) {
                return floatval($t->amount);
            }),
            'last_transaction' => $transactions->sortByDesc('date')->first(),
            'favorite_service' => $transactions->groupBy('servicename')->sortByDesc(function ($group) {
                return $group->count();
            })->keys()->first()
        ];
    }

    /**
     * Check if user has sufficient balance for transaction
     */
    public function hasSufficientBalance($userId, $amount)
    {
        $user = User::find($userId);
        return $user && $user->wallet_balance >= $amount;
    }

    /**
     * Get minimum balance requirement
     */
    public function getMinimumBalance()
    {
        return 0; // Configurable if needed
    }

    /**
     * Success response helper
     */
    protected function successResponse($message, $data = [])
    {
        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Error response helper
     */
    /**
     * Get best wallet provider for transaction
     */
    public function getBestProvider($amount = 0)
    {
        if ($amount > 0) {
            return WalletProvider::active()
                ->withSufficientBalance($amount)
                ->orderBy('priority')
                ->first();
        }

        return WalletProvider::active()
            ->orderBy('priority')
            ->first();
    }

    /**
     * Update provider balance from API
     */
    public function updateProviderBalance($providerId)
    {
        $provider = WalletProvider::find($providerId);
        if (!$provider) {
            return $this->errorResponse('Provider not found');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $provider->api_key,
                'Content-Type' => 'application/json',
            ])->get($provider->api_url . 'balance');

            if ($response->successful()) {
                $data = $response->json();
                $provider->updateBalance($data['balance'] ?? 0);

                return $this->successResponse('Provider balance updated', [
                    'provider' => $provider->provider_name,
                    'balance' => $provider->balance,
                ]);
            }

            return $this->errorResponse('Failed to fetch provider balance');
        } catch (Exception $e) {
            Log::error('Provider balance update error: ' . $e->getMessage());
            return $this->errorResponse('Provider connection failed');
        }
    }

    /**
     * Get all providers status
     */
    public function getProvidersStatus()
    {
        $providers = WalletProvider::all();
        $status = [];

        foreach ($providers as $provider) {
            $health = $this->checkProviderHealth($provider);
            $status[] = [
                'id' => $provider->id,
                'name' => $provider->provider_name,
                'balance' => $provider->balance,
                'is_active' => $provider->is_active,
                'priority' => $provider->priority,
                'health' => $health,
            ];
        }

        return [
            'status' => 'success',
            'providers' => $status,
            'total_providers' => count($providers),
            'active_providers' => count(array_filter($status, fn($p) => $p['is_active'])),
        ];
    }

    /**
     * Check provider health
     */
    private function checkProviderHealth($provider)
    {
        try {
            $response = Http::timeout(5)->get($provider->api_url . 'health');
            return $response->successful() ? 'healthy' : 'unhealthy';
        } catch (Exception $e) {
            return 'unhealthy';
        }
    }

    /**
     * Switch to next available provider
     */
    public function switchProvider($amount = 0)
    {
        $currentProvider = $this->getBestProvider($amount);

        if (!$currentProvider) {
            return $this->errorResponse('No available providers');
        }

        // Try to find alternative provider
        $alternativeProvider = WalletProvider::active()
            ->where('id', '!=', $currentProvider->id)
            ->withSufficientBalance($amount)
            ->orderBy('priority')
            ->first();

        if ($alternativeProvider) {
            return $this->successResponse('Alternative provider found', [
                'provider' => $alternativeProvider->provider_name,
                'balance' => $alternativeProvider->balance,
            ]);
        }

        return $this->errorResponse('No alternative provider available');
    }

    /**
     * Process transaction with provider fallback
     */
    public function processWithFallback($amount, $operation, $data = [])
    {
        $providers = WalletProvider::active()
            ->withSufficientBalance($amount)
            ->orderBy('priority')
            ->get();

        foreach ($providers as $provider) {
            try {
                $result = $this->executeProviderOperation($provider, $operation, array_merge($data, [
                    'amount' => $amount
                ]));

                if ($result['status'] === 'success') {
                    // Update provider balance on successful transaction
                    $provider->debitBalance($amount);
                    return $result;
                }
            } catch (Exception $e) {
                Log::warning("Provider {$provider->provider_name} failed: " . $e->getMessage());
                continue;
            }
        }

        return $this->errorResponse('All providers failed');
    }

    /**
     * Execute operation on specific provider
     */
    private function executeProviderOperation($provider, $operation, $data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $provider->api_key,
            'Content-Type' => 'application/json',
        ])->post($provider->api_url . $operation, $data);

        if ($response->successful()) {
            return $this->successResponse('Operation successful', [
                'provider' => $provider->provider_name,
                'response' => $response->json(),
            ]);
        }

        throw new Exception('Provider operation failed');
    }

    private function errorResponse($message, $data = null)
    {
        return [
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ];
    }
}
