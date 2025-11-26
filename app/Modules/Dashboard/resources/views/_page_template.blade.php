{{--
    PAGE TEMPLATE
    Copy this template to create new pages quickly
    
    Instructions:
    1. Copy this file
    2. Rename it (e.g., users.blade.php)
    3. Update the @section directives
    4. Add your content
    5. Create route in routes/web.php
    6. Add method in DashboardController.php
--}}

@extends('layouts.backend.master')

{{-- Page Title (shows in browser tab) --}}
@section('title', 'Your Page Title')

{{-- Main Content --}}
@section('content')
    {{-- Page Header with Title and Actions --}}
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Your Page Title</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Your page description goes here</p>
        </div>
        <div class="action-buttons flex items-center space-x-2">
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <button class="px-4 py-2 primary-color text-white rounded-lg hover:shadow-lg transition text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Add New
            </button>
        </div>
    </div>

    {{-- Example: Stats Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between mb-3">
                <div class="primary-color rounded-lg p-2.5">
                    <i class="fas fa-chart-line text-white text-lg"></i>
                </div>
                <span class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded font-semibold">
                    <i class="fas fa-arrow-up"></i> 12%
                </span>
            </div>
            <p class="text-gray-500 text-xs font-medium mb-1">Metric Name</p>
            <h3 class="text-2xl font-bold text-gray-800">1,234</h3>
        </div>
        
        {{-- Add more stat cards as needed --}}
    </div>

    {{-- Example: Content Card --}}
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Section Title</h3>
        <p class="text-gray-600 mb-4">Your content goes here...</p>
        
        {{-- Example: Alert Box --}}
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Info:</strong> This is an information message.
            </p>
        </div>
    </div>

    {{-- Example: Data Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base md:text-lg font-bold text-gray-800">Data Table</h3>
                    <p class="text-gray-500 text-xs mt-0.5">List of items</p>
                </div>
                <button class="text-cyan-600 hover:text-cyan-700 font-medium text-sm">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Column 1</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Column 2</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Column 3</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-3 text-sm text-gray-800">Data 1</td>
                        <td class="py-3 px-3 text-sm text-gray-600">Data 2</td>
                        <td class="py-3 px-3 text-sm text-gray-600">Data 3</td>
                        <td class="py-3 px-3 text-center">
                            <button class="text-cyan-600 hover:text-cyan-700 text-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    {{-- Add more rows --}}
                </tbody>
            </table>
        </div>
    </div>
@endsection

{{-- Page-specific Styles (Optional) --}}
@push('styles')
<style>
    /* Add custom CSS here if needed */
</style>
@endpush

{{-- Page-specific Scripts (Optional) --}}
@push('scripts')
<script>
    // Add custom JavaScript here
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded');
        
        // Your code here
    });
</script>
@endpush

{{--
    COMMON COMPONENTS YOU CAN USE:

    1. Alert Boxes:
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <p class="text-sm text-green-800"><i class="fas fa-check-circle mr-2"></i>Success message</p>
    </div>

    2. Buttons:
    <button class="btn-primary">Primary Button</button>
    <button class="btn-secondary">Secondary Button</button>
    <button class="px-4 py-2 primary-color text-white rounded-lg">Custom Button</button>

    3. Badges:
    <span class="badge-primary">Primary</span>
    <span class="badge-success">Success</span>
    <span class="badge-warning">Warning</span>
    <span class="badge-danger">Danger</span>

    4. Grid Layouts:
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Your cards here -->
    </div>

    5. Cards:
    <div class="card card-hover">
        <!-- Card content -->
    </div>

    For more components and examples, check:
    - index.blade.php (Dashboard page with many examples)
    - analytics.blade.php (Simple page example)
    - README.md (Full documentation)
--}}
