<!-- Admin Top Navigation Bar -->
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
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">Admin Dashboard</a>
                    <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                <!-- System Status -->
                <div class="hidden sm:flex items-center space-x-2 bg-green-50 px-4 py-2 rounded-full">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-gray-700">System Online</span>
                </div>

                <!-- Notifications -->
                <button class="p-2 text-gray-400 hover:text-gray-500 relative">
                    <i class="fas fa-bell text-xl"></i>
                    <!-- Notification badge -->
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 transform translate-x-1/2 -translate-y-1/2"></span>
                </button>

                <!-- Profile dropdown -->
                <div class="relative">
                    <button id="profile-menu-button" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 p-2">
                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-red-500 to-orange-600 flex items-center justify-center">
                            <span class="text-sm font-medium text-white">{{ strtoupper(substr(Auth::guard('admin')->user()->sysName ?? 'A', 0, 1)) }}</span>
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-gray-700">{{ Auth::guard('admin')->user()->sysName ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::guard('admin')->user()->sysRole == 1 ? 'Super Admin' : (Auth::guard('admin')->user()->sysRole == 2 ? 'Admin' : 'Support') }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <!-- Profile dropdown menu -->
                    <div id="profile-menu" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                        <div class="py-1">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::guard('admin')->user()->sysName ?? 'Admin' }}</p>
                                <p class="text-sm text-gray-500">{{ Auth::guard('admin')->user()->sysUsername ?? 'N/A' }}</p>
                            </div>
                            <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile Settings
                            </a>
                            <a href="{{ route('admin.system.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>System Settings
                            </a>
                            <a href="{{ route('admin.system.api-monitoring.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-chart-line mr-2"></i>System Health
                            </a>
                            <div class="border-t border-gray-100">
                                <form method="POST" action="{{ route('admin.logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Sign Out
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
