@extends('layouts.user-layout')

@php
    $title = 'Profile Settings';
@endphp

@section('page-content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-full">
                        <i class="fas fa-user text-4xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">Profile Settings</h1>
                <p class="text-blue-100 text-lg text-center">Manage your account information and security settings</p>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                <i class="fas fa-cog text-9xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Personal Information</h2>
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-user-edit text-blue-600"></i>
                    </div>
                </div>

                <form id="profile-form" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="fname" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user text-blue-500 mr-2"></i>First Name
                            </label>
                            <input 
                                type="text" 
                                id="fname" 
                                name="fname" 
                                value="{{ auth()->user()->sFname }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required
                            >
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="lname" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user text-blue-500 mr-2"></i>Last Name
                            </label>
                            <input 
                                type="text" 
                                id="lname" 
                                name="lname" 
                                value="{{ auth()->user()->sLname }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                required
                            >
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-blue-500 mr-2"></i>Email Address
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ auth()->user()->sEmail }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            required
                        >
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone text-blue-500 mr-2"></i>Phone Number
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            value="{{ auth()->user()->sPhone }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            required
                        >
                    </div>

                    <!-- State -->
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>State
                        </label>
                        <select 
                            id="state" 
                            name="state" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            required
                        >
                            <option value="">Select your state</option>
                            @php
                                $states = [
                                    'Abuja FCT', 'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 
                                    'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 
                                    'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 
                                    'Kwara', 'Lagos', 'Nassarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 
                                    'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
                                ];
                            @endphp
                            @foreach($states as $state)
                                <option value="{{ $state }}" {{ auth()->user()->sState == $state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <i class="fas fa-save mr-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="space-y-8">
            <!-- Account Overview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>Account Overview
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Account Type</span>
                        <span class="text-sm font-medium text-gray-900">{{ auth()->user()->account_type_name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ auth()->user()->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ auth()->user()->registration_status_name }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Member Since</span>
                        <span class="text-sm font-medium text-gray-900">{{ auth()->user()->sRegDate ? auth()->user()->sRegDate->format('M Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Password Change -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-lock text-blue-500 mr-2"></i>Change Password
                </h3>
                <form id="password-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                            minlength="8"
                            maxlength="15"
                        >
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                        >
                    </div>
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200"
                    >
                        <i class="fas fa-key mr-2"></i>Change Password
                    </button>
                </form>
            </div>

            <!-- PIN Change -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-shield-alt text-blue-500 mr-2"></i>Transaction PIN
                </h3>
                <form id="pin-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="current_pin" class="block text-sm font-medium text-gray-700 mb-2">Current PIN</label>
                        <input 
                            type="password" 
                            id="current_pin" 
                            name="current_pin" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                            maxlength="4"
                            pattern="[0-9]{4}"
                        >
                    </div>
                    <div>
                        <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">New PIN</label>
                        <input 
                            type="password" 
                            id="pin" 
                            name="pin" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                            maxlength="4"
                            pattern="[0-9]{4}"
                        >
                    </div>
                    <div>
                        <label for="pin_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm PIN</label>
                        <input 
                            type="password" 
                            id="pin_confirmation" 
                            name="pin_confirmation" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required
                            maxlength="4"
                            pattern="[0-9]{4}"
                        >
                    </div>
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200"
                    >
                        <i class="fas fa-key mr-2"></i>Change PIN
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Profile Update Form
    $('#profile-form').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin mr-2"></i>Updating...');
        
        $.ajax({
            url: '{{ route("user.profile.update") }}',
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
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
                let message = 'Failed to update profile.';
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
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Password Change Form
    $('#password-form').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Validate password match
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();
        
        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'warning',
                title: 'Error!',
                text: 'Passwords do not match.'
            });
            return;
        }
        
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin mr-2"></i>Changing...');
        
        $.ajax({
            url: '{{ route("user.password.change") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#password-form')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                let message = 'Failed to change password.';
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
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // PIN Change Form
    $('#pin-form').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Validate PIN match
        const pin = $('#pin').val();
        const confirmPin = $('#pin_confirmation').val();
        
        if (pin !== confirmPin) {
            Swal.fire({
                icon: 'warning',
                title: 'Error!',
                text: 'PINs do not match.'
            });
            return;
        }
        
        submitBtn.prop('disabled', true)
                 .html('<i class="fas fa-spinner fa-spin mr-2"></i>Changing...');
        
        $.ajax({
            url: '{{ route("user.pin.change") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#pin-form')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                let message = 'Failed to change PIN.';
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
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Limit PIN inputs to 4 digits
    $('#current_pin, #pin, #pin_confirmation').on('input', function() {
        if (this.value.length > 4) {
            this.value = this.value.slice(0, 4);
        }
        // Only allow numbers
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
</script>
@endpush
@endsection