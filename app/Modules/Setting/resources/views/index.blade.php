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

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>Please fix the errors below</span>
        </div>
    @endif

    {{-- Settings Layout with Sidebar --}}
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Settings Sidebar Navigation --}}
        <div class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden sticky top-6">
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-blue-50">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-sliders-h mr-2 text-cyan-600"></i>
                        Settings Menu
                    </h3>
                </div>
                <nav class="p-3">
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('settings.index', ['tab' => 'general']) }}" 
                               class="settings-nav-item {{ $activeTab === 'general' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 group">
                                <i class="fas fa-cog mr-3 text-base w-5 transition-transform group-hover:rotate-90 duration-500"></i>
                                <span>General Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index', ['tab' => 'seo']) }}" 
                               class="settings-nav-item {{ $activeTab === 'seo' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 group">
                                <i class="fas fa-search mr-3 text-base w-5 transition-transform group-hover:scale-110 duration-300"></i>
                                <span>SEO Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index', ['tab' => 'authentication']) }}" 
                               class="settings-nav-item {{ $activeTab === 'authentication' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 group">
                                <i class="fas fa-user-lock mr-3 text-base w-5 transition-transform group-hover:scale-110 duration-300"></i>
                                <span>Authentication</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index', ['tab' => 'security']) }}" 
                               class="settings-nav-item {{ $activeTab === 'security' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 group">
                                <i class="fas fa-shield-alt mr-3 text-base w-5 transition-transform group-hover:scale-110 duration-300"></i>
                                <span>Security</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index', ['tab' => 'email']) }}" 
                               class="settings-nav-item {{ $activeTab === 'email' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 group">
                                <i class="fas fa-envelope mr-3 text-base w-5 transition-transform group-hover:scale-110 duration-300"></i>
                                <span>Email Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index', ['tab' => 'social']) }}" 
                               class="settings-nav-item {{ $activeTab === 'social' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 group">
                                <i class="fas fa-share-alt mr-3 text-base w-5 transition-transform group-hover:rotate-12 duration-300"></i>
                                <span>Social Media</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index', ['tab' => 'notifications']) }}" 
                               class="settings-nav-item {{ $activeTab === 'notifications' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 group">
                                <i class="fas fa-bell mr-3 text-base w-5 group-hover:animate-pulse"></i>
                                <span>Notifications</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index', ['tab' => 'backup']) }}" 
                               class="settings-nav-item {{ $activeTab === 'backup' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 group">
                                <i class="fas fa-database mr-3 text-base w-5 transition-transform group-hover:scale-110 duration-300"></i>
                                <span>Backup Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index', ['tab' => 'developer']) }}" 
                               class="settings-nav-item {{ $activeTab === 'developer' ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 group">
                                <i class="fas fa-code mr-3 text-base w-5 transition-transform group-hover:scale-110 duration-300"></i>
                                <span>Developer Options</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        {{-- Settings Content Area --}}
        <div class="flex-1 min-w-0">
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
