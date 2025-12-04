# AI Service Usage Guide

## Industry-Standard AI Integration

This module provides a clean, fluent API for AI interactions following Laravel best practices.

---

## Basic Usage

### Simple Question/Answer
```php
use App\Modules\AI\Facades\AI;

// Simplest form - just ask!
$response = AI::ask('What is Laravel?');

if ($response['success']) {
    echo $response['content'];      // AI response text
    echo $response['cost'];          // Cost in USD
    echo $response['tokens'];        // Token usage
    echo $response['response_time']; // Response time in ms
}
```

### Get Only Text (Quick Helper)
```php
$text = AI::text('Explain dependency injection');
echo $text; // Just the response content, no metadata
```

---

## Advanced Usage

### Custom System Prompt
```php
$response = AI::ask('How do I center a div?', [
    'system_prompt' => 'You are an expert CSS developer. Provide modern solutions.'
]);
```

### Code Generation
```php
// Generate PHP code
$response = AI::generateCode('Create a User model with validation', 'php');

// Generate JavaScript
$response = AI::generateCode('Create a React button component', 'javascript');

// With custom context
$response = AI::generateCode('Create API endpoint for users', 'php', [
    'system_prompt' => 'Use Laravel 11 best practices with strict types'
]);
```

### Code Analysis/Review
```php
$code = file_get_contents('app/Models/User.php');

$response = AI::analyze($code, 'code_review');
// Returns analysis with suggestions, bugs, security issues

$response = AI::analyze($userFeedback, 'sentiment');
// Returns sentiment analysis

$response = AI::analyze($longArticle, 'summary');
// Returns concise summary
```

### Chat with Context
```php
$messages = [
    ['role' => 'user', 'content' => 'What is Cypress?'],
    ['role' => 'assistant', 'content' => 'Cypress is an E2E testing framework...'],
    ['role' => 'user', 'content' => 'How do I install it?']
];

$response = AI::chat($messages);
```

---

## Fluent API (Chaining)

### Specify Provider
```php
// Use specific provider (must be active)
$response = AI::withProvider('gemini')
    ->ask('What is the meaning of life?');

$response = AI::withProvider('openai')
    ->ask('Write a poem about Laravel');
```

### Control Creativity (Temperature)
```php
// More creative (higher temperature)
$creative = AI::withTemperature(1.5)
    ->ask('Write a creative story about AI');

// More factual/deterministic (lower temperature)
$factual = AI::withTemperature(0.2)
    ->ask('What is 2+2?');
```

### Control Response Length
```php
$response = AI::withMaxTokens(500)
    ->ask('Explain quantum computing in detail');
```

### Combine Multiple Options
```php
$response = AI::withProvider('gemini')
    ->withTemperature(0.7)
    ->withMaxTokens(2000)
    ->generateCode('Create a REST API for blog posts', 'php');
```

---

## Real-World Examples

### Cypress Test Helper
```php
public function generateCypressTest(Request $request)
{
    $systemPrompt = "You are a Cypress testing expert. Generate clean, maintainable test code.";
    
    $response = AI::withTemperature(0.3) // More deterministic for code
        ->ask($request->input('description'), [
            'system_prompt' => $systemPrompt
        ]);
    
    return response()->json($response);
}
```

### Code Review
```php
public function reviewCode(Request $request)
{
    $code = $request->input('code');
    
    $response = AI::analyze($code, 'code_review', [
        'system_prompt' => 'Review for Laravel best practices, security, and performance.'
    ]);
    
    return view('review-results', ['review' => $response['content']]);
}
```

### Documentation Generator
```php
public function generateDocs(string $className)
{
    $classCode = file_get_contents(app_path("Models/{$className}.php"));
    
    $response = AI::ask("Generate comprehensive documentation for this class:\n\n{$classCode}", [
        'system_prompt' => 'You are a technical documentation expert. Write clear PHPDoc comments.'
    ]);
    
    return $response['content'];
}
```

### Test Data Generator
```php
public function generateTestData(string $model, int $count = 10)
{
    $response = AI::ask("Generate {$count} realistic test records for a {$model} model", [
        'system_prompt' => 'You are a test data expert. Return valid JSON array of objects.'
    ]);
    
    return json_decode($response['content'], true);
}
```

### Smart Search Assistant
```php
public function smartSearch(string $query)
{
    // Use AI to understand user intent
    $response = AI::withTemperature(0.1) // Very focused
        ->ask("Convert this user query to SQL WHERE clause: {$query}", [
            'system_prompt' => 'You are a SQL expert. Return only the WHERE clause, no explanations.'
        ]);
    
    $whereClause = trim($response['content']);
    
    return DB::table('products')->whereRaw($whereClause)->get();
}
```

---

## Error Handling

```php
$response = AI::ask('Your question here');

if (!$response['success']) {
    // Handle error
    logger()->error('AI Error', ['error' => $response['error']]);
    
    return response()->json([
        'error' => $response['error']
    ], 500);
}

// Success - use the response
return $response['content'];
```

---

## Comparison: Before vs After

### ❌ Before (Verbose)
```php
$activeProvider = AIProvider::where('is_active', true)->first();

if (!$activeProvider) {
    throw new Exception('No active provider');
}

$aiProvider = AIProviderFactory::make($activeProvider->name);

$response = $aiProvider->sendRequest(
    $prompt,
    'feature_name',
    ['system_prompt' => $systemPrompt]
);

if ($response['success']) {
    return $response['content'];
}
```

### ✅ After (Clean)
```php
$response = AI::ask($prompt, ['system_prompt' => $systemPrompt]);

if ($response['success']) {
    return $response['content'];
}
```

### ✅ Even Better (One-liner)
```php
return AI::text($prompt); // Just the content, no checks needed
```

---

## Best Practices

1. **Use `text()` for simple cases**: When you only need the response text
2. **Use `ask()` when you need metadata**: Tokens, cost, response time
3. **Set temperature appropriately**:
   - 0.0-0.3: Factual, code generation, structured output
   - 0.7-1.0: Balanced creativity
   - 1.5-2.0: Creative writing, brainstorming
4. **Use specific methods**: `generateCode()`, `analyze()` for better context
5. **Handle errors gracefully**: Always check `$response['success']`
6. **Cache when possible**: AI calls are expensive, cache results when appropriate

---

## Performance Tips

### Caching Results
```php
$cacheKey = 'ai_response_' . md5($prompt);

$response = Cache::remember($cacheKey, now()->addHour(), function() use ($prompt) {
    return AI::ask($prompt);
});
```

### Async Processing (Queue)
```php
// In a job
public function handle()
{
    $response = AI::generateCode($this->request, 'php');
    
    // Store result
    $this->result->update(['content' => $response['content']]);
}
```

---

## Configuration

All AI providers are managed in `/ai/settings`:
- Add/rotate API keys
- Set active provider
- Configure pricing
- View analytics

The facade automatically uses the active provider, but you can override:

```php
AI::withProvider('gemini')->ask('Question');
AI::withProvider('openai')->ask('Question');
```

---

## Industry Standards Followed

1. **Facade Pattern**: Clean, static-like API (Laravel convention)
2. **Fluent Interface**: Chainable methods for readability
3. **Service Container**: Singleton registration in service provider
4. **Single Responsibility**: Each method has one clear purpose
5. **Error Handling**: Consistent response structure
6. **Type Safety**: Proper type hints and return types
7. **Convention over Configuration**: Sensible defaults
8. **DRY Principle**: Reusable, composable methods

---

## Extending the Service

Add custom methods in `AIService.php`:

```php
public function translateCode(string $code, string $from, string $to): array
{
    return $this->ask("Translate this {$from} code to {$to}:\n\n{$code}", [
        'system_prompt' => "You are an expert in {$from} and {$to}. Provide only code, no explanations."
    ]);
}
```

Usage:
```php
$response = AI::translateCode($pythonCode, 'python', 'php');
```
