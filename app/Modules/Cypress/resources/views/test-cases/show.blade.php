@extends('layouts.backend.master')

@section('title', $testCase->name)

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
        <span class="text-gray-800 font-medium">{{ $testCase->name }}</span>
    </div>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ $testCase->name }}</h2>
                @if($testCase->status === 'completed')
                    <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Completed</span>
                @elseif($testCase->status === 'running')
                    <span class="badge-info"><i class="fas fa-circle text-[8px] mr-1"></i>Running</span>
                @elseif($testCase->status === 'failed')
                    <span class="badge-danger"><i class="fas fa-circle text-[8px] mr-1"></i>Failed</span>
                @else
                    <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Pending</span>
                @endif
            </div>
            <p class="text-gray-500 text-xs md:text-sm">{{ $testCase->description ?? 'No description provided' }}</p>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto flex-wrap">
            <a href="{{ route('modules.show', [$project, $module]) }}" class="btn-secondary flex-1 md:flex-none text-center text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <a href="{{ route('test-cases.capture-instructions', [$project, $module, $testCase]) }}" 
               class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm hover:shadow flex-1 md:flex-none text-center text-sm">
                <i class="fas fa-book mr-2"></i>Setup Instructions
            </a>
            <a href="{{ route('test-cases.generate-cypress', [$project, $module, $testCase]) }}" 
               id="generate-cypress-btn"
               class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm hover:shadow flex-1 md:flex-none text-center text-sm">
                <i class="fas fa-code mr-2"></i>Generate Code
            </a>
            <a href="{{ route('test-cases.edit', [$project, $module, $testCase]) }}" class="btn-warning flex-1 md:flex-none text-center text-sm">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Test Case Order</p>
                    <h3 class="text-2xl font-bold text-gray-800">#{{ $testCase->order }}</h3>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-sort-numeric-up text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-500 mb-1">Session ID</p>
                    <div class="flex items-center gap-2">
                        <p id="session-id-text" class="font-mono font-semibold text-gray-800 text-xs truncate flex-1">{{ $testCase->session_id }}</p>
                        <button onclick="copySessionId()" class="p-1.5 bg-cyan-100 hover:bg-cyan-200 text-cyan-700 rounded transition text-xs" title="Copy Session ID">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="primary-color rounded-lg p-3 ml-2">
                    <i class="fas fa-fingerprint text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Saved Events</p>
                    <h3 class="text-2xl font-bold text-gray-800" id="saved-count">{{ $testCase->savedEvents()->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Live Status</p>
                    <p id="live-status" class="font-mono font-semibold text-gray-600 text-lg">Stopped</p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg p-3">
                    <i class="fas fa-signal text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Event Capture Section --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 border-b border-gray-100 gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-radar text-cyan-600"></i>
                    Live Event Capture
                </h3>
                <p class="text-sm text-gray-500 mt-1">Monitor and save user interactions in real-time</p>
            </div>
            <div class="flex items-center gap-2 w-full md:w-auto flex-wrap">
                <a href="{{ route('test-cases.saved-events', [$project, $module, $testCase]) }}" 
                   class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm text-sm flex-1 md:flex-none text-center">
                    <i class="fas fa-database mr-1"></i> View Saved (<span id="saved-count-badge">{{ $testCase->savedEvents()->count() }}</span>)
                </a>
                <button id="live-capture-btn" onclick="toggleLiveCapture()" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm text-sm flex-1 md:flex-none">
                    <i class="fas fa-play"></i> Start Monitor
                </button>
                <button onclick="saveAllEvents()" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm text-sm flex-1 md:flex-none">
                    <i class="fas fa-save"></i> Save Events
                </button>
                <button onclick="clearUnsavedEvents()" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm text-sm flex-1 md:flex-none">
                    <i class="fas fa-trash"></i> Clear
                </button>
            </div>
        </div>

        <div class="p-5 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-orange-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4 border border-orange-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 mb-1 font-semibold uppercase">Live Events (Unsaved)</p>
                            <p id="unsaved-count" class="font-mono font-bold text-orange-600 text-3xl">0</p>
                        </div>
                        <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-3">
                            <i class="fas fa-clock text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-4 border border-cyan-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 mb-1 font-semibold uppercase">Monitor Status</p>
                            <p id="live-status-mobile" class="font-mono font-semibold text-gray-600 text-xl">Stopped</p>
                        </div>
                        <div class="bg-gradient-to-br from-cyan-400 to-cyan-600 rounded-lg p-3">
                            <i class="fas fa-signal text-white text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Events Monitor (Unsaved Only) --}}
        <div id="monitor-unsaved" class="p-5 max-h-[700px] overflow-y-auto bg-white">
            <div class="text-center py-16 text-gray-400">
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4 opacity-20">
                    <i class="fas fa-clock text-white text-4xl"></i>
                </div>
                <p class="text-xl font-semibold text-gray-700 mb-2">No Live Events Captured Yet</p>
                <p class="text-sm text-gray-500 mb-4">Start the monitor and interact with your website to capture events in real-time</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto text-left text-sm text-gray-700">
                    <p class="font-semibold text-blue-800 mb-2"><i class="fas fa-info-circle mr-1"></i> Quick Start:</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Click "Setup Instructions" for setup guide</li>
                        <li>Click "Start Monitor" button above</li>
                        <li>Use extension/bookmarklet to capture events</li>
                        <li>Events appear here automatically</li>
                        <li>Click "Save Events" when done</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

<meta name="csrf-token" content="{{ csrf_token() }}">

@push('styles')
<style>
/* Event item styles */
.event-item {
    @apply bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 mb-3 transition hover:shadow-md;
}

.event-item-unsaved {
    @apply bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-lg p-4 mb-3 transition hover:shadow-md;
}
</style>
@endpush

@push('scripts')
<script>
const sessionId = '{{ $testCase->session_id }}';
let pollingInterval = null;
let lastEventCount = 0;

// Load unsaved events on page load
loadUnsavedEvents();

function loadUnsavedEvents() {
    fetch('{{ route("test-cases.events.get", [$project, $module, $testCase]) }}', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.events) {
            displayUnsavedEvents(data.events);
            updateCounts(data.total, data.saved, data.unsaved);
        }
    })
    .catch(error => {
        console.error('Error loading events:', error);
    });
}

function displayUnsavedEvents(events) {
    const unsavedMonitor = document.getElementById('monitor-unsaved');

    // Clear monitor
    unsavedMonitor.innerHTML = '';

    // Filter only unsaved events
    const unsavedEvents = events.filter(e => !e.is_saved);

    // Display unsaved events with DESC numbering (first event = highest number)
    if (unsavedEvents.length === 0) {
        unsavedMonitor.innerHTML = `
            <div class="text-center py-16 text-gray-400">
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4 opacity-20">
                    <i class="fas fa-clock text-white text-4xl"></i>
                </div>
                <p class="text-xl font-semibold text-gray-700 mb-2">No Live Events Captured Yet</p>
                <p class="text-sm text-gray-500 mb-4">Start the monitor and interact with your website to capture events in real-time</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto text-left text-sm text-gray-700">
                    <p class="font-semibold text-blue-800 mb-2"><i class="fas fa-info-circle mr-1"></i> Quick Start:</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Click "Setup Instructions" for setup guide</li>
                        <li>Click "Start Monitor" button above</li>
                        <li>Use extension/bookmarklet to capture events</li>
                        <li>Events appear here automatically</li>
                        <li>Click "Save Events" when done</li>
                    </ol>
                </div>
            </div>
        `;
    } else {
        unsavedEvents.forEach((event, index) => {
            const eventNumber = unsavedEvents.length - index;
            displayEvent(event, eventNumber, 'unsaved', false);
        });
    }
}

function displayEvent(eventData, number = null, tabType = 'unsaved', scrollToBottom = true) {
    const monitor = document.getElementById('monitor-unsaved');

    // Remove placeholder if exists
    const placeholder = monitor.querySelector('.text-center');
    if (placeholder) {
        monitor.innerHTML = '';
    }

    const eventItem = document.createElement('div');
    eventItem.className = 'bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-lg p-4 mb-3 transition hover:shadow-md';

    const timestamp = new Date(eventData.created_at).toLocaleString();
    const eventNumber = number ? `<span class="bg-orange-600 text-white px-2 py-1 rounded text-xs font-bold mr-2">#${number}</span>` : '';

    // Create user-friendly event title
    const eventTitle = formatEventTitle(eventData);

    eventItem.innerHTML = `
        <div class="flex justify-between mb-2 items-center flex-wrap gap-2">
            <div class="flex items-center gap-2">
                ${eventNumber}
                <span class="font-bold text-blue-600 text-sm">${eventTitle}</span>
            </div>
            <span class="text-gray-500 text-xs">${timestamp}</span>
        </div>
        <div class="border-l-4 border-orange-400 pl-3 text-xs text-gray-600">
            ${formatEventDetails(eventData)}
        </div>
    `;

    monitor.appendChild(eventItem);

    if (scrollToBottom) {
        monitor.scrollTop = monitor.scrollHeight;
    }
}

function formatEventTitle(eventData) {
    const eventType = eventData.event_type || 'unknown';
    let eventJson = null;
    try {
        eventJson = typeof eventData.event_data === 'string' ? JSON.parse(eventData.event_data) : eventData.event_data;
    } catch(e) {}

    const text = eventData.inner_text || eventJson?.innerText || '';
    const value = eventData.value || eventJson?.value || '';
    const tagName = eventData.tag_name || eventJson?.tagName || '';

    switch(eventType.toLowerCase()) {
        case 'click':
            if (text && text.trim().length > 0) {
                const displayText = text.substring(0, 40) + (text.length > 40 ? '...' : '');
                return `🖱️ CLICK: "${displayText}"`;
            }
            if (eventJson?.ariaLabel) return `🖱️ CLICK: ${eventJson.ariaLabel}`;
            if (eventJson?.selectors?.id) return `🖱️ CLICK: ${tagName.toUpperCase()} #${eventJson.selectors.id}`;
            return `🖱️ CLICK: ${tagName.toUpperCase()}`;

        case 'input':
            const fieldName = eventJson?.selectors?.name || eventJson?.selectors?.id || eventJson?.placeholder || tagName;
            if (value) {
                const displayValue = value.substring(0, 20) + (value.length > 20 ? '...' : '');
                return `⌨️ INPUT: ${fieldName} = "${displayValue}"`;
            }
            return `⌨️ INPUT: ${fieldName}`;

        case 'change':
            if (eventJson?.selectedText) return `🔄 SELECT: "${eventJson.selectedText}"`;
            if (eventJson?.checked !== undefined) {
                const state = eventJson.checked ? 'Checked' : 'Unchecked';
                return `☑️ CHECKBOX: ${fieldName} (${state})`;
            }
            return `🔄 CHANGE: ${tagName.toUpperCase()}`;

        default:
            return `${eventType.toUpperCase()}`;
    }
}

function formatEventDetails(eventData) {
    let details = [];
    let eventJson = null;
    try {
        eventJson = typeof eventData.event_data === 'string' ? JSON.parse(eventData.event_data) : eventData.event_data;
    } catch(e) {}

    if (eventJson?.selectors?.id) {
        details.push(`<strong>📍 ID:</strong> <code class="bg-blue-50 px-1 rounded">#${eventJson.selectors.id}</code>`);
    }

    if (eventData.value && eventData.event_type !== 'click') {
        const displayValue = eventData.value.substring(0, 60) + (eventData.value.length > 60 ? '...' : '');
        details.push(`<strong>💬 Content:</strong> <code class="bg-blue-50 px-1 rounded">${displayValue}</code>`);
    }

    if (eventJson?.cypressSelector || eventData.selector) {
        const selector = eventJson?.cypressSelector || eventData.selector;
        details.push(`<strong>🎯 Selector:</strong> <code class="bg-gray-800 text-green-400 px-1 rounded text-xs">${selector}</code>`);
    }

    return details.join('<br>');
}

// Helper function to get user-friendly location info
function getLocationInfo(eventData, eventJson) {
    const tagName = eventData.tag_name || eventJson?.tagName || '';

    // Prioritize meaningful identifiers
    if (eventJson?.selectors?.id) {
        return `<strong style="color: #059669;">${tagName.toUpperCase()}</strong> with ID: <code>#${eventJson.selectors.id}</code>`;
    }

    if (eventJson?.selectors?.name) {
        return `<strong style="color: #0891b2;">${tagName.toUpperCase()}</strong> named: <code>${eventJson.selectors.name}</code>`;
    }

    if (eventJson?.selectors?.testId) {
        return `<strong style="color: #7c3aed;">${tagName.toUpperCase()}</strong> with test-id: <code>${eventJson.selectors.testId}</code>`;
    }

    // Default return if no identifier found
    return tagName ? `<strong>${tagName.toUpperCase()}</strong>` : null;
}

function formatEventDetails(eventData) {
    let details = [];

    // Parse event_data JSON if available
    let eventJson = null;
    try {
        eventJson = typeof eventData.event_data === 'string' ? JSON.parse(eventData.event_data) : eventData.event_data;
    } catch(e) {}

    // Show user-friendly location info first
    const locationInfo = getLocationInfo(eventData, eventJson);
    if (locationInfo) {
        details.push(`<strong>📍 Location:</strong> ${locationInfo}`);
    }

    // Show value/text if relevant
    if (eventData.value && eventData.event_type !== 'click') {
        const displayValue = eventData.value.substring(0, 60) + (eventData.value.length > 60 ? '...' : '');
        details.push(`<strong>💬 Content:</strong> <code style="background: #e0f2fe; padding: 2px 6px; border-radius: 3px;">${displayValue}</code>`);
    }

    // Show selected option for dropdowns
    if (eventJson?.selectedText) {
        details.push(`<strong>✅ Selected:</strong> ${eventJson.selectedText}`);
    }

    // Show checkbox state
    if (eventJson?.checked !== undefined && eventJson.checked !== null) {
        const state = eventJson.checked ? '✅ Checked' : '⬜ Unchecked';
        details.push(`<strong>State:</strong> ${state}`);
    }

    // Show file upload info
    if (eventJson?.fileNames && eventJson.fileNames.length > 0) {
        details.push(`<strong>📁 Files:</strong> ${eventJson.fileNames.join(', ')}`);
    }

    // Show technical selector (collapsed by default)
    if (eventJson?.cypressSelector || eventData.selector) {
        const selector = eventJson?.cypressSelector || eventData.selector;
        details.push(`<strong>🎯 Selector:</strong> <code style="background: #1f2937; color: #10b981; padding: 2px 6px; border-radius: 3px; font-size: 0.65rem;">${selector}</code>`);
    }

    // Collapsible section for all details
    const collapseId = 'collapse-' + (eventData.id || Math.random().toString(36));

    let allDetails = [];

    // All Selector Options (for code generation)
    if (eventJson?.selectors) {
        allDetails.push(`<div style="margin-top: 8px; padding: 8px; background: #f3f4f6; border-radius: 4px;">
            <strong style="color: #1f2937;">🔍 Available Selectors:</strong><br>
            ${eventJson.selectors.id ? `<span style="color: #059669;">• ID:</span> <code>#${eventJson.selectors.id}</code><br>` : ''}
            ${eventJson.selectors.name ? `<span style="color: #0891b2;">• Name:</span> <code>[name="${eventJson.selectors.name}"]</code><br>` : ''}
            ${eventJson.selectors.testId ? `<span style="color: #7c3aed;">• Test ID:</span> <code>[data-testid="${eventJson.selectors.testId}"]</code><br>` : ''}
            ${eventJson.selectors.ariaLabel ? `<span style="color: #dc2626;">• ARIA Label:</span> <code>[aria-label="${eventJson.selectors.ariaLabel}"]</code><br>` : ''}
            ${eventJson.selectors.placeholder ? `<span style="color: #ea580c;">• Placeholder:</span> <code>[placeholder="${eventJson.selectors.placeholder}"]</code><br>` : ''}
            ${eventJson.selectors.xpath ? `<span style="color: #6366f1;">• XPath:</span> <code style="font-size: 0.7rem;">${eventJson.selectors.xpath}</code><br>` : ''}
        </div>`);
    }

    // File Upload Info
    if (eventJson?.fileNames && eventJson.fileNames.length > 0) {
        allDetails.push(`<div style="margin-top: 8px; padding: 8px; background: #fef3c7; border-radius: 4px;">
            <strong style="color: #92400e;">📎 File Upload:</strong><br>
            <span style="color: #78350f;">• Files:</span> ${eventJson.fileNames.join(', ')}<br>
            <span style="color: #78350f;">• Types:</span> ${eventJson.fileTypes?.join(', ') || 'N/A'}<br>
            <span style="color: #78350f;">• Count:</span> ${eventJson.fileCount}
        </div>`);
    }

    // Select/Dropdown Info
    if (eventJson?.selectedText || eventJson?.selectedValue) {
        allDetails.push(`<div style="margin-top: 8px; padding: 8px; background: #dbeafe; border-radius: 4px;">
            <strong style="color: #1e40af;">📋 Select Option:</strong><br>
            ${eventJson.selectedText ? `<span style="color: #1e3a8a;">• Text:</span> ${eventJson.selectedText}<br>` : ''}
            ${eventJson.selectedValue ? `<span style="color: #1e3a8a;">• Value:</span> ${eventJson.selectedValue}<br>` : ''}
            ${eventJson.selectedIndex !== undefined ? `<span style="color: #1e3a8a;">• Index:</span> ${eventJson.selectedIndex}` : ''}
        </div>`);
    }

    // Checkbox/Radio Info
    if (eventJson?.checked !== undefined && eventJson.checked !== null) {
        const checkedState = eventJson.checked ? '✅ Checked' : '⬜ Unchecked';
        allDetails.push(`<div style="margin-top: 8px; padding: 8px; background: #dcfce7; border-radius: 4px;">
            <strong style="color: #166534;">☑️ State:</strong> ${checkedState}
        </div>`);
    }

    // Link/Form Info
    if (eventJson?.href || eventJson?.action) {
        allDetails.push(`<div style="margin-top: 8px; padding: 8px; background: #fce7f3; border-radius: 4px;">
            ${eventJson.href ? `<strong style="color: #9f1239;">🔗 Link:</strong> <code style="font-size: 0.7rem;">${eventJson.href}</code><br>` : ''}
            ${eventJson.action ? `<strong style="color: #9f1239;">📤 Form Action:</strong> ${eventJson.action}<br>` : ''}
            ${eventJson.method ? `<strong style="color: #9f1239;">📮 Method:</strong> ${eventJson.method}` : ''}
        </div>`);
    }

    // URL Info
    if (eventData.url) {
        allDetails.push(`<div style="margin-top: 8px; padding: 8px; background: #f1f5f9; border-radius: 4px;">
            <strong style="color: #475569;">🌐 Page URL:</strong><br>
            <code style="font-size: 0.7rem; word-break: break-all;">${eventData.url}</code>
        </div>`);
    }

    const collapsibleContent = allDetails.length > 0 ? `
        <div style="margin-top: 8px;">
            <button onclick="toggleCollapse('${collapseId}')" style="background: #3b82f6; color: white; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 0.75rem; font-weight: 600;">
                <span id="${collapseId}-icon">▼</span> Show Full Details
            </button>
            <div id="${collapseId}" style="display: none; margin-top: 8px;">
                ${allDetails.join('')}
            </div>
        </div>
    ` : '';

    return details.join('<br>') + collapsibleContent;
}

// Toggle collapse function
function toggleCollapse(id) {
    const element = document.getElementById(id);
    const icon = document.getElementById(id + '-icon');
    if (element.style.display === 'none') {
        element.style.display = 'block';
        icon.textContent = '▲';
    } else {
        element.style.display = 'none';
        icon.textContent = '▼';
    }
}

function updateCounts(total, saved, unsaved) {
    document.getElementById('saved-count').textContent = saved;
    document.getElementById('saved-count-badge').textContent = saved;
    document.getElementById('unsaved-count').textContent = unsaved;
}

function toggleLiveCapture() {
    if (pollingInterval) {
        stopLiveCapture();
    } else {
        startLiveCapture();
    }
}

function startLiveCapture() {
    if (pollingInterval) {
        return; // Already running
    }

    const btn = document.getElementById('live-capture-btn');
    btn.innerHTML = '<i class="fas fa-stop"></i> Stop Monitor';
    btn.className = 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm text-sm flex-1 md:flex-none';

    document.getElementById('live-status').textContent = 'Active';
    document.getElementById('live-status').style.color = '#16a34a';
    if(document.getElementById('live-status-mobile')) {
        document.getElementById('live-status-mobile').textContent = 'Active';
        document.getElementById('live-status-mobile').style.color = '#16a34a';
    }

    pollingInterval = setInterval(() => {
        fetch('{{ route("test-cases.events.get", [$project, $module, $testCase]) }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.events) {
                // Reload unsaved events to update the list
                displayUnsavedEvents(data.events);
                updateCounts(data.total, data.saved, data.unsaved);
            }
        })
        .catch(error => console.error('Error:', error));
    }, 1000);

    showNotification('success', 'Event Monitor Started', 'Events will update automatically every second.');
}

function stopLiveCapture() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;

        const btn = document.getElementById('live-capture-btn');
        btn.innerHTML = '<i class="fas fa-play"></i> Start Monitor';
        btn.className = 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm text-sm flex-1 md:flex-none';

        document.getElementById('live-status').textContent = 'Stopped';
        document.getElementById('live-status').style.color = '#6b7280';
        if(document.getElementById('live-status-mobile')) {
            document.getElementById('live-status-mobile').textContent = 'Stopped';
            document.getElementById('live-status-mobile').style.color = '#6b7280';
        }

        showNotification('info', 'Event Monitor Stopped', 'Event monitoring has been paused.');
    }
}

// Copy Session ID function
function copySessionId() {
    const sessionId = '{{ $testCase->session_id }}';
    navigator.clipboard.writeText(sessionId).then(() => {
        showNotification('success', 'Copied!', 'Session ID copied to clipboard');
    }).catch(err => {
        console.error('Failed to copy:', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = sessionId;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('success', 'Copied!', 'Session ID copied to clipboard');
    });
}

function saveAllEvents() {
    const unsavedCount = parseInt(document.getElementById('unsaved-count').textContent);

    if (unsavedCount === 0) {
        showNotification('info', 'No Unsaved Events', 'There are no unsaved events to save.');
        return;
    }

    // Show cleanup confirmation
    const confirmMsg = `💡 Smart Save with Event Cleanup\n\n` +
                      `This will:\n` +
                      `✓ Merge sequential typing in same field (keep final value only)\n` +
                      `✓ Remove clicks on blank/non-interactive areas\n` +
                      `✓ Keep all important events (buttons, links, forms, etc.)\n\n` +
                      `Total unsaved events: ${unsavedCount}\n\n` +
                      `Continue with smart cleanup?`;
    
    if (!confirm(confirmMsg)) {
        return;
    }

    showNotification('info', 'Processing...', 'Cleaning up and saving events...');

    fetch('{{ route("test-cases.events.save", [$project, $module, $testCase]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const cleaned = data.cleaned || 0;
            const message = cleaned > 0 
                ? `Saved ${data.saved} events (removed ${cleaned} redundant events)`
                : `Saved ${data.saved} events`;
            showNotification('success', 'Events Saved', message);
            loadUnsavedEvents();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Save Failed', 'Failed to save events. Please try again.');
    });
}

function clearUnsavedEvents() {
    const unsavedCount = parseInt(document.getElementById('unsaved-count').textContent);

    if (unsavedCount === 0) {
        showNotification('info', 'Nothing to Clear', 'There are no unsaved events to clear.');
        return;
    }

    // Show confirmation dialog with custom style
    if (!confirm(`⚠️ Clear ${unsavedCount} unsaved event(s)?\n\nThis action cannot be undone.\nSaved events will NOT be deleted.`)) {
        return;
    }

    fetch('{{ route("test-cases.events.clear", [$project, $module, $testCase]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification('success', 'Events Cleared', `Cleared ${data.deleted} unsaved event(s)`);
            loadUnsavedEvents();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Clear Failed', 'Failed to clear events. Please try again.');
    });
}

// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all-saved');
    const checkboxes = document.querySelectorAll('.event-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
        if (selectAllCheckbox.checked) {
            selectedEventIds.add(parseInt(checkbox.dataset.eventId));
        } else {
            selectedEventIds.delete(parseInt(checkbox.dataset.eventId));
        }
    });

    updateSelectedCount();
}

// Update selected count and show/hide delete button
function updateSelectedCount() {
    selectedEventIds.clear();
    const checkboxes = document.querySelectorAll('.event-checkbox:checked');

    checkboxes.forEach(checkbox => {
        selectedEventIds.add(parseInt(checkbox.dataset.eventId));
    });

    const deleteBtn = document.getElementById('delete-selected-btn');
    const selectedCountSpan = document.getElementById('selected-count');

    if (selectedEventIds.size > 0) {
        deleteBtn.style.display = 'inline-block';
        selectedCountSpan.textContent = selectedEventIds.size;
    } else {
        deleteBtn.style.display = 'none';
    }

    // Update select all checkbox state
    const selectAllCheckbox = document.getElementById('select-all-saved');
    const allCheckboxes = document.querySelectorAll('.event-checkbox');
    selectAllCheckbox.checked = allCheckboxes.length > 0 && selectedEventIds.size === allCheckboxes.length;
}

// Delete selected events
function deleteSelectedEvents() {
    if (selectedEventIds.size === 0) {
        showNotification('info', 'No Selection', 'Please select events to delete.');
        return;
    }

    if (!confirm(`⚠️ Delete ${selectedEventIds.size} selected event(s)?\n\nThis action cannot be undone.`)) {
        return;
    }

    fetch('{{ route("test-cases.events.delete", [$project, $module, $testCase]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            event_ids: Array.from(selectedEventIds)
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showNotification('success', 'Events Deleted', `Successfully deleted ${data.deleted} event(s)`);
            selectedEventIds.clear();
            document.getElementById('select-all-saved').checked = false;
            loadAllEvents();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Delete Failed', 'Failed to delete events. Please try again.');
    });
}

// Stop polling when leaving page
window.addEventListener('beforeunload', () => {
    stopLiveCapture();
});

// Console diagnostic on page load
console.log('═══════════════════════════════════════════════════════════');
console.log('🎯 TESTPILOT EVENT CAPTURE DIAGNOSTIC');
console.log('═══════════════════════════════════════════════════════════');
console.log('✅ Expected Session ID:', '{{ $testCase->session_id }}');
console.log('📊 Current Events Count:', {{ $testCase->events()->count() }});
console.log('');
console.log('🔍 TROUBLESHOOTING STEPS:');
console.log('1. Click "Setup Instructions" button above');
console.log('2. For Chrome Extension: Configure it with the Session ID shown above');
console.log('3. For Bookmarklet: Drag the bookmarklet from instructions page');
console.log('4. Open the target website in a NEW TAB');
console.log('5. Click the bookmarklet OR let extension auto-inject');
console.log('6. Perform actions on the website');
console.log('7. Come back here and click "Start Live Capture" to see events');
console.log('');
console.log('💡 TIP: Open Browser Console (F12) on the target website');
console.log('   You should see: "🚀 Testpilot Started!" and "📋 Session: {{ $testCase->session_id }}"');
console.log('═══════════════════════════════════════════════════════════');
</script>
@endpush

@endsection