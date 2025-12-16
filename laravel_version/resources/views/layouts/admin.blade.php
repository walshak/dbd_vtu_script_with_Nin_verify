<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VTU Admin Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:200,300,400,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'nunito': ['Nunito', 'sans-serif'],
                    },
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                    }
                }
            }
        }
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .box-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            border-radius: 8px 8px 0 0;
        }

        .box-body {
            padding: 20px;
        }

        .box-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-left: 4px solid #007bff;
        }

        .stat-value {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
        }
    </style>

    @stack('styles')
</head>

<body class="font-nunito antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @include('components.admin-sidebar')

        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Top Navigation Bar -->
            @include('components.admin-topbar', ['title' => $title ?? 'Dashboard'])

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="px-4 sm:px-6 lg:px-8 py-6">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @push('scripts')
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

    <!-- Additional Scripts -->
    @stack('scripts')
</body>

</html>
