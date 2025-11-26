// ==UserScript==
// @name         Cypress Event Auto Capture
// @namespace    http://127.0.0.1:3030/
// @version      1.0
// @description  Automatically capture events on all websites
// @author       LaraKit
// @match        *://*/*
// @grant        none
// @run-at       document-start
// ==/UserScript==

(function() {
    'use strict';
    
    // Configuration - Update these values from your dashboard
    const SERVER_URL = 'http://127.0.0.1:3030';
    const SESSION_ID = localStorage.getItem('cypress_session_id') || 'YOUR_SESSION_ID_HERE';
    
    // Don't run on the dashboard itself
    if (window.location.href.includes(SERVER_URL + '/cypress')) {
        return;
    }
    
    // Store session info
    localStorage.setItem('cypress_session_id', SESSION_ID);
    localStorage.setItem('cypress_server_url', SERVER_URL);
    localStorage.setItem('cypress_auto_inject', 'true');
    
    // Inject the capture script
    function injectCaptureScript() {
        if (window.__cypressEventCaptureActive) {
            return; // Already loaded
        }
        
        const script = document.createElement('script');
        script.src = SERVER_URL + '/cypress/capture-script.js?session=' + SESSION_ID + '&t=' + Date.now();
        (document.head || document.body || document.documentElement).appendChild(script);
        
        console.log('🎯 Cypress Auto-Capture: Injected on', window.location.href);
    }
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectCaptureScript);
    } else {
        injectCaptureScript();
    }
})();
