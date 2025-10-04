<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Hostel CRM</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-primary: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            --bg-secondary: rgba(255, 255, 255, 0.1);
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.4);
            --input-bg: #ffffff;
            --input-border: #d1d5db;
            --button-bg: #2563eb;
            --button-hover: #1d4ed8;
            --scrollbar-track: #f1f5f9;
            --scrollbar-thumb: #cbd5e1;
            --scrollbar-thumb-hover: #94a3b8;
        }

        [data-theme="dark"] {
            --bg-primary: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            --bg-secondary: rgba(0, 0, 0, 0.2);
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --glass-bg: rgba(0, 0, 0, 0.75);
            --glass-border: rgba(255, 255, 255, 0.3);
            --input-bg: #1f2937;
            --input-border: #374151;
            --button-bg: #3b82f6;
            --button-hover: #2563eb;
            --scrollbar-track: #1f2937;
            --scrollbar-thumb: #4b5563;
            --scrollbar-thumb-hover: #6b7280;
        }

        /* Global Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--scrollbar-track, #f1f5f9);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb, #cbd5e1);
            border-radius: 10px;
            border: 2px solid var(--scrollbar-track, #f1f5f9);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-hover, #94a3b8);
        }

        ::-webkit-scrollbar-corner {
            background: var(--scrollbar-track, #f1f5f9);
        }

        /* Firefox Scrollbar Styling */
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb, #cbd5e1) var(--scrollbar-track, #f1f5f9);
        }

        /* Custom scrollbar for specific elements */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb, #cbd5e1) var(--scrollbar-track, #f1f5f9);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb, #cbd5e1);
            border-radius: 10px;
            border: none;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-hover, #94a3b8);
        }

        .gradient-bg {
            background: var(--bg-primary);
            position: relative;
            min-height: 100vh;
        }

        .glass-effect {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
        }

        /* Force input backgrounds to always be white */
        input[type="email"], input[type="password"] {
            background-color: #ffffff !important;
        }

        [data-theme="dark"] input[type="email"],
        [data-theme="dark"] input[type="password"] {
            background-color: #ffffff !important;
            color: #1f2937 !important;
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Animated Background Elements */
        .animated-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .morphing-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(1px);
            opacity: 0.7;
            animation: morph 8s ease-in-out infinite;
        }

        .shape-1 {
            width: 200px;
            height: 200px;
            background: linear-gradient(45deg, #f8fafc, #e2e8f0);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 150px;
            height: 150px;
            background: linear-gradient(45deg, #1f2937, #374151);
            top: 60%;
            right: 15%;
            animation-delay: -2s;
        }

        .shape-3 {
            width: 180px;
            height: 180px;
            background: linear-gradient(45deg, #ffffff, #f1f5f9);
            bottom: 20%;
            left: 20%;
            animation-delay: -4s;
        }

        .shape-4 {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #4b5563, #6b7280);
            top: 30%;
            right: 30%;
            animation-delay: -6s;
        }

        .shape-5 {
            width: 160px;
            height: 160px;
            background: linear-gradient(45deg, #e5e7eb, #d1d5db);
            bottom: 40%;
            right: 10%;
            animation-delay: -1s;
        }

        .shape-6 {
            width: 140px;
            height: 140px;
            background: linear-gradient(45deg, #111827, #1f2937);
            top: 70%;
            left: 5%;
            animation-delay: -3s;
        }

        .shape-7 {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #f9fafb, #ffffff);
            top: 15%;
            left: 50%;
            animation-delay: -5s;
        }

        .shape-8 {
            width: 220px;
            height: 220px;
            background: linear-gradient(45deg, #9ca3af, #d1d5db);
            bottom: 10%;
            right: 40%;
            animation-delay: -7s;
        }

        @keyframes morph {
            0%, 100% {
                border-radius: 50%;
                transform: rotate(0deg) scale(1);
            }
            25% {
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                transform: rotate(90deg) scale(1.1);
            }
            50% {
                border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%;
                transform: rotate(180deg) scale(0.9);
            }
            75% {
                border-radius: 40% 60% 60% 40% / 60% 40% 60% 40%;
                transform: rotate(270deg) scale(1.05);
            }
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            25% { transform: translateY(-20px) translateX(10px); }
            50% { transform: translateY(-10px) translateX(-5px); }
            75% { transform: translateY(-30px) translateX(15px); }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 0.3; }
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .morphing-shape {
                filter: blur(2px);
                opacity: 0.5;
            }

            .shape-1, .shape-3, .shape-8 {
                width: 120px;
                height: 120px;
            }

            .shape-2, .shape-6 {
                width: 100px;
                height: 100px;
            }

            .shape-4, .shape-7 {
                width: 80px;
                height: 80px;
            }

            .shape-5 {
                width: 110px;
                height: 110px;
            }
        }

        @media (max-width: 480px) {
            .morphing-shape {
                filter: blur(3px);
                opacity: 0.4;
            }

            .shape-1, .shape-3, .shape-8 {
                width: 80px;
                height: 80px;
            }

            .shape-2, .shape-5, .shape-6 {
                width: 70px;
                height: 70px;
            }

            .shape-4, .shape-7 {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4 overflow-y-auto">
    <!-- Animated Background Elements -->
    <div class="animated-bg">
        <div class="morphing-shape shape-1"></div>
        <div class="morphing-shape shape-2"></div>
        <div class="morphing-shape shape-3"></div>
        <div class="morphing-shape shape-4"></div>
        <div class="morphing-shape shape-5"></div>
        <div class="morphing-shape shape-6"></div>
        <div class="morphing-shape shape-7"></div>
        <div class="morphing-shape shape-8"></div>
    </div>

    <div class="relative z-10 w-full max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Theme Toggle Button -->
    <div class="absolute top-4 right-4 z-20">
        <button onclick="toggleTheme()" class="w-12 h-12 flex items-center justify-center rounded-full bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300 backdrop-blur-sm border border-white border-opacity-20" title="Toggle theme">
            <i class="fas fa-sun text-white text-lg" id="themeIcon"></i>
        </button>
    </div>

        <!-- Logo and Title -->
        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 bg-gray-800 rounded-2xl mb-3 sm:mb-4 shadow-lg">
                <i class="fas fa-building text-xl sm:text-2xl text-white"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold mb-2" style="color: var(--text-primary);">Hostel CRM</h1>
            <p class="text-sm sm:text-base font-medium" style="color: var(--text-secondary);">Welcome back! Please sign in to continue.</p>
        </div>

        <!-- Login Form -->
        <div class="glass-effect rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 shadow-2xl">
            <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-6">
                @csrf

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold" style="color: var(--text-primary);">
                        <i class="fas fa-envelope mr-2" style="color: var(--text-secondary);"></i>Email Address
                    </label>
                    <div class="relative">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 pl-10 sm:pl-12 border rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 input-focus transition-all duration-300 shadow-lg text-sm sm:text-base"
                            style="background-color: #ffffff !important; border-color: var(--input-border); color: var(--text-primary);"
                            placeholder="Enter your email"
                            required
                            autofocus
                        >
                        <i class="fas fa-envelope absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-sm sm:text-base" style="color: var(--text-secondary);"></i>
                    </div>
                    @error('email')
                        <p class="text-red-600 text-xs sm:text-sm mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold" style="color: var(--text-primary);">
                        <i class="fas fa-lock mr-2" style="color: var(--text-secondary);"></i>Password
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 pl-10 sm:pl-12 pr-10 sm:pr-12 border rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 input-focus transition-all duration-300 shadow-lg text-sm sm:text-base"
                            style="background-color: #ffffff !important; border-color: var(--input-border); color: var(--text-primary);"
                            placeholder="Enter your password"
                            required
                        >
                        <i class="fas fa-lock absolute left-3 sm:left-4 top-1/2 transform -translate-y-1/2 text-sm sm:text-base" style="color: var(--text-secondary);"></i>
                        <button type="button" class="absolute right-3 sm:right-4 top-1/2 transform -translate-y-1/2 hover:scale-110 transition-all duration-200 focus:outline-none" onclick="togglePassword()" title="Toggle password visibility" style="color: var(--text-secondary);">
                            <i class="fas fa-eye text-sm sm:text-base" id="passwordToggle"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-xs sm:text-sm mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <span class="ml-2 text-xs sm:text-sm font-medium" style="color: var(--text-primary);">Remember me</span>
                    </label>
                    <a href="#" class="text-xs sm:text-sm font-medium hover:text-blue-600 transition-all duration-200 underline decoration-transparent hover:decoration-blue-600" style="color: var(--text-primary);">
                        Forgot password?
                    </a>
                </div>

                <!-- Login Button -->
                <button
                    type="submit"
                    class="w-full text-white font-semibold py-2.5 sm:py-3 px-6 rounded-lg sm:rounded-xl transition-all duration-300 btn-hover shadow-lg text-sm sm:text-base"
                    style="background-color: var(--button-bg);"
                    onmouseover="this.style.backgroundColor='var(--button-hover)'"
                    onmouseout="this.style.backgroundColor='var(--button-bg)'"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>

                <!-- Demo Credentials -->
                <div class="mt-4 sm:mt-6 p-3 sm:p-4 rounded-lg sm:rounded-xl shadow-lg" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                    <p class="font-semibold text-xs sm:text-sm text-center mb-2 sm:mb-3" style="color: var(--text-primary);">
                        <i class="fas fa-info-circle mr-1 sm:mr-2 text-blue-600"></i>
                        Demo Credentials
                    </p>
                    <div class="text-xs sm:text-sm space-y-1.5 sm:space-y-2" style="color: var(--text-primary);">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between rounded-lg p-2 space-y-1 sm:space-y-0" style="background-color: var(--bg-secondary);">
                            <span class="font-medium text-xs sm:text-sm">Email:</span>
                            <span class="font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs break-all sm:break-normal">admin@hostel.com</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between rounded-lg p-2 space-y-1 sm:space-y-0" style="background-color: var(--bg-secondary);">
                            <span class="font-medium text-xs sm:text-sm">Password:</span>
                            <span class="font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">password</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 sm:mt-8">
            <p class="font-medium text-xs sm:text-sm" style="color: var(--text-secondary);">
                © 2024 Hostel CRM. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Theme functionality
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = html.getAttribute('data-theme');

            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'dark');
            }

            // Force input backgrounds to stay white
            forceInputBackgrounds();
        }

        function forceInputBackgrounds() {
            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.style.backgroundColor = '#ffffff';
                input.style.color = '#1f2937';
            });
        }

        // Initialize theme on page load
        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (prefersDark ? 'dark' : 'light');

            document.documentElement.setAttribute('data-theme', theme);
            const themeIcon = document.getElementById('themeIcon');

            if (theme === 'dark') {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            } else {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }

            // Force input backgrounds to stay white after theme initialization
            setTimeout(forceInputBackgrounds, 100);
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
                passwordToggle.setAttribute('title', 'Hide password');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
                passwordToggle.setAttribute('title', 'Show password');
            }
        }

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize theme
            initializeTheme();

            const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');

            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-blue-500', 'ring-opacity-50');
                    this.parentElement.style.transform = 'scale(1.02)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-blue-500', 'ring-opacity-50');
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // Add click-to-copy functionality for demo credentials
            const demoCredentials = document.querySelectorAll('.font-mono');
            demoCredentials.forEach(element => {
                element.addEventListener('click', function() {
                    const text = this.textContent;
                    navigator.clipboard.writeText(text).then(() => {
                        // Visual feedback
                        const originalText = this.textContent;
                        this.textContent = 'Copied!';
                        this.style.backgroundColor = 'rgba(34, 197, 94, 0.2)';
                        this.style.color = 'rgb(22, 163, 74)';

                        setTimeout(() => {
                            this.textContent = originalText;
                            this.style.backgroundColor = 'rgb(219, 234, 254)';
                            this.style.color = 'rgb(30, 64, 175)';
                        }, 1000);
                    });
                });

                element.style.cursor = 'pointer';
                element.title = 'Click to copy';
            });
        });
    </script>
</body>
</html>
