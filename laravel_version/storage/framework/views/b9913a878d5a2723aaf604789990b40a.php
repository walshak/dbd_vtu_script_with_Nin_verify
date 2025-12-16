<?php $__env->startSection('title', 'Airtime Pricing Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">
                <i class="fas fa-phone-alt text-green-600 mr-2"></i> Airtime Pricing Management
            </h1>
            <p class="text-sm text-gray-600 mt-1">Manage airtime discount rates for different user types</p>
        </div>
        <button type="button" onclick="openAddModal()" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-150">
            <i class="fas fa-plus mr-2"></i> Add/Update Pricing
        </button>
    </div>

    <!-- Pricing Table Card -->
    <div class="bg-white shadow-lg rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-xs font-semibold uppercase text-gray-600 bg-gray-50">
                            <th class="px-4 py-3 text-left">Network</th>
                            <th class="px-4 py-3 text-center">User Discount (%)</th>
                            <th class="px-4 py-3 text-center">Agent Discount (%)</th>
                            <th class="px-4 py-3 text-center">Vendor Discount (%)</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $pricings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pricing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-800"><?php echo e(strtoupper($pricing->network->network ?? $pricing->aNetwork)); ?></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo e(number_format($pricing->aUserDiscount, 2)); ?>%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <?php echo e(number_format($pricing->aAgentDiscount, 2)); ?>%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <?php echo e(number_format($pricing->aVendorDiscount, 2)); ?>%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="editPricing(<?php echo e($pricing->aId); ?>)" class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded transition">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>
                                <button onclick="deletePricing(<?php echo e($pricing->aId); ?>)" class="inline-flex items-center px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded transition ml-2">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p>No pricing configured. Add pricing for networks to get started.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Pricing Modal -->
<div id="pricingModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add/Update Airtime Pricing</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="pricingForm" onsubmit="submitPricing(event)">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="pricing_id" name="pricing_id">

                <div class="space-y-4">
                    <div>
                        <label for="network" class="block text-sm font-medium text-gray-700 mb-1">Network</label>
                        <select id="network" name="network" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Network</option>
                            <?php $__currentLoopData = $networks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($network->nId); ?>"><?php echo e(strtoupper($network->network)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label for="user_discount" class="block text-sm font-medium text-gray-700 mb-1">User Discount (%)</label>
                        <input type="number" step="0.01" id="user_discount" name="user_discount" required min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">E.g., 99 = user pays 99% of face value (1% markup)</p>
                    </div>

                    <div>
                        <label for="agent_discount" class="block text-sm font-medium text-gray-700 mb-1">Agent Discount (%)</label>
                        <input type="number" step="0.01" id="agent_discount" name="agent_discount" required min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Discount for agent users</p>
                    </div>

                    <div>
                        <label for="vendor_discount" class="block text-sm font-medium text-gray-700 mb-1">Vendor Discount (%)</label>
                        <input type="number" step="0.01" id="vendor_discount" name="vendor_discount" required min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Discount for vendor users</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                        Close
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                        Save Pricing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function openAddModal() {
    document.getElementById('pricingForm').reset();
    document.getElementById('pricing_id').value = '';
    document.getElementById('modalTitle').textContent = 'Add Airtime Pricing';
    document.getElementById('pricingModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('pricingModal').classList.add('hidden');
}

function submitPricing(e) {
    e.preventDefault();

    const pricingId = document.getElementById('pricing_id').value;
    const url = pricingId ? `/admin/airtime/${pricingId}` : '<?php echo e(route("admin.airtime.store")); ?>';
    const method = pricingId ? 'PUT' : 'POST';

    const formData = new FormData(document.getElementById('pricingForm'));
    const data = Object.fromEntries(formData);

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save pricing'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving pricing');
    });
}

function editPricing(id) {
    fetch(`/admin/airtime/${id}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const pricing = data.pricing;
            document.getElementById('pricing_id').value = pricing.aId;
            document.getElementById('network').value = pricing.aNetwork;
            document.getElementById('user_discount').value = pricing.aUserDiscount;
            document.getElementById('agent_discount').value = pricing.aAgentDiscount;
            document.getElementById('vendor_discount').value = pricing.aVendorDiscount;
            document.getElementById('modalTitle').textContent = 'Edit Airtime Pricing';
            document.getElementById('pricingModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load pricing data');
    });
}

function deletePricing(id) {
    if (!confirm('Are you sure you want to delete this pricing?')) return;

    fetch(`/admin/airtime/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete pricing'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting pricing');
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/admin/airtime/index.blade.php ENDPATH**/ ?>