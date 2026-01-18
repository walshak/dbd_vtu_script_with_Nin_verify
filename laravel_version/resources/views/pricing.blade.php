@extends('layouts.user-layout')

@php
    $title = 'Service Pricing';
@endphp

@section('page-content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Service Pricing</h1>
        <p class="text-gray-600 dark:text-gray-400">View current rates for all our services</p>
    </div>

    <!-- Pricing Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-4 px-6" aria-label="Tabs">
                <button type="button" data-tab="data" class="pricing-tab active border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                    <i class="fas fa-wifi mr-2"></i>Data Plans
                </button>
                <button type="button" data-tab="airtime" class="pricing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-phone mr-2"></i>Airtime
                </button>
                <button type="button" data-tab="cable" class="pricing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-tv mr-2"></i>Cable TV
                </button>
                <button type="button" data-tab="electricity" class="pricing-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-bolt mr-2"></i>Electricity
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Data Plans Tab -->
            <div id="data-tab-content" class="tab-content">
                @if($dataPlans->isEmpty())
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">No data plans available at the moment</p>
                    </div>
                @else
                    @foreach($dataPlans as $networkName => $plans)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <span class="inline-block w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 text-center leading-8 mr-2">
                                    <i class="fas fa-signal text-sm"></i>
                                </span>
                                {{ $networkName }}
                            </h3>

                            <!-- Group plans by dGroup (SME, Gifting, Corporate) -->
                            @php
                                $groupedPlans = $plans->groupBy('dGroup');
                            @endphp

                            @foreach($groupedPlans as $groupName => $groupPlans)
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">
                                        {{ $groupName }}
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                        @foreach($groupPlans as $plan)
                                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                                <div class="flex justify-between items-start mb-2">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $plan->dAmount }}
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                        {{ $plan->dValidity }}
                                                    </span>
                                                </div>
                                                <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                                    ₦{{ number_format($plan->selling_price ?? $plan->userPrice, 2) }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ Str::limit($plan->dPlan, 30) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if(!$loop->last)
                            <hr class="border-gray-200 dark:border-gray-700 my-6">
                        @endif
                    @endforeach
                @endif
            </div>

            <!-- Airtime Tab -->
            <div id="airtime-tab-content" class="tab-content hidden">
                <div class="max-w-3xl">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Airtime Pricing</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                                    <p>Enjoy instant airtime top-up at discounted rates. The more you buy, the more you save!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($networks->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">No networks available at the moment</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($networks as $network)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="inline-block w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white text-center leading-12 mr-4">
                                                <i class="fas fa-phone text-lg"></i>
                                            </span>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $network->network }}</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Instant top-up</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">2-5% OFF</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Discount rate</div>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>Instant delivery
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>Available 24/7
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cable TV Tab -->
            <div id="cable-tab-content" class="tab-content hidden">
                @if($cablePlans->isEmpty())
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">No cable TV plans available at the moment</p>
                    </div>
                @else
                    @foreach($cablePlans as $providerName => $plans)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <span class="inline-block w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 text-center leading-8 mr-2">
                                    <i class="fas fa-tv text-sm"></i>
                                </span>
                                {{ $providerName }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($plans as $plan)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                                    {{ $plan->name }}
                                                </h4>
                                                @if($plan->type)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                        {{ $plan->type }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-end justify-between">
                                            <div>
                                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                                    ₦{{ number_format($plan->selling_price ?? $plan->userprice, 2) }}
                                                </div>
                                                @if($plan->day)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ $plan->day }} {{ Str::plural('day', $plan->day) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="border-gray-200 dark:border-gray-700 my-6">
                        @endif
                    @endforeach
                @endif
            </div>

            <!-- Electricity Tab -->
            <div id="electricity-tab-content" class="tab-content hidden">
                <div class="max-w-3xl">
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-bolt text-yellow-600 dark:text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Electricity Bill Payment</h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                                    <p>Pay your electricity bills instantly with no additional charges. We support all major electricity distribution companies in Nigeria.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $providers = [
                                ['name' => 'Ikeja Electric (IKEDC)', 'icon' => 'fa-building'],
                                ['name' => 'Eko Electric (EKEDC)', 'icon' => 'fa-city'],
                                ['name' => 'Ibadan Electric (IBEDC)', 'icon' => 'fa-home'],
                                ['name' => 'Enugu Electric (EEDC)', 'icon' => 'fa-building'],
                                ['name' => 'Port Harcourt Electric (PHEDC)', 'icon' => 'fa-building'],
                                ['name' => 'Abuja Electric (AEDC)', 'icon' => 'fa-city'],
                                ['name' => 'Kano Electric (KEDCO)', 'icon' => 'fa-building'],
                                ['name' => 'Jos Electric (JEDC)', 'icon' => 'fa-home'],
                            ];
                        @endphp

                        @foreach($providers as $provider)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="inline-block w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 text-center leading-10 mr-3">
                                            <i class="fas {{ $provider['icon'] }}"></i>
                                        </span>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $provider['name'] }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">No extra charges</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600 dark:text-green-400">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Features:</h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                <span>Instant token delivery</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                <span>Pay what you see - no hidden charges</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                <span>24/7 availability</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                <span>Meter verification before payment</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Important Information</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                    <ul class="list-disc list-inside space-y-1">
                        <li>All prices shown are current selling prices</li>
                        <li>Prices may be subject to change based on market conditions</li>
                        <li>Transactions are processed instantly upon successful payment</li>
                        <li>For bulk purchases or special rates, please contact support</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabs = document.querySelectorAll('.pricing-tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // Update active tab styling
            tabs.forEach(t => {
                t.classList.remove('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                t.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            this.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            this.classList.add('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');

            // Show corresponding content
            contents.forEach(content => {
                content.classList.add('hidden');
            });

            document.getElementById(targetTab + '-tab-content').classList.remove('hidden');
        });
    });
});
</script>
@endpush
@endsection
