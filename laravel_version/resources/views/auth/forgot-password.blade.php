@extends('layouts.app')

@section('title', 'Forgot Password - VASTLEAD ')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center">
            <div class="pb-3 pt-3">
                <img src="{{ asset('assets1/images/stanlogo.jpg') }}" class="mx-auto h-20 w-auto rounded-r-2xl" alt="VASTLEAD " />
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Forgot Password</h2>
            <p class="text-blue-600 font-medium">Enter your email address to receive a reset code</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="forgot-password-form" method="POST" action="{{ route('password.reset.request') }}" class="mt-8 space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope text-blue-500 mr-2"></i>Email Address
                </label>
                <div class="relative">
                    <input
                        id="email"
                        name="email"
                        type="email"
                        required
                        value="{{ old('email') }}"
                        class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200"
                        placeholder="Enter your email address"
                    >
                    <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    id="submit-btn"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-full text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105"
                >
                    <i class="fas fa-paper-plane mr-2"></i>
                    Send Reset Code
                </button>
            </div>

            <!-- Back to Login -->
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#forgot-password-form').submit(function(e) {
        e.preventDefault();

        const submitBtn = $('#submit-btn');
        const originalText = submitBtn.html();

        // Disable button and show loading
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin mr-2"></i>Sending...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reset Code Sent!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        // Show OTP verification modal or redirect
                        showOTPModal($('#email').val());
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                let message = 'Failed to send reset code. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    message = Object.values(errors).flat().join(', ');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message
                });
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

function showOTPModal(email) {
    Swal.fire({
        title: 'Enter Reset Code',
        html: `
            <p class="mb-4">Enter the 6-digit code sent to your email:</p>
            <input type="text" id="otp-input" class="swal2-input" placeholder="000000" maxlength="6" style="text-align: center; font-size: 18px; letter-spacing: 5px;">
        `,
        showCancelButton: true,
        confirmButtonText: 'Verify Code',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
        preConfirm: () => {
            const otp = document.getElementById('otp-input').value;
            if (!otp || otp.length !== 6) {
                Swal.showValidationMessage('Please enter a valid 6-digit code');
                return false;
            }
            return otp;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            verifyOTP(email, result.value);
        }
    });
}

function verifyOTP(email, otp) {
    Swal.fire({
        title: 'Verifying...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ route("password.reset.verify") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            email: email,
            otp: otp
        },
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Code Verified!',
                    text: 'Redirecting to password reset...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    showPasswordResetModal(email, response.token);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Code',
                    text: response.message
                }).then(() => {
                    showOTPModal(email);
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to verify code. Please try again.'
            }).then(() => {
                showOTPModal(email);
            });
        }
    });
}

function showPasswordResetModal(email, token) {
    Swal.fire({
        title: 'Set New Password',
        html: `
            <div class="space-y-4">
                <input type="password" id="new-password" class="swal2-input" placeholder="New Password" minlength="8" maxlength="15">
                <input type="password" id="confirm-password" class="swal2-input" placeholder="Confirm Password" minlength="8" maxlength="15">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Update Password',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
        preConfirm: () => {
            const password = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (!password || password.length < 8) {
                Swal.showValidationMessage('Password must be at least 8 characters');
                return false;
            }

            if (password !== confirmPassword) {
                Swal.showValidationMessage('Passwords do not match');
                return false;
            }

            return { password: password, password_confirmation: confirmPassword };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updatePassword(email, token, result.value.password, result.value.password_confirmation);
        }
    });
}

function updatePassword(email, token, password, passwordConfirmation) {
    Swal.fire({
        title: 'Updating Password...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ route("password.reset.update") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            token: token,
            email: email,
            password: password,
            password_confirmation: passwordConfirmation
        },
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Password Updated!',
                    text: 'Your password has been updated successfully. You can now login.',
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '{{ route("login") }}';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message
                });
            }
        },
        error: function(xhr) {
            let message = 'Failed to update password. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                message = Object.values(errors).flat().join(', ');
            }

            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message
            });
        }
    });
}
</script>
@endpush
@endsection
