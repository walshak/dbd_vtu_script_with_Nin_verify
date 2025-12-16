<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPlan;
use App\Models\NetworkId;
use App\Models\ServiceSyncStatus;
use App\Services\UzobestSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DataPlanController extends Controller
{
    protected $syncService;

    public function __construct(UzobestSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Display a listing of data plans
     * Auto-syncs from Uzobest if needed (never synced or older than 24 hours)
     */
    public function index()
    {
        // Get sync status
        $syncStatus = ServiceSyncStatus::getStatus('data_plans');

        // Auto-sync if needed
        if (!$syncStatus || $syncStatus->needsSync()) {
            try {
                $this->performAutoSync();
                $syncStatus = ServiceSyncStatus::getStatus('data_plans');
            } catch (\Exception $e) {
                Log::error('Auto-sync failed on data plans page load', [
                    'error' => $e->getMessage()
                ]);
                // Continue loading page even if sync fails
            }
        }

        $dataPlans = DataPlan::with(['network'])->orderBy('dId', 'desc')->get();
        $networks = NetworkId::all();

        return view('admin.data-plans.index', compact('dataPlans', 'networks', 'syncStatus'));
    }

    /**
     * Perform automatic sync (called on page load)
     */
    private function performAutoSync()
    {
        $result = $this->syncService->fetchDataPlans();

        if (!$result['success']) {
            ServiceSyncStatus::recordFailure('data_plans', $result['message'] ?? 'Sync failed');
            return;
        }

        $organizedPlans = $this->syncService->parseDataPlans($result['data']);

        if (empty($organizedPlans)) {
            ServiceSyncStatus::recordFailure('data_plans', 'No plans found in API response');
            return;
        }

        $syncedCount = 0;
        $createdCount = 0;
        $updatedCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            $networkMapping = $this->syncService->getNetworkMapping();

            foreach ($organizedPlans as $networkName => $planTypes) {
                $network = NetworkId::firstOrCreate([
                    'network' => strtoupper($networkName)
                ], [
                    'network' => strtoupper($networkName),
                    'nStatus' => 1
                ]);

                foreach ($planTypes as $planType => $plans) {
                    foreach ($plans as $plan) {
                        try {
                            $planId = $plan['id'] ?? $plan['plan_id'] ?? null;
                            $planName = $plan['plan'] ?? $plan['name'] ?? null;
                            $price = $plan['price'] ?? 0;
                            $validity = $plan['validity'] ?? $plan['day'] ?? 30;

                            if (!$planId || !$planName) {
                                continue;
                            }

                            $basePrice = floatval($price);

                            // Default markup: 5% for selling price
                            $defaultSellingPrice = $basePrice * 1.05;

                            $existingPlan = DataPlan::where('dPlanId', $planId)
                                ->where('nId', $network->nId)
                                ->where('dGroup', $planType)
                                ->first();

                            $planData = [
                                'nId' => $network->nId,
                                'dPlan' => $planName,
                                'dGroup' => $planType,
                                'dPlanId' => $planId,
                                'dValidity' => intval($validity),
                                'dAmount' => $basePrice,
                                'cost_price' => $basePrice, // Cost from Uzobest
                                'uzobest_plan_id' => $planId,
                            ];

                            if ($existingPlan) {
                                // Update existing plan
                                $existingPlan->update($planData);

                                // Only update selling price if admin hasn't customized it
                                if (!$existingPlan->selling_price || $existingPlan->selling_price == ($existingPlan->cost_price * 1.05)) {
                                    $sellingPrice = $defaultSellingPrice;
                                    $existingPlan->update([
                                        'selling_price' => $sellingPrice,
                                        'profit_margin' => $sellingPrice - $basePrice,
                                        'userPrice' => $sellingPrice, // Backwards compatibility
                                    ]);
                                } else {
                                    // Recalculate profit margin with new cost price
                                    $existingPlan->update([
                                        'profit_margin' => $existingPlan->selling_price - $basePrice,
                                    ]);
                                }
                                $updatedCount++;
                            } else {
                                // Create new plan with default markup
                                DataPlan::create(array_merge($planData, [
                                    'selling_price' => $defaultSellingPrice,
                                    'profit_margin' => $defaultSellingPrice - $basePrice,
                                    'userPrice' => $defaultSellingPrice,
                                    'agentPrice' => $basePrice * 1.03, // Keep for legacy
                                    'apiPrice' => $basePrice * 1.02, // Keep for legacy
                                ]));
                                $createdCount++;
                            }

                            $syncedCount++;
                        } catch (\Exception $e) {
                            $errors[] = "Failed to sync plan {$planName}: " . $e->getMessage();
                        }
                    }
                }
            }

            DB::commit();

            // Record sync status
            ServiceSyncStatus::recordSync(
                'data_plans',
                $syncedCount,
                $createdCount,
                $updatedCount,
                count($errors),
                !empty($errors) ? implode('; ', array_slice($errors, 0, 3)) : null
            );
        } catch (\Exception $e) {
            DB::rollBack();
            ServiceSyncStatus::recordFailure('data_plans', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Show the form for creating a new data plan
     */
    public function create()
    {
        $networks = NetworkId::all();
        return view('admin.data-plans.create', compact('networks'));
    }

    /**
     * Store a newly created data plan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|exists:network_ids,nId',
            'dataname' => 'required|string|max:255',
            'datatype' => 'required|in:Gifting,SME,Corporate',
            'planid' => 'required|string|unique:data_plans,dPlanId',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'userprice' => 'required|numeric|min:0',
            'agentprice' => 'required|numeric|min:0',
            'vendorprice' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $network = NetworkId::find($request->network);

        $dataPlan = DataPlan::create([
            'nId' => $request->network,
            'dPlan' => $request->dataname,
            'dGroup' => $request->datatype,
            'dPlanId' => $request->planid,
            'dValidity' => $request->duration,
            'dAmount' => $request->price,
            'userPrice' => $request->userprice,
            'agentPrice' => $request->agentprice,
            'apiPrice' => $request->vendorprice,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data plan created successfully',
                'plan' => $dataPlan->load('network')
            ]);
        }

        return redirect()->route('admin.data-plans.index')
            ->with('success', 'Data plan created successfully');
    }

    /**
     * Display the specified data plan
     */
    public function show(DataPlan $dataPlan)
    {
        $dataPlan->load('network');

        // Always return JSON for AJAX requests (from edit modal)
        return response()->json([
            'success' => true,
            'plan' => $dataPlan
        ]);
    }

    /**
     * Show the form for editing the specified data plan
     */
    public function edit(DataPlan $dataPlan)
    {
        $networks = NetworkId::all();
        $dataPlan->load('network');
        return view('admin.data-plans.edit', compact('dataPlan', 'networks'));
    }

    /**
     * Update the specified data plan
     */
    public function update(Request $request, DataPlan $dataPlan)
    {
        // Simplified validation - only allow updating selling_price
        $validator = Validator::make($request->all(), [
            'selling_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Calculate profit margin
        $costPrice = $dataPlan->cost_price ?? $dataPlan->dAmount ?? 0;
        $sellingPrice = $request->selling_price;
        $profitMargin = $sellingPrice - $costPrice;

        $dataPlan->update([
            'selling_price' => $sellingPrice,
            'profit_margin' => $profitMargin,
            'userPrice' => $sellingPrice, // Keep userPrice in sync for backwards compatibility
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Selling price updated successfully',
                'plan' => $dataPlan->load('network')
            ]);
        }

        return redirect()->route('admin.data-plans.index')
            ->with('success', 'Selling price updated successfully');
    }

    /**
     * Remove the specified data plan
     */
    public function destroy(DataPlan $dataPlan)
    {
        $dataPlan->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data plan deleted successfully'
            ]);
        }

        return redirect()->route('admin.data-plans.index')
            ->with('success', 'Data plan deleted successfully');
    }

    /**
     * Bulk delete data plans
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_ids' => 'required|array',
            'plan_ids.*' => 'exists:data_plans,dId'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DataPlan::whereIn('dId', $request->plan_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->plan_ids) . ' data plans deleted successfully'
        ]);
    }

    /**
     * Toggle plan status
     */
    public function toggleStatus(DataPlan $dataPlan)
    {
        $dataPlan->update([
            'status' => $dataPlan->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'status' => $dataPlan->status,
            'message' => 'Plan status updated successfully'
        ]);
    }

    /**
     * Bulk update prices
     */
    public function bulkUpdatePrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_ids' => 'required|array',
            'plan_ids.*' => 'exists:data_plans,dId',
            'price_type' => 'required|in:price,userprice,agentprice,vendorprice',
            'adjustment_type' => 'required|in:percentage,fixed',
            'adjustment_value' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $plans = DataPlan::whereIn('dId', $request->plan_ids)->get();
        $priceField = $request->price_type;
        $adjustmentType = $request->adjustment_type;
        $adjustmentValue = $request->adjustment_value;

        foreach ($plans as $plan) {
            $currentPrice = $plan->$priceField;

            if ($adjustmentType === 'percentage') {
                $newPrice = $currentPrice + ($currentPrice * ($adjustmentValue / 100));
            } else {
                $newPrice = $currentPrice + $adjustmentValue;
            }

            $plan->update([$priceField => max(0, $newPrice)]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prices updated successfully for ' . count($request->plan_ids) . ' plans'
        ]);
    }

    /**
     * Get plans by network
     */
    public function getByNetwork(Request $request)
    {
        $networkId = $request->get('network_id');
        $dataType = $request->get('data_type');

        $query = DataPlan::where('dNetwork', $networkId);

        if ($dataType) {
            $query->where('type', $dataType);
        }

        $plans = $query->orderBy('userprice')->get();

        return response()->json($plans);
    }

    /**
     * Export data plans
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $plans = DataPlan::with('network')->get();

        if ($format === 'csv') {
            $filename = 'data_plans_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            return response()->stream(function () use ($plans) {
                $file = fopen('php://output', 'w');

                // CSV Headers
                fputcsv($file, [
                    'ID',
                    'Network',
                    'Name',
                    'Type',
                    'Plan ID',
                    'Days',
                    'Price',
                    'User Price',
                    'Agent Price',
                    'Vendor Price',
                    'Status'
                ]);

                foreach ($plans as $plan) {
                    fputcsv($file, [
                        $plan->pId,
                        $plan->network->network ?? 'N/A',
                        $plan->name,
                        $plan->type,
                        $plan->planid,
                        $plan->day,
                        $plan->price,
                        $plan->userprice,
                        $plan->agentprice,
                        $plan->vendorprice,
                        $plan->status ?? 'active'
                    ]);
                }

                fclose($file);
            }, 200, $headers);
        }

        return response()->json(['error' => 'Unsupported format'], 400);
    }

    /**
     * Get plan statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total_plans' => DataPlan::count(),
            'active_plans' => DataPlan::where('status', 'active')->count(),
            'by_network' => DataPlan::select('datanetwork')
                ->selectRaw('count(*) as count')
                ->groupBy('datanetwork')
                ->get(),
            'by_type' => DataPlan::select('type')
                ->selectRaw('count(*) as count')
                ->groupBy('type')
                ->get(),
            'avg_prices' => [
                'user' => DataPlan::avg('userPrice'),
                'agent' => DataPlan::avg('agentPrice'),
                'vendor' => DataPlan::avg('apiPrice'),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Sync data plans from Uzobest API
     * Fetches plans from /api/network/ and updates local database
     *
     * @param UzobestSyncService $syncService
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncFromUzobest(UzobestSyncService $syncService)
    {
        try {
            // Fetch data plans from Uzobest
            $result = $syncService->fetchDataPlans();

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to fetch plans from Uzobest'
                ], 400);
            }

            // Parse the plans
            $organizedPlans = $syncService->parseDataPlans($result['data']);

            if (empty($organizedPlans)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No plans found or failed to parse Uzobest response'
                ], 400);
            }

            $syncedCount = 0;
            $createdCount = 0;
            $updatedCount = 0;
            $errors = [];

            DB::beginTransaction();

            try {
                // Get network mappings
                $networkMapping = $syncService->getNetworkMapping();

                foreach ($organizedPlans as $networkName => $planTypes) {
                    // Find or create network in our database
                    $network = NetworkId::firstOrCreate([
                        'network' => strtoupper($networkName)
                    ], [
                        'network' => strtoupper($networkName),
                        'nStatus' => 1
                    ]);

                    // Process each plan type (SME, Gifting, Corporate)
                    foreach ($planTypes as $planType => $plans) {
                        foreach ($plans as $plan) {
                            try {
                                // Extract plan details from Uzobest format
                                $planId = $plan['id'] ?? $plan['plan_id'] ?? null;
                                $planName = $plan['plan'] ?? $plan['name'] ?? null;
                                $price = $plan['price'] ?? 0;
                                $validity = $plan['validity'] ?? $plan['day'] ?? 30;

                                if (!$planId || !$planName) {
                                    continue; // Skip invalid plans
                                }

                                // Calculate pricing tiers (add markup)
                                // Base price is from Uzobest, add margins for different user types
                                $basePrice = floatval($price);
                                $userPrice = $basePrice * 1.05; // 5% markup for users
                                $agentPrice = $basePrice * 1.03; // 3% markup for agents
                                $vendorPrice = $basePrice * 1.02; // 2% markup for vendors

                                // Check if plan exists
                                $existingPlan = DataPlan::where('dPlanId', $planId)
                                    ->where('nId', $network->nId)
                                    ->where('dGroup', $planType)
                                    ->first();

                                $planData = [
                                    'nId' => $network->nId,
                                    'dPlan' => $planName,
                                    'dGroup' => $planType,
                                    'dPlanId' => $planId,
                                    'dValidity' => intval($validity),
                                    'dAmount' => $basePrice,
                                    'userPrice' => $userPrice,
                                    'agentPrice' => $agentPrice,
                                    'apiPrice' => $vendorPrice,
                                ];

                                if ($existingPlan) {
                                    // Update existing plan
                                    $existingPlan->update($planData);
                                    $updatedCount++;
                                } else {
                                    // Create new plan
                                    DataPlan::create($planData);
                                    $createdCount++;
                                }

                                $syncedCount++;
                            } catch (\Exception $e) {
                                $errors[] = "Failed to sync plan {$planName}: " . $e->getMessage();
                                Log::error('Plan sync error', [
                                    'plan' => $plan,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                }

                DB::commit();

                Log::info('Data plans synced from Uzobest', [
                    'total_synced' => $syncedCount,
                    'created' => $createdCount,
                    'updated' => $updatedCount,
                    'errors' => count($errors)
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data plans synced successfully',
                    'data' => [
                        'total_synced' => $syncedCount,
                        'created' => $createdCount,
                        'updated' => $updatedCount,
                        'errors' => $errors
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Uzobest sync exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Exception during sync: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync data plans from Uzobest API using artisan command
     */
    public function syncPlans()
    {
        try {
            // Run the sync command
            \Artisan::call('data:sync-plans');
            $output = \Artisan::output();

            // Parse the output to get statistics
            preg_match('/Total plans processed\s+\|\s+(\d+)/', $output, $totalMatch);
            preg_match('/Plans updated\s+\|\s+(\d+)/', $output, $updatedMatch);
            preg_match('/New plans created\s+\|\s+(\d+)/', $output, $newMatch);

            $totalPlans = isset($totalMatch[1]) ? (int)$totalMatch[1] : 0;
            $updatedPlans = isset($updatedMatch[1]) ? (int)$updatedMatch[1] : 0;
            $newPlans = isset($newMatch[1]) ? (int)$newMatch[1] : 0;

            return response()->json([
                'status' => 'success',
                'message' => 'Data plans synced successfully',
                'data' => [
                    'total_plans' => $totalPlans,
                    'updated_plans' => $updatedPlans,
                    'new_plans' => $newPlans,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Data plans sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
