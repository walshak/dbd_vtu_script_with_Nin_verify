@extends('layouts.admin')

@section('title', 'API Configuration - General Settings')
@section('page-title', 'API Configuration')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-success me-2" href="{{ route('admin.api-configuration.index') }}">General Setting</a>
    <a class="btn btn-primary me-2" href="{{ route('admin.system.wallet-providers.monnify') }}">Monnify Setting</a>
    <a class="btn btn-info" href="{{ route('admin.system.wallet-providers.paystack') }}">Paystack Setting</a>
</div>

<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-dark btn-sm me-2" href="{{ route('admin.api-configuration.index') }}">General</a>
    <a class="btn btn-dark btn-sm me-2" href="{{ route('admin.api-configuration.airtime') }}">Airtime</a>
    <a class="btn btn-dark btn-sm me-2" href="{{ route('admin.api-configuration.data') }}">Data</a>
    <a class="btn btn-dark btn-sm" href="{{ route('admin.api-configuration.wallet') }}">Wallet</a>
</div>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Manage API Settings</h4>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form method="post" action="{{ route('admin.api-configuration.update-general') }}" class="form-submit">
                    @csrf

                    <!-- Cable TV Configuration -->
                    <div class="form-group mb-4">
                        <label for="cableVerificationApi" class="control-label font-medium">Cable Api Key</label>
                        <div class="">
                            <input type="text" name="cableVerificationApi"
                                   value="{{ $configs->get('cableVerificationApi')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="cableVerificationProvider" class="control-label font-medium">Cable TV IUC Verification Url</label>
                        <div class="">
                            <select name="cableVerificationProvider" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Select Api Provider</option>
                                @php $cableVerificationProvider = $configs->get('cableVerificationProvider')->value ?? ''; @endphp
                                @foreach($apiLinks->get('CableVer', collect()) as $apiLink)
                                    <option value="{{ $apiLink->value }}"
                                            {{ $cableVerificationProvider == $apiLink->value ? 'selected' : '' }}>
                                        {{ $apiLink->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="cableApi" class="control-label font-medium">Cable Api Key</label>
                        <div class="">
                            <input type="text" name="cableApi"
                                   value="{{ $configs->get('cableApi')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="cableProvider" class="control-label font-medium">Cable TV API Url</label>
                        <div class="">
                            <select name="cableProvider" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Select Api Provider</option>
                                @php $cableProvider = $configs->get('cableProvider')->value ?? ''; @endphp
                                @foreach($apiLinks->get('Cable', collect()) as $apiLink)
                                    <option value="{{ $apiLink->value }}"
                                            {{ $cableProvider == $apiLink->value ? 'selected' : '' }}>
                                        {{ $apiLink->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Electricity Configuration -->
                    <div class="form-group mb-4">
                        <label for="meterVerificationApi" class="control-label font-medium">Electricity Meter Api Key</label>
                        <div class="">
                            <input type="text" name="meterVerificationApi"
                                   value="{{ $configs->get('meterVerificationApi')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="meterVerificationProvider" class="control-label font-medium">Electricity Meter Verification Url</label>
                        <div class="">
                            <select name="meterVerificationProvider" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Select Api Provider</option>
                                @php $meterVerificationProvider = $configs->get('meterVerificationProvider')->value ?? ''; @endphp
                                @foreach($apiLinks->get('ElectricityVer', collect()) as $apiLink)
                                    <option value="{{ $apiLink->value }}"
                                            {{ $meterVerificationProvider == $apiLink->value ? 'selected' : '' }}>
                                        {{ $apiLink->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="meterApi" class="control-label font-medium">Electricity Meter Api Key</label>
                        <div class="">
                            <input type="text" name="meterApi"
                                   value="{{ $configs->get('meterApi')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="meterProvider" class="control-label font-medium">Electricity API Url</label>
                        <div class="">
                            <select name="meterProvider" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Select Api Provider</option>
                                @php $meterProvider = $configs->get('meterProvider')->value ?? ''; @endphp
                                @foreach($apiLinks->get('Electricity', collect()) as $apiLink)
                                    <option value="{{ $apiLink->value }}"
                                            {{ $meterProvider == $apiLink->value ? 'selected' : '' }}>
                                        {{ $apiLink->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Exam Configuration -->
                    <div class="form-group mb-4">
                        <label for="examApi" class="control-label font-medium">Exam Api Key</label>
                        <div class="">
                            <input type="text" name="examApi"
                                   value="{{ $configs->get('examApi')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="examProvider" class="control-label font-medium">Exam Checker API Url</label>
                        <div class="">
                            <select name="examProvider" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Select Api Provider</option>
                                @php $examProvider = $configs->get('examProvider')->value ?? ''; @endphp
                                @foreach($apiLinks->get('Exam', collect()) as $apiLink)
                                    <option value="{{ $apiLink->value }}"
                                            {{ $examProvider == $apiLink->value ? 'selected' : '' }}>
                                        {{ $apiLink->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Recharge Card Configuration -->
                    <div class="form-group mb-4">
                        <label for="rechargePinApi" class="control-label font-medium">Recharge Card API Key</label>
                        <div class="">
                            <input type="text" name="rechargePinApi"
                                   value="{{ $configs->get('rechargePinApi')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="rechargePinProvider" class="control-label font-medium">Recharge Card API Url</label>
                        <div class="">
                            <input type="text" name="rechargePinProvider"
                                   value="{{ $configs->get('rechargePinProvider')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <!-- Data Pin Configuration -->
                    <div class="form-group mb-4">
                        <label for="dataPinApi" class="control-label font-medium">Data Pin Api Key</label>
                        <div class="">
                            <input type="text" name="dataPinApi"
                                   value="{{ $configs->get('dataPinApi')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="dataPinProvider" class="control-label font-medium">Data Pin API Url</label>
                        <div class="">
                            <select name="dataPinProvider" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">Select Api Provider</option>
                                @php $dataPinProvider = $configs->get('dataPinProvider')->value ?? ''; @endphp
                                @foreach($apiLinks->get('Data Pin', collect()) as $apiLink)
                                    <option value="{{ $apiLink->value }}"
                                            {{ $dataPinProvider == $apiLink->value ? 'selected' : '' }}>
                                        {{ $apiLink->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Alpha Topup Configuration -->
                    <div class="form-group mb-4">
                        <label for="alphaApi" class="control-label font-medium">Alpha Topup API Key</label>
                        <div class="">
                            <input type="text" name="alphaApi"
                                   value="{{ $configs->get('alphaApi')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="alphaProvider" class="control-label font-medium">Alpha Topup API Url</label>
                        <div class="">
                            <input type="text" name="alphaProvider"
                                   value="{{ $configs->get('alphaProvider')->value ?? '' }}"
                                   class="form-control w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="">
                           <button type="submit" name="update-api-config" class="btn btn-info btn-submit">
                               <i class="fa fa-save" aria-hidden="true"></i> Update Details
                           </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
@endsection

@push('scripts')
<script>
// Add any specific JavaScript for this page
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save functionality could be added here
    console.log('API Configuration page loaded');
});
</script>
@endpush
