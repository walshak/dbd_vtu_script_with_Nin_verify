@extends('layouts.admin')

@section('title', 'Monnify Configuration')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Monnify Configuration</h1>
                        <nav class="text-blue-100 text-sm">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Dashboard</a>
                            <span class="mx-2">›</span>
                            <a href="{{ route('admin.system.wallet-providers.index') }}"
                                class="hover:text-white transition-colors">Wallet Providers</a>
                            <span class="mx-2">›</span>
                            <span class="text-blue-200">Monnify</span>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.system.wallet-providers.index') }}"
                            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Providers
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Configuration Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <!-- Card Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-100 rounded-xl p-3 mr-4">
                                    <i class="fas fa-university text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">Monnify Payment Gateway</h2>
                                    <p class="text-sm text-gray-500 mt-1">Configure Monnify for virtual account generation
                                        and payment processing</p>
                                </div>
                            </div>
                            <div>
                                @if (isset($configurations['monifyStatus']) && $configurations['monifyStatus']->config_value === 'On')
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Active
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                        @if (session('success'))
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    <p class="text-green-800">{{ session('success') }}</p>
                                    <button onclick="this.parentElement.parentElement.remove()"
                                        class="ml-auto text-green-500 hover:text-green-700">
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
                                    <button onclick="this.parentElement.parentElement.remove()"
                                        class="ml-auto text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('admin.system.wallet-providers.monnify.update') }}" method="POST"
                            class="space-y-6">
                            @csrf

                            <!-- Validation Errors -->
                            @if ($errors->any())
                                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                                        <div>
                                            <h4 class="font-semibold text-red-800 mb-2">Please fix the following errors:
                                            </h4>
                                            <ul class="list-disc list-inside text-red-700 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Service Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-3">Service Status</label>
                                <div class="flex items-center">
                                    <input type="checkbox" id="monifyStatus" name="monifyStatus" value="On"
                                        {{ isset($configurations['monifyStatus']) && $configurations['monifyStatus']->config_value === 'On' ? 'checked' : '' }}
                                        class="w-6 h-6 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <label for="monifyStatus" class="ml-3 text-sm text-gray-700">
                                        <span
                                            class="status-text font-medium">{{ isset($configurations['monifyStatus']) && $configurations['monifyStatus']->config_value === 'On' ? 'Enabled' : 'Disabled' }}</span>
                                    </label>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Enable or disable Monnify payment gateway</p>
                            </div>

                            <!-- Environment Selection -->
                            <div>
                                <label for="monifyEnvironment"
                                    class="block text-sm font-medium text-gray-900 mb-2">Environment</label>
                                <select name="monifyEnvironment" id="monifyEnvironment"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="sandbox"
                                        {{ isset($configurations['monifyEnvironment']) && $configurations['monifyEnvironment']->config_value === 'sandbox' ? 'selected' : '' }}>
                                        Sandbox (Testing)
                                    </option>
                                    <option value="live"
                                        {{ isset($configurations['monifyEnvironment']) && $configurations['monifyEnvironment']->config_value === 'live' ? 'selected' : '' }}>
                                        Live (Production)
                                    </option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Use sandbox for testing, live for production</p>
                            </div>

                            <!-- API Credentials -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="monifyApi" class="block text-sm font-medium text-gray-900 mb-2">
                                        API Key <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="monifyApi" id="monifyApi"
                                            value="{{ isset($configurations['monifyApi']) ? $configurations['monifyApi']->config_value : '' }}"
                                            placeholder="Enter Monnify API Key"
                                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <button type="button" onclick="togglePassword('monifyApi')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye" id="monifyApi-icon"></i>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Your Monnify API Key from merchant dashboard</p>
                                </div>
                                <div>
                                    <label for="monifySecrete" class="block text-sm font-medium text-gray-900 mb-2">
                                        Secret Key <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="monifySecrete" id="monifySecrete"
                                            value="{{ isset($configurations['monifySecrete']) ? $configurations['monifySecrete']->config_value : '' }}"
                                            placeholder="Enter Monnify Secret Key"
                                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <button type="button" onclick="togglePassword('monifySecrete')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye" id="monifySecrete-icon"></i>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Your Monnify Secret Key from merchant dashboard
                                    </p>
                                </div>
                            </div>

                            <!-- Contract Code and Charges -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="monifyContract" class="block text-sm font-medium text-gray-900 mb-2">
                                        Contract Code <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="monifyContract" id="monifyContract"
                                        value="{{ isset($configurations['monifyContract']) ? $configurations['monifyContract']->config_value : '' }}"
                                        placeholder="Enter Contract Code"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <p class="mt-1 text-sm text-gray-500">Your Monnify Contract Code for virtual accounts
                                    </p>
                                </div>
                                <div>
                                    <label for="monifyCharges" class="block text-sm font-medium text-gray-900 mb-2">
                                        Transaction Charges (%)
                                    </label>
                                    <input type="number" step="0.01" min="0" max="100"
                                        name="monifyCharges" id="monifyCharges"
                                        value="{{ isset($configurations['monifyCharges']) ? $configurations['monifyCharges']->config_value : '1.5' }}"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <p class="mt-1 text-sm text-gray-500">Percentage charge for transactions</p>
                                </div>
                            </div>

                            <!-- Webhook URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Webhook URL</label>
                                <div class="flex">
                                    <input type="text" value="{{ url('/webhook/monnify') }}" readonly
                                        class="flex-1 px-4 py-3 bg-gray-50 border border-gray-300 rounded-l-lg text-gray-700">
                                    <button type="button" onclick="copyWebhookUrl()"
                                        class="px-4 py-3 bg-blue-600 text-white border border-blue-600 rounded-r-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-copy mr-1"></i>Copy
                                    </button>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Add this URL to your Monnify merchant dashboard
                                    webhook settings</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3 pt-6">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i>Save Configuration
                                </button>
                                <button type="button" onclick="testConnection()"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-plug mr-2"></i>Test Connection
                                </button>
                                <a href="{{ route('admin.system.wallet-providers.index') }}"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Setup Guide -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-question-circle text-blue-600 mr-2"></i>Setup Guide
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                    1</div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Get Monnify Account</h4>
                                    <p class="text-sm text-gray-500">Sign up at <a href="https://monnify.com"
                                            target="_blank" class="text-blue-600 hover:text-blue-800">monnify.com</a> and
                                        complete KYC verification</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                    2</div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Get API Credentials</h4>
                                    <p class="text-sm text-gray-500">From your merchant dashboard, get API Key, Secret Key,
                                        and Contract Code</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                    3</div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Configure Webhook</h4>
                                    <p class="text-sm text-gray-500">Add the webhook URL to your Monnify dashboard for
                                        payment notifications</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                    4</div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Test Integration</h4>
                                    <p class="text-sm text-gray-500">Use sandbox mode first, then switch to live after
                                        testing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>Features
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span class="text-gray-700">Virtual account generation</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span class="text-gray-700">Instant payment processing</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span class="text-gray-700">Multiple bank support</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span class="text-gray-700">Webhook notifications</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                <span class="text-gray-700">Secure transactions</span>
                            </li>
                        </ul>
                    </div>
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

        // Copy webhook URL
        function copyWebhookUrl() {
            const webhookUrl = '{{ url('/webhook/monnify') }}';
            navigator.clipboard.writeText(webhookUrl).then(() => {
                showToast('Webhook URL copied to clipboard!', 'success');
            });
        }

        // Test Monnify connection
        function testConnection() {
            const btn = event.target;
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
            btn.disabled = true;

            fetch('{{ route('admin.system.wallet-providers.test-connection') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        provider: 'monnify'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Connection successful!', 'success');
                    } else {
                        showToast('Connection failed: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showToast('Connection test failed', 'error');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
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

        // Update status text when toggle changes
        document.getElementById('monifyStatus').addEventListener('change', function() {
            const statusText = this.closest('div').querySelector('.status-text');
            statusText.textContent = this.checked ? 'Enabled' : 'Disabled';
        });
    </script>
@endsection
