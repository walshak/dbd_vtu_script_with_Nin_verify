<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->middleware('auth');
        $this->referralService = $referralService;
    }

    /**
     * Show referral dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $stats = $this->referralService->getUserReferralStats($user->sId);
        $referralLink = $this->referralService->getReferralLink($user->sId);
        $leaderboard = $this->referralService->getReferralLeaderboard();

        return view('user.referrals.index', compact('stats', 'referralLink', 'leaderboard'));
    }

    /**
     * Transfer earnings to main wallet
     */
    public function transferEarnings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $user->sRefWallet,
            'transaction_pin' => $user->sPinStatus ? 'required|digits:4' : 'nullable',
        ]);

        $result = $this->referralService->transferEarningsToWallet(
            $user->sId,
            $request->amount,
            $request->transaction_pin
        );

        if ($result['status'] === 'success') {
            return redirect()->route('referrals')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Get referral statistics (API)
     */
    public function getStats()
    {
        $user = Auth::user();
        $stats = $this->referralService->getUserReferralStats($user->sId);

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    /**
     * Calculate potential earnings
     */
    public function calculateEarnings(Request $request)
    {
        $request->validate([
            'service_type' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        $potential = $this->referralService->calculatePotentialEarnings(
            $request->service_type,
            $request->amount
        );

        return response()->json([
            'status' => 'success',
            'potential_earnings' => $potential,
            'service_type' => $request->service_type,
            'amount' => $request->amount,
        ]);
    }

    /**
     * Get referral link
     */
    public function getReferralLink()
    {
        $user = Auth::user();
        $link = $this->referralService->getReferralLink($user->sId);

        return response()->json([
            'status' => 'success',
            'referral_link' => $link,
            'referral_code' => $this->referralService->generateReferralCode($user->sId),
        ]);
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(Request $request)
    {
        $limit = $request->get('limit', 10);
        $leaderboard = $this->referralService->getReferralLeaderboard($limit);

        return response()->json([
            'status' => 'success',
            'leaderboard' => $leaderboard,
        ]);
    }
}