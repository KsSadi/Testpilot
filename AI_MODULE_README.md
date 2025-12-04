# AI Module Implementation

## Overview
Complete AI integration system supporting multiple providers (OpenAI, Gemini, DeepSeek) with centralized configuration management, usage tracking, and role-based access control.

## Setup Complete ✅

### 1. Database Layer
- **ai_providers**: Provider configurations (API keys encrypted, models, settings)
- **ai_settings**: Flexible key-value settings storage
- **ai_usage_logs**: Comprehensive usage tracking with cost monitoring
- All migrations executed successfully

### 2. Models
- **AIProvider**: Encrypted API keys, active provider management, validation
- **AISetting**: Typed value accessors, static get/set helpers
- **AIUsageLog**: User relationships, cost/time formatting, statistics

### 3. Provider Architecture
- **AIProviderBase**: Abstract class with request handling, validation, cost estimation
- **OpenAIProvider**: GPT-4, GPT-4 Turbo, GPT-4o, GPT-3.5 support
- **GeminiProvider**: Gemini 1.5 Pro/Flash, Gemini Pro support
- **DeepSeekProvider**: DeepSeek Chat & Coder specialized models
- **AIProviderFactory**: Provider instantiation with fallback support

### 4. Configuration
- **config/ai.php**: Complete configuration with features, limits, logging, caching

### 5. Admin Panel
- **Route**: `/ai/settings` (requires `edit-settings` permission)
- **Features**:
  - Global AI enable/disable toggle
  - Per-provider configuration (API keys, models, priority)
  - Test connection functionality
  - Provider activation
  - Usage statistics dashboard
  - Rate and cost limit configuration

### 6. Permissions
Created 6 AI permissions:
- `ai-access`: Basic AI feature access
- `ai-configure`: Configure AI settings
- `ai-view-logs`: View usage logs
- `ai-test-generation`: Use test generation
- `ai-code-optimization`: Use code optimization
- `ai-bug-analysis`: Use bug analysis

**Role Assignments**:
- Super Admin: All AI permissions
- Admin: ai-access, ai-test-generation, ai-code-optimization, ai-bug-analysis

### 7. Middleware
- **CheckAIAccess**: Validates AI permissions and feature-specific access
- Registered as `ai.access` middleware alias

## Usage

### Admin Configuration
1. Visit `/ai/settings`
2. Enable AI features globally
3. Configure provider API keys
4. Select default models
5. Test connections
6. Activate preferred provider
7. Set rate/cost limits

### For Developers

#### Using AI in Your Code
```php
use App\Services\AI\AIProviderFactory;

// Get active provider
$ai = AIProviderFactory::make();

// Send request
$result = $ai->sendRequest(
    prompt: "Generate a Cypress test for login functionality",
    feature: 'test_generation',
    options: [
        'temperature' => 0.7,
        'max_tokens' => 2000,
    ]
);

if ($result['success']) {
    $content = $result['content'];
    $cost = $result['cost'];
    $tokens = $result['tokens'];
}
```

#### With Fallback Support
```php
// Try active provider, fallback to others by priority
$ai = AIProviderFactory::makeWithFallback();
```

#### Checking Permissions in Routes
```php
// Require basic AI access
Route::middleware('ai.access')->group(function() {
    Route::post('/ai/generate-test', [TestController::class, 'generate']);
});

// Require specific feature permission
Route::middleware('ai.access:test_generation')->group(function() {
    Route::post('/ai/generate-test', [TestController::class, 'generate']);
});
```

#### Checking Permissions in Controllers
```php
if (auth()->user()->can('ai-test-generation')) {
    // Use AI test generation
}
```

#### Getting Settings
```php
use App\Models\AISetting;

$enabled = AISetting::get('ai_enabled', false);
$maxRequests = AISetting::get('max_requests_per_day', 100);
```

#### Logging Custom Usage
```php
AIUsageLog::create([
    'user_id' => auth()->id(),
    'provider' => 'openai',
    'model' => 'gpt-4-turbo',
    'feature' => 'custom_feature',
    'prompt' => $prompt,
    'response' => $response,
    'prompt_tokens' => 150,
    'completion_tokens' => 300,
    'total_tokens' => 450,
    'estimated_cost' => 0.0045,
    'response_time_ms' => 1200,
    'status' => 'success',
]);
```

## Environment Variables

Add to `.env`:
```env
# AI Configuration
AI_ENABLED=true
AI_DEFAULT_PROVIDER=openai

# Provider API Keys
OPENAI_API_KEY=your_openai_key_here
GEMINI_API_KEY=your_gemini_key_here
DEEPSEEK_API_KEY=your_deepseek_key_here

# Features
AI_FEATURE_TEST_GENERATION=true
AI_FEATURE_CODE_OPTIMIZATION=true
AI_FEATURE_BUG_ANALYSIS=true
AI_FEATURE_VISUAL_TESTING=false
AI_FEATURE_SELF_HEALING=false

# Limits
AI_RATE_LIMIT_PER_MINUTE=20
AI_RATE_LIMIT_PER_HOUR=100
AI_RATE_LIMIT_PER_DAY=500
AI_MAX_COST_PER_REQUEST=0.50
AI_MAX_COST_PER_DAY_USER=5.00
AI_MAX_COST_PER_DAY_TOTAL=50.00
```

## Next Steps (Future Phases)

### Phase 1: Test Generation
- Natural language to Cypress test conversion
- UI for test generation interface
- Template system for test patterns

### Phase 2: Code Optimization
- Analyze existing tests
- Suggest optimizations
- Refactor recommendations

### Phase 3: Bug Analysis
- Analyze test failures
- Suggest fixes
- Root cause analysis

### Phase 4: Visual Testing
- Screenshot analysis
- UI change detection
- Visual regression reporting

### Phase 5: Self-Healing
- Auto-update selectors
- Detect element changes
- Suggest alternative selectors

## Security Notes
- API keys are encrypted using Laravel's encryption
- Permissions required for all AI features
- Usage logged for audit trail
- Rate limiting to prevent abuse
- Cost limits to control expenses

## Monitoring
- View usage statistics at `/ai/settings`
- Check logs via API: `/ai/usage-logs`
- Get statistics: `/ai/statistics?days=30`
- All requests tracked with cost and performance metrics
