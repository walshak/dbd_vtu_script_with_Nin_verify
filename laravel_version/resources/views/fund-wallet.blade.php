@extends('layouts.app')

@section('title', 'Fund Wallet - VASTLEAD ')

@section('content')
<div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar (same as dashboard) -->
    <div id="sidebar" class="bg-white shadow-lg w-64 min-h-screen fixed left-0 top-0 transform transition-transform duration-300 ease-in-out z-30 lg:translate-x-0 lg:static lg:inset-0">
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
                        <a href="/fund-wallet" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg bg-green-50 text-green-700 border-r-2 border-green-700">
                            <i class="fas fa-plus-circle mr-3 text-green-500"></i>
                            Fund Wallet
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-exchange-alt mr-3 text-blue-500"></i>
                            Transfer Funds
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-university mr-3 text-purple-500"></i>
                            Wallet to Bank
                        </a>
                    </div>
                </div>

                <!-- Services Section -->
                <div class="pt-4">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Services</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-mobile-alt mr-3 text-green-500"></i>
                            Buy Airtime
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-wifi mr-3 text-blue-500"></i>
                            Buy Data
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-tv mr-3 text-purple-500"></i>
                            Cable TV
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-bolt mr-3 text-yellow-500"></i>
                            Electricity
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-graduation-cap mr-3 text-indigo-500"></i>
                            Exam Pins
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-receipt mr-3 text-orange-500"></i>
                            Recharge Pins
                        </a>
                    </div>
                </div>

                <!-- Account Section -->
                <div class="pt-4">
                    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</h3>
                    <div class="mt-2 space-y-1">
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-history mr-3 text-gray-500"></i>
                            Transactions
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-user mr-3 text-blue-500"></i>
                            Profile
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
                            <i class="fas fa-users mr-3 text-green-500"></i>
                            Referrals
                        </a>
                        <a href="#" class="group flex items-center px-4 py-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900">
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
    <div class="flex-1 lg:ml-64">
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
                            <h1 class="text-2xl font-semibold text-gray-900">Fund Wallet</h1>
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
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                    <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="flex items-center justify-center mb-4">
                                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                                    <i class="fas fa-plus-circle text-4xl"></i>
                                </div>
                            </div>
                            <h1 class="text-3xl font-bold text-center mb-2">Fund Your Wallet</h1>
                            <p class="text-green-100 text-lg text-center">Add money to your wallet to purchase our services</p>
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
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Fidelity Bank -->
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-blue-900">Fidelity Bank</h3>
                                        <i class="fas fa-university text-blue-600 text-2xl"></i>
                                    </div>
                                    <div class="space-y-3 mb-4">
                                        <div>
                                            <p class="text-sm text-blue-700 font-medium">Account Number</p>
                                            <p class="text-lg font-bold text-blue-900" id="fidelity-account">{{ auth()->user()->sFidelityBank ?? '1234567890' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-blue-700 font-medium">Account Name</p>
                                            <p class="text-blue-900 font-medium">{{ auth()->user()->sFname ?? 'User' }} {{ auth()->user()->sLname ?? 'Account' }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-red-600 mb-4">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Automated transfer attracts ₦50 charges only
                                    </p>
                                    <button onclick="copyToClipboard('fidelity-account')" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-copy mr-2"></i>Copy Account Number
                                    </button>
                                </div>

                                <!-- Moniepoint Bank -->
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-purple-900">Moniepoint Bank</h3>
                                        <i class="fas fa-university text-purple-600 text-2xl"></i>
                                    </div>
                                    <div class="space-y-3 mb-4">
                                        <div>
                                            <p class="text-sm text-purple-700 font-medium">Account Number</p>
                                            <p class="text-lg font-bold text-purple-900" id="moniepoint-account">{{ auth()->user()->sRolexBank ?? '0987654321' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-purple-700 font-medium">Account Name</p>
                                            <p class="text-purple-900 font-medium">{{ auth()->user()->sFname ?? 'User' }} {{ auth()->user()->sLname ?? 'Account' }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-red-600 mb-4">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Automated transfer attracts ₦50 charges only
                                    </p>
                                    <button onclick="copyToClipboard('moniepoint-account')" class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                                        <i class="fas fa-copy mr-2"></i>Copy Account Number
                                    </button>
                                </div>

                                <!-- Wema Bank -->
                                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-green-900">Wema Bank</h3>
                                        <i class="fas fa-university text-green-600 text-2xl"></i>
                                    </div>
                                    <div class="space-y-3 mb-4">
                                        <div>
                                            <p class="text-sm text-green-700 font-medium">Account Number</p>
                                            <p class="text-lg font-bold text-green-900" id="wema-account">{{ auth()->user()->sBankNo ?? '1122334455' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-green-700 font-medium">Account Name</p>
                                            <p class="text-green-900 font-medium">{{ auth()->user()->sFname ?? 'User' }} {{ auth()->user()->sLname ?? 'Account' }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-red-600 mb-4">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Automated transfer attracts ₦50 charges only
                                    </p>
                                    <button onclick="copyToClipboard('wema-account')" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-copy mr-2"></i>Copy Account Number
                                    </button>
                                </div>
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
                                    @csrf
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

                                    <input type="hidden" id="paystack-charges" value="1.5">
                                    <input type="hidden" name="email" value="{{ auth()->user()->sEmail }}">

                                    <button type="submit"
                                            id="pay-now-btn"
                                            class="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-green-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-credit-card mr-2"></i>Pay Now with Paystack
                                    </button>
                                </form>
                            </div>
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
                                                <p class="text-lg font-bold text-orange-900">VASTLEAD  LIMITED</p>
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

        // Here you would integrate with Paystack
        Swal.fire({
            icon: 'info',
            title: 'Payment Gateway',
            text: 'Paystack integration will be implemented here',
            confirmButtonColor: '#3B82F6'
        });
    });

    // Initialize sidebar state on mobile
    if ($(window).width() < 1024) {
        $('#sidebar').addClass('-translate-x-full');
    }
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
</script>
@endpush
@endsection
