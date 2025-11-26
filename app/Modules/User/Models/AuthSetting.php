<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AuthSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'is_active',
        'group',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("auth_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->where('is_active', true)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Get a boolean setting value.
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        return (bool) self::get($key, $default);
    }

    /**
     * Get a JSON decoded setting value.
     */
    public static function getJson(string $key, $default = [])
    {
        $value = self::get($key);
        return $value ? json_decode($value, true) : $default;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, $value): bool
    {
        Cache::forget("auth_setting_{$key}");
        
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value]
        ) ? true : false;
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }
}
