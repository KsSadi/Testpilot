<?php

namespace App\Modules\AI\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * AI Facade for simplified AI interactions
 * 
 * @method static array ask(string $prompt, array $options = [])
 * @method static array chat(array $messages, array $options = [])
 * @method static array generateCode(string $prompt, string $language = 'php', array $options = [])
 * @method static array analyze(string $content, string $type = 'general', array $options = [])
 * @method static \App\Modules\AI\Services\AIService withProvider(string $provider)
 * @method static \App\Modules\AI\Services\AIService withModel(string $model)
 * @method static \App\Modules\AI\Services\AIService withTemperature(float $temperature)
 * @method static \App\Modules\AI\Services\AIService withMaxTokens(int $tokens)
 * 
 * @see \App\Modules\AI\Services\AIService
 */
class AI extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ai';
    }
}
