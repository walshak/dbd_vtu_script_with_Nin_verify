<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="Data, Airtel, MTN, GLO, 9mobile, Airtime, Electricity bill, bill, data pin, recharge card, tv cable, DSTV, GOTV, startimes, vtu, vtu website, sme, cooperate, buy data">
    <meta name="description" content="VASTLEAD  - Buy/Resell Cheap Data, VTU Airtime, and Pay Utility Bills">
    <meta name="robots" content="index, follow">

    <title>@yield('title', 'VASTLEAD  - Buy/Resell Cheap Data, VTU Airtime, and Pay Utility Bills')</title>

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

    <!-- Favicon -->
    <link href="{{ asset('assets1/images/fav.png') }}" rel="icon" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-nunito antialiased bg-gray-50">
    <div class="min-h-screen">
        @yield('content')
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @stack('scripts')
</body>
</html>
