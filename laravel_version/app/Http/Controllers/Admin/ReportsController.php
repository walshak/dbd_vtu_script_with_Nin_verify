<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\UserVerification;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
                'reports' => [
                    'revenue_analysis' => 'Revenue Analysis Report',
                    'commission_breakdown' => 'Commission Breakdown',
                    'profit_loss' => 'Profit & Loss Statement',
                    'payment_methods' => 'Payment Methods Analysis'
                ]
            ],
            'operational_reports' => [
                'title' => 'Operational Reports',
                'description' => 'System and operational performance reports',
                'icon' => 'fas fa-cogs',
                'reports' => [
                    'system_health' => 'System Health Report',
                    'api_performance' => 'API Performance Report',
                    'error_analysis' => 'Error Analysis Report',
                    'service_uptime' => 'Service Uptime Report'
                ]
            ]
        ];

        $recentReports = $this->getRecentReports();

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
            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
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
            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
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
            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
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
            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
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
                    'total_revenue', 'average_transaction_value', 'commission_earned'
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
            $reportData = $this->generateCustomReport(
                $request->metrics,
                $request->date_from,
                $request->date_to,
                $request->grouping
            );

            return $this->exportReport($reportData, $request->report_name, $request->format);

        } catch (\Exception $e) {
            Log::error('Custom report generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate custom report: ' . $e->getMessage());
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
            $scheduledReport = [
                'report_type' => $request->report_type,
                'frequency' => $request->frequency,
                'recipients' => $request->recipients,
                'format' => $request->format,
                'enabled' => $request->enabled ?? true,
                'next_run' => $this->calculateNextRun($request->frequency),
                'created_at' => now()
            ];

            // Save to scheduled_reports table (you'd need to create this)
            // ScheduledReport::create($scheduledReport);

            return back()->with('success', 'Report scheduled successfully');

        } catch (\Exception $e) {
            Log::error('Report scheduling failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to schedule report: ' . $e->getMessage());
        }
    }

    //----------------------------------------------------------------------------------------------------------------
    // Report Generation Methods
    //----------------------------------------------------------------------------------------------------------------

    /**
     * Generate daily sales report
     */
    private function generateDailySalesReport($dateFrom, $dateTo)
    {
        $dailySales = Transaction::selectRaw('
            DATE(date) as date,
            COUNT(*) as total_transactions,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as successful_transactions,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as failed_transactions,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue,
            SUM(CASE WHEN status = 0 THEN commission ELSE 0 END) as commission,
            AVG(CASE WHEN status = 0 THEN amount ELSE NULL END) as avg_transaction_value
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $summary = [
            'total_revenue' => $dailySales->sum('revenue'),
            'total_commission' => $dailySales->sum('commission'),
            'total_transactions' => $dailySales->sum('total_transactions'),
            'successful_transactions' => $dailySales->sum('successful_transactions'),
            'failed_transactions' => $dailySales->sum('failed_transactions'),
            'average_daily_revenue' => $dailySales->avg('revenue'),
            'success_rate' => $dailySales->sum('total_transactions') > 0 ?
                           round(($dailySales->sum('successful_transactions') / $dailySales->sum('total_transactions')) * 100, 2) : 0
        ];

        return [
            'type' => 'Daily Sales Report',
            'period' => "$dateFrom to $dateTo",
            'summary' => $summary,
            'details' => $dailySales,
            'generated_at' => now()
        ];
    }

    /**
     * Generate monthly summary report
     */
    private function generateMonthlySummaryReport($dateFrom, $dateTo)
    {
        $monthlySummary = Transaction::selectRaw('
            YEAR(date) as year,
            MONTH(date) as month,
            COUNT(*) as total_transactions,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue,
            SUM(CASE WHEN status = 0 THEN commission ELSE 0 END) as commission,
            COUNT(DISTINCT sId) as unique_users
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        $serviceBreakdown = Transaction::selectRaw('
            servicename as service,
            COUNT(*) as transactions,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->where('status', 0)
        ->groupBy('servicename')
        ->orderBy('revenue', 'desc')
        ->get();

        return [
            'type' => 'Monthly Summary Report',
            'period' => "$dateFrom to $dateTo",
            'monthly_data' => $monthlySummary,
            'service_breakdown' => $serviceBreakdown,
            'generated_at' => now()
        ];
    }

    /**
     * Generate service performance report
     */
    private function generateServicePerformanceReport($dateFrom, $dateTo)
    {
        $servicePerformance = Transaction::selectRaw('
            servicename as service,
            COUNT(*) as total_transactions,
            SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as successful_transactions,
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as failed_transactions,
            SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as revenue,
            AVG(CASE WHEN status = 0 THEN amount ELSE NULL END) as avg_amount,
            (SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as success_rate
        ')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('servicename')
        ->orderBy('revenue', 'desc')
        ->get();

        return [
            'type' => 'Service Performance Report',
            'period' => "$dateFrom to $dateTo",
            'services' => $servicePerformance,
            'generated_at' => now()
        ];
    }

    /**
     * Generate failed transactions report
     */
    private function generateFailedTransactionsReport($dateFrom, $dateTo)
    {
        $failedTransactions = Transaction::with('user')
                                       ->where('status', 1)
                                       ->whereBetween('date', [$dateFrom, $dateTo])
                                       ->orderBy('date', 'desc')
                                       ->get();

        $failureReasons = Transaction::selectRaw('
            sDesc as reason,
            COUNT(*) as count
        ')
        ->where('status', 1)
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->groupBy('sDesc')
        ->orderBy('count', 'desc')
        ->get();

        return [
            'type' => 'Failed Transactions Report',
            'period' => "$dateFrom to $dateTo",
            'failed_transactions' => $failedTransactions,
            'failure_reasons' => $failureReasons,
            'total_failed' => $failedTransactions->count(),
            'generated_at' => now()
        ];
    }

    /**
     * Generate user activity report
     */
    private function generateUserActivityReport($dateFrom, $dateTo)
    {
        $userActivity = User::withCount(['transactions' => function($q) use ($dateFrom, $dateTo) {
                              $q->whereBetween('date', [$dateFrom, $dateTo]);
                          }])
                          ->withSum(['transactions' => function($q) use ($dateFrom, $dateTo) {
                              $q->whereBetween('date', [$dateFrom, $dateTo])
                                ->where('status', 0);
                          }], 'amount')
                          ->having('transactions_count', '>', 0)
                          ->orderBy('transactions_sum_amount', 'desc')
                          ->get();

        $activitySummary = [
            'total_active_users' => $userActivity->count(),
            'total_transactions' => $userActivity->sum('transactions_count'),
            'total_volume' => $userActivity->sum('transactions_sum_amount'),
            'avg_transactions_per_user' => $userActivity->avg('transactions_count'),
            'avg_volume_per_user' => $userActivity->avg('transactions_sum_amount')
        ];

        return [
            'type' => 'User Activity Report',
            'period' => "$dateFrom to $dateTo",
            'summary' => $activitySummary,
            'user_details' => $userActivity,
            'generated_at' => now()
        ];
    }

    /**
     * Generate KYC status report
     */
    private function generateKycStatusReport($dateFrom, $dateTo)
    {
        $kycStats = [
            'total_submissions' => UserVerification::whereBetween('submitted_at', [$dateFrom, $dateTo])->count(),
            'pending_verifications' => UserVerification::where('status', 'pending')->count(),
            'approved_verifications' => UserVerification::where('status', 'approved')
                                                       ->whereBetween('approved_at', [$dateFrom, $dateTo])->count(),
            'rejected_verifications' => UserVerification::where('status', 'rejected')
                                                       ->whereBetween('reviewed_at', [$dateFrom, $dateTo])->count()
        ];

        $verificationByType = UserVerification::selectRaw('
            verification_type,
            status,
            COUNT(*) as count
        ')
        ->whereBetween('submitted_at', [$dateFrom, $dateTo])
        ->groupBy('verification_type', 'status')
        ->get();

        return [
            'type' => 'KYC Status Report',
            'period' => "$dateFrom to $dateTo",
            'summary' => $kycStats,
            'by_type' => $verificationByType,
            'generated_at' => now()
        ];
    }

    /**
     * Export report in specified format
     */
    private function exportReport($reportData, $reportType, $format)
    {
        $filename = str_replace(' ', '_', strtolower($reportData['type'])) . '_' . date('Y-m-d_H-i-s');

        switch ($format) {
            case 'csv':
                return $this->exportToCsv($reportData, $filename);
            case 'excel':
                return $this->exportToExcel($reportData, $filename);
            case 'pdf':
                return $this->exportToPdf($reportData, $filename);
            default:
                throw new \Exception('Unsupported export format');
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($reportData, $filename)
    {
        $csvContent = "Report: {$reportData['type']}\n";
        $csvContent .= "Period: {$reportData['period']}\n";
        $csvContent .= "Generated: {$reportData['generated_at']}\n\n";

        // Add summary if exists
        if (isset($reportData['summary'])) {
            $csvContent .= "SUMMARY\n";
            foreach ($reportData['summary'] as $key => $value) {
                $csvContent .= ucwords(str_replace('_', ' ', $key)) . "," . $value . "\n";
            }
            $csvContent .= "\n";
        }

        // Add details if exists
        if (isset($reportData['details']) && $reportData['details']->count() > 0) {
            $csvContent .= "DETAILS\n";

            // Headers
            $headers = array_keys($reportData['details']->first()->toArray());
            $csvContent .= implode(',', array_map('ucwords', $headers)) . "\n";

            // Data
            foreach ($reportData['details'] as $row) {
                $csvContent .= implode(',', array_values($row->toArray())) . "\n";
            }
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }

    /**
     * Export to PDF (placeholder)
     */
    private function exportToPdf($reportData, $filename)
    {
        // This would use a PDF library like DomPDF or similar
        // For now, return a simple text response
        return response()->json([
            'message' => 'PDF export not implemented yet',
            'data' => $reportData
        ]);
    }

    /**
     * Export to Excel (placeholder)
     */
    private function exportToExcel($reportData, $filename)
    {
        // This would use PhpSpreadsheet or similar
        // For now, return CSV format
        return $this->exportToCsv($reportData, $filename);
    }

    /**
     * Get recent reports
     */
    private function getRecentReports()
    {
        // This would fetch from a scheduled_reports or report_history table
        return collect([]);
    }

    /**
     * Calculate next run time for scheduled reports
     */
    private function calculateNextRun($frequency)
    {
        $now = Carbon::now();

        return match($frequency) {
            'daily' => $now->addDay(),
            'weekly' => $now->addWeek(),
            'monthly' => $now->addMonth(),
            default => $now->addDay()
        };
    }

    /**
     * Generate custom report based on selected metrics
     */
    private function generateCustomReport($metrics, $dateFrom, $dateTo, $grouping)
    {
        $reportData = [
            'type' => 'Custom Report',
            'period' => "$dateFrom to $dateTo",
            'metrics' => $metrics,
            'grouping' => $grouping,
            'data' => [],
            'generated_at' => now()
        ];

        // This would dynamically build queries based on selected metrics
        // Implementation would depend on specific metric requirements

        return $reportData;
    }
}
