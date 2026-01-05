@extends('DemoFrontend::layouts.page')

@section('title', 'API Reference')
@section('description', 'Complete API reference for TestPilot automation platform')

@section('styles')
<style>
    /* API Reference Page Styles */
    .api-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        padding: 4rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .api-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 30% 50%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 70% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%);
        pointer-events: none;
    }

    .api-hero-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
    }

    .api-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(139, 92, 246, 0.2);
        color: #A78BFA;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .api-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .api-hero p {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.7);
        max-width: 600px;
        margin: 0 auto 1.5rem;
    }

    .api-base-url {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        font-family: 'Fira Code', monospace;
        font-size: 0.9rem;
        color: #22D3EE;
    }

    .api-base-url .label {
        color: rgba(255, 255, 255, 0.6);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Main Layout */
    .api-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 2rem;
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Sidebar */
    .api-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-lighter);
    }

    .api-sidebar-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--gray);
        margin-bottom: 1rem;
    }

    .api-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .api-nav li {
        margin-bottom: 0.25rem;
    }

    .api-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 1rem;
        color: var(--gray);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .api-nav a:hover {
        background: rgba(139, 92, 246, 0.08);
        color: #8B5CF6;
    }

    .api-nav a.active {
        background: rgba(139, 92, 246, 0.1);
        color: #8B5CF6;
        font-weight: 600;
    }

    .api-nav-icon {
        font-size: 1rem;
    }

    /* Content */
    .api-content {
        min-width: 0;
    }

    .api-section {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-lighter);
        scroll-margin-top: 100px;
    }

    .api-section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gray-lighter);
    }

    .api-section-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #8B5CF6 0%, #6366F1 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .api-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .api-section p {
        color: var(--gray);
        line-height: 1.8;
        margin-bottom: 1rem;
    }

    /* Endpoint Card */
    .endpoint-card {
        background: var(--gray-lightest);
        border-radius: 12px;
        border: 1px solid var(--gray-lighter);
        margin-bottom: 1.25rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .endpoint-card:hover {
        border-color: rgba(139, 92, 246, 0.3);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.1);
    }

    .endpoint-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: white;
        border-bottom: 1px solid var(--gray-lighter);
    }

    .method-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .method-badge.get { background: #ECFDF5; color: #059669; }
    .method-badge.post { background: #EFF6FF; color: #2563EB; }
    .method-badge.put { background: #FFFBEB; color: #D97706; }
    .method-badge.delete { background: #FEF2F2; color: #DC2626; }
    .method-badge.patch { background: #F5F3FF; color: #7C3AED; }

    .endpoint-path {
        font-family: 'Fira Code', monospace;
        font-size: 0.9rem;
        color: var(--dark);
        font-weight: 500;
    }

    .endpoint-body {
        padding: 1.25rem;
    }

    .endpoint-desc {
        color: var(--gray);
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }

    /* Parameter Table */
    .param-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .param-table th {
        background: rgba(139, 92, 246, 0.05);
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        border-bottom: 2px solid var(--gray-lighter);
    }

    .param-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--gray-lighter);
        color: var(--gray);
    }

    .param-table code {
        background: rgba(139, 92, 246, 0.1);
        color: #7C3AED;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-family: 'Fira Code', monospace;
        font-size: 0.85rem;
    }

    .param-required {
        color: #DC2626;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .param-optional {
        color: var(--gray-light);
        font-size: 0.8rem;
    }

    /* Code Block */
    .code-block {
        background: #1E293B;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 1rem;
    }

    .code-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.6rem 1rem;
        background: #0F172A;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .code-dots {
        display: flex;
        gap: 0.4rem;
    }

    .code-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .code-dot:nth-child(1) { background: #EF4444; }
    .code-dot:nth-child(2) { background: #F59E0B; }
    .code-dot:nth-child(3) { background: #22C55E; }

    .code-label {
        font-size: 0.7rem;
        color: #64748B;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .code-content {
        padding: 1rem 1.25rem;
        font-family: 'Fira Code', monospace;
        font-size: 0.8rem;
        line-height: 1.6;
        color: #E2E8F0;
        overflow-x: auto;
        white-space: pre;
    }

    .code-content .key { color: #22D3EE; }
    .code-content .string { color: #A5F3FC; }
    .code-content .number { color: #FBBF24; }
    .code-content .comment { color: #64748B; }

    /* Auth Box */
    .auth-box {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(99, 102, 241, 0.08) 100%);
        border: 1px solid rgba(139, 92, 246, 0.2);
        border-radius: 12px;
        padding: 1.25rem;
        margin: 1rem 0;
    }

    .auth-box-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #7C3AED;
        margin-bottom: 0.75rem;
    }

    .auth-box code {
        display: block;
        background: rgba(0, 0, 0, 0.1);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-family: 'Fira Code', monospace;
        font-size: 0.875rem;
        color: var(--dark);
    }

    /* Response Codes */
    .response-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-top: 1rem;
    }

    .response-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--gray-lightest);
        border-radius: 10px;
        border: 1px solid var(--gray-lighter);
    }

    .response-code {
        font-family: 'Fira Code', monospace;
        font-weight: 700;
        font-size: 1.1rem;
        width: 50px;
    }

    .response-code.success { color: #059669; }
    .response-code.error { color: #DC2626; }
    .response-code.warning { color: #D97706; }

    .response-info h4 {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.125rem;
    }

    .response-info p {
        font-size: 0.8rem;
        color: var(--gray);
        margin: 0;
    }

    /* Rate Limit Box */
    .rate-limit-box {
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        border: 1px solid #F59E0B;
        border-radius: 12px;
        padding: 1.25rem;
        margin: 1rem 0;
    }

    .rate-limit-box h4 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #92400E;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .rate-limit-box p {
        color: #78350F;
        font-size: 0.9rem;
        margin: 0;
    }

    /* Help Card */
    .help-cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-top: 1rem;
    }

    .help-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: var(--gray-lightest);
        border-radius: 12px;
        border: 1px solid var(--gray-lighter);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .help-card:hover {
        border-color: rgba(139, 92, 246, 0.3);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.1);
        transform: translateY(-2px);
    }

    .help-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #8B5CF6 0%, #6366F1 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .help-info h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.125rem;
    }

    .help-info p {
        font-size: 0.85rem;
        color: var(--gray);
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .api-layout {
            grid-template-columns: 1fr;
        }

        .api-sidebar {
            position: relative;
            top: 0;
        }

        .api-hero h1 {
            font-size: 2.25rem;
        }

        .response-grid,
        .help-cards {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="api-hero">
    <div class="api-hero-content">
        <div class="api-hero-badge">
            <span>🔌</span>
            <span>REST API v1.0</span>
        </div>
        <h1>API Reference</h1>
        <p>Complete REST API documentation for integrating TestPilot into your workflow</p>
        <div class="api-base-url">
            <span class="label">Base URL:</span>
            <span>https://testpilot.app/api/v1</span>
        </div>
    </div>
</div>

<!-- Main Layout -->
<div class="api-layout">
    <!-- Sidebar -->
    <aside class="api-sidebar">
        <div class="api-sidebar-title">Endpoints</div>
        <ul class="api-nav">
            <li><a href="#authentication" class="active"><span class="api-nav-icon">🔐</span> Authentication</a></li>
            <li><a href="#projects"><span class="api-nav-icon">📋</span> Projects</a></li>
            <li><a href="#test-cases"><span class="api-nav-icon">📦</span> Test Cases</a></li>
            <li><a href="#recording"><span class="api-nav-icon">🎬</span> Recording</a></li>
            <li><a href="#code-generation"><span class="api-nav-icon">⚡</span> Code Generation</a></li>
            <li><a href="#responses"><span class="api-nav-icon">📊</span> Response Codes</a></li>
            <li><a href="#rate-limits"><span class="api-nav-icon">⏱️</span> Rate Limits</a></li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="api-content">
        <!-- Authentication -->
        <section id="authentication" class="api-section">
            <div class="api-section-header">
                <div class="api-section-icon">🔐</div>
                <h2>Authentication</h2>
            </div>
            <p>All API requests require authentication using Bearer tokens. Include your API token in the Authorization header of every request.</p>
            
            <div class="auth-box">
                <div class="auth-box-title">
                    <span>🔑</span>
                    <span>Authorization Header</span>
                </div>
                <code>Authorization: Bearer YOUR_API_TOKEN</code>
            </div>

            <p>You can generate an API token from your <strong>Account Settings → API Tokens</strong> page in the TestPilot dashboard.</p>
        </section>

        <!-- Projects -->
        <section id="projects" class="api-section">
            <div class="api-section-header">
                <div class="api-section-icon">📋</div>
                <h2>Projects</h2>
            </div>
            <p>Manage your test projects programmatically.</p>

            <!-- GET Projects -->
            <div class="endpoint-card">
                <div class="endpoint-header">
                    <span class="method-badge get">GET</span>
                    <span class="endpoint-path">/projects</span>
                </div>
                <div class="endpoint-body">
                    <p class="endpoint-desc">Retrieve a list of all projects for the authenticated user.</p>
                    
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>page</code></td>
                                <td>integer</td>
                                <td>Page number <span class="param-optional">(optional)</span></td>
                            </tr>
                            <tr>
                                <td><code>per_page</code></td>
                                <td>integer</td>
                                <td>Items per page, default: 15 <span class="param-optional">(optional)</span></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="code-block">
                        <div class="code-header">
                            <div class="code-dots">
                                <div class="code-dot"></div>
                                <div class="code-dot"></div>
                                <div class="code-dot"></div>
                            </div>
                            <span class="code-label">Response</span>
                        </div>
                        <div class="code-content">{
  <span class="key">"data"</span>: [
    {
      <span class="key">"id"</span>: <span class="number">1</span>,
      <span class="key">"name"</span>: <span class="string">"E-commerce Tests"</span>,
      <span class="key">"description"</span>: <span class="string">"Test suite for online store"</span>,
      <span class="key">"created_at"</span>: <span class="string">"2025-12-30T10:00:00Z"</span>
    }
  ],
  <span class="key">"meta"</span>: {
    <span class="key">"current_page"</span>: <span class="number">1</span>,
    <span class="key">"total"</span>: <span class="number">10</span>
  }
}</div>
                    </div>
                </div>
            </div>

            <!-- POST Projects -->
            <div class="endpoint-card">
                <div class="endpoint-header">
                    <span class="method-badge post">POST</span>
                    <span class="endpoint-path">/projects</span>
                </div>
                <div class="endpoint-body">
                    <p class="endpoint-desc">Create a new project.</p>
                    
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>name</code></td>
                                <td>string</td>
                                <td>Project name <span class="param-required">required</span></td>
                            </tr>
                            <tr>
                                <td><code>description</code></td>
                                <td>string</td>
                                <td>Project description <span class="param-optional">(optional)</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Test Cases -->
        <section id="test-cases" class="api-section">
            <div class="api-section-header">
                <div class="api-section-icon">📦</div>
                <h2>Test Cases</h2>
            </div>
            <p>Manage test cases within your projects and modules.</p>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <span class="method-badge get">GET</span>
                    <span class="endpoint-path">/projects/{project}/modules/{module}/test-cases</span>
                </div>
                <div class="endpoint-body">
                    <p class="endpoint-desc">Get all test cases in a specific module.</p>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <span class="method-badge post">POST</span>
                    <span class="endpoint-path">/projects/{project}/modules/{module}/test-cases</span>
                </div>
                <div class="endpoint-body">
                    <p class="endpoint-desc">Create a new test case in a module.</p>
                    
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>name</code></td>
                                <td>string</td>
                                <td>Test case name <span class="param-required">required</span></td>
                            </tr>
                            <tr>
                                <td><code>description</code></td>
                                <td>string</td>
                                <td>Test description <span class="param-optional">(optional)</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Recording -->
        <section id="recording" class="api-section">
            <div class="api-section-header">
                <div class="api-section-icon">🎬</div>
                <h2>Recording Sessions</h2>
            </div>
            <p>Control browser recording sessions programmatically.</p>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <span class="method-badge post">POST</span>
                    <span class="endpoint-path">/recording/start</span>
                </div>
                <div class="endpoint-body">
                    <p class="endpoint-desc">Start a new recording session. This will launch a browser instance.</p>
                    
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>test_case_id</code></td>
                                <td>integer</td>
                                <td>Target test case ID <span class="param-required">required</span></td>
                            </tr>
                            <tr>
                                <td><code>url</code></td>
                                <td>string</td>
                                <td>Starting URL <span class="param-required">required</span></td>
                            </tr>
                            <tr>
                                <td><code>browser</code></td>
                                <td>string</td>
                                <td>Browser type: chromium, firefox <span class="param-optional">(optional)</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <span class="method-badge post">POST</span>
                    <span class="endpoint-path">/recording/stop</span>
                </div>
                <div class="endpoint-body">
                    <p class="endpoint-desc">Stop the active recording session and save captured events.</p>
                </div>
            </div>
        </section>

        <!-- Code Generation -->
        <section id="code-generation" class="api-section">
            <div class="api-section-header">
                <div class="api-section-icon">⚡</div>
                <h2>Code Generation</h2>
            </div>
            <p>Generate test code from recorded events.</p>

            <div class="endpoint-card">
                <div class="endpoint-header">
                    <span class="method-badge post">POST</span>
                    <span class="endpoint-path">/test-cases/{id}/generate-code</span>
                </div>
                <div class="endpoint-body">
                    <p class="endpoint-desc">Generate test code from recorded events in a test case.</p>
                    
                    <table class="param-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>format</code></td>
                                <td>string</td>
                                <td>Output format: cypress or playwright <span class="param-required">required</span></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="code-block">
                        <div class="code-header">
                            <div class="code-dots">
                                <div class="code-dot"></div>
                                <div class="code-dot"></div>
                                <div class="code-dot"></div>
                            </div>
                            <span class="code-label">Response</span>
                        </div>
                        <div class="code-content">{
  <span class="key">"format"</span>: <span class="string">"cypress"</span>,
  <span class="key">"code"</span>: <span class="string">"describe('Login Test', () => {\n  it('should login', () => {\n    cy.visit('https://example.com');\n  });\n});"</span>,
  <span class="key">"generated_at"</span>: <span class="string">"2025-12-30T10:30:00Z"</span>
}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Response Codes -->
        <section id="responses" class="api-section">
            <div class="api-section-header">
                <div class="api-section-icon">📊</div>
                <h2>Response Codes</h2>
            </div>
            <p>Standard HTTP response codes used by the API.</p>

            <div class="response-grid">
                <div class="response-item">
                    <span class="response-code success">200</span>
                    <div class="response-info">
                        <h4>OK</h4>
                        <p>Request completed successfully</p>
                    </div>
                </div>
                <div class="response-item">
                    <span class="response-code success">201</span>
                    <div class="response-info">
                        <h4>Created</h4>
                        <p>Resource created successfully</p>
                    </div>
                </div>
                <div class="response-item">
                    <span class="response-code warning">400</span>
                    <div class="response-info">
                        <h4>Bad Request</h4>
                        <p>Invalid request parameters</p>
                    </div>
                </div>
                <div class="response-item">
                    <span class="response-code error">401</span>
                    <div class="response-info">
                        <h4>Unauthorized</h4>
                        <p>Authentication required</p>
                    </div>
                </div>
                <div class="response-item">
                    <span class="response-code error">404</span>
                    <div class="response-info">
                        <h4>Not Found</h4>
                        <p>Resource not found</p>
                    </div>
                </div>
                <div class="response-item">
                    <span class="response-code error">500</span>
                    <div class="response-info">
                        <h4>Server Error</h4>
                        <p>Internal server error</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rate Limits -->
        <section id="rate-limits" class="api-section">
            <div class="api-section-header">
                <div class="api-section-icon">⏱️</div>
                <h2>Rate Limits</h2>
            </div>
            <p>API requests are rate limited to ensure fair usage and platform stability.</p>

            <div class="rate-limit-box">
                <h4>⚠️ Rate Limit</h4>
                <p><strong>100 requests per minute</strong> per API token. Rate limit information is included in response headers.</p>
            </div>

            <div class="code-block">
                <div class="code-header">
                    <div class="code-dots">
                        <div class="code-dot"></div>
                        <div class="code-dot"></div>
                        <div class="code-dot"></div>
                    </div>
                    <span class="code-label">Response Headers</span>
                </div>
                <div class="code-content">X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1735560000</div>
            </div>

            <p style="margin-top: 1rem;">If you exceed the rate limit, you'll receive a <code>429 Too Many Requests</code> response. Wait until the reset time before making additional requests.</p>
        </section>

        <!-- Help Section -->
        <section id="help" class="api-section">
            <div class="api-section-header">
                <div class="api-section-icon">💬</div>
                <h2>Need Help?</h2>
            </div>
            <p>Get support and connect with our community for API assistance.</p>

            <div class="help-cards">
                <a href="{{ route('landing.support') }}" class="help-card">
                    <div class="help-icon">🎧</div>
                    <div class="help-info">
                        <h4>Support</h4>
                        <p>Get help from our team</p>
                    </div>
                </a>
                <a href="{{ route('landing.community') }}" class="help-card">
                    <div class="help-icon">👥</div>
                    <div class="help-info">
                        <h4>Community</h4>
                        <p>Join discussions</p>
                    </div>
                </a>
            </div>
        </section>
    </main>
</div>

<script>
    // Smooth scroll and active link highlighting
    document.querySelectorAll('.api-nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            document.querySelectorAll('.api-nav a').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Highlight active section on scroll
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('.api-section');
        const navLinks = document.querySelectorAll('.api-nav a');
        
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 150;
            if (scrollY >= sectionTop) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
</script>
@endsection
