<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Modules\Subsription\Models\SubscriptionPlan;
use App\Modules\Subsription\Models\SubscriptionCoupon;
use App\Modules\Subsription\Models\UserSubscription;
use App\Modules\Subsription\Models\SubscriptionPayment;
use Carbon\Carbon;

class SubscriptionDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data safely
        SubscriptionPayment::query()->delete();
        UserSubscription::query()->delete();
        SubscriptionCoupon::query()->delete();
        SubscriptionPlan::query()->delete();
        
        // Ensure subscription permissions exist and are assigned
        $this->call(SubscriptionPermissionsSeeder::class);
        
        // Create 4 Subscription Plans
        $freePlan = SubscriptionPlan::create([
            'name' => 'Free Plan',
            'description' => 'Perfect for getting started with basic features',
            'monthly_price' => 0,
            'yearly_price' => 0,
            'features' => json_encode([
                'Up to 3 projects',
                '10 modules per project',
                '100 test cases',
                '1 collaborator',
                'Basic AI assistance',
                'Email support'
            ]),
            'max_projects' => 3,
            'max_modules' => 10,
            'max_test_cases' => 100,
            'max_collaborators' => 1,
            'is_active' => true,
            'sort_order' => 1
        ]);

        $starterPlan = SubscriptionPlan::create([
            'name' => 'Starter',
            'description' => 'Great for small teams and growing projects',
            'monthly_price' => 19.00,
            'yearly_price' => 190.00,
            'features' => json_encode([
                'Up to 10 projects',
                '50 modules per project',
                '500 test cases',
                '5 collaborators',
                'Advanced AI features',
                'Priority email support',
                'Basic analytics'
            ]),
            'max_projects' => 10,
            'max_modules' => 50,
            'max_test_cases' => 500,
            'max_collaborators' => 5,
            'is_active' => true,
            'sort_order' => 2
        ]);

        $proPlan = SubscriptionPlan::create([
            'name' => 'Professional',
            'description' => 'For professional teams requiring advanced capabilities',
            'monthly_price' => 49.00,
            'yearly_price' => 490.00,
            'features' => json_encode([
                'Up to 50 projects',
                'Unlimited modules',
                '2000 test cases',
                '20 collaborators',
                'Full AI capabilities',
                '24/7 support',
                'Advanced analytics',
                'API access',
                'Custom integrations'
            ]),
            'max_projects' => 50,
            'max_modules' => -1, // unlimited
            'max_test_cases' => 2000,
            'max_collaborators' => 20,
            'is_active' => true,
            'sort_order' => 3
        ]);

        $businessPlan = SubscriptionPlan::create([
            'name' => 'Business',
            'description' => 'Enterprise-grade solution for large organizations',
            'monthly_price' => 149.00,
            'yearly_price' => 1490.00,
            'features' => json_encode([
                'Unlimited projects',
                'Unlimited modules',
                'Unlimited test cases',
                'Unlimited collaborators',
                'Premium AI features',
                'Dedicated support manager',
                'Custom analytics',
                'Full API access',
                'White-label options',
                'SLA guarantee'
            ]),
            'max_projects' => -1, // unlimited
            'max_modules' => -1,
            'max_test_cases' => -1,
            'max_collaborators' => -1,
            'is_active' => true,
            'sort_order' => 4
        ]);

        // Create 5 Active Coupons
        $welcomeCoupon = SubscriptionCoupon::create([
            'code' => 'WELCOME50',
            'name' => 'Welcome Offer',
            'discount_type' => 'percentage',
            'discount_value' => 50.00,
            'description' => 'Welcome offer - 50% off first month',
            'max_redemptions' => 100,
            'times_redeemed' => 23,
            'is_active' => true,
            'valid_from' => Carbon::now()->subDays(10),
            'valid_until' => Carbon::now()->addDays(20),
            'applicable_plan_ids' => json_encode([$starterPlan->id, $proPlan->id])
        ]);

        SubscriptionCoupon::create([
            'code' => 'SAVE20',
            'name' => 'Save 20%',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'description' => '20% off on any plan',
            'max_redemptions' => 200,
            'times_redeemed' => 45,
            'is_active' => true,
            'valid_from' => Carbon::now()->subDays(5),
            'valid_until' => Carbon::now()->addDays(60),
            'applicable_plan_ids' => null // applicable to all plans
        ]);

        $blackFridayCoupon = SubscriptionCoupon::create([
            'code' => 'BLACKFRIDAY',
            'name' => 'Black Friday Special',
            'discount_type' => 'percentage',
            'discount_value' => 75.00,
            'description' => 'Black Friday Special - 75% off',
            'max_redemptions' => 50,
            'times_redeemed' => 12,
            'is_active' => true,
            'valid_from' => Carbon::now()->subDays(2),
            'valid_until' => Carbon::now()->addDays(5),
            'applicable_plan_ids' => json_encode([$proPlan->id, $businessPlan->id])
        ]);

        SubscriptionCoupon::create([
            'code' => 'STARTUP100',
            'name' => 'Startup Discount',
            'discount_type' => 'fixed_amount',
            'discount_value' => 100.00,
            'description' => 'Startup discount - $100 off',
            'max_redemptions' => 30,
            'times_redeemed' => 8,
            'is_active' => true,
            'valid_from' => Carbon::now()->subDays(15),
            'valid_until' => Carbon::now()->addDays(45),
            'applicable_plan_ids' => json_encode([$businessPlan->id])
        ]);

        $yearlyCoupon = SubscriptionCoupon::create([
            'code' => 'YEARLY25',
            'name' => 'Yearly Plan Discount',
            'discount_type' => 'percentage',
            'discount_value' => 25.00,
            'description' => '25% off on yearly plans',
            'max_redemptions' => null, // unlimited
            'times_redeemed' => 67,
            'is_active' => true,
            'applies_to_yearly_only' => true,
            'valid_from' => Carbon::now()->subMonth(),
            'valid_until' => null, // no expiry
            'applicable_plan_ids' => null
        ]);

        // Create 2 Inactive/Expired Coupons
        SubscriptionCoupon::create([
            'code' => 'EXPIRED50',
            'name' => 'Expired Summer Sale',
            'discount_type' => 'percentage',
            'discount_value' => 50.00,
            'description' => 'Expired summer sale',
            'max_redemptions' => 100,
            'times_redeemed' => 89,
            'is_active' => false,
            'valid_from' => Carbon::now()->subMonths(3),
            'valid_until' => Carbon::now()->subMonth(),
            'applicable_plan_ids' => null
        ]);

        // Create demo users with subscriptions
        $demoUser1 = User::firstOrCreate(
            ['email' => 'john.smith@example.com'],
            [
                'name' => 'John Smith',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        $demoUser2 = User::firstOrCreate(
            ['email' => 'sarah.johnson@example.com'],
            [
                'name' => 'Sarah Johnson',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        $demoUser3 = User::firstOrCreate(
            ['email' => 'michael.brown@example.com'],
            [
                'name' => 'Michael Brown',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        $demoUser4 = User::firstOrCreate(
            ['email' => 'emily.davis@example.com'],
            [
                'name' => 'Emily Davis',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Active subscription with manual payment (approved)
        $subscription1 = UserSubscription::create([
            'user_id' => $demoUser1->id,
            'subscription_plan_id' => $proPlan->id,
            'billing_cycle' => 'monthly',
            'amount' => 49.00,
            'final_amount' => 49.00,
            'status' => 'active',
            'payment_method' => 'manual',
            'current_period_start' => Carbon::now()->startOfMonth(),
            'current_period_end' => Carbon::now()->endOfMonth(),
            'auto_renew' => true,
        ]);

        SubscriptionPayment::create([
            'user_id' => $demoUser1->id,
            'subscription_id' => $subscription1->id,
            'amount' => 49.00,
            'payment_method' => 'bkash',
            'status' => 'completed',
            'transaction_id' => 'BKH' . time() . '001',
            'admin_notes' => 'Verified and approved',
            'approved_at' => Carbon::now()->subDays(5),
        ]);

        // Active subscription with Stripe
        $subscription2 = UserSubscription::create([
            'user_id' => $demoUser2->id,
            'subscription_plan_id' => $starterPlan->id,
            'billing_cycle' => 'yearly',
            'amount' => 190.00,
            'discount_amount' => 47.50,
            'final_amount' => 142.50, // with 25% discount
            'coupon_id' => $yearlyCoupon->id,
            'status' => 'active',
            'payment_method' => 'stripe',
            'stripe_subscription_id' => 'sub_' . uniqid(),
            'current_period_start' => Carbon::now()->subMonths(2),
            'current_period_end' => Carbon::now()->addMonths(10),
            'auto_renew' => true,
        ]);

        SubscriptionPayment::create([
            'user_id' => $demoUser2->id,
            'subscription_id' => $subscription2->id,
            'amount' => 142.50,
            'payment_method' => 'stripe',
            'status' => 'completed',
            'stripe_payment_intent_id' => 'pi_' . uniqid(),
            'approved_at' => Carbon::now()->subMonths(2),
        ]);

        // Pending payment (waiting admin approval)
        $subscription3 = UserSubscription::create([
            'user_id' => $demoUser3->id,
            'subscription_plan_id' => $businessPlan->id,
            'billing_cycle' => 'monthly',
            'amount' => 149.00,
            'discount_amount' => 111.75,
            'final_amount' => 37.25, // with BLACKFRIDAY coupon
            'coupon_id' => $blackFridayCoupon->id,
            'status' => 'pending',
            'payment_method' => 'manual',
            'current_period_start' => Carbon::now(),
            'current_period_end' => Carbon::now()->addMonth(),
            'auto_renew' => false,
        ]);

        SubscriptionPayment::create([
            'user_id' => $demoUser3->id,
            'subscription_id' => $subscription3->id,
            'amount' => 37.25,
            'payment_method' => 'rocket',
            'status' => 'pending',
            'transaction_id' => 'RKT' . time() . '003',
        ]);

        // Cancelled subscription
        $subscription4 = UserSubscription::create([
            'user_id' => $demoUser4->id,
            'subscription_plan_id' => $proPlan->id,
            'billing_cycle' => 'monthly',
            'amount' => 49.00,
            'final_amount' => 49.00,
            'status' => 'cancelled',
            'payment_method' => 'manual',
            'current_period_start' => Carbon::now()->subMonth(),
            'current_period_end' => Carbon::now(),
            'cancelled_at' => Carbon::now()->subDays(10),
            'auto_renew' => false,
        ]);

        SubscriptionPayment::create([
            'user_id' => $demoUser4->id,
            'subscription_id' => $subscription4->id,
            'amount' => 49.00,
            'payment_method' => 'nagad',
            'status' => 'completed',
            'transaction_id' => 'NAG' . time() . '004',
            'approved_at' => Carbon::now()->subMonth(),
        ]);

        // Add one more pending payment for demo
        $demoUser5 = User::firstOrCreate(
            ['email' => 'david.wilson@example.com'],
            [
                'name' => 'David Wilson',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        $subscription5 = UserSubscription::create([
            'user_id' => $demoUser5->id,
            'subscription_plan_id' => $starterPlan->id,
            'billing_cycle' => 'monthly',
            'amount' => 19.00,
            'discount_amount' => 9.50,
            'final_amount' => 9.50, // with WELCOME50 coupon
            'coupon_id' => $welcomeCoupon->id,
            'status' => 'pending',
            'payment_method' => 'manual',
            'current_period_start' => Carbon::now(),
            'current_period_end' => Carbon::now()->addMonth(),
            'auto_renew' => true,
        ]);

        SubscriptionPayment::create([
            'user_id' => $demoUser5->id,
            'subscription_id' => $subscription5->id,
            'amount' => 9.50,
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
            'transaction_id' => 'BANK' . time() . '005',
        ]);

        $this->command->info('✓ 4 subscription plans created');
        $this->command->info('✓ 7 coupons created (5 active, 2 expired)');
        $this->command->info('✓ 5 demo users created with subscriptions');
        $this->command->info('✓ 5 subscriptions created (2 active, 2 pending, 1 cancelled)');
        $this->command->info('✓ 5 payment records created (3 approved, 2 pending)');
        $this->command->info('');
        $this->command->info('Demo Users:');
        $this->command->info('  - john.smith@example.com (Active - Pro Monthly)');
        $this->command->info('  - sarah.johnson@example.com (Active - Starter Yearly)');
        $this->command->info('  - michael.brown@example.com (Pending - Business Monthly)');
        $this->command->info('  - emily.davis@example.com (Cancelled - Pro Monthly)');
        $this->command->info('  - david.wilson@example.com (Pending - Starter Monthly)');
        $this->command->info('  All passwords: password123');
    }
}
