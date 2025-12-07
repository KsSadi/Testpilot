<?php

namespace App\Modules\Subsription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Subsription\Models\UserSubscription;
use App\Modules\Subsription\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = UserSubscription::with(['user', 'plan']);

        // Filter by plan
        if ($request->has('plan') && $request->plan) {
            $query->where('plan_id', $request->plan);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by user
        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $subscriptions = $query->latest()->paginate(20);
        $plans = SubscriptionPlan::all();

        $stats = [
            'active' => UserSubscription::active()->count(),
            'pending' => UserSubscription::pending()->count(),
            'cancelled' => UserSubscription::where('status', 'cancelled')->count(),
            'total_revenue' => UserSubscription::where('status', 'active')->sum('final_amount'),
        ];

        return view('Subsription::admin.subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    public function show(UserSubscription $subscription)
    {
        $subscription->load(['user', 'plan', 'payments', 'coupon']);

        return view('Subsription::admin.subscriptions.show', compact('subscription'));
    }

    public function updateLimits(Request $request, User $user)
    {
        $validated = $request->validate([
            'override_max_projects' => 'required|integer|min:-1',
            'override_max_modules' => 'required|integer|min:-1',
            'override_max_test_cases' => 'required|integer|min:-1',
            'override_max_collaborators' => 'required|integer|min:-1',
        ]);

        $user->update($validated);

        return back()->with('success', 'User limits updated successfully!');
    }

    public function cancel(UserSubscription $subscription)
    {
        $subscription->cancel(immediately: false);

        return back()->with('success', 'Subscription will be cancelled at the end of billing period.');
    }

    public function cancelImmediately(UserSubscription $subscription)
    {
        $subscription->cancel(immediately: true);

        return back()->with('success', 'Subscription cancelled immediately!');
    }

    public function resume(UserSubscription $subscription)
    {
        $subscription->resume();

        return back()->with('success', 'Subscription resumed successfully!');
    }
}
