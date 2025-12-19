@extends('layouts.admin')

@section('title', 'Uzobest API Configuration')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Uzobest API Configuration</h1>
                        <nav class="text-blue-100 text-sm">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Dashboard</a>
                            <span class="mx-2">›</span>
                            <a href="{{ route('admin.api-configuration.index') }}" class="hover:text-white transition-colors">API Configuration</a>
                            <span class="mx-2">›</span>
                            <span class="text-blue-200">Uzobest</span>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.api-configuration.index') }}"
                            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Back to API Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default Provider Info Alert -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-1">Default VTU Provider</h4>
                    <p class="text-blue-700 text-sm">Uzobest GSM is configured as your default API provider for all VTU services including Data, Airtime, Cable TV, and Electricity.</p>
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
                                    <i class="fas fa-key text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">API Credentials</h2>
                                    <p class="text-sm text-gray-500 mt-1">Configure your Uzobest API credentials for VTU services</p>
                                </div>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
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

                        <form action="{{ route('admin.api-configuration.update-uzobest') }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- Validation Errors -->
                            @if ($errors->any())
                                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                                        <div>
                                            <h4 class="font-semibold text-red-800 mb-2">Please fix the following errors:</h4>
                                            <ul class="list-disc list-inside text-red-700 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- API Base URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">API Base URL</label>
                                <input type="text" value="{{ config('services.uzobest.url', 'https://uzobestgsm.com/api') }}" readonly
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-700">
                                <p class="mt-1 text-sm text-gray-500">Production: https://uzobestgsm.com/api</p>
                            </div>

                            <!-- API Key -->
                            <div>
                                <label for="uzobest_api_key" class="block text-sm font-medium text-gray-900 mb-2">
                                    API Key <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="uzobest_api_key" id="uzobest_api_key"
                                        value="{{ config('services.uzobest.key', '') }}"
                                        placeholder="Enter your Uzobest API Key"
                                        required
                                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" onclick="togglePassword('uzobest_api_key')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="uzobest_api_key-icon"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Get your API key from your Uzobest merchant dashboard</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3 pt-6">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i>Update Credentials
                                </button>
                                <button type="button" onclick="testConnection()"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-plug mr-2"></i>Test Connection
                                </button>
                                <a href="{{ route('admin.api-configuration.index') }}"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Service Endpoints Card -->
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-xl p-3 mr-4">
                                <i class="fas fa-plug text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Service Endpoints</h2>
                                <p class="text-sm text-gray-500 mt-1">Available Uzobest API endpoints</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Data Service -->
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-wifi text-blue-600 mr-2"></i>
                                        <span class="font-semibold text-gray-900">Data</span>
                                    </div>
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                                </div>
                                <code class="text-xs text-gray-600">/api/data/</code>
                            </div>

                            <!-- Airtime Service -->
                            <div class="p-4 bg-cyan-50 rounded-lg border border-cyan-100">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-cyan-600 mr-2"></i>
                                        <span class="font-semibold text-gray-900">Airtime</span>
                                    </div>
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                                </div>
                                <code class="text-xs text-gray-600">/api/topup/</code>
                            </div>

                            <!-- Cable TV Service -->
                            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-tv text-yellow-600 mr-2"></i>
                                        <span class="font-semibold text-gray-900">Cable TV</span>
                                    </div>
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                                </div>
                                <code class="text-xs text-gray-600">/api/cablesub/</code>
                            </div>

                            <!-- Electricity Service -->
                            <div class="p-4 bg-red-50 rounded-lg border border-red-100">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-bolt text-red-600 mr-2"></i>
                                        <span class="font-semibold text-gray-900">Electricity</span>
                                    </div>
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                                </div>
                                <code class="text-xs text-gray-600">/api/billpayment/</code>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-3">Validation Endpoints:</h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>IUC Validation: <code class="bg-gray-200 px-2 py-1 rounded">/api/validateiuc</code></span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Meter Validation: <code class="bg-gray-200 px-2 py-1 rounded">/api/validatemeter</code></span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span>Data Plans: <code class="bg-gray-200 px-2 py-1 rounded">/api/network/</code></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Network ID Mappings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="bg-purple-100 rounded-xl p-3 mr-4">
                                <i class="fas fa-signal text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Network IDs</h3>
                                <p class="text-sm text-gray-500 mt-1">Uzobest network mappings</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                                <div class="flex items-center">
                                    <span class="px-3 py-1 bg-yellow-500 text-white rounded font-semibold text-sm">MTN</span>
                                    <span class="ml-3 text-gray-700 font-medium">ID: <code>1</code></span>
                                </div>
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
                                <div class="flex items-center">
                                    <span class="px-3 py-1 bg-green-500 text-white rounded font-semibold text-sm">GLO</span>
                                    <span class="ml-3 text-gray-700 font-medium">ID: <code>2</code></span>
                                </div>
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100">
                                <div class="flex items-center">
                                    <span class="px-3 py-1 bg-red-500 text-white rounded font-semibold text-sm">9MOBILE</span>
                                    <span class="ml-3 text-gray-700 font-medium">ID: <code>3</code></span>
                                </div>
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                                <div class="flex items-center">
                                    <span class="px-3 py-1 bg-blue-500 text-white rounded font-semibold text-sm">AIRTEL</span>
                                    <span class="ml-3 text-gray-700 font-medium">ID: <code>4</code></span>
                                </div>
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Test & Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="bg-orange-100 rounded-xl p-3 mr-4">
                                <i class="fas fa-flask text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Connection Test</h3>
                                <p class="text-sm text-gray-500 mt-1">Test API connectivity</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="api-test-result" class="mb-4"></div>

                        <div class="space-y-3">
                            <button type="button" onclick="testUzobestConnection()"
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-plug mr-2"></i>Test Connection
                            </button>
                            <button type="button" onclick="fetchDataPlans()"
                                class="w-full bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-download mr-2"></i>Fetch Data Plans
                            </button>
                        </div>

                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-2 text-sm">Authentication:</h4>
                            <p class="text-xs text-gray-600 mb-1"><strong>Type:</strong> Token-based</p>
                            <p class="text-xs text-gray-600"><strong>Header:</strong> <code class="bg-gray-200 px-1 py-0.5 rounded">Authorization: Token &lt;api_key&gt;</code></p>
                        </div>

                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-2 text-sm">Response Format:</h4>
                            <pre class="text-xs text-gray-700 overflow-x-auto"><code>{
  "Status": "successful",
  "msg": "Transaction successful",
  "id": "12345"
}</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 rounded-xl p-3 mr-4">
                                <i class="fas fa-bolt text-indigo-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Quick Links</h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-2">
                            <a href="{{ route('admin.api-configuration.data') }}"
                                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="flex items-center">
                                    <i class="fas fa-wifi text-blue-600 mr-3"></i>
                                    <span class="text-sm font-medium text-gray-700">Data Plans</span>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400"></i>
                            </a>
                            <a href="{{ route('admin.api-configuration.airtime') }}"
                                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-cyan-600 mr-3"></i>
                                    <span class="text-sm font-medium text-gray-700">Airtime</span>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400"></i>
                            </a>
                            <a href="{{ route('admin.transactions.index') }}"
                                class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="flex items-center">
                                    <i class="fas fa-list text-gray-600 mr-3"></i>
                                    <span class="text-sm font-medium text-gray-700">Transactions</span>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
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

function testConnection() {
    testUzobestConnection();
}

function testUzobestConnection() {
    const resultDiv = document.getElementById('api-test-result');
    resultDiv.innerHTML = `
        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-600 mr-2"></i>
                <span class="text-sm text-blue-700">Testing connection...</span>
            </div>
        </div>
    `;

    fetch('/admin/api-configuration/test-uzobest', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Connection Successful!</p>
                            <p class="text-xs text-green-600 mt-1">Response time: ${data.response_time || 'N/A'}ms</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-times-circle text-red-600 mr-2"></i>
                        <div>
                            <p class="text-sm font-semibold text-red-800">Connection Failed</p>
                            <p class="text-xs text-red-600 mt-1">${data.message || 'Unknown error'}</p>
                        </div>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-800">Error</p>
                        <p class="text-xs text-red-600 mt-1">${error.message}</p>
                    </div>
                </div>
            </div>
        `;
    });
}

function fetchDataPlans() {
    const resultDiv = document.getElementById('api-test-result');
    resultDiv.innerHTML = `
        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-600 mr-2"></i>
                <span class="text-sm text-blue-700">Fetching data plans...</span>
            </div>
        </div>
    `;

    fetch('/admin/api-configuration/fetch-uzobest-plans', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Plans Fetched Successfully!</p>
                            <p class="text-xs text-green-600 mt-1">Total plans: ${data.count || 0}</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">${data.message}</p>
                        </div>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-800">Error</p>
                        <p class="text-xs text-red-600 mt-1">${error.message}</p>
                    </div>
                </div>
            </div>
        `;
    });
}
</script>
@endpush
