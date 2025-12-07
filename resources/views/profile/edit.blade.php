@extends('layouts.backend.master')

@section('title', 'Edit Profile - ' . ($appName ?? config('app.name')))

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('profile.show') }}" class="text-gray-500 hover:text-cyan-600">Profile</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Edit Profile</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Edit Profile</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Update your account information and settings</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('profile.show') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Profile
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Avatar Section --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-camera text-cyan-600 mr-2"></i>Profile Picture
                </h3>
                
                <div class="flex flex-col items-center">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-40 h-40 rounded-full object-cover border-4 border-gray-100 shadow-lg mb-4" id="avatarPreview">
                    @else
                        <div class="w-40 h-40 rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center border-4 border-gray-100 shadow-lg mb-4" id="avatarPreview">
                            <span class="text-white text-5xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="w-full max-w-xs">
                        @csrf
                        @method('PUT')
                        
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <input type="hidden" name="mobile" value="{{ $user->mobile }}">
                        
                        <label for="avatar" class="w-full btn-primary cursor-pointer text-center block mb-2">
                            <i class="fas fa-upload mr-2"></i>Upload New Photo
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(event); this.form.submit();">
                        
                        @error('avatar')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </form>

                    @if($user->avatar)
                        <form action="{{ route('profile.avatar.delete') }}" method="POST" class="w-full max-w-xs mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full btn-secondary text-red-600 hover:bg-red-50" onclick="return confirm('Are you sure you want to delete your avatar?')">
                                <i class="fas fa-trash mr-2"></i>Remove Photo
                            </button>
                        </form>
                    @endif

                    <p class="text-xs text-gray-500 text-center mt-4">
                        Allowed: JPG, PNG, GIF<br>
                        Max size: 2MB
                    </p>
                </div>
            </div>

            {{-- Account Info --}}
            <div class="bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl border border-cyan-100 p-6 mt-6">
                <h4 class="text-sm font-semibold text-gray-800 mb-3">Account Information</h4>
                <div class="space-y-2 text-xs text-gray-600">
                    <div class="flex items-center justify-between">
                        <span>Account ID:</span>
                        <span class="font-mono font-semibold">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Member Since:</span>
                        <span class="font-semibold">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Status:</span>
                        <span class="px-2 py-0.5 rounded font-semibold
                            {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Forms --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Personal Information Form --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mr-3">
                            <i class="fas fa-user-edit text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Personal Information</h3>
                    </div>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Mobile Number
                            </label>
                            <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 @error('mobile') border-red-500 @enderror">
                            @error('mobile')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password Form --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm" id="password">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center mr-3">
                            <i class="fas fa-lock text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Change Password</h3>
                    </div>
                </div>
                <form action="{{ route('profile.password.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Current Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="current_password" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 @error('current_password') border-red-500 @enderror">
                            @error('current_password')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-key mr-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            preview.innerHTML = `<img src="${e.target.result}" class="w-40 h-40 rounded-full object-cover border-4 border-gray-100 shadow-lg">`;
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
