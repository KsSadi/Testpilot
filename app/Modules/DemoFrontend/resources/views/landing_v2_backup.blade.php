<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TestPilot | Automated Browser Testing & Code Generation Platform</title>
    <meta name="description" content="Record browser interactions and auto-generate Cypress & Playwright tests. Playwright-style codegen with smart selectors, real-time capture, and team collaboration.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Modern Color Palette */
            --primary: #6366F1;
            --primary-dark: #4F46E5;
            --primary-light: #818CF8;
            --secondary: #EC4899;
            --accent: #14B8A6;
            --success: #10B981;
            --warning: #F59E0B;
            
            /* Neutrals */
            --dark: #0F172A;
            --dark-light: #1E293B;
            --gray: #64748B;
            --gray-light: #94A3B8;
            --gray-lighter: #CBD5E1;
            --gray-lightest: #F1F5F9;
            --white: #FFFFFF;
            
            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #6366F1 0%, #8B5CF6 50%, #EC4899 100%);
            --gradient-secondary: linear-gradient(135deg, #14B8A6 0%, #06B6D4 100%);
            --gradient-dark: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
            
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
            --shadow-glow: 0 0 40px rgba(99, 102, 241, 0.3);
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
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
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
            backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid rgba(203, 213, 225, 0.3);
            z-index: 1000;
            animation: navSlideDown 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes navSlideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
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
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
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
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
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
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
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

        .hero-bg-gradient {
            position: absolute;
            top: -200px;
            right: -100px;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: heroGradientFloat 20s ease-in-out infinite;
            z-index: 0;
        }

        .hero-bg-gradient:nth-child(2) {
            top: auto;
            bottom: -300px;
            left: -200px;
            right: auto;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.1) 0%, transparent 70%);
            animation-delay: -10s;
        }

        @keyframes heroGradientFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
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
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
            backdrop-filter: blur(10px);
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
            animation: badgePulse 2s ease-in-out infinite;
        }

        @keyframes badgePulse {
            0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
            50% { opacity: 0.8; transform: scale(1.1); box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
        }

        .hero h1 {
            font-size: 4.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 80%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
        }

        .hero-highlight {
            position: relative;
            display: inline-block;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-highlight::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 0;
            width: 100%;
            height: 12px;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.3) 0%, rgba(236, 72, 153, 0.3) 100%);
            z-index: -1;
            border-radius: 4px;
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
            align-items: center;
            flex-wrap: wrap;
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
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl), 0 0 30px rgba(99, 102, 241, 0.3);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
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
            gap: 0.75rem;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .hero-stat {
            text-align: left;
        }

        .hero-stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .hero-stat-label {
            font-size: 0.95rem;
            color: var(--gray);
            font-weight: 600;
        }

        /* ========== HERO VISUAL ========== */
        .hero-visual {
            position: relative;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.4s both;
        }

        .demo-container {
            background: var(--dark);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            overflow: hidden;
        }

        .demo-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-primary);
            opacity: 0.05;
            z-index: 0;
        }

        .demo-dots {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .demo-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gray);
        }

        .demo-dot:nth-child(1) { background: #FF5F56; }
        .demo-dot:nth-child(2) { background: #FFBD2E; }
        .demo-dot:nth-child(3) { background: #27C93F; }

        .code-window {
            font-family: 'Fira Code', monospace;
            font-size: 0.95rem;
            line-height: 1.8;
            color: #E2E8F0;
            position: relative;
            z-index: 1;
        }

        .code-line {
            white-space: nowrap;
            overflow: hidden;
            border-right: 2px solid var(--primary);
            width: 0;
            animation: typewriter 1s steps(40) forwards;
        }

        .code-line:nth-child(1) { animation-delay: 0.5s; }
        .code-line:nth-child(2) { animation-delay: 1.2s; }
        .code-line:nth-child(3) { animation-delay: 1.9s; }
        .code-line:nth-child(4) { animation-delay: 2.6s; }

        @keyframes typewriter {
            to { width: 100%; border-right: none; }
        }

        .code-keyword { color: #C792EA; font-weight: 600; }
        .code-function { color: #82AAFF; }
        .code-string { color: #C3E88D; }
        .code-comment { color: #697098; font-style: italic; }

        /* ========== TRUST SECTION ========== */
        .trust-section {
            background: linear-gradient(180deg, var(--gray-lightest) 0%, var(--white) 100%);
            padding: var(--space-xl) var(--space-lg);
            text-align: center;
            border-top: 1px solid var(--gray-lighter);
        }

        .trust-container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .trust-label {
            font-size: 0.875rem;
            color: var(--gray);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 2rem;
        }

        .trust-stats {
            display: flex;
            justify-content: center;
            gap: 5rem;
            flex-wrap: wrap;
        }

        .trust-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .trust-stat-number {
            font-size: 2.25rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .trust-stat-label {
            font-size: 0.95rem;
            color: var(--gray);
            font-weight: 600;
        }
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary-blue);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-cyan));
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-cta {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--deep-blue) 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.2);
        }

        .nav-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 102, 255, 0.3);
        }

        /* Hero Section */
        .hero {
            margin-top: 80px;
            padding: var(--spacing-2xl) 3rem var(--spacing-xl);
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-50px, 50px) scale(1.1); }
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content {
            animation: fadeInLeft 0.8s ease-out 0.2s both;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--light-blue);
            color: var(--primary-blue);
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 2rem;
            border: 1px solid rgba(0, 102, 255, 0.2);
        }

        .hero-badge-dot {
            width: 8px;
            height: 8px;
            background: var(--primary-blue);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .hero h1 {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--dark-navy) 0%, var(--primary-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .hero-highlight {
            position: relative;
            display: inline-block;
        }

        .hero-highlight::after {
            content: '';
            position: absolute;
            bottom: 8px;
            left: 0;
            width: 100%;
            height: 20px;
            background: linear-gradient(90deg, var(--accent-cyan) 0%, var(--sky-blue) 100%);
            opacity: 0.3;
            z-index: -1;
            border-radius: 4px;
        }

        .hero-description {
            font-size: 1.25rem;
            line-height: 1.8;
            color: var(--text-secondary);
            margin-bottom: 3rem;
            max-width: 540px;
        }

        .hero-actions {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--deep-blue) 100%);
            color: white;
            padding: 1.25rem 3rem;
            border: none;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-radius: var(--border-radius-md);
            box-shadow: 0 8px 24px rgba(0, 102, 255, 0.25);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0, 102, 255, 0.35);
        }

        .btn-secondary {
            color: var(--primary-blue);
            font-size: 1.05rem;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: gap 0.3s ease;
            padding: 1.25rem 2rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius-md);
            background: white;
        }

        .btn-secondary:hover {
            gap: 1rem;
            border-color: var(--primary-blue);
            background: var(--light-blue);
        }

        .hero-stats {
            display: flex;
            gap: 3rem;
            margin-top: 3rem;
            padding-top: 3rem;
            border-top: 1px solid var(--gray-200);
        }

        .hero-stat {
            display: flex;
            flex-direction: column;
        }

        .hero-stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .hero-stat-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .hero-visual {
            position: relative;
            animation: fadeInRight 0.8s ease-out 0.4s both;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .demo-container {
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--light-blue) 100%);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 102, 255, 0.15);
        }

        .demo-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--deep-blue) 100%);
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }

        .demo-dots {
            position: absolute;
            top: 12px;
            left: 1.5rem;
            display: flex;
            gap: 0.5rem;
            z-index: 10;
        }

        .demo-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
        }

        .demo-dot:nth-child(1) { background: #FF5F57; }
        .demo-dot:nth-child(2) { background: #FEBC2E; }
        .demo-dot:nth-child(3) { background: #28C840; }

        .code-window {
            background: var(--dark-navy);
            border-radius: var(--border-radius-sm);
            padding: 2rem;
            color: var(--white);
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            line-height: 1.8;
            margin-top: 2rem;
            box-shadow: 0 10px 40px rgba(10, 25, 41, 0.5);
        }

        .code-line {
            animation: typewriter 0.5s steps(40) both;
            white-space: nowrap;
            overflow: hidden;
        }

        .code-line:nth-child(1) { animation-delay: 0.8s; }
        .code-line:nth-child(2) { animation-delay: 1.1s; }
        .code-line:nth-child(3) { animation-delay: 1.4s; }
        .code-line:nth-child(4) { animation-delay: 1.7s; }

        @keyframes typewriter {
            from { width: 0; }
            to { width: 100%; }
        }

        .code-keyword { color: #FF6B9D; }
        .code-function { color: #4ECDC4; }
        .code-string { color: #FFE66D; }
        .code-comment { color: #95A5A6; }

        /* Trust Section */
        .trust-section {
            background: var(--gray-50);
            padding: var(--spacing-lg) 3rem;
            text-align: center;
        }

        .trust-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .trust-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 2rem;
        }

        .trust-stats {
            display: flex;
            justify-content: center;
            gap: 4rem;
            flex-wrap: wrap;
        }

        .trust-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .trust-stat-number {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .trust-stat-label {
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* How It Works */
        .how-it-works {
            padding: var(--spacing-xl) 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }

        .section-badge {
            display: inline-block;
            background: var(--light-blue);
            color: var(--primary-blue);
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 102, 255, 0.2);
        }

        .section-title {
            font-size: 3.5rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .section-description {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .process-timeline {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: var(--spacing-lg);
            position: relative;
        }

        .process-timeline::before {
            content: '';
            position: absolute;
            top: 60px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            z-index: 0;
        }

        .process-step {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .process-icon-wrapper {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            position: relative;
        }

        .process-icon {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.3);
            transition: all 0.4s ease;
        }

        .process-step:hover .process-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 40px rgba(0, 102, 255, 0.4);
        }

        .process-number {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 36px;
            height: 36px;
            background: white;
            border: 3px solid var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary-blue);
            font-size: 0.95rem;
        }

        .process-title {
            font-size: 1.35rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .process-description {
            color: var(--text-secondary);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        /* Features Section */
        .features {
            padding: var(--spacing-xl) 3rem;
            background: linear-gradient(180deg, var(--white) 0%, var(--gray-50) 100%);
        }

        .features-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: var(--spacing-lg);
        }

        .feature-card {
            background: white;
            border: 1px solid var(--gray-200);
            padding: 3rem 2.5rem;
            border-radius: var(--border-radius-lg);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 48px rgba(0, 102, 255, 0.12);
            border-color: var(--primary-blue);
        }

        .feature-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--gray-100) 100%);
            border-radius: var(--border-radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 2rem;
            transition: all 0.4s ease;
        }

        .feature-card:hover .feature-icon {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            transform: rotate(-5deg) scale(1.05);
        }

        .feature-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .feature-description {
            color: var(--text-secondary);
            line-height: 1.8;
            font-size: 0.95rem;
        }

        /* Benefits Section */
        .benefits {
            padding: var(--spacing-xl) 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin-top: var(--spacing-lg);
        }

        .benefits-visual {
            position: relative;
        }

        .benefits-image {
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--gray-100) 100%);
            border-radius: var(--border-radius-lg);
            padding: 3rem;
            border: 1px solid var(--gray-200);
            box-shadow: 0 20px 60px rgba(0, 102, 255, 0.1);
        }

        .benefits-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .benefit-item {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .benefit-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .benefit-content h3 {
            font-size: 1.35rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .benefit-content p {
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* Technology Stack */
        .tech-stack {
            padding: var(--spacing-xl) 3rem;
            background: var(--dark-navy);
            color: white;
        }

        .tech-stack-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: var(--spacing-lg);
        }

        .tech-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2.5rem;
            border-radius: var(--border-radius-lg);
            text-align: center;
            transition: all 0.3s ease;
        }

        .tech-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--accent-cyan);
            transform: translateY(-5px);
        }

        .tech-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        .tech-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .tech-description {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
        }

        /* Team Section */
        .team {
            padding: var(--spacing-xl) 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .team-grid {
            display: flex;
            justify-content: center;
            gap: 4rem;
            margin-top: var(--spacing-lg);
            flex-wrap: wrap;
        }

        .team-member {
            text-align: center;
            transition: transform 0.4s ease;
            max-width: 320px;
        }

        .team-member:hover {
            transform: translateY(-10px);
        }

        .team-avatar {
            width: 220px;
            height: 220px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            margin: 0 auto 2rem;
            position: relative;
            overflow: hidden;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 20px 60px rgba(0, 102, 255, 0.3);
        }

        .team-avatar::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 4rem;
            color: white;
            font-weight: 700;
        }

        .team-member:nth-child(1) .team-avatar::before {
            content: 'KS';
        }

        .team-member:nth-child(2) .team-avatar::before {
            content: 'AN';
        }

        .team-name {
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .team-role {
            color: var(--primary-blue);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .team-company {
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
            background: var(--light-blue);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            display: inline-block;
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--deep-blue) 100%);
            color: white;
            padding: var(--spacing-2xl) 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .cta-content {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .cta-title {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .cta-description {
            font-size: 1.35rem;
            margin-bottom: 3rem;
            opacity: 0.9;
            line-height: 1.7;
        }

        .cta-button {
            background: white;
            color: var(--primary-blue);
            padding: 1.5rem 4rem;
            border: none;
            font-size: 1.15rem;
            font-weight: 700;
            cursor: pointer;
            border-radius: var(--border-radius-md);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        /* Footer */
        footer {
            background: var(--dark-navy);
            color: white;
            padding: var(--spacing-lg) 3rem 2rem;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 4rem;
            margin-bottom: 3rem;
        }

        .footer-brand {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.75rem;
            margin-bottom: 1rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--sky-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .footer-description {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.8;
            font-size: 0.95rem;
        }

        .footer-column h4 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            margin-bottom: 1.5rem;
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--accent-cyan);
        }

        .footer-bottom {
            max-width: 1400px;
            margin: 0 auto;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .process-timeline {
                grid-template-columns: repeat(2, 1fr);
            }

            .process-timeline::before {
                display: none;
            }

            .benefits-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .nav-container {
                padding: 1rem 1.5rem;
            }

            .nav-links {
                display: none;
            }

            .hero {
                padding: var(--spacing-lg) 1.5rem;
            }

            .hero h1 {
                font-size: 3rem;
            }

            .hero-stats {
                flex-direction: column;
                gap: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .process-timeline {
                grid-template-columns: 1fr;
            }

            .footer-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .section-title {
                font-size: 2.5rem;
            }

            .cta-title {
                font-size: 2.5rem;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
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
                <li><a href="#benefits">Benefits</a></li>
                <li><a href="#team">Team</a></li>
                <li><button class="nav-cta">Get Started</button></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
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
                    TestPilot's Browser Automation (Codegen) launches browsers automatically, records every interaction, and generates production-ready Cypress & Playwright test code instantly. Zero manual coding required.
                </p>
                <div class="hero-actions">
                    <button class="btn-primary">Start Recording Now</button>
                    <a href="#how-it-works" class="btn-secondary">
                        See How It Works →
                    </a>
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
                        <div class="code-line">  <span class="code-function">it</span>(<span class="code-string">'should execute recorded actions'</span>, () => {</div>
                        <div class="code-line">    cy.<span class="code-function">get</span>(<span class="code-string">'[data-testid="email"]'</span>).<span class="code-function">type</span>(<span class="code-string">'user@test.com'</span>);</div>
                        <div class="code-line">    <span class="code-comment">// ✨ Auto-generated by TestPilot</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Section -->
    <section class="trust-section">
        <div class="trust-container">
            <div class="trust-label">Powerful Test Automation Platform</div>
            <div class="trust-stats">
                <div class="trust-stat">
                    <div class="trust-stat-number">Projects</div>
                    <div class="trust-stat-label">Organized Hierarchy</div>
                </div>
                <div class="trust-stat">
                    <div class="trust-stat-number">Modules</div>
                    <div class="trust-stat-label">Feature Grouping</div>
                </div>
                <div class="trust-stat">
                    <div class="trust-stat-number">Test Cases</div>
                    <div class="trust-stat-label">Individual Tests</div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-header">
            <div class="section-badge">Simple & Powerful</div>
            <h2 class="section-title">From Browser Launch to Production Code</h2>
            <p class="section-description">
                Like Playwright Codegen, but better. Browser auto-launches, captures everything, and generates optimal test code in multiple formats.
            </p>
        </div>
        <div class="process-timeline">
            <div class="process-step">
                <div class="process-icon-wrapper">
                    <div class="process-icon">🚀</div>
                    <div class="process-number">1</div>
                </div>
                <h3 class="process-title">Auto Browser Launch</h3>
                <p class="process-description">
                    Enter website URL and click "Start Recording". Puppeteer launches a real browser automatically - no setup needed.
                </p>
            </div>
            <div class="process-step">
                <div class="process-icon-wrapper">
                    <div class="process-icon">🎥</div>
                    <div class="process-number">2</div>
                </div>
                <h3 class="process-title">Interact Naturally</h3>
                <p class="process-description">
                    Click, type, navigate - everything is captured with smart selectors. Works on any website, even complex ones.
                </p>
            </div>
            <div class="process-step">
                <div class="process-icon-wrapper">
                    <div class="process-icon">✨</div>
                    <div class="process-number">3</div>
                </div>
                <h3 class="process-title">Smart Code Generation</h3>
                <p class="process-description">
                    Intelligent selector optimization creates stable, maintainable tests. Choose Cypress or Playwright format.
                </p>
            </div>
            <div class="process-step">
                <div class="process-icon-wrapper">
                    <div class="process-icon">💾</div>
                    <div class="process-number">4</div>
                </div>
                <h3 class="process-title">Download & Deploy</h3>
                <p class="process-description">
                    Save to test case, download as .cy.js or .spec.js file, or copy to clipboard. Ready to run immediately.
                </p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-container">
            <div class="section-header">
                <div class="section-badge">Powerful Features</div>
                <h2 class="section-title">Everything You Need for Test Automation</h2>
                <p class="section-description">
                    From project organization to AI-enhanced code generation - all the tools you need in one platform.
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎬</div>
                    <h3 class="feature-title">Browser Automation (Codegen)</h3>
                    <p class="feature-description">
                        Playwright-style auto-launch browser with real-time event capture. Puppeteer-powered service records every interaction automatically.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🧠</div>
                    <h3 class="feature-title">Smart Selector Optimizer</h3>
                    <p class="feature-description">
                        Priority-based selector generation: data-testid > data-cy > id > aria-label. Creates stable, maintainable locators automatically.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔄</div>
                    <h3 class="feature-title">Multi-Format Code Generation</h3>
                    <p class="feature-description">
                        Generate tests in Cypress OR Playwright format from the same recording. Switch formats anytime without re-recording.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">�</div>
                    <h3 class="feature-title">Project Organization</h3>
                    <p class="feature-description">
                        Hierarchical structure: Projects → Modules → Test Cases. Organize unlimited tests with team sharing & granular permissions.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔐</div>
                    <h3 class="feature-title">Granular Sharing System</h3>
                    <p class="feature-description">
                        Share projects, modules, or individual test cases with role-based access (viewer, editor, admin). Accept/reject invitations.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3 class="feature-title">Event Management</h3>
                    <p class="feature-description">
                        Edit, delete, reorder captured events. Import events from files. Save multiple event sessions with version history.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">WebSocket Real-Time Updates</h3>
                    <p class="feature-description">
                        See events appear instantly as you interact with the browser. Real-time streaming from Node.js recording service.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3 class="feature-title">Beautiful Code Preview</h3>
                    <p class="feature-description">
                        Syntax-highlighted preview with download, copy, and save options. Live preview updates as you capture events.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📦</div>
                    <h3 class="feature-title">Export Test Suites</h3>
                    <p class="feature-description">
                        Export entire modules with all test cases as complete Cypress test suite. One-click download of full project structure.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits" id="benefits">
        <div class="section-header">
            <div class="section-badge">Why Choose TestPilot</div>
            <h2 class="section-title">Transform Your Testing Workflow</h2>
            <p class="section-description">
                Eliminate manual test writing, reduce bugs, and ship faster with intelligent automation.
            </p>
        </div>
        <div class="benefits-grid">
            <div class="benefits-visual">
                <div class="benefits-image">
                    <div style="text-align: center; padding: 2rem;">
                        <div style="font-size: 5rem; margin-bottom: 1rem;">🚀</div>
                        <h3 style="font-size: 2rem; color: var(--primary-blue); margin-bottom: 1rem;">Zero Manual Coding</h3>
                        <p style="color: var(--text-secondary); font-size: 1.1rem;">
                            Click, type, interact. TestPilot writes all the code.
                        </p>
                    </div>
                </div>
            </div>
            <div class="benefits-content">
                <ul class="benefits-list">
                    <li class="benefit-item">
                        <div class="benefit-icon">⚡</div>
                        <div class="benefit-content">
                            <h3>Reduce Test Creation Time by 80%</h3>
                            <p>What takes hours manually is completed in minutes. Record once, generate instantly, deploy immediately.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">🎯</div>
                        <div class="benefit-content">
                            <h3>Stable & Maintainable Tests</h3>
                            <p>Smart selector optimization ensures tests survive UI changes. Priority-based selection creates resilient locators.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">🔄</div>
                        <div class="benefit-content">
                            <h3>Multi-Framework Support</h3>
                            <p>Generate Cypress or Playwright tests from the same recording. Switch frameworks without re-recording.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">👥</div>
                        <div class="benefit-content">
                            <h3>Team Collaboration Built-In</h3>
                            <p>Share projects and test cases with granular permissions. Collaborate seamlessly across distributed teams.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">📈</div>
                        <div class="benefit-content">
                            <h3>Accelerate Release Cycles</h3>
                            <p>Ship faster with confidence. Complete test coverage without bottlenecks in your development pipeline.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Technology Stack -->
    <section class="tech-stack">
        <div class="tech-stack-container">
            <div class="section-header">
                <div class="section-badge" style="background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.2);">Powered By</div>
                <h2 class="section-title" style="color: white;">Built on Modern Technology</h2>
                <p class="section-description" style="color: rgba(255,255,255,0.8);">
                    Leveraging cutting-edge tools and frameworks for reliable test automation
                </p>
            </div>
            <div class="tech-grid">
                <div class="tech-card">
                    <div class="tech-icon">🌲</div>
                    <div class="tech-name">Cypress</div>
                    <div class="tech-description">Industry-leading E2E testing framework</div>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">🎭</div>
                    <div class="tech-name">Playwright</div>
                    <div class="tech-description">Modern multi-browser automation</div>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">🔧</div>
                    <div class="tech-name">Puppeteer</div>
                    <div class="tech-description">Headless Chrome automation engine</div>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">⚡</div>
                    <div class="tech-name">WebSocket</div>
                    <div class="tech-description">Real-time event streaming</div>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">🐘</div>
                    <div class="tech-name">Laravel</div>
                    <div class="tech-description">Robust backend framework</div>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">🟢</div>
                    <div class="tech-name">Node.js</div>
                    <div class="tech-description">Browser automation service</div>
                </div>
            </div>
        </div>
    </section>
                <h2 class="section-title" style="color: white;">Built on Modern Technology</h2>
                <p class="section-description" style="color: rgba(255,255,255,0.8);">
                    Leveraging cutting-edge tools and frameworks for reliable test automation
                </p>
            </div>
            <div class="tech-grid">
                <div class="tech-card">
                    <div class="tech-icon">🌲</div>
                    <div class="tech-name">Cypress</div>
                    <div class="tech-description">Industry-leading E2E testing framework</div>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">🤖</div>
                    <div class="tech-name">AI/ML Models</div>
                    <div class="tech-description">Advanced code generation & fixing</div>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">🔧</div>
                    <div class="tech-name">Puppeteer</div>
                    <div class="tech-description">Headless browser automation</div>
                </div>
                <div class="tech-card">
                    <div class="tech-icon">⚡</div>
                    <div class="tech-name">WebDriver</div>
                    <div class="tech-description">Cross-browser compatibility</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team" id="team">
        <div class="section-header">
            <div class="section-badge">Meet the Creators</div>
            <h2 class="section-title">Built by webcrafter</h2>
            <p class="section-description">
                Passionate engineers dedicated to revolutionizing test automation
            </p>
        </div>
        <div class="team-grid">
            <div class="team-member">
                <div class="team-avatar"></div>
                <h3 class="team-name">Khaled Saifullah Sadi</h3>
                <div class="team-role">Co-Creator & Lead Developer</div>
                <div class="team-company">webcrafter</div>
            </div>
            <div class="team-member">
                <div class="team-avatar"></div>
                <h3 class="team-name">Arpa Nihan</h3>
                <div class="team-role">Co-Creator & Product Lead</div>
                <div class="team-company">webcrafter</div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Automate Your Testing?</h2>
            <p class="cta-description">
                Join development teams worldwide who have eliminated manual test writing with TestPilot's intelligent browser automation and code generation.
            </p>
            <button class="cta-button">Start Recording Free</button>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-col">
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
                    <li><a href="#benefits">Benefits</a></li>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">API Reference</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Company</h4>
                <ul class="footer-links">
                    <li><a href="#team">Team</a></li>
                    <li><a href="#">About webcrafter</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Resources</h4>
                <ul class="footer-links">
                    <li><a href="#">Quick Start Guide</a></li>
                    <li><a href="#">Support Center</a></li>
                    <li><a href="#">Community</a></li>
                    <li><a href="#">GitHub</a></li>
                    <li><a href="#">Changelog</a></li>
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