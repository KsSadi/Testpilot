<?php

namespace App\Modules\AI\Services;

use App\Modules\AI\Models\AIProvider;
use App\Modules\AI\Models\AIUsageLog;
use Illuminate\Support\Facades\Auth;
use Exception;

abstract class AIProviderBase
{
    protected AIProvider $provider;
    protected array $config;

    public function __construct(AIProvider $provider)
    {
        $this->provider = $provider;
        $this->config = $provider->settings ?? [];
    }

    /**
     * Send a request to the AI provider
     * 
     * @param string $prompt The prompt to send
     * @param string $feature The feature using AI (e.g., 'test_generation', 'code_optimization')
     * @param array $options Additional options for the request
     * @return array Response data including content, tokens, cost
     */
    abstract public function sendRequest(string $prompt, string $feature = 'general', array $options = []): array;

    /**
     * Validate the provider configuration
     * 
     * @return bool True if configuration is valid
     * @throws Exception If configuration is invalid
     */
    abstract public function validateConfig(): bool;

    /**
     * Test the connection to the AI provider
     * 
     * @return array Test result with status and message
     */
    abstract public function testConnection(): array;

    /**
     * Estimate the cost of a request
     * 
     * @param int $promptTokens Number of tokens in the prompt
     * @param int $completionTokens Number of tokens in the completion
     * @return float Estimated cost in USD
     */
    abstract public function estimateCost(int $promptTokens, int $completionTokens): float;

    /**
     * Get the maximum tokens allowed for this provider/model
     * 
     * @return int Maximum tokens
     */
    abstract public function getMaxTokens(): int;

    /**
     * Log the AI usage
     * 
     * @param string $prompt The prompt sent
     * @param string $response The response received
     * @param string $feature The feature that used AI
     * @param array $tokenData Token usage data
     * @param float $cost Estimated cost
     * @param int $responseTime Response time in milliseconds
     * @param string $status Request status (success, error, timeout)
     * @param string|null $errorMessage Error message if failed
     * @param array $metadata Additional metadata
     * @return AIUsageLog
     */
    protected function logUsage(
        string $prompt,
        string $response,
        string $feature,
        array $tokenData,
        float $cost,
        int $responseTime,
        string $status = 'success',
        ?string $errorMessage = null,
        array $metadata = []
    ): AIUsageLog {
        return AIUsageLog::create([
            'user_id' => Auth::id(),
            'provider' => $this->provider->name,
            'model' => $this->getModel(),
            'feature' => $feature,
            'prompt' => $prompt,
            'response' => $response,
            'prompt_tokens' => $tokenData['prompt_tokens'] ?? 0,
            'completion_tokens' => $tokenData['completion_tokens'] ?? 0,
            'total_tokens' => $tokenData['total_tokens'] ?? 0,
            'estimated_cost' => $cost,
            'response_time_ms' => $responseTime,
            'status' => $status,
            'error_message' => $errorMessage,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get the current model being used
     * 
     * @return string Model name
     */
    protected function getModel(): string
    {
        return $this->config['model'] ?? $this->provider->default_model;
    }

    /**
     * Get API key (with automatic failover support)
     * 
     * @return string API key
     * @throws Exception If API key is not configured
     */
    protected function getApiKey(): string
    {
        $apiKey = $this->provider->getCurrentApiKey();
        
        if (empty($apiKey)) {
            throw new Exception("API key not configured for {$this->provider->display_name}");
        }
        
        return $apiKey;
    }

    /**
     * Handle API key failover when request fails
     * Automatically rotates to next API key if available
     * 
     * @return bool True if rotated to next key, false if no more keys
     */
    protected function handleApiKeyFailover(): bool
    {
        return $this->provider->rotateToNextKey();
    }

    /**
     * Get API base URL (dynamic from database or fallback to default)
     * 
     * @param string $defaultUrl Default URL if not configured
     * @return string API base URL
     */
    protected function getApiBaseUrl(string $defaultUrl): string
    {
        return $this->provider->getApiBaseUrl($defaultUrl);
    }

    /**
     * Get temperature setting
     * 
     * @return float Temperature value
     */
    protected function getTemperature(): float
    {
        return $this->config['temperature'] ?? 0.7;
    }

    /**
     * Get max tokens setting
     * 
     * @return int Max tokens
     */
    protected function getMaxTokensSetting(): int
    {
        return $this->config['max_tokens'] ?? 4000;
    }

    /**
     * Handle API errors and return standardized response
     * 
     * @param Exception $e The exception
     * @param string $prompt Original prompt
     * @param string $feature Feature that was being used
     * @param int $responseTime Response time before error
     * @return array Error response
     */
    protected function handleError(Exception $e, string $prompt, string $feature, int $responseTime): array
    {
        $this->logUsage(
            $prompt,
            '',
            $feature,
            ['prompt_tokens' => 0, 'completion_tokens' => 0, 'total_tokens' => 0],
            0,
            $responseTime,
            'error',
            $e->getMessage()
        );

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'provider' => $this->provider->name,
        ];
    }
}
