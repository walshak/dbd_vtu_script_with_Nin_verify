

<?php $__env->startSection('title', 'Exam Pin Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Exam Pin Management</h1>
            <p class="text-muted">Manage exam pin providers, pricing, and statistics</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExamPinModal">
                <i class="fas fa-plus"></i> Add Exam Provider
            </button>
            <button type="button" class="btn btn-success" onclick="exportExamPins()">
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
                                Total Providers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($statistics['total_providers']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
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
                                Active Providers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($statistics['active_providers']); ?></div>
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
                                30-Day Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₦<?php echo e(number_format($statistics['total_revenue_30d'], 2)); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($statistics['success_rate']); ?>%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Pins Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Exam Pin Providers</h6>
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
                <table class="table table-bordered" id="examPinsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>ID</th>
                            <th>Exam Provider</th>
                            <th>Selling Price</th>
                            <th>Buying Price</th>
                            <th>Profit Margin</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $examPins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examPin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="exam-pin-checkbox" value="<?php echo e($examPin->eId); ?>">
                            </td>
                            <td><?php echo e($examPin->eId); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo e($examPin->logo_path); ?>" alt="<?php echo e($examPin->ePlan); ?>" 
                                         class="rounded-circle me-2" width="30" height="30" 
                                         onerror="this.src='/assets/images/exam-default.png'">
                                    <div>
                                        <strong><?php echo e(strtoupper($examPin->ePlan)); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo e($examPin->description); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>₦<?php echo e(number_format($examPin->ePrice, 2)); ?></td>
                            <td>₦<?php echo e(number_format($examPin->eBuyingPrice, 2)); ?></td>
                            <td>
                                <?php
                                    $profit = $examPin->ePrice - $examPin->eBuyingPrice;
                                    $profitPercentage = $examPin->eBuyingPrice > 0 ? 
                                        round(($profit / $examPin->eBuyingPrice) * 100, 2) : 0;
                                ?>
                                <span class="badge bg-<?php echo e($profit > 0 ? 'success' : 'danger'); ?>">
                                    ₦<?php echo e(number_format($profit, 2)); ?> (<?php echo e($profitPercentage); ?>%)
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($examPin->eStatus == 1 ? 'success' : 'secondary'); ?>">
                                    <?php echo e($examPin->eStatus == 1 ? 'Active' : 'Inactive'); ?>

                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            onclick="editExamPin(<?php echo e($examPin->eId); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-<?php echo e($examPin->eStatus == 1 ? 'warning' : 'success'); ?>" 
                                            onclick="toggleExamPinStatus(<?php echo e($examPin->eId); ?>)">
                                        <i class="fas fa-<?php echo e($examPin->eStatus == 1 ? 'pause' : 'play'); ?>"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deleteExamPin(<?php echo e($examPin->eId); ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Exam Pin Modal -->
<div class="modal fade" id="addExamPinModal" tabindex="-1" aria-labelledby="addExamPinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExamPinModalLabel">Add Exam Provider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addExamPinForm">
                    <div class="mb-3">
                        <label for="examPlan" class="form-label">Exam Provider Name</label>
                        <input type="text" class="form-control" id="examPlan" name="plan" required>
                        <div class="form-text">e.g., WAEC, NECO, JAMB, NABTEB</div>
                    </div>
                    <div class="mb-3">
                        <label for="examPrice" class="form-label">Selling Price (₦)</label>
                        <input type="number" class="form-control" id="examPrice" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="examBuyingPrice" class="form-label">Buying Price (₦)</label>
                        <input type="number" class="form-control" id="examBuyingPrice" name="buying_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="examStatus" class="form-label">Status</label>
                        <select class="form-select" id="examStatus" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveExamPin()">Save Exam Provider</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Exam Pin Modal -->
<div class="modal fade" id="editExamPinModal" tabindex="-1" aria-labelledby="editExamPinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editExamPinModalLabel">Edit Exam Provider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editExamPinForm">
                    <input type="hidden" id="editExamPinId" name="exam_pin_id">
                    <div class="mb-3">
                        <label for="editExamPlan" class="form-label">Exam Provider Name</label>
                        <input type="text" class="form-control" id="editExamPlan" name="plan" required>
                    </div>
                    <div class="mb-3">
                        <label for="editExamPrice" class="form-label">Selling Price (₦)</label>
                        <input type="number" class="form-control" id="editExamPrice" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="editExamBuyingPrice" class="form-label">Buying Price (₦)</label>
                        <input type="number" class="form-control" id="editExamBuyingPrice" name="buying_price" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="editExamStatus" class="form-label">Status</label>
                        <select class="form-select" id="editExamStatus" name="status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateExamPin()">Update Exam Provider</button>
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
                        <small>Select exam providers from the table before applying bulk updates.</small>
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
                <h5 class="modal-title" id="pricingCalculatorModalLabel">Exam Pin Pricing Calculator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pricingCalculatorForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="calcExamPin" class="form-label">Exam Provider</label>
                                <select class="form-select" id="calcExamPin" name="exam_pin_id" required>
                                    <option value="">Select Exam Provider</option>
                                    <?php $__currentLoopData = $examPins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examPin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($examPin->eId); ?>"><?php echo e(strtoupper($examPin->ePlan)); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="calcQuantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="calcQuantity" name="quantity" min="1" max="50" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                                            <td>Unit Price:</td>
                                            <td id="resultUnitPrice">-</td>
                                        </tr>
                                        <tr>
                                            <td>Quantity:</td>
                                            <td id="resultQuantity">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td><strong id="resultTotalAmount">-</strong></td>
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
                                            <td>Profit per Unit:</td>
                                            <td id="resultProfitPerUnit">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Profit:</strong></td>
                                            <td><strong id="resultTotalProfit">-</strong></td>
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    $('#examPinsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 7] }
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
    const checkboxes = document.querySelectorAll('.exam-pin-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function getSelectedExamPins() {
    const checkboxes = document.querySelectorAll('.exam-pin-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function saveExamPin() {
    const formData = new FormData(document.getElementById('addExamPinForm'));
    
    $.ajax({
        url: '<?php echo e(route("admin.exam-pins.store")); ?>',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#addExamPinModal').modal('hide');
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

function editExamPin(examPinId) {
    // Find the exam pin data from the table
    const row = $(`input[value="${examPinId}"]`).closest('tr');
    const plan = row.find('td:nth-child(3) strong').text();
    const price = row.find('td:nth-child(4)').text().replace('₦', '').replace(',', '');
    const buyingPrice = row.find('td:nth-child(5)').text().replace('₦', '').replace(',', '');
    const status = row.find('td:nth-child(7) span').text().trim() === 'Active' ? 1 : 0;

    $('#editExamPinId').val(examPinId);
    $('#editExamPlan').val(plan);
    $('#editExamPrice').val(price);
    $('#editExamBuyingPrice').val(buyingPrice);
    $('#editExamStatus').val(status);

    $('#editExamPinModal').modal('show');
}

function updateExamPin() {
    const examPinId = $('#editExamPinId').val();
    const formData = new FormData(document.getElementById('editExamPinForm'));
    
    $.ajax({
        url: `<?php echo e(route("admin.exam-pins.index")); ?>/${examPinId}`,
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
                $('#editExamPinModal').modal('hide');
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

function toggleExamPinStatus(examPinId) {
    if (confirm('Are you sure you want to change the status of this exam provider?')) {
        $.ajax({
            url: `<?php echo e(route("admin.exam-pins.index")); ?>/${examPinId}/toggle-status`,
            method: 'POST',
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
                alert(response?.message || 'An error occurred');
            }
        });
    }
}

function deleteExamPin(examPinId) {
    if (confirm('Are you sure you want to delete this exam provider? This action cannot be undone.')) {
        $.ajax({
            url: `<?php echo e(route("admin.exam-pins.index")); ?>/${examPinId}`,
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
    const selectedIds = getSelectedExamPins();
    
    if (selectedIds.length === 0) {
        alert('Please select at least one exam provider to update');
        return;
    }

    const formData = new FormData(document.getElementById('bulkUpdateForm'));
    formData.append('exam_pin_ids', JSON.stringify(selectedIds));

    $.ajax({
        url: '<?php echo e(route("admin.exam-pins.bulk-update-prices")); ?>',
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

function exportExamPins() {
    window.location.href = '<?php echo e(route("admin.exam-pins.export")); ?>';
}

function showPricingCalculator() {
    $('#pricingResults').hide();
    $('#pricingCalculatorModal').modal('show');
}

function calculatePricing() {
    const formData = new FormData(document.getElementById('pricingCalculatorForm'));
    
    $.ajax({
        url: '<?php echo e(route("admin.exam-pins.calculate-pricing")); ?>',
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
                
                $('#resultUnitPrice').text('₦' + parseFloat(data.unit_price).toLocaleString());
                $('#resultQuantity').text(data.quantity);
                $('#resultTotalAmount').text('₦' + parseFloat(data.total_amount).toLocaleString());
                $('#resultBuyingPrice').text('₦' + parseFloat(data.buying_price).toLocaleString());
                $('#resultProfitPerUnit').text('₦' + parseFloat(data.profit_per_unit).toLocaleString());
                $('#resultTotalProfit').text('₦' + parseFloat(data.total_profit).toLocaleString());
                
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/admin/exam-pins/index.blade.php ENDPATH**/ ?>