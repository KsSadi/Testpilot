@extends('DemoFrontend::layouts.page')

@section('title', 'Careers')
@section('description', 'Join the TestPilot team and help build the future of automated testing')

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Careers at webcrafter</h1>
        <p class="page-subtitle">Join us in building the future of automated browser testing</p>
    </div>

    <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-2xl); margin-bottom: var(--space-xl); border: 1px solid var(--gray-lighter); text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-md); color: var(--dark);">Why Work With Us?</h2>
        <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray); max-width: 800px; margin: 0 auto var(--space-2xl);">
            We're a passionate team building tools that make developers' lives easier. Join us if you're excited about automation, AI, and creating exceptional developer experiences.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--space-lg); text-align: left;">
            <div style="background: var(--gray-lightest); padding: var(--space-xl); border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: var(--space-sm);">🚀</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Impactful Work</h3>
                <p style="color: var(--gray); line-height: 1.7;">Build products used by development teams worldwide</p>
            </div>

            <div style="background: var(--gray-lightest); padding: var(--space-xl); border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: var(--space-sm);">💡</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Innovation</h3>
                <p style="color: var(--gray); line-height: 1.7;">Work with cutting-edge technologies and modern stacks</p>
            </div>

            <div style="background: var(--gray-lightest); padding: var(--space-xl); border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: var(--space-sm);">🌱</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Growth</h3>
                <p style="color: var(--gray); line-height: 1.7;">Continuous learning and professional development</p>
            </div>

            <div style="background: var(--gray-lightest); padding: var(--space-xl); border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: var(--space-sm);">🏠</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Flexibility</h3>
                <p style="color: var(--gray); line-height: 1.7;">Remote-friendly with flexible working hours</p>
            </div>
        </div>
    </div>

    <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-xl); color: var(--dark); text-align: center;">Open Positions</h2>

    <div style="display: grid; gap: var(--space-lg); margin-bottom: var(--space-2xl);">
        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-md);">
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-xs); color: var(--dark);">Senior Full-Stack Developer</h3>
                    <div style="display: flex; gap: var(--space-md); color: var(--gray); font-size: 0.9375rem;">
                        <span>💼 Full-time</span>
                        <span>📍 Remote</span>
                        <span>💰 Competitive</span>
                    </div>
                </div>
                <a href="{{ route('landing.contact') }}" style="background: var(--gradient-primary); color: white; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); text-decoration: none; font-weight: 600; white-space: nowrap;">Apply Now</a>
            </div>
            <p style="color: var(--gray); line-height: 1.7; margin-bottom: var(--space-md);">
                We're looking for an experienced full-stack developer to help build and scale TestPilot. You'll work with Laravel, Node.js, Puppeteer, WebSockets, and modern frontend frameworks.
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: var(--space-sm);">
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">Laravel</span>
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">Node.js</span>
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">Puppeteer</span>
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">WebSockets</span>
            </div>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-md);">
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-xs); color: var(--dark);">DevOps Engineer</h3>
                    <div style="display: flex; gap: var(--space-md); color: var(--gray); font-size: 0.9375rem;">
                        <span>💼 Full-time</span>
                        <span>📍 Remote</span>
                        <span>💰 Competitive</span>
                    </div>
                </div>
                <a href="{{ route('landing.contact') }}" style="background: var(--gradient-primary); color: white; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); text-decoration: none; font-weight: 600; white-space: nowrap;">Apply Now</a>
            </div>
            <p style="color: var(--gray); line-height: 1.7; margin-bottom: var(--space-md);">
                Help us build and maintain robust infrastructure for TestPilot. Experience with Docker, Kubernetes, CI/CD, and cloud platforms required.
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: var(--space-sm);">
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">Docker</span>
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">Kubernetes</span>
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">AWS</span>
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">CI/CD</span>
            </div>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-md);">
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-xs); color: var(--dark);">Technical Writer</h3>
                    <div style="display: flex; gap: var(--space-md); color: var(--gray); font-size: 0.9375rem;">
                        <span>💼 Part-time</span>
                        <span>📍 Remote</span>
                        <span>💰 Competitive</span>
                    </div>
                </div>
                <a href="{{ route('landing.contact') }}" style="background: var(--gradient-primary); color: white; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); text-decoration: none; font-weight: 600; white-space: nowrap;">Apply Now</a>
            </div>
            <p style="color: var(--gray); line-height: 1.7; margin-bottom: var(--space-md);">
                Create comprehensive documentation, tutorials, and guides for TestPilot users. Experience with developer tools and technical writing essential.
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: var(--space-sm);">
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">Technical Writing</span>
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">Documentation</span>
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">Developer Tools</span>
            </div>
        </div>
    </div>

    <div style="background: var(--gradient-primary); border-radius: var(--radius-lg); padding: var(--space-2xl); text-align: center; color: white;">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-md);">Don't See Your Role?</h2>
        <p style="font-size: 1.125rem; margin-bottom: var(--space-xl); opacity: 0.95;">
            We're always looking for talented people. Send us your resume and let's talk!
        </p>
        <a href="{{ route('landing.contact') }}" style="background: white; color: var(--primary); padding: 1rem 2rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 700; display: inline-block;">Get in Touch</a>
    </div>
</div>
@endsection
