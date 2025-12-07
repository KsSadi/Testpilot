# Subscription System - Quick Reference

## 🚀 Quick Start Checklist

- [x] Database migrations run
- [x] Models created
- [x] Controllers implemented
- [x] Views created
- [x] Routes configured
- [x] Middleware registered
- [x] Permissions seeded
- [x] Default plans seeded
- [ ] Add navigation links to layout
- [ ] Apply middleware to existing routes
- [ ] Configure Stripe (if using)
- [ ] Test complete flow

## 📍 Important Routes

### User Routes
```
GET  /subscription              → View plans and current subscription
POST /subscription/subscribe    → Purchase a subscription
POST /subscription/validate-coupon → Validate coupon code
POST /subscription/cancel       → Cancel current subscription
POST /subscription/resume       → Resume cancelled subscription
```

### Admin Routes
```
# Plans
GET  /admin/subscriptions/plans              → List all plans
GET  /admin/subscriptions/plans/create       → Create plan form
POST /admin/subscriptions/plans              → Store new plan
GET  /admin/subscriptions/plans/{id}/edit    → Edit plan form
PUT  /admin/subscriptions/plans/{id}         → Update plan
DELETE /admin/subscriptions/plans/{id}       → Delete plan
POST /admin/subscriptions/plans/{id}/toggle  → Toggle active status

# Coupons
GET  /admin/subscriptions/coupons              → List all coupons
GET  /admin/subscriptions/coupons/create       → Create coupon form
POST /admin/subscriptions/coupons              → Store new coupon
GET  /admin/subscriptions/coupons/{id}/edit    → Edit coupon form
PUT  /admin/subscriptions/coupons/{id}         → Update coupon
DELETE /admin/subscriptions/coupons/{id}       → Delete coupon
POST /admin/subscriptions/coupons/{id}/toggle  → Toggle active status

# Payments
GET  /admin/subscriptions/payments           → Payment approval queue
GET  /admin/subscriptions/payments/{id}      → Review payment
POST /admin/subscriptions/payments/{id}/approve → Approve payment
POST /admin/subscriptions/payments/{id}/reject  → Reject payment

# Subscriptions
GET  /admin/subscriptions/manage                    → All subscriptions
POST /admin/subscriptions/manage/{id}/cancel        → Cancel subscription
POST /admin/subscriptions/manage/{id}/resume        → Resume subscription
POST /admin/subscriptions/manage/override-limits/{userId} → Override user limits
```

## 🎯 Common Code Snippets

### Check User Limits in Controller
```php
// In your controller
public function store(Request $request)
{
    if (!auth()->user()->canCreateProject()) {
        return redirect()->route('subscription.index')
            ->with('error', 'You have reached your project limit. Please upgrade your plan.');
    }
    
    // Continue with project creation...
}
```

### Add Middleware to Route
```php
Route::post('/projects', [ProjectController::class, 'store'])
    ->middleware(['auth', 'check.project.limit']);
```

### Display Usage in View
```blade
@php
    $usage = auth()->user()->getAllUsageStats();
    $plan = auth()->user()->currentPlan ?? auth()->user()->currentSubscription?->plan;
@endphp

<div class="usage-stats">
    <p>Projects: {{ $usage['projects_count'] }} / {{ $plan->isUnlimitedProjects() ? '∞' : $plan->max_projects }}</p>
    <p>Modules: {{ $usage['modules_count'] }} / {{ $plan->isUnlimitedModules() ? '∞' : $plan->max_modules }}</p>
    <p>Test Cases: {{ $usage['test_cases_count'] }} / {{ $plan->isUnlimitedTestCases() ? '∞' : $plan->max_test_cases }}</p>
    <p>Collaborators: {{ $usage['shared_count'] }} / {{ $plan->isUnlimitedCollaborators() ? '∞' : $plan->max_collaborators }}</p>
</div>
```

### Create Upgrade Banner
```blade
@if(!auth()->user()->canCreateProject())
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    You've reached your project limit.
    <a href="{{ route('subscription.index') }}" class="btn btn-sm btn-primary">
        Upgrade Plan
    </a>
</div>
@endif
```

## 🔧 Admin Quick Actions

### Create a New Plan
1. Navigate to `/admin/subscriptions/plans`
2. Click "Create New Plan"
3. Fill in plan details and limits
4. Set yearly discount percentage
5. Click "Create Plan"

### Create Discount Coupon
1. Navigate to `/admin/subscriptions/coupons`
2. Click "Create Coupon"
3. Enter unique code (uppercase)
4. Choose discount type and value
5. Set validity dates and usage limits
6. Select applicable plans (or leave empty for all)
7. Click "Create Coupon"

### Approve Manual Payment
1. Navigate to `/admin/subscriptions/payments`
2. Click "Review" on pending payment
3. Verify transaction ID and details
4. Click "Approve Payment" (or reject with reason)
5. Subscription activates automatically

### Override User Limits
1. Navigate to `/admin/subscriptions/manage`
2. Find user in subscription list
3. Click the sliders icon
4. Enter custom limits (-1 for unlimited)
5. Click "Update Limits"

## 📊 Database Quick Reference

### Default Plans
```
Free:     $0/mo   - 3 projects, 10 modules, 50 tests, 0 collab
Starter:  $19/mo  - 10 projects, 50 modules, 500 tests, 3 collab
Pro:      $49/mo  - 50 projects, 200 modules, 5000 tests, 10 collab
Business: $149/mo - Unlimited everything
```

### Coupon Example
```
Code: SAVE20
Type: Percentage
Value: 20
Valid Until: 2024-12-31
Max Uses: 100
Max Per User: 1
```

## 🛠️ Troubleshooting

### Issue: Middleware not working
**Solution:** Make sure middleware is registered in `bootstrap/app.php`

### Issue: Permission denied
**Solution:** Run `php artisan db:seed --class=SubscriptionPermissionsSeeder`

### Issue: Plans not showing
**Solution:** Run `php artisan db:seed --class=SubscriptionPlansSeeder`

### Issue: Routes not found
**Solution:** Clear route cache with `php artisan route:clear`

## 🔐 Permissions Reference

| Permission | Who Needs It |
|------------|-------------|
| `manage-subscriptions` | Admins managing plans/coupons |
| `approve-payments` | Admins approving manual payments |
| `view-all-subscriptions` | Admins viewing user subscriptions |
| `override-user-limits` | Super admins only |

## 📱 Middleware Reference

| Middleware | Apply To | Redirects To |
|------------|----------|--------------|
| `check.project.limit` | Project creation routes | `/subscription` |
| `check.module.limit` | Module creation routes | `/subscription` |
| `check.testcase.limit` | Test case creation routes | `/subscription` |
| `check.collaborator.limit` | Project sharing routes | Previous page |

## 💳 Payment Methods

### Stripe (Automatic)
- Instant activation
- Requires Stripe API keys in `.env`
- Credit/Debit cards

### Manual (Admin Approval Required)
- bKash
- Rocket
- Nagad
- Bank Transfer

## 🎨 CSS Classes Used

The views use Tailwind CSS classes. If using Bootstrap, you'll need to adjust:
- `primary-color` → Your brand's primary color class
- `rounded-xl` → `rounded`
- `shadow-sm` → `shadow-sm`
- Grid classes → Bootstrap grid

## ⚡ Performance Tips

1. **Eager Load Relationships**: When querying subscriptions, eager load plan and user
   ```php
   UserSubscription::with(['plan', 'user'])->get();
   ```

2. **Cache Plans**: Cache active plans for quick access
   ```php
   $plans = Cache::remember('active_plans', 3600, function() {
       return SubscriptionPlan::where('is_active', true)->get();
   });
   ```

3. **Index Database**: Add indexes to frequently queried columns
   - `user_subscriptions.user_id`
   - `subscription_payments.status`
   - `subscription_coupons.code`

## 📧 Email Templates Needed (Optional)

Create these notifications:
- `SubscriptionActivated`
- `PaymentApproved`
- `PaymentRejected`
- `SubscriptionExpiring`
- `SubscriptionCancelled`

## 🔄 Cron Jobs Needed (Optional)

Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Check for expiring subscriptions daily
    $schedule->command('subscriptions:check-expired')->daily();
    
    // Send renewal reminders 7 days before expiry
    $schedule->command('subscriptions:send-reminders')->daily();
}
```

---

**Need Help?** Check `SUBSCRIPTION_SYSTEM_GUIDE.md` for detailed documentation.
