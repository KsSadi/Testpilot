@extends('layouts.backend.master')

@section('title', 'Saved Events History')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">Cypress Testing</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-cyan-600">Projects</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('projects.show', $project) }}" class="text-gray-500 hover:text-cyan-600">{{ $project->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('modules.show', [$project, $module]) }}" class="text-gray-500 hover:text-cyan-600">{{ $module->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="text-gray-500 hover:text-cyan-600">{{ $testCase->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Saved Events</span>
    </div>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                    <i class="fas fa-database text-green-600 mr-2"></i>Saved Events History
                </h2>
                <span class="badge-success">
                    <i class="fas fa-check-circle text-[8px] mr-1"></i>{{ $savedEvents->count() }} Events
                </span>
            </div>
            <p class="text-gray-500 text-xs md:text-sm">
                All permanently saved events for: <strong>{{ $testCase->name }}</strong>
            </p>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto flex-wrap">
            <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" 
               class="btn-secondary flex-1 md:flex-none text-center text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Live Events
            </a>
            <a href="{{ route('test-cases.generate-cypress', [$project, $module, $testCase]) }}" 
               class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm hover:shadow flex-1 md:flex-none text-center text-sm">
                <i class="fas fa-code mr-2"></i>Generate Code
            </a>
            <a href="{{ route('test-cases.download-cypress', [$project, $module, $testCase]) }}" 
               class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm hover:shadow flex-1 md:flex-none text-center text-sm">
                <i class="fas fa-download mr-2"></i>Download
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Saved</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $savedEvents->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-database text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Click Events</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $savedEvents->where('event_type', 'click')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg p-3">
                    <i class="fas fa-mouse-pointer text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Input Events</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $savedEvents->where('event_type', 'input')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-keyboard text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Other Events</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $savedEvents->whereNotIn('event_type', ['click', 'input'])->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-3">
                    <i class="fas fa-cogs text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-filter text-cyan-600 mr-2"></i>Filter Events
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search text-gray-400 mr-1"></i>Search Events
                    </label>
                    <input type="text" id="search-input" placeholder="Type to search by selector, text, or value..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-filter text-gray-400 mr-1"></i>Event Type
                    </label>
                    <select id="event-type-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition">
                        <option value="">All Events</option>
                        <option value="click">Click Events</option>
                        <option value="input">Input Events</option>
                        <option value="change">Change Events</option>
                        <option value="submit">Submit Events</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Saved Events List --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="p-5 border-b border-gray-100">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-list text-cyan-600"></i>
                        All Saved Events
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Events are displayed in chronological order (newest first)</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">
                        Showing <strong id="visible-count">{{ $savedEvents->count() }}</strong> of <strong>{{ $savedEvents->count() }}</strong> events
                    </span>
                </div>
            </div>
        </div>

        <div id="events-container" class="p-5 max-h-[800px] overflow-y-auto">
            @if($savedEvents->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 opacity-20">
                        <i class="fas fa-database text-white text-3xl"></i>
                    </div>
                    <p class="text-lg font-semibold text-gray-600 mb-2">No saved events yet</p>
                    <p class="text-sm">Capture and save events from the live capture page to see them here</p>
                    <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" 
                       class="inline-block mt-4 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white font-semibold px-6 py-2 rounded-lg transition duration-200 shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Go to Live Capture
                    </a>
                </div>
            @else
                @foreach($savedEvents as $index => $event)
                    @php
                        $eventData = json_decode($event->event_data, true);
                        $eventNumber = $savedEvents->count() - $index;
                    @endphp
                    <div class="event-item bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 mb-3 transition hover:shadow-md" 
                         data-event-type="{{ $event->event_type }}"
                         data-selector="{{ $event->selector }}"
                         data-value="{{ $event->value }}"
                         data-text="{{ $event->inner_text }}">
                        
                        {{-- Event Header --}}
                        <div class="flex flex-col md:flex-row justify-between mb-3 gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold">
                                    #{{ $eventNumber }}
                                </span>
                                <span class="font-bold text-blue-600 text-sm">
                                    {{ formatEventTitle($event) }}
                                </span>
                            </div>
                            <span class="text-gray-500 text-xs">
                                <i class="far fa-clock mr-1"></i>{{ $event->created_at->format('M d, Y H:i:s') }}
                            </span>
                        </div>

                        {{-- Event Details --}}
                        <div class="border-l-4 border-green-600 pl-4 space-y-2">
                            @if($event->event_type)
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="font-semibold text-gray-700 min-w-[100px]">
                                        <i class="fas fa-tag text-cyan-600 mr-1"></i>Event Type:
                                    </span>
                                    <span class="bg-cyan-100 text-cyan-800 px-2 py-0.5 rounded text-xs font-semibold uppercase">
                                        {{ $event->event_type }}
                                    </span>
                                </div>
                            @endif

                            @if($eventData['selectors']['id'] ?? null)
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="font-semibold text-gray-700 min-w-[100px]">
                                        <i class="fas fa-hashtag text-blue-600 mr-1"></i>Element ID:
                                    </span>
                                    <code class="bg-blue-50 text-blue-800 px-2 py-0.5 rounded text-xs font-mono">
                                        #{{ $eventData['selectors']['id'] }}
                                    </code>
                                </div>
                            @endif

                            @if($event->value && $event->event_type !== 'click')
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="font-semibold text-gray-700 min-w-[100px]">
                                        <i class="fas fa-keyboard text-purple-600 mr-1"></i>Value:
                                    </span>
                                    <code class="bg-purple-50 text-purple-800 px-2 py-0.5 rounded text-xs font-mono break-all">
                                        {{ Str::limit($event->value, 100) }}
                                    </code>
                                </div>
                            @endif

                            @if($event->inner_text && $event->event_type === 'click')
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="font-semibold text-gray-700 min-w-[100px]">
                                        <i class="fas fa-quote-right text-green-600 mr-1"></i>Button Text:
                                    </span>
                                    <span class="text-gray-700 font-medium">
                                        "{{ Str::limit($event->inner_text, 100) }}"
                                    </span>
                                </div>
                            @endif

                            @if($eventData['cypressSelector'] ?? $event->selector)
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="font-semibold text-gray-700 min-w-[100px]">
                                        <i class="fas fa-crosshairs text-orange-600 mr-1"></i>Selector:
                                    </span>
                                    <code class="bg-gray-800 text-green-400 px-2 py-0.5 rounded text-xs font-mono break-all flex-1">
                                        {{ $eventData['cypressSelector'] ?? $event->selector }}
                                    </code>
                                </div>
                            @endif

                            @if($event->tag_name ?? $eventData['tagName'] ?? null)
                                <div class="flex items-start gap-2 text-sm">
                                    <span class="font-semibold text-gray-700 min-w-[100px]">
                                        <i class="fas fa-code text-red-600 mr-1"></i>Element:
                                    </span>
                                    <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-xs font-semibold uppercase">
                                        &lt;{{ $event->tag_name ?? $eventData['tagName'] }}&gt;
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Session Info (Collapsible) --}}
                        <details class="mt-3">
                            <summary class="cursor-pointer text-xs text-gray-600 hover:text-cyan-600 transition">
                                <i class="fas fa-info-circle mr-1"></i>Technical Details
                            </summary>
                            <div class="mt-2 p-3 bg-gray-50 rounded text-xs space-y-1">
                                <div><strong>Session ID:</strong> <code class="text-cyan-600">{{ $event->session_id }}</code></div>
                                <div><strong>Event ID:</strong> <code class="text-cyan-600">{{ $event->id }}</code></div>
                                <div><strong>Timestamp:</strong> {{ $event->created_at->toDateTimeString() }}</div>
                            </div>
                        </details>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

@endsection

@php
function formatEventTitle($event) {
    $eventType = $event->event_type ?? 'unknown';
    $eventData = json_decode($event->event_data, true);
    $text = $event->inner_text ?? $eventData['innerText'] ?? '';
    $value = $event->value ?? $eventData['value'] ?? '';
    $tagName = $event->tag_name ?? $eventData['tagName'] ?? '';

    switch(strtolower($eventType)) {
        case 'click':
            if ($text && trim($text)) {
                $displayText = Str::limit($text, 40);
                return "🖱️ CLICK: \"$displayText\"";
            }
            if ($eventData['ariaLabel'] ?? null) return "🖱️ CLICK: " . $eventData['ariaLabel'];
            if ($eventData['selectors']['id'] ?? null) return "🖱️ CLICK: " . strtoupper($tagName) . " #{$eventData['selectors']['id']}";
            return "🖱️ CLICK: " . strtoupper($tagName);

        case 'input':
            $fieldName = $eventData['selectors']['name'] ?? $eventData['selectors']['id'] ?? $eventData['placeholder'] ?? $tagName;
            if ($value) {
                $displayValue = Str::limit($value, 20);
                return "⌨️ INPUT: $fieldName = \"$displayValue\"";
            }
            return "⌨️ INPUT: $fieldName";

        case 'change':
            if ($eventData['selectedText'] ?? null) return "🔄 SELECT: \"{$eventData['selectedText']}\"";
            if (isset($eventData['checked'])) {
                $state = $eventData['checked'] ? 'Checked' : 'Unchecked';
                $fieldName = $eventData['selectors']['name'] ?? $eventData['selectors']['id'] ?? $tagName;
                return "☑️ CHECKBOX: $fieldName ($state)";
            }
            return "🔄 CHANGE: " . strtoupper($tagName);

        default:
            return strtoupper($eventType);
    }
}
@endphp

@push('scripts')
<script>
// Auto-filtering functionality with smooth animations
const searchInput = document.getElementById('search-input');
const eventTypeFilter = document.getElementById('event-type-filter');
const eventsContainer = document.getElementById('events-container');
const visibleCount = document.getElementById('visible-count');

// Debounce function for search input
let searchTimeout;
function debounce(func, delay) {
    return function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(func, delay);
    };
}

function filterEvents() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const eventType = eventTypeFilter.value.toLowerCase();
    const eventItems = eventsContainer.querySelectorAll('.event-item');
    
    let visibleEvents = 0;
    
    eventItems.forEach(item => {
        const itemEventType = item.dataset.eventType.toLowerCase();
        const itemSelector = item.dataset.selector.toLowerCase();
        const itemValue = item.dataset.value.toLowerCase();
        const itemText = item.dataset.text.toLowerCase();
        
        const matchesSearch = searchTerm === '' || 
                            itemSelector.includes(searchTerm) || 
                            itemValue.includes(searchTerm) || 
                            itemText.includes(searchTerm);
        
        const matchesType = eventType === '' || itemEventType === eventType;
        
        if (matchesSearch && matchesType) {
            item.style.display = 'block';
            item.style.animation = 'fadeIn 0.3s ease-in';
            visibleEvents++;
        } else {
            item.style.display = 'none';
        }
    });
    
    visibleCount.textContent = visibleEvents;
    
    // Show message if no results
    if (visibleEvents === 0 && (searchTerm || eventType)) {
        showNoResultsMessage();
    } else {
        hideNoResultsMessage();
    }
}

function showNoResultsMessage() {
    let noResultsDiv = document.getElementById('no-results-message');
    if (!noResultsDiv) {
        noResultsDiv = document.createElement('div');
        noResultsDiv.id = 'no-results-message';
        noResultsDiv.className = 'text-center py-12 text-gray-400';
        noResultsDiv.innerHTML = `
            <div class="bg-gradient-to-br from-gray-400 to-gray-600 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 opacity-20">
                <i class="fas fa-search text-white text-3xl"></i>
            </div>
            <p class="text-lg font-semibold text-gray-600 mb-2">No events found</p>
            <p class="text-sm">Try adjusting your search or filter criteria</p>
        `;
        eventsContainer.appendChild(noResultsDiv);
    }
}

function hideNoResultsMessage() {
    const noResultsDiv = document.getElementById('no-results-message');
    if (noResultsDiv) {
        noResultsDiv.remove();
    }
}

// Add event listeners with debounce for search
searchInput.addEventListener('input', debounce(filterEvents, 300));
eventTypeFilter.addEventListener('change', filterEvents);

// Add visual feedback when typing
searchInput.addEventListener('input', function() {
    if (this.value.length > 0) {
        this.style.borderColor = '#06b6d4';
        this.style.boxShadow = '0 0 0 3px rgba(6, 182, 212, 0.1)';
    } else {
        this.style.borderColor = '#d1d5db';
        this.style.boxShadow = 'none';
    }
});

// Add smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.event-item {
    transition: all 0.2s ease;
}

.event-item:hover {
    transform: translateX(4px);
}

details summary {
    list-style: none;
}

details summary::-webkit-details-marker {
    display: none;
}

details[open] summary i {
    transform: rotate(90deg);
}

details summary i {
    transition: transform 0.2s;
}

.card-hover {
    transition: all 0.2s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

#events-container::-webkit-scrollbar {
    width: 8px;
}

#events-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

#events-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

#events-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

#search-input:focus,
#event-type-filter:focus {
    outline: none;
}
</style>
@endpush
