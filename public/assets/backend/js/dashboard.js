// Mobile Sidebar Toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.toggle('mobile-open');
    overlay.classList.toggle('active');
    
    // Prevent body scroll when sidebar is open on mobile
    if (sidebar.classList.contains('mobile-open')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const mobileButton = document.querySelector('.mobile-menu-button');
    
    if (sidebar && sidebar.classList.contains('mobile-open') && 
        !sidebar.contains(event.target) && 
        mobileButton && !mobileButton.contains(event.target)) {
        toggleSidebar();
    }
});

// Close sidebar on window resize if switching to desktop view
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar && window.innerWidth > 768 && sidebar.classList.contains('mobile-open')) {
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// Toggle Notifications Dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userMenuDropdown');
    
    // Close user menu if open
    if (userDropdown && !userDropdown.classList.contains('hidden')) {
        userDropdown.classList.add('hidden');
    }
    
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Toggle User Menu Dropdown
function toggleUserMenu() {
    const dropdown = document.getElementById('userMenuDropdown');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    // Close notifications if open
    if (notificationDropdown && !notificationDropdown.classList.contains('hidden')) {
        notificationDropdown.classList.add('hidden');
    }
    
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const notificationBtn = document.querySelector('.notification-dropdown');
    const userMenuBtn = document.querySelector('.user-menu-dropdown');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const userMenuDropdown = document.getElementById('userMenuDropdown');
    
    // Close notification dropdown if clicking outside
    if (notificationDropdown && notificationBtn && !notificationBtn.contains(event.target)) {
        notificationDropdown.classList.add('hidden');
    }
    
    // Close user menu dropdown if clicking outside
    if (userMenuDropdown && userMenuBtn && !userMenuBtn.contains(event.target)) {
        userMenuDropdown.classList.add('hidden');
    }
});

// Keyboard shortcut for search (Ctrl/Cmd + K)
document.addEventListener('keydown', function(event) {
    if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
        event.preventDefault();
        const searchInput = document.querySelector('.header-search input');
        if (searchInput) {
            searchInput.focus();
        }
    }
});

// Toggle Submenu
function toggleSubmenu(menuId) {
    const menu = document.getElementById(menuId);
    const icon = document.getElementById(menuId + 'Icon');
    const parent = menu ? menu.parentElement : null;
    
    if (menu && icon && parent) {
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            parent.classList.add('submenu-open');
        } else {
            menu.classList.add('hidden');
            parent.classList.remove('submenu-open');
        }
    }
}

// Update current time
function updateTime() {
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        timeElement.textContent = timeString;
    }
}

// Initialize time update
if (document.getElementById('currentTime')) {
    updateTime();
    setInterval(updateTime, 60000);
}
