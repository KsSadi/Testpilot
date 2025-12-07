@extends('layouts.backend.master')

@section('title', 'Subscription Plans Management')

@section('content')
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Subscription Plans</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Manage subscription plans and pricing
            </p>
        </div>
        <div>
            <a href="{{ route('admin.subscriptions.plans.create') }}" class="px-4 py-2 primary-color text-white rounded-lg hover:shadow-lg transition text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Create New Plan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Limits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscribers</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($plans as $plan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $plan->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $plan->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">
                                    ${{ number_format($plan->monthly_price, 2) }}/mo
                                </div>
                                @if($plan->yearly_discount_percentage > 0)
                                    <div class="text-xs text-green-600">
                                        ${{ number_format($plan->getYearlyPrice(), 2) }}/yr (-{{ $plan->yearly_discount_percentage }}%)
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="space-y-1">
                                    <div><i class="fas fa-project-diagram w-4"></i> {{ $plan->isUnlimitedProjects() ? '∞' : $plan->max_projects }} Projects</div>
                                    <div><i class="fas fa-cube w-4"></i> {{ $plan->isUnlimitedModules() ? '∞' : $plan->max_modules }} Modules</div>
                                    <div><i class="fas fa-vial w-4"></i> {{ $plan->isUnlimitedTestCases() ? '∞' : $plan->max_test_cases }} Tests</div>
                                    <div><i class="fas fa-users w-4"></i> {{ $plan->isUnlimitedCollaborators() ? '∞' : $plan->max_collaborators }} Collaborators</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.subscriptions.plans.toggle', $plan) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 text-xs font-semibold rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $plan->activeSubscriptions()->count() }} active
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.subscriptions.plans.edit', $plan) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($plan->activeSubscriptions()->count() === 0)
                                        <form action="{{ route('admin.subscriptions.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Delete this plan?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No plans found. <a href="{{ route('admin.subscriptions.plans.create') }}" class="text-blue-600 hover:underline">Create your first plan</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
