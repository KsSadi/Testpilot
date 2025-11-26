@extends('layouts.backend.master')

@section('title', 'Cypress - Event Capture Bookmarklet')

@section('content')
<div style="padding: 24px;">
    {{-- Page Header --}}
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;">Event Capture Bookmarklet</h1>
        <p style="color: #6b7280;">Install this bookmarklet to capture events from ANY website (even those that block iframes)</p>
    </div>

    {{-- Installation Guide --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 16px;">📚 How to Install & Use</h2>
        
        {{-- Step 1 --}}
        <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
            <h3 style="font-weight: 600; color: #1e40af; margin-bottom: 8px;">Step 1: Drag the Bookmarklet to Your Bookmarks Bar</h3>
            <p style="color: #1e40af; margin-bottom: 12px;">Drag this button to your browser's bookmarks bar:</p>
            <div style="text-align: center; padding: 20px;">
                <a id="bookmarklet-link" href="#" 
                   style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: bold; border-radius: 8px; text-decoration: none; font-size: 1.125rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2); cursor: move;"
                   onclick="alert('Please DRAG this button to your bookmarks bar, don\\'t click it here!'); return false;">
                    🎯 Cypress Event Capture
                </a>
            </div>
            <p style="color: #1e40af; font-size: 0.875rem; margin-top: 8px;">
                <strong>💡 Tip:</strong> Show your bookmarks bar: <kbd>Ctrl+Shift+B</kbd> (Chrome/Edge) or <kbd>Cmd+Shift+B</kbd> (Mac)
            </p>
        </div>

        {{-- Step 2 --}}
        <div style="background: #f0fdf4; border-left: 4px solid #16a34a; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
            <h3 style="font-weight: 600; color: #166534; margin-bottom: 8px;">Step 2: Navigate to ANY Website</h3>
            <p style="color: #166534;">Open any website in your browser (Google, Facebook, banking sites, anything!)</p>
        </div>

        {{-- Step 3 --}}
        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
            <h3 style="font-weight: 600; color: #92400e; margin-bottom: 8px;">Step 3: Click the Bookmarklet on EVERY Page</h3>
            <p style="color: #92400e; margin-bottom: 8px;">⚠️ <strong>Important:</strong> You need to click the bookmarklet on each new page you navigate to.</p>
            <p style="color: #92400e; font-size: 0.875rem;">
                <strong>Why?</strong> Browser security prevents bookmarklets from auto-running on page navigation. Just click it once per page!
            </p>
            <p style="color: #92400e; font-size: 0.875rem; margin-top: 8px;">
                <strong>💡 Tip:</strong> Use keyboard shortcut! Most browsers let you press <kbd>Ctrl+1</kbd>, <kbd>Ctrl+2</kbd>, etc. to activate bookmarks quickly.
            </p>
        </div>

        {{-- Step 4 --}}
        <div style="background: #fce7f3; border-left: 4px solid #ec4899; padding: 16px; border-radius: 4px;">
            <h3 style="font-weight: 600; color: #9f1239; margin-bottom: 8px;">Step 4: Interact & Watch Events Being Captured</h3>
            <p style="color: #9f1239;">All your clicks, inputs, and interactions will be captured and sent to this dashboard!</p>
        </div>
    </div>

    {{-- Alternative: Copy & Paste Method --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 16px;">🔧 Alternative: Manual Injection (Developer Console)</h2>
        
        <p style="color: #6b7280; margin-bottom: 16px;">Can't use bookmarklets? Paste this code directly into the browser's Developer Console (F12):</p>
        
        <div style="position: relative;">
            <pre id="console-code" style="background: #1f2937; color: #e5e7eb; padding: 16px; border-radius: 6px; overflow-x: auto; font-size: 0.875rem; font-family: 'Courier New', monospace; margin: 0;">(function() {
    var script = document.createElement('script');
    script.src = '{{ url('/cypress/capture-script.js') }}?session=' + Date.now();
    document.body.appendChild(script);
})();</pre>
            <button onclick="copyConsoleCode()" style="position: absolute; top: 8px; right: 8px; padding: 6px 12px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.875rem;">
                📋 Copy
            </button>
        </div>
    </div>

    {{-- Current Session Monitor --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin: 0;">📊 Live Event Monitor</h2>
            <div style="display: flex; gap: 8px;">
                <button onclick="startNewSession()" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    ✨ New Session
                </button>
                <button onclick="clearLocalStorage()" style="padding: 8px 16px; background: #f59e0b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    🧹 Clear Storage
                </button>
                <button onclick="loadSessionEvents()" style="padding: 8px 16px; background: #16a34a; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    🔄 Refresh Events
                </button>
                <button onclick="exportCurrentSession()" style="padding: 8px 16px; background: #7c3aed; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    💾 Export JSON
                </button>
                <button onclick="clearMonitor()" style="padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    🗑️ Clear
                </button>
            </div>
        </div>

        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; margin-bottom: 16px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 4px 0;">Current Session ID</p>
                    <p id="current-session" style="font-family: monospace; font-weight: 600; color: #1f2937; margin: 0;">-</p>
                </div>
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 4px 0;">Events Captured</p>
                    <p id="event-count" style="font-family: monospace; font-weight: 600; color: #16a34a; margin: 0;">0</p>
                </div>
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 4px 0;">Status</p>
                    <p id="status" style="font-family: monospace; font-weight: 600; color: #6b7280; margin: 0;">Waiting...</p>
                </div>
            </div>
        </div>

        <div id="event-monitor" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; max-height: 500px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 0.875rem;">
            <div style="color: #9ca3af; text-align: center; padding: 40px;">
                No events yet. Use the bookmarklet on any website to start capturing!
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

@endsection

@push('scripts')
<script>
let currentSessionId = null;
let eventCount = 0;

// Check if there's an active session in localStorage
const storedSession = localStorage.getItem('cypress_session_id');
if (storedSession) {
    currentSessionId = storedSession;
    console.log('📌 Found active session:', currentSessionId);
} else {
    // Create new session
    currentSessionId = Date.now();
    console.log('✨ Created new session:', currentSessionId);
}

// Update current session ID on page load
document.getElementById('current-session').textContent = currentSessionId;

// Update bookmarklet link to use current session
updateBookmarkletLink();

function updateBookmarkletLink() {
    const link = document.getElementById('bookmarklet-link');
    if (link) {
        const serverUrl = '{{ url('/') }}';
        // Use a function to get current session dynamically
        const bookmarkletCode = `javascript:(function(){var dashboardUrl='${serverUrl}/cypress/bookmarklet';if(window.location.href===dashboardUrl){alert('Please use this bookmarklet on OTHER websites, not on this dashboard page!');return;}fetch(dashboardUrl).then(r=>r.text()).then(html=>{var m=html.match(/id="current-session">([^<]+)</);var s=m?m[1]:'${currentSessionId}';var u='${serverUrl}';localStorage.setItem('cypress_session_id',s);localStorage.setItem('cypress_server_url',u);localStorage.setItem('cypress_auto_inject','true');var c=document.createElement('script');c.src=u+'/cypress/capture-script.js?session='+s+'&t='+Date.now();(document.head||document.body||document.documentElement).appendChild(c);console.log('Using session:',s);}).catch(()=>{var s='${currentSessionId}',u='${serverUrl}';localStorage.setItem('cypress_session_id',s);localStorage.setItem('cypress_server_url',u);localStorage.setItem('cypress_auto_inject','true');var c=document.createElement('script');c.src=u+'/cypress/capture-script.js?session='+s+'&t='+Date.now();(document.head||document.body||document.documentElement).appendChild(c);});})();`;
        link.href = bookmarkletCode;
    }
}

// Function to copy console code
function copyConsoleCode() {
    const code = document.getElementById('console-code').textContent;
    navigator.clipboard.writeText(code).then(() => {
        alert('✅ Code copied to clipboard! Now paste it in the browser console (F12).');
    });
}

// Clear localStorage
function clearLocalStorage() {
    if (confirm('⚠️ This will clear all Cypress data from browser storage.\n\nYou will need to click the bookmarklet again on any open pages.\n\nContinue?')) {
        localStorage.removeItem('cypress_session_id');
        localStorage.removeItem('cypress_auto_inject');
        localStorage.removeItem('cypress_server_url');
        alert('✅ Browser storage cleared!\n\nThe current dashboard session ID is still active.\nClick the bookmarklet on any page to start fresh.');
    }
}

// Start a new session
function startNewSession() {
    // Clear old session from localStorage
    localStorage.removeItem('cypress_session_id');
    localStorage.removeItem('cypress_auto_inject');
    
    currentSessionId = Date.now();
    document.getElementById('current-session').textContent = currentSessionId;
    eventCount = 0;
    lastEventCount = 0;
    document.getElementById('event-count').textContent = '0';
    document.getElementById('status').textContent = 'Ready';
    document.getElementById('status').style.color = '#16a34a';
    document.getElementById('event-monitor').innerHTML = '<div style="color: #9ca3af; text-align: center; padding: 40px;">Session ready! Use the bookmarklet on any website to start capturing.</div>';
    
    // Update bookmarklet with new session ID
    updateBookmarkletLink();
    
    // Restart polling
    stopPolling();
    startPolling();
    
    alert('🎉 New session started!\n\nSession ID: ' + currentSessionId + '\n\nThe bookmarklet is now updated with this session ID. Just click it on any website!');
}

// Clear monitor
function clearMonitor() {
    document.getElementById('event-monitor').innerHTML = '<div style="color: #9ca3af; text-align: center; padding: 40px;">Monitor cleared. Use the bookmarklet to start capturing again!</div>';
    eventCount = 0;
    lastEventCount = 0;
    document.getElementById('event-count').textContent = '0';
}

// Load all events for current session
function loadSessionEvents() {
    if (!currentSessionId) {
        alert('⚠️ No active session. Start a new session first!');
        return;
    }
    
    document.getElementById('status').textContent = 'Loading...';
    document.getElementById('status').style.color = '#f59e0b';
    
    const url = window.location.origin + '/cypress/get-events?session_id=' + currentSessionId;
    console.log('Loading events from:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Load response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.events) {
            console.log('✅ Loaded ' + data.events.length + ' events');
            
            // Clear and reload all events
            document.getElementById('event-monitor').innerHTML = '';
            lastEventCount = 0;
            eventCount = 0;
            
            data.events.forEach(event => displayEvent(event));
            
            document.getElementById('status').textContent = 'Loaded';
            document.getElementById('status').style.color = '#16a34a';
            
            alert('✅ Loaded ' + data.events.length + ' events from server!');
        } else {
            alert('⚠️ No events found for this session yet.');
            document.getElementById('status').textContent = 'No events';
            document.getElementById('status').style.color = '#6b7280';
        }
    })
    .catch(error => {
        console.error('Error loading events:', error);
        alert('❌ Error loading events: ' + error.message);
        document.getElementById('status').textContent = 'Error';
        document.getElementById('status').style.color = '#dc2626';
    });
}

// Export current session as JSON
function exportCurrentSession() {
    if (!currentSessionId) {
        alert('⚠️ No active session to export!');
        return;
    }
    
    const url = '{{ url("/cypress/export-results") }}?session_id=' + currentSessionId;
    window.open(url, '_blank');
}

// Setup polling to receive events from server
let pollingInterval = null;
let lastEventCount = 0;

function startPolling() {
    if (pollingInterval) return;
    
    pollingInterval = setInterval(function() {
        fetchLatestEvents();
    }, 2000); // Poll every 2 seconds
    
    console.log('📡 Started polling for events...');
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
        console.log('🛑 Stopped polling');
    }
}

function fetchLatestEvents() {
    if (!currentSessionId) return;
    
    const url = window.location.origin + '/cypress/get-events?session_id=' + currentSessionId;
    console.log('Fetching from:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.events) {
            // Only display new events
            const newEvents = data.events.slice(lastEventCount);
            if (newEvents.length > 0) {
                console.log('📥 Received ' + newEvents.length + ' new events');
                newEvents.forEach(event => displayEvent(event));
                lastEventCount = data.events.length;
                
                // Update status
                document.getElementById('status').textContent = 'Active';
                document.getElementById('status').style.color = '#16a34a';
            }
        }
    })
    .catch(error => {
        console.error('Error fetching events:', error);
    });
}

// Listen for events from bookmarklet (via postMessage or polling)
window.addEventListener('message', function(event) {
    if (event.data && event.data.type === 'cypress-bookmarklet-event') {
        displayEvent(event.data.data);
    }
});

function displayEvent(eventData) {
    const monitor = document.getElementById('event-monitor');
    
    // Remove placeholder if exists
    if (monitor.querySelector('.text-center')) {
        monitor.innerHTML = '';
    }
    
    // Update status
    document.getElementById('status').textContent = 'Active';
    document.getElementById('status').style.color = '#16a34a';
    
    // Increment count
    eventCount++;
    document.getElementById('event-count').textContent = eventCount;
    
    // Create event item
    const eventItem = document.createElement('div');
    eventItem.style.cssText = 'background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 12px; margin-bottom: 8px;';
    
    const timestamp = new Date().toLocaleTimeString();
    
    eventItem.innerHTML = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-weight: bold; color: #2563eb;">${eventData.type?.toUpperCase() || 'UNKNOWN'}</span>
            <span style="color: #6b7280; font-size: 0.75rem;">${timestamp}</span>
        </div>
        <div style="border-left: 2px solid #3b82f6; padding-left: 12px; font-size: 0.75rem; color: #4b5563;">
            ${formatEventDetails(eventData)}
        </div>
    `;
    
    monitor.appendChild(eventItem);
    monitor.scrollTop = monitor.scrollHeight;
}

function formatEventDetails(eventData) {
    let details = [];
    for (let key in eventData) {
        if (key !== 'type' && eventData[key] !== null && eventData[key] !== undefined) {
            let value = eventData[key];
            if (typeof value === 'object') {
                value = JSON.stringify(value);
            }
            details.push(`<strong>${key}:</strong> ${value}`);
        }
    }
    return details.join('<br>') || 'No details';
}

// Initialize
startPolling();
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
</style>
@endpush
