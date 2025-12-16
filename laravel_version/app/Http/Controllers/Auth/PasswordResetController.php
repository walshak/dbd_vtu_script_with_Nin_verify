<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Send password reset OTP to user's phone
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $phone = $request->phone;

        // Find user by phone
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No account found with this phone number.'
            ]);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Generate reset token
        $token = Str::random(60);

        // Store OTP and token in session (in production, you'd use database or cache)
        session([
            'password_reset_otp' => $otp,
            'password_reset_token' => $token,
            'password_reset_phone' => $phone,
            'password_reset_expires' => now()->addMinutes(10)
        ]);

        // In production, send OTP via SMS
        // For development, we'll just log it
        Log::info("Password reset OTP for {$phone}: {$otp}");

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent to your phone number.',
            'token' => $token
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'otp' => 'required|string|size:6'
        ]);

        $sessionToken = session('password_reset_token');
        $sessionOTP = session('password_reset_otp');
        $expiresAt = session('password_reset_expires');

        if (!$sessionToken || !$sessionOTP || !$expiresAt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired reset session.'
            ]);
        }

        if (now()->gt($expiresAt)) {
            // Clear expired session
            session()->forget(['password_reset_otp', 'password_reset_token', 'password_reset_phone', 'password_reset_expires']);

            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new one.'
            ]);
        }

        if ($request->token !== $sessionToken || $request->otp !== $sessionOTP) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP. Please try again.'
            ]);
        }

        // Mark OTP as verified
        session(['password_reset_verified' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully.'
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password'
        ]);

        $sessionToken = session('password_reset_token');
        $verified = session('password_reset_verified');
        $phone = session('password_reset_phone');

        if (!$sessionToken || !$verified || !$phone) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid reset session.'
            ]);
        }

        if ($request->token !== $sessionToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid reset token.'
            ]);
        }

        // Find user and update password
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ]);
        }

        // Update password (plain text as per existing system)
        $user->password = $request->password;
        $user->save();

        // Clear reset session
        session()->forget([
            'password_reset_otp',
            'password_reset_token',
            'password_reset_phone',
            'password_reset_expires',
            'password_reset_verified'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully.'
        ]);
    }
}
