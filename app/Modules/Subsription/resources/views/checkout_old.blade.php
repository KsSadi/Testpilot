@extends('layouts.backend.master')

@section('title', 'Checkout - Subscribe to ' . $plan->name)

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('subscription.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Plans
        </a>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Complete Your Subscription</h2>
        <p class="text-gray-500 text-sm md:text-base mt-1">
            You're subscribing to the {{ $plan->name }} plan
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Form --}}
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('subscription.subscribe') }}" method="POST" id="checkoutForm">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <input type="hidden" name="billing_cycle" value="{{ $billingCycle }}">
                <input type="hidden" name="coupon_code" id="applied_coupon_code">

                {{-- Currency Selection --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-dollar-sign mr-2 text-blue-600"></i>
                        Select Currency
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition group">
                            <input type="radio" name="currency" value="USD" checked class="sr-only currency-radio">
                            <div class="text-center w-full currency-option">
                                <div class="text-2xl mb-2">🇺🇸</div>
                                <p class="font-semibold text-gray-900">USD ($)</p>
                                <p class="text-xs text-gray-500 mt-1">US Dollar</p>
                            </div>
                        </label>
                        <label class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-green-50 hover:border-green-300 transition group">
                            <input type="radio" name="currency" value="BDT" class="sr-only currency-radio">
                            <div class="text-center w-full currency-option">
                                <div class="text-2xl mb-2">🇧🇩</div>
                                <p class="font-semibold text-gray-900">BDT (৳)</p>
                                <p class="text-xs text-gray-500 mt-1">Bangladeshi Taka</p>
                            </div>
                        </label>
                    </div>
                </div>

                    {{-- Coupon Section --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-tag mr-2 text-blue-600"></i>
                            Have a Discount Coupon?
                        </h3>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" id="coupon_input" 
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="Enter coupon code">
                            <button type="button" onclick="validateCoupon()" 
                                    class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition font-medium whitespace-nowrap">
                                <i class="fas fa-check mr-2"></i>Apply Coupon
                            </button>
                        </div>
                        <div id="couponFeedback" class="mt-3"></div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-credit-card mr-2 text-blue-600"></i>
                            Payment Method
                        </h3>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition group">
                                <input type="radio" name="payment_method" value="stripe" checked 
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                <div class="ml-4 flex-1 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fab fa-cc-stripe text-blue-600 text-2xl mr-3"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">Credit/Debit Card</p>
                                            <p class="text-xs text-gray-500">Pay securely with Stripe</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <i class="fab fa-cc-visa text-blue-600 text-xl"></i>
                                        <i class="fab fa-cc-mastercard text-red-600 text-xl"></i>
                                        <i class="fab fa-cc-amex text-blue-500 text-xl"></i>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-pink-50 hover:border-pink-300 transition group">
                                <input type="radio" name="payment_method" value="bkash" 
                                       class="w-4 h-4 text-pink-600 focus:ring-2 focus:ring-pink-500">
                                <div class="ml-4 flex items-center">
                                    <i class="fas fa-mobile-alt text-pink-600 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">bKash</p>
                                        <p class="text-xs text-gray-500">Mobile payment</p>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-purple-50 hover:border-purple-300 transition group">
                                <input type="radio" name="payment_method" value="rocket" 
                                       class="w-4 h-4 text-purple-600 focus:ring-2 focus:ring-purple-500">
                                <div class="ml-4 flex items-center">
                                    <i class="fas fa-rocket text-purple-600 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">Rocket</p>
                                        <p class="text-xs text-gray-500">Mobile payment</p>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition group">
                                <input type="radio" name="payment_method" value="nagad" 
                                       class="w-4 h-4 text-orange-600 focus:ring-2 focus:ring-orange-500">
                                <div class="ml-4 flex items-center">
                                    <i class="fas fa-wallet text-orange-600 text-2xl mr-3"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">Nagad</p>
                                        <p class="text-xs text-gray-500">Mobile payment</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Manual Payment Details --}}
                    <div id="manualPaymentSection" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hidden">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-yellow-800 mb-1">Manual Payment Process</h4>
                                    <p class="text-sm text-yellow-700">
                                        After submitting your payment details, our admin team will review and approve your payment. 
                                        You'll receive an email once your subscription is activated.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Transaction ID <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="transaction_id" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       placeholder="Enter your transaction ID">
                                <p class="text-xs text-gray-500 mt-1">Enter the transaction ID from your payment receipt</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Sender Number <span class="text-gray-400">(Optional)</span>
                                </label>
                                <input type="text" name="sender_number" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       placeholder="e.g., 01712345678">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Additional Notes <span class="text-gray-400">(Optional)</span>
                                </label>
                                <textarea name="payment_details" rows="3" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                          placeholder="Any additional information about your payment"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" 
                                class="flex-1 px-6 py-4 primary-color text-white rounded-xl font-semibold text-lg hover:shadow-lg transition">
                            <i class="fas fa-lock mr-2"></i>
                            <span id="submitButtonText">Complete Payment</span>
                        </button>
                        <a href="{{ route('subscription.index') }}" 
                           class="px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-semibold text-center hover:bg-gray-200 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h3>
                    
                    {{-- Plan Details --}}
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $plan->name }} Plan</p>
                                <p class="text-sm text-gray-500 capitalize">{{ $billingCycle }} billing</p>
                            </div>
                            @if($billingCycle === 'yearly' && $plan->yearly_discount_percentage > 0)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">
                                    Save {{ $plan->yearly_discount_percentage }}%
                                </span>
                            @endif
                        </div>
                        
                        {{-- Features Preview --}}
                        <ul class="mt-3 space-y-1.5 text-sm text-gray-600">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 text-xs mr-2"></i>
                                {{ $plan->isUnlimitedProjects() ? 'Unlimited' : $plan->max_projects }} Projects
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 text-xs mr-2"></i>
                                {{ $plan->isUnlimitedModules() ? 'Unlimited' : $plan->max_modules }} Modules
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 text-xs mr-2"></i>
                                {{ $plan->isUnlimitedTestCases() ? 'Unlimited' : $plan->max_test_cases }} Test Cases
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 text-xs mr-2"></i>
                                {{ $plan->isUnlimitedCollaborators() ? 'Unlimited' : $plan->max_collaborators }} Collaborators
                            </li>
                        </ul>
                    </div>

                    {{-- Pricing Breakdown --}}
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span id="subtotal">${{ number_format($billingCycle === 'yearly' ? $plan->getYearlyPrice() : $plan->monthly_price, 2) }}</span>
                        </div>
                        
                        <div id="discountRow" class="hidden flex justify-between text-green-600">
                            <span>Discount</span>
                            <span id="discountAmount">-$0.00</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-baseline">
                            <span class="text-lg font-semibold text-gray-900">Total</span>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-blue-600" id="totalAmount">
                                    ${{ number_format($billingCycle === 'yearly' ? $plan->getYearlyPrice() : $plan->monthly_price, 2) }}
                                </p>
                                <p class="text-xs text-gray-500 capitalize">per {{ $billingCycle === 'yearly' ? 'year' : 'month' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Security Badge --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center text-sm text-gray-500">
                            <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                            Secure payment processing
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let basePrice = {{ $billingCycle === 'yearly' ? $plan->getYearlyPrice() : $plan->monthly_price }};
let discountAmount = 0;

// Toggle manual payment fields
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const manualMethods = ['bkash', 'rocket', 'nagad'];
        const isManual = manualMethods.includes(this.value);
        
        document.getElementById('manualPaymentSection').classList.toggle('hidden', !isManual);
        document.getElementById('submitButtonText').textContent = isManual ? 'Submit Payment Details' : 'Complete Payment';
        
        // Set required on transaction_id for manual methods
        const transactionInput = document.querySelector('input[name="transaction_id"]');
        if (transactionInput) {
            if (isManual) {
                transactionInput.setAttribute('required', 'required');
            } else {
                transactionInput.removeAttribute('required');
            }
        }
    });
});

function validateCoupon() {
    const code = document.getElementById('coupon_input').value.trim();
    
    if (!code) {
        document.getElementById('couponFeedback').innerHTML = `
            <div class="text-red-600 text-sm">
                <i class="fas fa-times-circle mr-1"></i>
                Please enter a coupon code
            </div>
        `;
        return;
    }
    
    // Show loading
    document.getElementById('couponFeedback').innerHTML = `
        <div class="text-blue-600 text-sm">
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Validating coupon...
        </div>
    `;
    
    fetch('{{ route("subscription.validate-coupon") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            code: code, 
            plan_id: {{ $plan->id }}, 
            billing_cycle: '{{ $billingCycle }}' 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            discountAmount = data.discount;
            document.getElementById('applied_coupon_code').value = code;
            document.getElementById('couponFeedback').innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-green-700 text-sm">
                    <i class="fas fa-check-circle mr-1"></i>
                    Coupon applied successfully! You save $${data.discount.toFixed(2)}
                </div>
            `;
            updateTotals();
        } else {
            discountAmount = 0;
            document.getElementById('applied_coupon_code').value = '';
            document.getElementById('couponFeedback').innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">
                    <i class="fas fa-times-circle mr-1"></i>
                    ${data.message || 'Invalid coupon code'}
                </div>
            `;
            updateTotals();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('couponFeedback').innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">
                <i class="fas fa-times-circle mr-1"></i>
                Error validating coupon. Please try again.
            </div>
        `;
    });
}

function updateTotals() {
    const finalAmount = basePrice - discountAmount;
    
    if (discountAmount > 0) {
        document.getElementById('discountRow').classList.remove('hidden');
        document.getElementById('discountAmount').textContent = `-$${discountAmount.toFixed(2)}`;
    } else {
        document.getElementById('discountRow').classList.add('hidden');
    }
    
    document.getElementById('totalAmount').textContent = `$${finalAmount.toFixed(2)}`;
}
</script>
@endpush
