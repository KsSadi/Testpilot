<?php

namespace App\Modules\AI\Services;

use App\Modules\AI\Models\AIProvider;
use App\Modules\AI\Models\AISetting;
use Exception;

class AIProviderFactory
{
    /**
     * Get the active AI provider instance
     * 
     * @param string|null $providerName Optional provider name to override active provider
     * @return AIProviderBase
     * @throws Exception If no provider is configured or enabled
     */
    public static function make(?string $providerName = null): AIProviderBase
    {
        // Check if AI is globally enabled
        if (!AISetting::get('ai_enabled', false)) {
            throw new Exception('AI features are currently disabled');
        }

        // Get the provider
        if ($providerName) {
            $provider = AIProvider::where('name', $providerName)
                ->where('is_enabled', true)
                ->first();
        } else {
            $provider = AIProvider::getActive();
        }

        if (!$provider) {
            throw new Exception('No active AI provider configured');
        }

        if (!$provider->hasValidApiKey()) {
            throw new Exception("API key not configured for {$provider->display_name}");
        }

        // Instantiate the appropriate provider class
        return match($provider->name) {
            'openai' => new OpenAIProvider($provider),
            'gemini' => new GeminiProvider($provider),
            'deepseek' => new DeepSeekProvider($provider),
            default => throw new Exception("Unknown provider: {$provider->name}"),
        };
    }

    /**
     * Get provider with fallback support
     * Try the active provider, if it fails, try enabled providers by priority
     * 
     * @return AIProviderBase
     * @throws Exception If no providers are available
     */
    public static function makeWithFallback(): AIProviderBase
    {
        $providers = AIProvider::getEnabled();

        if ($providers->isEmpty()) {
            throw new Exception('No AI providers are enabled');
        }

        // Try each provider by priority
        $lastException = null;
        foreach ($providers as $provider) {
            try {
                if ($provider->hasValidApiKey()) {
                    return self::make($provider->name);
                }
            } catch (Exception $e) {
                $lastException = $e;
                continue;
            }
        }

        throw new Exception('No working AI provider available: ' . ($lastException?->getMessage() ?? 'Unknown error'));
    }

    /**
     * Test all enabled providers
     * 
     * @return array Test results for each provider
     */
    public static function testAllProviders(): array
    {
        $providers = AIProvider::getEnabled();
        $results = [];

        foreach ($providers as $provider) {
            try {
                $instance = match($provider->name) {
                    'openai' => new OpenAIProvider($provider),
                    'gemini' => new GeminiProvider($provider),
                    'deepseek' => new DeepSeekProvider($provider),
                    default => null,
                };

                if ($instance) {
                    $results[$provider->name] = $instance->testConnection();
                }
            } catch (Exception $e) {
                $results[$provider->name] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
