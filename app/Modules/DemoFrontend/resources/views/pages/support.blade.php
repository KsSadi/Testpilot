@extends('DemoFrontend::layouts.page')

@section('title', 'Support Center')
@section('description', 'Get help with TestPilot - FAQs, troubleshooting, and support resources')

@section('styles')
<style>
    .faq-item {
        background: white;
        border-radius: var(--radius-lg);
        padding: var(--space-xl);
        margin-bottom: var(--space-md);
        border: 1px solid var(--gray-lighter);
    }

    .faq-question {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: var(--space-sm);
        color: var(--dark);
        display: flex;
        align-items: start;
        gap: var(--space-sm);
    }

    .faq-answer {
        color: var(--gray);
        line-height: 1.8;
        padding-left: calc(var(--space-sm) + 1.5rem);
    }

    .support-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: var(--space-xl);
        border: 1px solid var(--gray-lighter);
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Support Center</h1>
        <p class="page-subtitle">Find answers to common questions and get help when you need it</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-lg); margin-bottom: var(--space-2xl);">
        <div class="support-card">
            <div style="font-size: 3rem; margin-bottom: var(--space-md);">📚</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Documentation</h3>
            <p style="color: var(--gray); margin-bottom: var(--space-md);">Comprehensive guides and tutorials</p>
            <a href="{{ route('landing.documentation') }}" style="color: var(--primary); font-weight: 600;">View Docs →</a>
        </div>

        <div class="support-card">
            <div style="font-size: 3rem; margin-bottom: var(--space-md);">💬</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Community Forum</h3>
            <p style="color: var(--gray); margin-bottom: var(--space-md);">Connect with other users</p>
            <a href="{{ route('landing.community') }}" style="color: var(--primary); font-weight: 600;">Join Community →</a>
        </div>

        <div class="support-card">
            <div style="font-size: 3rem; margin-bottom: var(--space-md);">📧</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Email Support</h3>
            <p style="color: var(--gray); margin-bottom: var(--space-md);">Get direct help from our team</p>
            <a href="{{ route('landing.contact') }}" style="color: var(--primary); font-weight: 600;">Contact Us →</a>
        </div>

        <div class="support-card">
            <div style="font-size: 3rem; margin-bottom: var(--space-md);">🔍</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">API Reference</h3>
            <p style="color: var(--gray); margin-bottom: var(--space-md);">Complete API documentation</p>
            <a href="{{ route('landing.api-reference') }}" style="color: var(--primary); font-weight: 600;">View API Docs →</a>
        </div>
    </div>

    <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-xl); color: var(--dark);">Frequently Asked Questions</h2>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>How does TestPilot's browser automation work?</span>
        </div>
        <div class="faq-answer">
            TestPilot uses Puppeteer to automatically launch and control browsers, similar to Playwright's codegen feature. When you start recording, we launch a browser instance and inject scripts to capture all your interactions in real-time using WebSocket connections.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>What test formats does TestPilot support?</span>
        </div>
        <div class="faq-answer">
            TestPilot currently generates production-ready code for Cypress and Playwright testing frameworks. Both formats include intelligent selectors, proper assertions, and follow best practices for maintainable test code.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>How does the selector optimization work?</span>
        </div>
        <div class="faq-answer">
            Our intelligent selector engine prioritizes stability and maintainability. It first looks for data-testid attributes, then unique IDs, ARIA labels, stable CSS classes, and uses XPath only as a last resort. This ensures your tests won't break when UI changes.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>Can I share projects with my team?</span>
        </div>
        <div class="faq-answer">
            Yes! TestPilot includes powerful collaboration features with granular role-based permissions. You can share projects with team members as Owner (full control), Editor (can create/modify), or Viewer (read-only access).
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>What browsers are supported?</span>
        </div>
        <div class="faq-answer">
            TestPilot currently supports Chromium-based browsers (Chrome, Edge) through Puppeteer. We're working on adding Firefox and WebKit support in future releases.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>Can I edit recorded events before generating code?</span>
        </div>
        <div class="faq-answer">
            Yes! All recorded events are stored with versioning. You can review, modify, or delete events before generating the final test code. Events are captured in real-time and displayed in your dashboard.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>Is there a limit on the number of tests I can create?</span>
        </div>
        <div class="faq-answer">
            Free accounts can create unlimited projects and test cases. However, there are rate limits on browser automation sessions. Check our pricing page for details on different plan tiers.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>How do I export my generated test code?</span>
        </div>
        <div class="faq-answer">
            After generating code, you can copy it to clipboard with one click or export entire test suites as files. The export feature allows you to download all tests in a module or project at once.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>Can TestPilot handle authentication and sessions?</span>
        </div>
        <div class="faq-answer">
            Absolutely! TestPilot records all interactions including login flows, session management, and authenticated requests. You can create test cases that start from logged-in states or test the entire authentication flow.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span style="color: var(--primary);">Q:</span>
            <span>What if I encounter a bug or have a feature request?</span>
        </div>
        <div class="faq-answer">
            We'd love to hear from you! Please contact us through our support page or join our community forum. Bug reports and feature requests help us improve TestPilot for everyone.
        </div>
    </div>

    <div style="background: var(--gradient-primary); border-radius: var(--radius-lg); padding: var(--space-2xl); text-align: center; color: white; margin-top: var(--space-2xl);">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-md);">Still Need Help?</h2>
        <p style="font-size: 1.125rem; margin-bottom: var(--space-xl); opacity: 0.95;">
            Our support team is here to help. Reach out and we'll get back to you as soon as possible.
        </p>
        <a href="{{ route('landing.contact') }}" style="background: white; color: var(--primary); padding: 1rem 2rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 700; display: inline-block;">Contact Support</a>
    </div>
</div>
@endsection
