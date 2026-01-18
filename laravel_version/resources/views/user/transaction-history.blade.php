@extends('layouts.user-layout')

@section('title', 'Transaction History')

@section('page-content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-full">
                        <i class="fas fa-history text-4xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Transaction History</h1>
                <p class="text-indigo-100 text-lg text-center">View all your wallet transactions and activities</p>
                <div class="text-center mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-6 py-3 inline-block">
                        <p class="text-sm font-medium">Current Balance</p>
                        <p class="text-2xl font-bold">₦{{ number_format($balance ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                <i class="fas fa-chart-line text-9xl"></i>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-filter text-indigo-600 mr-2"></i>Filter Transactions
                <span class="text-sm text-gray-500 font-normal ml-2">(Default: Last 30 days)</span>
            </h2>
            <form method="GET" action="{{ route('transactions') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="service" class="block text-sm font-medium text-gray-700 mb-2">
                        Service Type
                    </label>
                    <select id="service"
                            name="service"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="all" {{ $serviceFilter == 'all' ? 'selected' : '' }}>All Services</option>
                        @foreach($availableServices as $service)
                            <option value="{{ $service }}" {{ $serviceFilter == $service ? 'selected' : '' }}>
                                {{ $service }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select id="status"
                            name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="success" {{ $statusFilter == 'success' ? 'selected' : '' }}>Successful</option>
                        <option value="failed" {{ $statusFilter == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                        From Date
                    </label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           value="{{ $dateFrom }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                        To Date
                    </label>
                    <input type="date"
                           id="date_to"
                           name="date_to"
                           value="{{ $dateTo }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 bg-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply
                    </button>
                    <a href="{{ route('transactions') }}"
                       class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Wallet Funding</p>
                    <p class="text-2xl font-bold text-green-600">₦{{ number_format($summary['wallet_topup'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-arrow-down text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Spent</p>
                    <p class="text-2xl font-bold text-orange-600">₦{{ number_format($summary['total_spent'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Successful</p>
                    <p class="text-2xl font-bold text-green-600">{{ $summary['successful'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">₦{{ number_format($summary['total_amount'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $summary['total_transactions'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $summary['failed'] ?? 0 }} failed</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-list text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Recent Transactions</h2>
                <div class="flex space-x-2">
                    <button id="export-btn"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    <button id="refresh-btn"
                            class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reference
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Service
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Balance
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-600">
                            {{ substr($transaction->transref, 0, 15) }}...
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full {{ $transaction->status == 0 ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center mr-2">
                                    <i class="{{ $transaction->service_icon }} text-xs {{ $transaction->status == 0 ? 'text-green-600' : 'text-red-600' }}"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $transaction->servicename }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                            {{ $transaction->servicedesc }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $transaction->servicename == 'Wallet Topup' ? 'text-green-600' : 'text-gray-900' }}">
                            {{ $transaction->formatted_amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                            <div>Old: ₦{{ number_format(floatval($transaction->oldbal), 2) }}</div>
                            <div class="font-medium text-gray-700">New: ₦{{ number_format(floatval($transaction->newbal), 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->status == 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Success
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Failed
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $transaction->date ? $transaction->date->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-xs text-gray-400">{{ $transaction->date ? $transaction->date->format('h:i A') : '' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                            <p class="text-gray-500 mb-4">No transactions match your filter criteria</p>
                            <a href="{{ route('transactions') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                Clear filters
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden p-6 space-y-4">
            @forelse($transactions as $transaction)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-indigo-300 transition-colors">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full {{ $transaction->status == 0 ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center mr-3">
                            <i class="{{ $transaction->service_icon }} {{ $transaction->status == 0 ? 'text-green-600' : 'text-red-600' }}"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $transaction->servicename }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ substr($transaction->transref, 0, 15) }}...</p>
                        </div>
                    </div>
                    @if($transaction->status == 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Success
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>Failed
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-700 mb-3 line-clamp-2">{{ $transaction->servicedesc }}</p>
                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Amount</p>
                        <p class="text-lg font-bold {{ $transaction->servicename == 'Wallet Topup' ? 'text-green-600' : 'text-gray-900' }}">
                            {{ $transaction->formatted_amount }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Balance</p>
                        <p class="text-sm font-medium text-gray-700">₦{{ number_format(floatval($transaction->newbal), 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">{{ $transaction->date ? $transaction->date->format('M d, Y') : 'N/A' }}</p>
                        <p class="text-xs text-gray-400">{{ $transaction->date ? $transaction->date->format('h:i A') : '' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                <p class="text-gray-500 mb-4">No transactions match your filter criteria</p>
                <a href="{{ route('transactions') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                    Clear filters
                </a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $transactions->firstItem() ?? 0 }}</span>
                    to <span class="font-medium">{{ $transactions->lastItem() ?? 0 }}</span>
                    of <span class="font-medium">{{ $transactions->total() }}</span> results
                </div>
                <div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Refresh button
    $('#refresh-btn').click(function() {
        window.location.reload();
    });

    // Export button
    $('#export-btn').click(function() {
        Swal.fire({
            icon: 'info',
            title: 'Export Feature',
            text: 'Export functionality will be implemented soon. You can take a screenshot or print this page for now.',
            confirmButtonColor: '#6366F1'
        });
    });
});
</script>
@endpush
@endsection
