<?php

namespace App\Modules\Subsription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Subsription\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->orderBy('monthly_price')->get();
        
        return view('Subsription::admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('Subsription::admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:subscription_plans,slug',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
            'yearly_discount_percentage' => 'required|integer|min:0|max:100',
            'max_projects' => 'required|integer|min:-1',
            'max_modules' => 'required|integer|min:-1',
            'max_test_cases' => 'required|integer|min:-1',
            'max_collaborators' => 'required|integer|min:-1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['features'] = $request->input('features', []);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Subscription plan created successfully!');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('Subsription::admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $plan->id,
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
            'yearly_discount_percentage' => 'required|integer|min:0|max:100',
            'max_projects' => 'required|integer|min:-1',
            'max_modules' => 'required|integer|min:-1',
            'max_test_cases' => 'required|integer|min:-1',
            'max_collaborators' => 'required|integer|min:-1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['features'] = $request->input('features', []);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        $plan->update($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Subscription plan updated successfully!');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        // Check if plan has active subscriptions
        if ($plan->activeSubscriptions()->count() > 0) {
            return back()->with('error', 'Cannot delete plan with active subscriptions!');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Subscription plan deleted successfully!');
    }

    public function toggle(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Plan {$status} successfully!");
    }
}
