@extends('layouts.backend.master')

@section('title', $project->name)

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">Cypress Testing</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-cyan-600">Projects</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">{{ $project->name }}</span>
    </div>
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                @if($project->logo)
                    <div class="w-12 h-12 rounded-lg border-2 border-gray-200 overflow-hidden flex-shrink-0">
                        <img src="{{ asset('storage/' . $project->logo) }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                    </div>
                @endif
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ $project->name }}</h2>
                        @if($project->status === 'active')
                            <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Active</span>
                        @else
                            <span class="badge-danger"><i class="fas fa-circle text-[8px] mr-1"></i>Inactive</span>
                        @endif
                    </div>
                    <p class="text-gray-500 text-xs md:text-sm">{{ $project->description ?? 'No description provided' }}</p>
                    @if($project->url)
                        <a href="{{ $project->url }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-cyan-600 hover:text-cyan-700 mt-1">
                            <i class="fas fa-external-link-alt"></i>
                            <span>{{ $project->url }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-2 w-full md:w-auto">
            <a href="{{ route('projects.index') }}" class="btn-secondary flex-1 md:flex-none text-center">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            @if($project->created_by === auth()->id())
                <button onclick="openShareModal()" class="btn-secondary flex-1 md:flex-none text-center bg-purple-500 text-white hover:bg-purple-600">
                    <i class="fas fa-share-alt mr-2"></i>Share
                </button>
            @endif
            <a href="{{ route('modules.create', $project) }}" class="btn-primary flex-1 md:flex-none text-center">
                <i class="fas fa-plus mr-2"></i>Add Module
            </a>
            @if($project->created_by === auth()->id())
                <a href="{{ route('projects.edit', $project) }}" class="btn-warning flex-1 md:flex-none text-center">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Modules</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $project->modules->count() }}</h3>
                </div>
                <div class="primary-color rounded-lg p-3">
                    <i class="fas fa-cube text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Test Cases</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $project->testCases->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg p-3">
                    <i class="fas fa-tasks text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Active Modules</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $project->modules->where('status', 'active')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Created By</p>
                    <h3 class="text-base font-bold text-gray-800">{{ $project->creator->name ?? 'Unknown' }}</h3>
                    <p class="text-xs text-gray-500">{{ $project->created_at->format('M d, Y') }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Modules Section --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Modules</h3>
                <p class="text-sm text-gray-500 mt-1">Organize your test cases into modules</p>
            </div>
            @if($project->modules->count() > 0)
            <a href="{{ route('modules.create', $project) }}" class="btn-primary mt-3 md:mt-0">
                <i class="fas fa-plus mr-2"></i>Add Module
            </a>
            @endif
        </div>

        @if($project->modules->count() > 0)
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle px-4 md:px-0">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Order</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Module</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Description</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Test Cases</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-center py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($project->modules as $module)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-5">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-cyan-50 text-cyan-700 rounded-lg font-semibold text-sm">
                                        {{ $module->order }}
                                    </span>
                                </td>
                                <td class="py-3 px-5">
                                    <a href="{{ route('modules.show', [$project, $module]) }}" class="text-cyan-600 hover:text-cyan-700 font-semibold hover:underline">
                                        {{ $module->name }}
                                    </a>
                                </td>
                                <td class="py-3 px-5">
                                    <p class="text-sm text-gray-600">{{ Str::limit($module->description, 50) ?? 'No description' }}</p>
                                </td>
                                <td class="py-3 px-5">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold">
                                            <i class="fas fa-tasks mr-1 text-[10px]"></i>
                                            {{ $module->testCases->count() }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-5">
                                    @if($module->status === 'active')
                                        <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Active</span>
                                    @else
                                        <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('modules.show', [$project, $module]) }}" class="p-2 text-cyan-600 hover:text-cyan-700 hover:bg-cyan-50 rounded-lg transition" title="View Module">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('modules.edit', [$project, $module]) }}" class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit Module">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('modules.destroy', [$project, $module]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure? This will delete all test cases in this module.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Delete Module">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        {{-- Empty State --}}
        <div class="py-16 px-4 text-center">
            <div class="primary-color rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cube text-white text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Modules Yet</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                Get started by creating your first module to organize test cases
            </p>
            <a href="{{ route('modules.create', $project) }}" class="btn-primary inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Create First Module
            </a>
        </div>
        @endif
    </div>

    {{-- Share Modal --}}
    <div id="shareModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Share Project</h3>
                        <p class="text-sm text-gray-500 mt-1">Invite team members to collaborate on this project</p>
                    </div>
                    <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Invite Form -->
                <div class="p-6 border-b border-gray-200">
                    <form id="inviteForm" class="flex gap-3">
                        <input type="email" id="inviteEmail" placeholder="Enter email address" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <select id="inviteRole" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="editor">Editor</option>
                            <option value="viewer">Viewer</option>
                        </select>
                        <button type="submit" class="btn-primary bg-purple-500 hover:bg-purple-600">
                            <i class="fas fa-paper-plane mr-2"></i>Invite
                        </button>
                    </form>
                </div>

                <!-- Collaborators List -->
                <div class="p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">Collaborators</h4>
                    <div id="collaboratorsList" class="space-y-3">
                        <div class="text-center text-gray-500 py-4">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let projectId = "{{ $project->id }}";

function openShareModal() {
    document.getElementById('shareModal').classList.remove('hidden');
    loadCollaborators();
}

function closeShareModal() {
    document.getElementById('shareModal').classList.add('hidden');
}

// Invite Form Submission
document.getElementById('inviteForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('inviteEmail').value;
    const role = document.getElementById('inviteRole').value;
    const btn = e.target.querySelector('button[type="submit"]');
    const originalHTML = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    
    try {
        const response = await fetch(`/projects/${projectId}/share`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email, role })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showNotification('Invitation sent successfully!', 'success');
            
            // Clear form
            document.getElementById('inviteEmail').value = '';
            
            // Reload collaborators
            loadCollaborators();
        } else {
            showNotification(data.message || 'Failed to send invitation', 'error');
        }
    } catch (error) {
        showNotification('An error occurred', 'error');
        console.error(error);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
});

// Load Collaborators
async function loadCollaborators() {
    const container = document.getElementById('collaboratorsList');
    container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-gray-400"></i></div>';
    
    try {
        const response = await fetch(`/projects/${projectId}/share`);
        const data = await response.json();
        
        if (data.success) {
            renderCollaborators(data.collaborators, data.is_owner);
        }
    } catch (error) {
        container.innerHTML = '<div class="text-center py-4 text-red-500">Failed to load collaborators</div>';
        console.error(error);
    }
}

// Render Collaborators
function renderCollaborators(collaborators, isOwner) {
    const container = document.getElementById('collaboratorsList');
    
    if (collaborators.length === 0) {
        container.innerHTML = `
            <div class="text-center py-6 bg-gray-50 rounded-lg">
                <i class="fas fa-user-friends text-gray-300 text-3xl mb-2"></i>
                <p class="text-sm text-gray-500">No collaborators yet</p>
                <p class="text-xs text-gray-400 mt-1">Invite people to collaborate on this project</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    collaborators.forEach(share => {
        const statusBadge = share.status === 'pending' 
            ? '<span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Pending</span>'
            : '<span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">Active</span>';
        
        const roleBadge = share.role === 'editor'
            ? '<span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded font-medium">Editor</span>'
            : '<span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-medium">Viewer</span>';
        
        html += `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold">
                        ${share.shared_with.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">${share.shared_with.name}</p>
                        <p class="text-xs text-gray-500">${share.shared_with.email}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    ${statusBadge}
                    ${isOwner ? `
                        <select onchange="updateRole(${share.id}, this.value)" class="text-xs px-2 py-1 border border-gray-300 rounded bg-white">
                            <option value="viewer" ${share.role === 'viewer' ? 'selected' : ''}>Viewer</option>
                            <option value="editor" ${share.role === 'editor' ? 'selected' : ''}>Editor</option>
                        </select>
                        <button onclick="removeCollaborator(${share.id})" class="text-red-600 hover:text-red-700 px-2 py-1 hover:bg-red-50 rounded transition" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : roleBadge}
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Update Role
async function updateRole(shareId, newRole) {
    try {
        const response = await fetch(`/projects/${projectId}/share/${shareId}/role`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ role: newRole })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Role updated successfully', 'success');
        } else {
            showNotification(data.message || 'Failed to update role', 'error');
            loadCollaborators();
        }
    } catch (error) {
        showNotification('An error occurred', 'error');
        loadCollaborators();
    }
}

// Remove Collaborator
async function removeCollaborator(shareId) {
    if (!confirm('Are you sure you want to remove this collaborator?')) return;
    
    try {
        const response = await fetch(`/projects/${projectId}/share/${shareId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Collaborator removed', 'success');
            loadCollaborators();
        } else {
            showNotification(data.message || 'Failed to remove collaborator', 'error');
        }
    } catch (error) {
        showNotification('An error occurred', 'error');
    }
}

// Show Notification
function showNotification(message, type) {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('animate-slide-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<style>
@keyframes slide-in {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes slide-out {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}
.animate-slide-in { animation: slide-in 0.3s ease-out; }
.animate-slide-out { animation: slide-out 0.3s ease-in; }
</style>
@endpush
