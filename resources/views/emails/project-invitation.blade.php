<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            border-radius: 10px;
        }
        .content {
            background: white;
            padding: 30px;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        h1 {
            color: #667eea;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .project-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .project-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #667eea;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }
        .accept-btn {
            background: #10b981;
            color: white;
        }
        .decline-btn {
            background: #ef4444;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .info-item {
            margin: 10px 0;
            font-size: 14px;
        }
        .info-label {
            color: #6b7280;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="header">
                <div class="icon">🎉</div>
                <h1>You've Been Invited!</h1>
                <p style="color: #6b7280;">{{ $share->sharedBy->name }} has invited you to collaborate</p>
            </div>

            <div class="project-info">
                @php
                    $shareable = $share->shareable ?? $share->project;
                    $shareType = $share->share_type ?? 'Project';
                    $shareIcon = $shareType === 'Project' ? '📁' :
                        ($shareType === 'Module' ? '📦' :
                        ($shareType === 'Test Case' ? '🧪' : '📄'));
                @endphp
                
                <div class="project-name">{{ $shareIcon }} {{ $shareable->name }}</div>
                
                @if($shareable->description)
                <p style="color: #6b7280; font-size: 14px; margin: 10px 0;">
                    {{ $shareable->description }}
                </p>
                @endif

                <div class="info-item">
                    <span class="info-label">Type:</span>
                    <span style="font-weight: bold; color: #667eea;">{{ $shareType }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Your Role:</span>
                    <span class="role-badge">{{ $share->role_display }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Invited by:</span>
                    {{ $share->sharedBy->name }} ({{ $share->sharedBy->email }})
                </div>

                @if($share->role === 'editor')
                <p style="margin-top: 15px; font-size: 13px; color: #059669;">
                    ✅ You'll be able to view and edit this {{ strtolower($shareType) }}
                </p>
                @else
                <p style="margin-top: 15px; font-size: 13px; color: #0284c7;">
                    👁️ You'll be able to view this {{ strtolower($shareType) }}
                </p>
                @endif
            </div>

            <div class="button-container">
                <a href="{{ url('/dashboard?action=accept&share=' . $share->id) }}" class="button accept-btn">
                    Accept Invitation
                </a>
                <a href="{{ url('/dashboard?action=reject&share=' . $share->id) }}" class="button decline-btn">
                    Decline
                </a>
            </div>

            <div class="footer">
                <p>This invitation was sent by {{ config('app.name') }}</p>
                <p>If you're having trouble clicking the buttons, copy and paste this URL into your browser:</p>
                <p style="color: #667eea; word-break: break-all;">
                    {{ url('/dashboard') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
