<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'allow_registration',
                'value' => '1',
                'is_active' => true,
                'group' => 'general',
                'description' => 'Allow new user registration'
            ],
            [
                'key' => 'default_user_role',
                'value' => 'user',
                'is_active' => true,
                'group' => 'general',
                'description' => 'Default role assigned to new users'
            ],
            
            // Email Authentication
            [
                'key' => 'email_login_enabled',
                'value' => '1',
                'is_active' => true,
                'group' => 'email',
                'description' => 'Enable email-based login'
            ],
            [
                'key' => 'email_registration_enabled',
                'value' => '1',
                'is_active' => true,
                'group' => 'email',
                'description' => 'Enable email-based registration'
            ],
            [
                'key' => 'email_verification_required',
                'value' => '0',
                'is_active' => true,
                'group' => 'email',
                'description' => 'Require email verification after registration'
            ],
            
            // Mobile Authentication
            [
                'key' => 'mobile_login_enabled',
                'value' => '0',
                'is_active' => true,
                'group' => 'mobile',
                'description' => 'Enable mobile-based login with OTP'
            ],
            [
                'key' => 'mobile_registration_enabled',
                'value' => '0',
                'is_active' => true,
                'group' => 'mobile',
                'description' => 'Enable mobile-based registration'
            ],
            [
                'key' => 'mobile_verification_required',
                'value' => '0',
                'is_active' => true,
                'group' => 'mobile',
                'description' => 'Require mobile verification after registration'
            ],
            [
                'key' => 'otp_length',
                'value' => '6',
                'is_active' => true,
                'group' => 'mobile',
                'description' => 'Length of OTP code (4 or 6 digits)'
            ],
            [
                'key' => 'otp_expiry_minutes',
                'value' => '5',
                'is_active' => true,
                'group' => 'mobile',
                'description' => 'OTP expiry time in minutes'
            ],
            [
                'key' => 'otp_resend_cooldown_seconds',
                'value' => '60',
                'is_active' => true,
                'group' => 'mobile',
                'description' => 'Cooldown time before OTP can be resent'
            ],
            
            // Social Authentication
            [
                'key' => 'social_login_enabled',
                'value' => '0',
                'is_active' => true,
                'group' => 'social',
                'description' => 'Enable social login'
            ],
            [
                'key' => 'social_providers',
                'value' => json_encode(['google', 'facebook', 'github']),
                'is_active' => true,
                'group' => 'social',
                'description' => 'Enabled social login providers'
            ],
            [
                'key' => 'google_login_enabled',
                'value' => '0',
                'is_active' => true,
                'group' => 'social',
                'description' => 'Enable Google OAuth login'
            ],
            [
                'key' => 'facebook_login_enabled',
                'value' => '0',
                'is_active' => true,
                'group' => 'social',
                'description' => 'Enable Facebook OAuth login'
            ],
            [
                'key' => 'github_login_enabled',
                'value' => '0',
                'is_active' => true,
                'group' => 'social',
                'description' => 'Enable GitHub OAuth login'
            ],
        ];

        foreach ($settings as $setting) {
            \DB::table('auth_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
