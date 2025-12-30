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
            /* Modern Blue Color System */
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --primary-light: #3B82F6;
            --secondary: #0EA5E9;
            --accent: #06B6D4;
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
            --gradient-primary: linear-gradient(135deg, #2563EB 0%, #3B82F6 50%, #0EA5E9 100%);
            --gradient-secondary: linear-gradient(135deg, #06B6D4 0%, #0284C7 100%);
            
            /* Spacing - Compact */
            --space-xs: 0.375rem;
            --space-sm: 0.75rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;
            --space-3xl: 4rem;
            
            /* Border Radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-full: 9999px;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-glow: 0 0 30px rgba(37, 99, 235, 0.2);
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
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.97) 0%, rgba(30, 41, 59, 0.97) 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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
            padding: 0.875rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            text-decoration: none;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3B82F6 0%, #06B6D4 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .logo-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }
            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        .logo-icon:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
        }

        .logo-text {
            background: linear-gradient(90deg, #FFFFFF 0%, #94A3B8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 0.25rem;
        }

        .nav-links a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3B82F6, #06B6D4);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .nav-links a:hover {
            color: #FFFFFF;
        }

        .nav-links a:hover::before {
            width: 100%;
        }

        .nav-cta {
            background: linear-gradient(135deg, #3B82F6 0%, #06B6D4 100%);
            color: #FFFFFF !important;
            padding: 0.65rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-cta::after {
            content: '→';
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .nav-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
        }

        .nav-cta:hover::after {
            transform: translateX(4px);
        }

        /* ========== HERO SECTION ========== */
        .hero {
            margin-top: 70px;
            padding: var(--space-2xl) var(--space-lg) var(--space-xl);
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            top: -200px;
            right: -100px;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
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
            gap: 3rem;
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
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            padding: 0.5rem 1.25rem;
            border-radius: var(--radius-full);
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(37, 99, 235, 0.2);
        }

        .hero-badge-dot {
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7); }
            50% { opacity: 0.8; box-shadow: 0 0 0 8px rgba(37, 99, 235, 0); }
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }

        .hero-highlight {
            position: relative;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 1.125rem;
            line-height: 1.7;
            color: var(--gray);
            margin-bottom: 2rem;
            max-width: 560px;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            padding: 1rem 2.25rem;
            border: none;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl), var(--shadow-glow);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.4);
        }

        .btn-secondary {
            color: var(--dark);
            padding: 1rem 1.75rem;
            font-size: 1rem;
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
            display: flex;
            justify-content: center;
            gap: 4rem;
            text-align: center;
            margin: 2.5rem auto 0;
            width: 100%;
        }

        .hero-stat {
            text-align: center;
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
            padding: var(--space-2xl) var(--space-lg);
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }

        .section-badge {
            display: inline-block;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            padding: 0.5rem 1.25rem;
            border-radius: var(--radius-full);
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .section-title {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }

        .section-description {
            font-size: 1.125rem;
            color: var(--gray);
            max-width: 680px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* Process Timeline */
        .process-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: var(--space-xl);
        }

        .process-step {
            text-align: center;
        }

        .process-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.25rem;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
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
            font-size: 1.125rem;
            margin-bottom: 0.625rem;
            color: var(--dark);
            font-weight: 700;
        }

        .process-description {
            color: var(--gray);
            line-height: 1.6;
            font-size: 0.9375rem;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: var(--space-xl);
        }

        .feature-card {
            background: white;
            border: 1px solid var(--gray-lighter);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-light);
        }

        .feature-icon {
            font-size: 2rem;
            margin-bottom: 1.125rem;
        }

        .feature-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: var(--dark);
            font-weight: 700;
        }

        .feature-description {
            color: var(--gray);
            line-height: 1.65;
            font-size: 0.9375rem;
        }

        /* Why TestPilot Comparison */
        .comparison-section {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            padding: var(--space-3xl) var(--space-lg);
        }

        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 80px 1fr;
            gap: 2rem;
            max-width: 1100px;
            margin: var(--space-xl) auto 0;
            align-items: stretch;
        }

        .comparison-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .comparison-card.manual {
            border-color: rgba(239, 68, 68, 0.3);
        }

        .comparison-card.testpilot {
            border-color: rgba(34, 197, 94, 0.3);
            background: rgba(34, 197, 94, 0.05);
        }

        .comparison-card:hover {
            transform: translateY(-5px);
        }

        .comparison-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .comparison-icon {
            font-size: 1.5rem;
        }

        .comparison-title {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .comparison-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .comparison-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .comparison-list li:last-child {
            margin-bottom: 0;
        }

        .comparison-list .icon-bad {
            color: #EF4444;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .comparison-list .icon-good {
            color: #22C55E;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .comparison-vs {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vs-badge {
            background: linear-gradient(135deg, #3B82F6, #06B6D4);
            color: white;
            font-weight: 800;
            font-size: 1.25rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
        }

        @media (max-width: 768px) {
            .comparison-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .comparison-vs {
                order: -1;
            }
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-primary);
            padding: var(--space-2xl) var(--space-lg);
            text-align: center;
        }

        .cta-title {
            font-size: 2.75rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.25rem;
            letter-spacing: -0.02em;
        }

        .cta-description {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 2rem;
            max-width: 680px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
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
                <span class="logo-text">TestPilot</span>
            </div>
            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#tech">Why TestPilot</a></li>
                <li><a href="{{ route('login') }}" class="nav-cta" style="text-decoration: none;">Get Started</a></li>
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
                    <a href="{{ route('register') }}" class="btn-primary" style="text-decoration: none; display: inline-block;">Start Recording Now</a>
                    <a href="#how-it-works" class="btn-secondary">See How It Works →</a>
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
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-number">85%+</div>
                <div class="hero-stat-label">Time Saved</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-number">10x</div>
                <div class="hero-stat-label">Faster Testing</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-number">100%</div>
                <div class="hero-stat-label">Auto-Generated</div>
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
                <h3 class="feature-title">Browser Automation</h3>
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
                <div class="feature-icon">⚡</div>
                <h3 class="feature-title">Real-Time Test Preview</h3>
                <p class="feature-description">
                    Watch your test code generate live as you interact with your application. Instant visual feedback.
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
                <div class="feature-icon">�</div>
                <h3 class="feature-title">One-Click Export</h3>
                <p class="feature-description">
                    Export your generated tests instantly. Copy to clipboard or download ready-to-run test files.
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
                <div class="feature-icon">�</div>
                <h3 class="feature-title">Assertion Builder</h3>
                <p class="feature-description">
                    Easily add assertions to your tests with point-and-click. Verify text, visibility, and element states.
                </p>
            </div>
        </div>
    </section>

    <!-- Why TestPilot -->
    <section class="comparison-section" id="tech">
        <div style="max-width: 1280px; margin: 0 auto; text-align: center;">
            <div class="section-badge" style="background: rgba(96, 165, 250, 0.2); color: #93C5FD;">Why TestPilot?</div>
            <h2 style="font-size: 2.75rem; font-weight: 800; margin-bottom: 0.75rem; color: #FFFFFF; letter-spacing: -0.02em;">Stop Writing Tests Manually</h2>
            <p class="section-description" style="color: rgba(255, 255, 255, 0.8);">
                See how TestPilot transforms your testing workflow and saves hours of development time
            </p>
        </div>
        <div class="comparison-grid">
            <div class="comparison-card manual">
                <div class="comparison-header">
                    <span class="comparison-icon">📝</span>
                    <span class="comparison-title">Manual Test Writing</span>
                </div>
                <ul class="comparison-list">
                    <li><span class="icon-bad">✗</span> Hours spent writing test code from scratch</li>
                    <li><span class="icon-bad">✗</span> Manually finding and typing element selectors</li>
                    <li><span class="icon-bad">✗</span> Constant debugging of flaky selectors</li>
                    <li><span class="icon-bad">✗</span> Context switching between app and code</li>
                    <li><span class="icon-bad">✗</span> Repetitive boilerplate for each test</li>
                    <li><span class="icon-bad">✗</span> Easy to miss edge cases and interactions</li>
                </ul>
            </div>
            <div class="comparison-vs">
                <div class="vs-badge">VS</div>
            </div>
            <div class="comparison-card testpilot">
                <div class="comparison-header">
                    <span class="comparison-icon">🚀</span>
                    <span class="comparison-title">With TestPilot</span>
                </div>
                <ul class="comparison-list">
                    <li><span class="icon-good">✓</span> Generate complete tests in minutes</li>
                    <li><span class="icon-good">✓</span> Auto-capture optimal selectors as you click</li>
                    <li><span class="icon-good">✓</span> Smart selector priority for stable tests</li>
                    <li><span class="icon-good">✓</span> Record directly in your application</li>
                    <li><span class="icon-good">✓</span> Production-ready code instantly</li>
                    <li><span class="icon-good">✓</span> Capture every interaction automatically</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <h2 class="cta-title">Ready to Automate Your Testing?</h2>
        <p class="cta-description">
            Join development teams worldwide who have eliminated manual test writing with TestPilot's intelligent browser automation and code generation.
        </p>
        <a href="{{ route('register') }}" class="cta-button" style="text-decoration: none;">Start Recording Free</a>
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
                    <li><a href="{{ route('landing.documentation') }}">Documentation</a></li>
                    <li><a href="{{ route('landing.api-reference') }}">API Reference</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Company</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('landing.about') }}">About webcrafter</a></li>
                    <li><a href="{{ route('landing.contact') }}">Contact</a></li>
                    <li><a href="{{ route('landing.blog') }}">Blog</a></li>
                    <li><a href="{{ route('landing.careers') }}">Careers</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Resources</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('landing.quick-start') }}">Quick Start</a></li>
                    <li><a href="{{ route('landing.support') }}">Support</a></li>
                    <li><a href="{{ route('landing.community') }}">Community</a></li>
                    <li><a href="https://github.com" target="_blank">GitHub</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <div>© 2025 TestPilot by webcrafter. All rights reserved.</div>
            <div>Developed by Khaled Saifullah Sadi & Arpa Nihan</div>
        </div>
    </footer>
</body>
</html>
