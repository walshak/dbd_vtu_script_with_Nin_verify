<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TransactionPinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show PIN setup form
     */
    public function showSetupForm()
    {
        $user = Auth::user();
        
        return view('user.pin.setup', compact('user'));
    }

    /**
     * Setup transaction PIN
     */
    public function setupPin(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'pin' => 'required|digits:4',
            'pin_confirmation' => 'required|same:pin',
            'password' => 'required',
        ]);

        // Verify current password
        if (!Hash::check($request->password, $user->sPass)) {
            return back()->withErrors(['password' => 'Current password is incorrect']);
        }

        // Set PIN
        $user->sPin = $request->pin;
        $user->sPinStatus = 1; // Enable PIN
        $user->save();

        return redirect()->route('profile')->with('success', 'Transaction PIN set successfully');
    }

    /**
     * Show PIN change form
     */
    public function showChangeForm()
    {
        $user = Auth::user();
        
        if ($user->sPinStatus == 0) {
            return redirect()->route('pin.setup')->with('info', 'Please set up your transaction PIN first');
        }

        return view('user.pin.change', compact('user'));
    }

    /**
     * Change transaction PIN
     */
    public function changePin(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_pin' => 'required|digits:4',
            'new_pin' => 'required|digits:4',
            'new_pin_confirmation' => 'required|same:new_pin',
        ]);

        // Verify current PIN
        if ($user->sPin != $request->current_pin) {
            return back()->withErrors(['current_pin' => 'Current PIN is incorrect']);
        }

        // Update PIN
        $user->sPin = $request->new_pin;
        $user->save();

        return redirect()->route('profile')->with('success', 'Transaction PIN changed successfully');
    }

    /**
     * Toggle PIN status (enable/disable)
     */
    public function togglePinStatus(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'password' => 'required',
        ]);

        // Verify password
        if (!Hash::check($request->password, $user->sPass)) {
            return back()->withErrors(['password' => 'Current password is incorrect']);
        }

        // Toggle PIN status
        $user->sPinStatus = $user->sPinStatus == 1 ? 0 : 1;
        $user->save();

        $status = $user->sPinStatus == 1 ? 'enabled' : 'disabled';
        return redirect()->route('profile')->with('success', "Transaction PIN {$status} successfully");
    }

    /**
     * Reset PIN with password verification
     */
    public function resetPin(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'password' => 'required',
            'new_pin' => 'required|digits:4',
            'new_pin_confirmation' => 'required|same:new_pin',
        ]);

        // Verify password
        if (!Hash::check($request->password, $user->sPass)) {
            return back()->withErrors(['password' => 'Current password is incorrect']);
        }

        // Reset PIN
        $user->sPin = $request->new_pin;
        $user->sPinStatus = 1; // Enable PIN
        $user->save();

        return redirect()->route('profile')->with('success', 'Transaction PIN reset successfully');
    }

    /**
     * Validate PIN (API endpoint)
     */
    public function validatePin(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        $isValid = $user->sPin == $request->pin;

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'PIN is valid' : 'Invalid PIN'
        ]);
    }

    /**
     * Get PIN status
     */
    public function getPinStatus()
    {
        $user = Auth::user();

        return response()->json([
            'pin_enabled' => $user->sPinStatus == 1,
            'pin_set' => $user->sPin != 1234, // Default PIN
        ]);
    }
}