<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #06B6D4;
            --primary-dark: #0891B2;
            --primary-light: #22D3EE;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            font-size: 15px;
        }

        .primary-color {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        }

        .btn-primary {
            background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            box-shadow: 0 10px 20px rgba(6, 182, 212, 0.25);
            transform: translateY(-1px);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    @yield('content')

    @stack('scripts')
    
    <!-- Toggle Password Visibility Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('[data-toggle-password]');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.closest('.relative').querySelector('input');
                    const icon = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Auto-refresh CSRF token every 10 minutes to prevent 419 errors
            // when login page is left open for a long time
            setInterval(function() {
                fetch('/csrf-token')
                    .then(response => response.json())
                    .then(data => {
                        // Update meta tag
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        
                        // Update all CSRF input fields
                        document.querySelectorAll('input[name="_token"]').forEach(input => {
                            input.value = data.csrf_token;
                        });
                        
                        console.log('CSRF token refreshed at:', new Date().toLocaleTimeString());
                    })
                    .catch(error => {
                        console.error('Failed to refresh CSRF token:', error);
                    });
            }, 600000); // 10 minutes in milliseconds
        });
    </script>
</body>
</html>
