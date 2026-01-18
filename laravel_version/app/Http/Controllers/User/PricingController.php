<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataPlan;
use App\Models\CablePlan;
use App\Models\CableId;
use App\Models\NetworkId;
use Illuminate\Support\Facades\DB;

class PricingController extends Controller
{
    /**
     * Display pricing page with all service prices
     */
    public function index()
    {
        // Get data plans with network info
        $dataPlans = DataPlan::with('network')
            ->select('dId', 'nId', 'dPlan', 'dGroup', 'dAmount', 'dValidity', 'selling_price', 'userPrice')
            ->orderBy('nId', 'asc')
            ->orderBy('dGroup', 'asc')
            ->orderByRaw('CAST(REPLACE(REPLACE(dAmount, "GB", ""), "MB", "") AS DECIMAL(10,2)) ASC')
            ->get()
            ->groupBy(function($plan) {
                return $plan->network ? $plan->network->network : 'Unknown';
            });

        // Get cable plans with provider info
        $cablePlans = CablePlan::with('provider')
            ->where('status', 'active')
            ->select('cpId', 'name', 'cableprovider', 'selling_price', 'userprice', 'day', 'type')
            ->orderBy('cableprovider', 'asc')
            ->orderBy('selling_price', 'asc')
            ->get()
            ->groupBy(function($plan) {
                return $plan->provider ? strtoupper($plan->provider->provider) : 'Unknown';
            });

        // Get networks for airtime info
        $networks = NetworkId::where('status', 'active')
            ->where('networkStatus', 'On')
            ->select('nId', 'network')
            ->orderBy('network', 'asc')
            ->get();

        return view('pricing', compact('dataPlans', 'cablePlans', 'networks'));
    }
}
