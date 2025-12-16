<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm(Request $request)
    {
        // Clear any potentially corrupted sessions when accessing login
        if (Auth::check()) {
            Log::info('User was already logged in when accessing login form', [
                'user_id' => Auth::id(),
                'phone' => Auth::user()->phone
            ]);
            return redirect()->intended('/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);

        try {
            // Clear any existing session before attempting login
            if (Auth::check()) {
                Auth::logout();
                $request->session()->flush();
            }

            // Find user by phone number
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                Log::warning('Login attempt with non-existent phone', [
                    'phone' => $request->phone,
                    'ip' => $request->ip()
                ]);

                // Check if it's an AJAX request
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Phone number not found. Please check your phone number or register for a new account.'
                    ], 401);
                }
                return back()->withErrors([
                    'phone' => 'Phone number not found. Please check your phone number or register for a new account.'
                ])->withInput($request->except('password'));
            }

            // Check if account is blocked
            if ($user->isBlocked()) {
                Log::warning('Login attempt on blocked account', [
                    'phone' => $request->phone,
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Your account has been blocked. Please contact support for assistance.'
                    ], 401);
                }
                return back()->withErrors([
                    'phone' => 'Your account has been blocked. Please contact support for assistance.'
                ])->withInput($request->except('password'));
            }

            // Check password (using Laravel's password hashing)
            $passwordMatch = Hash::check($request->password, $user->password);

            if (!$passwordMatch) {
                // Log failed login attempt
                Log::warning('Failed login attempt - invalid password', [
                    'phone' => $request->phone,
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid password. Please check your password and try again.'
                    ], 401);
                }
                return back()->withErrors([
                    'password' => 'Invalid password. Please check your password and try again.'
                ])->withInput($request->except('password'));
            }

            // Update last activity
            $user->update(['last_activity' => now()]);

            // Login successful - regenerate session for security
            $request->session()->regenerate();
            Auth::login($user, $request->boolean('remember'));

            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'phone' => $user->phone,
                'name' => $user->name,
                'ip' => $request->ip()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Login successful! Welcome back.',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'sFname' => explode(' ', $user->name)[0], // First name for frontend compatibility
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'wallet' => $user->wallet_balance,
                        'account_type' => $user->account_type_name ?? 'User'
                    ]
                ]);
            }

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            Log::error('Login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone' => $request->phone ?? 'N/A',
                'ip' => $request->ip()
            ]);

            // Ensure no user is logged in if there was an error
            if (Auth::check()) {
                Auth::logout();
                $request->session()->flush();
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred during login. Please try again.'
                ], 500);
            }

            return back()->withErrors([
                'phone' => 'An error occurred during login. Please try again.'
            ])->withInput($request->except('password'));
        }
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm(Request $request)
    {
        // Clear any existing authentication session when accessing registration
        if (Auth::check()) {
            Log::info('User was logged in when accessing registration form, logging out', [
                'user_id' => Auth::id(),
                'phone' => Auth::user()->phone
            ]);
            Auth::logout();
            $request->session()->flush();
            $request->session()->regenerate();
        }

        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        // Clear any existing session/authentication before registration
        if (Auth::check()) {
            Auth::logout();
            $request->session()->flush();
            $request->session()->regenerate();
        }

        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone|regex:/^[0-9]{11}$/',
            'password' => 'required|string|min:8|max:15',
            'cpassword' => 'required|string|same:password',
            'state' => 'required|string|max:100',
            'transpin' => 'required|string|size:4|regex:/^[0-9]{4}$/',
            'account' => 'required|integer|in:1,2,3',
            'referal' => 'nullable|string'
        ], [
            'email.unique' => 'This email address is already registered. Please use a different email or login to your existing account.',
            'phone.unique' => 'This phone number is already registered. Please use a different phone number or login to your existing account.',
            'phone.regex' => 'Phone number must be exactly 11 digits.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.max' => 'Password must not exceed 15 characters.',
            'cpassword.same' => 'Password confirmation does not match.',
            'transpin.size' => 'Transaction PIN must be exactly 4 digits.',
            'transpin.regex' => 'Transaction PIN can only contain numbers.',
            'account.in' => 'Please select a valid account type.'
        ]);

        try {
            // Double-check uniqueness to prevent race conditions
            $existingUser = User::where('email', $request->email)
                ->orWhere('phone', $request->phone)
                ->first();

            if ($existingUser) {
                $field = $existingUser->email === $request->email ? 'email' : 'phone';
                $message = $field === 'email'
                    ? 'This email address is already registered.'
                    : 'This phone number is already registered.';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $message
                    ], 422);
                }
                return back()->withErrors([$field => $message])->withInput($request->except(['password', 'cpassword', 'transpin']));
            }

            // Check if referral user exists
            $referralUser = null;
            if ($request->referal) {
                $referralUser = User::where('phone', 'like', '%' . $request->referal)->first();
            }

            // Create user - using Laravel's password hashing
            $user = User::create([
                'name' => $request->fname . ' ' . $request->lname,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'state' => $request->state,
                'transaction_pin' => $request->transpin,
                'user_type' => $request->account,
                'reg_status' => User::REG_STATUS_ACTIVE,
                'wallet_balance' => 0.00,
                'referral_wallet' => 0.00,
                'pin_status' => 1,
                'referred_by' => $referralUser ? $referralUser->phone : null,
                'api_key' => User::generateApiKey($request->phone),
                'last_activity' => now()
            ]);

            if (!$user || !$user->id) {
                throw new \Exception('User creation failed');
            }

            // Login the newly created user
            Auth::login($user);
            $request->session()->regenerate();

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'phone' => $user->phone,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Registration successful! Welcome to VASTLEAD.',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'sFname' => explode(' ', $user->name)[0], // First name for frontend compatibility
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'wallet' => $user->wallet_balance,
                        'account_type' => $user->account_type_name ?? 'User'
                    ]
                ]);
            }

            return redirect('/dashboard')->with('success', 'Registration successful! Welcome to VASTLEAD.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors specifically
            Log::warning('Registration validation error', [
                'errors' => $e->errors(),
                'phone' => $request->phone ?? 'N/A',
                'email' => $request->email ?? 'N/A',
                'ip' => $request->ip()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please check your input and try again.',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput($request->except(['password', 'cpassword', 'transpin']));
        } catch (\Exception $e) {
            Log::error('Registration error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone' => $request->phone ?? 'N/A',
                'email' => $request->email ?? 'N/A',
                'ip' => $request->ip()
            ]);

            // Ensure no user is logged in if there was an error
            if (Auth::check()) {
                Auth::logout();
                $request->session()->flush();
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Registration failed. Please check your information and try again.'
                ], 500);
            }

            return back()->withErrors([
                'email' => 'Registration failed. Please check your information and try again.'
            ])->withInput($request->except(['password', 'cpassword', 'transpin']));
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Log::info('User logged out', ['user_id' => $user->id, 'phone' => $user->phone]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ]);
        }

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show password reset request form
     */
    public function showPasswordResetForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset code via email
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Email address not found.'
                    ], 404);
                }
                return back()->withErrors(['email' => 'Email address not found.']);
            }

            // Generate 6-digit reset code
            $resetCode = rand(100000, 999999);

            // Store reset code temporarily (in a real app, you'd use a separate table)
            $user->update([
                'ver_code' => $resetCode,
                'last_activity' => now()
            ]);

            // TODO: Send email with reset code
            // For now, we'll just log it for testing
            Log::info('Password reset code generated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'code' => $resetCode
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reset code sent to your email address.',
                    // For testing only - remove in production
                    'reset_code' => $resetCode
                ]);
            }

            return back()->with('success', 'Reset code sent to your email address.');
        } catch (\Exception $e) {
            Log::error('Password reset error', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send reset code. Please try again.'
                ], 500);
            }

            return back()->withErrors(['email' => 'Failed to send reset code. Please try again.']);
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        try {
            $user = User::where('email', $request->email)
                ->where('ver_code', $request->otp)
                ->first();

            if (!$user) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid verification code.'
                    ], 400);
                }
                return back()->withErrors(['otp' => 'Invalid verification code.']);
            }

            // Generate reset token
            $resetToken = Str::random(60);

            // For simplicity, we'll use the ver_code field to store the token temporarily
            $user->update(['ver_code' => $resetToken]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Code verified successfully.',
                    'token' => $resetToken
                ]);
            }

            return redirect()->route('password.reset.form', ['token' => $resetToken]);
        } catch (\Exception $e) {
            Log::error('OTP verification error', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to verify code. Please try again.'
                ], 500);
            }

            return back()->withErrors(['otp' => 'Failed to verify code. Please try again.']);
        }
    }

    /**
     * Show password reset form with token
     */
    public function showResetForm($token)
    {
        $user = User::where('ver_code', $token)->first();

        if (!$user) {
            return redirect()->route('login')->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        return view('auth.reset-password', compact('token'));
    }

    /**
     * Update password with token
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|max:15|confirmed'
        ]);

        try {
            $user = User::where('email', $request->email)
                ->where('ver_code', $request->token)
                ->first();

            if (!$user) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid or expired reset token.'
                    ], 400);
                }
                return back()->withErrors(['token' => 'Invalid or expired reset token.']);
            }

            // Update password using Laravel's password hashing
            $user->update([
                'password' => Hash::make($request->password),
                'ver_code' => null // Clear the token
            ]);

            Log::info('Password reset successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password updated successfully!'
                ]);
            }

            return redirect()->route('login')->with('success', 'Password updated successfully! You can now login with your new password.');
        } catch (\Exception $e) {
            Log::error('Password update error', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update password. Please try again.'
                ], 500);
            }

            return back()->withErrors(['password' => 'Failed to update password. Please try again.']);
        }
    }
}
