<?php

namespace App\Modules\Cypress\Services;

use App\Modules\Cypress\Models\TestCase;
use App\Modules\Cypress\Models\TestCaseEvent;
use Illuminate\Support\Collection;

class CodeGeneratorService
{
    protected SelectorOptimizerService $selectorOptimizer;

    public function __construct(SelectorOptimizerService $selectorOptimizer)
    {
        $this->selectorOptimizer = $selectorOptimizer;
    }

    /**
     * Generate Cypress code from raw events array (from browser automation)
     * Used by the auto-recorder feature
     */
    public function generateFromEvents(array $events): string
    {
        if (empty($events)) {
            return $this->generateEmptyCode();
        }

        // Optimize and deduplicate events
        $events = $this->deduplicateEvents($events);

        $code = "describe('Recorded Test', () => {\n";
        $code .= "  it('should perform recorded actions', () => {\n";

        $lastUrl = '';
        foreach ($events as $event) {
            $eventType = $event['type'] ?? 'unknown';
            $selector = $event['selector'] ?? '';
            $value = $event['value'] ?? '';
            $url = $event['url'] ?? '';

            switch ($eventType) {
                case 'pageload':
                    if ($url && $url !== 'about:blank' && $url !== $lastUrl) {
                        $code .= "    cy.visit('{$url}');\n";
                        $lastUrl = $url;
                    }
                    break;

                case 'click':
                    if ($selector) {
                        // Handle :contains():eq() selector combination
                        if (preg_match('/^(\w+):contains\("([^"]+)"\):eq\((\d+)\)$/', $selector, $matches)) {
                            $tag = $matches[1];
                            $text = $matches[2];
                            $index = $matches[3];
                            // Use first() for index 0, eq() for others
                            if ($index == 0) {
                                $code .= "    cy.contains('{$tag}', '{$text}').first().click({force: true});\n";
                            } else {
                                $code .= "    cy.contains('{$tag}', '{$text}').eq({$index}).click({force: true});\n";
                            }
                        }
                        // Handle :contains() selector - convert to cy.contains()
                        elseif (preg_match('/^(\w+):contains\("([^"]+)"\)$/', $selector, $matches)) {
                            $tag = $matches[1];
                            $text = $matches[2];
                            $code .= "    cy.contains('{$tag}', '{$text}').first().click({force: true});\n";
                        }
                        // Handle :eq(index) selector
                        elseif (preg_match('/(.+):eq\((\d+)\)/', $selector, $matches)) {
                            $baseSelector = $matches[1];
                            $index = $matches[2];
                            if ($index == 0) {
                                $code .= "    cy.get('{$baseSelector}').first().click({force: true});\n";
                            } else {
                                $code .= "    cy.get('{$baseSelector}').eq({$index}).click({force: true});\n";
                            }
                        } else {
                            $code .= "    cy.get('{$selector}').click({force: true});\n";
                        }
                    }
                    break;

                case 'input':
                    if ($selector && $value) {
                        $escapedValue = addslashes($value);
                        // Handle :contains() selector - convert to cy.contains()
                        if (preg_match('/^(\w+):contains\("([^"]+)"\)$/', $selector, $matches)) {
                            $tag = $matches[1];
                            $text = $matches[2];
                            $code .= "    cy.contains('{$tag}', '{$text}').clear().type('{$escapedValue}');\n";
                        }
                        // Handle :eq(index) selector
                        elseif (preg_match('/(.+):eq\((\d+)\)/', $selector, $matches)) {
                            $baseSelector = $matches[1];
                            $index = $matches[2];
                            $code .= "    cy.get('{$baseSelector}').eq({$index}).clear().type('{$escapedValue}');\n";
                        } else {
                            $code .= "    cy.get('{$selector}').clear().type('{$escapedValue}');\n";
                        }
                    }
                    break;

                case 'change':
                    // Skip change events as they're handled by input
                    break;

                case 'submit':
                    if ($selector) {
                        $code .= "    cy.get('{$selector}').submit();\n";
                    }
                    break;

                case 'navigation':
                    if ($url && $url !== 'about:blank') {
                        $code .= "    // Navigation to: {$url}\n";
                    }
                    break;
            }
        }

        $code .= "  });\n";
        $code .= "});\n";

        return $code;
    }

    /**
     * Deduplicate consecutive events on same element
     */
    protected function deduplicateEvents(array $events): array
    {
        $deduplicated = [];
        $lastEvent = null;

        foreach ($events as $event) {
            // Skip duplicate pageloads
            if ($event['type'] === 'pageload' && $lastEvent && $lastEvent['type'] === 'pageload' && 
                ($event['url'] ?? '') === ($lastEvent['url'] ?? '')) {
                continue;
            }

            // Merge consecutive input/change on same element - keep the last value
            if ($lastEvent && 
                in_array($event['type'], ['input', 'change']) && 
                in_array($lastEvent['type'], ['input', 'change']) &&
                ($event['selector'] ?? '') === ($lastEvent['selector'] ?? '')) {
                // Update the value in last event instead of adding new
                $lastEvent['value'] = $event['value'];
                $lastEvent['type'] = 'input'; // Prefer input
                continue;
            }

            // Skip click on input field if immediately followed by input (redundant)
            if ($lastEvent && 
                $lastEvent['type'] === 'click' && 
                $event['type'] === 'input' &&
                ($event['selector'] ?? '') === ($lastEvent['selector'] ?? '')) {
                // Remove the click from deduplicated array
                array_pop($deduplicated);
            }

            $deduplicated[] = $event;
            $lastEvent = $event;
        }

        return $deduplicated;
    }

    /**
     * Generate empty code template
     */
    protected function generateEmptyCode(): string
    {
        return "describe('Recorded Test', () => {\n  it('should perform recorded actions', () => {\n    // No events recorded\n  });\n});\n";
    }

    /**
     * Generate Cypress test code from test case events
     */
    public function generateCypressCode(TestCase $testCase, array $options = []): string
    {
        $events = $testCase->savedEvents()->orderBy('created_at')->get();
        
        if ($events->isEmpty()) {
            return $this->generateEmptyTestTemplate($testCase);
        }

        $code = $this->generateTestHeader($testCase, $options);
        $code .= $this->generateTestBody($events, $options);
        $code .= $this->generateTestFooter();

        return $code;
    }

    /**
     * Generate test header with describe and beforeEach blocks
     */
    protected function generateTestHeader(TestCase $testCase, array $options = []): string
    {
        $testName = $this->escapeString($testCase->name);
        $description = $testCase->description ? $this->escapeString($testCase->description) : '';
        
        $code = "describe('{$testName}', () => {\n";
        
        if ($description) {
            $code .= "  // {$description}\n\n";
        }

        // Get first URL from events if available
        $firstEvent = $testCase->savedEvents()->orderBy('created_at')->first();
        $baseUrl = $firstEvent->url ?? 'https://example.com';
        
        // Extract base URL (remove path and query string)
        $parsedUrl = parse_url($baseUrl);
        $baseUrlClean = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? 'example.com');

        $code .= "  beforeEach(() => {\n";
        $code .= "    // Visit the base URL\n";
        $code .= "    cy.visit('{$baseUrlClean}');\n";
        $code .= "  });\n\n";

        return $code;
    }

    /**
     * Generate main test body from events
     */
    protected function generateTestBody(Collection $events, array $options = []): string
    {
        $code = "  it('should execute recorded test case', () => {\n";
        
        $currentUrl = null;
        $stepNumber = 1;

        foreach ($events as $event) {
            // Add URL navigation if URL changed
            if ($event->url && $event->url !== $currentUrl) {
                $currentUrl = $event->url;
                $code .= "\n    // Step {$stepNumber}: Navigate to {$event->url}\n";
                $code .= "    cy.visit('{$event->url}');\n";
                $stepNumber++;
            }

            // Generate command for event
            $command = $this->generateCommandForEvent($event, $stepNumber, $options);
            if ($command) {
                $code .= $command;
                $stepNumber++;
            }
        }

        $code .= "  });\n";
        return $code;
    }

    /**
     * Generate Cypress command for a single event
     */
    protected function generateCommandForEvent(TestCaseEvent $event, int $stepNumber, array $options = []): string
    {
        $eventType = $event->event_type;
        $selector = $this->selectorOptimizer->optimizeSelector($event);
        
        $code = "\n    // Step {$stepNumber}: {$this->getEventDescription($event)}\n";

        switch ($eventType) {
            case 'click':
                $code .= "    cy.get('{$selector}').click();\n";
                break;

            case 'input':
            case 'change':
                $value = $this->escapeString($event->value ?? '');
                if ($event->tag_name === 'SELECT') {
                    $code .= "    cy.get('{$selector}').select('{$value}');\n";
                } else {
                    $code .= "    cy.get('{$selector}').clear().type('{$value}');\n";
                }
                break;

            case 'submit':
                $code .= "    cy.get('{$selector}').submit();\n";
                break;

            case 'focus':
                $code .= "    cy.get('{$selector}').focus();\n";
                break;

            case 'blur':
                $code .= "    cy.get('{$selector}').blur();\n";
                break;

            case 'dblclick':
                $code .= "    cy.get('{$selector}').dblclick();\n";
                break;

            case 'rightclick':
                $code .= "    cy.get('{$selector}').rightclick();\n";
                break;

            case 'hover':
                $code .= "    cy.get('{$selector}').trigger('mouseover');\n";
                break;

            case 'keypress':
            case 'keydown':
                if ($event->value) {
                    $key = $this->mapKeyToCypress($event->value);
                    $code .= "    cy.get('{$selector}').type('{$key}');\n";
                }
                break;

            case 'scroll':
                $code .= "    cy.scrollTo(0, {$event->value});\n";
                break;

            default:
                $code .= "    // Unsupported event type: {$eventType}\n";
                break;
        }

        // Add assertion if configured
        if ($options['add_assertions'] ?? false) {
            $code .= $this->generateAssertion($event);
        }

        return $code;
    }

    /**
     * Generate assertion for event
     */
    protected function generateAssertion(TestCaseEvent $event): string
    {
        $selector = $this->selectorOptimizer->optimizeSelector($event);
        
        switch ($event->event_type) {
            case 'click':
                return "    cy.get('{$selector}').should('be.visible');\n";
            
            case 'input':
            case 'change':
                $value = $this->escapeString($event->value ?? '');
                return "    cy.get('{$selector}').should('have.value', '{$value}');\n";
            
            default:
                return '';
        }
    }

    /**
     * Get human-readable description for event
     */
    protected function getEventDescription(TestCaseEvent $event): string
    {
        $eventType = ucfirst($event->event_type);
        $tagName = strtolower($event->tag_name ?? 'element');
        $innerText = $event->inner_text ? " '{$event->inner_text}'" : '';
        
        switch ($event->event_type) {
            case 'click':
                return "Click on {$tagName}{$innerText}";
            case 'input':
            case 'change':
                return "Enter value into {$tagName}";
            case 'submit':
                return "Submit {$tagName}";
            default:
                return "{$eventType} on {$tagName}";
        }
    }

    /**
     * Generate test footer
     */
    protected function generateTestFooter(): string
    {
        return "});\n";
    }

    /**
     * Generate empty test template
     */
    protected function generateEmptyTestTemplate(TestCase $testCase): string
    {
        $testName = $this->escapeString($testCase->name);
        
        return <<<CODE
describe('{$testName}', () => {
  beforeEach(() => {
    cy.visit('https://example.com');
  });

  it('should execute test case', () => {
    // No events recorded yet
    // Start recording events to generate test code
  });
});
CODE;
    }

    /**
     * Generate Playwright code (alternative format)
     */
    public function generatePlaywrightCode(TestCase $testCase, array $options = []): string
    {
        $events = $testCase->savedEvents()->orderBy('created_at')->get();
        
        if ($events->isEmpty()) {
            return $this->generateEmptyPlaywrightTemplate($testCase);
        }

        $testName = $this->escapeString($testCase->name);
        
        $code = "import { test, expect } from '@playwright/test';\n\n";
        $code .= "test('{$testName}', async ({ page }) => {\n";

        $currentUrl = null;

        foreach ($events as $event) {
            // Add URL navigation if URL changed
            if ($event->url && $event->url !== $currentUrl) {
                $currentUrl = $event->url;
                $code .= "  await page.goto('{$event->url}');\n";
            }

            // Generate Playwright command
            $command = $this->generatePlaywrightCommand($event, $options);
            if ($command) {
                $code .= $command;
            }
        }

        $code .= "});\n";
        return $code;
    }

    /**
     * Generate Playwright command for event
     */
    protected function generatePlaywrightCommand(TestCaseEvent $event, array $options = []): string
    {
        $selector = $this->selectorOptimizer->optimizeSelector($event);
        
        switch ($event->event_type) {
            case 'click':
                return "  await page.locator('{$selector}').click();\n";
            
            case 'input':
            case 'change':
                $value = $this->escapeString($event->value ?? '');
                return "  await page.locator('{$selector}').fill('{$value}');\n";
            
            case 'submit':
                return "  await page.locator('{$selector}').press('Enter');\n";
            
            default:
                return '';
        }
    }

    /**
     * Generate empty Playwright template
     */
    protected function generateEmptyPlaywrightTemplate(TestCase $testCase): string
    {
        $testName = $this->escapeString($testCase->name);
        
        return <<<CODE
import { test, expect } from '@playwright/test';

test('{$testName}', async ({ page }) => {
  await page.goto('https://example.com');
  
  // No events recorded yet
  // Start recording events to generate test code
});
CODE;
    }

    /**
     * Map keyboard keys to Cypress format
     */
    protected function mapKeyToCypress(string $key): string
    {
        $keyMap = [
            'Enter' => '{enter}',
            'Tab' => '{tab}',
            'Escape' => '{esc}',
            'Backspace' => '{backspace}',
            'Delete' => '{del}',
            'ArrowUp' => '{uparrow}',
            'ArrowDown' => '{downarrow}',
            'ArrowLeft' => '{leftarrow}',
            'ArrowRight' => '{rightarrow}',
        ];

        return $keyMap[$key] ?? $key;
    }

    /**
     * Escape string for use in generated code
     */
    protected function escapeString(string $string): string
    {
        return addslashes($string);
    }

    /**
     * Generate code with AI enhancements
     */
    public function generateWithAI(TestCase $testCase, array $options = []): string
    {
        // Base code generation
        $code = $this->generateCypressCode($testCase, $options);

        // Add AI-generated comments and suggestions
        if ($options['ai_enhance'] ?? false) {
            // This can be extended to integrate with AI module
            $code = $this->addAIComments($code, $testCase);
        }

        return $code;
    }

    /**
     * Add AI-generated comments
     */
    protected function addAIComments(string $code, TestCase $testCase): string
    {
        // Placeholder for AI integration
        // This can call the AI module to generate intelligent comments
        return $code;
    }
}
