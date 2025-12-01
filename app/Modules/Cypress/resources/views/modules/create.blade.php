@extends('layouts.backend.master')

@section('title', 'Create Module')

@section('content')
<div style="padding: 24px;">
    <div style="margin-bottom: 24px;">
        <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;">Create Module</h1>
        <p style="color: #6b7280;">Create a new module for {{ $project->name }}</p>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; max-width: 800px;">
        <form action="{{ route('modules.store', $project) }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Module Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem;"
                    placeholder="Enter module name">
                @error('name')
                    <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Description</label>
                <textarea name="description" rows="4"
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem;"
                    placeholder="Enter module description (optional)">{{ old('description') }}</textarea>
                @error('description')
                    <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Order *</label>
                <input type="number" name="order" value="{{ old('order', $nextOrder) }}" required min="0"
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem;"
                    placeholder="Enter order number">
                <p style="color: #6b7280; font-size: 0.875rem; margin-top: 4px;">Determines the display order of modules</p>
                @error('order')
                    <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Status *</label>
                <select name="status" required
                    style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem;">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="submit" style="padding: 10px 24px; background: #16a34a; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Create Module
                </button>
                <a href="{{ route('projects.show', $project) }}" style="padding: 10px 24px; background: #6b7280; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection