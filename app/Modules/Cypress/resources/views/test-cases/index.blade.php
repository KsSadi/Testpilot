@extends('layouts.backend.master')

@section('title', 'Test Cases - ' . $module->name)

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">Cypress Testing</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-cyan-600">Projects</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('projects.show', $project) }}" class="text-gray-500 hover:text-cyan-600">{{ $project->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Test Cases - {{ $module->name }}</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Test Cases - {{ $module->name }}</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Manage test cases for this module</p>
        </div>
        <div class="action-buttons flex items-center space-x-2 w-full md:w-auto">
            <a href="{{ route('modules.show', [$project, $module]) }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Module
            </a>
            <a href="{{ route('test-cases.create', [$project, $module]) }}" class="btn-primary flex-1 md:flex-none text-center">
                <i class="fas fa-plus mr-2"></i>Create Test Case
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Test Cases</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $testCases->count() }}</h3>
                </div>
                <div class="primary-color rounded-lg p-3">
                    <i class="fas fa-list-check text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Active Cases</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $testCases->where('status', 'active')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Inactive Cases</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $testCases->where('status', 'inactive')->count() }}</h3>
                </div>
                <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-3">
                    <i class="fas fa-pause-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Module</p>
                    <h3 class="text-lg font-bold text-gray-800">{{ $module->name }}</h3>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3">
                    <i class="fas fa-cube text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Test Cases Table --}}
    <div class="bg-white rounded-xl p-4 md:p-5 border border-gray-100 shadow-sm">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle px-4 md:px-0">
                <table class="w-full min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Order</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Test Case</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Description</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($testCases as $testCase)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-3">
                                    <span class="badge-primary">{{ $testCase->order }}</span>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center mr-3">
                                            <i class="fas fa-list-check text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">{{ $testCase->name }}</p>
                                            <p class="text-xs text-gray-500">Created {{ $testCase->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-sm text-gray-600">
                                    {{ Str::limit($testCase->description, 80) ?? 'No description' }}
                                </td>
                                <td class="py-3 px-3">
                                    @if($testCase->status === 'active')
                                        <span class="badge-success"><i class="fas fa-circle text-[8px] mr-1"></i>Active</span>
                                    @else
                                        <span class="badge-warning"><i class="fas fa-circle text-[8px] mr-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="p-2 text-cyan-600 hover:text-cyan-700 hover:bg-cyan-50 rounded-lg transition" title="View Test Case">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('test-cases.edit', [$project, $module, $testCase]) }}" class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition" title="Edit Test Case">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('test-cases.destroy', [$project, $module, $testCase]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this test case?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Delete Test Case">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-list-check text-5xl mb-3"></i>
                                        <p class="text-lg font-medium text-gray-500">No test cases yet</p>
                                        <p class="text-sm text-gray-400">Create your first test case to get started</p>
                                        <a href="{{ route('test-cases.create', [$project, $module]) }}" class="btn-primary inline-block mt-4">
                                            <i class="fas fa-plus mr-2"></i>Create Test Case
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
