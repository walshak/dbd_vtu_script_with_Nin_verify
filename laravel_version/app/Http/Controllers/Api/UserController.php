<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\UserVerification;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $this->formatUserProfile($user),
                    'statistics' => $this->getUserStatistics($user),
                    'preferences' => $this->getUserPreferences($user)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get profile failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile'
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20|unique:users,phone,' . $request->user()->id,
                'email' => 'nullable|email|max:255|unique:users,email,' . $request->user()->id,
                'date_of_birth' => 'nullable|date|before:today',
                'address' => 'nullable|string|max:500',
                'state' => 'nullable|string|max:100',
                'gender' => 'nullable|in:male,female,other'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $updates = array_filter($request->only([
                'firstname', 'lastname', 'phone', 'email', 
                'date_of_birth', 'address', 'state', 'gender'
            ]));

            $user->update($updates);

            Log::info('Profile updated', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $this->formatUserProfile($user->fresh())
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed'
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Revoke all other tokens for security
            $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

            Log::info('Password changed', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Password change failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Password change failed'
            ], 500);
        }
    }

    /**
     * Change transaction PIN
     */
    public function changePin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_pin' => 'nullable|string|size:4',
                'new_pin' => 'required|string|size:4|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Check current PIN if user has one
            if ($user->transaction_pin && !Hash::check($request->current_pin, $user->transaction_pin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current PIN is incorrect'
                ], 400);
            }

            $user->update([
                'transaction_pin' => Hash::make($request->new_pin),
                'pin_enabled' => true
            ]);

            Log::info('Transaction PIN changed', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction PIN changed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('PIN change failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'PIN change failed'
            ], 500);
        }
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Delete old avatar if exists
            if ($user->avatar && Storage::exists('public/avatars/' . basename($user->avatar))) {
                Storage::delete('public/avatars/' . basename($user->avatar));
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('public/avatars');
            $avatarUrl = Storage::url($avatarPath);

            $user->update(['avatar' => $avatarUrl]);

            Log::info('Avatar uploaded', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'data' => [
                    'avatar_url' => $avatarUrl
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Avatar upload failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Avatar upload failed'
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStats(Request $request)
    {
        try {
            $user = $request->user();
            $stats = $this->getUserStatistics($user);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Get user stats failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics'
            ], 500);
        }
    }

    /**
     * Get user activity
     */
    public function getActivity(Request $request)
    {
        try {
            $user = $request->user();
            $limit = $request->get('limit', 20);

            $activities = Transaction::where('user_id', $user->id)
                ->with(['service'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'type' => $transaction->type,
                        'service' => $transaction->service_type,
                        'amount' => (float) $transaction->amount,
                        'status' => $transaction->status,
                        'description' => $transaction->description,
                        'created_at' => $transaction->created_at->toISOString()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'activities' => $activities,
                    'pagination' => [
                        'total' => $activities->count(),
                        'limit' => $limit
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get user activity failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve activity'
            ], 500);
        }
    }

    /**
     * Get API key
     */
    public function getApiKey(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'api_key' => $user->api_key,
                    'last_used' => $user->api_key_last_used?->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get API key failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve API key'
            ], 500);
        }
    }

    /**
     * Regenerate API key
     */
    public function regenerateApiKey(Request $request)
    {
        try {
            $user = $request->user();
            $newApiKey = 'sk_' . bin2hex(random_bytes(32));

            $user->update([
                'api_key' => $newApiKey,
                'api_key_last_used' => null
            ]);

            Log::info('API key regenerated', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'API key regenerated successfully',
                'data' => [
                    'api_key' => $newApiKey
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API key regeneration failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'API key regeneration failed'
            ], 500);
        }
    }

    /**
     * Get KYC status
     */
    public function getKycStatus(Request $request)
    {
        try {
            $user = $request->user();
            $verification = UserVerification::where('user_id', $user->id)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $user->kyc_status ?? 'not_submitted',
                    'verification_level' => $verification?->verification_level ?? 'level_0',
                    'submitted_at' => $verification?->created_at?->toISOString(),
                    'reviewed_at' => $verification?->reviewed_at?->toISOString(),
                    'required_documents' => [
                        'identity_document' => 'National ID, Driver\'s License, or Passport',
                        'proof_of_address' => 'Utility bill or bank statement (not older than 3 months)',
                        'selfie' => 'Clear selfie photo'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get KYC status failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve KYC status'
            ], 500);
        }
    }

    /**
     * Submit KYC documents
     */
    public function submitKyc(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'document_type' => 'required|in:identity,address,selfie',
                'document' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'document_number' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Store document
            $documentPath = $request->file('document')->store('public/kyc/' . $user->id);
            $documentUrl = Storage::url($documentPath);

            // Create or update verification record
            $verification = UserVerification::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'status' => 'pending',
                    'verification_level' => 'level_1',
                    'documents' => array_merge(
                        $verification->documents ?? [],
                        [
                            $request->document_type => [
                                'url' => $documentUrl,
                                'number' => $request->document_number,
                                'uploaded_at' => now()->toISOString()
                            ]
                        ]
                    )
                ]
            );

            $user->update(['kyc_status' => 'pending']);

            Log::info('KYC document submitted', [
                'user_id' => $user->id,
                'document_type' => $request->document_type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'KYC document submitted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('KYC submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'KYC submission failed'
            ], 500);
        }
    }

    /**
     * Get upgrade options
     */
    public function getUpgradeOptions(Request $request)
    {
        try {
            $user = $request->user();

            $options = [
                'agent' => [
                    'name' => 'Agent Account',
                    'fee' => (float) SiteSettings::getSetting('agent_upgrade_fee', 5000),
                    'benefits' => [
                        'Higher transaction limits',
                        'Better commission rates',
                        'Priority customer support',
                        'Advanced analytics'
                    ],
                    'available' => $user->account_type === 'user'
                ],
                'vendor' => [
                    'name' => 'Vendor Account',
                    'fee' => (float) SiteSettings::getSetting('vendor_upgrade_fee', 15000),
                    'benefits' => [
                        'Highest transaction limits',
                        'Best commission rates',
                        'API access',
                        'White-label solutions',
                        'Dedicated account manager'
                    ],
                    'available' => in_array($user->account_type, ['user', 'agent'])
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'current_type' => $user->account_type,
                    'upgrade_options' => $options
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get upgrade options failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve upgrade options'
            ], 500);
        }
    }

    /**
     * Get referral statistics
     */
    public function getReferralStats(Request $request)
    {
        try {
            $user = $request->user();

            $referrals = User::where('referred_by', $user->id)->get();
            $totalEarnings = $referrals->sum('referral_earnings');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_referrals' => $referrals->count(),
                    'active_referrals' => $referrals->where('status', 'active')->count(),
                    'total_earnings' => (float) $totalEarnings,
                    'available_earnings' => (float) $user->referral_balance,
                    'referral_code' => $user->referral_code,
                    'recent_referrals' => $referrals->take(10)->map(function ($referral) {
                        return [
                            'name' => $referral->firstname . ' ' . $referral->lastname,
                            'joined_at' => $referral->created_at->toISOString(),
                            'status' => $referral->status
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get referral stats failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve referral statistics'
            ], 500);
        }
    }

    /**
     * Format user profile for API response
     */
    private function formatUserProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'fullname' => $user->firstname . ' ' . $user->lastname,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'account_type' => $user->account_type,
            'status' => $user->status,
            'avatar' => $user->avatar,
            'balance' => (float) $user->balance,
            'referral_code' => $user->referral_code,
            'referral_balance' => (float) $user->referral_balance,
            'email_verified' => !is_null($user->email_verified_at),
            'phone_verified' => !is_null($user->phone_verified_at),
            'kyc_status' => $user->kyc_status ?? 'not_submitted',
            'pin_enabled' => $user->pin_enabled ?? false,
            'date_of_birth' => $user->date_of_birth,
            'address' => $user->address,
            'state' => $user->state,
            'gender' => $user->gender,
            'created_at' => $user->created_at?->toISOString(),
            'last_login_at' => $user->last_login_at?->toISOString()
        ];
    }

    /**
     * Get user statistics
     */
    private function getUserStatistics(User $user): array
    {
        $transactions = Transaction::where('user_id', $user->id);
        
        return [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $transactions->where('status', 'success')->count(),
            'total_spent' => (float) $transactions->where('type', 'debit')->sum('amount'),
            'this_month_spent' => (float) $transactions->where('type', 'debit')
                ->whereMonth('created_at', now()->month)->sum('amount'),
            'favorite_service' => $transactions->select('service_type')
                ->groupBy('service_type')
                ->orderByRaw('COUNT(*) DESC')
                ->value('service_type'),
            'member_since' => $user->created_at?->diffForHumans()
        ];
    }

    /**
     * Get user preferences
     */
    private function getUserPreferences(User $user): array
    {
        return [
            'notifications' => [
                'email' => true,
                'sms' => true,
                'push' => true
            ],
            'security' => [
                'two_factor_enabled' => false,
                'pin_enabled' => $user->pin_enabled ?? false
            ],
            'privacy' => [
                'show_balance' => true,
                'show_activity' => true
            ]
        ];
    }
}