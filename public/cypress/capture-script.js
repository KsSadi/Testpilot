/**
 * Testpilot Event Capture Script
 */
(function() {
    const scriptTag = document.currentScript || document.querySelector('script[src*="capture-script.js"]');
    const scriptSrc = scriptTag ? scriptTag.src : '';
    const sessionMatch = scriptSrc.match(/[?&]session=([^&]+)/);
    let sessionId = sessionMatch ? decodeURIComponent(sessionMatch[1]) : null;

    if (!sessionId) {
        console.error('🔴 No session ID provided');
        return;
    }

    if (window.__testpilotCaptureActive) {
        console.log('✅ Already active!');
        return;
    }
    window.__testpilotCaptureActive = true;
    window.__testpilotCapturePaused = false;

    console.log('🚀 Testpilot Started!');
    console.log('📋 Session:', sessionId);

    const scriptUrlObj = new URL(scriptSrc);
    const SERVER_URL = scriptUrlObj.origin;
    const CAPTURE_ENDPOINT = SERVER_URL + '/cypress/capture-event-bookmarklet';

    console.log('🌐 Server:', SERVER_URL);

    let eventCounter = 0;
    const isFromExtension = localStorage.getItem('cypress_auto_inject') === 'true';
    console.log('🔌 Extension:', isFromExtension);

    // Create floating counter widget (only for bookmarklet, not extension)
    let counterWidget = null;
    if (!isFromExtension) {
        counterWidget = document.createElement('div');
        counterWidget.id = 'testpilot-counter-widget';
        counterWidget.innerHTML = `
            <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; position: fixed; bottom: 20px; right: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 16px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 999999; font-size: 14px; font-weight: 600; min-width: 160px; text-align: center; user-select: none; transition: transform 0.15s ease-out;">
                <div style="cursor: move;" id="testpilot-drag-handle">
                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 4px;">🎯 Testpilot</div>
                    <div style="font-size: 24px; font-weight: bold; margin: 4px 0; transition: transform 0.15s ease-out;" id="testpilot-event-count">0</div>
                    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 8px;">Events Captured</div>
                </div>
                <div style="display: flex; gap: 6px; margin-bottom: 8px;">
                    <button id="testpilot-pause-btn" style="flex: 1; padding: 6px 10px; background: rgba(251, 191, 36, 0.9); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: 600; transition: all 0.2s; transform: scale(1);">
                        ⏸ Pause
                    </button>
                    <button id="testpilot-stop-btn" style="flex: 1; padding: 6px 10px; background: rgba(239, 68, 68, 0.9); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: 600; transition: all 0.2s; transform: scale(1);">
                        ⏹ Stop
                    </button>
                </div>
                <div style="padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.3); font-size: 10px; opacity: 0.8;">
                    <span id="testpilot-status" style="color: #4ade80; transition: color 0.3s;">● Recording</span>
                </div>
            </div>
        `;
        document.body.appendChild(counterWidget);

        // Get button elements
        const pauseBtn = document.getElementById('testpilot-pause-btn');
        const stopBtn = document.getElementById('testpilot-stop-btn');
        const dragHandle = document.getElementById('testpilot-drag-handle');

        // Pause/Resume button handler
        pauseBtn.addEventListener('click', () => {
            window.__testpilotCapturePaused = !window.__testpilotCapturePaused;
            const isPaused = window.__testpilotCapturePaused;
            
            const statusElement = document.getElementById('testpilot-status');
            if (isPaused) {
                pauseBtn.innerHTML = '▶️ Resume';
                pauseBtn.style.background = 'rgba(34, 197, 94, 0.9)';
                statusElement.innerHTML = '⏸ Paused';
                statusElement.style.color = '#fbbf24';
                console.log('⏸ Capture paused');
            } else {
                pauseBtn.innerHTML = '⏸ Pause';
                pauseBtn.style.background = 'rgba(251, 191, 36, 0.9)';
                statusElement.innerHTML = '● Recording';
                statusElement.style.color = '#4ade80';
                console.log('▶️ Capture resumed');
            }
        });

        // Stop button handler
        stopBtn.addEventListener('click', () => {
            if (confirm('⏹ Stop event capture?\n\nThis will remove the widget and stop capturing events.\n\nTotal events captured: ' + eventCounter)) {
                window.__testpilotCaptureActive = false;
                window.__testpilotCapturePaused = false;
                counterWidget.remove();
                counterWidget = null;
                console.log('⏹ Capture stopped - widget removed');
                console.log('💡 Click bookmarklet again to restart');
            }
        });

        // Hover effects
        pauseBtn.addEventListener('mouseenter', () => {
            pauseBtn.style.transform = 'scale(1.05)';
        });
        pauseBtn.addEventListener('mouseleave', () => {
            pauseBtn.style.transform = 'scale(1)';
        });
        
        stopBtn.addEventListener('mouseenter', () => {
            stopBtn.style.transform = 'scale(1.05)';
        });
        stopBtn.addEventListener('mouseleave', () => {
            stopBtn.style.transform = 'scale(1)';
        });

        // Make widget draggable (only from drag handle area)
        let isDragging = false;
        let currentX, currentY, initialX, initialY;
        const widget = counterWidget.firstElementChild;

        dragHandle.addEventListener('mousedown', (e) => {
            isDragging = true;
            initialX = e.clientX - (parseInt(widget.style.right) || 20);
            initialY = e.clientY - (parseInt(widget.style.bottom) || 20);
            dragHandle.style.cursor = 'grabbing';
        });

        document.addEventListener('mousemove', (e) => {
            if (isDragging) {
                e.preventDefault();
                widget.style.right = (window.innerWidth - e.clientX - 80) + 'px';
                widget.style.bottom = (window.innerHeight - e.clientY - 60) + 'px';
            }
        });

        document.addEventListener('mouseup', () => {
            if (isDragging) {
                isDragging = false;
                dragHandle.style.cursor = 'move';
            }
        });

        console.log('✅ Counter widget created with controls');
    }

    // Track last event to prevent duplicates
    let lastEventData = null;
    let lastEventTime = 0;
    const DEBOUNCE_THRESHOLD = 100; // ms

    function sendEvent(eventData) {
        if (window.__testpilotCapturePaused) {
            console.log('⏸ Event skipped - paused');
            return;
        }
        if (!window.__testpilotCaptureActive) {
            console.log('⏹ Event skipped - stopped');
            return;
        }

        // Prevent duplicate events (debouncing)
        const now = Date.now();
        const eventSignature = eventData.type + eventData.cypressSelector + (eventData.value || '');
        if (lastEventData === eventSignature && (now - lastEventTime) < DEBOUNCE_THRESHOLD) {
            console.log('⚠️ Duplicate event skipped:', eventData.type);
            return;
        }
        lastEventData = eventSignature;
        lastEventTime = now;

        eventCounter++;

        eventData.session_id = sessionId;
        eventData.url = window.location.href;
        eventData.timestamp = new Date().toISOString();

        console.log('📤 Event #' + eventCounter + ':', eventData.type);

        fetch(CAPTURE_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ event: eventData })
        })
        .then(r => r.json())
        .then(() => {
            console.log('✅ Captured');
            
            // Update floating widget counter (bookmarklet only)
            if (counterWidget) {
                const countElement = document.getElementById('testpilot-event-count');
                if (countElement) {
                    countElement.textContent = eventCounter;
                    // Pulse animation
                    countElement.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        countElement.style.transform = 'scale(1)';
                    }, 150);
                }
            }
            
            window.dispatchEvent(new CustomEvent('testpilot-event-captured', {
                detail: { count: eventCounter },
                bubbles: true
            }));
            console.log('📊 Count:', eventCounter);
        })
        .catch(e => console.error('❌ Capture error:', e));
    }

    function getElementData(el, type) {
        // Generate multiple selector strategies for Cypress
        const selectors = {
            id: el.id || null,
            name: el.name || null,
            className: el.className || null,
            type: el.type || null,
            testId: el.getAttribute('data-testid') || el.getAttribute('data-test') || null,
            ariaLabel: el.getAttribute('aria-label') || null,
            placeholder: el.placeholder || null,
            label: getAssociatedLabel(el),
            xpath: getXPath(el)
        };

        // Determine best selector for Cypress
        let cypressSelector = null;
        if (selectors.testId) cypressSelector = `[data-testid="${selectors.testId}"]`;
        else if (selectors.id) cypressSelector = `#${selectors.id}`;
        else if (selectors.name) cypressSelector = `[name="${selectors.name}"]`;
        else if (selectors.ariaLabel) cypressSelector = `[aria-label="${selectors.ariaLabel}"]`;
        else if (selectors.placeholder) cypressSelector = `[placeholder="${selectors.placeholder}"]`;
        else cypressSelector = selectors.xpath;

        return {
            type: type,
            tagName: el.tagName,
            selectors: selectors,
            cypressSelector: cypressSelector,
            text: (el.textContent || '').trim().substring(0, 100),
            innerText: (el.innerText || '').trim().substring(0, 100),
            value: el.value || null,
            checked: el.checked || null,
            href: el.href || null,
            src: el.src || null,
            alt: el.alt || null,
            title: el.title || null,
            role: el.getAttribute('role') || null,
            pageUrl: window.location.href,
            // For file uploads
            accept: el.accept || null,
            multiple: el.multiple || null,
            // For forms
            action: el.action || null,
            method: el.method || null
        };
    }

    // Get associated label for form elements
    function getAssociatedLabel(el) {
        // Check if element has an id and there's a label with for attribute
        if (el.id) {
            const label = document.querySelector(`label[for="${el.id}"]`);
            if (label) return label.textContent.trim();
        }

        // Check if element is wrapped in a label
        let parent = el.parentElement;
        while (parent && parent.tagName !== 'BODY') {
            if (parent.tagName === 'LABEL') {
                return parent.textContent.trim();
            }
            parent = parent.parentElement;
        }

        return null;
    }

    // Generate XPath for element
    function getXPath(el) {
        if (el.id) return `//*[@id="${el.id}"]`;
        if (el === document.body) return '/html/body';

        let path = '';
        while (el && el.nodeType === 1) {
            let index = 0;
            let sibling = el.previousSibling;
            while (sibling) {
                if (sibling.nodeType === 1 && sibling.tagName === el.tagName) index++;
                sibling = sibling.previousSibling;
            }

            const tagName = el.tagName.toLowerCase();
            const pathIndex = index > 0 ? `[${index + 1}]` : '';
            path = `/${tagName}${pathIndex}${path}`;

            el = el.parentNode;
        }
        return path;
    }

    // Track which elements have been handled to avoid duplicates
    let handledInputs = new WeakMap();

    // Click events - capture meaningful clicks only
    document.addEventListener('click', e => {
        const el = e.target;
        const tagName = el.tagName.toLowerCase();

        // Only capture clicks on interactive elements
        if (tagName === 'button' || tagName === 'a' || el.onclick ||
            el.getAttribute('role') === 'button' || el.classList.contains('btn') ||
            tagName === 'input' && (el.type === 'submit' || el.type === 'button' || el.type === 'reset')) {

            const data = getElementData(el, 'click');

            // Capture navigation intent for links
            if (tagName === 'a' && el.href) {
                data.targetUrl = el.href;
                data.isExternal = el.hostname !== window.location.hostname;
            }

            sendEvent(data);
        }
    }, false);

    // Input events - only capture when user is typing (debounced by sendEvent)
    document.addEventListener('input', e => {
        const el = e.target;
        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
            const data = getElementData(el, 'input');
            sendEvent(data);
        }
    }, false);

    // Change events - handle different input types
    document.addEventListener('change', e => {
        const el = e.target;
        const tagName = el.tagName.toLowerCase();

        // File uploads
        if (el.type === 'file' && el.files && el.files.length > 0) {
            const data = getElementData(el, 'file_upload');
            data.fileCount = el.files.length;
            data.fileNames = Array.from(el.files).map(f => f.name);
            data.fileTypes = Array.from(el.files).map(f => f.type);
            sendEvent(data);
            return;
        }

        // Select dropdowns
        if (tagName === 'select') {
            const data = getElementData(el, 'select');
            data.selectedIndex = el.selectedIndex;
            data.selectedValue = el.value;
            data.selectedText = el.options[el.selectedIndex]?.text || '';
            sendEvent(data);
            return;
        }

        // Checkbox/Radio
        if (el.type === 'checkbox' || el.type === 'radio') {
            const data = getElementData(el, el.type);
            data.checked = el.checked;
            sendEvent(data);
            return;
        }

        // Other inputs (not already handled by input event)
        if (tagName === 'input' && el.type !== 'text' && el.type !== 'email' && el.type !== 'password') {
            const data = getElementData(el, 'change');
            sendEvent(data);
        }
    }, false);

    // Capture form submissions
    document.addEventListener('submit', e => {
        const data = getElementData(e.target, 'form_submit');
        sendEvent(data);
    }, false);

    // Capture navigation events
    let lastUrl = window.location.href;
    const checkUrlChange = () => {
        const currentUrl = window.location.href;
        if (currentUrl !== lastUrl) {
            console.log('🔄 Navigation detected:', currentUrl);
            sendEvent({
                type: 'navigation',
                url: currentUrl,
                previousUrl: lastUrl,
                pageUrl: currentUrl,
                timestamp: new Date().toISOString()
            });
            lastUrl = currentUrl;
        }
    };

    // Monitor URL changes for SPAs
    window.addEventListener('popstate', checkUrlChange);
    window.addEventListener('hashchange', checkUrlChange);

    // Intercept pushState and replaceState for SPA navigation
    const originalPushState = history.pushState;
    const originalReplaceState = history.replaceState;

    history.pushState = function() {
        originalPushState.apply(this, arguments);
        checkUrlChange();
    };

    history.replaceState = function() {
        originalReplaceState.apply(this, arguments);
        checkUrlChange();
    };

    // Listen for pause/stop messages from content script
    window.addEventListener('message', function(event) {
        if (event.source !== window) return;

        if (event.data.type === 'TESTPILOT_UPDATE_PAUSE') {
            window.__testpilotCapturePaused = event.data.isPaused;
            console.log('⏸ Pause state changed:', event.data.isPaused);
            
            // Update widget status and button
            if (counterWidget) {
                const statusElement = document.getElementById('testpilot-status');
                const pauseBtn = document.getElementById('testpilot-pause-btn');
                
                if (statusElement && pauseBtn) {
                    if (event.data.isPaused) {
                        pauseBtn.innerHTML = '▶️ Resume';
                        pauseBtn.style.background = 'rgba(34, 197, 94, 0.9)';
                        statusElement.innerHTML = '⏸ Paused';
                        statusElement.style.color = '#fbbf24';
                    } else {
                        pauseBtn.innerHTML = '⏸ Pause';
                        pauseBtn.style.background = 'rgba(251, 191, 36, 0.9)';
                        statusElement.innerHTML = '● Recording';
                        statusElement.style.color = '#4ade80';
                    }
                }
            }
        }

        if (event.data.type === 'TESTPILOT_STOP_CAPTURE') {
            window.__testpilotCaptureActive = false;
            console.log('⏹ Capture stopped');
            
            // Remove widget
            if (counterWidget) {
                counterWidget.remove();
                counterWidget = null;
            }
        }
    });

    console.log('✅ Listeners attached');
})();
