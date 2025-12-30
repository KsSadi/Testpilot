@extends('DemoFrontend::layouts.page')

@section('title', 'Blog')
@section('description', 'Latest news, updates, and insights from the TestPilot team')

@section('styles')
<style>
    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: var(--space-xl);
        margin-bottom: var(--space-xl);
    }

    .blog-card {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid var(--gray-lighter);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .blog-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
    }

    .blog-image {
        width: 100%;
        height: 200px;
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
    }

    .blog-content {
        padding: var(--space-xl);
    }

    .blog-meta {
        display: flex;
        gap: var(--space-md);
        margin-bottom: var(--space-md);
        font-size: 0.875rem;
        color: var(--gray);
    }

    .blog-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: var(--space-sm);
        color: var(--dark);
    }

    .blog-excerpt {
        color: var(--gray);
        line-height: 1.7;
        margin-bottom: var(--space-md);
    }

    .read-more {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Blog</h1>
        <p class="page-subtitle">Stay updated with the latest news, features, and best practices from TestPilot</p>
    </div>

    <div class="blog-grid">
        <div class="blog-card">
            <div class="blog-image">🚀</div>
            <div class="blog-content">
                <div class="blog-meta">
                    <span>Dec 30, 2025</span>
                    <span>•</span>
                    <span>Product Updates</span>
                </div>
                <h2 class="blog-title">Introducing TestPilot 2.0</h2>
                <p class="blog-excerpt">
                    We're excited to announce TestPilot 2.0 with enhanced AI-powered selector optimization, real-time WebSocket event capture, and support for both Cypress and Playwright code generation.
                </p>
                <a href="#" class="read-more">Read More →</a>
            </div>
        </div>

        <div class="blog-card">
            <div class="blog-image" style="background: linear-gradient(135deg, #0EA5E9 0%, #06B6D4 100%);">🎯</div>
            <div class="blog-content">
                <div class="blog-meta">
                    <span>Dec 25, 2025</span>
                    <span>•</span>
                    <span>Best Practices</span>
                </div>
                <h2 class="blog-title">Best Practices for Stable Test Selectors</h2>
                <p class="blog-excerpt">
                    Learn how TestPilot's intelligent selector engine prioritizes data-testid attributes and ARIA labels to create maintainable test code that won't break with UI changes.
                </p>
                <a href="#" class="read-more">Read More →</a>
            </div>
        </div>

        <div class="blog-card">
            <div class="blog-image" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">💡</div>
            <div class="blog-content">
                <div class="blog-meta">
                    <span>Dec 20, 2025</span>
                    <span>•</span>
                    <span>Tutorials</span>
                </div>
                <h2 class="blog-title">From Zero to Testing Hero in 10 Minutes</h2>
                <p class="blog-excerpt">
                    Complete guide to getting started with TestPilot. Create your first project, record interactions, and generate production-ready test code in minutes.
                </p>
                <a href="#" class="read-more">Read More →</a>
            </div>
        </div>

        <div class="blog-card">
            <div class="blog-image" style="background: linear-gradient(135deg, #8B5CF6 0%, #6366F1 100%);">🔧</div>
            <div class="blog-content">
                <div class="blog-meta">
                    <span>Dec 15, 2025</span>
                    <span>•</span>
                    <span>Engineering</span>
                </div>
                <h2 class="blog-title">How We Built Real-Time Event Capture</h2>
                <p class="blog-excerpt">
                    Deep dive into the technical architecture behind TestPilot's WebSocket-powered event capture system and why it matters for your testing workflow.
                </p>
                <a href="#" class="read-more">Read More →</a>
            </div>
        </div>

        <div class="blog-card">
            <div class="blog-image" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">🤝</div>
            <div class="blog-content">
                <div class="blog-meta">
                    <span>Dec 10, 2025</span>
                    <span>•</span>
                    <span>Collaboration</span>
                </div>
                <h2 class="blog-title">Team Collaboration Features</h2>
                <p class="blog-excerpt">
                    Discover how TestPilot's granular sharing permissions and role-based access control help teams work together on test automation projects.
                </p>
                <a href="#" class="read-more">Read More →</a>
            </div>
        </div>

        <div class="blog-card">
            <div class="blog-image" style="background: linear-gradient(135deg, #EC4899 0%, #DB2777 100%);">📊</div>
            <div class="blog-content">
                <div class="blog-meta">
                    <span>Dec 5, 2025</span>
                    <span>•</span>
                    <span>Case Studies</span>
                </div>
                <h2 class="blog-title">How Teams Save 80% Testing Time</h2>
                <p class="blog-excerpt">
                    Real-world case studies showing how development teams eliminated manual test writing and dramatically reduced their QA time using TestPilot.
                </p>
                <a href="#" class="read-more">Read More →</a>
            </div>
        </div>
    </div>

    <div style="background: var(--gray-lightest); border-radius: var(--radius-lg); padding: var(--space-2xl); text-align: center;">
        <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: var(--space-md); color: var(--dark);">Want to Contribute?</h2>
        <p style="color: var(--gray); margin-bottom: var(--space-lg); font-size: 1.125rem;">
            Share your TestPilot experiences, tips, and insights with the community
        </p>
        <a href="{{ route('landing.contact') }}" style="background: var(--gradient-primary); color: white; padding: 1rem 2rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 700; display: inline-block;">Submit a Guest Post</a>
    </div>
</div>
@endsection
