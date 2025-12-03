@extends('layouts.backend.master')

@section('title', 'Edit Test Case')

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
        <a href="{{ route('modules.show', [$project, $module]) }}" class="text-gray-500 hover:text-cyan-600">{{ $module->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Edit Test Case</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Edit Test Case</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Update test case details</p>
        </div>
        <a href="{{ route('modules.show', [$project, $module]) }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Module
        </a>
    </div>

    {{-- Test Case Form --}}
    <form action="{{ route('test-cases.update', [$project, $module, $testCase]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Test Case Information</h3>
            
            <div class="space-y-4">
                {{-- Test Case Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Case Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $testCase->name) }}" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                           placeholder="Enter test case name">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" 
                              rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                              placeholder="Enter test case description (optional)">{{ old('description', $testCase->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Execution Order --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Execution Order <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="order" 
                           value="{{ old('order', $testCase->order) }}" 
                           required 
                           min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                           placeholder="Enter execution order">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>Lower numbers execute first. Test cases will share session in order.
                    </p>
                    @error('order')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <option value="active" {{ old('status', $testCase->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $testCase->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-100">
                <a href="{{ route('modules.show', [$project, $module]) }}" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Update Test Case
                </button>
            </div>
        </div>
    </form>
@endsection
