@extends('layouts.backend.master')

@section('title', $testCase->name)

@section('content')
<div style="padding: 24px;">
    {{-- Page Header --}}
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 8px;">{{ $testCase->name }}</h1>
            <p style="color: #6b7280;">Test Case #{{ $testCase->order }} - {{ $project->name }}</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('test-cases.edit', [$project, $testCase]) }}" style="padding: 10px 20px; background: #f59e0b; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('projects.show', $project) }}" style="padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Test Case Info --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 24px;">
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Order</p>
                <p style="font-weight: 600; color: #1f2937; font-size: 1.25rem;">{{ $testCase->order }}</p>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Status</p>
                <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;
                    @if($testCase->status === 'active') background: #dcfce7; color: #166534;
                    @else background: #f3f4f6; color: #6b7280;
                    @endif">
                    {{ ucfirst($testCase->status) }}
                </span>
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Previous Test</p>
                @if($previousTestCase)
                <a href="{{ route('test-cases.show', [$project, $previousTestCase]) }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                    #{{ $previousTestCase->order }} {{ $previousTestCase->name }}
                </a>
                @else
                <p style="color: #9ca3af; font-style: italic;">First test case</p>
                @endif
            </div>
            <div>
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Next Test</p>
                @if($nextTestCase)
                <a href="{{ route('test-cases.show', [$project, $nextTestCase]) }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                    #{{ $nextTestCase->order }} {{ $nextTestCase->name }}
                </a>
                @else
                <p style="color: #9ca3af; font-style: italic;">Last test case</p>
                @endif
            </div>
        </div>

        @if($testCase->description)
        <div style="border-top: 1px solid #e5e7eb; padding-top: 16px;">
            <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 4px;">Description</p>
            <p style="color: #1f2937;">{{ $testCase->description }}</p>
        </div>
        @endif
    </div>

    {{-- Session Information --}}
    @if($previousTestCase)
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
        <div style="display: flex; align-items: start; gap: 12px;">
            <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 1.25rem; margin-top: 2px;"></i>
            <div>
                <p style="color: #1e40af; font-weight: 600; margin-bottom: 4px;">Session Sharing Enabled</p>
                <p style="color: #1e40af; font-size: 0.875rem; margin: 0;">
                    This test case will use the session from Test Case #{{ $previousTestCase->order }} ({{ $previousTestCase->name }}).
                    You don't need to repeat previous steps like login.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Cypress Testing Interface --}}
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin: 0;">Test Execution</h2>
            <button id="load-cypress-btn" onclick="loadCypressModule()" style="padding: 10px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-vial"></i> Load Cypress Testing Interface
            </button>
        </div>

        <div id="cypress-container" style="display: none;">
            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 16px; margin-bottom: 16px;">
                <p style="color: #374151; margin: 0;"><strong>Instructions:</strong></p>
                <ul style="color: #6b7280; font-size: 0.875rem; margin: 8px 0 0 20px;">
                    <li>The Cypress testing interface will load below</li>
                    <li>Enter the URL you want to test</li>
                    <li>Interact with the website - all actions will be captured</li>
                    @if($previousTestCase)
                    <li><strong>Note:</strong> Session data from Test Case #{{ $previousTestCase->order }} will be automatically used</li>
                    @endif
                    <li>Export results when done to save the test case data</li>
                </ul>
            </div>

            {{-- Iframe container for Cypress module --}}
            <div id="cypress-iframe-container">
                <iframe id="cypress-iframe"
                        src="{{ route('cypress.index') }}?embed=1"
                        style="width: 100%; height: 900px; border: 1px solid #e5e7eb; border-radius: 6px;"
                        frameborder="0">
                </iframe>
            </div>

            <div style="margin-top: 16px; display: flex; gap: 12px;">
                <button onclick="saveSessionData()" style="padding: 10px 20px; background: #16a34a; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-save"></i> Save Session Data
                </button>
                <button onclick="hideCypressModule()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-times"></i> Hide Testing Interface
                </button>
            </div>
        </div>
    </div>

    {{-- Session Data Display --}}
    @if($testCase->session_data)
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-top: 24px;">
        <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Stored Session Data</h2>
        <pre style="background: #f9fafb; padding: 16px; border-radius: 6px; overflow-x: auto; font-size: 0.875rem;">{{ json_encode($testCase->session_data, JSON_PRETTY_PRINT) }}</pre>
    </div>
    @endif
</div>

@push('scripts')
<script>
function loadCypressModule() {
    document.getElementById('cypress-container').style.display = 'block';
    document.getElementById('load-cypress-btn').style.display = 'none';

    // Load previous session data if available
    @if($previousTestCase && $previousTestCase->session_data)
    const previousSessionData = @json($previousTestCase->session_data);
    console.log('Loading session data from previous test case:', previousSessionData);

    // Post message to iframe when it loads
    const iframe = document.getElementById('cypress-iframe');
    iframe.addEventListener('load', function() {
        iframe.contentWindow.postMessage({
            type: 'load-session-data',
            data: previousSessionData
        }, '*');
    });
    @endif
}

function hideCypressModule() {
    if (confirm('Are you sure you want to hide the testing interface? Unsaved data will be lost.')) {
        document.getElementById('cypress-container').style.display = 'none';
        document.getElementById('load-cypress-btn').style.display = 'flex';
    }
}

function saveSessionData() {
    // Get session data from iframe
    const iframe = document.getElementById('cypress-iframe');

    // Request session data from iframe
    iframe.contentWindow.postMessage({
        type: 'get-session-data'
    }, '*');

    // Listen for response
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'session-data-response') {
            const sessionData = event.data.data;

            // Save to backend
            fetch('{{ route("test-cases.update", [$project, $testCase]) }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: '{{ $testCase->name }}',
                    description: '{{ $testCase->description }}',
                    order: {{ $testCase->order }},
                    status: '{{ $testCase->status }}',
                    session_data: sessionData
                })
            })
            .then(response => response.json())
            .then(data => {
                alert('Session data saved successfully!');
                location.reload();
            })
            .catch(error => {
                console.error('Error saving session data:', error);
                alert('Failed to save session data. Check console for details.');
            });
        }
    });
}
</script>
@endpush

@endsection
