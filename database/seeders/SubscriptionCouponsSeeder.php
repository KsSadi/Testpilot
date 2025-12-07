<?php

namespace Database\Seeders;

use App\Modules\Subsription\Models\SubscriptionCoupon;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SubscriptionCouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME50',
                'name' => 'Welcome Discount',
                'description' => 'Welcome discount - 50% off for new users',
                'discount_type' => 'percentage',
                'discount_value' => 50.00,
                'applicable_plan_ids' => null, // All plans
                'applies_to_yearly_only' => false,
                'duration' => 'once',
                'duration_in_months' => null,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'max_redemptions' => 100,
                'max_redemptions_per_user' => 1,
                'times_redeemed' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'NEWYEAR2025',
                'name' => 'New Year 2025 Special',
                'description' => 'New Year 2025 special - 30% off all plans',
                'discount_type' => 'percentage',
                'discount_value' => 30.00,
                'applicable_plan_ids' => null,
                'applies_to_yearly_only' => false,
                'duration' => 'once',
                'duration_in_months' => null,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::parse('2025-12-31'),
                'max_redemptions' => 500,
                'max_redemptions_per_user' => 1,
                'times_redeemed' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'YEARLYPLAN',
                'name' => 'Yearly Plan Bonus',
                'description' => 'Extra 20% off on yearly subscriptions',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'applicable_plan_ids' => null,
                'applies_to_yearly_only' => true,
                'duration' => 'once',
                'duration_in_months' => null,
                'valid_from' => Carbon::now(),
                'valid_until' => null, // No expiration
                'max_redemptions' => null, // Unlimited
                'max_redemptions_per_user' => 1,
                'times_redeemed' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'ENTERPRISE100',
                'name' => 'Enterprise Discount',
                'description' => 'Enterprise plan - Flat ৳10,000 off',
                'discount_type' => 'fixed_amount',
                'discount_value' => 10000.00,
                'applicable_plan_ids' => json_encode([4]), // Enterprise plan ID
                'applies_to_yearly_only' => false,
                'duration' => 'once',
                'duration_in_months' => null,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(6),
                'max_redemptions' => 10,
                'max_redemptions_per_user' => 1,
                'times_redeemed' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'FIRSTMONTH',
                'name' => 'First Month Free',
                'description' => 'First month free for Basic plan',
                'discount_type' => 'percentage',
                'discount_value' => 100.00,
                'applicable_plan_ids' => json_encode([2]), // Basic plan ID
                'applies_to_yearly_only' => false,
                'duration' => 'once',
                'duration_in_months' => null,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonth(),
                'max_redemptions' => 50,
                'max_redemptions_per_user' => 1,
                'times_redeemed' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            SubscriptionCoupon::updateOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }

        $this->command->info('Subscription coupons seeded successfully!');
        $this->command->info('Sample coupons: WELCOME50, NEWYEAR2025, YEARLYPLAN, ENTERPRISE100, FIRSTMONTH');
    }
}
