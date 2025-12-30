@extends('DemoFrontend::layouts.page')

@section('title', 'Contact Us')
@section('description', 'Get in touch with the TestPilot team')

@section('styles')
<style>
    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-2xl);
        margin-bottom: var(--space-xl);
    }

    .form-group {
        margin-bottom: var(--space-lg);
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: var(--space-sm);
        color: var(--dark);
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: var(--space-md);
        border: 1px solid var(--gray-lighter);
        border-radius: var(--radius-md);
        font-family: inherit;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 150px;
    }

    .submit-btn {
        background: var(--gradient-primary);
        color: white;
        padding: 1rem 2.5rem;
        border: none;
        border-radius: var(--radius-lg);
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
    }

    .contact-card {
        background: white;
        padding: var(--space-xl);
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-lighter);
        text-align: center;
    }

    .contact-icon {
        font-size: 2.5rem;
        margin-bottom: var(--space-md);
    }

    @media (max-width: 768px) {
        .contact-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Contact Us</h1>
        <p class="page-subtitle">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>

    <div class="contact-grid">
        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-2xl); border: 1px solid var(--gray-lighter);">
            <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: var(--space-xl); color: var(--dark);">Send us a Message</h2>
            
            <form action="#" method="POST">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required placeholder="John Doe">
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="john@example.com">
                </div>

                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <select id="subject" name="subject" required>
                        <option value="">Select a subject</option>
                        <option value="general">General Inquiry</option>
                        <option value="support">Technical Support</option>
                        <option value="billing">Billing Question</option>
                        <option value="feature">Feature Request</option>
                        <option value="bug">Bug Report</option>
                        <option value="partnership">Partnership Opportunity</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" required placeholder="Tell us how we can help you..."></textarea>
                </div>

                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>

        <div>
            <div class="contact-card" style="margin-bottom: var(--space-lg);">
                <div class="contact-icon">📧</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Email Us</h3>
                <p style="color: var(--gray); margin-bottom: var(--space-sm);">For general inquiries</p>
                <a href="mailto:support@testpilot.dev" style="color: var(--primary); font-weight: 600;">support@testpilot.dev</a>
            </div>

            <div class="contact-card" style="margin-bottom: var(--space-lg);">
                <div class="contact-icon">💬</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Live Chat</h3>
                <p style="color: var(--gray); margin-bottom: var(--space-sm);">Available Mon-Fri, 9AM-6PM</p>
                <a href="#" style="color: var(--primary); font-weight: 600;">Start Chat</a>
            </div>

            <div class="contact-card">
                <div class="contact-icon">🌐</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Community Forum</h3>
                <p style="color: var(--gray); margin-bottom: var(--space-sm);">Join our community</p>
                <a href="{{ route('landing.community') }}" style="color: var(--primary); font-weight: 600;">Visit Forum</a>
            </div>
        </div>
    </div>

    <div style="background: var(--gray-lightest); border-radius: var(--radius-lg); padding: var(--space-2xl); text-align: center;">
        <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: var(--space-md); color: var(--dark);">Need Immediate Help?</h2>
        <p style="color: var(--gray); margin-bottom: var(--space-lg); font-size: 1.125rem;">
            Check out our comprehensive documentation and support resources
        </p>
        <div style="display: flex; gap: var(--space-md); justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('landing.documentation') }}" style="background: white; color: var(--dark); padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600; border: 1px solid var(--gray-lighter);">Documentation</a>
            <a href="{{ route('landing.support') }}" style="background: white; color: var(--dark); padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600; border: 1px solid var(--gray-lighter);">Support Center</a>
            <a href="{{ route('landing.quick-start') }}" style="background: white; color: var(--dark); padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600; border: 1px solid var(--gray-lighter);">Quick Start</a>
        </div>
    </div>
</div>
@endsection
