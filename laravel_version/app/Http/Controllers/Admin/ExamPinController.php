<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ExamPin;
use App\Models\Transaction;

class ExamPinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display exam pin management page
     */
    public function index()
    {
        $examPins = ExamPin::orderBy('ePlan')->get();
        $statistics = $this->getStatistics();

        return view('admin.exam-pins.index', [
            'examPins' => $examPins,
            'statistics' => $statistics
        ]);
    }

    /**
     * Store new exam pin provider
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan' => 'required|string|max:100|unique:exampin,ePlan',
            'price' => 'required|numeric|min:0',
            'buying_price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $examPin = ExamPin::create([
                'ePlan' => $request->plan,
                'ePrice' => $request->price,
                'eBuyingPrice' => $request->buying_price,
                'eStatus' => $request->status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Exam pin provider added successfully',
                'data' => $examPin
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add exam pin provider'
            ], 500);
        }
    }

    /**
     * Update exam pin provider
     */
    public function update(Request $request, ExamPin $examPin)
    {
        $validator = Validator::make($request->all(), [
            'plan' => 'required|string|max:100|unique:exampin,ePlan,' . $examPin->eId . ',eId',
            'price' => 'required|numeric|min:0',
            'buying_price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $examPin->update([
                'ePlan' => $request->plan,
                'ePrice' => $request->price,
                'eBuyingPrice' => $request->buying_price,
                'eStatus' => $request->status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Exam pin provider updated successfully',
                'data' => $examPin
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update exam pin provider'
            ], 500);
        }
    }

    /**
     * Delete exam pin provider
     */
    public function destroy(ExamPin $examPin)
    {
        try {
            // Check if there are any transactions for this exam pin
            $transactionCount = Transaction::where('sDesc', 'LIKE', '%' . $examPin->ePlan . '%')
                ->where('servicename', 'exampin')
                ->count();

            if ($transactionCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete exam pin provider with existing transactions'
                ], 400);
            }

            $examPin->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Exam pin provider deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete exam pin provider'
            ], 500);
        }
    }

    /**
     * Toggle exam pin provider status
     */
    public function toggleStatus(ExamPin $examPin)
    {
        try {
            $examPin->eStatus = $examPin->eStatus == 1 ? 0 : 1;
            $examPin->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Exam pin provider status updated successfully',
                'data' => [
                    'new_status' => $examPin->eStatus,
                    'status_text' => $examPin->eStatus == 1 ? 'Active' : 'Inactive'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update status'
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
            'exam_pin_ids' => 'required|array|min:1',
            'exam_pin_ids.*' => 'exists:exampin,eId'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $examPins = ExamPin::whereIn('eId', $request->exam_pin_ids)->get();
            $updateCount = 0;

            foreach ($examPins as $examPin) {
                if ($request->update_type === 'percentage') {
                    $newPrice = $examPin->ePrice * (1 + ($request->adjustment_value / 100));
                } else {
                    $newPrice = $examPin->ePrice + $request->adjustment_value;
                }

                // Ensure price doesn't go below buying price
                if ($newPrice >= $examPin->eBuyingPrice) {
                    $examPin->ePrice = round($newPrice, 2);
                    $examPin->save();
                    $updateCount++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Successfully updated prices for {$updateCount} exam pin providers"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update prices'
            ], 500);
        }
    }

    /**
     * Export exam pins to CSV
     */
    public function export()
    {
        try {
            $examPins = ExamPin::orderBy('ePlan')->get();

            $filename = 'exam_pins_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($examPins) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Exam Provider',
                    'Selling Price',
                    'Buying Price',
                    'Profit Margin',
                    'Status',
                    'Category'
                ]);

                // Add data rows
                foreach ($examPins as $examPin) {
                    $profitMargin = $examPin->ePrice - $examPin->eBuyingPrice;
                    $profitPercentage = $examPin->eBuyingPrice > 0 ?
                        round(($profitMargin / $examPin->eBuyingPrice) * 100, 2) : 0;

                    fputcsv($file, [
                        $examPin->eId,
                        $examPin->ePlan,
                        number_format($examPin->ePrice, 2),
                        number_format($examPin->eBuyingPrice, 2),
                        number_format($profitMargin, 2) . ' (' . $profitPercentage . '%)',
                        $examPin->eStatus == 1 ? 'Active' : 'Inactive',
                        $examPin->description ?? 'Educational Examination'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export exam pins'
            ], 500);
        }
    }

    /**
     * Get exam pin statistics
     */
    public function getStatistics()
    {
        try {
            $totalProviders = ExamPin::count();
            $activeProviders = ExamPin::where('eStatus', 1)->count();
            $inactiveProviders = ExamPin::where('eStatus', 0)->count();

            // Transaction statistics for the last 30 days
            $thirtyDaysAgo = now()->subDays(30);
            $recentTransactions = Transaction::where('servicename', 'exampin')
                ->where('date', '>=', $thirtyDaysAgo)
                ->get();

            $totalRevenue = $recentTransactions->where('status', 'success')->sum('amount');
            $totalTransactions = $recentTransactions->where('status', 'success')->count();
            $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

            // Popular exam types
            $popularExams = Transaction::where('servicename', 'exampin')
                ->where('status', 'success')
                ->where('date', '>=', $thirtyDaysAgo)
                ->selectRaw('sDesc, COUNT(*) as transaction_count, SUM(amount) as total_amount')
                ->groupBy('sDesc')
                ->orderBy('transaction_count', 'desc')
                ->limit(5)
                ->get();

            return [
                'total_providers' => $totalProviders,
                'active_providers' => $activeProviders,
                'inactive_providers' => $inactiveProviders,
                'total_revenue_30d' => $totalRevenue,
                'total_transactions_30d' => $totalTransactions,
                'average_transaction' => $averageTransaction,
                'popular_exams' => $popularExams,
                'success_rate' => $recentTransactions->count() > 0 ?
                    round(($recentTransactions->where('status', 'success')->count() / $recentTransactions->count()) * 100, 2) : 0
            ];

        } catch (\Exception $e) {
            return [
                'total_providers' => 0,
                'active_providers' => 0,
                'inactive_providers' => 0,
                'total_revenue_30d' => 0,
                'total_transactions_30d' => 0,
                'average_transaction' => 0,
                'popular_exams' => [],
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
            'exam_pin_id' => 'required|exists:exampin,eId',
            'quantity' => 'required|integer|min:1|max:50',
            'account_type' => 'required|in:user,agent,vendor'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $examPin = ExamPin::find($request->exam_pin_id);
            $unitPrice = $examPin->getUserPrice($request->account_type);
            $totalAmount = $unitPrice * $request->quantity;
            $profit = $examPin->calculateProfit($request->account_type) * $request->quantity;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'exam_provider' => $examPin->ePlan,
                    'unit_price' => $unitPrice,
                    'quantity' => $request->quantity,
                    'total_amount' => $totalAmount,
                    'buying_price' => $examPin->eBuyingPrice,
                    'profit_per_unit' => $examPin->calculateProfit($request->account_type),
                    'total_profit' => $profit,
                    'account_type' => $request->account_type
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
