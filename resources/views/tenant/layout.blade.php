<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tenant Portal') - Hostel CRM</title>
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
            --card-bg: #ffffff;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --hover-bg: #f3f4f6;
            --primary-bg: #eff6ff;
            --primary-border: #dbeafe;
            --primary-text: #1d4ed8;
            --success-bg: #f0fdf4;
            --success-text: #166534;
            --warning-bg: #fffbeb;
            --warning-border: #fed7aa;
            --warning-text: #92400e;
            --info-bg: #f0f9ff;
            --info-text: #0c4a6e;
        }

        .dark {
            --bg-primary: #1f2937;
            --bg-secondary: #374151;
            --card-bg: #374151;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --border-color: #4b5563;
            --hover-bg: #4b5563;
            --primary-bg: #1e3a8a;
            --primary-border: #3b82f6;
            --primary-text: #93c5fd;
            --success-bg: #064e3b;
            --success-text: #6ee7b7;
            --warning-bg: #78350f;
            --warning-border: #f59e0b;
            --warning-text: #fbbf24;
            --info-bg: #0c4a6e;
            --info-text: #7dd3fc;
        }

        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900" style="background-color: var(--bg-secondary);">
    <div class="flex h-full">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 sidebar-transition dark:bg-gray-800" style="background-color: var(--bg-primary);">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700" style="border-color: var(--border-color);">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-home text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-lg font-semibold" style="color: var(--text-primary);">Tenant Portal</h1>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    <a href="{{ route('tenant.dashboard') }}"
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('tenant.dashboard') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                       style="color: var(--text-primary);">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('tenant.invoices') }}"
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('tenant.invoices*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                       style="color: var(--text-primary);">
                        <i class="fas fa-file-invoice mr-3"></i>
                        Invoices
                    </a>

                    <a href="{{ route('tenant.payments') }}"
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('tenant.payments*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                       style="color: var(--text-primary);">
                        <i class="fas fa-credit-card mr-3"></i>
                        Payments
                    </a>

                    <!-- Amenities Section -->
                    <div class="space-y-1">
                        <a href="{{ route('tenant.amenities') }}"
                           class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('tenant.amenities*') && !request()->routeIs('tenant.amenities.usage*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                           style="color: var(--text-primary);">
                            <i class="fas fa-star mr-3"></i>
                            My Amenities
                        </a>

            <a href="{{ route('tenant.amenities.usage') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('tenant.amenities.usage*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               style="color: var(--text-primary);">
                <i class="fas fa-chart-line mr-3"></i>
                Usage Tracking
            </a>
        </div>

        <!-- Documents Section -->
        <div class="space-y-1">
            <a href="{{ route('tenant.documents') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('tenant.documents*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
               style="color: var(--text-primary);">
                <i class="fas fa-file-alt mr-3"></i>
                My Documents
            </a>
        </div>

                    <a href="{{ route('tenant.bed-info') }}"
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('tenant.bed-info*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                       style="color: var(--text-primary);">
                        <i class="fas fa-bed mr-3"></i>
                        My Bed
                    </a>

                    <a href="{{ route('tenant.profile') }}"
                       class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('tenant.profile*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                       style="color: var(--text-primary);">
                        <i class="fas fa-user mr-3"></i>
                        Profile
                    </a>
                </div>
            </nav>

            <!-- User Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700" style="border-color: var(--border-color);">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if(Auth::user()->avatar)
                            <img class="h-8 w-8 rounded-full" src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                        @else
                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 dark:text-gray-300 text-xs"></i>
                            </div>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium" style="color: var(--text-primary);">{{ Auth::user()->name }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">Tenant</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700" style="background-color: var(--bg-primary); border-color: var(--border-color);">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="ml-2 text-xl font-semibold" style="color: var(--text-primary);">@yield('title', 'Tenant Portal')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <button onclick="toggleTheme()"
                                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            <i id="themeIcon" class="fas fa-moon"></i>
                        </button>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('tenant.logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @if(session('success'))
                            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" style="background-color: var(--success-bg); border-color: var(--success-border); color: var(--success-text);">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" style="background-color: var(--danger-bg); border-color: var(--danger-border); color: var(--danger-text);">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm">{{ session('error') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <script>
        // Sidebar toggle functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
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
    </script>
</body>
</html>
