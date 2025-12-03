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
  console.log('🔍 Content script: Requesting config...');
  chrome.runtime.sendMessage({ action: 'getConfig' }, (config) => {
    console.log('📦 Content script: Received config:', config);
    
    if (!config || !config.isEnabled) {
      console.log('⏹ Testpilot Event Capture: Disabled (isEnabled:', config?.isEnabled, ')');
      return;
    }

    const { serverUrl, sessionId, isPaused } = config;

    if (!sessionId) {
      console.warn('⚠️ Testpilot Event Capture: No session ID configured');
      return;
    }
    
    console.log('✅ Config validated - proceeding with injection');

    // Don't inject on the Testpilot dashboard itself
    const currentUrl = window.location.href;
    console.log('🌐 Current URL:', currentUrl);
    console.log('🏠 Server URL:', serverUrl);
    
    if (currentUrl.includes(serverUrl.replace('http://', '').replace('https://', ''))) {
      console.log('⏭ Testpilot: Skipping injection on dashboard');
      return;
    }

    console.log('🎯 Testpilot Event Capture: Starting injection...');
    console.log('📋 Session ID:', sessionId);
    console.log('🖥 Server URL:', serverUrl);

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
    const scriptUrl = serverUrl + '/cypress/capture-script.js?session=' + encodeURIComponent(sessionId) + '&t=' + Date.now();
    console.log('📜 Script URL:', scriptUrl);
    
    const script = document.createElement('script');
    script.src = scriptUrl;
    script.onerror = (e) => {
      console.error('❌ Testpilot Event Capture: Failed to load capture script!');
      console.error('URL:', scriptUrl);
      console.error('Error:', e);
      console.error('Is the server running at ' + serverUrl + '?');
      console.error('Try opening this URL in browser:', scriptUrl);
    };
    script.onload = () => {
      console.log('✅ Testpilot capture script loaded successfully!');
      console.log('📋 Session:', sessionId);
      console.log('🎬 Waiting for user interactions...');
    };

    const target = document.head || document.documentElement;
    console.log('📌 Injecting script into:', target.tagName);
    target.appendChild(script);
    console.log('✅ Script element added to DOM');
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

    return true; // Keep message channel open for async response
  });
})();
