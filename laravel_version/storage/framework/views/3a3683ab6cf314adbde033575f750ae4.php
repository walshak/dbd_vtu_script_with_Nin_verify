<?php
    $title = 'Dashboard';
    $user = auth()->user();
    $firstName = explode(' ', $user->name)[0] ?? 'User';
    $transactionSummary = \App\Models\Transaction::getUserTransactionSummary($user->id);
    $recentTransactions = \App\Models\Transaction::where('sId', $user->id)
        ->orderBy('date', 'desc')
        ->limit(5)
        ->get();
?>

<?php $__env->startSection('page-content'); ?>
            <div class="container mx-auto px-6 py-8">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <h1 class="text-3xl font-bold mb-2">Welcome back, <?php echo e($firstName); ?>!</h1>
                            <p class="text-blue-100 text-lg">Manage your VTU services and track your transactions</p>
                        </div>
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                            <i class="fas fa-mobile-alt text-9xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Wallet Balance -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Wallet Balance</p>
                                <p class="text-2xl font-bold text-gray-900">₦<?php echo e(number_format($user->wallet_balance ?? 0, 2)); ?></p>
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-arrow-up mr-1"></i>Available
                                </p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-wallet text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Transactions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Transactions</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo e($transactionSummary['total_transactions']); ?></p>
                                <p class="text-xs text-blue-600 mt-1">
                                    <i class="fas fa-chart-line mr-1"></i>All time
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-exchange-alt text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Account Type -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Account Type</p>
                                <p class="text-2xl font-bold text-gray-900"><?php echo e($user->account_type_name); ?></p>
                                <p class="text-xs text-purple-600 mt-1">
                                    <i class="fas fa-user-tag mr-1"></i><?php echo e($user->registration_status_name); ?>

                                </p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-users text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Spent -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Spent</p>
                                <p class="text-2xl font-bold text-gray-900">₦<?php echo e(number_format($transactionSummary['total_spent'], 2)); ?></p>
                                <p class="text-xs text-orange-600 mt-1">
                                    <i class="fas fa-credit-card mr-1"></i><?php echo e($user->created_at->diffForHumans()); ?>

                                </p>
                            </div>
                            <div class="bg-orange-100 p-3 rounded-full">
                                <i class="fas fa-calendar-alt text-orange-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    <!-- Quick Services -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-gray-900">Quick Services</h2>
                                <span class="text-sm text-gray-500">Most used services</span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <!-- Airtime -->
                                <a href="/buy-airtime" class="service-btn group p-4 rounded-xl border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-all duration-200 text-center">
                                    <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-green-200 transition-colors">
                                        <i class="fas fa-mobile-alt text-green-600 text-xl"></i>
                                    </div>
                                    <h3 class="font-medium text-gray-900 text-sm">Buy Airtime</h3>
                                    <p class="text-xs text-gray-500 mt-1">All networks</p>
                                </a>

                                <!-- Data -->
                                <a href="/buy-data" class="service-btn group p-4 rounded-xl border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 text-center">
                                    <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-wifi text-blue-600 text-xl"></i>
                                    </div>
                                    <h3 class="font-medium text-gray-900 text-sm">Buy Data</h3>
                                    <p class="text-xs text-gray-500 mt-1">Internet bundles</p>
                                </a>

                                <!-- Cable TV -->
                                <a href="/cable-tv" class="service-btn group p-4 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 text-center">
                                    <div class="bg-purple-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-purple-200 transition-colors">
                                        <i class="fas fa-tv text-purple-600 text-xl"></i>
                                    </div>
                                    <h3 class="font-medium text-gray-900 text-sm">Cable TV</h3>
                                    <p class="text-xs text-gray-500 mt-1">DSTV, GOTV</p>
                                </a>

                                <!-- Electricity -->
                                <a href="/electricity" class="service-btn group p-4 rounded-xl border border-gray-200 hover:border-yellow-300 hover:bg-yellow-50 transition-all duration-200 text-center">
                                    <div class="bg-yellow-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-yellow-200 transition-colors">
                                        <i class="fas fa-bolt text-yellow-600 text-xl"></i>
                                    </div>
                                    <h3 class="font-medium text-gray-900 text-sm">Electricity</h3>
                                    <p class="text-xs text-gray-500 mt-1">Pay bills</p>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Fund Wallet -->
                    <div class="space-y-6">
                        <!-- Fund Wallet Card -->
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">Fund Wallet</h3>
                                <i class="fas fa-credit-card text-2xl opacity-75"></i>
                            </div>
                            <p class="text-indigo-100 text-sm mb-4">Add money to your wallet securely using bank transfer or card payment</p>
                            <a href="/fund-wallet" id="fund-wallet-btn" class="block w-full bg-white text-indigo-600 font-semibold py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors text-center">
                                <i class="fas fa-plus mr-2"></i>Add Funds
                            </a>
                        </div>

                        <!-- Account Summary -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Phone Number</span>
                                    <span class="text-sm font-medium"><?php echo e($user->phone ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Email</span>
                                    <span class="text-sm font-medium"><?php echo e($user->email ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Status</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($user->isActive() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                        <?php echo e($user->registration_status_name); ?>

                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Referral Wallet</span>
                                    <span class="text-sm font-medium">₦<?php echo e(number_format($user->referral_wallet ?? 0, 2)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions and Notifications -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Transactions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900">Recent Transactions</h2>
                                <a href="/transactions" class="text-sm text-blue-600 hover:text-blue-500 font-medium">View all</a>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if($recentTransactions->count() > 0): ?>
                                <div class="space-y-4">
                                    <?php $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full <?php echo e($transaction->status_badge_class); ?> flex items-center justify-center">
                                                <i class="<?php echo e($transaction->service_icon); ?> text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate"><?php echo e($transaction->servicename); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo e($transaction->formatted_date); ?></p>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <p class="text-sm font-medium text-gray-900"><?php echo e($transaction->formatted_amount); ?></p>
                                            <p class="text-xs <?php echo e($transaction->status == 0 ? 'text-green-600' : 'text-red-600'); ?>">
                                                <?php echo e($transaction->status_text); ?>

                                            </p>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12">
                                    <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">No transactions yet</h3>
                                    <p class="text-sm text-gray-500">Your transaction history will appear here</p>
                                    <a href="/buy-airtime" class="mt-4 text-blue-600 hover:text-blue-500 text-sm font-medium">
                                        Make your first transaction
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Notifications & Updates -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900">Notifications</h2>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    3 new
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="bg-blue-100 p-2 rounded-full">
                                        <i class="fas fa-info-circle text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">Welcome to VASTLEAD </h4>
                                        <p class="text-sm text-gray-500 mt-1">Complete your profile to get started with our services.</p>
                                        <span class="text-xs text-gray-400">2 hours ago</span>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div class="bg-green-100 p-2 rounded-full">
                                        <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">Account Verified</h4>
                                        <p class="text-sm text-gray-500 mt-1">Your account has been successfully verified.</p>
                                        <span class="text-xs text-gray-400">1 day ago</span>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-3">
                                    <div class="bg-purple-100 p-2 rounded-full">
                                        <i class="fas fa-gift text-purple-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">Welcome Bonus</h4>
                                        <p class="text-sm text-gray-500 mt-1">Enjoy special rates on your first transactions!</p>
                                        <span class="text-xs text-gray-400">2 days ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Service button clicks with links
    $('.service-btn').click(function(e) {
        e.preventDefault();
        const href = $(this).data('href');
        if (href) {
            window.location.href = href;
        }
    });

    // Fund wallet button
    $('#fund-wallet-btn').click(function() {
        window.location.href = '/fund-wallet';
    });

    // Sidebar link hover effects
    $('.group').hover(
        function() {
            $(this).find('i').addClass('transform scale-110');
        },
        function() {
            $(this).find('i').removeClass('transform scale-110');
        }
    );

    // Add smooth transitions to cards
    $('.hover\\:shadow-md').hover(
        function() {
            $(this).addClass('transform scale-105');
        },
        function() {
            $(this).removeClass('transform scale-105');
        }
    );
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/dashboard.blade.php ENDPATH**/ ?>