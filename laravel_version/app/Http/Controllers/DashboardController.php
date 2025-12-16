<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get transaction summary for the user
        $transactionSummary = Transaction::getUserTransactionSummary($user->id);

        // Get recent transactions
        $recentTransactions = Transaction::where('sId', $user->id)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Get monthly statistics
        $thisMonth = now()->startOfMonth();
        $monthlyTransactions = Transaction::where('sId', $user->id)
            ->where('date', '>=', $thisMonth)
            ->successful()
            ->get();

        $monthlyStats = [
            'count' => $monthlyTransactions->count(),
            'amount' => $monthlyTransactions->sum(function ($t) {
                return floatval($t->amount);
            })
        ];

        // Get service breakdown
        $serviceBreakdown = Transaction::where('sId', $user->id)
            ->successful()
            ->select('servicename', DB::raw('COUNT(*) as count'), DB::raw('SUM(CAST(amount AS DECIMAL(10,2))) as total'))
            ->groupBy('servicename')
            ->orderBy('count', 'desc')
            ->get();

        return view('dashboard', compact(
            'user',
            'transactionSummary',
            'recentTransactions',
            'monthlyStats',
            'serviceBreakdown'
        ));
    }

    /**
     * Show admin dashboard
     */
    public function adminIndex()
    {
        // Admin dashboard analytics
        $totalUsers = User::count();
        $activeUsers = User::active()->count();
        $totalTransactions = Transaction::count();
        $successfulTransactions = Transaction::successful()->count();

        $todayTransactions = Transaction::whereDate('date', today())
            ->successful()
            ->get();

        $todayStats = [
            'count' => $todayTransactions->count(),
            'revenue' => $todayTransactions->sum(function ($t) {
                return floatval($t->amount);
            }),
            'profit' => $todayTransactions->sum('profit')
        ];

        // Monthly analytics
        $monthlyAnalytics = Transaction::getSalesAnalytics(
            now()->startOfMonth(),
            now()->endOfMonth()
        );

        // Recent transactions for admin view
        $recentTransactions = Transaction::with('user')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Service analytics
        $serviceAnalytics = $monthlyAnalytics['service_breakdown'];

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'totalTransactions',
            'successfulTransactions',
            'todayStats',
            'monthlyAnalytics',
            'recentTransactions',
            'serviceAnalytics'
        ));
    }

    /**
     * Get dashboard statistics via AJAX
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', '30'); // days

        $fromDate = now()->subDays($period);

        $transactions = Transaction::where('sId', $user->id)
            ->where('date', '>=', $fromDate)
            ->get();

        $successful = $transactions->where('status', Transaction::STATUS_SUCCESS);
        $failed = $transactions->where('status', Transaction::STATUS_FAILED);

        $stats = [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $successful->count(),
            'failed_transactions' => $failed->count(),
            'total_amount' => $successful->sum(function ($t) {
                return floatval($t->amount);
            }),
            'success_rate' => $transactions->count() > 0
                ? round(($successful->count() / $transactions->count()) * 100, 1)
                : 0,
            'services' => $successful->groupBy('servicename')->map(function ($group, $service) {
                return [
                    'name' => $service,
                    'count' => $group->count(),
                    'amount' => $group->sum(function ($t) {
                        return floatval($t->amount);
                    })
                ];
            })->values()
        ];

        return response()->json($stats);
    }

    /**
     * Get transaction chart data
     */
    public function getTransactionChart(Request $request)
    {
        $user = Auth::user();
        $days = $request->get('days', 7);

        $chartData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayTransactions = Transaction::where('sId', $user->id)
                ->whereDate('date', $date)
                ->successful()
                ->get();

            $chartData[] = [
                'date' => $date->format('M d'),
                'count' => $dayTransactions->count(),
                'amount' => $dayTransactions->sum(function ($t) {
                    return floatval($t->amount);
                })
            ];
        }

        return response()->json($chartData);
    }
}
