@extends('layouts.backend.master')

@section('title', 'Generate Cypress Code')

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
        <span class="text-gray-800 font-medium">Generate Code</span>
    </div>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Generate Cypress Code</h2>
            <p class="text-gray-500 text-xs md:text-sm">Generated test code for: <strong>{{ $testCase->name }}</strong></p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Test Case
            </a>
            <button onclick="copyCode()" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm hover:shadow">
                <i class="fas fa-copy mr-2"></i>Copy Code
            </button>
            <a href="{{ route('test-cases.download-cypress', [$project, $module, $testCase]) }}" class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold px-4 py-2 rounded-lg transition duration-200 shadow-sm hover:shadow">
                <i class="fas fa-download mr-2"></i>Download File
            </a>
        </div>
    </div>

    @if($events->isEmpty())
        {{-- No Events Warning --}}
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-lg p-6 mb-6">
            <div class="flex items-start gap-3">
                <div class="bg-amber-100 rounded-full p-2 flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-amber-900 mb-2">No Saved Events Found</h3>
                    <p class="text-amber-800 mb-3">You need to save some events before generating Cypress code.</p>
                    <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="inline-block px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Go Back and Save Events
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Stats Section --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Total Events</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $events->count() }}</h3>
                    </div>
                    <div class="bg-gradient-to-br from-cyan-400 to-cyan-600 rounded-lg p-3">
                        <i class="fas fa-list text-white text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Code Lines</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ substr_count($cypressCode, "\n") + 1 }}</h3>
                    </div>
                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg p-3">
                        <i class="fas fa-code text-white text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Generated</p>
                        <h3 class="text-sm font-bold text-gray-800">{{ now()->format('M d, Y H:i') }}</h3>
                    </div>
                    <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- XPath Warning --}}
        @if($usesXpath)
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-6">
            <div class="flex items-start gap-3">
                <div class="bg-blue-100 rounded-full p-2 flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-blue-900 mb-2">XPath Plugin Required</h3>
                    <p class="text-blue-800 mb-3">This test uses XPath selectors. You need to install the cypress-xpath plugin:</p>
                    <div class="bg-gray-900 text-green-400 rounded-lg p-4 font-mono text-sm mb-3">
                        npm install -D cypress-xpath
                    </div>
                    <p class="text-blue-800 mb-2">Then add to <code class="bg-blue-100 px-2 py-1 rounded">cypress/support/e2e.js</code>:</p>
                    <div class="bg-gray-900 text-green-400 rounded-lg p-4 font-mono text-sm">
                        require('cypress-xpath')
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Generated Code Section --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-code text-cyan-600"></i>
                        Generated Cypress Test
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $filename }}</p>
                </div>
                <button onclick="copyCode()" class="btn-primary">
                    <i class="fas fa-copy mr-2"></i>Copy Code
                </button>
            </div>
            
            <div class="relative">
                <pre id="cypress-code" class="bg-gray-900 text-gray-100 p-6 overflow-x-auto text-sm font-mono leading-relaxed max-h-[600px]"><code class="language-javascript">{{ $cypressCode }}</code></pre>
                <button onclick="copyCode()" class="absolute top-4 right-4 px-3 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition shadow-sm text-sm font-semibold">
                    <i class="fas fa-copy mr-1"></i>Copy
                </button>
            </div>
        </div>

        {{-- Usage Instructions --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm mt-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-book-open text-emerald-600"></i>
                    How to Use This Code
                </h3>
                
                <ol class="space-y-4 text-gray-700">
                    <li class="flex gap-3">
                        <span class="bg-cyan-100 text-cyan-700 font-bold rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0">1</span>
                        <div class="flex-1">
                            <strong class="text-gray-800">Create a new file</strong>
                            <p class="text-sm text-gray-600 mt-1">In your Cypress project: <code class="bg-gray-100 px-2 py-1 rounded">cypress/e2e/{{ $filename }}</code></p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="bg-cyan-100 text-cyan-700 font-bold rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0">2</span>
                        <div class="flex-1">
                            <strong class="text-gray-800">Copy the generated code</strong>
                            <p class="text-sm text-gray-600 mt-1">Click the "Copy Code" button above and paste into your new file</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="bg-cyan-100 text-cyan-700 font-bold rounded-full w-8 h-8 flex items-center justify-center flex-shrink-0">3</span>
                        <div class="flex-1">
                            <strong class="text-gray-800">Run the test</strong>
                            <div class="bg-gray-900 text-green-400 rounded-lg p-3 font-mono text-sm mt-2">
                                npx cypress open
                            </div>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    @endif

<meta name="csrf-token" content="{{ csrf_token() }}">

@push('scripts')
<script>
function copyCode() {
    const code = document.getElementById('cypress-code').textContent;
    navigator.clipboard.writeText(code).then(() => {
        showNotification('success', 'Copied!', 'Cypress code copied to clipboard');
    }).catch(() => {
        // Fallback
        const textArea = document.createElement('textarea');
        textArea.value = code;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('success', 'Copied!', 'Cypress code copied to clipboard');
    });
}
</script>
@endpush

@endsection
