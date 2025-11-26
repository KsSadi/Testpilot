@extends('layouts.backend.master')

@section('title', 'Member Details')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">User Management</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('members.index') }}" class="text-gray-500 hover:text-cyan-600">Member List</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Member Details</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Member Details</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">View complete member information</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('members.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            @can('edit-users')
            <a href="{{ route('members.edit', $user->id) }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Edit Member
            </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm text-center">
                <img src="{{ $user->avatar_url }}" 
                     alt="{{ $user->name }}" 
                     class="w-32 h-32 rounded-xl object-cover border-4 border-gray-100 mx-auto mb-4">
                
                <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500 mb-3">{{ $user->email }}</p>

                @if($user->roles->first())
                    <span class="badge-{{ $user->roles->first()->name === 'superadmin' ? 'primary' : ($user->roles->first()->name === 'admin' ? 'info' : 'secondary') }} text-sm">
                        <i class="fas fa-shield-alt mr-1"></i>{{ ucfirst($user->roles->first()->name) }}
                    </span>
                @else
                    <span class="badge-secondary text-sm">No Role Assigned</span>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100">
                    @if($user->status === 'active')
                        <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Active</span>
                    @elseif($user->status === 'inactive')
                        <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Inactive</span>
                    @else
                        <span class="badge-danger"><i class="fas fa-circle text-[8px] mr-1"></i>Suspended</span>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm mt-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Account Information</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Member Since</span>
                        <span class="text-gray-800 font-medium">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Last Updated</span>
                        <span class="text-gray-800 font-medium">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Email Verified</span>
                        @if($user->email_verified_at)
                            <span class="text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>Yes</span>
                        @else
                            <span class="text-gray-400 font-medium"><i class="fas fa-times-circle mr-1"></i>No</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Mobile Verified</span>
                        @if($user->mobile_verified_at)
                            <span class="text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>Yes</span>
                        @else
                            <span class="text-gray-400 font-medium"><i class="fas fa-times-circle mr-1"></i>No</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            @if(auth()->id() !== $user->id)
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm mt-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Actions</h4>
                <div class="space-y-2">
                    @can('edit-users')
                    @if($user->status === 'active')
                        <form action="{{ route('members.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="inactive">
                            <button type="submit" class="w-full btn-secondary text-left" onclick="return confirm('Deactivate this member?');">
                                <i class="fas fa-pause mr-2"></i>Deactivate Account
                            </button>
                        </form>
                    @else
                        <form action="{{ route('members.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="active">
                            <button type="submit" class="w-full btn-primary text-left" onclick="return confirm('Activate this member?');">
                                <i class="fas fa-check mr-2"></i>Activate Account
                            </button>
                        </form>
                    @endif
                    @endcan
                    
                    @can('delete-users')
                    <form action="{{ route('members.destroy', $user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium text-left" onclick="return confirm('Are you sure you want to delete this member? This action cannot be undone.');">
                            <i class="fas fa-trash mr-2"></i>Delete Member
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Detailed Information --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Contact Information --}}
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-address-card text-cyan-500 mr-2"></i>Contact Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Full Name</label>
                        <p class="text-sm text-gray-800 mt-1">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Email Address</label>
                        <p class="text-sm text-gray-800 mt-1">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Mobile Number</label>
                        <p class="text-sm text-gray-800 mt-1">{{ $user->mobile }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Phone Number</label>
                        <p class="text-sm text-gray-800 mt-1">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Date of Birth</label>
                        <p class="text-sm text-gray-800 mt-1">{{ $user->date_of_birth ? $user->date_of_birth->format('F d, Y') : 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase">Age</label>
                        <p class="text-sm text-gray-800 mt-1">{{ $user->date_of_birth ? $user->date_of_birth->age . ' years' : 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Address Information --}}
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-cyan-500 mr-2"></i>Address Information
                </h3>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase">Full Address</label>
                    <p class="text-sm text-gray-800 mt-1">{{ $user->address ?? 'No address provided' }}</p>
                </div>
            </div>

            {{-- Role & Permissions --}}
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-shield-alt text-cyan-500 mr-2"></i>Role & Permissions
                </h3>
                
                @if($user->roles->first())
                    <div class="mb-4">
                        <label class="text-xs font-medium text-gray-500 uppercase">Assigned Role</label>
                        <div class="mt-2">
                            <span class="badge-{{ $user->roles->first()->name === 'superadmin' ? 'primary' : ($user->roles->first()->name === 'admin' ? 'info' : 'secondary') }}">
                                {{ ucfirst($user->roles->first()->name) }}
                            </span>
                        </div>
                    </div>

                    @if($user->roles->first()->permissions->count() > 0)
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase mb-2 block">Permissions ({{ $user->roles->first()->permissions->count() }})</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($user->roles->first()->permissions as $permission)
                                    <div class="flex items-center text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded-lg">
                                        <i class="fas fa-check text-green-500 text-xs mr-2"></i>
                                        {{ ucwords(str_replace('-', ' ', $permission->name)) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">This role has no permissions assigned.</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">No role assigned to this member.</p>
                @endif
            </div>

            {{-- Account Timeline --}}
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clock text-cyan-500 mr-2"></i>Account Timeline
                </h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-user-plus text-green-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Account Created</p>
                            <p class="text-xs text-gray-500">{{ $user->created_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if($user->email_verified_at)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-envelope-check text-blue-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Email Verified</p>
                            <p class="text-xs text-gray-500">{{ $user->email_verified_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">{{ $user->email_verified_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif

                    @if($user->mobile_verified_at)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-mobile-alt text-purple-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Mobile Verified</p>
                            <p class="text-xs text-gray-500">{{ $user->mobile_verified_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">{{ $user->mobile_verified_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-edit text-gray-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Last Updated</p>
                            <p class="text-xs text-gray-500">{{ $user->updated_at->format('F d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-400">{{ $user->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
