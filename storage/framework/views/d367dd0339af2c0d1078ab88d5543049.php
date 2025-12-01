<?php $__env->startSection('title', $testCase->name); ?>

<?php $__env->startSection('content'); ?>
<!-- Notification Container -->
<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>

<div style="padding: 24px;">
    
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;"><?php echo e($testCase->name); ?></h1>
            <p style="color: #6b7280;">Test Case #<?php echo e($testCase->order); ?> - <?php echo e($project->name); ?></p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="<?php echo e(route('test-cases.edit', [$project, $module, $testCase])); ?>" style="padding: 10px 20px; background: #f59e0b; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?php echo e(route('projects.show', $project)); ?>" style="padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 24px;">
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Order</p>
                <p style="font-weight: 600; color: #1f2937; font-size: 1.25rem;"><?php echo e($testCase->order); ?></p>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Status</p>
                <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;
                    <?php if($testCase->status === 'active'): ?> background: #dcfce7; color: #166534;
                    <?php else: ?> background: #f3f4f6; color: #6b7280;
                    <?php endif; ?>">
                    <?php echo e(ucfirst($testCase->status)); ?>

                </span>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Session ID</p>
                <p style="font-family: monospace; font-weight: 600; color: #1f2937; font-size: 0.875rem;"><?php echo e($testCase->session_id); ?></p>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Events Saved</p>
                <p style="font-weight: 600; color: #16a34a; font-size: 1.25rem;" id="saved-count"><?php echo e($testCase->savedEvents()->count()); ?></p>
            </div>
        </div>

        <?php if($testCase->description): ?>
        <div style="border-top: 1px solid #e5e7eb; padding-top: 16px;">
            <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Description</p>
            <p style="color: #1f2937;"><?php echo e($testCase->description); ?></p>
        </div>
        <?php endif; ?>
    </div>

    
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0;">Event Capture</h2>
            <div style="display: flex; gap: 8px;">
                <a href="<?php echo e(route('test-cases.capture-instructions', [$project, $module, $testCase])); ?>" target="_blank" style="padding: 8px 16px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 0.875rem;">
                    <i class="fas fa-info-circle"></i> Setup Instructions
                </a>
                <button id="live-capture-btn" onclick="toggleLiveCapture()" style="padding: 8px 16px; background: #16a34a; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 0.875rem;">
                    <i class="fas fa-play"></i> Start Live Capture
                </button>
                <button onclick="saveAllEvents()" style="padding: 8px 16px; background: #7c3aed; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 0.875rem;">
                    <i class="fas fa-save"></i> Save Events
                </button>
                <button onclick="clearUnsavedEvents()" style="padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 0.875rem;">
                    <i class="fas fa-trash"></i> Clear Unsaved
                </button>
                <button id="delete-selected-btn" onclick="deleteSelectedEvents()" style="display: none; padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 0.875rem;">
                    <i class="fas fa-trash-alt"></i> Delete Selected (<span id="selected-count">0</span>)
                </button>
            </div>
        </div>

        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; margin-bottom: 16px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 4px 0;">Saved Events</p>
                    <p id="saved-count-display" style="font-family: monospace; font-weight: 600; color: #16a34a; margin: 0; font-size: 1.5rem;">0</p>
                </div>
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 4px 0;">Unsaved (Live)</p>
                    <p id="unsaved-count" style="font-family: monospace; font-weight: 600; color: #f59e0b; margin: 0; font-size: 1.5rem;">0</p>
                </div>
                <div>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 4px 0;">Live Status</p>
                    <p id="live-status" style="font-family: monospace; font-weight: 600; color: #6b7280; margin: 0;">Stopped</p>
                </div>
            </div>
        </div>

        
        <div style="margin-bottom: 16px; border-bottom: 2px solid #e5e7eb;">
            <div style="display: flex; gap: 4px; justify-content: space-between; align-items: center;">
                <div style="display: flex; gap: 4px;">
                    <button onclick="switchTab('unsaved')" id="tab-unsaved" style="padding: 12px 24px; background: #f59e0b; color: white; border: none; border-radius: 8px 8px 0 0; cursor: pointer; font-weight: 600; font-size: 0.875rem;">
                        <i class="fas fa-clock"></i> Unsaved (Live) (<span id="tab-unsaved-count">0</span>)
                    </button>
                    <button onclick="switchTab('saved')" id="tab-saved" style="padding: 12px 24px; background: #e5e7eb; color: #6b7280; border: none; border-radius: 8px 8px 0 0; cursor: pointer; font-weight: 600; font-size: 0.875rem;">
                        <i class="fas fa-save"></i> Saved Events (<span id="tab-saved-count">0</span>)
                    </button>
                </div>
                <div id="saved-actions" style="display: none; padding: 8px 0;">
                    <label style="display: inline-flex; align-items: center; cursor: pointer; padding: 6px 12px; background: #f3f4f6; border-radius: 6px; font-size: 0.875rem; font-weight: 500;">
                        <input type="checkbox" id="select-all-saved" onchange="toggleSelectAll()" style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                        Select All
                    </label>
                </div>
            </div>
        </div>

        
        <div id="monitor-unsaved" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; max-height: 600px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 0.875rem;">
            <div style="color: #9ca3af; text-align: center; padding: 40px;">
                <i class="fas fa-clock" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p style="font-size: 1.125rem; margin: 0;">No unsaved events yet</p>
                <p style="margin-top: 8px;">Use the extension/bookmarklet to capture events - they appear here in real-time</p>
            </div>
        </div>

        
        <div id="monitor-saved" style="display: none; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; max-height: 600px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 0.875rem;">
            <div style="color: #9ca3af; text-align: center; padding: 40px;">
                <i class="fas fa-save" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p style="font-size: 1.125rem; margin: 0;">No saved events yet</p>
                <p style="margin-top: 8px;">Capture events using the extension/bookmarklet, then click "Save Events"</p>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<?php $__env->startPush('styles'); ?>
<style>
/* Notification Styles */
.notification {
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    padding: 16px 20px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 300px;
    animation: slideIn 0.3s ease-out, fadeOut 0.3s ease-in 4.7s;
    border-left: 4px solid;
    position: relative;
    overflow: hidden;
}

.notification::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: currentColor;
    animation: progress 5s linear;
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

.notification-success {
    border-left-color: #16a34a;
    color: #16a34a;
}

.notification-error {
    border-left-color: #dc2626;
    color: #dc2626;
}

.notification-warning {
    border-left-color: #f59e0b;
    color: #f59e0b;
}

.notification-info {
    border-left-color: #3b82f6;
    color: #3b82f6;
}

.notification-icon {
    font-size: 24px;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    margin: 0 0 4px 0;
    color: #1f2937;
}

.notification-message {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.notification-close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    font-size: 20px;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
}

.notification-close:hover {
    background: #f3f4f6;
    color: #1f2937;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const sessionId = '<?php echo e($testCase->session_id); ?>';
let pollingInterval = null;
let lastEventCount = 0;
let currentTab = 'unsaved'; // Start with unsaved tab
let selectedEventIds = new Set(); // Track selected events for deletion

// Notification System
function showNotification(type, title, message) {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
    };

    notification.innerHTML = `
        <div class="notification-icon">${icons[type]}</div>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;

    container.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Tab switching function
function switchTab(tab) {
    currentTab = tab;
    const savedTab = document.getElementById('tab-saved');
    const unsavedTab = document.getElementById('tab-unsaved');
    const savedMonitor = document.getElementById('monitor-saved');
    const unsavedMonitor = document.getElementById('monitor-unsaved');
    const savedActions = document.getElementById('saved-actions');
    const deleteBtn = document.getElementById('delete-selected-btn');

    if (tab === 'saved') {
        savedTab.style.background = '#16a34a';
        savedTab.style.color = 'white';
        unsavedTab.style.background = '#e5e7eb';
        unsavedTab.style.color = '#6b7280';
        savedMonitor.style.display = 'block';
        unsavedMonitor.style.display = 'none';
        savedActions.style.display = 'block';
    } else {
        unsavedTab.style.background = '#f59e0b';
        unsavedTab.style.color = 'white';
        savedTab.style.background = '#e5e7eb';
        savedTab.style.color = '#6b7280';
        unsavedMonitor.style.display = 'block';
        savedMonitor.style.display = 'none';
        savedActions.style.display = 'none';
        deleteBtn.style.display = 'none';
        selectedEventIds.clear();
    }
}

// Load saved events on page load
loadAllEvents();

function loadAllEvents() {
    fetch('<?php echo e(route("test-cases.events.get", [$project, $module, $testCase])); ?>', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.events) {
            displayAllEvents(data.events);
            updateCounts(data.total, data.saved, data.unsaved);
        }
    })
    .catch(error => {
        console.error('Error loading events:', error);
    });
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

    if (eventJson?.ariaLabel) {
        return `<strong style="color: #dc2626;">${tagName.toUpperCase()}</strong> labeled: <code>${eventJson.ariaLabel}</code>`;
    }

    if (eventJson?.placeholder) {
        return `<strong style="color: #ea580c;">${tagName.toUpperCase()}</strong> placeholder: <code>${eventJson.placeholder}</code>`;
    }

    if (eventJson?.selectors?.className) {
        const firstClass = eventJson.selectors.className.split(' ')[0];
        return `<strong style="color: #8b5cf6;">${tagName.toUpperCase()}</strong> with class: <code>.${firstClass}</code>`;
    }

    if (tagName) {
        return `<strong style="color: #6366f1;">${tagName.toUpperCase()}</strong> element`;
    }

    return null;
}

function displayAllEvents(events) {
    const savedMonitor = document.getElementById('monitor-saved');
    const unsavedMonitor = document.getElementById('monitor-unsaved');

    // Clear both monitors
    savedMonitor.innerHTML = '';
    unsavedMonitor.innerHTML = '';

    // Filter events - NO reverse, keep original order (oldest first)
    const savedEvents = events.filter(e => e.is_saved);
    const unsavedEvents = events.filter(e => !e.is_saved);

    // Display saved events with DESC numbering (first event = highest number)
    if (savedEvents.length === 0) {
        savedMonitor.innerHTML = `
            <div style="color: #9ca3af; text-align: center; padding: 40px;">
                <i class="fas fa-save" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p style="font-size: 1.125rem; margin: 0;">No saved events yet</p>
                <p style="margin-top: 8px;">Capture events using the extension/bookmarklet, then click "Save Events"</p>
            </div>
        `;
    } else {
        savedEvents.forEach((event, index) => {
            // Number in descending order: first event gets total count, last gets 1
            const eventNumber = savedEvents.length - index;
            displayEvent(event, eventNumber, 'saved', false);
        });
    }

    // Display unsaved events with DESC numbering (first event = highest number)
    if (unsavedEvents.length === 0) {
        unsavedMonitor.innerHTML = `
            <div style="color: #9ca3af; text-align: center; padding: 40px;">
                <i class="fas fa-clock" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                <p style="font-size: 1.125rem; margin: 0;">No unsaved events yet</p>
                <p style="margin-top: 8px;">Use the extension/bookmarklet to capture events - they appear here in real-time</p>
            </div>
        `;
    } else {
        unsavedEvents.forEach((event, index) => {
            // Number in descending order: first event gets total count, last gets 1
            const eventNumber = unsavedEvents.length - index;
            displayEvent(event, eventNumber, 'unsaved', false);
        });
    }

    // Update tab counts
    document.getElementById('tab-saved-count').textContent = savedEvents.length;
    document.getElementById('tab-unsaved-count').textContent = unsavedEvents.length;
}

function displayEvent(eventData, number = null, tabType = 'saved', scrollToBottom = true) {
    const monitor = tabType === 'saved' ? document.getElementById('monitor-saved') : document.getElementById('monitor-unsaved');

    // Remove placeholder if exists
    const placeholder = monitor.querySelector('.text-center');
    if (placeholder) {
        monitor.innerHTML = '';
    }

    const eventItem = document.createElement('div');
    const bgColor = tabType === 'saved' ? '#f0fdf4' : '#fef3c7';
    const borderColor = tabType === 'saved' ? '#86efac' : '#fcd34d';
    const badgeColor = tabType === 'saved' ? '#16a34a' : '#f59e0b';

    eventItem.style.cssText = `background: ${bgColor}; border: 1px solid ${borderColor}; border-radius: 4px; padding: 12px; margin-bottom: 8px; position: relative;`;

    const timestamp = new Date(eventData.created_at).toLocaleString();
    const eventNumber = number ? `<span style="background: ${badgeColor}; color: white; padding: 2px 8px; border-radius: 4px; font-weight: bold; margin-right: 8px;">#${number}</span>` : '';

    // Add checkbox only for saved events
    const checkbox = tabType === 'saved' ?
        `<input type="checkbox" class="event-checkbox" data-event-id="${eventData.id}" onchange="updateSelectedCount()" style="width: 18px; height: 18px; cursor: pointer; margin-right: 8px;">`
        : '';

    // Create user-friendly event title
    const eventTitle = formatEventTitle(eventData);

    eventItem.innerHTML = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; align-items: center;">
            <div style="display: flex; align-items: center;">
                ${checkbox}
                ${eventNumber}
                <span style="font-weight: bold; color: #2563eb;">${eventTitle}</span>
            </div>
            <span style="color: #6b7280; font-size: 0.75rem;">${timestamp}</span>
        </div>
        <div style="border-left: 2px solid #3b82f6; padding-left: 12px; font-size: 0.75rem; color: #4b5563; ${checkbox ? 'margin-left: 26px;' : ''}">
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

    // Parse event_data JSON if available
    let eventJson = null;
    try {
        eventJson = typeof eventData.event_data === 'string' ? JSON.parse(eventData.event_data) : eventData.event_data;
    } catch(e) {}

    // Get meaningful text content
    const text = eventData.inner_text || eventJson?.innerText || '';
    const value = eventData.value || eventJson?.value || '';
    const tagName = eventData.tag_name || eventJson?.tagName || '';

    // Create user-friendly titles based on event type and context
    switch(eventType.toLowerCase()) {
        case 'click':
            // For clicks, show what was clicked
            if (text && text.trim().length > 0) {
                const displayText = text.substring(0, 40) + (text.length > 40 ? '...' : '');
                return `🖱️ CLICK: "${displayText}"`;
            }
            if (eventJson?.ariaLabel) {
                return `🖱️ CLICK: ${eventJson.ariaLabel}`;
            }
            if (eventJson?.alt) {
                return `🖱️ CLICK: Image "${eventJson.alt}"`;
            }
            if (eventJson?.placeholder) {
                return `🖱️ CLICK: ${eventJson.placeholder}`;
            }
            if (eventJson?.title) {
                return `🖱️ CLICK: ${eventJson.title}`;
            }
            // Show tag and selector info if no text
            if (eventJson?.selectors?.id) {
                return `🖱️ CLICK: ${tagName.toUpperCase()} #${eventJson.selectors.id}`;
            }
            if (eventJson?.selectors?.name) {
                return `🖱️ CLICK: ${tagName.toUpperCase()} [name="${eventJson.selectors.name}"]`;
            }
            if (eventJson?.selectors?.testId) {
                return `🖱️ CLICK: ${tagName.toUpperCase()} [data-testid="${eventJson.selectors.testId}"]`;
            }
            return `🖱️ CLICK: ${tagName.toUpperCase()}`;

        case 'input':
            // For inputs, show field name and value
            if (eventJson?.selectors?.name || eventJson?.selectors?.id) {
                const fieldName = eventJson.selectors.name || eventJson.selectors.id;
                if (value) {
                    const displayValue = value.substring(0, 20) + (value.length > 20 ? '...' : '');
                    return `⌨️ INPUT: ${fieldName} = "${displayValue}"`;
                }
                return `⌨️ INPUT: ${fieldName}`;
            }
            if (eventJson?.placeholder) {
                return `⌨️ INPUT: ${eventJson.placeholder}`;
            }
            return `⌨️ INPUT: ${tagName.toUpperCase()}`;

        case 'change':
            // For change events (select, checkbox, radio)
            if (eventJson?.selectedText) {
                return `🔄 SELECT: "${eventJson.selectedText}"`;
            }
            if (eventJson?.checked !== undefined) {
                const state = eventJson.checked ? 'Checked' : 'Unchecked';
                const fieldName = eventJson?.selectors?.name || eventJson?.selectors?.id || tagName;
                return `☑️ CHECKBOX: ${fieldName} (${state})`;
            }
            if (value) {
                const displayValue = value.substring(0, 30) + (value.length > 30 ? '...' : '');
                return `🔄 CHANGE: "${displayValue}"`;
            }
            return `🔄 CHANGE: ${tagName.toUpperCase()}`;

        case 'file':
            // For file uploads
            if (eventJson?.fileNames && eventJson.fileNames.length > 0) {
                const fileList = eventJson.fileNames.join(', ');
                const displayFiles = fileList.substring(0, 40) + (fileList.length > 40 ? '...' : '');
                return `📎 FILE UPLOAD: ${displayFiles}`;
            }
            return `📎 FILE UPLOAD`;

        case 'submit':
        case 'form_submit':
            // For form submissions
            if (eventJson?.selectors?.id) {
                return `📤 SUBMIT FORM: #${eventJson.selectors.id}`;
            }
            if (eventJson?.action) {
                const actionPath = eventJson.action.split('/').pop() || eventJson.action;
                return `📤 SUBMIT: ${actionPath}`;
            }
            return `📤 SUBMIT FORM`;

        default:
            return `${eventType.toUpperCase()}`;
    }
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
    document.getElementById('saved-count-display').textContent = saved;
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
    btn.innerHTML = '<i class="fas fa-stop"></i> Stop Live Capture';
    btn.style.background = '#dc2626';

    document.getElementById('live-status').textContent = 'Active';
    document.getElementById('live-status').style.color = '#16a34a';

    pollingInterval = setInterval(() => {
        fetch('<?php echo e(route("test-cases.events.get", [$project, $module, $testCase])); ?>', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.events) {
                // Reload all events to update the list
                displayAllEvents(data.events);
                updateCounts(data.total, data.saved, data.unsaved);
            }
        })
        .catch(error => console.error('Error:', error));
    }, 1000);

    showNotification('success', 'Live Capture Started', 'Events will update automatically every second.');
}

function stopLiveCapture() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;

        const btn = document.getElementById('live-capture-btn');
        btn.innerHTML = '<i class="fas fa-play"></i> Start Live Capture';
        btn.style.background = '#16a34a';

        document.getElementById('live-status').textContent = 'Stopped';
        document.getElementById('live-status').style.color = '#6b7280';

        showNotification('info', 'Live Capture Stopped', 'Event monitoring has been paused.');
    }
}

function saveAllEvents() {
    const unsavedCount = parseInt(document.getElementById('unsaved-count').textContent);

    if (unsavedCount === 0) {
        showNotification('info', 'No Unsaved Events', 'There are no unsaved events to save.');
        return;
    }

    fetch('<?php echo e(route("test-cases.events.save", [$project, $module, $testCase])); ?>', {
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
            showNotification('success', 'Events Saved', `Successfully saved ${data.saved} event(s)`);
            loadAllEvents();
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

    fetch('<?php echo e(route("test-cases.events.clear", [$project, $module, $testCase])); ?>', {
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
            loadAllEvents();
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

    fetch('<?php echo e(route("test-cases.events.delete", [$project, $module, $testCase])); ?>', {
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
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Arpa\business automation ltd\sadi vai\Testpilot\app\Modules/Cypress/resources/views/test-cases/show.blade.php ENDPATH**/ ?>