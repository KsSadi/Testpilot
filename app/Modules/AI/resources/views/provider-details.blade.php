@extends('layouts.backend.master')

@section('title', $provider->display_name . ' - Analytics')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">{{ $provider->display_name }}</h1>
                @if($provider->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Active Provider
                    </span>
                @endif
                @if(!$provider->is_enabled)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-1"></i> Disabled
                    </span>
                @endif
            </div>
            <p class="text-gray-500 text-xs md:text-sm">{{ $provider->description }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('ai.settings') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to AI Settings
            </a>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Requests</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_requests']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-green-600">{{ number_format($stats['today_requests']) }}</span> today
                    </p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-paper-plane text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Success Rate</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['success_rate'] }}%</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ number_format($stats['successful_requests']) }} / {{ number_format($stats['total_requests']) }}
                    </p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Cost</p>
                    <p class="text-3xl font-bold text-purple-600">${{ number_format($stats['total_cost'], 4) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-purple-600">${{ number_format($stats['today_cost'], 4) }}</span> today
                    </p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-dollar-sign text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Avg Response Time</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['avg_response_time']) }}ms</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ number_format($stats['total_tokens']) }} tokens
                    </p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-clock text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Usage Timeline Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-line text-cyan-600 mr-2"></i>
                Daily Usage (Last 30 Days)
            </h3>
            <div style="height: 300px;">
                <canvas id="usageChart"></canvas>
            </div>
        </div>

        <!-- Feature Breakdown -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
                Usage by Feature
            </h3>
            @if($featureBreakdown->count() > 0)
                <div class="space-y-3">
                    @foreach($featureBreakdown as $feature)
                        @php
                            $percentage = $stats['total_requests'] > 0 ? ($feature->count / $stats['total_requests']) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($feature->feature) }}</span>
                                <span class="text-sm text-gray-600">{{ number_format($feature->count) }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-cyan-500 to-purple-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Cost: ${{ number_format($feature->cost, 4) }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>No usage data yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Provider Configuration -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-cog text-gray-600 mr-2"></i>
                Configuration
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">Default Model</dt>
                    <dd class="text-sm text-gray-900">{{ $provider->models[$provider->default_model] ?? $provider->default_model }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">API Endpoint</dt>
                    <dd class="text-sm text-gray-900 font-mono truncate">{{ $provider->api_base_url }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">API Keys</dt>
                    <dd class="text-sm text-gray-900">
                        @if(is_array($provider->api_keys) && count($provider->api_keys) > 0)
                            <span class="text-green-600"><i class="fas fa-key mr-1"></i>{{ count($provider->api_keys) }} keys (Failover enabled)</span>
                        @elseif($provider->hasValidApiKey())
                            <span class="text-green-600"><i class="fas fa-key mr-1"></i>1 key configured</span>
                        @else
                            <span class="text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>Not configured</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">Priority</dt>
                    <dd class="text-sm text-gray-900">{{ $provider->priority }}</dd>
                </div>
                @if(is_array($provider->api_keys) && count($provider->api_keys) > 0)
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">Current Key Index</dt>
                    <dd class="text-sm text-gray-900">
                        Key #{{ ($provider->current_key_index % count($provider->api_keys)) + 1 }} of {{ count($provider->api_keys) }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Monthly Stats -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-calendar-alt text-cyan-600 mr-2"></i>
                This Month
            </h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">Requests</dt>
                    <dd class="text-sm text-gray-900 font-semibold">{{ number_format($stats['month_requests']) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">Cost</dt>
                    <dd class="text-sm text-gray-900 font-semibold">${{ number_format($stats['month_cost'], 4) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">Failed Requests</dt>
                    <dd class="text-sm text-gray-900 font-semibold">{{ number_format($stats['failed_requests']) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">Avg Cost/Request</dt>
                    <dd class="text-sm text-gray-900 font-semibold">
                        ${{ $stats['total_requests'] > 0 ? number_format($stats['total_cost'] / $stats['total_requests'], 6) : '0.00' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-history text-blue-600 mr-2"></i>
            Recent Activity
        </h3>
        @if($recentLogs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feature</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tokens</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->created_at->format('M d, H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($log->feature) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status === 'success')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Success
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Error
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($log->total_tokens) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($log->response_time) }}ms</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($log->cost, 6) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-inbox text-5xl mb-4"></i>
                <p class="text-lg">No activity yet</p>
                <p class="text-sm">Start using this provider to see usage logs</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Usage Timeline Chart
    const dailyUsage = @json($dailyUsage);
    
    const dates = dailyUsage.map(d => d.date);
    const requests = dailyUsage.map(d => d.requests);
    const costs = dailyUsage.map(d => d.cost);
    
    const ctx = document.getElementById('usageChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Requests',
                data: requests,
                borderColor: 'rgb(14, 165, 233)',
                backgroundColor: 'rgba(14, 165, 233, 0.1)',
                yAxisID: 'y',
                tension: 0.3
            }, {
                label: 'Cost ($)',
                data: costs,
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                yAxisID: 'y1',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Requests'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Cost ($)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
});
</script>
@endpush
