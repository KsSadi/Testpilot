{{-- Browser Automation Recorder (Codegen Style) --}}
<div class="primary-color rounded-xl p-6 shadow-lg mb-6" id="recording-section">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-robot"></i>
                Auto Recorder (Codegen)
            </h3>
            <p class="text-white/80 text-sm mt-1">Launch browser automatically and record your interactions</p>
        </div>
        <div id="service-status" class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-sm">
                <i class="fas fa-circle text-white animate-pulse"></i>
                <span class="text-white">Checking...</span>
            </span>
        </div>
    </div>

    {{-- Recording Controls --}}
    <div class="bg-white/10 backdrop-blur rounded-lg p-4 mb-4">
        <div class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-white text-sm font-medium mb-2">
                    <i class="fas fa-globe mr-1"></i> Website URL
                </label>
                <input type="url" 
                       id="recording-url" 
                       placeholder="https://example.com" 
                       value="{{ $project->url ?? 'https://' }}"
                       class="w-full px-4 py-2.5 rounded-lg border-2 border-white/30 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:border-white/60 transition">
            </div>
            <button id="start-recording-btn" 
                    onclick="startRecording()" 
                    class="bg-white text-indigo-600 hover:bg-indigo-50 font-semibold px-6 py-2.5 rounded-lg transition duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="fas fa-play"></i>
                <span>Start Recording</span>
            </button>
            <button id="stop-recording-btn" 
                    onclick="stopRecording()" 
                    style="display: none;"
                    class="bg-red-500 hover:bg-red-600 text-white font-semibold px-6 py-2.5 rounded-lg transition duration-200 shadow-lg flex items-center gap-2">
                <i class="fas fa-stop"></i>
                <span>Stop Recording</span>
            </button>
        </div>
    </div>

    {{-- Recording Status --}}
    <div id="recording-status" style="display: none;" class="bg-white/10 backdrop-blur rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                    <div class="absolute inset-0 w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
                </div>
                <span class="text-white font-semibold">Recording in Progress...</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-white/80 text-sm">
                    <i class="fas fa-mouse-pointer mr-1"></i>
                    <span id="events-count">0</span> events captured
                </span>
                <span class="text-white/80 text-sm">
                    <i class="fas fa-clock mr-1"></i>
                    <span id="recording-duration">00:00</span>
                </span>
            </div>
        </div>
        
        {{-- Live Event Feed --}}
        <div class="bg-black/30 rounded-lg p-4 max-h-48 overflow-y-auto" id="live-events-feed">
            <p class="text-white/60 text-sm text-center py-8">
                <i class="fas fa-info-circle mr-2"></i>
                Interact with the browser to see events here...
            </p>
        </div>
    </div>

    {{-- Captured Events Section (After Recording Stops) --}}
    <div id="captured-events-section" style="display: none;" class="bg-white/10 backdrop-blur rounded-lg p-4 mt-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-white font-semibold flex items-center gap-2">
                <i class="fas fa-list-alt"></i>
                Captured Events (<span id="captured-events-total">0</span>)
            </h4>
            <div class="flex items-center gap-2">
                <button id="toggle-events-btn" onclick="toggleEventsTable()" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded text-sm transition">
                    <i class="fas fa-eye mr-1"></i> <span id="toggle-events-text">Show Events</span>
                </button>
                <button onclick="exportEventsJson()" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded text-sm transition">
                    <i class="fas fa-download mr-1"></i> Export JSON
                </button>
                <button id="save-events-btn" onclick="saveEventsOnly()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded text-sm transition font-medium">
                    <i class="fas fa-save mr-1"></i> Save Events
                </button>
            </div>
        </div>
        
        {{-- Events Table (Initially Hidden) --}}
        <div id="events-table-container" style="display: none;" class="bg-black/30 rounded-lg overflow-hidden">
            <table class="w-full text-sm text-white">
                <thead class="bg-black/40 text-white/80">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Type</th>
                        <th class="px-4 py-2 text-left">Selector/URL</th>
                        <th class="px-4 py-2 text-left">Value</th>
                        <th class="px-4 py-2 text-left">Time</th>
                    </tr>
                </thead>
                <tbody id="events-table-body">
                </tbody>
            </table>
        </div>
    </div>

    {{-- Generate Code Button (After Saving Events) --}}
    <div id="generate-code-section" style="display: none;" class="bg-white/10 backdrop-blur rounded-lg p-4 mt-4">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-400"></i>
                    Events Saved Successfully!
                </h4>
                <p class="text-white/70 text-sm mt-1">You can now generate Cypress test code from your captured events.</p>
            </div>
            <a id="open-code-generator-btn" href="#" class="bg-white hover:bg-white/90 text-cyan-600 font-semibold px-6 py-2.5 rounded-lg transition duration-200 shadow-lg flex items-center gap-2">
                <i class="fas fa-code"></i>
                <span>Generate Code</span>
            </a>
        </div>
    </div>
</div>

<script>
let recordingSessionId = null;
let recordingInterval = null;
let recordingStartTime = null;
let wsConnection = null;
let capturedEvents = [];
let eventPollInterval = null; // Track polling interval to clear on stop

// Check service health on page load
document.addEventListener('DOMContentLoaded', function() {
    checkServiceHealth();
    setInterval(checkServiceHealth, 10000); // Check every 10 seconds
});

async function checkServiceHealth() {
    try {
        const response = await fetch('{{ route("recording.health") }}');
        const data = await response.json();
        
        const statusEl = document.getElementById('service-status');
        const startBtn = document.getElementById('start-recording-btn');
        
        if (data.serviceStatus === 'running') {
            statusEl.innerHTML = `
                <span class="inline-flex items-center gap-2 bg-green-500/80 px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-circle text-white"></i>
                    <span class="text-white font-medium">Service Online</span>
                </span>
            `;
            // Enable the start recording button
            if (startBtn && !recordingSessionId) {
                startBtn.disabled = false;
                startBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                startBtn.title = '';
            }
        } else {
            statusEl.innerHTML = `
                <span class="inline-flex items-center gap-2 bg-red-500/80 px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-exclamation-circle text-white"></i>
                    <span class="text-white font-medium">Service Offline</span>
                </span>
            `;
            // Disable the start recording button
            if (startBtn) {
                startBtn.disabled = true;
                startBtn.classList.add('opacity-50', 'cursor-not-allowed');
                startBtn.title = 'Service is offline. Run: npm run recorder';
            }
        }
    } catch (error) {
        console.error('Health check failed:', error);
        // Disable button on error
        const startBtn = document.getElementById('start-recording-btn');
        if (startBtn) {
            startBtn.disabled = true;
            startBtn.classList.add('opacity-50', 'cursor-not-allowed');
            startBtn.title = 'Cannot connect to service. Run: npm run recorder';
        }
    }
}

async function startRecording() {
    const url = document.getElementById('recording-url').value;
    
    if (!url || !url.startsWith('http')) {
        alert('Please enter a valid URL starting with http:// or https://');
        return;
    }
    
    const startBtn = document.getElementById('start-recording-btn');
    startBtn.disabled = true;
    startBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Launching Browser...';
    
    // Get fresh CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // PERMANENT SOLUTION: Extract IDs from current URL
    // URL format: /projects/{project}/modules/{module}/test-cases/{testCase}
    const urlPath = window.location.pathname;
    const urlMatch = urlPath.match(/\/projects\/([^\/]+)\/modules\/([^\/]+)\/test-cases\/([^\/]+)/);
    
    if (!urlMatch) {
        alert('Could not extract test case information from URL');
        startBtn.disabled = false;
        startBtn.innerHTML = '<i class="fas fa-play mr-2"></i>Start Recording';
        return;
    }
    
    const [, projectId, moduleId, testCaseId] = urlMatch;
    const apiUrl = `/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/recording/start`;
    
    try {
        console.log('Calling URL:', apiUrl);
        console.log('Test Case ID:', testCaseId);
        console.log('Project ID:', projectId);
        console.log('Module ID:', moduleId);
        
        const requestBody = {
            url: document.getElementById('recording-url').value,
            test_case_id: testCaseId
        };
        
        console.log('Request body:', requestBody);
        
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(requestBody)
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Server returned HTML instead of JSON:', text.substring(0, 500));
            throw new Error('Server error. Check console for details.');
        }
        
        const data = await response.json();
        
        // Handle validation errors
        if (response.status === 422) {
            console.error('Validation errors:', data);
            throw new Error(data.message || 'Validation failed: ' + JSON.stringify(data.errors || data));
        }
        
        if (data.success) {
            recordingSessionId = data.sessionId;
            capturedEvents = [];
            
            // Check if VNC is enabled (VPS mode) - show viewer link
            if (data.vncEnabled && data.viewerUrl) {
                // Show VNC viewer link for remote browser access
                showNotification(`
                    <div class="text-left">
                        <p class="font-semibold mb-2">🖥️ Browser launched on server!</p>
                        <p class="mb-2">Click the button below to view and interact with the browser:</p>
                        <a href="${data.viewerUrl}" target="_blank" 
                           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-external-link-alt"></i>
                            Open Browser Viewer
                        </a>
                        <p class="text-xs mt-2 text-gray-400">Opens in new tab. Interact with the browser there.</p>
                    </div>
                `, 'success', 15000);
                
                // Also show a persistent banner
                showVncViewerBanner(data.viewerUrl);
            } else {
                // Local mode - browser opens directly
                showNotification('Browser launched successfully! Start interacting with the website.', 'success');
            }
            
            // Update UI
            document.getElementById('start-recording-btn').style.display = 'none';
            document.getElementById('stop-recording-btn').style.display = 'flex';
            document.getElementById('recording-status').style.display = 'block';
            document.getElementById('recording-url').disabled = true;
            
            // Start duration timer
            recordingStartTime = Date.now();
            recordingInterval = setInterval(updateDuration, 1000);
            
            // Connect WebSocket for live events
            connectWebSocket(data.wsUrl);
            
            // Poll for events every 2 seconds
            startEventPolling();
            
        } else {
            alert(data.message || 'Failed to start recording. Make sure the browser automation service is running.');
            startBtn.disabled = false;
            startBtn.innerHTML = '<i class="fas fa-play mr-2"></i>Start Recording';
        }
        
    } catch (error) {
        console.error('Start recording error:', error);
        alert('Error: ' + error.message + '\n\nMake sure to run: npm run recorder');
        startBtn.disabled = false;
        startBtn.innerHTML = '<i class="fas fa-play mr-2"></i>Start Recording';
    }
}

function connectWebSocket(wsUrl) {
    try {
        wsConnection = new WebSocket(wsUrl);
        
        wsConnection.onopen = function() {
            console.log('WebSocket connected');
        };
        
        wsConnection.onmessage = function(event) {
            const data = JSON.parse(event.data);
            if (data.type === 'event') {
                addEventToFeed(data.data);
            } else if (data.type === 'browser_closed') {
                // Browser was closed manually - auto stop recording
                console.log('Browser closed manually - triggering auto stop');
                handleBrowserClosedAutomatically();
            }
        };
        
        wsConnection.onerror = function(error) {
            console.error('WebSocket error:', error);
        };
        
        wsConnection.onclose = function() {
            console.log('WebSocket closed');
        };
    } catch (error) {
        console.error('WebSocket connection failed:', error);
    }
}

function startEventPolling() {
    if (!recordingSessionId) return;
    
    // Clear any existing poll interval first
    if (eventPollInterval) {
        clearInterval(eventPollInterval);
        eventPollInterval = null;
    }
    
    // Extract path from URL
    const urlPath = window.location.pathname;
    const urlMatch = urlPath.match(/\/projects\/([^\/]+)\/modules\/([^\/]+)\/test-cases\/([^\/]+)/);
    if (!urlMatch) return;
    
    const [, projectId, moduleId, testCaseId] = urlMatch;
    
    console.log('[Event Polling] Starting polling for session:', recordingSessionId);
    
    eventPollInterval = setInterval(async () => {
        if (!recordingSessionId) {
            clearInterval(eventPollInterval);
            eventPollInterval = null;
            return;
        }
        
        try {
            const pollUrl = `/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/recording/events/${recordingSessionId}`;
            console.log('[Event Polling] Fetching:', pollUrl);
            
            const response = await fetch(pollUrl);
            const data = await response.json();
            
            console.log('[Event Polling] Response:', data);
            
            if (data.success && data.events) {
                capturedEvents = data.events;
                document.getElementById('events-count').textContent = data.eventsCount;
                console.log('[Event Polling] Events count:', data.eventsCount);
                
                // Update event feed
                updateEventFeed(data.events);
            }
        } catch (error) {
            console.error('[Event Polling] Error:', error);
        }
    }, 2000);
}

// Render a single event to the live feed UI (no array modification)
function renderEventToFeed(event, prepend = true) {
    const feed = document.getElementById('live-events-feed');
    const eventEl = document.createElement('div');
    eventEl.className = 'text-white text-sm py-1 border-l-2 border-green-400 pl-3 mb-1 animate-fade-in';
    
    let icon = 'fa-mouse-pointer';
    let color = 'text-green-300';
    
    switch(event.type) {
        case 'click': icon = 'fa-mouse-pointer'; color = 'text-blue-300'; break;
        case 'input': icon = 'fa-keyboard'; color = 'text-yellow-300'; break;
        case 'change': icon = 'fa-exchange-alt'; color = 'text-purple-300'; break;
        case 'navigation': icon = 'fa-compass'; color = 'text-pink-300'; break;
    }
    
    eventEl.innerHTML = `
        <i class="fas ${icon} ${color} mr-2"></i>
        <span class="font-medium">${event.type}</span>: 
        <span class="text-white/70">${event.selector || event.url || ''}</span>
    `;
    
    if (prepend) {
        feed.prepend(eventEl);
    } else {
        feed.appendChild(eventEl);
    }
    
    // Keep only last 20 events in the UI
    while (feed.children.length > 20) {
        feed.removeChild(feed.lastChild);
    }
}

// Called from WebSocket - adds to array AND renders
function addEventToFeed(event) {
    // Only add if recording is still active
    if (!recordingSessionId) {
        console.log('[addEventToFeed] Skipped - recording stopped');
        return;
    }
    
    capturedEvents.push(event);
    document.getElementById('events-count').textContent = capturedEvents.length;
    renderEventToFeed(event, true);
}

// Called from polling - replaces array and re-renders (NO double-add!)
function updateEventFeed(events) {
    // Only update if recording is still active
    if (!recordingSessionId) {
        console.log('[updateEventFeed] Skipped - recording stopped');
        return;
    }
    
    const feed = document.getElementById('live-events-feed');
    feed.innerHTML = '';
    
    // Just render the last 20 events - don't add to array (polling already set capturedEvents)
    events.slice(-20).reverse().forEach(event => {
        renderEventToFeed(event, false);
    });
}

function updateDuration() {
    if (!recordingStartTime) return;
    
    const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
    const minutes = Math.floor(elapsed / 60);
    const seconds = elapsed % 60;
    
    document.getElementById('recording-duration').textContent = 
        `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

async function stopRecording() {
    if (!recordingSessionId) return;
    
    // Extract path from URL
    const urlPath = window.location.pathname;
    const urlMatch = urlPath.match(/\/projects\/([^\/]+)\/modules\/([^\/]+)\/test-cases\/([^\/]+)/);
    if (!urlMatch) return;
    
    const [, projectId, moduleId, testCaseId] = urlMatch;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    const stopBtn = document.getElementById('stop-recording-btn');
    stopBtn.disabled = true;
    stopBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Stopping...';
    
    try {
        const response = await fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/recording/stop`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                session_id: recordingSessionId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Hide VNC viewer banner if shown
            hideVncViewerBanner();
            
            // Show captured events section (NO auto-generate code)
            showCapturedEventsSection();
            
            // Update UI
            document.getElementById('start-recording-btn').style.display = 'flex';
            document.getElementById('start-recording-btn').disabled = false;
            document.getElementById('start-recording-btn').innerHTML = '<i class="fas fa-play mr-2"></i>Start Recording';
            document.getElementById('stop-recording-btn').style.display = 'none';
            document.getElementById('recording-url').disabled = false;
            document.getElementById('recording-status').style.display = 'none';
            
            // Clear ALL timers - this is critical to prevent duplicate events!
            clearInterval(recordingInterval);
            recordingInterval = null;
            
            // Stop event polling immediately
            if (eventPollInterval) {
                clearInterval(eventPollInterval);
                eventPollInterval = null;
                console.log('[Stop Recording] Event polling stopped');
            }
            
            // Close WebSocket
            if (wsConnection) {
                wsConnection.close();
                wsConnection = null;
            }
            
            // Reset session ID to prevent any more event captures
            const stoppedSessionId = recordingSessionId;
            recordingSessionId = null;
            console.log('[Stop Recording] Session stopped:', stoppedSessionId);
            
            showNotification('Recording stopped! Review your captured events.', 'success');
            
        } else {
            alert('Failed to stop recording');
        }
        
    } catch (error) {
        console.error('Stop recording error:', error);
        alert('Error stopping recording: ' + error.message);
    } finally {
        stopBtn.disabled = false;
        stopBtn.innerHTML = '<i class="fas fa-stop mr-2"></i>Stop Recording';
    }
}

async function handleBrowserClosedAutomatically() {
    if (!recordingSessionId) return;
    
    console.log('Handling automatic browser close...');
    
    // Hide VNC viewer banner if shown
    hideVncViewerBanner();
    
    // Update UI immediately
    document.getElementById('start-recording-btn').style.display = 'flex';
    document.getElementById('start-recording-btn').disabled = false;
    document.getElementById('start-recording-btn').innerHTML = '<i class="fas fa-play mr-2"></i>Start Recording';
    document.getElementById('stop-recording-btn').style.display = 'none';
    document.getElementById('recording-url').disabled = false;
    document.getElementById('recording-status').style.display = 'none';
    
    // Clear ALL timers - critical to prevent duplicate events!
    if (recordingInterval) {
        clearInterval(recordingInterval);
        recordingInterval = null;
    }
    
    // Stop event polling immediately
    if (eventPollInterval) {
        clearInterval(eventPollInterval);
        eventPollInterval = null;
        console.log('[handleBrowserClosedAutomatically] Event polling stopped');
    }
    
    // Close WebSocket
    if (wsConnection) {
        wsConnection.close();
        wsConnection = null;
    }
    
    // Show captured events section (NO auto-generate code)
    showCapturedEventsSection();
    showNotification('Browser closed. Review your captured events.', 'info');
    
    // Reset session ID to prevent any more event captures
    const stoppedSessionId = recordingSessionId;
    recordingSessionId = null;
    console.log('[handleBrowserClosedAutomatically] Session stopped:', stoppedSessionId);
    recordingStartTime = null;
}

function showCapturedEventsSection() {
    const section = document.getElementById('captured-events-section');
    section.style.display = 'block';
    
    // Update total count
    document.getElementById('captured-events-total').textContent = capturedEvents.length;
    
    // Populate the events table
    populateEventsTable();
}

function populateEventsTable() {
    const tbody = document.getElementById('events-table-body');
    tbody.innerHTML = '';
    
    capturedEvents.forEach((event, index) => {
        const row = document.createElement('tr');
        row.className = 'border-b border-white/10 hover:bg-white/5';
        
        let typeIcon = 'fa-mouse-pointer';
        let typeColor = 'text-blue-300';
        
        switch(event.type) {
            case 'click': typeIcon = 'fa-mouse-pointer'; typeColor = 'text-blue-300'; break;
            case 'input': typeIcon = 'fa-keyboard'; typeColor = 'text-yellow-300'; break;
            case 'change': typeIcon = 'fa-exchange-alt'; typeColor = 'text-purple-300'; break;
            case 'navigation': typeIcon = 'fa-compass'; typeColor = 'text-pink-300'; break;
        }
        
        const timestamp = event.timestamp ? new Date(event.timestamp).toLocaleTimeString() : '-';
        
        row.innerHTML = `
            <td class="px-4 py-2 text-white/60">${index + 1}</td>
            <td class="px-4 py-2">
                <span class="${typeColor}"><i class="fas ${typeIcon} mr-1"></i>${event.type}</span>
            </td>
            <td class="px-4 py-2 text-white/80 font-mono text-xs max-w-xs truncate" title="${event.selector || event.url || '-'}">${event.selector || event.url || '-'}</td>
            <td class="px-4 py-2 text-white/60 max-w-xs truncate" title="${event.value || '-'}">${event.value || '-'}</td>
            <td class="px-4 py-2 text-white/60">${timestamp}</td>
        `;
        
        tbody.appendChild(row);
    });
}

function toggleEventsTable() {
    const container = document.getElementById('events-table-container');
    const btnText = document.getElementById('toggle-events-text');
    const btn = document.getElementById('toggle-events-btn');
    
    if (container.style.display === 'none') {
        container.style.display = 'block';
        btnText.textContent = 'Hide Events';
        btn.querySelector('i').className = 'fas fa-eye-slash mr-1';
    } else {
        container.style.display = 'none';
        btnText.textContent = 'Show Events';
        btn.querySelector('i').className = 'fas fa-eye mr-1';
    }
}

function exportEventsJson() {
    const dataStr = JSON.stringify(capturedEvents, null, 2);
    const blob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = '{{ $testCase->name }}_events.json';
    a.click();
    showNotification('Events exported!', 'success');
}

async function saveEventsOnly() {
    if (capturedEvents.length === 0) {
        alert('No events to save');
        return;
    }
    
    const urlPath = window.location.pathname;
    const urlMatch = urlPath.match(/\/projects\/([^\/]+)\/modules\/([^\/]+)\/test-cases\/([^\/]+)/);
    if (!urlMatch) {
        alert('Could not determine test case ID');
        return;
    }
    
    const [, projectId, moduleId, testCaseId] = urlMatch;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    const saveBtn = document.getElementById('save-events-btn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Saving...';
    
    try {
        const response = await fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/recording/save-events`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                session_id: recordingSessionId || 'manual',
                events: capturedEvents
            })
        });
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Server returned HTML instead of JSON:', text.substring(0, 500));
            throw new Error('Server error. Please check if you are logged in.');
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Show version info in notification
            const versionInfo = data.event_session ? ` as ${data.event_session.version_label}` : '';
            showNotification(`${data.events_saved} events saved${versionInfo}!`, 'success');
            
            // Hide the save button and show Generate Code section
            document.getElementById('captured-events-section').style.display = 'none';
            document.getElementById('generate-code-section').style.display = 'block';
            
            // Set the code generator URL with session param if available
            let codeGeneratorUrl = `/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/code-generator`;
            if (data.event_session && data.event_session.id) {
                codeGeneratorUrl += `?session=${data.event_session.id}`;
            }
            document.getElementById('open-code-generator-btn').href = codeGeneratorUrl;
            
            // Reset session
            recordingSessionId = null;
            
        } else {
            alert('Failed to save events: ' + (data.message || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('Save events error:', error);
        alert('Error saving events: ' + error.message);
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Save Events';
    }
}

function showNotification(message, type = 'info', duration = 3000) {
    // Simple notification - you can use your existing notification system
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in max-w-md`;
    notification.innerHTML = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, duration);
}

// Show persistent VNC viewer banner during recording
function showVncViewerBanner(viewerUrl) {
    // Remove any existing banner
    const existingBanner = document.getElementById('vnc-viewer-banner');
    if (existingBanner) existingBanner.remove();
    
    const banner = document.createElement('div');
    banner.id = 'vnc-viewer-banner';
    banner.className = 'fixed top-0 left-0 right-0 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-4 shadow-lg z-50 flex items-center justify-center gap-4';
    banner.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas fa-desktop text-xl animate-pulse"></i>
            <span class="font-medium">Browser running on server</span>
        </div>
        <a href="${viewerUrl}" target="_blank" 
           class="inline-flex items-center gap-2 bg-white text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-lg font-semibold transition-colors shadow">
            <i class="fas fa-external-link-alt"></i>
            Open Browser Viewer
        </a>
        <span class="text-blue-200 text-sm">Click to view & interact with the browser</span>
    `;
    document.body.prepend(banner);
    
    // Add padding to body to account for banner
    document.body.style.paddingTop = '60px';
}

// Hide VNC viewer banner
function hideVncViewerBanner() {
    const banner = document.getElementById('vnc-viewer-banner');
    if (banner) {
        banner.remove();
        document.body.style.paddingTop = '';
    }
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
