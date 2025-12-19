<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Get recent transactions for authenticated user
     */
    public function getRecent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => 'sometimes|string|in:airtime,data,cable,electricity,exam,recharge-pin',
            'limit' => 'sometimes|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = Auth::user();
            $limit = $request->get('limit', 10);
            $service = $request->get('service');

            $query = Transaction::where('sId', $user->id);

            if ($service) {
                // Case-insensitive service name matching
                $query->whereRaw('LOWER(servicename) = ?', [strtolower($service)]);
            }

            $transactions = $query->orderBy('date', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($transaction) {
                    // Parse service description to extract phone and network
                    $serviceDesc = $transaction->servicedesc ?? '';
                    $phone = '';
                    $network = '';

                    // Extract phone number (11 digits)
                    if (preg_match('/(\d{11})/', $serviceDesc, $matches)) {
                        $phone = $matches[1];
                    }

                    // Extract network from service description
                    if (preg_match('/(MTN|GLO|AIRTEL|9MOBILE)/i', $serviceDesc, $matches)) {
                        $network = strtoupper($matches[1]);
                    }

                    // Determine status text
                    $statusText = $transaction->status == 1 ? 'Completed' :
                                 ($transaction->status == 0 ? 'Pending' : 'Failed');

                    return [
                        'id' => $transaction->tId,
                        'reference' => $transaction->transref,
                        'service_type' => $transaction->servicename,
                        'service_name' => $this->formatServiceName($transaction->servicename),
                        'amount' => $transaction->amount,
                        'status' => $statusText,
                        'status_code' => $transaction->status,
                        'phone' => $phone,
                        'network' => $network ?: strtoupper($transaction->servicename),
                        'description' => $serviceDesc,
                        'created_at' => date('M d, Y H:i', strtotime($transaction->date)),
                        'created_at_human' => Carbon::parse($transaction->date)->diffForHumans()
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $transactions,
                'meta' => [
                    'total' => $transactions->count(),
                    'service' => $service,
                    'user_id' => $user->sId
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch recent transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download transaction receipt
     */
    public function downloadReceipt($reference)
    {
        try {
            $user = Auth::user();

            $transaction = Transaction::where('transref', $reference)
                ->where('sId', $user->id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Parse service description to extract details
            $serviceDesc = $transaction->servicedesc ?? '';
            $phone = '';
            $network = '';
            $serviceType = $transaction->servicename ?? 'Transaction';

            // Extract phone number (11 digits)
            if (preg_match('/(\d{11})/', $serviceDesc, $matches)) {
                $phone = $matches[1];
            }

            // Extract network (MTN, GLO, AIRTEL, 9MOBILE, etc.)
            if (preg_match('/(MTN|GLO|AIRTEL|9MOBILE|DSTV|GOTV|STARTIMES|EKEDC|IKEDC|AEDC)/i', $serviceDesc, $matches)) {
                $network = strtoupper($matches[1]);
            }

            // Format transaction data for receipt view
            $formattedTransaction = (object) [
                'reference' => $transaction->transref,
                'service_type' => $serviceType,
                'servicedesc' => $serviceDesc,
                'phone' => $phone,
                'network' => $network,
                'amount' => $transaction->amount,
                'status' => $transaction->status == 1 ? 'Completed' : ($transaction->status == 0 ? 'Pending' : 'Failed'),
                'created_at' => $transaction->date,
                'oldbal' => $transaction->oldbal,
                'newbal' => $transaction->newbal,
            ];

            // Generate HTML receipt
            $receiptData = [
                'transaction' => $formattedTransaction,
                'user' => $user,
                'generated_at' => now(),
                'company' => [
                    'name' => config('app.name', 'VTU Platform'),
                    'email' => config('mail.from.address', 'support@vtu.com'),
                    'phone' => config('app.support_phone', '+234 XXX XXX XXXX')
                ]
            ];

            return view('receipts.transaction', $receiptData);
        } catch (\Exception $e) {
            \Log::error('Receipt generation failed', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction history with pagination
     */
    public function history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => 'sometimes|string|in:airtime,data,cable,electricity,exam,recharge-pin',
            'status' => 'sometimes|string|in:Pending,Completed,Failed,Cancelled',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'per_page' => 'sometimes|integer|min:10|max:100',
            'search' => 'sometimes|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 20);

            $query = Transaction::where('user_id', $user->id);

            // Apply filters
            if ($request->filled('service')) {
                $query->where('service_type', $request->service);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('recipient', 'like', "%{$search}%")
                        ->orWhere('network', 'like', "%{$search}%");
                });
            }

            $transactions = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                    'has_more' => $transactions->hasMorePages()
                ],
                'filters' => $request->only(['service', 'status', 'date_from', 'date_to', 'search'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch transaction history'
            ], 500);
        }
    }

    /**
     * Get transaction statistics for dashboard
     */
    public function getStats(Request $request)
    {
        try {
            $user = Auth::user();
            $period = $request->get('period', '30'); // days

            $startDate = now()->subDays($period);

            $stats = [
                'total_transactions' => Transaction::where('user_id', $user->id)
                    ->where('created_at', '>=', $startDate)
                    ->count(),

                'successful_transactions' => Transaction::where('user_id', $user->id)
                    ->where('status', 'Completed')
                    ->where('created_at', '>=', $startDate)
                    ->count(),

                'total_amount_spent' => Transaction::where('user_id', $user->id)
                    ->where('status', 'Completed')
                    ->where('created_at', '>=', $startDate)
                    ->sum('amount'),

                'pending_transactions' => Transaction::where('user_id', $user->id)
                    ->where('status', 'Pending')
                    ->count(),

                'services_breakdown' => Transaction::where('user_id', $user->id)
                    ->where('created_at', '>=', $startDate)
                    ->select('service_type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total_amount'))
                    ->groupBy('service_type')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item->service_type => [
                            'count' => $item->count,
                            'total_amount' => $item->total_amount,
                            'service_name' => $this->formatServiceName($item->service_type)
                        ]];
                    })
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats,
                'meta' => [
                    'period_days' => $period,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => now()->format('Y-m-d')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch transaction statistics'
            ], 500);
        }
    }

    /**
     * Format service name for display
     */
    private function formatServiceName($serviceType)
    {
        $serviceNames = [
            'airtime' => 'Airtime Purchase',
            'data' => 'Data Bundle',
            'cable' => 'Cable TV',
            'electricity' => 'Electricity Bill',
            'exam' => 'Exam Pin',
            'recharge-pin' => 'Recharge Pin'
        ];

        return $serviceNames[$serviceType] ?? ucfirst($serviceType);
    }

    /**
     * Format transaction details for display
     */
    private function formatTransactionDetails($transaction)
    {
        $details = [];

        switch ($transaction->service_type) {
            case 'airtime':
                $details = [
                    'Phone Number' => $transaction->phone,
                    'Network' => $transaction->network,
                    'Amount' => '₦' . number_format($transaction->amount, 2)
                ];
                break;

            case 'data':
                $details = [
                    'Phone Number' => $transaction->phone,
                    'Network' => $transaction->network,
                    'Data Plan' => $transaction->plan_name ?? 'N/A',
                    'Amount' => '₦' . number_format($transaction->amount, 2)
                ];
                break;

            case 'cable':
                $details = [
                    'IUC/SmartCard' => $transaction->iuc_number ?? $transaction->recipient,
                    'Provider' => $transaction->service_id,
                    'Package' => $transaction->plan_name ?? 'N/A',
                    'Amount' => '₦' . number_format($transaction->amount, 2)
                ];
                break;

            case 'electricity':
                $details = [
                    'Meter Number' => $transaction->meter_number ?? $transaction->recipient,
                    'DISCO' => $transaction->service_id,
                    'Meter Type' => $transaction->meter_type ?? 'N/A',
                    'Amount' => '₦' . number_format($transaction->amount, 2)
                ];
                break;

            default:
                $details = [
                    'Service' => $this->formatServiceName($transaction->service_type),
                    'Amount' => '₦' . number_format($transaction->amount, 2)
                ];
        }

        return $details;
    }
}
