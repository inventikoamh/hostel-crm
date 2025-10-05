<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Login - Hostel CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;

            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --hover-bg: #f3f4f6;
        }

        .dark {
            --bg-primary: #1f2937;
            --bg-secondary: #374151;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --border-color: #4b5563;
            --hover-bg: #4b5563;
        }

        .animated-shape {
            animation: float 6s ease-in-out infinite;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .shape-1 { animation-delay: 0s; }
        .shape-2 { animation-delay: 1s; }
        .shape-3 { animation-delay: 2s; }
        .shape-4 { animation-delay: 3s; }
        .shape-5 { animation-delay: 4s; }
        .shape-6 { animation-delay: 5s; }
        .shape-7 { animation-delay: 6s; }
        .shape-8 { animation-delay: 7s; }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Animated Background Shapes -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-10 left-10 w-20 h-20 bg-blue-400 opacity-20 animated-shape shape-1"></div>
        <div class="absolute top-32 right-20 w-16 h-16 bg-purple-400 opacity-20 animated-shape shape-2"></div>
        <div class="absolute bottom-20 left-32 w-24 h-24 bg-pink-400 opacity-20 animated-shape shape-3"></div>
        <div class="absolute bottom-32 right-10 w-18 h-18 bg-indigo-400 opacity-20 animated-shape shape-4"></div>
        <div class="absolute top-1/2 left-10 w-14 h-14 bg-green-400 opacity-20 animated-shape shape-5"></div>
        <div class="absolute top-1/3 right-1/3 w-22 h-22 bg-yellow-400 opacity-20 animated-shape shape-6"></div>
        <div class="absolute bottom-1/3 left-1/3 w-16 h-16 bg-red-400 opacity-20 animated-shape shape-7"></div>
        <div class="absolute top-20 right-1/2 w-20 h-20 bg-teal-400 opacity-20 animated-shape shape-8"></div>
    </div>

    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-home text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold" style="color: var(--text-primary);">Tenant Portal</h2>
                <p class="mt-2 text-sm" style="color: var(--text-secondary);">
                    Access your hostel account and manage your stay
                </p>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 dark:bg-gray-800 dark:border-gray-700" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                <form class="space-y-6" method="POST" action="{{ route('tenant.login.post') }}">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email"
                                   name="email"
                                   type="email"
                                   autocomplete="email"
                                   required
                                   value="{{ old('email') }}"
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter your email">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password"
                                   name="password"
                                   type="password"
                                   autocomplete="current-password"
                                   required
                                   class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter your password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" onclick="togglePassword()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i id="passwordToggle" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400"></i>
                            </span>
                            Sign in to Tenant Portal
                        </button>
                    </div>

                    <!-- Demo Credentials -->
                    <div class="mt-4 sm:mt-6 p-3 sm:p-4 rounded-lg sm:rounded-xl shadow-lg" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                        <p class="font-semibold text-xs sm:text-sm text-center mb-2 sm:mb-3" style="color: var(--text-primary);">
                            <i class="fas fa-info-circle mr-1 sm:mr-2 text-blue-600"></i>
                            Demo Tenant Credentials
                        </p>
                        <div class="text-xs sm:text-sm space-y-1.5 sm:space-y-2" style="color: var(--text-primary);">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between rounded-lg p-2 space-y-1 sm:space-y-0" style="background-color: var(--bg-primary);">
                                <span class="font-medium text-xs sm:text-sm">Email:</span>
                                <span class="font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs break-all sm:break-normal cursor-pointer hover:bg-blue-200 transition-colors duration-200"
                                      onclick="copyToClipboard('john.smith@email.com', this)"
                                      title="Click to copy">john.smith@email.com</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between rounded-lg p-2 space-y-1 sm:space-y-0" style="background-color: var(--bg-primary);">
                                <span class="font-medium text-xs sm:text-sm">Password:</span>
                                <span class="font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs cursor-pointer hover:bg-blue-200 transition-colors duration-200"
                                      onclick="copyToClipboard('password', this)"
                                      title="Click to copy">password</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-sm" style="color: var(--text-secondary);">
                    Need help? Contact your hostel administrator
                </p>
                <div class="mt-4 flex justify-center space-x-4">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200" style="color: var(--primary-text);">
                        <i class="fas fa-user-shield mr-1"></i>
                        Admin Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Toggle -->
    <button onclick="toggleTheme()"
            class="fixed top-4 right-4 p-3 rounded-full bg-white shadow-lg hover:shadow-xl transition-all duration-200 dark:bg-gray-800"
            style="background-color: var(--bg-primary);">
        <i id="themeIcon" class="fas fa-moon text-gray-600 dark:text-gray-300" style="color: var(--text-primary);"></i>
    </button>

    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggle');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Theme toggle functionality
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');

            if (body.classList.contains('dark')) {
                body.classList.remove('dark');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            } else {
                body.classList.add('dark');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark');
                document.getElementById('themeIcon').classList.remove('fa-moon');
                document.getElementById('themeIcon').classList.add('fa-sun');
            }
        });

        // Copy to clipboard functionality
        function copyToClipboard(text, element) {
            // Create a temporary textarea element
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);

            // Select and copy the text
            textarea.select();
            textarea.setSelectionRange(0, 99999); // For mobile devices

            try {
                document.execCommand('copy');

                // Visual feedback
                const originalText = element.textContent;
                const originalBg = element.style.backgroundColor;

                element.textContent = 'Copied!';
                element.style.backgroundColor = '#10b981'; // Green color

                // Reset after 1.5 seconds
                setTimeout(() => {
                    element.textContent = originalText;
                    element.style.backgroundColor = originalBg;
                }, 1500);

            } catch (err) {
                console.error('Failed to copy text: ', err);
                // Fallback for modern browsers
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text).then(() => {
                        const originalText = element.textContent;
                        const originalBg = element.style.backgroundColor;

                        element.textContent = 'Copied!';
                        element.style.backgroundColor = '#10b981';

                        setTimeout(() => {
                            element.textContent = originalText;
                            element.style.backgroundColor = originalBg;
                        }, 1500);
                    });
                }
            }

            // Remove the temporary textarea
            document.body.removeChild(textarea);
        }
    </script>
</body>
</html>
