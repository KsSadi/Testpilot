# Code Generator Feature - Playwright-Style for Cypress Module

## Overview
This feature adds Playwright-style automatic code generation capabilities to your Cypress testing module. It converts captured user events into clean, maintainable Cypress or Playwright test code.

## Features Implemented

### 1. **Smart Code Generation** ✅
- **Multiple Formats**: Generate code in Cypress or Playwright format
- **Intelligent Selectors**: Automatically optimizes selectors for stability
- **Auto-Assertions**: Optional automatic verification steps
- **Event Timeline**: Visual representation of captured events

### 2. **Selector Optimizer** ✅
- **Priority-Based Selection**:
  1. `data-testid` (highest priority - most stable)
  2. `data-cy` (Cypress-specific attributes)
  3. `id` attributes
  4. `name` attributes (for form elements)
  5. `aria-label` (accessibility-friendly)
  6. Type + Placeholder combinations
  7. Text content (for buttons/links)
  8. Filtered class names (removes dynamic classes)
  9. Tag names (fallback)

- **Selector Validation**: Scores selectors from 0-100 based on stability
- **Multiple Suggestions**: Provides alternative selector options

### 3. **Code Generator Service** ✅
Handles the conversion of events to test code:
- Generates proper test structure (describe/beforeEach/it blocks)
- Adds contextual comments for each step
- Supports all common event types (click, input, submit, etc.)
- Handles keyboard events and special keys
- Optional AI enhancement hooks

### 4. **API Endpoints** ✅

#### Preview Code
```
GET /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/preview
```
Parameters:
- `format`: 'cypress' or 'playwright'
- `add_assertions`: boolean
- `ai_enhance`: boolean

#### Download Code
```
GET /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/download
```
Downloads generated code as `.cy.js` or `.spec.js` file

#### Generate Code (AJAX)
```
POST /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/generate
```
Returns JSON with generated code

#### Live Preview
```
GET /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/live-preview
```
Real-time code generation as events are captured

#### Selector Operations
```
GET /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/events/{eventId}/selectors
GET /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/events/{eventId}/optimize
POST /projects/{project}/modules/{module}/test-cases/{testCase}/code-generator/validate-selector
```

#### Export Test Suite
```
POST /projects/{project}/modules/{module}/export-suite
```
Export multiple test cases as a single test suite

## Usage Guide

### Basic Usage

1. **Navigate to Test Case**
   - Go to your test case page
   - Click the "Code Generator" button (purple button)

2. **Select Options**
   - Choose test framework (Cypress or Playwright)
   - Enable/disable assertions
   - Enable AI enhancement (if available)

3. **Generate & Download**
   - Click "Download" to get the code file
   - Or click "Copy to Clipboard" to copy the code

### Example Generated Code

#### Cypress Format:
```javascript
describe('Login Test', () => {
  beforeEach(() => {
    cy.visit('https://example.com');
  });

  it('should execute recorded test case', () => {
    // Step 1: Enter value into input
    cy.get('[data-testid="email"]').clear().type('user@example.com');

    // Step 2: Enter value into input
    cy.get('[data-testid="password"]').clear().type('password123');

    // Step 3: Click on button
    cy.get('button[type="submit"]').click();

    // Step 4: Verify success message
    cy.get('.success-message').should('be.visible');
  });
});
```

#### Playwright Format:
```javascript
import { test, expect } from '@playwright/test';

test('Login Test', async ({ page }) => {
  await page.goto('https://example.com');
  await page.locator('[data-testid="email"]').fill('user@example.com');
  await page.locator('[data-testid="password"]').fill('password123');
  await page.locator('button[type="submit"]').click();
});
```

## Advanced Features

### Selector Quality Scoring
Each selector is scored based on stability:
- **Excellent (70-100)**: Uses data-testid, data-cy, or stable IDs
- **Good (50-69)**: Uses name attributes or aria-labels
- **Fair (30-49)**: Uses filtered class names
- **Poor (0-29)**: Uses nth-child or complex selectors

### AI Enhancement (Placeholder)
Hook ready for integration with your AI module to:
- Generate intelligent test descriptions
- Add context-aware comments
- Suggest improvements
- Identify potential issues

## File Structure

```
app/Modules/Cypress/
├── Services/
│   ├── CodeGeneratorService.php      # Main code generation logic
│   └── SelectorOptimizerService.php  # Selector optimization
├── Http/Controllers/
│   └── CodeGeneratorController.php   # API endpoints
├── resources/views/
│   └── code-generator/
│       └── preview.blade.php         # Code preview UI
└── routes/
    └── web.php                       # Added new routes
```

## API Response Examples

### Generate Code Response:
```json
{
  "success": true,
  "code": "describe('Test', () => { ... })",
  "format": "cypress",
  "event_count": 15
}
```

### Selector Suggestions Response:
```json
{
  "success": true,
  "suggestions": {
    "optimized": {
      "selector": "[data-testid='submit-btn']",
      "priority": "high",
      "description": "Recommended selector (stable and maintainable)"
    },
    "role_based": {
      "selector": "role=button[name='Submit']",
      "priority": "high",
      "description": "Accessibility-friendly selector"
    }
  },
  "event": {
    "id": 123,
    "type": "click",
    "tag": "BUTTON",
    "text": "Submit"
  }
}
```

## Benefits

### For Developers
- ✅ **Zero Manual Work**: No need to write test code manually
- ✅ **Best Practices**: Automatically generates maintainable selectors
- ✅ **Multi-Format**: Supports both Cypress and Playwright
- ✅ **Time Saving**: Converts hours of work into seconds

### For Test Quality
- ✅ **Stable Tests**: Prioritizes stable selectors over fragile ones
- ✅ **Accessibility**: Supports aria-labels and role-based selectors
- ✅ **Maintainable**: Filters out dynamic/random class names
- ✅ **Well-Commented**: Auto-generates descriptive comments

## Future Enhancements (Roadmap)

1. **AI Integration**: Full integration with AI module for intelligent code generation
2. **Selenium Support**: Add Selenium WebDriver code generation
3. **Visual Assertions**: Generate visual regression tests
4. **Data-Driven Tests**: Support for parameterized tests
5. **Custom Templates**: Allow users to define code templates
6. **API Testing**: Generate API test code from network events
7. **Mobile Testing**: Support for Appium code generation

## Notes

- ✅ **No Existing Code Changed**: All new functionality, zero breaking changes
- ✅ **Backward Compatible**: Works with existing test cases
- ✅ **Service Architecture**: Clean separation of concerns
- ✅ **Extensible**: Easy to add new formats or features

## Next Steps

To use this feature:
1. Navigate to any test case with saved events
2. Click the "Code Generator" button
3. Select your options and download the code
4. Run the generated test in your CI/CD pipeline

Enjoy automated test code generation! 🚀
