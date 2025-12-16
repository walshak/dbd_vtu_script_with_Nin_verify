<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserUpgradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show upgrade to agent page
     */
    public function showAgentUpgrade()
    {
        $user = Auth::user();
        
        if (!$user->canUpgradeToAgent()) {
            return redirect()->route('dashboard')->with('error', 'You cannot upgrade to Agent at this time.');
        }

        $upgradeCost = $user->getUpgradeCost(User::TYPE_AGENT);
        $siteSettings = SiteSettings::first();

        return view('user.upgrade.agent', compact('user', 'upgradeCost', 'siteSettings'));
    }

    /**
     * Show upgrade to vendor page
     */
    public function showVendorUpgrade()
    {
        $user = Auth::user();
        
        if (!$user->canUpgradeToVendor()) {
            return redirect()->route('dashboard')->with('error', 'You cannot upgrade to Vendor at this time.');
        }

        $upgradeCost = $user->getUpgradeCost(User::TYPE_VENDOR);
        $siteSettings = SiteSettings::first();

        return view('user.upgrade.vendor', compact('user', 'upgradeCost', 'siteSettings'));
    }

    /**
     * Process agent upgrade
     */
    public function upgradeToAgent(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'transaction_pin' => $user->sPinStatus ? 'required|digits:4' : 'nullable',
        ]);

        $result = $user->upgradeAccountType(User::TYPE_AGENT, $request->transaction_pin);

        if ($result['status'] === 'success') {
            return redirect()->route('dashboard')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Process vendor upgrade
     */
    public function upgradeToVendor(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'transaction_pin' => $user->sPinStatus ? 'required|digits:4' : 'nullable',
        ]);

        $result = $user->upgradeAccountType(User::TYPE_VENDOR, $request->transaction_pin);

        if ($result['status'] === 'success') {
            return redirect()->route('dashboard')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Get upgrade costs (API endpoint)
     */
    public function getUpgradeCosts()
    {
        $user = Auth::user();
        $siteSettings = SiteSettings::first();

        return response()->json([
            'agent_cost' => $siteSettings?->agentupgrade ?? 500,
            'vendor_cost' => $siteSettings?->vendorupgrade ?? 1000,
            'can_upgrade_to_agent' => $user->canUpgradeToAgent(),
            'can_upgrade_to_vendor' => $user->canUpgradeToVendor(),
            'current_type' => $user->account_type_name,
            'wallet_balance' => $user->sWallet,
        ]);
    }
}