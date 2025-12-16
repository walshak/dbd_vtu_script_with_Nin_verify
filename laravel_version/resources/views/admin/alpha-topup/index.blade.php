@extends('layouts.admin')

@section('title', 'Alpha Topup Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Alpha Topup Management</h1>
            <p class="text-muted">Manage alpha topup plans, pricing, and statistics</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAlphaTopupModal">
                <i class="fas fa-plus"></i> Add Alpha Plan
            </button>
            <button type="button" class="btn btn-success" onclick="exportAlphaTopups()">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Plans
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_plans'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
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
                                30-Day Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₦{{ number_format($statistics['total_revenue_30d'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Price Range
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₦{{ number_format($statistics['min_amount'], 0) }} - ₦{{ number_format($statistics['max_amount'], 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['success_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpha Topup Plans Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Alpha Topup Plans</h6>
            <div>
                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                    <i class="fas fa-edit"></i> Bulk Update
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="showPricingCalculator()">
                    <i class="fas fa-calculator"></i> Pricing Calculator
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="alphaTopupsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Selling Price</th>
                            <th>Buying Price</th>
                            <th>Agent Price</th>
                            <th>Vendor Price</th>
                            <th>Profit Margin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alphaTopups as $alphaTopup)
                        <tr>
                            <td>
                                <input type="checkbox" class="alpha-topup-checkbox" value="{{ $alphaTopup->alphaId }}">
                            </td>
                            <td>{{ $alphaTopup->alphaId }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-coins text-warning me-2"></i>
                                    <strong>₦{{ number_format($alphaTopup->sellingPrice, 2) }}</strong>
                                </div>
                            </td>
                            <td>₦{{ number_format($alphaTopup->sellingPrice, 2) }}</td>
                            <td>₦{{ number_format($alphaTopup->buyingPrice, 2) }}</td>
                            <td>₦{{ number_format($alphaTopup->agent, 2) }}</td>
                            <td>₦{{ number_format($alphaTopup->vendor, 2) }}</td>
                            <td>
                                @php
                                    $userProfit = $alphaTopup->sellingPrice - $alphaTopup->buyingPrice;
                                    $agentProfit = $alphaTopup->agent - $alphaTopup->buyingPrice;
                                    $vendorProfit = $alphaTopup->vendor - $alphaTopup->buyingPrice;
                                @endphp
                                <div class="small">
                                    <div><span class="badge bg-success">User: ₦{{ number_format($userProfit, 2) }}</span></div>
                                    <div><span class="badge bg-info">Agent: ₦{{ number_format($agentProfit, 2) }}</span></div>
                                    <div><span class="badge bg-warning">Vendor: ₦{{ number_format($vendorProfit, 2) }}</span></div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            onclick="editAlphaTopup({{ $alphaTopup->alphaId }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deleteAlphaTopup({{ $alphaTopup->alphaId }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Add Alpha Topup Modal -->
<div class="modal fade" id="addAlphaTopupModal" tabindex="-1" aria-labelledby="addAlphaTopupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAlphaTopupModalLabel">Add Alpha Topup Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAlphaTopupForm">
                    <div class="mb-3">
                        <label for="sellingPrice" class="form-label">Selling Price (₦)</label>
                        <input type="number" class="form-control" id="sellingPrice" name="selling_price" step="0.01" min="0" required>
                        <div class="form-text">The price customers will pay</div>
                    </div>
                    <div class="mb-3">
                        <label for="buyingPrice" class="form-label">Buying Price (₦)</label>
                        <input type="number" class="form-control" id="buyingPrice" name="buying_price" step="0.01" min="0" required>
                        <div class="form-text">The cost price from the provider</div>
                    </div>
                    <div class="mb-3">
                        <label for="agentPrice" class="form-label">Agent Price (₦)</label>
                        <input type="number" class="form-control" id="agentPrice" name="agent_price" step="0.01" min="0" required>
                        <div class="form-text">Special price for agents</div>
                    </div>
                    <div class="mb-3">
                        <label for="vendorPrice" class="form-label">Vendor Price (₦)</label>
                        <input type="number" class="form-control" id="vendorPrice" name="vendor_price" step="0.01" min="0" required>
                        <div class="form-text">Special price for vendors</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAlphaTopup()">Save Alpha Plan</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Alpha Topup Modal -->
<div class="modal fade" id="editAlphaTopupModal" tabindex="-1" aria-labelledby="editAlphaTopupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAlphaTopupModalLabel">Edit Alpha Topup Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAlphaTopupForm">
                    <input type="hidden" id="editAlphaTopupId" name="alpha_topup_id">
                    <div class="mb-3">
                        <label for="editSellingPrice" class="form-label">Selling Price (₦)</label>
                        <input type="number" class="form-control" id="editSellingPrice" name="selling_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="editBuyingPrice" class="form-label">Buying Price (₦)</label>
                        <input type="number" class="form-control" id="editBuyingPrice" name="buying_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="editAgentPrice" class="form-label">Agent Price (₦)</label>
                        <input type="number" class="form-control" id="editAgentPrice" name="agent_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="editVendorPrice" class="form-label">Vendor Price (₦)</label>
                        <input type="number" class="form-control" id="editVendorPrice" name="vendor_price" step="0.01" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateAlphaTopup()">Update Alpha Plan</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkUpdateModalLabel">Bulk Update Prices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkUpdateForm">
                    <div class="mb-3">
                        <label for="priceType" class="form-label">Price Type</label>
                        <select class="form-select" id="priceType" name="price_type" required>
                            <option value="selling">Selling Price</option>
                            <option value="buying">Buying Price</option>
                            <option value="agent">Agent Price</option>
                            <option value="vendor">Vendor Price</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="updateType" class="form-label">Update Type</label>
                        <select class="form-select" id="updateType" name="update_type" required>
                            <option value="percentage">Percentage Increase/Decrease</option>
                            <option value="amount">Fixed Amount Increase/Decrease</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjustmentValue" class="form-label">Adjustment Value</label>
                        <input type="number" class="form-control" id="adjustmentValue" name="adjustment_value" step="0.01" required>
                        <div class="form-text" id="adjustmentHelp">
                            Enter percentage (e.g., 10 for 10% increase, -5 for 5% decrease)
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <small>Select alpha topup plans from the table before applying bulk updates.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="applyBulkUpdate()">Apply Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Calculator Modal -->
<div class="modal fade" id="pricingCalculatorModal" tabindex="-1" aria-labelledby="pricingCalculatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pricingCalculatorModalLabel">Alpha Topup Pricing Calculator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pricingCalculatorForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="calcAlphaTopup" class="form-label">Alpha Topup Plan</label>
                                <select class="form-select" id="calcAlphaTopup" name="alpha_topup_id" required>
                                    <option value="">Select Alpha Plan</option>
                                    @foreach($alphaTopups as $alphaTopup)
                                    <option value="{{ $alphaTopup->alphaId }}">₦{{ number_format($alphaTopup->sellingPrice, 2) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="calcAccountType" class="form-label">Account Type</label>
                                <select class="form-select" id="calcAccountType" name="account_type" required>
                                    <option value="user">Regular User</option>
                                    <option value="agent">Agent</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="calculatePricing()">Calculate</button>
                        </div>
                    </div>
                </form>
                <div id="pricingResults" class="mt-4" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Pricing Breakdown</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Plan Amount:</td>
                                            <td id="resultPlanAmount">-</td>
                                        </tr>
                                        <tr>
                                            <td>User Price:</td>
                                            <td id="resultUserPrice">-</td>
                                        </tr>
                                        <tr>
                                            <td>Discount:</td>
                                            <td id="resultDiscount">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Profit Analysis</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Buying Price:</td>
                                            <td id="resultBuyingPrice">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Profit:</strong></td>
                                            <td><strong id="resultProfit">-</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Has Discount:</td>
                                            <td id="resultHasDiscount">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#alphaTopupsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[2, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 8] }
        ]
    });

    // Update help text based on update type
    $('#updateType').change(function() {
        if ($(this).val() === 'percentage') {
            $('#adjustmentHelp').text('Enter percentage (e.g., 10 for 10% increase, -5 for 5% decrease)');
        } else {
            $('#adjustmentHelp').text('Enter amount in Naira (e.g., 100 for ₦100 increase, -50 for ₦50 decrease)');
        }
    });
});

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.alpha-topup-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function getSelectedAlphaTopups() {
    const checkboxes = document.querySelectorAll('.alpha-topup-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function saveAlphaTopup() {
    const formData = new FormData(document.getElementById('addAlphaTopupForm'));
    
    $.ajax({
        url: '{{ route("admin.alpha-topup.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#addAlphaTopupModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'An error occurred while saving');
        }
    });
}

function editAlphaTopup(alphaTopupId) {
    // Find the alpha topup data from the table
    const row = $(`input[value="${alphaTopupId}"]`).closest('tr');
    const sellingPrice = row.find('td:nth-child(4)').text().replace('₦', '').replace(',', '');
    const buyingPrice = row.find('td:nth-child(5)').text().replace('₦', '').replace(',', '');
    const agentPrice = row.find('td:nth-child(6)').text().replace('₦', '').replace(',', '');
    const vendorPrice = row.find('td:nth-child(7)').text().replace('₦', '').replace(',', '');

    $('#editAlphaTopupId').val(alphaTopupId);
    $('#editSellingPrice').val(sellingPrice);
    $('#editBuyingPrice').val(buyingPrice);
    $('#editAgentPrice').val(agentPrice);
    $('#editVendorPrice').val(vendorPrice);

    $('#editAlphaTopupModal').modal('show');
}

function updateAlphaTopup() {
    const alphaTopupId = $('#editAlphaTopupId').val();
    const formData = new FormData(document.getElementById('editAlphaTopupForm'));
    
    $.ajax({
        url: `{{ route("admin.alpha-topup.index") }}/${alphaTopupId}`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-HTTP-Method-Override': 'PUT'
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#editAlphaTopupModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'An error occurred while updating');
        }
    });
}

function deleteAlphaTopup(alphaTopupId) {
    if (confirm('Are you sure you want to delete this alpha topup plan? This action cannot be undone.')) {
        $.ajax({
            url: `{{ route("admin.alpha-topup.index") }}/${alphaTopupId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'An error occurred while deleting');
            }
        });
    }
}

function applyBulkUpdate() {
    const selectedIds = getSelectedAlphaTopups();
    
    if (selectedIds.length === 0) {
        alert('Please select at least one alpha topup plan to update');
        return;
    }

    const formData = new FormData(document.getElementById('bulkUpdateForm'));
    formData.append('alpha_topup_ids', JSON.stringify(selectedIds));

    $.ajax({
        url: '{{ route("admin.alpha-topup.bulk-update-prices") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#bulkUpdateModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'An error occurred during bulk update');
        }
    });
}

function exportAlphaTopups() {
    window.location.href = '{{ route("admin.alpha-topup.export") }}';
}

function showPricingCalculator() {
    $('#pricingResults').hide();
    $('#pricingCalculatorModal').modal('show');
}

function calculatePricing() {
    const formData = new FormData(document.getElementById('pricingCalculatorForm'));
    
    $.ajax({
        url: '{{ route("admin.alpha-topup.calculate-pricing") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                const data = response.data;
                
                $('#resultPlanAmount').text('₦' + parseFloat(data.plan_amount).toLocaleString());
                $('#resultUserPrice').text('₦' + parseFloat(data.user_price).toLocaleString());
                $('#resultDiscount').text(parseFloat(data.discount_percentage).toFixed(1) + '%');
                $('#resultBuyingPrice').text('₦' + parseFloat(data.buying_price).toLocaleString());
                $('#resultProfit').text('₦' + parseFloat(data.profit).toLocaleString());
                $('#resultHasDiscount').text(data.has_discount ? 'Yes' : 'No');
                
                $('#pricingResults').show();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'An error occurred during calculation');
        }
    });
}
</script>
@endsection