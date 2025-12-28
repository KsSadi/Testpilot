# 🚀 Code Generator - Quick Reference

## Quick Start (3 Steps)

1. **Navigate** → Go to any test case with recorded events
2. **Generate** → Click the purple "Code Generator" button
3. **Download** → Click "Download" or "Copy to Clipboard"

---

## Routes (All Available Endpoints)

```
📄 Preview Page
GET  /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/preview

⬇️ Download Code
GET  /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/download

🔄 Generate (AJAX)
POST /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/generate

👁️ Live Preview
GET  /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/live-preview

🎯 Selector Tools
GET  /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/events/{eventId}/selectors
GET  /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/events/{eventId}/optimize
POST /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/validate-selector

📦 Export Suite
POST /projects/{project}/modules/{module}/export-suite
```

---

## Parameters

### Format Options
- `format=cypress` → Generate Cypress code (.cy.js)
- `format=playwright` → Generate Playwright code (.spec.js)

### Generation Options
- `add_assertions=1` → Add automatic verification steps
- `ai_enhance=1` → Enable AI-powered enhancements

---

## Quick AJAX Example

```javascript
// Generate code via JavaScript
fetch(`/projects/1/modules/2/test-cases/3/code-generator/generate`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]').content
    },
    body: JSON.stringify({
        format: 'cypress',
        add_assertions: true
    })
})
.then(r => r.json())
.then(data => console.log(data.code));
```

---

## PHP Quick Example

```php
use App\Modules\Cypress\Services\CodeGeneratorService;
use App\Modules\Cypress\Models\TestCase;

$generator = app(CodeGeneratorService::class);
$testCase = TestCase::find(1);

// Generate Cypress code
$code = $generator->generateCypressCode($testCase);

// Generate Playwright code
$code = $generator->generatePlaywrightCode($testCase);
```

---

## Selector Priority (Best to Worst)

1. ⭐⭐⭐⭐⭐ `data-testid="button"` (Best - most stable)
2. ⭐⭐⭐⭐⭐ `data-cy="submit"`
3. ⭐⭐⭐⭐ `#unique-id`
4. ⭐⭐⭐ `[name="email"]`
5. ⭐⭐⭐ `[aria-label="Submit"]`
6. ⭐⭐ `input[type="text"][placeholder="Email"]`
7. ⭐⭐ `button:contains("Submit")`
8. ⭐ `.stable-class-name`
9. ⭐ `button` (tag name fallback)

---

## Troubleshooting

### Routes Not Found (404)
```bash
php artisan route:clear
php artisan route:cache
```

### Services Not Loading
```bash
php artisan config:clear
php artisan optimize:clear
```

### Views Not Rendering
```bash
php artisan view:clear
```

---

## File Locations

```
Services:
→ app/Modules/Cypress/Services/CodeGeneratorService.php
→ app/Modules/Cypress/Services/SelectorOptimizerService.php

Controller:
→ app/Modules/Cypress/Http/Controllers/CodeGeneratorController.php

View:
→ app/Modules/Cypress/resources/views/code-generator/preview.blade.php

Routes:
→ app/Modules/Cypress/routes/web.php (lines 72-87)
```

---

## Output Examples

### Cypress Output
```javascript
describe('Login Test', () => {
  beforeEach(() => {
    cy.visit('https://example.com');
  });

  it('should execute recorded test case', () => {
    cy.get('[data-testid="email"]').clear().type('user@example.com');
    cy.get('[data-testid="password"]').clear().type('password123');
    cy.get('button[type="submit"]').click();
  });
});
```

### Playwright Output
```javascript
import { test, expect } from '@playwright/test';

test('Login Test', async ({ page }) => {
  await page.goto('https://example.com');
  await page.locator('[data-testid="email"]').fill('user@example.com');
  await page.locator('[data-testid="password"]').fill('password123');
  await page.locator('button[type="submit"]').click();
});
```

---

## API Response Format

```json
{
  "success": true,
  "code": "describe('Test', () => { ... })",
  "format": "cypress",
  "event_count": 15
}
```

---

## Supported Event Types

✅ `click` → cy.get().click()
✅ `input` → cy.get().type()
✅ `change` → cy.get().type() or select()
✅ `submit` → cy.get().submit()
✅ `focus` → cy.get().focus()
✅ `blur` → cy.get().blur()
✅ `dblclick` → cy.get().dblclick()
✅ `rightclick` → cy.get().rightclick()
✅ `hover` → cy.get().trigger('mouseover')
✅ `keypress` → cy.get().type('{key}')
✅ `scroll` → cy.scrollTo()

---

## Features At a Glance

✅ Multi-format support (Cypress, Playwright)
✅ Smart selector optimization
✅ Auto-generated comments
✅ Assertion support
✅ Download as file
✅ Copy to clipboard
✅ Live preview
✅ Event timeline
✅ Selector validation
✅ Suite export

---

## Need Help?

📖 Full Documentation: `CODE_GENERATOR_GUIDE.md`
💻 API Examples: `CODE_GENERATOR_API_EXAMPLES.md`
📋 Implementation Details: `IMPLEMENTATION_COMPLETE.md`

---

**That's it! Start generating test code in seconds! 🎉**
