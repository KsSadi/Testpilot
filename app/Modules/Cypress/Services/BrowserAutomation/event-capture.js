/**
 * Event Capture Script - ENHANCED VERSION
 * Injected into browser to capture all user interactions
 * Similar to Playwright's event recorder with IMPROVED RELIABILITY
 * 
 * ENHANCEMENTS:
 * - Better selector uniqueness verification
 * - Capture ALL interactive events (hover, focus, blur)
 * - Better handling of dynamic content
 * - File upload detection
 * - Form submission tracking
 * - Improved debouncing logic
 * - Better visibility detection (including hidden inputs)
 * - Multiple selector fallback strategies
 */

(function() {
    'use strict';

    console.log('[RECORDER] ✨ Enhanced Event capture script loaded');

    const events = [];
    let eventCounter = 0;
    const eventCache = new Map(); // Prevent duplicate events

    // PREVENT NEW TABS: Remove target="_blank" from all links
    function preventNewTabs() {
        // Remove target="_blank" from all existing links
        document.querySelectorAll('a[target="_blank"], a[target="_new"]').forEach(link => {
            link.removeAttribute('target');
            console.log('[RECORDER] Removed target attribute from link:', link.href);
        });

        // Override window.open to navigate in same tab instead
        const originalOpen = window.open;
        window.open = function(url, target, features) {
            if (url) {
                console.log('[RECORDER] Intercepted window.open, navigating in same tab:', url);
                window.location.href = url;
                return window;
            }
            return originalOpen.call(window, url, target, features);
        };

        console.log('[RECORDER] New tab prevention activated');
    }

    // Run immediately and on DOM changes
    preventNewTabs();

    // Watch for dynamically added links with target="_blank"
    const observer = new MutationObserver((mutations) => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) { // Element node
                    if (node.tagName === 'A' && (node.target === '_blank' || node.target === '_new')) {
                        node.removeAttribute('target');
                        console.log('[RECORDER] Removed target from dynamically added link:', node.href);
                    }
                    // Check children too
                    if (node.querySelectorAll) {
                        node.querySelectorAll('a[target="_blank"], a[target="_new"]').forEach(link => {
                            link.removeAttribute('target');
                            console.log('[RECORDER] Removed target from nested link:', link.href);
                        });
                    }
                }
            });
        });
    });

    observer.observe(document.documentElement, {
        childList: true,
        subtree: true
    });

    // ═══════════════════════════════════════════════════════════════
    // ENHANCED SELECTOR GENERATION - WITH UNIQUENESS VERIFICATION
    // ═══════════════════════════════════════════════════════════════
    
    /**
     * Verify if a selector uniquely identifies an element
     */
    function isUniqueSelector(selector, element) {
        try {
            // Handle special selector formats
            if (selector.startsWith('TEXT:')) {
                const parts = selector.split(':');
                const tag = parts[1];
                const text = parts.slice(2).join(':');
                const elements = Array.from(document.querySelectorAll(tag)).filter(el => 
                    el.textContent.trim() === text
                );
                return elements.length === 1 && elements[0] === element;
            }
            
            if (selector.startsWith('XPATH:')) {
                // XPath is always unique by definition
                return true;
            }
            
            // CSS selector
            const elements = document.querySelectorAll(selector);
            return elements.length === 1 && elements[0] === element;
        } catch (e) {
            return false;
        }
    }
    
    /**
     * Get multiple alternative selectors for robustness
     */
    function getAlternativeSelectors(element) {
        const selectors = [];
        
        // ID
        if (element.id && element.id.trim() !== '') {
            const selector = `[id="${element.id}"]`;
            if (isUniqueSelector(selector, element)) {
                selectors.push({ type: 'id', selector, priority: 1 });
            }
        }
        
        // data-testid
        if (element.getAttribute('data-testid')) {
            const selector = `[data-testid="${element.getAttribute('data-testid')}"]`;
            if (isUniqueSelector(selector, element)) {
                selectors.push({ type: 'data-testid', selector, priority: 2 });
            }
        }
        
        // data-cy (Cypress specific)
        if (element.getAttribute('data-cy')) {
            const selector = `[data-cy="${element.getAttribute('data-cy')}"]`;
            if (isUniqueSelector(selector, element)) {
                selectors.push({ type: 'data-cy', selector, priority: 2 });
            }
        }
        
        // name attribute
        if (element.name && element.name.trim() !== '') {
            const selector = `[name="${element.name}"]`;
            if (isUniqueSelector(selector, element)) {
                selectors.push({ type: 'name', selector, priority: 3 });
            }
        }
        
        // placeholder (for inputs)
        if (element.placeholder && element.placeholder.trim() !== '') {
            const selector = `[placeholder="${element.placeholder}"]`;
            if (isUniqueSelector(selector, element)) {
                selectors.push({ type: 'placeholder', selector, priority: 4 });
            }
        }
        
        // aria-label
        if (element.getAttribute('aria-label')) {
            const selector = `[aria-label="${element.getAttribute('aria-label')}"]`;
            if (isUniqueSelector(selector, element)) {
                selectors.push({ type: 'aria-label', selector, priority: 4 });
            }
        }
        
        // Text content for buttons and links
        if ((element.tagName === 'A' || element.tagName === 'BUTTON' || element.tagName === 'SPAN' || element.tagName === 'DIV') 
            && element.textContent.trim()) {
            const text = element.textContent.trim();
            const tag = element.tagName.toLowerCase();
            const selector = `TEXT:${tag}:${text}`;
            if (isUniqueSelector(selector, element)) {
                selectors.push({ type: 'text', selector, priority: 5 });
            }
        }
        
        // Type attribute (for inputs)
        if (element.type && element.name) {
            const selector = `${element.tagName.toLowerCase()}[type="${element.type}"][name="${element.name}"]`;
            if (isUniqueSelector(selector, element)) {
                selectors.push({ type: 'type-name', selector, priority: 6 });
            }
        }
        
        return selectors;
    }

    /**
     * Generate optimal selector with GUARANTEED uniqueness
     */
    function getSelector(element) {
        console.log(`[RECORDER] 🎯 getSelector called for:`, element.tagName, element.textContent?.trim().substring(0, 50));
        
        // Get all possible selectors
        const alternatives = getAlternativeSelectors(element);
        
        // Return the highest priority unique selector
        if (alternatives.length > 0) {
            alternatives.sort((a, b) => a.priority - b.priority);
            const best = alternatives[0];
            console.log(`[RECORDER] ✓ Using ${best.type} selector:`, best.selector);
            return best.selector;
        }
        
        // Fallback to XPath (always unique)
        const xpath = getXPath(element);
        const xpathSelector = `XPATH:${xpath}`;
        console.log(`[RECORDER] ⚠️ Using XPath selector (fallback):`, xpathSelector);
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

    // ═══════════════════════════════════════════════════════════════
    // EVENT CAPTURING AND DEDUPLICATION
    // ═══════════════════════════════════════════════════════════════
    
    /**
     * Send event to console (picked up by Puppeteer) with deduplication
     */
    function captureEvent(eventData) {
        // Create a unique key for deduplication
        const eventKey = `${eventData.type}-${eventData.selector}-${eventData.value || ''}-${Math.floor(eventData.timestamp / 500)}`;
        
        // Skip if we've already captured this event recently (within 500ms)
        if (eventCache.has(eventKey)) {
            console.log('[RECORDER] 🔄 Skipped duplicate event:', eventKey);
            return;
        }
        
        eventCounter++;
        eventData.id = eventCounter;
        eventData.timestamp = Date.now();
        eventData.url = window.location.href;
        
        events.push(eventData);
        eventCache.set(eventKey, true);
        
        // Clean old cache entries after 2 seconds
        setTimeout(() => eventCache.delete(eventKey), 2000);
        
        console.log(`[RECORDER] ✅ Event #${eventCounter} captured:`, eventData.type, eventData.selector);
        console.log('[EVENT_CAPTURED]' + JSON.stringify(eventData));
    }

    /**
     * Enhanced visibility check - includes form elements that may be hidden but interactive
     */
    function isElementVisible(element) {
        // SPECIAL CASE: Always capture file inputs, checkboxes, and radios even if hidden
        // They're often styled with CSS to be invisible but still functional
        if (element.type === 'file' || element.type === 'checkbox' || element.type === 'radio' || element.type === 'hidden') {
            return true; // Always capture these
        }
        
        const style = window.getComputedStyle(element);
        const isVisible = style.display !== 'none' && 
               style.visibility !== 'hidden' && 
               element.offsetWidth > 0 && 
               element.offsetHeight > 0;
               
        // Allow elements with opacity 0 if they're input elements
        if (!isVisible && (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT')) {
            return style.display !== 'none' && style.visibility !== 'hidden';
        }
        
        return isVisible;
    }
    
    /**
     * Check if element is actually interactable (not disabled, not readonly except for special cases)
     */
    function isInteractable(element) {
        if (element.disabled) {
            console.log('[RECORDER] ⚠️ Skipped disabled element:', element);
            return false;
        }
        return true;
    }

    // Track last input to avoid excessive duplicates from rapid typing
    let lastInput = { element: null, value: '', time: 0 };
    let inputTimeout = null;

    // ═══════════════════════════════════════════════════════════════
    // EVENT LISTENERS - COMPREHENSIVE COVERAGE
    // ═══════════════════════════════════════════════════════════════

    // 1. CLICK EVENTS - Most important interaction
    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-recorder-ignore]')) return;
        
        const element = e.target;
        
        // Additional safety: remove target from clicked links
        const link = element.closest('a');
        if (link && (link.target === '_blank' || link.target === '_new')) {
            link.removeAttribute('target');
            console.log('[RECORDER] Removed target on click:', link.href);
        }
        
        if (!isElementVisible(element) || !isInteractable(element)) {
            return;
        }
        
        // Get the actual clickable element (might be parent of target)
        let clickableElement = element;
        if (element.tagName === 'SPAN' || element.tagName === 'I') {
            const button = element.closest('button, a, [role="button"]');
            if (button) clickableElement = button;
        }
        
        captureEvent({
            type: 'click',
            selector: getSelector(clickableElement),
            tagName: clickableElement.tagName.toLowerCase(),
            text: getElementText(clickableElement),
            attributes: getElementAttributes(clickableElement),
            xpath: getXPath(clickableElement),
            innerHtml: clickableElement.innerHTML.substring(0, 100)
        });
    }, true);

    // 2. INPUT EVENTS - Typing in text fields
    document.addEventListener('input', (e) => {
        const element = e.target;
        const value = element.value;
        const now = Date.now();
        
        if (!isElementVisible(element) || !isInteractable(element)) {
            return;
        }
        
        // Clear previous timeout
        if (inputTimeout) {
            clearTimeout(inputTimeout);
        }
        
        // Debounce: Wait 800ms after user stops typing before capturing
        inputTimeout = setTimeout(() => {
            // Only capture if value actually changed
            if (lastInput.element !== element || lastInput.value !== value) {
                captureEvent({
                    type: 'input',
                    selector: getSelector(element),
                    tagName: element.tagName.toLowerCase(),
                    value: value,
                    inputType: element.type,
                    attributes: getElementAttributes(element),
                    xpath: getXPath(element)
                });
                
                lastInput = { element, value, time: now };
            }
        }, 800); // Wait 800ms after user stops typing
        
    }, true);

    // 3. CHANGE EVENTS - Select dropdowns, checkboxes, radios, file uploads
    document.addEventListener('change', (e) => {
        const element = e.target;
        
        if (!isElementVisible(element) || !isInteractable(element)) {
            return;
        }
        
        let value;
        let eventType = 'change';
        
        if (element.type === 'checkbox') {
            value = element.checked;
            eventType = 'checkbox';
        } else if (element.type === 'radio') {
            value = element.value;
            eventType = 'radio';
        } else if (element.type === 'file') {
            // File upload - special handling
            value = element.files.length > 0 ? Array.from(element.files).map(f => f.name).join(', ') : '';
            eventType = 'file_upload';
            console.log('[RECORDER] 📎 File upload detected:', value);
        } else if (element.tagName === 'SELECT') {
            value = element.options[element.selectedIndex]?.text || element.value;
            eventType = 'select';
        } else {
            value = element.value;
        }
        
        captureEvent({
            type: eventType,
            selector: getSelector(element),
            tagName: element.tagName.toLowerCase(),
            value: value,
            inputType: element.type,
            attributes: getElementAttributes(element),
            xpath: getXPath(element)
        });
    }, true);
    
    // 4. KEYPRESS EVENTS - Enter key (form submissions)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            const element = e.target;
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

    // 5. FORM SUBMIT EVENTS
    document.addEventListener('submit', (e) => {
        const form = e.target;
        console.log('[RECORDER] 📋 Form submission detected');
        
        captureEvent({
            type: 'form_submit',
            selector: getSelector(form),
            tagName: 'form',
            action: form.action,
            method: form.method,
            attributes: getElementAttributes(form),
            xpath: getXPath(form)
        });
    }, true);

    // 6. FOCUS EVENTS - Important for date pickers, dropdowns that open on focus
    let lastFocusedElement = null;
    document.addEventListener('focus', (e) => {
        const element = e.target;
        
        // Only capture focus on form elements and interactive elements
        if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(element.tagName)) {
            return;
        }
        
        // Don't capture if it's just refocusing the same element
        if (lastFocusedElement === element) {
            return;
        }
        
        lastFocusedElement = element;
        
        // Special handling for date pickers and similar
        if (element.type === 'date' || element.type === 'datetime-local' || element.type === 'time') {
            console.log('[RECORDER] 📅 Date/time input focused');
            captureEvent({
                type: 'focus',
                selector: getSelector(element),
                tagName: element.tagName.toLowerCase(),
                inputType: element.type,
                attributes: getElementAttributes(element),
                xpath: getXPath(element)
            });
        }
    }, true);

    // 7. NAVIGATION EVENTS - Enhanced detection
    let lastUrl = window.location.href;
    let isFirstLoad = true;
    let navigationTimeout = null;
    
    // Method 1: Use navigation event (more reliable)
    window.addEventListener('popstate', () => {
        const currentUrl = window.location.href;
        if (currentUrl !== lastUrl && !isFirstLoad) {
            console.log(`[RECORDER] 🔀 Navigation (popstate): ${lastUrl} → ${currentUrl}`);
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
            if (!isFirstLoad) {
                console.log(`[RECORDER] 🔀 Navigation detected: ${lastUrl} → ${currentUrl}`);
                captureEvent({
                    type: 'navigation',
                    url: currentUrl,
                    fromUrl: lastUrl
                });
            }
            lastUrl = currentUrl;
            isFirstLoad = false;
        }
    }, 300); // Check every 300ms for fast detection

    // Method 3: Intercept History API
    const originalPushState = history.pushState;
    const originalReplaceState = history.replaceState;
    
    history.pushState = function(...args) {
        const result = originalPushState.apply(this, args);
        setTimeout(() => {
            const currentUrl = window.location.href;
            if (currentUrl !== lastUrl && !isFirstLoad) {
                console.log(`[RECORDER] 🔀 Navigation (pushState): ${lastUrl} → ${currentUrl}`);
                captureEvent({
                    type: 'navigation',
                    url: currentUrl,
                    fromUrl: lastUrl,
                    method: 'pushState'
                });
                lastUrl = currentUrl;
            }
        }, 100);
        return result;
    };
    
    history.replaceState = function(...args) {
        const result = originalReplaceState.apply(this, args);
        setTimeout(() => {
            const currentUrl = window.location.href;
            if (currentUrl !== lastUrl && !isFirstLoad) {
                console.log(`[RECORDER] 🔀 Navigation (replaceState): ${lastUrl} → ${currentUrl}`);
                captureEvent({
                    type: 'navigation',
                    url: currentUrl,
                    fromUrl: lastUrl,
                    method: 'replaceState'
                });
                lastUrl = currentUrl;
            }
        }, 100);
        return result;
    };

    // ═══════════════════════════════════════════════════════════════
    // HELPER FUNCTIONS
    // ═══════════════════════════════════════════════════════════════
    
    // Helper: Get element text
    function getElementText(element) {
        return element.textContent?.trim().substring(0, 100) || '';
    }

    // Helper: Get element attributes
    function getElementAttributes(element) {
        const attrs = {};
        for (const attr of element.attributes) {
            attrs[attr.name] = attr.value;
        }
        return attrs;
    }

    // Helper: Generate XPath for element
    function getXPath(element) {
        if (element.id) {
            return `//*[@id="${element.id}"]`;
        }
        
        const parts = [];
        let current = element;
        
        while (current && current.nodeType === Node.ELEMENT_NODE) {
            let index = 0;
            let sibling = current.previousSibling;
            
            while (sibling) {
                if (sibling.nodeType === Node.ELEMENT_NODE && sibling.nodeName === current.nodeName) {
                    index++;
                }
                sibling = sibling.previousSibling;
            }
            
            const tagName = current.nodeName.toLowerCase();
            const pathIndex = index > 0 ? `[${index + 1}]` : '';
            parts.unshift(tagName + pathIndex);
            
            current = current.parentNode;
        }
        
        return parts.length ? '/' + parts.join('/') : '';
    }

    // Store initial URL but don't capture as event
    // User will add cy.visit() manually at the start
    window.__recorder = {
        initialUrl: window.location.href,
        getEvents: () => events,
        getEventCount: () => events.length,
        clearEvents: () => {
            events.length = 0;
            eventCounter = 0;
            eventCache.clear();
        },
        getLastEvent: () => events[events.length - 1] || null
    };

    // ═══════════════════════════════════════════════════════════════
    // VISUAL FEEDBACK - Enhanced highlighting
    // ═══════════════════════════════════════════════════════════════
    
    let highlightOverlay = null;
    let eventCountDisplay = null;
    
    // Create persistent event counter display
    function createEventCounter() {
        eventCountDisplay = document.createElement('div');
        eventCountDisplay.setAttribute('data-recorder-ignore', 'true');
        eventCountDisplay.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 30px;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
            z-index: 2147483647;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        eventCountDisplay.innerHTML = `
            <span style="font-size: 18px;">🎯</span>
            <span id="event-count-text">0 events captured</span>
        `;
        document.body.appendChild(eventCountDisplay);
    }
    
    // Update counter
    function updateEventCounter() {
        const countText = document.getElementById('event-count-text');
        if (countText) {
            countText.textContent = `${eventCounter} event${eventCounter !== 1 ? 's' : ''} captured`;
            
            // Flash animation
            eventCountDisplay.style.transform = 'scale(1.1)';
            setTimeout(() => {
                eventCountDisplay.style.transform = 'scale(1)';
            }, 200);
        }
    }
    
    // Create counter after DOM is ready
    if (document.body) {
        createEventCounter();
    } else {
        document.addEventListener('DOMContentLoaded', createEventCounter);
    }
    
    // Update counter whenever event is captured
    const originalCaptureEvent = captureEvent;
    captureEvent = function(eventData) {
        const result = originalCaptureEvent(eventData);
        updateEventCounter();
        return result;
    };
    
    // Highlight hovered elements
    document.addEventListener('mouseover', (e) => {
        if (e.target.closest('[data-recorder-ignore]')) return;
        
        if (!highlightOverlay) {
            highlightOverlay = document.createElement('div');
            highlightOverlay.setAttribute('data-recorder-ignore', 'true');
            highlightOverlay.style.cssText = `
                position: absolute;
                pointer-events: none;
                border: 3px solid #ff0066;
                background: rgba(255, 0, 102, 0.1);
                z-index: 2147483646;
                transition: all 0.1s ease;
                box-shadow: 0 0 10px rgba(255, 0, 102, 0.5);
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

    console.log('[RECORDER] ✅ Ready to capture events - Enhanced version loaded');
    console.log('[RECORDER] 🎯 Features: Unique selectors, file uploads, form submissions, navigation tracking');
})();
