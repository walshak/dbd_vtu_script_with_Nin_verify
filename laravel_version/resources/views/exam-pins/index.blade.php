@extends('layouts.user-layout')

@section('title', 'Exam Pin Purchase')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Enhanced Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-green-500 via-blue-600 to-indigo-700 rounded-2xl p-8 text-white relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-6 -right-6 w-40 h-40 bg-white rounded-full"></div>
                    <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white rounded-full"></div>
                    <div class="absolute top-1/3 left-1/3 w-20 h-20 bg-white rounded-full"></div>
                </div>

                <div class="relative z-10">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center">
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold mb-3 flex items-center">
                                <div class="bg-white bg-opacity-20 p-3 rounded-xl mr-4">
                                    <i class="fas fa-graduation-cap text-2xl"></i>
                                </div>
                                Exam Pin Purchase
                            </h1>
                            <p class="text-green-100 text-lg mb-4">Purchase exam pins for WAEC, NECO, JAMB, NABTEB and other educational examinations</p>
                            <div class="flex flex-wrap gap-4 text-sm">
                                <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                    <i class="fas fa-certificate text-yellow-300 mr-2"></i>All Exam Boards
                                </div>
                                <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                    <i class="fas fa-shield-alt text-green-300 mr-2"></i>Authentic Pins
                                </div>
                                <div class="flex items-center bg-white bg-opacity-15 rounded-full px-3 py-1">
                                    <i class="fas fa-clock text-blue-300 mr-2"></i>Instant Delivery
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 lg:mt-0">
                            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl px-6 py-4 border border-white border-opacity-30">
                                <div class="text-center">
                                    <div class="text-sm text-green-100 mb-1">Wallet Balance</div>
                                    <div class="flex items-center justify-center space-x-2">
                                        <i class="fas fa-wallet text-yellow-300"></i>
                                        <span class="font-bold text-xl" id="walletBalance">₦{{ number_format(auth()->user()->wallet_balance, 2) }}</span>
                                    </div>
                                    <a href="{{ route('fund-wallet') }}" class="text-xs text-green-200 hover:text-white transition-colors">
                                        <i class="fas fa-plus mr-1"></i>Add Funds
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between relative">
                    <!-- Progress Line -->
                    <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gray-200 transform -translate-y-1/2 z-0"></div>
                    <div id="progressLine" class="absolute top-1/2 left-0 h-0.5 bg-gradient-to-r from-green-500 to-blue-600 transform -translate-y-1/2 z-0 transition-all duration-500" style="width: 25%"></div>

                    <!-- Steps -->
                    <div class="flex justify-between w-full relative z-10">
                        <div class="progress-step active flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 border-green-500 bg-green-500 flex items-center justify-center text-white font-semibold text-sm">1</div>
                            <span class="text-xs font-medium text-gray-600 mt-2">Exam Board</span>
                        </div>
                        <div class="progress-step flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center text-gray-500 font-semibold text-sm">2</div>
                            <span class="text-xs font-medium text-gray-500 mt-2">Quantity</span>
                        </div>
                        <div class="progress-step flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center text-gray-500 font-semibold text-sm">3</div>
                            <span class="text-xs font-medium text-gray-500 mt-2">Phone Number</span>
                        </div>
                        <div class="progress-step flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 border-gray-300 bg-white flex items-center justify-center text-gray-500 font-semibold text-sm">4</div>
                            <span class="text-xs font-medium text-gray-500 mt-2">Purchase</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Enhanced Purchase Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <div class="bg-green-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-graduation-cap text-green-600"></i>
                        </div>
                        Purchase Exam Pins
                    </h2>

                    <form id="examPinForm" class="space-y-6">
                        @csrf

                        <!-- Enhanced Exam Provider Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-school mr-2 text-green-600"></i>
                                Step 1: Select Exam Board
                                <span class="ml-2 text-xs text-gray-500">(Required)</span>
                            </label>

                            @php
                                $examBoardLogos = [
                                    'waec' => 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f4/West_African_Examinations_Council_logo.png/200px-West_African_Examinations_Council_logo.png',
                                    'neco' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/NECO_Logo.png/200px-NECO_Logo.png',
                                    'jamb' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a8/JAMB_logo.png/200px-JAMB_logo.png',
                                    'nabteb' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 40"><rect width="100" height="40" fill="%23059669"/><text x="50" y="25" text-anchor="middle" fill="white" font-size="10">NABTEB</text></svg>',
                                    'gce' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 40"><rect width="100" height="40" fill="%2306b6d4"/><text x="50" y="25" text-anchor="middle" fill="white" font-size="12">GCE</text></svg>'
                                ];
                            @endphp

                            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($examProviders as $provider)
                                    @php
                                        $examKey = strtolower($provider->ePlan);
                                        $examName = strtoupper($provider->ePlan);
                                        $price = $provider->getUserPrice(auth()->user()->sType ?? 'User');
                                    @endphp
                                    <label class="relative cursor-pointer exam-provider-option">
                                        <input type="radio" name="provider" value="{{ $provider->ePlan }}"
                                               data-price="{{ $price }}"
                                               class="sr-only peer" required>
                                        <div class="exam-card group bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 hover:shadow-md peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-lg transition-all duration-300 transform hover:scale-105">
                                            <div class="flex flex-col items-center space-y-3">
                                                <!-- Logo Section -->
                                                <div class="relative">
                                                    <img src="{{ $examBoardLogos[$examKey] ?? $provider->logoPath ?? 'data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 40\"><rect width=\"100\" height=\"40\" fill=\"%23059669\"/><text x=\"50\" y=\"25\" text-anchor=\"middle\" fill=\"white\" font-size=\"10\">' . strtoupper(substr($examName, 0, 6)) . '</text></svg>' }}"
                                                         alt="{{ $examName }}"
                                                         class="h-12 w-auto max-w-20 group-hover:scale-110 transition-transform duration-300"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="hidden flex-col items-center justify-center h-12 w-20 bg-green-100 rounded text-green-600">
                                                        <i class="fas fa-graduation-cap text-xl"></i>
                                                    </div>
                                                </div>

                                                <!-- Exam Info -->
                                                <div>
                                                    <div class="font-semibold text-gray-900 text-sm">{{ $examName }}</div>
                                                    <div class="text-lg font-bold text-green-600 mt-1">₦{{ number_format($price, 2) }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">{{ $provider->description ?? 'Exam Pin' }}</div>
                                                </div>

                                                <!-- Selection Indicator -->
                                                <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity duration-300">
                                                    <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-check text-white text-xs"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div class="text-red-500 text-sm mt-2 hidden" id="provider-error">Please select an exam board.</div>
                        </div>

                        <!-- Enhanced Quantity Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-sort-numeric-up mr-2 text-green-600"></i>
                                Step 2: Select Quantity
                                <span class="ml-2 text-xs text-gray-500">(Required)</span>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <!-- Quick Quantity Options -->
                                <button type="button" class="quantity-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 hover:shadow-md transition-all duration-300" data-quantity="1">
                                    <div class="text-2xl font-bold text-green-600">1</div>
                                    <div class="text-xs text-gray-500">Single Pin</div>
                                </button>
                                <button type="button" class="quantity-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 hover:shadow-md transition-all duration-300" data-quantity="5">
                                    <div class="text-2xl font-bold text-green-600">5</div>
                                    <div class="text-xs text-gray-500">Small Batch</div>
                                </button>
                                <button type="button" class="quantity-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 hover:shadow-md transition-all duration-300" data-quantity="10">
                                    <div class="text-2xl font-bold text-green-600">10</div>
                                    <div class="text-xs text-gray-500">Standard</div>
                                </button>
                                <button type="button" class="quantity-btn bg-white border-2 border-gray-200 rounded-xl p-4 text-center hover:border-green-300 hover:shadow-md transition-all duration-300" data-quantity="20">
                                    <div class="text-2xl font-bold text-green-600">20</div>
                                    <div class="text-xs text-gray-500">Bulk Order</div>
                                </button>
                            </div>
                            <div class="relative">
                                <input type="number" id="quantity" name="quantity" min="1" max="50" value="1" required
                                       class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                                       placeholder="Enter custom quantity (1-50)">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <div class="text-gray-400">
                                        <i class="fas fa-hashtag"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                Maximum 50 pins per transaction
                            </p>
                            <div class="text-red-500 text-sm mt-2 hidden" id="quantity-error">Please enter a valid quantity (1-50).</div>
                        </div>

                        <!-- Enhanced Phone Number Input -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-phone mr-2 text-green-600"></i>
                                Step 3: Enter Phone Number
                                <span class="ml-2 text-xs text-gray-500">(Required for notifications)</span>
                            </label>
                            <div class="relative">
                                <input type="tel" id="phone" name="phone" required
                                       class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 pr-12"
                                       placeholder="Enter your phone number (08012345678)"
                                       pattern="[0-9]{11}" maxlength="11">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <div id="phoneValidationIcon" class="hidden">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2 flex items-center">
                                <i class="fas fa-sms mr-2 text-blue-500"></i>
                                SMS will be sent to this number with exam pin details
                            </p>
                        </div>

                        <!-- Enhanced Transaction PIN -->
                        <div>
                            <label for="transaction_pin" class="block text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-lock mr-2 text-green-600"></i>
                                Step 4: Enter Transaction PIN
                                <span class="ml-2 text-xs text-gray-500">(Required for security)</span>
                            </label>
                            <div class="relative max-w-md">
                                <input type="password"
                                       id="transaction_pin"
                                       name="transaction_pin"
                                       maxlength="4"
                                       pattern="[0-9]{4}"
                                       required
                                       class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 pr-12 text-center text-2xl tracking-widest"
                                       placeholder="••••">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <button type="button" id="togglePin" class="text-gray-400 hover:text-gray-600">
                                        <i id="pinToggleIcon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2 flex items-center">
                                <i class="fas fa-shield-alt mr-2 text-blue-500"></i>
                                Your 4-digit transaction PIN ensures secure purchases
                            </p>
                            <div class="text-red-500 text-sm mt-2 hidden" id="pin-error">Please enter your 4-digit transaction PIN.</div>
                        </div>

                        <!-- Enhanced Purchase Summary -->
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 border border-green-100">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-shopping-cart mr-2 text-green-600"></i>
                                Purchase Summary
                            </h3>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Exam Board:</span>
                                    <span id="summary-provider" class="font-semibold text-gray-800">-</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Quantity:</span>
                                    <span id="summary-quantity" class="font-semibold text-gray-800">1 pin(s)</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Price per pin:</span>
                                    <span id="summary-price" class="font-semibold text-green-600">₦0.00</span>
                                </div>
                                <div class="flex justify-between items-center py-3 bg-white rounded-lg px-4 border-2 border-green-200">
                                    <span class="text-lg font-semibold text-gray-800">Total Amount:</span>
                                    <span id="summary-total" class="text-xl font-bold text-green-600">₦0.00</span>
                                </div>

                                <!-- Wallet Balance Display -->
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 flex items-center">
                                            <i class="fas fa-wallet mr-2 text-blue-500"></i>
                                            Wallet Balance:
                                        </span>
                                        <span class="font-semibold text-blue-600">₦{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}</span>
                                    </div>
                                    <div id="balance-status" class="text-sm mt-2 hidden">
                                        <span id="balance-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Submit Button -->
                        <div class="pt-6">
                            <button type="submit"
                                    id="purchaseBtn"
                                    class="w-full bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg"
                                    disabled>
                                <span id="btn-text">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Purchase Exam Pins
                                </span>
                                <span id="btn-loading" class="hidden">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Processing...
                                </span>
                            </button>

                            <p class="text-center text-sm text-gray-600 mt-4">
                                <i class="fas fa-lock mr-1"></i>
                                Secure transaction protected by encryption
                            </p>
                        </div>
                            <div id="purchase-summary" class="hidden">
                                <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-xl p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                        <i class="fas fa-receipt text-green-600"></i>
                                        Purchase Summary
                                    </h3>
                                    <div class="grid md:grid-cols-4 gap-4">
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-800" id="summary-provider">-</div>
                                            <div class="text-sm text-gray-600">Exam Provider</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-800" id="summary-quantity">-</div>
                                            <div class="text-sm text-gray-600">Quantity</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-800" id="summary-phone">-</div>
                                            <div class="text-sm text-gray-600">Phone Number</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-green-600" id="summary-amount">₦0</div>
                                            <div class="text-sm text-gray-600">Total Amount</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="purchaseBtn" class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white font-bold py-4 px-6 rounded-xl hover:from-green-700 hover:to-blue-700 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" disabled>
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Purchase Exam Pins
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Service Features -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            Service Features
                        </h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700">Instant exam pin generation</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700">All major exam boards supported</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700">Bulk purchase available (up to 50)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700">SMS delivery to phone number</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700">Valid exam pin guarantee</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fas fa-check text-green-500"></i>
                                <span class="text-gray-700">24/7 availability</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Exam Providers Info -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-graduation-cap"></i>
                            Supported Exam Boards
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($examProviders as $provider)
                            <div class="text-center p-4 bg-gray-50 rounded-xl">
                                <img src="{{ $provider->logoPath }}" alt="{{ $provider->ePlan }}"
                                     class="h-8 mx-auto object-contain mb-2"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="hidden">
                                    <i class="fas fa-graduation-cap text-2xl text-blue-600 mb-2"></i>
                                </div>
                                <div class="text-sm font-bold text-gray-800">{{ strtoupper($provider->ePlan) }}</div>
                                <div class="text-sm text-green-600 font-semibold">₦{{ number_format($provider->getUserPrice(auth()->user()->sType), 2) }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            Important Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-arrow-right text-orange-500 mt-0.5 flex-shrink-0"></i>
                                <span class="text-gray-700">Exam pins are delivered via SMS to your phone number</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-arrow-right text-orange-500 mt-0.5 flex-shrink-0"></i>
                                <span class="text-gray-700">Please ensure your phone number is correct before purchase</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-arrow-right text-orange-500 mt-0.5 flex-shrink-0"></i>
                                <span class="text-gray-700">Pins are valid for the current examination session</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-arrow-right text-orange-500 mt-0.5 flex-shrink-0"></i>
                                <span class="text-gray-700">Contact support if you don't receive your pins within 5 minutes</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-history"></i>
                            Recent Purchases
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center py-8">
                            <i class="fas fa-graduation-cap text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">Your recent exam pin purchases will appear here</p>
                            <a href="{{ route('exam-pins.history') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                View All Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-6 text-center min-w-[300px]">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>
        <div id="loadingText" class="text-gray-700">Processing...</div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl overflow-hidden max-w-md mx-4">
        <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-6">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                Purchase Successful
            </h3>
        </div>
        <div class="p-6">
            <div id="successMessage"></div>
        </div>
        <div class="px-6 pb-6">
            <button type="button" onclick="closeModal('successModal')"
                    class="w-full bg-green-600 text-white py-3 px-4 rounded-xl font-semibold hover:bg-green-700 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl overflow-hidden max-w-md mx-4">
        <div class="bg-gradient-to-r from-red-500 to-pink-500 p-6">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                Purchase Failed
            </h3>
        </div>
        <div class="p-6">
            <div id="errorMessage"></div>
        </div>
        <div class="px-6 pb-6">
            <button type="button" onclick="closeModal('errorModal')"
                    class="w-full bg-red-600 text-white py-3 px-4 rounded-xl font-semibold hover:bg-red-700 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let selectedProvider = null;
    let currentProviders = [];

    // Initialize with exam providers
    @if(isset($examProviders))
    currentProviders = {!! json_encode($examProviders) !!};
    @endif

    // Provider selection handler
    $('.provider-card').click(function() {
        const radio = $(this).find('input[type="radio"]');

        // Remove selected state from all cards
        $('.provider-card').removeClass('border-green-500 bg-green-50 shadow-lg selected');
        $('.provider-card .selection-indicator').removeClass('bg-green-500').addClass('bg-gray-200');

        // Add selected state to current card
        radio.prop('checked', true);
        $(this).addClass('border-green-500 bg-green-50 shadow-lg selected');
        $(this).find('.selection-indicator').removeClass('bg-gray-200').addClass('bg-green-500');

        selectedProvider = radio.val();
        updateProgress('provider');
        hideError('provider-error');
        updateForm();
        updateSummary();
    });

    // Quick quantity buttons
    $('.quantity-btn').click(function() {
        const quantity = $(this).data('quantity');
        $('#quantity').val(quantity);

        // Update button states
        $('.quantity-btn').removeClass('border-green-500 bg-green-50 shadow-lg');
        $(this).addClass('border-green-500 bg-green-50 shadow-lg');

        updateProgress('quantity');
        hideError('quantity-error');
        updateForm();
        updateSummary();
    });

    // Quantity input handler
    $('#quantity').on('input', function() {
        let quantity = parseInt($(this).val()) || 1;

        if (quantity < 1) {
            quantity = 1;
            showError('quantity-error', 'Minimum quantity is 1 pin.');
        } else if (quantity > 50) {
            quantity = 50;
            showError('quantity-error', 'Maximum quantity is 50 pins per transaction.');
        } else {
            hideError('quantity-error');
            updateProgress('quantity');
        }

        $(this).val(quantity);

        // Update quick button states
        $('.quantity-btn').removeClass('border-green-500 bg-green-50 shadow-lg');
        $(`.quantity-btn[data-quantity="${quantity}"]`).addClass('border-green-500 bg-green-50 shadow-lg');

        updateForm();
        updateSummary();
    });

    // Phone input handler with formatting
    $('#phone').on('input', function() {
        let phone = $(this).val().replace(/\D/g, '');

        if (phone.length > 11) {
            phone = phone.substr(0, 11);
        }

        $(this).val(phone);

        // Validation and icon update
        if (phone.length === 11 && /^[0-9]{11}$/.test(phone)) {
            $('#phoneValidationIcon').removeClass('hidden');
            hideError('phone-error');
            updateProgress('phone');
        } else {
            $('#phoneValidationIcon').addClass('hidden');
            if (phone.length > 0 && phone.length < 11) {
                showError('phone-error', 'Phone number must be 11 digits.');
            } else {
                hideError('phone-error');
            }
        }

        updateForm();
        updateSummary();
    });

    // Transaction PIN handler with toggle visibility
    $('#transaction_pin').on('input', function() {
        let pin = $(this).val().replace(/\D/g, '');

        if (pin.length > 4) {
            pin = pin.substr(0, 4);
        }

        $(this).val(pin);

        if (pin.length === 4) {
            hideError('pin-error');
            updateProgress('pin');
        } else {
            if (pin.length > 0 && pin.length < 4) {
                showError('pin-error', 'Transaction PIN must be 4 digits.');
            } else {
                hideError('pin-error');
            }
        }

        updateForm();
    });

    // PIN visibility toggle
    $('#togglePin').click(function() {
        const pinInput = $('#transaction_pin');
        const icon = $('#pinToggleIcon');

        if (pinInput.attr('type') === 'password') {
            pinInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            pinInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    function updateProgress(step) {
        const steps = ['provider', 'quantity', 'phone', 'pin'];
        const stepIndex = steps.indexOf(step);

        // Update progress indicators
        $('.progress-step').each(function(index) {
            const circle = $(this).find('.step-circle');
            const line = $(this).find('.step-line');

            if (index <= stepIndex) {
                circle.removeClass('bg-gray-300 text-gray-500').addClass('bg-green-500 text-white');
                line.removeClass('bg-gray-300').addClass('bg-green-500');
            }
        });
    }

    function showError(elementId, message) {
        $(`#${elementId}`).removeClass('hidden').text(message);
    }

    function hideError(elementId) {
        $(`#${elementId}`).addClass('hidden');
    }

    function updateForm() {
        const provider = selectedProvider;
        const quantity = parseInt($('#quantity').val()) || 1;
        const phone = $('#phone').val();
        const pin = $('#transaction_pin').val();

        const isValid = provider &&
                       quantity >= 1 && quantity <= 50 &&
                       phone.length === 11 &&
                       pin.length === 4;

        const btn = $('#purchaseBtn');
        if (isValid) {
            btn.prop('disabled', false).removeClass('opacity-50');
        } else {
            btn.prop('disabled', true).addClass('opacity-50');
        }

        // Update wallet balance status
        updateWalletStatus();
    }

    function updateWalletStatus() {
        const quantity = parseInt($('#quantity').val()) || 1;
        const provider = selectedProvider;
        const walletBalance = {{ auth()->user()->wallet_balance ?? 0 }};

        if (provider && currentProviders.length > 0) {
            const providerData = currentProviders.find(p => p.ePlan === provider);
            if (providerData) {
                const totalAmount = providerData.fee * quantity;
                const balanceStatus = $('#balance-status');
                const balanceMessage = $('#balance-message');

                if (walletBalance >= totalAmount) {
                    balanceStatus.removeClass('hidden');
                    balanceMessage.html('<i class="fas fa-check-circle text-green-500 mr-2"></i>Sufficient balance for this transaction');
                    balanceMessage.removeClass('text-red-600').addClass('text-green-600');
                } else {
                    balanceStatus.removeClass('hidden');
                    balanceMessage.html('<i class="fas fa-exclamation-circle text-red-500 mr-2"></i>Insufficient balance. Please fund your wallet.');
                    balanceMessage.removeClass('text-green-600').addClass('text-red-600');
                }
            }
        }
    }

    function updateSummary() {
        const provider = selectedProvider;
        const quantity = parseInt($('#quantity').val()) || 1;

        if (provider && currentProviders.length > 0) {
            const providerData = currentProviders.find(p => p.ePlan === provider);

            if (providerData) {
                const price = providerData.fee || 0;
                const total = price * quantity;

                $('#summary-provider').text(provider.toUpperCase());
                $('#summary-quantity').text(quantity + ' pin(s)');
                $('#summary-price').text('₦' + parseFloat(price).toLocaleString());
                $('#summary-total').text('₦' + parseFloat(total).toLocaleString());
            }
        }
    }

    // Form submission with enhanced feedback
    $('#examPinForm').submit(function(e) {
        e.preventDefault();

        // Final validation
        if (!selectedProvider) {
            showError('provider-error', 'Please select an exam board.');
            return;
        }

        const quantity = parseInt($('#quantity').val());
        if (quantity < 1 || quantity > 50) {
            showError('quantity-error', 'Please enter a valid quantity (1-50).');
            return;
        }

        const phone = $('#phone').val();
        if (phone.length !== 11) {
            showError('phone-error', 'Please enter a valid 11-digit phone number.');
            return;
        }

        const pin = $('#transaction_pin').val();
        if (pin.length !== 4) {
            showError('pin-error', 'Please enter your 4-digit transaction PIN.');
            return;
        }

        // Show loading state
        const btn = $('#purchaseBtn');
        const btnText = $('#btn-text');
        const btnLoading = $('#btn-loading');

        btn.prop('disabled', true);
        btnText.addClass('hidden');
        btnLoading.removeClass('hidden');

        // Submit the form
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Reset loading state
                btn.prop('disabled', false);
                btnText.removeClass('hidden');
                btnLoading.addClass('hidden');

                if (response.status === 'success') {
                    showSuccess('Purchase Successful!', response.data);
                    resetForm();
                } else {
                    showError(response.message || 'Purchase failed. Please try again.');
                }
            },
            error: function(xhr) {
                // Reset loading state
                btn.prop('disabled', false);
                btnText.removeClass('hidden');
                btnLoading.addClass('hidden');

                const response = xhr.responseJSON;
                showError(response?.message || 'An error occurred. Please try again.');
            }
        });
    });

    function resetForm() {
        $('#examPinForm')[0].reset();
        $('.provider-card').removeClass('border-green-500 bg-green-50 shadow-lg selected');
        $('.selection-indicator').removeClass('bg-green-500').addClass('bg-gray-200');
        $('.quantity-btn').removeClass('border-green-500 bg-green-50 shadow-lg');
        $('.progress-step .step-circle').removeClass('bg-green-500 text-white').addClass('bg-gray-300 text-gray-500');
        $('.progress-step .step-line').removeClass('bg-green-500').addClass('bg-gray-300');
        $('#phoneValidationIcon').addClass('hidden');
        $('#balance-status').addClass('hidden');
        selectedProvider = null;
        updateForm();
    }

    function showSuccess(title, data) {
        let html = `<div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-check text-green-500 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800">${title}</h3>
        </div>`;

        if (data) {
            html += `
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div><span class="font-medium">Reference:</span> ${data.reference || 'N/A'}</div>
                        <div><span class="font-medium">Provider:</span> ${data.provider || 'N/A'}</div>
                        <div><span class="font-medium">Quantity:</span> ${data.quantity || 0} pins</div>
                        <div><span class="font-medium">Amount:</span> ₦${data.amount || 0}</div>
                        <div><span class="font-medium">Phone:</span> ${data.phone || 'N/A'}</div>
                        <div><span class="font-medium">New Balance:</span> ₦${data.balance || 0}</div>
                    </div>
                </div>
            `;

            if (data.pins && data.pins.length > 0) {
                html += `
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Generated Exam Pins:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                `;
                data.pins.forEach(function(pin) {
                    html += `<div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                        <span class="font-mono font-bold text-green-800">${pin}</span>
                    </div>`;
                });
                html += `
                        </div>
                        <p class="text-sm text-gray-600 mt-3 text-center">
                            <i class="fas fa-sms mr-1"></i>
                            Pins have been sent to your phone via SMS
                        </p>
                    </div>
                `;
            }
        }

        showModal('Success', html, 'success');
    }

    function showError(message) {
        const html = `
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-times text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Transaction Failed</h3>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
                ${message}
            </div>
        `;
        showModal('Error', html, 'error');
    }

    function showModal(title, content, type) {
        const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';
        const borderColor = type === 'success' ? 'border-green-200' : 'border-red-200';

        const modal = `
            <div id="responseModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl max-w-md w-full max-h-90vh overflow-y-auto">
                    <div class="p-6">
                        ${content}
                        <div class="text-center mt-6">
                            <button onclick="closeModal('responseModal')"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        $('#responseModal').remove();
        $('body').append(modal);
    }

    // Initialize
    updateForm();
});

function closeModal(modalId) {
    $('#' + modalId).remove();
}
</script>
    $('#transaction_pin').on('input', function() {
        let pin = $(this).val().replace(/\D/g, '');
        if (pin.length > 4) {
            pin = pin.substr(0, 4);
        }
        $(this).val(pin);
        updateForm();
    });

    // Update form state
    function updateForm() {
        const provider = $('input[name="provider"]:checked').val();
        const quantity = $('#quantity').val();
        const phone = $('#phone').val();
        const pin = $('#transaction_pin').val();

        const isValid = provider && quantity && phone.length === 11 && pin.length === 4;
        $('#purchaseBtn').prop('disabled', !isValid);

        // Update button appearance
        if (isValid) {
            $('#purchaseBtn').removeClass('opacity-50 cursor-not-allowed');
        } else {
            $('#purchaseBtn').addClass('opacity-50 cursor-not-allowed');
        }
    }

    // Update purchase summary
    function updateSummary() {
        const provider = $('input[name="provider"]:checked').val();
        const quantity = parseInt($('#quantity').val()) || 1;
        const phone = $('#phone').val();

        if (provider && phone.length === 11) {
            // Find provider details
            const providerData = currentProviders.find(p => p.ePlan === provider);
            if (providerData) {
                const unitPrice = providerData.ePrice; // Using base price from PHP
                const totalAmount = unitPrice * quantity;

                $('#summary-provider').text(provider.toUpperCase());
                $('#summary-quantity').text(quantity + ' pin' + (quantity > 1 ? 's' : ''));
                $('#summary-phone').text(phone);
                $('#summary-amount').text('₦' + totalAmount.toLocaleString());
                $('#purchase-summary').removeClass('hidden');
            }
        } else {
            $('#purchase-summary').addClass('hidden');
        }
    }

    // Form submission
    $('#examPinForm').submit(function(e) {
        e.preventDefault();

        const formData = {
            _token: '{{ csrf_token() }}',
            provider: $('input[name="provider"]:checked').val(),
            quantity: parseInt($('#quantity').val()),
            phone: $('#phone').val(),
            transaction_pin: $('#transaction_pin').val()
        };

        showLoading('Processing exam pin purchase...');

        $.ajax({
            url: '{{ route("exam-pins.purchase") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                if (response.status === 'success') {
                    showSuccess(response.message, response.data);
                    resetForm();
                } else {
                    showError(response.message || 'Purchase failed. Please try again.');
                }
            },
            error: function(xhr) {
                hideLoading();
                const response = xhr.responseJSON;
                showError(response?.message || 'Purchase failed. Please try again.');
            }
        });
    });

    // Helper functions
    function resetForm() {
        $('#examPinForm')[0].reset();
        $('input[name="provider"]').prop('checked', false);
        $('.provider-card').removeClass('border-green-500 bg-green-50 shadow-md');
        selectedProvider = null;
        $('#purchase-summary').addClass('hidden');
        $('#purchaseBtn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
    }

    function showLoading(text) {
        $('#loadingText').text(text);
        $('#loadingModal').removeClass('hidden');
    }

    function hideLoading() {
        $('#loadingModal').addClass('hidden');
    }

    function showSuccess(message, data) {
        let html = `<div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-4">${message}</div>`;

        if (data) {
            html += `
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div><strong class="text-gray-700">Reference:</strong> <span class="text-gray-600">${data.reference || 'N/A'}</span></div>
                    <div><strong class="text-gray-700">Provider:</strong> <span class="text-gray-600">${data.provider || 'N/A'}</span></div>
                    <div><strong class="text-gray-700">Quantity:</strong> <span class="text-gray-600">${data.quantity || 0} pins</span></div>
                    <div><strong class="text-gray-700">Amount:</strong> <span class="text-gray-600">₦${data.amount || 0}</span></div>
                    <div><strong class="text-gray-700">Phone:</strong> <span class="text-gray-600">${data.phone || 'N/A'}</span></div>
                    <div><strong class="text-gray-700">Balance:</strong> <span class="text-gray-600">₦${data.balance || 0}</span></div>
                </div>
            `;

            if (data.pins && data.pins.length > 0) {
                html += `
                    <div class="mt-4">
                        <strong class="text-gray-700">Generated Pins:</strong>
                        <div class="mt-2 flex flex-wrap gap-2">
                `;
                data.pins.forEach(function(pin) {
                    html += `<span class="bg-green-600 text-white px-3 py-1 rounded-lg text-sm font-mono">${pin}</span>`;
                });
                html += `
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Pins have been sent to your phone number via SMS</p>
                    </div>
                `;
            }
        }

        $('#successMessage').html(html);
        $('#successModal').removeClass('hidden');
    }

    function showError(message) {
        $('#errorMessage').html(`<div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">${message}</div>`);
        $('#errorModal').removeClass('hidden');
    }

    // Initialize
    updateForm();
});

// Modal close function
function closeModal(modalId) {
    $('#' + modalId).addClass('hidden');
}
</script>
@endpush

@push('styles')
<style>
.provider-card {
    transition: all 0.3s ease;
}

.provider-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.provider-card input:checked + .provider-card {
    border-color: #10b981;
    background-color: #ecfdf5;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);
}

@media (max-width: 768px) {
    .provider-card {
        min-height: 100px;
    }
}
</style>
@endpush
