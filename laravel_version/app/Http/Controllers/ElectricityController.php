<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\ElectricityProvider;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SiteSettings;
use App\Services\ElectricityService;

class ElectricityController extends Controller
{
    protected $electricityService;

    public function __construct(ElectricityService $electricityService)
    {
        $this->middleware('auth');
        $this->electricityService = $electricityService;
    }

    /**
     * Display electricity bill payment page
     */
    public function index()
    {
        try {
            // Get active electricity providers
            $providers = ElectricityProvider::active()->orderBy('ePlan')->get();

            // Get site settings for service charges and limits
            $siteSettings = SiteSettings::getSiteSettings();
            $serviceCharges = $siteSettings->electricitycharges ?? 50;
            $minimumAmount = $siteSettings->electricity_minimum_amount ?? 1000;
            $maximumAmount = $siteSettings->electricity_maximum_amount ?? 50000;

            // Check maintenance mode
            $maintenanceMode = $siteSettings->electricity_maintenance_mode ?? false;
            $maintenanceMessage = $siteSettings->electricity_maintenance_message ?? 'Electricity service is temporarily unavailable.';

            // Get recent transactions for the user
            $recentTransactions = Transaction::where('sId', Auth::user()->id)
                ->where('servicename', 'Electricity Bill')
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();

            return view('electricity.index', compact(
                'providers',
                'serviceCharges',
                'minimumAmount',
                'maximumAmount',
                'maintenanceMode',
                'maintenanceMessage',
                'recentTransactions'
            ));
        } catch (\Exception $e) {
            Log::error('Electricity Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load electricity service');
        }
    }    /**
     * Get electricity providers
     */
    public function getProviders()
    {
        $providers = ElectricityProvider::getActiveProviders();

        return response()->json([
            'status' => 'success',
            'data' => $providers
        ]);
    }

    /**
     * Validate meter number
     */
    public function validateMeter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string',
            'meter_number' => 'required|string',
            'meter_type' => 'required|string|in:prepaid,postpaid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $result = $this->electricityService->validateMeterNumber(
            $request->provider,
            $request->meter_number,
            $request->meter_type
        );

        return response()->json($result);
    }

    /**
     * Purchase electricity token
     */
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string',
            'meter_number' => 'required|string',
            'meter_type' => 'required|string|in:prepaid,postpaid',
            'amount' => 'required|numeric|min:1000|max:50000',
            'customer_name' => 'required|string',
            'phone' => 'required|string|regex:/^[0-9]{11}$/',
            'transaction_pin' => 'required|string|size:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Verify transaction PIN
        $user = Auth::user();
        if (!$this->verifyTransactionPin($user, $request->transaction_pin)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid transaction PIN'
            ], 400);
        }

        $result = $this->electricityService->purchaseElectricity(
            Auth::user()->id,
            $request->provider,
            $request->meter_number,
            $request->meter_type,
            $request->amount,
            $request->customer_name,
            $request->phone
        );

        return response()->json($result);
    }

    /**
     * Get electricity purchase history
     */
    public function history()
    {
        $transactions = Transaction::where('tUser', Auth::user()->id)
            ->where('tType', Transaction::TYPE_ELECTRICITY)
            ->orderBy('tDate', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    /**
     * Get electricity pricing
     */
    public function getPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string',
            'amount' => 'required|numeric|min:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $provider = ElectricityProvider::getByPlan($request->provider);

        if (!$provider) {
            return response()->json([
                'status' => 'error',
                'message' => 'Provider not found'
            ], 404);
        }

        $user = Auth::user();
        $userPrice = $provider->getUserPrice($user->sType);
        $serviceCharge = $provider->getServiceCharges();
        $totalAmount = $request->amount + $serviceCharge;

        return response()->json([
            'status' => 'success',
            'data' => [
                'amount' => $request->amount,
                'service_charge' => $serviceCharge,
                'total_amount' => $totalAmount,
                'provider' => $provider->ePlan,
                'units_estimate' => round($request->amount / $userPrice, 2)
            ]
        ]);
    }

    /**
     * Verify transaction PIN
     */
    private function verifyTransactionPin($user, $pin)
    {
        return hash('sha256', $pin) === $user->sTransactionPin;
    }
}
