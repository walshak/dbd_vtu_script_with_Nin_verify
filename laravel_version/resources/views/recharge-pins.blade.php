@extends('layouts.app')

@section('title', 'Recharge Pins')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">Recharge Pins</h5>
                            <p class="text-sm mb-0">
                                Purchase recharge pins for all major networks with instant delivery
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-0">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Purchase Recharge Pin</h6>
                                </div>
                                <div class="card-body">
                                    <form id="rechargePinForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Network Provider</label>
                                                <select class="form-select" id="network" name="network" required>
                                                    <option value="">Select Network</option>
                                                    <option value="MTN">MTN</option>
                                                    <option value="GLO">Glo</option>
                                                    <option value="AIRTEL">Airtel</option>
                                                    <option value="9MOBILE">9mobile</option>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Denomination (₦)</label>
                                                <select class="form-select" id="denomination" name="denomination" required>
                                                    <option value="">Select Amount</option>
                                                    <option value="100">₦100</option>
                                                    <option value="200">₦200</option>
                                                    <option value="400">₦400</option>
                                                    <option value="500">₦500</option>
                                                    <option value="750">₦750</option>
                                                    <option value="1000">₦1,000</option>
                                                    <option value="1500">₦1,500</option>
                                                    <option value="2000">₦2,000</option>
                                                    <option value="2500">₦2,500</option>
                                                    <option value="3000">₦3,000</option>
                                                    <option value="4000">₦4,000</option>
                                                    <option value="5000">₦5,000</option>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Quantity</label>
                                                <select class="form-select" id="quantity" name="quantity" required>
                                                    <option value="">Select Quantity</option>
                                                    @for($i = 1; $i <= 10; $i++)
                                                        <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Pin' : 'Pins' }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Business Name (Optional)</label>
                                                <input type="text" class="form-control" id="businessName" name="business_name" 
                                                       placeholder="Enter business name for pin customization">
                                            </div>
                                            
                                            <div class="col-12 mb-3">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-title">Pricing Information</h6>
                                                        <div id="pricingInfo" class="d-none">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Face Value:</strong> <span id="faceValue">₦0</span></p>
                                                                    <p class="mb-1"><strong>You Pay:</strong> <span id="amountToPay" class="text-success">₦0</span></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Total Savings:</strong> <span id="totalSavings" class="text-primary">₦0</span></p>
                                                                    <p class="mb-1"><strong>Discount:</strong> <span id="discountPercent" class="text-info">0%</span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted mb-0" id="selectOptions">Please select network, denomination and quantity to see pricing</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Transaction PIN</label>
                                                <input type="password" class="form-control" id="transactionPin" name="transaction_pin" 
                                                       maxlength="4" placeholder="Enter 4-digit PIN" required>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                                    <label class="form-check-label" for="agreeTerms">
                                                        I agree to the terms and conditions for recharge pin purchase
                                                    </label>
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary" id="purchaseBtn">
                                                    <i class="fas fa-credit-card"></i> Purchase Recharge Pin(s)
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Account Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon icon-shape icon-sm bg-gradient-primary text-white me-2">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm mb-0">Wallet Balance</p>
                                            <h6 class="mb-0">₦{{ number_format(auth()->user()->sWallet ?? 0, 2) }}</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon icon-shape icon-sm bg-gradient-success text-white me-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm mb-0">Account Type</p>
                                            <h6 class="mb-0">{{ ucfirst(auth()->user()->sType ?? 'User') }}</h6>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <h6 class="mb-2">Features</h6>
                                    <ul class="list-unstyled text-sm">
                                        <li><i class="fas fa-check text-success me-2"></i>Instant Pin Generation</li>
                                        <li><i class="fas fa-check text-success me-2"></i>All Networks Supported</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Bulk Purchase Available</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Print-Ready Format</li>
                                        <li><i class="fas fa-check text-success me-2"></i>24/7 Support</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Recent Recharge Pin Purchases</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-flush" id="recentTransactions">
                            <thead class="thead-light">
                                <tr>
                                    <th>Reference</th>
                                    <th>Network</th>
                                    <th>Amount</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Recent transactions will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Purchase Successful
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="pinResults"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printPins()">
                    <i class="fas fa-print"></i> Print Pins
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load recent transactions
    loadRecentTransactions();
    
    // Calculate pricing when inputs change
    $('#network, #denomination, #quantity').change(calculatePricing);
    
    // Form submission
    $('#rechargePinForm').submit(function(e) {
        e.preventDefault();
        purchaseRechargePin();
    });
    
    // Format transaction PIN input
    $('#transactionPin').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
    });
});

function calculatePricing() {
    const network = $('#network').val();
    const denomination = $('#denomination').val();
    const quantity = $('#quantity').val();
    
    if (!network || !denomination || !quantity) {
        $('#pricingInfo').addClass('d-none');
        $('#selectOptions').show();
        return;
    }
    
    $.ajax({
        url: '{{ route("recharge-pins.pricing") }}',
        method: 'POST',
        data: {
            network: network,
            denomination: denomination,
            quantity: quantity,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                const data = response.data;
                
                $('#faceValue').text('₦' + (data.denomination * data.quantity).toLocaleString());
                $('#amountToPay').text('₦' + data.total_amount.toLocaleString());
                $('#totalSavings').text('₦' + ((data.denomination * data.quantity) - data.total_amount).toLocaleString());
                $('#discountPercent').text(Math.round(((data.denomination * data.quantity - data.total_amount) / (data.denomination * data.quantity)) * 100) + '%');
                
                $('#pricingInfo').removeClass('d-none');
                $('#selectOptions').hide();
            }
        },
        error: function(xhr) {
            console.error('Pricing calculation failed:', xhr.responseJSON);
        }
    });
}

function purchaseRechargePin() {
    const btn = $('#purchaseBtn');
    const originalText = btn.html();
    
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    
    $.ajax({
        url: '{{ route("recharge-pins.purchase") }}',
        method: 'POST',
        data: $('#rechargePinForm').serialize(),
        success: function(response) {
            if (response.status === 'success') {
                showSuccessModal(response.data);
                $('#rechargePinForm')[0].reset();
                $('#pricingInfo').addClass('d-none');
                $('#selectOptions').show();
                loadRecentTransactions();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert('Error: ' + (response?.message || 'Purchase failed'));
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
}

function showSuccessModal(data) {
    let html = `
        <div class="alert alert-success">
            <h6>Transaction Successful!</h6>
            <p><strong>Reference:</strong> ${data.reference}</p>
            <p><strong>Network:</strong> ${data.network}</p>
            <p><strong>Denomination:</strong> ₦${data.denomination.toLocaleString()}</p>
            <p><strong>Quantity:</strong> ${data.quantity}</p>
            <p><strong>Total Amount:</strong> ₦${data.total_amount.toLocaleString()}</p>
        </div>
    `;
    
    if (data.pins && data.pins.length > 0) {
        html += '<div class="mt-3"><h6>Your Recharge Pins:</h6>';
        html += '<div class="table-responsive">';
        html += '<table class="table table-sm table-bordered">';
        html += '<thead><tr><th>S/N</th><th>PIN</th><th>Serial</th><th>Load Code</th></tr></thead><tbody>';
        
        data.pins.forEach((pin, index) => {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><code>${pin.pin}</code></td>
                    <td><code>${pin.serial}</code></td>
                    <td><code>${pin.load_pin}</code></td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        html += '<div class="alert alert-info mt-2">';
        html += '<small><strong>Instructions:</strong> ' + (data.instructions || 'Use the load code format with your PIN to recharge.') + '</small>';
        html += '</div></div>';
    }
    
    $('#pinResults').html(html);
    $('#successModal').modal('show');
}

function loadRecentTransactions() {
    $.ajax({
        url: '{{ route("recharge-pins.history") }}',
        method: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                let html = '';
                response.data.forEach(function(transaction) {
                    const statusBadge = transaction.status === 'successful' ? 'bg-success' : 
                                      transaction.status === 'failed' ? 'bg-danger' : 'bg-warning';
                    
                    html += `
                        <tr>
                            <td><code>${transaction.reference}</code></td>
                            <td>${transaction.network}</td>
                            <td>₦${transaction.amount.toLocaleString()}</td>
                            <td>${transaction.details?.quantity || 1}</td>
                            <td><span class="badge ${statusBadge}">${transaction.status}</span></td>
                            <td>${new Date(transaction.date).toLocaleDateString()}</td>
                            <td>
                                ${transaction.status === 'successful' ? 
                                    `<button class="btn btn-sm btn-outline-primary" onclick="viewPins('${transaction.reference}')">
                                        <i class="fas fa-eye"></i> View
                                    </button>` : 
                                    '<span class="text-muted">N/A</span>'
                                }
                            </td>
                        </tr>
                    `;
                });
                
                $('#recentTransactions tbody').html(html || '<tr><td colspan="7" class="text-center">No transactions found</td></tr>');
            }
        },
        error: function(xhr) {
            console.error('Failed to load transactions:', xhr.responseJSON);
        }
    });
}

function viewPins(reference) {
    // Implement pin viewing functionality
    alert('View pins for reference: ' + reference);
}

function printPins() {
    window.print();
}
</script>
@endpush
