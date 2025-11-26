// Content script - Injects the Cypress capture script on every page
(function() {
  'use strict';
  
  // Get configuration from extension storage
  chrome.runtime.sendMessage({ action: 'getConfig' }, (config) => {
    if (!config || !config.isEnabled) {
      console.log('Cypress Event Capture: Disabled');
      return;
    }
    
    const { serverUrl, sessionId } = config;
    
    // Don't inject on the Cypress dashboard itself
    if (window.location.href.includes(serverUrl + '/cypress')) {
      return;
    }
    
    // Check if already injected
    if (window.__cypressEventCaptureActive) {
      return;
    }
    
    console.log('🎯 Cypress Event Capture: Injecting on', window.location.href);
    console.log('📋 Using Session ID from extension:', sessionId);
    
    // CRITICAL: Clear old localStorage session BEFORE injecting script
    const oldSession = localStorage.getItem('cypress_session_id');
    if (oldSession && oldSession !== sessionId) {
      console.warn('⚠️ Clearing old localStorage session:', oldSession);
      localStorage.removeItem('cypress_session_id');
    }
    
    // Set the correct session from extension
    localStorage.setItem('cypress_session_id', sessionId);
    localStorage.setItem('cypress_server_url', serverUrl);
    localStorage.setItem('cypress_auto_inject', 'true');
    
    console.log('✅ localStorage updated with session:', sessionId);
    
    // Inject the capture script with session ID in URL (this takes priority)
    const script = document.createElement('script');
    script.src = serverUrl + '/cypress/capture-script.js?session=' + encodeURIComponent(sessionId) + '&t=' + Date.now();
    script.onerror = () => {
      console.error('Cypress Event Capture: Failed to load capture script. Is the server running?');
    };
    script.onload = () => {
      console.log('✅ Cypress capture script loaded with session:', sessionId);
    };
    
    (document.head || document.documentElement).appendChild(script);
  });
})();
