@extends('layouts.backend.master')

@section('title', $module->name)

@section('content')
<div style="padding: 24px;">
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;">{{ $module->name }}</h1>
            <p style="color: #6b7280;">{{ $module->description ?? 'No description' }}</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('test-cases.create', [$project, $module]) }}" style="padding: 10px 20px; background: #16a34a; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-plus"></i> Add Test Case
            </a>
            <a href="{{ route('modules.edit', [$project, $module]) }}" style="padding: 10px 20px; background: #f59e0b; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-edit"></i> Edit Module
            </a>
            <a href="{{ route('projects.show', $project) }}" style="padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Project</p>
                <p style="font-weight: 600; color: #1f2937;">{{ $project->name }}</p>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Order</p>
                <p style="font-weight: 600; color: #1f2937;">{{ $module->order }}</p>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Status</p>
                <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: {{ $module->status === 'active' ? '#dcfce7' : '#fee2e2' }}; color: {{ $module->status === 'active' ? '#166534' : '#b91c1c' }};">
                    {{ ucfirst($module->status) }}
                </span>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Test Cases</p>
                <p style="font-weight: 600; color: #1f2937;">{{ $module->testCases->count() }}</p>
            </div>
        </div>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; overflow: hidden;">
        <div style="padding: 16px 24px; border-bottom: 1px solid #e5e7eb;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0;">Test Cases</h2>
        </div>

        @if($module->testCases->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Order</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Name</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Description</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #374151;">Status</th>
                    <th style="padding: 12px 16px; text-align: right; font-weight: 600; color: #374151;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($module->testCases as $testCase)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 16px;">
                        <span style="padding: 4px 8px; background: #dbeafe; color: #1e40af; border-radius: 4px; font-weight: 600;">
                            {{ $testCase->order }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px;">
                        <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                            {{ $testCase->name }}
                        </a>
                    </td>
                    <td style="padding: 12px 16px; color: #6b7280;">
                        {{ Str::limit($testCase->description, 50) ?? 'No description' }}
                    </td>
                    <td style="padding: 12px 16px;">
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;
                            @if($testCase->status === 'completed') background: #dcfce7; color: #166534;
                            @elseif($testCase->status === 'running') background: #dbeafe; color: #1e40af;
                            @elseif($testCase->status === 'failed') background: #fee2e2; color: #b91c1c;
                            @else background: #f3f4f6; color: #374151;
                            @endif">
                            {{ ucfirst($testCase->status) }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" style="padding: 6px 12px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('test-cases.edit', [$project, $module, $testCase]) }}" style="padding: 6px 12px; background: #f59e0b; color: white; text-decoration: none; border-radius: 6px; font-size: 0.875rem;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('test-cases.destroy', [$project, $module, $testCase]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
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
            <i class="fas fa-list-check" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
            <p style="font-size: 1.125rem; margin: 0;">No test cases yet</p>
            <p style="margin-top: 8px;">Create your first test case to get started</p>
            <a href="{{ route('test-cases.create', [$project, $module]) }}" style="display: inline-block; margin-top: 16px; padding: 10px 20px; background: #16a34a; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-plus"></i> Create Test Case
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
