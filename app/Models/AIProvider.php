<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Encrypted;

class AIProvider extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'api_key',
        'models',
        'default_model',
        'is_active',
        'is_enabled',
        'settings',
        'priority',
    ];

    protected $casts = [
        'models' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_enabled' => 'boolean',
        'priority' => 'integer',
        'api_key' => Encrypted::class,
    ];

    /**
     * Get the active AI provider
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->where('is_enabled', true)
            ->first();
    }

    /**
     * Get enabled providers ordered by priority
     */
    public static function getEnabled()
    {
        return self::where('is_enabled', true)
            ->orderBy('priority')
            ->get();
    }

    /**
     * Set this provider as the active one
     */
    public function setAsActive()
    {
        // Deactivate all other providers
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activate this provider
        $this->update(['is_active' => true]);
        
        // Update active provider setting
        AISetting::updateOrCreate(
            ['key' => 'active_provider'],
            ['value' => $this->name]
        );
    }

    /**
     * Check if this provider has a valid API key
     */
    public function hasValidApiKey()
    {
        return !empty($this->api_key);
    }

    /**
     * Get usage logs for this provider
     */
    public function usageLogs()
    {
        return $this->hasMany(AIUsageLog::class, 'provider', 'name');
    }
}
