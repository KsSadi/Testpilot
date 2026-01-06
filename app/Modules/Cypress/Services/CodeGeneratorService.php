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
     * Generate code for cross-origin block
     */
    protected function generateCrossOriginBlock(string $domain, array $events): string
    {
        $code = "    // Handle cross-origin authentication on {$domain}\n";
        $code .= "    cy.origin('https://{$domain}', () => {\n";
        $code .= "      // Suppress uncaught exceptions from third-party OAuth pages\n";
        $code .= "      // Common errors: 'baseUrl already declared', 'ResizeObserver loop', etc.\n";
        $code .= "      cy.on('uncaught:exception', (err) => {\n";
        $code .= "        // Return false to prevent Cypress from failing the test\n";
        $code .= "        console.log('Cross-origin exception suppressed:', err.message);\n";
        $code .= "        return false;\n";
        $code .= "      });\n\n";
        
        foreach ($events as $event) {
            $eventType = $event['type'] ?? 'unknown';
            $selector = $event['selector'] ?? '';
            $value = $event['value'] ?? '';
            
            switch ($eventType) {
                case 'pageload':
                    // Skip pageload events
                    break;
                    
                case 'click':
                    if ($selector) {
                        // Extract ID selector
                        if (preg_match('/^#(.+)$/', $selector, $matches)) {
                            $id = addslashes($matches[1]);
                            $code .= "      cy.get('[id=\"{$id}\"]').click({ force: true });\n";
                            $code .= "      cy.wait(2000);\n";
                        }
                        // TEXT selector
                        elseif (preg_match('/^TEXT:([^:]+):(.+)$/', $selector, $matches)) {
                            $tag = $matches[1];
                            $text = addslashes($matches[2]);
                            $code .= "      cy.contains('{$tag}', '{$text}').click({ force: true });\n";
                            $code .= "      cy.wait(2000);\n";
                        }
                        // XPATH selector
                        elseif (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                            $xpath = addslashes($matches[1]);
                            $code .= "      cy.xpath('{$xpath}').first().click({ force: true });\n";
                            $code .= "      cy.wait(2000);\n";
                        }
                        // Other selectors (name, aria-label, etc.)
                        else {
                            $escapedSelector = addslashes($selector);
                            $code .= "      cy.get('{$escapedSelector}').click({ force: true });\n";
                            $code .= "      cy.wait(2000);\n";
                        }
                    }
                    break;
                    
                case 'input':
                    if ($selector && $value !== '') {
                        // Extract ID selector
                        if (preg_match('/^#(.+)$/', $selector, $matches)) {
                            $id = addslashes($matches[1]);
                            $escapedValue = addslashes($value);
                            $code .= "      cy.get('[id=\"{$id}\"]').type('{$escapedValue}');\n";
                            $code .= "      cy.wait(2000);\n";
                        }
                        // name attribute
                        elseif (preg_match('/^\[name="([^"]+)"\]$/', $selector, $matches)) {
                            $name = addslashes($matches[1]);
                            $escapedValue = addslashes($value);
                            $code .= "      cy.get('[name=\"{$name}\"]').type('{$escapedValue}');\n";
                            $code .= "      cy.wait(2000);\n";
                        }
                        // XPATH selector
                        elseif (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                            $xpath = addslashes($matches[1]);
                            $escapedValue = addslashes($value);
                            $code .= "      cy.xpath('{$xpath}').first().type('{$escapedValue}');\n";
                            $code .= "      cy.wait(2000);\n";
                        }
                        // Other selectors
                        else {
                            $escapedSelector = addslashes($selector);
                            $escapedValue = addslashes($value);
                            $code .= "      cy.get('{$escapedSelector}').type('{$escapedValue}');\n";
                            $code .= "      cy.wait(2000);\n";
                        }
                    }
                    break;
                    
                case 'select':
                case 'change':
                    if ($selector && $value !== '') {
                        if (preg_match('/^#(.+)$/', $selector, $matches)) {
                            $id = addslashes($matches[1]);
                            $escapedValue = addslashes($value);
                            $code .= "      cy.get('[id=\"{$id}\"]').select('{$escapedValue}');\n";
                            $code .= "      cy.wait(2000);\n";
                        } else {
                            $escapedSelector = addslashes($selector);
                            $escapedValue = addslashes($value);
                            $code .= "      cy.get('{$escapedSelector}').select('{$escapedValue}');\n";
                            $code .= "      cy.wait(2000);\n";
                        }
                    }
                    break;
            }
        }
        
        $code .= "    });\n\n";
        return $code;
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
        
        // Check if we need XPath support or file uploads
        $needsXPath = false;
        $needsFileUpload = false;
        $domains = [];
        foreach ($events as $event) {
            if (isset($event['selector']) && strpos($event['selector'], 'XPATH:') === 0) {
                $needsXPath = true;
            }
            // Check for file uploads
            if (isset($event['inputType']) && $event['inputType'] === 'file') {
                $needsFileUpload = true;
            }
            if (isset($event['value']) && strpos($event['value'], 'fakepath') !== false) {
                $needsFileUpload = true;
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
        $mainDomain = $domains[0] ?? null;
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
        
        // Clean the initial URL - remove callback paths and query parameters
        if ($initialUrl) {
            $parsedUrl = parse_url($initialUrl);
            $path = $parsedUrl['path'] ?? '/';
            
            // Remove authentication callback paths
            if (strpos($path, '/keycloak/callback') !== false ||
                strpos($path, '/auth/callback') !== false ||
                strpos($path, '/oauth/callback') !== false ||
                strpos($path, '/saml/callback') !== false) {
                $path = '/';
            }
            
            // Rebuild clean URL (scheme + host + clean path, no query params)
            $initialUrl = ($parsedUrl['scheme'] ?? 'https') . '://' . 
                          ($parsedUrl['host'] ?? '') . 
                          $path;
        }

        // Get cross-origin domains (non-main domains)
        $crossOriginDomains = array_filter($domains, fn($d) => $d !== $mainDomain);

        $code = "describe('Recorded Test', () => {\n";
        
        // Add global uncaught exception handler
        $code .= "  // Global handler to suppress third-party script errors\n";
        $code .= "  Cypress.on('uncaught:exception', (err, runnable) => {\n";
        $code .= "    const ignoredErrors = [\n";
        $code .= "      'baseUrl', 'has already been declared', 'ResizeObserver loop',\n";
        $code .= "      'Script error', 'NetworkError', 'Load failed', 'cancelled', 'ChunkLoadError'\n";
        $code .= "    ];\n";
        $code .= "    if (ignoredErrors.some(msg => err.message.includes(msg))) {\n";
        $code .= "      console.log('Suppressed error:', err.message);\n";
        $code .= "      return false;\n";
        $code .= "    }\n";
        $code .= "    return true;\n";
        $code .= "  });\n\n";

        // Pre-configure cross-origin error handlers BEFORE any navigation
        if (!empty($crossOriginDomains)) {
            $code .= "  // ═══════════════════════════════════════════════════════════════\n";
            $code .= "  // PRE-CONFIGURE CROSS-ORIGIN ERROR HANDLERS\n";
            $code .= "  // Must be set up BEFORE navigation to these OAuth/SSO domains\n";
            $code .= "  // ═══════════════════════════════════════════════════════════════\n";
            foreach ($crossOriginDomains as $crossDomain) {
                $code .= "  before(() => {\n";
                $code .= "    cy.origin('https://{$crossDomain}', () => {\n";
                $code .= "      Cypress.on('uncaught:exception', (err) => {\n";
                $code .= "        console.log('Suppressed cross-origin error:', err.message);\n";
                $code .= "        return false;\n";
                $code .= "      });\n";
                $code .= "    });\n";
                $code .= "  });\n\n";
            }
        }

        $code .= "  it('should perform recorded actions', () => {\n";
        
        // Add initial visit only if we have a URL
        if ($initialUrl) {
            $code .= "    cy.visit('{$initialUrl}');\n\n";
        }
        
        // Add modal check at the beginning (handles modals that appear on page load)
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
        
        if ($needsFileUpload) {
            $code .= "    // Note: File uploads require files to be placed in cypress/fixtures/ folder\n";
            $code .= "    // Place your test files (e.g., demo.pdf, test.jpg) in the fixtures folder\n\n";
        }

        $lastUrl = $initialUrl;
        $visitedUrls = [$initialUrl];
        
        // Determine main domain (first domain)
        $mainDomain = $domains[0] ?? null;
        
        // Group events by domain for cross-origin handling
        if ($hasCrossOrigin && count($domains) > 1) {
            // Process events in order, grouping consecutive same-domain events
            $currentDomain = $mainDomain;
            $eventGroups = [];
            $currentGroup = [];
            
            foreach ($events as $event) {
                $eventUrl = $event['url'] ?? '';
                $eventDomain = $eventUrl ? parse_url($eventUrl, PHP_URL_HOST) : $currentDomain;
                
                // If domain changes, save current group and start new one
                if ($eventDomain !== $currentDomain && !empty($currentGroup)) {
                    $eventGroups[] = [
                        'domain' => $currentDomain,
                        'events' => $currentGroup
                    ];
                    $currentGroup = [];
                    $currentDomain = $eventDomain;
                }
                
                $currentGroup[] = $event;
            }
            
            // Add last group
            if (!empty($currentGroup)) {
                $eventGroups[] = [
                    'domain' => $currentDomain,
                    'events' => $currentGroup
                ];
            }
            
            // Generate code for each group
            foreach ($eventGroups as $group) {
                $groupDomain = $group['domain'];
                $groupEvents = $group['events'];
                
                if ($groupDomain === $mainDomain) {
                    // Main domain - generate normal code
                    foreach ($groupEvents as $event) {
                        $code .= $this->generateEventCode($event);
                    }
                } else {
                    // Cross-origin domain - wrap in cy.origin()
                    $code .= $this->generateCrossOriginBlock($groupDomain, $groupEvents);
                }
            }
        } else {
            // No cross-origin, process all events normally
            foreach ($events as $event) {
                $code .= $this->generateEventCode($event);
            }
        }

        $code .= "  });\n";
        $code .= "});\n";

        return $code;
    }
    
    /**
     * Generate code for a single event
     */
    protected function generateEventCode(array $event): string
    {
        $code = '';
        $eventType = $event['type'] ?? 'unknown';
        $selector = $event['selector'] ?? '';
        $value = $event['value'] ?? '';
        $url = $event['url'] ?? '';
        
        switch ($eventType) {
            case 'pageload':
                break;

            case 'click':
                if ($selector) {
                    $code .= $this->generateClickCommand($selector, $event);
                }
                break;

            case 'input':
                if ($selector && $value !== '') {
                    $inputType = $event['inputType'] ?? '';
                    $tagName = $event['tagName'] ?? '';
                    if (in_array($inputType, ['radio', 'checkbox'])) {
                        $code .= $this->generateClickCommand($selector, $event);
                    } else {
                        $code .= $this->generateInputCommand($selector, $value, $tagName, $inputType);
                    }
                }
                break;

            case 'change':
                if ($selector && isset($event['inputType'])) {
                    $inputType = $event['inputType'];
                    if (in_array($inputType, ['radio', 'checkbox'])) {
                        $code .= $this->generateClickCommand($selector, $event);
                    } elseif ($inputType === 'select-one' || strtolower($event['tagName'] ?? '') === 'select') {
                        $code .= $this->generateSelectCommand($selector, $value);
                    }
                }
                break;

            case 'keypress':
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
                break;
        }
        
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
    protected function generateClickCommand(string $selector, array $event = []): string
    {
        // Don't generate click commands for select elements - they should use .select() instead
        $tagName = strtolower($event['tagName'] ?? '');
        $inputType = $event['inputType'] ?? '';
        
        if ($tagName === 'select' || $inputType === 'select-one' || $inputType === 'select-multiple') {
            // Skip - this should be handled by change event with generateSelectCommand
            return '';
        }
        
        // Check if this is a save_as_draft button that needs fallback
        $needsFallback = false;
        $fallbackSelector = '';
        
        if (preg_match('/^#(.+)$/', $selector, $matches)) {
            $id = $matches[1];
            if ($id === 'save_as_draft' || strpos($id, 'save') !== false && strpos($id, 'draft') !== false) {
                $needsFallback = true;
                $fallbackSelector = '#new_application';
            }
        }
        
        // Handle TEXT: prefix (text-based selector for buttons/links)
        if (preg_match('/^TEXT:([^:]+):(.+)$/s', $selector, $matches)) {
            $tag = trim($matches[1]);
            $text = trim($matches[2]);
            // Extract just the first line or key text to avoid multi-line issues
            $textLines = explode("\n", $text);
            $cleanText = trim($textLines[0]);
            
            // If text is too long or empty, just use tag selector
            if (strlen($cleanText) > 50 || empty($cleanText)) {
                return "    cy.get('{$tag}').first().click({ force: true });\n" .
                       "    cy.wait(2000);\n";
            }
            
            $escapedText = addslashes($cleanText);
            // Add { force: true } to handle display:none elements
            return "    cy.contains('{$tag}', '{$escapedText}').click({ force: true });\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Handle XPATH: prefix (last resort)
        if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
            $xpath = $matches[1];
            $escapedXpath = addslashes($xpath);
            // Add .first() to handle multiple matches, { force: true } for hidden elements
            return "    cy.xpath('{$escapedXpath}').first().click({ force: true });\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Handle ID selector (starts with #)
        if (preg_match('/^#(.+)$/', $selector, $matches)) {
            $id = $matches[1];
            $escapedId = addslashes($id);
            
            // Check if this is a date picker cell (appears in calendar popup)
            if (preg_match('/^cell\d+-.*_date$/i', $id)) {
                // Date picker cells need conditional check (they're in popups)
                return "    cy.get('body').then(\$body => {\n" .
                       "      if (\$body.find('[id=\"{$escapedId}\"]').length > 0) {\n" .
                       "        cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).click({ force: true });\n" .
                       "        cy.wait(2000);\n" .
                       "      }\n" .
                       "    });\n";
            }
            
            // Check if this is an optional element (modal, popup, notification, etc.)
            // These elements might not always appear, so wrap in conditional
            if (preg_match('/(modal|popup|dialog|alert|notification|toast|banner)/i', $id)) {
                return "    cy.get('body').then(\$body => {\n" .
                       "      if (\$body.find('[id=\"{$escapedId}\"]').length > 0) {\n" .
                       "        cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).click({ force: true });\n" .
                       "        cy.wait(2000);\n" .
                       "      }\n" .
                       "    });\n";
            }
            
            // Add { force: true } for elements that might be hidden
            return "    cy.get('[id=\"{$escapedId}\"]').click({ force: true });\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Fallback: Try to extract tag from TEXT selectors that didn't match regex
        if (strpos($selector, 'TEXT:') === 0) {
            $parts = explode(':', $selector, 3);
            if (count($parts) >= 2) {
                $tag = $parts[1] ?? 'a';
                return "    cy.get('{$tag}').first().click({ force: true });\n" .
                       "    cy.wait(2000);\n";
            }
        }
        
        // Fallback for class-based or other CSS selectors
        $escapedSelector = addslashes($selector);
        // Remove any newlines that might break the code
        $escapedSelector = preg_replace('/[\r\n]+/', ' ', $escapedSelector);
        return "    // WARNING: Unexpected selector format\n" .
               "    cy.get('body').then(\$body => {\n" .
               "      if (\$body.find('" . str_replace("'", "\\'", preg_replace('/[\r\n]+/', ' ', $selector)) . "').length > 0) {\n" .
               "        cy.get('{$escapedSelector}', { timeout: 15000 }).first().click({ force: true });\n" .
               "        cy.wait(2000);\n" .
               "      }\n" .
               "    });\n";
    }
    
    /**
     * Generate Cypress input/type command with ID or XPath selector
     */
    protected function generateInputCommand(string $selector, $value, string $tagName = '', string $inputType = ''): string
    {
        $escapedValue = addslashes($value);
        
        // If it's a select element, use select command instead
        if (strtolower($tagName) === 'select') {
            return $this->generateSelectCommand($selector, $value);
        }
        
        // If it's a file input, use selectFile command
        if (strtolower($inputType) === 'file' || strtolower($tagName) === 'file') {
            return $this->generateFileUploadCommand($selector, $value);
        }
        
        // Handle TEXT: prefix (shouldn't be used for input, but handle it anyway)
        if (preg_match('/^TEXT:([^:]+):(.+)$/', $selector, $matches)) {
            $tag = $matches[1];
            $text = addslashes($matches[2]);
            return "    cy.contains('{$tag}', '{$text}').clear().type('{$escapedValue}');\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Handle XPATH: prefix
        if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
            $xpath = addslashes($matches[1]);
            return "    cy.xpath('{$xpath}').first().clear().type('{$escapedValue}');\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Handle ID selector (starts with #) for input
        if (preg_match('/^#(.+)$/', $selector, $matches)) {
            $id = $matches[1];
            $escapedId = addslashes($id);
            return "    cy.get('[id=\"{$escapedId}\"]').type('{$escapedValue}');\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Fallback
        $escapedSelector = addslashes($selector);
        return "    cy.get('{$escapedSelector}').type('{$escapedValue}');\n" .
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
            return "    cy.xpath('{$xpath}').first().select('{$escapedValue}');\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Handle ID selector
        if (preg_match('/^#(.+)$/', $selector, $matches)) {
            $id = $matches[1];
            $escapedId = addslashes($id);
            return "    cy.get('[id=\"{$escapedId}\"]').select('{$escapedValue}');\n" .
                   "    cy.wait(2000);\n";
        }
        
        // Fallback
        $escapedSelector = addslashes($selector);
        return "    cy.get('{$escapedSelector}').select('{$escapedValue}');\n" .
               "    cy.wait(2000);\n";
    }

    /**
     * Generate Cypress file upload command for file input elements
     */
    protected function generateFileUploadCommand(string $selector, $value): string
    {
        // Extract filename from C:\fakepath\filename.ext or just filename.ext
        $filename = basename($value);
        $escapedFilename = addslashes($filename);
        
        // Handle XPATH: prefix
        if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
            $xpath = addslashes($matches[1]);
            return "    cy.xpath('{$xpath}', { timeout: 15000 }).then(\$el => {\n" .
                   "      if (\$el.length > 0) {\n" .
                   "        cy.xpath('{$xpath}').first().selectFile('cypress/fixtures/{$escapedFilename}', { force: true });\n" .
                   "        cy.wait(2000);\n" .
                   "      }\n" .
                   "    });\n";
        }
        
        // Handle ID selector (starts with #)
        if (preg_match('/^#(.+)$/', $selector, $matches)) {
            $id = $matches[1];
            $escapedId = addslashes($id);
            return "    cy.get('body').then(\$body => {\n" .
                   "      if (\$body.find('[id=\"{$id}\"]').length > 0) {\n" .
                   "        cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).first().selectFile('cypress/fixtures/{$escapedFilename}', { force: true });\n" .
                   "        cy.wait(2000);\n" .
                   "      }\n" .
                   "    });\n";
        }
        
        // Fallback for any other selector format
        $escapedSelector = addslashes($selector);
        return "    // File upload\n" .
               "    cy.get('body').then(\$body => {\n" .
               "      if (\$body.find('" . str_replace("'", "\\'", $selector) . "').length > 0) {\n" .
               "        cy.get('{$escapedSelector}', { timeout: 15000 }).first().selectFile('cypress/fixtures/{$escapedFilename}', { force: true });\n" .
               "        cy.wait(2000);\n" .
               "      }\n" .
               "    });\n";
    }

    /**
     * Deduplicate consecutive events on same element
     */
    protected function deduplicateEvents(array $events): array
    {
        $deduplicated = [];
        $lastEvent = null;
        $hasLogout = false;

        foreach ($events as $event) {
            $eventType = $event['type'] ?? '';
            
            // Detect logout action
            if ($eventType === 'click') {
                $text = strtolower($event['text'] ?? '');
                if (strpos($text, 'logout') !== false || strpos($text, 'log out') !== false) {
                    $hasLogout = true;
                }
            }
            
            // After logout detected, skip ALL login-related inputs and clicks
            if ($hasLogout) {
                $selector = strtolower($event['selector'] ?? '');
                $text = strtolower($event['text'] ?? '');
                $inputType = strtolower($event['inputType'] ?? '');
                
                // Skip login inputs
                if ($eventType === 'input' && (
                    strpos($selector, 'password') !== false || 
                    strpos($selector, 'identifier') !== false || 
                    strpos($selector, 'email') !== false ||
                    strpos($selector, 'username') !== false ||
                    $inputType === 'password' ||
                    $inputType === 'email')) {
                    continue;
                }
                
                // Skip login/next button clicks
                if ($eventType === 'click' && (
                    strpos($text, 'login') !== false ||
                    strpos($text, 'log in') !== false ||
                    strpos($text, 'sign in') !== false ||
                    strpos($text, 'next') !== false ||
                    strpos($selector, 'login_btn') !== false ||
                    strpos($selector, 'next_btn') !== false ||
                    strpos($selector, 'signin') !== false)) {
                    continue;
                }
            }
            
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
            
            // Skip clicks on select elements entirely - they should trigger change events instead
            if ($eventType === 'click') {
                $tagName = strtolower($event['tagName'] ?? '');
                $inputType = $event['inputType'] ?? '';
                if ($tagName === 'select' || $inputType === 'select-one' || $inputType === 'select-multiple') {
                    // Don't add click events for select elements
                    continue;
                }
            }
            
            // Skip duplicate clicks on same element within short time
            if ($lastEvent &&
                ($lastEvent['type'] ?? '') === 'click' &&
                $eventType === 'click' &&
                ($event['selector'] ?? '') === ($lastEvent['selector'] ?? '')) {
                continue;
            }
            
            // Skip duplicate clicks on same element (check ALL previous events)
            if ($eventType === 'click' && count($deduplicated) > 0) {
                foreach ($deduplicated as $prevEvent) {
                    if (($prevEvent['type'] ?? '') === 'click' &&
                        ($prevEvent['selector'] ?? '') === ($event['selector'] ?? '')) {
                        continue 2; // Skip this duplicate click
                    }
                }
            }
            
            // Skip duplicate input on same element (check ALL previous events)
            if ($eventType === 'input' && count($deduplicated) > 0) {
                foreach ($deduplicated as $prevEvent) {
                    if (($prevEvent['type'] ?? '') === 'input' &&
                        ($prevEvent['selector'] ?? '') === ($event['selector'] ?? '') &&
                        ($prevEvent['value'] ?? '') === ($event['value'] ?? '')) {
                        continue 2; // Skip this duplicate input
                    }
                }
            }
            
            // Skip duplicate keypress events (same key on same element)
            if ($lastEvent &&
                ($lastEvent['type'] ?? '') === 'keypress' &&
                $eventType === 'keypress' &&
                ($event['selector'] ?? '') === ($lastEvent['selector'] ?? '') &&
                ($event['key'] ?? '') === ($lastEvent['key'] ?? '')) {
                continue;
            }
            
            // Skip duplicate submit events on same element
            if ($lastEvent &&
                ($lastEvent['type'] ?? '') === 'submit' &&
                $eventType === 'submit' &&
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
                    $code .= "    cy.xpath('{$xpath}', { timeout: 15000 }).then(\$el => {\n";
                    $code .= "      if (\$el.length > 0) {\n";
                    $code .= "        cy.xpath('{$xpath}').first().click({ force: true });\n";
                    $code .= "        cy.wait(2000);\n";
                    $code .= "      }\n";
                    $code .= "    });\n";
                } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                    $id = $matches[1];
                    $escapedId = addslashes($id);
                    $code .= "    cy.get('body').then(\$body => {\n";
                    $code .= "      if (\$body.find('[id=\"{$id}\"]').length > 0) {\n";
                    $code .= "        cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).first().click({ force: true });\n";
                    $code .= "        cy.wait(2000);\n";
                    $code .= "      }\n";
                    $code .= "    });\n";
                } else {
                    $escapedSelector = addslashes($selector);
                    $code .= "    cy.get('body').then(\$body => {\n";
                    $code .= "      if (\$body.find('" . str_replace("'", "\\'", $selector) . "').length > 0) {\n";
                    $code .= "        cy.get('{$escapedSelector}', { timeout: 15000 }).first().click({ force: true });\n";
                    $code .= "        cy.wait(2000);\n";
                    $code .= "      }\n";
                    $code .= "    });\n";
                }
                break;

            case 'input':
            case 'change':
                $value = $this->escapeString($event->value ?? '');
                
                // Check if this is a file input
                if ($event->input_type === 'file' || strtolower($event->tag_name ?? '') === 'file') {
                    $filename = basename($value);
                    $escapedFilename = addslashes($filename);
                    
                    if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                        $xpath = addslashes($matches[1]);
                        $code .= "    cy.xpath('{$xpath}', { timeout: 15000 }).then(\$el => {\n";
                        $code .= "      if (\$el.length > 0) {\n";
                        $code .= "        cy.xpath('{$xpath}').first().selectFile('cypress/fixtures/{$escapedFilename}', { force: true });\n";
                        $code .= "        cy.wait(2000);\n";
                        $code .= "      }\n";
                        $code .= "    });\n";
                    } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                        $id = $matches[1];
                        $escapedId = addslashes($id);
                        $code .= "    cy.get('body').then(\$body => {\n";
                        $code .= "      if (\$body.find('[id=\"{$id}\"]').length > 0) {\n";
                        $code .= "        cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).first().selectFile('cypress/fixtures/{$escapedFilename}', { force: true });\n";
                        $code .= "        cy.wait(2000);\n";
                        $code .= "      }\n";
                        $code .= "    });\n";
                    } else {
                        $escapedSelector = addslashes($selector);
                        $code .= "    cy.get('body').then(\$body => {\n";
                        $code .= "      if (\$body.find('" . str_replace("'", "\\'", $selector) . "').length > 0) {\n";
                        $code .= "        cy.get('{$escapedSelector}', { timeout: 15000 }).first().selectFile('cypress/fixtures/{$escapedFilename}', { force: true });\n";
                        $code .= "        cy.wait(2000);\n";
                        $code .= "      }\n";
                        $code .= "    });\n";
                    }
                    break;
                }
                
                if ($event->tag_name === 'SELECT') {
                    // Use select for dropdown
                    if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                        $xpath = addslashes($matches[1]);
                        $code .= "    cy.xpath('{$xpath}', { timeout: 15000 }).then(\$el => {\n";
                        $code .= "      if (\$el.length > 0) {\n";
                        $code .= "        cy.xpath('{$xpath}').first().select('{$value}');\n";
                        $code .= "        cy.wait(2000);\n";
                        $code .= "      }\n";
                        $code .= "    });\n";
                    } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                        $id = $matches[1];
                        $escapedId = addslashes($id);
                        $code .= "    cy.get('body').then(\$body => {\n";
                        $code .= "      if (\$body.find('[id=\"{$id}\"]').length > 0) {\n";
                        $code .= "        cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).first().select('{$value}');\n";
                        $code .= "        cy.wait(2000);\n";
                        $code .= "      }\n";
                        $code .= "    });\n";
                    } else {
                        $escapedSelector = addslashes($selector);
                        $code .= "    cy.get('body').then(\$body => {\n";
                        $code .= "      if (\$body.find('" . str_replace("'", "\\'", $selector) . "').length > 0) {\n";
                        $code .= "        cy.get('{$escapedSelector}', { timeout: 15000 }).first().select('{$value}');\n";
                        $code .= "        cy.wait(2000);\n";
                        $code .= "      }\n";
                        $code .= "    });\n";
                    }
                } else {
                    // Use type for input/textarea
                    if (preg_match('/^XPATH:(.+)$/', $selector, $matches)) {
                        $xpath = addslashes($matches[1]);
                        $code .= "    cy.xpath('{$xpath}', { timeout: 15000 }).then(\$el => {\n";
                        $code .= "      if (\$el.length > 0) {\n";
                        $code .= "        cy.xpath('{$xpath}').first().clear().type('{$value}');\n";
                        $code .= "        cy.wait(2000);\n";
                        $code .= "      }\n";
                        $code .= "    });\n";
                    } elseif (preg_match('/^#(.+)$/', $selector, $matches)) {
                        $id = $matches[1];
                        $escapedId = addslashes($id);
                        $code .= "    cy.get('body').then(\$body => {\n";
                        $code .= "      if (\$body.find('[id=\"{$id}\"]').length > 0) {\n";
                        $code .= "        cy.get('[id=\"{$escapedId}\"]', { timeout: 15000 }).first().clear().type('{$value}');\n";
                        $code .= "        cy.wait(2000);\n";
                        $code .= "      }\n";
                        $code .= "    });\n";
                    } else {
                        $escapedSelector = addslashes($selector);
                        $code .= "    cy.get('body').then(\$body => {\n";
                        $code .= "      if (\$body.find('" . str_replace("'", "\\'", $selector) . "').length > 0) {\n";
                        $code .= "        cy.get('{$escapedSelector}', { timeout: 15000 }).first().clear().type('{$value}');\n";
                        $code .= "        cy.wait(2000);\n";
                        $code .= "      }\n";
                        $code .= "    });\n";
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
