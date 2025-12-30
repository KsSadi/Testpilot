@extends('DemoFrontend::layouts.page')

@section('title', 'Quick Start Guide')
@section('description', 'Get started with TestPilot in 5 minutes')

@section('styles')
<style>
    .step-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: var(--space-xl);
        margin-bottom: var(--space-lg);
        border: 1px solid var(--gray-lighter);
        position: relative;
        padding-left: calc(var(--space-xl) + 60px);
    }

    .step-number {
        position: absolute;
        left: var(--space-xl);
        top: var(--space-xl);
        width: 48px;
        height: 48px;
        background: var(--gradient-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: 800;
    }

    .step-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: var(--space-sm);
        color: var(--dark);
    }

    .step-content {
        color: var(--gray);
        line-height: 1.8;
    }

    .code-snippet {
        background: var(--dark);
        color: var(--gray-lightest);
        padding: var(--space-md);
        border-radius: var(--radius-md);
        font-family: 'Fira Code', monospace;
        font-size: 0.9rem;
        margin-top: var(--space-md);
        overflow-x: auto;
    }
</style>
@endsection

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Quick Start Guide</h1>
        <p class="page-subtitle">Get up and running with TestPilot in just 5 minutes</p>
    </div>

    <div class="step-card">
        <div class="step-number">1</div>
        <h2 class="step-title">Create Your Account</h2>
        <div class="step-content">
            <p>Start by creating your free TestPilot account. Click the button below to sign up with email or use social authentication (Google, Facebook, GitHub).</p>
            <div style="margin-top: var(--space-md);">
                <a href="{{ route('register') }}" style="background: var(--gradient-primary); color: white; padding: 0.875rem 2rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 700; display: inline-block;">Create Free Account</a>
            </div>
        </div>
    </div>

    <div class="step-card">
        <div class="step-number">2</div>
        <h2 class="step-title">Create Your First Project</h2>
        <div class="step-content">
            <p>Once logged in, navigate to the dashboard and create a new project. Projects help you organize related test suites.</p>
            <ul style="margin-top: var(--space-md); padding-left: var(--space-xl);">
                <li>Click "New Project" button</li>
                <li>Enter project name (e.g., "E-commerce Tests")</li>
                <li>Add description (optional)</li>
                <li>Click "Create Project"</li>
            </ul>
        </div>
    </div>

    <div class="step-card">
        <div class="step-number">3</div>
        <h2 class="step-title">Create a Module</h2>
        <div class="step-content">
            <p>Within your project, create modules to group related test cases by feature or functionality.</p>
            <ul style="margin-top: var(--space-md); padding-left: var(--space-xl);">
                <li>Open your project</li>
                <li>Click "New Module"</li>
                <li>Name it after the feature (e.g., "User Authentication")</li>
                <li>Save the module</li>
            </ul>
        </div>
    </div>

    <div class="step-card">
        <div class="step-number">4</div>
        <h2 class="step-title">Create a Test Case</h2>
        <div class="step-content">
            <p>Now create your first test case where you'll record actual browser interactions.</p>
            <ul style="margin-top: var(--space-md); padding-left: var(--space-xl);">
                <li>Open your module</li>
                <li>Click "New Test Case"</li>
                <li>Give it a descriptive name (e.g., "Login with valid credentials")</li>
                <li>Enter the starting URL for your test</li>
            </ul>
        </div>
    </div>

    <div class="step-card">
        <div class="step-number">5</div>
        <h2 class="step-title">Start Recording</h2>
        <div class="step-content">
            <p>TestPilot will automatically launch a browser and start capturing your interactions.</p>
            <ul style="margin-top: var(--space-md); padding-left: var(--space-xl);">
                <li>Click "Start Recording" button</li>
                <li>Wait for browser to launch automatically</li>
                <li>Perform your test actions (click, type, navigate)</li>
                <li>Every interaction is captured in real-time</li>
                <li>Click "Stop Recording" when done</li>
            </ul>
            <p style="margin-top: var(--space-md); padding: var(--space-md); background: var(--gray-lightest); border-radius: var(--radius-md); border-left: 4px solid var(--primary);">
                <strong>💡 Tip:</strong> TestPilot uses intelligent selector optimization to generate stable, maintainable test code. It prioritizes data-testid attributes, IDs, and ARIA labels.
            </p>
        </div>
    </div>

    <div class="step-card">
        <div class="step-number">6</div>
        <h2 class="step-title">Generate Test Code</h2>
        <div class="step-content">
            <p>Once you've stopped recording, generate production-ready test code in your preferred format.</p>
            <ul style="margin-top: var(--space-md); padding-left: var(--space-xl);">
                <li>Select format: Cypress or Playwright</li>
                <li>Click "Generate Code"</li>
                <li>Review the generated test code</li>
                <li>Copy to clipboard or export to file</li>
            </ul>
            <div class="code-snippet">describe('Login Test', () => {
  it('should login with valid credentials', () => {
    cy.visit('https://example.com/login');
    cy.get('[data-testid="email"]').type('user@example.com');
    cy.get('[data-testid="password"]').type('password123');
    cy.get('[data-testid="login-button"]').click();
    cy.url().should('include', '/dashboard');
  });
});</div>
        </div>
    </div>

    <div style="background: var(--gray-lightest); border-radius: var(--radius-lg); padding: var(--space-2xl); margin-top: var(--space-2xl); text-align: center;">
        <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: var(--space-md); color: var(--dark);">🎉 That's It!</h2>
        <p style="color: var(--gray); margin-bottom: var(--space-lg); font-size: 1.125rem;">
            You've created your first automated test with TestPilot. Continue exploring our features to get the most out of the platform.
        </p>
        <div style="display: flex; gap: var(--space-md); justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('landing.documentation') }}" style="background: white; color: var(--dark); padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600; border: 1px solid var(--gray-lighter);">Full Documentation</a>
            <a href="{{ route('landing.support') }}" style="background: white; color: var(--dark); padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600; border: 1px solid var(--gray-lighter);">Get Support</a>
            <a href="{{ route('register') }}" style="background: var(--gradient-primary); color: white; padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600;">Start Now</a>
        </div>
    </div>
</div>
@endsection
