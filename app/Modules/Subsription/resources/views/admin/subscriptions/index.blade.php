@extends('layouts.backend.master')

@section('title', 'Subscription Management')

@section('content')
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Subscription Management</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                View and manage all user subscriptions
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Subscriptions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $subscriptions->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $subscriptions->where('status', 'active')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Cancelled</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $subscriptions->where('status', 'cancelled')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Expired</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $subscriptions->where('status', 'expired')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" 
                       placeholder="Search user...">
            </div>
            <div>
                <select name="plan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">All Plans</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 primary-color text-white rounded-lg hover:shadow-lg transition text-sm">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.subscriptions.manage.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Subscriptions List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $subscription->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $subscription->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $subscription->plan->name }}</div>
                                <div class="text-xs text-gray-500 capitalize">{{ $subscription->billing_cycle }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">${{ number_format($subscription->amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $subscription->status === 'cancelled' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $subscription->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div>{{ $subscription->current_period_start->format('M d, Y') }}</div>
                                <div class="text-xs">to {{ $subscription->current_period_end->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="showOverrideModal({{ $subscription->user->id }}, '{{ $subscription->user->name }}')" 
                                            class="text-blue-600 hover:text-blue-900" title="Override Limits">
                                        <i class="fas fa-sliders-h"></i>
                                    </button>
                                    
                                    @if($subscription->status === 'active')
                                        <form action="{{ route('admin.subscriptions.manage.cancel', $subscription) }}" method="POST" 
                                              onsubmit="return confirm('Cancel this subscription?');" class="inline">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" title="Cancel">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @elseif($subscription->status === 'cancelled')
                                        <form action="{{ route('admin.subscriptions.manage.resume', $subscription) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Resume">
                                                <i class="fas fa-play-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No subscriptions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $subscriptions->links() }}
    </div>

    {{-- Override Limits Modal --}}
    <div id="overrideModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Override User Limits</h3>
                    <button onclick="closeOverrideModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <p class="text-sm text-gray-600 mb-4">Customize limits for <span id="modal_user_name" class="font-semibold"></span></p>

                <form id="overrideForm" action="{{ route('admin.subscriptions.manage.override-limits', 0) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Projects
                            </label>
                            <input type="number" name="override_max_projects" min="-1" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                   placeholder="Leave empty for plan default">
                            <p class="text-xs text-gray-500 mt-1">-1 for unlimited</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Modules
                            </label>
                            <input type="number" name="override_max_modules" min="-1" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                   placeholder="Leave empty for plan default">
                            <p class="text-xs text-gray-500 mt-1">-1 for unlimited</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Test Cases
                            </label>
                            <input type="number" name="override_max_test_cases" min="-1" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                   placeholder="Leave empty for plan default">
                            <p class="text-xs text-gray-500 mt-1">-1 for unlimited</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Collaborators
                            </label>
                            <input type="number" name="override_max_collaborators" min="-1" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                   placeholder="Leave empty for plan default">
                            <p class="text-xs text-gray-500 mt-1">-1 for unlimited</p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center space-x-3">
                        <button type="submit" class="px-6 py-3 primary-color text-white rounded-lg font-medium hover:shadow-lg transition">
                            <i class="fas fa-save mr-2"></i>Update Limits
                        </button>
                        <button type="button" onclick="closeOverrideModal()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function showOverrideModal(userId, userName) {
    document.getElementById('modal_user_name').textContent = userName;
    const form = document.getElementById('overrideForm');
    form.action = form.action.replace(/\/\d+$/, '/' + userId);
    
    document.getElementById('overrideModal').classList.remove('hidden');
    document.getElementById('overrideModal').classList.add('flex');
}

function closeOverrideModal() {
    document.getElementById('overrideModal').classList.add('hidden');
    document.getElementById('overrideModal').classList.remove('flex');
}
</script>
@endpush
