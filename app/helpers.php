<?php

use App\Modules\Setting\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('settings_group')) {
    /**
     * Get all settings in a group
     *
     * @param string $group
     * @return \Illuminate\Support\Collection
     */
    function settings_group($group)
    {
        return Setting::getByGroup($group);
    }
}

if (!function_exists('app_name')) {
    /**
     * Get application name from settings
     *
     * @return string
     */
    function app_name()
    {
        return Setting::get('app_name', config('app.name', 'Dashboard'));
    }
}

if (!function_exists('app_logo')) {
    /**
     * Get application logo URL
     *
     * @return string|null
     */
    function app_logo()
    {
        $logo = Setting::get('logo');
        return $logo ? Storage::url($logo) : null;
    }
}

if (!function_exists('app_favicon')) {
    /**
     * Get application favicon URL
     *
     * @return string|null
     */
    function app_favicon()
    {
        $favicon = Setting::get('favicon');
        return $favicon ? Storage::url($favicon) : null;
    }
}
