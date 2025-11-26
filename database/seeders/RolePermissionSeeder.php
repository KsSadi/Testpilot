<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Dashboard Permissions
            'view-dashboard',
            'view-analytics',
            
            // User Management Permissions
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Role Management Permissions
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            
            // Permission Management Permissions
            'view-permissions',
            'assign-permissions',
            
            // Settings Permissions
            'view-settings',
            'edit-settings',
            
            // Reports Permissions
            'view-reports',
            'export-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles and Assign Permissions

        // 1. Super Admin - Has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin - Has most permissions except role/permission management
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'view-dashboard',
            'view-analytics',
            'view-users',
            'create-users',
            'edit-users',
            'view-roles',
            'view-settings',
            'edit-settings',
            'view-reports',
            'export-reports',
        ]);

        // 3. Moderator - Has limited permissions
        $moderator = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        $moderator->givePermissionTo([
            'view-dashboard',
            'view-users',
            'edit-users',
            'view-reports',
        ]);

        // 4. User - Basic permissions only
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->givePermissionTo([
            'view-dashboard',
        ]);

        // Create Test Users for Each Role
        
        // Super Admin User
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        if (!$superAdminUser->hasRole('superadmin')) {
            $superAdminUser->assignRole('superadmin');
        }

        // Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }

        // Moderator User
        $moderatorUser = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Moderator User',
                'password' => Hash::make('password'),
            ]
        );
        if (!$moderatorUser->hasRole('moderator')) {
            $moderatorUser->assignRole('moderator');
        }

        // Regular User
        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
            ]
        );
        if (!$regularUser->hasRole('user')) {
            $regularUser->assignRole('user');
        }

        $this->command->info('Roles and Permissions seeded successfully!');
        $this->command->info('Test Users Created:');
        $this->command->info('Super Admin: superadmin@example.com / password');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Moderator: moderator@example.com / password');
        $this->command->info('User: user@example.com / password');
    }
}
