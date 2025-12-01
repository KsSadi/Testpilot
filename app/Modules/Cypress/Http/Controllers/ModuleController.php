<?php

namespace App\Modules\Cypress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cypress\Models\Project;
use App\Modules\Cypress\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Project $project)
    {
        $modules = $project->modules()->orderBy('order')->get();
        $data = [
            'pageTitle' => 'Modules - ' . $project->name,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => 'Modules']
            ],
            'project' => $project,
            'modules' => $modules
        ];
        return view('Cypress::modules.index', $data);
    }

    public function create(Project $project)
    {
        $maxOrder = $project->modules()->max('order') ?? 0;
        $data = [
            'pageTitle' => 'Create Module',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => 'Create Module']
            ],
            'project' => $project,
            'nextOrder' => $maxOrder + 1
        ];
        return view('Cypress::modules.create', $data);
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);
        $validated['project_id'] = $project->id;
        $module = Module::create($validated);
        return redirect()->route('projects.show', $project)->with('success', 'Module created successfully.');
    }

    public function show(Project $project, Module $module)
    {
        $module->load(['testCases' => function($query) {
            $query->orderBy('order');
        }]);
        $data = [
            'pageTitle' => $module->name,
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name]
            ],
            'project' => $project,
            'module' => $module
        ];
        return view('Cypress::modules.show', $data);
    }

    public function edit(Project $project, Module $module)
    {
        $data = [
            'pageTitle' => 'Edit Module',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('dashboard.index')],
                ['title' => 'Projects', 'url' => route('projects.index')],
                ['title' => $project->name, 'url' => route('projects.show', $project)],
                ['title' => $module->name, 'url' => route('modules.show', [$project, $module])],
                ['title' => 'Edit']
            ],
            'project' => $project,
            'module' => $module
        ];
        return view('Cypress::modules.edit', $data);
    }

    public function update(Request $request, Project $project, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);
        $module->update($validated);
        return redirect()->route('projects.show', $project)->with('success', 'Module updated successfully.');
    }

    public function destroy(Project $project, Module $module)
    {
        $module->delete();
        return redirect()->route('projects.show', $project)->with('success', 'Module deleted successfully.');
    }
}