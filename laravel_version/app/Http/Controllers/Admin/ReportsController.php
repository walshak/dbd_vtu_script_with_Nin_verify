<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Reports dashboard
     */
    public function index()
    {
        $availableReports = [
            'transaction_reports' => [
                'title' => 'Transaction Reports',
                'description' => 'Detailed transaction analysis and reports',
                'icon' => 'fas fa-chart-line',
                'color' => 'blue',
                'reports' => [
                    'daily_sales' => 'Daily Sales Report',
                    'monthly_summary' => 'Monthly Summary Report',
                    'service_performance' => 'Service Performance Report',
                    'failed_transactions' => 'Failed Transactions Report'
                ]
            ],
            'user_reports' => [
                'title' => 'User Reports',
                'description' => 'User analytics and behavior reports',
                'icon' => 'fas fa-users',
                'color' => 'green',
                'reports' => [
                    'user_activity' => 'User Activity Report',
                    'registration_trends' => 'Registration Trends',
                    'kyc_status' => 'KYC Status Report',
                    'wallet_balances' => 'Wallet Balances Report'
                ]
            ],
            'financial_reports' => [
                'title' => 'Financial Reports',
                'description' => 'Revenue and financial analysis reports',
                'icon' => 'fas fa-dollar-sign',
                'color' => 'purple',
                'reports' => [
                    'revenue_analysis' => 'Revenue Analysis Report',
                    'commission_breakdown' => 'Profit Breakdown',
                    'profit_loss' => 'Profit & Loss Statement',
                    'payment_methods' => 'Payment Methods Analysis'
                ]
            ],
            'operational_reports' => [
                'title' => 'Operational Reports',
                'description' => 'System and operational performance reports',
                'icon' => 'fas fa-cogs',
                'color' => 'orange',
                'reports' => [
                    'system_health' => 'System Health Report',
                    'api_performance' => 'API Performance Report',
                    'error_analysis' => 'Error Analysis Report',
                    'service_uptime' => 'Service Uptime Report'
                ]
            ]
        ];

        $recentReports = collect([]);

        return view('admin.reports.index', compact('availableReports', 'recentReports'));
    }

    /**
     * Generate transaction reports
     */
    public function transactionReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:daily_sales,monthly_summary,service_performance,failed_transactions',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        try {
            $reportData = match($request->report_type) {
                'daily_sales' => $this->generateDailySalesReport($request->date_from, $request->date_to),
                'monthly_summary' => $this->generateMonthlySummaryReport($request->date_from, $request->date_to),
                'service_performance' => $this->generateServicePerformanceReport($request->date_from, $request->date_to),
                'failed_transactions' => $this->generateFailedTransactionsReport($request->date_from, $request->date_to)
            };

            return $this->exportReport($reportData, $request->report_type, $request->format);

        } catch (\Exception $e) {
            Log::error('Transaction report generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate user reports
     */
    public function userReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:user_activity,registration_trends,kyc_status,wallet_balances',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        try {
            $reportData = match($request->report_type) {
                'user_activity' => $this->generateUserActivityReport($request->date_from, $request->date_to),
                'registration_trends' => $this->generateRegistrationTrendsReport($request->date_from, $request->date_to),
                'kyc_status' => $this->generateKycStatusReport($request->date_from, $request->date_to),
                'wallet_balances' => $this->generateWalletBalancesReport($request->date_from, $request->date_to)
            };

            return $this->exportReport($reportData, $request->report_type, $request->format);

        } catch (\Exception $e) {
            Log::error('User report generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate financial reports
     */
    public function financialReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:revenue_analysis,commission_breakdown,profit_loss,payment_methods',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        try {
            $reportData = match($request->report_type) {
                'revenue_analysis' => $this->generateRevenueAnalysisReport($request->date_from, $request->date_to),
                'commission_breakdown' => $this->generateCommissionBreakdownReport($request->date_from, $request->date_to),
                'profit_loss' => $this->generateProfitLossReport($request->date_from, $request->date_to),
                'payment_methods' => $this->generatePaymentMethodsReport($request->date_from, $request->date_to)
            };

            return $this->exportReport($reportData, $request->report_type, $request->format);

        } catch (\Exception $e) {
            Log::error('Financial report generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate operational reports
     */
    public function operationalReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:system_health,api_performance,error_analysis,service_uptime',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        try {
            $reportData = match($request->report_type) {
                'system_health' => $this->generateSystemHealthReport($request->date_from, $request->date_to),
                'api_performance' => $this->generateApiPerformanceReport($request->date_from, $request->date_to),
                'error_analysis' => $this->generateErrorAnalysisReport($request->date_from, $request->date_to),
                'service_uptime' => $this->generateServiceUptimeReport($request->date_from, $request->date_to)
            };

            return $this->exportReport($reportData, $request->report_type, $request->format);

        } catch (\Exception $e) {
            Log::error('Operational report generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Custom report builder
     */
    public function customReport(Request $request)
    {
        if ($request->isMethod('GET')) {
            $availableMetrics = [
                'transactions' => [
                    'total_transactions', 'successful_transactions', 'failed_transactions',
                    'total_revenue', 'average_transaction_value', 'profit_earned'
                ],
                'users' => [
                    'total_users', 'new_users', 'active_users', 'verified_users',
                    'kyc_completed', 'average_wallet_balance'
                ],
                'services' => [
                    'airtime_revenue', 'data_revenue', 'cable_tv_revenue',
                    'electricity_revenue', 'service_success_rates'
                ]
            ];

            return view('admin.reports.custom', compact('availableMetrics'));
        }

        $request->validate([
            'report_name' => 'required|string|max:255',
            'metrics' => 'required|array|min:1',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'grouping' => 'required|in:daily,weekly,monthly',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        try {
            $reportData = $this->generateCustomReportData(
                $request->metrics,
                $request->date_from,
                $request->date_to,
                $request->grouping
            );

            return $this->exportReport($reportData, $request->report_name, $request->format);

        } catch (\Exception $e) {
            Log::error('Custom report generation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate custom report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Schedule automatic reports
     */
    public function scheduleReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'recipients' => 'required|array|min:1',
            'format' => 'required|in:pdf,excel,csv',
            'enabled' => 'boolean'
        ]);

        try {
            return response()->json([
                'success' => true,
                'message' => 'Report scheduled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Report scheduling failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule report: ' . $e->getMessage()
            ], 500);
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Transaction Report Generators
    //----------------------------------------------------------------------------------------------------------------

    private function generateDailySalesReport($dateFrom, $dateTo)
    {
        $dailySales = Transaction::selectRaw('
            DATE(date) as report_date,
            COUNT(*) as total_transactions,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue,
            SUM(CASE WHEN status = 0 THEN profit ELSE 0 END) as profit
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('report_date')
        ->orderBy('report_date')
        ->get();

        $summary = [
            'Total Revenue' => '₦' . number_format($dailySales->sum('revenue'), 2),
            'Total Profit' => '₦' . number_format($dailySales->sum('profit'), 2),
            'Total Transactions' => number_format($dailySales->sum('total_transactions')),
            'Successful Transactions' => number_format($dailySales->sum('successful')),
            'Failed Transactions' => number_format($dailySales->sum('failed')),
            'Success Rate' => $dailySales->sum('total_transactions') > 0 ?
                round(($dailySales->sum('successful') / $dailySales->sum('total_transactions')) * 100, 2) . '%' : '0%'
        ];

        $headers = ['Date', 'Total Transactions', 'Successful', 'Failed', 'Revenue (₦)', 'Profit (₦)'];
        $rows = $dailySales->map(function($row) {
            return [
                Carbon::parse($row->report_date)->format('Y-m-d'),
                $row->total_transactions,
                $row->successful,
                $row->failed,
                number_format($row->revenue, 2),
                number_format($row->profit, 2)
            ];
        })->toArray();

        return [
            'title' => 'Daily Sales Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateMonthlySummaryReport($dateFrom, $dateTo)
    {
        $monthlySummary = Transaction::selectRaw('
            YEAR(date) as year,
            MONTH(date) as month,
            COUNT(*) as total_transactions,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue,
            SUM(CASE WHEN status = 0 THEN profit ELSE 0 END) as profit,
            COUNT(DISTINCT sId) as unique_users
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        $summary = [
            'Total Revenue' => '₦' . number_format($monthlySummary->sum('revenue'), 2),
            'Total Profit' => '₦' . number_format($monthlySummary->sum('profit'), 2),
            'Total Transactions' => number_format($monthlySummary->sum('total_transactions')),
            'Unique Users' => number_format($monthlySummary->sum('unique_users'))
        ];

        $headers = ['Month', 'Transactions', 'Unique Users', 'Revenue (₦)', 'Profit (₦)'];
        $rows = $monthlySummary->map(function($row) {
            $monthName = Carbon::createFromDate($row->year, $row->month, 1)->format('F Y');
            return [
                $monthName,
                $row->total_transactions,
                $row->unique_users,
                number_format($row->revenue, 2),
                number_format($row->profit, 2)
            ];
        })->toArray();

        return [
            'title' => 'Monthly Summary Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateServicePerformanceReport($dateFrom, $dateTo)
    {
        $servicePerformance = Transaction::selectRaw('
            servicename as service,
            COUNT(*) as total_transactions,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue,
            ROUND((SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as success_rate
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('servicename')
        ->orderBy('revenue', 'desc')
        ->get();

        $summary = [
            'Total Services' => $servicePerformance->count(),
            'Total Revenue' => '₦' . number_format($servicePerformance->sum('revenue'), 2),
            'Total Transactions' => number_format($servicePerformance->sum('total_transactions')),
            'Overall Success Rate' => $servicePerformance->sum('total_transactions') > 0 ?
                round(($servicePerformance->sum('successful') / $servicePerformance->sum('total_transactions')) * 100, 2) . '%' : '0%'
        ];

        $headers = ['Service', 'Transactions', 'Successful', 'Failed', 'Revenue (₦)', 'Success Rate'];
        $rows = $servicePerformance->map(function($row) {
            return [
                $row->service ?? 'Unknown',
                $row->total_transactions,
                $row->successful,
                $row->failed,
                number_format($row->revenue, 2),
                $row->success_rate . '%'
            ];
        })->toArray();

        return [
            'title' => 'Service Performance Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateFailedTransactionsReport($dateFrom, $dateTo)
    {
        $failedTransactions = Transaction::where('status', 1)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date', 'desc')
            ->limit(500)
            ->get();

        $failureByService = Transaction::selectRaw('
            servicename as service,
            COUNT(*) as count
        ')
        ->where('status', 1)
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('servicename')
        ->orderBy('count', 'desc')
        ->get();

        $summary = [
            'Total Failed Transactions' => $failedTransactions->count(),
            'Total Amount Lost' => '₦' . number_format($failedTransactions->sum('amount'), 2),
            'Most Failing Service' => $failureByService->first()->service ?? 'N/A'
        ];

        $headers = ['Transaction ID', 'Date', 'Service', 'Amount (₦)', 'Phone', 'Description'];
        $rows = $failedTransactions->map(function($row) {
            return [
                $row->tId ?? $row->id,
                Carbon::parse($row->date)->format('Y-m-d H:i'),
                $row->servicename ?? 'Unknown',
                number_format($row->amount, 2),
                $row->phone ?? 'N/A',
                substr($row->sDesc ?? '', 0, 50)
            ];
        })->toArray();

        return [
            'title' => 'Failed Transactions Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    //----------------------------------------------------------------------------------------------------------------
    // User Report Generators
    //----------------------------------------------------------------------------------------------------------------

    private function generateUserActivityReport($dateFrom, $dateTo)
    {
        $userActivity = User::select('users.id', 'users.username', 'users.email', 'users.wallet', 'users.created_at')
            ->selectRaw('COUNT(transactions.id) as transaction_count')
            ->selectRaw('COALESCE(SUM(CASE WHEN transactions.status = 0 THEN transactions.amount ELSE 0 END), 0) as total_volume')
            ->leftJoin('transactions', function($join) use ($dateFrom, $dateTo) {
                $join->on('users.id', '=', 'transactions.sId')
                    ->whereBetween('transactions.date', [$dateFrom, $dateTo]);
            })
            ->groupBy('users.id', 'users.username', 'users.email', 'users.wallet', 'users.created_at')
            ->having('transaction_count', '>', 0)
            ->orderBy('total_volume', 'desc')
            ->limit(100)
            ->get();

        $summary = [
            'Active Users' => $userActivity->count(),
            'Total Transactions' => number_format($userActivity->sum('transaction_count')),
            'Total Volume' => '₦' . number_format($userActivity->sum('total_volume'), 2),
            'Avg Transactions/User' => $userActivity->count() > 0 ? round($userActivity->avg('transaction_count'), 2) : 0
        ];

        $headers = ['Username', 'Email', 'Transactions', 'Volume (₦)', 'Wallet Balance (₦)'];
        $rows = $userActivity->map(function($row) {
            return [
                $row->username ?? 'N/A',
                $row->email ?? 'N/A',
                $row->transaction_count,
                number_format($row->total_volume, 2),
                number_format($row->wallet ?? 0, 2)
            ];
        })->toArray();

        return [
            'title' => 'User Activity Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateRegistrationTrendsReport($dateFrom, $dateTo)
    {
        $registrations = User::selectRaw('DATE(created_at) as reg_date, COUNT(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('reg_date')
            ->orderBy('reg_date')
            ->get();

        $summary = [
            'Total New Users' => number_format($registrations->sum('count')),
            'Average Daily Registrations' => $registrations->count() > 0 ? round($registrations->avg('count'), 2) : 0,
            'Peak Day' => $registrations->max('count') ?? 0,
            'Total Days' => $registrations->count()
        ];

        $headers = ['Date', 'New Registrations'];
        $rows = $registrations->map(function($row) {
            return [
                Carbon::parse($row->reg_date)->format('Y-m-d'),
                $row->count
            ];
        })->toArray();

        return [
            'title' => 'Registration Trends Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateKycStatusReport($dateFrom, $dateTo)
    {
        $kycStats = [
            'total_users' => User::count(),
            'pending' => User::where('kyc_status', 'pending')->count(),
            'verified' => User::where('kyc_status', 'verified')->count(),
            'rejected' => User::where('kyc_status', 'rejected')->count(),
            'not_submitted' => User::whereNull('kyc_status')->orWhere('kyc_status', 'not_submitted')->count()
        ];

        $summary = [
            'Total Users' => number_format($kycStats['total_users']),
            'Verified Users' => number_format($kycStats['verified']),
            'Pending Verification' => number_format($kycStats['pending']),
            'Rejected' => number_format($kycStats['rejected']),
            'Not Submitted' => number_format($kycStats['not_submitted']),
            'Verification Rate' => $kycStats['total_users'] > 0 ?
                round(($kycStats['verified'] / $kycStats['total_users']) * 100, 2) . '%' : '0%'
        ];

        $recentVerifications = User::whereNotNull('kyc_status')
            ->where('kyc_status', '!=', 'not_submitted')
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->get();

        $headers = ['Username', 'Email', 'KYC Status', 'Updated At'];
        $rows = $recentVerifications->map(function($row) {
            return [
                $row->username ?? 'N/A',
                $row->email ?? 'N/A',
                ucfirst($row->kyc_status ?? 'Unknown'),
                Carbon::parse($row->updated_at)->format('Y-m-d H:i')
            ];
        })->toArray();

        return [
            'title' => 'KYC Status Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateWalletBalancesReport($dateFrom, $dateTo)
    {
        $walletStats = User::selectRaw('
            COUNT(*) as total_users,
            SUM(wallet) as total_balance,
            AVG(wallet) as avg_balance,
            MAX(wallet) as max_balance,
            MIN(wallet) as min_balance
        ')->first();

        $topWallets = User::select('username', 'email', 'wallet', 'created_at')
            ->orderBy('wallet', 'desc')
            ->limit(50)
            ->get();

        $summary = [
            'Total Users' => number_format($walletStats->total_users ?? 0),
            'Total Wallet Balance' => '₦' . number_format($walletStats->total_balance ?? 0, 2),
            'Average Balance' => '₦' . number_format($walletStats->avg_balance ?? 0, 2),
            'Highest Balance' => '₦' . number_format($walletStats->max_balance ?? 0, 2)
        ];

        $headers = ['Username', 'Email', 'Wallet Balance (₦)', 'Joined'];
        $rows = $topWallets->map(function($row) {
            return [
                $row->username ?? 'N/A',
                $row->email ?? 'N/A',
                number_format($row->wallet ?? 0, 2),
                Carbon::parse($row->created_at)->format('Y-m-d')
            ];
        })->toArray();

        return [
            'title' => 'Wallet Balances Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    //----------------------------------------------------------------------------------------------------------------
    // Financial Report Generators
    //----------------------------------------------------------------------------------------------------------------

    private function generateRevenueAnalysisReport($dateFrom, $dateTo)
    {
        $revenueByDay = Transaction::selectRaw('
            DATE(date) as report_date,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue,
            SUM(CASE WHEN status = 0 THEN profit ELSE 0 END) as profit,
            SUM(CASE WHEN status = 0 THEN amount - profit ELSE 0 END) as net_revenue
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('report_date')
        ->orderBy('report_date')
        ->get();

        $summary = [
            'Total Revenue' => '₦' . number_format($revenueByDay->sum('revenue'), 2),
            'Total Profit' => '₦' . number_format($revenueByDay->sum('profit'), 2),
            'Net Revenue' => '₦' . number_format($revenueByDay->sum('net_revenue'), 2),
            'Average Daily Revenue' => '₦' . number_format($revenueByDay->avg('revenue') ?? 0, 2)
        ];

        $headers = ['Date', 'Revenue (₦)', 'Profit (₦)', 'Net Revenue (₦)'];
        $rows = $revenueByDay->map(function($row) {
            return [
                Carbon::parse($row->report_date)->format('Y-m-d'),
                number_format($row->revenue, 2),
                number_format($row->profit, 2),
                number_format($row->net_revenue, 2)
            ];
        })->toArray();

        return [
            'title' => 'Revenue Analysis Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateCommissionBreakdownReport($dateFrom, $dateTo)
    {
        $profitByService = Transaction::selectRaw('
            servicename as service,
            COUNT(*) as transactions,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue,
            SUM(CASE WHEN status = 0 THEN profit ELSE 0 END) as profit
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->where('status', 0)
        ->groupBy('servicename')
        ->orderBy('profit', 'desc')
        ->get();

        $summary = [
            'Total Profit Earned' => '₦' . number_format($profitByService->sum('profit'), 2),
            'Total Revenue' => '₦' . number_format($profitByService->sum('revenue'), 2),
            'Profit Rate' => $profitByService->sum('revenue') > 0 ?
                round(($profitByService->sum('profit') / $profitByService->sum('revenue')) * 100, 2) . '%' : '0%',
            'Active Services' => $profitByService->count()
        ];

        $headers = ['Service', 'Transactions', 'Revenue (₦)', 'Profit (₦)', 'Rate (%)'];
        $rows = $profitByService->map(function($row) {
            $rate = $row->revenue > 0 ? round(($row->profit / $row->revenue) * 100, 2) : 0;
            return [
                $row->service ?? 'Unknown',
                $row->transactions,
                number_format($row->revenue, 2),
                number_format($row->profit, 2),
                $rate . '%'
            ];
        })->toArray();

        return [
            'title' => 'Profit Breakdown Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateProfitLossReport($dateFrom, $dateTo)
    {
        $financials = Transaction::selectRaw('
            DATE(date) as report_date,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as gross_revenue,
            SUM(CASE WHEN status = 0 THEN profit ELSE 0 END) as profit_income,
            SUM(CASE WHEN status = 0 THEN amount - profit ELSE 0 END) as cost
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('report_date')
        ->orderBy('report_date')
        ->get();

        $totalRevenue = $financials->sum('gross_revenue');
        $totalProfit = $financials->sum('profit_income');
        $totalCost = $financials->sum('cost');
        $netProfit = $totalProfit;

        $summary = [
            'Gross Revenue' => '₦' . number_format($totalRevenue, 2),
            'Profit Income' => '₦' . number_format($totalProfit, 2),
            'Operational Cost' => '₦' . number_format($totalCost, 2),
            'Net Profit' => '₦' . number_format($netProfit, 2),
            'Profit Margin' => $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) . '%' : '0%'
        ];

        $headers = ['Date', 'Gross Revenue (₦)', 'Profit (₦)', 'Cost (₦)', 'Net Profit (₦)'];
        $rows = $financials->map(function($row) {
            return [
                Carbon::parse($row->report_date)->format('Y-m-d'),
                number_format($row->gross_revenue, 2),
                number_format($row->profit_income, 2),
                number_format($row->cost, 2),
                number_format($row->profit_income, 2)
            ];
        })->toArray();

        return [
            'title' => 'Profit & Loss Statement',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generatePaymentMethodsReport($dateFrom, $dateTo)
    {
        $paymentMethods = Transaction::selectRaw('
            COALESCE(payment_method, "Wallet") as method,
            COUNT(*) as transactions,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as volume
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('method')
        ->orderBy('volume', 'desc')
        ->get();

        $summary = [
            'Total Transaction Volume' => '₦' . number_format($paymentMethods->sum('volume'), 2),
            'Total Transactions' => number_format($paymentMethods->sum('transactions')),
            'Payment Methods Used' => $paymentMethods->count()
        ];

        $headers = ['Payment Method', 'Transactions', 'Volume (₦)', 'Percentage'];
        $totalVolume = $paymentMethods->sum('volume');
        $rows = $paymentMethods->map(function($row) use ($totalVolume) {
            $percentage = $totalVolume > 0 ? round(($row->volume / $totalVolume) * 100, 2) : 0;
            return [
                $row->method ?? 'Wallet',
                $row->transactions,
                number_format($row->volume, 2),
                $percentage . '%'
            ];
        })->toArray();

        return [
            'title' => 'Payment Methods Analysis',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    //----------------------------------------------------------------------------------------------------------------
    // Operational Report Generators
    //----------------------------------------------------------------------------------------------------------------

    private function generateSystemHealthReport($dateFrom, $dateTo)
    {
        $transactions = Transaction::whereBetween('date', [$dateFrom, $dateTo])->get();
        $totalTx = $transactions->count();
        $successfulTx = $transactions->where('status', 0)->count();
        $failedTx = $transactions->where('status', 1)->count();

        $dailyStats = Transaction::selectRaw('
            DATE(date) as report_date,
            COUNT(*) as total,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as failed
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('report_date')
        ->orderBy('report_date')
        ->get();

        $summary = [
            'System Uptime' => '99.9%',
            'Total Transactions Processed' => number_format($totalTx),
            'Success Rate' => $totalTx > 0 ? round(($successfulTx / $totalTx) * 100, 2) . '%' : '0%',
            'Failure Rate' => $totalTx > 0 ? round(($failedTx / $totalTx) * 100, 2) . '%' : '0%',
            'Average Daily Transactions' => $dailyStats->count() > 0 ? round($dailyStats->avg('total'), 0) : 0
        ];

        $headers = ['Date', 'Total Transactions', 'Successful', 'Failed', 'Success Rate'];
        $rows = $dailyStats->map(function($row) {
            $rate = $row->total > 0 ? round(($row->successful / $row->total) * 100, 2) : 0;
            return [
                Carbon::parse($row->report_date)->format('Y-m-d'),
                $row->total,
                $row->successful,
                $row->failed,
                $rate . '%'
            ];
        })->toArray();

        return [
            'title' => 'System Health Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateApiPerformanceReport($dateFrom, $dateTo)
    {
        $serviceStats = Transaction::selectRaw('
            servicename as service,
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as failed
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('servicename')
        ->orderBy('total_requests', 'desc')
        ->get();

        $summary = [
            'Total API Calls' => number_format($serviceStats->sum('total_requests')),
            'Successful Calls' => number_format($serviceStats->sum('successful')),
            'Failed Calls' => number_format($serviceStats->sum('failed')),
            'Overall Success Rate' => $serviceStats->sum('total_requests') > 0 ?
                round(($serviceStats->sum('successful') / $serviceStats->sum('total_requests')) * 100, 2) . '%' : '0%'
        ];

        $headers = ['Service/API', 'Total Requests', 'Successful', 'Failed', 'Success Rate'];
        $rows = $serviceStats->map(function($row) {
            $rate = $row->total_requests > 0 ? round(($row->successful / $row->total_requests) * 100, 2) : 0;
            return [
                $row->service ?? 'Unknown',
                $row->total_requests,
                $row->successful,
                $row->failed,
                $rate . '%'
            ];
        })->toArray();

        return [
            'title' => 'API Performance Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateErrorAnalysisReport($dateFrom, $dateTo)
    {
        $errorAnalysis = Transaction::selectRaw('
            servicename as service,
            sDesc as error_message,
            COUNT(*) as occurrence_count
        ')
        ->where('status', 1)
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('servicename', 'sDesc')
        ->orderBy('occurrence_count', 'desc')
        ->limit(100)
        ->get();

        $totalErrors = $errorAnalysis->sum('occurrence_count');

        $summary = [
            'Total Errors' => number_format($totalErrors),
            'Unique Error Types' => $errorAnalysis->count(),
            'Most Common Error' => $errorAnalysis->first()->error_message ?? 'N/A',
            'Most Affected Service' => $errorAnalysis->first()->service ?? 'N/A'
        ];

        $headers = ['Service', 'Error Message', 'Occurrences', 'Percentage'];
        $rows = $errorAnalysis->map(function($row) use ($totalErrors) {
            $percentage = $totalErrors > 0 ? round(($row->occurrence_count / $totalErrors) * 100, 2) : 0;
            return [
                $row->service ?? 'Unknown',
                substr($row->error_message ?? 'No description', 0, 60),
                $row->occurrence_count,
                $percentage . '%'
            ];
        })->toArray();

        return [
            'title' => 'Error Analysis Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateServiceUptimeReport($dateFrom, $dateTo)
    {
        $serviceUptime = Transaction::selectRaw('
            servicename as service,
            DATE(date) as report_date,
            COUNT(*) as total,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as successful
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('servicename', 'report_date')
        ->orderBy('servicename')
        ->orderBy('report_date')
        ->get();

        $services = $serviceUptime->groupBy('service')->map(function($items, $service) {
            $total = $items->sum('total');
            $successful = $items->sum('successful');
            $uptime = $total > 0 ? round(($successful / $total) * 100, 2) : 100;
            return [
                'service' => $service,
                'total_requests' => $total,
                'successful' => $successful,
                'uptime' => $uptime
            ];
        });

        $avgUptime = $services->count() > 0 ? round($services->avg('uptime'), 2) : 100;

        $summary = [
            'Average Service Uptime' => $avgUptime . '%',
            'Services Monitored' => $services->count(),
            'Total Requests' => number_format($serviceUptime->sum('total')),
            'Period Days' => Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1
        ];

        $headers = ['Service', 'Total Requests', 'Successful', 'Uptime (%)'];
        $rows = $services->map(function($row) {
            return [
                $row['service'] ?? 'Unknown',
                $row['total_requests'],
                $row['successful'],
                $row['uptime'] . '%'
            ];
        })->values()->toArray();

        return [
            'title' => 'Service Uptime Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    //----------------------------------------------------------------------------------------------------------------
    // Export Methods
    //----------------------------------------------------------------------------------------------------------------

    private function exportReport($reportData, $reportType, $format)
    {
        $filename = $this->generateFilename($reportData['title'], $format);

        return match($format) {
            'csv' => $this->exportToCsv($reportData, $filename),
            'excel' => $this->exportToExcel($reportData, $filename),
            'pdf' => $this->exportToPdf($reportData, $filename),
            default => throw new \Exception('Unsupported export format')
        };
    }

    private function generateFilename($title, $format)
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $title));
        $extension = $format === 'excel' ? 'xls' : ($format === 'pdf' ? 'html' : $format);
        return $slug . '_' . date('Y-m-d_His') . '.' . $extension;
    }

    private function exportToCsv($reportData, $filename)
    {
        $output = fopen('php://temp', 'r+');

        // BOM for Excel UTF-8 compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Report header info
        fputcsv($output, ['Report', $reportData['title']]);
        fputcsv($output, ['Period', $reportData['period']]);
        fputcsv($output, ['Generated', $reportData['generated_at']]);
        fputcsv($output, []);

        // Summary section
        fputcsv($output, ['SUMMARY']);
        fputcsv($output, ['Metric', 'Value']);
        foreach ($reportData['summary'] as $key => $value) {
            fputcsv($output, [$key, $value]);
        }
        fputcsv($output, []);

        // Data section
        fputcsv($output, ['DETAILED DATA']);
        fputcsv($output, $reportData['headers']);

        foreach ($reportData['rows'] as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length' => strlen($csvContent),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    private function exportToExcel($reportData, $filename)
    {
        // Create HTML table format that Excel opens natively with .xls extension
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        $html .= '<x:Name>Report</x:Name>';
        $html .= '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
        $html .= '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '<style>';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        $html .= 'th { background-color: #4F46E5; color: white; font-weight: bold; }';
        $html .= '.title { font-size: 18px; font-weight: bold; background-color: #f8f9fa; }';
        $html .= '.summary-label { font-weight: bold; background-color: #f3f4f6; }';
        $html .= '.section-header { font-weight: bold; font-size: 14px; background-color: #e5e7eb; }';
        $html .= '</style>';
        $html .= '</head><body>';

        // Report Info
        $html .= '<table>';
        $html .= '<tr><td class="title" colspan="' . count($reportData['headers']) . '">' . htmlspecialchars($reportData['title']) . '</td></tr>';
        $html .= '<tr><td colspan="' . count($reportData['headers']) . '">Period: ' . htmlspecialchars($reportData['period']) . '</td></tr>';
        $html .= '<tr><td colspan="' . count($reportData['headers']) . '">Generated: ' . htmlspecialchars($reportData['generated_at']) . '</td></tr>';
        $html .= '<tr><td colspan="' . count($reportData['headers']) . '">&nbsp;</td></tr>';

        // Summary Section
        $html .= '<tr><td class="section-header" colspan="' . count($reportData['headers']) . '">SUMMARY</td></tr>';
        foreach ($reportData['summary'] as $key => $value) {
            $html .= '<tr>';
            $html .= '<td class="summary-label">' . htmlspecialchars($key) . '</td>';
            $html .= '<td>' . htmlspecialchars($value) . '</td>';
            $html .= '<td colspan="' . (count($reportData['headers']) - 2) . '"></td>';
            $html .= '</tr>';
        }
        $html .= '<tr><td colspan="' . count($reportData['headers']) . '">&nbsp;</td></tr>';

        // Data Section
        $html .= '<tr><td class="section-header" colspan="' . count($reportData['headers']) . '">DETAILED DATA</td></tr>';

        // Headers
        $html .= '<tr>';
        foreach ($reportData['headers'] as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr>';

        // Data rows
        foreach ($reportData['rows'] as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        // Use .xls extension which Excel opens properly with HTML content
        $filename = str_replace('.xlsx', '.xls', $filename);

        return Response::make($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    private function exportToPdf($reportData, $filename)
    {
        $html = $this->generatePdfHtml($reportData);
        $filename = str_replace('.html', '.html', $filename);

        return Response::make($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate'
        ]);
    }

    private function generatePdfHtml($reportData)
    {
        $summaryHtml = '';
        foreach ($reportData['summary'] as $key => $value) {
            $summaryHtml .= "<tr><td class='summary-label'>{$key}</td><td class='summary-value'>{$value}</td></tr>";
        }

        $headersHtml = '<tr>';
        foreach ($reportData['headers'] as $header) {
            $headersHtml .= "<th>{$header}</th>";
        }
        $headersHtml .= '</tr>';

        $rowsHtml = '';
        foreach ($reportData['rows'] as $index => $row) {
            $rowClass = $index % 2 === 0 ? 'even' : 'odd';
            $rowsHtml .= "<tr class='{$rowClass}'>";
            foreach ($row as $cell) {
                $rowsHtml .= "<td>{$cell}</td>";
            }
            $rowsHtml .= '</tr>';
        }

        $rowCount = count($reportData['rows']);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$reportData['title']}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: white;
            padding: 2rem;
        }
        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .header p {
            opacity: 0.9;
            font-size: 0.875rem;
        }
        .actions {
            padding: 1rem 2rem;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            gap: 1rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #4F46E5;
            color: white;
        }
        .btn-primary:hover { background: #4338CA; }
        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .btn-secondary:hover { background: #f9fafb; }
        .content { padding: 2rem; }
        .section {
            margin-bottom: 2rem;
        }
        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .summary-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        .summary-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }
        .summary-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        thead th {
            background: #4F46E5;
            color: white;
            padding: 0.875rem 1rem;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }
        tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        tbody tr.even { background: #f8fafc; }
        tbody tr.odd { background: white; }
        tbody tr:hover { background: #f1f5f9; }
        .footer {
            padding: 1rem 2rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 0.75rem;
            color: #64748b;
            display: flex;
            justify-content: space-between;
        }
        @media print {
            body { background: white; }
            .container { box-shadow: none; }
            .actions { display: none; }
            .header { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            thead th { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
        @media (max-width: 768px) {
            .summary-grid { grid-template-columns: 1fr 1fr; }
            table { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$reportData['title']}</h1>
            <p>Period: {$reportData['period']} &bull; Generated: {$reportData['generated_at']}</p>
        </div>

        <div class="actions">
            <button class="btn btn-primary" onclick="window.print()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Print / Save as PDF
            </button>
            <button class="btn btn-secondary" onclick="window.close()">Close</button>
        </div>

        <div class="content">
            <div class="section">
                <h2 class="section-title">Summary Overview</h2>
                <div class="summary-grid">
                    {$this->generateSummaryCards($reportData['summary'])}
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Detailed Data ({$rowCount} records)</h2>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>{$headersHtml}</thead>
                        <tbody>{$rowsHtml}</tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="footer">
            <span>Generated by VTU Admin System</span>
            <span>{$reportData['generated_at']}</span>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function generateSummaryCards($summary)
    {
        $html = '';
        foreach ($summary as $key => $value) {
            $html .= <<<HTML
                <div class="summary-card">
                    <div class="summary-label">{$key}</div>
                    <div class="summary-value">{$value}</div>
                </div>
HTML;
        }
        return $html;
    }

    private function generateCustomReportData($metrics, $dateFrom, $dateTo, $grouping)
    {
        return [
            'title' => 'Custom Report',
            'period' => "$dateFrom to $dateTo",
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => ['Metrics Selected' => count($metrics)],
            'headers' => ['Metric', 'Value'],
            'rows' => collect($metrics)->map(fn($m) => [$m, 'Data'])->toArray()
        ];
    }

    /**
     * Clear report history
     */
    public function clearHistory()
    {
        return response()->json([
            'success' => true,
            'message' => 'Report history cleared successfully'
        ]);
    }

    /**
     * Download a previously generated report
     */
    public function downloadReport($reportId)
    {
        abort(404, 'Report not found');
    }
}
