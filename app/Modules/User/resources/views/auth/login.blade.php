{{-- 
    Login Page
    - Auto-refreshes CSRF token every 10 minutes to prevent 419 errors
    - Session lifetime: 30 days (43200 minutes)
--}}
@extends('User::layouts.auth')

@section('title', 'Login - ' . config('app.name'))

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <!-- Left Side - Branding -->
        <div class="hidden lg:block">
            <div class="text-center">
                <div class="primary-color rounded-3xl p-8 shadow-2xl inline-block mb-6 relative overflow-hidden">
                    <i class="fas fa-chart-line text-white text-6xl relative z-10"></i>
                    <div class="absolute inset-0 bg-white opacity-20 blur-sm"></div>
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome Back!</h1>
                <p class="text-lg text-gray-600 mb-6">Access your admin dashboard and manage your business with ease</p>
                <div class="grid grid-cols-3 gap-6 mt-8">
                    <div class="text-center">
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-xl inline-block mb-2">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">User Management</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-green-100 text-green-600 p-4 rounded-xl inline-block mb-2">
                            <i class="fas fa-chart-bar text-2xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Analytics</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 text-purple-600 p-4 rounded-xl inline-block mb-2">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Security</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full">
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10 animate-slide-in">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-6">
                    <div class="primary-color rounded-xl p-3 shadow-lg inline-block mb-3 relative overflow-hidden">
                        <i class="fas fa-chart-line text-white text-3xl relative z-10"></i>
                        <div class="absolute inset-0 bg-white opacity-20 blur-sm"></div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ config('app.name') }}</h2>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Sign In</h2>
                    <p class="text-gray-500">Enter your credentials to access your account</p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ $errors->first('error') ?: $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                <!-- Success Messages -->
                @if (session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Info Messages (OTP Sent) -->
                @if (session('info'))
                    <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>{{ session('info') }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="space-y-5">
                        @if (in_array('email', $authConfig['enabled_methods']))
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all text-base">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input type="password" name="password" placeholder="Enter your password" class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all text-base">
                                    <button type="button" data-toggle-password class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="remember" class="w-4 h-4 text-cyan-600 rounded focus:ring-2 focus:ring-cyan-400">
                                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                </label>
                                <a href="#" class="text-sm font-semibold text-cyan-600 hover:text-cyan-700">Forgot Password?</a>
                            </div>
                        @endif

                        <button type="submit" class="w-full btn-primary py-3 text-base font-semibold">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>
                    </div>
                </form>

                @if (isset($authConfig['enabled_methods']['social']) && count($authConfig['enabled_methods']['social']) > 0)
                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500">Or continue with</span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4">
                            @if (in_array('google', $authConfig['enabled_methods']['social']))
                                <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all font-medium text-gray-700">
                                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                    Google
                                </a>
                            @endif

                            @if (in_array('facebook', $authConfig['enabled_methods']['social']))
                                <a href="{{ route('social.redirect', 'facebook') }}" class="flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all font-medium text-gray-700">
                                    <i class="fab fa-facebook text-blue-600 text-lg mr-2"></i>
                                    Facebook
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($authConfig['registration_allowed'])
                    <div class="mt-8 text-center">
                        <p class="text-gray-600">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="font-semibold text-cyan-600 hover:text-cyan-700">Sign Up</a>
                        </p>
                    </div>
                @endif
            </div>

            <p class="text-center text-sm text-gray-500 mt-6">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</div>
@endsection
