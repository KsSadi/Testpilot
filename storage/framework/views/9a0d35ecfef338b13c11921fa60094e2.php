 

<?php $__env->startSection('title', $pageTitle); ?>

<?php $__env->startSection('content'); ?>
<div style="padding: 24px;">
    
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: start;">
        <div>
            <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;">
                <?php if($testCase): ?>
                    Run Test: <?php echo e($testCase->name); ?>

                <?php else: ?>
                    Cypress Testing
                <?php endif; ?>
            </h1>
            <p style="color: #6b7280;">
                <?php if($testCase): ?>
                    Testing: <?php echo e($testCase->url ?? 'No URL specified'); ?>

                    <br>
                    <small>Project: <a href="<?php echo e(route('projects.show', $project)); ?>" style="color: #3b82f6; text-decoration: none;"><?php echo e($project->name); ?></a></small>
                <?php else: ?>
                    Test websites and capture user interactions
                <?php endif; ?>
            </p>
        </div>
        <?php if(!$testCase): ?>
        <a href="<?php echo e(route('cypress.bookmarklet')); ?>" style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <i class="fas fa-bookmark"></i> 
            <span>Use Bookmarklet<br><small style="font-size: 0.75rem; opacity: 0.9;">(Works on ANY site!)</small></span>
        </a>
        <?php endif; ?>
    </div>

    
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Website Testing Interface</h2>
        
        
        <div style="display: flex; gap: 16px; margin-bottom: 16px;">
            <div style="flex: 1;">
                <label for="website-url" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 8px;">
                    Website URL to Test
                </label>
                <input type="url" 
                       id="website-url" 
                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;" 
                       placeholder="https://example.com" 
                       value="<?php echo e($testCase->url ?? ''); ?>">
                <input type="hidden" id="test-case-id" value="<?php echo e($testCase->id ?? ''); ?>">
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 4px;">
                    Enter the full URL including https://
                </p>
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="button" 
                        id="start-test-btn" 
                        style="padding: 8px 24px; background: #2563eb; color: white; font-weight: 500; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-play"></i> Go
                </button>
            </div>
        </div>

        <?php if(!$testCase): ?>
        
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 12px; margin-bottom: 16px;">
            <div style="display: flex; align-items: start; gap: 8px;">
                <i class="fas fa-info-circle" style="color: #3b82f6; margin-top: 2px;"></i>
                <div style="flex: 1;">
                    <p style="font-size: 0.875rem; color: #1e40af; margin: 0 0 8px 0; font-weight: 600;">Quick test examples:</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <button type="button" onclick="document.getElementById('website-url').value='https://example.com'" 
                                style="padding: 4px 12px; background: white; color: #3b82f6; border: 1px solid #3b82f6; border-radius: 4px; font-size: 0.75rem; cursor: pointer;">
                            example.com
                        </button>
                        <button type="button" onclick="document.getElementById('website-url').value='https://httpbin.org'" 
                                style="padding: 4px 12px; background: white; color: #3b82f6; border: 1px solid #3b82f6; border-radius: 4px; font-size: 0.75rem; cursor: pointer;">
                            httpbin.org
                        </button>
                        <button type="button" onclick="document.getElementById('website-url').value='http://localhost:8000'" 
                                style="padding: 4px 12px; background: white; color: #3b82f6; border: 1px solid #3b82f6; border-radius: 4px; font-size: 0.75rem; cursor: pointer;">
                            This App
                        </button>
                        <button type="button" onclick="document.getElementById('website-url').value='https://dev-btb.oss.net.bd'" 
                                style="padding: 4px 12px; background: white; color: #3b82f6; border: 1px solid #3b82f6; border-radius: 4px; font-size: 0.75rem; cursor: pointer;">
                            BTB (OAuth Site)
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        
        <div style="background: #fef3c7; border: 1px solid #fde047; border-radius: 6px; padding: 12px; margin-bottom: 16px;">
            <div style="display: flex; align-items: start; gap: 8px;">
                <i class="fas fa-exclamation-triangle" style="color: #f59e0b; margin-top: 2px;"></i>
                <div style="flex: 1;">
                    <p style="font-size: 0.875rem; color: #92400e; margin: 0 0 4px 0; font-weight: 600;">⚡ Browser Compatibility Note:</p>
                    <p style="font-size: 0.75rem; color: #92400e; margin: 0;">
                        <strong>✅ Works well:</strong> Public sites, localhost apps, simple pages<br>
                        <strong>⚠️ May fail:</strong> Sites with strict security (Google, Facebook, banking sites) - These will show an error page with option to open in new tab<br>
                        <strong>💡 Tip:</strong> If a site doesn't load in iframe, you can still open it directly from the error page!
                    </p>
                </div>
            </div>
        </div>

        
        <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
            <button type="button" 
                    id="stop-test-btn" 
                    style="padding: 8px 16px; background: #dc2626; color: white; font-weight: 500; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;" 
                    disabled>
                <i class="fas fa-stop"></i> Stop Test
            </button>
            <button type="button" 
                    id="export-results-btn" 
                    style="padding: 8px 16px; background: #16a34a; color: white; font-weight: 500; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;" 
                    disabled>
                <i class="fas fa-download"></i> Export Results
            </button>
            <button type="button" 
                    id="open-new-tab-btn" 
                    style="padding: 8px 16px; background: #7c3aed; color: white; font-weight: 500; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;"
                    disabled>
                <i class="fas fa-external-link-alt"></i> Open in New Tab
            </button>
            <button type="button" 
                    id="clear-results-btn" 
                    style="padding: 8px 16px; background: #4b5563; color: white; font-weight: 500; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-trash"></i> Clear
            </button>
        </div>

        
        <div id="test-status" style="padding: 16px; border-radius: 6px; display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span><strong>Status:</strong> <span id="status-text">Ready to start</span></span>
                <span>Events Captured: <span id="event-count" style="background: #2563eb; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem;">0</span></span>
            </div>
        </div>
    </div>

    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; min-height: 700px;">
        
        <div style="display: flex; flex-direction: column;">
            <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; display: flex; flex-direction: column; height: 100%;">
                <div style="padding: 16px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin: 0;">📚 How to Use Bookmarklet</h3>
                </div>
                <div style="padding: 24px; overflow-y: auto;">
                    
                    <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
                        <h4 style="font-weight: 600; color: #1e40af; margin: 0 0 12px 0; font-size: 0.875rem;">STEP 1: Drag to Bookmarks Bar</h4>
                        <div style="text-align: center; margin-bottom: 12px;">
                            <a id="bookmarklet-link" href="#" 
                               style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: bold; border-radius: 8px; text-decoration: none; font-size: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2); cursor: move;"
                               onclick="alert('Please DRAG this button to your bookmarks bar!'); return false;">
                                🎯 Cypress Capture
                            </a>
                        </div>
                        <p style="color: #1e40af; font-size: 0.75rem; margin: 0;">
                            <strong>Tip:</strong> Show bookmarks bar with <kbd style="background: white; padding: 2px 6px; border-radius: 3px; border: 1px solid #3b82f6;">Ctrl+Shift+B</kbd>
                        </p>
                    </div>

                    
                    <div style="background: #f0fdf4; border-left: 4px solid #16a34a; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
                        <h4 style="font-weight: 600; color: #166534; margin: 0 0 8px 0; font-size: 0.875rem;">STEP 2: Open Website</h4>
                        <p style="color: #166534; font-size: 0.75rem; margin: 0;">Navigate to the URL you entered above or any other website</p>
                    </div>

                    
                    <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; margin-bottom: 16px; border-radius: 4px;">
                        <h4 style="font-weight: 600; color: #92400e; margin: 0 0 8px 0; font-size: 0.875rem;">STEP 3: Click Bookmarklet</h4>
                        <p style="color: #92400e; font-size: 0.75rem; margin: 0;">Click the bookmarklet in your bookmarks bar to start capturing</p>
                    </div>

                    
                    <div style="background: #fce7f3; border-left: 4px solid #ec4899; padding: 16px; border-radius: 4px;">
                        <h4 style="font-weight: 600; color: #9f1239; margin: 0 0 8px 0; font-size: 0.875rem;">STEP 4: Interact & Capture</h4>
                        <p style="color: #9f1239; font-size: 0.75rem; margin: 0;">All clicks and interactions will appear in the Event Monitor →</p>
                    </div>

                    
                    <div style="margin-top: 24px; padding: 16px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px;">
                        <h4 style="font-weight: 600; color: #1f2937; margin: 0 0 8px 0; font-size: 0.875rem;">Current Session</h4>
                        <p style="font-family: monospace; font-size: 0.75rem; color: #6b7280; margin: 0; word-break: break-all;" id="current-session">Loading...</p>
                        <button onclick="startNewSession()" style="margin-top: 8px; padding: 6px 12px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.75rem; width: 100%;">
                            🔄 New Session
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div style="display: flex; flex-direction: column;">
            <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; display: flex; flex-direction: column; height: 100%;">
                <div style="padding: 16px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin: 0;">Event Monitor</h3>
                    <button type="button" 
                            id="clear-events-btn" 
                            style="padding: 4px 8px; font-size: 0.875rem; background: #f3f4f6; color: #374151; border-radius: 4px; border: none; cursor: pointer;">
                        <i class="fas fa-eraser"></i> Clear
                    </button>
                </div>
                <div style="flex: 1; overflow: hidden; padding: 8px;">
                    <div id="event-monitor" 
                         style="overflow-y: auto; overflow-x: hidden; font-size: 0.75rem; font-family: 'Courier New', monospace; height: 100%; word-break: break-word;">
                        <div style="color: #6b7280; padding: 16px; text-align: center; font-size: 0.875rem;">
                            No events captured yet
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($testCase && $project): ?>
    
    <div style="margin-top: 24px;">
        <a href="<?php echo e(route('projects.show', $project)); ?>" style="display: inline-flex; align-items: center; gap: 8px; color: #6b7280; text-decoration: none; font-size: 0.875rem;">
            <i class="fas fa-arrow-left"></i> Back to <?php echo e($project->name); ?>

        </a>
    </div>
    <?php endif; ?>
</div>


<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Button hover effects */
button:hover:not(:disabled) {
    opacity: 0.9;
    transform: translateY(-1px);
}

button:disabled {
    background-color: #9ca3af !important;
    cursor: not-allowed !important;
    opacity: 0.6;
}

.event-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    margin-bottom: 5px;
    padding: 8px;
    position: relative;
}

.event-item:hover {
    background: #e9ecef;
}

.event-type {
    font-weight: bold;
    color: #007bff;
}

.event-time {
    font-size: 10px;
    color: #6c757d;
}

.event-details {
    margin-top: 4px;
    padding-left: 10px;
    border-left: 2px solid #007bff;
}

.iframe-loading {
    background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%), 
                linear-gradient(-45deg, #f8f9fa 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #f8f9fa 75%), 
                linear-gradient(-45deg, transparent 75%, #f8f9fa 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    animation: slide 1s infinite linear;
}

@keyframes slide {
    0% { background-position: 0 0, 0 10px, 10px -10px, -10px 0px; }
    100% { background-position: 20px 20px, 20px 30px, 30px 10px, 10px 20px; }
}

.status-running {
    color: #16a34a !important;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.alert-info { background-color: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
.alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #86efac; }
.alert-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde047; }
.alert-danger { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

/* Responsive design */
@media (max-width: 768px) {
    div[style*="grid-template-columns: 2fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="display: flex; gap: 16px"] {
        flex-direction: column !important;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Cypress Test Manager with Bookmarklet
var cypressTestManager = {
    sessionId: null,
    isTestRunning: false,
    eventCount: 0,
    lastEventCount: 0,
    pollingInterval: null,
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    
    init: function() {
        var self = this;
        
        // Initialize session
        this.initializeSession();
        
        // Start test button - now opens URL in new tab
        document.getElementById('start-test-btn').addEventListener('click', function() {
            self.openUrlInNewTab();
        });
        
        // Stop test button  
        document.getElementById('stop-test-btn').addEventListener('click', function() {
            self.stopTest();
        });
        
        // Export results button
        document.getElementById('export-results-btn').addEventListener('click', function() {
            self.exportResults();
        });
        
        // Clear results button
        document.getElementById('clear-results-btn').addEventListener('click', function() {
            self.clearResults();
        });
        
        // Clear events button
        document.getElementById('clear-events-btn').addEventListener('click', function() {
            self.clearEvents();
        });
        
        // URL input enter key
        document.getElementById('website-url').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                self.openUrlInNewTab();
            }
        });
        
        // Start polling for events immediately
        console.log('🚀 Starting event polling on page load...');
        this.startPolling();
    },
    
    initializeSession: function() {
        var self = this;
        
        // Check if test_case_id is in URL
        const urlParams = new URLSearchParams(window.location.search);
        const testCaseId = urlParams.get('test_case_id');
        
        // Fetch current session from server
        fetch('/cypress/current-session', {
            credentials: 'include'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.session_id) {
                self.sessionId = data.session_id;
                console.log('✨ Session ID from server:', self.sessionId);
            } else {
                // Fallback to localStorage or create new
                const storedSession = localStorage.getItem('cypress_session_id');
                if (storedSession) {
                    self.sessionId = storedSession;
                } else {
                    self.sessionId = Date.now().toString();
                    localStorage.setItem('cypress_session_id', self.sessionId);
                }
                console.log('✨ Session ID from storage/new:', self.sessionId);
            }
            
            // Update UI
            document.getElementById('current-session').textContent = self.sessionId;
            
            // Update bookmarklet link
            self.updateBookmarkletLink();
            
            // If we have a test case ID, auto-start
            if (testCaseId) {
                console.log('🎯 Test case ID detected, starting test session');
                self.isTestRunning = true;
                self.updateStatus('Ready - Monitoring for events...', 'running');
                self.updateUIForRunningTest();
            }
        })
        .catch(() => {
            // Fallback if API fails
            const storedSession = localStorage.getItem('cypress_session_id');
            if (storedSession) {
                self.sessionId = storedSession;
            } else {
                self.sessionId = Date.now().toString();
                localStorage.setItem('cypress_session_id', self.sessionId);
            }
            console.log('✨ Session ID (fallback):', self.sessionId);
            document.getElementById('current-session').textContent = self.sessionId;
            self.updateBookmarkletLink();
        });
    },
    
    updateBookmarkletLink: function() {
        const link = document.getElementById('bookmarklet-link');
        if (link && this.sessionId) {
            const serverUrl = window.location.origin;
            const bookmarkletCode = `javascript:(function(){var dashboardUrl='${serverUrl}/cypress/bookmarklet';if(window.location.href===dashboardUrl){alert('Please use this bookmarklet on OTHER websites!');return;}var s='${this.sessionId}';var u='${serverUrl}';localStorage.setItem('cypress_session_id',s);localStorage.setItem('cypress_server_url',u);var c=document.createElement('script');c.src=u+'/cypress/capture-script.js?session='+s+'&t='+Date.now();(document.head||document.body||document.documentElement).appendChild(c);alert('✅ Cypress Capture Active!\\n\\nClick around to capture events.');})();`;
            link.href = bookmarkletCode;
        }
    },
    
    openUrlInNewTab: function() {
        var urlInput = document.getElementById('website-url');
        var url = urlInput.value.trim();
        
        if (!url) {
            this.showAlert('Please enter a valid URL', 'warning');
            return;
        }
        
        if (!this.isValidUrl(url)) {
            this.showAlert('Please enter a valid URL with https:// or http://', 'warning');
            return;
        }
        
        // Start test session
        this.startTest(url);
        
        // Open URL in new tab
        window.open(url, '_blank');
        
        this.showAlert('✅ Website opened in new tab! Click the bookmarklet there to start capturing.', 'success');
    },
    
    startTest: function(url) {
        var self = this;
        
        this.updateStatus('Starting test...', 'info');
        document.getElementById('test-status').style.display = 'block';
        
        // Call backend to start test session
        fetch('/cypress/start-test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify({ url: url })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                self.sessionId = data.session_id;
                self.isTestRunning = true;
                self.updateStatus('Waiting for bookmarklet activation...', 'running');
                self.updateUIForRunningTest();
                
                // Start polling for events
                self.startPolling();
            }
        })
        .catch(error => {
            console.error('Error starting test:', error);
            self.isTestRunning = true;
            self.updateStatus('Ready - use bookmarklet on website', 'running');
            self.updateUIForRunningTest();
            
            // Start polling even on error
            self.startPolling();
        });
    },
    
    startPolling: function() {
        var self = this;
        
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
        
        this.pollingInterval = setInterval(function() {
            self.fetchLatestEvents();
        }, 2000); // Poll every 2 seconds
        
        console.log('📡 Started polling for events...');
    },
    
    fetchLatestEvents: function() {
        var self = this;
        
        if (!this.sessionId) {
            console.log('⏸️ No session ID, skipping event fetch');
            return;
        }
        
        console.log('🔍 Fetching events for session:', this.sessionId);
        
        fetch('/cypress/get-events?session_id=' + this.sessionId, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            console.log('📦 Events response:', data);
            
            if (data.success && data.events) {
                console.log('✅ Total events in DB:', data.events.length, 'Last displayed:', self.lastEventCount);
                
                // Only display new events
                const newEvents = data.events.slice(self.lastEventCount);
                if (newEvents.length > 0) {
                    console.log('📥 Displaying ' + newEvents.length + ' new events');
                    newEvents.forEach(event => self.displayEvent(event));
                    self.lastEventCount = data.events.length;
                    
                    // Update status
                    if (!self.isTestRunning) {
                        self.isTestRunning = true;
                        self.updateStatus('Capturing events...', 'running');
                        self.updateUIForRunningTest();
                    }
                } else {
                    console.log('⏭️ No new events');
                }
            } else {
                console.log('⚠️ No events data or unsuccessful response');
            }
        })
        .catch(error => {
            console.error('❌ Error fetching events:', error);
        });
    },
    
    displayEvent: function(eventData) {
        var monitor = document.getElementById('event-monitor');
        
        // Remove "no events" placeholder
        var placeholder = monitor.querySelector('div[style*="No events"]');
        if (placeholder) {
            monitor.innerHTML = '';
        }
        
        // Increment count
        this.eventCount++;
        document.getElementById('event-count').textContent = this.eventCount;
        
        // Create event item
        var eventItem = document.createElement('div');
        eventItem.style.cssText = 'background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; padding: 12px; margin-bottom: 8px;';
        
        var timestamp = new Date().toLocaleTimeString();
        var eventType = (eventData.type || 'unknown').toUpperCase();
        
        eventItem.innerHTML = `
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: bold; color: #0284c7;">${eventType}</span>
                <span style="color: #64748b; font-size: 0.625rem;">${timestamp}</span>
            </div>
            <div style="border-left: 2px solid #0ea5e9; padding-left: 8px; font-size: 0.625rem; color: #475569;">
                ${this.formatEventDetails(eventData)}
            </div>
        `;
        
        monitor.appendChild(eventItem);
        monitor.scrollTop = monitor.scrollHeight;
    },
    
    formatEventDetails: function(eventData) {
        var details = [];
        for (var key in eventData) {
            if (key !== 'type' && eventData[key] !== null && eventData[key] !== undefined) {
                var value = eventData[key];
                if (typeof value === 'object') {
                    value = JSON.stringify(value);
                }
                if (value.length > 50) {
                    value = value.substring(0, 50) + '...';
                }
                details.push('<strong>' + key + ':</strong> ' + value);
            }
        }
        return details.join('<br>') || 'No details';
    },
    
    /* OLD IFRAME CODE - COMMENTED OUT
    // ===== OLD IFRAME CODE (Commented out - replaced with bookmarklet) =====
    /*
    loadUrlInIframe: function(url) {
        var self = this;
        var iframe = document.getElementById('test-iframe');
        var placeholder = document.getElementById('iframe-placeholder');
        
        console.log('Loading URL in iframe via proxy:', url);
        
        // Show loading state and hide placeholder
        placeholder.style.display = 'none';
        iframe.style.display = 'block';
        iframe.parentElement.classList.add('iframe-loading');
        
        // Use our proxy to bypass X-Frame-Options restrictions
        var proxyUrl = '/cypress/proxy?url=' + encodeURIComponent(url);
        iframe.src = proxyUrl;
        
        console.log('Iframe src set to proxy URL:', proxyUrl);
        
        // Setup message listener for events from iframe
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'cypress-event') {
                console.log('Received event from iframe:', event.data.data);
                self.captureEvent(event.data.data);
            } else if (event.data && event.data.type === 'cypress-proxy-error') {
                console.error('Proxy error received:', event.data);
                self.handleProxyError(event.data);
            }
        });
        
        // Setup iframe event monitoring
        iframe.onload = function() {
            console.log('Iframe loaded successfully via proxy');
            iframe.parentElement.classList.remove('iframe-loading');
        };
        
        iframe.onerror = function(error) {
            console.log('Iframe failed to load:', error);
            iframe.parentElement.classList.remove('iframe-loading');
            self.showAlert('Failed to load website. Check if URL is accessible.', 'warning');
        };
        
        // Add a timeout to remove loading state if iframe doesn't respond
        setTimeout(function() {
            if (iframe.parentElement.classList.contains('iframe-loading')) {
                console.log('Iframe loading timeout - removing loading state');
                iframe.parentElement.classList.remove('iframe-loading');
            }
        }, 10000); // 10 second timeout
    },
    */
    // ===== END OF OLD IFRAME CODE =====
    
    // ===== OLD IFRAME captureEvent (Commented out - events now polled from server) =====
    /*
    captureEvent: function(eventData) {
        var self = this;
        
        if (!this.isTestRunning || !this.sessionId) return;
        
        fetch('/cypress/capture-event', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify({
                session_id: this.sessionId,
                event: eventData
            })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                self.displayEvent(eventData);
                self.updateEventCount(data.event_count);
            }
        })
        .catch(function(error) {
            console.error('Error capturing event:', error);
        });
    },
    */
    
    // ===== OLD IFRAME handleProxyError (Commented out - no iframe anymore) =====
    /*
    handleProxyError: function(errorData) {
        var self = this;
        var url = errorData.url || 'unknown';
        
        // Show alert with fallback option
        var message = 'The website "' + url + '" cannot be loaded in the iframe due to security restrictions.\n\n' +
                      'Would you like to open it in a new tab instead?';
        
        if (confirm(message)) {
            window.open(url, '_blank');
        }
        
        // Log the error as an event
        this.displayEvent({
            type: 'proxy_error',
            url: url,
            error: errorData.error || 'Unknown error',
            timestamp: new Date().toISOString()
        });
    },
    */
    
    stopTest: function() {
        var self = this;
        
        if (!this.isTestRunning || !this.sessionId) return;
        
        fetch('/cypress/stop-test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify({ session_id: this.sessionId })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                self.isTestRunning = false;
                self.updateUIForStoppedTest();
                self.updateStatus('Test completed - ' + data.event_count + ' events captured', 'success');
                self.showAlert('Test stopped successfully', 'success');
            } else {
                throw new Error(data.message);
            }
        })
        .catch(function(error) {
            console.error('Error stopping test:', error);
            self.showAlert('Failed to stop test: ' + error.message, 'danger');
        });
    },
    
    exportResults: function() {
        if (!this.sessionId) {
            this.showAlert('No test session to export', 'warning');
            return;
        }
        
        try {
            var url = '/cypress/export-results?session_id=' + this.sessionId;
            window.open(url, '_blank');
            this.showAlert('Export started - check your downloads', 'success');
        } catch (error) {
            console.error('Error exporting results:', error);
            this.showAlert('Failed to export results: ' + error.message, 'danger');
        }
    },
    
    clearResults: function() {
        this.sessionId = null;
        this.isTestRunning = false;
        this.eventCount = 0;
        
        // Reset UI
        var iframe = document.getElementById('test-iframe');
        var placeholder = document.getElementById('iframe-placeholder');
        
        iframe.style.display = 'none';
        placeholder.style.display = 'flex';
        iframe.src = '';
        
        this.clearEvents();
        this.updateUIForStoppedTest();
        this.updateStatus('Ready to start', 'info');
        this.updateEventCount(0);
        
        console.log('Results cleared, iframe hidden, placeholder shown');
    },
    
    clearEvents: function() {
        var monitor = document.getElementById('event-monitor');
        monitor.innerHTML = '<div class="text-gray-500 p-4 text-center text-sm">No events captured yet</div>';
    },
    
    openInNewTab: function() {
        var urlInput = document.getElementById('website-url');
        var url = urlInput.value.trim();
        
        if (!url) {
            this.showAlert('Please enter a URL first', 'warning');
            return;
        }
        
        if (!this.isValidUrl(url)) {
            this.showAlert('Please enter a valid URL with https:// or http://', 'warning');
            return;
        }
        
        window.open(url, '_blank', 'noopener,noreferrer');
        this.showAlert('Website opened in new tab', 'success');
    },
    
    updateUIForRunningTest: function() {
        document.getElementById('start-test-btn').disabled = true;
        document.getElementById('stop-test-btn').disabled = false;
        document.getElementById('export-results-btn').disabled = true;
        document.getElementById('open-new-tab-btn').disabled = false;
        document.getElementById('website-url').disabled = true;
    },
    
    updateUIForStoppedTest: function() {
        document.getElementById('start-test-btn').disabled = false;
        document.getElementById('stop-test-btn').disabled = true;
        document.getElementById('export-results-btn').disabled = false;
        document.getElementById('open-new-tab-btn').disabled = true;
        document.getElementById('website-url').disabled = false;
    },
    
    updateStatus: function(message, type) {
        var statusDiv = document.getElementById('test-status');
        var statusText = document.getElementById('status-text');
        
        statusDiv.style.display = 'block';
        statusDiv.className = 'p-4 rounded-md alert-' + (type === 'running' ? 'success' : type);
        statusText.textContent = message;
        
        if (type === 'running') {
            statusText.classList.add('status-running');
        } else {
            statusText.classList.remove('status-running');
        }
    },
    
    updateEventCount: function(count) {
        this.eventCount = count;
        document.getElementById('event-count').textContent = count;
    },
    
    showAlert: function(message, type) {
        // Create temporary alert
        var alertDiv = document.createElement('div');
        alertDiv.className = 'fixed top-5 right-5 z-50 max-w-sm p-4 rounded-md alert-' + type;
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = message + 
            '<button onclick="this.parentNode.remove()" class="ml-2 text-sm underline">×</button>';
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    },
    
    isValidUrl: function(string) {
        try {
            new URL(string);
            return string.indexOf('http://') === 0 || string.indexOf('https://') === 0;
        } catch (_) {
            return false;
        }
    }
};

// Global function for new session button
function startNewSession() {
    // Clear old session
    localStorage.removeItem('cypress_session_id');
    
    cypressTestManager.sessionId = Date.now().toString();
    localStorage.setItem('cypress_session_id', cypressTestManager.sessionId);
    
    document.getElementById('current-session').textContent = cypressTestManager.sessionId;
    cypressTestManager.eventCount = 0;
    cypressTestManager.lastEventCount = 0;
    document.getElementById('event-count').textContent = '0';
    
    // Update bookmarklet
    cypressTestManager.updateBookmarkletLink();
    
    // Clear monitor
    var monitor = document.getElementById('event-monitor');
    monitor.innerHTML = '<div style="color: #6b7280; padding: 16px; text-align: center; font-size: 0.875rem;">No events captured yet</div>';
    
    alert('🎉 New session started!\n\nSession ID: ' + cypressTestManager.sessionId + '\n\nClick the bookmarklet on any website to start capturing!');
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    cypressTestManager.init();
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.backend.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Arpa Nihan_personal\code_study\Testpilot\app\Modules/Cypress/resources/views/index.blade.php ENDPATH**/ ?>