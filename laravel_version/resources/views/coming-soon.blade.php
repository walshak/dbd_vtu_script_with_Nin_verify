@extends('layouts.user-layout')

@php
    $title = $pageTitle ?? 'Coming Soon';
    $description = $pageDescription ?? 'This feature is under development';
    $icon = $pageIcon ?? 'fas fa-cog';
    $color = $pageColor ?? 'blue';
@endphp

@section('page-content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-{{ $color }}-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-full">
                        <i class="{{ $icon }} text-4xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-center mb-2">{{ $title }}</h1>
                <p class="text-{{ $color }}-100 text-lg text-center">{{ $description }}</p>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 opacity-20">
                <i class="{{ $icon }} text-9xl"></i>
            </div>
        </div>
    </div>

    <!-- Coming Soon Content -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
            <div class="mb-6">
                <div class="bg-{{ $color }}-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tools text-{{ $color }}-600 text-3xl"></i>
                </div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">Coming Soon!</h2>
                <p class="text-gray-600 text-lg">We're working hard to bring you this feature.</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">What to expect:</h3>
                <ul class="text-left text-gray-600 space-y-2">
                    @if(isset($features))
                        @foreach($features as $feature)
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                {{ $feature }}
                            </li>
                        @endforeach
                    @else
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            User-friendly interface
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            Fast and secure transactions
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            24/7 customer support
                        </li>
                    @endif
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/dashboard" class="bg-{{ $color }}-600 text-white px-6 py-3 rounded-lg hover:bg-{{ $color }}-700 transition-colors">
                    <i class="fas fa-home mr-2"></i>Back to Dashboard
                </a>
                <a href="mailto:support@dbdconcepts.com" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-envelope mr-2"></i>Contact Support
                </a>
            </div>
        </div>
    </div>

    <!-- Service Status -->
    <div class="max-w-2xl mx-auto mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Development Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Design Phase</span>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Completed</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Development</span>
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">In Progress</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Testing</span>
                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium">Pending</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Launch</span>
                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium">Pending</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
