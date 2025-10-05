<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel CRM - Complete Hostel Management Solution</title>
    <meta name="description" content="Comprehensive hostel management system with tenant tracking, billing automation, payment processing, and visual mapping. Built with Laravel 12.">
    <meta name="keywords" content="hostel management, CRM, tenant management, billing, payments, Laravel">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .hero-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .feature-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="scroll-smooth">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-building mr-2 text-blue-600"></i>
                            Hostel CRM
                        </h1>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-blue-600 transition duration-300">Features</a>
                    <a href="#modules" class="text-gray-600 hover:text-blue-600 transition duration-300">Modules</a>
                    <a href="#demo" class="text-gray-600 hover:text-blue-600 transition duration-300">Demo</a>
                    <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#features" class="block px-3 py-2 text-gray-600 hover:text-blue-600">Features</a>
                <a href="#modules" class="block px-3 py-2 text-gray-600 hover:text-blue-600">Modules</a>
                <a href="#demo" class="block px-3 py-2 text-gray-600 hover:text-blue-600">Demo</a>
                <a href="{{ route('login') }}" class="block px-3 py-2 bg-blue-600 text-white rounded-lg mx-3 text-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg text-white pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    Complete Hostel Management
                    <span class="block text-yellow-300">Made Simple</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto opacity-90">
                    Streamline your hostel operations with our comprehensive CRM system.
                    Manage tenants, track payments, automate billing, and visualize occupancy
                    with our modern, intuitive platform.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#demo" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-4 px-8 rounded-lg text-lg transition duration-300">
                        <i class="fas fa-play mr-2"></i>View Demo
                    </a>
                    <a href="{{ route('login') }}" class="bg-transparent border-2 border-white hover:bg-white hover:text-blue-600 text-white font-bold py-4 px-8 rounded-lg text-lg transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login to System
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl font-bold text-blue-600 mb-2">15+</div>
                    <div class="text-gray-600">Core Modules</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl font-bold text-green-600 mb-2">100%</div>
                    <div class="text-gray-600">Laravel 12</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl font-bold text-purple-600 mb-2">24/7</div>
                    <div class="text-gray-600">Support Ready</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl font-bold text-orange-600 mb-2">Free</div>
                    <div class="text-gray-600">Open Source</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Powerful Features</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to manage your hostel efficiently, from tenant registration to payment processing.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-lg shadow-lg card-hover">
                    <div class="text-4xl mb-4">
                        <i class="fas fa-users feature-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Tenant Management</h3>
                    <p class="text-gray-600">Complete tenant lifecycle management with profile updates, verification workflows, and billing cycles.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-lg shadow-lg card-hover">
                    <div class="text-4xl mb-4">
                        <i class="fas fa-building feature-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Multi-Hostel Support</h3>
                    <p class="text-gray-600">Manage multiple hostels from a single interface with comprehensive property management.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-lg shadow-lg card-hover">
                    <div class="text-4xl mb-4">
                        <i class="fas fa-map feature-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Visual Mapping</h3>
                    <p class="text-gray-600">Interactive floor-wise room and bed visualization with real-time occupancy status.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-lg shadow-lg card-hover">
                    <div class="text-4xl mb-4">
                        <i class="fas fa-file-invoice feature-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Automated Billing</h3>
                    <p class="text-gray-600">Professional PDF invoices with automated email delivery and payment tracking.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-lg shadow-lg card-hover">
                    <div class="text-4xl mb-4">
                        <i class="fas fa-credit-card feature-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Payment Processing</h3>
                    <p class="text-gray-600">Multi-method payment support with verification system and receipt generation.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-lg shadow-lg card-hover">
                    <div class="text-4xl mb-4">
                        <i class="fas fa-chart-line feature-icon"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Analytics & Reports</h3>
                    <p class="text-gray-600">Comprehensive reporting with charts, exports, and real-time analytics dashboard.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section id="modules" class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Complete Module Suite</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Every aspect of hostel management covered with dedicated modules and seamless integration.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Core Modules -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-bold mb-4 text-blue-600">
                        <i class="fas fa-cog mr-2"></i>Core Management
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Dashboard & Analytics</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Hostel Management</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Tenant Management</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Room & Bed Management</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>User & Role Management</li>
                    </ul>
                </div>

                <!-- Financial Modules -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-bold mb-4 text-green-600">
                        <i class="fas fa-dollar-sign mr-2"></i>Financial System
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Invoice Generation</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Payment Processing</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Billing Automation</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Paid Amenities</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Usage Tracking</li>
                    </ul>
                </div>

                <!-- Advanced Features -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-lg font-bold mb-4 text-purple-600">
                        <i class="fas fa-star mr-2"></i>Advanced Features
                    </h3>
                    <ul class="space-y-2 text-gray-600">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Visual Mapping</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Enquiry Management</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Notification System</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Profile Update Requests</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Usage Corrections</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">See It In Action</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience the power of Hostel CRM with our comprehensive demo and system overview.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-bold mb-6">Ready to Use</h3>
                    <p class="text-gray-600 mb-6">
                        Our system comes with comprehensive features and intuitive interface.
                        Get started immediately with our user-friendly platform.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Modern web interface with intuitive design</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Complete documentation and user guides</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Demo data included for immediate testing</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Responsive design works on all devices</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-100 p-8 rounded-lg">
                    <h4 class="text-lg font-bold mb-4">System Access</h4>
                    <div class="space-y-3 text-sm font-mono bg-gray-800 text-green-400 p-4 rounded">
                        <div># Login to the system</div>
                        <div>Visit: {{ route('login') }}</div>
                        <div># Access dashboard</div>
                        <div>Visit: {{ route('dashboard') }}</div>
                        <div># Explore features</div>
                        <div>Navigate through modules</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Technology Stack -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Built with Modern Technology</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Leveraging the latest technologies for performance, security, and scalability.
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl mb-2">
                        <i class="fab fa-laravel text-red-500"></i>
                    </div>
                    <div class="font-bold">Laravel 12</div>
                    <div class="text-sm text-gray-600">PHP Framework</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl mb-2">
                        <i class="fab fa-js-square text-yellow-500"></i>
                    </div>
                    <div class="font-bold">JavaScript ES6+</div>
                    <div class="text-sm text-gray-600">Frontend Logic</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl mb-2">
                        <i class="fas fa-database text-blue-500"></i>
                    </div>
                    <div class="font-bold">MySQL/SQLite</div>
                    <div class="text-sm text-gray-600">Database</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-3xl mb-2">
                        <i class="fab fa-css3-alt text-blue-600"></i>
                    </div>
                    <div class="font-bold">Tailwind CSS</div>
                    <div class="text-sm text-gray-600">Styling</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">
                        <i class="fas fa-building mr-2"></i>Hostel CRM
                    </h3>
                    <p class="text-gray-300 mb-4">
                        Complete hostel management solution built with Laravel 12 and modern web technologies.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#features" class="hover:text-white transition duration-300">Features</a></li>
                        <li><a href="#modules" class="hover:text-white transition duration-300">Modules</a></li>
                        <li><a href="#demo" class="hover:text-white transition duration-300">Demo</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition duration-300">Login</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">System Access</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition duration-300">Login</a></li>
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition duration-300">Dashboard</a></li>
                        <li><a href="#features" class="hover:text-white transition duration-300">Features</a></li>
                        <li><a href="#modules" class="hover:text-white transition duration-300">Modules</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Contact</h4>
                    <div class="space-y-2 text-gray-300">
                        <div><i class="fas fa-envelope mr-2"></i>support@hostelcrm.com</div>
                        <div><i class="fas fa-globe mr-2"></i>www.hostelcrm.com</div>
                        <div><i class="fas fa-code mr-2"></i>Open Source</div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; 2024 Hostel CRM. Built with ❤️ using Laravel 12 and modern web technologies.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navigation
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('bg-white/95', 'backdrop-blur-sm');
            } else {
                nav.classList.remove('bg-white/95', 'backdrop-blur-sm');
            }
        });
    </script>
</body>
</html>
