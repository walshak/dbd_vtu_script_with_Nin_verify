@extends('layouts.admin')

@section('title', 'API Configuration - Data Settings')
@section('page-title', 'Data API Configuration')

@section('content')
<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-success me-2" href="{{ route('admin.api-configuration.index') }}">General Setting</a>
    <a class="btn btn-primary me-2" href="{{ route('admin.system.wallet-providers.monnify') }}">Monnify Setting</a>
    <a class="btn btn-info" href="{{ route('admin.system.wallet-providers.paystack') }}">Paystack Setting</a>
</div>

<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-dark btn-sm me-2" href="{{ route('admin.api-configuration.index') }}">General</a>
    <a class="btn btn-dark btn-sm me-2" href="{{ route('admin.api-configuration.airtime') }}">Airtime</a>
    <a class="btn btn-primary btn-sm me-2" href="{{ route('admin.api-configuration.data') }}">Data</a>
    <a class="btn btn-dark btn-sm" href="{{ route('admin.api-configuration.wallet') }}">Wallet</a>
</div>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header">
                <div class="d-flex justify-content-between align-items-center mt-2 network-logos">
                    <a class="me-3" href="{{ route('admin.api-configuration.data', ['network' => 'MTN']) }}">
                        <img src="{{ asset('assets/images/mtn.png') }}" class="img-fluid {{ $network == 'MTN' ? 'border-primary' : '' }}" style="width:80px;" alt="MTN" />
                    </a>
                    <a class="me-3" href="{{ route('admin.api-configuration.data', ['network' => 'AIRTEL']) }}">
                        <img src="{{ asset('assets/images/airtel.png') }}" class="img-fluid {{ $network == 'AIRTEL' ? 'border-primary' : '' }}" style="width:80px;" alt="Airtel" />
                    </a>
                    <a class="me-3" href="{{ route('admin.api-configuration.data', ['network' => 'GLO']) }}">
                        <img src="{{ asset('assets/images/glo.png') }}" class="img-fluid {{ $network == 'GLO' ? 'border-primary' : '' }}" style="width:80px;" alt="Glo" />
                    </a>
                    <a class="me-3" href="{{ route('admin.api-configuration.data', ['network' => '9MOBILE']) }}">
                        <img src="{{ asset('assets/images/9mobile.png') }}" class="img-fluid {{ $network == '9MOBILE' ? 'border-primary' : '' }}" style="width:80px;" alt="9Mobile" />
                    </a>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form method="post" action="{{ route('admin.api-configuration.update-data') }}" class="form-submit">
                    @csrf
                    <input type="hidden" name="network" value="{{ $network }}">

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}SmeApi" class="form-label fw-bold">{{ $network }} Api Key (SME DATA)</label>
                        <input type="text" name="{{ strtolower($network) }}SmeApi"
                               value="{{ $configs->get(strtolower($network).'SmeApi')->value ?? '' }}"
                               placeholder="API Key" class="form-control" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}SmeProvider" class="form-label fw-bold">{{ $network }} Api Provider (SME DATA)</label>
                        <select name="{{ strtolower($network) }}SmeProvider" class="form-control" required>
                            <option value="">Select Api Provider</option>
                            @php $SmeProvider = $configs->get(strtolower($network).'SmeProvider')->value ?? ''; @endphp
                            @foreach($apiLinks as $apiLink)
                                <option value="{{ $apiLink->value }}"
                                        {{ $SmeProvider == $apiLink->value ? 'selected' : '' }}>
                                    {{ $apiLink->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}GiftingApi" class="form-label fw-bold">{{ $network }} Api Key (Gifting Data)</label>
                        <input type="text" name="{{ strtolower($network) }}GiftingApi"
                               value="{{ $configs->get(strtolower($network).'GiftingApi')->value ?? '' }}"
                               placeholder="API Key" class="form-control" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}GiftingProvider" class="form-label fw-bold">{{ $network }} Api Provider (Gifting Data)</label>
                        <select name="{{ strtolower($network) }}GiftingProvider" class="form-control" required>
                            <option value="">Select Api Provider</option>
                            @php $GiftingProvider = $configs->get(strtolower($network).'GiftingProvider')->value ?? ''; @endphp
                            @foreach($apiLinks as $apiLink)
                                <option value="{{ $apiLink->value }}"
                                        {{ $GiftingProvider == $apiLink->value ? 'selected' : '' }}>
                                    {{ $apiLink->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}CorporateApi" class="form-label fw-bold">{{ $network }} Api Key (Corporate Data)</label>
                        <input type="text" name="{{ strtolower($network) }}CorporateApi"
                               value="{{ $configs->get(strtolower($network).'CorporateApi')->value ?? '' }}"
                               placeholder="API Key" class="form-control" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="{{ strtolower($network) }}CorporateProvider" class="form-label fw-bold">{{ $network }} Api Provider (Corporate Data)</label>
                        <select name="{{ strtolower($network) }}CorporateProvider" class="form-control" required>
                            <option value="">Select Api Provider</option>
                            @php $CorporateProvider = $configs->get(strtolower($network).'CorporateProvider')->value ?? ''; @endphp
                            @foreach($apiLinks as $apiLink)
                                <option value="{{ $apiLink->value }}"
                                        {{ $CorporateProvider == $apiLink->value ? 'selected' : '' }}>
                                    {{ $apiLink->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="update-api-config" class="btn btn-primary">
                            <i class="fa fa-save" aria-hidden="true"></i> Update {{ $network }} Data Settings
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
    console.log('{{ $network }} Data configuration page loaded');
});
</script>
@endpush
