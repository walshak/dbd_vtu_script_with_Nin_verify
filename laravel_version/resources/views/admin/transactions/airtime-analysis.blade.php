@extends('layouts.admin')

@section('title', 'Airtime Sales Analysis')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-phone"></i> Airtime Sales Analysis
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.transactions.general-analysis') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to General Analysis
            </a>
            <button class="btn btn-primary" onclick="exportAirtimeData()">
                <i class="fas fa-download"></i> Export Airtime Data
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Airtime Analysis Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control"
                               value="{{ $dateFrom }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control"
                               value="{{ $dateTo }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fas fa-undo"></i> Reset Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Airtime Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Airtime Sales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($analytics['total_transactions']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-phone fa-2x text-gray-300"></i>
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
                                Revenue Generated
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₦{{ number_format($metrics['total_volume'], 2) }}
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Profit Earned
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₦{{ number_format($metrics['total_profit'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                Success Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['success_rate'], 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Network Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Network Provider Breakdown
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $networkStats = [];
                        foreach($transactions as $transaction) {
                            if($transaction->status == 0) { // Success only
                                // Extract network from description
                                $description = $transaction->servicedesc;
                                if(stripos($description, 'MTN') !== false) {
                                    $network = 'MTN';
                                } elseif(stripos($description, 'GLO') !== false) {
                                    $network = 'GLO';
                                } elseif(stripos($description, 'AIRTEL') !== false) {
                                    $network = 'AIRTEL';
                                } elseif(stripos($description, '9MOBILE') !== false) {
                                    $network = '9MOBILE';
                                } else {
                                    $network = 'Other';
                                }

                                if(!isset($networkStats[$network])) {
                                    $networkStats[$network] = [
                                        'count' => 0,
                                        'revenue' => 0,
                                        'profit' => 0
                                    ];
                                }
                                $networkStats[$network]['count']++;
                                $networkStats[$network]['revenue'] += floatval($transaction->amount);
                                $networkStats[$network]['profit'] += $transaction->profit;
                            }
                        }
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Network</th>
                                    <th>Transactions</th>
                                    <th>Revenue</th>
                                    <th>Profit</th>
                                    <th>Market Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($networkStats as $network => $stats)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{
                                            $network == 'MTN' ? 'warning' :
                                            ($network == 'GLO' ? 'success' :
                                            ($network == 'AIRTEL' ? 'danger' : 'info'))
                                        }}">
                                            {{ $network }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($stats['count']) }}</td>
                                    <td>₦{{ number_format($stats['revenue'], 2) }}</td>
                                    <td>₦{{ number_format($stats['profit'], 2) }}</td>
                                    <td>
                                        @php
                                            $marketShare = $analytics['successful_transactions'] > 0 ?
                                                ($stats['count'] / $analytics['successful_transactions']) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-primary"
                                                 style="width: {{ $marketShare }}%">
                                                {{ number_format($marketShare, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
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
                        <i class="fas fa-calculator"></i> Quick Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Average Transaction:</span>
                            <strong>₦{{ number_format($metrics['average_transaction'], 2) }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Profit Margin:</span>
                            @php
                                $profitMargin = $metrics['total_volume'] > 0 ?
                                    ($metrics['total_profit'] / $metrics['total_volume']) * 100 : 0;
                            @endphp
                            <strong>{{ number_format($profitMargin, 2) }}%</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Failed Transactions:</span>
                            <strong class="text-danger">
                                {{ number_format($analytics['failed_transactions']) }}
                            </strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Customers:</span>
                            <strong>
                                {{ $transactions->unique('sId')->count() }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Airtime Transactions -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history"></i> Recent Airtime Transactions
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Network/Amount</th>
                            <th>Status</th>
                            <th>Profit</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions->take(20) as $transaction)
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
                                <div>
                                    @php
                                        $description = $transaction->servicedesc;
                                        if(stripos($description, 'MTN') !== false) $networkClass = 'warning';
                                        elseif(stripos($description, 'GLO') !== false) $networkClass = 'success';
                                        elseif(stripos($description, 'AIRTEL') !== false) $networkClass = 'danger';
                                        elseif(stripos($description, '9MOBILE') !== false) $networkClass = 'info';
                                        else $networkClass = 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $networkClass }}">
                                        @if(stripos($description, 'MTN') !== false) MTN
                                        @elseif(stripos($description, 'GLO') !== false) GLO
                                        @elseif(stripos($description, 'AIRTEL') !== false) AIRTEL
                                        @elseif(stripos($description, '9MOBILE') !== false) 9MOBILE
                                        @else Unknown @endif
                                    </span><br>
                                    <strong>₦{{ number_format(floatval($transaction->amount), 2) }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $transaction->status == 0 ? 'success' : 'danger' }}">
                                    {{ $transaction->status_text }}
                                </span>
                            </td>
                            <td>
                                <span class="text-success">
                                    ₦{{ number_format($transaction->profit, 2) }}
                                </span>
                            </td>
                            <td>{{ $transaction->formatted_date }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        onclick="viewAirtimeTransaction({{ $transaction->tId }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($transaction->status == 1)
                                <button class="btn btn-sm btn-outline-warning"
                                        onclick="reverseTransaction({{ $transaction->tId }})">
                                    <i class="fas fa-undo"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-phone fa-3x mb-3"></i><br>
                                No airtime transactions found for the selected period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="airtimeTransactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Airtime Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="airtimeTransactionDetails">
                <!-- Transaction details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printTransaction()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function resetFilters() {
    window.location.href = "{{ route('admin.transactions.airtime-analysis') }}";
}

function exportAirtimeData() {
    const form = document.getElementById('filterForm');
    const exportUrl = "{{ route('admin.transactions.export') }}?service_type=Airtime&" +
                     new URLSearchParams(new FormData(form)).toString();
    window.open(exportUrl, '_blank');
}

function viewAirtimeTransaction(transactionId) {
    // Show loading
    document.getElementById('airtimeTransactionDetails').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Loading airtime transaction details...</p>
        </div>
    `;

    $('#airtimeTransactionModal').modal('show');

    // Fetch transaction details
    fetch(`/admin/transactions/${transactionId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const transaction = data.data;
                document.getElementById('airtimeTransactionDetails').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary">
                                <i class="fas fa-phone"></i> Airtime Purchase Details
                            </h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Reference:</strong></td>
                                    <td>${transaction.reference}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td class="text-success"><strong>${transaction.formatted_amount}</strong></td>
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
                                    <td><strong>Profit:</strong></td>
                                    <td class="text-info">₦${parseFloat(transaction.profit).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td>${transaction.formatted_date}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary">
                                <i class="fas fa-user"></i> Customer Information
                            </h6>
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
                            ` : '<p class="text-muted">Customer information not available</p>'}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-primary">
                                <i class="fas fa-info-circle"></i> Transaction Description
                            </h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">${transaction.description}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary">
                                <i class="fas fa-wallet"></i> Wallet Balance
                            </h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Before Transaction:</strong></td>
                                    <td>₦${parseFloat(transaction.old_balance).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td><strong>After Transaction:</strong></td>
                                    <td>₦${parseFloat(transaction.new_balance).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount Deducted:</strong></td>
                                    <td class="text-danger">₦${parseFloat(transaction.amount).toLocaleString()}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;
            } else {
                document.getElementById('airtimeTransactionDetails').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('airtimeTransactionDetails').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error loading transaction details
                </div>
            `;
        });
}

function reverseTransaction(transactionId) {
    if (!confirm('Are you sure you want to reverse this transaction? The amount will be refunded to the customer.')) {
        return;
    }

    const reason = prompt('Please enter the reason for reversal:');
    if (!reason) {
        return;
    }

    fetch('/admin/transactions/reverse', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            transaction_id: transactionId,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Transaction reversed successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error reversing transaction');
    });
}

function printTransaction() {
    const content = document.getElementById('airtimeTransactionDetails').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Airtime Transaction Details</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .badge { padding: 3px 6px; border-radius: 3px; }
                .badge-success { background-color: #28a745; color: white; }
                .badge-danger { background-color: #dc3545; color: white; }
            </style>
        </head>
        <body>
            <h2>Airtime Transaction Details</h2>
            ${content}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endsection
