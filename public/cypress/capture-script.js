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
        }

        if (event.data.type === 'TESTPILOT_STOP_CAPTURE') {
            window.__testpilotCaptureActive = false;
            console.log('⏹ Capture stopped');
        }
    });

    console.log('✅ Listeners attached');
})();
