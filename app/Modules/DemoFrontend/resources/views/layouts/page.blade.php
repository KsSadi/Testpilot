<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TestPilot') | Browser Testing & Code Generation</title>
    <meta name="description" content="@yield('description', 'Automated browser testing and intelligent code generation platform')">
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
            
            /* Spacing */
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: var(--dark);
            background: var(--gray-lightest);
            line-height: 1.6;
        }

        /* ========== NAVIGATION (Same as Landing Page) ========== */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.97) 0%, rgba(30, 41, 59, 0.97) 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 1000;
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
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
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

        /* Page Container */
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--space-2xl) var(--space-lg);
            padding-top: calc(70px + var(--space-2xl));
            min-height: calc(100vh - 300px);
        }

        .page-header {
            text-align: center;
            margin-bottom: var(--space-2xl);
            padding-bottom: var(--space-xl);
            border-bottom: 2px solid var(--gray-lighter);
        }

        .page-title {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: var(--space-sm);
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            font-size: 1.125rem;
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }

        /* ========== FOOTER (Same as Landing Page) ========== */
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
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .footer-container { grid-template-columns: 1fr; }
            .page-title { font-size: 2rem; }
            .footer-bottom {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navigation (Same as Landing Page) -->
    <nav>
        <div class="nav-container">
            <a href="{{ route('landing.index') }}" class="logo">
                <div class="logo-icon">TP</div>
                <span class="logo-text">TestPilot</span>
            </a>
            <ul class="nav-links">
                <li><a href="{{ route('landing.index') }}#features">Features</a></li>
                <li><a href="{{ route('landing.index') }}#how-it-works">How It Works</a></li>
                <li><a href="{{ route('landing.index') }}#tech">Why TestPilot</a></li>
                <li><a href="{{ route('login') }}" class="nav-cta" style="text-decoration: none;">Get Started</a></li>
            </ul>
        </div>
    </nav>

    @yield('content')

    <!-- Footer (Same as Landing Page) -->
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
                    <li><a href="{{ route('landing.index') }}#features">Features</a></li>
                    <li><a href="{{ route('landing.index') }}#how-it-works">How It Works</a></li>
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

    @yield('scripts')
</body>
</html>
