# Subscription Module

## 📦 Overview

The Subscription module provides a complete, industry-standard subscription management system for TestPilot with:

- ✅ Dynamic subscription plan management
- ✅ Discount coupon system
- ✅ Multi-payment method support (Stripe + Manual)
- ✅ Usage limit tracking and enforcement
- ✅ Admin approval workflow for manual payments
- ✅ Comprehensive admin panel

## 🎯 Features

### For Users
- View and compare subscription plans
- Toggle between monthly and yearly billing
- Apply discount coupons at checkout
- Choose payment method (Stripe or manual)
- Track usage against plan limits
- Manage subscription (cancel/resume)

### For Admins
- Create, edit, delete subscription plans
- Configure resource limits per plan
- Create and manage discount coupons
- Approve/reject manual payment submissions
- View all user subscriptions
- Override individual user limits
- Monitor subscription statistics

## 📂 Module Structure

```
app/Modules/Subsription/
├── Http/
│   ├── Controllers/
│   │   ├── SubscriptionController.php          # User operations
│   │   └── Admin/
│   │       ├── PlanController.php              # Plan management
│   │       ├── CouponController.php            # Coupon management
│   │       ├── PaymentController.php           # Payment approvals
│   │       └── SubscriptionController.php      # Subscription management
│   └── Middleware/
│       ├── CheckProjectLimit.php
│       ├── CheckModuleLimit.php
│       ├── CheckTestCaseLimit.php
│       └── CheckCollaboratorLimit.php
├── Models/
│   ├── SubscriptionPlan.php
│   ├── UserSubscription.php
│   ├── SubscriptionCoupon.php
│   ├── CouponRedemption.php
│   └── SubscriptionPayment.php
├── Traits/
│   └── HasSubscription.php                     # Added to User model
├── resources/
│   └── views/
│       ├── index.blade.php                     # User subscription page
│       └── admin/
│           ├── plans/                          # Plan CRUD views
│           ├── coupons/                        # Coupon CRUD views
│           ├── payments/                       # Payment approval views
│           └── subscriptions/                  # Subscription management
└── routes/
    └── web.php                                 # All module routes
```

## 🗄️ Database Tables

1. **subscription_plans** - Plan definitions and limits
2. **user_subscriptions** - User subscription records
3. **subscription_coupons** - Discount coupons
4. **coupon_redemptions** - Coupon usage tracking
5. **subscription_payments** - Payment records
6. **users** (extended) - Subscription fields and overrides

## 🚀 Quick Start

### Installation

The module is already installed and ready. Just run migrations and seeders:

```bash
# Run migrations
php artisan migrate

# Seed default plans
php artisan db:seed --class=SubscriptionPlansSeeder

# Seed permissions
php artisan db:seed --class=SubscriptionPermissionsSeeder
```

### Usage

#### Check User Limits

```php
// In controller
if (!auth()->user()->canCreateProject()) {
    return redirect()->route('subscription.index')
        ->with('error', 'Project limit reached');
}
```

#### Get User Usage Stats

```php
$usage = auth()->user()->getAllUsageStats();
// Returns: ['projects_count' => X, 'modules_count' => Y, ...]
```

#### Apply Middleware

```php
Route::post('/projects', [ProjectController::class, 'store'])
    ->middleware(['auth', 'check.project.limit']);
```

## 🔐 Permissions

| Permission | Description |
|------------|-------------|
| `manage-subscriptions` | Full access to plans, coupons, and subscriptions |
| `approve-payments` | Approve/reject manual payments |
| `view-all-subscriptions` | View all user subscriptions |
| `override-user-limits` | Override individual user limits |

## 📊 Default Plans

| Plan | Monthly | Projects | Modules | Tests | Collaborators |
|------|---------|----------|---------|-------|---------------|
| Free | $0 | 3 | 10 | 50 | 0 |
| Starter | $19 | 10 | 50 | 500 | 3 |
| Pro | $49 | 50 | 200 | 5000 | 10 |
| Business | $149 | ∞ | ∞ | ∞ | ∞ |

All paid plans have 20% yearly discount by default.

## 🎨 Key Routes

### User Routes
- `GET /subscription` - View plans
- `POST /subscription/subscribe` - Purchase subscription
- `POST /subscription/validate-coupon` - Validate coupon
- `POST /subscription/cancel` - Cancel subscription
- `POST /subscription/resume` - Resume subscription

### Admin Routes
- `/admin/subscriptions/plans/*` - Plan management
- `/admin/subscriptions/coupons/*` - Coupon management
- `/admin/subscriptions/payments/*` - Payment approvals
- `/admin/subscriptions/manage/*` - Subscription management

## 💡 Advanced Usage

### Override User Limits

Admins can set custom limits for specific users that override plan limits:

```php
$user->update([
    'override_max_projects' => 100,     // Custom limit
    'override_max_modules' => -1,       // Unlimited
]);
```

### Create Custom Coupon

```php
SubscriptionCoupon::create([
    'code' => 'SAVE50',
    'discount_type' => 'percentage',
    'discount_value' => 50,
    'valid_from' => now(),
    'valid_until' => now()->addDays(30),
    'max_uses' => 100,
    'max_uses_per_user' => 1,
    'is_active' => true,
]);
```

### Check Coupon Validity

```php
$coupon = SubscriptionCoupon::where('code', 'SAVE50')->first();
if ($coupon && $coupon->isValid($userId, $planId, $amount)) {
    $discount = $coupon->calculateDiscount($amount, $plan);
}
```

## 🔧 Configuration

### Payment Methods

The system supports:
- **Stripe**: Automatic payment processing
- **Manual**: bKash, Rocket, Nagad, Bank Transfer (admin approval required)

### Limit Values

- **-1**: Represents unlimited
- **0+**: Specific limit number

### Billing Cycles

- **monthly**: Billed monthly
- **yearly**: Billed annually with discount

## 📖 Documentation

Comprehensive documentation available:

- **SUBSCRIPTION_SYSTEM_GUIDE.md** - Complete system guide
- **SUBSCRIPTION_QUICK_REFERENCE.md** - Quick reference
- **SUBSCRIPTION_INTEGRATION_GUIDE.md** - Integration steps
- **SUBSCRIPTION_CHECKLIST.md** - Implementation checklist
- **SUBSCRIPTION_FLOW_DIAGRAMS.md** - Visual flow diagrams

## 🧪 Testing

Test the complete flow:

1. Create test user
2. Visit `/subscription`
3. Select plan and apply coupon
4. Submit manual payment
5. Login as admin
6. Approve payment at `/admin/subscriptions/payments`
7. Verify subscription activated
8. Test limit enforcement

## 🐛 Troubleshooting

### Middleware Not Working
- Verify middleware registered in `bootstrap/app.php`
- Clear route cache: `php artisan route:clear`

### Plans Not Showing
- Run seeder: `php artisan db:seed --class=SubscriptionPlansSeeder`
- Check plans are active in database

### Permission Denied
- Run: `php artisan db:seed --class=SubscriptionPermissionsSeeder`
- Verify user role has required permissions

## 🔄 Extending

### Add New Resource Limit

1. Add column to `subscription_plans` migration
2. Add column to `users` migration (for overrides)
3. Add method to `HasSubscription` trait:
   ```php
   public function canDoCustomAction(): bool
   {
       $limit = $this->getEffectiveLimit('max_custom');
       if ($limit === -1) return true;
       return $this->customActions()->count() < $limit;
   }
   ```
4. Create middleware if needed

### Add New Payment Method

Update `SubscriptionController@subscribe` to handle new payment type.

## 📝 Notes

- All prices in USD by default
- Yearly price auto-calculated from monthly + discount
- Manual payments require admin approval
- Stripe payments activate instantly (when configured)
- Free plan assigned to new users automatically

## 🤝 Support

For issues or questions:
1. Check documentation files
2. Review SUBSCRIPTION_QUICK_REFERENCE.md
3. Verify database and migrations
4. Check permissions and roles

## 📄 License

Part of TestPilot application.

---

**Version:** 1.0.0  
**Status:** Production Ready ✅  
**Last Updated:** December 7, 2025
