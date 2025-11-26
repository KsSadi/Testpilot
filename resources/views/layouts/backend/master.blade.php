<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', $appName ?? config('app.name', 'Dashboard'))</title>
    
    {{-- Favicon --}}
    @if($appFavicon ?? null)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($appFavicon) }}">
    @endif
    
    {{-- SEO Meta Tags --}}
    <meta name="description" content="@yield('meta_description', setting('meta_description', ''))">
    <meta name="keywords" content="@yield('meta_keywords', setting('meta_keywords', ''))">
    <meta name="author" content="{{ setting('meta_author', $appName ?? config('app.name')) }}">
    
    {{-- Tailwind CSS CDN (for development) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Custom Dashboard CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dashboard.css') }}">
    
    {{-- Additional Page Styles --}}
    @stack('styles')
</head>
<body class="bg-gray-50">
    {{-- Mobile Sidebar Overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <div class="flex h-screen overflow-hidden bg-gray-50">
        {{-- Sidebar Component --}}
        @include('layouts.backend.components.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header Component --}}
            @include('layouts.backend.components.header')

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar">
                {{-- Breadcrumb --}}
                @if(!isset($hideBreadcrumb) || !$hideBreadcrumb)
                    @include('layouts.backend.components.breadcrumb')
                @endif

                {{-- Page Content --}}
                @yield('content')

                {{-- Footer --}}
                @include('layouts.backend.components.footer')
            </main>
        </div>
    </div>
    
    {{-- Core Dashboard JavaScript --}}
    <script src="{{ asset('assets/backend/js/dashboard.js') }}"></script>
    
    {{-- Additional Page Scripts --}}
    @stack('scripts')
</body>
</html>
