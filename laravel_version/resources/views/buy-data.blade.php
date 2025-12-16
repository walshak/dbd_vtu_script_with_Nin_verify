@extends('layouts.user-layout')

@php
    $title = 'Buy Data';
@endphp

@section('page-content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-full">
                        <i class="fas fa-wifi text-4xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Buy Data</h1>
                <p class="text-blue-100 text-lg text-center">Purchase data bundles for any network</p>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                <i class="fas fa-signal text-9xl"></i>
            </div>
        </div>
    </div>

    <!-- Data Purchase Form -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form id="data-form" class="space-y-6">
                @csrf
                <!-- Network Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">Select Network</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="network-option" data-network="mtn">
                            <input type="radio" id="mtn-data" name="network" value="mtn" class="sr-only">
                            <label for="mtn-data" class="network-label cursor-pointer block p-4 border-2 border-gray-200 rounded-xl text-center hover:border-yellow-300 hover:bg-yellow-50 transition-all duration-200">
                                <div class="bg-yellow-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <span class="text-yellow-600 font-bold text-lg">MTN</span>
                                </div>
                                <h3 class="font-medium text-gray-900 text-sm">MTN</h3>
                            </label>
                        </div>

                        <div class="network-option" data-network="glo">
                            <input type="radio" id="glo-data" name="network" value="glo" class="sr-only">
                            <label for="glo-data" class="network-label cursor-pointer block p-4 border-2 border-gray-200 rounded-xl text-center hover:border-green-300 hover:bg-green-50 transition-all duration-200">
                                <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <span class="text-green-600 font-bold text-lg">GLO</span>
                                </div>
                                <h3 class="font-medium text-gray-900 text-sm">Glo</h3>
                            </label>
                        </div>

                        <div class="network-option" data-network="airtel">
                            <input type="radio" id="airtel-data" name="network" value="airtel" class="sr-only">
                            <label for="airtel-data" class="network-label cursor-pointer block p-4 border-2 border-gray-200 rounded-xl text-center hover:border-red-300 hover:bg-red-50 transition-all duration-200">
                                <div class="bg-red-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <span class="text-red-600 font-bold text-sm">AIRTEL</span>
                                </div>
                                <h3 class="font-medium text-gray-900 text-sm">Airtel</h3>
                            </label>
                        </div>

                        <div class="network-option" data-network="9mobile">
                            <input type="radio" id="9mobile-data" name="network" value="9mobile" class="sr-only">
                            <label for="9mobile-data" class="network-label cursor-pointer block p-4 border-2 border-gray-200 rounded-xl text-center hover:border-green-300 hover:bg-green-50 transition-all duration-200">
                                <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <span class="text-green-600 font-bold text-sm">9MOB</span>
                                </div>
                                <h3 class="font-medium text-gray-900 text-sm">9mobile</h3>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone-data" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel"
                           id="phone-data"
                           name="phone"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="08012345678"
                           pattern="[0-9]{11}"
                           maxlength="11"
                           required>
                    <p class="text-sm text-gray-500 mt-1">Enter 11-digit phone number</p>
                </div>

                <!-- Ported Number Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox"
                           id="ported-number"
                           name="ported_number"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="ported-number" class="ml-2 block text-sm text-gray-700">
                        This is a ported number
                        <span class="text-gray-500">(Number moved from another network)</span>
                    </label>
                </div>

                <!-- Data Plans -->
                <div id="data-plans-section" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Select Data Plan</label>
                    <div id="data-plans-grid" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Data plans will be loaded here -->
                    </div>
                </div>

                <input type="hidden" id="selected-plan" name="plan_id">

                <!-- Purchase Summary -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Purchase Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Network:</span>
                            <span class="font-medium" id="summary-network-data">Not selected</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone Number:</span>
                            <span class="font-medium" id="summary-phone-data">Not entered</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Data Plan:</span>
                            <span class="font-medium" id="summary-plan">Not selected</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Price:</span>
                            <span class="font-medium" id="summary-price">₦0.00</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-900">You Pay:</span>
                                <span class="font-bold text-gray-900" id="summary-total-data">₦0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        id="purchase-data-btn"
                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fas fa-wifi mr-2"></i>Buy Data
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="max-w-2xl mx-auto mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900">Recent Purchases</h2>
            </div>
            <div class="p-6">
                <div class="text-center py-12">
                    <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-wifi text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">No purchases yet</h3>
                    <p class="text-sm text-gray-500">Your data purchase history will appear here</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let selectedNetwork = null;
    let selectedPlan = null;
    let dataPlansCache = {}; // Cache for data plans

    // Network selection
    $('.network-option').click(function() {
        $('.network-option .network-label').removeClass('border-blue-500 bg-blue-50');
        $(this).find('.network-label').addClass('border-blue-500 bg-blue-50');

        selectedNetwork = $(this).data('network');
        $(this).find('input[type="radio"]').prop('checked', true);

        loadDataPlans();
        updateSummary();
    });

    function loadDataPlans() {
        if (!selectedNetwork) return;

        const planGrid = $('#data-plans-grid');
        planGrid.html('<div class="col-span-2 text-center py-4"><i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i><p class="text-gray-500 mt-2">Loading plans...</p></div>');
        $('#data-plans-section').removeClass('hidden');

        // Fetch data plans from API
        $.ajax({
            url: '/api/data/plans',
            method: 'GET',
            data: {
                network: selectedNetwork
            },
            success: function(response) {
                if (response.status === 'success' && response.data.plans) {
                    dataPlansCache[selectedNetwork] = response.data.plans;
                    displayDataPlans(response.data.plans);
                } else {
                    planGrid.html('<div class="col-span-2 text-center py-4 text-red-500">Failed to load plans</div>');
                }
            },
            error: function() {
                planGrid.html('<div class="col-span-2 text-center py-4 text-red-500">Error loading plans. Please try again.</div>');
            }
        });
    }

    function displayDataPlans(plans) {
        const planGrid = $('#data-plans-grid');
        planGrid.empty();

        if (!plans || plans.length === 0) {
            planGrid.html('<div class="col-span-2 text-center py-4 text-gray-500">No plans available</div>');
            return;
        }

        plans.forEach(plan => {
            const planHtml = `
                <div class="plan-option cursor-pointer p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-all duration-200" data-plan-id="${plan.plan_id}" data-plan-price="${plan.amount}" data-plan-name="${plan.plan}">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-semibold text-gray-900">${plan.plan}</h4>
                        <span class="text-lg font-bold text-blue-600">₦${parseFloat(plan.amount).toLocaleString()}</span>
                    </div>
                    ${plan.validity ? `<p class="text-sm text-gray-500">Valid for ${plan.validity}</p>` : ''}
                </div>
            `;
            planGrid.append(planHtml);
        });

        // Add click handlers to plan options
        $('.plan-option').click(function() {
            $('.plan-option').removeClass('border-blue-500 bg-blue-50');
            $(this).addClass('border-blue-500 bg-blue-50');

            const planId = $(this).data('plan-id');
            const planPrice = $(this).data('plan-price');
            const planName = $(this).data('plan-name');
            
            selectedPlan = {
                id: planId,
                name: planName,
                price: parseFloat(planPrice)
            };
            $('#selected-plan').val(planId);

            updateSummary();
        });
    }

    // Phone number input
    $('#phone-data').on('input', function() {
        let phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 11) {
            phone = phone.substr(0, 11);
        }
        $(this).val(phone);
        updateSummary();
    });

    function updateSummary() {
        const phone = $('#phone-data').val();

        // Update summary display
        $('#summary-network-data').text(selectedNetwork ? selectedNetwork.toUpperCase() : 'Not selected');
        $('#summary-phone-data').text(phone || 'Not entered');

        if (selectedPlan) {
            $('#summary-plan').text(`${selectedPlan.name} (${selectedPlan.validity})`);
            $('#summary-price').text('₦' + selectedPlan.price.toLocaleString());
            $('#summary-total-data').text('₦' + selectedPlan.price.toLocaleString());
        } else {
            $('#summary-plan').text('Not selected');
            $('#summary-price').text('₦0.00');
            $('#summary-total-data').text('₦0.00');
        }

        // Enable/disable purchase button
        const canPurchase = selectedNetwork && phone.length === 11 && selectedPlan;
        $('#purchase-data-btn').prop('disabled', !canPurchase);
    }

    // Form submission
    $('#data-form').submit(function(e) {
        e.preventDefault();

        if (!selectedNetwork || !selectedPlan) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Selection',
                text: 'Please select network and data plan',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        const phone = $('#phone-data').val();
        if (phone.length !== 11) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Phone Number',
                text: 'Please enter a valid 11-digit phone number',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        const portedNumber = $('#ported-number').is(':checked');

        // Show confirmation dialog
        Swal.fire({
            icon: 'question',
            title: 'Confirm Purchase',
            html: `
                <div class="text-left">
                    <p><strong>Network:</strong> ${selectedNetwork.toUpperCase()}</p>
                    <p><strong>Phone:</strong> ${phone}</p>
                    <p><strong>Data Plan:</strong> ${selectedPlan.name}</p>
                    <p><strong>Price:</strong> ₦${selectedPlan.price.toLocaleString()}</p>
                    ${portedNumber ? '<p class="text-blue-600"><i class="fas fa-info-circle"></i> Ported number</p>' : ''}
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Buy Now',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3B82F6'
        }).then((result) => {
            if (result.isConfirmed) {
                processPurchase(phone, portedNumber);
            }
        });
    });

    function processPurchase(phone, portedNumber) {
        const $btn = $('#purchase-data-btn');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

        $.ajax({
            url: '/api/data/purchase',
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                network: selectedNetwork,
                phone: phone,
                plan_id: selectedPlan.id,
                ported_number: portedNumber
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Purchase Successful!',
                        html: `
                            <div class="text-left">
                                <p><strong>Reference:</strong> ${response.data.reference}</p>
                                <p><strong>Network:</strong> ${response.data.network}</p>
                                <p><strong>Phone:</strong> ${response.data.phone}</p>
                                <p><strong>Plan:</strong> ${response.data.plan}</p>
                                <p><strong>Amount:</strong> ₦${parseFloat(response.data.amount).toLocaleString()}</p>
                                <p><strong>New Balance:</strong> ₦${parseFloat(response.data.balance).toLocaleString()}</p>
                            </div>
                        `,
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        // Reset form
                        $('#data-form')[0].reset();
                        $('.network-option .network-label').removeClass('border-blue-500 bg-blue-50');
                        $('.plan-option').removeClass('border-blue-500 bg-blue-50');
                        $('#data-plans-section').addClass('hidden');
                        selectedNetwork = null;
                        selectedPlan = null;
                        updateSummary();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Purchase Failed',
                        text: response.message || 'An error occurred',
                        confirmButtonColor: '#EF4444'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Purchase Failed',
                    text: errorMessage,
                    confirmButtonColor: '#EF4444'
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }
});
</script>
@endpush
@endsection
