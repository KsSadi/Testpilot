<?php

namespace App\Modules\AI\Services;

use App\Modules\AI\Models\AIProvider;
use Exception;

/**
 * Simplified AI Service
 * Industry-standard approach for AI interactions
 */
class AIService
{
    protected ?string $provider = null;
    protected ?string $model = null;
    protected ?float $temperature = null;
    protected ?int $maxTokens = null;
    protected array $metadata = [];

    /**
     * Simple ask method - most common use case
     * 
     * @param string $prompt The question/prompt
     * @param array $options Additional options (system_prompt, temperature, etc.)
     * @return array Response with content, tokens, cost, etc.
     */
    public function ask(string $prompt, array $options = []): array
    {
        return $this->generate($prompt, 'general', $options);
    }

    /**
     * Chat with conversation history
     * 
     * @param array $messages Array of ['role' => 'user/assistant', 'content' => '...']
     * @param array $options Additional options
     * @return array Response
     */
    public function chat(array $messages, array $options = []): array
    {
        // Convert messages to a single prompt for now
        // Can be enhanced to support native chat APIs
        $prompt = collect($messages)
            ->map(fn($msg) => "{$msg['role']}: {$msg['content']}")
            ->join("\n");
        
        return $this->generate($prompt, 'chat', $options);
    }

    /**
     * Generate code with specific language context
     * 
     * @param string $prompt Code generation request
     * @param string $language Target language (php, javascript, python, etc.)
     * @param array $options Additional options
     * @return array Response
     */
    public function generateCode(string $prompt, string $language = 'php', array $options = []): array
    {
        $systemPrompt = $options['system_prompt'] ?? "You are an expert {$language} developer. Generate clean, well-documented code following best practices.";
        
        return $this->generate($prompt, 'code_generation', array_merge($options, [
            'system_prompt' => $systemPrompt
        ]));
    }

    /**
     * Analyze content (code review, text analysis, etc.)
     * 
     * @param string $content Content to analyze
     * @param string $type Analysis type (code_review, sentiment, summary, etc.)
     * @param array $options Additional options
     * @return array Response
     */
    public function analyze(string $content, string $type = 'general', array $options = []): array
    {
        $systemPrompts = [
            'code_review' => 'You are an expert code reviewer. Analyze code for bugs, security issues, and improvements.',
            'sentiment' => 'You are a sentiment analysis expert. Analyze the emotional tone and sentiment.',
            'summary' => 'You are a summarization expert. Provide concise, accurate summaries.',
            'general' => 'You are an analytical expert. Provide thorough analysis.',
        ];

        $systemPrompt = $options['system_prompt'] ?? $systemPrompts[$type] ?? $systemPrompts['general'];
        
        return $this->generate($content, $type, array_merge($options, [
            'system_prompt' => $systemPrompt
        ]));
    }

    /**
     * Set specific provider (optional - uses active by default)
     */
    public function withProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Set specific model
     */
    public function withModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Set temperature (0.0 - 2.0)
     */
    public function withTemperature(float $temperature): self
    {
        $this->temperature = $temperature;
        return $this;
    }

    /**
     * Set max tokens
     */
    public function withMaxTokens(int $tokens): self
    {
        $this->maxTokens = $tokens;
        return $this;
    }

    /**
     * Add custom metadata
     */
    public function withMetadata(array $metadata): self
    {
        $this->metadata = array_merge($this->metadata, $metadata);
        return $this;
    }

    /**
     * Core generation method
     * 
     * @param string $prompt The prompt/content
     * @param string $feature Feature identifier for logging
     * @param array $options Options (system_prompt, temperature, max_tokens, etc.)
     * @return array Response
     * @throws Exception
     */
    protected function generate(string $prompt, string $feature = 'general', array $options = []): array
    {
        try {
            // Get provider
            if ($this->provider) {
                $activeProvider = AIProvider::where('name', $this->provider)->where('is_active', true)->first();
                if (!$activeProvider) {
                    throw new Exception("Provider '{$this->provider}' is not active");
                }
            } else {
                $activeProvider = AIProvider::where('is_active', true)->first();
                if (!$activeProvider) {
                    throw new Exception('No active AI provider configured');
                }
            }

            // Create provider instance
            $aiProvider = AIProviderFactory::make($activeProvider->name);

            // Merge options with fluent setters
            if ($this->temperature !== null) {
                $options['temperature'] = $this->temperature;
            }
            if ($this->maxTokens !== null) {
                $options['max_tokens'] = $this->maxTokens;
            }
            if ($this->model !== null) {
                $options['model'] = $this->model;
            }

            // Send request
            $response = $aiProvider->sendRequest($prompt, $feature, $options);

            // Reset fluent setters after use
            $this->reset();

            return $response;

        } catch (Exception $e) {
            // Reset on error
            $this->reset();
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'provider' => $this->provider ?? 'unknown'
            ];
        }
    }

    /**
     * Reset fluent settings
     */
    protected function reset(): void
    {
        $this->provider = null;
        $this->model = null;
        $this->temperature = null;
        $this->maxTokens = null;
        $this->metadata = [];
    }

    /**
     * Quick helper: Get just the text response
     */
    public function text(string $prompt, array $options = []): string
    {
        $response = $this->ask($prompt, $options);
        return $response['content'] ?? '';
    }
}
