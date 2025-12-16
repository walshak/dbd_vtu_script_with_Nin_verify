@extends('layouts.admin')

@section('title', 'Service Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Service Analytics</h1>
                <p class="text-indigo-100 mt-1">Performance metrics and insights for all services</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <form method="GET" class="flex items-center space-x-2">
                    <select name="period" onchange="this.form.submit()" class="bg-white/20 text-white placeholder-indigo-200 border border-white/30 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/50">
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

    <!-- Service Overview -->
    @if(isset($analytics['overview']))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Services</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $analytics['overview']['active_services'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Requests</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['total_requests'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Overall Success Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['overall_success_rate'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($analytics['overview']['total_revenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Service Performance Breakdown -->
    @if(isset($analytics['service_breakdown']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Service Performance Breakdown</h2>
            <p class="text-sm text-gray-500 mt-1">Detailed metrics for each service type</p>
        </div>
        <div class="p-6">
            @if(count($analytics['service_breakdown']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($analytics['service_breakdown'] as $service)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-indigo-100 rounded-lg">
                                    @if(($service['service'] ?? '') === 'airtime')
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    @elseif(($service['service'] ?? '') === 'data')
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                                        </svg>
                                    @elseif(($service['service'] ?? '') === 'cable')
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <h3 class="font-semibold text-gray-900">{{ ucfirst($service['service'] ?? 'Unknown') }}</h3>
                                    <p class="text-sm text-gray-500">{{ number_format($service['transaction_count'] ?? 0) }} transactions</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">{{ number_format($service['success_rate'] ?? 0, 1) }}%</p>
                                <p class="text-sm text-gray-500">success rate</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Revenue:</span>
                                <span class="font-medium text-gray-900">₦{{ number_format($service['revenue'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Avg Amount:</span>
                                <span class="font-medium text-gray-900">₦{{ number_format($service['avg_amount'] ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Failed Transactions:</span>
                                <span class="font-medium text-red-600">{{ number_format($service['failed_count'] ?? 0) }}</span>
                            </div>

                            <!-- Success Rate Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $service['success_rate'] ?? 0 }}%"></div>
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
                    <p class="text-gray-500">No service data available for the selected period</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Service Trends and Popular Services -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Service Usage Trends -->
        @if(isset($analytics['usage_trends']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Usage Trends</h2>
                <p class="text-sm text-gray-500 mt-1">Service usage over time</p>
            </div>
            <div class="p-6">
                @if(count($analytics['usage_trends']) > 0)
                    <div class="space-y-4">
                        @foreach($analytics['usage_trends'] as $trend)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $trend['date'] ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ $trend['day_name'] ?? '' }}</p>
                            </div>
                            <div class="flex items-center">
                                <div class="w-20 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ min(($trend['usage_percentage'] ?? 0), 100) }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $trend['transaction_count'] ?? 0 }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                        </svg>
                        <p class="text-gray-500">No usage trend data available</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Most Popular Services -->
        @if(isset($analytics['popular_services']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Most Popular Services</h2>
                <p class="text-sm text-gray-500 mt-1">Ranked by transaction volume</p>
            </div>
            <div class="p-6">
                @if(count($analytics['popular_services']) > 0)
                    <div class="space-y-4">
                        @foreach($analytics['popular_services'] as $index => $service)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">{{ ucfirst($service['service'] ?? 'Unknown') }}</p>
                                    <p class="text-sm text-gray-500">{{ number_format($service['success_rate'] ?? 0, 1) }}% success rate</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ number_format($service['transaction_count'] ?? 0) }}</p>
                                <p class="text-sm text-gray-500">transactions</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <p class="text-gray-500">No popularity data available</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Error Analysis -->
    @if(isset($analytics['error_analysis']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Error Analysis</h2>
            <p class="text-sm text-gray-500 mt-1">Common failure reasons and patterns</p>
        </div>
        <div class="p-6">
            @if(count($analytics['error_analysis']) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="pb-2 font-medium text-gray-600">Service</th>
                                <th class="pb-2 font-medium text-gray-600">Error Type</th>
                                <th class="pb-2 font-medium text-gray-600">Count</th>
                                <th class="pb-2 font-medium text-gray-600">Impact</th>
                                <th class="pb-2 font-medium text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($analytics['error_analysis'] as $error)
                            <tr>
                                <td class="py-3 text-gray-900">{{ ucfirst($error['service'] ?? 'Unknown') }}</td>
                                <td class="py-3 text-gray-900">{{ $error['error_type'] ?? 'Unknown Error' }}</td>
                                <td class="py-3 text-gray-900">{{ number_format($error['count'] ?? 0) }}</td>
                                <td class="py-3">
                                    @if(($error['impact'] ?? 'low') === 'high')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">High</span>
                                    @elseif(($error['impact'] ?? 'low') === 'medium')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Medium</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Low</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if(($error['status'] ?? 'active') === 'resolved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Resolved</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Active</span>
                                    @endif
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
                    <p class="text-gray-500">No errors detected</p>
                    <p class="text-sm text-gray-400 mt-1">All services are running smoothly!</p>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
