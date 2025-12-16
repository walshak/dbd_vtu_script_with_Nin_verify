@extends('layouts.user-layout')

@section('title', 'Transfer Funds')

@section('page-content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-500 to-blue-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-full">
                        <i class="fas fa-exchange-alt text-4xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Transfer Funds</h1>
                <p class="text-purple-100 text-lg text-center">Send money to other users quickly and securely</p>
                <div class="text-center mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-6 py-3 inline-block">
                        <p class="text-sm font-medium">Available Balance</p>
                        <p class="text-2xl font-bold">₦{{ number_format($balance ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                <i class="fas fa-paper-plane text-9xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Transfer Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900">Send Money</h2>
                <p class="text-gray-600 mt-1">Transfer funds to another user's wallet</p>
            </div>
            <div class="p-6">
                <form id="transfer-form" class="space-y-6">
                    @csrf
                    <div>
                        <label for="recipient_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Recipient Email or Username
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text"
                                   id="recipient_email"
                                   name="recipient_email"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Enter email or username"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Amount to Transfer
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Enter amount"
                                   min="50"
                                   max="50000"
                                   oninput="calculateTransferFee()"
                                   required>
                        </div>
                        <small class="text-gray-500">Minimum: ₦50, Maximum: ₦50,000</small>
                    </div>

                    <div>
                        <label for="transfer_fee" class="block text-sm font-medium text-gray-700 mb-2">
                            Transfer Fee
                        </label>
                        <input type="text"
                               id="transfer_fee"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                               readonly>
                        <small class="text-gray-500">Free for transfers above ₦1,000</small>
                    </div>

                    <div>
                        <label for="total_deduct" class="block text-sm font-medium text-gray-700 mb-2">
                            Total to Deduct
                        </label>
                        <input type="text"
                               id="total_deduct"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 font-semibold"
                               readonly>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description (Optional)
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                  placeholder="What's this transfer for?"></textarea>
                    </div>

                    <button type="submit"
                            id="transfer-btn"
                            class="w-full bg-gradient-to-r from-purple-500 to-blue-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-purple-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane mr-2"></i>Send Money
                    </button>
                </form>
            </div>
        </div>

        <!-- Transfer Info & Recent Transfers -->
        <div class="space-y-6">
            <!-- Transfer Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Transfer Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Instant Transfer</h4>
                                <p class="text-sm text-gray-600">Money is transferred instantly to recipient's wallet</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-shield-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Secure & Safe</h4>
                                <p class="text-sm text-gray-600">All transfers are encrypted and secure</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-purple-100 p-2 rounded-full">
                                <i class="fas fa-coins text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Low Fees</h4>
                                <p class="text-sm text-gray-600">₦25 fee for transfers below ₦1,000. Free above ₦1,000</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transfers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Transfers</h3>
                </div>
                <div class="p-6">
                    <div class="text-center py-8">
                        <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exchange-alt text-gray-400 text-2xl"></i>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 mb-1">No transfers yet</h4>
                        <p class="text-sm text-gray-500">Your transfer history will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Transfer form submission
    $('#transfer-form').submit(function(e) {
        e.preventDefault();

        const amount = parseFloat($('#amount').val());
        const recipientEmail = $('#recipient_email').val();

        if (!amount || amount < 50) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Please enter a valid amount (minimum ₦50)',
                confirmButtonColor: '#8B5CF6'
            });
            return;
        }

        if (!recipientEmail) {
            Swal.fire({
                icon: 'warning',
                title: 'Recipient Required',
                text: 'Please enter recipient email or username',
                confirmButtonColor: '#8B5CF6'
            });
            return;
        }

        // Disable submit button
        $('#transfer-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

        // Make transfer request
        $.ajax({
            url: '/wallet/transfer',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                recipient_email: recipientEmail,
                amount: amount,
                description: $('#description').val()
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transfer Successful!',
                        text: 'Money has been sent successfully',
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        // Reset form
                        $('#transfer-form')[0].reset();
                        calculateTransferFee();
                        // Reload page to update balance
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Transfer Failed',
                        text: response.message || 'Transfer could not be completed',
                        confirmButtonColor: '#EF4444'
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Transfer Failed',
                    text: response?.message || 'An error occurred during transfer',
                    confirmButtonColor: '#EF4444'
                });
            },
            complete: function() {
                // Re-enable submit button
                $('#transfer-btn').prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i>Send Money');
            }
        });
    });
});

function calculateTransferFee() {
    const amount = parseFloat($('#amount').val()) || 0;

    if (amount > 0) {
        let fee = 0;
        if (amount < 1000) {
            fee = 25; // ₦25 fee for transfers below ₦1,000
        }

        const totalDeduct = amount + fee;

        $('#transfer_fee').val(fee > 0 ? '₦' + fee.toLocaleString() : 'Free');
        $('#total_deduct').val('₦' + totalDeduct.toLocaleString());
    } else {
        $('#transfer_fee').val('');
        $('#total_deduct').val('');
    }
}
</script>
@endpush
@endsection
