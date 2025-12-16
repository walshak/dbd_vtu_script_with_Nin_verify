<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:50',
            'lname' => 'required|string|max:50',
            'email' => 'nullable|email|max:50',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|max:15',
            'cpassword' => 'required|same:password',
            'state' => 'required|string|max:50',
            'transpin' => 'required|numeric|digits:4',
            'referal' => 'nullable|string|max:15',
            'account' => 'required|integer|in:1,2,3',
        ], [
            'phone.unique' => 'Phone number already exists.',
            'cpassword.same' => 'Password is different from confirm password.',
            'transpin.digits' => 'Transaction PIN must be 4 digits.',
        ]);

        // Additional validations matching existing system
        if ($request->email) {
            $emailExists = User::where('email', $request->email)->exists();
            if ($emailExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email already exists.'
                ], 200);
            }
        }

        // Check if password is same as phone number
        if ($request->password === $request->phone) {
            return response()->json([
                'status' => 'error',
                'message' => "You can't use your phone number as password."
            ], 200);
        }

        // Create user
        $user = User::create([
            'name' => $request->fname . ' ' . $request->lname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'state' => $request->state,
            'transaction_pin' => $request->transpin,
            'user_type' => $request->account,
            'referred_by' => $request->referal,
            'api_key' => User::generateApiKey($request->phone),
            'reg_status' => User::REG_STATUS_ACTIVE, // Active by default
            'wallet_balance' => 0,
            'referral_wallet' => 0,
            'pin_status' => 1,
        ]);

        // Generate login token
        $randomToken = substr(str_shuffle("ABCDEFGHIJklmnopqrstvwxyz"), 0, 10);
        $userLoginToken = time() . $randomToken . mt_rand(100, 1000);

        // Store login token
        UserLogin::create([
            'user' => $user->id,
            'token' => $userLoginToken,
        ]);

        // Log the user in
        Auth::login($user);

        // Set session data
        session([
            'loginId' => $user->id,
            'loginAccToken' => $userLoginToken,
            'loginState' => $user->reg_status,
            'loginAccount' => $user->user_type,
            'loginPhone' => base64_encode($user->phone),
            'loginName' => base64_encode($user->name),
        ]);

        // Set cookies
        Cookie::queue('loginState', $user->reg_status, 2592000 * 30);
        Cookie::queue('loginAccount', $user->user_type, 2592000 * 30);
        Cookie::queue('loginPhone', base64_encode($user->phone), 31540000 * 30);
        Cookie::queue('loginName', base64_encode($user->name), 31540000 * 30);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful'
        ], 200);
    }
}
