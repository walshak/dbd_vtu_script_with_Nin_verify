@extends('layouts.admin')

@section('title', 'Transaction Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Transaction Analytics</h1>
                <p class="text-purple-100 mt-1">Detailed transaction analysis and insights</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('analytics.index') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                    ← Back to Analytics
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                <select name="service_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All Services</option>
                    <option value="airtime" {{ $serviceType === 'airtime' ? 'selected' : '' }}>Airtime</option>
                    <option value="data" {{ $serviceType === 'data' ? 'selected' : '' }}>Data</option>
                    <option value="cable" {{ $serviceType === 'cable' ? 'selected' : '' }}>Cable TV</option>
                    <option value="electricity" {{ $serviceType === 'electricity' ? 'selected' : '' }}>Electricity</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Analytics Overview -->
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['total_count'] ?? 0) }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Success Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['success_rate'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Average Amount</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($analytics['overview']['avg_amount'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Transaction Trends -->
    @if(isset($analytics['trends']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Transaction Trends</h2>
            <p class="text-sm text-gray-500 mt-1">Daily transaction volume and revenue</p>
        </div>
        <div class="p-6">
            @if(count($analytics['trends']) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="pb-2 font-medium text-gray-600">Date</th>
                                <th class="pb-2 font-medium text-gray-600">Transactions</th>
                                <th class="pb-2 font-medium text-gray-600">Revenue</th>
                                <th class="pb-2 font-medium text-gray-600">Success Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($analytics['trends'] as $trend)
                            <tr>
                                <td class="py-3 text-gray-900">{{ $trend['date'] ?? 'N/A' }}</td>
                                <td class="py-3 text-gray-900">{{ number_format($trend['count'] ?? 0) }}</td>
                                <td class="py-3 text-gray-900">₦{{ number_format($trend['revenue'] ?? 0, 2) }}</td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ number_format($trend['success_rate'] ?? 0, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-gray-500">No transaction data found for the selected period</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Service Breakdown -->
    @if(isset($analytics['by_service']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Service Breakdown</h2>
            <p class="text-sm text-gray-500 mt-1">Performance by service type</p>
        </div>
        <div class="p-6">
            @if(count($analytics['by_service']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($analytics['by_service'] as $service)
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-gray-900">{{ ucfirst($service['service'] ?? 'Unknown') }}</h3>
                            <span class="text-sm text-gray-500">{{ number_format($service['count'] ?? 0) }} transactions</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Revenue:</span>
                                <span class="font-medium">₦{{ number_format($service['revenue'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm items-center">
                                <span class="text-gray-600">Success Rate:</span>
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5 mr-2">
                                        <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $service['success_rate'] ?? 0 }}%"></div>
                                    </div>
                                    <span class="font-medium">{{ number_format($service['success_rate'] ?? 0, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    <p class="text-gray-500">No service data available</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Recent Failed Transactions -->
    @if(isset($analytics['failed_transactions']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Recent Failed Transactions</h2>
            <p class="text-sm text-gray-500 mt-1">Last 10 failed transactions for investigation</p>
        </div>
        <div class="p-6">
            @if(count($analytics['failed_transactions']) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="pb-2 font-medium text-gray-600">Date</th>
                                <th class="pb-2 font-medium text-gray-600">User</th>
                                <th class="pb-2 font-medium text-gray-600">Service</th>
                                <th class="pb-2 font-medium text-gray-600">Amount</th>
                                <th class="pb-2 font-medium text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($analytics['failed_transactions'] as $transaction)
                            <tr>
                                <td class="py-3 text-gray-900 text-sm">{{ $transaction['created_at'] ?? 'N/A' }}</td>
                                <td class="py-3 text-gray-900 text-sm">{{ $transaction['user_name'] ?? 'Unknown' }}</td>
                                <td class="py-3 text-gray-900 text-sm">{{ ucfirst($transaction['service'] ?? 'Unknown') }}</td>
                                <td class="py-3 text-gray-900 text-sm">₦{{ number_format($transaction['amount'] ?? 0, 2) }}</td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $transaction['status'] ?? 'Failed' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-green-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500">No failed transactions found</p>
                    <p class="text-sm text-gray-400 mt-1">Great! All transactions are successful</p>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
