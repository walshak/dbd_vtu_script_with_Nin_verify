<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('subscribers', 'sEmail')->ignore($user->sId, 'sId'),
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{11}$/',
                Rule::unique('subscribers', 'sPhone')->ignore($user->sId, 'sId'),
            ],
            'state' => 'required|string|max:100',
        ]);

        try {
            $user->update([
                'sFname' => $request->fname,
                'sLname' => $request->lname,
                'sEmail' => $request->email,
                'sPhone' => $request->phone,
                'sState' => $request->state,
            ]);

            Log::info('User profile updated', [
                'user_id' => $user->sId,
                'phone' => $user->sPhone,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Profile updated successfully!',
                    'user' => [
                        'id' => $user->sId,
                        'name' => $user->sFname . ' ' . $user->sLname,
                        'phone' => $user->sPhone,
                        'email' => $user->sEmail,
                        'state' => $user->sState
                    ]
                ]);
            }

            return back()->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            Log::error('Profile update error', [
                'error' => $e->getMessage(),
                'user_id' => $user->sId,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update profile. Please try again.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|max:15|confirmed',
        ]);

        try {
            // Check current password (supporting both plain text and hashed for compatibility)
            $currentPasswordMatch = ($user->sPass === $request->current_password) ||
                                  Hash::check($request->current_password, $user->sPass);

            if (!$currentPasswordMatch) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Current password is incorrect.'
                    ], 400);
                }

                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            // Update password (store as plain text for existing system compatibility)
            $user->update(['sPass' => $request->password]);

            Log::info('User password changed', [
                'user_id' => $user->sId,
                'phone' => $user->sPhone,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password changed successfully!'
                ]);
            }

            return back()->with('success', 'Password changed successfully!');

        } catch (\Exception $e) {
            Log::error('Password change error', [
                'error' => $e->getMessage(),
                'user_id' => $user->sId,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to change password. Please try again.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to change password. Please try again.']);
        }
    }

    /**
     * Change transaction PIN
     */
    public function changePin(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_pin' => 'required|string|size:4',
            'pin' => 'required|string|size:4|confirmed|regex:/^[0-9]{4}$/',
        ]);

        try {
            if ($user->sPin !== $request->current_pin) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Current PIN is incorrect.'
                    ], 400);
                }

                return back()->withErrors(['current_pin' => 'Current PIN is incorrect.']);
            }

            $user->update(['sPin' => $request->pin]);

            Log::info('User PIN changed', [
                'user_id' => $user->sId,
                'phone' => $user->sPhone,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction PIN changed successfully!'
                ]);
            }

            return back()->with('success', 'Transaction PIN changed successfully!');

        } catch (\Exception $e) {
            Log::error('PIN change error', [
                'error' => $e->getMessage(),
                'user_id' => $user->sId,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to change PIN. Please try again.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to change PIN. Please try again.']);
        }
    }

    /**
     * Get user dashboard data
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get transaction summary for the user
        $transactionSummary = \App\Models\Transaction::getUserTransactionSummary($user->id);
        
        // Get recent transactions
        $recentTransactions = \App\Models\Transaction::where('sId', $user->id)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        
        // Get monthly statistics
        $thisMonth = now()->startOfMonth();
        $monthlyTransactions = \App\Models\Transaction::where('sId', $user->id)
            ->where('date', '>=', $thisMonth)
            ->successful()
            ->get();
        
        $monthlyStats = [
            'count' => $monthlyTransactions->count(),
            'amount' => $monthlyTransactions->sum(function($t) {
                return floatval($t->amount);
            })
        ];
        
        // User statistics using correct field names
        $stats = [
            'wallet_balance' => $user->wallet_balance ?? 0,
            'ref_wallet_balance' => $user->referral_wallet ?? 0,
            'total_transactions' => $transactionSummary['total_transactions'],
            'successful_transactions' => $transactionSummary['successful_transactions'],
            'total_spent' => $transactionSummary['total_spent'],
            'monthly_count' => $monthlyStats['count'],
            'monthly_amount' => $monthlyStats['amount'],
            'account_type' => $user->account_type_name,
            'status' => $user->registration_status_name
        ];

        return view('dashboard', compact('user', 'stats', 'recentTransactions', 'transactionSummary', 'monthlyStats'));
    }

    /**
     * Get user API key
     */
    public function getApiKey()
    {
        $user = Auth::user();

        if (!$user->sApiKey) {
            $user->update(['sApiKey' => User::generateApiKey($user->sId)]);
            $user->refresh();
        }

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'api_key' => $user->sApiKey
            ]);
        }

        return back()->with('api_key', $user->sApiKey);
    }

    /**
     * Regenerate API key
     */
    public function regenerateApiKey()
    {
        $user = Auth::user();
        $newApiKey = User::generateApiKey($user->sId);

        $user->update(['sApiKey' => $newApiKey]);

        Log::info('User API key regenerated', [
            'user_id' => $user->sId,
            'phone' => $user->sPhone,
            'ip' => request()->ip()
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'API key regenerated successfully!',
                'api_key' => $newApiKey
            ]);
        }

        return back()->with('success', 'API key regenerated successfully!')
                    ->with('api_key', $newApiKey);
    }

    /**
     * Show referral information
     */
    public function referrals()
    {
        $user = Auth::user();

        // Get referrals (users referred by this user)
        $referrals = User::where('sReferal', $user->sPhone)->get();

        // Calculate referral earnings
        $referralEarnings = $user->sRefWallet;

        // Get referral code (last 6 digits of phone number)
        $referralCode = $user->referral_code;

        return view('referrals', compact('user', 'referrals', 'referralEarnings', 'referralCode'));
    }
}
