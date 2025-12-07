@extends('layouts.backend.master')

@section('title', 'My Subscription')

@section('content')
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">My Subscription</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Manage your subscription and view usage statistics
            </p>
        </div>
        <a href="{{ route('subscription.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm font-medium">
            <i class="fas fa-exchange-alt mr-2"></i>Change Plan
        </a>
    </div>

    @if($subscription)
        {{-- Current Subscription Card --}}
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 md:p-8 text-white mb-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h3 class="text-2xl md:text-3xl font-bold">{{ $subscription->plan->name }} Plan</h3>
                        @if($subscription->status === 'active')
                            <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">Active</span>
                        @elseif($subscription->status === 'pending')
                            <span class="px-3 py-1 bg-yellow-500 text-white text-xs font-semibold rounded-full">Pending</span>
                        @elseif($subscription->status === 'cancelled')
                            <span class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">Cancelled</span>
                        @endif
                    </div>
                    <p class="text-blue-100 mb-4">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        @if($subscription->billing_cycle === 'yearly')
                            Billing cycle: Yearly
                        @else
                            Billing cycle: Monthly
                        @endif
                    </p>
                    <div class="space-y-2 text-sm">
                        <p class="text-blue-100">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Current period: {{ $subscription->current_period_start->format('M d, Y') }} - {{ $subscription->current_period_end->format('M d, Y') }}
                        </p>
                        @if($subscription->status === 'active')
                            <p class="text-blue-100">
                                <i class="fas fa-redo mr-2"></i>
                                Next renewal: {{ $subscription->current_period_end->format('M d, Y') }}
                            </p>
                        @elseif($subscription->status === 'cancelled')
                            <p class="text-yellow-200">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Your subscription will end on {{ $subscription->current_period_end->format('M d, Y') }}
                            </p>
                        @elseif($subscription->status === 'pending')
                            <p class="text-yellow-200">
                                <i class="fas fa-clock mr-2"></i>
                                Waiting for payment approval
                            </p>
                        @endif
                    </div>
                </div>
                <div class="text-left md:text-right mt-4 md:mt-0">
                    <p class="text-3xl md:text-4xl font-bold">
                        ${{ number_format($subscription->final_amount, 2) }}
                    </p>
                    <p class="text-sm text-blue-100">/ {{ $subscription->billing_cycle }}</p>
                    <p class="text-xs text-blue-200 mt-1">via {{ ucfirst($subscription->payment_method) }}</p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 pt-6 border-t border-white/20 flex flex-wrap gap-3">
                @if($subscription->status === 'active')
                    <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription? You will continue to have access until the end of your current billing period.');" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-sm font-medium">
                            <i class="fas fa-times-circle mr-2"></i>Cancel Subscription
                        </button>
                    </form>
                @elseif($subscription->status === 'cancelled')
                    <form action="{{ route('subscription.resume') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white hover:bg-gray-100 text-blue-600 rounded-lg transition text-sm font-medium">
                            <i class="fas fa-play-circle mr-2"></i>Resume Subscription
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Usage Statistics --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>Usage Statistics
            </h3>
            <p class="text-sm text-gray-500 mb-6">Current billing period: {{ $usage['period_start']->format('M d, Y') }} - {{ $usage['period_end']->format('M d, Y') }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- AI Requests --}}
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">AI Requests</p>
                            <p class="text-2xl font-bold text-gray-800">
                                {{ number_format($usage['ai_requests']['used']) }} 
                                @if($usage['ai_requests']['limit'] > 0)
                                    <span class="text-sm text-gray-500">/ {{ number_format($usage['ai_requests']['limit']) }}</span>
                                @else
                                    <span class="text-sm text-gray-500">/ Unlimited</span>
                                @endif
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-robot text-white text-2xl"></i>
                        </div>
                    </div>
                    @if($usage['ai_requests']['limit'] > 0)
                        <div class="w-full bg-white rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-2 transition-all duration-300" style="width: {{ min(100, $usage['ai_requests']['percentage']) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">{{ number_format($usage['ai_requests']['percentage'], 1) }}% used</p>
                    @endif
                </div>

                {{-- AI Tokens --}}
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">AI Tokens</p>
                            <p class="text-2xl font-bold text-gray-800">
                                {{ number_format($usage['ai_tokens']['used']) }} 
                                @if($usage['ai_tokens']['limit'] > 0)
                                    <span class="text-sm text-gray-500">/ {{ number_format($usage['ai_tokens']['limit']) }}</span>
                                @else
                                    <span class="text-sm text-gray-500">/ Unlimited</span>
                                @endif
                            </p>
                        </div>
                        <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-brain text-white text-2xl"></i>
                        </div>
                    </div>
                    @if($usage['ai_tokens']['limit'] > 0)
                        <div class="w-full bg-white rounded-full h-2 overflow-hidden">
                            <div class="bg-purple-600 h-2 transition-all duration-300" style="width: {{ min(100, $usage['ai_tokens']['percentage']) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">{{ number_format($usage['ai_tokens']['percentage'], 1) }}% used</p>
                    @endif
                </div>
            </div>

            {{-- Total Cost --}}
            @if($usage['total_cost'] > 0)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Estimated AI Cost This Period</p>
                            <p class="text-xs text-gray-500 mt-1">This is an estimate based on API usage</p>
                        </div>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($usage['total_cost'], 4) }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Plan Features --}}
        @if($subscription->plan)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-list-check mr-2 text-blue-600"></i>Your Plan Features
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @php
                        $features = is_array($subscription->plan->features) 
                            ? $subscription->plan->features 
                            : json_decode($subscription->plan->features, true) ?? [];
                    @endphp
                    @if(!empty($features))
                        @foreach($features as $feature => $value)
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ ucwords(str_replace('_', ' ', $feature)) }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if(is_numeric($value))
                                            {{ $value == -1 ? 'Unlimited' : number_format($value) }}
                                        @elseif(is_bool($value))
                                            {{ $value ? 'Included' : 'Not included' }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 col-span-3">No features defined for this plan.</p>
                    @endif
                </div>
            </div>
        @endif

    @else
        {{-- No Subscription --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-crown text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No Active Subscription</h3>
            <p class="text-gray-500 mb-6">You don't have an active subscription yet. Choose a plan to get started!</p>
            <a href="{{ route('subscription.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                <i class="fas fa-rocket mr-2"></i>View Plans
            </a>
        </div>
    @endif
@endsection

@push('scripts')
<script>
// Auto-refresh page if subscription is pending (to check for approval)
@if($subscription && $subscription->status === 'pending')
    setTimeout(() => {
        location.reload();
    }, 30000); // Refresh every 30 seconds
@endif
</script>
@endpush
