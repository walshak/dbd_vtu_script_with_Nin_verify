<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Transaction;
use App\Models\WalletTransaction;
use App\Models\ReferralBonus;
use App\Models\SystemConfig;
use Carbon\Carbon;

class EnhancedWalletController extends Controller
{
    /**
     * Get comprehensive wallet information
     */
    public function getWalletInfo(Request $request)
    {
        try {
            $user = Auth::user();

            $walletInfo = [
                'balance' => number_format($user->wallet_balance ?? 0, 2),
                'bonus_balance' => number_format($user->bonus_balance ?? 0, 2),
                'total_available' => number_format(($user->wallet_balance ?? 0) + ($user->bonus_balance ?? 0), 2),
                'limits' => $this->getWalletLimits($user),
                'auto_funding' => $this->getAutoFundingInfo($user),
                'transaction_stats' => $this->getTransactionStats($user),
                'recent_activity' => $this->getRecentWalletActivity($user, 10),
                'referral_info' => $this->getReferralInfo($user)
            ];

            return response()->json([
                'status' => 'success',
                'data' => $walletInfo
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting wallet info: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get wallet information'
            ], 500);
        }
    }

    /**
     * Credit user wallet with bonus tracking
     */
    public function creditWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100|max:500000',
            'payment_method' => 'required|string|in:bank_transfer,card,ussd,online_payment',
            'payment_reference' => 'required|string|max:100',
            'apply_bonus' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $amount = $request->amount;
            $paymentMethod = $request->payment_method;
            $paymentReference = $request->payment_reference;

            // Check daily funding limit
            if (!$this->checkDailyFundingLimit($user, $amount)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Daily funding limit exceeded'
                ], 400);
            }

            // Check for duplicate payment reference
            if (WalletTransaction::where('payment_reference', $paymentReference)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment reference already used'
                ], 400);
            }

            // Calculate bonus if applicable
            $bonusAmount = 0;
            if ($request->apply_bonus ?? true) {
                $bonusAmount = $this->calculateFundingBonus($user, $amount);
            }

            // Create wallet transaction record
            $walletTransaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'bonus_amount' => $bonusAmount,
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentReference,
                'status' => 'pending',
                'description' => "Wallet funding via {$paymentMethod}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Update user wallet balance
            $user->increment('wallet_balance', $amount);
            if ($bonusAmount > 0) {
                $user->increment('bonus_balance', $bonusAmount);
            }

            // Update transaction status to completed
            $walletTransaction->update(['status' => 'completed']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet credited successfully',
                'data' => [
                    'transaction_id' => $walletTransaction->id,
                    'amount_credited' => number_format($amount, 2),
                    'bonus_awarded' => number_format($bonusAmount, 2),
                    'new_balance' => number_format($user->fresh()->wallet_balance, 2),
                    'new_bonus_balance' => number_format($user->fresh()->bonus_balance, 2)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error crediting wallet: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to credit wallet'
            ], 500);
        }
    }

    /**
     * Debit user wallet for transactions
     */
    public function debitWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'service_type' => 'required|string',
            'description' => 'required|string|max:255',
            'transaction_reference' => 'required|string|max:100',
            'use_bonus_first' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $amount = $request->amount;
            $useBonusFirst = $request->use_bonus_first ?? true;

            // Check if user has sufficient balance
            $totalAvailable = ($user->wallet_balance ?? 0) + ($user->bonus_balance ?? 0);
            if ($totalAvailable < $amount) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient wallet balance',
                    'data' => [
                        'required_amount' => number_format($amount, 2),
                        'available_balance' => number_format($totalAvailable, 2),
                        'shortage' => number_format($amount - $totalAvailable, 2)
                    ]
                ], 400);
            }

            // Check transaction limits
            if (!$this->checkTransactionLimits($user, $amount, $request->service_type)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction limit exceeded'
                ], 400);
            }

            // Calculate debit distribution (bonus vs main wallet)
            $debitInfo = $this->calculateDebitDistribution($user, $amount, $useBonusFirst);

            // Create wallet transaction record
            $walletTransaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'main_wallet_amount' => $debitInfo['main_wallet_debit'],
                'bonus_amount' => $debitInfo['bonus_debit'],
                'service_type' => $request->service_type,
                'transaction_reference' => $request->transaction_reference,
                'status' => 'completed',
                'description' => $request->description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Update user balances
            $user->decrement('wallet_balance', $debitInfo['main_wallet_debit']);
            $user->decrement('bonus_balance', $debitInfo['bonus_debit']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet debited successfully',
                'data' => [
                    'transaction_id' => $walletTransaction->id,
                    'amount_debited' => number_format($amount, 2),
                    'main_wallet_debited' => number_format($debitInfo['main_wallet_debit'], 2),
                    'bonus_debited' => number_format($debitInfo['bonus_debit'], 2),
                    'new_balance' => number_format($user->fresh()->wallet_balance, 2),
                    'new_bonus_balance' => number_format($user->fresh()->bonus_balance, 2)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error debiting wallet: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to debit wallet'
            ], 500);
        }
    }

    /**
     * Transfer money between users
     */
    public function transferFunds(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_username' => 'required|string|exists:users,sUsername',
            'amount' => 'required|numeric|min:100|max:50000',
            'description' => 'sometimes|string|max:255',
            'transaction_pin' => 'required|string|size:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $sender = Auth::user();
            $recipient = User::where('sUsername', $request->recipient_username)->first();
            $amount = $request->amount;

            // Verify transaction PIN
            if ($sender->sPin !== $request->transaction_pin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid transaction PIN'
                ], 400);
            }

            // Check if transferring to self
            if ($sender->id === $recipient->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot transfer to yourself'
                ], 400);
            }

            // Check sender balance
            if ($sender->wallet_balance < $amount) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient wallet balance'
                ], 400);
            }

            // Check daily transfer limit
            if (!$this->checkDailyTransferLimit($sender, $amount)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Daily transfer limit exceeded'
                ], 400);
            }

            $transferFee = $this->calculateTransferFee($amount);
            $totalDeduction = $amount + $transferFee;

            if ($sender->wallet_balance < $totalDeduction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient balance including transfer fee',
                    'data' => [
                        'transfer_amount' => number_format($amount, 2),
                        'transfer_fee' => number_format($transferFee, 2),
                        'total_required' => number_format($totalDeduction, 2),
                        'available_balance' => number_format($sender->wallet_balance, 2)
                    ]
                ], 400);
            }

            $reference = $this->generateTransferReference();

            // Debit sender
            $senderTransaction = WalletTransaction::create([
                'user_id' => $sender->id,
                'type' => 'transfer_out',
                'amount' => $totalDeduction,
                'main_wallet_amount' => $totalDeduction,
                'transfer_fee' => $transferFee,
                'recipient_id' => $recipient->id,
                'transaction_reference' => $reference,
                'status' => 'completed',
                'description' => $request->description ?? "Transfer to {$recipient->sUsername}",
                'ip_address' => $request->ip()
            ]);

            // Credit recipient
            $recipientTransaction = WalletTransaction::create([
                'user_id' => $recipient->id,
                'type' => 'transfer_in',
                'amount' => $amount,
                'main_wallet_amount' => $amount,
                'sender_id' => $sender->id,
                'transaction_reference' => $reference,
                'status' => 'completed',
                'description' => $request->description ?? "Transfer from {$sender->sUsername}",
                'ip_address' => $request->ip()
            ]);

            // Update balances
            $sender->decrement('wallet_balance', $totalDeduction);
            $recipient->increment('wallet_balance', $amount);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transfer completed successfully',
                'data' => [
                    'reference' => $reference,
                    'recipient' => $recipient->sUsername,
                    'amount_sent' => number_format($amount, 2),
                    'transfer_fee' => number_format($transferFee, 2),
                    'total_debited' => number_format($totalDeduction, 2),
                    'new_balance' => number_format($sender->fresh()->wallet_balance, 2)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error transferring funds: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to transfer funds'
            ], 500);
        }
    }

    /**
     * Set up auto-funding
     */
    public function setupAutoFunding(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enabled' => 'required|boolean',
            'threshold_amount' => 'required_if:enabled,true|numeric|min:100|max:10000',
            'funding_amount' => 'required_if:enabled,true|numeric|min:500|max:50000',
            'funding_source' => 'required_if:enabled,true|string|in:bank_account,saved_card'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = Auth::user();

            $user->update([
                'auto_funding_enabled' => $request->enabled,
                'auto_funding_threshold' => $request->threshold_amount ?? null,
                'auto_funding_amount' => $request->funding_amount ?? null,
                'auto_funding_source' => $request->funding_source ?? null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $request->enabled ? 'Auto-funding enabled' : 'Auto-funding disabled',
                'data' => [
                    'enabled' => $request->enabled,
                    'threshold' => number_format($request->threshold_amount ?? 0, 2),
                    'funding_amount' => number_format($request->funding_amount ?? 0, 2),
                    'funding_source' => $request->funding_source ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error setting up auto-funding: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to setup auto-funding'
            ], 500);
        }
    }

    /**
     * Get wallet transaction history
     */
    public function getTransactionHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->per_page ?? 20;
            $type = $request->type; // credit, debit, transfer_in, transfer_out

            $query = WalletTransaction::where('user_id', $user->id);

            if ($type) {
                $query->where('type', $type);
            }

            $transactions = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'transactions' => $transactions->items(),
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'total_pages' => $transactions->lastPage(),
                        'total_items' => $transactions->total(),
                        'per_page' => $transactions->perPage()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting transaction history: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get transaction history'
            ], 500);
        }
    }

    /**
     * Apply referral bonus
     */
    public function applyReferralBonus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referred_user_id' => 'required|integer|exists:users,id',
            'transaction_amount' => 'required|numeric|min:100',
            'service_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $referrer = Auth::user();
            $referredUser = User::find($request->referred_user_id);

            // Check if referral bonus is applicable
            $bonusInfo = $this->calculateReferralBonus($referrer, $referredUser, $request->transaction_amount, $request->service_type);

            if ($bonusInfo['bonus_amount'] > 0) {
                // Apply bonus to referrer
                $referrer->increment('bonus_balance', $bonusInfo['bonus_amount']);

                // Record referral bonus
                ReferralBonus::create([
                    'referrer_id' => $referrer->id,
                    'referred_user_id' => $referredUser->id,
                    'bonus_amount' => $bonusInfo['bonus_amount'],
                    'transaction_amount' => $request->transaction_amount,
                    'service_type' => $request->service_type,
                    'bonus_type' => $bonusInfo['bonus_type']
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'bonus_applied' => $bonusInfo['bonus_amount'] > 0,
                    'bonus_amount' => number_format($bonusInfo['bonus_amount'], 2),
                    'bonus_type' => $bonusInfo['bonus_type'],
                    'new_bonus_balance' => number_format($referrer->fresh()->bonus_balance, 2)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error applying referral bonus: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to apply referral bonus'
            ], 500);
        }
    }

    /**
     * Private helper methods
     */

    private function getWalletLimits($user)
    {
        $limits = [
            'daily_funding_limit' => $this->getDailyFundingLimit($user),
            'daily_spending_limit' => $this->getDailySpendingLimit($user),
            'daily_transfer_limit' => $this->getDailyTransferLimit($user),
            'single_transaction_limit' => $this->getSingleTransactionLimit($user)
        ];

        return $limits;
    }

    private function getAutoFundingInfo($user)
    {
        return [
            'enabled' => $user->auto_funding_enabled ?? false,
            'threshold' => number_format($user->auto_funding_threshold ?? 0, 2),
            'funding_amount' => number_format($user->auto_funding_amount ?? 0, 2),
            'funding_source' => $user->auto_funding_source
        ];
    }

    private function getTransactionStats($user)
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today_spent' => number_format($this->getTodaySpent($user), 2),
            'month_spent' => number_format($this->getMonthSpent($user), 2),
            'total_funded' => number_format($this->getTotalFunded($user), 2),
            'total_earned_bonus' => number_format($user->bonus_balance ?? 0, 2)
        ];
    }

    private function getRecentWalletActivity($user, $limit = 10)
    {
        return WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => number_format($transaction->amount, 2),
                    'description' => $transaction->description,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s')
                ];
            });
    }

    private function getReferralInfo($user)
    {
        $totalReferrals = User::where('sRefBy', $user->sUsername)->count();
        $totalBonus = ReferralBonus::where('referrer_id', $user->id)->sum('bonus_amount');

        return [
            'total_referrals' => $totalReferrals,
            'total_bonus_earned' => number_format($totalBonus, 2),
            'active_referrals' => User::where('sRefBy', $user->sUsername)->where('sStatus', 'active')->count()
        ];
    }

    private function checkDailyFundingLimit($user, $amount)
    {
        $dailyLimit = $this->getDailyFundingLimit($user);
        $todayFunded = $this->getTodayFunded($user);

        return ($todayFunded + $amount) <= $dailyLimit;
    }

    private function checkTransactionLimits($user, $amount, $serviceType)
    {
        $dailyLimit = $this->getDailySpendingLimit($user);
        $todaySpent = $this->getTodaySpent($user);
        $singleLimit = $this->getSingleTransactionLimit($user);

        return ($todaySpent + $amount) <= $dailyLimit && $amount <= $singleLimit;
    }

    private function checkDailyTransferLimit($user, $amount)
    {
        $dailyLimit = $this->getDailyTransferLimit($user);
        $todayTransferred = $this->getTodayTransferred($user);

        return ($todayTransferred + $amount) <= $dailyLimit;
    }

    private function calculateFundingBonus($user, $amount)
    {
        // Example bonus calculation: 1% for amounts over 5000
        if ($amount >= 5000) {
            return $amount * 0.01; // 1% bonus
        }
        return 0;
    }

    private function calculateDebitDistribution($user, $amount, $useBonusFirst)
    {
        $bonusBalance = $user->bonus_balance ?? 0;
        $mainBalance = $user->wallet_balance ?? 0;

        if ($useBonusFirst && $bonusBalance > 0) {
            $bonusDebit = min($amount, $bonusBalance);
            $mainDebit = $amount - $bonusDebit;
        } else {
            $mainDebit = min($amount, $mainBalance);
            $bonusDebit = $amount - $mainDebit;
        }

        return [
            'bonus_debit' => $bonusDebit,
            'main_wallet_debit' => $mainDebit
        ];
    }

    private function calculateTransferFee($amount)
    {
        // Example fee structure: 1% with minimum of 10 and maximum of 100
        $fee = $amount * 0.01;
        return max(10, min(100, $fee));
    }

    private function generateTransferReference()
    {
        return 'TRF' . time() . rand(1000, 9999);
    }

    private function calculateReferralBonus($referrer, $referredUser, $amount, $serviceType)
    {
        // Example: 2% of referred user's transaction as bonus to referrer
        $bonusPercentage = 0.02;
        $bonusAmount = $amount * $bonusPercentage;

        return [
            'bonus_amount' => $bonusAmount,
            'bonus_type' => 'transaction_referral'
        ];
    }

    // Limit getter methods
    private function getDailyFundingLimit($user)
    {
        $limits = [
            'user' => 100000,
            'agent' => 500000,
            'super_agent' => 1000000,
            'api_user' => 2000000
        ];
        return $limits[$user->sType] ?? 100000;
    }

    private function getDailySpendingLimit($user)
    {
        $limits = [
            'user' => 50000,
            'agent' => 200000,
            'super_agent' => 500000,
            'api_user' => 1000000
        ];
        return $limits[$user->sType] ?? 50000;
    }

    private function getDailyTransferLimit($user)
    {
        $limits = [
            'user' => 20000,
            'agent' => 100000,
            'super_agent' => 200000,
            'api_user' => 500000
        ];
        return $limits[$user->sType] ?? 20000;
    }

    private function getSingleTransactionLimit($user)
    {
        $limits = [
            'user' => 10000,
            'agent' => 50000,
            'super_agent' => 100000,
            'api_user' => 200000
        ];
        return $limits[$user->sType] ?? 10000;
    }

    // Stats getter methods
    private function getTodayFunded($user)
    {
        return WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
    }

    private function getTodaySpent($user)
    {
        return WalletTransaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
    }

    private function getMonthSpent($user)
    {
        return WalletTransaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('amount');
    }

    private function getTotalFunded($user)
    {
        return WalletTransaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('amount');
    }

    private function getTodayTransferred($user)
    {
        return WalletTransaction::where('user_id', $user->id)
            ->where('type', 'transfer_out')
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
    }
}
