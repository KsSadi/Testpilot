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
        
        // Check if we need XPath support
        $needsXPath = false;
        $domains = [];
        foreach ($events as $event) {
            if (isset($event['selector']) && strpos($event['selector'], 'XPATH:') === 0) {
                $needsXPath = true;
            }
            // Collect all domains for cross-origin detection
            if (isset($event['url']) && $event['url'] !== 'about:blank') {
                $domain = parse_url($event['url'], PHP_URL_HOST);
                if ($domain && !in_array($domain, $domains)) {
                    $domains[] = $domain;
                }
            }
        }
        
        // Detect if cross-origin navigation is needed
        $hasCrossOrigin = count($domains) > 1;
        $crossOriginDomain = null;
        if ($hasCrossOrigin && count($domains) >= 2) {
            // Assume second domain is the auth domain (id.oss.net.bd)
            $crossOriginDomain = $domains[1];
        }
        
        // Get the initial URL from the first event or recorder
        $initialUrl = '';
        foreach ($events as $event) {
            if (isset($event['url']) && $event['url'] !== 'about:blank') {
                $initialUrl = $event['url'];
                break;
            }
        }

        $code = "describe('Recorded Test', () => {\n";
        $code .= "  it('should perform recorded actions', () => {\n";
        
        // Add initial visit only if we have a URL
        if ($initialUrl) {
            $code .= "    cy.visit('{$initialUrl}');\n\n";
        }
        
        // Add ONE modal check at the beginning if needed
        $code .= "    // Close any open modals at start\n";
        $code .= "    cy.get('body').then(\$body => {\n";
        $code .= "      const modal = \$body.find('.modal.fade.in, .modal.show, .modal[style*=\"display: block\"]');\n";
        $code .= "      if (modal.length > 0) {\n";
        $code .= "        const closeBtn = modal.find('.close, button.close, [data-dismiss=\"modal\"]');\n";
        $code .= "        if (closeBtn.length > 0) {\n";
        $code .= "          closeBtn.first().click();\n";
        $code .= "          cy.wait(500);\n";
        $code .= "        }\n";
        $code .= "      }\n";
        $code .= "    });\n\n";
        
        if ($needsXPath) {
            $code .= "    // Note: cypress-xpath plugin required for XPath selectors\n";
            $code .= "    // Install: npm install -D cypress-xpath\n";
            $code .= "    // Add to support/e2e.js: require('cypress-xpath')\n\n";
        }

        $lastUrl = $initialUrl;
        $visitedUrls = [$initialUrl];
        $crossOriginAdded = false;
        
        foreach ($events as $event) {
            $eventType = $event['type'] ?? 'unknown';
            $selector = $event['selector'] ?? '';
            $value = $event['value'] ?? '';
            $url = $event['url'] ?? '';
            
            // Add cross-origin handler before navigation to auth domain (but after first click)
            if ($hasCrossOrigin && !$crossOriginAdded && $url && $crossOriginDomain) {
                $currentDomain = parse_url($url, PHP_URL_HOST);
                if ($currentDomain === $crossOriginDomain) {
                    $code .= "    // Cross-origin authentication handling\n";
                    $code .= "    cy.origin('{$crossOriginDomain}', () => {\n";
                    $code .= "      cy.on('uncaught:exception', (err) => {\n";
                    $code .= "        if (err.message.includes('baseUrl') || err.message.includes('Identifier')) {\n";
                    $code .= "          return false;\n";
                    $code .= "        }\n";
                    $code .= "        return true;\n";
                    $code .= "      });\n";
                    $code .= "    });\n\n";
                    $crossOriginAdded = true;
                }
            }

            switch ($eventType) {
                case 'pageload':
                    // Skip pageload events - already handled with initial visit
                    break;

                case 'click':
                    if ($selector) {
                        $code .= $this->generateClickCommand($selector);
                    }
                    break;

                case 'input':
                    if ($selector && $value !== '') {
                        // Check if it's a radio/checkbox based on event data
                        $inputType = $event['inputType'] ?? '';
                        $tagName = $event['tagName'] ?? '';
                        if (in_array($inputType, ['radio', 'checkbox'])) {
                            // Radio and checkbox should just be clicked, not typed into
                            $code .= $this->generateClickCommand($selector);
                        } else {
                            $code .= $this->generateInputCommand($selector, $value, $tagName);
                        }
                    }
                    break;

                case 'change':
                    // Handle change events for radio, checkbox, and select
                    if ($selector && isset($event['inputType'])) {
                        $inputType = $event['inputType'];
                        if (in_array($inputType, ['radio', 'checkbox'])) {
                            // Just click radio/checkbox
                            $code .= $this->generateClickCommand($selector);
                        } elseif ($inputType === 'select-one' || strtolower($event['tagName'] ?? '') === 'select') {
                            // Handle select dropdown
                            $code .= $this->generateSelectCommand($selector, $value);
                        }
                    }
                    break;

                case 'keypress':
                    // Handle Enter key press (often used for form submission)
                    if (isset($event['key']) && $event['key'] === 'Enter' && $selector) {
                        if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                            $xpath = addslashes($matches[1]);
                            $code .= "    cy.xpath('{$xpath}').type('{enter}');\n";
                        } else {
                            $escapedSelector = addslashes($selector);
                            $code .= "    cy.get('{$escapedSelector}').type('{enter}');\n";
                        }
                        $code .= "    cy.wait(2000);\n";
                    }
                    break;
                    
                case 'submit':
                    if ($selector) {
                        // Handle XPATH: prefix for submit
                        if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                            $xpath = addslashes($matches[1]);
                            $code .= "    cy.xpath('{$xpath}').submit();\n";
                        } else {
                            $escapedSelector = addslashes($selector);
                            $code .= "    cy.get('{$escapedSelector}').submit();\n";
                        }
                        $code .= "    cy.wait(2000);\n";
                    }
                    break;

                case 'navigation':
                    // Only add navigation if it's a different URL and not about:blank
                    if ($url && $url !== 'about:blank' && $url !== $lastUrl && !in_array($url, $visitedUrls)) {
                        $code .= "    cy.url().should('include', '" . parse_url($url, PHP_URL_PATH) . "');\n";
                        $visitedUrls[] = $url;
                        $lastUrl = $url;
                    }
                    break;
            }
        }

        $code .= "  });\n";
        $code .= "});\n";

        return $code;
    }
    
    /**
     * Wrap command with modal check (only once, not repetitively)
     */
    protected function generateModalCheckWrapper(string $command, bool $includeModalCheck = false): string
    {
        if (!$includeModalCheck) {
            return "    " . trim($command) . "\n";
        }
        
        return "    // Close any open modals first\n" .
               "    cy.get('body').then(\$body => {\n" .
               "      const modal = \$body.find('.modal.fade.in, .modal.show, .modal[style*=\"display: block\"]');\n" .
               "      if (modal.length > 0) {\n" .
               "        const closeBtn = modal.find('.close, button.close, [data-dismiss=\"modal\"]');\n" .
               "        if (closeBtn.length > 0) {\n" .
               "          closeBtn.first().click();\n" .
               "          cy.wait(500);\n" .
               "        }\n" .
               "      }\n" .
               "    });\n" .
               "    " . trim($command) . "\n";
    }

    /**
     * Generate Cypress click command with ID or XPath selector strategy
     */
    protected function generateClickCommand(string $selector): string
    {
        // Handle XPATH: prefix
        if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
            $xpath = $matches[1];
            $escapedXpath = addslashes($xpath);
            return "    cy.xpath('{$escapedXpath}', { timeout: 15000 }).should('be.visible').first().click();\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Handle ID selector (starts with #)
        if (preg_match('/^#(.+)$/', $selector, $matches)) {
            $id = $matches[1];
            $escapedId = addslashes($id);
            return "    cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).should('be.visible').first().click();\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Fallback for any other selector format (shouldn't happen with new strategy)
        $escapedSelector = addslashes($selector);
        return "    // WARNING: Unexpected selector format\n" .
               "    cy.get('{$escapedSelector}', { timeout: 15000 }).should('be.visible').first().click();\n" .
               "    cy.wait(2000);\n";
    }
    
    /**
     * Generate Cypress input/type command with ID or XPath selector
     */
    protected function generateInputCommand(string $selector, $value, string $tagName = ''): string
    {
        $escapedValue = addslashes($value);
        
        // If it's a select element, use select command instead
        if (strtolower($tagName) === 'select') {
            return $this->generateSelectCommand($selector, $value);
        }
        
        // Handle XPATH: prefix
        if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
            $xpath = addslashes($matches[1]);
            return "    cy.xpath('{$xpath}', { timeout: 15000 }).should('be.visible').first().clear().type('{$escapedValue}');\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Handle ID selector (starts with #)
        if (preg_match('/^#(.+)$/', $selector, $matches)) {
            $id = $matches[1];
            $escapedId = addslashes($id);
            return "    cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).should('be.visible').first().clear().type('{$escapedValue}');\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Fallback
        $escapedSelector = addslashes($selector);
        return "    // WARNING: Unexpected selector format\n" .
               "    cy.get('{$escapedSelector}', { timeout: 15000 }).should('be.visible').first().clear().type('{$escapedValue}');\n" .
               "    cy.wait(2000);\n";
    }
    
    /**
     * Generate Cypress select command for dropdown with ID or XPath selector
     */
    protected function generateSelectCommand(string $selector, $value): string
    {
        $escapedValue = addslashes($value);
        
        // Handle XPATH: prefix
        if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
            $xpath = addslashes($matches[1]);
            return "    cy.xpath('{$xpath}', { timeout: 15000 }).should('be.visible').first().select('{$escapedValue}');\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Handle ID selector (starts with #)
        if (preg_match('/^#(.+)$/', $selector, $matches)) {
            $id = $matches[1];
            $escapedId = addslashes($id);
            return "    cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).should('be.visible').first().select('{$escapedValue}');\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Fallback for any other selector format
        $escapedSelector = addslashes($selector);
        return "    // WARNING: Unexpected selector format\n" .
               "    cy.get('{$escapedSelector}', { timeout: 15000 }).should('be.visible').first().select('{$escapedValue}');\n" .
               "    cy.wait(2000);\n";
    }

    /**
     * Deduplicate consecutive events on same element
     */
    protected function deduplicateEvents(array $events): array
    {
        $deduplicated = [];
        $lastEvent = null;

        foreach ($events as $event) {
            $eventType = $event['type'] ?? '';
            
            // Skip all pageload events (handled separately)
            if ($eventType === 'pageload') {
                // But preserve the URL for initial visit
                if (empty($deduplicated)) {
                    $deduplicated[] = $event;
                }
                continue;
            }
            
            // Skip navigation events that are just redirects (same domain, automatic)
            if ($eventType === 'navigation') {
                // Only keep if it's a significant navigation
                $fromUrl = $event['fromUrl'] ?? '';
                $toUrl = $event['url'] ?? '';
                
                if ($fromUrl && $toUrl) {
                    $fromDomain = parse_url($fromUrl, PHP_URL_HOST);
                    $toDomain = parse_url($toUrl, PHP_URL_HOST);
                    
                    // Skip if same domain redirect (likely automatic)
                    if ($fromDomain === $toDomain) {
                        continue;
                    }
                }
            }

            // Merge consecutive input/change on same element - keep the last value
            if ($lastEvent && 
                in_array($eventType, ['input', 'change']) && 
                in_array($lastEvent['type'] ?? '', ['input', 'change']) &&
                ($event['selector'] ?? '') === ($lastEvent['selector'] ?? '')) {
                // Update the value in last event instead of adding new
                $deduplicated[count($deduplicated) - 1]['value'] = $event['value'];
                $deduplicated[count($deduplicated) - 1]['type'] = 'input'; // Prefer input
                $lastEvent = $deduplicated[count($deduplicated) - 1];
                continue;
            }

            // Skip click on input/select field if immediately followed by input/change (redundant)
            if ($lastEvent && 
                ($lastEvent['type'] ?? '') === 'click' && 
                in_array($eventType, ['input', 'change']) &&
                ($event['selector'] ?? '') === ($lastEvent['selector'] ?? '')) {
                // Remove the click from deduplicated array
                array_pop($deduplicated);
            }
            
            // Skip duplicate clicks on same element within short time
            if ($lastEvent &&
                ($lastEvent['type'] ?? '') === 'click' &&
                $eventType === 'click' &&
                ($event['selector'] ?? '') === ($lastEvent['selector'] ?? '')) {
                continue;
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
                // Use ID or XPath selector
                if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                    $xpath = addslashes($matches[1]);
                    $code .= "    cy.xpath('{$xpath}', { timeout: 15000 }).should('be.visible').click();\n";
                    $code .= "    cy.wait(2000);\n";
                } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                    $id = addslashes($matches[1]);
                    $code .= "    cy.get('[id=\"{$id}\"]', { timeout: 15000 }).should('be.visible').click();\n";
                    $code .= "    cy.wait(2000);\n";
                } else {
                    $escapedSelector = addslashes($selector);
                    $code .= "    cy.get('{$escapedSelector}', { timeout: 15000 }).should('be.visible').click();\n";
                    $code .= "    cy.wait(2000);\n";
                }
                break;

            case 'input':
            case 'change':
                $value = $this->escapeString($event->value ?? '');
                if ($event->tag_name === 'SELECT') {
                    // Use select for dropdown
                    if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                        $xpath = addslashes($matches[1]);
                        $code .= "    cy.xpath('{$xpath}', { timeout: 15000 }).should('be.visible').select('{$value}');\n";
                        $code .= "    cy.wait(2000);\n";
                    } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                        $id = addslashes($matches[1]);
                        $code .= "    cy.get('[id=\"{$id}\"]', { timeout: 15000 }).should('be.visible').select('{$value}');\n";
                        $code .= "    cy.wait(2000);\n";
                    } else {
                        $escapedSelector = addslashes($selector);
                        $code .= "    cy.get('{$escapedSelector}', { timeout: 15000 }).should('be.visible').select('{$value}');\n";
                        $code .= "    cy.wait(2000);\n";
                    }
                } else {
                    // Use type for input/textarea
                    if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                        $xpath = addslashes($matches[1]);
                        $code .= "    cy.xpath('{$xpath}', { timeout: 15000 }).should('be.visible').clear().type('{$value}');\n";
                        $code .= "    cy.wait(2000);\n";
                    } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                        $id = addslashes($matches[1]);
                        $code .= "    cy.get('[id=\"{$id}\"]', { timeout: 15000 }).should('be.visible').clear().type('{$value}');\n";
                        $code .= "    cy.wait(2000);\n";
                    } else {
                        $escapedSelector = addslashes($selector);
                        $code .= "    cy.get('{$escapedSelector}', { timeout: 15000 }).should('be.visible').clear().type('{$value}');\n";
                        $code .= "    cy.wait(2000);\n";
                    }
                }
                break;

            case 'submit':
                if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                    $xpath = addslashes($matches[1]);
                    $code .= "    cy.xpath('{$xpath}').submit();\n";
                    $code .= "    cy.wait(2000);\n";
                } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                    $id = addslashes($matches[1]);
                    $code .= "    cy.get('[id=\"{$id}\"]').submit();\n";
                    $code .= "    cy.wait(2000);\n";
                } else {
                    $escapedSelector = addslashes($selector);
                    $code .= "    cy.get('{$escapedSelector}').submit();\n";
                    $code .= "    cy.wait(2000);\n";
                }
                break;

            case 'focus':
                $escapedSelector = addslashes($selector);
                $code .= "    cy.get('{$escapedSelector}').focus();\n";
                break;

            case 'blur':
                $escapedSelector = addslashes($selector);
                $code .= "    cy.get('{$escapedSelector}').blur();\n";
                break;

            case 'dblclick':
                if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                    $xpath = addslashes($matches[1]);
                    $code .= "    cy.xpath('{$xpath}').dblclick();\n";
                    $code .= "    cy.wait(2000);\n";
                } else {
                    $escapedSelector = addslashes($selector);
                    $code .= "    cy.get('{$escapedSelector}').dblclick();\n";
                    $code .= "    cy.wait(2000);\n";
                }
                break;

            case 'rightclick':
                if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                    $xpath = addslashes($matches[1]);
                    $code .= "    cy.xpath('{$xpath}').rightclick();\n";
                    $code .= "    cy.wait(2000);\n";
                } else {
                    $escapedSelector = addslashes($selector);
                    $code .= "    cy.get('{$escapedSelector}').rightclick();\n";
                    $code .= "    cy.wait(2000);\n";
                }
                break;

            case 'hover':
                $escapedSelector = addslashes($selector);
                $code .= "    cy.get('{$escapedSelector}').trigger('mouseover');\n";
                break;

            case 'keypress':
            case 'keydown':
                if ($event->value) {
                    $key = $this->mapKeyToCypress($event->value);
                    $escapedSelector = addslashes($selector);
                    $code .= "    cy.get('{$escapedSelector}').type('{$key}');\n";
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
                // Use XPath or ID selector for assertions
                if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                    $xpath = addslashes($matches[1]);
                    return "    cy.xpath('{$xpath}').should('be.visible');\n";
                } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                    $id = addslashes($matches[1]);
                    return "    cy.get('[id=\"{$id}\"]').should('be.visible');\n";
                } else {
                    $escapedSelector = addslashes($selector);
                    return "    cy.get('{$escapedSelector}').should('be.visible');\n";
                }
            
            case 'input':
            case 'change':
                $value = $this->escapeString($event->value ?? '');
                if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                    $xpath = addslashes($matches[1]);
                    return "    cy.xpath('{$xpath}').should('have.value', '{$value}');\n";
                } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                    $id = addslashes($matches[1]);
                    return "    cy.get('[id=\"{$id}\"]').should('have.value', '{$value}');\n";
                } else {
                    $escapedSelector = addslashes($selector);
                    return "    cy.get('{$escapedSelector}').should('have.value', '{$value}');\n";
                }
            
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
        
        // Convert selector format for Playwright
        if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
            $xpath = addslashes($matches[1]);
            $playwrightSelector = "xpath={$xpath}";
        } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
            $id = addslashes($matches[1]);
            $playwrightSelector = "#" . $id;
        } else {
            $playwrightSelector = addslashes($selector);
        }
        
        switch ($event->event_type) {
            case 'click':
                return "  await page.locator('{$playwrightSelector}').click();\n";
            
            case 'input':
            case 'change':
                $value = $this->escapeString($event->value ?? '');
                return "  await page.locator('{$playwrightSelector}').fill('{$value}');\n";
            
            case 'submit':
                return "  await page.locator('{$playwrightSelector}').press('Enter');\n";
            
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
