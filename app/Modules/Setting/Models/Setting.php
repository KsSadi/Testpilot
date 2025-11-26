<?php

namespace App\Modules\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_active',
        'is_encrypted',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_encrypted' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Cache key prefix
     */
    const CACHE_PREFIX = 'setting_';
    const CACHE_ALL_KEY = 'settings_all';
    const CACHE_DURATION = 86400; // 24 hours

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are modified
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $cacheKey = self::CACHE_PREFIX . $key;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($key, $default) {
            $setting = self::where('key', $key)
                ->where('is_active', true)
                ->first();

            if (!$setting) {
                return $default;
            }

            return self::parseValue($setting);
        });
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @return Setting
     */
    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => self::encodeValue($value, $type),
                'type' => $type,
                'group' => $group,
            ]
        );

        self::clearCache($key);

        return $setting;
    }

    /**
     * Get all settings by group
     *
     * @param string|null $group
     * @return \Illuminate\Support\Collection
     */
    public static function getByGroup($group = null)
    {
        $cacheKey = self::CACHE_PREFIX . 'group_' . ($group ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($group) {
            $query = self::where('is_active', true)->orderBy('order');

            if ($group) {
                $query->where('group', $group);
            }

            return $query->get()->mapWithKeys(function ($setting) {
                return [$setting->key => self::parseValue($setting)];
            });
        });
    }

    /**
     * Get all settings cached
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAllCached()
    {
        return Cache::remember(self::CACHE_ALL_KEY, self::CACHE_DURATION, function () {
            return self::where('is_active', true)
                ->orderBy('group')
                ->orderBy('order')
                ->get()
                ->groupBy('group')
                ->map(function ($groupSettings) {
                    return $groupSettings->mapWithKeys(function ($setting) {
                        return [$setting->key => self::parseValue($setting)];
                    });
                });
        });
    }

    /**
     * Parse setting value based on type
     *
     * @param Setting $setting
     * @return mixed
     */
    protected static function parseValue($setting)
    {
        $value = $setting->value;

        // Decrypt if needed
        if ($setting->is_encrypted && $value) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }

        // Convert based on type
        switch ($setting->type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
            case 'json':
                return json_decode($value, true) ?? [];
            default:
                return $value;
        }
    }

    /**
     * Encode value for storage
     *
     * @param mixed $value
     * @param string $type
     * @return string|null
     */
    protected static function encodeValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'array':
            case 'json':
                return json_encode($value);
            default:
                return $value;
        }
    }

    /**
     * Clear all settings cache
     *
     * @param string|null $key
     * @return void
     */
    public static function clearCache($key = null)
    {
        if ($key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }

        // Clear all settings caches
        Cache::forget(self::CACHE_ALL_KEY);
        
        // Clear group caches
        $groups = ['general', 'seo', 'auth', 'email', 'social', 'notifications', 'backup', 'developer'];
        foreach ($groups as $group) {
            Cache::forget(self::CACHE_PREFIX . 'group_' . $group);
        }
    }

    /**
     * Refresh cache for all settings
     *
     * @return void
     */
    public static function refreshCache()
    {
        self::clearCache();
        self::getAllCached();
    }

    /**
     * Check if a setting exists
     *
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Delete a setting by key
     *
     * @param string $key
     * @return bool
     */
    public static function remove($key)
    {
        self::clearCache($key);
        return self::where('key', $key)->delete();
    }

    /**
     * Scope to get active settings only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get settings by group
     */
    public function scopeOfGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}
