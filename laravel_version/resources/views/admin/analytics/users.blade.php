@extends('layouts.admin')

@section('title', 'User Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-teal-700 rounded-xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">User Analytics</h1>
                <p class="text-green-100 mt-1">User behavior, demographics, and engagement insights</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <form method="GET" class="flex items-center space-x-2">
                    <select name="period" onchange="this.form.submit()" class="bg-white/20 text-white placeholder-green-200 border border-white/30 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/50">
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

    <!-- User Overview -->
    @if(isset($analytics['overview']))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['total_users'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">New Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['new_users'] ?? 0) }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['active_users'] ?? 0) }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Avg Transactions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['overview']['avg_transactions_per_user'] ?? 0, 1) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- User Registration Trends -->
    @if(isset($analytics['registration_trends']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">User Registration Trends</h2>
            <p class="text-sm text-gray-500 mt-1">New user registrations over time</p>
        </div>
        <div class="p-6">
            @if(count($analytics['registration_trends']) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                    @foreach($analytics['registration_trends'] as $trend)
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">{{ $trend['date'] ?? 'N/A' }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $trend['new_users'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">registrations</p>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <p class="text-gray-500">No registration data available</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Top Users and User Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Users by Transaction Value -->
        @if(isset($analytics['top_users']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Top Users by Value</h2>
                <p class="text-sm text-gray-500 mt-1">Highest spending customers</p>
            </div>
            <div class="p-6">
                @if(count($analytics['top_users']) > 0)
                    <div class="space-y-4">
                        @foreach($analytics['top_users'] as $index => $user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-teal-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">{{ $user['name'] ?? 'Unknown User' }}</p>
                                    <p class="text-sm text-gray-500">{{ $user['email'] ?? 'No email' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">₦{{ number_format($user['total_spent'] ?? 0, 2) }}</p>
                                <p class="text-sm text-gray-500">{{ $user['transaction_count'] ?? 0 }} txns</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-gray-500">No user transaction data available</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- User Activity Patterns -->
        @if(isset($analytics['activity_patterns']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Activity Patterns</h2>
                <p class="text-sm text-gray-500 mt-1">Peak usage times and patterns</p>
            </div>
            <div class="p-6">
                @if(count($analytics['activity_patterns']) > 0)
                    <div class="space-y-4">
                        @foreach($analytics['activity_patterns'] as $pattern)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $pattern['hour'] ?? 'Unknown' }}:00</p>
                                <p class="text-sm text-gray-500">{{ $pattern['period'] ?? '' }}</p>
                            </div>
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($pattern['activity_percentage'] ?? 0) }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $pattern['transaction_count'] ?? 0 }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">No activity pattern data available</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- User Segmentation -->
    @if(isset($analytics['user_segments']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">User Segmentation</h2>
            <p class="text-sm text-gray-500 mt-1">User categories based on activity level</p>
        </div>
        <div class="p-6">
            @if(count($analytics['user_segments']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($analytics['user_segments'] as $segment)
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-gray-900">{{ $segment['segment'] ?? 'Unknown' }}</h3>
                            <span class="text-2xl font-bold text-gray-900">{{ $segment['user_count'] ?? 0 }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Avg Transactions:</span>
                                <span class="font-medium">{{ number_format($segment['avg_transactions'] ?? 0, 1) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Avg Spend:</span>
                                <span class="font-medium">₦{{ number_format($segment['avg_spend'] ?? 0, 2) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($segment['percentage'] ?? 0) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500">{{ number_format($segment['percentage'] ?? 0, 1) }}% of all users</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500">No user segmentation data available</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- KYC Verification Status -->
    @if(isset($analytics['kyc_status']))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">KYC Verification Status</h2>
            <p class="text-sm text-gray-500 mt-1">User verification completion rates</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-3xl font-bold text-green-600">{{ $analytics['kyc_status']['verified'] ?? 0 }}</p>
                    <p class="text-sm text-green-700 font-medium">Verified Users</p>
                    <p class="text-xs text-green-600 mt-1">{{ number_format(($analytics['kyc_status']['verified'] ?? 0) / max(($analytics['kyc_status']['total'] ?? 1), 1) * 100, 1) }}% of total</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <p class="text-3xl font-bold text-yellow-600">{{ $analytics['kyc_status']['pending'] ?? 0 }}</p>
                    <p class="text-sm text-yellow-700 font-medium">Pending Verification</p>
                    <p class="text-xs text-yellow-600 mt-1">{{ number_format(($analytics['kyc_status']['pending'] ?? 0) / max(($analytics['kyc_status']['total'] ?? 1), 1) * 100, 1) }}% of total</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-3xl font-bold text-gray-600">{{ $analytics['kyc_status']['unverified'] ?? 0 }}</p>
                    <p class="text-sm text-gray-700 font-medium">Unverified Users</p>
                    <p class="text-xs text-gray-600 mt-1">{{ number_format(($analytics['kyc_status']['unverified'] ?? 0) / max(($analytics['kyc_status']['total'] ?? 1), 1) * 100, 1) }}% of total</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
