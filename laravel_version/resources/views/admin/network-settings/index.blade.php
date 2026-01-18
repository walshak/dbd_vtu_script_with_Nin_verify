@extends('layouts.admin')

@section('title', 'Network Settings')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Network Settings</h1>
                    <p class="text-blue-100 text-lg">Configure network services and IDs</p>
                </div>
                <div>
                    <i class="fas fa-network-wired text-5xl opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <!-- <div class="flex gap-4 mb-8">
        <a href="{{ route('admin.system.settings') }}"
           class="bg-gray-700 hover:bg-gray-800 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
            <i class="fas fa-cog mr-2"></i>General Settings
        </a>
        <a href="{{ route('admin.system.contacts') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
            <i class="fas fa-address-book mr-2"></i>Contact Settings
        </a>
        <a href="{{ route('admin.network-settings.index') }}"
           class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
            <i class="fas fa-network-wired mr-2"></i>Network Settings
        </a>
    </div> -->

    <!-- Network Selector -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Select Network</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="{{ route('admin.network-settings.index', ['network' => 'MTN']) }}"
               class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 hover:shadow-xl {{ $network == 'MTN' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-blue-300' }}">
                <div class="p-6 text-center">
                    <img src="{{ asset('assets/images/mtn.png') }}"
                         class="w-20 h-20 mx-auto mb-3 object-contain transition-transform duration-300 group-hover:scale-110"
                         alt="MTN" />
                    <h3 class="font-semibold text-gray-900">MTN</h3>
                    @if($network == 'MTN')
                        <span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                            <i class="fas fa-check"></i> Active
                        </span>
                    @endif
                </div>
            </a>

            <a href="{{ route('admin.network-settings.index', ['network' => 'AIRTEL']) }}"
               class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 hover:shadow-xl {{ $network == 'AIRTEL' ? 'border-red-500 bg-red-50' : 'border-gray-200 bg-white hover:border-red-300' }}">
                <div class="p-6 text-center">
                    <img src="{{ asset('assets/images/airtel.png') }}"
                         class="w-20 h-20 mx-auto mb-3 object-contain transition-transform duration-300 group-hover:scale-110"
                         alt="Airtel" />
                    <h3 class="font-semibold text-gray-900">AIRTEL</h3>
                    @if($network == 'AIRTEL')
                        <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                            <i class="fas fa-check"></i> Active
                        </span>
                    @endif
                </div>
            </a>

            <a href="{{ route('admin.network-settings.index', ['network' => 'GLO']) }}"
               class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 hover:shadow-xl {{ $network == 'GLO' ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-green-300' }}">
                <div class="p-6 text-center">
                    <img src="{{ asset('assets/images/glo.png') }}"
                         class="w-20 h-20 mx-auto mb-3 object-contain transition-transform duration-300 group-hover:scale-110"
                         alt="Glo" />
                    <h3 class="font-semibold text-gray-900">GLO</h3>
                    @if($network == 'GLO')
                        <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                            <i class="fas fa-check"></i> Active
                        </span>
                    @endif
                </div>
            </a>

            <a href="{{ route('admin.network-settings.index', ['network' => '9MOBILE']) }}"
               class="group relative overflow-hidden rounded-xl border-2 transition-all duration-300 hover:shadow-xl {{ $network == '9MOBILE' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 bg-white hover:border-emerald-300' }}">
                <div class="p-6 text-center">
                    <img src="{{ asset('assets/images/9mobile.png') }}"
                         class="w-20 h-20 mx-auto mb-3 object-contain transition-transform duration-300 group-hover:scale-110"
                         alt="9Mobile" />
                    <h3 class="font-semibold text-gray-900">9MOBILE</h3>
                    @if($network == '9MOBILE')
                        <span class="absolute top-2 right-2 bg-emerald-500 text-white text-xs px-2 py-1 rounded-full">
                            <i class="fas fa-check"></i> Active
                        </span>
                    @endif
                </div>
            </a>
        </div>
    </div>

    <!-- Settings Form -->
    <form method="post" action="{{ route('admin.network-settings.update') }}">
        @csrf
        @method('PUT')

        <!-- Service Status Section -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $network }} Network Status</h2>
                    <p class="text-gray-600 mt-1">Enable or disable network services</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="testNetworkServices('{{ $network }}')"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-check-circle mr-2"></i>Test Services
                    </button>
                    <button type="button" onclick="showServiceAnalytics('{{ $network }}')"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-chart-bar mr-2"></i>View Analytics
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- General Status -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-globe text-blue-500 mr-2"></i>{{ $network }} General (All)
                    </label>
                    <select name="general" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="On" {{ $networkData->networkStatus == 'On' ? 'selected' : '' }}>Enable</option>
                        <option value="Off" {{ $networkData->networkStatus == 'Off' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>

                <!-- VTU Status -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-mobile-alt text-green-500 mr-2"></i>{{ $network }} Airtime (VTU)
                    </label>
                    <select name="vtuStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="On" {{ $networkData->vtuStatus == 'On' ? 'selected' : '' }}>Enable</option>
                        <option value="Off" {{ $networkData->vtuStatus == 'Off' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>

                <!-- Share & Sell Status -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-share-alt text-purple-500 mr-2"></i>{{ $network }} Share & Sell
                    </label>
                    <select name="sharesellStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="On" {{ $networkData->sharesellStatus == 'On' ? 'selected' : '' }}>Enable</option>
                        <option value="Off" {{ $networkData->sharesellStatus == 'Off' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>

                <!-- SME Status -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-wifi text-blue-500 mr-2"></i>{{ $network }} SME
                    </label>
                    <select name="sme" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="On" {{ $networkData->smeStatus == 'On' ? 'selected' : '' }}>Enable</option>
                        <option value="Off" {{ $networkData->smeStatus == 'Off' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>

                <!-- Gifting Status -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-gift text-pink-500 mr-2"></i>{{ $network }} Gifting
                    </label>
                    <select name="gifting" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="On" {{ $networkData->giftingStatus == 'On' ? 'selected' : '' }}>Enable</option>
                        <option value="Off" {{ $networkData->giftingStatus == 'Off' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>

                <!-- Corporate Status -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-building text-indigo-500 mr-2"></i>{{ $network }} Corporate
                    </label>
                    <select name="corporate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="On" {{ $networkData->corporateStatus == 'On' ? 'selected' : '' }}>Enable</option>
                        <option value="Off" {{ $networkData->corporateStatus == 'Off' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>

                <!-- Recharge Card Status -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-receipt text-orange-500 mr-2"></i>{{ $network }} Recharge Card
                    </label>
                    <select name="airtimepin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="On" {{ $networkData->airtimepinStatus == 'On' ? 'selected' : '' }}>Enable</option>
                        <option value="Off" {{ $networkData->airtimepinStatus == 'Off' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>

                <!-- Data Pin Status -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-sim-card text-teal-500 mr-2"></i>{{ $network }} Data Pin
                    </label>
                    <select name="datapin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="On" {{ $networkData->datapinStatus == 'On' ? 'selected' : '' }}>Enable</option>
                        <option value="Off" {{ $networkData->datapinStatus == 'Off' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Network IDs Section -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900">{{ $network }} Network IDs</h2>
                <p class="text-red-600 mt-1 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Changing network IDs affects service routing. Use with caution.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- General ID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ $network }} General ID
                    </label>
                    <input type="number" name="networkid" value="{{ $networkData->networkid }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="General ID" required />
                </div>

                <!-- SME ID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ $network }} SME ID
                    </label>
                    <input type="number" name="smeId" value="{{ $networkData->smeId }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="SME ID" required />
                </div>

                <!-- Gifting ID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ $network }} Gifting ID
                    </label>
                    <input type="number" name="giftingId" value="{{ $networkData->giftingId }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Gifting ID" required />
                </div>

                <!-- Corporate ID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ $network }} Corporate ID
                    </label>
                    <input type="number" name="corporateId" value="{{ $networkData->corporateId }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Corporate ID" required />
                </div>

                <!-- VTU ID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ $network }} VTU ID
                    </label>
                    <input type="number" name="vtuId" value="{{ $networkData->vtuId }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="VTU ID" required />
                </div>

                <!-- Share & Sell ID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ $network }} Share & Sell ID
                    </label>
                    <input type="number" name="sharesellId" value="{{ $networkData->sharesellId }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Share & Sell ID" required />
                </div>
            </div>

            <input type="hidden" name="network" value="{{ $networkData->nId }}" />

            <div class="mt-8 flex gap-4">
                <button type="submit" name="update-network-setting"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200 shadow-lg">
                    <i class="fas fa-save mr-2"></i>Update {{ $network }} Settings
                </button>
                <button type="button" onclick="window.location.reload()"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Service Analytics Modal -->
<div id="analyticsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-600 bg-opacity-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="relative bg-white rounded-xl shadow-2xl max-w-4xl w-full">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 id="analyticsModalLabel" class="text-xl font-semibold text-gray-900">Network Service Analytics</h3>
                <button type="button" onclick="closeAnalyticsModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div id="analyticsContent" class="p-6">
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
                    <p class="mt-4 text-gray-600">Loading analytics...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testNetworkServices(network) {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing...';
    btn.disabled = true;

    fetch(`{{ route('admin.network-settings.status') }}?network=${network}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const services = data.services || [];
                const serviceStatus = services.length > 0
                    ? `Available services: ${services.join(', ')}`
                    : 'No active services';

                alert(`${network} Network Test Results:\n\n${serviceStatus}`);
            } else {
                alert('Network test failed');
            }
        })
        .catch(error => {
            console.error('Test error:', error);
            alert('Network test failed');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

function showServiceAnalytics(network) {
    const modal = document.getElementById('analyticsModal');
    document.getElementById('analyticsModalLabel').textContent = `${network} Service Analytics`;

    // Show modal
    modal.classList.remove('hidden');

    fetch(`{{ route('admin.network-settings.analytics') }}?network=${network}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const analytics = data.analytics;
                const content = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Total Transactions -->
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm mb-1">Total Transactions</p>
                                    <h3 class="text-3xl font-bold">${analytics.total_transactions}</h3>
                                </div>
                                <div class="bg-blue-400 bg-opacity-30 rounded-full p-4">
                                    <i class="fas fa-exchange-alt text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Success Rate -->
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm mb-1">Success Rate</p>
                                    <h3 class="text-3xl font-bold">${analytics.success_rate}%</h3>
                                </div>
                                <div class="bg-green-400 bg-opacity-30 rounded-full p-4">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue -->
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-sm mb-1">Revenue</p>
                                    <h3 class="text-3xl font-bold">₦${analytics.revenue}</h3>
                                </div>
                                <div class="bg-purple-400 bg-opacity-30 rounded-full p-4">
                                    <i class="fas fa-money-bill-wave text-2xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Failed Transactions -->
                        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-100 text-sm mb-1">Failed Transactions</p>
                                    <h3 class="text-3xl font-bold">${analytics.failed_transactions}</h3>
                                </div>
                                <div class="bg-red-400 bg-opacity-30 rounded-full p-4">
                                    <i class="fas fa-times-circle text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('analyticsContent').innerHTML = content;
            } else {
                document.getElementById('analyticsContent').innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
                        <i class="fas fa-exclamation-circle mr-2"></i>Failed to load analytics
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Analytics error:', error);
            document.getElementById('analyticsContent').innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
                    <i class="fas fa-exclamation-circle mr-2"></i>Failed to load analytics
                </div>
            `;
        });
}

function closeAnalyticsModal() {
    document.getElementById('analyticsModal').classList.add('hidden');
}

// Close modal on background click
document.getElementById('analyticsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAnalyticsModal();
    }
});

// Auto-save functionality
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('select[name^="general"], select[name$="Status"]');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Optional: Auto-save on change
            console.log(`${select.name} changed to ${select.value}`);
        });
    });
});
</script>
@endpush
