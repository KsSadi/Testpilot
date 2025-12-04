@extends('layouts.backend.master')

@section('title', 'Edit Project')

@section('breadcrumb')
    <div class="flex items-center space-x-2 text-sm">
        <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-cyan-600">Home</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-500">Cypress Testing</span>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-cyan-600">Projects</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Edit Project</span>
    </div>
@endsection

@section('content')
    {{-- Page Title Section --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Edit Project</h2>
            <p class="text-gray-500 text-xs md:text-sm mt-1">Update project information</p>
        </div>
        <a href="{{ route('projects.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Project Form --}}
    <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Project Information</h3>
            
            <div class="space-y-4">
                {{-- Project Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $project->name) }}" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                           placeholder="Enter project name">
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
                              placeholder="Enter project description (optional)">{{ old('description', $project->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Project Logo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project Logo</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="file" 
                                   name="logo" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" 
                                   onchange="previewLogo(event)">
                            <p class="text-xs text-gray-500 mt-1">Recommended: Square image, max 2MB (PNG, JPG, SVG)</p>
                            @if($project->logo)
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle"></i> Current logo exists - upload new to replace
                                </p>
                            @endif
                        </div>
                        <div id="logo-preview" class="w-20 h-20 rounded-lg border-2 border-gray-200 overflow-hidden {{ $project->logo ? '' : 'hidden' }}">
                            <img id="logo-preview-img" src="{{ $project->logo ? asset('storage/' . $project->logo) : '' }}" alt="Logo preview" class="w-full h-full object-cover">
                        </div>
                    </div>
                    @error('logo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Project URL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project URL</label>
                    <input type="url" 
                           name="url" 
                           value="{{ old('url', $project->url) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" 
                           placeholder="https://example.com">
                    <p class="text-xs text-gray-500 mt-1">The URL of the application you want to test</p>
                    @error('url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $project->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-100">
                <a href="{{ route('projects.index') }}" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Update Project
                </button>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
<script>
function previewLogo(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logo-preview').classList.remove('hidden');
            document.getElementById('logo-preview-img').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
