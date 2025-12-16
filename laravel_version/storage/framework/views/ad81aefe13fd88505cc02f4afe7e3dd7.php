<?php
    $title = 'Buy Data Bundle';
?>

<?php $__env->startPush('styles'); ?>
<style>
    .network-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .network-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .network-card.selected {
        border-color: #3b82f6 !important;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .plan-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .plan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .plan-card.selected {
        border-color: #3b82f6 !important;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('page-content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Enhanced Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-500 via-purple-600 to-indigo-700 rounded-2xl p-8 text-white relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-4 -right-4 w-32 h-32 bg-white rounded-full"></div>
                    <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white rounded-full"></div>
                    <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-white rounded-full"></div>
                </div>

                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center">
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold mb-3 flex items-center">
                                <div class="bg-white bg-opacity-20 p-3 rounded-xl mr-4">
                                    <i class="fas fa-wifi text-2xl"></i>
                                </div>
                                Data Bundle Purchase
                            </h1>
                            <p class="text-blue-100 text-lg mb-4">Purchase data bundles for all Nigerian networks with instant activation</p>
                            <div class="flex flex-wrap gap-4 text-sm">
                                <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                    <i class="fas fa-bolt text-yellow-300 mr-2"></i>Instant Activation
                                </div>
                                <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                    <i class="fas fa-shield-alt text-green-300 mr-2"></i>100% Secure
                                </div>
                                <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                    <i class="fas fa-clock text-blue-300 mr-2"></i>24/7 Available
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 lg:mt-0">
                            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl px-6 py-4 border border-white border-opacity-30">
                                <div class="text-center">
                                    <div class="text-sm text-blue-100 mb-1">Wallet Balance</div>
                                    <div class="flex items-center justify-center space-x-2">
                                        <i class="fas fa-wallet text-yellow-300"></i>
                                        <span class="font-bold text-xl" id="walletBalance">₦<?php echo e(number_format(auth()->user()->wallet_balance, 2)); ?></span>
                                    </div>
                                    <a href="<?php echo e(route('fund-wallet')); ?>" class="text-xs text-blue-200 hover:text-white transition-colors">
                                        <i class="fas fa-plus mr-1"></i>Add Funds
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <?php if (isset($component)) { $__componentOriginald32d6f4bee6169f17099a51ef4e3d957 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald32d6f4bee6169f17099a51ef4e3d957 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.progress-indicator','data' => ['steps' => ['Network', 'Plan Type', 'Phone & Plan', 'Purchase'],'currentStep' => 1,'color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('progress-indicator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['steps' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Network', 'Plan Type', 'Phone & Plan', 'Purchase']),'currentStep' => 1,'color' => 'blue']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald32d6f4bee6169f17099a51ef4e3d957)): ?>
<?php $attributes = $__attributesOriginald32d6f4bee6169f17099a51ef4e3d957; ?>
<?php unset($__attributesOriginald32d6f4bee6169f17099a51ef4e3d957); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald32d6f4bee6169f17099a51ef4e3d957)): ?>
<?php $component = $__componentOriginald32d6f4bee6169f17099a51ef4e3d957; ?>
<?php unset($__componentOriginald32d6f4bee6169f17099a51ef4e3d957); ?>
<?php endif; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Enhanced Purchase Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-wifi text-blue-600"></i>
                        </div>
                        Purchase Data Bundle
                    </h2>

                    <form id="dataForm" class="space-y-6">
                        <?php echo csrf_field(); ?>

                        <!-- Enhanced Network Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-signal mr-2 text-blue-600"></i>
                                Step 1: Select Network Provider
                                <span class="ml-2 text-xs text-gray-500">(Required)</span>
                            </label>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                <?php
                                    $networkLogos = [
                                        'mtn' => 'https://cdn.jsdelivr.net/gh/bemijonathan/nigerian-telco-logos/assets/mtn.png',
                                        'airtel' => 'https://cdn.jsdelivr.net/gh/bemijonathan/nigerian-telco-logos/assets/airtel.png',
                                        'glo' => 'https://cdn.jsdelivr.net/gh/bemijonathan/nigerian-telco-logos/assets/glo.png',
                                        '9mobile' => 'https://cdn.jsdelivr.net/gh/bemijonathan/nigerian-telco-logos/assets/9mobile.png'
                                    ];
                                ?>
                                <?php $__currentLoopData = $networks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="relative cursor-pointer network-option">
                                    <input type="radio" name="network" value="<?php echo e($network->network); ?>"
                                           class="sr-only peer" required>
                                    <div class="network-card group bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-blue-300 hover:shadow-md peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg transition-all duration-300 transform hover:scale-105">
                                        <div class="flex flex-col items-center space-y-3">
                                            <!-- Enhanced Logo Section -->
                                            <div class="relative">
                                                <img src="<?php echo e($networkLogos[strtolower($network->network)] ?? $network->logoPath); ?>"
                                                     alt="<?php echo e($network->network); ?>"
                                                     class="h-12 w-auto group-hover:scale-110 transition-transform duration-300"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="hidden items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg">
                                                    <i class="fas fa-signal text-xl text-white"></i>
                                                </div>
                                            </div>

                                            <!-- Network Info -->
                                            <div>
                                                <div class="font-bold text-gray-900 text-lg"><?php echo e(strtoupper($network->network)); ?></div>
                                                <div class="text-xs text-gray-500 flex items-center justify-center space-x-1">
                                                    <i class="fas fa-check-circle text-green-500"></i>
                                                    <span>All data plans</span>
                                                </div>
                                            </div>

                                            <!-- Network Status Indicator -->
                                            <div class="flex items-center space-x-1">
                                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                                <span class="text-xs text-green-600 font-medium">Available</span>
                                            </div>
                                        </div>

                                        <!-- Selection Indicator -->
                                        <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="text-red-500 text-sm mt-2 hidden" id="network-error">
                                <i class="fas fa-exclamation-circle mr-1"></i>Please select a network provider.
                            </div>
                        </div>

                        <!-- Enhanced Data Group Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-layer-group mr-2 text-purple-600"></i>
                                Step 2: Choose Data Type
                                <span class="ml-2 text-xs text-gray-500">(Select plan type)</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="data_group" value="SME" class="sr-only peer" checked>
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6 text-center hover:border-green-300 hover:shadow-md peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-lg transition-all duration-300 transform hover:scale-105">
                                        <div class="flex flex-col items-center space-y-3">
                                            <div class="bg-green-100 p-3 rounded-full group-hover:bg-green-200 transition-colors">
                                                <i class="fas fa-building text-2xl text-green-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">SME Data</div>
                                                <div class="text-sm text-gray-600 mt-1">Corporate gifting plans</div>
                                                <div class="text-xs text-green-600 mt-2 flex items-center justify-center">
                                                    <i class="fas fa-star mr-1"></i>Most Popular
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Selection Indicator -->
                                        <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                            <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="data_group" value="Gifting" class="sr-only peer">
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6 text-center hover:border-blue-300 hover:shadow-md peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg transition-all duration-300 transform hover:scale-105">
                                        <div class="flex flex-col items-center space-y-3">
                                            <div class="bg-blue-100 p-3 rounded-full group-hover:bg-blue-200 transition-colors">
                                                <i class="fas fa-gift text-2xl text-blue-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">Gifting</div>
                                                <div class="text-sm text-gray-600 mt-1">Direct gifting data</div>
                                                <div class="text-xs text-blue-600 mt-2 flex items-center justify-center">
                                                    <i class="fas fa-heart mr-1"></i>Perfect for gifts
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Selection Indicator -->
                                        <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                            <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="data_group" value="Corporate" class="sr-only peer">
                                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6 text-center hover:border-yellow-300 hover:shadow-md peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:shadow-lg transition-all duration-300 transform hover:scale-105">
                                        <div class="flex flex-col items-center space-y-3">
                                            <div class="bg-yellow-100 p-3 rounded-full group-hover:bg-yellow-200 transition-colors">
                                                <i class="fas fa-briefcase text-2xl text-yellow-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">Corporate</div>
                                                <div class="text-sm text-gray-600 mt-1">Bulk packages</div>
                                                <div class="text-xs text-yellow-600 mt-2 flex items-center justify-center">
                                                    <i class="fas fa-users mr-1"></i>Business use
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Selection Indicator -->
                                        <div class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                            <div class="w-5 h-5 bg-yellow-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Enhanced Phone Number Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-phone mr-2 text-indigo-600"></i>
                                Step 3: Enter Phone Number & Select Plan
                                <span class="ml-2 text-xs text-gray-500">(11-digit Nigerian number)</span>
                            </label>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                                <div class="lg:col-span-2">
                                    <div class="relative">
                                        <input type="tel" id="phone" name="phone" required
                                               class="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                                               placeholder="08012345678" pattern="[0-9]{11}" maxlength="11">
                                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                            <i class="fas fa-mobile-alt text-gray-400"></i>
                                        </div>
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden" id="phoneValidationIcon">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        </div>
                                    </div>
                                    <div class="text-red-500 text-sm mt-2 hidden" id="phone-error">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Please enter a valid 11-digit phone number.
                                    </div>
                                </div>

                                <div>
                                    <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700">Number Status</span>
                                            <div class="relative inline-flex items-center">
                                                <input type="checkbox" id="ported_number" name="ported_number"
                                                       class="sr-only peer">
                                                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </div>
                                        </div>
                                        <label for="ported_number" class="text-sm text-gray-600 cursor-pointer flex items-center">
                                            <i class="fas fa-exchange-alt mr-2 text-blue-600"></i>Ported Number
                                        </label>
                                        <div class="text-xs text-gray-500 mt-1">Enable if number was moved from another network</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Data Plans Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-list mr-2 text-green-600"></i>
                                Available Data Plans
                                <div class="ml-auto flex items-center space-x-2">
                                    <button type="button" id="refreshPlans" class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                                    </button>
                                </div>
                            </label>

                            <!-- Plans Loading State -->
                            <div id="plans-loading" class="hidden">
                                <div class="flex items-center justify-center py-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
                                    <span class="text-gray-600">Loading available plans...</span>
                                </div>
                            </div>

                            <!-- Plans Container -->
                            <div id="data-plans-container">
                                <div class="text-center py-12" id="plans-empty-state">
                                    <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-info-circle text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-700 mb-2">Select Network and Data Type</h3>
                                    <p class="text-gray-500 mb-4">Choose your network provider and data type above to view available plans</p>
                                    <div class="flex justify-center space-x-4 text-sm text-gray-400">
                                        <div class="flex items-center">
                                            <i class="fas fa-signal mr-1"></i>All Networks
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-bolt mr-1"></i>Instant Activation
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-shield-alt mr-1"></i>Secure Payment
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="plan_id" name="plan_id" required>
                            <div class="text-red-500 text-sm mt-2 hidden" id="plan-error">
                                <i class="fas fa-exclamation-circle mr-1"></i>Please select a data plan.
                            </div>
                        </div>

                        <!-- Enhanced Purchase Summary -->
                        <div id="purchase-summary" class="hidden">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-blue-800 flex items-center">
                                        <i class="fas fa-receipt mr-2"></i>Purchase Summary
                                    </h3>
                                    <div class="bg-blue-100 px-3 py-1 rounded-full text-xs font-medium text-blue-800">
                                        Step 4: Review & Purchase
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                    <div class="bg-white p-4 rounded-lg text-center">
                                        <div class="text-lg font-bold text-blue-700" id="summary-plan">-</div>
                                        <div class="text-sm text-blue-600">Data Plan</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg text-center">
                                        <div class="text-lg font-bold text-blue-700" id="summary-network">-</div>
                                        <div class="text-sm text-blue-600">Network</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg text-center">
                                        <div class="text-lg font-bold text-blue-700" id="summary-phone">-</div>
                                        <div class="text-sm text-blue-600">Phone Number</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg text-center border-2 border-green-200">
                                        <div class="text-xl font-bold text-green-700" id="summary-amount">₦0</div>
                                        <div class="text-sm text-green-600">Total Amount</div>
                                    </div>
                                </div>

                                <!-- Pricing Breakdown -->
                                <div id="pricing-breakdown" class="hidden bg-white rounded-lg p-4 border border-blue-200">
                                    <div class="text-sm space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Plan Price:</span>
                                            <span id="plan-price" class="font-medium">₦0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Discount:</span>
                                            <span id="discount-amount" class="font-medium text-green-600">-₦0</span>
                                        </div>
                                        <div class="border-t pt-2">
                                            <div class="flex justify-between font-semibold">
                                                <span>You Pay:</span>
                                                <span id="final-amount" class="text-green-600">₦0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Submit Button -->
                        <div class="pt-6">
                            <button type="submit" id="purchaseBtn" disabled
                                    class="w-full bg-gradient-to-r from-blue-500 via-purple-600 to-indigo-700 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-blue-600 hover:via-purple-700 hover:to-indigo-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 flex items-center justify-center space-x-3 transform hover:scale-105 disabled:transform-none">
                                <span id="purchase-text">Complete Data Purchase</span>
                                <i class="fas fa-arrow-right" id="purchase-icon"></i>
                                <div class="hidden" id="purchase-loading">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                                </div>
                            </button>
                            <div class="text-center mt-3">
                                <div class="flex items-center justify-center space-x-4 text-xs text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-shield-alt mr-1"></i>SSL Secured
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-lock mr-1"></i>Bank Grade Security
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-bolt mr-1"></i>Instant Activation
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Sidebar -->
            <div class="lg:col-span-1">
                <!-- Service Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-info-circle mr-2"></i>Service Features
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">Instant data activation</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">All networks supported</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">SME, Gifting & Corporate data</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">Ported number support</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">24/7 availability</span>
                        </div>
                    </div>
                </div>

                <!-- Data Types Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-layer-group mr-2"></i>Data Types
                    </h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <div class="font-semibold text-green-800 mb-1">SME Data</div>
                            <p class="text-sm text-green-600">Corporate gifting plans with competitive rates</p>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="font-semibold text-blue-800 mb-1">Gifting</div>
                            <p class="text-sm text-blue-600">Direct gifting data plans for personal use</p>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <div class="font-semibold text-yellow-800 mb-1">Corporate</div>
                            <p class="text-sm text-yellow-600">Bulk data packages for business use</p>
                        </div>
                    </div>
                </div>

                <!-- Network Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-signal mr-2"></i>Network Status
                    </h3>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $networks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $network): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <img src="<?php echo e($network->logoPath); ?>" alt="<?php echo e($network->network); ?>"
                                     class="h-5 w-auto"
                                     onerror="this.style.display='none'">
                                <span class="font-medium text-gray-700"><?php echo e(strtoupper($network->network)); ?></span>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo e($network->isServiceEnabled('sme') ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                <?php echo e($network->isServiceEnabled('sme') ? 'Active' : 'Inactive'); ?>

                            </span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-history mr-2"></i>Recent Purchases
                    </h3>
                    <div class="text-center py-8">
                        <i class="fas fa-wifi text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500 mb-4">Your recent data purchases will appear here</p>
                        <a href="#" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg text-sm text-blue-600 hover:bg-blue-50 transition-colors">
                            View All Transactions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<?php if (isset($component)) { $__componentOriginalcd89753d573545ecad03936f5fd5b43b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcd89753d573545ecad03936f5fd5b43b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modals.loading','data' => ['id' => 'loadingModal','title' => 'Processing...','message' => 'Please wait while we process your data purchase']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modals.loading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'loadingModal','title' => 'Processing...','message' => 'Please wait while we process your data purchase']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcd89753d573545ecad03936f5fd5b43b)): ?>
<?php $attributes = $__attributesOriginalcd89753d573545ecad03936f5fd5b43b; ?>
<?php unset($__attributesOriginalcd89753d573545ecad03936f5fd5b43b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcd89753d573545ecad03936f5fd5b43b)): ?>
<?php $component = $__componentOriginalcd89753d573545ecad03936f5fd5b43b; ?>
<?php unset($__componentOriginalcd89753d573545ecad03936f5fd5b43b); ?>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal381e6fd11bcb290126a22e0048087e4a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal381e6fd11bcb290126a22e0048087e4a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modals.success','data' => ['id' => 'successModal','title' => 'Purchase Successful!','showReceipt' => false,'showClose' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modals.success'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'successModal','title' => 'Purchase Successful!','showReceipt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'showClose' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal381e6fd11bcb290126a22e0048087e4a)): ?>
<?php $attributes = $__attributesOriginal381e6fd11bcb290126a22e0048087e4a; ?>
<?php unset($__attributesOriginal381e6fd11bcb290126a22e0048087e4a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal381e6fd11bcb290126a22e0048087e4a)): ?>
<?php $component = $__componentOriginal381e6fd11bcb290126a22e0048087e4a; ?>
<?php unset($__componentOriginal381e6fd11bcb290126a22e0048087e4a); ?>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal135dedf8dae23dd803b5abd320d7408c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal135dedf8dae23dd803b5abd320d7408c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modals.error','data' => ['id' => 'errorModal','title' => 'Purchase Failed','buttonText' => 'Close']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modals.error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'errorModal','title' => 'Purchase Failed','buttonText' => 'Close']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal135dedf8dae23dd803b5abd320d7408c)): ?>
<?php $attributes = $__attributesOriginal135dedf8dae23dd803b5abd320d7408c; ?>
<?php unset($__attributesOriginal135dedf8dae23dd803b5abd320d7408c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal135dedf8dae23dd803b5abd320d7408c)): ?>
<?php $component = $__componentOriginal135dedf8dae23dd803b5abd320d7408c; ?>
<?php unset($__componentOriginal135dedf8dae23dd803b5abd320d7408c); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let selectedPlan = null;
    let currentPlans = [];
    let currentStep = 1;

    // Initialize
    updateProgressSteps();

    // Network selection handler
    $('input[name="network"]').change(function() {
        updateStep(1);
        loadDataPlans();
        updateForm();
    });

    // Data group selection handler
    $('input[name="data_group"]').change(function() {
        updateStep(2);
        loadDataPlans();
        updateForm();
    });

    // Phone input handler
    $('#phone').on('input', function() {
        let phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 11) {
            phone = phone.substr(0, 11);
        }
        $(this).val(phone);

        if (phone.length === 11) {
            updateStep(3);
        }

        updateForm();
        updateSummary();

        // Auto-detect network
        if (phone.length >= 4) {
            detectNetwork(phone);
        }
    });

    // Auto-detect network based on phone number
    function detectNetwork(phone) {
        const prefix = phone.substring(0, 4);
        const networkMap = {
            '0803': 'mtn', '0806': 'mtn', '0810': 'mtn', '0813': 'mtn', '0814': 'mtn',
            '0816': 'mtn', '0903': 'mtn', '0906': 'mtn', '0913': 'mtn', '0916': 'mtn',
            '0805': 'glo', '0807': 'glo', '0811': 'glo', '0815': 'glo', '0905': 'glo',
            '0802': 'airtel', '0808': 'airtel', '0812': 'airtel', '0901': 'airtel', '0902': 'airtel', '0904': 'airtel', '0907': 'airtel',
            '0809': '9mobile', '0817': '9mobile', '0818': '9mobile', '0909': '9mobile', '0908': '9mobile'
        };

        const detectedNetwork = networkMap[prefix];
        if (detectedNetwork && !$('input[name="network"]:checked').length) {
            $(`input[name="network"][value="${detectedNetwork}"]`).prop('checked', true).trigger('change');
        }
    }

    // Load data plans based on network and group selection
    function loadDataPlans() {
        const network = $('input[name="network"]:checked').val();
        const dataGroup = $('input[name="data_group"]:checked').val();

        if (!network || !dataGroup) {
            showEmptyPlans();
            return;
        }

        showLoading('Loading data plans...');

        $.ajax({
            url: '<?php echo e(route("data.plans")); ?>',
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                network: network,
                data_group: dataGroup
            },
            success: function(response) {
                hideLoading();
                if (response.status === 'success' && response.data.length > 0) {
                    currentPlans = response.data;
                    displayDataPlans(response.data);
                } else {
                    showNoPlans();
                }
            },
            error: function() {
                hideLoading();
                showNoPlans();
            }
        });
    }

    // Display data plans
    function displayDataPlans(plans) {
        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4">';

        plans.forEach(function(plan, index) {
            html += `
                <div class="plan-card bg-white border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:border-blue-300 transition-all duration-300"
                     data-plan-id="${plan.id}" data-plan='${JSON.stringify(plan)}'>
                    <div class="text-center">
                        <h4 class="font-semibold text-gray-900 mb-2">${plan.name}</h4>
                        <div class="text-2xl font-bold text-blue-600 mb-2">₦${parseFloat(plan.price).toLocaleString()}</div>
                        <div class="text-sm text-gray-500 mb-2">
                            <i class="fas fa-clock mr-1"></i>Valid for ${plan.validity}
                        </div>
                        <div>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">${plan.group}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        $('#data-plans-container').html(html);

        // Add click handlers for plan selection
        $('.plan-card').click(function() {
            $('.plan-card').removeClass('border-blue-500 bg-blue-50');
            $(this).addClass('border-blue-500 bg-blue-50');

            selectedPlan = JSON.parse($(this).attr('data-plan'));
            $('#plan_id').val(selectedPlan.id);

            updateStep(4);
            updateForm();
            updateSummary();
        });
    }

    // Show empty plans state
    function showEmptyPlans() {
        $('#data-plans-container').html(`
            <div class="text-center py-8">
                <i class="fas fa-info-circle text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Please select a network and data type to view available plans</p>
            </div>
        `);
        selectedPlan = null;
        $('#plan_id').val('');
        updateForm();
    }

    // Show no plans available
    function showNoPlans() {
        $('#data-plans-container').html(`
            <div class="text-center py-8">
                <i class="fas fa-exclamation-circle text-4xl text-yellow-500 mb-4"></i>
                <p class="text-gray-500">No data plans available for the selected network and type</p>
            </div>
        `);
        selectedPlan = null;
        $('#plan_id').val('');
        updateForm();
    }

    // Update form state
    function updateForm() {
        const network = $('input[name="network"]:checked').val();
        const phone = $('#phone').val();
        const planId = $('#plan_id').val();

        const isValid = network && phone.length === 11 && planId;

        const submitBtn = $('#purchaseBtn');
        if (isValid) {
            submitBtn.removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
        } else {
            submitBtn.addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
        }
    }

    // Update purchase summary
    function updateSummary() {
        const network = $('input[name="network"]:checked').val();
        const phone = $('#phone').val();

        if (selectedPlan && network && phone.length === 11) {
            $('#summary-plan').text(selectedPlan.name);
            $('#summary-network').text(network.toUpperCase());
            $('#summary-phone').text(phone);
            $('#summary-amount').text('₦' + parseFloat(selectedPlan.price).toLocaleString());
            $('#purchase-summary').removeClass('hidden');
        } else {
            $('#purchase-summary').addClass('hidden');
        }
    }

    // Form submission
    $('#dataForm').submit(function(e) {
        e.preventDefault();

        // Validate form fields
        let isValid = true;

        if (!$('input[name="network"]:checked').val()) {
            $('#network-error').removeClass('hidden');
            isValid = false;
        } else {
            $('#network-error').addClass('hidden');
        }

        if ($('#phone').val().length !== 11) {
            $('#phone-error').removeClass('hidden');
            isValid = false;
        } else {
            $('#phone-error').addClass('hidden');
        }

        if (!$('#plan_id').val()) {
            $('#plan-error').removeClass('hidden');
            isValid = false;
        } else {
            $('#plan-error').addClass('hidden');
        }

        if (!isValid) return;

        const formData = {
            _token: '<?php echo e(csrf_token()); ?>',
            network: $('input[name="network"]:checked').val(),
            phone: $('#phone').val(),
            plan_id: $('#plan_id').val(),
            data_group: $('input[name="data_group"]:checked').val(),
            ported_number: $('#ported_number').is(':checked')
        };

        showLoading('Processing data purchase...');

        $.ajax({
            url: '<?php echo e(route("data.purchase")); ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                if (response.status === 'success') {
                    showSuccess(response.message, response.data);
                    resetForm();
                } else {
                    showError(response.message || 'Purchase failed. Please try again.');
                }
            },
            error: function(xhr) {
                hideLoading();
                const response = xhr.responseJSON;
                showError(response?.message || 'Purchase failed. Please try again.');
            }
        });
    });

    // Helper functions
    function resetForm() {
        $('#dataForm')[0].reset();
        $('.plan-card').removeClass('border-blue-500 bg-blue-50');
        selectedPlan = null;
        $('#plan_id').val('');
        $('#purchase-summary').addClass('hidden');
        $('#network-error, #phone-error, #plan-error').addClass('hidden');
        $('#purchaseBtn').addClass('opacity-50 cursor-not-allowed').prop('disabled', true);

        // Reset to SME default
        $('input[name="data_group"][value="SME"]').prop('checked', true);
        showEmptyPlans();

        // Reset progress
        currentStep = 1;
        updateProgressSteps();
    }

    // Progress step management
    function updateStep(step) {
        currentStep = Math.max(currentStep, step);
        updateProgressSteps();
    }

    function updateProgressSteps() {
        if (typeof window.updateProgressStep === 'function') {
            window.updateProgressStep(currentStep);
        }
    }

    // Initialize
    updateForm();
});

// Modal helper functions
function showLoading(text) {
    document.getElementById('loadingText').textContent = text;
    document.getElementById('loadingModal').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingModal').classList.add('hidden');
}

function showSuccess(message, data) {
    let html = `<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="text-green-800">${message}</div>
                </div>`;

    if (data) {
        html += `
            <div class="grid grid-cols-2 gap-4">
                <div><strong>Reference:</strong> ${data.reference || 'N/A'}</div>
                <div><strong>Plan:</strong> ${data.plan || 'N/A'}</div>
                <div><strong>Amount:</strong> ₦${data.amount || 0}</div>
                <div><strong>Phone:</strong> ${data.phone || 'N/A'}</div>
                <div><strong>Network:</strong> ${data.network || 'N/A'}</div>
                <div><strong>Balance:</strong> ₦${data.balance || 0}</div>
            </div>
        `;
    }

    document.getElementById('successMessage').innerHTML = html;
    document.getElementById('successModal').classList.remove('hidden');
}

function showError(message) {
    const html = `<div class="bg-red-50 border border-red-200 rounded-lg p-4">
                     <div class="text-red-800">${message}</div>
                  </div>`;
    document.getElementById('errorMessage').innerHTML = html;
    document.getElementById('errorModal').classList.remove('hidden');
}

function hideModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.user-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/data/index.blade.php ENDPATH**/ ?>