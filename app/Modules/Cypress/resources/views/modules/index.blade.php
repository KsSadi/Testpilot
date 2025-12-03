@extends('layouts.backend.master')

@section('title', 'Modules - ' . $project->name)

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">Cypress Testing</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-cyan-600">Projects</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Modules - {{ $project->name }}</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Modules - {{ $project->name }}</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Organize test cases into modules</p>
        </div>
        <div class="action-buttons flex items-center space-x-2 w-full md:w-auto">
            <a href="{{ route('projects.show', $project) }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Project
            </a>
            <a href="{{ route('modules.create', $project) }}" class="btn-primary flex-1 md:flex-none text-center">
                <i class="fas fa-plus mr-2"></i>Add Module
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Modules</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $modules->count() }}</h3>
                </div>
                <div class="primary-color rounded-lg p-3">
                    <i class="fas fa-cube text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Active Modules</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $modules->where('status', 'active')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Test Cases</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $modules->sum(fn($m) => $m->testCases->count()) }}</h3>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-list-check text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Inactive Modules</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $modules->where('status', 'inactive')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-3">
                    <i class="fas fa-pause-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Modules Table --}}
    <div class="bg-white rounded-xl p-4 md:p-5 border border-gray-100 shadow-sm">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle px-4 md:px-0">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Order</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Module</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Description</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Test Cases</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($modules as $module)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-3">
                                    <span class="badge-primary">{{ $module->order }}</span>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center mr-3">
                                            <i class="fas fa-cube text-white"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $module->name }}</p>
                                            <p class="text-xs text-gray-500">Created {{ $module->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-sm text-gray-600">
                                    {{ Str::limit($module->description, 50) ?? 'No description' }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $module->testCases->count() }} cases
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    @if($module->status === 'active')
                                        <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Active</span>
                                    @else
                                        <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('modules.show', [$project, $module]) }}" class="p-2 text-cyan-600 hover:text-cyan-700 hover:bg-cyan-50 rounded-lg transition" title="View Module">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('modules.edit', [$project, $module]) }}" class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit Module">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('modules.destroy', [$project, $module]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure? This will delete all test cases in this module.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Delete Module">
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
                                        <i class="fas fa-cube text-5xl mb-3"></i>
                                        <p class="text-lg font-medium text-gray-500">No modules yet</p>
                                        <p class="text-sm text-gray-400">Create your first module to organize test cases</p>
                                        <a href="{{ route('modules.create', $project) }}" class="btn-primary inline-block mt-4">
                                            <i class="fas fa-plus mr-2"></i>Add Module
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
