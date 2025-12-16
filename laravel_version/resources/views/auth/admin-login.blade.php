@extends('layouts.app')

@section('title', 'Admin Login - VASTLEAD ')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center">
            <div class="pb-3 pt-3">
                <img src="{{ asset('assets1/images/stanlogo.jpg') }}" class="mx-auto h-20 w-auto rounded-r-2xl" alt="VASTLEAD " />
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Admin Access</h2>
            <p class="text-red-600 font-medium">Authorized Personnel Only</p>
        </div>

        <form id="admin-login-form" class="mt-8 space-y-6">
            @csrf

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-shield text-red-500 mr-2"></i>Username
                </label>
                <div class="relative">
                    <input
                        id="username"
                        name="username"
                        type="text"
                        required
                        class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent sm:text-sm transition-all duration-200"
                        placeholder="Enter your username"
                    >
                    <i class="fas fa-user-shield absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock text-red-500 mr-2"></i>Password
                </label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="appearance-none relative block w-full px-4 py-3 pl-12 pr-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent sm:text-sm transition-all duration-200"
                        placeholder="Enter your password"
                    >
                    <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <button type="button" id="toggle-password" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember credentials
                    </label>
                </div>
                <div class="text-sm">
                    <a href="{{ url('/') }}" class="font-medium text-red-600 hover:text-red-500">
                        Back to site
                    </a>
                </div>
            </div>

            <!-- Login Button -->
            <div>
                <button
                    type="submit"
                    id="login-btn"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-full text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-white group-hover:text-gray-200"></i>
                    </span>
                    Admin Login
                </button>
            </div>

            <!-- Security Notice -->
            <div class="text-center">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This is a secure admin area. All access attempts are logged and monitored.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Load saved credentials
    loadSavedCredentials();

    // Toggle password visibility
    $('#toggle-password').click(function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');

        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Admin login form submission
    $('#admin-login-form').submit(function(e) {
        e.preventDefault();

        const username = $('#username').val().trim();
        const password = $('#password').val();
        const remember = $('#remember').is(':checked');

        // Basic validation
        if (!username || !password) {
            Swal.fire({
                icon: 'warning',
                title: 'Alert!',
                text: 'Please enter both username and password.',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        const loginBtn = $('#login-btn');
        const originalText = loginBtn.html();

        // Update button state
        loginBtn.prop('disabled', true)
                .removeClass('from-red-600 to-red-700 hover:from-red-700 hover:to-red-800')
                .addClass('from-gray-400 to-gray-500')
                .html('<i class="fas fa-spinner fa-spin mr-2"></i>Authenticating...');

        $.ajax({
            url: '{{ route('admin.login') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    // Save credentials if remember is checked
                    if (remember) {
                        saveCredentials(username, password);
                    } else {
                        clearSavedCredentials();
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome!',
                        text: response.message,
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/admin/dashboard';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied!',
                        text: response.message,
                        confirmButtonColor: '#EF4444'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Authentication failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Authentication Error!',
                    text: message,
                    confirmButtonColor: '#EF4444'
                });
            },
            complete: function() {
                // Reset button state
                loginBtn.prop('disabled', false)
                        .removeClass('from-gray-400 to-gray-500')
                        .addClass('from-red-600 to-red-700 hover:from-red-700 hover:to-red-800')
                        .html(originalText);
            }
        });
    });

    // Functions for saving/loading credentials
    function saveCredentials(username, password) {
        try {
            const credentials = {
                username: username,
                password: password,
                expires: new Date().getTime() + (30 * 24 * 60 * 60 * 1000) // 30 days
            };
            localStorage.setItem('admin_credentials', JSON.stringify(credentials));
        } catch (e) {
            console.log('Could not save credentials:', e);
        }
    }

    function loadSavedCredentials() {
        try {
            const saved = localStorage.getItem('admin_credentials');
            if (saved) {
                const credentials = JSON.parse(saved);
                const now = new Date().getTime();

                if (credentials.expires > now) {
                    $('#username').val(credentials.username);
                    $('#password').val(credentials.password);
                    $('#remember').prop('checked', true);
                } else {
                    clearSavedCredentials();
                }
            }
        } catch (e) {
            console.log('Could not load saved credentials:', e);
        }
    }

    function clearSavedCredentials() {
        try {
            localStorage.removeItem('admin_credentials');
        } catch (e) {
            console.log('Could not clear credentials:', e);
        }
    }
});
</script>
@endpush
@endsection
