# Cypress Auto-Recorder - Quick Reference

## Selector Priority (Strict Order)

1. **ID Attribute** - Primary and preferred
   - Input: Element with `id="myButton"`
   - Output: `cy.get('[id="myButton"]', { timeout: 15000 }).should('be.visible').click();`

2. **XPath** - Fallback when no ID
   - Input: Element without ID at `/html/body/div[1]/button[2]`
   - Output: `cy.xpath('/html/body/div[1]/button[2]', { timeout: 15000 }).should('be.visible').click();`

## Generated Command Patterns

### Click Action
```javascript
// With ID
cy.get('[id="submit-btn"]', { timeout: 15000 }).should('be.visible').click();
cy.wait(2000);

// With XPath
cy.xpath('/html/body/div[1]/form/button', { timeout: 15000 }).should('be.visible').click();
cy.wait(2000);
```

### Input/Type Action
```javascript
// With ID
cy.get('[id="username"]', { timeout: 15000 }).should('be.visible').clear().type('testuser');
cy.wait(2000);

// With XPath
cy.xpath('/html/body/form/input[1]', { timeout: 15000 }).should('be.visible').clear().type('testuser');
cy.wait(2000);
```

### Select Dropdown
```javascript
// With ID
cy.get('[id="country"]', { timeout: 15000 }).should('be.visible').select('United States');
cy.wait(2000);

// With XPath
cy.xpath('/html/body/form/select', { timeout: 15000 }).should('be.visible').select('United States');
cy.wait(2000);
```

## Command Components

| Component | Purpose | Value |
|-----------|---------|-------|
| `{ timeout: 15000 }` | Wait up to 15 seconds for element | 15000ms |
| `.should('be.visible')` | Assert element is visible before action | Ensures element ready |
| `cy.wait(2000)` | Wait after action completes | 2000ms |

## Files Modified

1. **event-capture.js** - Browser-side event capture with ID/XPath priority
2. **CodeGeneratorService.php** - Code generation with proper selectors and waits
3. **SelectorOptimizerService.php** - Selector optimization to ID/XPath only

## Setup Requirements

### Install cypress-xpath Plugin
```bash
npm install -D cypress-xpath
```

### Configure in support/e2e.js
```javascript
require('cypress-xpath')
```

## Common Use Cases

### Element Has ID
```html
<button id="submit-btn">Submit</button>
```
Generated:
```javascript
cy.get('[id="submit-btn"]', { timeout: 15000 }).should('be.visible').click();
cy.wait(2000);
```

### Element Without ID
```html
<button class="btn-primary">Submit</button>
```
Generated:
```javascript
cy.xpath('/html/body/div[1]/form/button', { timeout: 15000 }).should('be.visible').click();
cy.wait(2000);
```

### Form Input with ID
```html
<input type="text" id="email" placeholder="Enter email">
```
Generated:
```javascript
cy.get('[id="email"]', { timeout: 15000 }).should('be.visible').clear().type('user@example.com');
cy.wait(2000);
```

### Dropdown with ID
```html
<select id="country">
  <option>USA</option>
  <option>Canada</option>
</select>
```
Generated:
```javascript
cy.get('[id="country"]', { timeout: 15000 }).should('be.visible').select('USA');
cy.wait(2000);
```

## Best Practices

✅ **DO:**
- Add `id` attributes to important elements in your application
- Use descriptive, unique IDs
- Keep IDs stable across releases
- Use semantic naming (e.g., `submit-button`, `email-input`)

❌ **DON'T:**
- Use dynamic IDs that change on each page load
- Rely solely on XPath for frequently tested elements
- Remove IDs from elements used in tests

## Troubleshooting

### Issue: Generated test uses XPath for element with ID
**Solution:** Check if the ID is being captured correctly in the event. The element might have an empty ID attribute.

### Issue: XPath selector is too long
**Solution:** Add an ID attribute to the element or a parent element to shorten the path.

### Issue: Test fails with "element not visible"
**Solution:** The element might be hidden or off-screen. Check visibility and scroll position. The `.should('be.visible')` assertion will wait and fail if not visible.

### Issue: cypress-xpath not found
**Solution:** Install the plugin:
```bash
npm install -D cypress-xpath
```
And add to `cypress/support/e2e.js`:
```javascript
require('cypress-xpath')
```

## Migration from Old Selectors

Old code might have used various selector strategies. The new code only uses:
- `cy.get('[id="..."]')` for ID selectors
- `cy.xpath('...')` for XPath selectors

All other selectors (class, name, placeholder, text content) are no longer generated.
