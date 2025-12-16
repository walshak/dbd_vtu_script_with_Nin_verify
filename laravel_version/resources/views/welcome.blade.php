@extends('layouts.app')

@section('title', 'VASTLEAD  - Nigeria\'s Leading VTU Platform')

@section('content')
    <!-- Modern Navigation -->
    <nav class="fixed top-0 w-full bg-white/90 backdrop-blur-md z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand Name -->
                <div class="flex-shrink-0">
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        VASTLEAD
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#services" class="text-gray-600 hover:text-blue-600 transition-colors duration-300 font-medium">Services</a>
                    <a href="#features" class="text-gray-600 hover:text-blue-600 transition-colors duration-300 font-medium">Features</a>
                    <a href="#pricing" class="text-gray-600 hover:text-blue-600 transition-colors duration-300 font-medium">Pricing</a>
                    <a href="#contact" class="text-gray-600 hover:text-blue-600 transition-colors duration-300 font-medium">Contact</a>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition-all duration-300">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="text-gray-600 hover:text-blue-600 transition-colors duration-300 font-medium">Sign In</a>
                            <a href="{{ route('register') }}"
                               class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-full hover:shadow-lg transition-all duration-300">
                                Get Started
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="mobile-menu-button text-gray-600 hover:text-blue-600 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="md:hidden mobile-menu hidden bg-white border-t border-gray-100">
            <div class="px-4 pt-2 pb-3 space-y-1">
                <a href="#services" class="block px-3 py-2 text-gray-600 hover:text-blue-600 font-medium">Services</a>
                <a href="#features" class="block px-3 py-2 text-gray-600 hover:text-blue-600 font-medium">Features</a>
                <a href="#pricing" class="block px-3 py-2 text-gray-600 hover:text-blue-600 font-medium">Pricing</a>
                <a href="#contact" class="block px-3 py-2 text-gray-600 hover:text-blue-600 font-medium">Contact</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block px-3 py-2 bg-blue-600 text-white rounded-lg mt-4">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-600 hover:text-blue-600 font-medium">Sign In</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 bg-blue-600 text-white rounded-lg mt-2">Get Started</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-16 min-h-screen bg-gradient-to-br from-gray-50 to-white flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-200 rounded-full text-blue-700 text-sm font-medium mb-8">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Nigeria's #1 VTU Platform
                </div>

                <!-- Main Heading -->
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-8 leading-tight">
                    Digital
                    <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 bg-clip-text text-transparent">
                        VTU Solutions
                    </span>
                    <br>Made Simple
                </h1>

                <!-- Subheading -->
                <p class="text-xl md:text-2xl text-gray-600 mb-12 max-w-4xl mx-auto leading-relaxed">
                    Experience lightning-fast data purchases, instant airtime top-ups, and seamless bill payments.
                    <span class="font-semibold text-gray-800">Join thousands of satisfied customers</span> who trust us daily.
                </p>

                <!-- Stats Row -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12 max-w-2xl mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">10K+</div>
                        <div class="text-gray-500 text-sm">Active Users</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">99.9%</div>
                        <div class="text-gray-500 text-sm">Uptime</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">24/7</div>
                        <div class="text-gray-500 text-sm">Support</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900">5%</div>
                        <div class="text-gray-500 text-sm">Discount</div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                Start For Free
                            </a>
                            <a href="{{ route('login') }}"
                               class="bg-white border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-full text-lg font-semibold hover:border-blue-600 hover:text-blue-600 transition-all duration-300">
                                Sign In
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Trust Indicators -->
                <div class="flex flex-wrap justify-center items-center gap-8 text-gray-400">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium">Instant Delivery</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium">100% Secure</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                        <span class="text-sm font-medium">24/7 Support</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Supported Networks Section -->
    <section class="py-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                    Supported Networks
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    We support all major telecommunications networks in Nigeria for seamless transactions
                </p>
            </div>

            <!-- Network Logos Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 items-center justify-items-center">
                <!-- MTN -->
                <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 w-32 h-32 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <img src="{{ asset('home/img/clients/mtn.png') }}" alt="MTN"
                         class="relative z-10 h-16 w-auto object-contain filter group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        MTN
                    </div>
                </div>

                <!-- Airtel -->
                <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 w-32 h-32 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-red-100 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <img src="{{ asset('home/img/clients/airtel.png') }}" alt="Airtel"
                         class="relative z-10 h-16 w-auto object-contain filter group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        Airtel
                    </div>
                </div>

                <!-- Glo -->
                <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 w-32 h-32 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <img src="{{ asset('home/img/clients/glo.png') }}" alt="Glo"
                         class="relative z-10 h-16 w-auto object-contain filter group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        Glo
                    </div>
                </div>

                <!-- 9mobile -->
                <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 w-32 h-32 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <img src="{{ asset('home/img/clients/etisalat.png') }}" alt="9mobile"
                         class="relative z-10 h-16 w-auto object-contain filter group-hover:scale-110 transition-transform duration-300">
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-emerald-500 text-white px-3 py-1 rounded-full text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        9mobile
                    </div>
                </div>
            </div>

            <!-- Trust Indicators -->
            <div class="mt-12 flex flex-wrap justify-center items-center gap-8 text-gray-500">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">All Networks Supported</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">Instant Processing</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium">Best Rates Guaranteed</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Services Section -->
    <section class="py-24 bg-white" id="services">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Our <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Services</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need for digital transactions in one powerful platform
                </p>
            </div>

            <!-- Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Data Purchase -->
                <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="absolute top-6 right-6 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 mt-8">
                        Data Purchase
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Buy affordable data bundles for all networks with instant delivery and best prices guaranteed.
                    </p>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Instant delivery
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            All networks supported
                        </div>
                    </div>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition-colors duration-300">
                        Get Started
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Airtime Top-up -->
                <div class="group relative bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="absolute top-6 right-6 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 mt-8">
                        Airtime Top-up
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Purchase airtime for all networks with up to 5% discount on every transaction.
                    </p>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            5% discount
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Fast processing
                        </div>
                    </div>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center text-purple-600 hover:text-purple-800 font-semibold transition-colors duration-300">
                        Get Started
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Bill Payments -->
                <div class="group relative bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="absolute top-6 right-6 w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 mt-8">
                        Bill Payments
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Pay electricity bills, cable TV subscriptions, and other utility bills seamlessly.
                    </p>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            All utilities
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Secure payments
                        </div>
                    </div>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center text-green-600 hover:text-green-800 font-semibold transition-colors duration-300">
                        Get Started
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Educational Services -->
                <div class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="absolute top-6 right-6 w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 mt-8">
                        Exam Pins
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Get WAEC, NECO, and JAMB result checker pins instantly at competitive rates.
                    </p>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Valid pins only
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Instant delivery
                        </div>
                    </div>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold transition-colors duration-300">
                        Get Started
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-20 bg-white" id="features">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Virtual Top Up</h2>
                <p class="text-xl text-gray-600">Electronic vending of data and airtime</p>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Service 1 -->
                <div
                    class="bg-gray-50 rounded-2xl p-8 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <img src="{{ asset('home/img/feature-2.png') }}" alt="Data" class="w-16 h-16 mx-auto mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <a href="{{ route('login') }}" class="hover:text-blue-600 transition-colors">Buy cheap Data
                            Online</a>
                    </h3>
                    <p class="text-gray-600">Buy cheap mobile data at an affordable rate. Mighty data.</p>
                </div>

                <!-- Service 2 -->
                <div
                    class="bg-gray-50 rounded-2xl p-8 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <img src="{{ asset('home/img/feature-2.png') }}" alt="Airtime" class="w-16 h-16 mx-auto mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <a href="{{ route('login') }}" class="hover:text-blue-600 transition-colors">Buy Airtime</a>
                    </h3>
                    <p class="text-gray-600">Get up to 5% discount instantly when you purchase airtime.</p>
                </div>

                <!-- Service 3 - Temporarily Hidden
                <div
                    class="bg-gray-50 rounded-2xl p-8 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <img src="{{ asset('home/img/feature-3.png') }}" alt="Convert" class="w-16 h-16 mx-auto mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <a href="{{ route('login') }}" class="hover:text-blue-600 transition-colors">Convert Airtime to
                            Cash</a>
                    </h3>
                    <p class="text-gray-600">Convert your excess Airtime and get paid within 5 mins.</p>
                </div>
                -->

                <!-- Service 4 -->
                <div
                    class="bg-gray-50 rounded-2xl p-8 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <img src="{{ asset('home/img/feature-3.png') }}" alt="WAEC" class="w-16 h-16 mx-auto mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <a href="{{ route('login') }}" class="hover:text-blue-600 transition-colors">Waec ePins</a>
                    </h3>
                    <p class="text-gray-600">Easy Waec registration. Instant e-pin purchase.</p>
                </div>
            </div>

            <div class="mt-12">
                <div class="bg-blue-50 rounded-2xl p-6">
                    <p class="text-blue-800 font-semibold text-lg">
                        <strong>Other features:</strong> Cable Subscriptions (gotv/dstv/startimes), Waec scratch card,
                        Recharge Printing, MTN AWUF4U airtime, mPOS, pay Electricity Bills etc.
                    </p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-gray-50" id="features">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Why Choose <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">VASTLEAD </span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    We provide the most reliable, fast, and secure VTU services in Nigeria
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Lightning Fast</h3>
                    <p class="text-gray-600">
                        Experience instant delivery of all services. No waiting, no delays - just immediate results.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">100% Secure</h3>
                    <p class="text-gray-600">
                        Your transactions are protected with bank-level security. Safe, secure, and reliable always.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">24/7 Support</h3>
                    <p class="text-gray-600">
                        Round-the-clock customer support to help you with any questions or issues you might have.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Best Prices</h3>
                    <p class="text-gray-600">
                        Enjoy the most competitive rates in the market with additional discounts on bulk purchases.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Analytics</h3>
                    <p class="text-gray-600">
                        Track your spending, monitor usage patterns, and get detailed reports on all transactions.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">User Friendly</h3>
                    <p class="text-gray-600">
                        Simple, intuitive interface designed for everyone. Easy to use for both beginners and experts.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-gradient-to-r from-blue-600 to-purple-600">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-white/90 mb-12">
                Join thousands of satisfied customers and experience the future of VTU services today.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="bg-white text-blue-600 px-8 py-4 rounded-full text-lg font-semibold hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="bg-white text-blue-600 px-8 py-4 rounded-full text-lg font-semibold hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            Create Free Account
                        </a>
                        <a href="{{ route('login') }}"
                           class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white hover:text-blue-600 transition-all duration-300">
                            Sign In
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-24 bg-white" id="contact">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Get in <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Touch</span>
                </h2>
                <p class="text-xl text-gray-600">
                    Have questions? We're here to help you 24/7
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Info -->
                <div class="space-y-8">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Email Us</h3>
                            <p class="text-gray-600">support@dbdconcepts.com</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Call Us</h3>
                            <p class="text-gray-600">+234 (0) 123 456 7890</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Visit Us</h3>
                            <p class="text-gray-600">Lagos, Nigeria</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-gray-50 rounded-2xl p-8">
                    <form class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="name" name="name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea id="message" name="message" rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-4">
                    VASTLEAD
                </div>
                <p class="text-gray-400 mb-8">
                    Nigeria's leading VTU platform for all your digital needs
                </p>
                <div class="flex justify-center space-x-6 mb-8">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Support</a>
                </div>
                <p class="text-gray-500 text-sm">
                    © 2024 VASTLEAD . All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>

@endsection
