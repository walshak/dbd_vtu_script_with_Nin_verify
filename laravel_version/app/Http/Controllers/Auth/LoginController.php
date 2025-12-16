<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'password' => 'required|string|max:15',
        ]);

        // Find user by phone number
        $user = User::where('sPhone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Invalid login credentials'
            ], 200);
        }

        // Check password (plain text comparison - matching existing system)
        if ($user->sPass !== $request->password) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Invalid login credentials'
            ], 200);
        }

        // Check if account is blocked
        if ($user->sRegStatus == User::REG_STATUS_BLOCKED) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Your account has been blocked by admin.'
            ], 200);
        }

        // Update last activity
        $user->update(['sLastActivity' => now()]);

        // Generate login token (matching existing system)
        $randomToken = substr(str_shuffle("ABCDEFGHIJklmnopqrstvwxyz"), 0, 10);
        $userLoginToken = time() . $randomToken . mt_rand(100, 1000);

        // Store/update login token
        UserLogin::updateOrCreate(
            ['user' => $user->sId],
            ['token' => $userLoginToken, 'updated_at' => now()]
        );

        // Log the user in
        Auth::login($user);

        // Set session data (matching existing system)
        session([
            'loginId' => $user->sId,
            'loginAccToken' => $userLoginToken,
            'loginState' => $user->sRegStatus,
            'loginAccount' => $user->sType,
            'loginPhone' => base64_encode($user->sPhone),
            'loginName' => base64_encode($user->sFname),
        ]);

        // Set cookies (matching existing system lifespan)
        Cookie::queue('loginState', $user->sRegStatus, 2592000 * 30); // 30 months
        Cookie::queue('loginAccount', $user->sType, 2592000 * 30);
        Cookie::queue('loginPhone', base64_encode($user->sPhone), 31540000 * 30);
        Cookie::queue('loginName', base64_encode($user->sFname), 31540000 * 30);

        return response()->json([
            'status' => 'success', 
            'message' => 'Login successful'
        ], 200);
    }

    public function logout(Request $request)
    {
        // Clear login token from database
        if (session('loginId')) {
            UserLogin::where('user', session('loginId'))->delete();
        }

        // Logout user
        Auth::logout();
        
        // Clear session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear auth cookies
        Cookie::queue(Cookie::forget('loginState'));
        Cookie::queue(Cookie::forget('loginAccount'));
        Cookie::queue(Cookie::forget('loginPhone'));
        Cookie::queue(Cookie::forget('loginName'));
        
        return redirect('/login');
    }
}
