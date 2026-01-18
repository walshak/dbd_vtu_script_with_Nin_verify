@extends('layouts.user-layout')

@section('title', 'KYC Verification')

@section('page-content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <i class="fas fa-shield-alt text-4xl"></i>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-center mb-2">Complete KYC Verification</h1>
            <p class="text-blue-100 text-center">Verify your identity with NIN or BVN to enable virtual account</p>
        </div>

        <!-- Notice Banner -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>CBN Compliance Notice:</strong> In accordance with CBN regulations for virtual accounts,
                        you must verify either your NIN or BVN. Your data is securely transmitted and not stored on our servers.
                    </p>
                </div>
            </div>
        </div>

        <!-- KYC Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Choose Verification Method</h2>
                <p class="text-gray-600">You can verify using either your NIN or BVN</p>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 mb-6">
                <button
                    onclick="switchTab('nin')"
                    id="nin-tab"
                    class="flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium transition-colors"
                >
                    <i class="fas fa-id-card mr-2"></i>NIN Verification
                </button>
                <button
                    onclick="switchTab('bvn')"
                    id="bvn-tab"
                    class="flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 font-medium hover:text-gray-700 transition-colors"
                >
                    <i class="fas fa-university mr-2"></i>BVN Verification
                </button>
            </div>

            <!-- NIN Tab Content -->
            <div id="nin-content" class="tab-content">
                <form id="nin-form" onsubmit="verifyNIN(event)">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="nin" class="block text-sm font-medium text-gray-700 mb-2">
                                National Identification Number (NIN)
                            </label>
                            <input
                                type="text"
                                id="nin"
                                name="nin"
                                maxlength="11"
                                pattern="[0-9]{11}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Enter your 11-digit NIN"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                required
                            >
                            <p class="mt-1 text-sm text-gray-500">Enter your 11-digit NIN</p>
                        </div>

                        <button
                            type="submit"
                            id="verify-nin-btn"
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-check-circle mr-2"></i>Verify NIN
                        </button>
                    </div>
                </form>
            </div>

            <!-- BVN Tab Content -->
            <div id="bvn-content" class="tab-content hidden">
                <form id="bvn-form" onsubmit="verifyBVN(event)">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="bvn" class="block text-sm font-medium text-gray-700 mb-2">
                                Bank Verification Number (BVN)
                            </label>
                            <input
                                type="text"
                                id="bvn"
                                name="bvn"
                                maxlength="11"
                                pattern="[0-9]{11}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Enter your 11-digit BVN"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                required
                            >
                            <p class="mt-1 text-sm text-gray-500">Enter your 11-digit BVN</p>
                        </div>

                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">
                                Date of Birth
                            </label>
                            <input
                                type="date"
                                id="dob"
                                name="dob"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                                required
                            >
                            <p class="mt-1 text-sm text-gray-500">As registered with your bank</p>
                        </div>

                        <button
                            type="submit"
                            id="verify-bvn-btn"
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-check-circle mr-2"></i>Verify BVN
                        </button>
                    </div>
                </form>
            </div>

            <!-- Status Message -->
            <div id="status-message" class="mt-6 hidden">
                <!-- Dynamic content -->
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <h3 class="font-bold text-blue-900 mb-2">
                    <i class="fas fa-lock mr-2"></i>Secure & Private
                </h3>
                <p class="text-sm text-blue-800">Your KYC data is encrypted and transmitted securely to Monnify for verification only.</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <h3 class="font-bold text-green-900 mb-2">
                    <i class="fas fa-clock mr-2"></i>Instant Verification
                </h3>
                <p class="text-sm text-green-800">Verification typically completes within seconds once submitted.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    // Update tab buttons
    const ninTab = document.getElementById('nin-tab');
    const bvnTab = document.getElementById('bvn-tab');

    if (tab === 'nin') {
        ninTab.className = 'flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium transition-colors';
        bvnTab.className = 'flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 font-medium hover:text-gray-700 transition-colors';
    } else {
        ninTab.className = 'flex-1 py-4 px-6 text-center border-b-2 border-transparent text-gray-500 font-medium hover:text-gray-700 transition-colors';
        bvnTab.className = 'flex-1 py-4 px-6 text-center border-b-2 border-blue-500 text-blue-600 font-medium transition-colors';
    }

    // Update content
    document.getElementById('nin-content').classList.toggle('hidden', tab !== 'nin');
    document.getElementById('bvn-content').classList.toggle('hidden', tab !== 'bvn');

    // Clear status message when switching tabs
    document.getElementById('status-message').classList.add('hidden');
}

function verifyNIN(event) {
    event.preventDefault();

    const btn = document.getElementById('verify-nin-btn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';

    const formData = new FormData(event.target);

    fetch('/api/kyc/verify-nin', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;

        if (data.success) {
            showStatus('success', data.message);
            setTimeout(() => {
                window.location.href = '/user/fund-wallet';
            }, 2000);
        } else {
            showStatus('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = originalText;
        showStatus('error', 'Verification request failed. Please try again.');
    });
}

function verifyBVN(event) {
    event.preventDefault();

    const btn = document.getElementById('verify-bvn-btn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';

    const formData = new FormData(event.target);

    fetch('/api/kyc/verify-bvn', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;

        if (data.success) {
            showStatus('success', data.message);
            setTimeout(() => {
                window.location.href = '/user/fund-wallet';
            }, 2000);
        } else {
            showStatus('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = originalText;
        showStatus('error', 'Verification request failed. Please try again.');
    });
}

function showStatus(type, message) {
    const statusDiv = document.getElementById('status-message');
    const isSuccess = type === 'success';

    statusDiv.className = `mt-6 p-4 rounded-lg ${isSuccess
        ? 'bg-green-50 border border-green-200'
        : 'bg-red-50 border border-red-200'}`;

    statusDiv.innerHTML = `
        <div class="flex items-start">
            <i class="fas fa-${isSuccess ? 'check-circle text-green-600' : 'exclamation-circle text-red-600'} mt-1 mr-3"></i>
            <p class="text-sm ${isSuccess ? 'text-green-800' : 'text-red-800'}">${message}</p>
        </div>
    `;
    statusDiv.classList.remove('hidden');
}
</script>
@endpush
@endsection
