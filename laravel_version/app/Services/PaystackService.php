<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaystackService
{
    private $secretKey;
    private $publicKey;
    private $charges;
    private $status;
    private $baseUrl;

    public function __construct()
    {
        $this->secretKey = $this->getConfigValue('paystackApi');
        $this->charges = $this->getConfigValue('paystackCharges') ?? 1.5;
        $this->status = $this->getConfigValue('paystackStatus') ?? 'Off';
        $this->baseUrl = 'https://api.paystack.co';
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
            return null;
        }
    }

    /**
     * Check if Paystack is enabled
     */
    public function isEnabled()
    {
        return $this->status === 'On' && !empty($this->secretKey);
    }

    /**
     * Initialize payment transaction
     */
    public function initializePayment($amount, $email, $reference = null)
    {
        try {
            if (!$this->isEnabled()) {
                return ['success' => false, 'message' => 'Paystack is not configured or enabled'];
            }

            $reference = $reference ?? 'PAY_' . time() . '_' . uniqid();

            // Amount should be in kobo (multiply by 100)
            $amountInKobo = $amount * 100;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'amount' => $amountInKobo,
                'email' => $email,
                'reference' => $reference,
                'callback_url' => route('wallet.paystack.callback'),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status']) {
                    return [
                        'success' => true,
                        'data' => [
                            'authorization_url' => $data['data']['authorization_url'],
                            'access_code' => $data['data']['access_code'],
                            'reference' => $data['data']['reference']
                        ]
                    ];
                }
            }

            Log::error('Paystack initialization failed', [
                'response' => $response->body(),
                'amount' => $amount,
                'email' => $email
            ]);

            return ['success' => false, 'message' => 'Failed to initialize payment'];
        } catch (\Exception $e) {
            Log::error('Paystack initialization error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Payment initialization error'];
        }
    }

    /**
     * Verify payment transaction
     */
    public function verifyPayment($reference)
    {
        try {
            if (!$this->isEnabled()) {
                return ['success' => false, 'message' => 'Paystack is not configured'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] && $data['data']['status'] === 'success') {
                    return [
                        'success' => true,
                        'data' => [
                            'amount' => $data['data']['amount'] / 100, // Convert from kobo
                            'reference' => $data['data']['reference'],
                            'status' => $data['data']['status'],
                            'customer' => [
                                'email' => $data['data']['customer']['email']
                            ]
                        ]
                    ];
                }
            }

            return ['success' => false, 'message' => 'Payment verification failed'];
        } catch (\Exception $e) {
            Log::error('Paystack verification error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Payment verification error'];
        }
    }

    /**
     * Calculate charges for amount
     */
    public function calculateCharges($amount)
    {
        if ($amount <= 2500) {
            return ($amount * $this->charges) / 100;
        } else {
            // Different calculation for amounts above 2500
            return ($amount * $this->charges) / 100;
        }
    }

    /**
     * Get total amount to pay including charges
     */
    public function getTotalAmount($amount)
    {
        return $amount + $this->calculateCharges($amount);
    }

    /**
     * Process webhook notification
     */
    public function processWebhook($payload, $signature)
    {
        try {
            // Verify webhook signature
            $computedSignature = hash_hmac('sha512', $payload, $this->secretKey);

            if (!hash_equals($computedSignature, $signature)) {
                return ['success' => false, 'message' => 'Invalid signature'];
            }

            $data = json_decode($payload, true);

            if ($data['event'] === 'charge.success') {
                return $this->processSuccessfulPayment($data['data']);
            }

            return ['success' => false, 'message' => 'Unsupported event type'];
        } catch (\Exception $e) {
            Log::error('Paystack webhook processing error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Webhook processing failed'];
        }
    }

    /**
     * Process successful payment
     */
    private function processSuccessfulPayment($data)
    {
        try {
            $email = $data['customer']['email'];
            $amount = $data['amount'] / 100; // Convert from kobo
            $reference = $data['reference'];

            // Find user by email
            $user = User::where('email', $email)->first();
            if (!$user) {
                Log::warning('User not found for Paystack payment', ['email' => $email]);
                return ['success' => false, 'message' => 'User not found'];
            }

            // Check if transaction already processed
            $existingTransaction = DB::table('transactions')
                ->where('transref', $reference)
                ->first();

            if ($existingTransaction) {
                return ['success' => true, 'message' => 'Transaction already processed'];
            }

            // Calculate charges and net amount
            $charges = $this->calculateCharges($amount);
            $netAmount = $amount - $charges;

            // Add funds to user wallet
            DB::beginTransaction();

            // Get current balance
            $currentBalance = $user->wallet_balance ?? 0;
            $newBalance = $currentBalance + $netAmount;

            // Update user balance
            $user->update(['wallet_balance' => $newBalance]);

            // Record transaction
            DB::table('transactions')->insert([
                'sId' => $user->id,
                'transref' => $reference,
                'servicename' => 'Wallet Topup',
                'servicedesc' => "Wallet funding of ₦" . number_format($amount, 2) . " via Paystack. Charges: ₦" . number_format($charges, 2),
                'amount' => (string)$netAmount,
                'status' => 0, // 0 = success
                'oldbal' => (string)$currentBalance,
                'newbal' => (string)$newBalance,
                'profit' => 0,
                'date' => now(),
                // Fill new columns to avoid NOT NULL constraint errors
                'service_name' => 'Wallet Topup',
                'service_description' => "Wallet funding of ₦" . number_format($amount, 2) . " via Paystack. Charges: ₦" . number_format($charges, 2),
                'old_balance' => $currentBalance,
                'new_balance' => $newBalance
            ]);

            DB::commit();

            Log::info('Paystack payment processed successfully', [
                'user_id' => $user->id,
                'amount' => $amount,
                'net_amount' => $netAmount,
                'reference' => $reference
            ]);

            return ['success' => true, 'message' => 'Payment processed successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Paystack payment processing error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Payment processing failed'];
        }
    }
}
