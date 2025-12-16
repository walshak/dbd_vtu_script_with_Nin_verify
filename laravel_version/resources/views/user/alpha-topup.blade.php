@extends('layouts.app')

@section('title', 'Alpha Topup - VASTLEAD')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="h3 mb-1 text-dark">Alpha Topup Purchase</h1>
                        <p class="text-muted mb-0">Purchase alpha topup credits for various digital services and transactions</p>
                    </div>
                    <div class="text-end">
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-2">Wallet Balance:</span>
                            <span class="h5 mb-0 text-success" id="walletBalance">
                                ₦{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Purchase Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-coins me-2"></i>
                        Purchase Alpha Topup
                    </h5>
                </div>
                <div class="card-body">
                    <form id="alphaTopupPurchaseForm">
                        <!-- Amount Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="alphaAmount" class="form-label">
                                    <i class="fas fa-money-bill text-primary me-1"></i>
                                    Select Amount
                                </label>
                                <select class="form-select" id="alphaAmount" name="amount" required>
                                    <option value="">Select Alpha Topup Amount</option>
                                    @foreach($alphaProviders as $provider)
                                    <option value="{{ $provider->sellingPrice }}"
                                            data-user-price="{{ $provider->getUserPrice(auth()->user()->sType ?? 'user') }}"
                                            data-discount="{{ $provider->getDiscountPercentage(auth()->user()->sType ?? 'user') }}"
                                            data-id="{{ $provider->alphaId }}">
                                        ₦{{ number_format($provider->sellingPrice, 2) }}
                                        @if($provider->hasDiscount(auth()->user()->sType ?? 'user'))
                                            - Pay: ₦{{ number_format($provider->getUserPrice(auth()->user()->sType ?? 'user'), 2) }}
                                            ({{ $provider->getDiscountPercentage(auth()->user()->sType ?? 'user') }}% off)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Choose from available alpha topup amounts</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phoneNumber" class="form-label">
                                    <i class="fas fa-phone text-primary me-1"></i>
                                    Phone Number
                                </label>
                                <input type="text" class="form-control" id="phoneNumber" name="phone"
                                       value="{{ auth()->user()->sPhone ?? '' }}"
                                       pattern="[0-9]{11}" maxlength="11" required>
                                <div class="form-text">11-digit phone number for topup delivery</div>
                            </div>
                        </div>

                        <!-- Pricing Display -->
                        <div class="row mb-4" id="pricingSection" style="display: none;">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="fas fa-calculator text-success me-1"></i>
                                            Pricing Breakdown
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-muted small">Alpha Amount</div>
                                                    <div class="h6 mb-0" id="alphaAmountDisplay">₦0.00</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-muted small">You Pay</div>
                                                    <div class="h5 mb-0 text-success" id="youPayAmount">₦0.00</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-muted small">Discount</div>
                                                    <div class="h6 mb-0 text-info" id="discountDisplay">0%</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-muted small">Account Type</div>
                                                    <div class="h6 mb-0">{{ ucfirst(auth()->user()->sType ?? 'user') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction PIN -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="transactionPin" class="form-label">
                                    <i class="fas fa-lock text-primary me-1"></i>
                                    Transaction PIN
                                </label>
                                <input type="password" class="form-control" id="transactionPin"
                                       name="transaction_pin" maxlength="4" pattern="[0-9]{4}" required>
                                <div class="form-text">Enter your 4-digit transaction PIN</div>
                            </div>
                        </div>

                        <!-- Purchase Button -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg" id="purchaseBtn" disabled>
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Purchase Alpha Topup
                                </button>
                                <button type="button" class="btn btn-secondary btn-lg ms-2" onclick="resetForm()">
                                    <i class="fas fa-redo me-2"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="col-lg-4">
            <!-- Available Plans -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Available Alpha Plans
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($alphaProviders as $provider)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-coins text-warning me-2"></i>
                            <div>
                                <strong>₦{{ number_format($provider->sellingPrice, 2) }}</strong>
                                @if($provider->hasDiscount(auth()->user()->sType ?? 'user'))
                                <br>
                                <small class="text-success">
                                    Pay: ₦{{ number_format($provider->getUserPrice(auth()->user()->sType ?? 'user'), 2) }}
                                    ({{ $provider->getDiscountPercentage(auth()->user()->sType ?? 'user') }}% off)
                                </small>
                                @endif
                            </div>
                        </div>
                        <span class="badge bg-success">Available</span>
                    </div>
                    @if(!$loop->last)<hr>@endif
                    @endforeach
                </div>
            </div>

            <!-- Alpha Topup Benefits -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>
                        Alpha Topup Benefits
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Instant delivery and activation
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            No expiry date on credits
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Can be used for any transaction
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Transferable to other users
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            24/7 customer support
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        How to Purchase
                    </h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Select your desired alpha topup amount</li>
                        <li class="mb-2">Enter your phone number for delivery</li>
                        <li class="mb-2">Review the pricing and discount information</li>
                        <li class="mb-2">Enter your transaction PIN</li>
                        <li class="mb-2">Click purchase to complete the transaction</li>
                        <li class="mb-0">Your alpha credits will be delivered instantly</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Recent Alpha Topup Purchases
                    </h6>
                    <a href="{{ route('alpha-topup.history') }}" class="btn btn-light btn-sm">
                        View All History
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="recentTransactionsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransactions">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-spinner fa-spin me-2"></i>
                                        Loading recent transactions...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="fas fa-check-circle me-2"></i>
                    Alpha Topup Successful
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="text-success mt-2">Purchase Successful!</h4>
                    <p class="text-muted">Your alpha topup has been processed successfully</p>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Transaction Reference:</strong>
                                <br><span id="successReference" class="text-muted"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Amount Paid:</strong>
                                <br><span id="successAmount" class="text-success"></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Alpha Amount:</strong>
                                <br><span id="successAlphaAmount" class="text-primary"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Phone Number:</strong>
                                <br><span id="successPhone" class="text-muted"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <h6 class="alert-heading">Transaction Complete!</h6>
                    <ul class="mb-0">
                        <li>Your alpha credits have been delivered instantly</li>
                        <li>You can use these credits for various digital services</li>
                        <li>Credits do not expire and can be transferred</li>
                        <li>Check your alpha balance anytime through our platform</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="checkAlphaBalance()">
                    <i class="fas fa-balance-scale me-2"></i>
                    Check Balance
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    loadRecentTransactions();
    updatePricing();

    // Update pricing when amount changes
    $('#alphaAmount').on('change', updatePricing);

    // Form submission
    $('#alphaTopupPurchaseForm').on('submit', function(e) {
        e.preventDefault();
        purchaseAlphaTopup();
    });
});

function updatePricing() {
    const amount = $('#alphaAmount').val();
    const selectedOption = $('#alphaAmount option:selected');
    const userPrice = parseFloat(selectedOption.data('user-price')) || 0;
    const discount = parseFloat(selectedOption.data('discount')) || 0;

    if (amount && userPrice > 0) {
        $('#alphaAmountDisplay').text('₦' + parseFloat(amount).toLocaleString('en-NG', {minimumFractionDigits: 2}));
        $('#youPayAmount').text('₦' + userPrice.toLocaleString('en-NG', {minimumFractionDigits: 2}));
        $('#discountDisplay').text(discount.toFixed(1) + '%');

        $('#pricingSection').show();
        $('#purchaseBtn').prop('disabled', false);

        // Update wallet balance validation
        const walletBalance = parseFloat($('#walletBalance').text().replace('₦', '').replace(',', '')) || 0;
        if (userPrice > walletBalance) {
            $('#purchaseBtn').prop('disabled', true);
            $('#youPayAmount').addClass('text-danger').removeClass('text-success');
            $('#pricingSection .card').addClass('border-danger');
        } else {
            $('#youPayAmount').addClass('text-success').removeClass('text-danger');
            $('#pricingSection .card').removeClass('border-danger');
        }
    } else {
        $('#pricingSection').hide();
        $('#purchaseBtn').prop('disabled', true);
    }
}

function purchaseAlphaTopup() {
    const btn = $('#purchaseBtn');
    const originalText = btn.html();

    // Disable button and show loading
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');

    const formData = new FormData(document.getElementById('alphaTopupPurchaseForm'));

    $.ajax({
        url: '{{ route("alpha-topup.purchase") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                displaySuccessModal(response.data);
                resetForm();
                loadRecentTransactions();

                // Update wallet balance
                const selectedOption = $('#alphaAmount option:selected');
                const userPrice = parseFloat(selectedOption.data('user-price')) || 0;
                const newBalance = parseFloat($('#walletBalance').text().replace('₦', '').replace(',', '')) - userPrice;
                $('#walletBalance').text('₦' + newBalance.toLocaleString('en-NG', {minimumFractionDigits: 2}));
            } else {
                alert(response.message || 'Purchase failed. Please try again.');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'An error occurred. Please try again.');
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
}

function displaySuccessModal(data) {
    $('#successReference').text(data.reference);
    $('#successAmount').text('₦' + parseFloat(data.amount).toLocaleString('en-NG', {minimumFractionDigits: 2}));
    $('#successAlphaAmount').text('₦' + parseFloat($('#alphaAmount').val()).toLocaleString('en-NG', {minimumFractionDigits: 2}));
    $('#successPhone').text(data.recipient_phone);

    $('#successModal').modal('show');
}

function resetForm() {
    document.getElementById('alphaTopupPurchaseForm').reset();
    $('#pricingSection').hide();
    $('#purchaseBtn').prop('disabled', true);
}

function loadRecentTransactions() {
    $.ajax({
        url: '{{ route("alpha-topup.history") }}',
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.status === 'success' && response.data.length > 0) {
                let html = '';
                response.data.slice(0, 5).forEach(transaction => {
                    const statusBadge = transaction.status === 'success' ?
                        '<span class="badge bg-success">Success</span>' :
                        '<span class="badge bg-danger">Failed</span>';

                    html += `
                        <tr>
                            <td>${new Date(transaction.date).toLocaleDateString()}</td>
                            <td><code>${transaction.reference}</code></td>
                            <td>₦${parseFloat(transaction.amount).toLocaleString()}</td>
                            <td>${transaction.recipient}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewTransactionDetails('${transaction.reference}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#recentTransactions').html(html);
            } else {
                $('#recentTransactions').html(`
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No recent transactions found
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#recentTransactions').html(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Error loading transactions
                    </td>
                </tr>
            `);
        }
    });
}

function viewTransactionDetails(reference) {
    // Implement transaction details view
    alert('Transaction details for: ' + reference);
}

function checkAlphaBalance() {
    const phone = $('#phoneNumber').val();
    if (!phone) {
        alert('Please enter a phone number first');
        return;
    }

    $.ajax({
        url: '{{ route("alpha-topup.balance") }}',
        method: 'GET',
        data: { phone: phone },
        success: function(response) {
            if (response.status === 'success') {
                alert(`Alpha Balance for ${phone}: ₦${response.data.balance}\nLast Updated: ${response.data.last_updated}`);
            } else {
                alert(response.message || 'Unable to check balance at this time');
            }
        },
        error: function() {
            alert('Error checking alpha balance. Please try again.');
        }
    });
}
</script>
@endsection
