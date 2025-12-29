<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Recorder | AI-Powered Cypress Test Generation by webcrafter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0066FF;
            --deep-blue: #003D99;
            --sky-blue: #4DA6FF;
            --light-blue: #E6F2FF;
            --accent-cyan: #00D4FF;
            --dark-navy: #0A1929;
            --soft-navy: #132F4C;
            --text-primary: #0A1929;
            --text-secondary: #5A6C7D;
            --white: #FFFFFF;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --spacing-xs: 0.5rem;
            --spacing-sm: 1rem;
            --spacing-md: 2rem;
            --spacing-lg: 4rem;
            --spacing-xl: 6rem;
            --spacing-2xl: 8rem;
            --border-radius-sm: 8px;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--white);
            color: var(--text-primary);
            line-height: 1.6;
            font-weight: 400;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            line-height: 1.2;
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--gray-200);
            z-index: 1000;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.25rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-cyan) 100%);
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 3rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
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
                <div class="logo-icon">AR</div>
                Auto Recorder
            </div>
            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#benefits">Benefits</a></li>
                <li><a href="#team">Team</a></li>
                <li><button class="nav-cta">Start Free Trial</button></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-grid">
            <div class="hero-content">
                <div class="hero-badge">
                    <div class="hero-badge-dot"></div>
                    AI-Powered Test Automation
                </div>
                <h1>
                    Generate Cypress Tests<br>
                    <span class="hero-highlight">Automatically</span>
                </h1>
                <p class="hero-description">
                    Auto Recorder (Codegen) transforms your manual interactions into production-ready Cypress test code instantly. Record once, deploy everywhere with AI-powered code optimization.
                </p>
                <div class="hero-actions">
                    <button class="btn-primary">Start Recording Free</button>
                    <a href="#how-it-works" class="btn-secondary">
                        Watch Demo →
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-number">85%</div>
                        <div class="hero-stat-label">Time Saved</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">100%</div>
                        <div class="hero-stat-label">Code Accuracy</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">Zero</div>
                        <div class="hero-stat-label">Manual Coding</div>
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
                        <div class="code-line"><span class="code-keyword">describe</span>(<span class="code-string">'User Authentication'</span>, () => {</div>
                        <div class="code-line">  <span class="code-function">it</span>(<span class="code-string">'logs in successfully'</span>, () => {</div>
                        <div class="code-line">    cy.<span class="code-function">visit</span>(<span class="code-string">'/login'</span>);</div>
                        <div class="code-line">    <span class="code-comment">// Auto-generated by Codegen</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Section -->
    <section class="trust-section">
        <div class="trust-container">
            <div class="trust-label">Trusted by Development Teams</div>
            <div class="trust-stats">
                <div class="trust-stat">
                    <div class="trust-stat-number">10,000+</div>
                    <div class="trust-stat-label">Tests Generated</div>
                </div>
                <div class="trust-stat">
                    <div class="trust-stat-number">500+</div>
                    <div class="trust-stat-label">Active Users</div>
                </div>
                <div class="trust-stat">
                    <div class="trust-stat-number">99.9%</div>
                    <div class="trust-stat-label">Uptime</div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-header">
            <div class="section-badge">Simple 4-Step Process</div>
            <h2 class="section-title">From URL to Tests in Minutes</h2>
            <p class="section-description">
                Our AI-powered system captures, analyzes, and generates production-ready test code automatically. No coding required.
            </p>
        </div>
        <div class="process-timeline">
            <div class="process-step">
                <div class="process-icon-wrapper">
                    <div class="process-icon">🌐</div>
                    <div class="process-number">1</div>
                </div>
                <h3 class="process-title">Enter System URL</h3>
                <p class="process-description">
                    Simply provide your application URL and click "Start Recording" to initialize the test browser.
                </p>
            </div>
            <div class="process-step">
                <div class="process-icon-wrapper">
                    <div class="process-icon">📹</div>
                    <div class="process-number">2</div>
                </div>
                <h3 class="process-title">Record Interactions</h3>
                <p class="process-description">
                    Interact with your application naturally. Every click, input, and navigation is captured in real-time.
                </p>
            </div>
            <div class="process-step">
                <div class="process-icon-wrapper">
                    <div class="process-icon">🤖</div>
                    <div class="process-number">3</div>
                </div>
                <h3 class="process-title">AI Code Generation</h3>
                <p class="process-description">
                    Our AI instantly converts captured events into clean, maintainable Cypress test code.
                </p>
            </div>
            <div class="process-step">
                <div class="process-icon-wrapper">
                    <div class="process-icon">✅</div>
                    <div class="process-number">4</div>
                </div>
                <h3 class="process-title">Auto Fix & Deploy</h3>
                <p class="process-description">
                    AI-powered code fixing detects and resolves issues automatically before deployment.
                </p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-container">
            <div class="section-header">
                <div class="section-badge">Core Capabilities</div>
                <h2 class="section-title">Everything You Need for Test Automation</h2>
                <p class="section-description">
                    Comprehensive features designed to eliminate manual test writing while ensuring code quality.
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">Real-Time Event Capture</h3>
                    <p class="feature-description">
                        Live recording of UI interactions with precise element detection. Captures clicks, inputs, navigation, and form submissions instantly.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">Smart Element Selection</h3>
                    <p class="feature-description">
                        Intelligent selector generation that creates stable, maintainable locators resistant to UI changes and updates.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🤖</div>
                    <h3 class="feature-title">AI-Powered Code Generation</h3>
                    <p class="feature-description">
                        Advanced AI converts recorded interactions into production-ready Cypress tests following industry best practices.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔧</div>
                    <h3 class="feature-title">Automatic Code Fixing</h3>
                    <p class="feature-description">
                        Built-in AI identifies potential issues, syntax errors, and optimization opportunities, fixing them automatically.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚀</div>
                    <h3 class="feature-title">Instant Deployment</h3>
                    <p class="feature-description">
                        Generated tests are immediately ready for integration into your CI/CD pipeline without additional configuration.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Comprehensive Analytics</h3>
                    <p class="feature-description">
                        Track test coverage, performance metrics, success rates, and identify areas requiring additional testing.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔄</div>
                    <h3 class="feature-title">Cross-Browser Testing</h3>
                    <p class="feature-description">
                        Generated tests work seamlessly across all major browsers with automatic compatibility handling.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💾</div>
                    <h3 class="feature-title">Test History & Versioning</h3>
                    <p class="feature-description">
                        Complete history of all recorded sessions with version control and easy rollback capabilities.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h3 class="feature-title">Customizable Output</h3>
                    <p class="feature-description">
                        Configure code style, assertion preferences, and test structure to match your team's standards.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits" id="benefits">
        <div class="section-header">
            <div class="section-badge">Why Choose Auto Recorder</div>
            <h2 class="section-title">Transform Your Testing Workflow</h2>
        </div>
        <div class="benefits-grid">
            <div class="benefits-visual">
                <div class="benefits-image">
                    <svg width="100%" height="400" viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg">
                        <rect x="50" y="50" width="500" height="300" fill="none" stroke="#0066FF" stroke-width="2" rx="8"/>
                        <circle cx="300" cy="200" r="80" fill="url(#gradient1)" opacity="0.3"/>
                        <circle cx="300" cy="200" r="50" fill="url(#gradient2)"/>
                        <path d="M 250 200 L 280 230 L 350 160" stroke="white" stroke-width="4" fill="none" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0066FF"/>
                                <stop offset="100%" stop-color="#00D4FF"/>
                            </linearGradient>
                            <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0066FF"/>
                                <stop offset="100%" stop-color="#00D4FF"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <div class="benefits-content">
                <ul class="benefits-list">
                    <li class="benefit-item">
                        <div class="benefit-icon">⏱️</div>
                        <div class="benefit-content">
                            <h3>85% Time Reduction</h3>
                            <p>Eliminate hours of manual test writing. What used to take days now takes minutes with automated code generation.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">💰</div>
                        <div class="benefit-content">
                            <h3>Lower Testing Costs</h3>
                            <p>Reduce QA expenses significantly while maintaining higher test coverage and quality standards.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">🎓</div>
                        <div class="benefit-content">
                            <h3>No Coding Expertise Required</h3>
                            <p>Anyone can create tests—no Cypress knowledge needed. Perfect for non-technical team members.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">🔒</div>
                        <div class="benefit-content">
                            <h3>Enterprise-Grade Reliability</h3>
                            <p>AI-powered fixing ensures generated tests are stable, maintainable, and production-ready from day one.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">📈</div>
                        <div class="benefit-content">
                            <h3>Accelerate Release Cycles</h3>
                            <p>Ship faster with confidence. Comprehensive test coverage without bottlenecks in your development pipeline.</p>
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
                Join development teams worldwide who have eliminated manual test writing and accelerated their release cycles with Auto Recorder.
            </p>
            <button class="cta-button">Start Your Free Trial Today</button>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <div class="footer-brand">Auto Recorder</div>
                <p class="footer-description">
                    AI-powered test automation system that transforms user interactions into production-ready Cypress tests. Built by webcrafter.
                </p>
            </div>
            <div class="footer-column">
                <h4>Product</h4>
                <ul class="footer-links">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <li><a href="#benefits">Benefits</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Documentation</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Company</h4>
                <ul class="footer-links">
                    <li><a href="#team">Team</a></li>
                    <li><a href="#">About webcrafter</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Resources</h4>
                <ul class="footer-links">
                    <li><a href="#">API Documentation</a></li>
                    <li><a href="#">Support Center</a></li>
                    <li><a href="#">Community</a></li>
                    <li><a href="#">Tutorials</a></li>
                    <li><a href="#">Changelog</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <div>© 2024 Auto Recorder by webcrafter. All rights reserved.</div>
            <div>Created by Khaled Saifullah Sadi & Arpa Nihan</div>
        </div>
    </footer>
</body>
</html>