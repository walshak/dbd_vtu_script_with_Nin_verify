@extends('layouts.admin')

@section('title', 'Wallet Providers')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold mb-2">Wallet Providers</h1>
                    <p class="text-green-100">Manage payment gateways and wallet API providers</p>
                </div>
                <div>
                    <button class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200" onclick="refreshAllData()">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Provider Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <!-- Monnify Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-xl p-3 mr-4">
                            <i class="fas fa-university text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Monnify</h3>
                            <p class="text-sm text-gray-500">Virtual Account Provider</p>
                        </div>
                    </div>
                    <div>
                        @if(($configurations['monifyStatus']->config_value ?? 'Off') === 'On')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <i class="fas fa-percentage mr-2"></i>
                            <span>Charges:</span>
                        </div>
                        <div class="font-semibold text-gray-900">{{ $configurations['monifyCharges']->config_value ?? 'Not Set' }}%</div>
                    </div>
                    <div>
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <i class="fas fa-cog mr-2"></i>
                            <span>Services:</span>
                        </div>
                        <div class="font-semibold text-gray-900">
                            @php
                                $activeServices = 0;
                                if(($configurations['monifyWeStatus']->config_value ?? 'Off') === 'On') $activeServices++;
                                if(($configurations['monifyMoStatus']->config_value ?? 'Off') === 'On') $activeServices++;
                                if(($configurations['monifySaStatus']->config_value ?? 'Off') === 'On') $activeServices++;
                                if(($configurations['monifyFeStatus']->config_value ?? 'Off') === 'On') $activeServices++;
                            @endphp
                            {{ $activeServices }}/4 Active
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.system.wallet-providers.monnify') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center inline-block transition-colors duration-200">
                    <i class="fas fa-cog mr-2"></i>Configure
                </a>
            </div>
        </div>

        <!-- Paystack Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-xl p-3 mr-4">
                            <i class="fas fa-credit-card text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Paystack</h3>
                            <p class="text-sm text-gray-500">Payment Gateway</p>
                        </div>
                    </div>
                    <div>
                        @if(($configurations['paystackStatus']->config_value ?? 'Off') === 'On')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <i class="fas fa-percentage mr-2"></i>
                            <span>Charges:</span>
                        </div>
                        <div class="font-semibold text-gray-900">{{ $configurations['paystackCharges']->config_value ?? 'Not Set' }}%</div>
                    </div>
                    <div>
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <i class="fas fa-key mr-2"></i>
                            <span>API Status:</span>
                        </div>
                        <div class="font-semibold text-gray-900">
                            {{ !empty($configurations['paystackApi']->config_value ?? '') ? 'Configured' : 'Not Set' }}
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.system.wallet-providers.paystack') }}" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-center inline-block transition-colors duration-200">
                    <i class="fas fa-cog mr-2"></i>Configure
                </a>
            </div>
        </div>

        <!-- Wallet API Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-xl p-3 mr-4">
                            <i class="fas fa-exchange-alt text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Wallet APIs</h3>
                            <p class="text-sm text-gray-500">Service Providers</p>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-layer-group mr-1"></i>{{ $apiProviders->count() }} Providers
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <i class="fas fa-star mr-2"></i>
                        <span>Primary Provider:</span>
                    </div>
                    <div class="font-semibold text-gray-900 mb-3">{{ $configurations['walletOneProviderName']->config_value ?? 'Not Set' }}</div>

                    <div class="space-y-1 text-sm text-gray-600">
                        @if(!empty($configurations['walletOneProviderName']->config_value ?? ''))
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Wallet 1: {{ $configurations['walletOneProviderName']->config_value }}</span>
                            </div>
                        @endif
                        @if(!empty($configurations['walletTwoProviderName']->config_value ?? ''))
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Wallet 2: {{ $configurations['walletTwoProviderName']->config_value }}</span>
                            </div>
                        @endif
                        @if(!empty($configurations['walletThreeProviderName']->config_value ?? ''))
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Wallet 3: {{ $configurations['walletThreeProviderName']->config_value }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <a href="{{ route('admin.system.wallet-providers.wallet-api') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-center inline-block transition-colors duration-200">
                    <i class="fas fa-cog mr-2"></i>Configure
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
                    <p class="text-sm text-gray-500 mt-1">Monitor and manage your wallet providers</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button class="p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-center" onclick="getWalletBalances()">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-wallet text-blue-600 text-2xl mb-2"></i>
                        <span class="font-semibold text-gray-900">Check Balances</span>
                        <small class="text-gray-500">View wallet balances</small>
                    </div>
                </button>

                <button class="p-4 border border-gray-200 rounded-lg hover:bg-yellow-50 hover:border-yellow-300 transition-all duration-200 text-center" onclick="testAllConnections()">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-plug text-yellow-600 text-2xl mb-2"></i>
                        <span class="font-semibold text-gray-900">Test Connections</span>
                        <small class="text-gray-500">Verify provider APIs</small>
                    </div>
                </button>

                <button class="p-4 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 transition-all duration-200 text-center" onclick="getRecentTransactions()">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-chart-line text-green-600 text-2xl mb-2"></i>
                        <span class="font-semibold text-gray-900">Recent Transactions</span>
                        <small class="text-gray-500">View payment history</small>
                    </div>
                </button>

                <button class="p-4 border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-300 transition-all duration-200 text-center" onclick="generateWebhooks()">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-link text-purple-600 text-2xl mb-2"></i>
                        <span class="font-semibold text-gray-900">Webhook URLs</span>
                        <small class="text-gray-500">Get webhook endpoints</small>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Dynamic Content Areas -->
    <div class="hidden" id="balanceSection">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Wallet Balances</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="hideSection('balanceSection')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6" id="balanceContent">
                <!-- Balances will be loaded here -->
            </div>
        </div>
    </div>

    <div class="hidden" id="connectionTestSection">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Connection Tests</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="hideSection('connectionTestSection')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6" id="connectionTestContent">
                <!-- Connection test results will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function getWalletBalances() {
    const section = document.getElementById('balanceSection');
    const content = document.getElementById('balanceContent');

    section.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-500">Fetching wallet balances...</p>
            </div>
        </div>
    `;

    fetch('{{ route("admin.system.wallet-providers.balances") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="grid grid-cols-1 md:grid-cols-3 gap-6">';

                // Monnify Card
                const monnify = data.balances.monnify || {};
                let monnifyStatusColor = monnify.status === 'configured' ? 'blue' : 'gray';
                let monnifyIcon = monnify.status === 'configured' ? 'check-circle' : 'times-circle';
                html += `
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border-2 border-blue-200">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                                <div class="bg-blue-600 rounded-lg p-3 mr-3">
                                    <i class="fas fa-university text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-lg">Monnify</h4>
                                    <p class="text-sm text-gray-600">Virtual Accounts</p>
                                </div>
                            </div>
                            <i class="fas fa-${monnifyIcon} text-${monnifyStatusColor}-500 text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-2">${monnify.balance}</div>
                        <div class="text-sm text-gray-600 mb-3">${monnify.message || 'Available Balance'}</div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-${monnifyStatusColor}-100 text-${monnifyStatusColor}-800">
                            ${monnify.status === 'configured' ? 'Active' : 'Not Configured'}
                        </span>
                    </div>
                `;

                // Paystack Card
                const paystack = data.balances.paystack || {};
                let paystackStatusColor = paystack.status === 'configured' ? 'green' : 'gray';
                let paystackIcon = paystack.status === 'configured' ? 'check-circle' : 'times-circle';
                html += `
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border-2 border-green-200">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                                <div class="bg-green-600 rounded-lg p-3 mr-3">
                                    <i class="fas fa-credit-card text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-lg">Paystack</h4>
                                    <p class="text-sm text-gray-600">Payment Gateway</p>
                                </div>
                            </div>
                            <i class="fas fa-${paystackIcon} text-${paystackStatusColor}-500 text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-2">${paystack.balance}</div>
                        <div class="text-sm text-gray-600 mb-3">${paystack.message || 'Available Balance'}</div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-${paystackStatusColor}-100 text-${paystackStatusColor}-800">
                            ${paystack.status === 'configured' ? 'Active' : 'Not Configured'}
                        </span>
                    </div>
                `;

                // Uzobest Card
                const uzobest = data.balances.uzobest || {};
                let uzobestStatusColor = uzobest.status === 'success' ? 'purple' : (uzobest.status === 'error' ? 'red' : 'yellow');
                let uzobestIcon = uzobest.status === 'success' ? 'check-circle' : (uzobest.status === 'error' ? 'times-circle' : 'exclamation-triangle');
                html += `
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border-2 border-purple-200">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center">
                                <div class="bg-purple-600 rounded-lg p-3 mr-3">
                                    <i class="fas fa-server text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-lg">Uzobest</h4>
                                    <p class="text-sm text-gray-600">VTU Provider</p>
                                </div>
                            </div>
                            <i class="fas fa-${uzobestIcon} text-${uzobestStatusColor}-500 text-xl"></i>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-2">
                            ${uzobest.status === 'success' ? '₦' + Number(uzobest.balance).toLocaleString() : uzobest.balance}
                        </div>
                        <div class="text-sm text-gray-600 mb-3">${uzobest.message || 'Available Balance'}</div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-${uzobestStatusColor}-100 text-${uzobestStatusColor}-800">
                            ${uzobest.status === 'success' ? 'Connected' : uzobest.status === 'error' ? 'Error' : 'Warning'}
                        </span>
                    </div>
                `;

                html += '</div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                            <div>
                                <strong class="text-red-800">Failed to load balances</strong>
                                <p class="text-red-600 text-sm mt-1">${data.message || 'Please try again later or check your provider configurations.'}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-wifi text-red-500 mr-3"></i>
                        <div>
                            <strong class="text-red-800">Connection Error</strong>
                            <p class="text-red-600 text-sm mt-1">Unable to fetch wallet balances. Please check your internet connection.</p>
                        </div>
                    </div>
                </div>
            `;
        });
}

function testAllConnections() {
    const section = document.getElementById('connectionTestSection');
    const content = document.getElementById('connectionTestContent');

    section.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600 mx-auto mb-4"></div>
                <p class="text-gray-500">Testing provider connections...</p>
            </div>
        </div>
    `;

    const providers = ['monnify', 'paystack', 'uzobest'];
    let results = [];
    let completed = 0;

    providers.forEach(provider => {
        fetch('{{ route("admin.system.wallet-providers.test-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ provider: provider })
        })
        .then(response => response.json())
        .then(data => {
            results.push({
                provider: provider,
                success: data.success,
                message: data.message || (data.success ? 'Connection successful' : 'Connection failed')
            });
        })
        .catch(error => {
            results.push({
                provider: provider,
                success: false,
                message: 'Network error'
            });
        })
        .finally(() => {
            completed++;
            if (completed === providers.length) {
                displayConnectionResults(results);
            }
        });
    });

    function displayConnectionResults(results) {
        let html = '<div class="grid grid-cols-1 md:grid-cols-3 gap-6">';
        results.forEach(result => {
            const statusColor = result.success ? 'green' : 'red';
            const statusIcon = result.success ? 'check-circle' : 'times-circle';
            const providerName = result.provider.charAt(0).toUpperCase() + result.provider.slice(1);

            // Provider-specific styling
            let providerColor = 'blue';
            let providerIcon = 'server';
            if (result.provider === 'monnify') {
                providerColor = 'blue';
                providerIcon = 'university';
            } else if (result.provider === 'paystack') {
                providerColor = 'green';
                providerIcon = 'credit-card';
            } else if (result.provider === 'uzobest') {
                providerColor = 'purple';
                providerIcon = 'server';
            }

            html += `
                <div class="bg-gradient-to-br from-${providerColor}-50 to-${providerColor}-100 rounded-xl p-6 border-2 border-${providerColor}-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="bg-${providerColor}-600 rounded-lg p-3 mr-3">
                                <i class="fas fa-${providerIcon} text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">${providerName}</h4>
                                <p class="text-sm text-gray-600">${result.message}</p>
                            </div>
                        </div>
                        <i class="fas fa-${statusIcon} text-${statusColor}-500 text-2xl"></i>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800">
                        <i class="fas fa-${statusIcon} mr-1"></i>
                        ${result.success ? 'Connected' : 'Failed'}
                    </span>
                </div>
            `;
        });
        html += '</div>';
        content.innerHTML = html;
    }
}

function getRecentTransactions() {
    showToast('Transaction viewing will be available soon!', 'info');
}

function generateWebhooks() {
    showToast('Webhook URL generation will be available soon!', 'info');
}

function refreshAllData() {
    location.reload();
}

function hideSection(sectionId) {
    document.getElementById(sectionId).classList.add('hidden');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
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
