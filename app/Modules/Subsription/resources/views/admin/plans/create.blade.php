@extends('layouts.backend.master')

@section('title', isset($plan) ? 'Edit Plan' : 'Create Plan')

@section('content')
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ isset($plan) ? 'Edit' : 'Create' }} Subscription Plan</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Configure plan details and limits
            </p>
        </div>
        <div>
            <a href="{{ route('admin.subscriptions.plans.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Plans
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ isset($plan) ? route('admin.subscriptions.plans.update', $plan) : route('admin.subscriptions.plans.store') }}" method="POST">
            @csrf
            @if(isset($plan))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Plan Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Plan Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="e.g., Pro Plan" required>
                </div>

                {{-- Stripe Plan ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stripe Plan ID (Optional)
                    </label>
                    <input type="text" name="stripe_plan_id" value="{{ old('stripe_plan_id', $plan->stripe_plan_id ?? '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="price_xxxxx">
                </div>
            </div>

            {{-- Description --}}
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                          placeholder="Brief description of the plan">{{ old('description', $plan->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                {{-- Monthly Price --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Monthly Price ($) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="monthly_price" value="{{ old('monthly_price', $plan->monthly_price ?? '') }}" 
                           step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="49.00" required>
                </div>

                {{-- Yearly Discount --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Yearly Discount (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="yearly_discount_percentage" value="{{ old('yearly_discount_percentage', $plan->yearly_discount_percentage ?? 0) }}" 
                           min="0" max="100" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="20" required>
                    <p class="text-xs text-gray-500 mt-1">Discount when paying yearly</p>
                </div>

                {{-- Trial Days --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Trial Days
                    </label>
                    <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days ?? 0) }}" 
                           min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="14">
                </div>
            </div>

            {{-- Limits Section --}}
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Resource Limits</h3>
                <p class="text-sm text-gray-600 mb-4">Set to -1 for unlimited</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Max Projects --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Projects <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="max_projects" value="{{ old('max_projects', $plan->max_projects ?? '') }}" 
                               min="-1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="10" required>
                    </div>

                    {{-- Max Modules --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Modules <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="max_modules" value="{{ old('max_modules', $plan->max_modules ?? '') }}" 
                               min="-1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="50" required>
                    </div>

                    {{-- Max Test Cases --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Test Cases <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="max_test_cases" value="{{ old('max_test_cases', $plan->max_test_cases ?? '') }}" 
                               min="-1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="500" required>
                    </div>

                    {{-- Max Collaborators --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Collaborators <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="max_collaborators" value="{{ old('max_collaborators', $plan->max_collaborators ?? '') }}" 
                               min="-1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="5" required>
                    </div>
                </div>
            </div>

            {{-- Features --}}
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Features (JSON)
                </label>
                <textarea name="features" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-sm" 
                          placeholder='{"ai_tests": true, "priority_support": false}'>{{ old('features', isset($plan) ? json_encode($plan->features, JSON_PRETTY_PRINT) : '') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Optional: Additional plan features in JSON format</p>
            </div>

            {{-- Active Status --}}
            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Active (visible to users)</span>
                </label>
            </div>

            {{-- Submit Buttons --}}
            <div class="mt-8 flex items-center space-x-3">
                <button type="submit" class="px-6 py-3 primary-color text-white rounded-lg font-medium hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>{{ isset($plan) ? 'Update' : 'Create' }} Plan
                </button>
                <a href="{{ route('admin.subscriptions.plans.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
