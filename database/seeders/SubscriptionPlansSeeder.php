<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Subsription\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for trying out TestPilot',
                'monthly_price' => 0.00,
                'yearly_price' => 0.00,
                'yearly_discount_percentage' => 0,
                'max_projects' => 3,
                'max_modules' => 1,
                'max_test_cases' => 10,
                'max_collaborators' => 1,
                'features' => [
                    'Basic test recording',
                    'Chrome extension access',
                    'Community support',
                    'Export test scripts',
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'For freelancers and small teams',
                'monthly_price' => 19.00,
                'yearly_price' => 0, // Will be auto-calculated
                'yearly_discount_percentage' => 20, // 20% off yearly
                'max_projects' => 10,
                'max_modules' => 5,
                'max_test_cases' => 200,
                'max_collaborators' => 3,
                'features' => [
                    'Everything in Free',
                    'Advanced test recording',
                    'Share with team members',
                    'Email support',
                    'Test case organization',
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For professional QA teams',
                'monthly_price' => 49.00,
                'yearly_price' => 0,
                'yearly_discount_percentage' => 20,
                'max_projects' => 50,
                'max_modules' => -1, // Unlimited
                'max_test_cases' => 1000,
                'max_collaborators' => 10,
                'features' => [
                    'Everything in Starter',
                    'Unlimited modules',
                    'Team collaboration',
                    'Priority support',
                    'Advanced analytics',
                    'Test case templates',
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'For large teams and enterprises',
                'monthly_price' => 149.00,
                'yearly_price' => 0,
                'yearly_discount_percentage' => 25, // 25% off yearly
                'max_projects' => -1, // Unlimited
                'max_modules' => -1,
                'max_test_cases' => -1,
                'max_collaborators' => -1,
                'features' => [
                    'Everything in Pro',
                    'Unlimited everything',
                    'White-label options',
                    'Custom integrations',
                    'Dedicated support',
                    'SLA guarantee',
                    'Advanced security',
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
