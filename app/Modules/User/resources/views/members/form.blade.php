@extends('layouts.backend.master')

@section('title', isset($user) ? 'Edit Member' : 'Add Member')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">User Management</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('members.index') }}" class="text-gray-500 hover:text-cyan-600">Member List</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">{{ isset($user) ? 'Edit' : 'Add' }} Member</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ isset($user) ? 'Edit' : 'Add' }} Member</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">{{ isset($user) ? 'Update member information' : 'Create a new member account' }}</p>
        </div>
        <a href="{{ route('members.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                <div class="flex-1">
                    <p class="font-medium mb-1">Please correct the following errors:</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Member Form --}}
    <form action="{{ isset($user) ? route('members.update', $user->id) : route('members.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Avatar & Status --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Avatar & Status</h3>
                    
                    {{-- Avatar Upload --}}
                    <div class="mb-6">
                        <div class="flex flex-col items-center">
                            <div class="relative mb-4">
                                <img id="avatarPreview" 
                                     src="{{ isset($user) ? $user->avatar_url : 'https://ui-avatars.com/api/?name=New+User&size=200&background=random' }}" 
                                     alt="Avatar" 
                                     class="w-32 h-32 rounded-xl object-cover border-4 border-gray-100">
                                <label for="avatar" class="absolute bottom-0 right-0 bg-cyan-500 hover:bg-cyan-600 text-white rounded-full p-2 cursor-pointer transition">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>
                            <input type="file" 
                                   id="avatar" 
                                   name="avatar" 
                                   accept="image/*" 
                                   class="hidden"
                                   onchange="previewAvatar(event)">
                            <p class="text-xs text-gray-500 text-center">Allowed: JPG, PNG, WEBP<br>Max size: 2MB</p>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-0">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                            <option value="active" {{ (isset($user) && $user->status === 'active') || old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ (isset($user) && $user->status === 'inactive') || old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ (isset($user) && $user->status === 'suspended') || old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                </div>

                {{-- Role Selection --}}
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Role Assignment</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Role <span class="text-red-500">*</span></label>
                        <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                            <option value="">Select a role</option>
                            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                <option value="{{ $role->name }}" 
                                    {{ (isset($user) && $user->roles->first()?->name === $role->name) || old('role') === $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Each member can have only one role</p>
                    </div>
                </div>
            </div>

            {{-- Right Column: Basic Info & Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', isset($user) ? $user->name : '') }}" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                                   placeholder="Enter full name">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email', isset($user) ? $user->email : '') }}" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                                   placeholder="email@example.com">
                        </div>

                        {{-- Mobile --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   name="mobile" 
                                   value="{{ old('mobile', isset($user) ? $user->mobile : '') }}" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                                   placeholder="+1234567890">
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="text" 
                                   name="phone" 
                                   value="{{ old('phone', isset($user) ? $user->phone : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                                   placeholder="Optional phone number">
                        </div>

                        {{-- Date of Birth --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" 
                                   name="date_of_birth" 
                                   value="{{ old('date_of_birth', isset($user) ? $user->date_of_birth?->format('Y-m-d') : '') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        </div>

                        {{-- Email Verification --}}
                        <div class="flex items-center h-full pt-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="email_verified" 
                                       value="1"
                                       {{ (isset($user) && $user->email_verified_at) || old('email_verified') ? 'checked' : '' }}
                                       class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-2 focus:ring-cyan-400">
                                <span class="ml-2 text-sm text-gray-700">Email Verified</span>
                            </label>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                                  placeholder="Enter full address">{{ old('address', isset($user) ? $user->address : '') }}</textarea>
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        {{ isset($user) ? 'Change Password (Optional)' : 'Password' }}
                    </h3>
                    
                    @if(!isset($user))
                        <p class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                            <i class="fas fa-info-circle mr-2"></i>Default password will be set if left empty
                        </p>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password 
                                @if(!isset($user))
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password"
                                       name="password" 
                                       {{ !isset($user) ? 'required' : '' }}
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                                       placeholder="{{ isset($user) ? 'Leave blank to keep current' : 'Enter password' }}">
                                <button type="button" 
                                        onclick="togglePassword('password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-eye" id="password-toggle"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password 
                                @if(!isset($user))
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password_confirmation"
                                       name="password_confirmation" 
                                       {{ !isset($user) ? 'required' : '' }}
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                                       placeholder="{{ isset($user) ? 'Leave blank to keep current' : 'Confirm password' }}">
                                <button type="button" 
                                        onclick="togglePassword('password_confirmation')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-eye" id="password_confirmation-toggle"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(isset($user))
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>Leave password fields empty to keep the current password
                        </p>
                    @endif
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('members.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>{{ isset($user) ? 'Update' : 'Create' }} Member
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Avatar Preview
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    // Toggle Password Visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-toggle');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
