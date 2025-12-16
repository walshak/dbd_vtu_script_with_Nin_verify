@extends('layouts.admin')

@section('title', 'Transaction Management')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Transaction Management</h1>
                    <p class="text-blue-100 text-lg">Monitor and manage all platform transactions</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-blue-200">Total Transactions</div>
                    <div class="text-2xl font-bold">{{ number_format($transactions->total()) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Transactions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_transactions'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-exchange-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['total_amount'] ?? 0, 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-naira-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Successful Transactions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Successful</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['successful_transactions'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Failed Transactions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Failed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['failed_transactions'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Transactions</h3>
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="successful" {{ request('status') == 'successful' ? 'selected' : '' }}>Successful</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <!-- Service Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                <select name="service_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Services</option>
                    <option value="airtime" {{ request('service_type') == 'airtime' ? 'selected' : '' }}>Airtime</option>
                    <option value="data" {{ request('service_type') == 'data' ? 'selected' : '' }}>Data</option>
                    <option value="cable_tv" {{ request('service_type') == 'cable_tv' ? 'selected' : '' }}>Cable TV</option>
                    <option value="electricity" {{ request('service_type') == 'electricity' ? 'selected' : '' }}>Electricity</option>
                    <option value="exam_pin" {{ request('service_type') == 'exam_pin' ? 'selected' : '' }}>Exam Pin</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Search Input -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by transaction ID, phone number, or user..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Filter Actions -->
            <div class="lg:col-span-2 flex items-end gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
                <button type="button" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200" onclick="exportTransactions()">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $transaction->transaction_id ?? $transaction->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->phone ?? $transaction->user->phone ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->service_type ?? 'N/A')) }}</div>
                            <div class="text-sm text-gray-500">{{ $transaction->network ?? $transaction->provider ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">₦{{ number_format($transaction->amount ?? 0, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $transaction->status ?? 'pending';
                                $statusClasses = [
                                    'successful' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                ];
                                $statusClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->created_at ? $transaction->created_at->format('M d, Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="viewTransaction('{{ $transaction->id }}')"
                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-150">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="retryTransaction('{{ $transaction->id }}')"
                                        class="text-green-600 hover:text-green-900 transition-colors duration-150">
                                    <i class="fas fa-redo"></i>
                                </button>
                                <button onclick="refundTransaction('{{ $transaction->id }}')"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-150">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No transactions found</p>
                                <p class="text-gray-400">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Transaction Details Modal -->
<div id="transactionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Transaction Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="transactionDetails" class="space-y-4">
                <!-- Transaction details will be loaded here -->
            </div>
            <div class="flex justify-end mt-6 space-x-3">
                <button onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function viewTransaction(transactionId) {
    // Show modal
    document.getElementById('transactionModal').classList.remove('hidden');

    // Load transaction details
    fetch(`/admin/transactions/${transactionId}`)
        .then(response => response.json())
        .then(data => {
            const detailsDiv = document.getElementById('transactionDetails');
            detailsDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transaction ID</label>
                        <p class="mt-1 text-sm text-gray-900">${data.transaction_id || data.id}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <p class="mt-1 text-sm text-gray-900">${data.status}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                        <p class="mt-1 text-sm text-gray-900">₦${parseFloat(data.amount || 0).toLocaleString()}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Service Type</label>
                        <p class="mt-1 text-sm text-gray-900">${data.service_type}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <p class="mt-1 text-sm text-gray-900">${data.phone || 'N/A'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Network/Provider</label>
                        <p class="mt-1 text-sm text-gray-900">${data.network || data.provider || 'N/A'}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Response Message</label>
                        <p class="mt-1 text-sm text-gray-900">${data.response_message || 'N/A'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created At</label>
                        <p class="mt-1 text-sm text-gray-900">${new Date(data.created_at).toLocaleString()}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Updated At</label>
                        <p class="mt-1 text-sm text-gray-900">${new Date(data.updated_at).toLocaleString()}</p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('transactionDetails').innerHTML = '<p class="text-red-500">Error loading transaction details</p>';
        });
}

function closeModal() {
    document.getElementById('transactionModal').classList.add('hidden');
}

function retryTransaction(transactionId) {
    if (confirm('Are you sure you want to retry this transaction?')) {
        fetch(`/admin/transactions/${transactionId}/retry`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Transaction retry initiated successfully');
                location.reload();
            } else {
                alert('Failed to retry transaction: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error retrying transaction');
        });
    }
}

function refundTransaction(transactionId) {
    if (confirm('Are you sure you want to refund this transaction? This action cannot be undone.')) {
        fetch(`/admin/transactions/${transactionId}/refund`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Transaction refunded successfully');
                location.reload();
            } else {
                alert('Failed to refund transaction: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error refunding transaction');
        });
    }
}

function exportTransactions() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.append('export', 'true');

    window.open(`/admin/transactions/export?${params.toString()}`, '_blank');
}

// Close modal when clicking outside
document.getElementById('transactionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
