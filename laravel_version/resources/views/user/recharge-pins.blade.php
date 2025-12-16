@extends('layouts.user-layout')

@section('title', 'Recharge Pins')

@section('page-content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-100 p-4">
    <!-- Header -->
    <div class="max-w-7xl mx-auto">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-6 shadow-xl mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        <i class="fas fa-credit-card"></i>
                        Recharge Pins
                    </h1>
                    <p class="text-purple-100 mt-2">Purchase recharge pins for all major networks with instant email delivery</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                    <span class="text-white font-semibold text-lg">
                        <i class="fas fa-wallet mr-2"></i>₦{{ number_format(auth()->user()->balance ?? 0, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Purchase Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-credit-card"></i>
                            Purchase Recharge Pin
                        </h2>
                    </div>
                    <div class="p-6">
                        <form id="rechargePinForm" class="space-y-6">
                            @csrf

                            <!-- Network and Denomination Selection -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Network Selection -->
                                <div>
                                    <label for="network" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-signal text-purple-600 mr-2"></i>Network Provider
                                    </label>
                                    <select id="network" name="network" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors">
                                        <option value="">Select Network</option>
                                        @if(isset($networks))
                                            @foreach($networks as $network)
                                                <option value="{{ $network->network }}">{{ $network->network }}</option>
                                            @endforeach
                                        @else
                                            <option value="MTN">MTN</option>
                                            <option value="GLO">Glo</option>
                                            <option value="AIRTEL">Airtel</option>
                                            <option value="9MOBILE">9mobile</option>
                                        @endif
                                    </select>
                                </div>

                                <!-- Denomination Selection -->
                                <div>
                                    <label for="denomination" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-money-bill text-purple-600 mr-2"></i>Denomination (₦)
                                    </label>
                                    <select id="denomination" name="denomination" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors">
                                        <option value="">Select Amount</option>
                                        @if(isset($denominations))
                                            @foreach($denominations as $denomination)
                                                <option value="{{ $denomination->amount }}">₦{{ number_format($denomination->amount) }}</option>
                                            @endforeach
                                        @else
                                            <option value="100">₦100</option>
                                            <option value="200">₦200</option>
                                            <option value="400">₦400</option>
                                            <option value="500">₦500</option>
                                            <option value="750">₦750</option>
                                            <option value="1000">₦1,000</option>
                                            <option value="1500">₦1,500</option>
                                            <option value="2000">₦2,000</option>
                                            <option value="3000">₦3,000</option>
                                            <option value="5000">₦5,000</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    <i class="fas fa-bolt text-purple-600 mr-2"></i>Quick Select Amount
                                </label>
                                <div class="grid grid-cols-3 md:grid-cols-5 gap-3">
                                    <button type="button" class="quick-amount-btn bg-gradient-to-r from-purple-100 to-pink-100 border-2 border-purple-200 text-purple-700 py-2 px-3 rounded-xl hover:from-purple-200 hover:to-pink-200 transition-all duration-300 font-semibold" data-amount="100">
                                        ₦100
                                    </button>
                                    <button type="button" class="quick-amount-btn bg-gradient-to-r from-purple-100 to-pink-100 border-2 border-purple-200 text-purple-700 py-2 px-3 rounded-xl hover:from-purple-200 hover:to-pink-200 transition-all duration-300 font-semibold" data-amount="200">
                                        ₦200
                                    </button>
                                    <button type="button" class="quick-amount-btn bg-gradient-to-r from-purple-100 to-pink-100 border-2 border-purple-200 text-purple-700 py-2 px-3 rounded-xl hover:from-purple-200 hover:to-pink-200 transition-all duration-300 font-semibold" data-amount="500">
                                        ₦500
                                    </button>
                                    <button type="button" class="quick-amount-btn bg-gradient-to-r from-purple-100 to-pink-100 border-2 border-purple-200 text-purple-700 py-2 px-3 rounded-xl hover:from-purple-200 hover:to-pink-200 transition-all duration-300 font-semibold" data-amount="1000">
                                        ₦1K
                                    </button>
                                    <button type="button" class="quick-amount-btn bg-gradient-to-r from-purple-100 to-pink-100 border-2 border-purple-200 text-purple-700 py-2 px-3 rounded-xl hover:from-purple-200 hover:to-pink-200 transition-all duration-300 font-semibold" data-amount="2000">
                                        ₦2K
                                    </button>
                                </div>
                            </div>

                            <!-- Quantity and Email -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-sort-numeric-up text-purple-600 mr-2"></i>Quantity
                                    </label>
                                    <input type="number" id="quantity" name="quantity" min="1" max="10" value="1" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors">
                                    <p class="text-sm text-gray-600 mt-1">Maximum 10 pins per transaction</p>
                                </div>

                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-envelope text-purple-600 mr-2"></i>Customer Email
                                    </label>
                                    <input type="email" id="customer_email" name="customer_email" value="{{ auth()->user()->email ?? '' }}" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors">
                                    <p class="text-sm text-gray-600 mt-1">Email address to receive the pins</p>
                                </div>
                            </div>

                            <!-- Transaction Summary -->
                            <div id="transaction-summary" class="hidden">
                                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                        <i class="fas fa-receipt text-purple-600"></i>
                                        Purchase Summary
                                    </h3>
                                    <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-4">
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-800" id="summary-network">-</div>
                                            <div class="text-sm text-gray-600">Network</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-800" id="summary-denomination">-</div>
                                            <div class="text-sm text-gray-600">Denomination</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-800" id="summary-quantity">-</div>
                                            <div class="text-sm text-gray-600">Quantity</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-800" id="summary-unit-price">-</div>
                                            <div class="text-sm text-gray-600">Unit Price</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-purple-600" id="summary-total">-</div>
                                            <div class="text-sm text-gray-600">Total Amount</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="purchase-btn"
                                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-4 px-6 rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <span id="btn-text">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Purchase Recharge Pin
                                </span>
                                <span id="btn-loader" class="hidden">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Processing...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Features -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-violet-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-star"></i>
                            Service Features
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-bolt text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Instant Delivery</p>
                                    <p class="text-sm text-gray-600">Pins delivered to email instantly</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-network-wired text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">All Networks</p>
                                    <p class="text-sm text-gray-600">MTN, Glo, Airtel & 9mobile</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-percentage text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Best Rates</p>
                                    <p class="text-sm text-gray-600">Competitive pricing with discounts</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-headset text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">24/7 Support</p>
                                    <p class="text-sm text-gray-600">Round-the-clock customer service</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Network Providers -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-signal"></i>
                            Available Networks
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                                <div class="w-12 h-12 bg-yellow-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold text-lg">
                                    M
                                </div>
                                <div class="font-semibold text-gray-800">MTN</div>
                                <div class="text-sm text-gray-600">Available</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-xl border border-green-200">
                                <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold text-lg">
                                    G
                                </div>
                                <div class="font-semibold text-gray-800">Glo</div>
                                <div class="text-sm text-gray-600">Available</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-xl border border-red-200">
                                <div class="w-12 h-12 bg-red-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold text-lg">
                                    A
                                </div>
                                <div class="font-semibold text-gray-800">Airtel</div>
                                <div class="text-sm text-gray-600">Available</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-xl border border-blue-200">
                                <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold text-lg">
                                    9
                                </div>
                                <div class="font-semibold text-gray-800">9mobile</div>
                                <div class="text-sm text-gray-600">Available</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            Important Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-arrow-right text-orange-500 mt-0.5 flex-shrink-0"></i>
                                <span class="text-gray-700">Recharge pins are delivered instantly to your email</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-arrow-right text-orange-500 mt-0.5 flex-shrink-0"></i>
                                <span class="text-gray-700">Please check spam folder if you don't receive email</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-arrow-right text-orange-500 mt-0.5 flex-shrink-0"></i>
                                <span class="text-gray-700">Pins are valid and can be used immediately</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-arrow-right text-orange-500 mt-0.5 flex-shrink-0"></i>
                                <span class="text-gray-700">Contact support for any delivery issues</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-blue-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-history"></i>
                            Recent Pins
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center py-8">
                            <i class="fas fa-credit-card text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">Your recent recharge pins will appear here</p>
                            <a href="{{ route('transactions') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                View All Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for form functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('rechargePinForm');
    const networkSelect = document.getElementById('network');
    const denominationSelect = document.getElementById('denomination');
    const quantityInput = document.getElementById('quantity');
    const summaryDiv = document.getElementById('transaction-summary');
    const purchaseBtn = document.getElementById('purchase-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoader = document.getElementById('btn-loader');
    const quickAmountBtns = document.querySelectorAll('.quick-amount-btn');

    // Quick amount button handlers
    quickAmountBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');

            // Remove active state from all buttons
            quickAmountBtns.forEach(b => {
                b.classList.remove('border-purple-500', 'bg-purple-100', 'text-purple-800');
                b.classList.add('border-purple-200', 'bg-gradient-to-r', 'from-purple-100', 'to-pink-100', 'text-purple-700');
            });

            // Add active state to clicked button
            this.classList.remove('border-purple-200', 'bg-gradient-to-r', 'from-purple-100', 'to-pink-100', 'text-purple-700');
            this.classList.add('border-purple-500', 'bg-purple-100', 'text-purple-800');

            // Set the denomination select
            denominationSelect.value = amount;
            updateSummary();
        });
    });

    // Update summary when form values change
    function updateSummary() {
        const network = networkSelect.value;
        const denomination = denominationSelect.value;
        const quantity = quantityInput.value;

        if (network && denomination && quantity) {
            document.getElementById('summary-network').textContent = network;
            document.getElementById('summary-denomination').textContent = '₦' + parseInt(denomination).toLocaleString();
            document.getElementById('summary-quantity').textContent = quantity;

            // For now, using the denomination as unit price (would be fetched from API normally)
            const unitPrice = parseInt(denomination);
            const total = unitPrice * parseInt(quantity);

            document.getElementById('summary-unit-price').textContent = '₦' + unitPrice.toLocaleString();
            document.getElementById('summary-total').textContent = '₦' + total.toLocaleString();

            summaryDiv.classList.remove('hidden');
        } else {
            summaryDiv.classList.add('hidden');
        }
    }

    // Event listeners
    networkSelect.addEventListener('change', updateSummary);
    denominationSelect.addEventListener('change', function() {
        // Update quick amount buttons when denomination is changed via select
        quickAmountBtns.forEach(btn => {
            const amount = btn.getAttribute('data-amount');
            if (amount === this.value) {
                btn.classList.remove('border-purple-200', 'bg-gradient-to-r', 'from-purple-100', 'to-pink-100', 'text-purple-700');
                btn.classList.add('border-purple-500', 'bg-purple-100', 'text-purple-800');
            } else {
                btn.classList.remove('border-purple-500', 'bg-purple-100', 'text-purple-800');
                btn.classList.add('border-purple-200', 'bg-gradient-to-r', 'from-purple-100', 'to-pink-100', 'text-purple-700');
            }
        });
        updateSummary();
    });
    quantityInput.addEventListener('input', updateSummary);

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Basic validation
        if (!networkSelect.value || !denominationSelect.value || !quantityInput.value) {
            alert('Please fill in all required fields.');
            return;
        }

        // Show loading state
        purchaseBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoader.classList.remove('hidden');

        // Get form data
        const formData = new FormData(form);

        // Submit the form
        fetch('{{ route("recharge-pins.purchase") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success message
                showSuccessModal(data.message, data.data || {});

                // Reset form
                form.reset();
                updateSummary();

                // Reset quick amount buttons
                quickAmountBtns.forEach(btn => {
                    btn.classList.remove('border-purple-500', 'bg-purple-100', 'text-purple-800');
                    btn.classList.add('border-purple-200', 'bg-gradient-to-r', 'from-purple-100', 'to-pink-100', 'text-purple-700');
                });

                // Update wallet balance if provided
                if (data.new_balance) {
                    // Update balance display
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            } else {
                showErrorModal(data.message || 'Purchase failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorModal('An error occurred. Please try again.');
        })
        .finally(() => {
            // Reset button state
            purchaseBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoader.classList.add('hidden');
        });
    });

    // Modal functions
    function showSuccessModal(message, data) {
        let content = `<div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-4">${message}</div>`;

        if (data.pins && data.pins.length > 0) {
            content += `
                <div class="mt-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Generated Recharge Pins:</h4>
                    <div class="space-y-2">
            `;
            data.pins.forEach(pin => {
                content += `<div class="bg-purple-600 text-white px-3 py-2 rounded-lg font-mono text-center">${pin}</div>`;
            });
            content += `
                    </div>
                    <p class="text-sm text-gray-600 mt-3">Pins have been sent to your email address.</p>
                </div>
            `;
        }

        if (data.reference) {
            content += `<div class="mt-3 text-sm text-gray-600">Reference: <strong>${data.reference}</strong></div>`;
        }

        showModal('Success', content, 'success');
    }

    function showErrorModal(message) {
        const content = `<div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">${message}</div>`;
        showModal('Error', content, 'error');
    }

    function showModal(title, content, type) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl overflow-hidden max-w-md w-full">
                <div class="bg-gradient-to-r ${type === 'success' ? 'from-green-500 to-emerald-500' : 'from-red-500 to-pink-500'} p-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                        ${title}
                    </h3>
                </div>
                <div class="p-6">
                    ${content}
                </div>
                <div class="px-6 pb-6">
                    <button onclick="this.closest('.fixed').remove()"
                            class="w-full bg-${type === 'success' ? 'green' : 'red'}-600 text-white py-3 px-4 rounded-xl font-semibold hover:bg-${type === 'success' ? 'green' : 'red'}-700 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
});
</script>

@endsection
