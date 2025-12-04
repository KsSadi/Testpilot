<?php

namespace App\Modules\AI\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class DeepSeekProvider extends AIProviderBase
{
    private const API_BASE_URL = 'https://api.deepseek.com';

    public function sendRequest(string $prompt, string $feature = 'general', array $options = []): array
    {
        $startTime = microtime(true);

        try {
            $this->validateConfig();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getApiKey(),
                'Content-Type' => 'application/json',
            ])->timeout(60)->post(self::API_BASE_URL . '/chat/completions', [
                'model' => $this->getModel(),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $options['system_prompt'] ?? 'You are a helpful assistant for automated testing and code generation.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => $options['temperature'] ?? $this->getTemperature(),
                'max_tokens' => $options['max_tokens'] ?? $this->getMaxTokensSetting(),
                'top_p' => $options['top_p'] ?? 1.0,
                'stream' => false,
            ]);

            $responseTime = (int)((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                throw new Exception('DeepSeek API error: ' . $response->body());
            }

            $data = $response->json();
            
            $tokenData = [
                'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                'total_tokens' => $data['usage']['total_tokens'] ?? 0,
            ];

            $cost = $this->estimateCost($tokenData['prompt_tokens'], $tokenData['completion_tokens']);
            $content = $data['choices'][0]['message']['content'] ?? '';

            $this->logUsage(
                $prompt,
                $content,
                $feature,
                $tokenData,
                $cost,
                $responseTime,
                'success',
                null,
                [
                    'model' => $data['model'] ?? $this->getModel(),
                    'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
                ]
            );

            return [
                'success' => true,
                'content' => $content,
                'tokens' => $tokenData,
                'cost' => $cost,
                'response_time' => $responseTime,
                'provider' => 'deepseek',
            ];

        } catch (Exception $e) {
            $responseTime = (int)((microtime(true) - $startTime) * 1000);
            return $this->handleError($e, $prompt, $feature, $responseTime);
        }
    }

    public function validateConfig(): bool
    {
        $apiKey = $this->provider->getCurrentApiKey();
        
        if (empty($apiKey)) {
            throw new Exception('DeepSeek API key is not configured');
        }

        $model = $this->getModel();
        $availableModels = array_keys($this->provider->models ?? []);
        
        if (!in_array($model, $availableModels)) {
            throw new Exception("Invalid model: {$model}");
        }

        return true;
    }

    public function testConnection(): array
    {
        try {
            // Test with a minimal request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getApiKey(),
                'Content-Type' => 'application/json',
            ])->timeout(10)->post(self::API_BASE_URL . '/chat/completions', [
                'model' => $this->getModel(),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Hello',
                    ],
                ],
                'max_tokens' => 10,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to DeepSeek API',
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to connect: ' . $response->body(),
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }

    public function estimateCost(int $promptTokens, int $completionTokens): float
    {
        $model = $this->getModel();
        $pricing = $this->provider->getPricing($model);

        $inputCost = ($promptTokens / 1_000_000) * $pricing['input'];
        $outputCost = ($completionTokens / 1_000_000) * $pricing['output'];

        return $inputCost + $outputCost;
    }

    public function getMaxTokens(): int
    {
        return match($this->getModel()) {
            'deepseek-chat' => 131072,     // 128K context length
            'deepseek-reasoner' => 131072, // 128K context length
            default => 131072,
        };
    }
}
