@extends('layouts.backend.master')

@section('title', $project->name)

@section('content')
<div style="padding: 24px;">
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;">{{ $project->name }}</h1>
            <p style="color: #6b7280;">{{ $project->description ?? 'No description' }}</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('modules.create', $project) }}" style="padding: 10px 20px; background: #16a34a; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-plus"></i> Add Module
            </a>
            <a href="{{ route('projects.edit', $project) }}" style="padding: 10px 20px; background: #f59e0b; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    {{-- Project Info --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Status</p>
                <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: {{ $project->status === 'active' ? '#dcfce7' : '#fee2e2' }}; color: {{ $project->status === 'active' ? '#166534' : '#b91c1c' }};">
                    {{ ucfirst($project->status) }}
                </span>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Created By</p>
                <p style="font-weight: 600; color: #1f2937;">{{ $project->creator->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Modules</p>
                <p style="font-weight: 600; color: #1f2937;">{{ $project->modules->count() }}</p>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Total Test Cases</p>
                <p style="font-weight: 600; color: #1f2937;">{{ $project->testCases->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Modules List --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
        <div style="padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0;">Modules</h2>
        </div>

        @if($project->modules->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Order</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Name</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Description</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Test Cases</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Status</th>
                    <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #374151;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->modules as $module)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 16px;">
                        <span style="padding: 4px 8px; background: #dbeafe; color: #1e40af; border-radius: 4px; font-weight: 600;">
                            {{ $module->order }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px;">
                        <a href="{{ route('modules.show', [$project, $module]) }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                            {{ $module->name }}
                        </a>
                    </td>
                    <td style="padding: 12px 16px; color: #6b7280;">
                        {{ Str::limit($module->description, 50) ?? 'No description' }}
                    </td>
                    <td style="padding: 12px 16px;">
                        <span style="padding: 4px 8px; background: #f3f4f6; color: #374151; border-radius: 4px; font-weight: 600;">
                            {{ $module->testCases->count() }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px;">
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: {{ $module->status === 'active' ? '#dcfce7' : '#fee2e2' }}; color: {{ $module->status === 'active' ? '#166534' : '#b91c1c' }};">
                            {{ ucfirst($module->status) }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="{{ route('modules.show', [$project, $module]) }}" style="padding: 6px 12px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('modules.edit', [$project, $module]) }}" style="padding: 6px 12px; background: #f59e0b; color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('modules.destroy', [$project, $module]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This will delete all test cases in this module.');">
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
        @else
        <div style="padding: 48px; text-align: center; color: #6b7280;">
            <i class="fas fa-cube" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
            <p style="font-size: 1.125rem; margin: 0;">No modules yet</p>
            <p style="margin-top: 8px;">Create your first module to organize test cases</p>
            <a href="{{ route('modules.create', $project) }}" style="display: inline-block; margin-top: 16px; padding: 10px 20px; background: #16a34a; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-plus"></i> Create Module
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
