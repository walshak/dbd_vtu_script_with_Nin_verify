<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RechargePin;
use App\Models\NetworkId;
use App\Services\UzobestSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * RechargePin Controller - Manages airtime (recharge pin) discounts
 *
 * Note: Uzobest API supports 3 airtime types:
 * - VTU: Regular virtual top-up (standard airtime)
 * - Share and Sell: Airtime that can be resold/shared
 * - awuf4U: Bonus airtime with extra value
 *
 * The discounts configured here apply to all airtime types
 */

class RechargePinController extends Controller
{
    /**
     * Display a listing of recharge pin discounts
     */
    public function index()
    {
        $rechargePinDiscounts = RechargePin::with('network')
            ->orderBy('aId', 'desc')
            ->get();

        $networks = NetworkId::getSupportedNetworks();

        return view('admin.recharge-pins.index', compact('rechargePinDiscounts', 'networks'));
    }

    /**
     * Store a newly created recharge pin discount
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|exists:network_ids,nId',
            'userdiscount' => 'required|numeric|min:0|max:100',
            'agentdiscount' => 'required|numeric|min:0|max:100',
            'vendordiscount' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate network
        $existingDiscount = RechargePin::where('aNetwork', $request->network)->first();

        if ($existingDiscount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount already exists for this network'
            ], 422);
        }

        // Validate discount logic
        if ($request->userdiscount < $request->agentdiscount || $request->agentdiscount < $request->vendordiscount) {
            return response()->json([
                'success' => false,
                'message' => 'User discount should be highest, followed by agent, then vendor'
            ], 422);
        }

        $discount = RechargePin::create([
            'aNetwork' => $request->network,
            'aUserDiscount' => $request->userdiscount,
            'aAgentDiscount' => $request->agentdiscount,
            'aVendorDiscount' => $request->vendordiscount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recharge pin discount added successfully',
            'discount' => $discount
        ]);
    }

    /**
     * Display the specified recharge pin discount
     */
    public function show($id)
    {
        $discount = RechargePin::with('network')->findOrFail($id);

        return response()->json([
            'success' => true,
            'discount' => $discount
        ]);
    }

    /**
     * Update the specified recharge pin discount
     */
    public function update(Request $request, $id)
    {
        $discount = RechargePin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'network' => 'required|exists:network_ids,nId',
            'userdiscount' => 'required|numeric|min:0|max:100',
            'agentdiscount' => 'required|numeric|min:0|max:100',
            'vendordiscount' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate network (excluding current discount)
        $existingDiscount = RechargePin::where('aNetwork', $request->network)
            ->where('aId', '!=', $id)
            ->first();

        if ($existingDiscount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount already exists for this network'
            ], 422);
        }

        // Validate discount logic
        if ($request->userdiscount < $request->agentdiscount || $request->agentdiscount < $request->vendordiscount) {
            return response()->json([
                'success' => false,
                'message' => 'User discount should be highest, followed by agent, then vendor'
            ], 422);
        }

        $discount->update([
            'aNetwork' => $request->network,
            'aUserDiscount' => $request->userdiscount,
            'aAgentDiscount' => $request->agentdiscount,
            'aVendorDiscount' => $request->vendordiscount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recharge pin discount updated successfully',
            'discount' => $discount
        ]);
    }

    /**
     * Remove the specified recharge pin discount
     */
    public function destroy($id)
    {
        $discount = RechargePin::findOrFail($id);
        $discount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recharge pin discount deleted successfully'
        ]);
    }

    /**
     * Bulk update discounts
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'discount_ids' => 'required|string',
            'discount_type' => 'required|in:aUserDiscount,aAgentDiscount,aVendorDiscount',
            'adjustment_type' => 'required|in:percentage,fixed',
            'adjustment_value' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $discountIds = json_decode($request->discount_ids, true);
        $discountType = $request->discount_type;
        $adjustmentType = $request->adjustment_type;
        $adjustmentValue = $request->adjustment_value;

        $discounts = RechargePin::whereIn('aId', $discountIds)->get();
        $updatedCount = 0;

        foreach ($discounts as $discount) {
            $currentDiscount = $discount->{$discountType};

            if ($adjustmentType === 'percentage') {
                $newDiscount = $currentDiscount * (1 + ($adjustmentValue / 100));
            } else {
                $newDiscount = $currentDiscount + $adjustmentValue;
            }

            // Ensure discount is within valid range
            $newDiscount = max(0, min(100, $newDiscount));

            $discount->{$discountType} = round($newDiscount, 2);
            $discount->save();
            $updatedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} discounts updated successfully"
        ]);
    }

    /**
     * Get recharge pin statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total_networks' => RechargePin::count(),
            'avg_user_discount' => RechargePin::avg('aUserDiscount'),
            'avg_agent_discount' => RechargePin::avg('aAgentDiscount'),
            'avg_vendor_discount' => RechargePin::avg('aVendorDiscount'),
            'highest_user_discount' => RechargePin::max('aUserDiscount'),
            'lowest_user_discount' => RechargePin::min('aUserDiscount'),
            'networks_with_discounts' => RechargePin::distinct('aNetwork')->count()
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Get discount by network
     */
    public function getDiscountByNetwork(Request $request)
    {
        $networkId = $request->get('network_id');

        if (!$networkId) {
            return response()->json([
                'success' => false,
                'message' => 'Network ID is required'
            ], 422);
        }

        $discount = RechargePin::where('aNetwork', $networkId)->first();

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'No discount found for this network'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'discount' => $discount
        ]);
    }

    /**
     * Calculate pricing for amount and user type
     */
    public function calculatePricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required|exists:network_ids,nId',
            'amount' => 'required|numeric|min:100',
            'user_type' => 'required|in:user,agent,vendor',
            'quantity' => 'nullable|integer|min:1|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $discount = RechargePin::where('aNetwork', $request->network_id)->first();

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'No discount found for this network'
            ], 404);
        }

        $quantity = $request->quantity ?? 1;
        $discountRate = $discount->getUserDiscount($request->user_type);
        $amountToPay = ($request->amount * $discountRate / 100) * $quantity;
        $totalSavings = ($request->amount * $quantity) - $amountToPay;

        return response()->json([
            'success' => true,
            'pricing' => [
                'original_amount' => $request->amount * $quantity,
                'discount_rate' => $discountRate,
                'amount_to_pay' => round($amountToPay, 2),
                'total_savings' => round($totalSavings, 2),
                'quantity' => $quantity,
                'unit_price' => round($amountToPay / $quantity, 2)
            ]
        ]);
    }

    /**
     * Get Uzobest airtime type information
     * Returns supported airtime types and their descriptions
     *
     * @param UzobestSyncService $syncService
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAirtimeTypes(UzobestSyncService $syncService)
    {
        try {
            $airtimeTypes = $syncService->getAirtimeTypeMapping();

            $typesWithDescription = [
                'VTU' => [
                    'type' => 'VTU',
                    'name' => 'Virtual Top-Up',
                    'description' => 'Regular airtime that can be used for calls, SMS, and data'
                ],
                'Share and Sell' => [
                    'type' => 'Share and Sell',
                    'name' => 'Share and Sell',
                    'description' => 'Airtime that can be shared or resold to other users'
                ],
                'awuf4U' => [
                    'type' => 'awuf4U',
                    'name' => 'Awuf4U',
                    'description' => 'Bonus airtime with extra value (e.g., buy ₦100 get ₦120)'
                ]
            ];

            return response()->json([
                'success' => true,
                'airtime_types' => $typesWithDescription,
                'note' => 'Discounts configured in this section apply to all airtime types'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch airtime types', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch airtime types'
            ], 500);
        }
    }
}

