<?php

namespace App\Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Setting\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class SettingController
{
    /**
     * Display settings page with tabs
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'general');
        
        // Get settings by group
        $generalSettings = Setting::ofGroup('general')->get();
        $seoSettings = Setting::ofGroup('seo')->get();
        $authSettings = Setting::ofGroup('auth')->get();
        $emailSettings = Setting::ofGroup('email')->get();
        $socialSettings = Setting::ofGroup('social')->get();
        $notificationSettings = Setting::ofGroup('notifications')->get();
        $backupSettings = Setting::ofGroup('backup')->get();
        $developerSettings = Setting::ofGroup('developer')->get();
        
        return view('Setting::index', compact(
            'activeTab',
            'generalSettings',
            'seoSettings',
            'authSettings',
            'emailSettings',
            'socialSettings',
            'notificationSettings',
            'backupSettings',
            'developerSettings'
        ));
    }

    /**
     * Update general settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_tagline' => 'nullable|string|max:255',
            'app_description' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'copyright_text' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'timezone' => 'nullable|string|timezone',
            'date_format' => 'nullable|string|max:50',
            'time_format' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:1024',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'logo' && $request->hasFile('logo')) {
                $path = $request->file('logo')->store('settings', 'public');
                Setting::set('site_logo', $path, 'string', 'general');
            } elseif ($key === 'favicon' && $request->hasFile('favicon')) {
                $path = $request->file('favicon')->store('settings', 'public');
                Setting::set('site_favicon', $path, 'string', 'general');
            } else {
                Setting::set($key, $value, 'string', 'general');
            }
        }

        return redirect()->route('settings.index', ['tab' => 'general'])
            ->with('success', 'General settings updated successfully!');
    }

    /**
     * Update SEO settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'twitter_card' => 'nullable|string',
            'twitter_site' => 'nullable|string',
            'google_analytics_id' => 'nullable|string',
            'google_site_verification' => 'nullable|string',
            'robots_txt' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'og_image' && $request->hasFile('og_image')) {
                $path = $request->file('og_image')->store('settings/seo', 'public');
                Setting::set($key, $path, 'string', 'seo');
            } else {
                Setting::set($key, $value, 'string', 'seo');
            }
        }

        return redirect()->route('settings.index', ['tab' => 'seo'])
            ->with('success', 'SEO settings updated successfully!');
    }

    /**
     * Update authentication methods (auth_settings table)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAuthMethods(Request $request)
    {
        $validated = $request->validate([
            // Email Authentication
            'email_login_enabled' => 'nullable|boolean',
            'email_registration_enabled' => 'nullable|boolean',
            'email_verification_required' => 'nullable|boolean',
            
            // Mobile/OTP Authentication
            'mobile_login_enabled' => 'nullable|boolean',
            'mobile_registration_enabled' => 'nullable|boolean',
            'mobile_verification_required' => 'nullable|boolean',
            'otp_length' => 'nullable|in:4,6',
            'otp_expiry_minutes' => 'nullable|integer|min:1|max:30',
            'otp_resend_cooldown_seconds' => 'nullable|integer|min:30|max:300',
            
            // Social Authentication
            'social_login_enabled' => 'nullable|boolean',
            'google_login_enabled' => 'nullable|boolean',
            'facebook_login_enabled' => 'nullable|boolean',
            'github_login_enabled' => 'nullable|boolean',
            
            // General
            'allow_registration' => 'nullable|boolean',
            'default_user_role' => 'nullable|string|max:50',
        ]);

        // Update auth_settings table
        foreach ($validated as $key => $value) {
            \App\Modules\User\Models\AuthSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value ?? '0',
                    'is_active' => true,
                    'group' => $this->getAuthGroup($key),
                    'description' => $this->getAuthDescription($key),
                ]
            );
        }

        // Clear cache
        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('settings.index', ['tab' => 'auth'])
            ->with('success', 'Authentication methods updated successfully!');
    }

    /**
     * Get group for auth setting key
     */
    private function getAuthGroup(string $key): string
    {
        if (str_contains($key, 'email')) return 'email';
        if (str_contains($key, 'mobile') || str_contains($key, 'otp')) return 'mobile';
        if (str_contains($key, 'social') || str_contains($key, '_login_enabled')) return 'social';
        return 'general';
    }

    /**
     * Get description for auth setting key
     */
    private function getAuthDescription(string $key): string
    {
        $descriptions = [
            'email_login_enabled' => 'Enable email-based login',
            'email_registration_enabled' => 'Enable email-based registration',
            'email_verification_required' => 'Require email verification after registration',
            'mobile_login_enabled' => 'Enable mobile-based login with OTP',
            'mobile_registration_enabled' => 'Enable mobile-based registration',
            'mobile_verification_required' => 'Require mobile verification after registration',
            'otp_length' => 'Length of OTP code (4 or 6 digits)',
            'otp_expiry_minutes' => 'OTP expiry time in minutes',
            'otp_resend_cooldown_seconds' => 'Cooldown time before OTP can be resent',
            'social_login_enabled' => 'Enable social login',
            'google_login_enabled' => 'Enable Google OAuth login',
            'facebook_login_enabled' => 'Enable Facebook OAuth login',
            'github_login_enabled' => 'Enable GitHub OAuth login',
            'allow_registration' => 'Allow new user registration',
            'default_user_role' => 'Default role assigned to new users',
        ];

        return $descriptions[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Update authentication & security settings (settings table)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAuth(Request $request)
    {
        $validated = $request->validate([
            'allow_registration' => 'nullable|boolean',
            'require_email_verification' => 'nullable|boolean',
            'require_mobile_verification' => 'nullable|boolean',
            'enable_2fa' => 'nullable|boolean',
            'session_lifetime' => 'nullable|integer|min:1|max:10080',
            'password_min_length' => 'nullable|integer|min:6|max:32',
            'password_require_uppercase' => 'nullable|boolean',
            'password_require_numbers' => 'nullable|boolean',
            'password_require_special' => 'nullable|boolean',
            'max_login_attempts' => 'nullable|integer|min:1|max:10',
            'lockout_duration' => 'nullable|integer|min:1|max:1440',
        ]);

        // Auth settings that belong to auth_settings table
        $authSettingsKeys = ['allow_registration', 'require_email_verification', 'require_mobile_verification'];
        
        foreach ($authSettingsKeys as $key) {
            $value = $request->has($key) ? '1' : '0';
            \App\Modules\User\Models\AuthSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'is_active' => true,
                    'group' => $this->getAuthGroup($key),
                    'description' => $this->getAuthDescription($key),
                ]
            );
        }

        // Other settings go to settings table
        foreach ($validated as $key => $value) {
            if (!in_array($key, $authSettingsKeys)) {
                $type = is_bool($value) || in_array($key, ['enable_2fa', 'password_require_uppercase', 'password_require_numbers', 'password_require_special']) 
                    ? 'boolean' 
                    : 'integer';
                Setting::set($key, $value, $type, 'auth');
            }
        }

        \Illuminate\Support\Facades\Cache::flush();
        \App\Modules\User\Models\AuthSetting::clearCache();

        return redirect()->route('settings.index', ['tab' => 'security'])
            ->with('success', 'Authentication & Security settings updated successfully!');
    }

    /**
     * Update email settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            $isEncrypted = $key === 'mail_password';
            $type = $key === 'mail_port' ? 'integer' : 'string';
            
            if ($isEncrypted && $value) {
                $value = Crypt::encryptString($value);
            }
            
            $setting = Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => $type,
                    'group' => 'email',
                    'is_encrypted' => $isEncrypted,
                ]
            );
        }

        Setting::clearCache();

        return redirect()->route('settings.index', ['tab' => 'email'])
            ->with('success', 'Email settings updated successfully!');
    }

    /**
     * Update social media settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSocial(Request $request)
    {
        $validated = $request->validate([
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'github_url' => 'nullable|url',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'string', 'social');
        }

        return redirect()->route('settings.index', ['tab' => 'social'])
            ->with('success', 'Social media settings updated successfully!');
    }

    /**
     * Update notification settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'enable_email_notifications' => 'nullable|boolean',
            'enable_push_notifications' => 'nullable|boolean',
            'enable_sms_notifications' => 'nullable|boolean',
            'notify_on_new_user' => 'nullable|boolean',
            'notify_on_new_order' => 'nullable|boolean',
            'notify_on_payment' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'boolean', 'notifications');
        }

        return redirect()->route('settings.index', ['tab' => 'notifications'])
            ->with('success', 'Notification settings updated successfully!');
    }

    /**
     * Update backup & system settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBackup(Request $request)
    {
        $validated = $request->validate([
            'enable_auto_backup' => 'nullable|boolean',
            'backup_frequency' => 'nullable|string|in:daily,weekly,monthly',
            'backup_storage' => 'nullable|string|in:local,s3,dropbox,google',
            'backup_retention_days' => 'nullable|integer|min:1|max:365',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            $type = in_array($key, ['enable_auto_backup', 'maintenance_mode']) ? 'boolean' : 
                   ($key === 'backup_retention_days' ? 'integer' : 'string');
            Setting::set($key, $value, $type, 'backup');
        }

        return redirect()->route('settings.index', ['tab' => 'backup'])
            ->with('success', 'Backup & System settings updated successfully!');
    }

    /**
     * Update developer options
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDeveloper(Request $request)
    {
        $validated = $request->validate([
            'app_debug' => 'nullable|boolean',
            'app_url' => 'required|url',
            'api_rate_limit' => 'nullable|integer',
            'enable_api' => 'nullable|boolean',
            'enable_webhooks' => 'nullable|boolean',
            'webhook_url' => 'nullable|url',
            'enable_analytics' => 'nullable|boolean',
            'enable_chat' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            $type = is_bool($value) || in_array($key, ['app_debug', 'enable_api', 'enable_webhooks', 'enable_analytics', 'enable_chat'])
                ? 'boolean'
                : ($key === 'api_rate_limit' ? 'integer' : 'string');
            Setting::set($key, $value, $type, 'developer');
        }

        return redirect()->route('settings.index', ['tab' => 'developer'])
            ->with('success', 'Developer options updated successfully!');
    }

    /**
     * Clear all settings cache
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCache()
    {
        Setting::clearCache();
        Setting::refreshCache();

        return redirect()->route('settings.index')
            ->with('success', 'Settings cache cleared successfully!');
    }
}
