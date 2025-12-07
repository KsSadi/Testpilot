# 📋 Subscription Limits & Expiration - Complete Guide

## ✅ WHAT'S IMPLEMENTED

Your subscription system is now **FULLY FUNCTIONAL** with:

### 1. **Subscription Limits Enforcement** ✅

When users try to create:
- **Projects** → System checks if they've reached their plan limit
- **Modules** → System checks if they've reached their plan limit  
- **Test Cases** → System checks if they've reached their plan limit

If limit is reached, they are **automatically redirected** to the subscription page with an error message.

### 2. **Automatic Expiration** ✅

Every day at **12:30 AM**, the system automatically:
- Checks all active subscriptions
- Finds subscriptions where `current_period_end` has passed
- Marks them as `expired`
- **Reverts user to FREE plan**
- User loses access to premium features

### 3. **Renewal Process** ⚠️ MANUAL

Currently, renewal is **manual**:
1. User subscription expires
2. User goes to subscription page
3. User selects a plan and pays again
4. Admin approves payment
5. User gets new subscription period

**Auto-renewal with Stripe is NOT implemented** - that requires Stripe webhook integration.

---

## 🔧 HOW IT WORKS

### **Middleware Protection**

Routes protected by subscription limits:

```php
// In: app/Modules/Cypress/routes/web.php

// Project creation - checks project limit
Route::post('projects', 'ProjectController@store')
    ->middleware('check.project.limit');

// Module creation - checks module limit  
Route::post('modules', 'ModuleController@store')
    ->middleware('check.module.limit');

// Test case creation - checks test case limit
Route::post('test-cases', 'TestCaseController@store')
    ->middleware('check.testcase.limit');
```

### **What Happens When Limit Reached?**

Example: User on **Basic Plan** (3 projects max) tries to create 4th project:

1. User clicks "Create Project"
2. Middleware `CheckProjectLimit` runs
3. Calls `auth()->user()->canCreateProject()`
4. System counts: User has 3 projects
5. Checks limit: Basic plan allows 3 projects
6. **3 < 3 = FALSE** → Limit reached!
7. Redirects to `/subscription` with error message:
   > "You have reached your project limit. Please upgrade your plan to create more projects."

### **Subscription Expiration Flow**

Example: User subscribed on **Dec 1, 2025** for 1 month:

**Dec 1, 2025:**
- `current_period_start` = Dec 1, 2025 00:00
- `current_period_end` = Jan 1, 2026 00:00
- `status` = active
- `current_plan_id` = 2 (Professional)

**Jan 1, 2026 at 12:30 AM:**
- Cron job runs: `php artisan subscriptions:check-expired`
- Finds subscription where `current_period_end` < now
- Updates: `status` = expired
- Updates user: `current_plan_id` = 1 (Free)
- User can now only create 3 projects instead of 10

**Jan 2, 2026:**
- User tries to create 4th project → **BLOCKED**
- Error message: "You have reached your project limit. Please upgrade..."
- User must renew subscription

---

## 📊 PLAN LIMITS

| Plan | Projects | Modules | Test Cases |
|------|----------|---------|------------|
| **Free** | 3 | 1 | 10 |
| **Basic** | 10 | 5 | 200 |
| **Professional** | 50 | Unlimited (-1) | 1000 |
| **Enterprise** | Unlimited | Unlimited | Unlimited |

**Note:** `-1` means unlimited

---

## 🛠️ ADMIN OVERRIDE

Admins can override limits for specific users:

1. Go to **Admin → Subscriptions → Manage**
2. Click "Override Limits" on any user
3. Set custom limits:
   - `override_max_projects` = 100 (custom limit)
   - `override_max_modules` = -1 (unlimited)
   - `override_max_test_cases` = 500 (custom limit)

These override the plan limits!

---

## ⏰ CRON JOB SETUP

The scheduler is already configured in `routes/console.php`:

```php
Schedule::command('subscriptions:check-expired')
    ->daily()
    ->at('00:30')
    ->name('subscriptions:expire')
    ->description('Check and expire subscriptions');
```

### **To Make It Work:**

You already have the scheduler setup script! Just run:

```powershell
.\setup-scheduler.ps1
```

Or manually run once per day:

```powershell
php artisan subscriptions:check-expired
```

### **Testing Expiration Manually:**

To test expiration without waiting, you can:

1. Create a test subscription
2. Manually set `current_period_end` to yesterday in database
3. Run: `php artisan subscriptions:check-expired`
4. Check that status changed to `expired` and user reverted to free plan

---

## 🔄 RENEWAL WORKFLOW

### **Current (Manual) Renewal:**

1. **User subscription expires**
   - Status changes to `expired`
   - User reverted to Free plan

2. **User wants to renew**
   - Goes to `/subscription`
   - Sees "Renew Subscription" or selects new plan
   - Goes through checkout process
   - Makes payment (Stripe or manual)

3. **Admin approves payment**
   - Creates NEW subscription record
   - Sets new `current_period_start` and `current_period_end`
   - Updates user's `current_subscription_id` and `current_plan_id`

4. **User regains access**
   - Can create projects/modules/test cases again
   - Subscription active for another month/year

### **Future (Auto-Renewal with Stripe):**

To implement auto-renewal, you would need:

1. **Stripe Subscription API** (not just one-time payments)
2. **Stripe Webhooks** to handle:
   - `invoice.payment_succeeded` → Auto-renew subscription
   - `invoice.payment_failed` → Mark as failed, retry
   - `customer.subscription.deleted` → Cancel subscription

3. **Update `auto_renew` field** handling in your code

---

## 🎯 TESTING CHECKLIST

### **Test Limits:**

- [ ] Create 3 projects on Free plan → 4th should be blocked
- [ ] Create 1 module on Free plan → 2nd should be blocked
- [ ] Create 10 test cases on Free plan → 11th should be blocked
- [ ] Subscribe to Professional plan → Can create 50 projects
- [ ] Admin overrides user to 100 projects → Can create 100

### **Test Expiration:**

- [ ] Create subscription with end date = yesterday
- [ ] Run: `php artisan subscriptions:check-expired`
- [ ] Check subscription status changed to `expired`
- [ ] Check user's `current_plan_id` changed to Free plan
- [ ] Try creating 4th project → Should be blocked

### **Test Renewal:**

- [ ] Expired user goes to subscription page
- [ ] Selects Professional plan
- [ ] Makes payment
- [ ] Admin approves
- [ ] User can create projects again

---

## 🐛 TROUBLESHOOTING

### **Limits not enforcing?**

Check:
1. Is `HasSubscription` trait added to User model? ✅
2. Are middleware registered in `bootstrap/app.php`? ✅
3. Are routes using the middleware? ✅
4. Does user have a `current_plan_id`?

### **Expiration not running?**

Check:
1. Is cron job scheduled? Run: `php artisan schedule:list`
2. Is Windows Task Scheduler running? Check Task Scheduler
3. Test manually: `php artisan subscriptions:check-expired`

### **User can't create anything even on paid plan?**

Check:
1. Is subscription status `active`?
2. Is `current_period_end` in the future?
3. Is `current_plan_id` set correctly?
4. Check `override_max_*` fields (should be `-1` if not overridden)

---

## 📝 SUMMARY

✅ **Limits are enforced** - Users cannot exceed their plan limits
✅ **Expiration works** - Subscriptions expire automatically after period ends
✅ **Renewal is manual** - Users must repurchase when expired
⚠️ **Auto-renewal NOT implemented** - Requires Stripe subscription webhooks

Your subscription system is **COMPLETE and FUNCTIONAL** for manual subscription management!
