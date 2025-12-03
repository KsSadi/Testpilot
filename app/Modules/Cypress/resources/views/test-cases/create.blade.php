@extends('layouts.backend.master')

@section('title', 'Create Test Case')

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
        <span class="text-gray-800 font-medium">Create Test Case</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Create New Test Case</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Add a new test case to {{ $module->name }}</p>
        </div>
        <a href="{{ route('modules.show', [$project, $module]) }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Module
        </a>
    </div>

    {{-- Test Case Form --}}
    <form action="{{ route('test-cases.store', [$project, $module]) }}" method="POST" class="space-y-6">
        @csrf

        @php
            $hasClonableTestCases = $clonableTestCases->filter(fn($tc) => $tc->saved_events_count > 0)->isNotEmpty();
        @endphp

        @if($hasClonableTestCases)
        {{-- Clone Section --}}
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
            <div class="primary-color rounded-lg p-4 -m-5 mb-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-clone mr-2"></i> Clone from Existing Test Case
                </h3>
                <p class="text-cyan-50 text-sm mt-1">Optional: Copy events from another test case</p>
            </div>
            <div class="p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Test Case</label>
                <select name="clone_from" id="clone_from" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    <option value="">-- Create from scratch --</option>
                    @foreach($clonableTestCases as $clonableTestCase)
                        @if($clonableTestCase->saved_events_count > 0)
                            <option value="{{ $clonableTestCase->id }}" {{ old('clone_from') == $clonableTestCase->id ? 'selected' : '' }}>
                                {{ $clonableTestCase->name }} ({{ $clonableTestCase->saved_events_count }} events)
                            </option>
                        @endif
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>All saved events will be copied to your new test case
                </p>
            </div>
        </div>
        @endif

        {{-- Basic Information --}}
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Name --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Case Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           required 
                           value="{{ old('name') }}" 
                           placeholder="e.g., User Login Test" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" 
                              id="description" 
                              rows="4" 
                              placeholder="Describe what this test case validates..." 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Order --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Execution Order <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="order" 
                           id="order" 
                           required 
                           value="{{ old('order', $nextOrder) }}" 
                           min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>Lower numbers execute first
                    </p>
                    @error('order')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                    <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('modules.show', [$project, $module]) }}" class="btn-secondary">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-2"></i>Create Test Case
            </button>
        </div>
    </form>
@endsection
