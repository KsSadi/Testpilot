// Popup script
document.addEventListener('DOMContentLoaded', () => {
  const serverUrlInput = document.getElementById('serverUrl');
  const sessionIdInput = document.getElementById('sessionId');
  const statusDiv = document.getElementById('status');
  const currentSessionSpan = document.getElementById('currentSession');
  const enableBtn = document.getElementById('enableBtn');
  const disableBtn = document.getElementById('disableBtn');
  const saveBtn = document.getElementById('saveBtn');
  const fetchSessionBtn = document.getElementById('fetchSessionBtn');
  
  // Load current configuration
  chrome.storage.local.get(['serverUrl', 'sessionId', 'isEnabled'], (result) => {
    serverUrlInput.value = result.serverUrl || 'http://127.0.0.1:3030';
    sessionIdInput.value = result.sessionId || '';
    currentSessionSpan.textContent = result.sessionId || 'Not set';
    
    updateStatus(result.isEnabled || false);
  });
  
  // Update status display
  function updateStatus(isEnabled) {
    if (isEnabled) {
      statusDiv.className = 'status enabled';
      statusDiv.textContent = '✅ Enabled - Capturing events on all pages';
      enableBtn.disabled = true;
      disableBtn.disabled = false;
    } else {
      statusDiv.className = 'status disabled';
      statusDiv.textContent = '⚠️ Disabled - Not capturing events';
      enableBtn.disabled = false;
      disableBtn.disabled = true;
    }
  }
  
  // Fetch session ID from dashboard
  fetchSessionBtn.addEventListener('click', () => {
    const serverUrl = serverUrlInput.value.trim();
    if (!serverUrl) {
      alert('❌ Please enter Server URL first!');
      return;
    }
    
    fetchSessionBtn.textContent = '⏳ Fetching...';
    fetchSessionBtn.disabled = true;
    
    // Use API endpoint to get current session
    fetch(serverUrl + '/cypress/current-session', {
      credentials: 'include'
    })
      .then(response => response.json())
      .then(data => {
        if (data.success && data.session_id) {
          const sessionId = data.session_id;
          sessionIdInput.value = sessionId;
          currentSessionSpan.textContent = sessionId;
          
          // Auto-save
          chrome.storage.local.set({ sessionId: sessionId }, () => {
            alert('✅ Session ID fetched and saved!\n\nSession: ' + sessionId + '\n\nNow click "Enable" to start capturing.');
          });
        } else {
          alert('❌ Could not get session ID from server.');
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        alert('❌ Error fetching session ID.\n\nMake sure:\n1. Server is running at: ' + serverUrl + '\n2. You are logged in to the dashboard');
      })
      .finally(() => {
        fetchSessionBtn.textContent = '🔄 Fetch';
        fetchSessionBtn.disabled = false;
      });
  });
  
  // Save settings
  saveBtn.addEventListener('click', () => {
    const config = {
      serverUrl: serverUrlInput.value.trim(),
      sessionId: sessionIdInput.value.trim()
    };
    
    if (!config.serverUrl || !config.sessionId) {
      alert('❌ Please enter both Server URL and Session ID');
      return;
    }
    
    chrome.storage.local.set(config, () => {
      currentSessionSpan.textContent = config.sessionId;
      alert('✅ Settings saved!\n\nNow click "Enable" to start capturing.');
    });
  });
  
  // Enable capturing
  enableBtn.addEventListener('click', () => {
    chrome.storage.local.get(['sessionId'], (result) => {
      if (!result.sessionId) {
        alert('❌ Please save settings first!');
        return;
      }
      
      chrome.storage.local.set({ isEnabled: true }, () => {
        updateStatus(true);
        alert('✅ Event capture ENABLED!\n\nVisit any website and events will be captured automatically.');
      });
    });
  });
  
  // Disable capturing
  disableBtn.addEventListener('click', () => {
    chrome.storage.local.set({ isEnabled: false }, () => {
      updateStatus(false);
      alert('⚠️ Event capture DISABLED.\n\nNo events will be captured until you enable it again.');
    });
  });
});
