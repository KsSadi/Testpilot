<?php

namespace App\Modules\Cypress\Services;

use App\Modules\Cypress\Models\TestCaseEvent;

class SelectorOptimizerService
{
    /**
     * Optimize selector for Cypress/Playwright usage
     * Priority: data-testid > data-cy > id > name > aria-label > class > xpath
     */
    public function optimizeSelector(TestCaseEvent $event): string
    {
        $attributes = $event->attributes ?? [];
        
        // Priority 1: data-testid attribute
        if (isset($attributes['data-testid'])) {
            return '[data-testid="' . $attributes['data-testid'] . '"]';
        }

        // Priority 2: data-cy attribute (Cypress specific)
        if (isset($attributes['data-cy'])) {
            return '[data-cy="' . $attributes['data-cy'] . '"]';
        }

        // Priority 3: ID attribute
        if (isset($attributes['id']) && !empty($attributes['id'])) {
            return '#' . $attributes['id'];
        }

        // Priority 4: Name attribute (for form elements)
        if (isset($attributes['name']) && !empty($attributes['name'])) {
            $tagName = strtolower($event->tag_name ?? '');
            if (in_array($tagName, ['input', 'select', 'textarea', 'button'])) {
                return '[name="' . $attributes['name'] . '"]';
            }
        }

        // Priority 5: aria-label (for accessibility)
        if (isset($attributes['aria-label']) && !empty($attributes['aria-label'])) {
            return '[aria-label="' . $attributes['aria-label'] . '"]';
        }

        // Priority 6: Type + Placeholder for inputs
        if (isset($attributes['type']) && isset($attributes['placeholder'])) {
            $type = $attributes['type'];
            $placeholder = $attributes['placeholder'];
            return 'input[type="' . $type . '"][placeholder="' . $placeholder . '"]';
        }

        // Priority 7: Button text content
        $tagName = strtolower($event->tag_name ?? '');
        if ($tagName === 'button' && !empty($event->inner_text)) {
            $text = trim($event->inner_text);
            if (strlen($text) < 50) { // Only use short text
                return 'button:contains("' . addslashes($text) . '")';
            }
        }

        // Priority 8: Link text for anchors
        if ($tagName === 'a' && !empty($event->inner_text)) {
            $text = trim($event->inner_text);
            if (strlen($text) < 50) {
                return 'a:contains("' . addslashes($text) . '")';
            }
        }

        // Priority 9: Class names (filter out dynamic/random classes)
        if (isset($attributes['class']) && !empty($attributes['class'])) {
            $classes = $this->filterStableClasses($attributes['class']);
            if (!empty($classes)) {
                return '.' . implode('.', $classes);
            }
        }

        // Priority 10: Fall back to stored selector or tag name
        if (!empty($event->selector)) {
            return $event->selector;
        }

        // Last resort: tag name
        return strtolower($event->tag_name ?? 'div');
    }

    /**
     * Filter out dynamic/unstable class names
     */
    protected function filterStableClasses(string $classString): array
    {
        $classes = explode(' ', $classString);
        $stableClasses = [];

        foreach ($classes as $class) {
            $class = trim($class);
            
            // Skip if empty
            if (empty($class)) {
                continue;
            }

            // Skip classes with random hashes or numbers
            if (preg_match('/^[a-z0-9]{8,}$/i', $class)) {
                continue; // Likely a hash
            }

            // Skip classes with timestamps or UUIDs
            if (preg_match('/\d{10,}/', $class)) {
                continue; // Contains timestamp
            }

            // Skip utility classes that are too generic
            if (in_array($class, ['active', 'disabled', 'hidden', 'show', 'hide'])) {
                continue;
            }

            $stableClasses[] = $class;
        }

        // Return max 3 classes to keep selector concise
        return array_slice($stableClasses, 0, 3);
    }

    /**
     * Generate role-based selector (Playwright style)
     */
    public function generateRoleSelector(TestCaseEvent $event): ?string
    {
        $tagName = strtolower($event->tag_name ?? '');
        $attributes = $event->attributes ?? [];

        // Map HTML elements to ARIA roles
        $roleMap = [
            'button' => 'button',
            'a' => 'link',
            'input' => $this->getInputRole($attributes['type'] ?? 'text'),
            'textarea' => 'textbox',
            'select' => 'combobox',
            'nav' => 'navigation',
            'main' => 'main',
            'header' => 'banner',
            'footer' => 'contentinfo',
        ];

        if (!isset($roleMap[$tagName])) {
            return null;
        }

        $role = $roleMap[$tagName];
        $name = $event->inner_text ?? $attributes['aria-label'] ?? null;

        if ($name && strlen($name) < 50) {
            return "role={$role}[name='{$name}']";
        }

        return "role={$role}";
    }

    /**
     * Get ARIA role for input type
     */
    protected function getInputRole(string $type): string
    {
        $typeRoleMap = [
            'button' => 'button',
            'checkbox' => 'checkbox',
            'radio' => 'radio',
            'text' => 'textbox',
            'email' => 'textbox',
            'password' => 'textbox',
            'search' => 'searchbox',
            'tel' => 'textbox',
            'url' => 'textbox',
        ];

        return $typeRoleMap[$type] ?? 'textbox';
    }

    /**
     * Suggest multiple selector options
     */
    public function suggestSelectors(TestCaseEvent $event): array
    {
        $selectors = [];

        // Add optimized selector (primary)
        $selectors['optimized'] = [
            'selector' => $this->optimizeSelector($event),
            'priority' => 'high',
            'description' => 'Recommended selector (stable and maintainable)'
        ];

        // Add role-based selector if available
        $roleSelector = $this->generateRoleSelector($event);
        if ($roleSelector) {
            $selectors['role_based'] = [
                'selector' => $roleSelector,
                'priority' => 'high',
                'description' => 'Accessibility-friendly selector'
            ];
        }

        // Add original selector if different
        if (!empty($event->selector) && $event->selector !== $selectors['optimized']['selector']) {
            $selectors['original'] = [
                'selector' => $event->selector,
                'priority' => 'medium',
                'description' => 'Original captured selector'
            ];
        }

        // Add text-based selector if applicable
        $tagName = strtolower($event->tag_name ?? '');
        if (in_array($tagName, ['button', 'a']) && !empty($event->inner_text)) {
            $text = trim($event->inner_text);
            if (strlen($text) < 50) {
                $selectors['text_based'] = [
                    'selector' => "{$tagName}:contains('{$text}')",
                    'priority' => 'medium',
                    'description' => 'Text-based selector'
                ];
            }
        }

        return $selectors;
    }

    /**
     * Validate selector strength
     */
    public function validateSelector(string $selector): array
    {
        $score = 0;
        $warnings = [];
        $suggestions = [];

        // Check for data-testid (best practice)
        if (str_contains($selector, 'data-testid')) {
            $score += 50;
        } elseif (str_contains($selector, 'data-cy')) {
            $score += 45;
        } else {
            $suggestions[] = 'Consider adding data-testid attributes for more stable selectors';
        }

        // Check for ID
        if (preg_match('/^#[a-zA-Z]/', $selector)) {
            $score += 30;
        }

        // Check for classes
        if (str_contains($selector, '.')) {
            $score += 20;
            // Warn if too many classes
            $classCount = substr_count($selector, '.');
            if ($classCount > 3) {
                $warnings[] = 'Selector has too many classes, may be fragile';
                $score -= 10;
            }
        }

        // Check for complex XPath or CSS
        if (str_contains($selector, '>>') || str_contains($selector, '//')) {
            $warnings[] = 'Complex selector detected, consider simplifying';
            $score -= 15;
        }

        // Check for nth-child (fragile)
        if (str_contains($selector, ':nth-child')) {
            $warnings[] = 'Using :nth-child can be fragile if DOM structure changes';
            $score -= 20;
        }

        $score = max(0, min(100, $score)); // Clamp between 0-100

        return [
            'score' => $score,
            'rating' => $this->getScoreRating($score),
            'warnings' => $warnings,
            'suggestions' => $suggestions
        ];
    }

    /**
     * Get rating based on score
     */
    protected function getScoreRating(int $score): string
    {
        if ($score >= 70) return 'excellent';
        if ($score >= 50) return 'good';
        if ($score >= 30) return 'fair';
        return 'poor';
    }
}
