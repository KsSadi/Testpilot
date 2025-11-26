@extends('layouts.backend.master')

@section('title', isset($role) ? 'Edit Role' : 'Create Role')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">User Management</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('roles.index') }}" class="text-gray-500 hover:text-cyan-600">Roles</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">{{ isset($role) ? 'Edit' : 'Create' }}</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ isset($role) ? 'Edit Role' : 'Create New Role' }}</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">{{ isset($role) ? 'Update role details and permissions' : 'Define a new role and assign permissions' }}</p>
        </div>
        <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Roles
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <p class="font-semibold mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Please fix the following errors:</p>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST">
        @csrf
        @if(isset($role))
            @method('PUT')
        @endif

        {{-- Role Details Card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-user-shield mr-2 text-cyan-600"></i>Role Details
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Role Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $role->name ?? '') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition" 
                           placeholder="e.g., Manager"
                           required
                           {{ isset($role) && $role->name === 'superadmin' ? 'readonly' : '' }}>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Permissions Card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-key mr-2 text-cyan-600"></i>Assign Permissions
                </h3>
                <button type="button" onclick="toggleAllPermissions()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                    <i class="fas fa-check-double mr-2"></i>Toggle All
                </button>
            </div>

            @foreach($permissions as $category => $categoryPermissions)
                <div class="mb-6 last:mb-0">
                    <div class="bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-xl p-4 mb-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-white font-bold flex items-center">
                                <i class="fas fa-folder mr-3"></i>
                                {{ $category }} Permissions
                                <span class="ml-3 px-3 py-1 bg-white/20 backdrop-blur-md rounded-lg text-xs font-semibold">
                                    {{ $categoryPermissions->count() }} permissions
                                </span>
                            </h4>
                            <button type="button" onclick="toggleCategory('{{ $category }}')" class="px-3 py-1 bg-white/20 backdrop-blur-md text-white rounded-lg hover:bg-white/30 transition text-sm">
                                Select All
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($categoryPermissions as $permission)
                            <label class="group flex items-start space-x-3 p-4 bg-gray-50 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-cyan-500 hover:shadow-lg transition-all">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->name }}" 
                                       class="permission-checkbox category-{{ $category }} w-5 h-5 text-cyan-600 rounded focus:ring-2 focus:ring-cyan-400 mt-0.5"
                                       {{ isset($rolePermissions) && in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                       {{ old('permissions') && in_array($permission->name, old('permissions')) ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800 group-hover:text-cyan-600 transition-colors capitalize">
                                        {{ str_replace('-', ' ', $permission->name) }}
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('roles.index') }}" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 primary-color text-white rounded-lg hover:shadow-lg transition font-medium">
                <i class="fas fa-save mr-2"></i>{{ isset($role) ? 'Update Role' : 'Create Role' }}
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function toggleAllPermissions() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }

    function toggleCategory(category) {
        const checkboxes = document.querySelectorAll('.category-' + category);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endpush
