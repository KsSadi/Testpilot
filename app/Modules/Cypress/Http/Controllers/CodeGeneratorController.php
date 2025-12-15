<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cypress\Models\Project;
use App\Modules\Cypress\Models\Module;
use App\Modules\Cypress\Models\TestCase;
use App\Modules\Cypress\Models\TestCaseEvent;
use App\Modules\Cypress\Services\CodeGeneratorService;
use App\Modules\Cypress\Services\SelectorOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CodeGeneratorController extends Controller
{
    protected CodeGeneratorService $codeGenerator;
    protected SelectorOptimizerService $selectorOptimizer;

    public function __construct(
        CodeGeneratorService $codeGenerator,
        SelectorOptimizerService $selectorOptimizer
    ) {
        $this->codeGenerator = $codeGenerator;
        $this->selectorOptimizer = $selectorOptimizer;
    }

    /**
     * Generate and preview Cypress code
     */
    public function preview(Request $request, Project $project, Module $module, TestCase $testCase)
    {
        $format = $request->get('format', 'cypress');
        $options = [
            'add_assertions' => $request->boolean('add_assertions', false),
            'ai_enhance' => $request->boolean('ai_enhance', false),
        ];

        if ($format === 'playwright') {
            $code = $this->codeGenerator->generatePlaywrightCode($testCase, $options);
            $language = 'javascript';
        } else {
            $code = $this->codeGenerator->generateCypressCode($testCase, $options);
            $language = 'javascript';
        }

        $data = [
            'pageTitle' => 'Code Preview - ' . $testCase->name,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name, 'url' => route('modules.show', [$project, $module])],
                ['title' => $testCase->name, 'url' => route('test-cases.show', [$project, $module, $testCase])],
                ['title' => 'Code Preview']
            ],
            'project' => $project,
            'module' => $module,
            'testCase' => $testCase,
            'code' => $code,
            'format' => $format,
            'language' => $language,
            'options' => $options
        ];

        return view('Cypress::code-generator.preview', $data);
    }

    /**
     * Download generated code as file
     */
    public function download(Request $request, Project $project, Module $module, TestCase $testCase)
    {
        $format = $request->get('format', 'cypress');
        $options = [
            'add_assertions' => $request->boolean('add_assertions', false),
            'ai_enhance' => $request->boolean('ai_enhance', false),
        ];

        if ($format === 'playwright') {
            $code = $this->codeGenerator->generatePlaywrightCode($testCase, $options);
            $filename = $this->sanitizeFilename($testCase->name) . '.spec.js';
        } else {
            $code = $this->codeGenerator->generateCypressCode($testCase, $options);
            $filename = $this->sanitizeFilename($testCase->name) . '.cy.js';
        }

        return Response::make($code, 200, [
            'Content-Type' => 'application/javascript',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * API endpoint to generate code (for AJAX requests)
     */
    public function generate(Request $request, Project $project, Module $module, TestCase $testCase)
    {
        $format = $request->get('format', 'cypress');
        $options = [
            'add_assertions' => $request->boolean('add_assertions', false),
            'ai_enhance' => $request->boolean('ai_enhance', false),
        ];

        if ($format === 'playwright') {
            $code = $this->codeGenerator->generatePlaywrightCode($testCase, $options);
        } else {
            $code = $this->codeGenerator->generateCypressCode($testCase, $options);
        }

        return response()->json([
            'success' => true,
            'code' => $code,
            'format' => $format,
            'event_count' => $testCase->savedEvents()->count()
        ]);
    }

    /**
     * Get selector suggestions for an event
     */
    public function suggestSelectors(Request $request, Project $project, Module $module, TestCase $testCase, $eventId)
    {
        $event = TestCaseEvent::where('id', $eventId)
            ->where('session_id', $testCase->session_id)
            ->firstOrFail();

        $suggestions = $this->selectorOptimizer->suggestSelectors($event);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
            'event' => [
                'id' => $event->id,
                'type' => $event->event_type,
                'tag' => $event->tag_name,
                'text' => $event->inner_text
            ]
        ]);
    }

    /**
     * Validate selector strength
     */
    public function validateSelector(Request $request)
    {
        $request->validate([
            'selector' => 'required|string'
        ]);

        $validation = $this->selectorOptimizer->validateSelector($request->selector);

        return response()->json([
            'success' => true,
            'validation' => $validation
        ]);
    }

    /**
     * Optimize selector for an event
     */
    public function optimizeSelector(Request $request, Project $project, Module $module, TestCase $testCase, $eventId)
    {
        $event = TestCaseEvent::where('id', $eventId)
            ->where('session_id', $testCase->session_id)
            ->firstOrFail();

        $optimized = $this->selectorOptimizer->optimizeSelector($event);
        $validation = $this->selectorOptimizer->validateSelector($optimized);

        return response()->json([
            'success' => true,
            'original' => $event->selector,
            'optimized' => $optimized,
            'validation' => $validation
        ]);
    }

    /**
     * Real-time code preview (streaming)
     */
    public function livePreview(Request $request, Project $project, Module $module, TestCase $testCase)
    {
        // Get events with optional filtering
        $query = $testCase->savedEvents()->orderBy('created_at');
        
        if ($request->has('from_event')) {
            $query->where('id', '>=', $request->from_event);
        }

        $events = $query->get();
        $format = $request->get('format', 'cypress');

        // Generate code in real-time as events are processed
        $codeLines = [];
        
        foreach ($events as $index => $event) {
            $command = $this->generateSingleCommand($event, $format);
            if ($command) {
                $codeLines[] = [
                    'line' => $index + 1,
                    'code' => $command,
                    'event_id' => $event->id,
                    'event_type' => $event->event_type
                ];
            }
        }

        return response()->json([
            'success' => true,
            'lines' => $codeLines,
            'total_events' => count($events)
        ]);
    }

    /**
     * Generate single command for real-time preview
     */
    protected function generateSingleCommand(TestCaseEvent $event, string $format): ?string
    {
        $selector = $this->selectorOptimizer->optimizeSelector($event);

        if ($format === 'playwright') {
            switch ($event->event_type) {
                case 'click':
                    return "await page.locator('{$selector}').click();";
                case 'input':
                case 'change':
                    $value = addslashes($event->value ?? '');
                    return "await page.locator('{$selector}').fill('{$value}');";
                default:
                    return null;
            }
        } else {
            switch ($event->event_type) {
                case 'click':
                    return "cy.get('{$selector}').click();";
                case 'input':
                case 'change':
                    $value = addslashes($event->value ?? '');
                    return "cy.get('{$selector}').clear().type('{$value}');";
                default:
                    return null;
            }
        }
    }

    /**
     * Export multiple test cases as a single test suite
     */
    public function exportSuite(Request $request, Project $project, Module $module)
    {
        $testCaseIds = $request->input('test_cases', []);
        $format = $request->get('format', 'cypress');

        $testCases = TestCase::whereIn('id', $testCaseIds)
            ->where('module_id', $module->id)
            ->get();

        $code = '';
        
        if ($format === 'playwright') {
            $code = "import { test, expect } from '@playwright/test';\n\n";
            foreach ($testCases as $testCase) {
                $code .= $this->codeGenerator->generatePlaywrightCode($testCase) . "\n\n";
            }
            $filename = $this->sanitizeFilename($module->name) . '.spec.js';
        } else {
            foreach ($testCases as $testCase) {
                $code .= $this->codeGenerator->generateCypressCode($testCase) . "\n\n";
            }
            $filename = $this->sanitizeFilename($module->name) . '.cy.js';
        }

        return Response::make($code, 200, [
            'Content-Type' => 'application/javascript',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Sanitize filename
     */
    protected function sanitizeFilename(string $name): string
    {
        // Remove special characters and spaces
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        // Remove multiple underscores
        $name = preg_replace('/_+/', '_', $name);
        // Trim underscores
        $name = trim($name, '_');
        // Convert to lowercase
        return strtolower($name);
    }
}
