@extends('layouts.backend.master')

@section('title', 'Event Capture Instructions')

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
        <span class="text-gray-800 font-medium">Setup Instructions</span>
    </div>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Event Capture Instructions</h2>
            <p class="text-gray-500 text-xs md:text-sm">Setup guide for Test Case: <strong>{{ $testCase->name }}</strong></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Test Case
            </a>
        </div>
    </div>

    {{-- Session ID Display --}}
    <div class="bg-gradient-to-r from-cyan-500 to-cyan-700 rounded-xl p-6 mb-6 text-white shadow-lg">
        <div class="text-center">
            <p class="text-sm opacity-90 mb-2">Your Unique Session ID</p>
            <div class="bg-white bg-opacity-20 rounded-lg p-4 mb-3">
                <p id="session-id-display" class="font-mono font-bold text-2xl">{{ $testCase->session_id }}</p>
            </div>
            <button onclick="copySessionId()" class="bg-white text-cyan-600 font-semibold px-6 py-2 rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-copy mr-2"></i>Copy Session ID
            </button>
            <p class="text-sm opacity-90 mt-3">⚠️ This Session ID is permanent and unique to this test case</p>
        </div>
    </div>

    {{-- Method 1: Bookmarklet --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-bookmark text-blue-600"></i>
                Method 1: Bookmarklet (Easiest)
            </h3>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded">
                <h4 class="font-semibold text-blue-900 mb-2">Step 1: Drag to Bookmarks Bar</h4>
                <p class="text-blue-800 mb-3">Drag this button to your browser's bookmarks bar:</p>
                <div class="text-center py-5">
                    <p class="drag-instruction text-cyan-600 font-semibold mb-3 text-sm">
                        👇 Drag this button to your bookmarks bar 👇
                    </p>
                    <a id="bookmarklet-link" href="#"
                       draggable="true"
                       class="inline-block px-6 py-3 bg-gradient-to-r from-cyan-500 to-cyan-700 text-white font-bold rounded-lg text-lg shadow-lg hover:shadow-xl transition-all cursor-grab"
                       onclick="alert('Please DRAG this button to your bookmarks bar!'); return false;">
                        🎯 {{ $testCase->name }}
                    </a>
                    <p class="text-gray-600 mt-3 text-sm">
                        <i class="fas fa-hand-rock"></i> Click and hold to drag
                    </p>
                </div>
                <p class="text-blue-800 text-sm">
                    <strong>💡 Tip:</strong> Show bookmarks bar: <kbd>Ctrl+Shift+B</kbd> (Windows) or <kbd>Cmd+Shift+B</kbd> (Mac)
                </p>
            </div>

            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded">
                <h4 class="font-semibold text-green-900 mb-2">Step 2: Visit Target Website</h4>
                <p class="text-green-800">Open the website you want to test (Google, Facebook, any site!)</p>
            </div>

            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded">
                <h4 class="font-semibold text-amber-900 mb-2">Step 3: Click Bookmarklet</h4>
                <p class="text-amber-800 mb-2">⚠️ Click the bookmarklet on each new page you navigate to</p>
                <p class="text-amber-800 text-sm">Events will start capturing automatically!</p>
            </div>
        </div>
    </div>

    {{-- Method 2: Chrome Extension --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-puzzle-piece text-emerald-600"></i>
                Method 2: Chrome Extension (Auto-Capture)
            </h3>

            <div class="bg-sky-50 border-l-4 border-sky-500 p-4 mb-4 rounded">
                <p class="text-sky-900 font-semibold mb-1">🎯 RECOMMENDED - Automatic event capture!</p>
                <p class="text-sky-800 text-sm">No need to click on every page</p>
            </div>

            <div class="mb-4">
                <h4 class="font-semibold text-gray-800 mb-3">Installation Steps</h4>

                <ol class="text-gray-700 space-y-3 pl-5 list-decimal">
                    <li>
                        <strong>Download Extension:</strong> Located at <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ public_path('cypress/chrome-extension') }}</code>
                        <br>
                        <a href="{{ route('cypress.download-extension') }}" class="inline-block mt-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition">
                            <i class="fas fa-download mr-2"></i>Download ZIP
                        </a>
                    </li>
                    <li>
                        <strong>Install in Chrome:</strong>
                        <ul class="mt-2 ml-4 space-y-1 list-disc">
                            <li>Go to <code class="bg-gray-100 px-2 py-1 rounded text-sm">chrome://extensions/</code></li>
                            <li>Enable "Developer mode"</li>
                            <li>Click "Load unpacked"</li>
                            <li>Select extracted folder</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Configure Extension:</strong>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mt-2 font-mono text-sm">
                            <div class="mb-2"><strong class="font-sans">Server URL:</strong> {{ url('/') }}</div>
                            <div><strong class="font-sans">Session ID:</strong> {{ $testCase->session_id }}</div>
                        </div>
                    </li>
                    <li><strong>Done!</strong> Enable in extension popup and start browsing</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Method 3: Manual Console --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-terminal text-orange-600"></i>
                Method 3: Manual Console Injection
            </h3>

            <p class="text-gray-600 mb-4">Paste this code in browser console (F12):</p>

            <div class="relative">
                <pre id="console-code" class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm font-mono">(function() {
    var script = document.createElement('script');
    script.src = '{{ url('/cypress/capture-script.js') }}?session={{ $testCase->session_id }}&t=' + Date.now();
    document.body.appendChild(script);
})();</pre>
                <button onclick="copyConsoleCode()" class="absolute top-2 right-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition">
                    <i class="fas fa-copy mr-1"></i>Copy
                </button>
            </div>
        </div>
    </div>

<meta name="csrf-token" content="{{ csrf_token() }}">

@push('styles')
<style>
kbd {
    padding: 2px 8px;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.875rem;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

code {
    background: #f3f4f6;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.875rem;
}

/* Bookmarklet drag effect */
#bookmarklet-link {
    position: relative;
    transition: all 0.2s ease;
}

#bookmarklet-link:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4) !important;
}

#bookmarklet-link:active {
    transform: scale(0.98);
}

#bookmarklet-link.dragging {
    opacity: 0.5;
    cursor: grabbing !important;
}

/* Animated pulse on hover */
@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 4px 6px rgba(0,0,0,0.2), 0 0 0 0 rgba(102, 126, 234, 0.7);
    }
    50% {
        box-shadow: 0 4px 6px rgba(0,0,0,0.2), 0 0 0 10px rgba(102, 126, 234, 0);
    }
}

#bookmarklet-link:hover {
    animation: pulse-glow 2s infinite;
}

/* Drag instruction animation */
.drag-instruction {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}
</style>
@endpush

@push('scripts')
<script>
const sessionId = '{{ $testCase->session_id }}';
const serverUrl = '{{ url('/') }}';

// Update bookmarklet link
const bookmarkletCode = `javascript:(function(){var s='${sessionId}',u='${serverUrl}';localStorage.setItem('cypress_session_id',s);localStorage.setItem('cypress_server_url',u);var c=document.createElement('script');c.src=u+'/cypress/capture-script.js?session='+s+'&t='+Date.now();(document.head||document.body||document.documentElement).appendChild(c);console.log('Cypress capture active. Session:',s);})();`;
document.getElementById('bookmarklet-link').href = bookmarkletCode;

// Add drag events for visual feedback
const bookmarklet = document.getElementById('bookmarklet-link');

bookmarklet.addEventListener('dragstart', function(e) {
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'copy';
    e.dataTransfer.setData('text/plain', this.href);

    // Create custom drag image
    const dragImage = this.cloneNode(true);
    dragImage.style.position = 'absolute';
    dragImage.style.top = '-1000px';
    document.body.appendChild(dragImage);
    e.dataTransfer.setDragImage(dragImage, 0, 0);

    setTimeout(() => document.body.removeChild(dragImage), 0);
});

bookmarklet.addEventListener('dragend', function(e) {
    this.classList.remove('dragging');
});

bookmarklet.addEventListener('mousedown', function(e) {
    this.style.cursor = 'grabbing';
});

bookmarklet.addEventListener('mouseup', function(e) {
    this.style.cursor = 'grab';
});

function copySessionId() {
    navigator.clipboard.writeText(sessionId).then(() => {
        showNotification('success', 'Copied!', 'Session ID copied to clipboard');
    }).catch(() => {
        // Fallback
        const textArea = document.createElement('textarea');
        textArea.value = sessionId;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('success', 'Copied!', 'Session ID copied to clipboard');
    });
}

function copyConsoleCode() {
    const code = document.getElementById('console-code').textContent;
    navigator.clipboard.writeText(code).then(() => {
        showNotification('success', 'Copied!', 'Code copied! Now paste it in browser console (F12)');
    }).catch(() => {
        // Fallback
        const textArea = document.createElement('textarea');
        textArea.value = code;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('success', 'Copied!', 'Code copied! Now paste it in browser console (F12)');
    });
}
</script>
@endpush

@endsection
