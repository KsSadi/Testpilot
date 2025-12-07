# 🔄 Subscription System - Integration Migration Guide

This guide helps you integrate the subscription system with your existing TestPilot application.

---

## 🎯 Integration Overview

The subscription system is **ready to use** but needs to be connected to your existing:
1. Navigation menus
2. Project/Module/Test case creation routes
3. User registration flow
4. Dashboard statistics

---

## 📋 Step-by-Step Integration

### Step 1: Add Navigation Links (5 minutes)

#### For Users
Find your main layout file (likely `resources/views/layouts/backend/master.blade.php`) and add:

```blade
{{-- In user navigation menu --}}
<li class="nav-item">
    <a href="{{ route('subscription.index') }}" class="nav-link {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
        <i class="fas fa-crown text-yellow-500"></i>
        <span>My Subscription</span>
        @if(!auth()->user()->currentSubscription || auth()->user()->currentSubscription->status === 'cancelled')
            <span class="badge badge-warning">Upgrade</span>
        @endif
    </a>
</li>
```

#### For Admins
In your admin sidebar menu:

```blade
{{-- Subscription Management Section --}}
@can('manage-subscriptions')
<li class="nav-section">
    <span class="nav-section-title">Subscriptions</span>
</li>

<li class="nav-item">
    <a href="{{ route('admin.subscriptions.plans.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.plans.*') ? 'active' : '' }}">
        <i class="fas fa-box"></i>
        <span>Plans</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.subscriptions.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.coupons.*') ? 'active' : '' }}">
        <i class="fas fa-tags"></i>
        <span>Coupons</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.subscriptions.payments.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.payments.*') ? 'active' : '' }}">
        <i class="fas fa-money-check-alt"></i>
        <span>Payments</span>
        @php
            $pendingCount = \App\Modules\Subsription\Models\SubscriptionPayment::where('status', 'pending')->count();
        @endphp
        @if($pendingCount > 0)
            <span class="badge badge-danger">{{ $pendingCount }}</span>
        @endif
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('admin.subscriptions.manage.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.manage.*') ? 'active' : '' }}">
        <i class="fas fa-users-cog"></i>
        <span>Manage Subscriptions</span>
    </a>
</li>
@endcan
```

---

### Step 2: Apply Middleware to Existing Routes (10 minutes)

#### Find Your Project/Module/Test Routes

Look in your route files (likely in `app/Modules/Cypress/routes/web.php` or similar) and add middleware.

**Before:**
```php
Route::post('/projects', [ProjectController::class, 'store'])->middleware('auth');
```

**After:**
```php
Route::post('/projects', [ProjectController::class, 'store'])
    ->middleware(['auth', 'check.project.limit']);
```

#### Complete Route Updates

```php
// Project routes
Route::middleware(['auth', 'check.project.limit'])->group(function () {
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::post('/projects/create', [ProjectController::class, 'store']);
});

// Module routes  
Route::middleware(['auth', 'check.module.limit'])->group(function () {
    Route::post('/projects/{project}/modules', [ModuleController::class, 'store']);
    Route::post('/modules', [ModuleController::class, 'store']);
});

// Test case routes
Route::middleware(['auth', 'check.testcase.limit'])->group(function () {
    Route::post('/modules/{module}/test-cases', [TestCaseController::class, 'store']);
    Route::post('/test-cases', [TestCaseController::class, 'store']);
});

// Project sharing routes
Route::middleware(['auth', 'check.collaborator.limit'])->group(function () {
    Route::post('/projects/{project}/share', [ProjectShareController::class, 'store']);
    Route::post('/projects/{project}/collaborators', [CollaboratorController::class, 'store']);
});
```

---

### Step 3: Update User Registration (5 minutes)

Find your user registration controller (likely `app/Http/Controllers/Auth/RegisterController.php` or similar).

**Add to the registration success method:**

```php
use App\Modules\Subsription\Models\SubscriptionPlan;

protected function create(array $data)
{
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);

    // Assign free plan to new users
    $freePlan = SubscriptionPlan::where('monthly_price', 0)
                                 ->where('is_active', true)
                                 ->first();
    
    if ($freePlan) {
        $user->update([
            'current_plan_id' => $freePlan->id
        ]);
    }

    return $user;
}
```

---

### Step 4: Add Upgrade Prompts to Views (15 minutes)

#### In Project Creation View

```blade
{{-- At top of create project page --}}
@if(!auth()->user()->canCreateProject())
    <div class="alert alert-warning mb-4">
        <div class="flex items-center justify-between">
            <div>
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Project Limit Reached!</strong>
                <p class="mt-1 text-sm">
                    You've reached your limit of {{ auth()->user()->getEffectiveLimit('max_projects') }} projects.
                    Upgrade your plan to create more.
                </p>
            </div>
            <a href="{{ route('subscription.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-up mr-2"></i>Upgrade Plan
            </a>
        </div>
    </div>
@endif
```

#### In Dashboard (Show Usage Stats)

```blade
{{-- Add to dashboard --}}
@php
    $usage = auth()->user()->getAllUsageStats();
    $plan = auth()->user()->currentPlan ?? auth()->user()->currentSubscription?->plan;
@endphp

<div class="subscription-usage-card">
    <h3 class="text-lg font-semibold mb-4">
        <i class="fas fa-chart-bar mr-2"></i>
        Your Plan: {{ $plan ? $plan->name : 'Free' }}
    </h3>
    
    <div class="space-y-3">
        {{-- Projects --}}
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span>Projects</span>
                <span>{{ $usage['projects_count'] }} / {{ $plan && $plan->isUnlimitedProjects() ? '∞' : ($plan->max_projects ?? 0) }}</span>
            </div>
            @if($plan && !$plan->isUnlimitedProjects())
                @php
                    $projectPercent = ($usage['projects_count'] / max($plan->max_projects, 1)) * 100;
                @endphp
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar {{ $projectPercent >= 100 ? 'bg-danger' : ($projectPercent >= 80 ? 'bg-warning' : 'bg-success') }}" 
                         style="width: {{ min($projectPercent, 100) }}%"></div>
                </div>
            @endif
        </div>

        {{-- Modules --}}
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span>Modules</span>
                <span>{{ $usage['modules_count'] }} / {{ $plan && $plan->isUnlimitedModules() ? '∞' : ($plan->max_modules ?? 0) }}</span>
            </div>
            @if($plan && !$plan->isUnlimitedModules())
                @php
                    $modulePercent = ($usage['modules_count'] / max($plan->max_modules, 1)) * 100;
                @endphp
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar {{ $modulePercent >= 100 ? 'bg-danger' : ($modulePercent >= 80 ? 'bg-warning' : 'bg-success') }}" 
                         style="width: {{ min($modulePercent, 100) }}%"></div>
                </div>
            @endif
        </div>

        {{-- Test Cases --}}
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span>Test Cases</span>
                <span>{{ $usage['test_cases_count'] }} / {{ $plan && $plan->isUnlimitedTestCases() ? '∞' : ($plan->max_test_cases ?? 0) }}</span>
            </div>
            @if($plan && !$plan->isUnlimitedTestCases())
                @php
                    $testPercent = ($usage['test_cases_count'] / max($plan->max_test_cases, 1)) * 100;
                @endphp
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar {{ $testPercent >= 100 ? 'bg-danger' : ($testPercent >= 80 ? 'bg-warning' : 'bg-success') }}" 
                         style="width: {{ min($testPercent, 100) }}%"></div>
                </div>
            @endif
        </div>

        {{-- Collaborators --}}
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span>Collaborators</span>
                <span>{{ $usage['shared_count'] }} / {{ $plan && $plan->isUnlimitedCollaborators() ? '∞' : ($plan->max_collaborators ?? 0) }}</span>
            </div>
            @if($plan && !$plan->isUnlimitedCollaborators())
                @php
                    $collabPercent = ($usage['shared_count'] / max($plan->max_collaborators, 1)) * 100;
                @endphp
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar {{ $collabPercent >= 100 ? 'bg-danger' : ($collabPercent >= 80 ? 'bg-warning' : 'bg-success') }}" 
                         style="width: {{ min($collabPercent, 100) }}%"></div>
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('subscription.index') }}" class="btn btn-sm btn-outline-primary mt-3 w-full">
        <i class="fas fa-crown mr-2"></i>View Plans & Upgrade
    </a>
</div>
```

---

### Step 5: Update Existing Controllers (10 minutes)

While middleware handles most cases, you might want manual checks in controllers:

#### Example: Project Controller

```php
use App\Modules\Subsription\Models\SubscriptionPlan;

public function create()
{
    // Get user's current usage
    $usage = auth()->user()->getAllUsageStats();
    $plan = auth()->user()->currentPlan ?? auth()->user()->currentSubscription?->plan;
    
    // Pass to view for display
    return view('projects.create', compact('usage', 'plan'));
}

public function store(Request $request)
{
    // Middleware already checks limit, but you can add custom message
    if (!auth()->user()->canCreateProject()) {
        return redirect()->route('subscription.index')
            ->with('error', 'You have reached your project limit. Please upgrade to continue.');
    }
    
    // Continue with project creation...
    $project = Project::create([
        'user_id' => auth()->id(),
        'name' => $request->name,
        // ...
    ]);
    
    return redirect()->route('projects.show', $project)
        ->with('success', 'Project created successfully!');
}
```

---

### Step 6: Configure Stripe (Optional, 5 minutes)

If you want to use Stripe for payments:

1. **Install Stripe SDK:**
```bash
composer require stripe/stripe-php
```

2. **Add to `.env`:**
```env
STRIPE_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
```

3. **The subscription controller already handles Stripe integration** - just add your API keys!

---

### Step 7: Test Integration (15 minutes)

#### Manual Testing Steps:

1. **Test Free Plan (New User)**
   - [ ] Register new user
   - [ ] Verify free plan assigned
   - [ ] Create projects up to limit (3)
   - [ ] Try to create 4th project → Should be blocked
   - [ ] See upgrade prompt

2. **Test Plan Upgrade**
   - [ ] Visit subscription page
   - [ ] View all plans
   - [ ] Toggle monthly/yearly
   - [ ] Apply coupon code
   - [ ] Submit manual payment
   - [ ] Verify payment appears in admin queue

3. **Test Admin Approval**
   - [ ] Login as admin
   - [ ] Go to payments page
   - [ ] Review payment
   - [ ] Approve payment
   - [ ] Verify subscription activated

4. **Test Limit Enforcement**
   - [ ] With upgraded plan, create projects
   - [ ] Create modules
   - [ ] Create test cases
   - [ ] Share projects
   - [ ] Verify limits enforced correctly

5. **Test Admin Features**
   - [ ] Create new plan
   - [ ] Edit existing plan
   - [ ] Create coupon
   - [ ] View all subscriptions
   - [ ] Override user limits

---

## 🔍 Troubleshooting

### Issue: Middleware not blocking creation
**Check:**
1. Middleware registered in `bootstrap/app.php`
2. Middleware applied to correct routes
3. User has current_plan_id set
4. Clear route cache: `php artisan route:clear`

### Issue: Can't see admin menus
**Check:**
1. User has `manage-subscriptions` permission
2. Run: `php artisan db:seed --class=SubscriptionPermissionsSeeder`
3. Assign permission to user's role

### Issue: Plans not showing
**Check:**
1. Plans seeded: `php artisan db:seed --class=SubscriptionPlansSeeder`
2. Plans marked as active in database
3. Check `subscription_plans` table

### Issue: Routes not found
**Check:**
1. Module routes loaded in `modules.php`
2. Clear cache: `php artisan route:clear && php artisan config:clear`
3. Check `routes/web.php` in Subsription module

---

## 📊 Verification Checklist

After integration, verify:

- [ ] User can see "My Subscription" link
- [ ] User can view subscription plans
- [ ] User can apply coupons
- [ ] User can submit payment
- [ ] Admin can see subscription menus
- [ ] Admin can approve payments
- [ ] Limits are enforced on creation
- [ ] Upgrade prompts show when limit reached
- [ ] Usage stats display correctly
- [ ] New users get free plan automatically

---

## 🎯 Common Customizations

### Change Free Plan Limits

Edit `database/seeders/SubscriptionPlansSeeder.php`:
```php
[
    'name' => 'Free',
    'max_projects' => 5,      // Change from 3 to 5
    'max_modules' => 20,      // Change from 10 to 20
    // ...
]
```

Then re-seed: `php artisan db:seed --class=SubscriptionPlansSeeder`

### Add Custom Usage Tracking

In `app/Modules/Subsription/Traits/HasSubscription.php`, add:
```php
public function canDoCustomAction(): bool
{
    $limit = $this->getEffectiveLimit('max_custom_action');
    if ($limit === -1) return true;
    
    $currentUsage = $this->customActions()->count();
    return $currentUsage < $limit;
}
```

### Custom Redirect After Subscription

In `app/Modules/Subsription/Http/Controllers/SubscriptionController.php`:
```php
public function subscribe(Request $request)
{
    // ... existing code ...
    
    return redirect()->route('dashboard')  // Change redirect here
        ->with('success', 'Subscription activated!');
}
```

---

## 📝 Migration Complete!

After completing these steps, your subscription system will be fully integrated and functional.

**Estimated Total Time:** 1-2 hours

**Need Help?** Check:
- `SUBSCRIPTION_SYSTEM_GUIDE.md` - Complete documentation
- `SUBSCRIPTION_QUICK_REFERENCE.md` - Quick snippets
- `SUBSCRIPTION_CHECKLIST.md` - Detailed checklist

---

**Next Steps:**
1. Complete integration steps above
2. Test thoroughly
3. Configure Stripe (if needed)
4. Train admin users
5. Launch! 🚀
