{{--
    VTU Framework Integration
    Include this in your main layout to load the VTU framework
    Usage: @include('layouts.vtu-framework')
--}}

@push('styles')
    <!-- VTU Framework Styles -->
    <link href="{{ asset('css/vtu-framework.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <!-- VTU Framework Scripts -->
    <script src="{{ asset('js/vtu-framework.js') }}"></script>
    <script src="{{ asset('js/vtu-services.js') }}"></script>
    
    <!-- Initialize VTU Framework -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set user data for the framework
            window.userData = {
                id: {{ auth()->id() ?? 'null' }},
                name: "{{ auth()->user()->full_name ?? '' }}",
                email: "{{ auth()->user()->email ?? '' }}",
                phone: "{{ auth()->user()->phone ?? '' }}",
                wallet_balance: {{ auth()->user()->wallet_balance ?? 0 }}
            };
            
            // Initialize framework
            if (window.VTU) {
                VTU.loadUserData();
                console.log('VTU Framework initialized successfully');
            }
        });
    </script>
@endpush

{{-- Framework CSS Variables for Dynamic Theming --}}
<style>
:root {
    --vtu-user-balance: {{ auth()->user()->wallet_balance ?? 0 }};
}
</style>