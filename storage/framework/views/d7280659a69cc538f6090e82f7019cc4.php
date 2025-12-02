<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Cypress Testing Interface</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div style="padding: 24px;">
    
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: start;">
        <div>
            <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;">Cypress Testing</h1>
            <p style="color: #6b7280;">Test websites and capture user interactions</p>
        </div>
        <a href="<?php echo e(route('cypress.bookmarklet')); ?>" target="_blank" style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <i class="fas fa-bookmark"></i>
            <span>Use Bookmarklet<br><small style="font-size: 0.75rem; opacity: 0.9;">(Works on ANY site!)</small></span>
        </a>
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
                       value="">
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

    
    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 24px; min-height: 700px;">
        
        <div style="display: flex; flex-direction: column;">
            <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden; display: flex; flex-direction: column; height: 100%;">
                <div style="padding: 16px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin: 0;">Website Preview</h3>
                </div>
                <div style="position: relative; background: #f9fafb; flex: 1; overflow: hidden; min-height: 650px;">
                    <div id="iframe-placeholder" style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6b7280; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1;">
                        <div style="text-align: center;">
                            <i class="fas fa-globe" style="font-size: 3rem; margin-bottom: 16px; color: #9ca3af;"></i>
                            <p style="font-size: 1.125rem; margin: 0;">Enter a URL and click "Go" to start testing</p>
                        </div>
                    </div>
                    <iframe id="test-iframe"
                            style="width: 100%; height: 100%; border: none; display: none; position: absolute; top: 0; left: 0; z-index: 2;"
                            sandbox="allow-scripts allow-forms allow-popups allow-top-navigation allow-same-origin">
                    </iframe>
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
</div>

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

<script>
// Cypress Test Manager
var cypressTestManager = {
    sessionId: null,
    isTestRunning: false,
    eventCount: 0,
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

    init: function() {
        var self = this;

        // Start test button
        document.getElementById('start-test-btn').addEventListener('click', function() {
            self.startTest();
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

        // Open in new tab button
        document.getElementById('open-new-tab-btn').addEventListener('click', function() {
            self.openInNewTab();
        });

        // URL input enter key
        document.getElementById('website-url').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                self.startTest();
            }
        });
    },

    startTest: function() {
        var self = this;
        var urlInput = document.getElementById('website-url');
        var url = urlInput.value.trim();

        console.log('startTest called with URL:', url);

        if (!url) {
            this.showAlert('Please enter a valid URL', 'warning');
            return;
        }

        if (!this.isValidUrl(url)) {
            this.showAlert('Please enter a valid URL with https:// or http://', 'warning');
            return;
        }

        this.updateStatus('Starting test...', 'info');

        // Load iframe immediately (don't wait for backend)
        this.loadUrlInIframe(url);
        this.updateUIForRunningTest();

        // Call backend to start test session (in parallel)
        fetch('/cypress/start-test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify({ url: url })
        })
        .then(function(response) {
            console.log('Backend response:', response);
            return response.json();
        })
        .then(function(data) {
            console.log('Backend data:', data);
            if (data.success) {
                self.sessionId = data.session_id;
                self.isTestRunning = true;
                self.updateStatus('Test running - interact with the website', 'running');
            } else {
                console.error('Backend error:', data.message);
                self.showAlert('Backend error: ' + data.message, 'warning');
                // Still allow iframe testing even if backend fails
                self.isTestRunning = true;
                self.updateStatus('Test running (backend disabled) - interact with the website', 'running');
            }
        })
        .catch(function(error) {
            console.error('Error starting test:', error);
            self.showAlert('Backend unavailable, continuing with iframe only', 'warning');
            // Still allow iframe testing even if backend fails
            self.isTestRunning = true;
            self.updateStatus('Test running (backend disabled) - interact with the website', 'running');
        });
    },

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

    displayEvent: function(eventData) {
        var monitor = document.getElementById('event-monitor');

        // Remove placeholder if exists
        var placeholder = monitor.querySelector('.text-gray-500');
        if (placeholder) {
            monitor.innerHTML = '';
        }

        var eventItem = document.createElement('div');
        eventItem.className = 'event-item';

        var timestamp = new Date().toLocaleTimeString();

        eventItem.innerHTML =
            '<div class="event-type">' + eventData.type.toUpperCase() + '</div>' +
            '<div class="event-time">' + timestamp + '</div>' +
            '<div class="event-details">' + this.formatEventDetails(eventData) + '</div>';

        monitor.appendChild(eventItem);
        monitor.scrollTop = monitor.scrollHeight;
    },

    formatEventDetails: function(eventData) {
        var details = [];
        for (var key in eventData) {
            if (key !== 'type') {
                details.push('<strong>' + key + ':</strong> ' + JSON.stringify(eventData[key]));
            }
        }
        return details.join('<br>') || 'No additional details';
    },

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

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    cypressTestManager.init();
});
</script>
</body>
</html>
<?php /**PATH E:\Arpa Nihan_personal\code_study\Testpilot\app\Modules/Cypress/resources/views/standalone.blade.php ENDPATH**/ ?>