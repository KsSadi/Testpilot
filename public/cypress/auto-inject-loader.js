/**
 * Cypress Auto-Inject Loader
 * This tiny script checks localStorage and auto-loads the capture script
 * Paste this in browser console OR add as a bookmarklet to run on every page
 */
(function() {
    // Prevent double loading
    if (window.__cypressLoaderRan) return;
    window.__cypressLoaderRan = true;
    
    const autoInject = localStorage.getItem('cypress_auto_inject');
    const sessionId = localStorage.getItem('cypress_session_id');
    const serverUrl = localStorage.getItem('cypress_server_url');
    
    if (autoInject === 'true' && sessionId && serverUrl && !window.__cypressEventCaptureActive) {
        console.log('🔄 Cypress Auto-Loader: Loading capture script...');
        const script = document.createElement('script');
        script.src = serverUrl + '/cypress/capture-script.js?session=' + sessionId + '&t=' + Date.now();
        (document.head || document.body || document.documentElement).appendChild(script);
    }
})();
