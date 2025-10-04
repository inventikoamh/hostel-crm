<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hostel CRM')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-primary: #f9fafb;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --sidebar-bg: linear-gradient(180deg, #6b7280 0%, #4b5563 100%);
            --card-bg: #ffffff;
            --hover-bg: #f3f4f6;
            --scrollbar-track: #f1f5f9;
            --scrollbar-thumb: #cbd5e1;
            --scrollbar-thumb-hover: #94a3b8;
        }

        [data-theme="dark"] {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --border-color: #374151;
            --sidebar-bg: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            --card-bg: #1f2937;
            --hover-bg: #374151;
            --scrollbar-track: #1f2937;
            --scrollbar-thumb: #4b5563;
            --scrollbar-thumb-hover: #6b7280;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        .main-content {
            background-color: var(--bg-primary);
            overflow-x: hidden;
        }

        .sidebar-gradient {
            background: var(--sidebar-bg);
        }

        /* Global Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 10px;
            border: 2px solid var(--scrollbar-track);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-hover);
        }

        ::-webkit-scrollbar-corner {
            background: var(--scrollbar-track);
        }

        /* Firefox Scrollbar Styling */
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb) var(--scrollbar-track);
        }

        /* Custom scrollbar for specific elements */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb) var(--scrollbar-track);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 10px;
            border: none;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-hover);
        }

        /* Sidebar specific scrollbar */
        .sidebar-gradient nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        .sidebar-gradient nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-gradient nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-gradient nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            border: none;
        }

        .sidebar-gradient nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* User card text visibility */
        .user-profile-card {
            background: rgba(255, 255, 255, 0.95) !important;
        }

        .user-profile-card .text-gray-800 {
            color: #1f2937 !important;
        }

        .user-profile-card .text-gray-600 {
            color: #6b7280 !important;
        }

        [data-theme="dark"] .user-profile-card {
            background: rgba(31, 41, 55, 0.95) !important;
        }

        [data-theme="dark"] .user-profile-card .text-gray-800 {
            color: #f9fafb !important;
        }

        [data-theme="dark"] .user-profile-card .text-gray-600 {
            color: #d1d5db !important;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .sidebar-item {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .sidebar-sub-item {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.2s ease;
        }

        .sidebar-sub-item.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .sidebar-sub-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Sticky header */
        .sticky-header {
            height: 4rem; /* 64px */
        }

        /* Collapsible sidebar */
        .sidebar-collapsed {
            transform: translateX(-100%);
        }

        .sidebar-expanded {
            transform: translateX(0);
        }

        .main-content-expanded {
            margin-left: 16rem; /* 256px */
        }

        .main-content-collapsed {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .header-expanded {
            left: 16rem; /* 256px */
        }

        .header-collapsed {
            left: 0;
        }

        /* Smooth transitions - only applied after initialization */
        .main-content.transitions-enabled {
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .sticky-header.transitions-enabled {
            transition: left 0.3s ease;
        }

        /* Initial sidebar state to prevent flash */
        .sidebar-initially-collapsed #sidebar {
            transform: translateX(-100%);
        }

        .sidebar-initially-collapsed .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .sidebar-initially-collapsed .sticky-header {
            left: 0 !important;
        }

        #sidebar.transitions-enabled {
            transition: transform 0.3s ease;
        }

        /* Mobile responsive */
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0 !important;
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .sidebar-gradient {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar-gradient.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
                padding: 1rem;
            }

            .mobile-sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 40;
                display: none;
            }

            .mobile-sidebar-overlay.show {
                display: block;
            }

            /* Mobile typography */
            .mobile-text-lg {
                font-size: 1.125rem;
            }

            .mobile-text-xl {
                font-size: 1.25rem;
            }

            .mobile-text-2xl {
                font-size: 1.5rem;
            }

            .mobile-text-3xl {
                font-size: 1.875rem;
            }

            /* Mobile spacing */
            .mobile-p-4 {
                padding: 1rem;
            }

            .mobile-p-6 {
                padding: 1.5rem;
            }

            .mobile-mb-4 {
                margin-bottom: 1rem;
            }

            .mobile-mb-6 {
                margin-bottom: 1.5rem;
            }

            .mobile-mb-8 {
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 640px) {
            .main-content {
                padding: 0.75rem;
            }

            /* Extra small mobile adjustments */
            .mobile-text-sm {
                font-size: 0.875rem;
            }

            .mobile-p-3 {
                padding: 0.75rem;
            }

            .mobile-mb-2 {
                margin-bottom: 0.5rem;
            }

            .mobile-mb-3 {
                margin-bottom: 0.75rem;
            }
        }
    </style>

    @stack('styles')

    <!-- Prevent sidebar flash on page load -->
    <script>
        (function() {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            const isMobile = window.innerWidth < 1024;

            if (sidebarCollapsed && !isMobile) {
                document.documentElement.classList.add('sidebar-initially-collapsed');
            }
        })();
    </script>
</head>
<body class="min-h-screen">
    <div class="flex">
        <!-- Mobile Sidebar Overlay -->
        <div id="mobileSidebarOverlay" class="mobile-sidebar-overlay" onclick="closeMobileSidebar()"></div>

        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Sticky Header -->
        <div class="sticky-header fixed top-0 left-0 right-0 z-40 bg-white shadow-sm border-b border-gray-200" style="background-color: var(--card-bg); border-color: var(--border-color);" id="stickyHeader">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center">
                    <button onclick="toggleSidebar()" class="w-10 h-10 flex items-center justify-center rounded-lg transition-colors duration-200 mr-3" style="background-color: var(--hover-bg);">
                        <i class="fas fa-bars text-lg" style="color: var(--text-primary);"></i>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold" style="color: var(--text-primary);">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                </div>
                <button onclick="toggleTheme()" class="w-10 h-10 flex items-center justify-center rounded-lg transition-colors duration-200" style="background-color: var(--hover-bg);" title="Toggle theme">
                    <i class="fas fa-sun text-lg" id="headerThemeIcon" style="color: var(--text-primary);"></i>
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 p-4 sm:p-6 lg:p-8 min-h-screen pt-20" id="mainContent" style="margin-top: 4rem;">
            <!-- Header -->
            @include('components.header', [
                'title' => $title ?? 'Dashboard',
                'subtitle' => $subtitle ?? 'Welcome to Hostel CRM',
                'showBackButton' => $showBackButton ?? false,
                'backUrl' => $backUrl ?? null
            ])

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Global JavaScript -->
    <script>
        // Theme functionality
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('themeIcon');
            const headerThemeIcon = document.getElementById('headerThemeIcon');
            const currentTheme = html.getAttribute('data-theme');

            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
                if (headerThemeIcon) {
                    headerThemeIcon.classList.remove('fa-moon');
                    headerThemeIcon.classList.add('fa-sun');
                }
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                if (themeIcon) {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
                if (headerThemeIcon) {
                    headerThemeIcon.classList.remove('fa-sun');
                    headerThemeIcon.classList.add('fa-moon');
                }
                localStorage.setItem('theme', 'dark');
            }

            // Dispatch theme change event
            document.dispatchEvent(new CustomEvent('themeChanged'));
        }

        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (prefersDark ? 'dark' : 'light');

            document.documentElement.setAttribute('data-theme', theme);
            const themeIcon = document.getElementById('themeIcon');
            const headerThemeIcon = document.getElementById('headerThemeIcon');

            if (theme === 'dark') {
                if (themeIcon) {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
                if (headerThemeIcon) {
                    headerThemeIcon.classList.remove('fa-sun');
                    headerThemeIcon.classList.add('fa-moon');
                }
            } else {
                if (themeIcon) {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
                if (headerThemeIcon) {
                    headerThemeIcon.classList.remove('fa-moon');
                    headerThemeIcon.classList.add('fa-sun');
                }
            }
        }

        // Sidebar functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const stickyHeader = document.getElementById('stickyHeader');
            const overlay = document.getElementById('mobileSidebarOverlay');
            const chevronIcon = sidebar.querySelector('.fa-chevron-left');
            const isMobile = window.innerWidth < 1024;

            if (isMobile) {
                // Mobile behavior
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                // Desktop behavior
                const isCollapsed = sidebar.classList.contains('sidebar-collapsed');

                if (isCollapsed) {
                    // Expand sidebar
                    sidebar.classList.remove('sidebar-collapsed');
                    sidebar.classList.add('sidebar-expanded');
                    mainContent.classList.remove('main-content-collapsed');
                    mainContent.classList.add('main-content-expanded');
                    stickyHeader.classList.remove('header-collapsed');
                    stickyHeader.classList.add('header-expanded');
                    if (chevronIcon) {
                        chevronIcon.classList.remove('fa-chevron-right');
                        chevronIcon.classList.add('fa-chevron-left');
                    }
                    localStorage.setItem('sidebarCollapsed', 'false');
                } else {
                    // Collapse sidebar
                    sidebar.classList.remove('sidebar-expanded');
                    sidebar.classList.add('sidebar-collapsed');
                    mainContent.classList.remove('main-content-expanded');
                    mainContent.classList.add('main-content-collapsed');
                    stickyHeader.classList.remove('header-expanded');
                    stickyHeader.classList.add('header-collapsed');
                    if (chevronIcon) {
                        chevronIcon.classList.remove('fa-chevron-left');
                        chevronIcon.classList.add('fa-chevron-right');
                    }
                    localStorage.setItem('sidebarCollapsed', 'true');
                }
            }
        }

        function initializeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const stickyHeader = document.getElementById('stickyHeader');
            const chevronIcon = sidebar.querySelector('.fa-chevron-left');
            const isMobile = window.innerWidth < 1024;
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            if (!isMobile) {
                if (sidebarCollapsed) {
                    // Set collapsed state
                    sidebar.classList.add('sidebar-collapsed');
                    sidebar.classList.remove('sidebar-expanded');
                    mainContent.classList.add('main-content-collapsed');
                    mainContent.classList.remove('main-content-expanded');
                    stickyHeader.classList.add('header-collapsed');
                    stickyHeader.classList.remove('header-expanded');
                    if (chevronIcon) {
                        chevronIcon.classList.remove('fa-chevron-left');
                        chevronIcon.classList.add('fa-chevron-right');
                    }
                } else {
                    // Set expanded state (default)
                    sidebar.classList.add('sidebar-expanded');
                    sidebar.classList.remove('sidebar-collapsed');
                    mainContent.classList.add('main-content-expanded');
                    mainContent.classList.remove('main-content-collapsed');
                    stickyHeader.classList.add('header-expanded');
                    stickyHeader.classList.remove('header-collapsed');
                    if (chevronIcon) {
                        chevronIcon.classList.remove('fa-chevron-right');
                        chevronIcon.classList.add('fa-chevron-left');
                    }
                }
            } else {
                // Mobile: ensure collapsed state
                sidebar.classList.add('sidebar-collapsed');
                sidebar.classList.remove('sidebar-expanded');
                mainContent.classList.add('main-content-collapsed');
                mainContent.classList.remove('main-content-expanded');
                stickyHeader.classList.add('header-collapsed');
                stickyHeader.classList.remove('header-expanded');
            }

            // Remove initial flash prevention class and enable transitions
            setTimeout(() => {
                document.documentElement.classList.remove('sidebar-initially-collapsed');
                mainContent.classList.add('transitions-enabled');
                stickyHeader.classList.add('transitions-enabled');
                sidebar.classList.add('transitions-enabled');
            }, 100);
        }

        // User dropdown functionality
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const icon = document.getElementById('userDropdownIcon');

            dropdown.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // Config dropdown functionality
        function toggleConfigDropdown() {
            const dropdown = document.getElementById('configDropdown');
            const icon = document.getElementById('configDropdownIcon');

            dropdown.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // Services dropdown functionality
        function toggleServicesDropdown() {
            const dropdown = document.getElementById('servicesDropdown');
            const icon = document.getElementById('servicesDropdownIcon');

            dropdown.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // Billing dropdown functionality
        function toggleBillingDropdown() {
            const dropdown = document.getElementById('billingDropdown');
            const icon = document.getElementById('billingDropdownIcon');

            dropdown.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // Notifications dropdown functionality
        function toggleNotificationsDropdown() {
            const dropdown = document.getElementById('notificationsDropdown');
            const icon = document.getElementById('notificationsDropdownIcon');

            dropdown.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // User Management dropdown functionality
        function toggleUserManagementDropdown() {
            const dropdown = document.getElementById('userManagementDropdown');
            const icon = document.getElementById('userManagementDropdownIcon');

            dropdown.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('button[onclick="toggleUserDropdown()"]');

            if (!button && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
                document.getElementById('userDropdownIcon').classList.remove('rotate-180');
            }
        });

        // Mobile sidebar functionality
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileSidebarOverlay');

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileSidebarOverlay');

            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }

        // Initialize theme and sidebar on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
            initializeSidebar();
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            initializeSidebar();
        });
    </script>

    @stack('scripts')
</body>
</html>
