@extends('layouts.backend.master')

@section('title', 'Permissions Management')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">User Management</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Permissions</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Permissions Management</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Manage system permissions and access control</p>
        </div>
        <div class="action-buttons flex items-center space-x-2">
            <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                <i class="fas fa-user-shield mr-2"></i>Manage Roles
            </a>
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

    {{-- Role Selector --}}
    <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-xl p-4 md:p-5 mb-6 shadow-xl border border-cyan-400">
        <div class="quick-actions-content flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="bg-white bg-opacity-20 rounded-xl p-3 backdrop-blur-sm">
                    <i class="fas fa-user-shield text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-base md:text-lg">Configure Role Permissions</h3>
                    <p class="text-cyan-100 text-xs md:text-sm">Select a role to manage its access control</p>
                </div>
            </div>
            <div class="quick-actions-buttons flex items-center gap-2 flex-wrap">
                @foreach($roles as $roleItem)
                    <a href="{{ route('permissions.index', ['role' => $roleItem->id]) }}" 
                       class="{{ $selectedRole && $selectedRole->id === $roleItem->id ? 'bg-white text-cyan-600' : 'bg-white bg-opacity-20 backdrop-blur-sm text-white hover:bg-white hover:bg-opacity-30' }} px-4 py-2 rounded-lg text-sm font-medium transition-all capitalize">
                        <i class="fas {{ $roleItem->name === 'superadmin' ? 'fa-crown' : ($roleItem->name === 'admin' ? 'fa-user-tie' : 'fa-user') }} mr-2"></i>{{ $roleItem->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if($selectedRole)
        <form action="{{ route('permissions.update') }}" method="POST">
            @csrf
            <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">

            {{-- Permission Categories --}}
            <div class="space-y-6">
                @foreach($permissions as $category => $categoryPermissions)
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-{{ $loop->index % 4 === 0 ? 'blue' : ($loop->index % 4 === 1 ? 'green' : ($loop->index % 4 === 2 ? 'purple' : 'orange')) }}-500 to-{{ $loop->index % 4 === 0 ? 'cyan' : ($loop->index % 4 === 1 ? 'emerald' : ($loop->index % 4 === 2 ? 'pink' : 'red')) }}-600 p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                                        <i class="fas {{ 
                                            str_contains(strtolower($category), 'user') ? 'fa-users' : 
                                            (str_contains(strtolower($category), 'role') ? 'fa-user-shield' : 
                                            (str_contains(strtolower($category), 'permission') ? 'fa-key' : 
                                            (str_contains(strtolower($category), 'setting') ? 'fa-cog' : 
                                            (str_contains(strtolower($category), 'report') ? 'fa-chart-bar' : 
                                            (str_contains(strtolower($category), 'dashboard') || str_contains(strtolower($category), 'analytics') ? 'fa-chart-line' : 'fa-folder'))))) 
                                        }} text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-white flex items-center">
                                            {{ $category }} 
                                            @php
                                                $enabledCount = $selectedRole->permissions->whereIn('name', $categoryPermissions->pluck('name'))->count();
                                            @endphp
                                            <span class="ml-3 px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-xs font-semibold">
                                                {{ $enabledCount }} of {{ $categoryPermissions->count() }} enabled
                                            </span>
                                        </h3>
                                        <p class="text-sm text-white/80 mt-1">Control {{ strtolower($category) }} operations and access</p>
                                    </div>
                                </div>
                                <button type="button" onclick="toggleCategory('{{ $category }}')" class="px-4 py-2 bg-white/20 backdrop-blur-md text-white rounded-xl hover:bg-white/30 transition-all font-semibold text-sm">
                                    <i class="fas fa-check-double mr-2"></i>Select All
                                </button>
                            </div>
                        </div>
                        
                        <div class="p-6 bg-gradient-to-br from-gray-50 to-white">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach($categoryPermissions as $permission)
                                    @php
                                        $isChecked = $selectedRole->hasPermissionTo($permission->name);
                                        $action = explode('-', $permission->name)[0];
                                        $iconClass = $action === 'view' ? 'fa-eye text-cyan-500' : 
                                                    ($action === 'create' ? 'fa-plus-circle text-green-500' : 
                                                    ($action === 'edit' ? 'fa-edit text-blue-500' : 
                                                    ($action === 'delete' ? 'fa-trash-alt text-red-500' : 
                                                    'fa-check text-purple-500')));
                                    @endphp
                                    <label class="group flex items-start space-x-4 p-4 bg-white border-2 {{ $isChecked ? 'border-cyan-500' : 'border-gray-200' }} rounded-xl cursor-pointer hover:border-cyan-500 hover:shadow-lg transition-all">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission->name }}" 
                                               class="category-{{ $category }} w-6 h-6 text-cyan-600 rounded-lg focus:ring-2 focus:ring-cyan-400 mt-0.5"
                                               {{ $isChecked ? 'checked' : '' }}
                                               {{ $selectedRole->name === 'superadmin' ? 'disabled' : '' }}>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="font-bold text-gray-800 group-hover:text-cyan-600 transition-colors capitalize">
                                                    {{ str_replace('-', ' ', $permission->name) }}
                                                </p>
                                                <i class="fas {{ $iconClass }} text-sm"></i>
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                {{ ucfirst($action) }} {{ strtolower($category) }} 
                                                @if($action === 'view')
                                                    access
                                                @elseif($action === 'create')
                                                    new records
                                                @elseif($action === 'edit')
                                                    existing records
                                                @elseif($action === 'delete')
                                                    records permanently
                                                @else
                                                    operations
                                                @endif
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Action Buttons --}}
            @if($selectedRole->name !== 'superadmin')
            <div class="flex items-center justify-end space-x-3 mt-6">
                <a href="{{ route('permissions.index') }}" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 primary-color text-white rounded-lg hover:shadow-lg transition font-medium">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mt-6">
                    <i class="fas fa-info-circle mr-2"></i>Super Admin role has all permissions by default and cannot be modified.
                </div>
            @endif
        </form>
    @else
        {{-- No Role Selected --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="text-gray-300 mb-4">
                <i class="fas fa-user-shield text-6xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No Role Selected</h3>
            <p class="text-gray-500 mb-4">Please select a role above to manage its permissions</p>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function toggleCategory(category) {
        const checkboxes = document.querySelectorAll('.category-' + category);
        const allChecked = Array.from(checkboxes).filter(cb => !cb.disabled).every(cb => cb.checked);
        checkboxes.forEach(cb => {
            if (!cb.disabled) {
                cb.checked = !allChecked;
            }
        });
    }
</script>
@endpush
