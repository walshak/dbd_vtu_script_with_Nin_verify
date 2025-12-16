<?php
    $user = auth()->user();
    $firstName = explode(' ', $user->name)[0] ?? 'User';
?>

<!-- Top Navigation Bar -->
<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Breadcrumb -->
                <div class="hidden lg:flex lg:items-center lg:space-x-4">
                    <a href="/dashboard" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                    <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    <h1 class="text-2xl font-semibold text-gray-900"><?php echo e($title ?? 'Page'); ?></h1>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                <!-- Wallet Balance -->
                <div class="hidden sm:flex items-center space-x-2 bg-green-50 px-4 py-2 rounded-full">
                    <i class="fas fa-wallet text-green-500"></i>
                    <span class="text-sm font-medium text-gray-700">Balance:</span>
                    <span class="text-lg font-bold text-green-600">₦<?php echo e(number_format($user->wallet_balance ?? 0, 2)); ?></span>
                </div>

                <!-- Profile dropdown -->
                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 p-2">
                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-sm font-medium text-white"><?php echo e(strtoupper(substr($firstName, 0, 1))); ?></span>
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-gray-700"><?php echo e($firstName); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($user->account_type_name); ?></p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <!-- Profile dropdown menu -->
                    <div id="profile-menu" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="py-1">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e($user->phone); ?></p>
                            </div>
                            <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile Settings
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>Account Settings
                            </a>
                            <div class="border-t border-gray-100">
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<?php /**PATH C:\Users\HARDMOTIONS\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/components/user-topbar.blade.php ENDPATH**/ ?>