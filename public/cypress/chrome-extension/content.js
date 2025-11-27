// Content script - Injects the Testpilot capture script on every page
(function() {
  'use strict';
  
  // Listen for events captured by the page script (set up BEFORE injection)
  window.addEventListener('testpilot-event-captured', (event) => {
    const count = event.detail.count;
    console.log('📊 Content script received event count:', count);
    
    // Forward to background script
    chrome.runtime.sendMessage({
      action: 'eventCaptured',
      count: count
    }).catch((err) => {
      console.error('Failed to send to background:', err);
    });
  });
  
  // Get configuration from extension storage
  chrome.runtime.sendMessage({ action: 'getConfig' }, (config) => {
    if (!config || !config.isEnabled) {
      console.log('Testpilot Event Capture: Disabled');
      return;
    }
    
    const { serverUrl, sessionId, isPaused } = config;
    
    if (!sessionId) {
      console.warn('Testpilot Event Capture: No session ID configured');
      return;
    }
    
    // Don't inject on the Testpilot dashboard itself
    if (window.location.href.includes('/projects/') || window.location.href.includes('/test-cases/')) {
      return;
    }
    
    console.log('🎯 Testpilot Event Capture: Injecting on', window.location.href);
    console.log('📋 Using Test Case Session ID:', sessionId);
    
    // Set the correct session from extension
    localStorage.setItem('cypress_session_id', sessionId);
    localStorage.setItem('cypress_server_url', serverUrl);
    localStorage.setItem('cypress_auto_inject', 'true');
    
    // Set paused state if applicable
    if (isPaused) {
      localStorage.setItem('testpilot_paused', 'true');
    } else {
      localStorage.removeItem('testpilot_paused');
    }
    
    console.log('✅ localStorage updated with Test Case session:', sessionId);
    
    // Inject the capture script with session ID in URL (this takes priority)
    const script = document.createElement('script');
    script.src = serverUrl + '/cypress/capture-script.js?session=' + encodeURIComponent(sessionId) + '&t=' + Date.now();
    script.onerror = () => {
      console.error('Testpilot Event Capture: Failed to load capture script. Is the server running at ' + serverUrl + '?');
    };
    script.onload = () => {
      console.log('✅ Testpilot capture script loaded with Test Case session:', sessionId);
    };
    
    (document.head || document.documentElement).appendChild(script);
  });
  
  // Listen for pause state updates from popup
  chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.action === 'updatePauseState') {
      // Use postMessage to communicate with page context
      window.postMessage({
        type: 'TESTPILOT_UPDATE_PAUSE',
        isPaused: message.isPaused
      }, '*');
      
      if (message.isPaused) {
        localStorage.setItem('testpilot_paused', 'true');
      } else {
        localStorage.removeItem('testpilot_paused');
      }
      
      console.log('🎯 Testpilot: Pause state updated:', message.isPaused);
      sendResponse({ success: true });
    }
    
    if (message.action === 'stopCapture') {
      // Use postMessage to stop capture
      window.postMessage({
        type: 'TESTPILOT_STOP_CAPTURE'
      }, '*');
      
      localStorage.removeItem('testpilot_paused');
      localStorage.removeItem('cypress_auto_inject');
      
      console.log('🎯 Testpilot: Capture stopped');
      sendResponse({ success: true });
    }
  });
})();
