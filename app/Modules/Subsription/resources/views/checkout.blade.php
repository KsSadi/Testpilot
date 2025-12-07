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
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-dollar-sign mr-2 text-blue-600"></i>
                        Select Currency
                    </h3>
                    <div class="flex gap-3">
                        <label class="relative flex-1 flex items-center p-4 border-2 border-blue-500 bg-blue-50 rounded-lg cursor-pointer hover:bg-blue-100 transition currency-selector" data-currency="USD">
                            <input type="radio" name="currency" value="USD" checked class="absolute opacity-0">
                            <div class="flex items-center justify-center w-full gap-3">
                                <div class="text-2xl">🇺🇸</div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900">USD ($)</p>
                                    <p class="text-xs text-gray-600">US Dollar</p>
                                </div>
                            </div>
                        </label>
                        <label class="relative flex-1 flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-green-50 hover:border-green-300 transition currency-selector" data-currency="BDT">
                            <input type="radio" name="currency" value="BDT" class="absolute opacity-0">
                            <div class="flex items-center justify-center w-full gap-3">
                                <div class="text-2xl">🇧🇩</div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900">BDT (৳)</p>
                                    <p class="text-xs text-gray-600">Bangladeshi Taka</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Coupon Code --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-credit-card mr-2 text-blue-600"></i>
                        Payment Method
                    </h3>
                    
                    {{-- USD Payment Methods --}}
                    <div id="usd-payment-methods" class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition group">
                            <input type="radio" name="payment_method" value="stripe" checked 
                                   class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500">
                            <div class="ml-4 flex-1 flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fab fa-cc-stripe text-indigo-600 text-3xl mr-3"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">Credit/Debit Card</p>
                                        <p class="text-xs text-gray-500">Pay securely with Stripe</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <i class="fab fa-cc-visa text-blue-600 text-2xl"></i>
                                    <i class="fab fa-cc-mastercard text-red-600 text-2xl"></i>
                                    <i class="fab fa-cc-amex text-blue-500 text-2xl"></i>
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- BDT Payment Methods --}}
                    <div id="bdt-payment-methods" class="space-y-3 hidden">
                        {{-- bKash --}}
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-pink-50 hover:border-pink-300 transition group payment-method-card" data-method="bkash">
                            <input type="radio" name="payment_method" value="bkash" 
                                   class="w-4 h-4 text-pink-600 focus:ring-2 focus:ring-pink-500 payment-radio">
                            <div class="ml-4 flex-1 flex items-center">
                                <img src="https://download.logo.wine/logo/BKash/BKash-Logo.wine.png" alt="bKash" class="h-8 w-20 object-contain mr-3">
                                <div>
                                    <p class="font-medium text-gray-900">bKash</p>
                                    <p class="text-xs text-gray-500">Mobile wallet payment</p>
                                </div>
                            </div>
                        </label>

                        {{-- Nagad --}}
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-orange-50 hover:border-orange-300 transition group payment-method-card" data-method="nagad">
                            <input type="radio" name="payment_method" value="nagad" 
                                   class="w-4 h-4 text-orange-600 focus:ring-2 focus:ring-orange-500 payment-radio">
                            <div class="ml-4 flex-1 flex items-center">
                                <img src="https://seeklogo.com/images/N/nagad-logo-7A70CCFEE8-seeklogo.com.png" alt="Nagad" class="h-8 w-20 object-contain mr-3">
                                <div>
                                    <p class="font-medium text-gray-900">Nagad</p>
                                    <p class="text-xs text-gray-500">Mobile wallet payment</p>
                                </div>
                            </div>
                        </label>

                        {{-- Rocket --}}
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-purple-50 hover:border-purple-300 transition group payment-method-card" data-method="rocket">
                            <input type="radio" name="payment_method" value="rocket" 
                                   class="w-4 h-4 text-purple-600 focus:ring-2 focus:ring-purple-500 payment-radio">
                            <div class="ml-4 flex-1 flex items-center">
                                <img src="https://seeklogo.com/images/D/dutch-bangla-rocket-logo-B4D1CC458D-seeklogo.com.png" alt="Rocket" class="h-8 w-20 object-contain mr-3">
                                <div>
                                    <p class="font-medium text-gray-900">Rocket</p>
                                    <p class="text-xs text-gray-500">Mobile wallet payment</p>
                                </div>
                            </div>
                        </label>

                        {{-- Bank Transfer --}}
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-green-50 hover:border-green-300 transition group payment-method-card" data-method="bank_transfer">
                            <input type="radio" name="payment_method" value="bank_transfer" 
                                   class="w-4 h-4 text-green-600 focus:ring-2 focus:ring-green-500 payment-radio">
                            <div class="ml-4 flex-1 flex items-center">
                                <i class="fas fa-university text-green-600 text-2xl mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-900">Bank Transfer</p>
                                    <p class="text-xs text-gray-500">Direct bank transfer</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Payment Instructions Modal (Shows when BDT payment selected) --}}
                <div id="paymentInstructionsSection" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <span id="instructionTitle">Payment Instructions</span>
                        </h3>
                    </div>
                    
                    <div id="instructionContent" class="mb-4"></div>
                </div>

                {{-- Manual Payment Details (for BDT only) --}}
                <div id="bdtPaymentSection" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hidden">
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
                
                <div class="space-y-4">
                    {{-- Plan Details --}}
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-sm text-gray-500">Selected Plan</p>
                        <p class="text-lg font-bold text-gray-900">{{ $plan->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-sync-alt mr-1"></i>
                            Billed {{ ucfirst($billingCycle) }}
                        </p>
                    </div>

                    {{-- Features Summary --}}
                    <div class="pb-4 border-b border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-2">Included:</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><i class="fas fa-check text-green-600 mr-2"></i>
                                {{ $plan->max_projects == -1 ? 'Unlimited' : $plan->max_projects }} Projects
                            </li>
                            <li><i class="fas fa-check text-green-600 mr-2"></i>
                                {{ $plan->max_modules == -1 ? 'Unlimited' : $plan->max_modules }} Modules
                            </li>
                            <li><i class="fas fa-check text-green-600 mr-2"></i>
                                {{ $plan->max_test_cases == -1 ? 'Unlimited' : $plan->max_test_cases }} Test Cases
                            </li>
                            <li><i class="fas fa-check text-green-600 mr-2"></i>
                                {{ $plan->max_collaborators == -1 ? 'Unlimited' : $plan->max_collaborators }} Collaborators
                            </li>
                        </ul>
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900" id="subtotalAmount">
                                $<span class="price-value">{{ number_format($billingCycle === 'yearly' ? $plan->getYearlyPrice() : $plan->monthly_price, 2) }}</span>
                            </span>
                        </div>
                        
                        <div class="flex justify-between text-sm hidden" id="discountRow">
                            <span class="text-gray-600">Discount</span>
                            <span class="font-medium text-green-600" id="discountAmount">-$<span class="price-value">0.00</span></span>
                        </div>
                        
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-base font-semibold text-gray-900">Total</span>
                                <span class="text-xl font-bold text-blue-600" id="totalAmount">
                                    $<span class="price-value">{{ number_format($billingCycle === 'yearly' ? $plan->getYearlyPrice() : $plan->monthly_price, 2) }}</span>
                                </span>
                            </div>
                            @if($billingCycle === 'yearly' && $plan->yearly_discount_percentage > 0)
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-badge-percent mr-1"></i>
                                    You save {{ $plan->yearly_discount_percentage }}% with yearly billing
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Security Badge --}}
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center text-xs text-gray-500">
                            <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                            <span>Secure payment processing</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
const basePrice = parseFloat({!! json_encode($billingCycle === 'yearly' ? $plan->getYearlyPrice() : $plan->monthly_price) !!});
const usdToBdtRate = {!! json_encode($usdToBdtRate) !!};
let discountAmount = 0;
let currentCurrency = 'USD';

// Payment instructions data
const paymentInstructions = {!! json_encode($paymentInstructions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

// Currency Selection
document.querySelectorAll('.currency-selector').forEach(selector => {
    selector.addEventListener('click', function(e) {
        e.preventDefault();
        const currency = this.dataset.currency;
        currentCurrency = currency;
        
        // Check the radio button
        this.querySelector('input[type="radio"]').checked = true;
        
        // Update UI - Remove all active states
        document.querySelectorAll('.currency-selector').forEach(s => {
            s.classList.remove('border-blue-500', 'bg-blue-50', 'border-blue-100');
            s.classList.remove('border-green-500', 'bg-green-50', 'border-green-100');
            s.classList.add('border-gray-200');
        });
        
        if (currency === 'USD') {
            this.classList.remove('border-gray-200');
            this.classList.add('border-blue-500', 'bg-blue-50');
            document.getElementById('usd-payment-methods').classList.remove('hidden');
            document.getElementById('bdt-payment-methods').classList.add('hidden');
            document.getElementById('bdtPaymentSection').classList.add('hidden');
            const stripeRadio = document.querySelector('input[name="payment_method"][value="stripe"]');
            if (stripeRadio) stripeRadio.checked = true;
        } else {
            this.classList.remove('border-gray-200');
            this.classList.add('border-green-500', 'bg-green-50');
            document.getElementById('usd-payment-methods').classList.add('hidden');
            document.getElementById('bdt-payment-methods').classList.remove('hidden');
            document.getElementById('bdtPaymentSection').classList.remove('hidden');
            document.getElementById('paymentInstructionsSection').classList.add('hidden');
            const bkashRadio = document.querySelector('input[name="payment_method"][value="bkash"]');
            if (bkashRadio) {
                bkashRadio.checked = true;
                showPaymentInstructions('bkash');
            }
        }
        
        updateCurrencyDisplay();
    });
});

// Payment Method Selection for BDT
document.querySelectorAll('.payment-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const method = this.value;
        
        // Update card borders
        document.querySelectorAll('.payment-method-card').forEach(card => {
            card.classList.remove('border-pink-500', 'border-orange-500', 'border-purple-500', 'border-green-500', 'bg-pink-50', 'bg-orange-50', 'bg-purple-50', 'bg-green-50');
            card.classList.add('border-gray-200');
        });
        
        const selectedCard = this.closest('.payment-method-card');
        if (selectedCard) {
            const colorMap = {
                'bkash': ['border-pink-500', 'bg-pink-50'],
                'nagad': ['border-orange-500', 'bg-orange-50'],
                'rocket': ['border-purple-500', 'bg-purple-50'],
                'bank_transfer': ['border-green-500', 'bg-green-50']
            };
            const colors = colorMap[method] || [];
            selectedCard.classList.remove('border-gray-200');
            selectedCard.classList.add(...colors);
        }
        
        showPaymentInstructions(method);
        document.getElementById('bdtPaymentSection').classList.remove('hidden');
    });
});

function showPaymentInstructions(method) {
    const instructionsSection = document.getElementById('paymentInstructionsSection');
    const instructionTitle = document.getElementById('instructionTitle');
    const instructionContent = document.getElementById('instructionContent');
    
    if (!instructionsSection || currentCurrency !== 'BDT') {
        return;
    }
    
    const methodMap = {
        'bkash': {
            title: 'bKash Payment Instructions',
            logo: '<img src="https://download.logo.wine/logo/BKash/BKash-Logo.wine.png" alt="bKash" class="h-8 w-24 object-contain mb-3">',
            number: paymentInstructions.payment_bkash_number,
            type: paymentInstructions.payment_bkash_type,
            instructions: paymentInstructions.payment_bkash_instructions,
            color: 'pink'
        },
        'nagad': {
            title: 'Nagad Payment Instructions',
            logo: '<img src="https://seeklogo.com/images/N/nagad-logo-7A70CCFEE8-seeklogo.com.png" alt="Nagad" class="h-8 w-24 object-contain mb-3">',
            number: paymentInstructions.payment_nagad_number,
            type: paymentInstructions.payment_nagad_type,
            instructions: paymentInstructions.payment_nagad_instructions,
            color: 'orange'
        },
        'rocket': {
            title: 'Rocket Payment Instructions',
            logo: '<img src="https://seeklogo.com/images/D/dutch-bangla-rocket-logo-B4D1CC458D-seeklogo.com.png" alt="Rocket" class="h-8 w-24 object-contain mb-3">',
            number: paymentInstructions.payment_rocket_number,
            instructions: paymentInstructions.payment_rocket_instructions,
            color: 'purple'
        },
        'bank_transfer': {
            title: 'Bank Transfer Instructions',
            logo: '<i class="fas fa-university text-green-600 text-3xl mb-3"></i>',
            bankName: paymentInstructions.payment_bank_name,
            accountName: paymentInstructions.payment_bank_account_name,
            accountNumber: paymentInstructions.payment_bank_account_number,
            branch: paymentInstructions.payment_bank_branch,
            routing: paymentInstructions.payment_bank_routing,
            instructions: paymentInstructions.payment_bank_instructions,
            color: 'green'
        }
    };
    
    const info = methodMap[method];
    if (!info) return;
    
    instructionTitle.textContent = info.title;
    
    let content = `<div class="bg-${info.color}-50 border-l-4 border-${info.color}-500 p-4 rounded-r-lg mb-4">`;
    content += info.logo;
    
    if (method === 'bank_transfer') {
        content += `
            <div class="space-y-2 text-sm">
                <div class="flex"><span class="font-semibold w-32">Bank Name:</span><span class="text-gray-700">${info.bankName}</span></div>
                <div class="flex"><span class="font-semibold w-32">Account Name:</span><span class="text-gray-700">${info.accountName}</span></div>
                <div class="flex"><span class="font-semibold w-32">Account Number:</span><span class="text-gray-700 font-mono">${info.accountNumber}</span></div>
                <div class="flex"><span class="font-semibold w-32">Branch:</span><span class="text-gray-700">${info.branch}</span></div>
                ${info.routing ? `<div class="flex"><span class="font-semibold w-32">Routing:</span><span class="text-gray-700 font-mono">${info.routing}</span></div>` : ''}
            </div>
        `;
    } else {
        content += `
            <div class="space-y-2 text-sm">
                <div class="flex"><span class="font-semibold w-32">Number:</span><span class="text-gray-700 font-mono text-lg">${info.number}</span></div>
                <div class="flex"><span class="font-semibold w-32">Account Type:</span><span class="text-gray-700">${info.type}</span></div>
            </div>
        `;
    }
    
    content += `</div>`;
    content += `<div class="bg-gray-50 rounded-lg p-4">`;
    content += `<h4 class="font-semibold text-gray-800 mb-3 flex items-center"><i class="fas fa-list-ol mr-2 text-${info.color}-600"></i>How to Pay</h4>`;
    content += `<ol class="list-none space-y-2 text-sm text-gray-700">`;
    
    const steps = info.instructions.split('\n');
    steps.forEach(step => {
        if (step.trim()) {
            content += `<li class="flex items-start"><span class="text-${info.color}-600 mr-2">•</span><span>${step.trim()}</span></li>`;
        }
    });
    
    content += `</ol></div>`;
    
    instructionContent.innerHTML = content;
    instructionsSection.classList.remove('hidden');
}

function updateCurrencyDisplay() {
    const currencySymbol = currentCurrency === 'USD' ? '$' : '৳';
    const conversionRate = currentCurrency === 'USD' ? 1 : usdToBdtRate;
    
    const subtotal = basePrice * conversionRate;
    const discount = discountAmount * conversionRate;
    const total = (basePrice - discountAmount) * conversionRate;
    
    // Format numbers properly
    const formattedSubtotal = currentCurrency === 'USD' ? subtotal.toFixed(2) : Math.round(subtotal).toLocaleString();
    const formattedDiscount = currentCurrency === 'USD' ? discount.toFixed(2) : Math.round(discount).toLocaleString();
    const formattedTotal = currentCurrency === 'USD' ? total.toFixed(2) : Math.round(total).toLocaleString();
    
    document.getElementById('subtotalAmount').innerHTML = currencySymbol + formattedSubtotal;
    document.getElementById('discountAmount').innerHTML = '-' + currencySymbol + formattedDiscount;
    document.getElementById('totalAmount').innerHTML = currencySymbol + formattedTotal;
}

function validateCoupon() {
    const code = document.getElementById('coupon_input').value.trim();
    
    if (!code) {
        document.getElementById('couponFeedback').innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">
                <i class="fas fa-times-circle mr-1"></i>
                Please enter a coupon code
            </div>
        `;
        return;
    }
    
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
            document.getElementById('discountRow').classList.remove('hidden');
            updateCurrencyDisplay();
        } else {
            discountAmount = 0;
            document.getElementById('applied_coupon_code').value = '';
            document.getElementById('couponFeedback').innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 text-sm">
                    <i class="fas fa-times-circle mr-1"></i>
                    ${data.message || 'Invalid coupon code'}
                </div>
            `;
            document.getElementById('discountRow').classList.add('hidden');
            updateCurrencyDisplay();
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
</script>
@endpush
