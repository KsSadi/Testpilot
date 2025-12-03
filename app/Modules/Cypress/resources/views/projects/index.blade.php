@extends('layouts.backend.master')

@section('title', 'Projects')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">Cypress Testing</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Projects</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Test Projects</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Manage your test automation projects</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Create Project
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Projects</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $projects->total() }}</h3>
                </div>
                <div class="primary-color rounded-lg p-3">
                    <i class="fas fa-folder text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Active Projects</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $projects->where('status', 'active')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Modules</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $projects->sum(fn($p) => $p->modules->count()) }}</h3>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-cube text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Inactive Projects</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $projects->where('status', 'inactive')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-3">
                    <i class="fas fa-pause-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Projects Table --}}
    <div class="bg-white rounded-xl p-4 md:p-5 border border-gray-100 shadow-sm">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle px-4 md:px-0">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Project</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Description</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Modules</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Created By</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($projects as $project)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center mr-3">
                                            <i class="fas fa-folder text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $project->name }}</p>
                                            <p class="text-xs text-gray-500">Created {{ $project->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-sm text-gray-600">
                                    {{ Str::limit($project->description, 50) ?? 'No description' }}
                                </td>
                                <td class="py-3 px-3">
                                    @if($project->status === 'active')
                                        <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Active</span>
                                    @else
                                        <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <span class="badge-primary">{{ $project->modules->count() }} modules</span>
                                </td>
                                <td class="py-3 px-3 text-sm text-gray-600">
                                    {{ $project->creator->name ?? 'Unknown' }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('projects.show', $project) }}" class="p-2 text-cyan-600 hover:text-cyan-700 hover:bg-cyan-50 rounded-lg transition" title="View Project">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit Project">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Delete Project">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-folder-open text-5xl mb-3"></i>
                                        <p class="text-lg font-medium text-gray-500">No projects found</p>
                                        <p class="text-sm text-gray-400">Create your first project to get started</p>
                                        <a href="{{ route('projects.create') }}" class="btn-primary inline-block mt-4">
                                            <i class="fas fa-plus mr-2"></i>Create Project
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($projects->hasPages())
        <div class="flex flex-col md:flex-row items-center justify-between mt-4 pt-4 border-t border-gray-100 gap-4">
            <p class="text-sm text-gray-500">
                Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} projects
            </p>
            <div class="flex items-center space-x-2">
                {{ $projects->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
    </div>
@endsection
