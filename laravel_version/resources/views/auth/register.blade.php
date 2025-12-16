@extends('layouts.app')

@section('title', 'Register - VASTLEAD ')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full space-y-8 bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center">
            <div class="pb-3 pt-3">
                <img src="{{ asset('assets1/images/stanlogo.jpg') }}" class="mx-auto h-20 w-auto rounded-r-2xl" alt="VASTLEAD " />
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Register</h2>
            <p class="text-blue-600 font-medium">Enter your credentials below to create a free account</p>
        </div>

        <form id="reg-form" class="mt-8 space-y-6">
            @csrf

            <!-- Step 1: Basic Information -->
            <div id="step1" class="space-y-4">
                <!-- First Name -->
                <div>
                    <label for="fname" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-500 mr-2"></i>First Name
                    </label>
                    <div class="relative">
                        <input
                            id="fname"
                            name="fname"
                            type="text"
                            required
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200"
                            placeholder="Enter your first name"
                        >
                        <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Last Name -->
                <div>
                    <label for="lname" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-500 mr-2"></i>Last Name
                    </label>
                    <div class="relative">
                        <input
                            id="lname"
                            name="lname"
                            type="text"
                            required
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200"
                            placeholder="Enter your last name"
                        >
                        <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

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
                            readonly
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200 bg-gray-100"
                            placeholder="Enter your phone number"
                        >
                        <i class="fas fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-blue-500 mr-2"></i>Email
                    </label>
                    <div class="relative">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            readonly
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200 bg-gray-100"
                            placeholder="Enter your email"
                        >
                        <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Account Type -->
                <div>
                    <label for="account" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-blue-500 mr-2"></i>Account Type
                    </label>
                    <div class="relative">
                        <select
                            id="account"
                            name="account"
                            required
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200"
                        >
                            <option value="" disabled selected>Select account type</option>
                            <option value="1">User</option>
                            <option value="2">Agent</option>
                            <option value="3">Vendor</option>
                        </select>
                        <i class="fas fa-user-tag absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <button
                    type="button"
                    id="next-btn"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-full text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105"
                >
                    Continue
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>

            <!-- Step 2: Additional Information -->
            <div id="step2" class="space-y-4 hidden">
                <!-- State -->
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>State
                    </label>
                    <div class="relative">
                        <select
                            id="state"
                            name="state"
                            required
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200"
                        >
                            <option value="" disabled selected>Select your state</option>
                            <option value="Abuja FCT">Abuja FCT</option>
                            <option value="Abia">Abia</option>
                            <option value="Adamawa">Adamawa</option>
                            <option value="Akwa Ibom">Akwa Ibom</option>
                            <option value="Anambra">Anambra</option>
                            <option value="Bauchi">Bauchi</option>
                            <option value="Bayelsa">Bayelsa</option>
                            <option value="Benue">Benue</option>
                            <option value="Borno">Borno</option>
                            <option value="Cross River">Cross River</option>
                            <option value="Delta">Delta</option>
                            <option value="Ebonyi">Ebonyi</option>
                            <option value="Edo">Edo</option>
                            <option value="Ekiti">Ekiti</option>
                            <option value="Enugu">Enugu</option>
                            <option value="Gombe">Gombe</option>
                            <option value="Imo">Imo</option>
                            <option value="Jigawa">Jigawa</option>
                            <option value="Kaduna">Kaduna</option>
                            <option value="Kano">Kano</option>
                            <option value="Katsina">Katsina</option>
                            <option value="Kebbi">Kebbi</option>
                            <option value="Kogi">Kogi</option>
                            <option value="Kwara">Kwara</option>
                            <option value="Lagos">Lagos</option>
                            <option value="Nassarawa">Nassarawa</option>
                            <option value="Niger">Niger</option>
                            <option value="Ogun">Ogun</option>
                            <option value="Ondo">Ondo</option>
                            <option value="Osun">Osun</option>
                            <option value="Oyo">Oyo</option>
                            <option value="Plateau">Plateau</option>
                            <option value="Rivers">Rivers</option>
                            <option value="Sokoto">Sokoto</option>
                            <option value="Taraba">Taraba</option>
                            <option value="Yobe">Yobe</option>
                            <option value="Zamfara">Zamfara</option>
                        </select>
                        <i class="fas fa-map-marker-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-blue-500 mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            readonly
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200 bg-gray-100"
                            placeholder="Enter your password"
                        >
                        <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="cpassword" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-blue-500 mr-2"></i>Confirm Password
                    </label>
                    <div class="relative">
                        <input
                            id="cpassword"
                            name="cpassword"
                            type="password"
                            required
                            readonly
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200 bg-gray-100"
                            placeholder="Confirm your password"
                        >
                        <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Transaction PIN -->
                <div>
                    <label for="transpin" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key text-blue-500 mr-2"></i>Transaction PIN
                    </label>
                    <div class="relative">
                        <input
                            id="transpin"
                            name="transpin"
                            type="number"
                            required
                            maxlength="4"
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200"
                            placeholder="Enter 4-digit PIN"
                        >
                        <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Referral -->
                <div>
                    <label for="referal" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-plus text-blue-500 mr-2"></i>Referral Code (Optional)
                    </label>
                    <div class="relative">
                        <input
                            id="referal"
                            name="referal"
                            type="text"
                            value="{{ request('referral') }}"
                            class="appearance-none relative block w-full px-4 py-3 pl-12 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm transition-all duration-200"
                            placeholder="Enter referral code"
                        >
                        <i class="fas fa-user-plus absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button
                        type="button"
                        id="back-btn"
                        class="flex-1 py-3 px-4 border border-gray-300 text-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </button>
                    <button
                        type="submit"
                        id="submit-btn"
                        class="flex-1 group relative flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-full text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105"
                    >
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus text-white group-hover:text-gray-200"></i>
                        </span>
                        Register
                    </button>
                </div>
            </div>

            <div class="text-center space-y-3">
                @if (Route::has('login'))
                    <div>
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            Already Have An Account? <strong>Login Now</strong>
                        </a>
                    </div>
                @endif
                <div>
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-500 text-sm font-medium">
                        <strong>VTU Platform</strong>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Enable form inputs on click
    $('#email').click(function() {
        $(this).removeAttr('readonly').removeClass('bg-gray-100').addClass('bg-white');
    });

    $('#phone').click(function() {
        $(this).removeAttr('readonly').removeClass('bg-gray-100').addClass('bg-white');
    });

    $('#password').click(function() {
        $(this).removeAttr('readonly').removeClass('bg-gray-100').addClass('bg-white');
    });

    $('#cpassword').click(function() {
        $(this).removeAttr('readonly').removeClass('bg-gray-100').addClass('bg-white');
    });

    // Next button click
    $('#next-btn').click(function() {
        let msg = "";

        const nextBtn = $(this);
        const originalText = nextBtn.html();

        // Update button state
        nextBtn.prop('disabled', true)
               .removeClass('from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700')
               .addClass('from-gray-400 to-gray-500')
               .html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

        // Validation
        if (!$('#fname').val().trim()) msg = "Please enter first name.";
        if (!$('#lname').val().trim()) msg = "Please enter last name.";
        if (!$('#phone').val().trim()) msg = "Please enter phone number.";
        if (!$('#email').val().trim()) msg = "Please enter email.";
        if (!$('#account').val()) msg = "Please select account type.";

        if (msg !== "") {
            Swal.fire({
                icon: 'warning',
                title: 'Alert!',
                text: msg,
                confirmButtonColor: '#F59E0B'
            });

            // Reset button state
            nextBtn.prop('disabled', false)
                   .removeClass('from-gray-400 to-gray-500')
                   .addClass('from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700')
                   .html(originalText);
            return;
        }

        // Proceed to step 2
        $('#step1').addClass('hidden');
        $('#step2').removeClass('hidden');

        // Reset button
        nextBtn.prop('disabled', false)
               .removeClass('from-gray-400 to-gray-500')
               .addClass('from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700')
               .html(originalText);
    });

    // Back button click
    $('#back-btn').click(function() {
        $('#step2').addClass('hidden');
        $('#step1').removeClass('hidden');
    });

    // Registration form submission
    $('#reg-form').submit(function(e) {
        e.preventDefault();

        let msg = "";

        // Validation
        if ($('#password').val().length > 15) msg = "Password should not be more than 15 characters.";
        if ($('#password').val().length < 8) msg = "Password should be at least 8 characters.";
        if ($('#password').val() === $('#phone').val()) msg = "You can't use your phone number as password.";
        if (!$('#password').val()) msg = "Please enter password.";
        if (!$('#state').val()) msg = "Please select state.";
        if ($('#password').val() !== $('#cpassword').val()) msg = "Password is different from confirm password.";
        if ($('#transpin').val().length !== 4) msg = "Transaction PIN must be 4 digits.";

        if (msg !== "") {
            Swal.fire({
                icon: 'warning',
                title: 'Alert!',
                text: msg,
                confirmButtonColor: '#F59E0B'
            });
            return;
        }

        const submitBtn = $('#submit-btn');
        const originalText = submitBtn.html();

        // Update button state
        submitBtn.prop('disabled', true)
                 .removeClass('from-green-500 to-green-600 hover:from-green-600 hover:to-green-700')
                 .addClass('from-gray-400 to-gray-500')
                 .html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

        $.ajax({
            url: '{{ route('register') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        window.location.href = '/dashboard';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#EF4444'
                    });

                    // Go back to step 1 for certain errors
                    if (response.message.includes('exists')) {
                        $('#step2').addClass('hidden');
                        $('#step1').removeClass('hidden');
                    }
                }
            },
            error: function(xhr) {
                let message = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    message = Object.values(errors).flat().join(', ');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    confirmButtonColor: '#EF4444'
                });

                // Go back to step 1 for validation errors
                $('#step2').addClass('hidden');
                $('#step1').removeClass('hidden');
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false)
                         .removeClass('from-gray-400 to-gray-500')
                         .addClass('from-green-500 to-green-600 hover:from-green-600 hover:to-green-700')
                         .html(originalText);
            }
        });
    });

    // Limit transaction PIN to 4 digits
    $('#transpin').on('input', function() {
        if (this.value.length > 4) {
            this.value = this.value.slice(0, 4);
        }
    });
});
</script>
@endpush
@endsection
