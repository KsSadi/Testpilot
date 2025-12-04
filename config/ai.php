<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Features Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether AI features are globally enabled.
    | When disabled, all AI functionality will be unavailable.
    |
    */

    'enabled' => env('AI_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | This option defines the default AI provider to use.
    | Supported: "openai", "gemini", "deepseek"
    |
    */

    'default_provider' => env('AI_DEFAULT_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Provider Configurations
    |--------------------------------------------------------------------------
    |
    | Configuration for each AI provider. API keys should be set in .env
    |
    */

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => 'https://api.openai.com/v1',
            'timeout' => 60,
            'retry' => [
                'times' => 3,
                'sleep' => 1000, // milliseconds
            ],
        ],
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'timeout' => 60,
            'retry' => [
                'times' => 3,
                'sleep' => 1000,
            ],
        ],
        'deepseek' => [
            'api_key' => env('DEEPSEEK_API_KEY'),
            'base_url' => 'https://api.deepseek.com/v1',
            'timeout' => 60,
            'retry' => [
                'times' => 3,
                'sleep' => 1000,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific AI features
    |
    */

    'features' => [
        'test_generation' => env('AI_FEATURE_TEST_GENERATION', true),
        'code_optimization' => env('AI_FEATURE_CODE_OPTIMIZATION', true),
        'bug_analysis' => env('AI_FEATURE_BUG_ANALYSIS', true),
        'visual_testing' => env('AI_FEATURE_VISUAL_TESTING', false),
        'self_healing' => env('AI_FEATURE_SELF_HEALING', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limits
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for AI requests
    |
    */

    'rate_limits' => [
        'requests_per_minute' => env('AI_RATE_LIMIT_PER_MINUTE', 20),
        'requests_per_hour' => env('AI_RATE_LIMIT_PER_HOUR', 100),
        'requests_per_day' => env('AI_RATE_LIMIT_PER_DAY', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cost Limits
    |--------------------------------------------------------------------------
    |
    | Configure cost limits for AI usage
    |
    */

    'cost_limits' => [
        'max_cost_per_request' => env('AI_MAX_COST_PER_REQUEST', 0.50), // USD
        'max_cost_per_day_per_user' => env('AI_MAX_COST_PER_DAY_USER', 5.00), // USD
        'max_cost_per_day_total' => env('AI_MAX_COST_PER_DAY_TOTAL', 50.00), // USD
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Limits
    |--------------------------------------------------------------------------
    |
    | Configure token limits for AI requests
    |
    */

    'token_limits' => [
        'max_prompt_tokens' => env('AI_MAX_PROMPT_TOKENS', 8000),
        'max_completion_tokens' => env('AI_MAX_COMPLETION_TOKENS', 4000),
        'default_temperature' => env('AI_DEFAULT_TEMPERATURE', 0.7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Configure fallback behavior when primary provider fails
    |
    */

    'fallback' => [
        'enabled' => env('AI_FALLBACK_ENABLED', true),
        'priority' => [
            'openai',
            'gemini',
            'deepseek',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configure AI usage logging
    |
    */

    'logging' => [
        'enabled' => env('AI_LOGGING_ENABLED', true),
        'log_prompts' => env('AI_LOG_PROMPTS', true),
        'log_responses' => env('AI_LOG_RESPONSES', true),
        'retention_days' => env('AI_LOG_RETENTION_DAYS', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for AI responses
    |
    */

    'cache' => [
        'enabled' => env('AI_CACHE_ENABLED', false),
        'ttl' => env('AI_CACHE_TTL', 3600), // seconds
        'driver' => env('AI_CACHE_DRIVER', 'redis'),
    ],

];
