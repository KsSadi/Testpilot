@extends('layouts.backend.master')

@section('title', isset($coupon) ? 'Edit Coupon' : 'Create Coupon')

@section('content')
    <div class="page-title-section flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">{{ isset($coupon) ? 'Edit' : 'Create' }} Discount Coupon</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">
                Configure coupon code and discount settings
            </p>
        </div>
        <div>
            <a href="{{ route('admin.subscriptions.coupons.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Coupons
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
        <form action="{{ isset($coupon) ? route('admin.subscriptions.coupons.update', $coupon) : route('admin.subscriptions.coupons.store') }}" method="POST">
            @csrf
            @if(isset($coupon))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Coupon Code --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Coupon Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono uppercase" 
                           placeholder="SUMMER2024" required style="text-transform: uppercase;">
                    <p class="text-xs text-gray-500 mt-1">Must be unique and uppercase</p>
                </div>

                {{-- Discount Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Discount Type <span class="text-red-500">*</span>
                    </label>
                    <select name="discount_type" id="discount_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="percentage" {{ old('discount_type', $coupon->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type', $coupon->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                {{-- Discount Value --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Discount Value <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="discount_value" value="{{ old('discount_value', $coupon->discount_value ?? '') }}" 
                           step="0.01" min="0" id="discount_value"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="20" required>
                    <p class="text-xs text-gray-500 mt-1" id="discount_hint">Enter percentage value (0-100)</p>
                </div>

                {{-- Max Uses --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Maximum Total Uses
                    </label>
                    <input type="number" name="max_uses" value="{{ old('max_uses', $coupon->max_uses ?? '') }}" 
                           min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="Leave empty for unlimited">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                {{-- Max Uses Per User --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Max Uses Per User
                    </label>
                    <input type="number" name="max_uses_per_user" value="{{ old('max_uses_per_user', $coupon->max_uses_per_user ?? '') }}" 
                           min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="1">
                </div>

                {{-- Minimum Amount --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Minimum Purchase Amount ($)
                    </label>
                    <input type="number" name="minimum_amount" value="{{ old('minimum_amount', $coupon->minimum_amount ?? '') }}" 
                           step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                           placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                {{-- Valid From --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Valid From
                    </label>
                    <input type="datetime-local" name="valid_from" value="{{ old('valid_from', isset($coupon) && $coupon->valid_from ? $coupon->valid_from->format('Y-m-d\TH:i') : '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Valid Until --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Valid Until
                    </label>
                    <input type="datetime-local" name="valid_until" value="{{ old('valid_until', isset($coupon) && $coupon->valid_until ? $coupon->valid_until->format('Y-m-d\TH:i') : '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Applicable Plans --}}
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Applicable to Plans
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($plans as $plan)
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="applicable_plans[]" value="{{ $plan->id }}" 
                                   {{ isset($coupon) && in_array($plan->id, json_decode($coupon->applicable_plans ?? '[]')) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $plan->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-2">Leave unchecked to apply to all plans</p>
            </div>

            {{-- Active Status --}}
            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Active (users can use this coupon)</span>
                </label>
            </div>

            {{-- Submit Buttons --}}
            <div class="mt-8 flex items-center space-x-3">
                <button type="submit" class="px-6 py-3 primary-color text-white rounded-lg font-medium hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>{{ isset($coupon) ? 'Update' : 'Create' }} Coupon
                </button>
                <a href="{{ route('admin.subscriptions.coupons.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('discount_type').addEventListener('change', function() {
    const discountValue = document.getElementById('discount_value');
    const hint = document.getElementById('discount_hint');
    
    if (this.value === 'percentage') {
        discountValue.max = 100;
        hint.textContent = 'Enter percentage value (0-100)';
    } else {
        discountValue.removeAttribute('max');
        hint.textContent = 'Enter fixed dollar amount';
    }
});

// Auto-uppercase code input
document.querySelector('input[name="code"]').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endpush
