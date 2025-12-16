<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VTU Admin Dashboard')</title>

    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Admin Styles -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--light-color);
        }

        #wrapper {
            display: flex;
            width: 100%;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem;
            transition: margin 0.25s ease-out;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            width: 15rem;
        }

        #sidebar-wrapper.toggled {
            margin-left: 0;
        }

        .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
            color: rgba(255,255,255,.8);
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .list-group-item {
            background: transparent;
            border: none;
            border-radius: 0;
            color: rgba(255,255,255,.8);
            padding: 0.75rem 1.25rem;
            transition: all 0.15s ease-in-out;
        }

        .list-group-item:hover {
            background: rgba(255,255,255,.1);
            color: white;
        }

        .list-group-item.active {
            background: rgba(255,255,255,.1);
            color: white;
            border-left: 3px solid #fff;
        }

        #page-content-wrapper {
            width: 100%;
            transition: all 0.25s ease-out;
        }

        .navbar {
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
        }

        .card-header {
            background: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: #2e59d9;
            border-color: #2e59d9;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .box {
            background: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }

        .box-header {
            background: #f8f9fc;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #e3e6f0;
            border-radius: 0.35rem 0.35rem 0 0;
        }

        .box-body {
            padding: 1.25rem;
        }

        .box-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -15rem;
            }
        }

        .alert {
            border-radius: 0.35rem;
            border: 1px solid transparent;
        }

        .form-control {
            border-radius: 0.35rem;
            border: 1px solid #d1d3e2;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .table {
            color: var(--dark-color);
        }

        .table th {
            border-top: none;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
            color: var(--dark-color);
        }

        .badge {
            font-size: 0.8em;
            padding: 0.25em 0.5em;
        }

        .network-logos img {
            width: 80px;
            height: auto;
            margin-right: 10px;
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 8px;
            transition: border-color 0.3s;
        }

        .network-logos img:hover {
            border-color: var(--primary-color);
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-gradient-primary" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase">
                <i class="fas fa-mobile-alt me-2"></i>VTU Admin
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>

                <!-- System Monitoring -->
                <a href="{{ route('admin.monitoring.overview') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-heartbeat me-2"></i>System Monitoring
                </a>

                <!-- API Configuration -->
                <div class="list-group-item">
                    <i class="fas fa-cogs me-2"></i>API Configuration
                </div>
                <a href="{{ route('admin.api-configuration.index') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-settings me-2"></i>General Settings
                </a>
                <a href="{{ route('admin.api-configuration.airtime') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-phone me-2"></i>Airtime Settings
                </a>
                <a href="{{ route('admin.api-configuration.data') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-wifi me-2"></i>Data Settings
                </a>
                <a href="{{ route('admin.api-configuration.wallet') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-wallet me-2"></i>Wallet Settings
                </a>

                <!-- Network Settings -->
                <a href="{{ route('admin.network-settings.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-network-wired me-2"></i>Network Settings
                </a>

                <!-- Data Plans -->
                <a href="{{ route('admin.data-plans.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-database me-2"></i>Data Plans
                </a>

                <!-- Cable Plans -->
                <a href="{{ route('admin.cable-plans.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tv me-2"></i>Cable Plans
                </a>

                <!-- Transactions -->
                <div class="list-group-item">
                    <i class="fas fa-exchange-alt me-2"></i>Transactions
                </div>
                <a href="{{ route('admin.transactions.general-analysis') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-chart-line me-2"></i>General Analysis
                </a>
                <a href="{{ route('admin.transactions.airtime-analysis') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-phone me-2"></i>Airtime Analysis
                </a>
                <a href="{{ route('admin.transactions.data-analysis') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-wifi me-2"></i>Data Analysis
                </a>

                <!-- User Management -->
                <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-users me-2"></i>User Management
                </a>

                <!-- Service Plans -->
                <div class="list-group-item">
                    <i class="fas fa-list me-2"></i>Service Plans
                </div>
                <a href="{{ route('admin.data-plans.index') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-wifi me-2"></i>Data Plans
                </a>
                <a href="{{ route('admin.cable-plans.index') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-tv me-2"></i>Cable Plans
                </a>

                <!-- Payment Gateways -->
                <div class="list-group-item">
                    <i class="fas fa-credit-card me-2"></i>Payment Gateways
                </div>
                <a href="{{ route('admin.system.wallet-providers.index') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-wallet me-2"></i>Wallet Providers
                </a>
                <a href="{{ route('admin.system.wallet-providers.monnify') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-university me-2"></i>Monnify
                </a>
                <a href="{{ route('admin.system.wallet-providers.paystack') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-credit-card me-2"></i>Paystack
                </a>
                <a href="{{ route('admin.system.wallet-providers.wallet-api') }}" class="list-group-item list-group-item-action ps-4">
                    <i class="fas fa-exchange-alt me-2"></i>Wallet APIs
                </a>

                <!-- Reports -->
                <div class="list-group-item">
                    <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                </div>

                <!-- Settings -->
                <a href="{{ route('admin.system.settings') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-cog me-2"></i>Site Settings
                </a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">@yield('page-title', 'Dashboard')</h2>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle second-text fw-bold" href="#" id="navbarDropdown"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>{{ auth()->user()->name ?? 'Admin' }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}">Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.system.settings') }}">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid px-4">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
