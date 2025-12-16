@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Analytics Dashboard</h1>
                <p class="text-blue-100 mt-1">Comprehensive business intelligence and insights</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <form method="GET" class="flex space-x-2">
                    <select name="period" onchange="this.form.submit()" class="bg-white/20 text-white placeholder-blue-200 border border-white/30 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <option value="7_days" {{ $period === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30_days" {{ $period === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90_days" {{ $period === '90_days' ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="1_year" {{ $period === '1_year' ? 'selected' : '' }}>Last Year</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Overview Metrics -->
    @if(isset($data['overview']))
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
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($data['overview']['total_revenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($data['overview']['total_users'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($data['overview']['total_transactions'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Success Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($data['overview']['success_rate'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Navigation -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('analytics.transactions') }}" class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 hover:bg-gray-50 transition-colors">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">Transactions</p>
                    <p class="text-sm text-gray-500">Detailed transaction analysis</p>
                </div>
            </div>
        </a>

        <a href="{{ route('analytics.users') }}" class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 hover:bg-gray-50 transition-colors">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">Users</p>
                    <p class="text-sm text-gray-500">User behavior & demographics</p>
                </div>
            </div>
        </a>

        <a href="{{ route('analytics.services') }}" class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 hover:bg-gray-50 transition-colors">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">Services</p>
                    <p class="text-sm text-gray-500">Service performance metrics</p>
                </div>
            </div>
        </a>

        <a href="{{ route('analytics.revenue') }}" class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 hover:bg-gray-50 transition-colors">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">Revenue</p>
                    <p class="text-sm text-gray-500">Financial performance analysis</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Transaction Trends Chart -->
    @if(isset($data['transaction_trends']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Transaction Trends</h2>
            <p class="text-sm text-gray-500 mt-1">Daily transaction volume over time</p>
        </div>
        <div class="p-6">
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                    </svg>
                    <p class="text-gray-500">Transaction trends chart</p>
                    <p class="text-sm text-gray-400 mt-1">{{ count($data['transaction_trends']) }} data points</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Top Users & Service Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Users -->
        @if(isset($data['top_users']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Top Users</h2>
                <p class="text-sm text-gray-500 mt-1">Highest value customers</p>
            </div>
            <div class="p-6">
                @if(count($data['top_users']) > 0)
                    <div class="space-y-4">
                        @foreach($data['top_users'] as $user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($user['name'] ?? 'U', 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">{{ $user['name'] ?? 'Unknown User' }}</p>
                                    <p class="text-sm text-gray-500">{{ $user['transactions_count'] ?? 0 }} transactions</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">₦{{ number_format($user['total_spent'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-gray-500">No user data available</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Service Performance -->
        @if(isset($data['service_performance']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Service Performance</h2>
                <p class="text-sm text-gray-500 mt-1">Success rates by service type</p>
            </div>
            <div class="p-6">
                @if(count($data['service_performance']) > 0)
                    <div class="space-y-4">
                        @foreach($data['service_performance'] as $service)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ ucfirst($service['service'] ?? 'Unknown') }}</p>
                                <p class="text-sm text-gray-500">{{ $service['total_transactions'] ?? 0 }} transactions</p>
                            </div>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $service['success_rate'] ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($service['success_rate'] ?? 0, 1) }}%</span>
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
    </div>

    <!-- API Performance -->
    @if(isset($data['api_performance']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">API Performance</h2>
            <p class="text-sm text-gray-500 mt-1">API response times and success rates</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900">{{ $data['api_performance']['total_requests'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">Total Requests</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($data['api_performance']['success_rate'] ?? 0, 1) }}%</p>
                    <p class="text-sm text-gray-500">Success Rate</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($data['api_performance']['avg_response_time'] ?? 0, 0) }}ms</p>
                    <p class="text-sm text-gray-500">Avg Response Time</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh data every 5 minutes
    setTimeout(() => {
        window.location.reload();
    }, 300000);
</script>
@endpush
