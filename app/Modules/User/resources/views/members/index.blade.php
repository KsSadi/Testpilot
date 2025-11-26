@extends('layouts.backend.master')

@section('title', 'Member List')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">User Management</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Member List</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Member List</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Manage and view all registered members</p>
        </div>
        <div class="action-buttons flex items-center space-x-2 w-full md:w-auto">
            <button onclick="toggleFilters()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            @can('create-users')
            <a href="{{ route('members.create') }}" class="btn-primary flex-1 md:flex-none text-center">
                <i class="fas fa-user-plus mr-2"></i>Add Member
            </a>
            @endcan
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Members</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</h3>
                </div>
                <div class="primary-color rounded-lg p-3">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Active</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $activeUsers }}</h3>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Inactive</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $inactiveUsers }}</h3>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-3">
                    <i class="fas fa-pause-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">New This Month</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $newThisMonth }}</h3>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-user-plus text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div id="filterSection" class="hidden bg-white rounded-xl p-4 border border-gray-100 shadow-sm mb-6">
        <form action="{{ route('members.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, mobile..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    <option value="">All Roles</option>
                    @foreach(\Spatie\Permission\Models\Role::all() as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 primary-color text-white rounded-lg hover:shadow-lg transition font-medium">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                <a href="{{ route('members.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Members Table --}}
    <div class="bg-white rounded-xl p-4 md:p-5 border border-gray-100 shadow-sm">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle px-4 md:px-0">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Member</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Role</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Joined</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-3">
                                    <div class="flex items-center">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-lg mr-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    @if($user->roles->first())
                                        <span class="badge-{{ $user->roles->first()->name === 'superadmin' ? 'primary' : ($user->roles->first()->name === 'admin' ? 'info' : 'secondary') }}">
                                            {{ ucfirst($user->roles->first()->name) }}
                                        </span>
                                    @else
                                        <span class="badge-secondary">No Role</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    @if($user->status === 'active')
                                        <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Active</span>
                                    @elseif($user->status === 'inactive')
                                        <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Inactive</span>
                                    @else
                                        <span class="badge-danger"><i class="fas fa-circle text-[8px] mr-1"></i>Suspended</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        @can('view-users')
                                        <a href="{{ route('members.show', $user->id) }}" class="p-2 text-cyan-600 hover:text-cyan-700 hover:bg-cyan-50 rounded-lg transition" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('edit-users')
                                        <a href="{{ route('members.edit', $user->id) }}" class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit Member">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete-users')
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('members.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Delete Member">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-users text-5xl mb-3"></i>
                                        <p class="text-lg font-medium text-gray-500">No members found</p>
                                        <p class="text-sm text-gray-400">Try adjusting your search or filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="flex flex-col md:flex-row items-center justify-between mt-4 pt-4 border-t border-gray-100 gap-4">
            <p class="text-sm text-gray-500">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} members
            </p>
            <div class="flex items-center space-x-2">
                {{ $users->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function toggleFilters() {
        const filterSection = document.getElementById('filterSection');
        filterSection.classList.toggle('hidden');
    }
</script>
@endpush
