<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\UserVerification;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Main analytics dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30_days');
        $dateRange = $this->getDateRange($period);

        $data = [
            'overview' => $this->getOverviewMetrics($dateRange),
            'transaction_trends' => $this->getTransactionTrends($dateRange),
            'user_analytics' => $this->getUserAnalytics($dateRange),
            'service_performance' => $this->getServicePerformance($dateRange),
            'revenue_analysis' => $this->getRevenueAnalysis($dateRange),
            'top_users' => $this->getTopUsers($dateRange),
            'geographical_data' => $this->getGeographicalData($dateRange),
            'api_performance' => $this->getApiPerformance($dateRange)
        ];

        return view('admin.analytics.index', compact('data', 'period'));
    }

    /**
     * Transaction analytics page
     */
    public function transactions(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $serviceType = $request->get('service_type', 'all');

        $analytics = [
            'summary' => $this->getTransactionSummary($dateFrom, $dateTo, $serviceType),
            'trends' => $this->getDetailedTransactionTrends($dateFrom, $dateTo, $serviceType),
            'service_breakdown' => $this->getServiceBreakdown($dateFrom, $dateTo),
            'hourly_patterns' => $this->getHourlyPatterns($dateFrom, $dateTo, $serviceType),
            'success_rates' => $this->getSuccessRates($dateFrom, $dateTo),
            'failed_transactions' => $this->getFailedTransactionAnalysis($dateFrom, $dateTo),
            'commission_analysis' => $this->getCommissionAnalysis($dateFrom, $dateTo)
        ];

        return view('admin.analytics.transactions', compact('analytics', 'dateFrom', 'dateTo', 'serviceType'));
    }

    /**
     * User analytics page
     */
    public function users(Request $request)
    {
        $period = $request->get('period', '30_days');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'registration_trends' => $this->getRegistrationTrends($dateRange),
            'user_activity' => $this->getUserActivity($dateRange),
            'user_segments' => $this->getUserSegments(),
            'retention_analysis' => $this->getRetentionAnalysis($dateRange),
            'wallet_distribution' => $this->getWalletDistribution(),
            'kyc_statistics' => $this->getKycStatistics($dateRange),
            'referral_analytics' => $this->getReferralAnalytics($dateRange),
            'user_lifetime_value' => $this->getUserLifetimeValue()
        ];

        return view('admin.analytics.users', compact('analytics', 'period'));
    }

    /**
     * Service performance analytics
     */
    public function services(Request $request)
    {
        $period = $request->get('period', '30_days');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'service_overview' => $this->getServiceOverview($dateRange),
            'performance_metrics' => $this->getServicePerformanceMetrics($dateRange),
            'reliability_scores' => $this->getServiceReliabilityScores($dateRange),
            'response_times' => $this->getServiceResponseTimes($dateRange),
            'error_analysis' => $this->getServiceErrorAnalysis($dateRange),
            'capacity_utilization' => $this->getCapacityUtilization($dateRange),
            'cost_analysis' => $this->getServiceCostAnalysis($dateRange)
        ];

        return view('admin.analytics.services', compact('analytics', 'period'));
    }

    /**
     * Revenue analytics page
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', '30_days');
        $dateRange = $this->getDateRange($period);

        $analytics = [
            'revenue_overview' => $this->getRevenueOverview($dateRange),
            'revenue_trends' => $this->getRevenueTrends($dateRange),
            'profit_analysis' => $this->getProfitAnalysis($dateRange),
            'commission_breakdown' => $this->getCommissionBreakdown($dateRange),
            'payment_methods' => $this->getPaymentMethodAnalysis($dateRange),
            'refund_analysis' => $this->getRefundAnalysis($dateRange),
            'forecasting' => $this->getRevenueForecasting($dateRange)
        ];

        return view('admin.analytics.revenue', compact('analytics', 'period'));
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'transactions');
        $format = $request->get('format', 'csv');
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        try {
            switch ($type) {
                case 'transactions':
                    return $this->exportTransactions($dateFrom, $dateTo, $format);
                case 'users':
                    return $this->exportUsers($dateFrom, $dateTo, $format);
                case 'revenue':
                    return $this->exportRevenue($dateFrom, $dateTo, $format);
                case 'services':
                    return $this->exportServices($dateFrom, $dateTo, $format);
                default:
                    return back()->with('error', 'Invalid export type');
            }
        } catch (\Exception $e) {
            Log::error('Analytics export error: ' . $e->getMessage());
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Private Helper Methods
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();

        return match($period) {
            '7_days' => [$now->copy()->subDays(7), $now],
            '30_days' => [$now->copy()->subDays(30), $now],
            '90_days' => [$now->copy()->subDays(90), $now],
            '6_months' => [$now->copy()->subMonths(6), $now],
            '1_year' => [$now->copy()->subYear(), $now],
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            default => [$now->copy()->subDays(30), $now]
        };
    }

    /**
     * Get overview metrics
     */
    private function getOverviewMetrics($dateRange)
    {
        $cacheKey = 'analytics_overview_' . $dateRange[0]->format('Y-m-d') . '_' . $dateRange[1]->format('Y-m-d');

        return Cache::remember($cacheKey, 300, function () use ($dateRange) {
            $transactions = Transaction::whereBetween('date', $dateRange);
            $users = User::whereBetween('created_at', $dateRange);

            return [
                'total_transactions' => $transactions->count(),
                'successful_transactions' => $transactions->where('status', 0)->count(),
                'failed_transactions' => $transactions->where('status', 1)->count(),
                'pending_transactions' => $transactions->where('status', 2)->count(),
                'total_revenue' => $transactions->where('status', 0)->sum('amount'),
                'total_commission' => $transactions->where('status', 0)->sum('commission'),
                'new_users' => $users->count(),
                'active_users' => User::whereHas('transactions', function($q) use ($dateRange) {
                    $q->whereBetween('date', $dateRange);
                })->count(),
                'average_transaction_value' => $transactions->where('status', 0)->avg('amount'),
                'success_rate' => $transactions->count() > 0 ?
                    round(($transactions->where('status', 0)->count() / $transactions->count()) * 100, 2) : 0
            ];
        });
    }

    /**
     * Get transaction trends
     */
    private function getTransactionTrends($dateRange)
    {
        $days = [];
        $current = $dateRange[0]->copy();

        while ($current->lte($dateRange[1])) {
            $dayTransactions = Transaction::whereDate('date', $current);

            $days[] = [
                'date' => $current->format('Y-m-d'),
                'total' => $dayTransactions->count(),
                'successful' => $dayTransactions->where('status', 0)->count(),
                'failed' => $dayTransactions->where('status', 1)->count(),
                'revenue' => $dayTransactions->where('status', 0)->sum('amount')
            ];

            $current->addDay();
        }

        return $days;
    }

    /**
     * Get user analytics
     */
    private function getUserAnalytics($dateRange)
    {
        return [
            'new_registrations' => User::whereBetween('created_at', $dateRange)->count(),
            'active_users' => User::whereHas('transactions', function($q) use ($dateRange) {
                $q->whereBetween('date', $dateRange);
            })->count(),
            'verified_users' => User::where('sVerified', 1)->count(),
            'kyc_completed' => \Illuminate\Support\Facades\DB::table('kyc_verification')
                                             ->where('verification_status', 'verified')
                                             ->whereBetween('verified_at', $dateRange)->count()
        ];
    }

    /**
     * Get service performance
     */
    private function getServicePerformance($dateRange)
    {
        return Transaction::whereBetween('date', $dateRange)
                         ->where('status', 0)
                         ->select('servicename',
                                 DB::raw('COUNT(*) as count'),
                                 DB::raw('SUM(amount) as revenue'),
                                 DB::raw('AVG(amount) as avg_amount'))
                         ->groupBy('servicename')
                         ->orderBy('revenue', 'desc')
                         ->get();
    }

    /**
     * Get revenue analysis
     */
    private function getRevenueAnalysis($dateRange)
    {
        $currentRevenue = Transaction::whereBetween('date', $dateRange)
                                   ->where('status', 0)
                                   ->sum('amount');

        $previousPeriod = [
            $dateRange[0]->copy()->subDays($dateRange[1]->diffInDays($dateRange[0])),
            $dateRange[0]->copy()->subDay()
        ];

        $previousRevenue = Transaction::whereBetween('date', $previousPeriod)
                                    ->where('status', 0)
                                    ->sum('amount');

        $growth = $previousRevenue > 0 ?
                 round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 2) : 0;

        return [
            'current_revenue' => $currentRevenue,
            'previous_revenue' => $previousRevenue,
            'growth_percentage' => $growth,
            'daily_average' => round($currentRevenue / max(1, $dateRange[1]->diffInDays($dateRange[0])), 2)
        ];
    }

    /**
     * Get top users by transaction volume
     */
    private function getTopUsers($dateRange)
    {
        // Use a subquery approach that works with SQLite
        // The transactions.sId references users.id
        return User::select('users.*')
                   ->selectSub(function($query) use ($dateRange) {
                       $query->selectRaw('COUNT(*)')
                             ->from('transactions')
                             ->whereColumn('transactions.sId', 'users.id')
                             ->whereBetween('date', $dateRange)
                             ->where('status', 0);
                   }, 'transactions_count')
                   ->selectSub(function($query) use ($dateRange) {
                       $query->selectRaw('COALESCE(SUM(amount), 0)')
                             ->from('transactions')
                             ->whereColumn('transactions.sId', 'users.id')
                             ->whereBetween('date', $dateRange)
                             ->where('status', 0);
                   }, 'transactions_sum_amount')
                   ->whereExists(function($query) use ($dateRange) {
                       $query->select(\Illuminate\Support\Facades\DB::raw(1))
                             ->from('transactions')
                             ->whereColumn('transactions.sId', 'users.id')
                             ->whereBetween('date', $dateRange)
                             ->where('status', 0);
                   })
                   ->orderBy('transactions_sum_amount', 'desc')
                   ->limit(10)
                   ->get();
    }

    /**
     * Get geographical data (placeholder)
     */
    private function getGeographicalData($dateRange)
    {
        // This would analyze user locations if we had that data
        return [
            'top_states' => [],
            'top_cities' => [],
            'international_users' => 0
        ];
    }

    /**
     * Get API performance metrics
     */
    private function getApiPerformance($dateRange)
    {
        try {
            // Check if the api_logs table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('api_logs')) {
                return [
                    'total_requests' => 0,
                    'successful_requests' => 0,
                    'failed_requests' => 0,
                    'average_response_time' => 0
                ];
            }

            $logs = \Illuminate\Support\Facades\DB::table('api_logs')
                        ->whereBetween('created_at', $dateRange);

            $totalRequests = $logs->count();

            return [
                'total_requests' => $totalRequests,
                'successful_requests' => $logs->where('status_code', '<', 400)->count(),
                'failed_requests' => $logs->where('status_code', '>=', 400)->count(),
                'average_response_time' => $logs->avg('response_time') ?: 0
            ];
        } catch (\Exception $e) {
            // Fallback to default values if any database error occurs
            return [
                'total_requests' => 0,
                'successful_requests' => 0,
                'failed_requests' => 0,
                'average_response_time' => 0
            ];
        }
    }

    /**
     * Get transaction summary
     */
    private function getTransactionSummary($dateFrom, $dateTo, $serviceType)
    {
        $query = Transaction::whereBetween('date', [$dateFrom, $dateTo]);

        if ($serviceType !== 'all') {
            $query->where('servicename', $serviceType);
        }

        return [
            'total_transactions' => $query->count(),
            'successful_transactions' => $query->where('status', 0)->count(),
            'failed_transactions' => $query->where('status', 1)->count(),
            'total_volume' => $query->where('status', 0)->sum('amount'),
            'average_value' => $query->where('status', 0)->avg('amount') ?: 0,
            'total_commission' => $query->where('status', 0)->sum('commission') ?: 0
        ];
    }

    /**
     * Get detailed transaction trends
     */
    private function getDetailedTransactionTrends($dateFrom, $dateTo, $serviceType)
    {
        $query = Transaction::selectRaw('
            DATE(date) as date,
            COUNT(*) as total,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('date')
        ->orderBy('date');

        if ($serviceType !== 'all') {
            $query->where('sType', $serviceType);
        }

        return $query->get();
    }

    /**
     * Get service breakdown
     */
    private function getServiceBreakdown($dateFrom, $dateTo)
    {
        return Transaction::selectRaw('
            sType as service,
            COUNT(*) as transactions,
            SUM(CASE WHEN sStatus = "Completed" THEN sAmount ELSE 0 END) as revenue,
            AVG(CASE WHEN sStatus = "Completed" THEN sAmount ELSE NULL END) as avg_amount,
            (SUM(CASE WHEN sStatus = "Completed" THEN 1 ELSE 0 END) / COUNT(*)) * 100 as success_rate
        ')
        ->whereBetween('sDate', [$dateFrom, $dateTo])
        ->groupBy('sType')
        ->orderBy('revenue', 'desc')
        ->get();
    }

    /**
     * Get hourly patterns
     */
    private function getHourlyPatterns($dateFrom, $dateTo, $serviceType)
    {
        $query = Transaction::selectRaw('
            HOUR(sDate) as hour,
            COUNT(*) as transactions,
            SUM(CASE WHEN sStatus = "Completed" THEN sAmount ELSE 0 END) as revenue
        ')
        ->whereBetween('sDate', [$dateFrom, $dateTo])
        ->groupBy('hour')
        ->orderBy('hour');

        if ($serviceType !== 'all') {
            $query->where('sType', $serviceType);
        }

        return $query->get();
    }

    /**
     * Get success rates by service
     */
    private function getSuccessRates($dateFrom, $dateTo)
    {
        return Transaction::selectRaw('
            sType as service,
            COUNT(*) as total,
            SUM(CASE WHEN sStatus = "Completed" THEN 1 ELSE 0 END) as successful,
            (SUM(CASE WHEN sStatus = "Completed" THEN 1 ELSE 0 END) / COUNT(*)) * 100 as success_rate
        ')
        ->whereBetween('sDate', [$dateFrom, $dateTo])
        ->groupBy('sType')
        ->orderBy('success_rate', 'desc')
        ->get();
    }

    /**
     * Export transactions data
     */
    private function exportTransactions($dateFrom, $dateTo, $format)
    {
        $transactions = Transaction::with('user')
                                 ->whereBetween('sDate', [$dateFrom, $dateTo])
                                 ->orderBy('sDate', 'desc')
                                 ->get();

        if ($format === 'csv') {
            $csvData = "ID,Reference,User,Service,Amount,Status,Date,Description\n";

            foreach ($transactions as $transaction) {
                $userName = $transaction->user ?
                          $transaction->user->sFirstname . ' ' . $transaction->user->sLastname :
                          'Unknown User';

                $csvData .= implode(',', [
                    $transaction->tId,
                    $transaction->sReference,
                    '"' . $userName . '"',
                    $transaction->sType,
                    $transaction->sAmount,
                    $transaction->sStatus,
                    $transaction->sDate,
                    '"' . str_replace('"', '""', $transaction->sDesc) . '"'
                ]) . "\n";
            }

            $filename = 'transactions_' . $dateFrom . '_to_' . $dateTo . '.csv';

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        }

        // Add other format support (Excel, PDF) here
        return back()->with('error', 'Format not supported yet');
    }

    /**
     * Additional analytics methods would continue here...
     * Including: getUserSegments(), getRetentionAnalysis(), etc.
     */
}
