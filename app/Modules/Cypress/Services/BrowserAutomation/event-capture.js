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
    function getSelector(element) {
        // Priority: data-testid > id > name > aria-label > unique class > text content > nth-child
        
        // 1. data-testid (best practice for testing)
        if (element.getAttribute('data-testid')) {
            return `[data-testid="${element.getAttribute('data-testid')}"]`;
        }
        
        // 2. ID (unique and fast)
        if (element.id) {
            return `#${element.id}`;
        }
        
        // 3. Name attribute (common for forms)
        if (element.name && isUnique(`[name="${element.name}"]`)) {
            return `[name="${element.name}"]`;
        }
        
        // 4. Placeholder for inputs
        const placeholder = element.getAttribute('placeholder');
        if (placeholder && isUnique(`[placeholder="${placeholder}"]`)) {
            return `[placeholder="${placeholder}"]`;
        }
        
        // 5. aria-label
        if (element.getAttribute('aria-label') && isUnique(`[aria-label="${element.getAttribute('aria-label')}"]`)) {
            return `[aria-label="${element.getAttribute('aria-label')}"]`;
        }
        
        // 6. For buttons/links, try text content (without index)
        const text = element.textContent?.trim();
        if ((element.tagName === 'BUTTON' || element.tagName === 'A') && text && text.length < 50 && text.length > 0) {
            const escapedText = text.replace(/"/g, '\\"').replace(/'/g, "\\'");
            const matchingByText = Array.from(document.querySelectorAll(element.tagName.toLowerCase())).filter(
                el => el.textContent?.trim() === text
            );
            
            // Only use text selector if it's unique
            if (matchingByText.length === 1) {
                return `TEXT:${element.tagName.toLowerCase()}:${escapedText}`;
            }
        }
        
        // 7. Try unique class combination
        if (element.className && typeof element.className === 'string') {
            const classes = element.className.trim().split(/\s+/).filter(c => 
                c && !c.startsWith('ng-') && !c.startsWith('_') && !c.match(/^(active|disabled|hover|focus)$/)
            );
            
            // Try full class combination
            if (classes.length > 0 && classes.length <= 4) {
                const classSelector = `.${classes.join('.')}`;
                if (isUnique(classSelector)) {
                    return classSelector;
                }
            }
            
            // Try single most specific class
            if (classes.length > 0) {
                for (const cls of classes) {
                    const selector = `.${cls}`;
                    if (isUnique(selector)) {
                        return selector;
                    }
                }
            }
        }
        
        // 8. Type attribute for inputs
        if (element.type && element.tagName === 'INPUT') {
            const selector = `input[type="${element.type}"]`;
            if (isUnique(selector)) {
                return selector;
            }
        }
        
        // 9. Parent context with nth-child (more reliable than eq)
        const tag = element.tagName.toLowerCase();
        const parent = element.parentElement;
        if (parent) {
            const siblings = Array.from(parent.children).filter(e => e.tagName === element.tagName);
            if (siblings.length > 1) {
                const index = siblings.indexOf(element);
                const nthChild = index + 1;
                
                // Use parent's ID or class
                let parentSelector = '';
                if (parent.id) {
                    parentSelector = `#${parent.id}`;
                } else if (parent.className && typeof parent.className === 'string') {
                    const parentClass = parent.className.trim().split(/\s+/)[0];
                    if (parentClass) {
                        parentSelector = `.${parentClass}`;
                    }
                }
                
                if (parentSelector) {
                    return `${parentSelector} > ${tag}:nth-child(${nthChild})`;
                }
            }
            
            // Simple parent > child if unique
            if (parent.id) {
                const selector = `#${parent.id} > ${tag}`;
                if (isUnique(selector)) {
                    return selector;
                }
            }
        }
        
        // 10. Fallback: tag name (least specific, but better than nothing)
        return tag;
    }

    // Helper: Check if selector is unique
    function isUnique(selector) {
        try {
            const elements = document.querySelectorAll(selector);
            return elements.length === 1;
        } catch (e) {
            return false;
        }
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

    // Navigation events - only capture when URL actually changes
    let lastUrl = window.location.href;
    let isFirstLoad = true;
    
    setInterval(() => {
        const currentUrl = window.location.href;
        if (currentUrl !== lastUrl) {
            // Don't capture the very first page load, user will set it manually
            if (!isFirstLoad) {
                captureEvent({
                    type: 'navigation',
                    url: currentUrl,
                    fromUrl: lastUrl
                });
            }
            lastUrl = currentUrl;
            isFirstLoad = false;
        }
    }, 500);

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
