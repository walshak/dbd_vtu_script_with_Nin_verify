<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirtimePrice;
use App\Models\NetworkId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AirtimePricingController extends Controller
{
    /**
     * Display airtime pricing management page
     */
    public function index()
    {
        $networks = NetworkId::all();
        $pricings = AirtimePrice::with('network')->orderBy('aNetwork')->get();

        return view('admin.airtime.index', compact('networks', 'pricings'));
    }

    /**
     * Store new airtime pricing
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'user_discount' => 'required|numeric|min:0|max:100',
            'agent_discount' => 'required|numeric|min:0|max:100',
            'vendor_discount' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pricing = AirtimePrice::updateOrCreate(
                ['aNetwork' => $request->network],
                [
                    'aUserDiscount' => $request->user_discount,
                    'aAgentDiscount' => $request->agent_discount,
                    'aVendorDiscount' => $request->vendor_discount,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Airtime pricing saved successfully',
                'pricing' => $pricing->load('network')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save pricing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific airtime pricing
     */
    public function show(AirtimePrice $pricing)
    {
        $pricing->load('network');

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'pricing' => $pricing
            ]);
        }

        return view('admin.airtime.show', compact('pricing'));
    }

    /**
     * Update airtime pricing
     */
    public function update(Request $request, AirtimePrice $pricing)
    {
        $validator = Validator::make($request->all(), [
            'user_discount' => 'required|numeric|min:0|max:100',
            'agent_discount' => 'required|numeric|min:0|max:100',
            'vendor_discount' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pricing->update([
                'aUserDiscount' => $request->user_discount,
                'aAgentDiscount' => $request->agent_discount,
                'aVendorDiscount' => $request->vendor_discount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Airtime pricing updated successfully',
                'pricing' => $pricing->load('network')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pricing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete airtime pricing
     */
    public function destroy(AirtimePrice $pricing)
    {
        try {
            $pricing->delete();

            return response()->json([
                'success' => true,
                'message' => 'Airtime pricing deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete pricing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update airtime pricing
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pricing_ids' => 'required|array',
            'pricing_ids.*' => 'exists:airtimepinprice,aId',
            'discount_type' => 'required|in:user,agent,vendor',
            'adjustment_type' => 'required|in:percentage,fixed',
            'adjustment_value' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pricings = AirtimePrice::whereIn('aId', $request->pricing_ids)->get();
            $discountField = 'a' . ucfirst($request->discount_type) . 'Discount';
            $adjustmentType = $request->adjustment_type;
            $adjustmentValue = $request->adjustment_value;
            $updated = 0;

            DB::beginTransaction();

            foreach ($pricings as $pricing) {
                $currentValue = $pricing->$discountField;

                if ($adjustmentType === 'percentage') {
                    $newValue = $currentValue + ($currentValue * ($adjustmentValue / 100));
                } else {
                    $newValue = $currentValue + $adjustmentValue;
                }

                // Ensure discount stays between 0 and 100
                $newValue = max(0, min(100, $newValue));

                $pricing->update([
                    $discountField => $newValue
                ]);
                $updated++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "$updated pricing(s) updated successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pricings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export airtime pricing to CSV
     */
    public function export()
    {
        $pricings = AirtimePrice::with('network')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="airtime-pricing-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($pricings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Network', 'User Discount (%)', 'Agent Discount (%)', 'Vendor Discount (%)']);

            foreach ($pricings as $pricing) {
                fputcsv($file, [
                    $pricing->network->network ?? 'N/A',
                    $pricing->aUserDiscount,
                    $pricing->aAgentDiscount,
                    $pricing->aVendorDiscount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get pricing statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total_networks' => AirtimePrice::count(),
            'avg_user_discount' => AirtimePrice::avg('aUserDiscount'),
            'avg_agent_discount' => AirtimePrice::avg('aAgentDiscount'),
            'avg_vendor_discount' => AirtimePrice::avg('aVendorDiscount'),
            'by_network' => AirtimePrice::with('network')
                ->get()
                ->map(function ($pricing) {
                    return [
                        'network' => $pricing->network->network ?? 'N/A',
                        'user_discount' => $pricing->aUserDiscount,
                        'agent_discount' => $pricing->aAgentDiscount,
                        'vendor_discount' => $pricing->aVendorDiscount,
                    ];
                })
        ];

        return response()->json($stats);
    }
}
