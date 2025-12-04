@extends('layouts.backend.master')

@section('title', 'Projects')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">Cypress Testing</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Projects</span>
    </div>
@endsection

@section('content')
    {{-- Pending Invitations Section --}}
    <div id="invitationsCard" class="hidden mb-6 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold">
                <i class="fas fa-bell mr-2"></i>
                Pending Invitations
            </h3>
            <span id="invitationCount" class="bg-white text-purple-600 font-bold px-3 py-1 rounded-full text-sm"></span>
        </div>
        <div id="invitationsList" class="space-y-3">
            {{-- Invitations will be loaded here --}}
        </div>
    </div>

    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Test Projects</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Manage your test automation projects</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Create Project
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Projects</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $projects->count() }}</h3>
                </div>
                <div class="primary-color rounded-lg p-3">
                    <i class="fas fa-folder text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">My Projects</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $ownedCount }}</h3>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Shared with Me</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $sharedCount }}</h3>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-share-alt text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Modules</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $projects->sum(fn($p) => $p->modules->count()) }}</h3>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg p-3">
                    <i class="fas fa-cube text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Projects Table --}}
    <div class="bg-white rounded-xl p-4 md:p-5 border border-gray-100 shadow-sm">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle px-4 md:px-0">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Project</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Description</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Modules</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Created By</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($projects as $project)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-3">
                                    <div class="flex items-center">
                                        @if($project->logo)
                                            <div class="w-10 h-10 rounded-lg border-2 border-gray-200 overflow-hidden mr-3 flex-shrink-0">
                                                <img src="{{ asset('storage/' . $project->logo) }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center mr-3 flex-shrink-0">
                                                <i class="fas fa-folder text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-semibold text-gray-800">{{ $project->name }}</p>
                                                @if($project->created_by !== auth()->id())
                                                    <span class="badge-purple text-[10px] px-2 py-0.5">
                                                        <i class="fas fa-share-alt mr-1"></i>Shared
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500">Created {{ $project->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-sm text-gray-600">
                                    {{ Str::limit($project->description, 50) ?? 'No description' }}
                                </td>
                                <td class="py-3 px-3">
                                    @if($project->status === 'active')
                                        <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Active</span>
                                    @else
                                        <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <span class="badge-primary">{{ $project->modules->count() }} modules</span>
                                </td>
                                <td class="py-3 px-3 text-sm text-gray-600">
                                    {{ $project->creator->name ?? 'Unknown' }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('projects.show', $project) }}" class="p-2 text-cyan-600 hover:text-cyan-700 hover:bg-cyan-50 rounded-lg transition" title="View Project">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit Project">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Delete Project">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-folder-open text-5xl mb-3"></i>
                                        <p class="text-lg font-medium text-gray-500">No projects found</p>
                                        <p class="text-sm text-gray-400">Create your first project to get started</p>
                                        <a href="{{ route('projects.create') }}" class="btn-primary inline-block mt-4">
                                            <i class="fas fa-plus mr-2"></i>Create Project
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Project Count Info --}}
        @if($projects->count() > 0)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-500 text-center">
                Showing {{ $projects->count() }} project{{ $projects->count() !== 1 ? 's' : '' }}
                @if($ownedCount > 0 && $sharedCount > 0)
                    ({{ $ownedCount }} owned, {{ $sharedCount }} shared)
                @endif
            </p>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
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
            
            // Get shareable name
            const shareableName = inv.shareable ? (inv.shareable.name || inv.shareable.title) : 'Unknown';
            const shareableDesc = inv.shareable ? inv.shareable.description : '';
            
            return `
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4">
                    <div class="flex items-start justify-between gap-4">
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
                        <div class="flex gap-2">
                            <button onclick="handleInvitation(${inv.id}, 'accept')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition shadow-md whitespace-nowrap">
                                <i class="fas fa-check mr-1"></i>Accept
                            </button>
                            <button onclick="handleInvitation(${inv.id}, 'reject')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition shadow-md whitespace-nowrap">
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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification(data.message, 'success');
                
                // Reload page to show updated projects
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
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
</script>
@endpush
