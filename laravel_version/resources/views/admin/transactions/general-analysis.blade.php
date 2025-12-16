@extends('layouts.admin')

@section('title', 'General Sales Analysis')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line"></i> General Sales Analysis
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="exportTransactions()">
                <i class="fas fa-download"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Analysis Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control"
                               value="{{ $dateFrom }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control"
                               value="{{ $dateTo }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Service Type</label>
                        <select name="service_type" class="form-control" onchange="this.form.submit()">
                            <option value="all" {{ $serviceType == 'all' ? 'selected' : '' }}>All Services</option>
                            <option value="Airtime" {{ $serviceType == 'Airtime' ? 'selected' : '' }}>Airtime</option>
                            <option value="Data" {{ $serviceType == 'Data' ? 'selected' : '' }}>Data</option>
                            <option value="Cable Tv" {{ $serviceType == 'Cable Tv' ? 'selected' : '' }}>Cable TV</option>
                            <option value="Electricity" {{ $serviceType == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                            <option value="Exam Pin" {{ $serviceType == 'Exam Pin' ? 'selected' : '' }}>Exam Pin</option>
                            <option value="Recharge Pin" {{ $serviceType == 'Recharge Pin' ? 'selected' : '' }}>Recharge Pin</option>
                            <option value="Data Pin" {{ $serviceType == 'Data Pin' ? 'selected' : '' }}>Data Pin</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($analytics['total_transactions']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Successful Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($analytics['successful_transactions']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₦{{ number_format($analytics['total_revenue'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-naira-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Profit
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₦{{ number_format($analytics['total_profit'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie"></i> Service Breakdown
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Transactions</th>
                                    <th>Successful</th>
                                    <th>Revenue</th>
                                    <th>Profit</th>
                                    <th>Success Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['service_breakdown'] as $serviceKey => $service)
                                    @if($service['transactions'] > 0)
                                    <tr>
                                        <td>
                                            <i class="fas fa-{{
                                                $serviceKey == 'airtime' ? 'phone' :
                                                ($serviceKey == 'data' ? 'wifi' :
                                                ($serviceKey == 'cable_tv' ? 'tv' :
                                                ($serviceKey == 'electricity' ? 'bolt' : 'receipt')))
                                            }}"></i>
                                            {{ $service['name'] }}
                                        </td>
                                        <td>{{ number_format($service['transactions']) }}</td>
                                        <td>
                                            <span class="badge badge-success">
                                                {{ number_format($service['successful']) }}
                                            </span>
                                        </td>
                                        <td>₦{{ number_format($service['revenue'], 2) }}</td>
                                        <td>₦{{ number_format($service['profit'], 2) }}</td>
                                        <td>
                                            @php
                                                $successRate = $service['transactions'] > 0 ?
                                                    ($service['successful'] / $service['transactions']) * 100 : 0;
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar
                                                    {{ $successRate >= 90 ? 'bg-success' :
                                                       ($successRate >= 70 ? 'bg-warning' : 'bg-danger') }}"
                                                     style="width: {{ $successRate }}%">
                                                    {{ number_format($successRate, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exclamation-triangle"></i> Failed Transactions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h2 mb-0 text-danger">
                            {{ number_format($analytics['failed_transactions']) }}
                        </div>
                        <p class="text-muted">Failed Transactions</p>
                        @php
                            $failureRate = $analytics['total_transactions'] > 0 ?
                                ($analytics['failed_transactions'] / $analytics['total_transactions']) * 100 : 0;
                        @endphp
                        <div class="progress mb-3">
                            <div class="progress-bar bg-danger"
                                 style="width: {{ $failureRate }}%">
                                {{ number_format($failureRate, 1) }}%
                            </div>
                        </div>
                        <small class="text-muted">Failure Rate</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history"></i> Recent Transactions
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Ref</th>
                            <th>User</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                        <tr>
                            <td>
                                <small class="text-muted">{{ $transaction->transref }}</small>
                            </td>
                            <td>
                                @if($transaction->user)
                                    <div>
                                        <strong>{{ $transaction->user->sUserName }}</strong><br>
                                        <small class="text-muted">{{ $transaction->user->sEmail }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Unknown User</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $transaction->servicename }}
                                </span>
                            </td>
                            <td>₦{{ number_format(floatval($transaction->amount), 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $transaction->status == 0 ? 'success' : 'danger' }}">
                                    {{ $transaction->status_text }}
                                </span>
                            </td>
                            <td>{{ $transaction->formatted_date }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        onclick="viewTransaction({{ $transaction->tId }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                No transactions found for the selected criteria
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="transactionDetails">
                <!-- Transaction details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function resetFilters() {
    window.location.href = "{{ route('admin.transactions.general-analysis') }}";
}

function exportTransactions() {
    const form = document.getElementById('filterForm');
    const exportUrl = "{{ route('admin.transactions.export') }}?" + new URLSearchParams(new FormData(form)).toString();
    window.open(exportUrl, '_blank');
}

function viewTransaction(transactionId) {
    // Show loading
    document.getElementById('transactionDetails').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Loading transaction details...</p>
        </div>
    `;

    $('#transactionModal').modal('show');

    // Fetch transaction details
    fetch(`/admin/transactions/${transactionId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const transaction = data.data;
                document.getElementById('transactionDetails').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Transaction Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Reference:</strong></td>
                                    <td>${transaction.reference}</td>
                                </tr>
                                <tr>
                                    <td><strong>Service:</strong></td>
                                    <td>${transaction.service}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td>${transaction.formatted_amount}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-${transaction.status === 0 ? 'success' : 'danger'}">
                                            ${transaction.status_text}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td>${transaction.formatted_date}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">User Information</h6>
                            ${transaction.user ? `
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>${transaction.user.name}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>${transaction.user.email}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>${transaction.user.phone}</td>
                                    </tr>
                                </table>
                            ` : '<p class="text-muted">User information not available</p>'}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold">Description</h6>
                            <p class="text-muted">${transaction.description}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Balance Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Old Balance:</strong></td>
                                    <td>₦${parseFloat(transaction.old_balance).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td><strong>New Balance:</strong></td>
                                    <td>₦${parseFloat(transaction.new_balance).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td><strong>Profit:</strong></td>
                                    <td>₦${parseFloat(transaction.profit).toLocaleString()}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;
            } else {
                document.getElementById('transactionDetails').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('transactionDetails').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error loading transaction details
                </div>
            `;
        });
}
</script>
@endsection
