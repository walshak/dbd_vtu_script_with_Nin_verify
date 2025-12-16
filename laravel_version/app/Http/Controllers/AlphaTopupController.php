<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\AlphaTopup;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AlphaTopupService;

class AlphaTopupController extends Controller
{
    protected $alphaTopupService;

    public function __construct(AlphaTopupService $alphaTopupService)
    {
        $this->middleware('auth');
        $this->alphaTopupService = $alphaTopupService;
    }

    /**
     * Show alpha topup page
     */
    public function index()
    {
        $alphaProviders = AlphaTopup::getActiveProviders();
        return view('user.alpha-topup', compact('alphaProviders'));
    }

    /**
     * Get alpha topup providers
     */
    public function getProviders()
    {
        $providers = AlphaTopup::getActiveProviders();

        return response()->json([
            'status' => 'success',
            'data' => $providers
        ]);
    }

    /**
     * Get alpha topup pricing
     */
    public function getPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string',
            'amount' => 'required|numeric|min:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $alphaProvider = AlphaTopup::getByPlan($request->provider);

        if (!$alphaProvider || !$alphaProvider->isActive()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alpha topup provider not found or inactive'
            ], 400);
        }

        $user = Auth::user();
        $finalAmount = $alphaProvider->calculateFinalAmount($request->amount, $user->sType);

        return response()->json([
            'status' => 'success',
            'data' => [
                'provider' => $request->provider,
                'requested_amount' => $request->amount,
                'final_amount' => $finalAmount,
                'service_charge' => $finalAmount - $request->amount,
                'description' => $alphaProvider->description
            ]
        ]);
    }

    /**
     * Purchase alpha topup
     */
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string',
            'amount' => 'required|numeric|min:100',
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

        // Use the AlphaTopupService to handle the purchase
        $result = $this->alphaTopupService->purchaseAlphaTopup(
            Auth::id(),
            $request->provider,
            $request->phone,
            $request->amount
        );

        return response()->json($result, $result['status'] === 'success' ? 200 : 400);
    }

    /**
     * Get alpha topup history
     */
    public function history()
    {
        $history = $this->alphaTopupService->getAlphaTopupHistory(Auth::id());
        
        return view('user.alpha-topup-history', [
            'transactions' => $history
        ]);
    }

    /**
     * Get available alpha topup providers
     */
    public function getAvailableProviders()
    {
        $providers = AlphaTopup::getActiveProviders();
        
        return response()->json([
            'status' => 'success',
            'data' => $providers->map(function ($provider) {
                return [
                    'code' => $provider->provider_code,
                    'name' => $provider->provider_name,
                    'minimum_amount' => $provider->minimum_amount ?? 100,
                    'maximum_amount' => $provider->maximum_amount ?? 10000,
                    'service_charge' => $provider->service_charge ?? 0,
                    'available' => $provider->isActive()
                ];
            })
        ]);
    }

    /**
     * Verify transaction PIN
     */
    private function verifyTransactionPin($user, $pin)
    {
        return $user->sPin === $pin;
    }
}