<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CablePlan;
use App\Models\CableId;
use App\Models\ServiceSyncStatus;
use App\Services\UzobestSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class CablePlanController extends Controller
{
    /**
     * Display a listing of cable plans
     * Note: Uzobest doesn't provide cable plan listing, so sync status tracks manual entries
     */
    public function index()
    {
        // Initialize sync status for cable plans (manual entry tracking)
        $syncStatus = ServiceSyncStatus::firstOrCreate(
            ['service_type' => 'cable_plans'],
            [
                'sync_status' => 'never',
                'last_sync_at' => null,
                'total_synced' => CablePlan::count(),
                'api_source' => 'manual',
                'last_error' => 'Uzobest does not provide cable plan listing API'
            ]
        );

        $cablePlans = CablePlan::with('provider')
            ->orderBy('created_at', 'desc')
            ->get();

        $providers = CableId::active()->get();

        return view('admin.cable-plans.index', compact('cablePlans', 'providers', 'syncStatus'));
    }

    /**
     * Store a newly created cable plan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|exists:cable_ids,cId',
            'planname' => 'required|string|max:255',
            'planid' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'userprice' => 'required|numeric|min:0',
            'agentprice' => 'required|numeric|min:0',
            'vendorprice' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate plan ID within same provider
        $existingPlan = CablePlan::where('cableprovider', $request->provider)
            ->where('planid', $request->planid)
            ->first();

        if ($existingPlan) {
            return response()->json([
                'success' => false,
                'message' => 'A plan with this ID already exists for this provider'
            ], 422);
        }

        // Validate pricing tiers
        if ($request->userprice < $request->price) {
            return response()->json([
                'success' => false,
                'message' => 'User price cannot be less than buying price'
            ], 422);
        }

        // Get cable provider to set uzobest_cable_id
        $cableProvider = \App\Models\CableId::find($request->provider);
        $uzobestCableId = $cableProvider ? $cableProvider->cableid : $request->provider;

        // Calculate initial profit margin
        $costPrice = floatval($request->price);
        $sellingPrice = floatval($request->userprice);
        $profitMargin = $sellingPrice - $costPrice;

        $plan = CablePlan::create([
            'name' => $request->planname,
            'price' => $request->price,
            'cost_price' => $costPrice,
            'userprice' => $request->userprice,
            'selling_price' => $sellingPrice,
            'profit_margin' => $profitMargin,
            'agentprice' => $request->agentprice,
            'vendorprice' => $request->vendorprice,
            'planid' => $request->planid,
            'uzobest_plan_id' => $request->planid,
            'uzobest_cable_id' => $uzobestCableId,
            'cableprovider' => $request->provider,
            'day' => $request->duration,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cable plan added successfully',
            'plan' => $plan
        ]);
    }

    /**
     * Display the specified cable plan
     */
    public function show(CablePlan $plan)
    {
        $plan->load('provider');

        return response()->json([
            'success' => true,
            'plan' => $plan
        ]);
    }

    /**
     * Update the specified cable plan
     * Simplified to only update selling_price for unified pricing model
     */
    public function update(Request $request, CablePlan $plan)
    {

        // If simplified update (selling_price only)
        if ($request->has('selling_price') && !$request->has('planname')) {
            $validator = Validator::make($request->all(), [
                'selling_price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Calculate profit margin (absolute amount, not percentage)
            $costPrice = floatval($plan->cost_price ?? $plan->price ?? 0);
            $sellingPrice = floatval($request->selling_price);
            $profitMargin = $sellingPrice - $costPrice; // Absolute profit amount

            $plan->update([
                'selling_price' => $sellingPrice,
                'profit_margin' => $profitMargin,
                'userprice' => $sellingPrice, // Keep backwards compatibility
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cable plan price updated successfully',
                'plan' => $plan->fresh()
            ]);
        }

        // Full plan update (legacy support)
        $validator = Validator::make($request->all(), [
            'provider' => 'required|exists:cable_ids,cId',
            'planname' => 'required|string|max:255',
            'planid' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'userprice' => 'required|numeric|min:0',
            'agentprice' => 'required|numeric|min:0',
            'vendorprice' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate plan ID within same provider (excluding current plan)
        $existingPlan = CablePlan::where('cableprovider', $request->provider)
            ->where('planid', $request->planid)
            ->where('cpId', '!=', $id)
            ->first();

        if ($existingPlan) {
            return response()->json([
                'success' => false,
                'message' => 'A plan with this ID already exists for this provider'
            ], 422);
        }

        // Validate pricing tiers
        if ($request->userprice < $request->price) {
            return response()->json([
                'success' => false,
                'message' => 'User price cannot be less than buying price'
            ], 422);
        }

        $plan->update([
            'name' => $request->planname,
            'price' => $request->price,
            'userprice' => $request->userprice,
            'agentprice' => $request->agentprice,
            'vendorprice' => $request->vendorprice,
            'planid' => $request->planid,
            'cableprovider' => $request->provider,
            'day' => $request->duration
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cable plan updated successfully',
            'plan' => $plan
        ]);
    }

    /**
     * Remove the specified cable plan
     */
    public function destroy($id)
    {
        $plan = CablePlan::findOrFail($id);
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cable plan deleted successfully'
        ]);
    }

    /**
     * Toggle plan status
     */
    public function toggleStatus($id)
    {
        $plan = CablePlan::findOrFail($id);
        $plan->status = $plan->status === 'active' ? 'inactive' : 'active';
        $plan->save();

        return response()->json([
            'success' => true,
            'message' => 'Plan status updated successfully',
            'status' => $plan->status
        ]);
    }

    /**
     * Bulk delete plans
     */
    public function bulkDelete(Request $request)
    {
        $planIds = $request->input('plan_ids');

        if (!is_array($planIds) || empty($planIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No plans selected'
            ], 422);
        }

        $deletedCount = CablePlan::whereIn('cpId', $planIds)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} plans deleted successfully"
        ]);
    }

    /**
     * Bulk update prices
     */
    public function bulkUpdatePrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_ids' => 'required|string',
            'price_type' => 'required|in:price,userprice,agentprice,vendorprice',
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

        $planIds = json_decode($request->plan_ids, true);
        $priceType = $request->price_type;
        $adjustmentType = $request->adjustment_type;
        $adjustmentValue = $request->adjustment_value;

        $plans = CablePlan::whereIn('cpId', $planIds)->get();
        $updatedCount = 0;

        foreach ($plans as $plan) {
            $currentPrice = $plan->{$priceType};

            if ($adjustmentType === 'percentage') {
                $newPrice = $currentPrice * (1 + ($adjustmentValue / 100));
            } else {
                $newPrice = $currentPrice + $adjustmentValue;
            }

            // Ensure price doesn't go below 0
            $newPrice = max(0, $newPrice);

            $plan->{$priceType} = round($newPrice, 2);
            $plan->save();
            $updatedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} plans updated successfully"
        ]);
    }

    /**
     * Export plans to CSV
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $plans = CablePlan::with('provider')->get();

        if ($format === 'csv') {
            $filename = 'cable_plans_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($plans) {
                $file = fopen('php://output', 'w');

                // CSV header
                fputcsv($file, [
                    'ID',
                    'Name',
                    'Provider',
                    'Plan ID',
                    'Duration (Days)',
                    'Buying Price',
                    'User Price',
                    'Agent Price',
                    'Vendor Price',
                    'Status',
                    'Created At'
                ]);

                // CSV data
                foreach ($plans as $plan) {
                    fputcsv($file, [
                        $plan->cpId,
                        $plan->name,
                        $plan->provider ? $plan->provider->provider : 'N/A',
                        $plan->planid,
                        $plan->day,
                        $plan->price,
                        $plan->userprice,
                        $plan->agentprice,
                        $plan->vendorprice,
                        $plan->status,
                        $plan->created_at ? $plan->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return response()->json(['error' => 'Unsupported format'], 400);
    }

    /**
     * Get cable plan statistics
     */
    public function getStatistics()
    {
        $stats = CablePlan::getStatistics();

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Get plans by provider
     */
    public function getPlansByProvider(Request $request)
    {
        $providerId = $request->get('provider_id');

        if (!$providerId) {
            return response()->json([
                'success' => false,
                'message' => 'Provider ID is required'
            ], 422);
        }

        $plans = CablePlan::where('cableprovider', $providerId)
            ->where('status', 'active')
            ->get();

        return response()->json([
            'success' => true,
            'plans' => $plans
        ]);
    }

    /**
     * Validate IUC/Smartcard number via Uzobest API
     * Endpoint: GET /api/validateiuc
     *
     * @param Request $request
     * @param UzobestSyncService $syncService
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateIUC(Request $request, UzobestSyncService $syncService)
    {
        $validator = Validator::make($request->all(), [
            'iuc_number' => 'required|string',
            'provider_id' => 'required|exists:cable_ids,cId'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get cable provider
            $provider = CableId::find($request->provider_id);

            // Get Uzobest provider ID from our cable_ids table or use mapping
            $providerMapping = $syncService->getCableProviderMapping();
            $uzobestProviderId = $providerMapping[strtoupper($provider->provider)] ?? 1;

            // Validate IUC via Uzobest
            $result = $syncService->validateIUC($request->iuc_number, $uzobestProviderId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'IUC number validated successfully',
                    'customer_name' => $result['customer_name'] ?? 'N/A',
                    'data' => $result['data'] ?? []
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Invalid IUC number'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('IUC validation error', [
                'iuc' => $request->iuc_number,
                'provider' => $request->provider_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to validate IUC number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync cable plans from Uzobest API
     * Note: Uzobest API doesn't provide a direct endpoint to list all cable plans
     * This method serves as a placeholder for manual plan entry or future API enhancement
     *
     * @param UzobestSyncService $syncService
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncFromUzobest(UzobestSyncService $syncService)
    {
        try {
            // Note: Uzobest API doesn't have a /api/cableplans endpoint
            // Cable plans need to be obtained from Uzobest documentation or support
            // This method can be used when Uzobest provides an endpoint to list plans

            Log::info('Cable plan sync requested - manual implementation needed');

            return response()->json([
                'success' => false,
                'message' => 'Cable plan sync not available. Uzobest API does not provide a cable plans listing endpoint. Please enter cable plans manually based on Uzobest documentation or use the validateIUC feature to verify plans.',
                'note' => 'Cable providers supported: DSTV (ID: 1), GOTV (ID: 2), STARTIMES (ID: 3)'
            ], 501); // 501 Not Implemented
        } catch (\Exception $e) {
            Log::error('Cable sync exception', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Exception during sync: ' . $e->getMessage()
            ], 500);
        }
    }
}
