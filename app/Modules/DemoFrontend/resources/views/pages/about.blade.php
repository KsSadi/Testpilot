@extends('DemoFrontend::layouts.page')

@section('title', 'About TestPilot')
@section('description', 'Learn about TestPilot and our mission to revolutionize automated testing')

@section('styles')
<style>
    /* About Page Styles */
    .about-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        padding: 5rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .about-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
        pointer-events: none;
    }

    .about-hero-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
    }

    .about-badge {
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

    .about-hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 1.25rem;
        letter-spacing: -0.02em;
    }

    .about-hero p {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.8;
        max-width: 650px;
        margin: 0 auto;
    }

    /* Mission Section */
    .mission-section {
        padding: 5rem 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .mission-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: center;
    }

    .mission-content h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .mission-content h2 span {
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .mission-content p {
        font-size: 1.125rem;
        color: var(--gray);
        line-height: 1.9;
        margin-bottom: 1.25rem;
    }

    .mission-visual {
        position: relative;
    }

    .mission-card {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        border-radius: 20px;
        padding: 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .mission-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
        pointer-events: none;
    }

    .mission-card-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #3B82F6 0%, #06B6D4 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
    }

    .mission-card h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .mission-card p {
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.7;
        position: relative;
    }

    /* Values Section */
    .values-section {
        background: var(--gray-lightest);
        padding: 5rem 2rem;
    }

    .values-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .section-header h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 1rem;
    }

    .section-header p {
        font-size: 1.125rem;
        color: var(--gray);
        max-width: 600px;
        margin: 0 auto;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }

    .value-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        border: 1px solid var(--gray-lighter);
        transition: all 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: transparent;
    }

    .value-icon {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1.25rem;
    }

    .value-card h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.75rem;
    }

    .value-card p {
        color: var(--gray);
        font-size: 0.95rem;
        line-height: 1.7;
    }

    /* Stats Section */
    .stats-section {
        padding: 4rem 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
    }

    .stat-item {
        text-align: center;
        padding: 2rem;
    }

    .stat-number {
        font-size: 3.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray);
    }

    /* Team Section */
    .team-section {
        background: white;
        padding: 5rem 2rem;
    }

    .team-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }

    .team-card {
        background: var(--gray-lightest);
        border-radius: 20px;
        padding: 2.5rem;
        text-align: center;
        border: 1px solid var(--gray-lighter);
        transition: all 0.3s ease;
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    }

    .team-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 800;
        color: white;
        position: relative;
    }

    .team-avatar.primary {
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
    }

    .team-avatar.secondary {
        background: linear-gradient(135deg, #06B6D4 0%, #3B82F6 100%);
    }

    .team-avatar::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        z-index: -1;
        opacity: 0.3;
    }

    .team-card h3 {
        font-size: 1.375rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.375rem;
    }

    .team-role {
        color: #3B82F6;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }

    .team-bio {
        color: var(--gray);
        font-size: 0.9rem;
        line-height: 1.7;
    }

    .team-socials {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 1.25rem;
    }

    .team-social {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        border: 1px solid var(--gray-lighter);
    }

    .team-social:hover {
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        border-color: transparent;
        transform: translateY(-2px);
    }

    /* Journey Section */
    .journey-section {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        padding: 5rem 2rem;
        position: relative;
        overflow: hidden;
    }

    .journey-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.1) 0%, transparent 60%);
        pointer-events: none;
    }

    .journey-container {
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .journey-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .journey-header h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 1rem;
    }

    .journey-header p {
        font-size: 1.125rem;
        color: rgba(255, 255, 255, 0.7);
        max-width: 600px;
        margin: 0 auto;
    }

    .timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        max-width: 900px;
        margin: 0 auto;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 40px;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #3B82F6 0%, #8B5CF6 100%);
    }

    .timeline-item {
        text-align: center;
        position: relative;
        flex: 1;
    }

    .timeline-dot {
        width: 20px;
        height: 20px;
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        border-radius: 50%;
        margin: 30px auto 1rem;
        position: relative;
    }

    .timeline-dot::after {
        content: '';
        position: absolute;
        inset: -5px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.3);
    }

    .timeline-year {
        font-size: 1.5rem;
        font-weight: 800;
        color: #60A5FA;
        margin-bottom: 0.5rem;
    }

    .timeline-event {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.95rem;
        line-height: 1.6;
        max-width: 200px;
        margin: 0 auto;
    }

    /* CTA Section */
    .cta-section {
        padding: 5rem 2rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .cta-card {
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        border-radius: 24px;
        padding: 4rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .cta-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    .cta-card h2 {
        font-size: 2.25rem;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 1rem;
        position: relative;
    }

    .cta-card p {
        font-size: 1.125rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 2rem;
        position: relative;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        position: relative;
    }

    .cta-btn {
        padding: 1rem 2rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .cta-btn.primary {
        background: white;
        color: #3B82F6;
    }

    .cta-btn.primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .cta-btn.secondary {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .cta-btn.secondary:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-3px);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .mission-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .values-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .timeline {
            flex-direction: column;
            gap: 2rem;
        }

        .timeline::before {
            top: 0;
            bottom: 0;
            left: 50%;
            right: auto;
            width: 2px;
            height: 100%;
        }

        .timeline-dot {
            margin: 0 auto 1rem;
        }
    }

    @media (max-width: 768px) {
        .about-hero h1 {
            font-size: 2.5rem;
        }

        .values-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }

        .team-grid {
            grid-template-columns: 1fr;
        }

        .cta-buttons {
            flex-direction: column;
        }

        .cta-card {
            padding: 2.5rem 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="about-hero">
    <div class="about-hero-content">
        <div class="about-badge">
            <span>🚀</span>
            <span>Our Story</span>
        </div>
        <h1>About TestPilot</h1>
        <p>Building the future of automated browser testing — where creating tests is as simple as using your application.</p>
    </div>
</div>

<!-- Mission Section -->
<section class="mission-section">
    <div class="mission-grid">
        <div class="mission-content">
            <h2>Our <span>Mission</span></h2>
            <p>At TestPilot, we believe that automated testing should be accessible to every development team, regardless of their expertise in test automation.</p>
            <p>TestPilot was born from the frustration of writing repetitive test code manually and the desire to make browser automation as simple as clicking through your application.</p>
            <p>We're on a mission to eliminate manual test writing by providing intelligent tools that understand how modern web applications work and generate production-ready test code that developers actually want to use.</p>
        </div>
        <div class="mission-visual">
            <div class="mission-card">
                <div class="mission-card-icon">🎯</div>
                <h3>Our Vision</h3>
                <p>To become the world's most intuitive and powerful browser automation platform, where test creation is as simple as using your application — no coding expertise required.</p>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="values-section">
    <div class="values-container">
        <div class="section-header">
            <h2>What Drives Us</h2>
            <p>The core values that guide everything we build at TestPilot</p>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">💡</div>
                <h3>Innovation</h3>
                <p>We leverage cutting-edge technologies like Puppeteer, WebSockets, and AI-powered selectors.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h3>Collaboration</h3>
                <p>Built for teams with role-based access control and real-time sharing capabilities.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">⚡</div>
                <h3>Speed</h3>
                <p>Save 80%+ of testing time. What took hours now takes minutes with TestPilot.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">🛡️</div>
                <h3>Reliability</h3>
                <p>Generate stable, maintainable tests that won't break with every UI change.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number">85%</div>
            <div class="stat-label">Time Saved</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">10x</div>
            <div class="stat-label">Faster Testing</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">100%</div>
            <div class="stat-label">Code Control</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Support Available</div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="team-container">
        <div class="section-header">
            <h2>Meet the Team</h2>
            <p>The passionate engineers behind TestPilot who understand the challenges of modern web development</p>
        </div>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-avatar primary">KS</div>
                <h3>Khaled Saifullah Sadi</h3>
                <div class="team-role">Lead Developer</div>
                <p class="team-bio">Full-stack developer with a passion for creating tools that make developers' lives easier.</p>
                <div class="team-socials">
                    <a href="#" class="team-social">💼</a>
                    <a href="#" class="team-social">🐙</a>
                    <a href="#" class="team-social">🐦</a>
                </div>
            </div>
            <div class="team-card">
                <div class="team-avatar secondary">AN</div>
                <h3>Arpa Nihan</h3>
                <div class="team-role">Co-Developer</div>
                <p class="team-bio">Software engineer focused on building scalable applications and intuitive user experiences.</p>
                <div class="team-socials">
                    <a href="#" class="team-social">💼</a>
                    <a href="#" class="team-social">🐙</a>
                    <a href="#" class="team-social">🐦</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Journey Section -->
<section class="journey-section">
    <div class="journey-container">
        <div class="journey-header">
            <h2>Our Journey</h2>
            <p>From an idea to a powerful testing platform — here's how we got here</p>
        </div>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year">2024</div>
                <div class="timeline-event">Project inception and initial development</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year">2024</div>
                <div class="timeline-event">Core recording engine completed</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year">2025</div>
                <div class="timeline-event">Multi-format code generation launched</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-year">2025</div>
                <div class="timeline-event">Team collaboration features added</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-card">
        <h2>Join Us on This Journey</h2>
        <p>We're constantly improving TestPilot based on feedback from our community. Your insights help shape the future of automated testing.</p>
        <div class="cta-buttons">
            <a href="{{ route('landing.contact') }}" class="cta-btn primary">Contact Us</a>
            <a href="{{ route('landing.careers') }}" class="cta-btn secondary">View Careers</a>
        </div>
    </div>
</section>
@endsection
