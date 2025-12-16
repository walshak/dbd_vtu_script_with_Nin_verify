<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    /**
     * Show admin login form
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect('/admin/dashboard');
        }

        return view('auth.admin-login');
    }

    /**
     * Handle admin login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'remember' => 'boolean'
        ]);

        try {
            // Find admin
            $admin = Admin::where('sysUsername', $request->username)->first();

            if (!$admin) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Incorrect username or password'
                    ], 200);
                }
                return back()->withErrors([
                    'username' => 'No admin account found with this username.'
                ])->withInput($request->except('password'));
            }

            // Check if account is active
            if (!$admin->isActive()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Your account has been blocked.'
                    ], 200);
                }
                return back()->withErrors([
                    'username' => 'Your admin account is blocked. Please contact super admin.'
                ])->withInput($request->except('password'));
            }

            // Verify password (handle both plain text and hashed passwords for migration)
            $passwordValid = false;
            if (Hash::needsRehash($admin->sysToken)) {
                // Check plain text password for legacy compatibility
                if ($request->password === $admin->sysToken) {
                    $passwordValid = true;
                    // Update to hashed password for security
                    $admin->sysToken = Hash::make($request->password);
                    $admin->save();
                }
            } else {
                // Check hashed password
                $passwordValid = Hash::check($request->password, $admin->sysToken);
            }

            if (!$passwordValid) {
                // Log failed login attempt
                Log::warning('Failed admin login attempt', [
                    'username' => $request->username,
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent')
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Incorrect username or password'
                    ], 200);
                }
                return back()->withErrors([
                    'password' => 'Incorrect password.'
                ])->withInput($request->except('password'));
            }

            // Login successful
            Auth::guard('admin')->login($admin, $request->filled('remember'));

            // Set session data (matching existing system)
            session([
                'sysUser' => $admin->sysUsername,
                'sysRole' => $admin->sysRole,
                'sysName' => $admin->sysName,
                'sysId' => $admin->sysId,
            ]);

            // Log successful login
            Log::info('Successful admin login', [
                'admin_id' => $admin->sysId,
                'username' => $admin->sysUsername,
                'ip' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Login successful'
                ], 200);
            }
            return redirect()->intended('/admin/dashboard')
                ->with('success', 'Welcome back, ' . $admin->sysName . '!');
        } catch (\Exception $e) {
            Log::error('Admin login error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred during login. Please try again.'
                ], 200);
            }
            return back()->withErrors([
                'username' => 'An error occurred during login. Please try again.'
            ])->withInput($request->except('password'));
        }
    }

    /**
     * Handle admin logout request
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show admin profile
     */
    public function showProfile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile.show', compact('admin'));
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:sysusers,sysUsername,' . $admin->sysId . ',sysId',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|string|min:6|confirmed'
        ]);

        try {
            // Verify current password if changing password
            if ($request->filled('new_password')) {
                if (!Hash::check($request->current_password, $admin->sysToken)) {
                    return back()->withErrors([
                        'current_password' => 'Current password is incorrect.'
                    ]);
                }
                $admin->sysToken = Hash::make($request->new_password);
            }

            $admin->sysName = $request->name;
            $admin->sysUsername = $request->username;
            $admin->save();

            return back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Admin profile update error: ' . $e->getMessage());
            return back()->withErrors([
                'username' => 'An error occurred. Please try again.'
            ]);
        }
    }

    /**
     * Show change password form
     */
    public function showChangePasswordForm()
    {
        return view('admin.profile.change-password');
    }

    /**
     * Change admin password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        try {
            $admin = Auth::guard('admin')->user();

            // Verify current password
            if (!Hash::check($request->current_password, $admin->sysToken)) {
                return back()->withErrors([
                    'current_password' => 'Current password is incorrect.'
                ]);
            }

            // Update password
            $admin->sysToken = Hash::make($request->new_password);
            $admin->save();

            // Log password change
            Log::info('Admin password changed', [
                'admin_id' => $admin->sysId,
                'username' => $admin->sysUsername
            ]);

            return back()->with('success', 'Password changed successfully.');
        } catch (\Exception $e) {
            Log::error('Admin password change error: ' . $e->getMessage());
            return back()->withErrors([
                'new_password' => 'An error occurred. Please try again.'
            ]);
        }
    }
}
