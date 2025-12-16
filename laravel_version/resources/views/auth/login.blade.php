@extends('layouts.app')

@section('title', 'Login - VASTLEAD ')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <div class="pb-5 pt-5">
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        VASTLEAD
                    </h2>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-3">Login</h1>
                <h2 class="mb-3 text-blue-600 font-medium" id="accountname">Welcome Back</h2>
            </div>

            <!-- Login Form -->
            <form id="login-form" method="post" class="space-y-6">
                @csrf
                <div class="px-2">
                    <!-- Phone Number Field -->
                    <div class="mb-4" id="phonediv">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fa fa-phone text-blue-600 mr-2"></i>
                            Phone Number
                        </label>
                        <input type="number"
                               class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100 transition-all duration-300"
                               id="phone"
                               name="phone"
                               placeholder="Phone Number"
                               required
                               readonly />
                        <i class="fa fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        <small class="text-gray-500">(required)</small>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fa fa-lock text-blue-600 mr-2"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input type="password"
                                   class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100 transition-all duration-300"
                                   id="password"
                                   name="password"
                                   placeholder="Password"
                                   required
                                   readonly />
                            <i class="fa fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                        <small class="text-gray-500">(required)</small>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            id="submit-btn"
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 px-4 rounded-full transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 mt-4">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login
                    </button>

                    <!-- Links Section -->
                    <div class="pt-5 mb-3 space-y-3 text-center">
                        <div class="text-sm">
                            <a class="text-gray-600 hover:text-blue-600 transition-colors" href="{{ route('password.request') }}">
                                Forget Password? Recover It
                            </a>
                        </div>
                        <div class="text-sm">
                            <a class="text-gray-600 hover:text-blue-600 transition-colors" href="{{ route('register') }}">
                                New User? Create Account
                            </a>
                        </div>
                        <div class="text-sm">
                            <a class="text-gray-600 hover:text-blue-600 transition-colors" href="{{ route('welcome') }}">
                                <b>VTU Platform</b>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Trust Indicators -->
        <div class="text-center">
            <div class="flex justify-center items-center space-x-6 text-gray-400">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt mr-2 text-green-500"></i>
                    <span class="text-sm">Secure Login</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-clock mr-2 text-blue-500"></i>
                    <span class="text-sm">24/7 Access</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Check if phone number is saved in cookies
    checkIfPhoneNumberSaved();

    // Enable form inputs on click
    $('#phone').click(function() {
        $(this).removeAttr('readonly').removeClass('bg-gray-100').addClass('bg-white');
    });

    $('#password').click(function() {
        $(this).removeAttr('readonly').removeClass('bg-gray-100').addClass('bg-white');
    });

    // Login form submission
    $('#login-form').submit(function(e) {
        e.preventDefault();

        const submitBtn = $('#submit-btn');
        const originalText = submitBtn.html();

        // Disable button and show loading
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin mr-2"></i>Logging In...');

        $.ajax({
            url: '{{ route('login') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    // Save login info in cookies
                    setCookie('loginPhone', btoa($('#phone').val()), 30);
                    setCookie('loginName', btoa(response.user.sFname), 30);

                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/dashboard';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message,
                        confirmButtonColor: '#EF4444'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Login failed. Please check your credentials.';
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
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});

function checkIfPhoneNumberSaved() {
    const phone = atob(getCookie('loginPhone') || '');
    const name = atob(getCookie('loginName') || '');

    if (phone && phone !== '') {
        const msg = '<p class="mb-3"><a href="javascript:showNumber();" class="text-blue-600 hover:text-blue-500 font-medium">Login With Another Account?</a></p>';
        $('#accountname').after(msg);
        $('#accountname').text('Welcome Back ' + name + '!');
        $('#phonediv').hide();
        $('#phone').val(phone);
    }
}

function showNumber() {
    $('#phonediv').show();
    $('.mb-3').remove();
    $('#accountname').text('Welcome Back');
}

function getCookie(cname) {
    const name = cname + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1);
        if (c.indexOf(name) === 0) return c.substring(name.length, c.length);
    }
    return "";
}

function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    const expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
</script>
@endpush
@endsection
