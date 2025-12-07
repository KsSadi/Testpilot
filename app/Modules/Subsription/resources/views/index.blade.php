@extends('layouts.backend.master')

@section('title', 'Subscription Plans')

@section('content')
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Subscription Plans</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Choose the perfect plan for your testing needs
            </p>
        </div>
    </div>

    @if($currentSubscription)
        {{-- Current Subscription Banner --}}
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold">{{ $currentSubscription->plan->name }} Plan</h3>
                    <p class="mt-2 text-blue-100">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        @if($currentSubscription->billing_cycle === 'yearly')
                            Renews annually on {{ $currentSubscription->current_period_end->format('M d, Y') }}
                        @else
                            Renews monthly on {{ $currentSubscription->current_period_end->format('M d, Y') }}
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold">
                        ${{ number_format($currentSubscription->amount, 2) }}
                    </p>
                    <p class="text-sm text-blue-100">/ {{ $currentSubscription->billing_cycle }}</p>
                </div>
            </div>

            {{-- Usage Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-white/20">
                <div>
                    <p class="text-sm text-blue-100">Projects</p>
                    <p class="text-lg font-bold">{{ $usage['projects_count'] }} / {{ $currentSubscription->plan->isUnlimitedProjects() ? '∞' : $currentSubscription->plan->max_projects }}</p>
                </div>
                <div>
                    <p class="text-sm text-blue-100">Modules</p>
                    <p class="text-lg font-bold">{{ $usage['modules_count'] }} / {{ $currentSubscription->plan->isUnlimitedModules() ? '∞' : $currentSubscription->plan->max_modules }}</p>
                </div>
                <div>
                    <p class="text-sm text-blue-100">Test Cases</p>
                    <p class="text-lg font-bold">{{ $usage['test_cases_count'] }} / {{ $currentSubscription->plan->isUnlimitedTestCases() ? '∞' : $currentSubscription->plan->max_test_cases }}</p>
                </div>
                <div>
                    <p class="text-sm text-blue-100">Collaborators</p>
                    <p class="text-lg font-bold">{{ $usage['shared_count'] }} / {{ $currentSubscription->plan->isUnlimitedCollaborators() ? '∞' : $currentSubscription->plan->max_collaborators }}</p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 flex space-x-3">
                @if($currentSubscription->status === 'active')
                    <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition text-sm">
                            <i class="fas fa-times-circle mr-2"></i>Cancel Subscription
                        </button>
                    </form>
                @elseif($currentSubscription->status === 'cancelled')
                    <form action="{{ route('subscription.resume') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white hover:bg-gray-100 text-blue-600 rounded-lg transition text-sm font-medium">
                            <i class="fas fa-play-circle mr-2"></i>Resume Subscription
                        </button>
                    </form>
                    <p class="text-sm text-blue-100 self-center">Your subscription will end on {{ $currentSubscription->current_period_end->format('M d, Y') }}</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Billing Cycle Toggle --}}
    <div class="flex justify-center mb-8">
        <div class="bg-white rounded-lg p-1 inline-flex shadow-sm">
            <button onclick="toggleBilling('monthly')" id="monthlyBtn" class="px-6 py-2 rounded-lg text-sm font-medium transition billing-toggle active">
                Monthly
            </button>
            <button onclick="toggleBilling('yearly')" id="yearlyBtn" class="px-6 py-2 rounded-lg text-sm font-medium transition billing-toggle">
                Yearly <span class="ml-1 text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded">Save up to 20%</span>
            </button>
        </div>
    </div>

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($plans as $plan)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition plan-card {{ $currentSubscription && $currentSubscription->plan_id === $plan->id ? 'ring-2 ring-blue-500' : '' }}"
                 data-plan-id="{{ $plan->id }}"
                 data-plan-name="{{ $plan->name }}"
                 data-monthly-price="{{ $plan->monthly_price }}"
                 data-yearly-price="{{ $plan->getYearlyPrice() }}">
                @if($plan->name === 'Pro' || $plan->name === 'Professional')
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white text-center py-2 text-sm font-medium">
                        Most Popular
                    </div>
                @endif
                
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800">{{ $plan->name }}</h3>
                    <p class="text-gray-500 text-sm mt-1 h-10">{{ $plan->description }}</p>

                    <div class="mt-6">
                        <div class="monthly-price">
                            <span class="text-4xl font-bold text-gray-900">${{ number_format($plan->monthly_price, 0) }}</span>
                            <span class="text-gray-500">/month</span>
                        </div>
                        <div class="yearly-price hidden">
                            <span class="text-4xl font-bold text-gray-900">${{ number_format($plan->getYearlyPrice() / 12, 0) }}</span>
                            <span class="text-gray-500">/month</span>
                            <p class="text-sm text-green-600 mt-1">
                                ${{ number_format($plan->getYearlyPrice(), 0) }}/year (save {{ $plan->yearly_discount_percentage }}%)
                            </p>
                        </div>
                    </div>

                    <ul class="mt-6 space-y-3">
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                            <span>{{ $plan->isUnlimitedProjects() ? 'Unlimited' : $plan->max_projects }} Projects</span>
                        </li>
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                            <span>{{ $plan->isUnlimitedModules() ? 'Unlimited' : $plan->max_modules }} Modules</span>
                        </li>
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                            <span>{{ $plan->isUnlimitedTestCases() ? 'Unlimited' : $plan->max_test_cases }} Test Cases</span>
                        </li>
                        <li class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                            <span>{{ $plan->isUnlimitedCollaborators() ? 'Unlimited' : $plan->max_collaborators }} Collaborators</span>
                        </li>
                    </ul>

                    @if($currentSubscription && $currentSubscription->plan_id === $plan->id)
                        <button class="w-full mt-6 px-4 py-3 bg-gray-200 text-gray-500 rounded-lg font-medium cursor-not-allowed">
                            Current Plan
                        </button>
                    @elseif($plan->monthly_price == 0)
                        @if(!$currentSubscription)
                            <button class="w-full mt-6 px-4 py-3 bg-gray-100 text-gray-600 rounded-lg font-medium hover:bg-gray-200 transition">
                                Current Plan
                            </button>
                        @else
                            <a href="{{ route('subscription.checkout', ['plan' => $plan->id, 'billing_cycle' => 'monthly']) }}" 
                               class="block w-full mt-6 px-4 py-3 bg-gray-600 text-white text-center rounded-lg font-medium hover:bg-gray-700 transition">
                                Downgrade
                            </a>
                        @endif
                    @else
                        <a href="{{ route('subscription.checkout', ['plan' => $plan->id, 'billing_cycle' => 'monthly']) }}" 
                           class="block w-full mt-6 px-4 py-3 primary-color text-white text-center rounded-lg font-medium hover:shadow-lg transition subscribe-btn monthly-btn">
                            @if($currentSubscription && $currentSubscription->plan->monthly_price < $plan->monthly_price)
                                Upgrade to {{ $plan->name }}
                            @elseif($currentSubscription)
                                Switch to {{ $plan->name }}
                            @else
                                Get Started
                            @endif
                        </a>
                        <a href="{{ route('subscription.checkout', ['plan' => $plan->id, 'billing_cycle' => 'yearly']) }}" 
                           class="hidden w-full mt-6 px-4 py-3 primary-color text-white text-center rounded-lg font-medium hover:shadow-lg transition subscribe-btn yearly-btn">
                            @if($currentSubscription && $currentSubscription->plan->monthly_price < $plan->monthly_price)
                                Upgrade to {{ $plan->name }}
                            @elseif($currentSubscription)
                                Switch to {{ $plan->name }}
                            @else
                                Get Started
                            @endif
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
<script>
let selectedBilling = 'monthly';

function toggleBilling(cycle) {
    selectedBilling = cycle;
    
    // Update buttons
    document.getElementById('monthlyBtn').classList.toggle('active', cycle === 'monthly');
    document.getElementById('yearlyBtn').classList.toggle('active', cycle === 'yearly');
    
    // Update prices
    document.querySelectorAll('.monthly-price').forEach(el => {
        el.classList.toggle('hidden', cycle === 'yearly');
    });
    document.querySelectorAll('.yearly-price').forEach(el => {
        el.classList.toggle('hidden', cycle === 'monthly');
    });
    
    // Update subscribe buttons
    document.querySelectorAll('.monthly-btn').forEach(el => {
        el.classList.toggle('hidden', cycle === 'yearly');
        el.classList.toggle('block', cycle === 'monthly');
    });
    document.querySelectorAll('.yearly-btn').forEach(el => {
        el.classList.toggle('hidden', cycle === 'monthly');
        el.classList.toggle('block', cycle === 'yearly');
    });
}

// Add active class styles
const style = document.createElement('style');
style.textContent = `
    .billing-toggle.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .billing-toggle:not(.active) {
        color: #6b7280;
    }
`;
document.head.appendChild(style);
</script>
@endpush
