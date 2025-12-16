<?php

namespace App\Services;

use App\Models\AlphaTopup;
use App\Models\User;
use App\Services\ExternalApiService;
use App\Services\WalletService;
use App\Services\ConfigurationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class AlphaTopupService
{
    protected $externalApiService;
    protected $walletService;
    protected $configurationService;

    public function __construct(
        ExternalApiService $externalApiService,
        WalletService $walletService,
        ConfigurationService $configurationService
    ) {
        $this->externalApiService = $externalApiService;
        $this->walletService = $walletService;
        $this->configurationService = $configurationService;
    }

    /**
     * Purchase alpha topup
     */
    public function purchaseAlphaTopup(int $userId, float $amount, string $recipientPhone): array
    {
        try {
            DB::beginTransaction();

            // Validate user
            $user = User::find($userId);
            if (!$user) {
                return $this->errorResponse('User not found');
            }

            // Validate amount
            if ($amount <= 0) {
                return $this->errorResponse('Invalid amount specified');
            }

            // Validate phone number
            if (!$this->validatePhoneNumber($recipientPhone)) {
                return $this->errorResponse('Invalid phone number format');
            }

            // Check wallet balance
            if ($user->sWalletBalance < $amount) {
                return $this->errorResponse('Insufficient wallet balance. Current balance: ₦' . number_format($user->sWalletBalance, 2));
            }

            // Generate unique reference
            $reference = 'ALPHA_' . time() . '_' . $userId . '_' . rand(1000, 9999);

            // Debit user wallet
            $this->walletService->debitWallet($userId, $amount, 'Alpha Topup Purchase', $reference);

            // Call external API to purchase alpha topup
            $apiResponse = $this->externalApiService->purchaseAlphaTopup($amount, $recipientPhone, $reference);

            if ($apiResponse['success']) {
                // Record successful transaction
                $transactionData = [
                    'user_id' => $userId,
                    'type' => 'alpha_topup',
                    'amount' => $amount,
                    'recipient_phone' => $recipientPhone,
                    'reference' => $reference,
                    'server_reference' => $apiResponse['server_reference'] ?? $reference,
                    'status' => 'completed',
                    'description' => "Alpha Topup of ₦{$amount} to {$recipientPhone}",
                    'api_response' => json_encode($apiResponse['api_response'] ?? [])
                ];

                $this->recordTransaction($transactionData);

                DB::commit();

                Log::info('Alpha topup purchase successful', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'phone' => substr($recipientPhone, 0, 4) . '***' . substr($recipientPhone, -3),
                    'reference' => $reference
                ]);

                return [
                    'status' => 'success',
                    'message' => 'Alpha Topup completed successfully',
                    'data' => [
                        'reference' => $reference,
                        'server_reference' => $apiResponse['server_reference'] ?? $reference,
                        'amount' => $amount,
                        'recipient_phone' => $recipientPhone,
                        'balance_after' => $user->fresh()->sWalletBalance,
                        'transaction_date' => now()->format('Y-m-d H:i:s')
                    ]
                ];
            } else {
                // Refund on failure
                $this->walletService->creditWallet($userId, $amount, 'Alpha Topup Refund - Failed transaction', $reference);

                // Record failed transaction
                $transactionData = [
                    'user_id' => $userId,
                    'type' => 'alpha_topup',
                    'amount' => $amount,
                    'recipient_phone' => $recipientPhone,
                    'reference' => $reference,
                    'status' => 'failed',
                    'description' => "Failed Alpha Topup of ₦{$amount} to {$recipientPhone}",
                    'api_response' => json_encode($apiResponse['api_response'] ?? [])
                ];

                $this->recordTransaction($transactionData);

                DB::commit();

                Log::warning('Alpha topup purchase failed', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'phone' => substr($recipientPhone, 0, 4) . '***' . substr($recipientPhone, -3),
                    'reference' => $reference,
                    'error' => $apiResponse['message'] ?? 'Unknown error'
                ]);

                return $this->errorResponse($apiResponse['message'] ?? 'Alpha Topup purchase failed. Your wallet has been refunded.');
            }

        } catch (Exception $e) {
            DB::rollback();

            // Attempt to refund if wallet was debited
            try {
                if (isset($reference)) {
                    $this->walletService->creditWallet($userId, $amount, 'Alpha Topup Refund - System error', $reference);
                }
            } catch (Exception $refundError) {
                Log::error('Alpha topup refund failed', [
                    'user_id' => $userId,
                    'reference' => $reference ?? 'N/A',
                    'refund_error' => $refundError->getMessage()
                ]);
            }

            Log::error('Alpha topup purchase system error', [
                'user_id' => $userId,
                'amount' => $amount,
                'phone' => substr($recipientPhone, 0, 4) . '***' . substr($recipientPhone, -3),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('System error occurred. Please contact support if amount was debited.');
        }
    }

    /**
     * Get alpha topup plans
     */
    public function getAlphaTopupPlans(): array
    {
        try {
            $plans = AlphaTopup::getActivePlans();

            return [
                'status' => 'success',
                'data' => $plans->map(function ($plan) {
                    return [
                        'id' => $plan->alphaId,
                        'buying_price' => $plan->buyingPrice,
                        'selling_price' => $plan->sellingPrice,
                        'agent_price' => $plan->agent,
                        'vendor_price' => $plan->vendor,
                        'formatted_price' => $plan->formattedSellingPriceAttribute,
                        'description' => $plan->descriptionAttribute
                    ];
                })->toArray()
            ];

        } catch (Exception $e) {
            Log::error('Error fetching alpha topup plans: ' . $e->getMessage());
            return $this->errorResponse('Unable to fetch alpha topup plans');
        }
    }

    /**
     * Get alpha topup transaction history
     */
    public function getAlphaTopupHistory(int $userId, int $limit = 20): array
    {
        try {
            // Get transaction history from user transactions table
            $transactions = DB::table('user_transactions')
                ->where('user_id', $userId)
                ->where('type', 'alpha_topup')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return [
                'status' => 'success',
                'data' => $transactions->map(function ($transaction) {
                    return [
                        'reference' => $transaction->reference,
                        'server_reference' => $transaction->server_reference,
                        'amount' => $transaction->amount,
                        'recipient_phone' => $transaction->recipient_phone,
                        'status' => $transaction->status,
                        'description' => $transaction->description,
                        'date' => $transaction->created_at,
                        'formatted_amount' => '₦' . number_format($transaction->amount, 2)
                    ];
                })->toArray()
            ];

        } catch (Exception $e) {
            Log::error('Alpha topup history error: ' . $e->getMessage());
            return $this->errorResponse('Unable to fetch alpha topup history');
        }
    }

    /**
     * Print alpha topup receipt
     */
    public function printReceipt(string $reference): array
    {
        try {
            $transaction = DB::table('user_transactions')
                ->where('reference', $reference)
                ->where('type', 'alpha_topup')
                ->first();

            if (!$transaction) {
                return $this->errorResponse('Transaction not found');
            }

            $user = User::find($transaction->user_id);

            return [
                'status' => 'success',
                'receipt_data' => [
                    'company_name' => config('app.name', 'VTU Service'),
                    'transaction_type' => 'Alpha Topup',
                    'reference' => $transaction->reference,
                    'server_reference' => $transaction->server_reference,
                    'customer_name' => $user->sFname . ' ' . $user->sLname,
                    'customer_phone' => $user->sPhone,
                    'recipient_phone' => $transaction->recipient_phone,
                    'amount' => '₦' . number_format($transaction->amount, 2),
                    'status' => ucfirst($transaction->status),
                    'date' => $transaction->created_at,
                    'description' => $transaction->description
                ]
            ];

        } catch (Exception $e) {
            Log::error('Alpha topup receipt error: ' . $e->getMessage());
            return $this->errorResponse('Unable to generate receipt');
        }
    }

    /**
     * Validate phone number format
     */
    private function validatePhoneNumber(string $phone): bool
    {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Nigerian phone number validation patterns
        if (substr($phone, 0, 1) === '0' && strlen($phone) === 11) {
            return true; // 0XXXXXXXXXX format
        }

        if (substr($phone, 0, 3) === '234' && strlen($phone) === 13) {
            return true; // 234XXXXXXXXXX format
        }

        if (strlen($phone) === 10) {
            return true; // XXXXXXXXXX format
        }

        return false;
    }

    /**
     * Record transaction in database
     */
    private function recordTransaction(array $data): void
    {
        DB::table('user_transactions')->insert([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'reference' => $data['reference'],
            'server_reference' => $data['server_reference'] ?? null,
            'amount' => $data['amount'],
            'recipient_phone' => $data['recipient_phone'] ?? null,
            'status' => $data['status'],
            'description' => $data['description'],
            'api_response' => $data['api_response'] ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Return error response
     */
    private function errorResponse(string $message): array
    {
        return [
            'status' => 'error',
            'message' => $message
        ];
    }
}
