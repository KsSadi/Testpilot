<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AIProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'name' => 'openai',
                'display_name' => 'OpenAI (ChatGPT)',
                'description' => 'OpenAI GPT models for advanced AI capabilities',
                'api_base_url' => 'https://api.openai.com/v1',
                'api_key' => null,
                'models' => json_encode([
                    'gpt-4' => 'GPT-4 (Most capable)',
                    'gpt-4-turbo' => 'GPT-4 Turbo (Fast & capable)',
                    'gpt-4o' => 'GPT-4o (Optimized)',
                    'gpt-3.5-turbo' => 'GPT-3.5 Turbo (Fast & affordable)',
                ]),
                'default_model' => 'gpt-4-turbo',
                'is_active' => false,
                'is_enabled' => true,
                'settings' => json_encode([
                    'temperature' => 0.7,
                    'max_tokens' => 4000,
                    'top_p' => 1.0,
                    'frequency_penalty' => 0.0,
                    'presence_penalty' => 0.0,
                ]),
                'priority' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'gemini',
                'display_name' => 'Google Gemini',
                'description' => 'Google\'s advanced AI model with multimodal capabilities',
                'api_base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'api_key' => null,
                'models' => json_encode([
                    'gemini-3-pro-preview' => 'Gemini 3 Pro Preview (Flagship - Most Intelligent)',
                    'gemini-2.5-pro' => 'Gemini 2.5 Pro (Advanced Reasoning)',
                    'gemini-2.5-flash' => 'Gemini 2.5 Flash (Best Price-Performance)',
                    'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash-Lite (Fastest)',
                    'gemini-2.0-flash' => 'Gemini 2.0 Flash (Balanced)',
                    'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash-Lite (Fast & Cost-Efficient)',
                ]),
                'default_model' => 'gemini-2.5-flash',
                'is_active' => false,
                'is_enabled' => true,
                'settings' => json_encode([
                    'temperature' => 0.7,
                    'max_output_tokens' => 8000,
                    'top_p' => 1.0,
                    'top_k' => 40,
                ]),
                'priority' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'deepseek',
                'display_name' => 'DeepSeek',
                'description' => 'DeepSeek V3.2 - Advanced reasoning and coding capabilities',
                'api_base_url' => 'https://api.deepseek.com',
                'api_key' => null,
                'models' => json_encode([
                    'deepseek-chat' => 'DeepSeek Chat (V3.2 Non-Thinking Mode)',
                    'deepseek-reasoner' => 'DeepSeek Reasoner (V3.2 Thinking Mode)',
                ]),
                'default_model' => 'deepseek-chat',
                'is_active' => false,
                'is_enabled' => true,
                'settings' => json_encode([
                    'temperature' => 0.7,
                    'max_tokens' => 8000,
                    'top_p' => 1.0,
                ]),
                'priority' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($providers as $provider) {
            DB::table('ai_providers')->updateOrInsert(
                ['name' => $provider['name']],
                $provider
            );
        }

        // Add pricing data for each model
        $this->seedPricingData();

        // Insert default AI settings
        $settings = [
            [
                'key' => 'ai_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable/disable AI features globally',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'active_provider',
                'value' => 'openai',
                'type' => 'string',
                'description' => 'Currently active AI provider',
                'group' => 'general',
                'is_public' => false,
            ],
            [
                'key' => 'max_requests_per_day',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Maximum AI requests per user per day',
                'group' => 'limits',
                'is_public' => false,
            ],
            [
                'key' => 'max_tokens_per_request',
                'value' => '4000',
                'type' => 'integer',
                'description' => 'Maximum tokens per AI request',
                'group' => 'limits',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('ai_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Seed pricing data for all models
     */
    private function seedPricingData(): void
    {
        $openaiProvider = DB::table('ai_providers')->where('name', 'openai')->first();
        $geminiProvider = DB::table('ai_providers')->where('name', 'gemini')->first();
        $deepseekProvider = DB::table('ai_providers')->where('name', 'deepseek')->first();

        // OpenAI Pricing (per 1M tokens as of Jan 2025)
        $openaiPricing = [
            'gpt-4' => ['input' => 30.00, 'output' => 60.00],
            'gpt-4-turbo' => ['input' => 10.00, 'output' => 30.00],
            'gpt-4o' => ['input' => 5.00, 'output' => 15.00],
            'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50],
        ];

        foreach ($openaiPricing as $model => $pricing) {
            DB::table('ai_settings')->updateOrInsert(
                ['key' => "pricing_{$model}"], // Unique key per model
                [
                    'provider_id' => $openaiProvider->id,
                    'value' => $model,
                    'input_price' => $pricing['input'],
                    'output_price' => $pricing['output'],
                    'type' => 'pricing',
                    'description' => "Pricing for {$model}",
                    'group' => 'pricing',
                    'is_public' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Gemini Pricing (per 1M tokens as of Jan 2025)
        $geminiPricing = [
            'gemini-3-pro-preview' => ['input' => 2.00, 'output' => 12.00],
            'gemini-2.5-pro' => ['input' => 1.25, 'output' => 10.00],
            'gemini-2.5-flash' => ['input' => 0.30, 'output' => 2.50],
            'gemini-2.5-flash-lite' => ['input' => 0.10, 'output' => 0.40],
            'gemini-2.0-flash' => ['input' => 0.10, 'output' => 0.40],
            'gemini-2.0-flash-lite' => ['input' => 0.075, 'output' => 0.30],
        ];

        foreach ($geminiPricing as $model => $pricing) {
            DB::table('ai_settings')->updateOrInsert(
                ['key' => "pricing_{$model}"], // Unique key per model
                [
                    'provider_id' => $geminiProvider->id,
                    'value' => $model,
                    'input_price' => $pricing['input'],
                    'output_price' => $pricing['output'],
                    'type' => 'pricing',
                    'description' => "Pricing for {$model}",
                    'group' => 'pricing',
                    'is_public' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // DeepSeek Pricing (per 1M tokens as of Jan 2025)
        $deepseekPricing = [
            'deepseek-chat' => ['input' => 0.28, 'output' => 0.42],
            'deepseek-reasoner' => ['input' => 0.28, 'output' => 0.42],
        ];

        foreach ($deepseekPricing as $model => $pricing) {
            DB::table('ai_settings')->updateOrInsert(
                ['key' => "pricing_{$model}"], // Unique key per model
                [
                    'provider_id' => $deepseekProvider->id,
                    'value' => $model,
                    'input_price' => $pricing['input'],
                    'output_price' => $pricing['output'],
                    'type' => 'pricing',
                    'description' => "Pricing for {$model}",
                    'group' => 'pricing',
                    'is_public' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
