// Background service worker
let eventCount = 0;

chrome.runtime.onInstalled.addListener(() => {
  console.log('Testpilot Event Capture extension installed');
  
  // Set default configuration
  chrome.storage.local.get(['serverUrl', 'sessionId', 'isEnabled'], (result) => {
    if (!result.serverUrl) {
      chrome.storage.local.set({
        serverUrl: 'http://127.0.0.1:8000',
        sessionId: '',
        isEnabled: false, // Disabled by default until user configures
        isPaused: false,
        eventCount: 0
      });
    }
  });
});

// Listen for messages from content script
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
  if (request.action === 'getConfig') {
    chrome.storage.local.get(['serverUrl', 'sessionId', 'isEnabled', 'isPaused'], (result) => {
      sendResponse(result);
    });
    return true; // Will respond asynchronously
  }
  
  if (request.action === 'updateConfig') {
    chrome.storage.local.set(request.config, () => {
      sendResponse({ success: true });
    });
    return true;
  }
  
  // Handle event captured notification
  if (request.action === 'eventCaptured') {
    // Update event count
    eventCount = request.count;
    
    console.log('🎯 Background: Event count updated to', eventCount);
    
    // Update storage
    chrome.storage.local.set({ eventCount: eventCount });
    
    // Notify popup if it's open
    chrome.runtime.sendMessage({
      type: 'EVENT_CAPTURED',
      count: eventCount
    }).catch(() => {
      // Popup might not be open, ignore error
      console.log('Popup not open, count saved in storage');
    });
    
    sendResponse({ success: true, count: eventCount });
    return true;
  }
  
  // Reset event count when starting new session
  if (request.action === 'resetEventCount') {
    eventCount = 0;
    chrome.storage.local.set({ eventCount: 0 });
    sendResponse({ success: true });
    return true;
  }
});
