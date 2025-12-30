@extends('DemoFrontend::layouts.page')

@section('title', 'Documentation')
@section('description', 'Complete documentation for TestPilot browser automation and code generation platform')

@section('styles')
<style>
    /* Documentation Page Styles */
    .doc-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        padding: 4rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .doc-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 80% 50%, rgba(6, 182, 212, 0.15) 0%, transparent 50%);
        pointer-events: none;
    }

    .doc-hero-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
    }

    .doc-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(59, 130, 246, 0.2);
        color: #60A5FA;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .doc-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .doc-hero p {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.7);
        max-width: 600px;
        margin: 0 auto;
    }

    /* Main Layout */
    .doc-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Sidebar */
    .doc-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-lighter);
    }

    .doc-sidebar-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--gray);
        margin-bottom: 1rem;
    }

    .doc-nav {
        list-style: none;
    }

    .doc-nav li {
        margin-bottom: 0.25rem;
    }

    .doc-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--gray);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .doc-nav a:hover {
        background: rgba(59, 130, 246, 0.08);
        color: var(--primary);
    }

    .doc-nav a.active {
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary);
        font-weight: 600;
    }

    .doc-nav-icon {
        font-size: 1.1rem;
    }

    /* Content Area */
    .doc-content {
        min-width: 0;
    }

    .doc-section {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-lighter);
        scroll-margin-top: 100px;
    }

    .doc-section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--gray-lighter);
    }

    .doc-section-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #3B82F6 0%, #06B6D4 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .doc-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .doc-section h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .doc-section h3::before {
        content: '';
        width: 4px;
        height: 20px;
        background: var(--gradient-primary);
        border-radius: 2px;
    }

    .doc-section p {
        color: var(--gray);
        line-height: 1.8;
        margin-bottom: 1rem;
    }

    /* Step Cards */
    .step-cards {
        display: grid;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .step-card {
        display: flex;
        gap: 1rem;
        padding: 1.25rem;
        background: var(--gray-lightest);
        border-radius: 12px;
        border: 1px solid var(--gray-lighter);
        transition: all 0.3s ease;
    }

    .step-card:hover {
        border-color: var(--primary-light);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
    }

    .step-number {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #3B82F6 0%, #06B6D4 100%);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .step-content h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .step-content p {
        font-size: 0.9rem;
        color: var(--gray);
        margin: 0;
        line-height: 1.6;
    }

    /* Code Block */
    .code-block {
        background: #1E293B;
        border-radius: 12px;
        overflow: hidden;
        margin: 1rem 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .code-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: #0F172A;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .code-dots {
        display: flex;
        gap: 0.5rem;
    }

    .code-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .code-dot:nth-child(1) { background: #EF4444; }
    .code-dot:nth-child(2) { background: #F59E0B; }
    .code-dot:nth-child(3) { background: #22C55E; }

    .code-lang {
        font-size: 0.75rem;
        color: #64748B;
        font-weight: 600;
        text-transform: uppercase;
    }

    .code-content {
        padding: 1.25rem;
        font-family: 'Fira Code', monospace;
        font-size: 0.875rem;
        line-height: 1.7;
        color: #E2E8F0;
        overflow-x: auto;
        white-space: pre;
    }

    .code-keyword { color: #C084FC; }
    .code-string { color: #22D3EE; }
    .code-function { color: #FBBF24; }
    .code-comment { color: #64748B; font-style: italic; }

    /* Feature List */
    .feature-list {
        display: grid;
        gap: 0.75rem;
        list-style: none;
        margin-top: 1rem;
        padding: 0;
    }

    .feature-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: var(--gray-lightest);
        border-radius: 8px;
        color: var(--gray);
        font-size: 0.95rem;
    }

    .feature-list .check-icon {
        color: #22C55E;
        font-weight: bold;
        flex-shrink: 0;
    }

    /* Info Card */
    .info-card {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(6, 182, 212, 0.08) 100%);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 12px;
        padding: 1.25rem;
        margin: 1.5rem 0;
    }

    .info-card-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .info-card p {
        color: var(--gray);
        margin: 0;
        font-size: 0.9rem;
    }

    /* Structure Tree */
    .structure-tree {
        background: var(--gray-lightest);
        border-radius: 12px;
        padding: 1.5rem;
        font-family: 'Fira Code', monospace;
        font-size: 0.9rem;
        color: var(--dark);
        margin: 1rem 0;
    }

    .tree-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0;
    }

    .tree-item.level-1 { padding-left: 1.5rem; }
    .tree-item.level-2 { padding-left: 3rem; }

    .tree-icon { font-size: 1.2rem; }
    .tree-label { color: var(--gray); margin-left: 0.5rem; font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Role Cards */
    .role-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-top: 1rem;
    }

    .role-card {
        background: var(--gray-lightest);
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
        border: 1px solid var(--gray-lighter);
    }

    .role-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .role-name {
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .role-desc {
        font-size: 0.85rem;
        color: var(--gray);
    }

    /* Resource Links */
    .resource-links {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-top: 1rem;
    }

    .resource-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: var(--gray-lightest);
        border-radius: 12px;
        border: 1px solid var(--gray-lighter);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .resource-link:hover {
        border-color: var(--primary-light);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }

    .resource-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #3B82F6 0%, #06B6D4 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .resource-info h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.125rem;
    }

    .resource-info p {
        font-size: 0.85rem;
        color: var(--gray);
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .doc-layout {
            grid-template-columns: 1fr;
        }

        .doc-sidebar {
            position: relative;
            top: 0;
        }

        .doc-hero h1 {
            font-size: 2.25rem;
        }

        .role-cards,
        .resource-links {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="doc-hero">
    <div class="doc-hero-content">
        <div class="doc-hero-badge">
            <span>📖</span>
            <span>Documentation</span>
        </div>
        <h1>Learn TestPilot</h1>
        <p>Everything you need to master automated browser testing and intelligent code generation</p>
    </div>
</div>

<!-- Main Layout -->
<div class="doc-layout">
    <!-- Sidebar Navigation -->
    <aside class="doc-sidebar">
        <div class="doc-sidebar-title">On This Page</div>
        <ul class="doc-nav">
            <li><a href="#getting-started" class="active"><span class="doc-nav-icon">🚀</span> Getting Started</a></li>
            <li><a href="#features"><span class="doc-nav-icon">⚙️</span> Features Overview</a></li>
            <li><a href="#code-example"><span class="doc-nav-icon">💻</span> Code Example</a></li>
            <li><a href="#project-structure"><span class="doc-nav-icon">📁</span> Project Structure</a></li>
            <li><a href="#collaboration"><span class="doc-nav-icon">🤝</span> Collaboration</a></li>
            <li><a href="#resources"><span class="doc-nav-icon">📚</span> Resources</a></li>
        </ul>
    </aside>

    <!-- Content -->
    <main class="doc-content">
        <!-- Getting Started -->
        <section id="getting-started" class="doc-section">
            <div class="doc-section-header">
                <div class="doc-section-icon">🚀</div>
                <h2>Getting Started</h2>
            </div>
            <p>TestPilot is a powerful browser automation platform that records your interactions and generates production-ready test code automatically. Get up and running in minutes with these simple steps.</p>
            
            <div class="step-cards">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>Create an Account</h4>
                        <p>Sign up at <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">TestPilot Registration</a> to get started with your free account.</p>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>Create Your First Project</h4>
                        <p>Navigate to the dashboard and create a new test project. Organize your tests into logical modules for better management.</p>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>Start Recording</h4>
                        <p>Click "Start Recording" to launch the browser automation. TestPilot will automatically open your browser - no setup required.</p>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4>Interact with Your Application</h4>
                        <p>Click, type, navigate - every action is captured with intelligent selectors optimized for stability.</p>
                    </div>
                </div>
                <div class="step-card">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <h4>Generate Test Code</h4>
                        <p>Stop recording and instantly get production-ready Cypress test code ready to run in your CI/CD pipeline.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Overview -->
        <section id="features" class="doc-section">
            <div class="doc-section-header">
                <div class="doc-section-icon">⚙️</div>
                <h2>Features Overview</h2>
            </div>

            <h3>Browser Automation (Codegen)</h3>
            <p>TestPilot automatically launches browsers using Puppeteer, similar to Playwright's codegen feature. No manual browser setup required - just click record and start interacting.</p>

            <div class="info-card">
                <div class="info-card-title">
                    <span>💡</span>
                    <span>Pro Tip</span>
                </div>
                <p>TestPilot uses WebSocket for real-time event streaming, ensuring every interaction is captured instantly without delays.</p>
            </div>

            <h3>Intelligent Selector Optimization</h3>
            <p>Our smart selector engine generates stable, maintainable selectors using priority-based selection:</p>
            <ul class="feature-list">
                <li><span class="check-icon">✓</span> <strong>data-testid</strong> - Preferred for test stability</li>
                <li><span class="check-icon">✓</span> <strong>Unique IDs</strong> - When available and stable</li>
                <li><span class="check-icon">✓</span> <strong>ARIA labels</strong> - Accessibility-friendly selectors</li>
                <li><span class="check-icon">✓</span> <strong>CSS classes</strong> - Stable class-based targeting</li>
                <li><span class="check-icon">✓</span> <strong>XPath</strong> - Smart fallback for complex elements</li>
            </ul>

            <h3>Real-Time Event Capture</h3>
            <p>WebSocket-powered real-time event capturing ensures no interaction is missed. Watch your test code generate live as you interact with your application.</p>
        </section>

        <!-- Code Example -->
        <section id="code-example" class="doc-section">
            <div class="doc-section-header">
                <div class="doc-section-icon">💻</div>
                <h2>Code Generation Example</h2>
            </div>
            <p>Here's an example of the production-ready code TestPilot generates from your recorded interactions:</p>

            <div class="code-block">
                <div class="code-header">
                    <div class="code-dots">
                        <div class="code-dot"></div>
                        <div class="code-dot"></div>
                        <div class="code-dot"></div>
                    </div>
                    <span class="code-lang">Cypress</span>
                </div>
                <div class="code-content"><span class="code-keyword">describe</span>(<span class="code-string">'Login Test Suite'</span>, () => {
  <span class="code-keyword">it</span>(<span class="code-string">'should login successfully'</span>, () => {
    <span class="code-comment">// Navigate to login page</span>
    cy.<span class="code-function">visit</span>(<span class="code-string">'https://example.com/login'</span>);
    
    <span class="code-comment">// Enter credentials</span>
    cy.<span class="code-function">get</span>(<span class="code-string">'[data-testid="email-input"]'</span>).<span class="code-function">type</span>(<span class="code-string">'user@example.com'</span>);
    cy.<span class="code-function">get</span>(<span class="code-string">'[data-testid="password-input"]'</span>).<span class="code-function">type</span>(<span class="code-string">'password123'</span>);
    
    <span class="code-comment">// Submit form</span>
    cy.<span class="code-function">get</span>(<span class="code-string">'[data-testid="login-button"]'</span>).<span class="code-function">click</span>();
    
    <span class="code-comment">// Verify successful login</span>
    cy.<span class="code-function">url</span>().<span class="code-function">should</span>(<span class="code-string">'include'</span>, <span class="code-string">'/dashboard'</span>);
  });
});</div>
            </div>

            <p>The generated code follows best practices with proper comments, data-testid selectors, and clear test structure.</p>
        </section>

        <!-- Project Structure -->
        <section id="project-structure" class="doc-section">
            <div class="doc-section-header">
                <div class="doc-section-icon">📁</div>
                <h2>Project Structure</h2>
            </div>
            <p>TestPilot uses a three-level hierarchy to keep your tests organized and manageable:</p>

            <div class="structure-tree">
                <div class="tree-item">
                    <span class="tree-icon">📂</span>
                    <strong>Project</strong>
                    <span class="tree-label">— Top-level container</span>
                </div>
                <div class="tree-item level-1">
                    <span class="tree-icon">📁</span>
                    <strong>Module</strong>
                    <span class="tree-label">— Feature grouping</span>
                </div>
                <div class="tree-item level-2">
                    <span class="tree-icon">📄</span>
                    <strong>Test Case</strong>
                    <span class="tree-label">— Individual test</span>
                </div>
            </div>

            <h3>Projects</h3>
            <p>Top-level containers for related test suites. Perfect for separating different applications or major features.</p>

            <h3>Modules</h3>
            <p>Organize test cases by feature, page, or functionality. Ideal for grouping related tests together.</p>

            <h3>Test Cases</h3>
            <p>Individual test scenarios containing recorded events, generated code, and version history.</p>
        </section>

        <!-- Collaboration -->
        <section id="collaboration" class="doc-section">
            <div class="doc-section-header">
                <div class="doc-section-icon">🤝</div>
                <h2>Collaboration Features</h2>
            </div>
            <p>Share your test projects with team members using role-based permissions:</p>

            <div class="role-cards">
                <div class="role-card">
                    <div class="role-icon">👑</div>
                    <div class="role-name">Owner</div>
                    <div class="role-desc">Full control over project settings and members</div>
                </div>
                <div class="role-card">
                    <div class="role-icon">✏️</div>
                    <div class="role-name">Editor</div>
                    <div class="role-desc">Can create, edit, and delete tests</div>
                </div>
                <div class="role-card">
                    <div class="role-icon">👁️</div>
                    <div class="role-name">Viewer</div>
                    <div class="role-desc">Read-only access to view tests</div>
                </div>
            </div>
        </section>

        <!-- Resources -->
        <section id="resources" class="doc-section">
            <div class="doc-section-header">
                <div class="doc-section-icon">📚</div>
                <h2>Additional Resources</h2>
            </div>
            <p>Explore more resources to get the most out of TestPilot:</p>

            <div class="resource-links">
                <a href="{{ route('landing.api-reference') }}" class="resource-link">
                    <div class="resource-icon">🔌</div>
                    <div class="resource-info">
                        <h4>API Reference</h4>
                        <p>Complete API documentation</p>
                    </div>
                </a>
                <a href="{{ route('landing.quick-start') }}" class="resource-link">
                    <div class="resource-icon">⚡</div>
                    <div class="resource-info">
                        <h4>Quick Start Guide</h4>
                        <p>Get running in 5 minutes</p>
                    </div>
                </a>
                <a href="{{ route('landing.support') }}" class="resource-link">
                    <div class="resource-icon">💬</div>
                    <div class="resource-info">
                        <h4>Support</h4>
                        <p>Get help from our team</p>
                    </div>
                </a>
                <a href="{{ route('landing.community') }}" class="resource-link">
                    <div class="resource-icon">👥</div>
                    <div class="resource-info">
                        <h4>Community</h4>
                        <p>Connect with other users</p>
                    </div>
                </a>
            </div>
        </section>
    </main>
</div>

<script>
    // Smooth scroll and active link highlighting
    document.querySelectorAll('.doc-nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            document.querySelectorAll('.doc-nav a').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Highlight active section on scroll
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('.doc-section');
        const navLinks = document.querySelectorAll('.doc-nav a');
        
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
