<?php

namespace App\Modules\AI\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GeminiProvider extends AIProviderBase
{
    private const API_BASE_URL = 'https://generativelanguage.googleapis.com/v1beta';

    public function sendRequest(string $prompt, string $feature = 'general', array $options = []): array
    {
        $startTime = microtime(true);
        $maxRetries = $this->provider->api_keys ? count($this->provider->api_keys) : 1;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $this->validateConfig();

                $systemPrompt = $options['system_prompt'] ?? 'You are a helpful assistant for automated testing.';
                $combinedPrompt = $systemPrompt . "\n\n" . $prompt;

                $baseUrl = $this->getApiBaseUrl(self::API_BASE_URL);
                $apiKey = $this->getApiKey();

                $response = Http::timeout(60)->post(
                    $baseUrl . '/models/' . $this->getModel() . ':generateContent?key=' . $apiKey,
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $combinedPrompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => $options['temperature'] ?? $this->getTemperature(),
                            'maxOutputTokens' => $options['max_tokens'] ?? $this->getMaxTokensSetting(),
                            'topP' => $options['top_p'] ?? 1.0,
                            'topK' => $options['top_k'] ?? 40,
                        ],
                    ]
                );

                $responseTime = (int)((microtime(true) - $startTime) * 1000);

                if (!$response->successful()) {
                    $errorBody = $response->body();
                    
                    // Check if error is quota/rate limit related
                    if ($this->isQuotaError($errorBody) && $this->handleApiKeyFailover()) {
                        $attempt++;
                        continue; // Retry with next API key
                    }
                    
                    throw new Exception('Gemini API error: ' . $errorBody);
                }

                $data = $response->json();
                
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Gemini provides token counts in usageMetadata
                $tokenData = [
                    'prompt_tokens' => $data['usageMetadata']['promptTokenCount'] ?? 0,
                    'completion_tokens' => $data['usageMetadata']['candidatesTokenCount'] ?? 0,
                    'total_tokens' => $data['usageMetadata']['totalTokenCount'] ?? 0,
                ];

                $cost = $this->estimateCost($tokenData['prompt_tokens'], $tokenData['completion_tokens']);

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
                        'model' => $this->getModel(),
                        'finish_reason' => $data['candidates'][0]['finishReason'] ?? null,
                        'api_key_index' => $this->provider->current_key_index,
                    ]
                );

                // Reset to first key on success
                if ($attempt > 0) {
                    $this->provider->resetKeyIndex();
                }

                return [
                    'success' => true,
                    'content' => $content,
                    'tokens' => $tokenData,
                    'cost' => $cost,
                    'response_time' => $responseTime,
                    'provider' => 'gemini',
                ];

            } catch (Exception $e) {
                // If we have more keys to try, continue
                if ($attempt < $maxRetries - 1 && $this->handleApiKeyFailover()) {
                    $attempt++;
                    continue;
                }
                
                // No more keys or final attempt failed
                $responseTime = (int)((microtime(true) - $startTime) * 1000);
                return $this->handleError($e, $prompt, $feature, $responseTime);
            }
        }

        // Should not reach here, but just in case
        $responseTime = (int)((microtime(true) - $startTime) * 1000);
        return $this->handleError(new Exception('All API keys exhausted'), $prompt, $feature, $responseTime);
    }

    public function validateConfig(): bool
    {
        $apiKey = $this->provider->getCurrentApiKey();
        
        if (empty($apiKey)) {
            throw new Exception('Gemini API key is not configured');
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
            // Use gemini-2.5-flash for testing as it's stable and available
            $testModel = 'gemini-2.5-flash';
            $baseUrl = $this->getApiBaseUrl(self::API_BASE_URL);
            $apiKey = $this->getApiKey();
            
            // Test with a simple request with longer timeout
            $response = Http::timeout(30)->post(
                $baseUrl . '/models/' . $testModel . ':generateContent?key=' . $apiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Say hi'],
                            ],
                        ],
                    ],
                ]
            );

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to Gemini API',
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
            'gemini-3-pro-preview' => 2097152,   // 2M context window
            'gemini-2.5-pro' => 2097152,          // 2M context window
            'gemini-2.5-flash' => 1048576,        // 1M context window
            'gemini-2.5-flash-lite' => 1048576,   // 1M context window
            'gemini-2.0-flash' => 1048576,        // 1M context window
            'gemini-2.0-flash-lite' => 1048576,   // 1M context window
            default => 1048576,
        };
    }

    /**
     * Check if error is quota/rate limit related
     */
    private function isQuotaError(string $errorBody): bool
    {
        $quotaErrors = [
            'RESOURCE_EXHAUSTED',
            'quota',
            'rate limit',
            'too many requests',
            '429',
        ];
        
        foreach ($quotaErrors as $error) {
            if (stripos($errorBody, $error) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
