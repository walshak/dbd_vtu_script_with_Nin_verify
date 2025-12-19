@extends('layouts.user-layout')

@php
    $title = 'Cable TV Subscription';
@endphp

@push('styles')
    <style>
        .provider-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .provider-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .package-card {
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .package-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .package-card.selected {
            border-color: #a855f7 !important;
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
        }
    </style>
@endpush

@section('page-content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-purple-50 py-8">
        <div class="container mx-auto px-4">
            <!-- Enhanced Header -->
            <div class="mb-8">
                <div
                    class="bg-gradient-to-r from-purple-500 via-indigo-600 to-blue-700 rounded-2xl p-8 text-white relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute -top-6 -right-6 w-40 h-40 bg-white rounded-full"></div>
                        <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white rounded-full"></div>
                        <div class="absolute top-1/3 left-1/3 w-20 h-20 bg-white rounded-full"></div>
                    </div>

                    <div class="relative z-10">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center">
                            <div>
                                <h1 class="text-3xl lg:text-4xl font-bold mb-3 flex items-center">
                                    <div class="bg-white bg-opacity-20 p-3 rounded-xl mr-4">
                                        <i class="fas fa-tv text-2xl"></i>
                                    </div>
                                    Cable TV Subscription
                                </h1>
                                <p class="text-purple-100 text-lg mb-4">Subscribe to DSTV, GOTV, StarTimes and other premium
                                    cable services</p>
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                        <i class="fas fa-satellite-dish text-blue-300 mr-2"></i>All Providers
                                    </div>
                                    <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                        <i class="fas fa-shield-alt text-green-300 mr-2"></i>Secure Payment
                                    </div>
                                    <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                        <i class="fas fa-clock text-yellow-300 mr-2"></i>Instant Activation
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 lg:mt-0">
                                <div
                                    class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl px-6 py-4 border border-white border-opacity-30">
                                    <div class="text-center">
                                        <div class="text-sm text-purple-100 mb-1">Wallet Balance</div>
                                        <div class="flex items-center justify-center space-x-2">
                                            <i class="fas fa-wallet text-yellow-300"></i>
                                            <span class="font-bold text-xl"
                                                id="walletBalance">₦{{ number_format(auth()->user()->wallet_balance, 2) }}</span>
                                        </div>
                                        <a href="{{ route('fund-wallet') }}"
                                            class="text-xs text-purple-200 hover:text-white transition-colors">
                                            <i class="fas fa-plus mr-1"></i>Add Funds
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <x-progress-indicator :steps="['Provider', 'IUC/Card', 'Package', 'Payment']" :currentStep="1" color="purple" />
            </div>

            @if ($maintenanceMode ?? false)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                        <div>
                            <strong class="text-yellow-800">Maintenance Notice:</strong>
                            <span
                                class="text-yellow-700 ml-2">{{ $maintenanceMessage ?? 'Cable TV service is temporarily unavailable.' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Enhanced Payment Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-tv text-purple-600"></i>
                            </div>
                            Cable TV Subscription
                        </h2>

                        <form id="cableTVForm" class="space-y-6">
                            @csrf

                            <!-- Enhanced Provider Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                    <i class="fas fa-satellite-dish mr-2 text-purple-600"></i>
                                    Step 1: Select Cable Provider
                                    <span class="ml-2 text-xs text-gray-500">(Required)</span>
                                </label>

                                @php
                                    $defaultProviders = [
                                        'dstv' => 'DStv',
                                        'gotv' => 'GOtv',
                                        'startimes' => 'StarTimes',
                                        'showmax' => 'Showmax',
                                    ];
                                @endphp

                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                    @forelse($providers ?? [] as $provider)
                                        @php
                                            $providerName = strtolower(trim($provider->provider));
                                            $displayName = ucfirst(trim($provider->provider));
                                        @endphp
                                        <label class="relative cursor-pointer provider-option">
                                            <input type="radio" name="decoder"
                                                value="{{ $provider->provider }}"
                                                class="sr-only peer" required>
                                            <div
                                                class="provider-card group bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-purple-300 hover:shadow-md peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:shadow-lg transition-all duration-300 transform hover:scale-105">
                                                <div class="flex flex-col items-center space-y-3">
                                                    <!-- Logo Section -->
                                                    <div class="relative">
                                                        @if(isset($provider->logo_path) && file_exists(public_path($provider->logo_path)))
                                                            <img src="{{ asset($provider->logo_path) }}"
                                                                alt="{{ $displayName }}"
                                                                class="h-12 w-auto max-w-20 group-hover:scale-110 transition-transform duration-300">
                                                        @else
                                                            <div class="flex flex-col items-center justify-center h-12 w-20 bg-purple-100 rounded text-purple-600">
                                                                <i class="fas fa-tv text-xl"></i>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Provider Info -->
                                                    <div>
                                                        <div class="font-semibold text-gray-900 text-sm">{{ $displayName }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">All packages</div>
                                                    </div>

                                                    <!-- Selection Indicator -->
                                                    <div
                                                        class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                                        <div
                                                            class="w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-check text-white text-xs"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @empty
                                        @foreach ($defaultProviders as $key => $name)
                                            <label class="relative cursor-pointer provider-option">
                                                <input type="radio" name="decoder" value="{{ $key }}"
                                                    class="sr-only peer" required>
                                                <div
                                                    class="provider-card group bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-purple-300 hover:shadow-md peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:shadow-lg transition-all duration-300 transform hover:scale-105">
                                                    <div class="flex flex-col items-center space-y-3">
                                                        <!-- Logo Section -->
                                                        <div class="relative">
                                                            <img src="{{ $providerLogos[$key] }}" alt="{{ $name }}"
                                                                class="h-12 w-auto max-w-20 group-hover:scale-110 transition-transform duration-300"
                                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                            <div
                                                                class="hidden flex-col items-center justify-center h-12 w-20 bg-purple-100 rounded text-purple-600">
                                                                <i class="fas fa-tv text-xl"></i>
                                                            </div>
                                                        </div>

                                                        <!-- Provider Info -->
                                                        <div>
                                                            <div class="font-semibold text-gray-900 text-sm">
                                                                {{ $name }}</div>
                                                            <div class="text-xs text-gray-500 mt-1">All packages</div>
                                                        </div>

                                                        <!-- Selection Indicator -->
                                                        <div
                                                            class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                                            <div
                                                                class="w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-check text-white text-xs"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    @endforelse
                                </div>
                                <div class="text-red-500 text-sm mt-2 hidden" id="provider-error">Please select a cable
                                    provider.</div>
                            </div>

                            <!-- Enhanced IUC/Smart Card Section -->
                            <div>
                                <label for="iuc_number"
                                    class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                    <i class="fas fa-id-card mr-2 text-purple-600"></i>
                                    Step 2: Enter IUC/Smart Card Number
                                    <span class="ml-2 text-xs text-gray-500">(Required)</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="iuc_number" name="iuc_number" required
                                        class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 pr-12"
                                        placeholder="Enter IUC/Smart Card Number" maxlength="12">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="iucValidationIcon" class="hidden">
                                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-red-500 text-sm mt-2 hidden" id="iuc-error">Please enter a valid
                                    IUC/Smart Card number.</div>

                                <!-- Customer Verification Section -->
                                <div id="customer-info" class="hidden mt-6">
                                    <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                                        <h4 class="text-green-800 font-semibold mb-3 flex items-center">
                                            <i class="fas fa-user-check mr-2"></i>Customer Information Verified
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-600">Customer Name:</span>
                                                <span id="customer-name" class="font-medium text-gray-900 ml-2">-</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Account Status:</span>
                                                <span id="account-status"
                                                    class="font-medium text-green-600 ml-2">Active</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Current Package:</span>
                                                <span id="current-package" class="font-medium text-gray-900 ml-2">-</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Due Date:</span>
                                                <span id="due-date" class="font-medium text-gray-900 ml-2">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Error Section -->
                                <div id="customer-error" class="hidden mt-4">
                                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                                            <div>
                                                <h4 class="text-red-800 font-medium">Invalid IUC/Smart Card</h4>
                                                <p class="text-red-600 text-sm mt-1" id="customer-error-message">Please
                                                    check the number and try again.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Package Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                    <i class="fas fa-list-alt mr-2 text-purple-600"></i>
                                    Step 3: Select Subscription Package
                                    <span class="ml-2 text-xs text-gray-500">(Required)</span>
                                </label>

                                <!-- Package Grid -->
                                <div id="packages-container">
                                    <div class="text-center py-12">
                                        <i class="fas fa-tv text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-500">Please select a provider and verify IUC to view available
                                            packages</p>
                                    </div>
                                </div>

                                <input type="hidden" id="plan_id" name="plan_id" required>
                                <div class="text-red-500 text-sm mt-2 hidden" id="plan-error">Please select a subscription
                                    package.</div>
                            </div>

                            <!-- Package Details -->
                            <div id="package-details" class="hidden">
                                <div
                                    class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-xl p-6">
                                    <h3 class="text-lg font-medium text-purple-800 mb-4 flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>Selected Package Details
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="text-center">
                                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                                <div class="text-2xl font-bold text-purple-700" id="selected-plan-name">-
                                                </div>
                                                <div class="text-sm text-gray-600 mt-1">Package Name</div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                                <div class="text-2xl font-bold text-green-600">₦<span
                                                        id="selected-plan-amount">0</span></div>
                                                <div class="text-sm text-gray-600 mt-1">Monthly Fee</div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                                <div class="text-2xl font-bold text-blue-600" id="selected-plan-duration">
                                                    -</div>
                                                <div class="text-sm text-gray-600 mt-1">Validity</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Package Features -->
                                    <div id="package-features" class="mt-6">
                                        <h4 class="font-medium text-gray-900 mb-3">Package Features:</h4>
                                        <div id="features-list" class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            <!-- Features will be populated dynamically -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Purchase Button -->
                            <div class="pt-6">
                                <button type="submit" id="purchase-btn"
                                    class="w-full bg-gradient-to-r from-purple-500 to-blue-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-purple-600 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 flex items-center justify-center space-x-3 transform hover:scale-105"
                                    disabled>
                                    <span id="purchase-text">Subscribe Now</span>
                                    <i class="fas fa-credit-card" id="purchase-icon"></i>
                                    <div class="hidden" id="purchase-loading">
                                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Service Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">
                            <i class="fas fa-info-circle mr-2"></i>Service Information
                        </h3>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center p-4 border-r border-gray-200 last:border-r-0">
                                <div class="text-2xl font-bold text-green-600">₦{{ number_format($serviceCharges ?? 50) }}
                                </div>
                                <div class="text-sm text-gray-500">Service Charge</div>
                            </div>
                            <div class="text-center p-4">
                                <div class="text-2xl font-bold text-purple-600">24/7</div>
                                <div class="text-sm text-gray-500">Support</div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 p-2 rounded-full">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    <span class="text-gray-700">Instant activation</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 p-2 rounded-full">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    <span class="text-gray-700">All subscription packages</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 p-2 rounded-full">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    <span class="text-gray-700">IUC validation</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="bg-green-100 p-2 rounded-full">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    <span class="text-gray-700">Transaction history</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    @if (!empty($recentTransactions) && count($recentTransactions) > 0)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-history mr-2"></i>Recent Subscriptions
                            </h3>
                            <div class="space-y-4">
                                @foreach ($recentTransactions as $transaction)
                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="bg-purple-100 p-2 rounded-full">
                                            <i class="fas fa-tv text-purple-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">
                                                {{ $transaction->servicename ?? 'Cable Subscription' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ₦{{ number_format($transaction->amount ?? 0) }} •
                                                {{ \Carbon\Carbon::parse($transaction->date ?? $transaction->created_at)->format('M j, Y') }}
                                            </div>
                                        </div>
                                        <div>
                                            <span
                                                class="px-2 py-1 text-xs rounded-full {{ ($transaction->status ?? 'success') == 'success' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($transaction->status ?? 'completed') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Supported Providers -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-satellite-dish mr-2"></i>Supported Providers
                        </h3>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="p-4 border border-gray-200 rounded-lg hover:border-purple-300 transition-colors">
                                <img src="/assets/images/dstv.png" alt="DSTV" class="h-10 mx-auto mb-2"
                                    onerror="this.style.display='none'">
                                <div class="text-sm font-medium text-gray-700">DSTV</div>
                            </div>
                            <div class="p-4 border border-gray-200 rounded-lg hover:border-purple-300 transition-colors">
                                <img src="/assets/images/gotv.png" alt="GOTV" class="h-10 mx-auto mb-2"
                                    onerror="this.style.display='none'">
                                <div class="text-sm font-medium text-gray-700">GOTV</div>
                            </div>
                            <div class="p-4 border border-gray-200 rounded-lg hover:border-purple-300 transition-colors">
                                <img src="/assets/images/startimes.png" alt="StarTimes" class="h-10 mx-auto mb-2"
                                    onerror="this.style.display='none'">
                                <div class="text-sm font-medium text-gray-700">StarTimes</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <x-modals.loading id="loadingModal" title="Processing..."
        message="Please wait while we process your cable TV subscription" />

    <x-modals.success id="successModal" title="Subscription Successful!" :showReceipt="false" :showClose="true" />

    <x-modals.error id="errorModal" title="Subscription Failed" buttonText="Close" />
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let validateTimeout;
            let currentStep = 1;
            let selectedPackage = null;
            let customerVerified = false;

            // Initialize
            updateProgressSteps();
            updateFormState();

            // Provider selection handlers
            $('input[name="decoder"]').change(function() {
                if ($(this).is(':checked')) {
                    $('.provider-card').removeClass('border-purple-500 bg-purple-50');
                    $(this).closest('.provider-option').find('.provider-card').addClass(
                        'border-purple-500 bg-purple-50');

                    const provider = $(this).val();
                    updateStep(2);
                    loadPackages(provider);
                    resetCustomerInfo();
                    updateFormState();
                }
            });

            // IUC number input handler
            $('#iuc_number').on('input', function() {
                const iucNumber = $(this).val().replace(/\D/g, ''); // Remove non-digits
                $(this).val(iucNumber);

                $('#iucValidationIcon').removeClass('hidden').html(
                    '<i class="fas fa-spinner fa-spin text-gray-400"></i>');

                if (iucNumber.length >= 10) {
                    $('#iuc-error').addClass('hidden');
                    clearTimeout(validateTimeout);

                    // Auto-validate after 1.5 seconds of no typing
                    validateTimeout = setTimeout(() => {
                        validateIUC();
                    }, 1500);
                } else {
                    $('#iucValidationIcon').addClass('hidden');
                    resetCustomerInfo();
                    if (iucNumber.length > 0 && iucNumber.length < 10) {
                        $('#iuc-error').removeClass('hidden');
                    }
                }

                updateFormState();
            });

            // Load packages for selected provider
            function loadPackages(provider) {
                $('#packages-container').html(`
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-4"></div>
                <p class="text-gray-500">Loading packages...</p>
            </div>
        `);

                $.ajax({
                    url: '{{ route('cable-tv.plans') }}',
                    type: 'GET',
                    data: {
                        decoder: provider
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            displayPackages(response.data);
                        } else {
                            showNoPackages();
                        }
                    },
                    error: function() {
                        showNoPackages();
                    }
                });
            }

            // Display packages
            function displayPackages(packages) {
                let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4">';

                packages.forEach(function(package, index) {
                    const planName = package.plan || package.name || 'Unknown Plan';
                    const planAmount = parseFloat(package.amount || package.price || 0);
                    const planValidity = package.validity || package.duration || '30 days';

                    html += `
                <div class="package-card bg-white border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:border-purple-300 hover:shadow-md transition-all duration-300 transform hover:scale-105"
                     data-package='${JSON.stringify(package)}'>
                    <div class="flex flex-col h-full">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-2">${planName}</h4>
                            <div class="text-2xl font-bold text-purple-600 mb-3">₦${planAmount.toLocaleString()}</div>
                            <div class="text-sm text-gray-600 mb-3">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Valid for ${planValidity}
                            </div>
                        </div>

                        <div class="border-t pt-3 mt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 rounded-full">
                                    ${package.type || 'Standard'}
                                </span>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-tv mr-1"></i>
                                    ${package.channels || 'Multiple'} Channels
                                </div>
                            </div>
                        </div>

                        <!-- Selection indicator -->
                        <div class="package-indicator absolute top-2 right-2 opacity-0 transition-opacity duration-300">
                            <div class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                });

                html += '</div>';
                $('#packages-container').html(html);

                // Add click handlers for package selection
                $('.package-card').click(function() {
                    $('.package-card').removeClass('border-purple-500 bg-purple-50').find(
                        '.package-indicator').removeClass('opacity-100');
                    $(this).addClass('border-purple-500 bg-purple-50').find('.package-indicator').addClass(
                        'opacity-100');

                    selectedPackage = JSON.parse($(this).attr('data-package'));
                    $('#plan_id').val(selectedPackage.id);

                    updateStep(Math.max(currentStep, 3));
                    showPackageDetails(selectedPackage);
                    updateFormState();
                });
            }

            // Show package details
            function showPackageDetails(package) {
                const planName = package.plan || package.name || 'Unknown Plan';
                const planAmount = parseFloat(package.amount || package.price || 0);
                const planValidity = package.validity || package.duration || '30 days';

                $('#selected-plan-name').text(planName);
                $('#selected-plan-amount').text(planAmount.toLocaleString());
                $('#selected-plan-duration').text(planValidity);

                // Show features if available
                if (package.features && package.features.length > 0) {
                    let featuresHtml = '';
                    package.features.forEach(function(feature) {
                        featuresHtml += `
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        ${feature}
                    </div>
                `;
                    });
                    $('#features-list').html(featuresHtml);
                    $('#package-features').removeClass('hidden');
                } else {
                    $('#package-features').addClass('hidden');
                }

                $('#package-details').removeClass('hidden');
            }

            // Show no packages message
            function showNoPackages() {
                $('#packages-container').html(`
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                <p class="text-gray-600">No packages available for this provider</p>
                <p class="text-sm text-gray-500 mt-2">Please select a different provider or try again later</p>
            </div>
        `);
            }

            // Validate IUC
            function validateIUC() {
                const provider = $('input[name="decoder"]:checked').val();
                const iucNumber = $('#iuc_number').val().trim();

                if (!provider || !iucNumber || iucNumber.length < 10) return;

                $('#iucValidationIcon').html('<i class="fas fa-spinner fa-spin text-gray-400"></i>');
                $('#customer-error').addClass('hidden');

                $.ajax({
                    url: '{{ route('cable-tv.validate') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        decoder: provider,
                        iuc_number: iucNumber
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            showCustomerInfo(response.data);
                            $('#iucValidationIcon').html(
                                '<i class="fas fa-check-circle text-green-500"></i>');
                            customerVerified = true;
                            updateStep(Math.max(currentStep, 3));
                        } else {
                            showCustomerError(response.message || 'Invalid IUC/Smart Card number');
                            $('#iucValidationIcon').html(
                                '<i class="fas fa-exclamation-circle text-red-500"></i>');
                            customerVerified = false;
                        }
                        updateFormState();
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        showCustomerError(response?.message || 'Failed to validate IUC/Smart Card');
                        $('#iucValidationIcon').html(
                            '<i class="fas fa-exclamation-circle text-red-500"></i>');
                        customerVerified = false;
                        updateFormState();
                    }
                });
            }

            // Show customer info
            function showCustomerInfo(data) {
                $('#customer-name').text(data.customerName || data.name || 'N/A');
                $('#account-status').text(data.status || 'Active');
                $('#current-package').text(data.currentPackage || data.package || 'N/A');
                $('#due-date').text(data.dueDate || data.nextPayment || 'N/A');

                $('#customer-info').removeClass('hidden');
                $('#customer-error').addClass('hidden');
            }

            // Show customer error
            function showCustomerError(message) {
                $('#customer-error-message').text(message);
                $('#customer-error').removeClass('hidden');
                $('#customer-info').addClass('hidden');
            }

            // Reset customer info
            function resetCustomerInfo() {
                $('#customer-info').addClass('hidden');
                $('#customer-error').addClass('hidden');
                customerVerified = false;
            }

            // Update progress steps
            function updateStep(step) {
                currentStep = Math.max(currentStep, step);
                updateProgressSteps();
            }

            function updateProgressSteps() {
                if (typeof window.updateProgressStep === 'function') {
                    window.updateProgressStep(currentStep);
                }
            }

            // Update form state
            function updateFormState() {
                const provider = $('input[name="decoder"]:checked').val();
                const iucNumber = $('#iuc_number').val().trim();
                const packageSelected = selectedPackage !== null;

                const canPurchase = provider && iucNumber.length >= 10 && packageSelected && customerVerified;

                const submitBtn = $('#purchase-btn');
                if (canPurchase) {
                    submitBtn.prop('disabled', false).removeClass('opacity-50');
                    if (currentStep < 4) updateStep(4);
                } else {
                    submitBtn.prop('disabled', true).addClass('opacity-50');
                }
            }

            // Form submission
            $('#cableTVForm').submit(function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    showToast('Please complete all required fields', 'error');
                    return;
                }

                const formData = {
                    _token: '{{ csrf_token() }}',
                    decoder: $('input[name="decoder"]:checked').val(),
                    iuc_number: $('#iuc_number').val(),
                    plan_id: $('#plan_id').val(),
                    customer_name: $('#customer-name').text()
                };

                // Show loading state
                $('#purchase-text').text('Processing...');
                $('#purchase-icon').addClass('hidden');
                $('#purchase-loading').removeClass('hidden');
                $('#purchase-btn').prop('disabled', true);

                showModal('loadingModal');

                $.ajax({
                    url: '{{ route('cable-tv.purchase') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        hideModal('loadingModal');
                        resetButtonState();

                        if (response.status === 'success' || response.success) {
                            showSuccessModal(response.message || 'Subscription successful!',
                                response.data);
                            updateWalletBalance(response.data?.new_balance);
                            resetForm();
                        } else {
                            showErrorModal(response.message ||
                                'Subscription failed. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        hideModal('loadingModal');
                        resetButtonState();

                        const response = xhr.responseJSON;
                        const message = response?.message ||
                            'Network error. Please check your connection and try again.';
                        showErrorModal(message);
                    }
                });
            });

            // Validate form
            function validateForm() {
                const provider = $('input[name="decoder"]:checked').val();
                const iucNumber = $('#iuc_number').val().trim();
                const packageSelected = selectedPackage !== null;

                return provider && iucNumber.length >= 10 && packageSelected && customerVerified;
            }

            // Reset button state
            function resetButtonState() {
                $('#purchase-text').text('Subscribe Now');
                $('#purchase-icon').removeClass('hidden');
                $('#purchase-loading').addClass('hidden');
                $('#purchase-btn').prop('disabled', false);
            }

            // Reset form
            function resetForm() {
                $('#cableTVForm')[0].reset();
                $('.provider-card').removeClass('border-purple-500 bg-purple-50');
                $('.package-card').removeClass('border-purple-500 bg-purple-50').find('.package-indicator')
                    .removeClass('opacity-100');

                selectedPackage = null;
                customerVerified = false;
                currentStep = 1;

                $('#plan_id').val('');
                $('#customer-info').addClass('hidden');
                $('#customer-error').addClass('hidden');
                $('#package-details').addClass('hidden');
                $('#packages-container').html(`
            <div class="text-center py-12">
                <i class="fas fa-tv text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Please select a provider to view available packages</p>
            </div>
        `);

                updateProgressSteps();
                updateFormState();
            }

            // Update wallet balance
            function updateWalletBalance(newBalance) {
                if (newBalance !== undefined) {
                    $('#walletBalance').text('₦' + parseFloat(newBalance).toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    }));
                }
            }

            // Modal functions
            function showSuccessModal(message, data) {
                let html = `<p class="text-lg font-medium text-green-800 mb-4">${message}</p>`;

                if (data) {
                    html += `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-left">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><strong>Reference:</strong> ${data.reference || 'N/A'}</div>
                        <div><strong>Package:</strong> ${data.package || 'N/A'}</div>
                        <div><strong>Amount:</strong> ₦${parseFloat(data.amount || 0).toLocaleString()}</div>
                        <div><strong>IUC:</strong> ${data.iuc || 'N/A'}</div>
                        <div><strong>Provider:</strong> ${data.provider || 'N/A'}</div>
                        <div><strong>Status:</strong> <span class="text-green-600">Success</span></div>
                    </div>
                </div>
            `;
                }

                $('#successMessage').html(html);
                showModal('successModal');
            }

            function showErrorModal(message) {
                $('#errorMessage').html(`<p class="text-red-600">${message}</p>`);
                showModal('errorModal');
            }

            function showModal(modalId) {
                $('#' + modalId).removeClass('hidden');
            }

            function hideModal(modalId) {
                $('#' + modalId).addClass('hidden');
            }

            window.hideModal = hideModal;

            // Toast notification function
            function showToast(message, type = 'info') {
                const bgColor = type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' :
                    type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';

                const toast = $(`
            <div class="fixed top-4 right-4 z-50 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            </div>
        `);

                $('body').append(toast);

                setTimeout(() => toast.removeClass('translate-x-full'), 100);
                setTimeout(() => {
                    toast.addClass('translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        });
    </script>
@endpush
