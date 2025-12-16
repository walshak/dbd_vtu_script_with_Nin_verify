<!-- Admin Sidebar Component -->
<div id="sidebar"
    class="bg-white shadow-lg w-64 min-h-screen fixed left-0 top-0 transform transition-transform duration-300 ease-in-out z-30 lg:translate-x-0 lg:static lg:inset-0 lg:flex-shrink-0">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-center h-16 border-b border-gray-200">
        <h1 class="text-xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">
            VTU ADMIN
        </h1>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-8">
        <div class="px-4 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/dashboard') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>
                Dashboard
            </a>

            <!-- System Monitoring -->
            <a href="{{ route('admin.monitoring.overview') }}"
                class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/monitoring*') ? 'bg-red-50 text-red-700 border-r-2 border-red-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-heartbeat mr-3 text-red-500"></i>
                System Monitoring
            </a>

            <!-- User Management -->
            <a href="{{ route('admin.users.index') }}"
                class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/users*') ? 'bg-green-50 text-green-700 border-r-2 border-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-users mr-3 text-green-500"></i>
                User Management
            </a>

            <!-- Transaction Management -->
            <a href="{{ route('admin.transactions.index') }}"
                class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/transactions*') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-exchange-alt mr-3 text-purple-500"></i>
                Transactions
            </a>

            <!-- Service Management Section -->
            <div class="pt-4">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Service Management</h3>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.data-plans.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/data-plans*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-wifi mr-3 text-blue-500"></i>
                        Data Plans
                    </a>
                    <a href="{{ route('admin.airtime.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/airtime*') ? 'bg-green-50 text-green-700 border-r-2 border-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-phone-alt mr-3 text-green-500"></i>
                        Airtime Pricing
                    </a>
                    <a href="{{ route('admin.cable-plans.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/cable-plans*') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tv mr-3 text-purple-500"></i>
                        Cable TV Plans
                    </a>
                    {{-- Non-core services commented out - only showing Uzobest supported services
                    <a href="{{ route('admin.exam-pins.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/exam-pins*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-graduation-cap mr-3 text-indigo-500"></i>
                        Exam Pins
                    </a>
                    <a href="{{ route('admin.recharge-pins.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/recharge-pins*') ? 'bg-orange-50 text-orange-700 border-r-2 border-orange-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-receipt mr-3 text-orange-500"></i>
                        Recharge Pins
                    </a>
                    --}}
                    <a href="{{ route('admin.electricity.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/electricity*') ? 'bg-yellow-50 text-yellow-700 border-r-2 border-yellow-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-bolt mr-3 text-yellow-500"></i>
                        Electricity
                    </a>
                    {{-- Non-core service commented out
                    <a href="{{ route('admin.alpha-topup.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/alpha-topup*') ? 'bg-red-50 text-red-700 border-r-2 border-red-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-mobile-alt mr-3 text-red-500"></i>
                        Alpha Topup
                    </a>
                    --}}
                </div>
            </div>

            <!-- System Configuration Section -->
            <div class="pt-4">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Configuration</h3>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.api-configuration.uzobest') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/api-configuration*') ? 'bg-green-50 text-green-700 border-r-2 border-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-cogs mr-3 text-green-500"></i>
                        API Configuration
                        <span class="ml-auto text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">Uzobest</span>
                    </a>
                    <a href="{{ route('admin.network-settings.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/network-settings*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-network-wired mr-3 text-blue-500"></i>
                        Network Settings
                    </a>
                    <a href="{{ route('admin.wallet.providers.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/wallet*') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-wallet mr-3 text-purple-500"></i>
                        Payment Settings
                    </a>
                    <a href="{{ route('system-configuration.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/system-configuration*') ? 'bg-gray-50 text-gray-700 border-r-2 border-gray-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-sliders-h mr-3 text-gray-500"></i>
                        System Settings
                    </a>
                </div>
            </div>

            <!-- Analytics & Reports Section -->
            <div class="pt-4">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Analytics</h3>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('analytics.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/analytics*') ? 'bg-red-50 text-red-700 border-r-2 border-red-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-chart-line mr-3 text-red-500"></i>
                        Analytics
                    </a>
                    <a href="{{ route('reports.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/reports*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-file-alt mr-3 text-blue-500"></i>
                        Reports
                    </a>
                    <a href="{{ route('admin.system.notifications') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/system/notifications*') ? 'bg-green-50 text-green-700 border-r-2 border-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-bell mr-3 text-green-500"></i>
                        Notifications
                    </a>
                    <a href="{{ route('kyc.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/kyc*') ? 'bg-yellow-50 text-yellow-700 border-r-2 border-yellow-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-id-card mr-3 text-yellow-500"></i>
                        KYC Management
                    </a>
                </div>
            </div>

            <!-- Advanced System Management Section -->
            <div class="pt-4">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">System Management</h3>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('admin.system.accounts.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/system/accounts*') ? 'bg-indigo-50 text-indigo-700 border-r-2 border-indigo-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-user-cog mr-3 text-indigo-500"></i>
                        Admin Accounts
                    </a>
                    <a href="{{ route('admin.system.subscribers') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/system/subscribers*') ? 'bg-teal-50 text-teal-700 border-r-2 border-teal-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-users-cog mr-3 text-teal-500"></i>
                        Subscribers
                    </a>
                    <a href="{{ route('admin.system.wallet-providers.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/system/wallet-providers*') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-credit-card mr-3 text-purple-500"></i>
                        Wallet Providers
                    </a>
                    <a href="{{ route('admin.system.api-monitoring.index') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/system/api-monitoring*') ? 'bg-red-50 text-red-700 border-r-2 border-red-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-server mr-3 text-red-500"></i>
                        API Monitoring
                    </a>
                    <a href="{{ route('admin.system.settings') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/system/settings*') ? 'bg-gray-50 text-gray-700 border-r-2 border-gray-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-cogs mr-3 text-gray-500"></i>
                        Site Settings
                    </a>
                    <a href="{{ route('admin.system.analytics.general') }}"
                        class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->is('admin/system/analytics*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-chart-pie mr-3 text-blue-500"></i>
                        System Analytics
                    </a>
                </div>
            </div>
        </div>
    </nav>
</div>
