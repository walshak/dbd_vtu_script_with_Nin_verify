<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CableTVService;
use App\Models\CableId;
use App\Models\CablePlan;
use App\Models\Transaction;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CableTVController extends Controller
{
    protected $cableTVService;

    public function __construct(CableTVService $cableTVService)
    {
        $this->cableTVService = $cableTVService;
    }

    /**
     * Show cable TV subscription page
     */
    public function index()
    {
        try {
            // Get active cable providers
            $providers = CableId::active()->get();
            
            // Get site settings
            $siteSettings = SiteSettings::getSiteSettings();
            $serviceCharges = $siteSettings->cabletvcharges ?? 50;
            $minimumAmount = $siteSettings->cabletv_minimum_amount ?? 500;
            $maximumAmount = $siteSettings->cabletv_maximum_amount ?? 50000;
            
            // Check maintenance mode
            $maintenanceMode = $siteSettings->cabletv_maintenance_mode ?? false;
            $maintenanceMessage = $siteSettings->cabletv_maintenance_message ?? 'Cable TV service is temporarily unavailable.';
            
            // Get recent transactions for the user
            $recentTransactions = Transaction::where('sId', Auth::id())
                ->where('servicename', 'Cable Subscription')
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();

            return view('cable-tv.index', compact(
                'providers', 
                'serviceCharges', 
                'minimumAmount', 
                'maximumAmount',
                'maintenanceMode',
                'maintenanceMessage',
                'recentTransactions'
            ));
        } catch (\Exception $e) {
            Log::error('Cable TV Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load cable TV service');
        }
    }

    /**
     * Get cable plans for a decoder
     */
    public function getPlans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'decoder' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid decoder selected'
            ], 400);
        }

        // Get available decoders/providers  
        $decoders = $this->cableTVService->getAvailableDecoders();
        
        if (!$request->decoder || !in_array($request->decoder, $decoders)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid decoder provider'
            ]);
        }

        $plans = $this->cableTVService->getCablePlans(
            Auth::id(),
            $request->decoder
        );

        return response()->json([
            'status' => 'success',
            'data' => $plans
        ]);
    }

    /**
     * Validate IUC/Smart Card number
     */
    public function validateIUC(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'decoder' => 'required|string',
            'iuc_number' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $decoders = $this->cableTVService->getAvailableDecoders();
        
        if (!$request->decoder || !in_array($request->decoder, $decoders)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid decoder provider'
            ], 400);
        }

        $result = $this->cableTVService->validateIUC(
            $request->decoder,
            $request->iuc_number
        );

        return response()->json($result);
    }

    /**
     * Purchase cable TV subscription
     */
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'decoder' => 'required|string',
            'iuc_number' => 'required|string',
            'plan_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $decoders = $this->cableTVService->getAvailableDecoders();
        
        if (!$request->decoder || !in_array($request->decoder, $decoders)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid decoder provider'
            ], 400);
        }

        $result = $this->cableTVService->purchaseCableSubscription(
            Auth::id(),
            $request->decoder,
            $request->iuc_number,
            $request->plan_id
        );

        if ($result['status'] === 'success') {
            return response()->json($result);
        } else {
            return response()->json($result, 400);
        }
    }

    /**
     * Get supported decoders
     */
    public function getSupportedDecoders()
    {
        $decoders = $this->cableTVService->getAvailableDecoders();

        return response()->json([
            'status' => 'success',
            'data' => $decoders
        ]);
    }

    /**
     * API endpoint for mobile app
     */
    public function apiPurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'decoder' => 'required|string',
            'iuc_number' => 'required|string',
            'plan_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $decoders = $this->cableTVService->getAvailableDecoders();
        
        if (!$request->decoder || !in_array($request->decoder, $decoders)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid decoder provider'
            ], 400);
        }

        $result = $this->cableTVService->purchaseCableSubscription(
            Auth::id(),
            $request->decoder,
            $request->iuc_number,
            $request->plan_id
        );

        return response()->json($result);
    }
}
