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
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Filter Transactions</h2>
            <form id="filter-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Transaction Type
                    </label>
                    <select id="transaction_type"
                            name="transaction_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="deposit">Deposits</option>
                        <option value="withdrawal">Withdrawals</option>
                        <option value="transfer_sent">Transfers Sent</option>
                        <option value="transfer_received">Transfers Received</option>
                        <option value="airtime">Airtime Purchase</option>
                        <option value="data">Data Purchase</option>
                        <option value="cable_tv">Cable TV</option>
                        <option value="electricity">Electricity</option>
                        <option value="exam_pin">Exam Pin</option>
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                        From Date
                    </label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                        To Date
                    </label>
                    <input type="date"
                           id="date_to"
                           name="date_to"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full bg-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Deposits</p>
                    <p class="text-2xl font-bold text-green-600">₦0.00</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-arrow-down text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Withdrawals</p>
                    <p class="text-2xl font-bold text-red-600">₦0.00</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-arrow-up text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Spent</p>
                    <p class="text-2xl font-bold text-orange-600">₦0.00</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-bold text-blue-600">0</p>
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
                            Transaction ID
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                    </tr>
                </thead>
                <tbody id="transactions-table" class="bg-white divide-y divide-gray-200">
                    <!-- Transactions will be loaded here -->
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div id="transactions-mobile" class="md:hidden p-6 space-y-4">
            <!-- Mobile transaction cards will be loaded here -->
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="text-center py-12">
            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-receipt text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
            <p class="text-gray-500">Your transaction history will appear here once you start using the platform</p>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="px-6 py-4 border-t border-gray-100 hidden">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span id="showing-from">0</span> to <span id="showing-to">0</span> of <span id="total-records">0</span> results
                </div>
                <div class="flex space-x-2">
                    <button id="prev-btn"
                            class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button id="next-btn"
                            class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPage = 1;
let totalPages = 1;

$(document).ready(function() {
    loadTransactions();

    // Filter form submission
    $('#filter-form').submit(function(e) {
        e.preventDefault();
        currentPage = 1;
        loadTransactions();
    });

    // Refresh button
    $('#refresh-btn').click(function() {
        loadTransactions();
    });

    // Export button
    $('#export-btn').click(function() {
        exportTransactions();
    });

    // Pagination
    $('#prev-btn').click(function() {
        if (currentPage > 1) {
            currentPage--;
            loadTransactions();
        }
    });

    $('#next-btn').click(function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadTransactions();
        }
    });
});

function loadTransactions() {
    // Show loading state
    $('#transactions-table').html(`
        <tr>
            <td colspan="6" class="px-6 py-12 text-center">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                <p class="text-gray-500">Loading transactions...</p>
            </td>
        </tr>
    `);

    // Simulate API call (replace with actual AJAX call)
    setTimeout(() => {
        // Mock empty data for now
        const transactions = [];

        if (transactions.length === 0) {
            $('#transactions-table').empty();
            $('#transactions-mobile').empty();
            $('#empty-state').show();
            $('#pagination').hide();
        } else {
            renderTransactions(transactions);
            $('#empty-state').hide();
            $('#pagination').show();
        }
    }, 1000);
}

function renderTransactions(transactions) {
    // Desktop table
    let tableHTML = '';
    transactions.forEach(transaction => {
        tableHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${transaction.id}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getTypeClass(transaction.type)}">
                        ${getTypeIcon(transaction.type)} ${transaction.type}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${transaction.description}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium ${getAmountClass(transaction.type)}">
                    ${transaction.type.includes('deposit') || transaction.type.includes('received') ? '+' : '-'}₦${transaction.amount}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(transaction.status)}">
                        ${transaction.status}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${formatDate(transaction.created_at)}
                </td>
            </tr>
        `;
    });
    $('#transactions-table').html(tableHTML);

    // Mobile cards
    let mobileHTML = '';
    transactions.forEach(transaction => {
        mobileHTML += `
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-medium text-gray-900">${transaction.description}</p>
                        <p class="text-sm text-gray-500">${transaction.id}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusClass(transaction.status)}">
                        ${transaction.status}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getTypeClass(transaction.type)}">
                        ${getTypeIcon(transaction.type)} ${transaction.type}
                    </span>
                    <span class="text-lg font-semibold ${getAmountClass(transaction.type)}">
                        ${transaction.type.includes('deposit') || transaction.type.includes('received') ? '+' : '-'}₦${transaction.amount}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-2">${formatDate(transaction.created_at)}</p>
            </div>
        `;
    });
    $('#transactions-mobile').html(mobileHTML);
}

function getTypeClass(type) {
    const classes = {
        'deposit': 'bg-green-100 text-green-800',
        'withdrawal': 'bg-red-100 text-red-800',
        'transfer_sent': 'bg-purple-100 text-purple-800',
        'transfer_received': 'bg-blue-100 text-blue-800',
        'airtime': 'bg-orange-100 text-orange-800',
        'data': 'bg-cyan-100 text-cyan-800',
        'cable_tv': 'bg-yellow-100 text-yellow-800',
        'electricity': 'bg-indigo-100 text-indigo-800',
        'exam_pin': 'bg-pink-100 text-pink-800'
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
}

function getTypeIcon(type) {
    const icons = {
        'deposit': '↓',
        'withdrawal': '↑',
        'transfer_sent': '→',
        'transfer_received': '←',
        'airtime': '📱',
        'data': '📊',
        'cable_tv': '📺',
        'electricity': '⚡',
        'exam_pin': '📝'
    };
    return icons[type] || '•';
}

function getAmountClass(type) {
    return type.includes('deposit') || type.includes('received') ? 'text-green-600' : 'text-red-600';
}

function getStatusClass(status) {
    const classes = {
        'completed': 'bg-green-100 text-green-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'failed': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

function exportTransactions() {
    // Implement export functionality
    Swal.fire({
        icon: 'info',
        title: 'Export Feature',
        text: 'Export functionality will be implemented soon',
        confirmButtonColor: '#6366F1'
    });
}
</script>
@endpush
@endsection
