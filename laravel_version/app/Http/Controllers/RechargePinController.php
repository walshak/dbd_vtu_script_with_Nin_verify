<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\RechargePin;
use App\Models\NetworkId;
use App\Models\Transaction;
use App\Models\User;
use App\Services\RechargePinService;

class RechargePinController extends Controller
{
    protected $rechargePinService;

    public function __construct(RechargePinService $rechargePinService)
    {
        $this->middleware('auth');
        $this->rechargePinService = $rechargePinService;
    }

    /**
     * Show recharge pin purchase page
     */
    public function index()
    {
        $networks = NetworkId::getAllActive();
        $denominations = RechargePin::getAvailableDenominations();
        return view('user.recharge-pins', compact('networks', 'denominations'));
    }

    /**
     * Get recharge pin discounts
     */
    public function getDiscounts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'denomination' => 'required|numeric|min:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Get network details to find discount rates
        $network = NetworkId::getByName($request->network);
        if (!$network) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid network selected'
            ], 400);
        }

        $user = Auth::user();
        $rechargePin = RechargePin::getByNetworkAndDenomination($network->nId, $request->denomination);
        
        if (!$rechargePin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Recharge pin not available for this denomination'
            ], 400);
        }

        $discountedPrice = $rechargePin->getUserPrice($user->sType);
        $discount = $request->denomination - $discountedPrice;
        $discountPercentage = ($discount / $request->denomination) * 100;

        return response()->json([
            'status' => 'success',
            'data' => [
                'network' => $request->network,
                'denomination' => $request->denomination,
                'discounted_price' => $discountedPrice,
                'discount_amount' => $discount,
                'discount_percentage' => round($discountPercentage, 2)
            ]
        ]);
    }

    /**
     * Get pricing for recharge pin
     */
    public function getPricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'denomination' => 'required|numeric|min:100',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Get network details
        $network = NetworkId::getByName($request->network);
        if (!$network) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid network selected'
            ], 400);
        }

        $user = Auth::user();
        $rechargePin = RechargePin::getByNetworkAndDenomination($network->nId, $request->denomination);
        
        if (!$rechargePin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Recharge pin not available for this denomination'
            ], 400);
        }

        $unitPrice = $rechargePin->getUserPrice($user->sType);
        $totalAmount = $unitPrice * $request->quantity;

        return response()->json([
            'status' => 'success',
            'data' => [
                'network' => $request->network,
                'denomination' => $request->denomination,
                'unit_price' => $unitPrice,
                'quantity' => $request->quantity,
                'total_amount' => $totalAmount
            ]
        ]);
    }

    /**
     * Purchase recharge pin
     */
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'denomination' => 'required|numeric|min:100',
            'quantity' => 'required|integer|min:1|max:10',
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

        // Use the RechargePinService to handle the purchase
        $result = $this->rechargePinService->purchaseRechargePin(
            Auth::id(),
            $request->network,
            $request->denomination,
            $request->quantity
        );

        return response()->json($result, $result['status'] === 'success' ? 200 : 400);
    }

    /**
     * Get recharge pin history
     */
    public function history()
    {
        $history = $this->rechargePinService->getRechargePinHistory(Auth::id());
        
        return view('user.recharge-pin-history', [
            'transactions' => $history
        ]);
    }

    /**
     * Get available networks for recharge pins
     */
    public function getNetworks()
    {
        $networks = NetworkId::getAllActive();
        
        return response()->json([
            'status' => 'success',
            'data' => $networks->map(function ($network) {
                return [
                    'id' => $network->nId,
                    'name' => $network->network,
                    'code' => strtolower($network->network)
                ];
            })
        ]);
    }

    /**
     * Get available denominations
     */
    public function getDenominations()
    {
        $denominations = RechargePin::getAvailableDenominations();
        
        return response()->json([
            'status' => 'success',
            'data' => $denominations
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