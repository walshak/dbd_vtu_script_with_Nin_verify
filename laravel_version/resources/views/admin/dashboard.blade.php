@extends('layouts.app')

@section('title', 'Admin Dashboard - VASTLEAD ')

@section('content')
<div class="min-h-screen bg-gray-50 flex">
    <!-- Admin Sidebar -->
    <div id="admin-sidebar" class="bg-white shadow-lg w-64 min-h-screen fixed left-0 top-0 transform transition-transform duration-300 ease-in-out z-30 lg:translate-x-0 lg:static lg:inset-0">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-center h-16 border-b border-gray-200">
            <h1 class="text-xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent">
                ADMIN PANEL
            </h1>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-8">
            <div class="px-4 space-y-2">
                <!-- Dashboard -->
                <a href="/admin/dashboard" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg bg-red-50 text-red-700 border-r-2 border-red-700">
                    <i class="fas fa-tachometer-alt mr-3 text-red-600"></i>
                    Dashboard
                </a>

                <!-- User Management -->
                <div class="pt-4">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">User Management</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-users mr-3 text-blue-500"></i>
                            Subscribers
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-user-tie mr-3 text-green-500"></i>
                            Agents
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-building mr-3 text-purple-500"></i>
                            Vendors
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-plus-circle mr-3 text-indigo-500"></i>
                            Credit Users
                        </a>
                    </div>
                </div>

                <!-- Service Management -->
                <div class="pt-4">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Services</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-mobile-alt mr-3 text-green-500"></i>
                            Airtime API
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-wifi mr-3 text-blue-500"></i>
                            Data API
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-tv mr-3 text-purple-500"></i>
                            Cable TV
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-bolt mr-3 text-yellow-500"></i>
                            Electricity
                        </a>
                        {{-- Non-core services commented out - only showing Uzobest supported services
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-graduation-cap mr-3 text-indigo-500"></i>
                            Exam Pins
                        </a>
                        <a href="{{ route('admin.exam-pins.index') }}" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-graduation-cap mr-3 text-purple-500"></i>
                            Exam Pin Management
                        </a>
                        <a href="{{ route('admin.recharge-pins.index') }}" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-receipt mr-3 text-red-500"></i>
                            Recharge Pins
                        </a>
                        <a href="{{ route('admin.alpha-topup.index') }}" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-coins mr-3 text-yellow-500"></i>
                            Alpha Topup
                        </a>
                        --}}
                    </div>
                </div>

                <!-- Financial Management -->
                <div class="pt-4">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Financial</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-chart-line mr-3 text-green-500"></i>
                            Sales Analysis
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-percentage mr-3 text-orange-500"></i>
                            Discounts
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-exchange-alt mr-3 text-blue-500"></i>
                            Transactions
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-credit-card mr-3 text-purple-500"></i>
                            Payment Settings
                        </a>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="pt-4">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">System</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-cog mr-3 text-gray-500"></i>
                            Site Settings
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-bell mr-3 text-yellow-500"></i>
                            Notifications
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-envelope mr-3 text-blue-500"></i>
                            Messages
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Logout Button -->
        <div class="absolute bottom-0 w-full p-4">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full group flex items-center px-4 py-3 text-sm font-medium text-red-700 rounded-lg hover:bg-red-50">
                    <i class="fas fa-sign-out-alt mr-3 text-red-500"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div id="admin-sidebar-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 lg:ml-64">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button id="admin-mobile-menu-button" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500">
                            <span class="sr-only">Open admin menu</span>
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <!-- Breadcrumb -->
                        <div class="hidden lg:flex lg:items-center lg:space-x-4">
                            <h1 class="text-2xl font-semibold text-gray-900">Admin Dashboard</h1>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- System Status -->
                        <div class="hidden sm:flex items-center space-x-2 bg-green-50 px-4 py-2 rounded-full">
                            <i class="fas fa-circle text-green-500 text-xs"></i>
                            <span class="text-sm font-medium text-gray-700">System Online</span>
                        </div>

                        <!-- Admin Profile -->
                        <div class="relative">
                            <button id="admin-profile-menu-button" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 p-2">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-red-500 to-orange-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">A</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-700">Administrator</p>
                                    <p class="text-xs text-gray-500">System Admin</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>

                            <!-- Profile dropdown menu -->
                            <div id="admin-profile-menu" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="py-1">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">Admin User</p>
                                        <p class="text-sm text-gray-500">System Administrator</p>
                                    </div>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Admin Profile
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-shield-alt mr-2"></i>Security Settings
                                    </a>
                                    <div class="border-t border-gray-100">
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
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

        <!-- Main Dashboard Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
            <div class="container mx-auto px-6 py-8">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-red-500 to-orange-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <h1 class="text-3xl font-bold mb-2">Admin Dashboard Overview</h1>
                            <p class="text-red-100 text-lg">Monitor and manage your VTU platform</p>
                        </div>
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                            <i class="fas fa-chart-line text-9xl"></i>
                        </div>
                    </div>
                </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-2xl text-blue-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \App\Models\User::count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-2xl text-green-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Transactions</p>
                        <p class="text-lg font-semibold text-gray-900">0</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-wallet text-2xl text-yellow-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                        <p class="text-lg font-semibold text-gray-900">₦0.00</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-plus text-2xl text-purple-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">New Users Today</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \App\Models\User::whereDate('created_at', today())->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Tools Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- User Management -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-users-cog text-2xl text-white"></i>
                        <h3 class="ml-3 text-lg font-semibold text-white">User Management</h3>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Manage user accounts and permissions</p>
                    <button class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors">
                        Manage Users
                    </button>
                </div>
            </div>

            <!-- Transaction Management -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-exchange-alt text-2xl text-white"></i>
                        <h3 class="ml-3 text-lg font-semibold text-white">Transactions</h3>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">View and manage all transactions</p>
                    <button class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                        View Transactions
                    </button>
                </div>
            </div>

            <!-- API Settings -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-cogs text-2xl text-white"></i>
                        <h3 class="ml-3 text-lg font-semibold text-white">API Settings</h3>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Configure API endpoints and keys</p>
                    <button class="w-full bg-purple-500 text-white py-2 px-4 rounded-lg hover:bg-purple-600 transition-colors">
                        API Settings
                    </button>
                </div>
            </div>

            <!-- Payment Settings -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-credit-card text-2xl text-white"></i>
                        <h3 class="ml-3 text-lg font-semibold text-white">Payment Gateway</h3>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Configure payment gateways</p>
                    <button class="w-full bg-yellow-500 text-white py-2 px-4 rounded-lg hover:bg-yellow-600 transition-colors">
                        Payment Settings
                    </button>
                </div>
            </div>

            <!-- Reports -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-chart-bar text-2xl text-white"></i>
                        <h3 class="ml-3 text-lg font-semibold text-white">Reports</h3>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Generate various reports</p>
                    <button class="w-full bg-indigo-500 text-white py-2 px-4 rounded-lg hover:bg-indigo-600 transition-colors">
                        View Reports
                    </button>
                </div>
            </div>

            <!-- System Settings -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-server text-2xl text-white"></i>
                        <h3 class="ml-3 text-lg font-semibold text-white">System Settings</h3>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">Configure system settings</p>
                    <button class="w-full bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors">
                        System Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Activities and Users -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Users</h3>
                </div>
                <div class="px-6 py-4">
                    @php
                        $recentUsers = \App\Models\User::latest()->limit(5)->get();
                    @endphp

                    @if($recentUsers->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentUsers as $user)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ strtoupper(substr($user->fname, 0, 1)) }}</span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->fname }} {{ $user->lname }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->phone }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                        <p class="text-xs text-blue-600">{{ $user->getAccountTypeName() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-user-plus text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No users yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Database</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-circle text-green-400 mr-1"></i>
                                Online
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">API Services</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-circle text-green-400 mr-1"></i>
                                Active
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Payment Gateway</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-circle text-yellow-400 mr-1"></i>
                                Pending
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Server Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-circle text-green-400 mr-1"></i>
                                Healthy
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Admin menu toggle
    $('#admin-menu-button').click(function() {
        $('#admin-menu').toggleClass('hidden');
    });

    // Close admin menu when clicking outside
    $(document).click(function(event) {
        if (!$(event.target).closest('#admin-menu-button, #admin-menu').length) {
            $('#admin-menu').addClass('hidden');
        }
    });

    // Admin tool button clicks
    $('button').click(function() {
        const service = $(this).text().trim();
        if (service !== 'Sign out') {
            Swal.fire({
                icon: 'info',
                title: 'Coming Soon!',
                text: `${service} feature will be available soon.`,
                confirmButtonColor: '#3B82F6'
            });
        }
    });
});
</script>
@endpush
@endsection
