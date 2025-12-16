<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FeatureToggle;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class AuthController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'required|string|max:20|unique:users',
                'referral_code' => 'nullable|string|exists:users,referral_code',
                'device_info' => 'nullable|array',
                'fcm_token' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Create user
            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'referral_code' => $this->generateReferralCode(),
                'referred_by' => $request->referral_code ? 
                    User::where('referral_code', $request->referral_code)->first()?->id : null,
                'account_type' => 'user',
                'status' => 'active',
                'email_verification_token' => Str::random(60),
                'device_info' => $request->device_info,
                'fcm_token' => $request->fcm_token,
                'last_login_at' => now(),
                'ip_address' => $request->ip()
            ]);

            // Create access token
            $token = $user->createToken('mobile-app', ['*'], now()->addMonths(12))->plainTextToken;

            DB::commit();

            // Send welcome notification
            $this->notificationService->sendWelcomeNotification($user);

            // Send email verification if enabled
            if (FeatureToggle::isEnabled(FeatureToggle::FEATURE_EMAIL_VERIFICATION)) {
                $this->sendVerificationEmail($user);
            }

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $this->formatUserData($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => now()->addMonths(12)->toISOString()
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User registration failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
                'fcm_token' => 'nullable|string',
                'device_info' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find user by username or email
            $user = User::where('username', $request->username)
                      ->orWhere('email', $request->username)
                      ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                Log::warning('Failed login attempt', [
                    'username' => $request->username,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Check if account is active
            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is not active. Please contact support.'
                ], 403);
            }

            // Update user login information
            $user->update([
                'last_login_at' => now(),
                'fcm_token' => $request->fcm_token,
                'device_info' => $request->device_info,
                'ip_address' => $request->ip()
            ]);

            // Revoke old tokens (optional - keep only latest)
            $user->tokens()->where('created_at', '<', now()->subDays(30))->delete();

            // Create new access token
            $token = $user->createToken('mobile-app', ['*'], now()->addMonths(12))->plainTextToken;

            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $this->formatUserData($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => now()->addMonths(12)->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Login failed. Please try again.',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            Log::info('User logged out successfully', [
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);

        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Logout failed'
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();
            
            // Revoke current token
            $request->user()->currentAccessToken()->delete();
            
            // Create new token
            $token = $user->createToken('mobile-app', ['*'], now()->addMonths(12))->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => now()->addMonths(12)->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed'
            ], 500);
        }
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email_verification_token', $request->token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification token'
                ], 400);
            }

            $user->update([
                'email_verified_at' => now(),
                'email_verification_token' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Email verification failed'
            ], 500);
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if ($user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already verified'
                ], 400);
            }

            // Generate new verification token
            $user->update([
                'email_verification_token' => Str::random(60)
            ]);

            $this->sendVerificationEmail($user);

            return response()->json([
                'success' => true,
                'message' => 'Verification email sent successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Resend verification failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email'
            ], 500);
        }
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            // Generate reset token
            $resetToken = Str::random(60);
            
            $user->update([
                'password_reset_token' => $resetToken,
                'password_reset_expires_at' => now()->addHours(2)
            ]);

            // Send reset email
            $this->sendPasswordResetEmail($user, $resetToken);

            return response()->json([
                'success' => true,
                'message' => 'Password reset email sent successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Forgot password failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset email'
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('password_reset_token', $request->token)
                       ->where('password_reset_expires_at', '>', now())
                       ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token'
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'password_reset_token' => null,
                'password_reset_expires_at' => null
            ]);

            // Revoke all tokens for security
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed'
            ], 500);
        }
    }

    /**
     * Social login (Google, Facebook, etc.)
     */
    public function socialLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'provider' => 'required|in:google,facebook',
                'provider_id' => 'required|string',
                'email' => 'required|email',
                'name' => 'required|string',
                'avatar' => 'nullable|url',
                'fcm_token' => 'nullable|string',
                'device_info' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if user exists with this provider
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // Create new user
                $nameParts = explode(' ', $request->name, 2);
                $user = User::create([
                    'firstname' => $nameParts[0],
                    'lastname' => $nameParts[1] ?? '',
                    'username' => $this->generateUsername($request->email),
                    'email' => $request->email,
                    'password' => Hash::make(Str::random(32)),
                    'referral_code' => $this->generateReferralCode(),
                    'account_type' => 'user',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'avatar' => $request->avatar,
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id,
                    'fcm_token' => $request->fcm_token,
                    'device_info' => $request->device_info,
                    'last_login_at' => now(),
                    'ip_address' => $request->ip()
                ]);

                $this->notificationService->sendWelcomeNotification($user);
            } else {
                // Update existing user
                $user->update([
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id,
                    'avatar' => $request->avatar,
                    'fcm_token' => $request->fcm_token,
                    'device_info' => $request->device_info,
                    'last_login_at' => now(),
                    'ip_address' => $request->ip()
                ]);
            }

            // Create access token
            $token = $user->createToken('mobile-app', ['*'], now()->addMonths(12))->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Social login successful',
                'data' => [
                    'user' => $this->formatUserData($user),
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => now()->addMonths(12)->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Social login failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Social login failed'
            ], 500);
        }
    }

    /**
     * Format user data for API response
     */
    private function formatUserData(User $user): array
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
            'email_verified' => !is_null($user->email_verified_at),
            'phone_verified' => !is_null($user->phone_verified_at),
            'kyc_status' => $user->kyc_status ?? 'not_submitted',
            'created_at' => $user->created_at?->toISOString(),
            'last_login_at' => $user->last_login_at?->toISOString()
        ];
    }

    /**
     * Generate unique referral code
     */
    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Generate unique username
     */
    private function generateUsername(string $email): string
    {
        $baseUsername = explode('@', $email)[0];
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail(User $user): void
    {
        // Implementation would depend on your mail setup
        // For now, we'll just log it
        Log::info('Verification email sent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $user->email_verification_token
        ]);
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail(User $user, string $token): void
    {
        // Implementation would depend on your mail setup
        // For now, we'll just log it
        Log::info('Password reset email sent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token
        ]);
    }
}