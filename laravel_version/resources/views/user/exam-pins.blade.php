@extends('layouts.app')

@section('title', 'Exam Pins - VASTLEAD')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="h3 mb-1 text-dark">Exam Pin Purchase</h1>
                        <p class="text-muted mb-0">Purchase exam pins for WAEC, NECO, JAMB, NABTEB and other educational examinations</p>
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
                        <i class="fas fa-graduation-cap me-2"></i>
                        Purchase Exam Pins
                    </h5>
                </div>
                <div class="card-body">
                    <form id="examPinPurchaseForm">
                        <!-- Exam Provider Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="examProvider" class="form-label">
                                    <i class="fas fa-school text-primary me-1"></i>
                                    Exam Provider
                                </label>
                                <select class="form-select" id="examProvider" name="provider" required>
                                    <option value="">Select Exam Provider</option>
                                    @foreach($examProviders as $provider)
                                    <option value="{{ $provider->ePlan }}"
                                            data-price="{{ $provider->getUserPrice(auth()->user()->sType ?? 'user') }}"
                                            data-description="{{ $provider->description }}">
                                        {{ strtoupper($provider->ePlan) }} - ₦{{ number_format($provider->getUserPrice(auth()->user()->sType ?? 'user'), 2) }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text" id="examDescription"></div>
                            </div>
                            <div class="col-md-3">
                                <label for="quantity" class="form-label">
                                    <i class="fas fa-calculator text-primary me-1"></i>
                                    Quantity
                                </label>
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                       min="1" max="50" value="1" required>
                                <div class="form-text">Max: 50 pins per transaction</div>
                            </div>
                            <div class="col-md-3">
                                <label for="phoneNumber" class="form-label">
                                    <i class="fas fa-phone text-primary me-1"></i>
                                    Phone Number
                                </label>
                                <input type="text" class="form-control" id="phoneNumber" name="phone"
                                       value="{{ auth()->user()->sPhone ?? '' }}"
                                       pattern="[0-9]{11}" maxlength="11" required>
                                <div class="form-text">11-digit phone number</div>
                            </div>
                        </div>

                        <!-- Pricing Display -->
                        <div class="row mb-4" id="pricingSection" style="display: none;">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="fas fa-money-bill text-success me-1"></i>
                                            Pricing Breakdown
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-muted small">Unit Price</div>
                                                    <div class="h6 mb-0" id="unitPrice">₦0.00</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-muted small">Quantity</div>
                                                    <div class="h6 mb-0" id="displayQuantity">1</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="text-muted small">Total Amount</div>
                                                    <div class="h5 mb-0 text-success" id="totalAmount">₦0.00</div>
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
                                    Purchase Exam Pins
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
            <!-- Available Exam Providers -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Available Exam Providers
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($examProviders as $provider)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <img src="{{ $provider->logo_path }}" alt="{{ $provider->ePlan }}"
                                 class="rounded me-2" width="24" height="24"
                                 onerror="this.src='/assets/images/exam-default.png'">
                            <div>
                                <strong>{{ strtoupper($provider->ePlan) }}</strong>
                                <br>
                                <small class="text-muted">{{ $provider->description }}</small>
                            </div>
                        </div>
                        <span class="badge bg-success">
                            ₦{{ number_format($provider->getUserPrice(auth()->user()->sType ?? 'user'), 2) }}
                        </span>
                    </div>
                    @if(!$loop->last)<hr>@endif
                    @endforeach
                </div>
            </div>

            <!-- Instructions -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        How to Use Exam Pins
                    </h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Select your exam provider (WAEC, NECO, JAMB, etc.)</li>
                        <li class="mb-2">Enter the quantity of pins you need</li>
                        <li class="mb-2">Provide your phone number for notifications</li>
                        <li class="mb-2">Enter your transaction PIN</li>
                        <li class="mb-2">Click purchase to buy your exam pins</li>
                        <li class="mb-0">Use the generated PIN and Serial numbers for your exam registration</li>
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
                        Recent Exam Pin Purchases
                    </h6>
                    <a href="{{ route('exam-pins.history') }}" class="btn btn-light btn-sm">
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
                                    <th>Exam Provider</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransactions">
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
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
                    Exam Pins Purchased Successfully
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="text-success mt-2">Purchase Successful!</h4>
                    <p class="text-muted">Your exam pins have been generated successfully</p>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Transaction Reference:</strong>
                        <span id="successReference" class="text-muted"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Amount Paid:</strong>
                        <span id="successAmount" class="text-success"></span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Your Exam Pins</h6>
                    </div>
                    <div class="card-body" id="examPinsDisplay">
                        <!-- Pins will be populated here -->
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <h6 class="alert-heading">Important Instructions:</h6>
                    <ul class="mb-0">
                        <li>Keep your PIN and Serial numbers safe</li>
                        <li>Both PIN and Serial are required for exam registration</li>
                        <li>Each pin can only be used once</li>
                        <li>Screenshot or print this information for future reference</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="printPins()">
                    <i class="fas fa-print me-2"></i>
                    Print Pins
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

    // Update pricing when provider, quantity changes
    $('#examProvider, #quantity').on('change input', updatePricing);

    // Form submission
    $('#examPinPurchaseForm').on('submit', function(e) {
        e.preventDefault();
        purchaseExamPins();
    });

    // Update exam description when provider changes
    $('#examProvider').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const description = selectedOption.data('description');
        $('#examDescription').text(description || '');
    });
});

function updatePricing() {
    const provider = $('#examProvider').val();
    const quantity = parseInt($('#quantity').val()) || 1;
    const selectedOption = $('#examProvider option:selected');
    const unitPrice = parseFloat(selectedOption.data('price')) || 0;

    if (provider && unitPrice > 0) {
        const totalAmount = unitPrice * quantity;

        $('#unitPrice').text('₦' + unitPrice.toLocaleString('en-NG', {minimumFractionDigits: 2}));
        $('#displayQuantity').text(quantity);
        $('#totalAmount').text('₦' + totalAmount.toLocaleString('en-NG', {minimumFractionDigits: 2}));

        $('#pricingSection').show();
        $('#purchaseBtn').prop('disabled', false);

        // Update wallet balance validation
        const walletBalance = parseFloat($('#walletBalance').text().replace('₦', '').replace(',', '')) || 0;
        if (totalAmount > walletBalance) {
            $('#purchaseBtn').prop('disabled', true);
            $('#totalAmount').addClass('text-danger').removeClass('text-success');
            $('#pricingSection .card').addClass('border-danger');
        } else {
            $('#totalAmount').addClass('text-success').removeClass('text-danger');
            $('#pricingSection .card').removeClass('border-danger');
        }
    } else {
        $('#pricingSection').hide();
        $('#purchaseBtn').prop('disabled', true);
    }
}

function purchaseExamPins() {
    const btn = $('#purchaseBtn');
    const originalText = btn.html();

    // Disable button and show loading
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');

    const formData = new FormData(document.getElementById('examPinPurchaseForm'));

    $.ajax({
        url: '{{ route("exam-pins.purchase") }}',
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
                const newBalance = parseFloat($('#walletBalance').text().replace('₦', '').replace(',', '')) - response.data.total_amount;
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
    $('#successAmount').text('₦' + parseFloat(data.total_amount).toLocaleString('en-NG', {minimumFractionDigits: 2}));

    // Display exam pins
    let pinsHtml = '';
    if (data.pins && data.pins.length > 0) {
        data.pins.forEach((pin, index) => {
            pinsHtml += `
                <div class="border rounded p-3 mb-2">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>PIN ${index + 1}:</strong>
                            <code class="fs-6">${pin.pin}</code>
                        </div>
                        <div class="col-md-6">
                            <strong>Serial:</strong>
                            <code class="fs-6">${pin.serial}</code>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        pinsHtml = '<p class="text-muted">Pin details will be sent via SMS and email.</p>';
    }

    $('#examPinsDisplay').html(pinsHtml);
    $('#successModal').modal('show');
}

function resetForm() {
    document.getElementById('examPinPurchaseForm').reset();
    $('#pricingSection').hide();
    $('#purchaseBtn').prop('disabled', true);
    $('#examDescription').text('');
}

function loadRecentTransactions() {
    $.ajax({
        url: '{{ route("exam-pins.history") }}',
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
                            <td>${transaction.exam_type}</td>
                            <td>${transaction.details?.quantity || 1}</td>
                            <td>₦${parseFloat(transaction.amount).toLocaleString()}</td>
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
                        <td colspan="7" class="text-center text-muted">
                            No recent transactions found
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#recentTransactions').html(`
                <tr>
                    <td colspan="7" class="text-center text-muted">
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

function printPins() {
    const printContent = document.getElementById('examPinsDisplay').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Exam Pins - ${$('#successReference').text()}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .pin-block { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
                    code { background-color: #f8f9fa; padding: 5px; border-radius: 3px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>VASTLEAD - Exam Pins</h2>
                    <p>Reference: ${$('#successReference').text()}</p>
                    <p>Amount: ${$('#successAmount').text()}</p>
                </div>
                ${printContent}
                <p><small>Generated on: ${new Date().toLocaleString()}</small></p>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endsection
