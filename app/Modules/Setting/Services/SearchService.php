<?php

namespace App\Modules\Setting\Services;

use App\Modules\Cypress\Models\Project;
use App\Modules\Cypress\Models\Module;
use App\Modules\Cypress\Models\TestCase;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Perform global search across Projects, Modules, and Test Cases
     */
    public function search(string $query, int $userId)
    {
        $query = trim($query);
        
        // Perform searches in parallel
        $projects = $this->searchProjects($query, $userId);
        $modules = $this->searchModules($query, $userId);
        $testCases = $this->searchTestCases($query, $userId);

        // Calculate total
        $total = count($projects) + count($modules) + count($testCases);

        return [
            'projects' => $projects,
            'modules' => $modules,
            'test_cases' => $testCases,
            'total' => $total
        ];
    }

    /**
     * Search projects with fuzzy matching
     */
    private function searchProjects(string $query, int $userId)
    {
        $results = Project::where('created_by', $userId)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'description', 'status', 'created_at')
            ->withCount('modules')
            ->limit(5)
            ->get();

        return $results->map(function($project) use ($query) {
            return [
                'id' => $project->hashid(),
                'type' => 'project',
                'icon' => '🗂️',
                'name' => $project->name,
                'description' => $this->truncate($project->description, 60),
                'meta' => $project->modules_count . ' modules',
                'status' => $project->status,
                'url' => route('projects.show', $project),
                'created_at' => $project->created_at->diffForHumans(),
                'relevance' => $this->calculateRelevance($query, $project->name, $project->description)
            ];
        })->sortByDesc('relevance')->values()->toArray();
    }

    /**
     * Search modules with fuzzy matching
     */
    private function searchModules(string $query, int $userId)
    {
        $results = Module::where('created_by', $userId)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->with('project:id,name')
            ->select('id', 'project_id', 'name', 'description', 'status', 'created_at')
            ->withCount('testCases')
            ->limit(5)
            ->get();

        return $results->map(function($module) use ($query) {
            return [
                'id' => $module->hashid(),
                'type' => 'module',
                'icon' => '📦',
                'name' => $module->name,
                'description' => $this->truncate($module->description, 60),
                'parent' => $module->project->name ?? 'Unknown Project',
                'meta' => $module->test_cases_count . ' test cases',
                'status' => $module->status,
                'url' => route('modules.show', [$module->project, $module]),
                'created_at' => $module->created_at->diffForHumans(),
                'relevance' => $this->calculateRelevance($query, $module->name, $module->description)
            ];
        })->sortByDesc('relevance')->values()->toArray();
    }

    /**
     * Search test cases with fuzzy matching
     */
    private function searchTestCases(string $query, int $userId)
    {
        $results = TestCase::where('created_by', $userId)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->with(['project:id,name', 'module:id,name'])
            ->select('id', 'project_id', 'module_id', 'name', 'description', 'status', 'created_at')
            ->withCount(['events as saved_events' => function($q) {
                $q->where('is_saved', true);
            }])
            ->limit(7)
            ->get();

        return $results->map(function($testCase) use ($query) {
            return [
                'id' => $testCase->hashid(),
                'type' => 'test_case',
                'icon' => '✅',
                'name' => $testCase->name,
                'description' => $this->truncate($testCase->description, 60),
                'parent' => ($testCase->module->name ?? 'Unknown') . ' → ' . ($testCase->project->name ?? 'Unknown'),
                'meta' => $testCase->saved_events . ' events saved',
                'status' => $testCase->status,
                'url' => route('test-cases.show', [$testCase->project, $testCase->module, $testCase]),
                'created_at' => $testCase->created_at->diffForHumans(),
                'relevance' => $this->calculateRelevance($query, $testCase->name, $testCase->description)
            ];
        })->sortByDesc('relevance')->values()->toArray();
    }

    /**
     * Calculate relevance score for ranking
     */
    private function calculateRelevance(string $query, string $name, ?string $description = null): int
    {
        $score = 0;
        $query = strtolower($query);
        $name = strtolower($name);
        $description = strtolower($description ?? '');

        // Exact match in name (highest priority)
        if ($name === $query) {
            $score += 100;
        }

        // Starts with query in name
        if (strpos($name, $query) === 0) {
            $score += 50;
        }

        // Contains query in name
        if (strpos($name, $query) !== false) {
            $score += 30;
        }

        // Contains query in description
        if (strpos($description, $query) !== false) {
            $score += 10;
        }

        // Word match bonus (fuzzy)
        $queryWords = explode(' ', $query);
        foreach ($queryWords as $word) {
            if (strlen($word) > 2 && strpos($name, $word) !== false) {
                $score += 5;
            }
        }

        return $score;
    }

    /**
     * Truncate text with ellipsis
     */
    private function truncate(?string $text, int $length = 60): string
    {
        if (!$text || strlen($text) <= $length) {
            return $text ?? '';
        }

        return substr($text, 0, $length) . '...';
    }
}
