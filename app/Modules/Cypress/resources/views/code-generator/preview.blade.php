@extends('layouts.backend.master')

@section('title', 'Code Preview - ' . $testCase->name)

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
        <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="text-gray-500 hover:text-cyan-600">{{ $testCase->name }}</a>
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="text-gray-800 font-medium">Code Generator</span>
    </div>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Page Header --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <i class="fas fa-code text-2xl text-cyan-600"></i>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Code Generator</h2>
                <span class="badge-info">{{ ucfirst($format) }}</span>
            </div>
            <p class="text-gray-500 text-sm">Auto-generated test code from captured events</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="btn-secondary text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <button onclick="regenerateCode()" class="btn-primary text-sm">
                <i class="fas fa-sync-alt mr-2"></i>Regenerate
            </button>
            <a href="{{ route('code-generator.download', [$project, $module, $testCase]) }}?format={{ $format }}&add_assertions={{ $options['add_assertions'] ? '1' : '0' }}" 
               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition duration-200">
                <i class="fas fa-download mr-2"></i>Download
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Options Panel --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-sliders-h text-cyan-600 mr-2"></i>
                    Generation Options
                </h3>

                {{-- Format Selection --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Framework</label>
                    <select id="formatSelect" class="form-select w-full" onchange="updateFormat()">
                        <option value="cypress" {{ $format === 'cypress' ? 'selected' : '' }}>Cypress</option>
                        <option value="playwright" {{ $format === 'playwright' ? 'selected' : '' }}>Playwright</option>
                    </select>
                </div>

                {{-- Assertion Toggle --}}
                <div class="mb-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="addAssertions" class="form-checkbox h-5 w-5 text-cyan-600" 
                               {{ $options['add_assertions'] ? 'checked' : '' }} onchange="updateOptions()">
                        <span class="ml-3 text-sm text-gray-700">Add Assertions</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-8">Automatically add verification steps</p>
                </div>

                {{-- AI Enhancement Toggle --}}
                <div class="mb-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="aiEnhance" class="form-checkbox h-5 w-5 text-cyan-600" 
                               {{ $options['ai_enhance'] ? 'checked' : '' }} onchange="updateOptions()">
                        <span class="ml-3 text-sm text-gray-700">AI Enhancement</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-8">Add intelligent comments & suggestions</p>
                </div>

                {{-- Statistics --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Statistics</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Events:</span>
                            <span class="font-semibold text-gray-800">{{ $testCase->savedEvents()->count() }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Lines of Code:</span>
                            <span class="font-semibold text-gray-800" id="linesCount">{{ substr_count($code, "\n") + 1 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Format:</span>
                            <span class="font-semibold text-cyan-600">{{ ucfirst($format) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Actions</h4>
                    <div class="space-y-2">
                        <button onclick="copyToClipboard()" class="w-full btn-secondary text-sm justify-center">
                            <i class="fas fa-copy mr-2"></i>Copy to Clipboard
                        </button>
                        <button onclick="openLivePreview()" class="w-full btn-secondary text-sm justify-center">
                            <i class="fas fa-eye mr-2"></i>Live Preview
                        </button>
                    </div>
                </div>
            </div>

            {{-- Help Card --}}
            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-lg shadow-md p-6 mt-6">
                <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-info-circle text-cyan-600 mr-2"></i>
                    How to Use
                </h4>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Select your preferred test framework</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Enable assertions for automatic verification</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Download or copy the generated code</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        <span>Run the test in your CI/CD pipeline</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Code Display Panel --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-800 px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-code text-cyan-400"></i>
                        <span class="text-white font-semibold text-sm">
                            {{ $testCase->name }}.{{ $format === 'playwright' ? 'spec.js' : 'cy.js' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="copyToClipboard()" class="text-gray-300 hover:text-white text-sm px-3 py-1 rounded transition">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                </div>
                <div class="relative">
                    <pre class="language-javascript p-6 overflow-x-auto bg-gray-900 text-white text-sm" style="max-height: 600px;"><code id="codeContent" class="language-javascript">{{ $code }}</code></pre>
                    <div id="copyNotification" class="hidden absolute top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
                        <i class="fas fa-check mr-2"></i>Copied!
                    </div>
                </div>
            </div>

            {{-- Event Timeline --}}
            @if($testCase->savedEvents()->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-stream text-cyan-600 mr-2"></i>
                    Event Timeline
                </h3>
                <div class="space-y-3">
                    @foreach($testCase->savedEvents()->orderBy('created_at')->limit(10)->get() as $index => $event)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-shrink-0 w-8 h-8 bg-cyan-100 text-cyan-600 rounded-full flex items-center justify-center font-semibold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-800 text-sm">{{ ucfirst($event->event_type) }}</span>
                                <span class="text-xs text-gray-500">{{ strtolower($event->tag_name) }}</span>
                            </div>
                            @if($event->inner_text)
                            <p class="text-xs text-gray-600 truncate">{{ Str::limit($event->inner_text, 50) }}</p>
                            @endif
                            @if($event->value)
                            <p class="text-xs text-cyan-600 mt-1">Value: {{ Str::limit($event->value, 40) }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @if($testCase->savedEvents()->count() > 10)
                    <p class="text-center text-sm text-gray-500 mt-3">
                        And {{ $testCase->savedEvents()->count() - 10 }} more events...
                    </p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateFormat() {
    const format = document.getElementById('formatSelect').value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('format', format);
    window.location.href = currentUrl.toString();
}

function updateOptions() {
    regenerateCode();
}

function regenerateCode() {
    const format = document.getElementById('formatSelect').value;
    const addAssertions = document.getElementById('addAssertions').checked;
    const aiEnhance = document.getElementById('aiEnhance').checked;
    
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('format', format);
    currentUrl.searchParams.set('add_assertions', addAssertions ? '1' : '0');
    currentUrl.searchParams.set('ai_enhance', aiEnhance ? '1' : '0');
    
    window.location.href = currentUrl.toString();
}

function copyToClipboard() {
    const codeContent = document.getElementById('codeContent').textContent;
    
    navigator.clipboard.writeText(codeContent).then(() => {
        // Show notification
        const notification = document.getElementById('copyNotification');
        notification.classList.remove('hidden');
        
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy code to clipboard');
    });
}

function openLivePreview() {
    // This could open a modal or side panel with real-time code generation
    alert('Live preview feature coming soon!');
}
</script>
@endpush
@endsection
