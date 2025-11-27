@extends('layouts.backend.master')

@section('title', 'Event Capture Instructions')

@section('content')
<div style="padding: 24px;">
    {{-- Page Header --}}
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;">Event Capture Instructions</h1>
            <p style="color: #6b7280;">Setup guide for Test Case: <strong>{{ $testCase->name }}</strong></p>
        </div>
        <a href="{{ route('test-cases.show', [$project, $testCase]) }}" style="padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-arrow-left"></i> Back to Test Case
        </a>
    </div>

    {{-- Session ID Display --}}
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; padding: 24px; margin-bottom: 24px; color: white;">
        <div style="text-align: center;">
            <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 8px;">Your Unique Session ID</p>
            <div style="background: rgba(255,255,255,0.2); border-radius: 6px; padding: 16px; margin-bottom: 12px;">
                <p id="session-id-display" style="font-family: monospace; font-weight: bold; font-size: 1.5rem; margin: 0;">{{ $testCase->session_id }}</p>
            </div>
            <button onclick="copySessionId()" style="padding: 8px 24px; background: white; color: #667eea; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-copy"></i> Copy Session ID
            </button>
            <p style="font-size: 0.875rem; opacity: 0.9; margin-top: 12px;">⚠️ This Session ID is permanent and unique to this test case</p>
        </div>
    </div>

    {{-- Method 1: Bookmarklet --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 16px;">📚 Method 1: Bookmarklet (Easiest)</h2>
        
        <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
            <h3 style="font-weight: 600; color: #1e40af; margin-bottom: 8px;">Step 1: Drag to Bookmarks Bar</h3>
            <p style="color: #1e40af; margin-bottom: 12px;">Drag this button to your browser's bookmarks bar:</p>
            <div style="text-align: center; padding: 20px;">
                <p class="drag-instruction" style="color: #667eea; font-weight: 600; margin-bottom: 12px; font-size: 0.875rem;">
                    👇 Drag this button to your bookmarks bar 👇
                </p>
                <a id="bookmarklet-link" href="#" 
                   draggable="true"
                   style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: bold; border-radius: 8px; text-decoration: none; font-size: 1.125rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2); cursor: grab;"
                   onclick="alert('Please DRAG this button to your bookmarks bar!'); return false;">
                    🎯 {{ $testCase->name }}
                </a>
                <p style="color: #6b7280; margin-top: 12px; font-size: 0.875rem;">
                    <i class="fas fa-hand-rock"></i> Click and hold to drag
                </p>
            </div>
            <p style="color: #1e40af; font-size: 0.875rem;">
                <strong>💡 Tip:</strong> Show bookmarks bar: <kbd>Ctrl+Shift+B</kbd> (Windows) or <kbd>Cmd+Shift+B</kbd> (Mac)
            </p>
        </div>

        <div style="background: #f0fdf4; border-left: 4px solid #16a34a; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
            <h3 style="font-weight: 600; color: #166534; margin-bottom: 8px;">Step 2: Visit Target Website</h3>
            <p style="color: #166534;">Open the website you want to test (Google, Facebook, any site!)</p>
        </div>

        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 4px;">
            <h3 style="font-weight: 600; color: #92400e; margin-bottom: 8px;">Step 3: Click Bookmarklet</h3>
            <p style="color: #92400e; margin-bottom: 8px;">⚠️ Click the bookmarklet on each new page you navigate to</p>
            <p style="color: #92400e; font-size: 0.875rem;">Events will start capturing automatically!</p>
        </div>
    </div>

    {{-- Method 2: Chrome Extension --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 16px;">⭐ Method 2: Chrome Extension (Auto-Capture)</h2>
        
        <div style="background: #dbeafe; border-left: 4px solid #2563eb; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
            <p style="color: #1e40af; font-weight: 600; margin-bottom: 4px;">🎯 RECOMMENDED - Automatic event capture!</p>
            <p style="color: #1e40af; font-size: 0.875rem;">No need to click on every page</p>
        </div>

        <div style="margin-bottom: 16px;">
            <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 12px;">Installation Steps</h3>
            
            <ol style="color: #6b7280; padding-left: 20px;">
                <li style="margin-bottom: 12px;">
                    <strong>Download Extension:</strong> Located at <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 3px;">{{ public_path('cypress/chrome-extension') }}</code>
                    <br>
                    <a href="{{ url('/cypress/chrome-extension.zip') }}" download style="display: inline-block; margin-top: 4px; padding: 6px 12px; background: #2563eb; color: white; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">
                        📦 Download ZIP
                    </a>
                </li>
                <li style="margin-bottom: 12px;">
                    <strong>Install in Chrome:</strong>
                    <ul style="margin-top: 4px;">
                        <li>Go to <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 3px;">chrome://extensions/</code></li>
                        <li>Enable "Developer mode"</li>
                        <li>Click "Load unpacked"</li>
                        <li>Select extracted folder</li>
                    </ul>
                </li>
                <li style="margin-bottom: 12px;">
                    <strong>Configure Extension:</strong>
                    <div style="background: white; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; margin-top: 4px; font-family: monospace; font-size: 0.875rem;">
                        <strong>Server URL:</strong> {{ url('/') }}<br>
                        <strong>Session ID:</strong> {{ $testCase->session_id }}
                    </div>
                </li>
                <li><strong>Done!</strong> Enable in extension popup and start browsing</li>
            </ol>
        </div>
    </div>

    {{-- Method 3: Manual Console --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px;">
        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 16px;">🔧 Method 3: Manual Console Injection</h2>
        
        <p style="color: #6b7280; margin-bottom: 16px;">Paste this code in browser console (F12):</p>
        
        <div style="position: relative;">
            <pre id="console-code" style="background: #1f2937; color: #e5e7eb; padding: 16px; border-radius: 6px; overflow-x: auto; font-size: 0.875rem; font-family: 'Courier New', monospace; margin: 0;">(function() {
    var script = document.createElement('script');
    script.src = '{{ url('/cypress/capture-script.js') }}?session={{ $testCase->session_id }}&t=' + Date.now();
    document.body.appendChild(script);
})();</pre>
            <button onclick="copyConsoleCode()" style="position: absolute; top: 8px; right: 8px; padding: 6px 12px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.875rem;">
                📋 Copy
            </button>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

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
        alert('✅ Session ID copied!\n\n' + sessionId);
    });
}

function copyConsoleCode() {
    const code = document.getElementById('console-code').textContent;
    navigator.clipboard.writeText(code).then(() => {
        alert('✅ Code copied! Now paste it in browser console (F12).');
    });
}
</script>
@endpush

@push('styles')
<style>
kbd {
    padding: 2px 6px;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 3px;
    font-family: monospace;
    font-size: 0.875rem;
}

button:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    transition: all 0.2s;
}

code {
    background: #e5e7eb;
    padding: 2px 6px;
    border-radius: 3px;
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

/* Drag ghost effect */
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

/* Drag instruction */
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

@endsection
