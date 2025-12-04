@extends('layouts.backend.master')

@section('title', 'AI Cypress Test Assistant')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">AI Cypress Test Assistant</h1>
            <p class="text-gray-500 text-xs md:text-sm">Ask anything about Cypress testing and get AI-powered responses</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('ai.settings') }}" class="btn-secondary">
                <i class="fas fa-cog mr-2"></i>AI Settings
            </a>
        </div>
    </div>

    <!-- Active Provider Info -->
    @if($activeProvider)
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-robot text-green-600 text-2xl mr-3"></i>
            <div>
                <p class="text-sm font-medium text-green-800">Active AI Provider: {{ $activeProvider->display_name }}</p>
                <p class="text-xs text-green-600">Model: {{ $activeProvider->models[$activeProvider->default_model] ?? $activeProvider->default_model }}</p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-3"></i>
            <div>
                <p class="text-sm font-medium text-red-800">No active AI provider configured</p>
                <p class="text-xs text-red-600">Please configure and activate a provider in <a href="{{ route('ai.settings') }}" class="underline">AI Settings</a></p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Section -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-keyboard text-cyan-600 mr-2"></i>
                    Your Question
                </h3>
                
                <form id="aiTestForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ask about Cypress testing:</label>
                        <textarea id="userPrompt" rows="10" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent resize-none" placeholder="Example:&#10;&#10;How do I click a button with class 'submit-btn' in Cypress?&#10;&#10;Write a test to verify login functionality&#10;&#10;How to handle async operations in Cypress?"></textarea>
                    </div>

                    <button type="submit" id="generateBtn" class="w-full bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white font-semibold px-6 py-3 rounded-lg transition shadow-md" {{ !$activeProvider ? 'disabled' : '' }}>
                        <i class="fas fa-magic mr-2"></i>
                        <span id="btnText">Generate Answer</span>
                        <i class="fas fa-spinner fa-spin ml-2 hidden" id="btnLoader"></i>
                    </button>
                </form>

                <!-- Example Prompts -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-700 mb-3">Quick Examples:</p>
                    <div class="space-y-2">
                        <button class="example-prompt w-full text-left text-xs bg-gray-50 hover:bg-cyan-50 border border-gray-200 rounded px-3 py-2 transition" data-prompt="How do I select an element by its data attribute in Cypress?">
                            <i class="fas fa-chevron-right text-cyan-600 mr-2"></i>
                            Select by data attribute
                        </button>
                        <button class="example-prompt w-full text-left text-xs bg-gray-50 hover:bg-cyan-50 border border-gray-200 rounded px-3 py-2 transition" data-prompt="Write a Cypress test to verify a form submission with email and password fields">
                            <i class="fas fa-chevron-right text-cyan-600 mr-2"></i>
                            Test form submission
                        </button>
                        <button class="example-prompt w-full text-left text-xs bg-gray-50 hover:bg-cyan-50 border border-gray-200 rounded px-3 py-2 transition" data-prompt="How to wait for an API response before asserting in Cypress?">
                            <i class="fas fa-chevron-right text-cyan-600 mr-2"></i>
                            Wait for API response
                        </button>
                        <button class="example-prompt w-full text-left text-xs bg-gray-50 hover:bg-cyan-50 border border-gray-200 rounded px-3 py-2 transition" data-prompt="How to test file upload in Cypress?">
                            <i class="fas fa-chevron-right text-cyan-600 mr-2"></i>
                            Test file upload
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-robot text-purple-600 mr-2"></i>
                    AI Response
                </h3>

                <!-- Loading State -->
                <div id="loadingState" class="hidden text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-cyan-600 mb-4"></i>
                    <p class="text-gray-600">AI is thinking...</p>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="text-center py-12">
                    <i class="fas fa-comments text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Ask a question to get started</p>
                    <p class="text-sm text-gray-400 mt-2">Try asking about Cypress selectors, assertions, or best practices</p>
                </div>

                <!-- Response Display -->
                <div id="responseContainer" class="hidden">
                    <div class="bg-gradient-to-br from-purple-50 to-cyan-50 rounded-lg p-6 mb-4">
                        <div id="aiResponse" class="prose prose-sm max-w-none text-gray-800"></div>
                    </div>

                    <!-- Metadata -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-blue-600 font-medium">Provider</p>
                            <p class="text-sm text-blue-800 font-semibold mt-1" id="metaProvider">-</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-green-600 font-medium">Response Time</p>
                            <p class="text-sm text-green-800 font-semibold mt-1" id="metaTime">-</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-purple-600 font-medium">Tokens Used</p>
                            <p class="text-sm text-purple-800 font-semibold mt-1" id="metaTokens">-</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-orange-600 font-medium">Cost</p>
                            <p class="text-sm text-orange-800 font-semibold mt-1" id="metaCost">-</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 mt-4">
                        <button id="copyBtn" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-lg transition">
                            <i class="fas fa-copy mr-2"></i>Copy Response
                        </button>
                        <button id="clearBtn" class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-4 py-2 rounded-lg transition">
                            <i class="fas fa-trash mr-2"></i>Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('aiTestForm');
    const promptInput = document.getElementById('userPrompt');
    const generateBtn = document.getElementById('generateBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const responseContainer = document.getElementById('responseContainer');
    const aiResponse = document.getElementById('aiResponse');
    
    // Example prompts
    document.querySelectorAll('.example-prompt').forEach(btn => {
        btn.addEventListener('click', function() {
            promptInput.value = this.dataset.prompt;
            promptInput.focus();
        });
    });

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const prompt = promptInput.value.trim();
        if (!prompt) {
            alert('Please enter a question');
            return;
        }

        // Show loading state
        generateBtn.disabled = true;
        btnText.textContent = 'Generating...';
        btnLoader.classList.remove('hidden');
        emptyState.classList.add('hidden');
        responseContainer.classList.add('hidden');
        loadingState.classList.remove('hidden');

        try {
            const response = await fetch('{{ route("ai.test.generate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ prompt })
            });

            const data = await response.json();

            if (data.success) {
                // Display response
                aiResponse.innerHTML = marked.parse(data.response);
                
                // Update metadata
                document.getElementById('metaProvider').textContent = data.provider;
                document.getElementById('metaTime').textContent = data.response_time + 'ms';
                document.getElementById('metaTokens').textContent = data.tokens.total_tokens.toLocaleString();
                document.getElementById('metaCost').textContent = '$' + data.cost.toFixed(6);
                
                // Show response
                loadingState.classList.add('hidden');
                responseContainer.classList.remove('hidden');
            } else {
                alert('Error: ' + data.message);
                loadingState.classList.add('hidden');
                emptyState.classList.remove('hidden');
            }
        } catch (error) {
            alert('Request failed: ' + error.message);
            loadingState.classList.add('hidden');
            emptyState.classList.remove('hidden');
        } finally {
            generateBtn.disabled = false;
            btnText.textContent = 'Generate Answer';
            btnLoader.classList.add('hidden');
        }
    });

    // Copy response
    document.getElementById('copyBtn')?.addEventListener('click', function() {
        const text = aiResponse.innerText;
        navigator.clipboard.writeText(text).then(() => {
            this.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-copy mr-2"></i>Copy Response';
            }, 2000);
        });
    });

    // Clear response
    document.getElementById('clearBtn')?.addEventListener('click', function() {
        responseContainer.classList.add('hidden');
        emptyState.classList.remove('hidden');
        promptInput.value = '';
        promptInput.focus();
    });
});
</script>
@endpush
