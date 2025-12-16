<!-- User Sidebar Component -->
<div id="sidebar" class="bg-white shadow-lg w-64 min-h-screen fixed left-0 top-0 transform transition-transform duration-300 ease-in-out z-30 lg:translate-x-0 lg:static lg:inset-0 lg:flex-shrink-0">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-center h-16 border-b border-gray-200">
        <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            VASTLEAD
        </h1>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-8">
        <div class="px-4 space-y-2">
            <!-- Dashboard -->
            <a href="/dashboard" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>
                Dashboard
            </a>

            <!-- Wallet Section -->
            <div class="pt-4">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Wallet</h3>
                <div class="mt-2 space-y-1">
                    <a href="/fund-wallet" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('fund-wallet') ? 'bg-green-50 text-green-700 border-r-2 border-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-plus-circle mr-3 text-green-500"></i>
                        Fund Wallet
                    </a>
                    <a href="/transfer-funds" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('transfer-funds') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-exchange-alt mr-3 text-blue-500"></i>
                        Transfer Funds
                    </a>
                    <!-- Wallet to Bank - Temporarily Hidden
                    <a href="/wallet-to-bank" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('wallet-to-bank') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-university mr-3 text-purple-500"></i>
                        Wallet to Bank
                    </a>
                    -->
                </div>
            </div>

            <!-- Services Section -->
            <div class="pt-4">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Services</h3>
                <div class="mt-2 space-y-1">
                    <a href="/buy-airtime" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('buy-airtime') ? 'bg-green-50 text-green-700 border-r-2 border-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-mobile-alt mr-3 text-green-500"></i>
                        Buy Airtime
                    </a>
                    <a href="/buy-data" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('buy-data') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-wifi mr-3 text-blue-500"></i>
                        Buy Data
                    </a>
                    <a href="/cable-tv" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('cable-tv') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-tv mr-3 text-purple-500"></i>
                        Cable TV
                    </a>
                    <a href="/electricity" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('electricity') ? 'bg-yellow-50 text-yellow-700 border-r-2 border-yellow-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-bolt mr-3 text-yellow-500"></i>
                        Electricity
                    </a>
                    
                </div>
            </div>

            <!-- Account Section -->
            <div class="pt-4">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</h3>
                <div class="mt-2 space-y-1">
                    <a href="/transactions" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('transactions') ? 'bg-gray-50 text-gray-700 border-r-2 border-gray-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-history mr-3 text-gray-500"></i>
                        Transactions
                    </a>
                    <a href="/profile" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('profile') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-user mr-3 text-blue-500"></i>
                        Profile
                    </a>
                    <a href="/referrals" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('referrals') ? 'bg-green-50 text-green-700 border-r-2 border-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-users mr-3 text-green-500"></i>
                        Referrals
                    </a>
                    <a href="/pricing" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg <?php echo e(request()->is('pricing') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'); ?>">
                        <i class="fas fa-list mr-3 text-purple-500"></i>
                        Pricing
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Logout Button -->
    <div class="absolute bottom-0 w-full p-4">
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full group flex items-center px-4 py-3 text-sm font-medium text-red-700 rounded-lg hover:bg-red-50">
                <i class="fas fa-sign-out-alt mr-3 text-red-500"></i>
                Logout
            </button>
        </form>
    </div>
</div>
<?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/components/user-sidebar.blade.php ENDPATH**/ ?>