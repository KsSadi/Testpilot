@extends('layouts.backend.master')

@section('title', 'Roles Management')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">User Management</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Roles</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Roles Management</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Manage user roles and permissions</p>
        </div>
        @can('create-roles')
        <a href="{{ route('roles.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Add Role
        </a>
        @endcan
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
        <div class="stat-card bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Roles</p>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">{{ $roles->count() }}</h3>
                </div>
                <div class="bg-blue-50 text-blue-600 p-3 rounded-xl">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Active Roles</p>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">{{ $activeRoles }}</h3>
                </div>
                <div class="bg-green-50 text-green-600 p-3 rounded-xl">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Custom Roles</p>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">{{ $customRoles }}</h3>
                </div>
                <div class="bg-purple-50 text-purple-600 p-3 rounded-xl">
                    <i class="fas fa-cog text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Permissions</p>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mt-2">{{ $totalPermissions }}</h3>
                </div>
                <div class="bg-orange-50 text-orange-600 p-3 rounded-xl">
                    <i class="fas fa-key text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Roles Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Role Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Permissions</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Members</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="{{ $role->name === 'superadmin' ? 'primary-color' : ($role->name === 'admin' ? 'bg-blue-100 text-blue-600' : ($role->name === 'moderator' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600')) }} p-2 rounded-lg">
                                        <i class="fas {{ $role->name === 'superadmin' ? 'fa-crown text-white' : ($role->name === 'admin' ? 'fa-user-tie' : ($role->name === 'moderator' ? 'fa-users' : 'fa-user')) }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 capitalize">{{ $role->name }}</p>
                                        <p class="text-sm text-gray-500">
                                            @if($role->name === 'superadmin')
                                                Full system access
                                            @elseif($role->name === 'admin')
                                                Administrative access
                                            @elseif($role->name === 'moderator')
                                                Limited access
                                            @else
                                                Basic access
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($role->permissions_count === $totalPermissions)
                                    <span class="badge-primary">All ({{ $role->permissions_count }})</span>
                                @else
                                    <span class="badge-info">{{ $role->permissions_count }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-800 font-medium">{{ $role->users_count }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge-success">Active</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @can('view-permissions')
                                    <a href="{{ route('permissions.index', ['role' => $role->id]) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="View Permissions">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('edit-roles')
                                    <a href="{{ route('roles.edit', $role->id) }}" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Edit Role">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete-roles')
                                        @if($role->name !== 'superadmin')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Role">
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
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-user-shield text-4xl mb-3 text-gray-300"></i>
                                <p>No roles found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
