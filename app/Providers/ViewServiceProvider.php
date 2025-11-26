<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Modules\Setting\Models\Setting;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Skip if running migrations or if settings table doesn't exist
        if ($this->app->runningInConsole() && $this->isRunningMigrations()) {
            return;
        }

        // Check if settings table exists before querying
        if (!$this->settingsTableExists()) {
            return;
        }

        // Set application timezone from settings
        $appTimezone = Setting::get('timezone', config('app.timezone', 'UTC'));
        if ($appTimezone) {
            config(['app.timezone' => $appTimezone]);
            date_default_timezone_set($appTimezone);
        }
        
        // Share settings data with all views
        View::composer('*', function ($view) use ($appTimezone) {
            $settings = Setting::getAllCached();
            
            // Share individual setting groups
            $view->with('generalSettings', $settings['general'] ?? collect());
            $view->with('seoSettings', $settings['seo'] ?? collect());
            $view->with('authSettings', $settings['auth'] ?? collect());
            $view->with('emailSettings', $settings['email'] ?? collect());
            $view->with('socialSettings', $settings['social'] ?? collect());
            $view->with('notificationSettings', $settings['notifications'] ?? collect());
            $view->with('backupSettings', $settings['backup'] ?? collect());
            $view->with('developerSettings', $settings['developer'] ?? collect());
            
            // Share commonly used settings as helper variables
            $view->with('appName', Setting::get('app_name', config('app.name')));
            $view->with('appTagline', Setting::get('app_tagline', 'Analytics Hub'));
            $view->with('appLogo', Setting::get('logo', null));
            $view->with('appFavicon', Setting::get('favicon', null));
            $view->with('footerText', Setting::get('footer_text', ''));
            $view->with('copyrightText', Setting::get('copyright_text', '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.'));
            $view->with('currentTimezone', $appTimezone);
            $view->with('dateFormat', Setting::get('date_format', 'Y-m-d'));
            $view->with('timeFormat', Setting::get('time_format', 'H:i:s'));
        });
    }

    /**
     * Check if currently running migrations
     */
    protected function isRunningMigrations(): bool
    {
        return in_array(request()->server('argv')[1] ?? null, ['migrate', 'migrate:fresh', 'migrate:refresh', 'migrate:reset', 'migrate:rollback']);
    }

    /**
     * Check if settings table exists
     */
    protected function settingsTableExists(): bool
    {
        try {
            return \Schema::hasTable('settings');
        } catch (\Exception $e) {
            return false;
        }
    }
}
