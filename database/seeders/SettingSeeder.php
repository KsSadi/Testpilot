<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Setting\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing settings (optional - comment out if you want to preserve existing data)
        // Setting::truncate();

        $settings = [
            // ============================================
            // GENERAL SETTINGS
            // ============================================
            [
                'key' => 'app_name',
                'value' => 'LaraKit',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Application Name',
                'description' => 'Application name displayed across the system',
            ],
            [
                'key' => 'app_tagline',
                'value' => 'Modern Laravel Admin Kit',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Tagline',
                'description' => 'Application tagline or slogan',
            ],
            [
                'key' => 'app_description',
                'value' => 'A powerful and modern Laravel admin dashboard with role-based permissions, modular architecture, and comprehensive settings management.',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Application description for SEO and about pages',
                'label' => null,
            ],
            [
                'key' => 'logo',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application logo file path',
                'label' => null,
            ],
            [
                'key' => 'favicon',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'description' => 'Application favicon file path',
                'label' => null,
            ],
            [
                'key' => 'contact_email',
                'value' => 'admin@larakit.local',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Primary contact email address',
                'label' => null,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+1 (555) 123-4567',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Primary contact phone number',
                'label' => null,
            ],
            [
                'key' => 'address',
                'value' => '123 Main Street, Suite 100, City, State 12345',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Physical address',
                'label' => null,
            ],
            [
                'key' => 'timezone',
                'value' => 'UTC',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Default timezone for the application',
                'label' => null,
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Date format used across the application',
                'label' => null,
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i:s',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Time format used across the application',
                'label' => null,
            ],
            [
                'key' => 'footer_text',
                'value' => 'Built with Laravel & TailwindCSS',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Footer text displayed on all pages',
                'label' => null,
            ],
            [
                'key' => 'copyright_text',
                'value' => '© ' . date('Y') . ' LaraKit. All rights reserved.',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Copyright text displayed in footer',
                'label' => null,
            ],

            // ============================================
            // SEO SETTINGS
            // ============================================
            [
                'key' => 'meta_title',
                'value' => 'LaraKit - Modern Laravel Admin Dashboard',
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Default meta title for SEO',
                'label' => null,
            ],
            [
                'key' => 'meta_description',
                'value' => 'LaraKit is a modern Laravel admin dashboard with role-based permissions, modular architecture, and comprehensive settings management.',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Default meta description for SEO',
                'label' => null,
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'laravel, admin dashboard, role permissions, modular, tailwindcss',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Default meta keywords for SEO',
                'label' => null,
            ],
            [
                'key' => 'meta_author',
                'value' => 'LaraKit',
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Meta author tag',
                'label' => null,
            ],
            [
                'key' => 'og_title',
                'value' => 'LaraKit - Modern Laravel Admin Dashboard',
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Open Graph title for social sharing',
                'label' => null,
            ],
            [
                'key' => 'og_description',
                'value' => 'A powerful and modern Laravel admin dashboard with role-based permissions.',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Open Graph description for social sharing',
                'label' => null,
            ],
            [
                'key' => 'og_image',
                'value' => null,
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Open Graph image for social sharing',
                'label' => null,
            ],

            // ============================================
            // AUTH & SECURITY SETTINGS (Basic)
            // ============================================
            [
                'key' => 'allow_registration',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Allow new user registration',
                'label' => null,
            ],
            [
                'key' => 'require_email_verification',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Require email verification after registration',
                'label' => null,
            ],
            [
                'key' => 'require_mobile_verification',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Require mobile verification after registration',
                'label' => null,
            ],
            [
                'key' => 'enable_2fa',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Enable two-factor authentication',
                'label' => null,
            ],
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'integer',
                'group' => 'auth',
                'description' => 'Session lifetime in minutes',
                'label' => null,
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'group' => 'auth',
                'description' => 'Minimum password length',
                'label' => null,
            ],
            [
                'key' => 'password_require_uppercase',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Require uppercase letters in password',
                'label' => null,
            ],
            [
                'key' => 'password_require_numbers',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Require numbers in password',
                'label' => null,
            ],
            [
                'key' => 'password_require_special',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'auth',
                'description' => 'Require special characters in password',
                'label' => null,
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'group' => 'auth',
                'description' => 'Maximum login attempts before lockout',
                'label' => null,
            ],
            [
                'key' => 'lockout_duration',
                'value' => '15',
                'type' => 'integer',
                'group' => 'auth',
                'description' => 'Account lockout duration in minutes',
                'label' => null,
            ],

            // ============================================
            // EMAIL SETTINGS
            // ============================================
            [
                'key' => 'mail_mailer',
                'value' => 'smtp',
                'type' => 'string',
                'group' => 'email',
                'description' => 'Mail driver (smtp, sendmail, mailgun, etc.)',
                'label' => null,
            ],
            [
                'key' => 'mail_host',
                'value' => 'smtp.mailtrap.io',
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP host address',
                'label' => null,
            ],
            [
                'key' => 'mail_port',
                'value' => '2525',
                'type' => 'integer',
                'group' => 'email',
                'description' => 'SMTP port',
                'label' => null,
            ],
            [
                'key' => 'mail_username',
                'value' => null,
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP username',
                'label' => null,
            ],
            [
                'key' => 'mail_password',
                'value' => null,
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP password (encrypted)',
                'label' => null,
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP encryption (tls, ssl)',
                'label' => null,
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@larakit.local',
                'type' => 'string',
                'group' => 'email',
                'description' => 'From email address',
                'label' => null,
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'LaraKit',
                'type' => 'string',
                'group' => 'email',
                'description' => 'From name',
                'label' => null,
            ],

            // ============================================
            // SOCIAL MEDIA SETTINGS
            // ============================================
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com',
                'type' => 'string',
                'group' => 'social',
                'description' => 'Facebook page URL',
                'label' => null,
            ],
            [
                'key' => 'twitter_url',
                'value' => 'https://twitter.com',
                'type' => 'string',
                'group' => 'social',
                'description' => 'Twitter profile URL',
                'label' => null,
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com',
                'type' => 'string',
                'group' => 'social',
                'description' => 'Instagram profile URL',
                'label' => null,
            ],
            [
                'key' => 'linkedin_url',
                'value' => 'https://linkedin.com',
                'type' => 'string',
                'group' => 'social',
                'description' => 'LinkedIn company URL',
                'label' => null,
            ],
            [
                'key' => 'youtube_url',
                'value' => 'https://youtube.com',
                'type' => 'string',
                'group' => 'social',
                'description' => 'YouTube channel URL',
                'label' => null,
            ],

            // ============================================
            // NOTIFICATION SETTINGS
            // ============================================
            [
                'key' => 'enable_email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable email notifications',
                'label' => null,
            ],
            [
                'key' => 'enable_push_notifications',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable push notifications',
                'label' => null,
            ],
            [
                'key' => 'enable_sms_notifications',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable SMS notifications',
                'label' => null,
            ],
            [
                'key' => 'notification_email',
                'value' => 'admin@larakit.local',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Email address for system notifications',
                'label' => null,
            ],

            // ============================================
            // BACKUP SETTINGS
            // ============================================
            [
                'key' => 'enable_auto_backup',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'backup',
                'description' => 'Enable automatic database backups',
                'label' => null,
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'group' => 'backup',
                'description' => 'Backup frequency (daily, weekly, monthly)',
                'label' => null,
            ],
            [
                'key' => 'backup_storage',
                'value' => 'local',
                'type' => 'string',
                'group' => 'backup',
                'description' => 'Backup storage location (local, s3, dropbox)',
                'label' => null,
            ],
            [
                'key' => 'backup_retention_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'backup',
                'description' => 'Number of days to keep backups',
                'label' => null,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'backup',
                'description' => 'Enable maintenance mode',
                'label' => null,
            ],

            // ============================================
            // DEVELOPER SETTINGS
            // ============================================
            [
                'key' => 'app_debug',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'developer',
                'description' => 'Enable debug mode - Never enable in production!',
                'label' => null,
            ],
            [
                'key' => 'app_url',
                'value' => config('app.url', 'http://127.0.0.1:8000'),
                'type' => 'string',
                'group' => 'developer',
                'description' => 'Application URL for generating links',
                'label' => null,
            ],
            [
                'key' => 'enable_api',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'developer',
                'description' => 'Enable API endpoints for external access',
                'label' => null,
            ],
            [
                'key' => 'api_rate_limit',
                'value' => '60',
                'type' => 'integer',
                'group' => 'developer',
                'description' => 'API rate limit (requests per minute)',
                'label' => null,
            ],
            [
                'key' => 'enable_webhooks',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'developer',
                'description' => 'Enable webhook notifications to external services',
                'label' => null,
            ],
            [
                'key' => 'webhook_url',
                'value' => '',
                'type' => 'string',
                'group' => 'developer',
                'description' => 'Webhook URL for notifications',
                'label' => null,
            ],
            [
                'key' => 'enable_analytics',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'developer',
                'description' => 'Enable Analytics Module',
                'label' => null,
            ],
            [
                'key' => 'enable_chat',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'developer',
                'description' => 'Enable Chat Module',
                'label' => null,
            ],
        ];

        // Insert settings using updateOrInsert to avoid duplicates
        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully!');
        $this->command->info('Total settings: ' . count($settings));
        $this->command->info('Settings are now available via setting() helper function.');
    }
}
