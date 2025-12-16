@extends('layouts.user-layout')

@section('title', 'Withdraw Funds')

@section('page-content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-full">
                        <i class="fas fa-money-bill-wave text-4xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Withdraw Funds</h1>
                <p class="text-green-100 text-lg text-center">Transfer money from your wallet to your bank account</p>
                <div class="text-center mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-6 py-3 inline-block">
                        <p class="text-sm font-medium">Available Balance</p>
                        <p class="text-2xl font-bold">₦{{ number_format($balance ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                <i class="fas fa-university text-9xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Withdrawal Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900">Bank Withdrawal</h2>
                <p class="text-gray-600 mt-1">Withdraw funds to your registered bank account</p>
            </div>
            <div class="p-6">
                <form id="withdrawal-form" class="space-y-6">
                    @csrf
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Bank Name
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-university"></i>
                            </span>
                            <select id="bank_name"
                                    name="bank_name"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    required>
                                <option value="">Select your bank</option>
                                <option value="access_bank">Access Bank</option>
                                <option value="gtbank">GTBank</option>
                                <option value="first_bank">First Bank</option>
                                <option value="zenith_bank">Zenith Bank</option>
                                <option value="uba">UBA</option>
                                <option value="fidelity_bank">Fidelity Bank</option>
                                <option value="union_bank">Union Bank</option>
                                <option value="sterling_bank">Sterling Bank</option>
                                <option value="stanbic_ibtc">Stanbic IBTC</option>
                                <option value="fcmb">FCMB</option>
                                <option value="wema_bank">Wema Bank</option>
                                <option value="providus_bank">Providus Bank</option>
                                <option value="kuda_bank">Kuda Bank</option>
                                <option value="opay">Opay</option>
                                <option value="palmpay">PalmPay</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Number
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-credit-card"></i>
                            </span>
                            <input type="text"
                                   id="account_number"
                                   name="account_number"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Enter 10-digit account number"
                                   maxlength="10"
                                   onblur="verifyAccountNumber()"
                                   required>
                        </div>
                        <div id="account-name" class="mt-2 text-sm text-gray-600 hidden">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            <span></span>
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Withdrawal Amount
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Enter amount"
                                   min="500"
                                   max="100000"
                                   oninput="calculateWithdrawalFee()"
                                   required>
                        </div>
                        <small class="text-gray-500">Minimum: ₦500, Maximum: ₦100,000</small>
                    </div>

                    <div>
                        <label for="withdrawal_fee" class="block text-sm font-medium text-gray-700 mb-2">
                            Processing Fee
                        </label>
                        <input type="text"
                               id="withdrawal_fee"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                               readonly>
                        <small class="text-gray-500">₦50 flat fee per withdrawal</small>
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

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-3"></i>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium mb-1">Processing Time</p>
                                <p>Withdrawals are processed within 1-3 business hours during banking hours (9am - 4pm, Monday to Friday)</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            id="withdraw-btn"
                            class="w-full bg-gradient-to-r from-green-500 to-teal-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-green-600 hover:to-teal-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-money-bill-wave mr-2"></i>Request Withdrawal
                    </button>
                </form>
            </div>
        </div>

        <!-- Withdrawal Info & History -->
        <div class="space-y-6">
            <!-- Withdrawal Guidelines -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Withdrawal Guidelines</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-green-100 p-2 rounded-full">
                                <i class="fas fa-clock text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Processing Time</h4>
                                <p class="text-sm text-gray-600">1-3 hours during banking hours</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 p-2 rounded-full">
                                <i class="fas fa-coins text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Processing Fee</h4>
                                <p class="text-sm text-gray-600">₦50 flat fee per withdrawal</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-purple-100 p-2 rounded-full">
                                <i class="fas fa-shield-alt text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Secure Process</h4>
                                <p class="text-sm text-gray-600">All withdrawals are verified before processing</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-red-100 p-2 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Account Verification</h4>
                                <p class="text-sm text-gray-600">Ensure account details match your registered information</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Withdrawals -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Withdrawals</h3>
                </div>
                <div class="p-6">
                    <div class="text-center py-8">
                        <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-money-bill-wave text-gray-400 text-2xl"></i>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 mb-1">No withdrawals yet</h4>
                        <p class="text-sm text-gray-500">Your withdrawal history will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Withdrawal form submission
    $('#withdrawal-form').submit(function(e) {
        e.preventDefault();

        const amount = parseFloat($('#amount').val());
        const bankName = $('#bank_name').val();
        const accountNumber = $('#account_number').val();

        if (!amount || amount < 500) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Please enter a valid amount (minimum ₦500)',
                confirmButtonColor: '#10B981'
            });
            return;
        }

        if (!bankName) {
            Swal.fire({
                icon: 'warning',
                title: 'Bank Required',
                text: 'Please select your bank',
                confirmButtonColor: '#10B981'
            });
            return;
        }

        if (!accountNumber || accountNumber.length !== 10) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Account Number',
                text: 'Please enter a valid 10-digit account number',
                confirmButtonColor: '#10B981'
            });
            return;
        }

        // Disable submit button
        $('#withdraw-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

        // Make withdrawal request
        $.ajax({
            url: '/wallet/withdraw',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                bank_name: bankName,
                account_number: accountNumber,
                amount: amount
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Withdrawal Requested!',
                        text: 'Your withdrawal request has been submitted and will be processed within 1-3 hours',
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        // Reset form
                        $('#withdrawal-form')[0].reset();
                        calculateWithdrawalFee();
                        $('#account-name').addClass('hidden');
                        // Reload page to update balance
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Withdrawal Failed',
                        text: response.message || 'Withdrawal request could not be processed',
                        confirmButtonColor: '#EF4444'
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Withdrawal Failed',
                    text: response?.message || 'An error occurred during withdrawal request',
                    confirmButtonColor: '#EF4444'
                });
            },
            complete: function() {
                // Re-enable submit button
                $('#withdraw-btn').prop('disabled', false).html('<i class="fas fa-money-bill-wave mr-2"></i>Request Withdrawal');
            }
        });
    });
});

function calculateWithdrawalFee() {
    const amount = parseFloat($('#amount').val()) || 0;

    if (amount > 0) {
        const fee = 50; // ₦50 flat fee
        const totalDeduct = amount + fee;

        $('#withdrawal_fee').val('₦' + fee.toLocaleString());
        $('#total_deduct').val('₦' + totalDeduct.toLocaleString());
    } else {
        $('#withdrawal_fee').val('');
        $('#total_deduct').val('');
    }
}

function verifyAccountNumber() {
    const accountNumber = $('#account_number').val();
    const bankName = $('#bank_name').val();

    if (accountNumber.length === 10 && bankName) {
        // Simulate account verification (replace with actual API call)
        setTimeout(() => {
            $('#account-name').removeClass('hidden');
            $('#account-name span').text('JOHN DOE SAMPLE');
        }, 1000);
    } else {
        $('#account-name').addClass('hidden');
    }
}
</script>
@endpush
@endsection
