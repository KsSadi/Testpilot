// Background service worker
chrome.runtime.onInstalled.addListener(() => {
  console.log('Cypress Event Capture extension installed');
  
  // Set default configuration
  chrome.storage.local.get(['serverUrl', 'sessionId', 'isEnabled'], (result) => {
    if (!result.serverUrl) {
      chrome.storage.local.set({
        serverUrl: 'http://127.0.0.1:3030',
        sessionId: Date.now().toString(),
        isEnabled: false // Disabled by default until user configures
      });
    }
  });
});

// Listen for messages from content script
chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
  if (request.action === 'getConfig') {
    chrome.storage.local.get(['serverUrl', 'sessionId', 'isEnabled'], (result) => {
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
});
