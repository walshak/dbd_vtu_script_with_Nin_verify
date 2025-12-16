<?php $__env->startSection('title', 'Paystack Configuration'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Paystack Configuration</h1>
                    <nav class="text-green-100 text-sm">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white transition-colors">Dashboard</a>
                        <span class="mx-2">›</span>
                        <a href="<?php echo e(route('admin.system.wallet-providers.index')); ?>" class="hover:text-white transition-colors">Wallet Providers</a>
                        <span class="mx-2">›</span>
                        <span class="text-green-200">Paystack</span>
                    </nav>
                </div>
                <div>
                    <a href="<?php echo e(route('admin.system.wallet-providers.index')); ?>" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Providers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Configuration Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-xl p-3 mr-4">
                                <i class="fas fa-credit-card text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Paystack Payment Gateway</h2>
                                <p class="text-sm text-gray-500 mt-1">Configure Paystack for card payments and online transactions</p>
                            </div>
                        </div>
                        <div>
                            <?php if(isset($configurations['paystackStatus']) && $configurations['paystackStatus']->config_value === 'On'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6">
                    <?php if(session('success')): ?>
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <p class="text-green-800"><?php echo e(session('success')); ?></p>
                                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                                <p class="text-red-800"><?php echo e(session('error')); ?></p>
                                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('admin.system.wallet-providers.paystack.update')); ?>" method="POST" class="space-y-6">
                        <?php echo csrf_field(); ?>

                        <!-- Service Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-3">Service Status</label>
                            <div class="flex items-center">
                                <input type="checkbox" id="paystackStatus" name="paystackStatus" value="On"
                                    <?php echo e(isset($configurations['paystackStatus']) && $configurations['paystackStatus']->config_value === 'On' ? 'checked' : ''); ?>

                                    class="w-6 h-6 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2">
                                <label for="paystackStatus" class="ml-3 text-sm text-gray-700">
                                    <span class="status-text font-medium"><?php echo e(isset($configurations['paystackStatus']) && $configurations['paystackStatus']->config_value === 'On' ? 'Enabled' : 'Disabled'); ?></span>
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Enable or disable Paystack payment gateway</p>
                        </div>

                        <!-- API Configuration -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="paystackApi" class="block text-sm font-medium text-gray-900 mb-2">
                                    API Key <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="paystackApi" id="paystackApi"
                                        value="<?php echo e($configurations['paystackApi']->config_value ?? ''); ?>"
                                        placeholder="Enter Paystack Secret Key"
                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <button type="button" onclick="togglePassword('paystackApi')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="paystackApi-icon"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Your Paystack secret key from dashboard</p>
                            </div>
                            <div>
                                <label for="paystackCharges" class="block text-sm font-medium text-gray-900 mb-2">
                                    Transaction Charges (%)
                                </label>
                                <input type="number" step="0.01" min="0" max="100" name="paystackCharges" id="paystackCharges"
                                    value="<?php echo e($configurations['paystackCharges']->config_value ?? '1.5'); ?>" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <p class="mt-1 text-sm text-gray-500">Percentage charge for transactions</p>
                            </div>
                        </div>

                        <!-- Webhook URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Webhook URL</label>
                            <div class="flex">
                                <input type="text" value="<?php echo e(url('/webhook/paystack')); ?>" readonly
                                    class="flex-1 px-4 py-3 bg-gray-50 border border-gray-300 rounded-l-lg text-gray-700">
                                <button type="button" onclick="copyWebhookUrl('paystack')"
                                    class="px-4 py-3 bg-green-600 text-white border border-green-600 rounded-r-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-copy mr-1"></i>Copy
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Add this URL to your Paystack dashboard webhook settings</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3 pt-6">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Configuration
                            </button>
                            <button type="button" onclick="testConnection('paystack')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-plug mr-2"></i>Test Connection
                            </button>
                            <a href="<?php echo e(route('admin.system.wallet-providers.index')); ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Setup Guide -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-question-circle text-green-600 mr-2"></i>Setup Guide
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">1</div>
                            <div>
                                <h4 class="font-medium text-gray-900">Get Paystack Account</h4>
                                <p class="text-sm text-gray-500">Sign up at <a href="https://paystack.com" target="_blank" class="text-green-600 hover:text-green-800">paystack.com</a> and complete verification</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">2</div>
                            <div>
                                <h4 class="font-medium text-gray-900">Get API Keys</h4>
                                <p class="text-sm text-gray-500">From Settings > API Keys, copy your secret key</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">3</div>
                            <div>
                                <h4 class="font-medium text-gray-900">Configure Webhook</h4>
                                <p class="text-sm text-gray-500">Add webhook URL to receive payment notifications</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">4</div>
                            <div>
                                <h4 class="font-medium text-gray-900">Test Integration</h4>
                                <p class="text-sm text-gray-500">Use test mode first, then go live</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>Features
                    </h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Card payments (Visa, MasterCard)</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Bank transfers</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Mobile money</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Instant settlements</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">Fraud protection</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Copy webhook URL
function copyWebhookUrl(provider) {
    const webhookUrl = `<?php echo e(url('/webhook')); ?>/${provider}`;
    navigator.clipboard.writeText(webhookUrl).then(() => {
        showToast('Webhook URL copied to clipboard!', 'success');
    });
}

// Test connection
function testConnection(provider) {
    const btn = event.target;
    const originalText = btn.innerHTML;

    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
    btn.disabled = true;

    fetch('<?php echo e(route('admin.system.wallet-providers.test-connection')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                provider: provider
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Connection successful!', 'success');
            } else {
                showToast('Connection failed: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Connection test failed', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

// Show toast notification
function showToast(message, type) {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle mr-2"></i>
            ${message}
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Update status text when toggle changes
document.getElementById('paystackStatus').addEventListener('change', function() {
    const statusText = this.closest('div').querySelector('.status-text');
    statusText.textContent = this.checked ? 'Enabled' : 'Disabled';
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/admin/wallet-providers/paystack.blade.php ENDPATH**/ ?>