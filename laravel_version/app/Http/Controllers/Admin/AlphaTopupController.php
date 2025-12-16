<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AlphaTopup;
use App\Models\Transaction;

class AlphaTopupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display alpha topup management page
     */
    public function index()
    {
        $alphaTopups = AlphaTopup::orderBy('sellingPrice')->get();
        $statistics = $this->getStatistics();

        return view('admin.alpha-topup.index', [
            'alphaTopups' => $alphaTopups,
            'statistics' => $statistics
        ]);
    }

    /**
     * Store new alpha topup plan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selling_price' => 'required|numeric|min:0|unique:alphatopupprice,sellingPrice',
            'buying_price' => 'required|numeric|min:0',
            'agent_price' => 'required|numeric|min:0',
            'vendor_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Validate pricing logic
        if ($request->buying_price > $request->selling_price) {
            return response()->json([
                'status' => 'error',
                'message' => 'Buying price cannot be higher than selling price'
            ], 400);
        }

        if ($request->agent_price > $request->selling_price || $request->vendor_price > $request->selling_price) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agent and vendor prices cannot be higher than selling price'
            ], 400);
        }

        try {
            $alphaTopup = AlphaTopup::create([
                'sellingPrice' => $request->selling_price,
                'buyingPrice' => $request->buying_price,
                'agent' => $request->agent_price,
                'vendor' => $request->vendor_price,
                'dPosted' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Alpha topup plan added successfully',
                'data' => $alphaTopup
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add alpha topup plan'
            ], 500);
        }
    }

    /**
     * Update alpha topup plan
     */
    public function update(Request $request, AlphaTopup $alphaTopup)
    {
        $validator = Validator::make($request->all(), [
            'selling_price' => 'required|numeric|min:0|unique:alphatopupprice,sellingPrice,' . $alphaTopup->alphaId . ',alphaId',
            'buying_price' => 'required|numeric|min:0',
            'agent_price' => 'required|numeric|min:0',
            'vendor_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Validate pricing logic
        if ($request->buying_price > $request->selling_price) {
            return response()->json([
                'status' => 'error',
                'message' => 'Buying price cannot be higher than selling price'
            ], 400);
        }

        if ($request->agent_price > $request->selling_price || $request->vendor_price > $request->selling_price) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agent and vendor prices cannot be higher than selling price'
            ], 400);
        }

        try {
            $alphaTopup->update([
                'sellingPrice' => $request->selling_price,
                'buyingPrice' => $request->buying_price,
                'agent' => $request->agent_price,
                'vendor' => $request->vendor_price
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Alpha topup plan updated successfully',
                'data' => $alphaTopup
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update alpha topup plan'
            ], 500);
        }
    }

    /**
     * Delete alpha topup plan
     */
    public function destroy(AlphaTopup $alphaTopup)
    {
        try {
            // Check if there are any transactions for this alpha topup
            $transactionCount = Transaction::where('sDesc', 'LIKE', '%Alpha%')
                ->where('sAmount', $alphaTopup->sellingPrice)
                ->where('sType', 'alphatopup')
                ->count();

            if ($transactionCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete alpha topup plan with existing transactions'
                ], 400);
            }

            $alphaTopup->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Alpha topup plan deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete alpha topup plan'
            ], 500);
        }
    }

    /**
     * Bulk update pricing
     */
    public function bulkUpdatePrices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'update_type' => 'required|in:percentage,amount',
            'adjustment_value' => 'required|numeric',
            'price_type' => 'required|in:selling,buying,agent,vendor',
            'alpha_topup_ids' => 'required|array|min:1',
            'alpha_topup_ids.*' => 'exists:alphatopupprice,alphaId'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $alphaTopups = AlphaTopup::whereIn('alphaId', $request->alpha_topup_ids)->get();
            $updateCount = 0;

            foreach ($alphaTopups as $alphaTopup) {
                $fieldMap = [
                    'selling' => 'sellingPrice',
                    'buying' => 'buyingPrice', 
                    'agent' => 'agent',
                    'vendor' => 'vendor'
                ];

                $fieldName = $fieldMap[$request->price_type];
                $currentPrice = $alphaTopup->$fieldName;

                if ($request->update_type === 'percentage') {
                    $newPrice = $currentPrice * (1 + ($request->adjustment_value / 100));
                } else {
                    $newPrice = $currentPrice + $request->adjustment_value;
                }

                // Ensure price doesn't go below 0
                if ($newPrice >= 0) {
                    $alphaTopup->$fieldName = round($newPrice, 2);
                    $alphaTopup->save();
                    $updateCount++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Successfully updated {$request->price_type} prices for {$updateCount} alpha topup plans"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update prices'
            ], 500);
        }
    }

    /**
     * Export alpha topups to CSV
     */
    public function export()
    {
        try {
            $alphaTopups = AlphaTopup::orderBy('sellingPrice')->get();

            $filename = 'alpha_topups_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($alphaTopups) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Selling Price',
                    'Buying Price',
                    'Agent Price',
                    'Vendor Price',
                    'User Profit Margin',
                    'Agent Profit Margin',
                    'Vendor Profit Margin',
                    'Created Date'
                ]);

                // Add data rows
                foreach ($alphaTopups as $alphaTopup) {
                    $userProfit = $alphaTopup->sellingPrice - $alphaTopup->buyingPrice;
                    $agentProfit = $alphaTopup->agent - $alphaTopup->buyingPrice;
                    $vendorProfit = $alphaTopup->vendor - $alphaTopup->buyingPrice;

                    fputcsv($file, [
                        $alphaTopup->alphaId,
                        number_format($alphaTopup->sellingPrice, 2),
                        number_format($alphaTopup->buyingPrice, 2),
                        number_format($alphaTopup->agent, 2),
                        number_format($alphaTopup->vendor, 2),
                        number_format($userProfit, 2),
                        number_format($agentProfit, 2),
                        number_format($vendorProfit, 2),
                        $alphaTopup->dPosted ?? 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export alpha topups'
            ], 500);
        }
    }

    /**
     * Get alpha topup statistics
     */
    public function getStatistics()
    {
        try {
            $totalPlans = AlphaTopup::count();
            $minAmount = AlphaTopup::min('sellingPrice');
            $maxAmount = AlphaTopup::max('sellingPrice');

            // Transaction statistics for the last 30 days
            $thirtyDaysAgo = now()->subDays(30);
            $recentTransactions = Transaction::where('sType', 'alphatopup')
                ->where('sDate', '>=', $thirtyDaysAgo)
                ->get();

            $totalRevenue = $recentTransactions->where('sStatus', 'success')->sum('sAmount');
            $totalTransactions = $recentTransactions->where('sStatus', 'success')->count();
            $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

            // Popular amounts
            $popularAmounts = Transaction::where('sType', 'alphatopup')
                ->where('sStatus', 'success')
                ->where('sDate', '>=', $thirtyDaysAgo)
                ->selectRaw('sAmount, COUNT(*) as transaction_count')
                ->groupBy('sAmount')
                ->orderBy('transaction_count', 'desc')
                ->limit(5)
                ->get();

            return [
                'total_plans' => $totalPlans,
                'min_amount' => $minAmount ?? 0,
                'max_amount' => $maxAmount ?? 0,
                'total_revenue_30d' => $totalRevenue,
                'total_transactions_30d' => $totalTransactions,
                'average_transaction' => $averageTransaction,
                'popular_amounts' => $popularAmounts,
                'success_rate' => $recentTransactions->count() > 0 ? 
                    round(($recentTransactions->where('sStatus', 'success')->count() / $recentTransactions->count()) * 100, 2) : 0
            ];

        } catch (\Exception $e) {
            return [
                'total_plans' => 0,
                'min_amount' => 0,
                'max_amount' => 0,
                'total_revenue_30d' => 0,
                'total_transactions_30d' => 0,
                'average_transaction' => 0,
                'popular_amounts' => [],
                'success_rate' => 0
            ];
        }
    }

    /**
     * Get pricing calculation
     */
    public function calculatePricing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alpha_topup_id' => 'required|exists:alphatopupprice,alphaId',
            'account_type' => 'required|in:user,agent,vendor'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $alphaTopup = AlphaTopup::find($request->alpha_topup_id);
            $userPrice = $alphaTopup->getUserPrice($request->account_type);
            $profit = $alphaTopup->calculateProfit($request->account_type);
            $discountPercentage = $alphaTopup->getDiscountPercentage($request->account_type);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'plan_amount' => $alphaTopup->sellingPrice,
                    'user_price' => $userPrice,
                    'buying_price' => $alphaTopup->buyingPrice,
                    'profit' => $profit,
                    'discount_percentage' => $discountPercentage,
                    'account_type' => $request->account_type,
                    'has_discount' => $alphaTopup->hasDiscount($request->account_type)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to calculate pricing'
            ], 500);
        }
    }
}