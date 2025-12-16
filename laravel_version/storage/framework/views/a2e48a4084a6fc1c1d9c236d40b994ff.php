<?php $__env->startSection('title', 'Data Plans Management'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Data Plans Management</h1>
                    <p class="text-blue-100 text-lg">Manage all data plans and pricing</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="syncDataPlans()" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-sync-alt mr-2"></i>Sync Prices
                    </button>
                    <button onclick="exportPlans()" class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    <button onclick="bulkUpdatePrices()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Bulk Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Data Plans Sync</h3>
                <p class="mt-1 text-sm text-blue-700">
                    Data plans are automatically synced from Uzobest API. Cost prices are updated from the API, while you can customize selling prices.
                    Click "Sync Prices" to manually update cost prices from Uzobest.
                </p>
            </div>
        </div>
    </div>

    <!-- Data Plans Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">All Data Plans</h3>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="selectAll" class="text-sm text-gray-600">Select All</label>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="dataPlansTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllTable" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-cloud-download-alt text-blue-500 mr-1"></i>
                                Uzobest Cost
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-tag text-green-500 mr-1"></i>
                                Selling Price
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-chart-line text-purple-500 mr-1"></i>
                                Profit
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $cnt = 1; ?>
                    <?php $__empty_1 = true; $__currentLoopData = $dataPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="plan-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="<?php echo e($plan->dId); ?>">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($cnt++); ?></td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo e($plan->dPlan); ?></div>
                            <div class="text-sm text-gray-500">
                                <?php echo e($plan->network->network ?? 'N/A'); ?> <?php echo e($plan->dGroup); ?>

                                (<?php echo e($plan->dValidity); ?>)
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($plan->dPlanId); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-blue-600">
                                ₦<?php echo e(number_format((float)($plan->cost_price ?? $plan->dAmount ?? 0), 2)); ?>

                            </div>
                            <div class="text-xs text-gray-500">From Uzobest</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-green-600">
                                ₦<?php echo e(number_format((float)($plan->selling_price ?? $plan->userPrice ?? 0), 2)); ?>

                            </div>
                            <div class="text-xs text-gray-500">Your Price</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                                $cost = (float)($plan->cost_price ?? $plan->dAmount ?? 0);
                                $selling = (float)($plan->selling_price ?? $plan->userPrice ?? 0);
                                $profit = $selling - $cost;
                                $profitClass = $profit > 0 ? 'text-green-600' : ($profit < 0 ? 'text-red-600' : 'text-gray-600');
                            ?>
                            <div class="text-sm font-medium <?php echo e($profitClass); ?>">
                                ₦<?php echo e(number_format($profit, 2)); ?>

                            </div>
                            <?php if($cost > 0): ?>
                                <div class="text-xs text-gray-500"><?php echo e(number_format(($profit / $cost) * 100, 1)); ?>%</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                                $status = $plan->status ?? 'active';
                                $statusClass = $status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($statusClass); ?>">
                                <?php echo e(ucfirst($status)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="editDataPlan(<?php echo e($plan->dId); ?>)"
                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-150">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="togglePlanStatus(<?php echo e($plan->dId); ?>)"
                                        class="text-<?php echo e($status === 'active' ? 'yellow' : 'green'); ?>-600 hover:text-<?php echo e($status === 'active' ? 'yellow' : 'green'); ?>-900 transition-colors duration-150">
                                    <i class="fas fa-<?php echo e($status === 'active' ? 'pause' : 'play'); ?>"></i>
                                </button>
                                <button onclick="deletePlan(<?php echo e($plan->pId); ?>)"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-150">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-database text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No data plans found</p>
                                <p class="text-gray-400">Click "Sync from Uzobest" to fetch latest plans</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Data Plan Modal -->
<div id="addDataPlans" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 -m-5 mb-4 px-6 py-4 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">Add New Data Plan</h3>
                    <button onclick="closeAddModal()" class="text-white hover:text-gray-200 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <form id="addDataPlanForm" method="post" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="network" class="block text-sm font-medium text-gray-700 mb-2">Network</label>
                        <select name="network" id="network" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select Network</option>
                            <?php $__currentLoopData = $networks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($network->nId); ?>"><?php echo e(strtoupper($network->network)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label for="dataname" class="block text-sm font-medium text-gray-700 mb-2">Plan Name</label>
                        <input type="text" placeholder="Plan Name" name="dataname" id="dataname"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="datatype" class="block text-sm font-medium text-gray-700 mb-2">Data Type</label>
                        <select name="datatype" id="datatype" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="Gifting">Gifting</option>
                            <option value="SME">SME</option>
                            <option value="Corporate">Corporate</option>
                        </select>
                    </div>

                    <div>
                        <label for="planid" class="block text-sm font-medium text-gray-700 mb-2">Plan ID</label>
                        <input type="text" placeholder="Plan ID" name="planid" id="planid"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                        <input type="number" placeholder="Days" name="duration" id="duration"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div class="md:col-span-2">
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Buying Price (₦)</label>
                        <input type="number" step="0.01" placeholder="Buying Price" name="price" id="price"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="userprice" class="block text-sm font-medium text-gray-700 mb-2">User Price (₦)</label>
                        <input type="number" step="0.01" placeholder="User Price" name="userprice" id="userprice"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="agentprice" class="block text-sm font-medium text-gray-700 mb-2">Agent Price (₦)</label>
                        <input type="number" step="0.01" placeholder="Agent Price" name="agentprice" id="agentprice"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="vendorprice" class="block text-sm font-medium text-gray-700 mb-2">Vendor Price (₦)</label>
                        <input type="number" step="0.01" placeholder="Vendor Price" name="vendorprice" id="vendorprice"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" onclick="submitDataPlan()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Add Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Data Plan Modal - Simplified for Selling Price Only -->
<div id="editDataPlans" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full md:w-2/3 lg:w-1/2 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="bg-gradient-to-r from-green-500 to-blue-600 -m-5 mb-4 px-6 py-4 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">Edit Selling Price</h3>
                    <button onclick="closeEditModal()" class="text-white hover:text-gray-200 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <form id="editDataPlanForm" method="post" class="space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" id="editPlanId" name="plan_id">

                <!-- Plan Info (Read-only) -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Plan:</span>
                            <span class="font-medium ml-2" id="editPlanName"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Network:</span>
                            <span class="font-medium ml-2" id="editPlanNetwork"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium ml-2" id="editPlanType"></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Validity:</span>
                            <span class="font-medium ml-2" id="editPlanValidity"></span>
                        </div>
                    </div>
                </div>

                <!-- Cost Price (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-cloud-download-alt text-blue-500 mr-1"></i>
                        Uzobest Cost Price (Read-only)
                    </label>
                    <input type="text" id="editCostPrice" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                </div>

                <!-- Selling Price (Editable) -->
                <div>
                    <label for="editSellingPrice" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag text-green-500 mr-1"></i>
                        Your Selling Price (₦) *
                    </label>
                    <input type="number" step="0.01" placeholder="Enter selling price" name="selling_price" id="editSellingPrice"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                    <p class="text-xs text-gray-500 mt-1">This is the price customers will pay</p>
                </div>

                <!-- Profit Calculation -->
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">
                            <i class="fas fa-chart-line text-purple-500 mr-1"></i>
                            Estimated Profit:
                        </span>
                        <span class="text-lg font-bold" id="editProfitDisplay">₦0.00</span>
                    </div>
                    <div class="text-xs text-gray-600 mt-1">
                        Profit Margin: <span id="editProfitMargin">0%</span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" onclick="submitEditDataPlan()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>Update Price
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="editNetwork" class="block text-sm font-medium text-gray-700 mb-2">Network</label>
                        <select name="network" id="editNetwork" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select Network</option>
                            <?php $__currentLoopData = $networks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($network->nId); ?>"><?php echo e(strtoupper($network->network)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label for="editDataname" class="block text-sm font-medium text-gray-700 mb-2">Plan Name</label>
                        <input type="text" placeholder="Plan Name" name="dataname" id="editDataname"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="editDatatype" class="block text-sm font-medium text-gray-700 mb-2">Data Type</label>
                        <select name="datatype" id="editDatatype" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="Gifting">Gifting</option>
                            <option value="SME">SME</option>
                            <option value="Corporate">Corporate</option>
                        </select>
                    </div>

                    <div>
                        <label for="editPlanid" class="block text-sm font-medium text-gray-700 mb-2">Plan ID</label>
                        <input type="text" placeholder="Plan ID" name="planid" id="editPlanid"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="editDuration" class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                        <input type="number" placeholder="Days" name="duration" id="editDuration"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div class="md:col-span-2">
                        <label for="editPrice" class="block text-sm font-medium text-gray-700 mb-2">Buying Price (₦)</label>
                        <input type="number" step="0.01" placeholder="Buying Price" name="price" id="editPrice"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="editUserprice" class="block text-sm font-medium text-gray-700 mb-2">User Price (₦)</label>
                        <input type="number" step="0.01" placeholder="User Price" name="userprice" id="editUserprice"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="editAgentprice" class="block text-sm font-medium text-gray-700 mb-2">Agent Price (₦)</label>
                        <input type="number" step="0.01" placeholder="Agent Price" name="agentprice" id="editAgentprice"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label for="editVendorprice" class="block text-sm font-medium text-gray-700 mb-2">Vendor Price (₦)</label>
                        <input type="number" step="0.01" placeholder="Vendor Price" name="vendorprice" id="editVendorprice"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" onclick="updateDataPlan()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>Update Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Prices Modal -->
<div id="bulkUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 -m-5 mb-4 px-6 py-4 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">Bulk Update Prices</h3>
                    <button onclick="closeBulkModal()" class="text-white hover:text-gray-200 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <form id="bulkUpdateForm" class="space-y-4">
                <div>
                    <label for="priceType" class="block text-sm font-medium text-gray-700 mb-2">Price Type</label>
                    <select name="price_type" id="priceType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="userprice">User Price</option>
                        <option value="agentprice">Agent Price</option>
                        <option value="vendorprice">Vendor Price</option>
                        <option value="price">Buying Price</option>
                    </select>
                </div>

                <div>
                    <label for="adjustmentType" class="block text-sm font-medium text-gray-700 mb-2">Adjustment Type</label>
                    <select name="adjustment_type" id="adjustmentType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="percentage">Percentage</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                </div>

                <div>
                    <label for="adjustmentValue" class="block text-sm font-medium text-gray-700 mb-2">Adjustment Value</label>
                    <input type="number" step="0.01" name="adjustment_value" id="adjustmentValue"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <p class="text-sm text-gray-500 mt-1">Use positive values to increase, negative to decrease</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeBulkModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" onclick="performBulkUpdate()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Update Prices
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
<script>
// Ensure modals are hidden on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addDataPlans')?.classList.add('hidden');
    document.getElementById('editDataPlans')?.classList.add('hidden');
    document.getElementById('bulkUpdateModal')?.classList.add('hidden');
});

$(document).ready(function() {
    // Initialize DataTable with proper configuration
    $('#dataPlansTable').DataTable({
        "pageLength": 25,
        "order": [[ 1, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }
        ],
        "responsive": true,
        "language": {
            "search": "Search plans:",
            "lengthMenu": "Show _MENU_ plans per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ plans",
            "infoEmpty": "No plans available",
            "infoFiltered": "(filtered from _MAX_ total plans)"
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAll = document.getElementById('selectAll');
    const selectAllTable = document.getElementById('selectAllTable');
    const planCheckboxes = document.querySelectorAll('.plan-checkbox');

    // Sync both select all checkboxes
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            selectAllTable.checked = this.checked;
            planCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    if (selectAllTable) {
        selectAllTable.addEventListener('change', function() {
            selectAll.checked = this.checked;
            planCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Update select all when individual checkboxes change
    planCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.plan-checkbox:checked').length;
            const allChecked = checkedCount === planCheckboxes.length;
            const noneChecked = checkedCount === 0;

            selectAll.checked = allChecked;
            selectAllTable.checked = allChecked;
            selectAll.indeterminate = !allChecked && !noneChecked;
            selectAllTable.indeterminate = !allChecked && !noneChecked;
        });
    });
});

// Modal functions
function openAddModal() {
    document.getElementById('addDataPlans').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addDataPlans').classList.add('hidden');
    document.getElementById('addDataPlanForm').reset();
}

function closeEditModal() {
    document.getElementById('editDataPlans').classList.add('hidden');
    document.getElementById('editDataPlanForm').reset();
}

function closeBulkModal() {
    document.getElementById('bulkUpdateModal').classList.add('hidden');
    document.getElementById('bulkUpdateForm').reset();
}

// Close modals when clicking outside
document.getElementById('addDataPlans').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('editDataPlans').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

document.getElementById('bulkUpdateModal').addEventListener('click', function(e) {
    if (e.target === this) closeBulkModal();
});

function submitDataPlan() {
    const form = document.getElementById('addDataPlanForm');
    const formData = new FormData(form);

    // Show loading state
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
    submitBtn.disabled = true;

    fetch('<?php echo e(route("admin.data-plans.store")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAddModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add plan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the plan');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function editDataPlan(planId) {
    // Fetch plan data and populate edit form
    fetch(`<?php echo e(url('/admin/data-plans')); ?>/${planId}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const plan = data.plan;
            const costPrice = parseFloat(plan.cost_price || plan.dAmount || 0);
            const sellingPrice = parseFloat(plan.selling_price || plan.userPrice || 0);

            // Set form values
            document.getElementById('editPlanId').value = plan.dId;
            document.getElementById('editPlanName').textContent = plan.dPlan;
            document.getElementById('editPlanNetwork').textContent = plan.network?.network || 'N/A';
            document.getElementById('editPlanType').textContent = plan.dGroup;
            document.getElementById('editPlanValidity').textContent = plan.dValidity;
            document.getElementById('editCostPrice').value = '₦' + costPrice.toFixed(2);
            document.getElementById('editSellingPrice').value = sellingPrice;

            // Calculate and display profit
            updateEditProfit(costPrice, sellingPrice);

            // Show modal
            document.getElementById('editDataPlans').classList.remove('hidden');
        } else {
            alert('Failed to load plan: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load plan data. Please check the browser console for details.');
    });
}

// Update profit calculation when selling price changes
document.getElementById('editSellingPrice')?.addEventListener('input', function() {
    const costPrice = parseFloat(document.getElementById('editCostPrice').value.replace('₦', '') || 0);
    const sellingPrice = parseFloat(this.value || 0);
    updateEditProfit(costPrice, sellingPrice);
});

function updateEditProfit(cost, selling) {
    const profit = selling - cost;
    const profitMargin = cost > 0 ? ((profit / cost) * 100) : 0;

    const profitDisplay = document.getElementById('editProfitDisplay');
    const marginDisplay = document.getElementById('editProfitMargin');

    profitDisplay.textContent = '₦' + profit.toFixed(2);
    marginDisplay.textContent = profitMargin.toFixed(1) + '%';

    // Color code the profit
    if (profit > 0) {
        profitDisplay.className = 'text-lg font-bold text-green-600';
    } else if (profit < 0) {
        profitDisplay.className = 'text-lg font-bold text-red-600';
    } else {
        profitDisplay.className = 'text-lg font-bold text-gray-600';
    }
}

function submitEditDataPlan() {
    const form = document.getElementById('editDataPlanForm');
    const formData = new FormData(form);
    const planId = document.getElementById('editPlanId').value;

    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
    submitBtn.disabled = true;

    fetch(`<?php echo e(url('/admin/data-plans')); ?>/${planId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update plan'));
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update plan');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function deletePlan(planId) {
    if (confirm('Are you sure you want to delete this plan?')) {
        fetch(`<?php echo e(url('/admin/data-plans')); ?>/${planId}`, {
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
                alert('Error: ' + (data.message || 'Failed to delete plan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the plan');
        });
    }
}

function togglePlanStatus(planId) {
    fetch(`<?php echo e(url('/admin/data-plans')); ?>/${planId}/toggle-status`, {
        method: 'POST',
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
            alert('Error: ' + (data.message || 'Failed to toggle status'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while toggling status');
    });
}

function bulkUpdatePrices() {
    const checkedPlans = Array.from(document.querySelectorAll('.plan-checkbox:checked')).map(cb => cb.value);

    if (checkedPlans.length === 0) {
        alert('Please select at least one plan to update');
        return;
    }

    // Store selected plan IDs
    window.selectedPlanIds = checkedPlans;

    // Show bulk update modal
    document.getElementById('bulkUpdateModal').classList.remove('hidden');
}

function performBulkUpdate() {
    const form = document.getElementById('bulkUpdateForm');
    const formData = new FormData(form);
    formData.append('plan_ids', JSON.stringify(window.selectedPlanIds));

    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
    submitBtn.disabled = true;

    fetch('<?php echo e(route("admin.data-plans.bulk-update-prices")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeBulkModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update prices'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating prices');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function syncDataPlans() {
    if (!confirm('This will sync data plans from Uzobest API and update cost prices. Continue?')) {
        return;
    }

    // Show loading state
    const btn = event.target;
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...';

    $.ajax({
        url: '<?php echo e(route("admin.data-plans.sync")); ?>',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        success: function(response) {
            if (response.status === 'success') {
                alert('Sync completed successfully!\n\n' +
                      'Total plans: ' + response.data.total_plans + '\n' +
                      'Updated: ' + response.data.updated_plans + '\n' +
                      'New plans: ' + response.data.new_plans);

                // Reload the page to show updated data
                location.reload();
            } else {
                alert('Sync failed: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Sync failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
        },
        complete: function() {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    });
}

function exportPlans() {
    window.location.href = '<?php echo e(route("admin.data-plans.export")); ?>?format=csv';
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/admin/data-plans/index.blade.php ENDPATH**/ ?>