@extends('DemoFrontend::layouts.page')

@section('title', 'About webcrafter')
@section('description', 'Learn about webcrafter and the TestPilot platform')

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">About webcrafter</h1>
        <p class="page-subtitle">Building the future of automated browser testing and intelligent code generation</p>
    </div>

    <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-2xl); margin-bottom: var(--space-xl); border: 1px solid var(--gray-lighter);">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-lg); color: var(--dark);">Our Mission</h2>
        <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray); margin-bottom: var(--space-md);">
            At webcrafter, we believe that automated testing should be accessible to every development team, regardless of their expertise in test automation. TestPilot was born from the frustration of writing repetitive test code manually and the desire to make browser automation as simple as clicking through your application.
        </p>
        <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray);">
            We're on a mission to eliminate manual test writing by providing intelligent tools that understand how modern web applications work and generate production-ready test code that developers actually want to use.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-lg); margin-bottom: var(--space-xl);">
        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="font-size: 2.5rem; margin-bottom: var(--space-md);">🎯</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Our Vision</h3>
            <p style="color: var(--gray); line-height: 1.7;">
                To become the world's most intuitive and powerful browser automation platform, where test creation is as simple as using your application.
            </p>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="font-size: 2.5rem; margin-bottom: var(--space-md);">💡</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Innovation</h3>
            <p style="color: var(--gray); line-height: 1.7;">
                We leverage cutting-edge technologies like Puppeteer, WebSockets, and AI-powered selector optimization to deliver the best testing experience.
            </p>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="font-size: 2.5rem; margin-bottom: var(--space-md);">🤝</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Collaboration</h3>
            <p style="color: var(--gray); line-height: 1.7;">
                Built for teams. TestPilot includes powerful collaboration features with role-based access control and real-time sharing capabilities.
            </p>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="font-size: 2.5rem; margin-bottom: var(--space-md);">🚀</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Speed</h3>
            <p style="color: var(--gray); line-height: 1.7;">
                Save 80%+ of your testing time. What took hours of manual coding now takes minutes with TestPilot's intelligent automation.
            </p>
        </div>
    </div>

    <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-2xl); margin-bottom: var(--space-xl); border: 1px solid var(--gray-lighter);">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-lg); color: var(--dark);">The Team</h2>
        <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray); margin-bottom: var(--space-lg);">
            TestPilot is developed by a passionate team of software engineers who understand the challenges of modern web development and testing.
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--space-xl);">
            <div style="text-align: center;">
                <div style="width: 120px; height: 120px; background: var(--gradient-primary); border-radius: 50%; margin: 0 auto var(--space-md); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: 800;">KS</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-xs); color: var(--dark);">Khaled Saifullah Sadi</h3>
                <p style="color: var(--gray); font-size: 0.9375rem;">Lead Developer</p>
            </div>
            
            <div style="text-align: center;">
                <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #0EA5E9 0%, #06B6D4 100%); border-radius: 50%; margin: 0 auto var(--space-md); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: 800;">AN</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: var(--space-xs); color: var(--dark);">Arpa Nihan</h3>
                <p style="color: var(--gray); font-size: 0.9375rem;">Co-Developer</p>
            </div>
        </div>
    </div>

    <div style="background: var(--gradient-primary); border-radius: var(--radius-lg); padding: var(--space-2xl); text-align: center; color: white;">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-md);">Join Us on This Journey</h2>
        <p style="font-size: 1.125rem; margin-bottom: var(--space-xl); opacity: 0.95;">
            We're constantly improving TestPilot based on feedback from our community. Your insights help shape the future of automated testing.
        </p>
        <div style="display: flex; gap: var(--space-md); justify-content: center;">
            <a href="{{ route('landing.contact') }}" style="background: white; color: var(--primary); padding: 1rem 2rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 700; display: inline-block;">Contact Us</a>
            <a href="{{ route('landing.careers') }}" style="background: rgba(255,255,255,0.2); color: white; padding: 1rem 2rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 700; display: inline-block; backdrop-filter: blur(10px);">View Careers</a>
        </div>
    </div>
</div>
@endsection
