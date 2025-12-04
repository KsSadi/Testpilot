<?php

namespace App\Modules\AI\Models;

use Illuminate\Database\Eloquent\Model;

class AISetting extends Model
{
    protected $table = 'ai_settings';
    
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get the typed value of the setting
     */
    public function getTypedValueAttribute()
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return $setting->typed_value;
    }

    /**
     * Set a setting value by key
     */
    public static function set($key, $value, $type = 'string')
    {
        // Convert value to string based on type
        $stringValue = match($type) {
            'boolean' => $value ? 'true' : 'false',
            'json' => json_encode($value),
            default => (string) $value,
        };
        
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
            ]
        );
    }

    /**
     * Get settings by group
     */
    public static function getByGroup($group)
    {
        return self::where('group', $group)->get();
    }
}
