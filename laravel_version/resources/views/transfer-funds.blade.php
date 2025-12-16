@extends('layouts.app')

@section('title', 'Transfer Funds - VASTLEAD ')

@section('content')
<div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar (same as dashboard) -->
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
                <a href="/dashboard" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>
                    Dashboard
                </a>

                <!-- Wallet Section -->
                <div class="pt-4">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Wallet</h3>
                    <div class="mt-2 space-y-1">
                        <a href="/fund-wallet" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-plus-circle mr-3 text-green-500"></i>
                            Fund Wallet
                        </a>
                        <a href="/transfer-funds" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg bg-blue-50 text-blue-700 border-r-2 border-blue-700">
                            <i class="fas fa-exchange-alt mr-3 text-blue-500"></i>
                            Transfer Funds
                        </a>
                        <!-- Wallet to Bank - Temporarily Hidden
                        <a href="/wallet-to-bank" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
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
                        <a href="/buy-airtime" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-mobile-alt mr-3 text-green-500"></i>
                            Buy Airtime
                        </a>
                        <a href="/buy-data" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-wifi mr-3 text-blue-500"></i>
                            Buy Data
                        </a>
                        <a href="/cable-tv" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-tv mr-3 text-purple-500"></i>
                            Cable TV
                        </a>
                        <a href="/electricity" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-bolt mr-3 text-yellow-500"></i>
                            Electricity
                        </a>
                        <a href="/exam-pins" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-graduation-cap mr-3 text-indigo-500"></i>
                            Exam Pins
                        </a>
                        <a href="/recharge-pins" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-receipt mr-3 text-orange-500"></i>
                            Recharge Pins
                        </a>
                    </div>
                </div>

                <!-- Account Section -->
                <div class="pt-4">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</h3>
                    <div class="mt-2 space-y-1">
                        <a href="/transactions" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-history mr-3 text-gray-500"></i>
                            Transactions
                        </a>
                        <a href="/profile" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-user mr-3 text-blue-500"></i>
                            Profile
                        </a>
                        <a href="/referrals" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-users mr-3 text-green-500"></i>
                            Referrals
                        </a>
                        <a href="/pricing" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-list mr-3 text-purple-500"></i>
                            Pricing
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Logout Button -->
        <div class="absolute bottom-0 w-full p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full group flex items-center px-4 py-3 text-sm font-medium text-red-700 rounded-lg hover:bg-red-50">
                    <i class="fas fa-sign-out-alt mr-3 text-red-500"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 min-w-0">
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
                            <h1 class="text-2xl font-semibold text-gray-900">Transfer Funds</h1>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Wallet Balance -->
                        <div class="hidden sm:flex items-center space-x-2 bg-green-50 px-4 py-2 rounded-full">
                            <i class="fas fa-wallet text-green-500"></i>
                            <span class="text-sm font-medium text-gray-700">Balance:</span>
                            <span class="text-lg font-bold text-green-600">₦{{ number_format(auth()->user()->sWallet ?? 0, 2) }}</span>
                        </div>

                        <!-- Profile dropdown -->
                        <div class="relative">
                            <button id="profile-menu-button" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 p-2">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ strtoupper(substr(auth()->user()->sFname ?? 'U', 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->sFname ?? 'User' }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->sType == 1 ? 'User' : (auth()->user()->sType == 2 ? 'Agent' : 'Vendor') }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>

                            <!-- Profile dropdown menu -->
                            <div id="profile-menu" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="py-1">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->sFname ?? 'User' }} {{ auth()->user()->sLname ?? '' }}</p>
                                        <p class="text-sm text-gray-500">{{ auth()->user()->sPhone ?? 'N/A' }}</p>
                                    </div>
                                    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Profile Settings
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i>Account Settings
                                    </a>
                                    <div class="border-t border-gray-100">
                                        <form method="POST" action="{{ route('logout') }}">
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

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
            <div class="container mx-auto px-6 py-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="flex items-center justify-center mb-4">
                                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                                    <i class="fas fa-exchange-alt text-4xl"></i>
                                </div>
                            </div>
                            <h1 class="text-3xl font-bold text-center mb-2">Transfer Funds</h1>
                            <p class="text-blue-100 text-lg text-center">Send money to other users on the platform</p>
                        </div>
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                            <i class="fas fa-money-bill-transfer text-9xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Transfer Form -->
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                        <form id="transfer-form" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Recipient Phone/Username -->
                                <div>
                                    <label for="recipient" class="block text-sm font-medium text-gray-700 mb-2">
                                        Recipient Phone Number or Username
                                    </label>
                                    <input type="text"
                                           id="recipient"
                                           name="recipient"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Enter phone number or username"
                                           required>
                                    <p class="text-sm text-gray-500 mt-1">Enter recipient's registered phone number or username</p>
                                </div>

                                <!-- Amount -->
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                                        <input type="number"
                                               id="amount"
                                               name="amount"
                                               class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="0.00"
                                               min="100"
                                               step="0.01"
                                               required>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">Minimum: ₦100</p>
                                </div>
                            </div>

                            <!-- Transfer Note -->
                            <div>
                                <label for="note" class="block text-sm font-medium text-gray-700 mb-2">Transfer Note (Optional)</label>
                                <textarea id="note"
                                          name="note"
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Add a note for this transfer (optional)"></textarea>
                            </div>

                            <!-- Verification PIN -->
                            <div>
                                <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">Transaction PIN</label>
                                <input type="password"
                                       id="pin"
                                       name="pin"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter your 4-digit PIN"
                                       maxlength="4"
                                       pattern="[0-9]{4}"
                                       required>
                                <p class="text-sm text-gray-500 mt-1">4-digit transaction PIN for security</p>
                            </div>

                            <!-- Transfer Summary -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Transfer Summary</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Transfer Amount:</span>
                                        <span class="font-medium" id="summary-amount">₦0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Transfer Fee:</span>
                                        <span class="font-medium text-green-600">Free</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <div class="flex justify-between">
                                            <span class="font-medium text-gray-900">Total Deduction:</span>
                                            <span class="font-bold text-gray-900" id="summary-total">₦0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-paper-plane mr-2"></i>Send Money
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Recent Transfers -->
                <div class="max-w-2xl mx-auto mt-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-xl font-semibold text-gray-900">Recent Transfers</h2>
                        </div>
                        <div class="p-6">
                            <div class="text-center py-12">
                                <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-exchange-alt text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">No transfers yet</h3>
                                <p class="text-sm text-gray-500">Your transfer history will appear here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Mobile sidebar toggle
    $('#mobile-menu-button').click(function() {
        $('#sidebar').removeClass('-translate-x-full').addClass('translate-x-0');
        $('#sidebar-overlay').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    });

    // Close sidebar when clicking overlay
    $('#sidebar-overlay').click(function() {
        $('#sidebar').removeClass('translate-x-0').addClass('-translate-x-full');
        $('#sidebar-overlay').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    });

    // Profile menu toggle
    $('#profile-menu-button').click(function(e) {
        e.stopPropagation();
        $('#profile-menu').toggleClass('hidden');
    });

    // Close profile menu when clicking outside
    $(document).click(function(event) {
        if (!$(event.target).closest('#profile-menu-button, #profile-menu').length) {
            $('#profile-menu').addClass('hidden');
        }
    });

    // Amount input handler
    $('#amount').on('input', function() {
        const amount = parseFloat($(this).val()) || 0;
        $('#summary-amount').text('₦' + amount.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#summary-total').text('₦' + amount.toLocaleString('en-US', {minimumFractionDigits: 2}));
    });

    // Transfer form submission
    $('#transfer-form').submit(function(e) {
        e.preventDefault();

        const recipient = $('#recipient').val();
        const amount = parseFloat($('#amount').val());
        const pin = $('#pin').val();

        if (!recipient || !amount || !pin) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please fill in all required fields',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        if (amount < 100) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Minimum transfer amount is ₦100',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        if (pin.length !== 4) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid PIN',
                text: 'Transaction PIN must be 4 digits',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        // Simulate transfer process
        Swal.fire({
            icon: 'info',
            title: 'Processing Transfer',
            text: 'Transfer functionality will be implemented here',
            confirmButtonColor: '#3B82F6'
        });
    });

    // Initialize sidebar state on mobile
    if ($(window).width() < 1024) {
        $('#sidebar').addClass('-translate-x-full');
    }
});
</script>
@endpush
@endsection
