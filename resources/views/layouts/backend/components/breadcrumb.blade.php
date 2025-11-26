{{-- Breadcrumb Component --}}
<div class="breadcrumb-section flex items-center justify-between mb-4">
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        @if(isset($breadcrumbs) && is_array($breadcrumbs))
            @foreach($breadcrumbs as $breadcrumb)
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                @if(isset($breadcrumb['url']))
                    <a href="{{ $breadcrumb['url'] }}" class="text-gray-500 hover:text-cyan-600">{{ $breadcrumb['title'] }}</a>
                @else
                    <span class="text-gray-800 font-medium">{{ $breadcrumb['title'] }}</span>
                @endif
            @endforeach
        @else
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="text-gray-800 font-medium">@yield('page-title', 'Dashboard')</span>
        @endif
    </div>
    <div class="flex items-center space-x-3 text-sm text-gray-600">
        <div class="flex items-center space-x-2">
            <i class="fas fa-calendar text-cyan-600"></i>
            <span>{{ now()->format('F j, Y') }}</span>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-clock text-cyan-600"></i>
            <span id="currentTime">{{ now()->format('g:i A') }}</span>
        </div>
    </div>
</div>
