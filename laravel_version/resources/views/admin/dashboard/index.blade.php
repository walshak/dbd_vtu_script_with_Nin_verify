@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .monitoring-panel {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .provider-status {
        transition: all 0.3s ease;
    }
    .provider-status.healthy {
        border-left: 4px solid #10b981;
    }
    .provider-status.degraded {
        border-left: 4px solid #f59e0b;
    }
    .provider-status.down {
        border-left: 4px solid #ef4444;
    }
    .metric-card {
        transition: transform 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
    }
    .alert-critical {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 4px solid #dc2626;
    }
    .alert-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
        border-left: 4px solid #d97706;
    }
    .alert-info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left: 4px solid #2563eb;
    }
</style>
@endpush

@section('content')
<div class="space-y-6" id="dashboard-container">
    <!-- Dashboard Header with Real-time Status -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Overview</h1>
            <p class="mt-1 text-sm text-gray-600">Welcome back, {{ Auth::guard('admin')->user()->sysName ?? 'Admin' }}!</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                <span id="current-time">{{ now()->format('l, F j, Y g:i A') }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <div id="connection-status" class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-gray-700">Live</span>
            </div>
        </div>
    </div>

    <!-- Real-time Monitoring Panel -->
    <div class="monitoring-panel rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-chart-line mr-2"></i>
                Real-time System Monitoring
            </h2>
            <div class="text-sm text-white opacity-75">
                Last updated: <span id="last-updated">{{ now()->format('H:i:s') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4" id="realtime-metrics">
            <!-- Real-time metrics will be populated here -->
        </div>
    </div>

    <!-- System Alerts -->
    <div id="system-alerts" class="hidden">
        <!-- Alerts will be dynamically populated -->
    </div>

    <!-- Provider Health Status -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-heartbeat mr-2 text-red-500"></i>
                Provider Health Status
            </h3>
            <div id="provider-health-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Provider health cards will be populated here -->
            </div>
        </div>
    </div>

    <!-- API Performance Metrics -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-tachometer-alt mr-2 text-blue-500"></i>
                API Performance Metrics
            </h3>
            <div id="api-performance-chart" class="mt-4">
                <!-- Performance chart will be rendered here -->
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-2xl text-blue-500"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                        <dd class="stat-value">{{ number_format($stats['total_users'] ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-check text-2xl text-green-500"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                        <dd class="stat-value text-green-600">{{ number_format($stats['active_users'] ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exchange-alt text-2xl text-purple-500"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Transactions</dt>
                        <dd class="stat-value text-purple-600">{{ number_format($stats['total_transactions'] ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-money-bill-wave text-2xl text-green-500"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Today's Revenue</dt>
                        <dd class="stat-value text-green-600">₦{{ number_format($stats['today_revenue'] ?? 0, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-2xl text-indigo-500"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Month Revenue</dt>
                        <dd class="stat-value text-indigo-600">₦{{ number_format($stats['month_revenue'] ?? 0, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-2xl text-yellow-500"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Transactions</dt>
                        <dd class="stat-value text-yellow-600">{{ number_format($stats['pending_transactions'] ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl text-green-500"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Successful Transactions</dt>
                        <dd class="stat-value text-green-600">{{ number_format($stats['successful_transactions'] ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-wallet text-2xl text-blue-500"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Wallet Balance</dt>
                        <dd class="stat-value text-blue-600">₦{{ number_format($stats['total_wallet_balance'] ?? 0, 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                <i class="fas fa-history mr-2"></i>Recent Transactions
            </h3>
            @if(isset($dashboardData['recent_transactions']) && count($dashboardData['recent_transactions']) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($dashboardData['recent_transactions'] as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $transaction['reference'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction['user'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction['type'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₦{{ number_format($transaction['amount'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $transaction['status'] === 'Completed' ? 'bg-green-100 text-green-800' :
                                               ($transaction['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                                'bg-red-100 text-red-800') }}">
                                            {{ $transaction['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction['date'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No recent transactions found.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Top Services -->
    @if(isset($dashboardData['top_services']) && count($dashboardData['top_services']) > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                <i class="fas fa-chart-bar mr-2"></i>Top Services (Last 30 Days)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($dashboardData['top_services'] as $service)
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">{{ $service['type'] }}</h4>
                        <div class="mt-2 space-y-1">
                            <p class="text-sm text-gray-600">Transactions: {{ number_format($service['count']) }}</p>
                            <p class="text-sm text-gray-600">Revenue: ₦{{ number_format($service['revenue'], 2) }}</p>
                            <p class="text-sm text-gray-600">Average: ₦{{ number_format($service['average'], 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let charts = {};
    let refreshInterval = 30000; // 30 seconds

    // Initialize dashboard
    initializeDashboard();

    // Set up real-time updates
    setInterval(updateRealTimeData, refreshInterval);

    function initializeDashboard() {
        updateRealTimeData();
        updateProviderHealth();
        updateApiPerformance();
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);
    }

    function updateCurrentTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleString();
    }

    function updateRealTimeData() {
        fetch('/admin/dashboard/realtime')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateRealTimeMetrics(data.data.metrics);
                    updateSystemAlerts(data.data.alerts);
                    updateConnectionStatus(true);
                    document.getElementById('last-updated').textContent = data.data.timestamp;
                } else {
                    updateConnectionStatus(false);
                }
            })
            .catch(error => {
                console.error('Error fetching real-time data:', error);
                updateConnectionStatus(false);
            });
    }

    function updateRealTimeMetrics(metrics) {
        const container = document.getElementById('realtime-metrics');
        if (!container) return;

        container.innerHTML = `
            <div class="text-center">
                <div class="text-2xl font-bold text-white">${metrics.current_users || 0}</div>
                <div class="text-sm text-white opacity-75">Active Users</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">${metrics.transactions_per_minute || 0}</div>
                <div class="text-sm text-white opacity-75">Trans/Min</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">${metrics.success_rate || '0'}%</div>
                <div class="text-sm text-white opacity-75">Success Rate</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">${metrics.avg_response_time || '0'}ms</div>
                <div class="text-sm text-white opacity-75">Avg Response</div>
            </div>
        `;
    }

    function updateSystemAlerts(alerts) {
        const container = document.getElementById('system-alerts');
        if (!container || !alerts || alerts.length === 0) {
            container.classList.add('hidden');
            return;
        }

        container.classList.remove('hidden');
        container.innerHTML = alerts.map(alert => `
            <div class="alert-${alert.level} rounded-lg p-4 mb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-${getAlertIcon(alert.level)} text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium">${alert.title}</h4>
                        <p class="text-sm mt-1">${alert.message}</p>
                        <p class="text-xs mt-1 opacity-75">${alert.timestamp}</p>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function getAlertIcon(level) {
        switch(level) {
            case 'critical': return 'exclamation-triangle';
            case 'warning': return 'exclamation-circle';
            case 'info': return 'info-circle';
            default: return 'bell';
        }
    }

    function updateProviderHealth() {
        fetch('/admin/dashboard/provider-health')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderProviderHealthCards(data.data);
                }
            })
            .catch(error => {
                console.error('Error fetching provider health:', error);
            });
    }

    function renderProviderHealthCards(healthData) {
        const container = document.getElementById('provider-health-grid');
        if (!container || !healthData) return;

        container.innerHTML = Object.entries(healthData).map(([provider, status]) => `
            <div class="provider-status ${status.status} bg-white border rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <h4 class="font-medium text-gray-900">${provider.charAt(0).toUpperCase() + provider.slice(1)}</h4>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusClass(status.status)}">
                        ${status.status}
                    </span>
                </div>
                <div class="mt-2 space-y-1">
                    <p class="text-sm text-gray-600">Response Time: ${status.response_time || 'N/A'}</p>
                    <p class="text-sm text-gray-600">Success Rate: ${status.success_rate || 'N/A'}%</p>
                    <p class="text-sm text-gray-600">Last Check: ${status.last_check || 'N/A'}</p>
                </div>
                ${status.circuit_breaker ? `
                    <div class="mt-2 text-xs text-gray-500">
                        Circuit Breaker: ${status.circuit_breaker.state}
                    </div>
                ` : ''}
            </div>
        `).join('');
    }

    function getStatusClass(status) {
        switch(status) {
            case 'healthy': return 'bg-green-100 text-green-800';
            case 'degraded': return 'bg-yellow-100 text-yellow-800';
            case 'down': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    function updateApiPerformance() {
        fetch('/admin/dashboard/api-performance')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderPerformanceChart(data.data);
                }
            })
            .catch(error => {
                console.error('Error fetching API performance:', error);
            });
    }

    function renderPerformanceChart(performanceData) {
        const container = document.getElementById('api-performance-chart');
        if (!container || !performanceData) return;

        // Create canvas for chart
        container.innerHTML = '<canvas id="performance-chart" width="400" height="200"></canvas>';
        const ctx = document.getElementById('performance-chart').getContext('2d');

        // Destroy existing chart if it exists
        if (charts.performance) {
            charts.performance.destroy();
        }

        charts.performance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: performanceData.timeline || [],
                datasets: [{
                    label: 'Response Time (ms)',
                    data: performanceData.response_times || [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Success Rate (%)',
                    data: performanceData.success_rates || [],
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Response Time (ms)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Success Rate (%)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    }

    function updateConnectionStatus(connected) {
        const statusElement = document.getElementById('connection-status');
        if (connected) {
            statusElement.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse';
        } else {
            statusElement.className = 'w-3 h-3 bg-red-500 rounded-full';
        }
    }
});
</script>
@endpush
@endsection
