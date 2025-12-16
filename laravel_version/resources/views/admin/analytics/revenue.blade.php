@extends('layouts.admin')

@section('title', 'Revenue Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-600 to-green-700 rounded-xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Revenue Analytics</h1>
                <p class="text-emerald-100 mt-1">Financial performance and revenue insights</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <form method="GET" class="flex items-center space-x-2">
                    <select name="period" onchange="this.form.submit()" class="bg-white/20 text-white placeholder-emerald-200 border border-white/30 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <option value="7_days" {{ $period === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30_days" {{ $period === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90_days" {{ $period === '90_days' ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="1_year" {{ $period === '1_year' ? 'selected' : '' }}>Last Year</option>
                    </select>
                </form>
                <a href="{{ route('analytics.index') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                    ← Back to Analytics
                </a>
            </div>
        </div>
    </div>

    <!-- Revenue Overview -->
    @if(isset($analytics['overview']))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($analytics['overview']['total_revenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Daily Average</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($analytics['overview']['daily_average'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Growth Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['growth_rate'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Transactions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['total_transactions'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Revenue Trends -->
    @if(isset($analytics['revenue_trends']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Revenue Trends</h2>
            <p class="text-sm text-gray-500 mt-1">Daily revenue performance over time</p>
        </div>
        <div class="p-6">
            @if(count($analytics['revenue_trends']) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
                    @foreach(array_slice($analytics['revenue_trends'], -7) as $trend)
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">{{ $trend['date'] ?? 'N/A' }}</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">₦{{ number_format($trend['revenue'] ?? 0, 0) }}</p>
                        <p class="text-xs text-gray-500">{{ $trend['transaction_count'] ?? 0 }} txns</p>
                    </div>
                    @endforeach
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="pb-2 font-medium text-gray-600">Date</th>
                                <th class="pb-2 font-medium text-gray-600">Revenue</th>
                                <th class="pb-2 font-medium text-gray-600">Transactions</th>
                                <th class="pb-2 font-medium text-gray-600">Avg Per Transaction</th>
                                <th class="pb-2 font-medium text-gray-600">Growth</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($analytics['revenue_trends'] as $trend)
                            <tr>
                                <td class="py-3 text-gray-900">{{ $trend['date'] ?? 'N/A' }}</td>
                                <td class="py-3 text-gray-900 font-medium">₦{{ number_format($trend['revenue'] ?? 0, 2) }}</td>
                                <td class="py-3 text-gray-900">{{ number_format($trend['transaction_count'] ?? 0) }}</td>
                                <td class="py-3 text-gray-900">₦{{ number_format($trend['avg_per_transaction'] ?? 0, 2) }}</td>
                                <td class="py-3">
                                    @if(($trend['growth_rate'] ?? 0) > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            +{{ number_format($trend['growth_rate'], 1) }}%
                                        </span>
                                    @elseif(($trend['growth_rate'] ?? 0) < 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ number_format($trend['growth_rate'], 1) }}%
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            0.0%
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                    </svg>
                    <p class="text-gray-500">No revenue trend data available</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Revenue by Service and Top Revenue Days -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue by Service -->
        @if(isset($analytics['revenue_by_service']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Revenue by Service</h2>
                <p class="text-sm text-gray-500 mt-1">Service contribution to total revenue</p>
            </div>
            <div class="p-6">
                @if(count($analytics['revenue_by_service']) > 0)
                    <div class="space-y-4">
                        @foreach($analytics['revenue_by_service'] as $service)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 mr-3"></div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ ucfirst($service['service'] ?? 'Unknown') }}</p>
                                    <p class="text-sm text-gray-500">{{ number_format($service['transaction_count'] ?? 0) }} transactions</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">₦{{ number_format($service['revenue'] ?? 0, 2) }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($service['percentage'] ?? 0, 1) }}%</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <p class="text-gray-500">No service revenue data available</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Top Revenue Days -->
        @if(isset($analytics['top_revenue_days']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Top Revenue Days</h2>
                <p class="text-sm text-gray-500 mt-1">Best performing days by revenue</p>
            </div>
            <div class="p-6">
                @if(count($analytics['top_revenue_days']) > 0)
                    <div class="space-y-4">
                        @foreach($analytics['top_revenue_days'] as $index => $day)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">{{ $day['date'] ?? 'Unknown Date' }}</p>
                                    <p class="text-sm text-gray-500">{{ $day['day_name'] ?? '' }} • {{ $day['transaction_count'] ?? 0 }} transactions</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">₦{{ number_format($day['revenue'] ?? 0, 2) }}</p>
                                <p class="text-sm text-gray-500">₦{{ number_format($day['avg_per_transaction'] ?? 0, 2) }} avg</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-500">No top revenue days data available</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Monthly/Weekly Comparison and Financial Health -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Period Comparison -->
        @if(isset($analytics['period_comparison']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Period Comparison</h2>
                <p class="text-sm text-gray-500 mt-1">Current vs previous period performance</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Current Period</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($analytics['period_comparison']['current_revenue'] ?? 0, 2) }}</p>
                        <p class="text-sm text-gray-500">{{ $analytics['period_comparison']['current_transactions'] ?? 0 }} transactions</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Previous Period</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($analytics['period_comparison']['previous_revenue'] ?? 0, 2) }}</p>
                        <p class="text-sm text-gray-500">{{ $analytics['period_comparison']['previous_transactions'] ?? 0 }} transactions</p>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Revenue Change:</span>
                        @if(($analytics['period_comparison']['revenue_change'] ?? 0) > 0)
                            <span class="font-medium text-green-600">+{{ number_format($analytics['period_comparison']['revenue_change'], 1) }}%</span>
                        @elseif(($analytics['period_comparison']['revenue_change'] ?? 0) < 0)
                            <span class="font-medium text-red-600">{{ number_format($analytics['period_comparison']['revenue_change'], 1) }}%</span>
                        @else
                            <span class="font-medium text-gray-600">0.0%</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Transaction Change:</span>
                        @if(($analytics['period_comparison']['transaction_change'] ?? 0) > 0)
                            <span class="font-medium text-green-600">+{{ number_format($analytics['period_comparison']['transaction_change'], 1) }}%</span>
                        @elseif(($analytics['period_comparison']['transaction_change'] ?? 0) < 0)
                            <span class="font-medium text-red-600">{{ number_format($analytics['period_comparison']['transaction_change'], 1) }}%</span>
                        @else
                            <span class="font-medium text-gray-600">0.0%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Financial Health Metrics -->
        @if(isset($analytics['financial_health']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Financial Health</h2>
                <p class="text-sm text-gray-500 mt-1">Key financial performance indicators</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Revenue Consistency Score:</span>
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $analytics['financial_health']['consistency_score'] ?? 0 }}%"></div>
                            </div>
                            <span class="font-medium text-gray-900">{{ number_format($analytics['financial_health']['consistency_score'] ?? 0, 1) }}%</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Average Transaction Value:</span>
                        <span class="font-medium text-gray-900">₦{{ number_format($analytics['financial_health']['avg_transaction_value'] ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Revenue Per Day:</span>
                        <span class="font-medium text-gray-900">₦{{ number_format($analytics['financial_health']['revenue_per_day'] ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Peak Revenue Day:</span>
                        <span class="font-medium text-gray-900">{{ $analytics['financial_health']['peak_day'] ?? 'N/A' }}</span>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Success Rate Impact:</span>
                        <span class="font-medium text-gray-900">{{ number_format($analytics['financial_health']['success_rate_impact'] ?? 0, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
