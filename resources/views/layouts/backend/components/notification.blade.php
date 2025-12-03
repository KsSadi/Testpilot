<!-- Global Notification Container -->
<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>

<style>
/* Notification Styles */
.notification {
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    padding: 16px 20px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 300px;
    animation: slideIn 0.3s ease-out, fadeOut 0.3s ease-in 4.7s;
    border-left: 4px solid;
    position: relative;
    overflow: hidden;
}

.notification::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: currentColor;
    animation: progress 5s linear;
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

.notification-success {
    border-left-color: #16a34a;
    color: #16a34a;
}

.notification-error {
    border-left-color: #dc2626;
    color: #dc2626;
}

.notification-warning {
    border-left-color: #f59e0b;
    color: #f59e0b;
}

.notification-info {
    border-left-color: #3b82f6;
    color: #3b82f6;
}

.notification-icon {
    font-size: 24px;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    margin: 0 0 4px 0;
    color: #1f2937;
}

.notification-message {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.notification-close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    font-size: 20px;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all 0.2s;
    flex-shrink: 0;
}

.notification-close:hover {
    background: #f3f4f6;
    color: #1f2937;
}
</style>

<script>
// Global Notification System
function showNotification(type, title, message) {
    const container = document.getElementById('notification-container');
    if (!container) return;
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
    };

    notification.innerHTML = `
        <div class="notification-icon">${icons[type]}</div>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;

    container.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Auto-show notification from session flash messages
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showNotification('success', 'Success!', '{{ session('success') }}');
    @endif

    @if(session('error'))
        showNotification('error', 'Error!', '{{ session('error') }}');
    @endif

    @if(session('warning'))
        showNotification('warning', 'Warning!', '{{ session('warning') }}');
    @endif

    @if(session('info'))
        showNotification('info', 'Info', '{{ session('info') }}');
    @endif

    @if($errors->any())
        showNotification('error', 'Validation Error', '{{ $errors->first() }}');
    @endif
});
</script>
