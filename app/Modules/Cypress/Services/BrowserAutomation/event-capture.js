/**
 * Event Capture Script
 * Injected into browser to capture all user interactions
 * Similar to Playwright's event recorder
 */

(function() {
    'use strict';

    console.log('[RECORDER] Event capture script loaded');

    const events = [];
    let eventCounter = 0;

    // Helper: Generate optimal selector for element
    // STRICT PRIORITY: ID first, then XPath fallback only
    function getSelector(element) {
        console.log(`[RECORDER] getSelector called for:`, element.tagName, element.textContent?.trim().substring(0, 50));
        
        // Priority 1: ID attribute (primary selector - preferred method)
        if (element.id && element.id.trim() !== '') {
            const idSelector = `#${element.id}`;
            console.log(`[RECORDER] Using ID selector:`, idSelector);
            return idSelector;
        }
        
        // Priority 2: XPath (fallback when no ID exists)
        // XPath provides a unique path to the element
        const xpath = getXPath(element);
        const xpathSelector = `XPATH:${xpath}`;
        console.log(`[RECORDER] No ID found, using XPath selector:`, xpathSelector);
        return xpathSelector;
    }

    // Helper: Get element text
    function getElementText(element) {
        return element.textContent?.trim().substring(0, 50) || '';
    }

    // Helper: Get element attributes
    function getElementAttributes(element) {
        const attrs = {};
        for (const attr of element.attributes) {
            attrs[attr.name] = attr.value;
        }
        return attrs;
    }

    // Helper: Send event to console (picked up by Puppeteer)
    function captureEvent(eventData) {
        eventCounter++;
        eventData.id = eventCounter;
        eventData.timestamp = Date.now();
        eventData.url = window.location.href;
        
        events.push(eventData);
        console.log('[EVENT_CAPTURED]' + JSON.stringify(eventData));
    }

    // Helper: Check if element is visible
    function isElementVisible(element) {
        const style = window.getComputedStyle(element);
        return style.display !== 'none' && 
               style.visibility !== 'hidden' && 
               style.opacity !== '0' &&
               element.offsetWidth > 0 && 
               element.offsetHeight > 0;
    }

    // Track last input to avoid duplicates
    let lastInput = { element: null, value: '', time: 0 };

    // Click events
    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-recorder-ignore]')) return;
        
        const element = e.target;
        
        // Skip clicks on hidden elements (they shouldn't be clickable anyway)
        if (!isElementVisible(element)) {
            console.log('[RECORDER] Skipped hidden element:', element);
            return;
        }
        
        captureEvent({
            type: 'click',
            selector: getSelector(element),
            tagName: element.tagName.toLowerCase(),
            text: getElementText(element),
            attributes: getElementAttributes(element),
            xpath: getXPath(element)
        });
    }, true);

    // Input events (typing)
    document.addEventListener('input', (e) => {
        const element = e.target;
        const value = element.value;
        const now = Date.now();
        
        // Debounce: only capture if different element or 1 second passed
        if (lastInput.element !== element || now - lastInput.time > 1000) {
            captureEvent({
                type: 'input',
                selector: getSelector(element),
                tagName: element.tagName.toLowerCase(),
                value: value,
                inputType: element.type,
                attributes: getElementAttributes(element),
                xpath: getXPath(element)
            });
        }
        
        lastInput = { element, value, time: now };
    }, true);

    // Change events (select, checkbox, radio)
    document.addEventListener('change', (e) => {
        const element = e.target;
        
        let value;
        if (element.type === 'checkbox') {
            value = element.checked;
        } else if (element.type === 'radio') {
            value = element.value;
        } else if (element.tagName === 'SELECT') {
            value = element.options[element.selectedIndex]?.text || element.value;
        } else {
            value = element.value;
        }
        
        captureEvent({
            type: 'change',
            selector: getSelector(element),
            tagName: element.tagName.toLowerCase(),
            value: value,
            inputType: element.type,
            attributes: getElementAttributes(element),
            xpath: getXPath(element)
        });
    }, true);
    
    // Capture Enter key presses (important for form submissions)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            const element = e.target;
            // Only capture if it's in an input field or form
            if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                captureEvent({
                    type: 'keypress',
                    key: 'Enter',
                    selector: getSelector(element),
                    tagName: element.tagName.toLowerCase(),
                    attributes: getElementAttributes(element),
                    xpath: getXPath(element)
                });
            }
        }
    }, true);

    // Navigation events - using Navigation Timing API for better accuracy
    let lastUrl = window.location.href;
    let isFirstLoad = true;
    
    // Method 1: Use navigation event (more reliable)
    window.addEventListener('popstate', () => {
        const currentUrl = window.location.href;
        if (currentUrl !== lastUrl && !isFirstLoad) {
            captureEvent({
                type: 'navigation',
                url: currentUrl,
                fromUrl: lastUrl
            });
            lastUrl = currentUrl;
        }
    });
    
    // Method 2: Poll for URL changes (fallback for programmatic navigation)
    setInterval(() => {
        const currentUrl = window.location.href;
        if (currentUrl !== lastUrl) {
            // Don't capture the very first page load, user will set it manually
            if (!isFirstLoad) {
                console.log(`[RECORDER] Navigation detected: ${lastUrl} → ${currentUrl}`);
                captureEvent({
                    type: 'navigation',
                    url: currentUrl,
                    fromUrl: lastUrl
                });
            }
            lastUrl = currentUrl;
            isFirstLoad = false;
        }
    }, 300); // Reduced interval for faster detection

    // Store initial URL but don't capture as event
    // User will add cy.visit() manually at the start
    window.__recorder = {
        initialUrl: window.location.href,
        getEvents: () => events,
        getEventCount: () => events.length,
        clearEvents: () => events.length = 0
    };

    // Helper: Generate XPath for element
    function getXPath(element) {
        if (element.id) {
            return `//*[@id="${element.id}"]`;
        }
        
        const parts = [];
        while (element && element.nodeType === Node.ELEMENT_NODE) {
            let index = 0;
            let sibling = element.previousSibling;
            
            while (sibling) {
                if (sibling.nodeType === Node.ELEMENT_NODE && sibling.nodeName === element.nodeName) {
                    index++;
                }
                sibling = sibling.previousSibling;
            }
            
            const tagName = element.nodeName.toLowerCase();
            const pathIndex = index > 0 ? `[${index + 1}]` : '';
            parts.unshift(tagName + pathIndex);
            
            element = element.parentNode;
        }
        
        return parts.length ? '/' + parts.join('/') : '';
    }

    // Visual feedback - highlight hovered elements
    let highlightOverlay = null;
    document.addEventListener('mouseover', (e) => {
        if (e.target.closest('[data-recorder-ignore]')) return;
        
        if (!highlightOverlay) {
            highlightOverlay = document.createElement('div');
            highlightOverlay.setAttribute('data-recorder-ignore', 'true');
            highlightOverlay.style.cssText = `
                position: absolute;
                pointer-events: none;
                border: 2px solid #ff0000;
                background: rgba(255, 0, 0, 0.1);
                z-index: 999999;
                transition: all 0.1s;
            `;
            document.body.appendChild(highlightOverlay);
        }
        
        const rect = e.target.getBoundingClientRect();
        highlightOverlay.style.display = 'block';
        highlightOverlay.style.top = (rect.top + window.scrollY) + 'px';
        highlightOverlay.style.left = (rect.left + window.scrollX) + 'px';
        highlightOverlay.style.width = rect.width + 'px';
        highlightOverlay.style.height = rect.height + 'px';
    });

    document.addEventListener('mouseout', () => {
        if (highlightOverlay) {
            highlightOverlay.style.display = 'none';
        }
    });

    // Expose global functions for debugging
    // Removed duplicate declaration, already set above
    
    console.log('[RECORDER] Ready to capture events');
})();
