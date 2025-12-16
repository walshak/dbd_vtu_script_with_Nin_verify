<?php $__env->startSection('title', 'Recharge Pin Discounts'); ?>
<?php $__env->startSection('page-title', 'Recharge Pin Discounts'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.bootstrap5.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header d-flex align-items-center justify-content-between">
                <h4 class="box-title">All Pin Discounts</h4>
                <div class="btn-group">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAirtimeDiscount">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add New
                    </button>
                    <button class="btn btn-info" onclick="getStatistics()">
                        <i class="fa fa-chart-bar"></i> Statistics
                    </button>
                    <button class="btn btn-warning" onclick="bulkUpdateDiscounts()">
                        <i class="fa fa-edit"></i> Bulk Update
                    </button>
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table id="rechargePinTable" class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>#</th>
                                <th>Network</th>
                                <th>User Pays (%)</th>
                                <th>Agent Pays (%)</th>
                                <th>Vendor Pays (%)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $cnt = 1; ?>
                            <?php $__currentLoopData = $rechargePinDiscounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="discount-checkbox" value="<?php echo e($discount->aId); ?>">
                                </td>
                                <td><?php echo e($cnt++); ?></td>
                                <td>
                                    <strong><?php echo e($discount->network ? strtoupper($discount->network->network) : 'Unknown'); ?></strong>
                                    <?php if($discount->network): ?>
                                        <br><small class="text-muted">ID: <?php echo e($discount->network->nId); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo e(number_format($discount->aUserDiscount, 1)); ?>%</span>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?php echo e(number_format($discount->aAgentDiscount, 1)); ?>%</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?php echo e(number_format($discount->aVendorDiscount, 1)); ?>%</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-primary" onclick="editAirtimeDiscount(<?php echo e($discount->aId); ?>)">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-info" onclick="viewPricing(<?php echo e($discount->aId); ?>)">
                                            <i class="fa fa-calculator"></i>
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteDiscount(<?php echo e($discount->aId); ?>)">
                                            <i class="fa fa-trash"></i>
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
</div>

<!-- Add Discount Modal -->
<div class="modal fade" id="addAirtimeDiscount" tabindex="-1" aria-labelledby="addAirtimeDiscountLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="addAirtimeDiscountLabel">Add New Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDiscountForm" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="form-group mb-3">
                        <label for="network" class="form-label">Network</label>
                        <select name="network" id="network" class="form-control" required>
                            <option value="">Select Network</option>
                            <?php $__currentLoopData = $networks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($network->nId); ?>"><?php echo e(strtoupper($network->network)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="userdiscount" class="form-label">User Pays (%)</label>
                        <input type="number" step="0.01" placeholder="User Discount" name="userdiscount" id="userdiscount" class="form-control" required min="0" max="100">
                        <small class="form-text text-muted">Percentage users pay (e.g., 99 = 99% of face value)</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="agentdiscount" class="form-label">Agent Pays (%)</label>
                        <input type="number" step="0.01" placeholder="Agent Discount" name="agentdiscount" id="agentdiscount" class="form-control" required min="0" max="100">
                        <small class="form-text text-muted">Percentage agents pay (should be ≤ user discount)</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="vendordiscount" class="form-label">Vendor Pays (%)</label>
                        <input type="number" step="0.01" placeholder="Vendor Discount" name="vendordiscount" id="vendordiscount" class="form-control" required min="0" max="100">
                        <small class="form-text text-muted">Percentage vendors pay (should be ≤ agent discount)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="submitDiscount()">
                    <i class="fa fa-plus"></i> Add Discount
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Discount Modal -->
<div class="modal fade" id="editAirtimeDicount" tabindex="-1" aria-labelledby="editAirtimeDiscountLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editAirtimeDiscountLabel">Edit Discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDiscountForm" method="post">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" id="editDiscountId" name="discount_id">

                    <div class="form-group mb-3">
                        <label for="editNetwork" class="form-label">Network</label>
                        <select name="network" id="editNetwork" class="form-control" required>
                            <option value="">Select Network</option>
                            <?php $__currentLoopData = $networks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($network->nId); ?>"><?php echo e(strtoupper($network->network)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="editUserdiscount" class="form-label">User Pays (%)</label>
                        <input type="number" step="0.01" placeholder="User Discount" name="userdiscount" id="editUserdiscount" class="form-control" required min="0" max="100">
                    </div>

                    <div class="form-group mb-3">
                        <label for="editAgentdiscount" class="form-label">Agent Pays (%)</label>
                        <input type="number" step="0.01" placeholder="Agent Discount" name="agentdiscount" id="editAgentdiscount" class="form-control" required min="0" max="100">
                    </div>

                    <div class="form-group mb-3">
                        <label for="editVendordiscount" class="form-label">Vendor Pays (%)</label>
                        <input type="number" step="0.01" placeholder="Vendor Discount" name="vendordiscount" id="editVendordiscount" class="form-control" required min="0" max="100">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateDiscount()">
                    <i class="fa fa-save"></i> Update Discount
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="bulkUpdateLabel">Bulk Update Discounts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bulkUpdateForm">
                    <div class="form-group mb-3">
                        <label for="discountType" class="form-label">Discount Type</label>
                        <select name="discount_type" id="discountType" class="form-control" required>
                            <option value="aUserDiscount">User Discount</option>
                            <option value="aAgentDiscount">Agent Discount</option>
                            <option value="aVendorDiscount">Vendor Discount</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="adjustmentType" class="form-label">Adjustment Type</label>
                        <select name="adjustment_type" id="adjustmentType" class="form-control" required>
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="adjustmentValue" class="form-label">Adjustment Value</label>
                        <input type="number" step="0.01" name="adjustment_value" id="adjustmentValue" class="form-control" required>
                        <small class="form-text text-muted">Use positive values to increase, negative to decrease</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="performBulkUpdate()">
                    <i class="fa fa-edit"></i> Update Discounts
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Calculator Modal -->
<div class="modal fade" id="pricingModal" tabindex="-1" aria-labelledby="pricingLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="pricingLabel">Pricing Calculator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pricingForm">
                    <input type="hidden" id="pricingNetworkId">

                    <div class="form-group mb-3">
                        <label class="form-label">Network</label>
                        <input type="text" id="pricingNetworkName" class="form-control" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label for="pricingAmount" class="form-label">Amount (₦)</label>
                        <input type="number" id="pricingAmount" class="form-control" min="100" value="1000">
                    </div>

                    <div class="form-group mb-3">
                        <label for="pricingQuantity" class="form-label">Quantity</label>
                        <input type="number" id="pricingQuantity" class="form-control" min="1" max="20" value="1">
                    </div>

                    <div class="form-group mb-3">
                        <label for="pricingUserType" class="form-label">User Type</label>
                        <select id="pricingUserType" class="form-control">
                            <option value="user">User</option>
                            <option value="agent">Agent</option>
                            <option value="vendor">Vendor</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="calculatePricing()">Calculate</button>
                </form>

                <div id="pricingResults" class="mt-4" style="display: none;">
                    <h6>Pricing Results:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tr><td>Original Amount:</td><td id="originalAmount"></td></tr>
                            <tr><td>Discount Rate:</td><td id="discountRate"></td></tr>
                            <tr><td>Amount to Pay:</td><td id="amountToPay"></td></tr>
                            <tr><td>Total Savings:</td><td id="totalSavings"></td></tr>
                            <tr><td>Unit Price:</td><td id="unitPrice"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#rechargePinTable').DataTable({
        "pageLength": 25,
        "order": [[ 1, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 6] }
        ],
        "responsive": true
    });

    // Select all checkbox functionality
    $('#selectAll').change(function() {
        $('.discount-checkbox').prop('checked', this.checked);
    });

    $('.discount-checkbox').change(function() {
        if (!this.checked) {
            $('#selectAll').prop('checked', false);
        } else if ($('.discount-checkbox:checked').length === $('.discount-checkbox').length) {
            $('#selectAll').prop('checked', true);
        }
    });
});

function submitDiscount() {
    const form = document.getElementById('addDiscountForm');
    const formData = new FormData(form);

    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';
    submitBtn.disabled = true;

    fetch('<?php echo e(route("admin.recharge-pins.store")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addAirtimeDiscount')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add discount'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the discount');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function editAirtimeDiscount(discountId) {
    fetch(`<?php echo e(url('/admin/recharge-pins')); ?>/${discountId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const discount = data.discount;
            document.getElementById('editDiscountId').value = discount.aId;
            document.getElementById('editNetwork').value = discount.aNetwork;
            document.getElementById('editUserdiscount').value = discount.aUserDiscount;
            document.getElementById('editAgentdiscount').value = discount.aAgentDiscount;
            document.getElementById('editVendordiscount').value = discount.aVendorDiscount;

            new bootstrap.Modal(document.getElementById('editAirtimeDicount')).show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load discount data');
    });
}

function updateDiscount() {
    const form = document.getElementById('editDiscountForm');
    const formData = new FormData(form);
    const discountId = document.getElementById('editDiscountId').value;

    fetch(`<?php echo e(url('/admin/recharge-pins')); ?>/${discountId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editAirtimeDicount')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update discount'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the discount');
    });
}

function deleteDiscount(discountId) {
    if (confirm('Are you sure you want to delete this discount?')) {
        fetch(`<?php echo e(url('/admin/recharge-pins')); ?>/${discountId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete discount'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the discount');
        });
    }
}

function bulkUpdateDiscounts() {
    const checkedDiscounts = $('.discount-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (checkedDiscounts.length === 0) {
        alert('Please select at least one discount to update');
        return;
    }

    window.selectedDiscountIds = checkedDiscounts;
    new bootstrap.Modal(document.getElementById('bulkUpdateModal')).show();
}

function performBulkUpdate() {
    const form = document.getElementById('bulkUpdateForm');
    const formData = new FormData(form);
    formData.append('discount_ids', JSON.stringify(window.selectedDiscountIds));

    fetch('<?php echo e(route("admin.recharge-pins.bulk-update")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('bulkUpdateModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update discounts'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating discounts');
    });
}

function viewPricing(discountId) {
    fetch(`<?php echo e(url('/admin/recharge-pins')); ?>/${discountId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const discount = data.discount;
            document.getElementById('pricingNetworkId').value = discount.aNetwork;
            document.getElementById('pricingNetworkName').value = discount.network ? discount.network.network.toUpperCase() : 'Unknown';

            new bootstrap.Modal(document.getElementById('pricingModal')).show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load discount data');
    });
}

function calculatePricing() {
    const networkId = document.getElementById('pricingNetworkId').value;
    const amount = document.getElementById('pricingAmount').value;
    const quantity = document.getElementById('pricingQuantity').value;
    const userType = document.getElementById('pricingUserType').value;

    const formData = new FormData();
    formData.append('network_id', networkId);
    formData.append('amount', amount);
    formData.append('quantity', quantity);
    formData.append('user_type', userType);

    fetch('<?php echo e(route("admin.recharge-pins.calculate-pricing")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const pricing = data.pricing;
            document.getElementById('originalAmount').textContent = '₦' + pricing.original_amount.toLocaleString();
            document.getElementById('discountRate').textContent = pricing.discount_rate + '%';
            document.getElementById('amountToPay').textContent = '₦' + pricing.amount_to_pay.toLocaleString();
            document.getElementById('totalSavings').textContent = '₦' + pricing.total_savings.toLocaleString();
            document.getElementById('unitPrice').textContent = '₦' + pricing.unit_price.toLocaleString();

            document.getElementById('pricingResults').style.display = 'block';
        } else {
            alert('Error: ' + (data.message || 'Failed to calculate pricing'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while calculating pricing');
    });
}

function getStatistics() {
    fetch('<?php echo e(route("admin.recharge-pins.statistics")); ?>')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.statistics;
            let message = `Recharge Pin Statistics:\n\n`;
            message += `Total Networks: ${stats.total_networks}\n`;
            message += `Networks with Discounts: ${stats.networks_with_discounts}\n`;
            message += `Average User Discount: ${stats.avg_user_discount?.toFixed(2)}%\n`;
            message += `Average Agent Discount: ${stats.avg_agent_discount?.toFixed(2)}%\n`;
            message += `Average Vendor Discount: ${stats.avg_vendor_discount?.toFixed(2)}%\n`;
            message += `Highest User Discount: ${stats.highest_user_discount}%\n`;
            message += `Lowest User Discount: ${stats.lowest_user_discount}%`;

            alert(message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load statistics');
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/admin/recharge-pins/index.blade.php ENDPATH**/ ?>