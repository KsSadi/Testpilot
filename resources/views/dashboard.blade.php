<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Welcome, {{ Auth::user()->name }}!</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- User Info Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full p-4">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Name</p>
                            <p class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Email Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-full p-4">
                            <i class="fas fa-envelope text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Email</p>
                            <p class="text-lg font-semibold text-gray-800">{{ Auth::user()->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Role Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-full p-4">
                            <i class="fas fa-shield-alt text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Role</p>
                            <p class="text-xl font-bold text-gray-800 capitalize">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-2xl shadow-xl p-8 text-white">
                <h2 class="text-3xl font-bold mb-4">🎉 Welcome to Larakit!</h2>
                <p class="text-lg mb-6">Your authentication system is now fully functional with multiple login methods.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <i class="fas fa-envelope text-2xl mb-2"></i>
                        <p class="font-semibold">Email Login</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <i class="fas fa-mobile-alt text-2xl mb-2"></i>
                        <p class="font-semibold">Mobile/OTP</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <i class="fab fa-google text-2xl mb-2"></i>
                        <p class="font-semibold">Social Login</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <i class="fas fa-cog text-2xl mb-2"></i>
                        <p class="font-semibold">DB Configurable</p>
                    </div>
                </div>
            </div>

            <!-- Account Details -->
            <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Account Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Account Status</p>
                        <p class="text-lg font-semibold text-green-600 capitalize">{{ Auth::user()->status }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Mobile Number</p>
                        <p class="text-lg font-semibold text-gray-800">{{ Auth::user()->mobile ?? 'Not provided' }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Email Verified</p>
                        <p class="text-lg font-semibold {{ Auth::user()->email_verified_at ? 'text-green-600' : 'text-orange-600' }}">
                            {{ Auth::user()->email_verified_at ? 'Yes' : 'No' }}
                        </p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-500">Member Since</p>
                        <p class="text-lg font-semibold text-gray-800">{{ Auth::user()->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
