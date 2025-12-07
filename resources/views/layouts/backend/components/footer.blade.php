{{-- Footer Component - Simplified & Responsive --}}
<div class="mt-6 pt-4 border-t border-gray-200">
    <div class="flex flex-col lg:flex-row items-center justify-between gap-3 text-sm text-gray-600">
        <!-- Left: Copyright -->
        <div class="text-center lg:text-left">
            <span>© {{ date('Y') }} {{ $appName ?? config('app.name') }}</span>
        </div>

        <!-- Center: Links -->
        <div class="flex items-center gap-4 text-xs">
            <a href="{{ url('/privacy-policy') }}" class="text-cyan-600 hover:text-cyan-700 transition">Privacy</a>
            <span class="text-gray-300">•</span>
            <a href="{{ url('/terms-of-service') }}" class="text-cyan-600 hover:text-cyan-700 transition">Terms</a>
            <span class="text-gray-300 hidden sm:inline">•</span>
            <span class="text-gray-500 hidden sm:inline">v{{ config('app.version', '1.0.0') }}</span>
        </div>

        <!-- Right: Status -->
        <div class="flex items-center gap-2 text-xs">
            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
            <span class="text-green-600 font-medium">Operational</span>
        </div>
    </div>
</div>
