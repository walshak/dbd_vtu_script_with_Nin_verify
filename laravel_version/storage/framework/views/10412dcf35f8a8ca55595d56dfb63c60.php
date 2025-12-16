<?php $__env->startSection('title', 'Fund Wallet'); ?>

<?php $__env->startSection('page-content'); ?>
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-full">
                        <i class="fas fa-plus-circle text-4xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Fund Your Wallet</h1>
                <p class="text-green-100 text-lg text-center">Add money to your wallet to purchase our services</p>
                <div class="text-center mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-6 py-3 inline-block">
                        <p class="text-sm font-medium">Current Balance</p>
                        <p class="text-2xl font-bold">₦<?php echo e(number_format($balance ?? 0, 2)); ?></p>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                <i class="fas fa-credit-card text-9xl"></i>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button id="bank-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 active">
                    <i class="fas fa-university mr-2"></i>
                    Bank Transfer
                </button>
                <button id="card-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    <i class="fas fa-credit-card mr-2"></i>
                    Card Payment
                </button>
                <button id="manual-tab" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    <i class="fas fa-handshake mr-2"></i>
                    Manual Transfer
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Bank Transfer Tab -->
            <div id="bank-content" class="tab-content">
                <?php if(!empty($virtualAccounts) && count($virtualAccounts) > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php $__currentLoopData = $virtualAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-blue-900"><?php echo e($account['bank_name']); ?></h3>
                                <i class="fas fa-university text-blue-600 text-2xl"></i>
                            </div>
                            <div class="space-y-3 mb-4">
                                <div>
                                    <p class="text-sm text-blue-700 font-medium">Account Number</p>
                                    <p class="text-lg font-bold text-blue-900" id="account-<?php echo e($index); ?>"><?php echo e($account['account_number']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700 font-medium">Account Name</p>
                                    <p class="text-blue-900 font-medium"><?php echo e($account['account_name']); ?></p>
                                </div>
                            </div>
                            <p class="text-xs text-green-600 mb-4">
                                <i class="fas fa-check-circle mr-1"></i>
                                Instant funding with no charges
                            </p>
                            <button onclick="copyToClipboard('account-<?php echo e($index); ?>')" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-copy mr-2"></i>Copy Account Number
                            </button>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <!-- No Virtual Accounts - Show Setup Card -->
                    <div class="max-w-2xl mx-auto">
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-2xl p-8 text-center">
                            <div class="bg-blue-600 p-4 rounded-full w-20 h-20 mx-auto mb-6">
                                <i class="fas fa-credit-card text-white text-3xl mt-2"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-3">Get Your Virtual Account Numbers</h3>
                            <p class="text-gray-600 mb-8 text-lg">Get dedicated bank account numbers to fund your wallet instantly from any bank app, USSD, or transfer. No charges, instant funding!</p>

                            <button onclick="generateVirtualAccount()"
                                    id="generate-account-btn"
                                    class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-12 py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-plus mr-3"></i>Generate Account Numbers
                            </button>

                            <div class="mt-8 bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                                <h4 class="font-bold text-gray-800 mb-4 text-lg">Why Use Virtual Accounts?</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-green-100 p-2 rounded-full">
                                            <i class="fas fa-bolt text-green-600"></i>
                                        </div>
                                        <span class="text-gray-700">Instant wallet funding</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-green-100 p-2 rounded-full">
                                            <i class="fas fa-gift text-green-600"></i>
                                        </div>
                                        <span class="text-gray-700">Zero transaction fees</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-green-100 p-2 rounded-full">
                                            <i class="fas fa-clock text-green-600"></i>
                                        </div>
                                        <span class="text-gray-700">Available 24/7</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-green-100 p-2 rounded-full">
                                            <i class="fas fa-shield-alt text-green-600"></i>
                                        </div>
                                        <span class="text-gray-700">Secure & Reliable</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-lightbulb text-yellow-600 mt-1"></i>
                                    <div class="text-left">
                                        <p class="text-sm text-yellow-800 font-medium">How it works:</p>
                                        <p class="text-sm text-yellow-700 mt-1">Once generated, you can transfer money to these account numbers from any bank and your wallet will be funded instantly!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

                <!-- Transfer Instructions -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-start space-x-3">
                        <div class="bg-blue-100 p-2 rounded-full">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900 mb-2">Transfer Instructions</h3>
                            <ul class="text-blue-800 space-y-2 text-sm">
                                <li>• Use any of the above account numbers for instant funding</li>
                                <li>• Your wallet will be credited automatically within 5 minutes</li>
                                <li>• Transfer charges are automatically deducted</li>
                                <li>• Minimum funding amount is ₦100</li>
                                <li>• Maximum funding amount is ₦500,000 per transaction</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Payment Tab -->
            <div id="card-content" class="tab-content hidden">
                <?php if($paystackEnabled ?? false): ?>
                <div class="max-w-md mx-auto">
                    <div class="text-center mb-6">
                        <div class="bg-gradient-to-r from-green-500 to-blue-600 p-4 rounded-full inline-block mb-4">
                            <i class="fas fa-credit-card text-white text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Pay with Card</h3>
                        <p class="text-gray-600">Secure payment with card, bank transfer, USSD, or bank deposit</p>
                        <div class="mt-4 text-center">
                            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-shield-alt mr-1"></i>Powered by Paystack
                            </span>
                        </div>
                    </div>

                    <form id="paystack-form" class="space-y-6">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount to Fund</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                                <input type="number"
                                       id="amount"
                                       name="amount"
                                       class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter amount"
                                       min="100"
                                       max="500000"
                                       oninput="calculatePaystackCharges()"
                                       required>
                            </div>
                            <small class="text-gray-500">Minimum: ₦100, Maximum: ₦500,000</small>
                        </div>

                        <div>
                            <label for="charges" class="block text-sm font-medium text-gray-700 mb-2">Processing Charges</label>
                            <input type="text"
                                   id="charges"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                   readonly>
                        </div>

                        <div>
                            <label for="total-amount" class="block text-sm font-medium text-gray-700 mb-2">Total to Pay</label>
                            <input type="text"
                                   id="total-amount"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 font-semibold"
                                   readonly>
                        </div>

                        <input type="hidden" id="paystack-charges" value="<?php echo e($paystackCharges ?? '1.5'); ?>">
                        <input type="hidden" name="email" value="<?php echo e(auth()->user()->email); ?>">

                        <button type="submit"
                                id="pay-now-btn"
                                class="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-green-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-credit-card mr-2"></i>Pay Now with Paystack
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="max-w-md mx-auto text-center py-12">
                    <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-credit-card text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Card Payment Unavailable</h3>
                    <p class="text-gray-500">Paystack payment gateway is currently disabled. Please use bank transfer instead.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Manual Transfer Tab -->
            <div id="manual-content" class="tab-content hidden">
                <div class="max-w-2xl mx-auto">
                    <div class="text-center mb-8">
                        <div class="bg-gradient-to-r from-orange-500 to-red-600 p-4 rounded-full inline-block mb-4">
                            <i class="fas fa-handshake text-white text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Manual Bank Transfer</h3>
                        <p class="text-gray-600">Transfer to our official bank account and contact admin for confirmation</p>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-8 border border-orange-200 mb-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-orange-700 font-medium">Bank Name</p>
                                    <p class="text-lg font-bold text-orange-900">Access Bank</p>
                                </div>
                                <div>
                                    <p class="text-sm text-orange-700 font-medium">Account Name</p>
                                    <p class="text-lg font-bold text-orange-900">VASTLEAD LIMITED</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-orange-700 font-medium">Account Number</p>
                                <p class="text-2xl font-bold text-orange-900" id="manual-account">0123456789</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <button onclick="copyToClipboard('manual-account')" class="flex-1 bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                                <i class="fas fa-copy mr-2"></i>Copy Account Number
                            </button>
                            <a href="https://wa.me/2348123456789" class="flex-1 bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors text-center">
                                <i class="fab fa-whatsapp mr-2"></i>Contact Admin
                            </a>
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                        <div class="flex items-start space-x-3">
                            <div class="bg-red-100 p-2 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-red-900 mb-2">Important Notice</h3>
                                <ul class="text-red-800 space-y-2 text-sm">
                                    <li>• Please contact admin before making any manual transfer</li>
                                    <li>• Send screenshot of payment receipt via WhatsApp</li>
                                    <li>• Manual transfers may take 2-24 hours to reflect</li>
                                    <li>• Include your phone number and username in the transfer description</li>
                                    <li>• No additional charges for manual transfers</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Funding History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900">Recent Funding History</h2>
        </div>
        <div class="p-6">
            <div class="text-center py-12">
                <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-history text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">No funding history yet</h3>
                <p class="text-sm text-gray-500">Your wallet funding history will appear here</p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Tab functionality
    $('.tab-button').click(function() {
        const tabId = $(this).attr('id').replace('-tab', '');

        // Remove active class from all tabs
        $('.tab-button').removeClass('active border-blue-500 text-blue-600').addClass('border-transparent text-gray-500');
        $('.tab-content').addClass('hidden');

        // Add active class to clicked tab
        $(this).removeClass('border-transparent text-gray-500').addClass('active border-blue-500 text-blue-600');
        $('#' + tabId + '-content').removeClass('hidden');
    });

    // Paystack form submission
    $('#paystack-form').submit(function(e) {
        e.preventDefault();

        const amount = $('#amount').val();
        if (!amount || amount < 100) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Please enter a valid amount (minimum ₦100)',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        const button = $('#pay-now-btn');
        const originalText = button.html();
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

        // Initialize Paystack payment
        $.ajax({
            url: '<?php echo e(route("wallet.paystack.initialize")); ?>',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                amount: amount
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Redirect to Paystack checkout
                    window.location.href = response.data.authorization_url;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: response.message || 'Failed to initialize payment',
                        confirmButtonColor: '#EF4444'
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Failed',
                    text: response?.message || 'An error occurred while processing payment',
                    confirmButtonColor: '#EF4444'
                });
            },
            complete: function() {
                button.prop('disabled', false).html(originalText);
            }
        });
    });
});

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;

    navigator.clipboard.writeText(text).then(function() {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Account number copied to clipboard',
            timer: 2000,
            showConfirmButton: false
        });
    }).catch(function() {
        // Fallback for older browsers
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);

        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Account number copied to clipboard',
            timer: 2000,
            showConfirmButton: false
        });
    });
}

function calculatePaystackCharges() {
    const amount = parseFloat($('#amount').val()) || 0;
    const chargeRate = parseFloat($('#paystack-charges').val()) || 1.5;

    if (amount > 0) {
        const charges = Math.ceil((amount * chargeRate) / 100);
        const totalAmount = amount + charges;

        $('#charges').val('₦' + charges.toLocaleString());
        $('#total-amount').val('₦' + totalAmount.toLocaleString());
    } else {
        $('#charges').val('');
        $('#total-amount').val('');
    }
}

// Function to generate virtual account for new users
function generateVirtualAccount() {
    const button = $('#generate-account-btn');
    const originalText = button.html();

    // Show loading state
    button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Generating...');

    $.ajax({
        url: '<?php echo e(route("wallet.generate-virtual-account")); ?>',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Virtual Accounts Generated!',
                    text: 'Your virtual account numbers have been created successfully',
                    confirmButtonColor: '#10B981'
                }).then(() => {
                    // Reload page to show the new accounts
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Generation Failed',
                    text: response.message || 'Failed to generate virtual accounts',
                    confirmButtonColor: '#EF4444'
                });
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'Generation Failed',
                text: response?.message || 'An error occurred while generating virtual accounts',
                confirmButtonColor: '#EF4444'
            });
        },
        complete: function() {
            // Reset button
            button.prop('disabled', false).html(originalText);
        }
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/user/fund-wallet.blade.php ENDPATH**/ ?>