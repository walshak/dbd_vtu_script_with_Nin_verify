<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralBonus;
use App\Models\Transaction;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Generate referral code for user
     */
    public function generateReferralCode($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        // Use last 6 digits of phone as referral code (matching PHP app)
        return substr($user->sPhone, -6);
    }

    /**
     * Process referral when user makes a transaction
     */
    public function processReferralBonus($userId, $serviceType, $amount, $transactionRef)
    {
        $user = User::find($userId);
        if (!$user || !$user->sReferal) {
            return false;
        }

        // Find referrer by phone number
        $referrer = User::where('sPhone', $user->sReferal)->first();
        if (!$referrer) {
            return false;
        }

        // Get bonus rate from settings
        $bonusRate = $this->getBonusRate($serviceType);
        if ($bonusRate <= 0) {
            return false;
        }

        // Calculate bonus amount
        $bonusAmount = ($amount * $bonusRate) / 100;
        
        if ($bonusAmount <= 0) {
            return false;
        }

        try {
            // Credit referrer's bonus wallet
            $referrer->sRefWallet += $bonusAmount;
            $referrer->save();

            // Record referral bonus
            ReferralBonus::create([
                'referrer_id' => $referrer->sId,
                'referred_id' => $user->sId,
                'service_type' => $serviceType,
                'bonus_amount' => $bonusAmount,
                'transaction_ref' => $transactionRef,
                'paid' => true,
            ]);

            // Record transaction for referrer
            Transaction::create([
                'sId' => $referrer->sId,
                'transref' => 'REF' . time() . $referrer->sId,
                'servicename' => 'Referral Bonus',
                'servicedesc' => "Referral bonus from {$serviceType} transaction by {$user->sFname}",
                'amount' => $bonusAmount,
                'oldbal' => $referrer->sRefWallet - $bonusAmount,
                'newbal' => $referrer->sRefWallet,
                'status' => '0',
                'date' => now(),
            ]);

            Log::info("Referral bonus processed: {$bonusAmount} for user {$referrer->sId}");
            return true;

        } catch (\Exception $e) {
            Log::error('Referral bonus processing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get bonus rate for service type
     */
    private function getBonusRate($serviceType)
    {
        $settings = SiteSettings::first();
        if (!$settings) {
            return 0;
        }

        // Default bonus rates (can be configured in admin)
        return match(strtolower($serviceType)) {
            'data' => $settings->data_referral_bonus ?? 1.0,
            'airtime' => $settings->airtime_referral_bonus ?? 0.5,
            'cable tv' => $settings->cable_referral_bonus ?? 1.5,
            'electricity' => $settings->electricity_referral_bonus ?? 1.0,
            'exam pins' => $settings->exam_referral_bonus ?? 2.0,
            default => $settings->default_referral_bonus ?? 0.5,
        };
    }

    /**
     * Get user's referral statistics
     */
    public function getUserReferralStats($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        $referralCode = $this->generateReferralCode($userId);
        
        // Get direct referrals
        $directReferrals = User::where('sReferal', $user->sPhone)->get();
        
        // Get total earnings
        $totalEarnings = ReferralBonus::where('referrer_id', $userId)->sum('bonus_amount');
        
        // Get earnings by service type
        $earningsByService = ReferralBonus::where('referrer_id', $userId)
            ->selectRaw('service_type, SUM(bonus_amount) as total')
            ->groupBy('service_type')
            ->get();
        
        // Get recent bonuses
        $recentBonuses = ReferralBonus::where('referrer_id', $userId)
            ->with(['referred'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return [
            'referral_code' => $referralCode,
            'total_referrals' => $directReferrals->count(),
            'active_referrals' => $directReferrals->where('sRegStatus', 0)->count(),
            'total_earnings' => $totalEarnings,
            'current_bonus_balance' => $user->sRefWallet,
            'earnings_by_service' => $earningsByService,
            'recent_bonuses' => $recentBonuses,
            'referrals' => $directReferrals,
        ];
    }

    /**
     * Transfer referral earnings to main wallet
     */
    public function transferEarningsToWallet($userId, $amount, $transactionPin = null)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['status' => 'error', 'message' => 'User not found'];
        }

        // Validate PIN if required
        if ($user->sPinStatus == 1) {
            if (!$transactionPin || $user->sPin != $transactionPin) {
                return ['status' => 'error', 'message' => 'Invalid transaction PIN'];
            }
        }

        // Check referral wallet balance
        if ($user->sRefWallet < $amount) {
            return ['status' => 'error', 'message' => 'Insufficient referral balance'];
        }

        try {
            // Transfer funds
            $user->sRefWallet -= $amount;
            $user->sWallet += $amount;
            $user->save();

            // Record transaction
            Transaction::create([
                'sId' => $user->sId,
                'transref' => 'REFTRANS' . time() . $user->sId,
                'servicename' => 'Referral Transfer',
                'servicedesc' => 'Transfer from referral wallet to main wallet',
                'amount' => $amount,
                'oldbal' => $user->sWallet - $amount,
                'newbal' => $user->sWallet,
                'status' => '0',
                'date' => now(),
            ]);

            return [
                'status' => 'success',
                'message' => 'Earnings transferred successfully',
                'new_wallet_balance' => $user->sWallet,
                'new_referral_balance' => $user->sRefWallet,
            ];

        } catch (\Exception $e) {
            Log::error('Referral transfer failed: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Transfer failed'];
        }
    }

    /**
     * Get referral leaderboard
     */
    public function getReferralLeaderboard($limit = 10)
    {
        return User::selectRaw('sId, sFname, sLname, sRefWallet, 
                              (SELECT COUNT(*) FROM users u2 WHERE u2.sReferal = users.sPhone) as referral_count')
            ->havingRaw('referral_count > 0')
            ->orderByRaw('referral_count DESC, sRefWallet DESC')
            ->take($limit)
            ->get();
    }

    /**
     * Calculate potential earnings for user
     */
    public function calculatePotentialEarnings($serviceType, $amount)
    {
        $bonusRate = $this->getBonusRate($serviceType);
        return ($amount * $bonusRate) / 100;
    }

    /**
     * Get referral link for user
     */
    public function getReferralLink($userId)
    {
        $referralCode = $this->generateReferralCode($userId);
        $baseUrl = config('app.url');
        
        return "{$baseUrl}/register?ref={$referralCode}";
    }

    /**
     * Validate referral code
     */
    public function validateReferralCode($referralCode)
    {
        if (strlen($referralCode) !== 6) {
            return false;
        }

        $referrer = User::where('sPhone', 'LIKE', '%' . $referralCode)
            ->where('sRegStatus', 0) // Only active users
            ->first();

        return $referrer ? $referrer->sPhone : false;
    }

    /**
     * Apply referral code to new user
     */
    public function applyReferralCode($newUserId, $referralCode)
    {
        $validReferralPhone = $this->validateReferralCode($referralCode);
        
        if (!$validReferralPhone) {
            return false;
        }

        $newUser = User::find($newUserId);
        if (!$newUser) {
            return false;
        }

        // Set referral
        $newUser->sReferal = $validReferralPhone;
        $newUser->save();

        return true;
    }
}