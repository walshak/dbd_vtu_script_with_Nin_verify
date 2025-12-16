@extends('layouts.admin')

@section('title', 'System Monitoring Overview')

@push('styles')
<style>
    .monitoring-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-healthy { background-color: #10b981; }
    .status-warning { background-color: #f59e0b; }
    .status-critical { background-color: #ef4444; }
    .metric-trend-up { color: #10b981; }
    .metric-trend-down { color: #ef4444; }
    .monitoring-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .monitoring-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .security-event {
        border-left: 4px solid #dc2626;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    }
    .performance-metric {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #3b82f6;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">System Monitoring Overview</h1>
            <p class="mt-1 text-sm text-gray-600">Comprehensive real-time system monitoring and analytics</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
            <div class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                <span id="current-time">{{ now()->format('l, F j, Y g:i A') }}</span>
            </div>
        </div>
    </div>

    <!-- System Health Summary -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-heartbeat mr-2 text-red-500"></i>
            System Health Summary
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">
                    {{ count(array_filter($monitoringData['provider_health'] ?? [], function($p) { return $p['status'] === 'healthy'; })) }}
                </div>
                <div class="text-sm text-gray-600">Healthy Providers</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">
                    {{ count(array_filter($monitoringData['provider_health'] ?? [], function($p) { return $p['status'] === 'degraded'; })) }}
                </div>
                <div class="text-sm text-gray-600">Degraded Providers</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">
                    {{ count(array_filter($monitoringData['provider_health'] ?? [], function($p) { return $p['status'] === 'down'; })) }}
                </div>
                <div class="text-sm text-gray-600">Down Providers</div>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">
                    {{ count($monitoringData['system_alerts'] ?? []) }}
                </div>
                <div class="text-sm text-gray-600">Active Alerts</div>
            </div>
        </div>
    </div>

    <!-- Provider Health Details -->
    <div class="monitoring-card p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-server mr-2 text-blue-500"></i>
            Provider Health Status
        </h3>
        <div class="monitoring-grid">
            @if(isset($monitoringData['provider_health']))
                @foreach($monitoringData['provider_health'] as $provider => $health)
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900 flex items-center">
                                <span class="status-indicator status-{{ $health['status'] }} mr-2"></span>
                                {{ ucfirst($provider) }}
                            </h4>
                            <span class="text-xs text-gray-500">{{ $health['last_check'] ?? 'N/A' }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Response Time:</span>
                                <span class="font-medium">{{ $health['response_time'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Success Rate:</span>
                                <span class="font-medium">{{ $health['success_rate'] ?? 'N/A' }}%</span>
                            </div>
                            @if(isset($health['circuit_breaker']))
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Circuit Breaker:</span>
                                <span class="font-medium">{{ $health['circuit_breaker']['state'] ?? 'Unknown' }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-2xl mb-2"></i>
                    <p>No provider health data available</p>
                </div>
            @endif
        </div>
    </div>

    <!-- API Performance Metrics -->
    <div class="monitoring-card p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-tachometer-alt mr-2 text-green-500"></i>
            API Performance Metrics
        </h3>
        @if(isset($monitoringData['api_performance']))
            <div class="monitoring-grid">
                <div class="performance-metric p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">Average Response Time</h4>
                        <i class="fas fa-clock text-blue-500"></i>
                    </div>
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $monitoringData['api_performance']['avg_response_time'] ?? 'N/A' }}ms
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        Last 24 hours
                    </div>
                </div>

                <div class="performance-metric p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">Success Rate</h4>
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ $monitoringData['api_performance']['success_rate'] ?? 'N/A' }}%
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        Last 24 hours
                    </div>
                </div>

                <div class="performance-metric p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">Error Rate</h4>
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                    </div>
                    <div class="text-2xl font-bold text-red-600">
                        {{ $monitoringData['api_performance']['error_rate'] ?? 'N/A' }}%
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        Last 24 hours
                    </div>
                </div>

                <div class="performance-metric p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">Requests Per Minute</h4>
                        <i class="fas fa-chart-line text-purple-500"></i>
                    </div>
                    <div class="text-2xl font-bold text-purple-600">
                        {{ $monitoringData['api_performance']['requests_per_minute'] ?? 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        Current rate
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-info-circle text-2xl mb-2"></i>
                <p>No API performance data available</p>
            </div>
        @endif
    </div>

    <!-- Security Metrics -->
    <div class="monitoring-card p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-shield-alt mr-2 text-red-500"></i>
            Security Metrics & Events
        </h3>
        @if(isset($monitoringData['security_metrics']))
            <div class="space-y-4">
                <!-- Security Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="security-event p-4 rounded-lg">
                        <div class="text-lg font-bold text-red-800">
                            {{ $monitoringData['security_metrics']['failed_logins'] ?? 0 }}
                        </div>
                        <div class="text-sm text-red-700">Failed Logins (24h)</div>
                    </div>
                    <div class="security-event p-4 rounded-lg">
                        <div class="text-lg font-bold text-red-800">
                            {{ $monitoringData['security_metrics']['suspicious_activities'] ?? 0 }}
                        </div>
                        <div class="text-sm text-red-700">Suspicious Activities</div>
                    </div>
                    <div class="security-event p-4 rounded-lg">
                        <div class="text-lg font-bold text-red-800">
                            {{ $monitoringData['security_metrics']['blocked_ips'] ?? 0 }}
                        </div>
                        <div class="text-sm text-red-700">Blocked IPs</div>
                    </div>
                </div>

                <!-- Recent Security Events -->
                @if(isset($monitoringData['security_metrics']['recent_events']) && count($monitoringData['security_metrics']['recent_events']) > 0)
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Recent Security Events</h4>
                        <div class="space-y-2">
                            @foreach($monitoringData['security_metrics']['recent_events'] as $event)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-{{ $event['icon'] ?? 'exclamation-triangle' }} text-red-500 mr-3"></i>
                                        <div>
                                            <div class="font-medium">{{ $event['type'] ?? 'Security Event' }}</div>
                                            <div class="text-sm text-gray-600">{{ $event['description'] ?? 'No description' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $event['timestamp'] ?? 'Unknown time' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-info-circle text-2xl mb-2"></i>
                <p>No security metrics available</p>
            </div>
        @endif
    </div>

    <!-- System Alerts -->
    @if(isset($monitoringData['system_alerts']) && count($monitoringData['system_alerts']) > 0)
        <div class="monitoring-card p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-bell mr-2 text-yellow-500"></i>
                Active System Alerts
            </h3>
            <div class="space-y-3">
                @foreach($monitoringData['system_alerts'] as $alert)
                    <div class="alert-{{ $alert['level'] ?? 'info' }} rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-{{ $alert['level'] === 'critical' ? 'exclamation-triangle' : ($alert['level'] === 'warning' ? 'exclamation-circle' : 'info-circle') }} text-lg"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium">{{ $alert['title'] ?? 'System Alert' }}</h4>
                                    <span class="text-xs opacity-75">{{ $alert['timestamp'] ?? 'Unknown time' }}</span>
                                </div>
                                <p class="text-sm mt-1">{{ $alert['message'] ?? 'No message available' }}</p>
                                @if(isset($alert['action_required']) && $alert['action_required'])
                                    <div class="mt-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                            Action Required
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update current time
    function updateCurrentTime() {
        const now = new Date();
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = now.toLocaleString();
        }
    }

    // Update time every second
    setInterval(updateCurrentTime, 1000);

    // Auto-refresh page every 5 minutes
    setTimeout(() => {
        location.reload();
    }, 300000); // 5 minutes
});
</script>
@endpush
@endsection
