@extends('layouts.backend.master')

@section('title', 'Manual Payment Approvals')

@section('content')
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Manual Payment Approvals</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Review and approve manual payment submissions
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-6">
                <a href="?status=pending" class="pb-4 px-1 border-b-2 font-medium text-sm {{ request('status', 'pending') === 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Pending ({{ $payments->where('status', 'pending')->count() }})
                </a>
                <a href="?status=approved" class="pb-4 px-1 border-b-2 font-medium text-sm {{ request('status') === 'approved' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Approved
                </a>
                <a href="?status=rejected" class="pb-4 px-1 border-b-2 font-medium text-sm {{ request('status') === 'rejected' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Rejected
                </a>
            </nav>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($payments as $payment)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $payment->user->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $payment->user->email }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Plan</p>
                                <p class="text-sm font-medium text-gray-900">{{ $payment->subscription->plan->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Amount</p>
                                <p class="text-sm font-medium text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Payment Method</p>
                                <p class="text-sm font-medium text-gray-900 uppercase">{{ $payment->payment_method }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Submitted</p>
                                <p class="text-sm font-medium text-gray-900">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Transaction ID</p>
                                    <p class="text-sm font-mono font-medium text-gray-900">{{ $payment->transaction_id }}</p>
                                </div>
                                @if($payment->payment_details)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Payment Details</p>
                                        <p class="text-sm text-gray-700">{{ $payment->payment_details }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($payment->admin_notes)
                            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-xs text-yellow-800 mb-1">
                                    <i class="fas fa-sticky-note mr-1"></i>Admin Notes
                                </p>
                                <p class="text-sm text-yellow-900">{{ $payment->admin_notes }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="ml-6 flex flex-col items-end space-y-2">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $payment->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $payment->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ ucfirst($payment->status) }}
                        </span>

                        @if($payment->status === 'pending')
                            <div class="flex space-x-2 mt-2">
                                <a href="{{ route('admin.subscriptions.payments.show', $payment) }}" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                                    <i class="fas fa-eye mr-1"></i>Review
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">No payment submissions found.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $payments->links() }}
    </div>
@endsection
