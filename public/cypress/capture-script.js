/**
 * Testpilot Event Capture Script
 */
(function() {
    const scriptTag = document.currentScript || document.querySelector('script[src*="capture-script.js"]');
    const scriptSrc = scriptTag ? scriptTag.src : '';
    const sessionMatch = scriptSrc.match(/[?&]session=([^&]+)/);
    let sessionId = sessionMatch ? decodeURIComponent(sessionMatch[1]) : null;
    
    if (!sessionId) {
        console.error(' No session ID provided');
        return;
    }
    
    if (window.__testpilotCaptureActive) {
        console.log(' Already active!');
        return;
    }
    window.__testpilotCaptureActive = true;
    window.__testpilotCapturePaused = false;
    
    console.log(' Testpilot Started!');
    console.log(' Session:', sessionId);
    
    const scriptUrlObj = new URL(scriptSrc);
    const SERVER_URL = scriptUrlObj.origin;
    const CAPTURE_ENDPOINT = SERVER_URL + '/cypress/capture-event-bookmarklet';
    
    console.log(' Server:', SERVER_URL);
    
    let eventCounter = 0;
    const isFromExtension = localStorage.getItem('cypress_auto_inject') === 'true';
    console.log(' Extension:', isFromExtension);
    
    function sendEvent(eventData) {
        if (window.__testpilotCapturePaused) {
            console.log('⏸ Event skipped - paused');
            return;
        }
        if (!window.__testpilotCaptureActive) {
            console.log('⏹ Event skipped - stopped');
            return;
        }
        eventCounter++;
        
        eventData.session_id = sessionId;
        eventData.url = window.location.href;
        eventData.timestamp = new Date().toISOString();
        
        console.log(' Event #' + eventCounter + ':', eventData.type);
        
        fetch(CAPTURE_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ event: eventData })
        })
        .then(r => r.json())
        .then(() => {
            console.log(' Captured');
            window.dispatchEvent(new CustomEvent('testpilot-event-captured', { 
                detail: { count: eventCounter },
                bubbles: true
            }));
            console.log(' Count:', eventCounter);
        })
        .catch(e => console.error('', e));
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
            value: el.value || null,
            checked: el.checked || null,
            href: el.href || null,
            src: el.src || null,
            alt: el.alt || null,
            title: el.title || null,
            role: el.getAttribute('role') || null,
            // For file uploads
            accept: el.accept || null,
            multiple: el.multiple || null,
            // For forms
            action: el.action || null,
            method: el.method || null
        };
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
    
    document.addEventListener('click', e => sendEvent(getElementData(e.target, 'click')), false);
    document.addEventListener('input', e => sendEvent(getElementData(e.target, 'input')), false);
    document.addEventListener('change', e => sendEvent(getElementData(e.target, 'change')), false);
    
    // Capture file uploads
    document.addEventListener('change', e => {
        if (e.target.type === 'file' && e.target.files && e.target.files.length > 0) {
            const data = getElementData(e.target, 'file_upload');
            data.fileCount = e.target.files.length;
            data.fileNames = Array.from(e.target.files).map(f => f.name);
            data.fileTypes = Array.from(e.target.files).map(f => f.type);
            sendEvent(data);
        }
    }, false);
    
    // Capture form submissions
    document.addEventListener('submit', e => {
        const data = getElementData(e.target, 'form_submit');
        sendEvent(data);
    }, false);
    
    // Capture select dropdown changes
    document.addEventListener('change', e => {
        if (e.target.tagName === 'SELECT') {
            const data = getElementData(e.target, 'select');
            data.selectedIndex = e.target.selectedIndex;
            data.selectedValue = e.target.value;
            data.selectedText = e.target.options[e.target.selectedIndex]?.text || '';
            sendEvent(data);
        }
    }, false);
    
    // Capture checkbox/radio changes
    document.addEventListener('change', e => {
        if (e.target.type === 'checkbox' || e.target.type === 'radio') {
            const data = getElementData(e.target, e.target.type);
            data.checked = e.target.checked;
            sendEvent(data);
        }
    }, false);
    
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
    
    console.log(' Listeners attached');
})();
