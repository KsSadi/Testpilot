@extends('layouts.backend.master')

@section('title', 'Settings')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Settings</span>
    </div>
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Application Settings</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Manage your application configuration</p>
        </div>
        <div class="flex items-center space-x-2">
            <form action="{{ route('settings.cache.clear') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-secondary">
                    <i class="fas fa-sync-alt mr-2"></i>Clear Cache
                </button>
            </form>
        </div>
    </div>

    {{-- Settings Content Area (Full Width - Sidebar menu is in main sidebar) --}}
    <div class="w-full">
            {{-- General Settings --}}
            @if($activeTab === 'general')
                @include('Setting::tabs.general')
            @endif

            {{-- SEO Settings --}}
            @if($activeTab === 'seo')
                @include('Setting::tabs.seo')
            @endif

            {{-- Authentication Settings --}}
            @if($activeTab === 'authentication')
                @include('Setting::tabs.authentication')
            @endif

            {{-- Security Settings --}}
            @if($activeTab === 'security')
                @include('Setting::tabs.security')
            @endif

            {{-- Email Settings --}}
            @if($activeTab === 'email')
                @include('Setting::tabs.email')
            @endif

            {{-- Social Media Settings --}}
            @if($activeTab === 'social')
                @include('Setting::tabs.social')
            @endif

            {{-- Notifications Settings --}}
            @if($activeTab === 'notifications')
                @include('Setting::tabs.notifications')
            @endif

            {{-- Backup Settings --}}
            @if($activeTab === 'backup')
                @include('Setting::tabs.backup')
            @endif

            {{-- Developer Options --}}
            @if($activeTab === 'developer')
                @include('Setting::tabs.developer')
            @endif
    </div>
@endsection

@push('styles')
<style>
    /* Settings Navigation Styles */
    .settings-nav-item {
        color: #6b7280;
        position: relative;
        overflow: hidden;
    }
    
    .settings-nav-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #06b6d4, #0891b2);
        transform: scaleY(0);
        transition: transform 0.3s ease;
        border-radius: 0 3px 3px 0;
    }
    
    .settings-nav-item.active {
        color: #06b6d4;
        background: linear-gradient(90deg, rgba(6, 182, 212, 0.1), rgba(6, 182, 212, 0.02));
        font-weight: 600;
    }
    
    .settings-nav-item.active::before {
        transform: scaleY(1);
    }
    
    .settings-nav-item:hover:not(.active) {
        color: #0891b2;
        background: rgba(6, 182, 212, 0.05);
    }
    
    /* Smooth page transition */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .settings-content-area {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    /* Smooth scrollbar for tab navigation */
    .overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f3f4f6;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, #06b6d4, #0891b2);
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(90deg, #0891b2, #06b6d4);
    }
    
    /* Enhanced input fields */
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="url"]:focus,
    input[type="number"]:focus,
    input[type="password"]:focus,
    input[type="date"]:focus,
    input[type="file"]:focus,
    textarea:focus,
    select:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    /* Card hover effects */
    .hover\:shadow-md:hover {
        box-shadow: 0 10px 25px -5px rgba(6, 182, 212, 0.1), 0 8px 10px -6px rgba(6, 182, 212, 0.05);
    }
    
    /* Smooth button hover */
    .btn-primary {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-primary:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-primary:active {
        transform: scale(0.98);
    }
    
    /* Sticky sidebar */
    @media (min-width: 1024px) {
        .sticky {
            position: sticky;
            top: 1.5rem;
        }
    }
</style>
@endpush
