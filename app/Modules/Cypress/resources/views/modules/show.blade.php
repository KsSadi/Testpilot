@extends('layouts.backend.master')

@section('title', $module->name)

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">Cypress Testing</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-cyan-600">Projects</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('projects.show', $project) }}" class="text-gray-500 hover:text-cyan-600">{{ $project->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">{{ $module->name }}</span>
    </div>
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ $module->name }}</h2>
                @if($module->status === 'active')
                    <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Active</span>
                @else
                    <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Inactive</span>
                @endif
            </div>
            <p class="text-gray-500 text-xs md:text-sm">{{ $module->description ?? 'No description provided' }}</p>
        </div>
        <div class="flex items-center space-x-2 w-full md:w-auto">
            <a href="{{ route('projects.show', $project) }}" class="btn-secondary flex-1 md:flex-none text-center">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            @if($module->created_by === auth()->id())
            <button onclick="openShareModal()" class="btn-secondary flex-1 md:flex-none text-center bg-purple-500 text-white hover:bg-purple-600">
                <i class="fas fa-share-alt mr-2"></i>Share
            </button>
            @endif
            <a href="{{ route('test-cases.create', [$project, $module]) }}" class="btn-primary flex-1 md:flex-none text-center">
                <i class="fas fa-plus mr-2"></i>Add Test Case
            </a>
            <a href="{{ route('modules.edit', [$project, $module]) }}" class="btn-warning flex-1 md:flex-none text-center">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Test Cases</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $module->testCases->count() }}</h3>
                </div>
                <div class="primary-color rounded-lg p-3">
                    <i class="fas fa-tasks text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Completed</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $module->testCases->where('status', 'completed')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Running</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $module->testCases->where('status', 'running')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg p-3">
                    <i class="fas fa-play-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Module Order</p>
                    <h3 class="text-2xl font-bold text-gray-800">#{{ $module->order }}</h3>
                    <p class="text-xs text-gray-500">in {{ $project->name }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-sort-numeric-up text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Test Cases Section --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Test Cases</h3>
                <p class="text-sm text-gray-500 mt-1">Manage and execute your test scenarios</p>
            </div>
            @if($module->testCases->count() > 0)
            <a href="{{ route('test-cases.create', [$project, $module]) }}" class="btn-primary mt-3 md:mt-0">
                <i class="fas fa-plus mr-2"></i>Add Test Case
            </a>
            @endif
        </div>

        @if($module->testCases->count() > 0)
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle px-4 md:px-0">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Order</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Test Case</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Description</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-center py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($module->testCases as $testCase)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-5">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-cyan-50 text-cyan-700 rounded-lg font-semibold text-sm">
                                        {{ $testCase->order }}
                                    </span>
                                </td>
                                <td class="py-3 px-5">
                                    <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="text-cyan-600 hover:text-cyan-700 font-semibold hover:underline">
                                        {{ $testCase->name }}
                                    </a>
                                </td>
                                <td class="py-3 px-5">
                                    <p class="text-sm text-gray-600">{{ Str::limit($testCase->description, 50) ?? 'No description' }}</p>
                                </td>
                                <td class="py-3 px-5">
                                    @if($testCase->status === 'completed')
                                        <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Completed</span>
                                    @elseif($testCase->status === 'running')
                                        <span class="badge-info"><i class="fas fa-circle text-[8px] mr-1"></i>Running</span>
                                    @elseif($testCase->status === 'failed')
                                        <span class="badge-danger"><i class="fas fa-circle text-[8px] mr-1"></i>Failed</span>
                                    @else
                                        <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Pending</span>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="p-2 text-cyan-600 hover:text-cyan-700 hover:bg-cyan-50 rounded-lg transition" title="View Test Case">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('test-cases.edit', [$project, $module, $testCase]) }}" class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit Test Case">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('test-cases.destroy', [$project, $module, $testCase]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this test case?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Delete Test Case">
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
                <i class="fas fa-list-check text-white text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Test Cases Yet</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                Get started by creating your first test case for this module
            </p>
            <a href="{{ route('test-cases.create', [$project, $module]) }}" class="btn-primary inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Create First Test Case
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
                        <h3 class="text-xl font-bold text-gray-800">Share Module</h3>
                        <p class="text-sm text-gray-500 mt-1">Invite team members to collaborate on this module</p>
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
    let moduleShareableId = '{{ $module->id }}';

    function openShareModal() {
        document.getElementById('shareModal').classList.remove('hidden');
        loadCollaborators();
    }

    function closeShareModal() {
        document.getElementById('shareModal').classList.add('hidden');
    }

    function loadCollaborators() {
        fetch(`{{ route('share.index') }}?shareable_type=module&shareable_id=${moduleShareableId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayCollaborators(data.collaborators, data.is_owner);
                }
            })
            .catch(error => {
                console.error('Error loading collaborators:', error);
                showNotification('Failed to load collaborators', 'error');
            });
    }

    function displayCollaborators(collaborators, isOwner) {
        const container = document.getElementById('collaboratorsList');
        
        if (collaborators.length === 0) {
            container.innerHTML = `
                <div class="text-center text-gray-500 py-4">
                    <i class="fas fa-users text-3xl mb-2 opacity-50"></i>
                    <p>No collaborators yet</p>
                </div>
            `;
            return;
        }

        container.innerHTML = collaborators.map(share => `
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-semibold">
                        ${share.shared_with.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">${share.shared_with.name}</p>
                        <p class="text-sm text-gray-500">${share.shared_with.email}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${
                        share.status === 'accepted' ? 'bg-green-100 text-green-700' :
                        share.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                        'bg-red-100 text-red-700'
                    }">
                        ${share.status.charAt(0).toUpperCase() + share.status.slice(1)}
                    </span>
                    ${isOwner && share.status === 'accepted' ? `
                        <select onchange="updateRole(${share.id}, this.value)" class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="editor" ${share.role === 'editor' ? 'selected' : ''}>Editor</option>
                            <option value="viewer" ${share.role === 'viewer' ? 'selected' : ''}>Viewer</option>
                        </select>
                        <button onclick="removeCollaborator(${share.id})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }

    document.getElementById('inviteForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('inviteEmail').value;
        const role = document.getElementById('inviteRole').value;

        try {
            const response = await fetch('{{ route('share.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: email,
                    role: role,
                    shareable_type: 'module',
                    shareable_id: moduleShareableId
                })
            });

            const data = await response.json();

            if (data.success) {
                showNotification(data.message, 'success');
                document.getElementById('inviteEmail').value = '';
                loadCollaborators();
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Failed to send invitation', 'error');
        }
    });

    async function updateRole(shareId, newRole) {
        try {
            const response = await fetch(`/share/${shareId}/role`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ role: newRole })
            });

            const data = await response.json();

            if (data.success) {
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
                loadCollaborators();
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Failed to update role', 'error');
        }
    }

    async function removeCollaborator(shareId) {
        if (!confirm('Are you sure you want to remove this collaborator?')) {
            return;
        }

        try {
            const response = await fetch(`/share/${shareId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                showNotification(data.message, 'success');
                loadCollaborators();
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Failed to remove collaborator', 'error');
        }
    }

    function showNotification(message, type = 'info') {
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
</script>
@endpush
