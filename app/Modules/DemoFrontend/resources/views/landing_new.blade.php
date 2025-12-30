<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TestPilot | Automated Browser Testing & Intelligent Code Generation</title>
    <meta name="description" content="Record browser interactions and auto-generate production-ready Cypress & Playwright tests. Playwright-style codegen with smart selectors and real-time capture.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Modern Color System */
            --primary: #6366F1;
            --primary-dark: #4F46E5;
            --primary-light: #818CF8;
            --secondary: #EC4899;
            --accent: #14B8A6;
            --success: #10B981;
            
            /* Neutral Colors */
            --dark: #0F172A;
            --dark-light: #1E293B;
            --gray: #64748B;
            --gray-light: #94A3B8;
            --gray-lighter: #E2E8F0;
            --gray-lightest: #F8FAFC;
            --white: #FFFFFF;
            
            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #6366F1 0%, #8B5CF6 50%, #EC4899 100%);
            --gradient-secondary: linear-gradient(135deg, #14B8A6 0%, #06B6D4 100%);
            
            /* Spacing */
            --space-xs: 0.5rem;
            --space-sm: 1rem;
            --space-md: 1.5rem;
            --space-lg: 2rem;
            --space-xl: 3rem;
            --space-2xl: 4rem;
            --space-3xl: 6rem;
            
            /* Border Radius */
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
            --radius-full: 9999px;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-glow: 0 0 40px rgba(99, 102, 241, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--white);
            color: var(--dark);
            line-height: 1.7;
            font-weight: 400;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        /* ========== NAVIGATION ========== */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            z-index: 1000;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            background: var(--gradient-primary);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            font-weight: 800;
            box-shadow: var(--shadow-lg);
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: var(--dark);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-links a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--gradient-primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links a:hover::before {
            width: 100%;
        }

        .nav-cta {
            background: var(--gradient-primary);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: var(--shadow-lg), var(--shadow-glow);
        }

        .nav-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -5px rgba(99, 102, 241, 0.4);
        }

        /* ========== HERO SECTION ========== */
        .hero {
            margin-top: 80px;
            padding: var(--space-3xl) var(--space-lg) var(--space-2xl);
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            top: -200px;
            right: -100px;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -30px) scale(1.05); }
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content {
            animation: fadeIn 0.8s ease 0.2s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            padding: 0.5rem 1.25rem;
            border-radius: var(--radius-full);
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .hero-badge-dot {
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
            50% { opacity: 0.8; box-shadow: 0 0 0 8px rgba(99, 102, 241, 0); }
        }

        .hero h1 {
            font-size: 4.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.1;
        }

        .hero-highlight {
            position: relative;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 1.25rem;
            line-height: 1.8;
            color: var(--gray);
            margin-bottom: 2.5rem;
            max-width: 580px;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            padding: 1.125rem 2.5rem;
            border: none;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl), var(--shadow-glow);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.5);
        }

        .btn-secondary {
            color: var(--dark);
            padding: 1.125rem 2rem;
            font-size: 1.05rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border-radius: var(--radius-lg);
            background: var(--gray-lightest);
        }

        .btn-secondary:hover {
            background: var(--gray-lighter);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .hero-stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .hero-stat-label {
            font-size: 0.95rem;
            color: var(--gray);
            font-weight: 600;
        }

        /* Demo Container */
        .demo-container {
            background: var(--dark);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: fadeIn 0.8s ease 0.4s both;
        }

        .demo-dots {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .demo-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .demo-dot:nth-child(1) { background: #FF5F56; }
        .demo-dot:nth-child(2) { background: #FFBD2E; }
        .demo-dot:nth-child(3) { background: #27C93F; }

        .code-window {
            font-family: 'Fira Code', monospace;
            font-size: 0.95rem;
            line-height: 1.8;
            color: #E2E8F0;
        }

        .code-line {
            white-space: nowrap;
            overflow: hidden;
        }

        .code-keyword { color: #C792EA; }
        .code-function { color: #82AAFF; }
        .code-string { color: #C3E88D; }
        .code-comment { color: #697098; }

        /* ========== SECTIONS ========== */
        .section {
            padding: var(--space-3xl) var(--space-lg);
            max-width: 1280px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--space-2xl);
        }

        .section-badge {
            display: inline-block;
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            padding: 0.5rem 1.25rem;
            border-radius: var(--radius-full);
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-description {
            font-size: 1.25rem;
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Process Timeline */
        .process-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: var(--space-2xl);
        }

        .process-step {
            text-align: center;
        }

        .process-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: var(--shadow-lg);
        }

        .process-number {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 32px;
            height: 32px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--primary);
            box-shadow: var(--shadow-md);
        }

        .process-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--dark);
        }

        .process-description {
            color: var(--gray);
            line-height: 1.6;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: var(--space-2xl);
        }

        .feature-card {
            background: white;
            border: 1px solid var(--gray-lighter);
            border-radius: var(--radius-xl);
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-light);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.35rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .feature-description {
            color: var(--gray);
            line-height: 1.7;
        }

        /* Tech Stack */
        .tech-stack {
            background: var(--dark);
            padding: var(--space-3xl) var(--space-lg);
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 2rem;
            max-width: 1280px;
            margin: var(--space-2xl) auto 0;
        }

        .tech-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            padding: 2rem 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .tech-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
        }

        .tech-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .tech-name {
            color: white;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .tech-description {
            color: var(--gray-light);
            font-size: 0.875rem;
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-primary);
            padding: var(--space-3xl) var(--space-lg);
            text-align: center;
        }

        .cta-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
        }

        .cta-description {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-button {
            background: white;
            color: var(--primary);
            padding: 1.25rem 3rem;
            border: none;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 25px 50px -10px rgba(0, 0, 0, 0.4);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: var(--gray-light);
            padding: var(--space-2xl) var(--space-lg) var(--space-lg);
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-brand {
            font-size: 1.5rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .footer-description {
            color: var(--gray-light);
            line-height: 1.7;
        }

        .footer-column h4 {
            color: white;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: var(--gray-light);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            max-width: 1280px;
            margin: 0 auto;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            color: var(--gray);
            font-size: 0.875rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero h1 { font-size: 3.5rem; }
            .section-title { font-size: 2.5rem; }
            .hero-grid, .features-grid { grid-template-columns: 1fr; }
            .tech-grid { grid-template-columns: repeat(3, 1fr); }
            .process-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .nav-links { display: none; }
            .tech-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon">TP</div>
                TestPilot
            </div>
            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#tech">Technology</a></li>
                <li><button class="nav-cta">Get Started</button></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-grid">
            <div class="hero-content">
                <div class="hero-badge">
                    <div class="hero-badge-dot"></div>
                    Playwright-Style Browser Automation
                </div>
                <h1>
                    Auto-Generate Tests<br>
                    <span class="hero-highlight">While You Click</span>
                </h1>
                <p class="hero-description">
                    Launch browsers automatically, record every interaction, and generate production-ready Cypress & Playwright test code instantly. Zero manual coding required.
                </p>
                <div class="hero-actions">
                    <button class="btn-primary">Start Recording Now</button>
                    <a href="#how-it-works" class="btn-secondary">See How It Works →</a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-number">80%+</div>
                        <div class="hero-stat-label">Time Saved</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">2</div>
                        <div class="hero-stat-label">Test Formats</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">100%</div>
                        <div class="hero-stat-label">Auto-Generated</div>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="demo-container">
                    <div class="demo-dots">
                        <div class="demo-dot"></div>
                        <div class="demo-dot"></div>
                        <div class="demo-dot"></div>
                    </div>
                    <div class="code-window">
                        <div class="code-line"><span class="code-keyword">describe</span>(<span class="code-string">'Recorded Test'</span>, () => {</div>
                        <div class="code-line">  <span class="code-function">it</span>(<span class="code-string">'should execute actions'</span>, () => {</div>
                        <div class="code-line">    cy.<span class="code-function">get</span>(<span class="code-string">'[data-testid="email"]'</span>).<span class="code-function">type</span>(<span class="code-string">'user@test.com'</span>);</div>
                        <div class="code-line">    <span class="code-comment">// ✨ Auto-generated by TestPilot</span></div>
                        <div class="code-line">  });</div>
                        <div class="code-line">});</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section" id="how-it-works">
        <div class="section-header">
            <div class="section-badge">Simple & Powerful</div>
            <h2 class="section-title">From Browser Launch to Production Code</h2>
            <p class="section-description">
                Like Playwright Codegen, but better. Browser auto-launches, captures everything, and generates optimal test code.
            </p>
        </div>
        <div class="process-grid">
            <div class="process-step">
                <div class="process-icon" style="position: relative;">
                    🚀
                    <div class="process-number">1</div>
                </div>
                <h3 class="process-title">Auto Browser Launch</h3>
                <p class="process-description">
                    Enter website URL and click "Start Recording". Puppeteer launches a real browser automatically.
                </p>
            </div>
            <div class="process-step">
                <div class="process-icon" style="position: relative;">
                    🎥
                    <div class="process-number">2</div>
                </div>
                <h3 class="process-title">Interact Naturally</h3>
                <p class="process-description">
                    Click, type, navigate - everything is captured with smart selectors in real-time.
                </p>
            </div>
            <div class="process-step">
                <div class="process-icon" style="position: relative;">
                    ✨
                    <div class="process-number">3</div>
                </div>
                <h3 class="process-title">Smart Code Generation</h3>
                <p class="process-description">
                    Intelligent selector optimization creates stable tests. Choose Cypress or Playwright format.
                </p>
            </div>
            <div class="process-step">
                <div class="process-icon" style="position: relative;">
                    💾
                    <div class="process-number">4</div>
                </div>
                <h3 class="process-title">Download & Deploy</h3>
                <p class="process-description">
                    Save to test case, download as .cy.js or .spec.js file. Ready to run immediately.
                </p>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="section" id="features" style="background: var(--gray-lightest);">
        <div class="section-header">
            <div class="section-badge">Powerful Features</div>
            <h2 class="section-title">Everything You Need for Test Automation</h2>
            <p class="section-description">
                From project organization to AI-enhanced code generation - all the tools you need.
            </p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🎬</div>
                <h3 class="feature-title">Browser Automation (Codegen)</h3>
                <p class="feature-description">
                    Playwright-style auto-launch browser with real-time event capture powered by Puppeteer service.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🧠</div>
                <h3 class="feature-title">Smart Selector Optimizer</h3>
                <p class="feature-description">
                    Priority-based selector generation: data-testid > data-cy > id > aria-label for stable locators.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <h3 class="feature-title">Multi-Format Code Generation</h3>
                <p class="feature-description">
                    Generate Cypress OR Playwright tests from the same recording. Switch formats anytime.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📁</div>
                <h3 class="feature-title">Project Organization</h3>
                <p class="feature-description">
                    Hierarchical structure: Projects → Modules → Test Cases with unlimited organization.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔐</div>
                <h3 class="feature-title">Granular Sharing System</h3>
                <p class="feature-description">
                    Share projects, modules, or test cases with role-based access control (viewer, editor, admin).
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3 class="feature-title">Event Management</h3>
                <p class="feature-description">
                    Edit, delete, reorder captured events. Import events from files with version history.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3 class="feature-title">WebSocket Real-Time Updates</h3>
                <p class="feature-description">
                    See events appear instantly as you interact. Real-time streaming from Node.js service.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎨</div>
                <h3 class="feature-title">Beautiful Code Preview</h3>
                <p class="feature-description">
                    Syntax-highlighted preview with download, copy, and save options. Live preview updates.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📦</div>
                <h3 class="feature-title">Export Test Suites</h3>
                <p class="feature-description">
                    Export entire modules with all test cases as complete Cypress test suite instantly.
                </p>
            </div>
        </div>
    </section>

    <!-- Technology Stack -->
    <section class="tech-stack" id="tech">
        <div style="max-width: 1280px; margin: 0 auto; text-align: center;">
            <div class="section-badge">Powered By</div>
            <h2 class="section-title" style="color: white;">Built on Modern Technology</h2>
            <p class="section-description" style="color: rgba(255, 255, 255, 0.8);">
                Leveraging cutting-edge tools and frameworks for reliable test automation
            </p>
        </div>
        <div class="tech-grid">
            <div class="tech-card">
                <div class="tech-icon">🌲</div>
                <div class="tech-name">Cypress</div>
                <div class="tech-description">E2E Testing</div>
            </div>
            <div class="tech-card">
                <div class="tech-icon">🎭</div>
                <div class="tech-name">Playwright</div>
                <div class="tech-description">Multi-Browser</div>
            </div>
            <div class="tech-card">
                <div class="tech-icon">🔧</div>
                <div class="tech-name">Puppeteer</div>
                <div class="tech-description">Automation</div>
            </div>
            <div class="tech-card">
                <div class="tech-icon">⚡</div>
                <div class="tech-name">WebSocket</div>
                <div class="tech-description">Real-Time</div>
            </div>
            <div class="tech-card">
                <div class="tech-icon">🐘</div>
                <div class="tech-name">Laravel</div>
                <div class="tech-description">Backend</div>
            </div>
            <div class="tech-card">
                <div class="tech-icon">🟢</div>
                <div class="tech-name">Node.js</div>
                <div class="tech-description">Service Layer</div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <h2 class="cta-title">Ready to Automate Your Testing?</h2>
        <p class="cta-description">
            Join development teams worldwide who have eliminated manual test writing with TestPilot's intelligent browser automation and code generation.
        </p>
        <button class="cta-button">Start Recording Free</button>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div>
                <div class="footer-brand">TestPilot</div>
                <p class="footer-description">
                    Browser automation and intelligent test generation platform. Record interactions, generate production-ready Cypress & Playwright tests automatically. Built by webcrafter.
                </p>
            </div>
            <div class="footer-column">
                <h4>Product</h4>
                <ul class="footer-links">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">API Reference</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Company</h4>
                <ul class="footer-links">
                    <li><a href="#">About webcrafter</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Resources</h4>
                <ul class="footer-links">
                    <li><a href="#">Quick Start</a></li>
                    <li><a href="#">Support</a></li>
                    <li><a href="#">Community</a></li>
                    <li><a href="#">GitHub</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <div>© 2024 TestPilot by webcrafter. All rights reserved.</div>
            <div>Created by Khaled Saifullah Sadi & Arpa Nihan</div>
        </div>
    </footer>
</body>
</html>
