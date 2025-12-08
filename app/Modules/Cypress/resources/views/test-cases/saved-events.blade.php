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
            @if($savedEvents->count() > 0)
            <button onclick="clearAllSavedEvents()" 
                    class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm hover:shadow flex-1 md:flex-none text-center text-sm">
                <i class="fas fa-trash-alt mr-2"></i>Clear All
            </button>
            @endif
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
                    <p class="text-sm text-gray-500 mb-1">File Uploads</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $savedEvents->where('event_type', 'file_upload')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-lg p-3">
                    <i class="fas fa-file-upload text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Other Events</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $savedEvents->whereNotIn('event_type', ['click', 'input', 'file_upload'])->count() }}</h3>
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
                <div class="flex items-center gap-3 flex-wrap">
                    <button onclick="openImportModal()" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm hover:shadow text-sm">
                        <i class="fas fa-file-import mr-2"></i>Import Events
                    </button>
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
                         data-text="{{ $event->inner_text }}"
                         data-event-id="{{ $event->id }}">
                        
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
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 text-xs hidden md:block">
                                    <i class="far fa-clock mr-1"></i>{{ $event->created_at->format('M d, Y H:i:s') }}
                                </span>
                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-1">
                                    @if($index > 0)
                                        <button onclick="moveEvent({{ $event->id }}, 'up')" 
                                                class="p-1.5 text-blue-600 hover:bg-blue-100 rounded transition" 
                                                title="Move Up">
                                            <i class="fas fa-arrow-up text-sm"></i>
                                        </button>
                                    @endif
                                    @if($index < $savedEvents->count() - 1)
                                        <button onclick="moveEvent({{ $event->id }}, 'down')" 
                                                class="p-1.5 text-blue-600 hover:bg-blue-100 rounded transition" 
                                                title="Move Down">
                                            <i class="fas fa-arrow-down text-sm"></i>
                                        </button>
                                    @endif
                                    <button onclick="editEvent({{ $event->id }})" 
                                            class="p-1.5 text-green-600 hover:bg-green-100 rounded transition" 
                                            title="Edit Event">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button onclick="deleteEvent({{ $event->id }})" 
                                            class="p-1.5 text-red-600 hover:bg-red-100 rounded transition" 
                                            title="Delete Event">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
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

                            @if($event->event_type === 'file_upload' && isset($eventData['files']))
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 space-y-2">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-blue-800">
                                        <i class="fas fa-file-upload text-blue-600"></i>
                                        File Upload - {{ count($eventData['files']) }} file{{ count($eventData['files']) > 1 ? 's' : '' }}
                                    </div>
                                    @foreach($eventData['files'] as $index => $file)
                                        <div class="bg-white rounded p-2 text-xs space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-semibold text-gray-800 flex items-center gap-1">
                                                    <i class="fas fa-file text-gray-500"></i>
                                                    {{ $file['name'] ?? 'Unknown file' }}
                                                </span>
                                                @if(isset($file['size']))
                                                    <span class="text-gray-500">{{ round($file['size'] / 1024, 2) }} KB</span>
                                                @endif
                                            </div>
                                            @if(isset($file['type']))
                                                <div class="text-gray-600">
                                                    <i class="fas fa-info-circle mr-1"></i>Type: <code class="bg-gray-100 px-1 rounded">{{ $file['type'] }}</code>
                                                </div>
                                            @endif
                                            <div class="text-gray-500 font-mono">
                                                cypress/fixtures/{{ $file['name'] ?? 'file' }}
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($eventData['multiple'] ?? false)
                                        <div class="text-xs text-blue-700">
                                            <i class="fas fa-check-circle mr-1"></i>Multiple files enabled
                                        </div>
                                    @endif
                                    @if($eventData['accept'] ?? null)
                                        <div class="text-xs text-blue-700">
                                            <i class="fas fa-filter mr-1"></i>Accept: <code class="bg-blue-100 px-1 rounded">{{ $eventData['accept'] }}</code>
                                        </div>
                                    @endif
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

    {{-- Edit Event Modal --}}
    <div id="edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            Edit Event
                        </h3>
                        <p class="text-green-100 text-sm mt-1">Modify event details and selector</p>
                    </div>
                    <button onclick="closeEditModal()" class="text-white hover:text-green-200 transition">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <form id="edit-event-form" class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
                <input type="hidden" id="edit-event-id">
                
                <div class="space-y-4">
                    {{-- Event Type (Read-only) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag text-cyan-600 mr-1"></i>Event Type
                        </label>
                        <input type="text" id="edit-event-type" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed text-gray-600">
                        <p class="text-xs text-gray-500 mt-1">Event type cannot be changed</p>
                    </div>

                    {{-- Selector --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-crosshairs text-orange-600 mr-1"></i>Selector *
                        </label>
                        <input type="text" id="edit-selector" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 font-mono text-sm"
                               placeholder="CSS selector or XPath">
                        <p class="text-xs text-gray-500 mt-1">CSS selector or XPath to target the element</p>
                    </div>

                    {{-- Value (for input/change events) --}}
                    <div id="edit-value-field">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-keyboard text-purple-600 mr-1"></i>Value
                        </label>
                        <input type="text" id="edit-value"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Input value">
                        <p class="text-xs text-gray-500 mt-1">Value to be entered (for input/change events)</p>
                    </div>

                    {{-- Inner Text (for click events) --}}
                    <div id="edit-text-field">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-quote-right text-green-600 mr-1"></i>Element Text
                        </label>
                        <input type="text" id="edit-inner-text"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Button or element text">
                        <p class="text-xs text-gray-500 mt-1">Text content of the element</p>
                    </div>

                    {{-- Tag Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-code text-red-600 mr-1"></i>HTML Tag
                        </label>
                        <input type="text" id="edit-tag-name"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="button, input, div, etc.">
                        <p class="text-xs text-gray-500 mt-1">HTML tag name of the element</p>
                    </div>

                    {{-- Comment/Note (Optional) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-comment text-blue-600 mr-1"></i>Comment (Optional)
                        </label>
                        <textarea id="edit-comment" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Add a note about this event..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Optional note for documentation</p>
                    </div>
                </div>
            </form>

            {{-- Modal Footer --}}
            <div class="border-t border-gray-200 p-5 bg-gray-50">
                <div class="flex items-center justify-end gap-3">
                    <button onclick="closeEditModal()" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button onclick="saveEventChanges()" class="btn-primary bg-green-600 hover:bg-green-700">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Events Modal --}}
    <div id="import-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold flex items-center gap-2">
                            <i class="fas fa-file-import"></i>
                            Import Events from Another Test Case
                        </h3>
                        <p class="text-purple-100 text-sm mt-1">Select a test case to copy its saved events</p>
                    </div>
                    <button onclick="closeImportModal()" class="text-white hover:text-purple-200 transition">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
                {{-- Search Filter --}}
                <div class="mb-4">
                    <input type="text" id="test-case-search" placeholder="Search test cases..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           onkeyup="filterTestCases()">
                </div>

                {{-- Loading State --}}
                <div id="loading-test-cases" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
                    <p class="text-gray-600 mt-3">Loading test cases...</p>
                </div>

                {{-- Test Cases List --}}
                <div id="test-cases-list" class="hidden space-y-3">
                    {{-- Will be populated by JavaScript --}}
                </div>

                {{-- No Results --}}
                <div id="no-test-cases" class="hidden text-center py-8 text-gray-400">
                    <i class="fas fa-search text-5xl mb-3"></i>
                    <p class="text-lg font-medium text-gray-500">No test cases found</p>
                    <p class="text-sm">Try a different search term or create new test cases</p>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="border-t border-gray-200 p-5 bg-gray-50">
                <div class="flex items-center justify-end gap-3">
                    <button onclick="closeImportModal()" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </div>
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

@push('scripts')
<script>
const projectId = '{{ $project->getRouteKey() }}';
const moduleId = '{{ $module->getRouteKey() }}';
const testCaseId = '{{ $testCase->getRouteKey() }}';

// Open import modal and load test cases
function openImportModal() {
    document.getElementById('import-modal').classList.remove('hidden');
    loadTestCases();
}

// Close import modal
function closeImportModal() {
    document.getElementById('import-modal').classList.add('hidden');
    document.getElementById('test-case-search').value = '';
}

// Load test cases from the same project
function loadTestCases() {
    const loadingEl = document.getElementById('loading-test-cases');
    const listEl = document.getElementById('test-cases-list');
    const noResultsEl = document.getElementById('no-test-cases');

    loadingEl.classList.remove('hidden');
    listEl.classList.add('hidden');
    noResultsEl.classList.add('hidden');

    fetch(`/projects/${projectId}/test-cases-for-import?exclude=${testCaseId}`)
        .then(response => response.json())
        .then(data => {
            loadingEl.classList.add('hidden');
            
            if (data.testCases && data.testCases.length > 0) {
                renderTestCases(data.testCases);
                listEl.classList.remove('hidden');
            } else {
                noResultsEl.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading test cases:', error);
            loadingEl.classList.add('hidden');
            noResultsEl.classList.remove('hidden');
        });
}

// Render test cases list
function renderTestCases(testCases) {
    const listEl = document.getElementById('test-cases-list');
    listEl.innerHTML = '';

    testCases.forEach(testCase => {
        const div = document.createElement('div');
        div.className = 'test-case-item bg-gray-50 hover:bg-purple-50 border border-gray-200 hover:border-purple-300 rounded-lg p-4 cursor-pointer transition';
        div.dataset.name = testCase.name.toLowerCase();
        div.dataset.moduleName = testCase.module_name.toLowerCase();
        div.onclick = () => importEventsFrom(testCase.hashid, testCase.name);

        div.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="font-semibold text-gray-800">${testCase.name}</h4>
                        <span class="badge-primary text-xs">${testCase.module_name}</span>
                    </div>
                    <p class="text-sm text-gray-600">${testCase.description || 'No description'}</p>
                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                        <span><i class="fas fa-database mr-1 text-green-600"></i>${testCase.events_count} saved events</span>
                        <span><i class="far fa-clock mr-1"></i>Created ${testCase.created_at}</span>
                    </div>
                </div>
                <div class="ml-4">
                    <button class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                        <i class="fas fa-download mr-2"></i>Import
                    </button>
                </div>
            </div>
        `;

        listEl.appendChild(div);
    });
}

// Filter test cases based on search
function filterTestCases() {
    const searchTerm = document.getElementById('test-case-search').value.toLowerCase();
    const testCaseItems = document.querySelectorAll('.test-case-item');
    let visibleCount = 0;

    testCaseItems.forEach(item => {
        const name = item.dataset.name;
        const moduleName = item.dataset.moduleName;
        
        if (name.includes(searchTerm) || moduleName.includes(searchTerm)) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });

    // Show no results message if nothing visible
    const noResultsEl = document.getElementById('no-test-cases');
    const listEl = document.getElementById('test-cases-list');
    
    if (visibleCount === 0 && searchTerm !== '') {
        noResultsEl.classList.remove('hidden');
        listEl.classList.add('hidden');
    } else if (visibleCount > 0) {
        noResultsEl.classList.add('hidden');
        listEl.classList.remove('hidden');
    }
}

// Import events from selected test case
function importEventsFrom(sourceTestCaseId, sourceTestCaseName) {
    if (!confirm(`Import all saved events from "${sourceTestCaseName}"?\n\nThis will add them to your current test case.`)) {
        return;
    }

    // Show loading state
    const modal = document.getElementById('import-modal');
    modal.innerHTML = `
        <div class="p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-2 border-purple-600 mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Importing Events...</h3>
            <p class="text-gray-600">Please wait while we copy the events</p>
        </div>
    `;

    fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/import-events`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            source_test_case_id: sourceTestCaseId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeImportModal();
            
            // Show success notification
            if (typeof showNotification === 'function') {
                showNotification('success', 'Import Successful!', `Successfully imported ${data.imported_count} events from "${sourceTestCaseName}"`);
            } else {
                alert(`Success! Imported ${data.imported_count} events from "${sourceTestCaseName}"`);
            }
            
            // Reload page to show new events
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            alert('Error: ' + (data.message || 'Failed to import events'));
            closeImportModal();
        }
    })
    .catch(error => {
        console.error('Error importing events:', error);
        alert('An error occurred while importing events');
        closeImportModal();
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImportModal();
        closeEditModal();
    }
});

// Close modal on background click
document.getElementById('import-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeImportModal();
    }
});

document.getElementById('edit-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// =============================================================================
// EDIT EVENT FUNCTIONALITY
// =============================================================================

function editEvent(eventId) {
    // Find the event card
    const eventCard = document.querySelector(`[data-event-id="${eventId}"]`);
    if (!eventCard) return;

    // Get event data from the card
    const eventType = eventCard.dataset.eventType;
    const selector = eventCard.dataset.selector;
    const value = eventCard.dataset.value || '';
    const innerText = eventCard.dataset.text || '';

    // Populate the modal
    document.getElementById('edit-event-id').value = eventId;
    document.getElementById('edit-event-type').value = eventType.toUpperCase();
    document.getElementById('edit-selector').value = selector;
    document.getElementById('edit-value').value = value;
    document.getElementById('edit-inner-text').value = innerText;

    // Show/hide fields based on event type
    const valueField = document.getElementById('edit-value-field');
    const textField = document.getElementById('edit-text-field');
    
    if (eventType === 'click') {
        valueField.classList.add('hidden');
        textField.classList.remove('hidden');
    } else {
        valueField.classList.remove('hidden');
        textField.classList.add('hidden');
    }

    // Show modal
    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
    document.getElementById('edit-event-form').reset();
}

function saveEventChanges() {
    const eventId = document.getElementById('edit-event-id').value;
    const selector = document.getElementById('edit-selector').value;
    const value = document.getElementById('edit-value').value;
    const innerText = document.getElementById('edit-inner-text').value;
    const tagName = document.getElementById('edit-tag-name').value;
    const comment = document.getElementById('edit-comment').value;

    if (!selector.trim()) {
        alert('Selector is required');
        return;
    }

    // Show loading
    const modal = document.getElementById('edit-modal');
    const originalContent = modal.innerHTML;
    modal.innerHTML = `
        <div class="p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-2 border-green-600 mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Saving Changes...</h3>
            <p class="text-gray-600">Please wait</p>
        </div>
    `;

    fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/events/${eventId}/update`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            selector: selector,
            value: value,
            inner_text: innerText,
            tag_name: tagName,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            if (typeof showNotification === 'function') {
                showNotification('success', 'Updated!', 'Event updated successfully!');
            }
            setTimeout(() => window.location.reload(), 1000);
        } else {
            modal.innerHTML = originalContent;
            alert('Error: ' + (data.message || 'Failed to update event'));
        }
    })
    .catch(error => {
        console.error('Error updating event:', error);
        modal.innerHTML = originalContent;
        alert('An error occurred while updating the event');
    });
}

// =============================================================================
// DELETE EVENT FUNCTIONALITY
// =============================================================================

function deleteEvent(eventId) {
    if (!confirm('Are you sure you want to delete this event?\n\nThis action cannot be undone.')) {
        return;
    }

    // Show loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
    loadingOverlay.innerHTML = `
        <div class="bg-white p-8 rounded-xl shadow-2xl text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-800">Deleting Event...</h3>
        </div>
    `;
    document.body.appendChild(loadingOverlay);

    fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/events/${eventId}/delete`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingOverlay.remove();
        
        if (data.success) {
            if (typeof showNotification === 'function') {
                showNotification('success', 'Deleted!', 'Event deleted successfully!');
            }
            
            // Remove the event card from DOM
            const eventCard = document.querySelector(`[data-event-id="${eventId}"]`);
            if (eventCard) {
                eventCard.style.opacity = '0';
                eventCard.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    eventCard.remove();
                    // Update visible count
                    const visibleCount = document.querySelectorAll('.event-item').length;
                    document.getElementById('visible-count').textContent = visibleCount;
                }, 300);
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to delete event'));
        }
    })
    .catch(error => {
        loadingOverlay.remove();
        console.error('Error deleting event:', error);
        alert('An error occurred while deleting the event');
    });
}

// =============================================================================
// REORDER EVENT FUNCTIONALITY
// =============================================================================

function moveEvent(eventId, direction) {
    const eventCard = document.querySelector(`[data-event-id="${eventId}"]`);
    if (!eventCard) return;

    // Show loading
    eventCard.style.opacity = '0.5';
    eventCard.style.pointerEvents = 'none';

    fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/events/${eventId}/move`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            direction: direction
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof showNotification === 'function') {
                showNotification('success', 'Reordered!', `Event moved ${direction} successfully!`);
            }
            // Reload to show new order
            setTimeout(() => window.location.reload(), 500);
        } else {
            eventCard.style.opacity = '1';
            eventCard.style.pointerEvents = 'auto';
            alert('Error: ' + (data.message || 'Failed to move event'));
        }
    })
    .catch(error => {
        eventCard.style.opacity = '1';
        eventCard.style.pointerEvents = 'auto';
        console.error('Error moving event:', error);
        alert('An error occurred while moving the event');
    });
}

// Clear all saved events
function clearAllSavedEvents() {
    if (!confirm('⚠️ WARNING: This will permanently delete ALL saved events!\n\nThis action cannot be undone. Are you sure?')) {
        return;
    }

    // Double confirmation for safety
    if (!confirm('Are you absolutely sure? All {{ $savedEvents->count() }} saved events will be deleted permanently.')) {
        return;
    }

    // Show loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
    loadingOverlay.innerHTML = `
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center gap-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                <span class="text-gray-700 font-medium">Deleting all events...</span>
            </div>
        </div>
    `;
    document.body.appendChild(loadingOverlay);

    fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/events/clear-all`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingOverlay.remove();
        if (data.success) {
            if (typeof showNotification === 'function') {
                showNotification('success', 'Cleared!', data.message || 'All saved events deleted successfully!');
            }
            // Reload page to show empty state
            setTimeout(() => window.location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Failed to clear events'));
        }
    })
    .catch(error => {
        loadingOverlay.remove();
        console.error('Error clearing events:', error);
        alert('An error occurred while clearing events');
    });
}
</script>
@endpush
