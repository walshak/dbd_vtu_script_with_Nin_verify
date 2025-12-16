@extends('layouts.admin')

@section('title', 'API Configuration - Airtime Settings')
@section('page-title', 'Airtime API Configuration')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-success me-2" href="{{ route('admin.api-configuration.index') }}">General Setting</a>
    <a class="btn btn-primary me-2" href="{{ route('admin.system.wallet-providers.monnify') }}">Monnify Setting</a>
    <a class="btn btn-info" href="{{ route('admin.system.wallet-providers.paystack') }}">Paystack Setting</a>
</div>

<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-dark btn-sm me-2" href="{{ route('admin.api-configuration.index') }}">General</a>
    <a class="btn btn-primary btn-sm me-2" href="{{ route('admin.api-configuration.airtime') }}">Airtime</a>
    <a class="btn btn-dark btn-sm me-2" href="{{ route('admin.api-configuration.data') }}">Data</a>
    <a class="btn btn-dark btn-sm" href="{{ route('admin.api-configuration.wallet') }}">Wallet</a>
</div>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header">
                <div class="d-flex justify-content-between align-items-center mt-2 network-logos">
                    <a class="me-3" href="{{ route('admin.api-configuration.airtime', ['network' => 'MTN']) }}">
                        <img src="{{ asset('assets/images/mtn.png') }}" class="img-fluid {{ $network == 'MTN' ? 'border-primary' : '' }}" style="width:80px;" alt="MTN" />
                    </a>
                    <a class="me-3" href="{{ route('admin.api-configuration.airtime', ['network' => 'AIRTEL']) }}">
                        <img src="{{ asset('assets/images/airtel.png') }}" class="img-fluid {{ $network == 'AIRTEL' ? 'border-primary' : '' }}" style="width:80px;" alt="Airtel" />
                    </a>
                    <a class="me-3" href="{{ route('admin.api-configuration.airtime', ['network' => 'GLO']) }}">
                        <img src="{{ asset('assets/images/glo.png') }}" class="img-fluid {{ $network == 'GLO' ? 'border-primary' : '' }}" style="width:80px;" alt="Glo" />
                    </a>
                    <a class="me-3" href="{{ route('admin.api-configuration.airtime', ['network' => '9MOBILE']) }}">
                        <img src="{{ asset('assets/images/9mobile.png') }}" class="img-fluid {{ $network == '9MOBILE' ? 'border-primary' : '' }}" style="width:80px;" alt="9Mobile" />
                    </a>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form method="post" action="{{ route('admin.api-configuration.update-airtime') }}" class="form-submit">
                    @csrf
                    <input type="hidden" name="network" value="{{ $network }}">

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}VtuKey" class="form-label fw-bold">{{ $network }} Api Key (VTU)</label>
                        <input type="text" name="{{ strtolower($network) }}VtuKey"
                               value="{{ $configs->get(strtolower($network).'VtuKey')->value ?? '' }}"
                               placeholder="API Key" class="form-control" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}VtuProvider" class="form-label fw-bold">{{ $network }} Api Provider (VTU)</label>
                        <select name="{{ strtolower($network) }}VtuProvider" class="form-control" required>
                            <option value="">Select Api Provider</option>
                            @php $VtuProvider = $configs->get(strtolower($network).'VtuProvider')->value ?? ''; @endphp
                            @foreach($apiLinks as $apiLink)
                                <option value="{{ $apiLink->value }}"
                                        {{ $VtuProvider == $apiLink->value ? 'selected' : '' }}>
                                    {{ $apiLink->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}SharesellKey" class="form-label fw-bold">{{ $network }} Api Key (Share & Sell)</label>
                        <input type="text" name="{{ strtolower($network) }}SharesellKey"
                               value="{{ $configs->get(strtolower($network).'SharesellKey')->value ?? '' }}"
                               placeholder="API Key" class="form-control" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}SharesellProvider" class="form-label fw-bold">{{ $network }} Api Provider (Share & Sell)</label>
                        <select name="{{ strtolower($network) }}SharesellProvider" class="form-control" required>
                            <option value="">Select Api Provider</option>
                            @php $SharesellProvider = $configs->get(strtolower($network).'SharesellProvider')->value ?? ''; @endphp
                            @foreach($apiLinks as $apiLink)
                                <option value="{{ $apiLink->value }}"
                                        {{ $SharesellProvider == $apiLink->value ? 'selected' : '' }}>
                                    {{ $apiLink->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="update-api-config" class="btn btn-primary">
                            <i class="fa fa-save" aria-hidden="true"></i> Update {{ $network }} Settings
                        </button>
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('{{ $network }} Airtime configuration page loaded');
});
</script>
@endpush
