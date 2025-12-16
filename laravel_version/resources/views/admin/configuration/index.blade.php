@extends('layouts.admin')

@section('title', 'System Configuration')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg p-6 mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">System Configuration</h1>
        <p class="text-blue-100">Manage system-wide settings, features, and maintenance options</p>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Feature Toggles Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Features</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $data['feature_stats']['enabled'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">System Health</p>
                    <p class="text-2xl font-bold text-{{ ($data['system_health']['status'] ?? 'unknown') == 'healthy' ? 'green' : 'red' }}-600">
                        {{ ucfirst($data['system_health']['status'] ?? 'Unknown') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Cache Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Cache Status</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $data['cache_status']['size'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Maintenance</p>
                    <p class="text-2xl font-bold text-{{ ($data['maintenance_status']['enabled'] ?? false) ? 'orange' : 'green' }}-600">
                        {{ ($data['maintenance_status']['enabled'] ?? false) ? 'Active' : 'Normal' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Feature Management -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Feature Management</h3>
                <p class="text-sm text-gray-600 mt-1">Control system features and toggles</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">Total Features</p>
                            <p class="text-sm text-gray-600">{{ $data['feature_stats']['total'] ?? 0 }} configured</p>
                        </div>
                        <a href="{{ route('system-configuration.feature-toggles') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition duration-200">
                            Manage Features
                        </a>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Enabled</span>
                            <span class="font-semibold text-green-600">{{ $data['feature_stats']['enabled'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Disabled</span>
                            <span class="font-semibold text-red-600">{{ $data['feature_stats']['disabled'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">System Health</h3>
                <p class="text-sm text-gray-600 mt-1">Monitor system performance and health</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">Overall Status</p>
                            <p class="text-sm text-gray-600">Last checked: {{ now()->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('system-configuration.system-health') }}"
                           class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition duration-200">
                            View Details
                        </a>
                    </div>
                    @if(isset($data['system_health']['metrics']))
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        @foreach($data['system_health']['metrics'] as $metric => $value)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ ucfirst($metric) }}</span>
                            <span class="font-semibold">{{ $value }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cache Management -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Cache Management</h3>
                <p class="text-sm text-gray-600 mt-1">Manage application cache and performance</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">Cache Size</p>
                            <p class="text-sm text-gray-600">{{ $data['cache_status']['size'] ?? 'Unknown' }}</p>
                        </div>
                        <a href="{{ route('system-configuration.cache') }}"
                           class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-700 transition duration-200">
                            Manage Cache
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Maintenance Mode</h3>
                <p class="text-sm text-gray-600 mt-1">Control system maintenance and downtime</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">Current Status</p>
                            <p class="text-sm text-gray-600">
                                {{ ($data['maintenance_status']['enabled'] ?? false) ? 'Maintenance Active' : 'System Online' }}
                            </p>
                        </div>
                        <a href="{{ route('system-configuration.maintenance') }}"
                           class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition duration-200">
                            Manage Mode
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            <p class="text-sm text-gray-600 mt-1">Common system administration tasks</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('system-configuration.backup') }}"
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Backup Management</p>
                        <p class="text-sm text-gray-600">Create and manage backups</p>
                    </div>
                </a>

                <a href="{{ route('system-configuration.environment') }}"
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Environment Config</p>
                        <p class="text-sm text-gray-600">Manage environment settings</p>
                    </div>
                </a>

                <a href="{{ route('system-configuration.api-status') }}"
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="p-2 bg-orange-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">API Status</p>
                        <p class="text-sm text-gray-600">Monitor API health</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
