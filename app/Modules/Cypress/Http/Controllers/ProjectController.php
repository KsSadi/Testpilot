<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cypress\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();

        // Get owned projects
        $ownedProjects = Project::where('created_by', $userId)
            ->with('creator', 'modules')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get projects shared directly with user (accepted invitations)
        $directlySharedProjects = Project::whereHas('shares', function($query) use ($userId) {
                $query->where('shareable_type', 'App\\Modules\\Cypress\\Models\\Project')
                      ->where('shared_with_user_id', $userId)
                      ->where('status', 'accepted');
            })
            ->with(['creator', 'modules', 'shares' => function($query) use ($userId) {
                $query->where('shared_with_user_id', $userId);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get projects where user has access via modules or test cases
        $indirectProjectIds = \App\Models\ProjectShare::where('shared_with_user_id', $userId)
            ->where('status', 'accepted')
            ->whereIn('shareable_type', [
                'App\\Modules\\Cypress\\Models\\Module',
                'App\\Modules\\Cypress\\Models\\TestCase'
            ])
            ->with('shareable')
            ->get()
            ->pluck('shareable.project_id')
            ->unique()
            ->filter();

        $indirectlySharedProjects = Project::whereIn('id', $indirectProjectIds)
            ->whereNotIn('id', $directlySharedProjects->pluck('id'))
            ->with('creator', 'modules')
            ->orderBy('created_at', 'desc')
            ->get();

        // Combine all projects
        $allProjects = $ownedProjects
            ->concat($directlySharedProjects)
            ->concat($indirectlySharedProjects);

        $data = [
            'pageTitle' => 'Projects',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects']
            ],
            'projects' => $allProjects,
            'ownedCount' => $ownedProjects->count(),
            'sharedCount' => $directlySharedProjects->count() + $indirectlySharedProjects->count()
        ];

        return view('Cypress::projects.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Create Project',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => 'Create']
            ]
        ];

        return view('Cypress::projects.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'url' => 'nullable|url|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('projects/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $validated['created_by'] = Auth::id();

        $project = Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $userId = auth()->id();
        
        // Check if user has access to the project
        $hasProjectAccess = $project->canView($userId);
        
        if (!$hasProjectAccess) {
            abort(403, 'You do not have access to this project.');
        }

        // Get user's direct shares for modules and test cases in this project
        $moduleShareIds = \App\Models\ProjectShare::where('shareable_type', 'App\\Modules\\Cypress\\Models\\Module')
            ->where('shared_with_user_id', $userId)
            ->where('status', 'accepted')
            ->pluck('shareable_id')
            ->toArray();

        $testCaseShareIds = \App\Models\ProjectShare::where('shareable_type', 'App\\Modules\\Cypress\\Models\\TestCase')
            ->where('shared_with_user_id', $userId)
            ->where('status', 'accepted')
            ->pluck('shareable_id')
            ->toArray();

        // If user owns project or has project-level access, show all
        $showAllModules = $project->isOwnedBy($userId) || 
            $project->shares()
                ->where('shareable_type', 'App\\Modules\\Cypress\\Models\\Project')
                ->where('shared_with_user_id', $userId)
                ->where('status', 'accepted')
                ->exists();

        if ($showAllModules) {
            // Show all modules and test cases
            $project->load(['modules' => function($query) {
                $query->orderBy('order')->with('testCases');
            }]);
        } else {
            // Show only shared modules with their test cases
            $project->load(['modules' => function($query) use ($moduleShareIds, $testCaseShareIds) {
                $query->whereIn('id', $moduleShareIds)
                    ->orderBy('order')
                    ->with(['testCases' => function($q) use ($testCaseShareIds, $moduleShareIds) {
                        // Show test cases if: module is shared OR test case is directly shared
                        $q->where(function($query) use ($testCaseShareIds, $moduleShareIds) {
                            $query->whereIn('id', $testCaseShareIds)
                                  ->orWhereIn('module_id', $moduleShareIds);
                        });
                    }]);
            }]);
        }

        $data = [
            'pageTitle' => $project->name,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name]
            ],
            'project' => $project
        ];

        return view('Cypress::projects.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $data = [
            'pageTitle' => 'Edit Project',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => 'Edit']
            ],
            'project' => $project
        ];

        return view('Cypress::projects.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'url' => 'nullable|url|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($project->logo && \Storage::disk('public')->exists($project->logo)) {
                \Storage::disk('public')->delete($project->logo);
            }
            
            $logoPath = $request->file('logo')->store('projects/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Get all test cases from a project for import selection
     */
    public function getTestCasesForImport(Request $request, Project $project)
    {
        $excludeTestCaseId = $request->query('exclude');

        $testCases = \App\Modules\Cypress\Models\TestCase::whereHas('module', function($query) use ($project) {
            $query->where('project_id', $project->id);
        })
        ->with(['module', 'savedEvents'])
        ->when($excludeTestCaseId, function($query) use ($excludeTestCaseId) {
            // Decode the hashid to get the actual ID
            $actualId = \Hashids::decode($excludeTestCaseId)[0] ?? null;
            if ($actualId) {
                $query->where('id', '!=', $actualId);
            }
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($testCase) {
            return [
                'hashid' => $testCase->hashid,
                'name' => $testCase->name,
                'description' => $testCase->description,
                'module_name' => $testCase->module->name ?? 'Unknown Module',
                'events_count' => $testCase->savedEvents->count(),
                'created_at' => $testCase->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'testCases' => $testCases
        ]);
    }
}
