// Popup script
document.addEventListener('DOMContentLoaded', () => {
  const serverUrlInput = document.getElementById('serverUrl');
  const sessionIdInput = document.getElementById('sessionId');
  const saveBtn = document.getElementById('saveBtn');
  
  const setupView = document.getElementById('setupView');
  const activeView = document.getElementById('activeView');
  const statusSection = document.getElementById('statusSection');
  const statusBadge = document.getElementById('statusBadge');
  const statusText = document.getElementById('statusText');
  const eventCounter = document.getElementById('eventCounter');
  const currentSession = document.getElementById('currentSession');
  const currentServer = document.getElementById('currentServer');
  
  const pauseBtn = document.getElementById('pauseBtn');
  const stopBtn = document.getElementById('stopBtn');
  const changeSessionBtn = document.getElementById('changeSessionBtn');
  
  let isPaused = false;
  let eventCount = 0;
  
  // Load current configuration
  chrome.storage.local.get(['serverUrl', 'sessionId', 'isEnabled', 'isPaused', 'eventCount'], (result) => {
    serverUrlInput.value = result.serverUrl || 'http://127.0.0.1:8000';
    sessionIdInput.value = result.sessionId || '';
    isPaused = result.isPaused || false;
    eventCount = result.eventCount || 0;
    
    if (result.isEnabled && result.sessionId) {
      showActiveView(result.serverUrl, result.sessionId);
    } else {
      showSetupView();
    }
  });
  
  // Show setup view
  function showSetupView() {
    setupView.style.display = 'block';
    activeView.style.display = 'none';
  }
  
  // Show active view
  function showActiveView(serverUrl, sessionId) {
    setupView.style.display = 'none';
    activeView.style.display = 'block';
    
    currentSession.textContent = sessionId;
    currentServer.textContent = serverUrl.replace('http://', '').replace('https://', '');
    eventCounter.textContent = eventCount;
    
    updatePauseState();
  }
  
  // Update pause state UI
  function updatePauseState() {
    if (isPaused) {
      statusSection.classList.add('paused');
      statusSection.classList.remove('recording');
      statusBadge.classList.add('paused');
      statusBadge.classList.remove('recording');
      statusText.textContent = 'Paused';
      pauseBtn.textContent = '▶️ Resume';
      pauseBtn.className = 'btn btn-success';
    } else {
      statusSection.classList.remove('paused');
      statusSection.classList.add('recording');
      statusBadge.classList.remove('paused');
      statusBadge.classList.add('recording');
      statusText.textContent = 'Recording';
      pauseBtn.textContent = '⏸ Pause';
      pauseBtn.className = 'btn btn-warning';
    }
  }
  
  // Save and start
  saveBtn.addEventListener('click', () => {
    const serverUrl = serverUrlInput.value.trim();
    const sessionId = sessionIdInput.value.trim();
    
    if (!serverUrl || !sessionId) {
      showNotification('❌ Error', 'Please enter both Server URL and Session ID');
      return;
    }
    
    // Validate session ID format
    if (!sessionId.startsWith('tc_')) {
      if (!confirm('⚠️ Warning: Session ID should start with "tc_"\n\nMake sure you copied it from a test case page.\n\nContinue anyway?')) {
        return;
      }
    }
    
    // Reset event count for new session
    eventCount = 0;
    isPaused = false;
    
    chrome.storage.local.set({
      serverUrl: serverUrl,
      sessionId: sessionId,
      isEnabled: true,
      isPaused: false,
      eventCount: 0
    }, () => {
      console.log('✅ Configuration saved to chrome.storage.local');
      console.log('Server URL:', serverUrl);
      console.log('Session ID:', sessionId);
      console.log('isEnabled: true');
      
      // Notify background to reset count
      chrome.runtime.sendMessage({ action: 'resetEventCount' });
      
      showActiveView(serverUrl, sessionId);
      showNotification('✅ Started', 'Event capture is now active! Reloading all tabs...');
      
      // Reload all tabs to inject script
      chrome.tabs.query({}, (tabs) => {
        console.log('🔄 Reloading', tabs.length, 'tabs...');
        let reloadedCount = 0;
        tabs.forEach((tab) => {
          if (tab.url && !tab.url.startsWith('chrome://') && !tab.url.startsWith('chrome-extension://')) {
            console.log('🔄 Reloading tab:', tab.url);
            chrome.tabs.reload(tab.id);
            reloadedCount++;
          }
        });
        console.log('✅ Reloaded', reloadedCount, 'tabs');
      });
    });
  });
  
  // Pause/Resume button
  pauseBtn.addEventListener('click', () => {
    isPaused = !isPaused;
    
    chrome.storage.local.set({ isPaused: isPaused }, () => {
      updatePauseState();
      showNotification(
        isPaused ? '⏸ Paused' : '▶️ Resumed',
        isPaused ? 'Event capture paused' : 'Event capture resumed'
      );
      
      // Update all active tabs
      chrome.tabs.query({}, (tabs) => {
        tabs.forEach((tab) => {
          if (tab.url && !tab.url.startsWith('chrome://') && !tab.url.startsWith('chrome-extension://')) {
            chrome.tabs.sendMessage(tab.id, {
              action: 'updatePauseState',
              isPaused: isPaused
            }).catch(() => {
              // Tab might not have content script, ignore
            });
          }
        });
      });
    });
  });
  
  // Stop button
  stopBtn.addEventListener('click', () => {
    if (!confirm('⏹ Stop event capture?\n\nThis will disable capturing on all pages.\n\nTotal events captured: ' + eventCount)) {
      return;
    }
    
    chrome.storage.local.set({
      isEnabled: false,
      isPaused: false,
      eventCount: 0
    }, () => {
      // Send stop message to all tabs
      chrome.tabs.query({}, (tabs) => {
        tabs.forEach((tab) => {
          if (tab.url && !tab.url.startsWith('chrome://') && !tab.url.startsWith('chrome-extension://')) {
            chrome.tabs.sendMessage(tab.id, {
              action: 'stopCapture'
            }).catch(() => {});
          }
        });
      });
      
      showSetupView();
      showNotification('⏹ Stopped', 'Event capture has been stopped');
    });
  });
  
  // Change session button
  changeSessionBtn.addEventListener('click', () => {
    if (!confirm('🔄 Change session?\n\nThis will stop current capture and return to setup.\n\nEvents captured: ' + eventCount)) {
      return;
    }
    
    chrome.storage.local.set({
      isEnabled: false,
      isPaused: false,
      eventCount: 0
    }, () => {
      showSetupView();
    });
  });
  
  // Listen for event count updates from background
  chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    console.log('📬 Popup received message:', message);
    if (message.type === 'EVENT_CAPTURED') {
      eventCount = message.count;
      eventCounter.textContent = eventCount;
      chrome.storage.local.set({ eventCount: eventCount });
      console.log('✅ Popup updated event count to:', eventCount);
    }
  });
  
  // Show notification (simple visual feedback)
  function showNotification(title, message) {
    // Create temporary notification overlay
    const notification = document.createElement('div');
    notification.style.cssText = `
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.9);
      color: white;
      padding: 20px 30px;
      border-radius: 12px;
      z-index: 10000;
      text-align: center;
      animation: fadeIn 0.2s ease-out;
    `;
    notification.innerHTML = `
      <div style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">${title}</div>
      <div style="font-size: 13px; opacity: 0.9;">${message}</div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.style.animation = 'fadeOut 0.2s ease-out';
      setTimeout(() => notification.remove(), 200);
    }, 2000);
  }
});
