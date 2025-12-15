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
        // Priority: data-testid > id > name > aria-label > unique class > text content > position
        
        if (element.getAttribute('data-testid')) {
            return `[data-testid="${element.getAttribute('data-testid')}"]`;
        }
        
        if (element.id) {
            return `#${element.id}`;
        }
        
        if (element.name && isUnique(`[name="${element.name}"]`)) {
            return `[name="${element.name}"]`;
        }
        
        if (element.getAttribute('aria-label') && isUnique(`[aria-label="${element.getAttribute('aria-label')}"]`)) {
            return `[aria-label="${element.getAttribute('aria-label')}"]`;
        }
        
        // Try text content for buttons/links
        const text = element.textContent?.trim();
        if ((element.tagName === 'BUTTON' || element.tagName === 'A') && text && text.length < 50) {
            // Count how many elements match this text
            const matchingByText = Array.from(document.querySelectorAll(element.tagName.toLowerCase())).filter(
                el => el.textContent?.trim() === text
            );
            
            if (matchingByText.length === 1) {
                return `${element.tagName.toLowerCase()}:contains("${text.replace(/"/g, '\\"')}")`;
            } else if (matchingByText.length > 1) {
                // Multiple matches, add index
                const index = matchingByText.indexOf(element);
                if (index >= 0) {
                    return `${element.tagName.toLowerCase()}:contains("${text.replace(/"/g, '\\"')}"):eq(${index})`;
                }
            }
        }
        
        // Try class combination
        if (element.className && typeof element.className === 'string') {
            const classes = element.className.trim().split(/\s+/).filter(c => c && !c.startsWith('ng-') && !c.startsWith('_'));
            if (classes.length > 0 && classes.length <= 4) {
                const classSelector = `.${classes.join('.')}`;
                if (isUnique(classSelector)) {
                    return classSelector;
                }
                
                // If not unique, add :eq(index) to make it specific
                const matchingElements = document.querySelectorAll(classSelector);
                if (matchingElements.length > 1) {
                    const index = Array.from(matchingElements).indexOf(element);
                    if (index >= 0) {
                        return `${classSelector}:eq(${index})`;
                    }
                }
            }
        }
        
        // Fallback: use tag with parent context
        const tag = element.tagName.toLowerCase();
        const parent = element.parentElement;
        if (parent) {
            const parentSelector = parent.id ? `#${parent.id}` : 
                                  parent.className ? `.${parent.className.trim().split(/\s+/)[0]}` : 
                                  parent.tagName.toLowerCase();
            const siblings = Array.from(parent.children).filter(e => e.tagName === element.tagName);
            if (siblings.length > 1) {
                const index = siblings.indexOf(element);
                return `${parentSelector} > ${tag}:eq(${index})`;
            }
            return `${parentSelector} > ${tag}`;
        }
        
        return tag;
    }

    // Helper: Check if selector is unique
    function isUnique(selector) {
        try {
            // Handle :contains() pseudo-selector (not standard CSS)
            if (selector.includes(':contains(')) {
                return true; // Cypress supports this, assume unique for now
            }
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

    // Track last input to avoid duplicates
    let lastInput = { element: null, value: '', time: 0 };

    // Click events
    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-recorder-ignore]')) return;
        
        const element = e.target;
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

    // Navigation events
    let lastUrl = window.location.href;
    setInterval(() => {
        if (window.location.href !== lastUrl) {
            captureEvent({
                type: 'navigation',
                url: window.location.href,
                fromUrl: lastUrl
            });
            lastUrl = window.location.href;
        }
    }, 500);

    // Page load
    captureEvent({
        type: 'pageload',
        url: window.location.href,
        title: document.title
    });

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
    window.__recorder = {
        getEvents: () => events,
        getEventCount: () => events.length,
        clearEvents: () => events.length = 0
    };

    console.log('[RECORDER] Ready to capture events');
})();
