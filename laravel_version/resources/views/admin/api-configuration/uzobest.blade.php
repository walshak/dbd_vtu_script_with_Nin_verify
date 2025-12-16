@extends('layouts.admin')

@section('title', 'Uzobest API Configuration')
@section('page-title', 'Uzobest API Configuration')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-primary me-2" href="{{ route('admin.api-configuration.uzobest') }}">Uzobest (Default)</a>
    <a class="btn btn-secondary me-2" href="{{ route('admin.api-configuration.index') }}">General Settings</a>
    <a class="btn btn-info" href="{{ route('admin.system.wallet-providers.monnify') }}">Monnify Wallet</a>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <strong>Default Provider:</strong> Uzobest GSM is configured as your default API provider for all VTU services.
        </div>
    </div>
</div>

<div class="row">
    <!-- API Credentials -->
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border bg-primary">
                <h4 class="box-title text-white">
                    <i class="fa fa-key"></i> API Credentials
                </h4>
            </div>
            <div class="box-body">
                <form method="post" action="{{ route('admin.api-configuration.update-uzobest') }}" class="form-submit">
                    @csrf

                    <div class="form-group mb-3">
                        <label class="control-label font-medium">API Base URL</label>
                        <input type="text" name="uzobest_url" 
                               value="{{ config('services.uzobest.url') }}"
                               class="form-control" readonly>
                        <small class="text-muted">Production: https://uzobestgsm.com/api</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="control-label font-medium">API Key <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" id="uzobest_api_key" name="uzobest_api_key" 
                                   value="{{ env('UZOBEST_API_KEY') }}"
                                   class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('uzobest_api_key')">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Get your API key from Uzobest dashboard</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update Credentials
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Service Endpoints -->
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border bg-success">
                <h4 class="box-title text-white">
                    <i class="fa fa-plug"></i> Service Endpoints
                </h4>
            </div>
            <div class="box-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Endpoint</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fa fa-wifi text-primary"></i> Data</td>
                            <td><code>/api/data/</code></td>
                            <td><span class="badge bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-phone text-info"></i> Airtime</td>
                            <td><code>/api/topup/</code></td>
                            <td><span class="badge bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-tv text-warning"></i> Cable TV</td>
                            <td><code>/api/cablesub/</code></td>
                            <td><span class="badge bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-bolt text-danger"></i> Electricity</td>
                            <td><code>/api/billpayment/</code></td>
                            <td><span class="badge bg-success">Active</span></td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-3">
                    <h6>Validation Endpoints:</h6>
                    <ul class="list-unstyled small">
                        <li><i class="fa fa-check-circle text-success"></i> IUC Validation: <code>/api/validateiuc</code></li>
                        <li><i class="fa fa-check-circle text-success"></i> Meter Validation: <code>/api/validatemeter</code></li>
                        <li><i class="fa fa-check-circle text-success"></i> Data Plans: <code>/api/network/</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Network ID Mappings -->
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">
                    <i class="fa fa-signal"></i> Network ID Mappings
                </h4>
            </div>
            <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Network</th>
                            <th>Uzobest ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-warning">MTN</span></td>
                            <td><strong>1</strong></td>
                            <td><i class="fa fa-check-circle text-success"></i></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-success">GLO</span></td>
                            <td><strong>2</strong></td>
                            <td><i class="fa fa-check-circle text-success"></i></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger">9MOBILE</span></td>
                            <td><strong>3</strong></td>
                            <td><i class="fa fa-check-circle text-success"></i></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info">AIRTEL</span></td>
                            <td><strong>4</strong></td>
                            <td><i class="fa fa-check-circle text-success"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- API Test & Status -->
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">
                    <i class="fa fa-flask"></i> API Connection Test
                </h4>
            </div>
            <div class="box-body">
                <div id="api-test-result" class="mb-3"></div>
                
                <button type="button" class="btn btn-success btn-sm" onclick="testUzobestConnection()">
                    <i class="fa fa-refresh"></i> Test Connection
                </button>
                
                <button type="button" class="btn btn-info btn-sm" onclick="fetchDataPlans()">
                    <i class="fa fa-download"></i> Fetch Data Plans
                </button>

                <hr>

                <h6>Authentication:</h6>
                <p class="small">
                    <strong>Type:</strong> Token-based (Header)<br>
                    <strong>Header:</strong> <code>Authorization: Token &lt;api_key&gt;</code>
                </p>

                <h6>Response Format:</h6>
                <pre class="bg-light p-2 small"><code>{
  "Status": "successful",
  "msg": "Transaction successful",
  "id": "12345"
}</code></pre>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">
                    <i class="fa fa-cogs"></i> Quick Actions
                </h4>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.api-configuration.data') }}" class="btn btn-block btn-outline-primary">
                            <i class="fa fa-wifi"></i><br>
                            Configure Data Plans
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.api-configuration.airtime') }}" class="btn btn-block btn-outline-info">
                            <i class="fa fa-phone"></i><br>
                            Configure Airtime
                        </a>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-block btn-outline-warning" onclick="syncProviders()">
                            <i class="fa fa-sync"></i><br>
                            Sync Providers
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-block btn-outline-secondary">
                            <i class="fa fa-list"></i><br>
                            View Transactions
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
    const icon = event.target.closest('button').querySelector('i');
    
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

function testUzobestConnection() {
    const resultDiv = document.getElementById('api-test-result');
    resultDiv.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Testing connection...</div>';
    
    fetch('/admin/api/test-uzobest', {
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
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> <strong>Connection Successful!</strong><br>
                    <small>Response time: ${data.response_time || 'N/A'}</small>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa fa-times-circle"></i> <strong>Connection Failed</strong><br>
                    <small>${data.message || 'Unknown error'}</small>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fa fa-times-circle"></i> <strong>Error:</strong> ${error.message}
            </div>
        `;
    });
}

function fetchDataPlans() {
    const resultDiv = document.getElementById('api-test-result');
    resultDiv.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Fetching data plans...</div>';
    
    fetch('/admin/api/fetch-uzobest-plans', {
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
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> <strong>Plans Fetched Successfully!</strong><br>
                    <small>Total plans: ${data.count || 0}</small>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fa fa-times-circle"></i> <strong>Error:</strong> ${error.message}
            </div>
        `;
    });
}

function syncProviders() {
    if (!confirm('This will sync cable and electricity providers from Uzobest. Continue?')) {
        return;
    }
    
    const resultDiv = document.getElementById('api-test-result');
    resultDiv.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Syncing providers...</div>';
    
    // Implementation would call backend sync endpoint
    alert('Provider sync feature coming soon!');
}
</script>
@endpush
