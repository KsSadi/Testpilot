<?php

namespace App\Modules\AI\Models;

use Illuminate\Database\Eloquent\Model;

class AIProvider extends Model
{
    protected $table = 'ai_providers';
    
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'api_base_url',
        'api_key',
        'api_keys',
        'current_key_index',
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
        'api_keys' => 'array',
        'is_active' => 'boolean',
        'is_enabled' => 'boolean',
        'priority' => 'integer',
        'current_key_index' => 'integer',
        'api_key' => 'encrypted',
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
        // Check if we have either single key or multiple keys
        return !empty($this->api_key) || (!empty($this->api_keys) && count($this->api_keys) > 0);
    }

    /**
     * Get the current API key (with failover support)
     */
    public function getCurrentApiKey()
    {
        // If using multiple API keys
        if (!empty($this->api_keys) && count($this->api_keys) > 0) {
            $index = $this->current_key_index % count($this->api_keys);
            return $this->api_keys[$index];
        }
        
        // Fallback to single API key
        return $this->api_key;
    }

    /**
     * Rotate to next API key (for failover)
     */
    public function rotateToNextKey()
    {
        if (!empty($this->api_keys) && count($this->api_keys) > 1) {
            $this->increment('current_key_index');
            return true;
        }
        return false;
    }

    /**
     * Reset to first API key
     */
    public function resetKeyIndex()
    {
        $this->update(['current_key_index' => 0]);
    }

    /**
     * Get API base URL (dynamic or default)
     */
    public function getApiBaseUrl($default = null)
    {
        return $this->api_base_url ?: $default;
    }

    /**
     * Get usage logs for this provider
     */
    public function usageLogs()
    {
        return $this->hasMany(AIUsageLog::class, 'provider', 'name');
    }

    /**
     * Get pricing for a specific model
     */
    public function getPricing($model)
    {
        $pricing = AISetting::where('provider_id', $this->id)
            ->where('key', 'model_pricing')
            ->where('value', $model)
            ->first();

        if ($pricing) {
            return [
                'input' => (float) $pricing->input_price,
                'output' => (float) $pricing->output_price,
            ];
        }

        // Return default pricing if not found
        return ['input' => 0.0, 'output' => 0.0];
    }

    /**
     * Get all pricing data for this provider
     */
    public function getAllPricing()
    {
        return AISetting::where('provider_id', $this->id)
            ->where('key', 'model_pricing')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->value => [
                        'input' => (float) $item->input_price,
                        'output' => (float) $item->output_price,
                    ]
                ];
            })
            ->toArray();
    }

    /**
     * Update pricing for a specific model
     */
    public function updatePricing($model, $inputPrice, $outputPrice)
    {
        return AISetting::updateOrCreate(
            [
                'provider_id' => $this->id,
                'key' => 'model_pricing',
                'value' => $model,
            ],
            [
                'input_price' => $inputPrice,
                'output_price' => $outputPrice,
                'type' => 'pricing',
                'description' => "Pricing for {$model}",
                'group' => 'pricing',
                'is_public' => false,
            ]
        );
    }

    /**
     * Relationship to pricing settings
     */
    public function pricingSettings()
    {
        return $this->hasMany(AISetting::class, 'provider_id')
            ->where('key', 'model_pricing');
    }
}
