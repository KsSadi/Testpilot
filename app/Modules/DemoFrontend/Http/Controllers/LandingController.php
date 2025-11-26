<?php

namespace App\Modules\DemoFrontend\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Setting\Models\Setting;
use App\Modules\User\Models\AuthSetting;

class LandingController
{
    /**
     * Display the landing page
     */
    public function index()
    {
        // Get features from settings or use defaults
        $features = [
            [
                'icon' => 'fa-layer-group',
                'title' => 'Modular Architecture',
                'description' => 'Built with Laravel Modules for clean, maintainable, and scalable code structure.',
                'color' => 'from-blue-500 to-cyan-500'
            ],
            [
                'icon' => 'fa-shield-alt',
                'title' => 'Role & Permissions',
                'description' => 'Complete role-based access control with Spatie Laravel Permission package.',
                'color' => 'from-purple-500 to-pink-500'
            ],
            [
                'icon' => 'fa-cog',
                'title' => 'Dynamic Settings',
                'description' => 'Comprehensive settings system with caching, supporting multiple data types and groups.',
                'color' => 'from-green-500 to-teal-500'
            ],
            [
                'icon' => 'fa-lock',
                'title' => 'Multi-Auth System',
                'description' => 'Email, Mobile OTP, and Social login (Google, Facebook, GitHub) support out of the box.',
                'color' => 'from-orange-500 to-red-500'
            ],
            [
                'icon' => 'fa-user-circle',
                'title' => 'User Management',
                'description' => 'Complete user CRUD with profile management, avatar upload, and password change.',
                'color' => 'from-indigo-500 to-blue-500'
            ],
            [
                'icon' => 'fa-paint-brush',
                'title' => 'Modern UI',
                'description' => 'Beautiful admin dashboard built with TailwindCSS v4 and Font Awesome icons.',
                'color' => 'from-pink-500 to-rose-500'
            ],
            [
                'icon' => 'fa-database',
                'title' => 'Database Ready',
                'description' => 'Pre-configured migrations, seeders, and factories for quick project setup.',
                'color' => 'from-yellow-500 to-amber-500'
            ],
            [
                'icon' => 'fa-rocket',
                'title' => 'Production Ready',
                'description' => 'Optimized for performance with caching, eager loading, and best practices.',
                'color' => 'from-cyan-500 to-blue-500'
            ],
        ];

        $stats = [
            ['label' => 'Modules', 'value' => '5+', 'icon' => 'fa-layer-group'],
            ['label' => 'Settings', 'value' => '61', 'icon' => 'fa-cog'],
            ['label' => 'Permissions', 'value' => '15+', 'icon' => 'fa-shield-alt'],
            ['label' => 'Ready to Use', 'value' => '100%', 'icon' => 'fa-check-circle'],
        ];

        // Authentication methods available
        $authMethods = [
            [
                'name' => 'Email Authentication',
                'enabled' => AuthSetting::getBool('email_login_enabled', true),
                'icon' => 'fa-envelope',
                'color' => 'blue'
            ],
            [
                'name' => 'Mobile OTP',
                'enabled' => AuthSetting::getBool('mobile_login_enabled', false),
                'icon' => 'fa-mobile-alt',
                'color' => 'green'
            ],
            [
                'name' => 'Google Login',
                'enabled' => AuthSetting::getBool('google_login_enabled', false),
                'icon' => 'fab fa-google',
                'color' => 'red'
            ],
            [
                'name' => 'Facebook Login',
                'enabled' => AuthSetting::getBool('facebook_login_enabled', false),
                'icon' => 'fab fa-facebook',
                'color' => 'blue'
            ],
            [
                'name' => 'GitHub Login',
                'enabled' => AuthSetting::getBool('github_login_enabled', false),
                'icon' => 'fab fa-github',
                'color' => 'gray'
            ],
        ];

        return view('DemoFrontend::landing', compact('features', 'stats', 'authMethods'));
    }

    /**
     * Display the documentation page
     */
    public function docs()
    {
        $sections = [
            [
                'id' => 'installation',
                'title' => 'Installation',
                'icon' => 'fa-download',
            ],
            [
                'id' => 'settings',
                'title' => 'Settings System',
                'icon' => 'fa-cog',
            ],
            [
                'id' => 'authentication',
                'title' => 'Authentication',
                'icon' => 'fa-lock',
            ],
            [
                'id' => 'developer',
                'title' => 'Developer Settings',
                'icon' => 'fa-code',
            ],
            [
                'id' => 'backup',
                'title' => 'Backup System',
                'icon' => 'fa-database',
            ],
            [
                'id' => 'permissions',
                'title' => 'Roles & Permissions',
                'icon' => 'fa-shield-alt',
            ],
            [
                'id' => 'modules',
                'title' => 'Module System',
                'icon' => 'fa-layer-group',
            ],
            [
                'id' => 'helpers',
                'title' => 'Helper Functions',
                'icon' => 'fa-wrench',
            ],
        ];

        return view('DemoFrontend::docs', compact('sections'));
    }
}
