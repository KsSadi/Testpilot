<?php

namespace App\Modules\Subsription\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Modules\Subsription\Models\SubscriptionPlan;
use App\Modules\Subsription\Models\SubscriptionCoupon;
use App\Modules\Subsription\Models\UserSubscription;
use App\Modules\Subsription\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        $currentSubscription = auth()->user()->currentSubscription;
        $usage = auth()->user()->getAllUsageStats();

        return view('Subsription::index', compact('plans', 'currentSubscription', 'usage'));
    }

    public function checkout(Request $request, $planId)
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $billingCycle = $request->get('billing_cycle', 'monthly');

        if (!in_array($billingCycle, ['monthly', 'yearly'])) {
            $billingCycle = 'monthly';
        }

        // Get dynamic currency conversion rate from database
        $usdToBdtRate = SystemSetting::get('currency_usd_to_bdt_rate', 110);
        
        // Get payment instructions
        $paymentInstructions = SystemSetting::where('group', 'payment_instructions')->get()->mapWithKeys(function($setting) {
            return [$setting->key => $setting->value];
        })->toArray();

        return view('Subsription::checkout', compact('plan', 'billingCycle', 'usdToBdtRate', 'paymentInstructions'));
    }

    public function mySubscription()
    {
        $user = auth()->user();
        $subscription = $user->currentSubscription;
        $usage = $user->getAllUsageStats();

        return view('Subsription::my-subscription', compact('subscription', 'usage'));
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $coupon = SubscriptionCoupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Invalid coupon code']);
        }

        if (!$coupon->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Coupon has expired or is no longer valid']);
        }

        if (!$coupon->canBeUsedBy(auth()->user())) {
            return response()->json(['valid' => false, 'message' => 'You have already used this coupon']);
        }

        if (!$coupon->appliesTo($request->plan_id, $request->billing_cycle)) {
            return response()->json(['valid' => false, 'message' => 'Coupon not applicable to this plan']);
        }

        $plan = SubscriptionPlan::find($request->plan_id);
        $amount = $request->billing_cycle === 'yearly' ? $plan->calculated_yearly_price : $plan->monthly_price;
        $discount = $coupon->calculateDiscount($amount);

        return response()->json([
            'valid' => true,
            'discount_amount' => $discount,
            'final_amount' => $amount - $discount,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
        ]);
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|in:stripe,bkash,rocket,nagad,bank_transfer',
            'coupon_code' => 'nullable|string',
            
            // For manual payments
            'transaction_id' => 'required_if:payment_method,bkash,rocket,nagad,bank_transfer',
            'sender_number' => 'required_if:payment_method,bkash,rocket,nagad',
            'payment_proof' => 'nullable|image|max:2048',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $amount = $request->billing_cycle === 'yearly' ? $plan->calculated_yearly_price : $plan->monthly_price;
        
        $couponId = null;
        $discountAmount = 0;

        // Apply coupon if provided
        if ($request->coupon_code) {
            $coupon = SubscriptionCoupon::where('code', strtoupper($request->coupon_code))->first();
            
            if ($coupon && $coupon->isValid() && $coupon->canBeUsedBy(auth()->user()) && $coupon->appliesTo($plan->id, $request->billing_cycle)) {
                $discountAmount = $coupon->calculateDiscount($amount);
                $couponId = $coupon->id;
            }
        }

        $finalAmount = $amount - $discountAmount;

        DB::beginTransaction();
        try {
            // Create subscription
            $periodStart = now();
            $periodEnd = $request->billing_cycle === 'yearly' ? $periodStart->copy()->addYear() : $periodStart->copy()->addMonth();

            $subscription = UserSubscription::create([
                'user_id' => auth()->id(),
                'subscription_plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'status' => $request->payment_method === 'stripe' ? 'active' : 'pending',
                'payment_method' => $request->payment_method,
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
                'amount' => $amount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'coupon_id' => $couponId,
            ]);

            // Create payment record
            $paymentData = [
                'user_id' => auth()->id(),
                'subscription_id' => $subscription->id,
                'payment_method' => $request->payment_method,
                'amount' => $finalAmount,
                'currency' => 'USD',
                'status' => $request->payment_method === 'stripe' ? 'completed' : 'pending',
            ];

            if (in_array($request->payment_method, ['bkash', 'rocket', 'nagad', 'bank_transfer'])) {
                $paymentData['transaction_id'] = $request->transaction_id;
                $paymentData['sender_number'] = $request->sender_number;

                if ($request->hasFile('payment_proof')) {
                    $paymentData['payment_proof'] = $request->file('payment_proof')->store('payment-proofs', 'public');
                }
            }

            $payment = SubscriptionPayment::create($paymentData);

            // Redeem coupon if used
            if ($couponId) {
                $coupon->redeem(auth()->user(), $subscription);
            }

            // If Stripe payment (auto-approved)
            if ($request->payment_method === 'stripe') {
                auth()->user()->update([
                    'current_subscription_id' => $subscription->id,
                    'current_plan_id' => $plan->id,
                ]);
            }

            DB::commit();

            if ($request->payment_method === 'stripe') {
                return redirect()->route('subscription.my-subscription')
                    ->with('success', 'Subscription activated successfully!');
            } else {
                return redirect()->route('subscription.my-subscription')
                    ->with('info', 'Payment submitted! Please wait for admin approval.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Subscription failed: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $subscription = auth()->user()->currentSubscription;

        if (!$subscription) {
            return back()->with('error', 'No active subscription found');
        }

        $subscription->cancel(immediately: false);

        return back()->with('success', 'Your subscription will be cancelled at the end of the current billing period.');
    }

    public function resume()
    {
        $subscription = auth()->user()->currentSubscription;

        if (!$subscription || !$subscription->cancel_at_period_end) {
            return back()->with('error', 'No cancelled subscription found');
        }

        $subscription->resume();

        return back()->with('success', 'Subscription resumed successfully!');
    }
}
