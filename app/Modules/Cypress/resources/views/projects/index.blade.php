@extends('layouts.backend.master')

@section('title', 'Projects')

@section('content')
<div style="padding: 24px;">
    {{-- Page Header --}}
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;">Projects</h1>
            <p style="color: #6b7280;">Manage your test projects</p>
        </div>
        <a href="{{ route('projects.create') }}" style="padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Create Project
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
        {{ session('success') }}
    </div>
    @endif

    {{-- Projects List --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
        @if($projects->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Name</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Description</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Status</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Modules</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Created By</th>
                    <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #374151;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 16px;">
                        <a href="{{ route('projects.show', $project) }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                            {{ $project->name }}
                        </a>
                    </td>
                    <td style="padding: 12px 16px; color: #6b7280;">
                        {{ Str::limit($project->description, 50) ?? 'No description' }}
                    </td>
                    <td style="padding: 12px 16px;">
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: {{ $project->status === 'active' ? '#dcfce7' : '#fee2e2' }}; color: {{ $project->status === 'active' ? '#166534' : '#b91c1c' }};">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px;">
                        <span style="padding: 4px 8px; background: #dbeafe; color: #1e40af; border-radius: 4px; font-size: 0.875rem;">
                            {{ $project->modules->count() }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px; color: #6b7280;">
                        {{ $project->creator->name ?? 'Unknown' }}
                    </td>
                    <td style="padding: 12px 16px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="{{ route('projects.show', $project) }}" style="padding: 6px 12px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem;">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('projects.edit', $project) }}" style="padding: 6px 12px; background: #f59e0b; color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 6px 12px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div style="padding: 16px;">
            {{ $projects->links() }}
        </div>
        @else
        <div style="padding: 48px; text-align: center; color: #6b7280;">
            <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
            <p style="font-size: 1.125rem; margin: 0;">No projects yet</p>
            <p style="margin-top: 8px;">Create your first project to get started</p>
        </div>
        @endif
    </div>
</div>
@endsection
