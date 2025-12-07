<?php

namespace App\Modules\Subsription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Subsription\Models\SubscriptionCoupon;
use App\Modules\Subsription\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = SubscriptionCoupon::with('redemptions')
            ->latest()
            ->paginate(20);
        
        return view('Subsription::admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::active()->get();
        
        return view('Subsription::admin.coupons.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:subscription_coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'applicable_plan_ids' => 'nullable|array',
            'applies_to_yearly_only' => 'boolean',
            'duration' => 'required|in:once,forever,repeating',
            'duration_in_months' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'max_redemptions' => 'nullable|integer|min:1',
            'max_redemptions_per_user' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['applies_to_yearly_only'] = $request->has('applies_to_yearly_only');
        $validated['is_active'] = $request->has('is_active');

        SubscriptionCoupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully!');
    }

    public function edit(SubscriptionCoupon $coupon)
    {
        $plans = SubscriptionPlan::active()->get();
        
        return view('Subsription::admin.coupons.edit', compact('coupon', 'plans'));
    }

    public function update(Request $request, SubscriptionCoupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:subscription_coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'applicable_plan_ids' => 'nullable|array',
            'applies_to_yearly_only' => 'boolean',
            'duration' => 'required|in:once,forever,repeating',
            'duration_in_months' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'max_redemptions' => 'nullable|integer|min:1',
            'max_redemptions_per_user' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['applies_to_yearly_only'] = $request->has('applies_to_yearly_only');
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully!');
    }

    public function destroy(SubscriptionCoupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully!');
    }

    public function toggle(SubscriptionCoupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $status = $coupon->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Coupon {$status} successfully!");
    }
}
