@extends('layouts.backend.master')

@section('title', 'Create Project')

@section('content')
<div style="padding: 24px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 24px;">Create New Project</h1>

        <form action="{{ route('projects.store') }}" method="POST" style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px;">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Project Name *</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                @error('name')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Description</label>
                <textarea name="description" id="description" rows="4" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">{{ old('description') }}</textarea>
                @error('description')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="status" style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px;">Status *</label>
                <select name="status" id="status" required style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; outline: none;">
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Create Project
                </button>
                <a href="{{ route('projects.index') }}" style="padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
