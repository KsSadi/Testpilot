@extends('layouts.backend.master')

@section('title', 'Settings Test')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Settings Test</span>
    </div>
@endsection

@section('content')
    <div class="page-title-section mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Settings Integration Test</h2>
        <p class="text-gray-500 text-sm mt-1">Verify that settings from database are loaded correctly</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Application Info --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Application Info</h3>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">App Name</p>
                    <p class="text-sm font-medium text-gray-800">{{ $appName ?? 'Not Set' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Tagline</p>
                    <p class="text-sm font-medium text-gray-800">{{ $appTagline ?? 'Not Set' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Description</p>
                    <p class="text-sm font-medium text-gray-800">{{ setting('app_description', 'Not Set') }}</p>
                </div>
            </div>
        </div>

        {{-- Branding --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mr-3">
                    <i class="fas fa-image text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Branding</h3>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-2">Logo</p>
                    @if($appLogo ?? null)
                        <img src="{{ Storage::url($appLogo) }}" alt="Logo" class="h-16 object-contain border rounded-lg p-2">
                        <p class="text-xs text-green-600 mt-1">✓ Logo set</p>
                    @else
                        <p class="text-sm text-gray-400">No logo uploaded</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-2">Favicon</p>
                    @if($appFavicon ?? null)
                        <img src="{{ Storage::url($appFavicon) }}" alt="Favicon" class="h-8 object-contain border rounded-lg p-1">
                        <p class="text-xs text-green-600 mt-1">✓ Favicon set</p>
                    @else
                        <p class="text-sm text-gray-400">No favicon uploaded</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contact Info --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mr-3">
                    <i class="fas fa-address-book text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Contact Info</h3>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Email</p>
                    <p class="text-sm font-medium text-gray-800">{{ setting('contact_email', 'Not Set') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Phone</p>
                    <p class="text-sm font-medium text-gray-800">{{ setting('contact_phone', 'Not Set') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Address</p>
                    <p class="text-sm font-medium text-gray-800">{{ setting('address', 'Not Set') }}</p>
                </div>
            </div>
        </div>

        {{-- Footer Info --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center mr-3">
                    <i class="fas fa-align-center text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Footer</h3>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Footer Text</p>
                    <p class="text-sm font-medium text-gray-800">{{ $footerText ?: 'Not Set' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Copyright</p>
                    <p class="text-sm font-medium text-gray-800">{{ $copyrightText }}</p>
                </div>
            </div>
        </div>

        {{-- Helper Functions --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center mr-3">
                    <i class="fas fa-code text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Helper Functions</h3>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 mb-1">app_name()</p>
                    <p class="text-sm font-medium text-gray-800">{{ app_name() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">setting('timezone')</p>
                    <p class="text-sm font-medium text-gray-800">{{ setting('timezone', 'Not Set') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">setting('date_format')</p>
                    <p class="text-sm font-medium text-gray-800">{{ setting('date_format', 'Not Set') }}</p>
                </div>
            </div>
        </div>

        {{-- Cache Status --}}
        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center mr-3">
                    <i class="fas fa-database text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Cache Status</h3>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Settings Cached</span>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-semibold">Active</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Cache Duration</span>
                    <span class="text-sm font-medium text-gray-800">24 hours</span>
                </div>
                <form action="{{ route('settings.cache.clear') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full btn-secondary text-sm">
                        <i class="fas fa-sync-alt mr-2"></i>Clear Settings Cache
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Usage Examples --}}
    <div class="mt-6 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-book text-cyan-600 mr-2"></i>Usage Examples
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg p-4">
                <p class="text-xs text-gray-500 mb-2">In Blade Templates</p>
                <code class="text-xs bg-gray-800 text-green-400 px-2 py-1 rounded block">{{ '{{ $appName }}' }}</code>
                <code class="text-xs bg-gray-800 text-green-400 px-2 py-1 rounded block mt-1">{{ '{{ setting("key") }}' }}</code>
            </div>
            <div class="bg-white rounded-lg p-4">
                <p class="text-xs text-gray-500 mb-2">In Controllers</p>
                <code class="text-xs bg-gray-800 text-green-400 px-2 py-1 rounded block">Setting::get('key')</code>
                <code class="text-xs bg-gray-800 text-green-400 px-2 py-1 rounded block mt-1">setting('key')</code>
            </div>
        </div>
    </div>
@endsection
