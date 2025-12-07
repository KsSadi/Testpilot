<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed settings and authentication settings
        $this->call([
            SettingSeeder::class,
            AuthSettingsSeeder::class,
            RolePermissionSeeder::class,
        ]);

        // Seed AI Providers
        $this->call([
            AIProvidersSeeder::class,
        ]);

        // Seed Subscription System
        $this->call([
            SubscriptionPlansSeeder::class,
            SubscriptionPermissionsSeeder::class,
            SubscriptionCouponsSeeder::class,
        ]);

        // Seed System Settings (Currency & Payment Gateways)
        $this->call([
            SystemSettingsSeeder::class,
        ]);

        // Create default admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@larakit.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create default test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'status' => 'active',
        ]);
    }
}
