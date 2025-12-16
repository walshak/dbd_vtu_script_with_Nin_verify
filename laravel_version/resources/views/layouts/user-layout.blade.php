@extends('layouts.app')

@section('title', ($title ?? 'Page') . ' - VASTLEAD ')

@section('content')
<div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    @include('components.user-sidebar')

    <!-- Mobile sidebar overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 min-w-0">
        <!-- Top Navigation Bar -->
        @include('components.user-topbar', ['title' => $title ?? 'Page'])

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
            @yield('page-content')
        </main>
    </div>
</div>

@push('scripts')
<!-- VTU Framework Core -->
<script src="{{ asset('js/vtu-framework.js') }}"></script>
<script src="{{ asset('js/vtu-services.js') }}"></script>

<script>
$(document).ready(function() {
    // Mobile sidebar toggle
    $('#mobile-menu-button').click(function() {
        $('#sidebar').removeClass('-translate-x-full').addClass('translate-x-0');
        $('#sidebar-overlay').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    });

    // Close sidebar when clicking overlay
    $('#sidebar-overlay').click(function() {
        $('#sidebar').removeClass('translate-x-0').addClass('-translate-x-full');
        $('#sidebar-overlay').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    });

    // Profile menu toggle
    $('#profile-menu-button').click(function(e) {
        e.stopPropagation();
        $('#profile-menu').toggleClass('hidden');
    });

    // Close profile menu when clicking outside
    $(document).click(function(event) {
        if (!$(event.target).closest('#profile-menu-button, #profile-menu').length) {
            $('#profile-menu').addClass('hidden');
        }
    });

    // Initialize sidebar state on mobile
    if ($(window).width() < 1024) {
        $('#sidebar').addClass('-translate-x-full');
    }

    // Auto-close mobile sidebar on resize
    $(window).resize(function() {
        if ($(window).width() >= 1024) {
            $('#sidebar').removeClass('-translate-x-full').addClass('translate-x-0');
            $('#sidebar-overlay').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        } else {
            $('#sidebar').removeClass('translate-x-0').addClass('-translate-x-full');
        }
    });
});
</script>
@endpush
@endsection
