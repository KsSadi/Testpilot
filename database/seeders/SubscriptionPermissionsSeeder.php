<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SubscriptionPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create subscription-related permissions
        $permissions = [
            'manage-subscriptions' => 'Manage all subscription plans, coupons, and user subscriptions',
            'approve-payments' => 'Approve or reject manual payment submissions',
            'view-all-subscriptions' => 'View all user subscriptions',
            'override-user-limits' => 'Override subscription limits for individual users',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }

        // Assign all subscription permissions to Super Admin role
        $superAdminRole = Role::whereIn('name', ['Super Admin', 'superadmin'])->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo(array_keys($permissions));
            $this->command->info('Permissions assigned to Super Admin role!');
        }

        // Assign all subscription permissions to Admin role
        $adminRole = Role::whereIn('name', ['Admin', 'admin'])->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(array_keys($permissions));
            $this->command->info('Permissions assigned to Admin role!');
        }

        // Optionally create a Subscription Manager role
        $subscriptionManagerRole = Role::firstOrCreate(
            ['name' => 'Subscription Manager'],
            ['guard_name' => 'web']
        );
        
        $subscriptionManagerRole->givePermissionTo([
            'manage-subscriptions',
            'approve-payments',
            'view-all-subscriptions',
        ]);

        $this->command->info('Subscription permissions created and assigned successfully!');
    }
}
