<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->middleware('admin');
        $this->walletService = $walletService;
    }

    /**
     * Display list of transactions (compatible with old PHP app)
     */
    public function index(Request $request)
    {
        try {
            $query = Transaction::with('user');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('transref', 'like', "%{$search}%")
                      ->orWhere('servicedesc', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('email', 'like', "%{$search}%")
                                   ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('service')) {
                $query->where('servicename', $request->service);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            if ($request->filled('amount_min')) {
                $query->where('amount', '>=', $request->amount_min);
            }

            if ($request->filled('amount_max')) {
                $query->where('amount', '<=', $request->amount_max);
            }

            // Sort and paginate
            $sortBy = $request->get('sort_by', 'date');
            $sortOrder = $request->get('sort_order', 'desc');

            $transactions = $query->orderBy($sortBy, $sortOrder)
                                  ->paginate(25)
                                  ->withQueryString();

            // Get transaction statistics
            $stats = [
                'total_transactions' => Transaction::count(),
                'successful_transactions' => Transaction::where('status', Transaction::STATUS_SUCCESS)->count(),
                'failed_transactions' => Transaction::where('status', Transaction::STATUS_FAILED)->count(),
                'total_revenue' => Transaction::where('status', Transaction::STATUS_SUCCESS)
                    ->get()
                    ->sum(function($t) { return floatval($t->amount); }),
                'total_profit' => Transaction::where('status', Transaction::STATUS_SUCCESS)->sum('profit'),
                'today_revenue' => Transaction::whereDate('date', today())
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->get()
                    ->sum(function($t) { return floatval($t->amount); }),
                'today_transactions' => Transaction::whereDate('date', today())->count(),
            ];

            return view('admin.transactions.index', compact('transactions', 'stats'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error loading transactions: ' . $e->getMessage());
        }
    }

    /**
     * Show transaction details (compatible with old PHP app)
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::with('user')->findOrFail($id);

            // Get related transactions (same user, same day)
            $relatedTransactions = Transaction::where('sId', $transaction->sId)
                ->whereDate('date', Carbon::parse($transaction->date)->toDateString())
                ->where('tId', '!=', $transaction->tId)
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();

            return view('admin.transactions.show', compact('transaction', 'relatedTransactions'));

        } catch (\Exception $e) {
            return back()->with('error', 'Transaction not found');
        }
    }

    /**
     * Display general sales analysis (compatible with old PHP app)
     */
    public function generalSalesAnalysis(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $serviceType = $request->get('service_type', 'all');

        $analytics = Transaction::getSalesAnalytics($dateFrom, $dateTo, $serviceType);

        // Get recent transactions
        $recentTransactions = Transaction::with('user')
            ->when($dateFrom, function($query) use ($dateFrom) {
                return $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                return $query->whereDate('date', '<=', $dateTo);
            })
            ->when($serviceType !== 'all', function($query) use ($serviceType) {
                return $query->where('servicename', $serviceType);
            })
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get();

        return view('admin.transactions.general-analysis', compact(
            'analytics',
            'recentTransactions',
            'dateFrom',
            'dateTo',
            'serviceType'
        ));
    }

    /**
     * Display airtime sales analysis
     */
    public function airtimeSalesAnalysis(Request $request)
    {
        return $this->serviceSalesAnalysis($request, Transaction::SERVICE_AIRTIME, 'airtime-analysis');
    }

    /**
     * Display data sales analysis
     */
    public function dataSalesAnalysis(Request $request)
    {
        return $this->serviceSalesAnalysis($request, Transaction::SERVICE_DATA, 'data-analysis');
    }

    /**
     * Generic service sales analysis
     */
    protected function serviceSalesAnalysis(Request $request, $serviceName, $viewName)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $analytics = Transaction::getSalesAnalytics($dateFrom, $dateTo, $serviceName);

        // Get service-specific transactions
        $transactions = Transaction::with('user')
            ->where('servicename', $serviceName)
            ->when($dateFrom, function($query) use ($dateFrom) {
                return $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                return $query->whereDate('date', '<=', $dateTo);
            })
            ->orderBy('date', 'desc')
            ->paginate(50);

        // Calculate additional service-specific metrics
        $serviceTransactions = $transactions->where('status', Transaction::STATUS_SUCCESS);
        $metrics = [
            'total_volume' => $serviceTransactions->sum(function($t) {
                return floatval($t->amount);
            }),
            'total_profit' => $serviceTransactions->sum('profit'),
            'success_rate' => $transactions->count() > 0 ?
                ($serviceTransactions->count() / $transactions->count()) * 100 : 0,
            'average_transaction' => $serviceTransactions->count() > 0 ?
                $serviceTransactions->avg(function($t) {
                    return floatval($t->amount);
                }) : 0
        ];

        return view('admin.transactions.' . $viewName, compact(
            'analytics',
            'transactions',
            'metrics',
            'dateFrom',
            'dateTo',
            'serviceName'
        ));
    }

    /**
     * Credit user account (compatible with old PHP app)
     */
    public function creditUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255'
        ]);

        try {
            $user = User::find($request->user_id);
            $adminEmail = Auth::user()->email ?? 'admin@system.com';

            $result = $this->walletService->creditUser(
                $request->user_id,
                $request->amount,
                $request->description,
                $adminEmail
            );

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => "Successfully credited {$user->name}'s account with ₦" . number_format($request->amount, 2),
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Credit User Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to credit user account'
            ]);
        }
    }

    /**
     * Update transaction status (compatible with old PHP app)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1',
            'admin_note' => 'nullable|string|max:500'
        ]);

        try {
            $transaction = Transaction::findOrFail($id);
            $oldStatus = $transaction->status;

            // Prevent changing successful transactions to failed
            if ($oldStatus === Transaction::STATUS_SUCCESS && $request->status == Transaction::STATUS_FAILED) {
                // Handle refund logic
                $this->processTransactionRefund($transaction);
            }

            $transaction->status = $request->status;
            $transaction->save();

            // Log the status change
            Log::info('Transaction status updated by admin', [
                'transaction_id' => $transaction->tId,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'admin_note' => $request->admin_note,
                'admin_user' => Auth::user()->id
            ]);

            return back()->with('success', 'Transaction status updated successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating transaction: ' . $e->getMessage());
        }
    }

    /**
     * Reverse/refund a transaction
     */
    public function reverseTransaction(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,tId',
            'reason' => 'required|string|max:255'
        ]);

        try {
            $result = $this->walletService->reverseTransaction(
                $request->transaction_id,
                $request->reason
            );

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction reversed successfully',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Reverse Transaction Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reverse transaction'
            ]);
        }
    }

    /**
     * Get transaction details
     */
    public function getTransactionDetails($transactionId)
    {
        try {
            $transaction = Transaction::with('user')->find($transactionId);

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $transaction->tId,
                    'reference' => $transaction->transref,
                    'user' => $transaction->user ? [
                        'id' => $transaction->user->id,
                        'name' => $transaction->user->name,
                        'email' => $transaction->user->email,
                        'phone' => $transaction->user->phone
                    ] : null,
                    'service' => $transaction->servicename,
                    'description' => $transaction->servicedesc,
                    'amount' => $transaction->amount,
                    'formatted_amount' => $transaction->formatted_amount,
                    'status' => $transaction->status,
                    'status_text' => $transaction->status_text,
                    'old_balance' => $transaction->oldbal,
                    'new_balance' => $transaction->newbal,
                    'profit' => $transaction->profit,
                    'date' => $transaction->date,
                    'formatted_date' => $transaction->formatted_date
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get Transaction Details Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get transaction details'
            ]);
        }
    }

    /**
     * Export transactions to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = Transaction::with('user');

            // Apply same filters as index
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('service')) {
                $query->where('servicename', $request->service);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            $transactions = $query->orderBy('date', 'desc')->get();

            $filename = 'transactions_export_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($transactions) {
                $file = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($file, [
                    'Transaction ID', 'Reference', 'User Name', 'User Email', 'Service',
                    'Description', 'Amount', 'Status', 'Old Balance', 'New Balance',
                    'Profit', 'Date'
                ]);

                // Add transaction data
                foreach ($transactions as $transaction) {
                    fputcsv($file, [
                        $transaction->tId,
                        $transaction->transref,
                        $transaction->user ? $transaction->user->name : 'N/A',
                        $transaction->user ? $transaction->user->email : 'N/A',
                        $transaction->servicename,
                        $transaction->servicedesc,
                        $transaction->amount,
                        $transaction->status_text,
                        $transaction->oldbal,
                        $transaction->newbal,
                        $transaction->profit,
                        $transaction->formatted_date
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting transactions: ' . $e->getMessage());
        }
    }

    /**
     * Transaction analytics (compatible with old PHP app)
     */
    public function analytics(Request $request)
    {
        try {
            $period = $request->get('period', '30'); // days
            $startDate = Carbon::now()->subDays($period);

            // Daily transaction trends
            $dailyTrends = collect();
            for ($i = $period - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $dayTransactions = Transaction::whereDate('date', $date);

                $dailyTrends->push([
                    'date' => $date,
                    'count' => $dayTransactions->count(),
                    'revenue' => $dayTransactions->where('status', Transaction::STATUS_SUCCESS)->sum(function($t) {
                        return floatval($t->amount);
                    }),
                    'successful' => $dayTransactions->where('status', Transaction::STATUS_SUCCESS)->count(),
                    'failed' => $dayTransactions->where('status', Transaction::STATUS_FAILED)->count(),
                    'profit' => $dayTransactions->where('status', Transaction::STATUS_SUCCESS)->sum('profit')
                ]);
            }

            // Service type breakdown
            $serviceBreakdown = Transaction::select('servicename',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(CASE WHEN status = 0 THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as revenue'),
                    DB::raw('SUM(CASE WHEN status = 0 THEN profit ELSE 0 END) as profit')
                )
                ->where('date', '>=', $startDate)
                ->groupBy('servicename')
                ->orderBy('revenue', 'desc')
                ->get();

            // Top users by transaction volume
            $topUsers = Transaction::select('sId',
                    DB::raw('COUNT(*) as transaction_count'),
                    DB::raw('SUM(CASE WHEN status = 0 THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as total_spent')
                )
                ->with('user:id,name,email')
                ->where('date', '>=', $startDate)
                ->where('status', Transaction::STATUS_SUCCESS)
                ->groupBy('sId')
                ->orderBy('total_spent', 'desc')
                ->limit(10)
                ->get();

            // Hourly patterns
            $hourlyPatterns = Transaction::select(
                    DB::raw('HOUR(date) as hour'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(CASE WHEN status = 0 THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as revenue')
                )
                ->where('date', '>=', $startDate)
                ->groupBy(DB::raw('HOUR(date)'))
                ->orderBy('hour')
                ->get();

            // Success rates by service type
            $successRates = Transaction::select('servicename',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as successful'),
                    DB::raw('ROUND((SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as success_rate')
                )
                ->where('date', '>=', $startDate)
                ->groupBy('servicename')
                ->orderBy('success_rate', 'desc')
                ->get();

            return view('admin.transactions.analytics', compact(
                'dailyTrends',
                'serviceBreakdown',
                'topUsers',
                'hourlyPatterns',
                'successRates',
                'period'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Error loading analytics: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard stats for admin (compatible with old PHP app)
     */
    public function getDashboardStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        $stats = [
            'today' => [
                'transactions' => Transaction::whereDate('date', $today)->count(),
                'revenue' => Transaction::whereDate('date', $today)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->sum(function($t) { return floatval($t->amount); }),
                'profit' => Transaction::whereDate('date', $today)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->sum('profit')
            ],
            'this_month' => [
                'transactions' => Transaction::where('date', '>=', $thisMonth)->count(),
                'revenue' => Transaction::where('date', '>=', $thisMonth)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->sum(function($t) { return floatval($t->amount); }),
                'profit' => Transaction::where('date', '>=', $thisMonth)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->sum('profit')
            ],
            'this_year' => [
                'transactions' => Transaction::where('date', '>=', $thisYear)->count(),
                'revenue' => Transaction::where('date', '>=', $thisYear)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->sum(function($t) { return floatval($t->amount); }),
                'profit' => Transaction::where('date', '>=', $thisYear)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->sum('profit')
            ],
            'services' => []
        ];

        // Service breakdown
        $services = [
            Transaction::SERVICE_AIRTIME,
            Transaction::SERVICE_DATA,
            Transaction::SERVICE_CABLE_TV,
            Transaction::SERVICE_ELECTRICITY
        ];

        foreach ($services as $service) {
            $stats['services'][strtolower(str_replace(' ', '_', $service))] = [
                'name' => $service,
                'today' => Transaction::whereDate('date', $today)
                    ->where('servicename', $service)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->count(),
                'this_month' => Transaction::where('date', '>=', $thisMonth)
                    ->where('servicename', $service)
                    ->where('status', Transaction::STATUS_SUCCESS)
                    ->count()
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * Process transaction refund
     */
    private function processTransactionRefund($transaction)
    {
        try {
            $result = $this->walletService->reverseTransaction(
                $transaction->tId,
                'Transaction refunded by admin'
            );

            if ($result['status'] !== 'success') {
                Log::error('Failed to process refund: ' . $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('Process refund error: ' . $e->getMessage());
        }
    }
}
