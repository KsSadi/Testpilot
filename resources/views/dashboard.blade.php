<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Welcome, {{ Auth::user()->name }}!</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Project Invitations Card -->
            <div id="invitationsCard" class="hidden mb-6 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">
                        <i class="fas fa-bell mr-2"></i>
                        Project Invitations
                    </h3>
                    <span id="invitationCount" class="bg-white text-purple-600 font-bold px-3 py-1 rounded-full text-sm"></span>
                </div>
                <div id="invitationsList" class="space-y-3">
                    <!-- Invitations will be loaded here -->
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- User Info Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full p-4">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Name</p>
                            <p class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Email Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-full p-4">
                            <i class="fas fa-envelope text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Email</p>
                            <p class="text-lg font-semibold text-gray-800">{{ Auth::user()->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Role Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-full p-4">
                            <i class="fas fa-shield-alt text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Role</p>
                            <p class="text-xl font-bold text-gray-800 capitalize">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-2xl shadow-xl p-8 text-white">
                <h2 class="text-3xl font-bold mb-4">🎉 Welcome to Larakit!</h2>
                <p class="text-lg mb-6">Your authentication system is now fully functional with multiple login methods.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <i class="fas fa-envelope text-2xl mb-2"></i>
                        <p class="font-semibold">Email Login</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <i class="fas fa-mobile-alt text-2xl mb-2"></i>
                        <p class="font-semibold">Mobile/OTP</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <i class="fab fa-google text-2xl mb-2"></i>
                        <p class="font-semibold">Social Login</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <i class="fas fa-cog text-2xl mb-2"></i>
                        <p class="font-semibold">DB Configurable</p>
                    </div>
                </div>
            </div>

            <!-- Account Details -->
            <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Account Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Account Status</p>
                        <p class="text-lg font-semibold text-green-600 capitalize">{{ Auth::user()->status }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Mobile Number</p>
                        <p class="text-lg font-semibold text-gray-800">{{ Auth::user()->mobile ?? 'Not provided' }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Email Verified</p>
                        <p class="text-lg font-semibold {{ Auth::user()->email_verified_at ? 'text-green-600' : 'text-orange-600' }}">
                            {{ Auth::user()->email_verified_at ? 'Yes' : 'No' }}
                        </p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Member Since</p>
                        <p class="text-lg font-semibold text-gray-800">{{ Auth::user()->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Quick Links</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/projects" class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-xl p-6 transition transform hover:scale-105 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Projects</p>
                                <p class="text-3xl font-bold mt-2">View All</p>
                            </div>
                            <i class="fas fa-folder text-5xl opacity-30"></i>
                        </div>
                    </a>
                    <a href="/modules" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl p-6 transition transform hover:scale-105 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Modules</p>
                                <p class="text-3xl font-bold mt-2">Browse</p>
                            </div>
                            <i class="fas fa-cubes text-5xl opacity-30"></i>
                        </div>
                    </a>
                    <a href="/test-cases" class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white rounded-xl p-6 transition transform hover:scale-105 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-90">Test Cases</p>
                                <p class="text-3xl font-bold mt-2">Explore</p>
                            </div>
                            <i class="fas fa-vial text-5xl opacity-30"></i>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Load pending invitations
        async function loadInvitations() {
            try {
                const response = await fetch('/invitations/pending');
                const data = await response.json();
                
                if (data.success && data.invitations.length > 0) {
                    displayInvitations(data.invitations);
                }
            } catch (error) {
                console.error('Failed to load invitations:', error);
            }
        }

        function displayInvitations(invitations) {
            const card = document.getElementById('invitationsCard');
            const list = document.getElementById('invitationsList');
            const count = document.getElementById('invitationCount');
            
            card.classList.remove('hidden');
            count.textContent = invitations.length;
            
            list.innerHTML = invitations.map(inv => {
                // Determine shareable type badge
                const shareType = inv.shareable_type ? 
                    (inv.shareable_type.includes('Project') ? 'Project' :
                     inv.shareable_type.includes('Module') ? 'Module' :
                     inv.shareable_type.includes('TestCase') ? 'Test Case' : 'Item') 
                    : 'Project';
                
                const shareTypeIcon = shareType === 'Project' ? '📁' :
                    shareType === 'Module' ? '📦' :
                    shareType === 'Test Case' ? '🧪' : '📄';
                
                const shareTypeBadge = shareType === 'Project' ? 'bg-blue-500' :
                    shareType === 'Module' ? 'bg-green-500' :
                    shareType === 'Test Case' ? 'bg-orange-500' : 'bg-gray-500';
                
                // Get shareable name (backward compatible)
                const shareableName = inv.shareable ? inv.shareable.name : (inv.project ? inv.project.name : 'Unknown');
                const shareableDesc = inv.shareable ? inv.shareable.description : (inv.project ? inv.project.description : '');
                
                return `
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-purple-600 font-bold">
                                        ${inv.shared_by.name.charAt(0)}
                                    </div>
                                    <div>
                                        <p class="font-semibold">${inv.shared_by.name}</p>
                                        <p class="text-xs opacity-90">${inv.shared_by.email}</p>
                                    </div>
                                </div>
                                <p class="text-sm mb-1">invited you to collaborate on:</p>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-block px-2 py-1 ${shareTypeBadge} text-white rounded text-xs font-bold">
                                        ${shareType}
                                    </span>
                                    <p class="font-bold text-lg">${shareTypeIcon} ${shareableName}</p>
                                </div>
                                ${shareableDesc ? `<p class="text-sm opacity-90 mb-2">${shareableDesc}</p>` : ''}
                                <span class="inline-block px-3 py-1 bg-white bg-opacity-30 rounded-full text-xs font-semibold">
                                    ${inv.role === 'editor' ? '✏️ Editor' : '👁️ Viewer'} Access
                                </span>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <button onclick="handleInvitation(${inv.id}, 'accept')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition shadow-md">
                                    <i class="fas fa-check mr-1"></i>Accept
                                </button>
                                <button onclick="handleInvitation(${inv.id}, 'reject')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition shadow-md">
                                    <i class="fas fa-times mr-1"></i>Decline
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        async function handleInvitation(shareId, action) {
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            try {
                const response = await fetch(`/invitations/${shareId}/${action}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    loadInvitations(); // Reload to update list
                    
                    // If accepted, redirect to project after a moment
                    if (action === 'accept' && data.project) {
                        setTimeout(() => {
                            window.location.href = `/projects/${data.project.id}`;
                        }, 1500);
                    }
                } else {
                    showNotification(data.message, 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            } catch (error) {
                showNotification('An error occurred', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        }

        function showNotification(message, type) {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500'
            };
            
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            notification.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => notification.remove(), 3000);
        }

        // Load invitations on page load
        document.addEventListener('DOMContentLoaded', loadInvitations);

        // Handle URL parameters for direct accept/reject from email
        const urlParams = new URLSearchParams(window.location.search);
        const action = urlParams.get('action');
        const shareId = urlParams.get('share');
        
        if (action && shareId && (action === 'accept' || action === 'reject')) {
            handleInvitation(parseInt(shareId), action);
            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
</body>
</html>
