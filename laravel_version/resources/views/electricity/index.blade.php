@extends('layouts.user-layout')

@php
    $title = 'Electricity Bill Payment';
@endphp

@push('styles')
<style>
    .disco-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .disco-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .meter-type-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .meter-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

</style>
@endpush

@section('page-content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Enhanced Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-yellow-500 via-orange-600 to-red-700 rounded-2xl p-8 text-white relative overflow-hidden">
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
                                    <i class="fas fa-bolt text-2xl"></i>
                                </div>
                                Electricity Bill Payment
                            </h1>
                            <p class="text-yellow-100 text-lg mb-4">Purchase electricity tokens for all major Nigerian DISCOs</p>
                            <div class="flex flex-wrap gap-4 text-sm">
                                <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                    <i class="fas fa-zap text-yellow-300 mr-2"></i>Instant Token
                                </div>
                                <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                    <i class="fas fa-shield-alt text-green-300 mr-2"></i>All DISCOs
                                </div>
                                <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                    <i class="fas fa-clock text-blue-300 mr-2"></i>24/7 Available
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 lg:mt-0">
                            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl px-6 py-4 border border-white border-opacity-30">
                                <div class="text-center">
                                    <div class="text-sm text-yellow-100 mb-1">Wallet Balance</div>
                                    <div class="flex items-center justify-center space-x-2">
                                        <i class="fas fa-wallet text-yellow-300"></i>
                                        <span class="font-bold text-xl" id="walletBalance">₦{{ number_format(auth()->user()->wallet_balance, 2) }}</span>
                                    </div>
                                    <a href="{{ route('fund-wallet') }}" class="text-xs text-yellow-200 hover:text-white transition-colors">
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
            <x-progress-indicator
                :steps="['DISCO', 'Meter Info', 'Amount', 'Payment']"
                :currentStep="1"
                color="orange"
            />
        </div>

        @if($maintenanceMode)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                <div>
                    <strong class="text-yellow-800">Maintenance Notice:</strong>
                    <span class="text-yellow-700 ml-2">{{ $maintenanceMessage }}</span>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Enhanced Payment Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <div class="bg-orange-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-bolt text-orange-600"></i>
                        </div>
                        Purchase Electricity Token
                    </h2>

                    <form id="electricityForm" class="space-y-6">
                        @csrf

                        <!-- Enhanced DISCO Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-building mr-2 text-orange-600"></i>
                                Step 1: Select Electricity Distribution Company (DISCO)
                                <span class="ml-2 text-xs text-gray-500">(Required)</span>
                            </label>

                            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($providers as $provider)
                                    @php
                                        $discoName = strtoupper($provider->ePlan);
                                    @endphp
                                    <label class="relative cursor-pointer disco-option">
                                        <input type="radio" name="provider" value="{{ $provider->ePlan }}"
                                               data-rate="{{ $provider->ePrice }}"
                                               data-charges="{{ $serviceCharges }}"
                                               class="sr-only peer" required>
                                        <div class="disco-card group bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-orange-300 hover:shadow-md peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:shadow-lg transition-all duration-300 transform hover:scale-105">
                                            <div class="flex flex-col items-center space-y-3">
                                                <!-- Logo Section -->
                                                <div class="relative">
                                                    @if($provider->logo_path && file_exists(public_path($provider->logo_path)))
                                                        <img src="{{ asset($provider->logo_path) }}"
                                                             alt="{{ $discoName }}"
                                                             class="h-12 w-auto max-w-20 group-hover:scale-110 transition-transform duration-300">
                                                    @else
                                                        <div class="flex flex-col items-center justify-center h-12 w-20 bg-orange-100 rounded text-orange-600">
                                                            <i class="fas fa-bolt text-xl"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- DISCO Info -->
                                                <div>
                                                    <div class="font-semibold text-gray-900 text-sm">{{ $discoName }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">₦{{ number_format($provider->ePrice, 2) }}/kWh</div>
                                                </div>

                                                <!-- Selection Indicator -->
                                                <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                                    <div class="w-5 h-5 bg-orange-500 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-check text-white text-xs"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div class="text-red-500 text-sm mt-2 hidden" id="provider-error">Please select an electricity provider.</div>
                        </div>

                        <!-- Enhanced Meter Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-tachometer-alt mr-2 text-orange-600"></i>
                                Step 2: Select Meter Type
                                <span class="ml-2 text-xs text-gray-500">(Required)</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="relative cursor-pointer meter-type-option">
                                    <input type="radio" name="meter_type" value="prepaid"
                                           class="sr-only peer" required>
                                    <div class="meter-type-card bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-green-300 hover:shadow-md peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-lg transition-all duration-300">
                                        <div class="flex items-center space-x-4">
                                            <div class="bg-green-100 p-3 rounded-full">
                                                <i class="fas fa-credit-card text-green-600 text-xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">Prepaid Meter</div>
                                                <div class="text-sm text-gray-600 mt-1">Pay before you consume electricity</div>
                                                <div class="text-xs text-green-600 mt-1">
                                                    <i class="fas fa-check mr-1"></i>Instant token generation
                                                </div>
                                            </div>
                                            <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                                <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </div>
                                            </div>
                        </div>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer meter-type-option">
                                    <input type="radio" name="meter_type" value="postpaid"
                                           class="sr-only peer">
                                    <div class="meter-type-card bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-blue-300 hover:shadow-md peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg transition-all duration-300">
                                        <div class="flex items-center space-x-4">
                                            <div class="bg-blue-100 p-3 rounded-full">
                                                <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">Postpaid Meter</div>
                                                <div class="text-sm text-gray-600 mt-1">Pay after consuming electricity</div>
                                                <div class="text-xs text-blue-600 mt-1">
                                                    <i class="fas fa-calendar mr-1"></i>Monthly billing cycle
                                                </div>
                                            </div>
                                            <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                                <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="text-red-500 text-sm mt-2 hidden" id="meter-type-error">Please select a meter type.</div>
                        </div>

                        <!-- Enhanced Meter Number Input -->
                        <div>
                            <label for="meter_number" class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-hashtag mr-2 text-orange-600"></i>
                                Step 3: Enter Meter Number
                                <span class="ml-2 text-xs text-gray-500">(Required)</span>
                            </label>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input type="text" id="meter_number" name="meter_number" required
                                           class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 pr-12"
                                           placeholder="Enter your meter number"
                                           maxlength="15">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <div id="meterValidationIcon" class="hidden">
                                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="validateMeterBtn"
                                        class="bg-gradient-to-r from-orange-500 to-red-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-orange-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
                                        disabled>
                                    <i class="fas fa-check-circle mr-2"></i>Validate
                                </button>
                            </div>
                            <div class="text-red-500 text-sm mt-2 hidden" id="meter-error">Please enter a valid meter number.</div>

                            <!-- Customer Verification Section -->
                            <div id="customer-info" class="hidden mt-4">
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
                                            <span class="text-gray-600">Customer Address:</span>
                                            <span id="customer-address" class="font-medium text-gray-900 ml-2">-</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Tariff Class:</span>
                                            <span id="tariff-class" class="font-medium text-gray-900 ml-2">-</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Outstanding Balance:</span>
                                            <span id="outstanding-balance" class="font-medium text-gray-900 ml-2">₦0.00</span>
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
                                            <h4 class="text-red-800 font-medium">Invalid Meter Number</h4>
                                            <p class="text-red-600 text-sm mt-1" id="customer-error-message">Please check the meter number and try again.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Input -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-naira-sign mr-2 text-orange-600"></i>
                                Step 4: Enter Amount (₦)
                                <span class="ml-2 text-xs text-gray-500">(Required)</span>
                            </label>
                            <input type="number" id="amount" name="amount" required
                                   class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                                   placeholder="Enter amount" min="{{ $minimumAmount }}"
                                   max="{{ $maximumAmount }}">
                            <div class="text-red-500 text-sm mt-2 hidden" id="amount-error">
                                Amount must be between ₦{{ number_format($minimumAmount) }} and ₦{{ number_format($maximumAmount) }}.
                            </div>
                            <div class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>Min: ₦{{ number_format($minimumAmount) }} | Max: ₦{{ number_format($maximumAmount) }}
                            </div>
                        </div>

                        <!-- Transaction Summary -->
                        <div id="transactionSummary" class="hidden">
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                                <h3 class="text-lg font-medium text-gray-800 mb-4">
                                    <i class="fas fa-calculator mr-2"></i>Transaction Summary
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-orange-600" id="summaryAmount">₦0.00</div>
                                        <div class="text-sm text-gray-500">Amount</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-yellow-600" id="summaryCharges">₦0.00</div>
                                        <div class="text-sm text-gray-500">Service Charge</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600" id="summaryTotal">₦0.00</div>
                                        <div class="text-sm text-gray-500">Total</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600" id="summaryUnits">0 kWh</div>
                                        <div class="text-sm text-gray-500">Units (Estimated)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" id="purchaseBtn" disabled
                                    class="flex-1 bg-gradient-to-r from-orange-500 to-red-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-orange-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-shopping-cart mr-2"></i>Purchase Token
                            </button>

                            <button type="button" id="resetFormBtn"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300">
                                <i class="fas fa-refresh mr-2"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Sidebar -->
            <div class="lg:col-span-1">
                <!-- Quick Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-info-circle mr-2"></i>Quick Information
                    </h3>

                    <div class="space-y-6">
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Service Hours</h4>
                                <p class="text-sm text-gray-600">24/7 Available</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-bolt text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Instant Delivery</h4>
                                <p class="text-sm text-gray-600">Tokens delivered immediately</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="bg-purple-100 p-2 rounded-full">
                                <i class="fas fa-shield-alt text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Secure Payment</h4>
                                <p class="text-sm text-gray-600">Your transactions are protected</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="bg-yellow-100 p-2 rounded-full">
                                <i class="fas fa-headset text-yellow-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Customer Support</h4>
                                <p class="text-sm text-gray-600">We're here to help</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Charges -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-tags mr-2"></i>Service Charges
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Service Fee:</span>
                            <span class="font-semibold text-gray-900">₦{{ number_format($serviceCharges) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Min Amount:</span>
                            <span class="font-semibold text-gray-900">₦{{ number_format($minimumAmount) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Max Amount:</span>
                            <span class="font-semibold text-gray-900">₦{{ number_format($maximumAmount) }}</span>
                        </div>
                    </div>
                </div>

                <!-- How It Works -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-question-circle mr-2"></i>How It Works
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-orange-100 text-orange-600 rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-semibold">1</div>
                            <p class="text-sm text-gray-600">Select your electricity distribution company (DISCO)</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-orange-100 text-orange-600 rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-semibold">2</div>
                            <p class="text-sm text-gray-600">Choose your meter type (Prepaid or Postpaid)</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-orange-100 text-orange-600 rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-semibold">3</div>
                            <p class="text-sm text-gray-600">Enter and validate your meter number</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-orange-100 text-orange-600 rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-semibold">4</div>
                            <p class="text-sm text-gray-600">Enter amount and complete payment</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-orange-100 text-orange-600 rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 text-sm font-semibold">5</div>
                            <p class="text-sm text-gray-600">Receive your token instantly</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<x-modals.loading
    id="loadingModal"
    title="Processing..."
    message="Please wait while we process your electricity purchase"
/>

<x-modals.success
    id="successModal"
    title="Purchase Successful!"
    :showReceipt="true"
    :showClose="true"
/>

<x-modals.error
    id="errorModal"
    title="Purchase Failed"
    buttonText="Try Again"
/>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let meterValidated = false;
    let customerData = null;
    const serviceCharges = {{ $serviceCharges }};
    const minimumAmount = {{ $minimumAmount }};
    const maximumAmount = {{ $maximumAmount }};

    // Enable validate button when all required fields are filled
    function checkValidationEnabled() {
        const provider = $('input[name="provider"]:checked').val();
        const meterType = $('input[name="meter_type"]:checked').val();
        const meterNumber = $('#meter_number').val().trim();

        const canValidate = provider && meterType && meterNumber.length >= 8;
        $('#validateMeterBtn').prop('disabled', !canValidate);
    }

    // Watch for changes
    $('input[name="provider"], input[name="meter_type"], #meter_number').on('change input', function() {
        meterValidated = false;
        $('#purchaseBtn').prop('disabled', true);
        $('#customer-info').addClass('hidden');
        $('#customer-error').addClass('hidden');
        checkValidationEnabled();
    });

    // Validate meter button click
    $('#validateMeterBtn').on('click', function() {
        const provider = $('input[name="provider"]:checked').val();
        const meterType = $('input[name="meter_type"]:checked').val();
        const meterNumber = $('#meter_number').val().trim();

        if (!provider || !meterType || !meterNumber) {
            showError('Please fill all required fields');
            return;
        }

        // Get provider name from the label
        const providerName = $('input[name="provider"]:checked').closest('.disco-option').find('.font-semibold').text().trim();

        validateMeter(providerName, meterNumber, meterType);
    });

    function validateMeter(provider, meterNumber, meterType) {
        // Show loading state
        $('#validateMeterBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Validating...');
        $('#customer-info').addClass('hidden');
        $('#customer-error').addClass('hidden');
        $('#meterValidationIcon').removeClass('hidden');

        $.ajax({
            url: '{{ route("electricity.validate-meter") }}',
            method: 'POST',
            data: {
                provider: provider,
                meter_number: meterNumber,
                meter_type: meterType,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status === 'success') {
                    meterValidated = true;
                    customerData = response.data;

                    // Show customer info
                    $('#customer-name').text(customerData.customer_name || 'N/A');
                    $('#customer-address').text(customerData.address || 'N/A');
                    $('#tariff-class').text(customerData.tariff_class || 'N/A');
                    $('#outstanding-balance').text('₦' + (customerData.outstanding_balance || '0.00'));
                    $('#customer-info').removeClass('hidden');

                    // Enable purchase button if amount is valid
                    checkPurchaseEnabled();

                    showSuccess('Meter validated successfully!');
                } else {
                    showValidationError(response.message || 'Invalid meter number');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to validate meter. Please try again.';
                showValidationError(message);
            },
            complete: function() {
                $('#validateMeterBtn').prop('disabled', false).html('<i class="fas fa-check-circle mr-2"></i>Validate');
                $('#meterValidationIcon').addClass('hidden');
                checkValidationEnabled();
            }
        });
    }

    function showValidationError(message) {
        $('#customer-error-message').text(message);
        $('#customer-error').removeClass('hidden');
        meterValidated = false;
        $('#purchaseBtn').prop('disabled', true);
    }

    // Check if purchase can be enabled
    function checkPurchaseEnabled() {
        const amount = parseFloat($('#amount').val());
        const isAmountValid = amount >= minimumAmount && amount <= maximumAmount;
        $('#purchaseBtn').prop('disabled', !(meterValidated && isAmountValid));
    }

    // Watch amount changes
    $('#amount').on('input', function() {
        checkPurchaseEnabled();
        updateTransactionSummary();
    });

    function updateTransactionSummary() {
        const amount = parseFloat($('#amount').val()) || 0;
        if (amount > 0) {
            const total = amount + serviceCharges;
            const provider = $('input[name="provider"]:checked').closest('.disco-option').find('.font-semibold').text().trim();
            const rate = parseFloat($('input[name="provider"]:checked').closest('.disco-option').find('.text-xs').text().replace('₦', '').replace('/kWh', ''));
            const estimatedUnits = (amount / rate).toFixed(2);

            $('#summaryAmount').text('₦' + amount.toLocaleString());
            $('#summaryCharges').text('₦' + serviceCharges.toLocaleString());
            $('#summaryTotal').text('₦' + total.toLocaleString());
            $('#summaryUnits').text(estimatedUnits + ' kWh');
            $('#transactionSummary').removeClass('hidden');
        } else {
            $('#transactionSummary').addClass('hidden');
        }
    }

    // Form submission
    $('#electricityForm').on('submit', function(e) {
        e.preventDefault();

        if (!meterValidated) {
            showError('Please validate meter number first');
            return;
        }

        // TODO: Implement purchase logic
        showError('Purchase functionality coming soon');
    });

    // Reset form
    $('#resetFormBtn').on('click', function() {
        $('#electricityForm')[0].reset();
        meterValidated = false;
        customerData = null;
        $('#customer-info').addClass('hidden');
        $('#customer-error').addClass('hidden');
        $('#transactionSummary').addClass('hidden');
        $('#purchaseBtn').prop('disabled', true);
        $('#validateMeterBtn').prop('disabled', true);
    });

    // Utility functions
    function showError(message) {
        // Use existing error modal or alert
        alert(message);
    }

    function showSuccess(message) {
        // Use existing success notification or alert
        console.log(message);
    }

    // Modal functions
    window.showModal = function(modalId) {
        $('#' + modalId).removeClass('hidden');
    };

    window.hideModal = function(modalId) {
        $('#' + modalId).addClass('hidden');
    };

    window.downloadReceipt = function() {
        window.print();
    };
});
</script>
@endpush
