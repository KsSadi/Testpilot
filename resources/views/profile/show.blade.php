@extends('layouts.backend.master')

@section('title', 'My Profile - ' . ($appName ?? config('app.name')))

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">My Profile</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">My Profile</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">View your account information</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('profile.edit') }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Edit Profile
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="h-32 bg-gradient-to-r from-cyan-400 to-blue-500"></div>
                <div class="relative px-6 pb-6">
                    <div class="flex flex-col items-center -mt-16">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                        @else
                            <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center">
                                <span class="text-white text-4xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        
                        <h3 class="mt-4 text-2xl font-bold text-gray-800">{{ $user->name }}</h3>
                        <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                        
                        <div class="mt-4 flex items-center space-x-2">
                            @foreach($user->getRoleNames() as $role)
                                <span class="px-3 py-1 text-xs font-semibold bg-cyan-100 text-cyan-700 rounded-full">
                                    {{ ucfirst($role) }}
                                </span>
                            @endforeach
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mt-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-line text-cyan-600 mr-2"></i>Quick Stats
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Member Since</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Last Updated</span>
                        <span class="text-sm font-semibold text-gray-800">{{ $user->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-600">Account ID</span>
                        <span class="text-sm font-mono font-semibold text-gray-800">#{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Account Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Personal Information --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Personal Information</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Full Name</label>
                            <p class="text-sm text-gray-800 font-semibold mt-1">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Email Address</label>
                            <p class="text-sm text-gray-800 font-semibold mt-1">{{ $user->email }}</p>
                            @if($user->email_verified_at)
                                <span class="text-xs text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            @else
                                <span class="text-xs text-orange-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>Not Verified
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Mobile Number</label>
                            <p class="text-sm text-gray-800 font-semibold mt-1">{{ $user->mobile ?? 'Not provided' }}</p>
                            @if($user->mobile && $user->mobile_verified_at)
                                <span class="text-xs text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Account Status</label>
                            <p class="text-sm font-semibold mt-1
                                {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                                {{ ucfirst($user->status) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security Information --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center mr-3">
                            <i class="fas fa-shield-alt text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Security Information</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Password</p>
                            <p class="text-xs text-gray-500 mt-1">Last changed {{ $user->updated_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}#password" class="text-sm text-cyan-600 hover:text-cyan-700 font-medium">
                            Change Password
                        </a>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Two-Factor Authentication</p>
                            <p class="text-xs text-gray-500 mt-1">Add an extra layer of security</p>
                        </div>
                        <span class="text-xs px-2 py-1 bg-orange-100 text-orange-600 rounded">Not Enabled</span>
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mr-3">
                            <i class="fas fa-key text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Roles & Permissions</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label class="text-xs text-gray-500 font-medium">Assigned Roles</label>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @forelse($user->getRoleNames() as $role)
                                <span class="px-3 py-1.5 text-sm font-medium bg-cyan-100 text-cyan-700 rounded-lg">
                                    <i class="fas fa-user-tag mr-1"></i>{{ ucfirst($role) }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-500">No roles assigned</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Direct Permissions</label>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @forelse($user->getDirectPermissions() as $permission)
                                <span class="px-3 py-1.5 text-sm font-medium bg-purple-100 text-purple-700 rounded-lg">
                                    <i class="fas fa-check mr-1"></i>{{ $permission->name }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-500">No direct permissions</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
