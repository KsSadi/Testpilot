{{-- Browser Automation Recorder (Codegen Style) --}}
<div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 shadow-lg mb-6" id="recording-section">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-robot"></i>
                Auto Recorder (Codegen)
            </h3>
            <p class="text-indigo-100 text-sm mt-1">Launch browser automatically and record your interactions</p>
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
                       value="https://www.google.com"
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

    {{-- Generated Code Preview --}}
    <div id="code-preview-section" style="display: none;" class="bg-white/10 backdrop-blur rounded-lg p-4 mt-4">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-white font-semibold flex items-center gap-2">
                <i class="fas fa-code"></i>
                Generated Cypress Code
            </h4>
            <div class="flex items-center gap-2">
                <button onclick="copyGeneratedCode()" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded text-sm transition">
                    <i class="fas fa-copy mr-1"></i> Copy
                </button>
                <button onclick="downloadGeneratedCode()" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded text-sm transition">
                    <i class="fas fa-download mr-1"></i> Download
                </button>
                <button onclick="saveGeneratedCode()" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded text-sm transition">
                    <i class="fas fa-save mr-1"></i> Save to Test Case
                </button>
            </div>
        </div>
        <pre class="bg-black/40 rounded-lg p-4 overflow-x-auto"><code id="generated-code" class="text-green-300 text-sm font-mono"></code></pre>
    </div>
</div>

<script>
let recordingSessionId = null;
let recordingInterval = null;
let recordingStartTime = null;
let wsConnection = null;
let capturedEvents = [];

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
        if (data.serviceStatus === 'running') {
            statusEl.innerHTML = `
                <span class="inline-flex items-center gap-2 bg-green-500/80 px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-circle text-white"></i>
                    <span class="text-white font-medium">Service Online</span>
                </span>
            `;
        } else {
            statusEl.innerHTML = `
                <span class="inline-flex items-center gap-2 bg-red-500/80 px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-exclamation-circle text-white"></i>
                    <span class="text-white font-medium">Service Offline</span>
                </span>
            `;
        }
    } catch (error) {
        console.error('Health check failed:', error);
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
            
            // Show success message
            showNotification('Browser launched successfully! Start interacting with the website.', 'success');
            
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
    
    // Extract path from URL
    const urlPath = window.location.pathname;
    const urlMatch = urlPath.match(/\/projects\/([^\/]+)\/modules\/([^\/]+)\/test-cases\/([^\/]+)/);
    if (!urlMatch) return;
    
    const [, projectId, moduleId, testCaseId] = urlMatch;
    
    const pollInterval = setInterval(async () => {
        if (!recordingSessionId) {
            clearInterval(pollInterval);
            return;
        }
        
        try {
            const response = await fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/recording/events/${recordingSessionId}`);
            const data = await response.json();
            
            if (data.success && data.events) {
                capturedEvents = data.events;
                document.getElementById('events-count').textContent = data.eventsCount;
                
                // Update event feed
                updateEventFeed(data.events);
            }
        } catch (error) {
            console.error('Event polling error:', error);
        }
    }, 2000);
}

function addEventToFeed(event) {
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
    
    feed.prepend(eventEl);
    
    // Keep only last 20 events
    while (feed.children.length > 20) {
        feed.removeChild(feed.lastChild);
    }
    
    capturedEvents.push(event);
    document.getElementById('events-count').textContent = capturedEvents.length;
}

function updateEventFeed(events) {
    const feed = document.getElementById('live-events-feed');
    feed.innerHTML = '';
    
    events.slice(-20).reverse().forEach(event => {
        addEventToFeed(event);
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
            // Generate code
            await generateCode();
            
            // Update UI
            document.getElementById('start-recording-btn').style.display = 'flex';
            document.getElementById('start-recording-btn').disabled = false;
            document.getElementById('start-recording-btn').innerHTML = '<i class="fas fa-play mr-2"></i>Start Recording';
            document.getElementById('stop-recording-btn').style.display = 'none';
            document.getElementById('recording-url').disabled = false;
            
            // Clear timers
            clearInterval(recordingInterval);
            
            // Close WebSocket
            if (wsConnection) {
                wsConnection.close();
            }
            
            showNotification('Recording stopped! Code generated successfully.', 'success');
            
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

async function generateCode() {
    if (!recordingSessionId) {
        console.error('No recording session ID');
        return;
    }
    
    const urlPath = window.location.pathname;
    const urlMatch = urlPath.match(/\/projects\/([^\/]+)\/modules\/([^\/]+)\/test-cases\/([^\/]+)/);
    if (!urlMatch) {
        console.error('Could not extract URL parameters');
        return;
    }
    
    const [, projectId, moduleId, testCaseId] = urlMatch;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    console.log('🔧 Generating code...', {
        sessionId: recordingSessionId,
        url: `/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/recording/generate-code`
    });
    
    try {
        const response = await fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/recording/generate-code`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                session_id: recordingSessionId
            })
        });
        
        console.log('📡 Response status:', response.status);
        const data = await response.json();
        console.log('📦 Response data:', data);
        
        if (data.success) {
            console.log('✅ Code generated successfully');
            document.getElementById('generated-code').textContent = data.code;
            document.getElementById('code-preview-section').style.display = 'block';
        } else {
            console.error('❌ Code generation failed:', data.message);
            alert('Failed to generate code: ' + (data.message || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('💥 Code generation error:', error);
        alert('Error generating code: ' + error.message);
    }
}

function copyGeneratedCode() {
    const code = document.getElementById('generated-code').textContent;
    navigator.clipboard.writeText(code);
    showNotification('Code copied to clipboard!', 'success');
}

function downloadGeneratedCode() {
    const code = document.getElementById('generated-code').textContent;
    const blob = new Blob([code], { type: 'text/javascript' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = '{{ $testCase->name }}.cy.js';
    a.click();
    showNotification('Code downloaded!', 'success');
}

async function saveGeneratedCode() {
    const code = document.getElementById('generated-code').textContent;
    
    const urlPath = window.location.pathname;
    const urlMatch = urlPath.match(/\/projects\/([^\/]+)\/modules\/([^\/]+)\/test-cases\/([^\/]+)/);
    if (!urlMatch) return;
    
    const [, projectId, moduleId, testCaseId] = urlMatch;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch(`/projects/${projectId}/modules/${moduleId}/test-cases/${testCaseId}/recording/save-code`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                test_case_id: testCaseId,
                code: code,
                session_id: recordingSessionId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Code saved to test case!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Failed to save code');
        }
        
    } catch (error) {
        console.error('Save code error:', error);
        alert('Error saving code: ' + error.message);
    }
}

function showNotification(message, type = 'info') {
    // Simple notification - you can use your existing notification system
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
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
