/**
 * Cypress Event Capture Script
 * This script is injected into any webpage to capture user interactions
 * It persists across page navigation using localStorage and auto-reinjects
 */

// Main capture script
(function() {
    // Get session ID from URL parameter or localStorage
    const scriptTag = document.currentScript || document.querySelector('script[src*="capture-script.js"]');
    const scriptSrc = scriptTag ? scriptTag.src : '';
    const sessionMatch = scriptSrc.match(/[?&]session=([^&]+)/);
    let sessionId = sessionMatch ? decodeURIComponent(sessionMatch[1]) : null;
    
    // Always use URL parameter if provided, otherwise check localStorage
    if (sessionId) {
        // Store the session and server URL for future page loads
        localStorage.setItem('cypress_session_id', sessionId);
        localStorage.setItem('cypress_auto_inject', 'true');
        if (scriptSrc) {
            const scriptUrlObj = new URL(scriptSrc);
            const serverUrl = scriptUrlObj.origin;
            localStorage.setItem('cypress_server_url', serverUrl);
            console.log('💾 Server URL stored:', serverUrl);
        }
        console.log('💾 Session stored:', sessionId);
    } else {
        // No URL parameter, check localStorage
        const storedSession = localStorage.getItem('cypress_session_id');
        if (storedSession) {
            sessionId = storedSession;
            console.log('📌 Resuming session:', sessionId);
        } else {
            // Create new session as fallback
            sessionId = Date.now().toString();
            localStorage.setItem('cypress_session_id', sessionId);
            console.log('✨ Created new session:', sessionId);
        }
    }
    
    // Prevent double injection
    if (window.__cypressEventCaptureActive) {
        console.log('🔵 Cypress Event Capture already active!');
        return;
    }
    window.__cypressEventCaptureActive = true;
    
    console.log('🎯 Cypress Event Capture Started!');
    console.log('📋 Session ID:', sessionId);
    console.log('🌐 Capturing events on:', window.location.href);
    
    // Server endpoint - extract from script src
    const scriptUrlObj = new URL(scriptSrc || window.location.href);
    const SERVER_URL = scriptUrlObj.origin;
    const CAPTURE_ENDPOINT = SERVER_URL + '/cypress/capture-event-bookmarklet';
    
    console.log('📡 Server URL:', SERVER_URL);
    console.log('📤 Capture endpoint:', CAPTURE_ENDPOINT);
    
    // Event counter
    let eventCounter = 0;
    
    // Create floating indicator
    const indicator = document.createElement('div');
    indicator.id = 'cypress-indicator';
    indicator.innerHTML = `
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 8px; height: 8px; background: #16a34a; border-radius: 50%; animation: pulse 2s infinite;"></div>
            <span style="font-weight: 600;">Cypress Recording</span>
            <span id="cypress-counter" style="background: #2563eb; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">0</span>
        </div>
        <button id="cypress-stop" style="background: #dc2626; color: white; border: none; padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 0.75rem; margin-left: 8px;">Stop</button>
        <style>
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
        </style>
    `;
    indicator.style.cssText = `
        position: fixed;
        top: 10px;
        right: 10px;
        background: white;
        border: 2px solid #2563eb;
        border-radius: 8px;
        padding: 12px 16px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 999999;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        cursor: move;
    `;
    (document.body || document.documentElement).appendChild(indicator);
    
    // Make indicator draggable
    let isDragging = false;
    let currentX, currentY, initialX, initialY;
    
    indicator.addEventListener('mousedown', function(e) {
        if (e.target.id !== 'cypress-stop') {
            isDragging = true;
            initialX = e.clientX - indicator.offsetLeft;
            initialY = e.clientY - indicator.offsetTop;
        }
    });
    
    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            e.preventDefault();
            currentX = e.clientX - initialX;
            currentY = e.clientY - initialY;
            indicator.style.left = currentX + 'px';
            indicator.style.top = currentY + 'px';
            indicator.style.right = 'auto';
        }
    });
    
    document.addEventListener('mouseup', function() {
        isDragging = false;
    });
    
    // Stop button
    document.getElementById('cypress-stop').addEventListener('click', function() {
        if (confirm('Stop event capture?\n\nThis will stop recording on ALL pages in this browser.')) {
            indicator.remove();
            window.__cypressEventCaptureActive = false;
            
            // Clear localStorage to prevent auto-inject
            localStorage.removeItem('cypress_auto_inject');
            localStorage.removeItem('cypress_session_id');
            localStorage.removeItem('cypress_server_url');
            
            console.log('🛑 Cypress Event Capture Stopped');
            alert('✅ Event capture stopped!\n\nTotal events: ' + eventCounter + '\n\nView all captured events in the dashboard.');
        }
    });
    
    // Send event to server
    function sendEvent(eventData) {
        eventCounter++;
        const counterEl = document.getElementById('cypress-counter');
        if (counterEl) {
            counterEl.textContent = eventCounter;
        }
        
        // Add metadata
        eventData.session_id = sessionId;
        eventData.url = window.location.href;
        eventData.page_title = document.title;
        eventData.timestamp = new Date().toISOString();
        eventData.sequence = eventCounter;
        
        console.log('📤 Sending event #' + eventCounter + ':', eventData.type);
        
        // Send to server via fetch
        fetch(CAPTURE_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ event: eventData }),
            mode: 'cors',
            credentials: 'omit'
        }).then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
          .then(function(data) {
              console.log('✅ Event #' + eventCounter + ' captured successfully');
          })
          .catch(function(error) {
              console.error('❌ Error sending event:', error);
          });
    }
    
    // Generate XPath for element
    function getElementXPath(element) {
        if (!element) return '';
        if (element.id) return `//*[@id="${element.id}"]`;
        if (element === document.body) return '/html/body';
        
        let ix = 0;
        const siblings = element.parentNode.childNodes;
        for (let i = 0; i < siblings.length; i++) {
            const sibling = siblings[i];
            if (sibling === element) {
                const tagName = element.tagName.toLowerCase();
                return getElementXPath(element.parentNode) + '/' + tagName + '[' + (ix + 1) + ']';
            }
            if (sibling.nodeType === 1 && sibling.tagName === element.tagName) {
                ix++;
            }
        }
        return '';
    }
    
    // Get element data
    function getElementData(element, eventType) {
        return {
            type: eventType,
            element: element.tagName || 'UNKNOWN',
            id: element.id || null,
            class: element.className || null,
            name: element.name || null,
            xpath: getElementXPath(element),
            text: (element.textContent || element.innerText || '').trim().substring(0, 100),
            value: element.value || null,
            href: element.href || null,
            src: element.src || null,
            placeholder: element.placeholder || null,
            elementType: element.type || null
        };
    }
    
    // Capture click events
    document.addEventListener('click', function(e) {
        const data = getElementData(e.target, 'click');
        data.coordinates = { x: e.clientX, y: e.clientY };
        data.button = e.button;
        sendEvent(data);
    }, true);
    
    // Capture input events
    document.addEventListener('input', function(e) {
        const data = getElementData(e.target, 'input');
        data.value = e.target.value ? e.target.value.substring(0, 50) : '';
        sendEvent(data);
    }, true);
    
    // Capture change events (selects, checkboxes, radios)
    document.addEventListener('change', function(e) {
        const data = getElementData(e.target, 'change');
        data.checked = e.target.checked || null;
        if (e.target.tagName === 'SELECT') {
            data.selectedIndex = e.target.selectedIndex;
            data.selectedText = e.target.options[e.target.selectedIndex]?.text || '';
        }
        sendEvent(data);
    }, true);
    
    // Capture form submissions
    document.addEventListener('submit', function(e) {
        const data = getElementData(e.target, 'form_submit');
        data.action = e.target.action;
        data.method = e.target.method;
        sendEvent(data);
    }, true);
    
    // Capture page load
    sendEvent({
        type: 'page_loaded',
        url: window.location.href,
        title: document.title,
        domain: window.location.hostname
    });
    
    // Show notification
    console.log('%c🎯 Cypress Event Capture Active!', 'background: #2563eb; color: white; padding: 8px 16px; font-size: 14px; font-weight: bold;');
    console.log('%cSession ID: ' + sessionId, 'color: #16a34a; font-weight: bold;');
    console.log('%c💡 Recording will continue on page navigation!', 'color: #f59e0b; font-weight: bold;');
    
})();
