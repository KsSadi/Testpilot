# Code Generator API Examples

## Quick Test Examples

### 1. Generate Cypress Code (AJAX)

```javascript
// Example: Generate Cypress code via AJAX
fetch('/projects/1/modules/2/test-cases/3/code-generator/generate', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        format: 'cypress',
        add_assertions: true,
        ai_enhance: false
    })
})
.then(response => response.json())
.then(data => {
    console.log('Generated Code:', data.code);
    console.log('Event Count:', data.event_count);
});
```

### 2. Get Selector Suggestions

```javascript
// Get multiple selector options for an event
fetch('/projects/1/modules/2/test-cases/3/code-generator/events/123/selectors')
.then(response => response.json())
.then(data => {
    console.log('Optimized:', data.suggestions.optimized.selector);
    console.log('Role-based:', data.suggestions.role_based?.selector);
});
```

### 3. Validate Selector Quality

```javascript
// Check how good a selector is
fetch('/projects/1/modules/2/test-cases/3/code-generator/validate-selector', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        selector: '[data-testid="submit-btn"]'
    })
})
.then(response => response.json())
.then(data => {
    console.log('Score:', data.validation.score); // 0-100
    console.log('Rating:', data.validation.rating); // excellent/good/fair/poor
    console.log('Warnings:', data.validation.warnings);
});
```

### 4. Real-time Code Preview

```javascript
// Get live code as events are captured
fetch('/projects/1/modules/2/test-cases/3/code-generator/live-preview?format=cypress')
.then(response => response.json())
.then(data => {
    data.lines.forEach(line => {
        console.log(`Line ${line.line}:`, line.code);
    });
});
```

### 5. Export Module as Test Suite

```javascript
// Export all test cases from a module
fetch('/projects/1/modules/2/export-suite', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        test_cases: [1, 2, 3, 4],
        format: 'playwright'
    })
})
.then(response => response.blob())
.then(blob => {
    // Download the file
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'test-suite.spec.js';
    a.click();
});
```

## PHP Usage Examples

### Using the Services Directly

```php
use App\Modules\Cypress\Services\CodeGeneratorService;
use App\Modules\Cypress\Services\SelectorOptimizerService;
use App\Modules\Cypress\Models\TestCase;

// Generate Cypress code
$codeGenerator = app(CodeGeneratorService::class);
$testCase = TestCase::find(1);

$cypressCode = $codeGenerator->generateCypressCode($testCase, [
    'add_assertions' => true,
    'ai_enhance' => false
]);

// Generate Playwright code
$playwrightCode = $codeGenerator->generatePlaywrightCode($testCase);

// Optimize a selector
$selectorOptimizer = app(SelectorOptimizerService::class);
$event = $testCase->savedEvents()->first();
$optimizedSelector = $selectorOptimizer->optimizeSelector($event);

// Get selector suggestions
$suggestions = $selectorOptimizer->suggestSelectors($event);

// Validate selector
$validation = $selectorOptimizer->validateSelector('[data-testid="button"]');
echo "Score: " . $validation['score']; // 0-100
echo "Rating: " . $validation['rating']; // excellent/good/fair/poor
```

### Creating Custom Code Templates

```php
// Extend CodeGeneratorService for custom templates
namespace App\Modules\Cypress\Services;

class CustomCodeGenerator extends CodeGeneratorService
{
    public function generateCustomFormat(TestCase $testCase): string
    {
        $events = $testCase->savedEvents()->get();
        
        // Your custom format logic
        $code = "// Custom Test Format\n";
        foreach ($events as $event) {
            $selector = $this->selectorOptimizer->optimizeSelector($event);
            $code .= "action('{$event->event_type}', '{$selector}');\n";
        }
        
        return $code;
    }
}
```

## Route Testing

### Test Routes with Artisan

```bash
# List all code generator routes
php artisan route:list --name=code-generator

# Expected output:
# code-generator.preview
# code-generator.download
# code-generator.generate
# code-generator.live-preview
# code-generator.suggest-selectors
# code-generator.optimize-selector
# code-generator.validate-selector
```

## Integration with Frontend

### Add to Your Blade Template

```blade
<!-- Button to open code generator -->
<a href="{{ route('code-generator.preview', [$project, $module, $testCase]) }}" 
   class="btn btn-primary">
    <i class="fas fa-code"></i> Generate Code
</a>

<!-- Download link -->
<a href="{{ route('code-generator.download', [$project, $module, $testCase]) }}?format=cypress" 
   download
   class="btn btn-success">
    <i class="fas fa-download"></i> Download Cypress Code
</a>

<!-- AJAX Generate -->
<button onclick="generateCode()">Generate Code</button>
<pre id="code-output"></pre>

<script>
function generateCode() {
    fetch('{{ route("code-generator.generate", [$project, $module, $testCase]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            format: 'cypress',
            add_assertions: true
        })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('code-output').textContent = data.code;
    });
}
</script>
```

## Testing the Implementation

### Manual Test Steps

1. **Navigate to a Test Case**
   ```
   URL: /projects/{project}/modules/{module}/test-cases/{testCase}
   ```

2. **Click "Code Generator" Button**
   - Should see purple button in header
   - Takes you to code preview page

3. **Test Different Options**
   - Switch between Cypress/Playwright
   - Enable/disable assertions
   - Click "Regenerate" to see changes

4. **Download Code**
   - Click "Download" button
   - Should download `.cy.js` or `.spec.js` file

5. **Test API Endpoints**
   - Use browser console or Postman
   - Test AJAX generation
   - Test selector suggestions

### Expected Results

✅ Code should be syntactically correct JavaScript
✅ Selectors should prioritize data-testid attributes
✅ Comments should describe each step
✅ Downloaded files should have proper extensions
✅ No errors in browser console
✅ All routes should work without 404 errors

## Troubleshooting

### Common Issues

**Issue**: Routes not found (404)
**Solution**: Clear route cache
```bash
php artisan route:clear
php artisan route:cache
```

**Issue**: Services not found
**Solution**: Clear config cache
```bash
php artisan config:clear
php artisan optimize:clear
```

**Issue**: Views not loading
**Solution**: Clear view cache
```bash
php artisan view:clear
```

## Performance Tips

1. **Batch Processing**: Use `exportSuite()` for multiple test cases
2. **Caching**: Consider caching generated code for large test suites
3. **Lazy Loading**: Use live preview for real-time updates
4. **Indexing**: Add database indexes on `session_id` if needed

## Security Considerations

✅ All routes require authentication
✅ CSRF protection enabled on POST routes
✅ Validated project/module/testCase ownership
✅ SQL injection protected via Eloquent
✅ XSS protection via Blade escaping

Enjoy your new Playwright-style code generator! 🎉
