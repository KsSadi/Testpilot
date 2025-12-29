@extends('layouts.backend.master')

@section('title', 'Code Generator - ' . $testCase->name)

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

    {{-- Page Header --}}
    <div class="page-title-section flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                    <i class="fas fa-code text-cyan-600 mr-2"></i>Code Generator
                </h2>
            </div>
            <p class="text-gray-500 text-xs md:text-sm">Generate Cypress test code from captured events for <strong>{{ $testCase->name }}</strong></p>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto flex-wrap">
            <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="btn-secondary flex-1 md:flex-none text-center text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back to Test Case
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Event Sessions (Versioned) --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Event Sessions Selector --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-cyan-600 to-cyan-700 px-4 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-layer-group"></i>
                        Recording Sessions ({{ $eventSessions->count() }})
                    </h3>
                </div>
                <div class="p-3">
                    @if($eventSessions->count() > 0)
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($eventSessions as $session)
                                <div class="flex items-center gap-2 p-2 rounded-lg border transition cursor-pointer group
                                    {{ $selectedSession && $selectedSession->id === $session->id 
                                        ? 'bg-cyan-50 border-cyan-300 ring-2 ring-cyan-200' 
                                        : 'bg-gray-50 border-gray-200 hover:bg-gray-100' }}"
                                    onclick="selectSession('{{ $session->hash_id }}')"
                                    id="session-{{ $session->hash_id }}">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-sm {{ $selectedSession && $selectedSession->id === $session->id ? 'text-cyan-700' : 'text-gray-700' }}">
                                                {{ $session->version_label }}
                                            </span>
                                            <span class="bg-gray-200 text-gray-600 text-xs px-1.5 py-0.5 rounded">
                                                {{ $session->events_count }} events
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate">
                                            <i class="fas fa-clock mr-1"></i>{{ $session->formatted_recorded_at }}
                                        </p>
                                    </div>
                                    <button onclick="event.stopPropagation(); deleteSession('{{ $session->hash_id }}')" 
                                        class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 p-1 transition" 
                                        title="Delete Session">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No recording sessions yet</p>
                            <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="text-cyan-600 hover:text-cyan-700 text-xs mt-1 inline-block">
                                <i class="fas fa-arrow-left mr-1"></i> Record events
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Selected Session Events --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="primary-color px-4 py-3 flex items-center justify-between">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-list-alt"></i>
                        @if($selectedSession)
                            {{ $selectedSession->version_label }} Events ({{ $events->count() }})
                        @else
                            Events
                        @endif
                    </h3>
                </div>
                <div class="p-4">
                    @if($events->count() > 0)
                        <div class="max-h-[350px] overflow-y-auto space-y-2">
                            @foreach($events as $index => $event)
                                @php
                                    $eventData = is_string($event->event_data) ? json_decode($event->event_data, true) : $event->event_data;
                                    $type = $eventData['type'] ?? 'unknown';
                                    $icon = match($type) {
                                        'click' => 'fa-mouse-pointer text-blue-500',
                                        'input' => 'fa-keyboard text-yellow-500',
                                        'change' => 'fa-exchange-alt text-purple-500',
                                        'navigation' => 'fa-compass text-pink-500',
                                        default => 'fa-circle text-gray-400'
                                    };
                                @endphp
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 hover:bg-gray-100 transition">
                                    <div class="flex items-start gap-2">
                                        <span class="text-gray-400 text-xs font-mono">{{ $index + 1 }}</span>
                                        <i class="fas {{ $icon }} mt-0.5"></i>
                                        <div class="flex-1 min-w-0">
                                            <span class="font-medium text-gray-700 text-sm">{{ ucfirst($type) }}</span>
                                            <p class="text-xs text-gray-500 truncate font-mono" title="{{ $eventData['selector'] ?? $eventData['url'] ?? '' }}">
                                                {{ $eventData['selector'] ?? $eventData['url'] ?? '-' }}
                                            </p>
                                            @if(!empty($eventData['value']))
                                                <p class="text-xs text-gray-400 truncate">Value: {{ $eventData['value'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                            {{-- Standard Code Generator --}}
                            <button onclick="generateCode()" id="generate-btn" class="w-full bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-3 rounded-lg transition duration-200 shadow flex items-center justify-center gap-2">
                                <i class="fas fa-code"></i>
                                Generate Basic Code
                            </button>
                            
                            {{-- AI Code Generator --}}
                            <button onclick="generateAICode()" id="ai-generate-btn" class="w-full bg-gradient-to-r from-purple-600 via-indigo-600 to-cyan-500 hover:from-purple-700 hover:via-indigo-700 hover:to-cyan-600 text-white font-semibold px-4 py-3 rounded-lg transition duration-200 shadow-lg flex items-center justify-center gap-2 relative overflow-hidden group">
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-400/20 via-indigo-400/20 to-cyan-400/20 animate-pulse"></div>
                                <i class="fas fa-robot relative z-10"></i>
                                <span class="relative z-10">Generate with AI</span>
                                <span class="relative z-10 bg-white/20 text-xs px-2 py-0.5 rounded-full ml-1">Pro</span>
                            </button>
                            <p class="text-xs text-gray-500 text-center">
                                <i class="fas fa-sparkles text-purple-500 mr-1"></i>
                                AI generates optimized, industry-standard code
                            </p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No events in this session</p>
                            <p class="text-gray-400 text-xs mt-1">Select a session or record new events</p>
                            <a href="{{ route('test-cases.show', [$project, $module, $testCase]) }}" class="inline-block mt-3 text-cyan-600 hover:text-cyan-700 text-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Go back to record
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Generated Codes --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-800 px-4 py-3 flex items-center justify-between">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-history"></i>
                        Generated Code Versions ({{ $generatedCodes->count() }})
                    </h3>
                </div>
                <div class="p-4">
                    @if($generatedCodes->count() > 0)
                        <div class="space-y-4">
                            @foreach($generatedCodes as $code)
                                <div class="border border-gray-200 rounded-lg overflow-hidden" id="code-block-{{ $code->hash_id }}">
                                    <div class="bg-gray-50 px-4 py-2 flex items-center justify-between border-b border-gray-200">
                                        <div class="flex items-center gap-3">
                                            <span class="bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded text-xs font-medium">
                                                {{ $code->version_label }}
                                            </span>
                                            @if($code->is_ai_generated)
                                                <span class="bg-gradient-to-r from-purple-500 via-indigo-500 to-cyan-500 text-white px-2 py-0.5 rounded text-xs font-medium flex items-center gap-1 shadow-sm">
                                                    <i class="fas fa-robot text-[10px]"></i>
                                                    AI Generated
                                                </span>
                                            @endif
                                            <span class="text-gray-500 text-sm">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $code->formatted_generated_at }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button onclick="copyCode('{{ $code->hash_id }}')" class="text-gray-500 hover:text-cyan-600 p-1.5 rounded hover:bg-gray-100 transition" title="Copy Code">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button onclick="downloadCode('{{ $code->hash_id }}', '{{ $testCase->name }}_{{ $code->version_label }}')" class="text-gray-500 hover:text-green-600 p-1.5 rounded hover:bg-gray-100 transition" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button onclick="deleteCode('{{ $code->hash_id }}')" class="text-gray-500 hover:text-red-600 p-1.5 rounded hover:bg-gray-100 transition" title="Delete Version">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <pre class="bg-gray-900 p-4 overflow-x-auto max-h-80"><code id="code-content-{{ $code->hash_id }}" class="text-green-400 text-sm font-mono whitespace-pre">{{ $code->code }}</code></pre>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-code text-gray-400 text-3xl"></i>
                            </div>
                            <p class="text-gray-600 font-medium">No Generated Code Yet</p>
                            <p class="text-gray-400 text-sm mt-1">Click "Generate Cypress Code" to create your first version</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// Current selected session
let selectedSessionId = '{{ $selectedSession?->hash_id ?? "" }}';

// Select a different session
function selectSession(sessionId) {
    window.location.href = '{{ route("code-generator.index", [$project, $module, $testCase]) }}?session=' + sessionId;
}

// Delete a session
async function deleteSession(sessionId) {
    if (!confirm('Delete this recording session? All events in this session will be permanently deleted.')) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch('{{ route("event-sessions.delete", [$project, $module, $testCase, "__HASH__"]) }}'.replace('__HASH__', sessionId), {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to delete session', 'error');
        }
    } catch (error) {
        console.error('Delete error:', error);
        showNotification('Error deleting session: ' + error.message, 'error');
    }
}

async function generateCode() {
    const btn = document.getElementById('generate-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating...';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch('{{ route("code-generator.generate", [$project, $module, $testCase]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                event_session_id: selectedSessionId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Code generated successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to generate code', 'error');
        }
    } catch (error) {
        console.error('Generate error:', error);
        showNotification('Error generating code: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-code mr-2"></i> Generate Basic Code';
    }
}

// AI Loading messages with icons
const aiLoadingStages = [
    { text: 'AI is thinking...', icon: 'fa-brain', duration: 2000 },
    { text: 'Analyzing events...', icon: 'fa-search', duration: 2000 },
    { text: 'Understanding patterns...', icon: 'fa-project-diagram', duration: 2500 },
    { text: 'Generating code...', icon: 'fa-code', duration: 3000 },
    { text: 'Optimizing selectors...', icon: 'fa-magic', duration: 2000 },
    { text: 'Adding best practices...', icon: 'fa-check-double', duration: 2000 },
    { text: 'Finalizing...', icon: 'fa-flag-checkered', duration: 2000 },
    { text: 'Almost done...', icon: 'fa-hourglass-half', duration: 3000 }
];

let aiLoadingInterval = null;
let currentStageIndex = 0;

function updateAILoadingText(btn) {
    const stage = aiLoadingStages[currentStageIndex];
    btn.innerHTML = `
        <div class="absolute inset-0 bg-gradient-to-r from-purple-400/30 via-indigo-400/30 to-cyan-400/30 animate-pulse"></div>
        <i class="fas ${stage.icon} fa-spin relative z-10 mr-2"></i>
        <span class="relative z-10">${stage.text}</span>
    `;
    currentStageIndex = (currentStageIndex + 1) % aiLoadingStages.length;
}

function startAILoadingAnimation(btn) {
    currentStageIndex = 0;
    updateAILoadingText(btn);
    aiLoadingInterval = setInterval(() => updateAILoadingText(btn), 2500);
}

function stopAILoadingAnimation() {
    if (aiLoadingInterval) {
        clearInterval(aiLoadingInterval);
        aiLoadingInterval = null;
    }
    currentStageIndex = 0;
}

async function generateAICode() {
    const btn = document.getElementById('ai-generate-btn');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    
    // Start the animated loading messages
    startAILoadingAnimation(btn);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    try {
        const response = await fetch('{{ route("code-generator.generate-ai", [$project, $module, $testCase]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                event_session_id: selectedSessionId
            })
        });
        
        const data = await response.json();
        
        // Stop animation before showing result
        stopAILoadingAnimation();
        
        if (data.success) {
            // Show success state before reload
            btn.innerHTML = `
                <div class="absolute inset-0 bg-gradient-to-r from-green-400/30 via-emerald-400/30 to-teal-400/30"></div>
                <i class="fas fa-check-circle relative z-10 mr-2"></i>
                <span class="relative z-10">✨ Code Generated!</span>
            `;
            showNotification('🎉 AI generated optimized code successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.message || 'AI generation failed', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    } catch (error) {
        console.error('AI Generate error:', error);
        stopAILoadingAnimation();
        showNotification('Error: ' + error.message, 'error');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

function copyCode(hashId) {
    const codeEl = document.getElementById('code-content-' + hashId);
    navigator.clipboard.writeText(codeEl.textContent);
    showNotification('Code copied to clipboard!', 'success');
}

function downloadCode(hashId, filename) {
    const codeEl = document.getElementById('code-content-' + hashId);
    const blob = new Blob([codeEl.textContent], { type: 'text/javascript' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename.replace(/[^a-z0-9]/gi, '_') + '.cy.js';
    a.click();
    showNotification('Code downloaded!', 'success');
}

async function deleteCode(hashId) {
    if (!confirm('Are you sure you want to delete this code version? This action cannot be undone.')) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const codeBlock = document.getElementById('code-block-' + hashId);
    
    try {
        const response = await fetch('{{ route("code-generator.delete", [$project, $module, $testCase, "__HASH__"]) }}'.replace('__HASH__', hashId), {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Animate and remove the element
            if (codeBlock) {
                codeBlock.style.transition = 'all 0.3s ease-out';
                codeBlock.style.opacity = '0';
                codeBlock.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    codeBlock.remove();
                    // Check if no more code blocks exist
                    const remainingBlocks = document.querySelectorAll('[id^="code-block-"]');
                    if (remainingBlocks.length === 0) {
                        location.reload(); // Reload to show empty state
                    }
                }, 300);
            }
            showNotification('Code version deleted!', 'success');
        } else {
            showNotification(data.message || 'Failed to delete code', 'error');
        }
    } catch (error) {
        console.error('Delete error:', error);
        showNotification('Error deleting code: ' + error.message, 'error');
    }
}

function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 3000);
}
</script>
@endpush
