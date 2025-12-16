@extends('layouts.admin')

@section('title', 'Wallet API Configuration')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Wallet API Configuration</h1>
                    <nav class="text-purple-100 text-sm">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Dashboard</a>
                        <span class="mx-2">›</span>
                        <a href="{{ route('admin.system.wallet-providers.index') }}" class="hover:text-white transition-colors">Wallet Providers</a>
                        <span class="mx-2">›</span>
                        <span class="text-purple-200">Wallet APIs</span>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.system.wallet-providers.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Providers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800">{{ session('success') }}</p>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-800">{{ session('error') }}</p>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.system.wallet-providers.wallet-api.update') }}" method="POST">
        @csrf

        <!-- Wallet Provider Cards -->
        <div class="grid grid-cols-1 xl:grid-cols-3 lg:grid-cols-2 gap-6 mb-8">
            <!-- Wallet One -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-200">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-xl p-3 mr-4">
                            <i class="fas fa-wallet text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Primary Wallet</h3>
                            <p class="text-sm text-gray-500">Main service provider</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="walletOneProviderName" class="block text-sm font-medium text-gray-900 mb-2">Provider Name</label>
                        <input type="text" name="walletOneProviderName" id="walletOneProviderName"
                               value="{{ $configurations['walletOneProviderName']->config_value ?? '' }}"
                               placeholder="e.g., Maskawasub" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="walletOneApi" class="block text-sm font-medium text-gray-900 mb-2">API Key</label>
                        <div class="relative">
                            <input type="password" name="walletOneApi" id="walletOneApi"
                                   value="{{ $configurations['walletOneApi']->config_value ?? '' }}"
                                   placeholder="Enter API key" required
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" onclick="togglePassword('walletOneApi')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="walletOneApi-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="walletOneProvider" class="block text-sm font-medium text-gray-900 mb-2">Provider URL</label>
                        <input type="url" name="walletOneProvider" id="walletOneProvider"
                               value="{{ $configurations['walletOneProvider']->config_value ?? '' }}"
                               placeholder="https://api.provider.com/" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Wallet Two -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-200">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-xl p-3 mr-4">
                            <i class="fas fa-wallet text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Secondary Wallet</h3>
                            <p class="text-sm text-gray-500">Backup service provider</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="walletTwoProviderName" class="block text-sm font-medium text-gray-900 mb-2">Provider Name</label>
                        <input type="text" name="walletTwoProviderName" id="walletTwoProviderName"
                               value="{{ $configurations['walletTwoProviderName']->config_value ?? '' }}"
                               placeholder="e.g., Topupmate" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="walletTwoApi" class="block text-sm font-medium text-gray-900 mb-2">API Key</label>
                        <div class="relative">
                            <input type="password" name="walletTwoApi" id="walletTwoApi"
                                   value="{{ $configurations['walletTwoApi']->config_value ?? '' }}"
                                   placeholder="Enter API key" required
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <button type="button" onclick="togglePassword('walletTwoApi')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="walletTwoApi-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="walletTwoProvider" class="block text-sm font-medium text-gray-900 mb-2">Provider URL</label>
                        <input type="url" name="walletTwoProvider" id="walletTwoProvider"
                               value="{{ $configurations['walletTwoProvider']->config_value ?? '' }}"
                               placeholder="https://api.provider.com/" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
            </div>

            <!-- Wallet Three -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-200">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-xl p-3 mr-4">
                            <i class="fas fa-wallet text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Tertiary Wallet</h3>
                            <p class="text-sm text-gray-500">Additional service provider</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="walletThreeProviderName" class="block text-sm font-medium text-gray-900 mb-2">Provider Name</label>
                        <input type="text" name="walletThreeProviderName" id="walletThreeProviderName"
                               value="{{ $configurations['walletThreeProviderName']->config_value ?? '' }}"
                               placeholder="e.g., Aabaxztech" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label for="walletThreeApi" class="block text-sm font-medium text-gray-900 mb-2">API Key</label>
                        <div class="relative">
                            <input type="password" name="walletThreeApi" id="walletThreeApi"
                                   value="{{ $configurations['walletThreeApi']->config_value ?? '' }}"
                                   placeholder="Enter API key" required
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <button type="button" onclick="togglePassword('walletThreeApi')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="walletThreeApi-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="walletThreeProvider" class="block text-sm font-medium text-gray-900 mb-2">Provider URL</label>
                        <input type="url" name="walletThreeProvider" id="walletThreeProvider"
                               value="{{ $configurations['walletThreeProvider']->config_value ?? '' }}"
                               placeholder="https://api.provider.com/" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Configuration Actions</h3>
                        <p class="text-sm text-gray-500 mt-1">Save your changes and test connections</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button type="button" onclick="testAllWalletConnections()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            <i class="fas fa-plug mr-2"></i>Test All Connections
                        </button>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>Save Configuration
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Provider Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- How It Works -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>How Wallet APIs Work
                </h3>
            </div>
            <div class="p-6">
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                        <span class="text-gray-700">Primary wallet handles most transactions</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                        <span class="text-gray-700">Secondary wallet serves as backup when primary fails</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                        <span class="text-gray-700">Tertiary wallet provides additional redundancy</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                        <span class="text-gray-700">System automatically switches between providers</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Available Providers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-server text-purple-600 mr-2"></i>Available Provider List
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($apiProviders as $provider)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-server text-purple-600 mr-3"></i>
                            <div class="flex-grow">
                                <div class="font-medium text-gray-900">{{ $provider->name }}</div>
                                <div class="text-sm text-gray-500">{{ $provider->value }}</div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $provider->type }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Connection Test Results -->
    <div class="mt-8 hidden" id="connectionTestSection">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Connection Test Results</h3>
                    <button onclick="hideTestResults()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6" id="connectionTestContent">
                <!-- Test results will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Test all wallet connections
function testAllWalletConnections() {
    const section = document.getElementById('connectionTestSection');
    const content = document.getElementById('connectionTestContent');

    section.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-4"></div>
                <p class="text-gray-500">Testing wallet connections...</p>
            </div>
        </div>
    `;

    const wallets = ['walletOne', 'walletTwo', 'walletThree'];
    let results = [];
    let completed = 0;

    wallets.forEach(wallet => {
        fetch('{{ route("admin.system.wallet-providers.test-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ provider: wallet })
        })
        .then(response => response.json())
        .then(data => {
            results.push({
                wallet: wallet,
                success: data.success,
                message: data.message || (data.success ? 'Connection successful' : 'Connection failed')
            });
        })
        .catch(error => {
            results.push({
                wallet: wallet,
                success: false,
                message: 'Network error'
            });
        })
        .finally(() => {
            completed++;
            if (completed === wallets.length) {
                displayConnectionResults(results);
            }
        });
    });
}

function displayConnectionResults(results) {
    const content = document.getElementById('connectionTestContent');
    let html = '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';

    results.forEach(result => {
        const statusColor = result.success ? 'green' : 'red';
        const statusIcon = result.success ? 'check-circle' : 'times-circle';
        const walletName = result.wallet.replace('wallet', 'Wallet ').replace('One', '1').replace('Two', '2').replace('Three', '3');

        html += `
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-${statusIcon} text-${statusColor}-500 text-xl mr-3"></i>
                    <div class="flex-grow">
                        <h4 class="font-semibold text-gray-900">${walletName}</h4>
                        <p class="text-sm text-gray-500">${result.message}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800">
                        ${result.success ? 'Connected' : 'Failed'}
                    </span>
                </div>
            </div>
        `;
    });

    html += '</div>';
    content.innerHTML = html;
}

function hideTestResults() {
    document.getElementById('connectionTestSection').classList.add('hidden');
}

// Show toast notification
function showToast(message, type) {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle mr-2"></i>
            ${message}
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endsection
