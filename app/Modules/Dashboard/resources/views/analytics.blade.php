@extends('layouts.backend.master')

@section('title', 'Analytics Dashboard')

@section('content')
    {{-- Page Title --}}
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Analytics Dashboard</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                View detailed analytics and insights
            </p>
        </div>
        <div class="action-buttons flex items-center space-x-2">
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <i class="fas fa-calendar mr-2"></i>Last 30 Days
            </button>
            <button class="px-4 py-2 primary-color text-white rounded-lg hover:shadow-lg transition text-sm font-medium">
                <i class="fas fa-download mr-2"></i>Export Report
            </button>
        </div>
    </div>

    {{-- Simple Content Card --}}
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Example Analytics Page</h3>
        <p class="text-gray-600 mb-4">
            This is an example page showing how easy it is to create new pages using the master layout.
            All the header, sidebar, breadcrumb, and footer are automatically included!
        </p>
        <div class="bg-cyan-50 border-l-4 border-cyan-500 p-4 rounded">
            <p class="text-sm text-cyan-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Note:</strong> Simply extend the master layout and add your content in the @section('content') directive.
            </p>
        </div>
    </div>

    {{-- Grid Example --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center space-x-3 mb-3">
                <div class="primary-color rounded-lg p-3">
                    <i class="fas fa-chart-bar text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Total Views</p>
                    <h4 class="text-xl font-bold text-gray-800">{{ number_format(15234) }}</h4>
                </div>
            </div>
            <p class="text-sm text-gray-600">Analytics data for the current period</p>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center space-x-3 mb-3">
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Active Users</p>
                    <h4 class="text-xl font-bold text-gray-800">{{ number_format(3456) }}</h4>
                </div>
            </div>
            <p class="text-sm text-gray-600">Currently active users on platform</p>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center space-x-3 mb-3">
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Avg. Session</p>
                    <h4 class="text-xl font-bold text-gray-800">4m 32s</h4>
                </div>
            </div>
            <p class="text-sm text-gray-600">Average session duration</p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    console.log('Analytics page loaded successfully');
    // Add your page-specific JavaScript here
</script>
@endpush
