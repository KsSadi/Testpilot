@extends('DemoFrontend::layouts.page')

@section('title', 'Contact Us')
@section('description', 'Get in touch with the TestPilot team')

@section('styles')
<style>
    /* Contact Page Styles */
    .contact-hero {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        padding: 5rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .contact-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 30% 70%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 70% 30%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
        pointer-events: none;
    }

    .contact-hero-content {
        position: relative;
        z-index: 1;
        max-width: 700px;
        margin: 0 auto;
    }

    .contact-badge {
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

    .contact-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .contact-hero p {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.7;
    }

    /* Main Section */
    .contact-main {
        max-width: 1200px;
        margin: 0 auto;
        padding: 4rem 2rem;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 3rem;
    }

    /* Contact Form */
    .contact-form-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-lighter);
    }

    .form-header {
        margin-bottom: 2rem;
    }

    .form-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .form-header p {
        color: var(--gray);
        font-size: 1rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }

    .form-group label .required {
        color: #EF4444;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--gray-lighter);
        border-radius: 12px;
        font-family: inherit;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--gray-lightest);
    }

    .form-input:focus {
        outline: none;
        border-color: #3B82F6;
        background: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-input::placeholder {
        color: var(--gray-light);
    }

    select.form-input {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.25rem;
        padding-right: 3rem;
    }

    textarea.form-input {
        resize: vertical;
        min-height: 140px;
    }

    .submit-btn {
        width: 100%;
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        color: white;
        padding: 1rem 2rem;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }

    /* Contact Cards */
    .contact-cards {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .contact-card {
        background: white;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid var(--gray-lighter);
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        transition: all 0.3s ease;
    }

    .contact-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        transform: translateY(-3px);
    }

    .contact-card-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .contact-card-content h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.375rem;
    }

    .contact-card-content p {
        color: var(--gray);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .contact-card-link {
        color: #3B82F6;
        font-weight: 600;
        text-decoration: none;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        transition: gap 0.3s ease;
    }

    .contact-card-link:hover {
        gap: 0.625rem;
    }

    /* Office Card */
    .office-card {
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        border-radius: 16px;
        padding: 1.75rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .office-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .office-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }

    .office-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #3B82F6 0%, #06B6D4 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .office-card h3 {
        font-size: 1.25rem;
        font-weight: 700;
        position: relative;
    }

    .office-card p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
        line-height: 1.7;
        position: relative;
    }

    /* Help Section */
    .help-section {
        background: var(--gray-lightest);
        padding: 4rem 2rem;
    }

    .help-container {
        max-width: 1000px;
        margin: 0 auto;
        text-align: center;
    }

    .help-header {
        margin-bottom: 2.5rem;
    }

    .help-header h2 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 0.75rem;
    }

    .help-header p {
        color: var(--gray);
        font-size: 1.125rem;
    }

    .help-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    .help-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid var(--gray-lighter);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .help-card:hover {
        border-color: transparent;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }

    .help-card-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin: 0 auto 1.25rem;
    }

    .help-card h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .help-card p {
        color: var(--gray);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* FAQ Section */
    .faq-section {
        max-width: 800px;
        margin: 0 auto;
        padding: 4rem 2rem;
    }

    .faq-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .faq-header h2 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 0.75rem;
    }

    .faq-header p {
        color: var(--gray);
        font-size: 1.125rem;
    }

    .faq-item {
        background: white;
        border-radius: 14px;
        margin-bottom: 1rem;
        border: 1px solid var(--gray-lighter);
        overflow: hidden;
    }

    .faq-question {
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        font-weight: 600;
        color: var(--dark);
        transition: background 0.3s ease;
    }

    .faq-question:hover {
        background: var(--gray-lightest);
    }

    .faq-toggle {
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #3B82F6;
        transition: transform 0.3s ease;
    }

    .faq-item.active .faq-toggle {
        transform: rotate(45deg);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .faq-item.active .faq-answer {
        max-height: 200px;
    }

    .faq-answer-content {
        padding: 0 1.5rem 1.25rem;
        color: var(--gray);
        line-height: 1.7;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }

        .contact-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .contact-hero h1 {
            font-size: 2.25rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .contact-cards {
            grid-template-columns: 1fr;
        }

        .help-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="contact-hero">
    <div class="contact-hero-content">
        <div class="contact-badge">
            <span>💬</span>
            <span>Get in Touch</span>
        </div>
        <h1>Contact Us</h1>
        <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>
</div>

<!-- Main Contact Section -->
<div class="contact-main">
    <div class="contact-grid">
        <!-- Contact Form -->
        <div class="contact-form-card">
            <div class="form-header">
                <h2>Send us a Message</h2>
                <p>Fill out the form and our team will get back to you within 24 hours.</p>
            </div>

            <form action="#" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name <span class="required">*</span></label>
                        <input type="text" id="firstName" name="firstName" class="form-input" required placeholder="John">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name <span class="required">*</span></label>
                        <input type="text" id="lastName" name="lastName" class="form-input" required placeholder="Doe">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-input" required placeholder="john@example.com">
                </div>

                <div class="form-group">
                    <label for="subject">Subject <span class="required">*</span></label>
                    <select id="subject" name="subject" class="form-input" required>
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
                    <label for="message">Message <span class="required">*</span></label>
                    <textarea id="message" name="message" class="form-input" required placeholder="Tell us how we can help you..."></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <span>Send Message</span>
                    <span>→</span>
                </button>
            </form>
        </div>

        <!-- Contact Cards -->
        <div class="contact-cards">
            <div class="contact-card">
                <div class="contact-card-icon">📧</div>
                <div class="contact-card-content">
                    <h3>Email Us</h3>
                    <p>For general inquiries and support</p>
                    <a href="mailto:support@testpilot.dev" class="contact-card-link">
                        support@testpilot.dev →
                    </a>
                </div>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon">💬</div>
                <div class="contact-card-content">
                    <h3>Live Chat</h3>
                    <p>Available Mon-Fri, 9AM-6PM EST</p>
                    <a href="#" class="contact-card-link">
                        Start a conversation →
                    </a>
                </div>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon">🐦</div>
                <div class="contact-card-content">
                    <h3>Twitter / X</h3>
                    <p>Follow us for updates and tips</p>
                    <a href="#" class="contact-card-link">
                        @TestPilotApp →
                    </a>
                </div>
            </div>

            <div class="office-card">
                <div class="office-card-header">
                    <div class="office-icon">🌐</div>
                    <h3>We're Global</h3>
                </div>
                <p>TestPilot is a fully remote company with team members across the globe. We're available to support you wherever you are.</p>
            </div>
        </div>
    </div>
</div>

<!-- Help Section -->
<section class="help-section">
    <div class="help-container">
        <div class="help-header">
            <h2>Need Immediate Help?</h2>
            <p>Check out our comprehensive resources for quick answers</p>
        </div>
        <div class="help-grid">
            <a href="{{ route('landing.documentation') }}" class="help-card">
                <div class="help-card-icon">📚</div>
                <h3>Documentation</h3>
                <p>Detailed guides and tutorials to help you get the most out of TestPilot.</p>
            </a>
            <a href="{{ route('landing.support') }}" class="help-card">
                <div class="help-card-icon">🎧</div>
                <h3>Support Center</h3>
                <p>Browse our knowledge base for common questions and troubleshooting.</p>
            </a>
            <a href="{{ route('landing.community') }}" class="help-card">
                <div class="help-card-icon">👥</div>
                <h3>Community</h3>
                <p>Join our community forum to connect with other TestPilot users.</p>
            </a>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="faq-header">
        <h2>Frequently Asked Questions</h2>
        <p>Quick answers to common questions</p>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>What's the typical response time?</span>
            <div class="faq-toggle">+</div>
        </div>
        <div class="faq-answer">
            <div class="faq-answer-content">
                We aim to respond to all inquiries within 24 hours during business days. For urgent technical issues, our live chat typically has response times under 5 minutes.
            </div>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>How can I report a bug?</span>
            <div class="faq-toggle">+</div>
        </div>
        <div class="faq-answer">
            <div class="faq-answer-content">
                You can report bugs through this contact form by selecting "Bug Report" as the subject, or directly through your TestPilot dashboard under Settings → Support → Report Issue.
            </div>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Do you offer phone support?</span>
            <div class="faq-toggle">+</div>
        </div>
        <div class="faq-answer">
            <div class="faq-answer-content">
                Phone support is available for Enterprise plan customers. For other plans, we offer email support, live chat, and community forums for assistance.
            </div>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Can I request a demo or consultation?</span>
            <div class="faq-toggle">+</div>
        </div>
        <div class="faq-answer">
            <div class="faq-answer-content">
                Absolutely! Select "General Inquiry" in the contact form and mention you'd like a demo. We offer personalized demos for teams evaluating TestPilot.
            </div>
        </div>
    </div>
</section>

<script>
    // FAQ Toggle
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const item = question.parentElement;
            const isActive = item.classList.contains('active');
            
            // Close all items
            document.querySelectorAll('.faq-item').forEach(faq => {
                faq.classList.remove('active');
            });
            
            // Toggle current item
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });
</script>
@endsection
