<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cypress\Models\Project;
use App\Modules\Cypress\Models\TestCase;
use App\Modules\Cypress\Models\TestCaseEvent;
use Illuminate\Http\Request;

class TestCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $testCases = $project->testCases()->orderBy('order')->get();

        $data = [
            'pageTitle' => 'Test Cases - ' . $project->name,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => 'Test Cases']
            ],
            'project' => $project,
            'testCases' => $testCases
        ];

        return view('Cypress::test-cases.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $maxOrder = $project->testCases()->max('order') ?? 0;

        $data = [
            'pageTitle' => 'Create Test Case',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => 'Create Test Case']
            ],
            'project' => $project,
            'nextOrder' => $maxOrder + 1
        ];

        return view('Cypress::test-cases.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['project_id'] = $project->id;

        $testCase = TestCase::create($validated);

        return redirect()->route('test-cases.index', $project)
            ->with('success', 'Test case created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, TestCase $testCase)
    {
        $previousTestCase = $testCase->previousTestCase();
        $nextTestCase = $testCase->nextTestCase();

        $data = [
            'pageTitle' => $testCase->name,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $testCase->name]
            ],
            'project' => $project,
            'testCase' => $testCase,
            'previousTestCase' => $previousTestCase,
            'nextTestCase' => $nextTestCase
        ];

        return view('Cypress::test-cases.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, TestCase $testCase)
    {
        $data = [
            'pageTitle' => 'Edit Test Case',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $testCase->name, 'url' => route('test-cases.show', [$project, $testCase])],
                ['title' => 'Edit']
            ],
            'project' => $project,
            'testCase' => $testCase
        ];

        return view('Cypress::test-cases.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, TestCase $testCase)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $testCase->update($validated);

        return redirect()->route('test-cases.index', $project)
            ->with('success', 'Test case updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, TestCase $testCase)
    {
        $testCase->delete();

        return redirect()->route('test-cases.index', $project)
            ->with('success', 'Test case deleted successfully.');
    }

    /**
     * Get events for a test case session
     */
    public function getEvents(Project $project, TestCase $testCase)
    {
        $events = $testCase->events()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'events' => $events,
            'total' => $events->count(),
            'saved' => $testCase->savedEvents()->count(),
            'unsaved' => $testCase->unsavedEvents()->count()
        ]);
    }

    /**
     * Clear all unsaved events for a test case
     */
    public function clearEvents(Project $project, TestCase $testCase)
    {
        $deletedCount = $testCase->unsavedEvents()->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared $deletedCount unsaved events",
            'deleted' => $deletedCount
        ]);
    }

    /**
     * Save all unsaved events for a test case
     */
    public function saveEvents(Project $project, TestCase $testCase)
    {
        $updated = $testCase->unsavedEvents()->update(['is_saved' => true]);

        return response()->json([
            'success' => true,
            'message' => "Saved $updated events",
            'saved' => $updated
        ]);
    }

    /**
     * Delete selected events
     */
    public function deleteEvents(Request $request, Project $project, TestCase $testCase)
    {
        $eventIds = $request->input('event_ids', []);
        
        if (empty($eventIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No events selected'
            ], 400);
        }

        $deleted = TestCaseEvent::whereIn('id', $eventIds)
            ->where('session_id', $testCase->session_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted $deleted event(s)",
            'deleted' => $deleted
        ]);
    }

    /**
     * Show event capture instructions page
     */
    public function captureInstructions(Project $project, TestCase $testCase)
    {
        $data = [
            'pageTitle' => 'Event Capture Instructions',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $testCase->name, 'url' => route('test-cases.show', [$project, $testCase])],
                ['title' => 'Capture Instructions']
            ],
            'project' => $project,
            'testCase' => $testCase
        ];

        return view('Cypress::test-cases.capture-instructions', $data);
    }
}
