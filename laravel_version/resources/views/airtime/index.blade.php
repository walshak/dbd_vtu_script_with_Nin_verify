@extends('layouts.user-layout')

@php
    $title = 'Buy Airtime';
@endphp

@push('styles')
<style>
    .network-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .network-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .network-card.selected {
        border-color: #10b981 !important;
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .amount-btn {
        transition: all 0.2s ease;
    }
    .amount-btn:hover {
        transform: scale(1.05);
    }
    .amount-btn.selected {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-color: #10b981;
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    .success-animation {
        animation: successPulse 0.6s ease-out;
    }
    @keyframes successPulse {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endpush

@section('page-content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-20 p-4 rounded-full mr-4">
                                <i class="fas fa-mobile-alt text-4xl"></i>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold mb-2">Buy Airtime</h1>
                                <p class="text-green-100 text-lg">Purchase airtime for all Nigerian networks with instant delivery</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl px-4 py-3">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-wallet text-yellow-300 text-xl"></i>
                                <div>
                                    <p class="text-sm text-white opacity-75">Wallet Balance</p>
                                    <p class="text-xl font-bold" id="walletBalance">₦{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                <i class="fas fa-signal text-9xl"></i>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="mb-8">
        <x-progress-indicator
            :steps="['Select Network', 'Enter Details', 'Complete Purchase']"
            :currentStep="1"
            color="green"
        />
    </div>

    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Purchase Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <form id="airtimeForm" class="space-y-8">
                        @csrf
                        <input type="hidden" name="type" value="VTU">

                        <!-- Network Selection -->
                        <div role="radiogroup" aria-labelledby="network-label" aria-required="true">
                            <label id="network-label" class="block text-lg font-semibold text-gray-800 mb-6">
                                <i class="fas fa-signal mr-3 text-green-600" aria-hidden="true"></i>Select Network Provider
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($networks as $network)
                                <div class="network-option" data-network="{{ strtolower($network->network) }}">
                                    <input type="radio"
                                           id="network-{{ strtolower($network->network) }}"
                                           name="network"
                                           value="{{ strtolower($network->network) }}"
                                           class="sr-only"
                                           required
                                           aria-describedby="network-error"
                                           aria-invalid="false">
                                    <label for="network-{{ strtolower($network->network) }}"
                                           class="network-card cursor-pointer block p-6 border-2 border-gray-200 rounded-xl text-center"
                                           tabindex="0"
                                           role="radio"
                                           aria-checked="false">
                                        <div class="bg-gradient-to-br from-green-100 to-blue-100 w-16 h-16 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:shadow-lg">
                                            @if($network->logoPath && file_exists(public_path($network->logoPath)))
                                                <img src="{{ asset($network->logoPath) }}" alt="{{ $network->network }}" class="w-12 h-12 object-contain">
                                            @else
                                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                                     style="background: {{ $network->brand_color ?? '#10b981' }}">
                                                    <span class="text-white font-bold text-lg">{{ strtoupper(substr($network->network, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <h3 class="font-semibold text-gray-900 text-lg">{{ strtoupper($network->network) }}</h3>
                                        <div class="flex items-center justify-center mt-2">
                                            <span class="text-green-600 text-sm font-medium">Available</span>
                                            <div class="w-2 h-2 bg-green-500 rounded-full ml-2 pulse-animation"></div>
                                        </div>
                                        <small class="text-gray-500 block mt-1">Instant delivery</small>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-red-500 text-sm mt-3 hidden" id="network-error" role="alert" aria-live="polite">
                                <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>Please select a network provider.
                            </div>
                        </div>

                        <!-- Phone Number Input with Ported Number Toggle -->
                        <div>
                            <label for="phone" class="block text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-phone mr-3 text-blue-600" aria-hidden="true"></i>Enter Phone Number
                            </label>
                            <div class="space-y-4">
                                <div class="relative">
                                    <input type="tel"
                                           id="phone"
                                           name="phone"
                                           class="w-full px-4 py-4 pl-12 pr-20 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-lg"
                                           placeholder="08012345678"
                                           maxlength="11"
                                           required
                                           aria-label="Phone number"
                                           aria-describedby="phone-error phone-help"
                                           aria-required="true"
                                           aria-invalid="false"
                                           inputmode="numeric"
                                           autocomplete="tel">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                        <span id="phoneValidationIcon" class="hidden">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Ported Number Toggle -->
                                <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Number Status</span>
                                        <div class="relative inline-flex items-center">
                                            <input type="checkbox" id="ported_number" name="ported_number"
                                                   class="sr-only peer">
                                            <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        </div>
                                    </div>
                                    <label for="ported_number" class="text-sm text-gray-600 cursor-pointer flex items-center">
                                        <i class="fas fa-exchange-alt mr-2 text-blue-600"></i>Ported Number
                                    </label>
                                    <div class="text-xs text-gray-500 mt-1">Enable if number was moved from another network</div>
                                </div>

                                <div class="text-red-500 text-sm hidden" id="phone-error" role="alert" aria-live="polite">
                                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>Please enter a valid 11-digit phone number.
                                </div>
                                <p class="text-gray-500 text-sm" id="phone-help">
                                    <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>Enter the phone number you want to recharge
                                </p>
                            </div>
                        </div>

                        <!-- Amount Selection -->
                        <div>
                            <label for="amount" class="block text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-money-bill-wave mr-3 text-green-600" aria-hidden="true"></i>Select Amount
                            </label>

                            <!-- Quick Amount Buttons -->
                            <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-4" role="group" aria-label="Quick amount selection">
                                @foreach([50, 100, 200, 500, 1000, 2000] as $amount)
                                <button type="button"
                                        class="amount-btn px-4 py-3 border-2 border-gray-200 rounded-lg text-center font-semibold text-gray-700 hover:border-green-500 hover:bg-green-50"
                                        data-amount="{{ $amount }}"
                                        aria-label="Select {{ $amount }} naira"
                                        tabindex="0">
                                    ₦{{ $amount }}
                                </button>
                                @endforeach
                            </div>

                            <!-- Custom Amount Input -->
                            <div class="relative">
                                <input type="number"
                                       id="amount"
                                       name="amount"
                                       class="w-full px-4 py-4 pl-12 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-lg"
                                       placeholder="Enter custom amount"
                                       min="50"
                                       max="50000"
                                       step="1"
                                       required
                                       aria-label="Custom amount"
                                       aria-describedby="amount-error amount-help"
                                       aria-required="true"
                                       aria-invalid="false"
                                       inputmode="numeric">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4">
                                    <span class="text-gray-600 font-semibold">₦</span>
                                </div>
                            </div>
                            <div class="text-red-500 text-sm mt-3 hidden" id="amount-error" role="alert" aria-live="polite">
                                <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>Amount must be between ₦50 and ₦50,000.
                            </div>
                            <p class="text-gray-500 text-sm mt-2" id="amount-help">
                                <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>Minimum: ₦50, Maximum: ₦50,000
                            </p>
                        </div>

                        <!-- Purchase Button -->
                        <div class="pt-6">
                            <button type="submit"
                                    id="purchase-btn"
                                    class="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-green-600 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 flex items-center justify-center space-x-3"
                                    disabled
                                    aria-label="Complete airtime purchase"
                                    aria-disabled="true">
                                <span id="purchase-text">Complete Purchase</span>
                                <i class="fas fa-arrow-right" id="purchase-icon"></i>
                                <div class="hidden" id="purchase-loading">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="lg:col-span-1">
                <!-- Service Features -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">
                        <i class="fas fa-info-circle mr-2"></i>Service Features
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">Instant delivery</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">All networks supported</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">Ported number support</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">VTU & Share and Sell</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <span class="text-gray-700">24/7 availability</span>
                        </div>
                    </div>
                </div>

                <!-- Transaction Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-6" role="complementary" aria-label="Transaction summary">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-receipt mr-3 text-purple-600" aria-hidden="true"></i>Transaction Summary
                    </h3>

                    <div id="summary-content" class="space-y-4 hidden" aria-live="polite">
                        <!-- Network Info -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Network:</span>
                            <div class="flex items-center">
                                <span id="summary-network" class="font-semibold text-gray-900"></span>
                            </div>
                        </div>

                        <!-- Phone Info -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Phone:</span>
                            <span id="summary-phone" class="font-semibold text-gray-900"></span>
                        </div>

                        <!-- Amount Info -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Amount:</span>
                            <span id="summary-amount" class="font-semibold text-gray-900"></span>
                        </div>

                        <!-- Pricing Info -->
                        <div id="pricing-info" class="hidden border-t pt-4">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Customer Price:</span>
                                    <span id="customer-price" class="font-medium">₦0</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Our Price:</span>
                                    <span id="our-price" class="font-medium text-green-600">₦0</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">You Save:</span>
                                    <span id="discount-amount" class="font-medium text-green-600">₦0</span>
                                </div>
                                <div class="border-t pt-2">
                                    <div class="flex justify-between font-semibold">
                                        <span>Total:</span>
                                        <span id="total-amount" class="text-lg text-green-600">₦0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="summary-empty" class="text-center py-8">
                        <div class="text-gray-400 mb-3">
                            <i class="fas fa-clipboard-list text-3xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Fill the form to see transaction summary</p>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-history mr-2"></i>Recent Transactions
                        </h4>
                        <div class="space-y-3" id="recent-transactions">
                            <div class="text-center py-4 text-gray-500 text-sm">
                                <i class="fas fa-clock mb-2 block"></i>
                                No recent transactions
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
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

<x-modals.loading
    id="loadingModal"
    title="Processing..."
    message="Please wait while we process your airtime purchase"
/>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Airtime page script loaded');
    let currentPricing = null;
    let currentStep = 1;
    let lastSuccessfulTransaction = null;

    // Initialize
    loadRecentTransactions();
    updateProgressSteps();

    // Network selection handlers
    $('input[name="network"]').change(function() {
        console.log('Network changed:', $(this).val());
        if ($(this).is(':checked')) {
            $('.network-card').removeClass('selected');
            $(this).closest('.network-option').find('.network-card').addClass('selected');

            updateStep(2);
            loadPricing();
            updateSummary();
            validateForm();
        }
    });

    // Also handle label clicks directly
    $('.network-card').click(function() {
        console.log('Network card clicked');
        const input = $(this).closest('.network-option').find('input[name="network"]');
        if (!input.is(':checked')) {
            input.prop('checked', true).trigger('change');
        }
    });

    // Phone input validation
    $('#phone').on('input', function() {
        const phone = $(this).val().replace(/\D/g, '');
        $(this).val(phone);

        if (phone.length === 11) {
            $('#phoneValidationIcon').removeClass('hidden');
            $('#phone-error').addClass('hidden');
            validatePhoneNetwork();
        } else {
            $('#phoneValidationIcon').addClass('hidden');
            if (phone.length > 0 && phone.length < 11) {
                $('#phone-error').removeClass('hidden');
            }
        }

        updateSummary();
        validateForm();
    });

    // Amount button handlers
    $('.amount-btn').click(function() {
        console.log('Amount button clicked:', $(this).data('amount'));
        $('.amount-btn').removeClass('selected');
        $(this).addClass('selected');

        const amount = $(this).data('amount');
        $('#amount').val(amount);
        $('#amount-error').addClass('hidden');

        updateStep(3);
        updatePricing();
        updateSummary();
        validateForm();
    });

    // Amount input handler
    $('#amount').on('input', function() {
        $('.amount-btn').removeClass('selected');

        const amount = parseFloat($(this).val()) || 0;

        if (amount >= 50 && amount <= 50000) {
            $('#amount-error').addClass('hidden');
            updateStep(3);
            updatePricing();
        } else if (amount > 0) {
            $('#amount-error').removeClass('hidden');
        }

        updateSummary();
        validateForm();
    });

    // Load pricing for selected network
    function loadPricing() {
        const network = $('input[name="network"]:checked').val();
        if (!network) return;

        $.ajax({
            url: '{{ route("airtime.pricing") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                network: network
            },
            success: function(response) {
                if (response.status === 'success') {
                    currentPricing = response.data;
                    updatePricing();
                }
            },
            error: function() {
                showToast('Failed to load pricing information', 'error');
            }
        });
    }

    // Validate phone number against network
    function validatePhoneNetwork() {
        const phone = $('#phone').val();
        const network = $('input[name="network"]:checked').val();

        if (!phone || !network) return;

        $.ajax({
            url: '{{ route("phone.validate") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                phone: phone,
                network: network,
                ported_number: $('#ported_number').is(':checked')
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#phoneValidationIcon').html('<i class="fas fa-check-circle text-green-500"></i>');
                } else {
                    $('#phoneValidationIcon').html('<i class="fas fa-exclamation-circle text-yellow-500"></i>');
                    showToast('Phone number might not match selected network', 'warning');
                }
            }
        });
    }

    // Update pricing display
    function updatePricing() {
        const amount = parseFloat($('#amount').val()) || 0;

        if (amount === 0 || !currentPricing) {
            $('#pricing-info').addClass('hidden');
            return;
        }

        const discount = currentPricing.discount || 0;
        const discountAmount = (amount * discount) / 100;
        const finalAmount = amount - discountAmount;

        $('#customer-price').text('₦' + amount.toLocaleString());
        $('#our-price').text('₦' + finalAmount.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#discount-amount').text('₦' + discountAmount.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#total-amount').text('₦' + finalAmount.toLocaleString('en-US', {minimumFractionDigits: 2}));

        $('#pricing-info').removeClass('hidden');
    }

    // Update transaction summary
    function updateSummary() {
        const network = $('input[name="network"]:checked').val();
        const phone = $('#phone').val();
        const amount = parseFloat($('#amount').val()) || 0;

        if (network || phone || amount > 0) {
            $('#summary-content').removeClass('hidden');
            $('#summary-empty').addClass('hidden');

            $('#summary-network').text(network ? network.toUpperCase() : '-');
            $('#summary-phone').text(phone || '-');
            $('#summary-amount').text(amount > 0 ? '₦' + amount.toLocaleString() : '-');
        } else {
            $('#summary-content').addClass('hidden');
            $('#summary-empty').removeClass('hidden');
        }
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

    // Validate form
    function validateForm() {
        const network = $('input[name="network"]:checked').val();
        const phone = $('#phone').val();
        const amount = parseFloat($('#amount').val()) || 0;

        const isValid = network && phone.length === 11 && amount >= 50 && amount <= 50000;

        const submitBtn = $('#purchase-btn');
        if (isValid) {
            submitBtn.prop('disabled', false).removeClass('opacity-50');
        } else {
            submitBtn.prop('disabled', true).addClass('opacity-50');
        }

        return isValid;
    }

    // Form submission
    $('#airtimeForm').submit(function(e) {
        e.preventDefault();

        if (!validateForm()) {
            showToast('Please fill all required fields correctly', 'error');
            return;
        }

        const formData = {
            _token: '{{ csrf_token() }}',
            network: $('input[name="network"]:checked').val(),
            phone: $('#phone').val(),
            amount: parseFloat($('#amount').val()),
            type: 'VTU',
            ported_number: $('#ported_number').is(':checked')
        };

        $('#purchase-text').text('Processing...');
        $('#purchase-icon').addClass('hidden');
        $('#purchase-loading').removeClass('hidden');
        $('#purchase-btn').prop('disabled', true);

        showModal('loadingModal');

        $.ajax({
            url: '{{ route("airtime.purchase") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                hideModal('loadingModal');
                resetButtonState();

                if (response.status === 'success') {
                    lastSuccessfulTransaction = response.data;
                    showSuccessModal(response.message, response.data);
                    updateWalletBalance(response.data.new_balance);
                    loadRecentTransactions();
                    resetForm();
                } else {
                    showErrorModal(response.message || 'Purchase failed. Please try again.');
                }
            },
            error: function(xhr) {
                hideModal('loadingModal');
                resetButtonState();

                const response = xhr.responseJSON;
                const message = response?.message || 'Network error. Please check your connection and try again.';
                showErrorModal(message);
            }
        });
    });

    // Reset button state
    function resetButtonState() {
        $('#purchase-text').text('Complete Purchase');
        $('#purchase-icon').removeClass('hidden');
        $('#purchase-loading').addClass('hidden');
        $('#purchase-btn').prop('disabled', false);
    }

    // Load recent transactions
    function loadRecentTransactions() {
        $.ajax({
            url: '{{ route("transactions.recent") }}',
            type: 'GET',
            data: { service: 'airtime', limit: 3 },
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(transaction) {
                        const status = transaction.status === 'Completed' ? 'text-green-600' :
                                     transaction.status === 'Pending' ? 'text-yellow-600' : 'text-red-600';

                        html += `
                            <div class="p-3 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-sm">${transaction.network}</p>
                                        <p class="text-xs text-gray-500">${transaction.phone}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-sm">₦${parseFloat(transaction.amount).toLocaleString()}</p>
                                        <p class="text-xs ${status}">${transaction.status}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#recent-transactions').html(html);
                }
            }
        });
    }

    // Update wallet balance
    function updateWalletBalance(newBalance) {
        if (newBalance !== undefined) {
            $('#walletBalance').text('₦' + parseFloat(newBalance).toLocaleString('en-US', {minimumFractionDigits: 2}));
        }
    }

    // Reset form
    function resetForm() {
        $('#airtimeForm')[0].reset();
        $('.network-card').removeClass('selected');
        $('.amount-btn').removeClass('selected');
        $('#pricing-info').addClass('hidden');
        $('#phoneValidationIcon').addClass('hidden');
        $('.text-red-500').addClass('hidden');

        currentStep = 1;
        updateProgressSteps();
        updateSummary();
        validateForm();
    }

    // Modal functions
    function showSuccessModal(message, data) {
        let html = `<p class="text-lg font-medium text-green-800 mb-4">${message}</p>`;

        if (data) {
            html += `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-left">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><strong>Reference:</strong> ${data.reference || 'N/A'}</div>
                        <div><strong>Amount:</strong> ₦${parseFloat(data.amount || 0).toLocaleString()}</div>
                        <div><strong>Phone:</strong> ${data.phone || 'N/A'}</div>
                        <div><strong>Network:</strong> ${data.network || 'N/A'}</div>
                        <div><strong>New Balance:</strong> ₦${parseFloat(data.new_balance || 0).toLocaleString()}</div>
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

    // Download receipt function
    window.downloadReceipt = function() {
        if (lastSuccessfulTransaction && lastSuccessfulTransaction.reference) {
            const baseUrl = '{{ url("transactions/receipt") }}';
            window.open(`${baseUrl}/${lastSuccessfulTransaction.reference}`, '_blank');
        }
    };

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
