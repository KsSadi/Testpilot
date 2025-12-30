@extends('DemoFrontend::layouts.page')

@section('title', 'Community')
@section('description', 'Join the TestPilot community - connect, share, and learn together')

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Community</h1>
        <p class="page-subtitle">Connect with developers worldwide using TestPilot for automated testing</p>
    </div>

    <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-2xl); margin-bottom: var(--space-xl); border: 1px solid var(--gray-lighter); text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-md); color: var(--dark);">Join Our Growing Community</h2>
        <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray); max-width: 800px; margin: 0 auto var(--space-xl);">
            Connect with fellow developers, share your experiences, get help with challenges, and stay updated on the latest TestPilot features and best practices.
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-lg); margin-bottom: var(--space-xl);">
            <div style="padding: var(--space-lg);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">👥</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-bottom: var(--space-xs);">5,000+</div>
                <div style="color: var(--gray);">Active Members</div>
            </div>
            <div style="padding: var(--space-lg);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">💬</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-bottom: var(--space-xs);">1,200+</div>
                <div style="color: var(--gray);">Discussions</div>
            </div>
            <div style="padding: var(--space-lg);">
                <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">🎯</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-bottom: var(--space-xs);">500+</div>
                <div style="color: var(--gray);">Solutions Shared</div>
            </div>
        </div>
    </div>

    <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-xl); color: var(--dark); text-align: center;">Community Channels</h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--space-lg); margin-bottom: var(--space-2xl);">
        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-md);">
                <div style="font-size: 3rem;">💬</div>
                <span style="background: var(--gray-lightest); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); font-size: 0.875rem; color: var(--gray);">Most Active</span>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Discord Server</h3>
            <p style="color: var(--gray); line-height: 1.7; margin-bottom: var(--space-lg);">
                Real-time chat with the community. Get instant help, share tips, and connect with other TestPilot users.
            </p>
            <a href="#" style="display: inline-block; background: #5865F2; color: white; padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600;">Join Discord</a>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="font-size: 3rem; margin-bottom: var(--space-md);">📺</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">YouTube Channel</h3>
            <p style="color: var(--gray); line-height: 1.7; margin-bottom: var(--space-lg);">
                Tutorials, feature demos, and community showcases. Learn from video guides and share your own.
            </p>
            <a href="#" style="display: inline-block; background: #FF0000; color: white; padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600;">Subscribe</a>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="font-size: 3rem; margin-bottom: var(--space-md);">🐦</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Twitter</h3>
            <p style="color: var(--gray); line-height: 1.7; margin-bottom: var(--space-lg);">
                Follow us for updates, tips, and announcements. Share your TestPilot success stories!
            </p>
            <a href="#" style="display: inline-block; background: #1DA1F2; color: white; padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600;">Follow Us</a>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="font-size: 3rem; margin-bottom: var(--space-md);">💻</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">GitHub</h3>
            <p style="color: var(--gray); line-height: 1.7; margin-bottom: var(--space-lg);">
                Contribute to open-source components, report issues, and follow our development roadmap.
            </p>
            <a href="https://github.com" target="_blank" style="display: inline-block; background: #24292F; color: white; padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600;">View on GitHub</a>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="font-size: 3rem; margin-bottom: var(--space-md);">📝</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Blog & Articles</h3>
            <p style="color: var(--gray); line-height: 1.7; margin-bottom: var(--space-lg);">
                Read in-depth articles, tutorials, and case studies from our team and community members.
            </p>
            <a href="{{ route('landing.blog') }}" style="display: inline-block; background: var(--gradient-primary); color: white; padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600;">Read Blog</a>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-xl); border: 1px solid var(--gray-lighter);">
            <div style="font-size: 3rem; margin-bottom: var(--space-md);">🎓</div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Learning Resources</h3>
            <p style="color: var(--gray); line-height: 1.7; margin-bottom: var(--space-lg);">
                Access comprehensive documentation, guides, and API references to master TestPilot.
            </p>
            <a href="{{ route('landing.documentation') }}" style="display: inline-block; background: var(--gray-lightest); color: var(--dark); padding: 0.875rem 1.75rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 600; border: 1px solid var(--gray-lighter);">View Docs</a>
        </div>
    </div>

    <div style="background: var(--gray-lightest); border-radius: var(--radius-lg); padding: var(--space-2xl);">
        <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: var(--space-md); color: var(--dark); text-align: center;">Community Guidelines</h2>
        <p style="color: var(--gray); text-align: center; margin-bottom: var(--space-xl); max-width: 700px; margin-left: auto; margin-right: auto;">
            To keep our community welcoming and productive, we ask all members to follow these simple guidelines:
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-lg);">
            <div style="background: white; padding: var(--space-lg); border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: var(--space-sm);">🤝</div>
                <h4 style="font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Be Respectful</h4>
                <p style="color: var(--gray); line-height: 1.7; font-size: 0.9375rem;">Treat everyone with respect. No harassment, discrimination, or personal attacks.</p>
            </div>

            <div style="background: white; padding: var(--space-lg); border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: var(--space-sm);">💬</div>
                <h4 style="font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Be Helpful</h4>
                <p style="color: var(--gray); line-height: 1.7; font-size: 0.9375rem;">Share knowledge, answer questions, and help others succeed with TestPilot.</p>
            </div>

            <div style="background: white; padding: var(--space-lg); border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: var(--space-sm);">🎯</div>
                <h4 style="font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">Stay On Topic</h4>
                <p style="color: var(--gray); line-height: 1.7; font-size: 0.9375rem;">Keep discussions relevant to TestPilot and automated testing.</p>
            </div>

            <div style="background: white; padding: var(--space-lg); border-radius: var(--radius-md);">
                <div style="font-size: 2rem; margin-bottom: var(--space-sm);">🚫</div>
                <h4 style="font-weight: 700; margin-bottom: var(--space-sm); color: var(--dark);">No Spam</h4>
                <p style="color: var(--gray); line-height: 1.7; font-size: 0.9375rem;">Don't post promotional content or spam. Focus on valuable contributions.</p>
            </div>
        </div>
    </div>

    <div style="background: var(--gradient-primary); border-radius: var(--radius-lg); padding: var(--space-2xl); text-align: center; color: white; margin-top: var(--space-2xl);">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: var(--space-md);">Ready to Get Involved?</h2>
        <p style="font-size: 1.125rem; margin-bottom: var(--space-xl); opacity: 0.95;">
            Join thousands of developers automating their testing with TestPilot
        </p>
        <div style="display: flex; gap: var(--space-md); justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('register') }}" style="background: white; color: var(--primary); padding: 1rem 2rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 700; display: inline-block;">Sign Up Free</a>
            <a href="#" style="background: rgba(255,255,255,0.2); color: white; padding: 1rem 2rem; border-radius: var(--radius-lg); text-decoration: none; font-weight: 700; display: inline-block; backdrop-filter: blur(10px);">Join Discord</a>
        </div>
    </div>
</div>
@endsection
