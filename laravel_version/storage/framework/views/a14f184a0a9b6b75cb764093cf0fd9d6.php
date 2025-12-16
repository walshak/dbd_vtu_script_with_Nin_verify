<?php $__env->startSection('title', 'Electricity Bill Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        <i class="fas fa-bolt mr-2"></i>Electricity Bill Management
                    </h1>
                    <p class="text-yellow-100 text-lg">Manage electricity providers, pricing, and service settings</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openAddProviderModal()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Add Provider
                    </button>
                    <button onclick="syncProviders()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-sync mr-2"></i>Sync Providers
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Active Providers</p>
                    <p class="text-3xl font-bold text-gray-800" id="activeProvidersCount"><?php echo e($statistics['active_providers']); ?></p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-plug text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Today's Transactions</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo e($statistics['today_transactions']); ?></p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-cyan-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Today's Revenue</p>
                    <p class="text-3xl font-bold text-gray-800">₦<?php echo e(number_format($statistics['today_revenue'], 2)); ?></p>
                </div>
                <div class="bg-cyan-100 rounded-full p-3">
                    <i class="fas fa-naira-sign text-cyan-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Profit</p>
                    <p class="text-3xl font-bold text-gray-800">₦<?php echo e(number_format($statistics['total_profit'], 2)); ?></p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-coins text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" id="electricityTabs">
                <button onclick="switchTab('providers')" id="providers-tab" class="tab-button active flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm focus:outline-none">
                    <i class="fas fa-building mr-2"></i>Providers
                </button>
                <button onclick="switchTab('transactions')" id="transactions-tab" class="tab-button flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm focus:outline-none">
                    <i class="fas fa-list mr-2"></i>Transactions
                </button>
                <button onclick="switchTab('settings')" id="settings-tab" class="tab-button flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm focus:outline-none">
                    <i class="fas fa-cog mr-2"></i>Settings
                </button>
                <button onclick="switchTab('api-config')" id="api-config-tab" class="tab-button flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm focus:outline-none">
                    <i class="fas fa-plug mr-2"></i>API Config
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Providers Tab -->
            <div id="providers" class="tab-content">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="providersTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($provider->eId); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="<?php echo e($provider->logo_path); ?>" alt="<?php echo e($provider->ePlan); ?>"
                                             class="rounded-full mr-3" width="30" height="30">
                                        <strong class="text-sm font-medium text-gray-900"><?php echo e(strtoupper($provider->ePlan)); ?></strong>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($provider->eStatus ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo e($provider->eStatus ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-blue-600">
                                        ₦<?php echo e(number_format($provider->cost_price ?? $provider->eBuyingPrice, 2)); ?>/kWh
                                    </div>
                                    <div class="text-xs text-gray-500">From Uzobest</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-green-600">
                                        ₦<?php echo e(number_format($provider->selling_price ?? $provider->ePrice, 2)); ?>/kWh
                                    </div>
                                    <div class="text-xs text-gray-500">Your Price</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                        $cost = $provider->cost_price ?? $provider->eBuyingPrice ?? 0;
                                        $selling = $provider->selling_price ?? $provider->ePrice ?? 0;
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($provider->updated_at ? $provider->updated_at->format('M d, Y H:i') : 'N/A'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="editProvider(<?php echo e($provider->eId); ?>)"
                                                class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                                                title="Edit Provider">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="toggleProvider(<?php echo e($provider->eId); ?>)"
                                                class="text-<?php echo e($provider->eStatus ? 'yellow' : 'green'); ?>-600 hover:text-<?php echo e($provider->eStatus ? 'yellow' : 'green'); ?>-900 transition-colors duration-150"
                                                title="<?php echo e($provider->eStatus ? 'Disable' : 'Enable'); ?> Provider">
                                            <i class="fas fa-<?php echo e($provider->eStatus ? 'pause' : 'play'); ?>"></i>
                                        </button>
                                        <button onclick="deleteProvider(<?php echo e($provider->eId); ?>, '<?php echo e($provider->ePlan); ?>')"
                                                class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                                title="Delete Provider">
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

            <!-- Transactions Tab -->
            <div id="transactions" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" id="transactionStatus">
                        <option value="">All Status</option>
                        <option value="1">Successful</option>
                        <option value="0">Failed</option>
                        <option value="2">Pending</option>
                    </select>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" id="transactionProvider">
                        <option value="">All Providers</option>
                        <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($provider->ePlan); ?>"><?php echo e(strtoupper($provider->ePlan)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" id="transactionDate" placeholder="Select Date">
                    <button onclick="filterTransactions()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="transactionsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings" class="tab-content hidden">
                <form id="electricitySettingsForm" onsubmit="saveSettings(event)">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h5 class="text-lg font-semibold text-gray-900 mb-4">Service Configuration</h5>

                            <div class="mb-4">
                                <label for="electricityCharges" class="block text-sm font-medium text-gray-700 mb-2">Service Charges (₦)</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                       id="electricityCharges" name="electricity_charges" step="0.01" value="<?php echo e($settings['electricity_charges'] ?? 50); ?>">
                                <p class="mt-1 text-sm text-gray-500">Additional charges added to each transaction</p>
                            </div>

                            <div class="mb-4">
                                <label for="minimumAmount" class="block text-sm font-medium text-gray-700 mb-2">Minimum Amount (₦)</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                       id="minimumAmount" name="minimum_amount" value="<?php echo e($settings['minimum_amount'] ?? 1000); ?>">
                            </div>

                            <div class="mb-4">
                                <label for="maximumAmount" class="block text-sm font-medium text-gray-700 mb-2">Maximum Amount (₦)</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                       id="maximumAmount" name="maximum_amount" value="<?php echo e($settings['maximum_amount'] ?? 50000); ?>">
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" id="serviceEnabled" name="service_enabled"
                                           class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500"
                                           <?php echo e(($settings['service_enabled'] ?? true) ? 'checked' : ''); ?>>
                                    <span class="ml-2 text-sm font-medium text-gray-700">Enable Electricity Service</span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h5 class="text-lg font-semibold text-gray-900 mb-4">Discount Settings</h5>

                            <div class="mb-4">
                                <label for="agentDiscount" class="block text-sm font-medium text-gray-700 mb-2">Agent Discount (%)</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                       id="agentDiscount" name="agent_discount" step="0.01" value="<?php echo e($settings['agent_discount'] ?? 1); ?>">
                            </div>

                            <div class="mb-4">
                                <label for="vendorDiscount" class="block text-sm font-medium text-gray-700 mb-2">Vendor Discount (%)</label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                       id="vendorDiscount" name="vendor_discount" step="0.01" value="<?php echo e($settings['vendor_discount'] ?? 2); ?>">
                            </div>

                            <div class="mb-4">
                                <label for="maintenanceMessage" class="block text-sm font-medium text-gray-700 mb-2">Maintenance Message</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                          id="maintenanceMessage" name="maintenance_message" rows="3"><?php echo e($settings['maintenance_message'] ?? 'Electricity service is temporarily unavailable. Please try again later.'); ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" id="maintenanceMode" name="maintenance_mode"
                                           class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500"
                                           <?php echo e(($settings['maintenance_mode'] ?? false) ? 'checked' : ''); ?>>
                                    <span class="ml-2 text-sm font-medium text-gray-700">Maintenance Mode</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>Save Settings
                        </button>
                        <button type="button" onclick="resetSettings()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200">
                            <i class="fas fa-undo mr-2"></i>Reset to Default
                        </button>
                    </div>
                </form>
            </div>

            <!-- API Configuration Tab -->
            <div id="api-config" class="tab-content hidden">
                <form id="apiConfigForm" onsubmit="saveApiConfig(event)">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h5 class="text-lg font-semibold text-gray-900 mb-4">Primary API Configuration</h5>

                            <div class="mb-4">
                                <label for="apiUrl" class="block text-sm font-medium text-gray-700 mb-2">API URL</label>
                                <input type="url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                       id="apiUrl" name="electricity_api_url" value="<?php echo e($apiConfig['electricity_api_url'] ?? ''); ?>">
                            </div>

                            <div class="mb-4">
                                <label for="apiKey" class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                                <div class="flex">
                                    <input type="password" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                           id="apiKey" name="electricity_api_key" value="<?php echo e($apiConfig['electricity_api_key'] ?? ''); ?>">
                                    <button type="button" onclick="toggleApiKey()" class="px-4 py-2 bg-gray-200 border border-l-0 border-gray-300 rounded-r-lg hover:bg-gray-300 transition-colors duration-200">
                                        <i class="fas fa-eye" id="apiKeyIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="validationUrl" class="block text-sm font-medium text-gray-700 mb-2">Validation URL</label>
                                <input type="url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                       id="validationUrl" name="electricity_validation_url" value="<?php echo e($apiConfig['electricity_validation_url'] ?? ''); ?>">
                            </div>

                            <div class="mb-4">
                                <label for="authType" class="block text-sm font-medium text-gray-700 mb-2">Authentication Type</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                        id="authType" name="electricity_auth_type">
                                    <option value="basic" <?php echo e(($apiConfig['electricity_auth_type'] ?? 'basic') == 'basic' ? 'selected' : ''); ?>>Basic Auth</option>
                                    <option value="token" <?php echo e(($apiConfig['electricity_auth_type'] ?? 'basic') == 'token' ? 'selected' : ''); ?>>Bearer Token</option>
                                </select>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h5 class="text-lg font-semibold text-gray-900 mb-4">API Testing & Status</h5>

                            <div class="mb-4">
                                <label for="testMeterNumber" class="block text-sm font-medium text-gray-700 mb-2">Test Meter Number</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                       id="testMeterNumber" placeholder="Enter meter number for testing">
                            </div>

                            <div class="mb-4">
                                <label for="testProvider" class="block text-sm font-medium text-gray-700 mb-2">Test Provider</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" id="testProvider">
                                    <option value="">Select Provider</option>
                                    <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($provider->ePlan); ?>"><?php echo e(strtoupper($provider->ePlan)); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <button type="button" onclick="testValidation()" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-check-circle mr-2"></i>Test Validation API
                                </button>
                                <button type="button" onclick="testPurchase()" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-shopping-cart mr-2"></i>Test Purchase API
                                </button>
                            </div>

                            <div id="apiTestResults" class="mt-4 hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <strong class="text-blue-900">Test Results:</strong>
                                    <pre id="testResultsContent" class="mt-2 text-sm text-blue-800 overflow-auto"></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>Save API Configuration
                        </button>
                        <button type="button" onclick="testConnection()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200">
                            <i class="fas fa-link mr-2"></i>Test Connection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Provider Modal -->
<div id="addProviderModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 -m-5 mb-4 px-6 py-4 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">Add Electricity Provider</h3>
                    <button onclick="closeAddProviderModal()" class="text-white hover:text-gray-200 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <form id="addProviderForm" onsubmit="submitProvider(event)" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label for="providerPlan" class="block text-sm font-medium text-gray-700 mb-2">Provider Name</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           id="providerPlan" name="ePlan" required>
                    <p class="mt-1 text-sm text-gray-500">e.g., AEDC, EKEDC, IKEDC, KEDCO</p>
                </div>

                <div>
                    <label for="providerId" class="block text-sm font-medium text-gray-700 mb-2">Provider Code</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           id="providerId" name="eProviderId">
                    <p class="mt-1 text-sm text-gray-500">API provider identifier</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="buyingPrice" class="block text-sm font-medium text-gray-700 mb-2">Buying Price (₦/kWh)</label>
                        <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               id="buyingPrice" name="eBuyingPrice" step="0.01" required>
                    </div>
                    <div>
                        <label for="sellingPrice" class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₦/kWh)</label>
                        <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               id="sellingPrice" name="ePrice" step="0.01" required>
                    </div>
                </div>

                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="providerStatus" name="eStatus" checked
                               class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Active Provider</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddProviderModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Add Provider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Provider Modal -->
<div id="editProviderModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="bg-gradient-to-r from-green-500 to-blue-600 -m-5 mb-4 px-6 py-4 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">Edit Electricity Provider</h3>
                    <button onclick="closeEditProviderModal()" class="text-white hover:text-gray-200 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <form id="editProviderForm" onsubmit="updateProvider(event)" class="space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" id="editProviderId" name="provider_id">

                <div>
                    <label for="editProviderPlan" class="block text-sm font-medium text-gray-700 mb-2">Provider Name</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           id="editProviderPlan" name="ePlan" required>
                </div>

                <div>
                    <label for="editProviderCode" class="block text-sm font-medium text-gray-700 mb-2">Provider Code</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           id="editProviderCode" name="eProviderId">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="editBuyingPrice" class="block text-sm font-medium text-gray-700 mb-2">Buying Price (₦/kWh)</label>
                        <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               id="editBuyingPrice" name="eBuyingPrice" step="0.01" required>
                    </div>
                    <div>
                        <label for="editSellingPrice" class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₦/kWh)</label>
                        <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               id="editSellingPrice" name="ePrice" step="0.01" required>
                    </div>
                </div>

                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="editProviderStatus" name="eStatus"
                               class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Active Provider</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditProviderModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Update Provider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Tab switching functionality
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active', 'border-yellow-500', 'text-yellow-600');
        btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });

    // Show selected tab
    document.getElementById(tabName).classList.remove('hidden');

    // Add active class to selected button
    const activeBtn = document.getElementById(tabName + '-tab');
    activeBtn.classList.add('active', 'border-yellow-500', 'text-yellow-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
}

// Modal functions
function openAddProviderModal() {
    document.getElementById('addProviderModal').classList.remove('hidden');
}

function closeAddProviderModal() {
    document.getElementById('addProviderModal').classList.add('hidden');
    document.getElementById('addProviderForm').reset();
}

function closeEditProviderModal() {
    document.getElementById('editProviderModal').classList.add('hidden');
    document.getElementById('editProviderForm').reset();
}

function submitProvider(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('<?php echo e(route("admin.electricity.providers.store")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Provider added successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add provider'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the provider');
    });
}

function editProvider(providerId) {
    fetch(`<?php echo e(url('/admin/electricity/providers')); ?>/${providerId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const provider = data.provider;
            document.getElementById('editProviderId').value = provider.eId;
            document.getElementById('editProviderPlan').value = provider.ePlan;
            document.getElementById('editProviderCode').value = provider.eProviderId || '';
            document.getElementById('editBuyingPrice').value = provider.eBuyingPrice;
            document.getElementById('editSellingPrice').value = provider.ePrice;
            document.getElementById('editProviderStatus').checked = provider.eStatus == 1;
            document.getElementById('editProviderModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load provider data');
    });
}

function updateProvider(e) {
    e.preventDefault();
    const providerId = document.getElementById('editProviderId').value;
    const formData = new FormData(e.target);

    fetch(`<?php echo e(url('/admin/electricity/providers')); ?>/${providerId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Provider updated successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update provider'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the provider');
    });
}

function toggleProvider(providerId) {
    if (confirm('Are you sure you want to change the status of this provider?')) {
        fetch(`<?php echo e(url('/admin/electricity/providers')); ?>/${providerId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to toggle provider status'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

function deleteProvider(providerId, providerName) {
    if (confirm(`Are you sure you want to delete ${providerName}? This action cannot be undone.`)) {
        fetch(`<?php echo e(url('/admin/electricity/providers')); ?>/${providerId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Provider deleted successfully');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete provider'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the provider');
        });
    }
}

function syncProviders() {
    if (confirm('This will sync providers from the API. Continue?')) {
        fetch('<?php echo e(route("admin.electricity.sync-providers")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Providers synced successfully');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to sync providers'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while syncing providers');
        });
    }
}

function filterTransactions() {
    const status = document.getElementById('transactionStatus').value;
    const provider = document.getElementById('transactionProvider').value;
    const date = document.getElementById('transactionDate').value;

    // Implement AJAX filter logic here
    console.log('Filtering:', {status, provider, date});
}

function saveSettings(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('<?php echo e(route("admin.electricity.settings.update")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully');
        } else {
            alert('Error: ' + (data.message || 'Failed to save settings'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving settings');
    });
}

function resetSettings() {
    if (confirm('Reset all settings to default values?')) {
        location.reload();
    }
}

function saveApiConfig(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    fetch('<?php echo e(route("admin.electricity.api-config.update")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('API configuration saved successfully');
        } else {
            alert('Error: ' + (data.message || 'Failed to save API configuration'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving API configuration');
    });
}

function toggleApiKey() {
    const apiKeyInput = document.getElementById('apiKey');
    const icon = document.getElementById('apiKeyIcon');

    if (apiKeyInput.type === 'password') {
        apiKeyInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        apiKeyInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function testValidation() {
    const meterNumber = document.getElementById('testMeterNumber').value;
    const provider = document.getElementById('testProvider').value;

    if (!meterNumber || !provider) {
        alert('Please enter meter number and select provider');
        return;
    }

    // Implement validation test logic here
    document.getElementById('apiTestResults').classList.remove('hidden');
    document.getElementById('testResultsContent').textContent = 'Testing validation...';
}

function testPurchase() {
    const meterNumber = document.getElementById('testMeterNumber').value;
    const provider = document.getElementById('testProvider').value;

    if (!meterNumber || !provider) {
        alert('Please enter meter number and select provider');
        return;
    }

    // Implement purchase test logic here
    document.getElementById('apiTestResults').classList.remove('hidden');
    document.getElementById('testResultsContent').textContent = 'Testing purchase...';
}

function testConnection() {
    alert('Testing API connection...');
    // Implement connection test logic here
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddProviderModal();
        closeEditProviderModal();
    }
});

// Initialize default tab styles
document.addEventListener('DOMContentLoaded', function() {
    const activeBtn = document.getElementById('providers-tab');
    activeBtn.classList.add('border-yellow-500', 'text-yellow-600');
    activeBtn.classList.remove('border-transparent', 'text-gray-500');

    document.querySelectorAll('.tab-button:not(.active)').forEach(btn => {
        btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/admin/electricity/index.blade.php ENDPATH**/ ?>