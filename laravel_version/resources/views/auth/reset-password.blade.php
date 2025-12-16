@extends('layouts.app')

@section('title', 'Reset Password - VASTLEAD ')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center">
            <div class="pb-3 pt-3">
                <img src="{{ asset('assets1/images/stanlogo.jpg') }}" class="mx-auto h-20 w-auto rounded-r-2xl" alt="VASTLEAD " />
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Reset Password</h2>
            <p class="text-blue-600 font-medium">Enter your phone number to reset your password</p>
        </div>

        <form id="reset-form" class="mt-8 space-y-6">
            @csrf

            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-phone text-blue-500 mr-2"></i>Phone Number
                </label>
                <div class="relative">
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        required
                        class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200"
                        placeholder="Enter your phone number"
                    >
                    <i class="fas fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <p class="text-xs text-gray-500 mt-2">Enter the phone number associated with your account</p>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    id="reset-btn"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-full text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-key text-white group-hover:text-gray-200"></i>
                    </span>
                    Send Reset Link
                </button>
            </div>

            <!-- Back to Login -->
            <div class="text-center space-y-3">
                <div>
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Login
                    </a>
                </div>
                <div>
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-500 text-sm font-medium">
                        <strong>VTU Platform</strong>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- OTP Verification Modal -->
<div id="otp-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <i class="fas fa-sms text-blue-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Verify OTP</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Enter the 6-digit code sent to your phone number
                </p>
                <div class="mt-4">
                    <input
                        type="text"
                        id="otp-code"
                        maxlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center text-lg tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="000000"
                    >
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <div class="flex space-x-3">
                    <button
                        id="cancel-otp"
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-lg shadow-sm hover:bg-gray-400 focus:outline-none"
                    >
                        Cancel
                    </button>
                    <button
                        id="verify-otp"
                        class="flex-1 px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-lg shadow-sm hover:bg-blue-600 focus:outline-none"
                    >
                        Verify
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Password Modal -->
<div id="password-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="fas fa-lock text-green-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3 text-center">Set New Password</h3>
            <form id="new-password-form" class="mt-4 space-y-4">
                <div>
                    <label for="new-password" class="block text-sm font-medium text-gray-700 mb-1">
                        New Password
                    </label>
                    <input
                        type="password"
                        id="new-password"
                        name="new_password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter new password"
                    >
                </div>
                <div>
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm Password
                    </label>
                    <input
                        type="password"
                        id="confirm-password"
                        name="confirm_password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Confirm new password"
                    >
                </div>
                <div class="flex space-x-3 pt-3">
                    <button
                        type="button"
                        id="cancel-password"
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-lg shadow-sm hover:bg-gray-400 focus:outline-none"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        id="update-password"
                        class="flex-1 px-4 py-2 bg-green-500 text-white text-base font-medium rounded-lg shadow-sm hover:bg-green-600 focus:outline-none"
                    >
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let resetToken = null;

    // Password reset form submission
    $('#reset-form').submit(function(e) {
        e.preventDefault();

        const phone = $('#phone').val().trim();

        if (!phone) {
            Swal.fire({
                icon: 'warning',
                title: 'Alert!',
                text: 'Please enter your phone number.',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        const resetBtn = $('#reset-btn');
        const originalText = resetBtn.html();

        // Update button state
        resetBtn.prop('disabled', true)
                .removeClass('from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700')
                .addClass('from-gray-400 to-gray-500')
                .html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

        $.ajax({
            url: '{{ route('password.reset.request') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    resetToken = response.token;
                    $('#otp-modal').removeClass('hidden');

                    Swal.fire({
                        icon: 'info',
                        title: 'OTP Sent!',
                        text: 'Please check your phone for the verification code.',
                        confirmButtonColor: '#3B82F6'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#EF4444'
                    });
                }
            },
            error: function(xhr) {
                let message = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    confirmButtonColor: '#EF4444'
                });
            },
            complete: function() {
                // Reset button state
                resetBtn.prop('disabled', false)
                        .removeClass('from-gray-400 to-gray-500')
                        .addClass('from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700')
                        .html(originalText);
            }
        });
    });

    // OTP verification
    $('#verify-otp').click(function() {
        const otp = $('#otp-code').val().trim();

        if (!otp || otp.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Alert!',
                text: 'Please enter a valid 6-digit OTP.',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        const verifyBtn = $(this);
        const originalText = verifyBtn.text();

        verifyBtn.prop('disabled', true).text('Verifying...');

        $.ajax({
            url: '{{ route('password.reset.verify') }}',
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                token: resetToken,
                otp: otp
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#otp-modal').addClass('hidden');
                    $('#password-modal').removeClass('hidden');
                    $('#otp-code').val('');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid OTP!',
                        text: response.message,
                        confirmButtonColor: '#EF4444'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Verification Failed!',
                    text: 'Invalid or expired OTP. Please try again.',
                    confirmButtonColor: '#EF4444'
                });
            },
            complete: function() {
                verifyBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // New password form submission
    $('#new-password-form').submit(function(e) {
        e.preventDefault();

        const newPassword = $('#new-password').val();
        const confirmPassword = $('#confirm-password').val();

        if (newPassword.length < 8) {
            Swal.fire({
                icon: 'warning',
                title: 'Alert!',
                text: 'Password must be at least 8 characters long.',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'warning',
                title: 'Alert!',
                text: 'Passwords do not match.',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        const updateBtn = $('#update-password');
        const originalText = updateBtn.text();

        updateBtn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: '{{ route('password.reset.update') }}',
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                token: resetToken,
                password: newPassword,
                password_confirmation: confirmPassword
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#password-modal').addClass('hidden');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Your password has been updated successfully.',
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        window.location.href = '{{ route('login') }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#EF4444'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed!',
                    text: 'Failed to update password. Please try again.',
                    confirmButtonColor: '#EF4444'
                });
            },
            complete: function() {
                updateBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Modal close handlers
    $('#cancel-otp').click(function() {
        $('#otp-modal').addClass('hidden');
        $('#otp-code').val('');
    });

    $('#cancel-password').click(function() {
        $('#password-modal').addClass('hidden');
        $('#new-password').val('');
        $('#confirm-password').val('');
    });

    // OTP input formatting
    $('#otp-code').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
    });
});
</script>
@endpush
@endsection
