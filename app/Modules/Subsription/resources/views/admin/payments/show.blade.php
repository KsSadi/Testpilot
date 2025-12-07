@extends('layouts.backend.master')

@section('title', 'Review Payment')

@section('content')
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Review Payment Submission</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Verify and approve/reject manual payment
            </p>
        </div>
        <div>
            <a href="{{ route('admin.subscriptions.payments.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Payments
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Payment Details --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-3 border-b">Payment Information</h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Payment ID</p>
                        <p class="text-lg font-mono font-medium text-gray-900">#{{ $payment->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status</p>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                            {{ $payment->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $payment->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Amount</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Payment Method</p>
                        <p class="text-lg font-medium text-gray-900 uppercase">
                            <i class="fas fa-mobile-alt mr-2"></i>{{ $payment->payment_method }}
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-sm text-gray-500 mb-1">Transaction ID</p>
                    <p class="text-lg font-mono bg-gray-100 px-4 py-2 rounded-lg">{{ $payment->transaction_id }}</p>
                </div>

                @if($payment->payment_details)
                    <div class="mt-6">
                        <p class="text-sm text-gray-500 mb-1">Additional Details</p>
                        <p class="text-sm text-gray-700 bg-gray-50 px-4 py-3 rounded-lg">{{ $payment->payment_details }}</p>
                    </div>
                @endif

                <div class="mt-6 grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-gray-500 mb-1">Submitted At</p>
                        <p class="text-gray-900 font-medium">{{ $payment->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    @if($payment->approved_at)
                        <div>
                            <p class="text-gray-500 mb-1">Processed At</p>
                            <p class="text-gray-900 font-medium">{{ $payment->approved_at->format('M d, Y H:i:s') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Subscription Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-3 border-b">Subscription Details</h3>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Plan</p>
                        <p class="text-lg font-medium text-gray-900">{{ $payment->subscription->plan->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $payment->subscription->plan->description }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Billing Cycle</p>
                        <p class="text-lg font-medium text-gray-900 capitalize">{{ $payment->subscription->billing_cycle }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-sm text-gray-500 mb-2">Plan Limits</p>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <p class="text-2xl font-bold text-blue-600">{{ $payment->subscription->plan->isUnlimitedProjects() ? '∞' : $payment->subscription->plan->max_projects }}</p>
                            <p class="text-xs text-gray-600 mt-1">Projects</p>
                        </div>
                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                            <p class="text-2xl font-bold text-purple-600">{{ $payment->subscription->plan->isUnlimitedModules() ? '∞' : $payment->subscription->plan->max_modules }}</p>
                            <p class="text-xs text-gray-600 mt-1">Modules</p>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">{{ $payment->subscription->plan->isUnlimitedTestCases() ? '∞' : $payment->subscription->plan->max_test_cases }}</p>
                            <p class="text-xs text-gray-600 mt-1">Test Cases</p>
                        </div>
                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                            <p class="text-2xl font-bold text-orange-600">{{ $payment->subscription->plan->isUnlimitedCollaborators() ? '∞' : $payment->subscription->plan->max_collaborators }}</p>
                            <p class="text-xs text-gray-600 mt-1">Collaborators</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- User & Actions --}}
        <div class="lg:col-span-1">
            {{-- User Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-3 border-b">User Information</h3>

                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">{{ $payment->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $payment->user->email }}</p>
                    </div>
                </div>

                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">User ID:</span>
                        <span class="font-medium text-gray-900">#{{ $payment->user->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Joined:</span>
                        <span class="font-medium text-gray-900">{{ $payment->user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            @if($payment->status === 'pending')
                {{-- Approval Actions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-3 border-b">Actions</h3>

                    {{-- Approve Form --}}
                    <form action="{{ route('admin.subscriptions.payments.approve', $payment) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Admin Notes (Optional)
                            </label>
                            <textarea name="admin_notes" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm" 
                                      placeholder="Add any notes about this approval..."></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                            <i class="fas fa-check-circle mr-2"></i>Approve Payment
                        </button>
                    </form>

                    {{-- Reject Form --}}
                    <form action="{{ route('admin.subscriptions.payments.reject', $payment) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this payment?');">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Rejection Reason <span class="text-red-500">*</span>
                            </label>
                            <textarea name="admin_notes" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 text-sm" 
                                      placeholder="Why is this payment being rejected?" required></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                            <i class="fas fa-times-circle mr-2"></i>Reject Payment
                        </button>
                    </form>
                </div>
            @else
                {{-- Processing Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-3 border-b">Processing Information</h3>

                    @if($payment->approved_by)
                        <div class="mb-3">
                            <p class="text-sm text-gray-500 mb-1">Processed By</p>
                            <p class="text-sm font-medium text-gray-900">{{ $payment->approver->name ?? 'N/A' }}</p>
                        </div>
                    @endif

                    @if($payment->admin_notes)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Admin Notes</p>
                            <p class="text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded">{{ $payment->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
