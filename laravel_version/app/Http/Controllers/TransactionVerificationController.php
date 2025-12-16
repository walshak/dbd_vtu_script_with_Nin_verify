<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class TransactionVerificationController extends Controller
{
    /**
     * Verify transaction PIN before processing
     */
    public function verifyTransactionPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_pin' => 'required|string|size:4',
            'user_id' => 'sometimes|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid transaction PIN format',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $userId = $request->user_id ?? auth()->id();
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            // Check if user has set transaction PIN
            if (empty($user->sPin)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction PIN not set. Please set your PIN first.',
                    'requires_setup' => true
                ], 400);
            }

            // Verify PIN
            $pinValid = $user->sPin === $request->transaction_pin;

            if (!$pinValid) {
                // Log failed attempt
                $this->logFailedPinAttempt($userId);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid transaction PIN'
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction PIN verified successfully',
                'data' => [
                    'verified' => true,
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying transaction PIN: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify transaction PIN'
            ], 500);
        }
    }

    /**
     * Check for duplicate transactions
     */
    public function checkDuplicateTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'amount' => 'required|numeric',
            'service_type' => 'required|string|in:airtime,data,cable_tv,electricity,exam_pin,recharge_pin',
            'product_code' => 'sometimes|string',
            'user_id' => 'sometimes|integer|exists:users,id',
            'check_window_minutes' => 'sometimes|integer|min:1|max:60' // Default 5 minutes
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $userId = $request->user_id ?? auth()->id();
            $checkWindow = $request->check_window_minutes ?? 5; // 5 minutes default

            $cutoffTime = Carbon::now()->subMinutes($checkWindow);

            // Check for duplicate transaction
            $duplicateTransaction = Transaction::where('user_id', $userId)
                ->where('phone', $request->phone)
                ->where('amount', $request->amount)
                ->where('service_type', $request->service_type)
                ->where('created_at', '>', $cutoffTime)
                ->when($request->has('product_code'), function ($query) use ($request) {
                    return $query->where('product_code', $request->product_code);
                })
                ->first();

            if ($duplicateTransaction) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => 'Duplicate transaction detected',
                    'data' => [
                        'is_duplicate' => true,
                        'original_transaction' => [
                            'id' => $duplicateTransaction->id,
                            'reference' => $duplicateTransaction->reference,
                            'status' => $duplicateTransaction->status,
                            'created_at' => $duplicateTransaction->created_at->toISOString(),
                            'minutes_ago' => $duplicateTransaction->created_at->diffInMinutes(now())
                        ],
                        'check_window_minutes' => $checkWindow
                    ]
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'No duplicate transaction found',
                'data' => [
                    'is_duplicate' => false,
                    'can_proceed' => true
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking duplicate transaction: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check for duplicate transaction'
            ], 500);
        }
    }

    /**
     * Verify transaction status with external provider
     */
    public function verifyTransactionStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|integer|exists:transactions,id',
            'external_reference' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $transaction = Transaction::find($request->transaction_id);

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Check if user owns this transaction
            if (auth()->id() !== $transaction->user_id && !auth()->user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to transaction'
                ], 403);
            }

            // Verify with external provider based on service type
            $verificationResult = $this->verifyWithProvider($transaction);

            // Update transaction status if needed
            if ($verificationResult['status_changed']) {
                $transaction->update([
                    'status' => $verificationResult['new_status'],
                    'provider_response' => json_encode($verificationResult['provider_response']),
                    'verified_at' => now()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'status' => $transaction->status,
                    'external_status' => $verificationResult['external_status'],
                    'status_match' => $verificationResult['status_match'],
                    'last_verified' => $transaction->verified_at,
                    'provider_response' => $verificationResult['provider_response']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying transaction status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify transaction status'
            ], 500);
        }
    }

    /**
     * Verify wallet balance before transaction
     */
    public function verifyWalletBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'user_id' => 'sometimes|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $userId = $request->user_id ?? auth()->id();
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $requestedAmount = $request->amount;
            $currentBalance = $user->wallet_balance ?? 0;
            $hasSufficientBalance = $currentBalance >= $requestedAmount;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'current_balance' => number_format($currentBalance, 2),
                    'requested_amount' => number_format($requestedAmount, 2),
                    'sufficient_balance' => $hasSufficientBalance,
                    'shortage' => $hasSufficientBalance ? 0 : $requestedAmount - $currentBalance,
                    'can_proceed' => $hasSufficientBalance
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying wallet balance: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify wallet balance'
            ], 500);
        }
    }

    /**
     * Generate unique transaction reference
     */
    public function generateTransactionReference(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string|in:airtime,data,cable_tv,electricity,exam_pin,recharge_pin',
            'prefix' => 'sometimes|string|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $servicePrefix = $this->getServicePrefix($request->service_type);
            $customPrefix = $request->prefix ?? '';

            $reference = $this->generateUniqueReference($servicePrefix, $customPrefix);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'reference' => $reference,
                    'service_type' => $request->service_type,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating transaction reference: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate transaction reference'
            ], 500);
        }
    }

    /**
     * Verify with external provider
     */
    private function verifyWithProvider($transaction)
    {
        // This would implement actual provider verification logic
        // For now, return mock verification result

        $mockStatuses = ['successful', 'pending', 'failed'];
        $externalStatus = $mockStatuses[array_rand($mockStatuses)];

        $statusMatch = $transaction->status === $externalStatus;
        $statusChanged = !$statusMatch && $externalStatus !== 'pending';

        return [
            'external_status' => $externalStatus,
            'status_match' => $statusMatch,
            'status_changed' => $statusChanged,
            'new_status' => $statusChanged ? $externalStatus : $transaction->status,
            'provider_response' => [
                'verified_at' => now()->toISOString(),
                'provider_reference' => 'EXT_' . rand(100000, 999999),
                'verification_method' => 'api_query'
            ]
        ];
    }

    /**
     * Log failed PIN attempt
     */
    private function logFailedPinAttempt($userId)
    {
        Log::warning('Failed transaction PIN attempt', [
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get service prefix for reference generation
     */
    private function getServicePrefix($serviceType)
    {
        $prefixes = [
            'airtime' => 'AIR',
            'data' => 'DAT',
            'cable_tv' => 'CAB',
            'electricity' => 'ELE',
            'exam_pin' => 'EXM',
            'recharge_pin' => 'RCH'
        ];

        return $prefixes[$serviceType] ?? 'TXN';
    }

    /**
     * Generate unique transaction reference
     */
    private function generateUniqueReference($servicePrefix, $customPrefix = '')
    {
        $timestamp = now()->format('ymdHis');
        $randomString = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4));

        $reference = $customPrefix . $servicePrefix . $timestamp . $randomString;

        // Ensure uniqueness
        $attempts = 0;
        while (Transaction::where('reference', $reference)->exists() && $attempts < 10) {
            $randomString = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4));
            $reference = $customPrefix . $servicePrefix . $timestamp . $randomString;
            $attempts++;
        }

        return $reference;
    }

    /**
     * Batch verify multiple transactions
     */
    public function batchVerifyTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_ids' => 'required|array|min:1|max:50',
            'transaction_ids.*' => 'required|integer|exists:transactions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $results = [];
            $transactions = Transaction::whereIn('id', $request->transaction_ids)->get();

            foreach ($transactions as $transaction) {
                if (auth()->id() === $transaction->user_id || auth()->user()->isAdmin()) {
                    $verificationResult = $this->verifyWithProvider($transaction);

                    if ($verificationResult['status_changed']) {
                        $transaction->update([
                            'status' => $verificationResult['new_status'],
                            'provider_response' => json_encode($verificationResult['provider_response']),
                            'verified_at' => now()
                        ]);
                    }

                    $results[] = [
                        'transaction_id' => $transaction->id,
                        'reference' => $transaction->reference,
                        'status' => $transaction->status,
                        'external_status' => $verificationResult['external_status'],
                        'status_match' => $verificationResult['status_match']
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_verified' => count($results),
                    'verifications' => $results,
                    'verified_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in batch transaction verification: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify transactions'
            ], 500);
        }
    }
}
