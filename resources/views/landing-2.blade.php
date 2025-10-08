<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Resido - Complete Hostel Management Solution</title>
    <meta name="description" content="Streamline operations, automate billing, and manage tenants with Resido - our comprehensive hostel management system. Professional PDF generation, multi-method payments, and real-time analytics.">
    <meta name="keywords" content="hostel management, CRM, tenant management, billing, Laravel, Resido, hostel software">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Landing Page CSS V2 -->
    <link rel="stylesheet" href="{{ asset('css/landing-2.css') }}">
</head>
<body>
    <!-- Navigation - Redesigned -->
    <nav class="new-navbar">
        <div class="container">
            <div class="navbar-content">
                <div class="navbar-brand">
                    <a href="/" class="brand-link">
                        <div class="brand-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <span class="brand-text">Resido</span>
                    </a>
                </div>
                
                <div class="navbar-menu">
                    <div class="navbar-links">
                        <a href="#features" class="nav-link">
                            <span>Features</span>
                        </a>
                        <a href="#statistics" class="nav-link">
                            <span>Statistics</span>
                        </a>
                        <a href="#technology" class="nav-link">
                            <span>Technology</span>
                        </a>
                        <a href="#demo" class="nav-link">
                            <span>Live Demo</span>
                        </a>
                    </div>
                    
                    <div class="navbar-actions">
                        <a href="/login" class="nav-btn nav-btn-secondary">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                        <a href="/register" class="nav-btn nav-btn-primary">
                            <i class="fas fa-rocket"></i>
                            <span>Start Free Trial</span>
                        </a>
                    </div>
                </div>
                
                <div class="navbar-toggle">
                    <button class="toggle-btn" id="navbar-toggle">
                        <span class="toggle-line"></span>
                        <span class="toggle-line"></span>
                        <span class="toggle-line"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobile-menu">
            <div class="mobile-menu-content">
                <div class="mobile-links">
                    <a href="#features" class="mobile-link">
                        <i class="fas fa-star"></i>
                        <span>Features</span>
                    </a>
                    <a href="#statistics" class="mobile-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Statistics</span>
                    </a>
                    <a href="#technology" class="mobile-link">
                        <i class="fas fa-code"></i>
                        <span>Technology</span>
                    </a>
                    <a href="#demo" class="mobile-link">
                        <i class="fas fa-play-circle"></i>
                        <span>Live Demo</span>
                    </a>
                </div>
                
                <div class="mobile-actions">
                    <a href="/login" class="mobile-btn mobile-btn-secondary">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                    <a href="/register" class="mobile-btn mobile-btn-primary">
                        <i class="fas fa-rocket"></i>
                        <span>Start Free Trial</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="hero-badge">
                        <i class="fas fa-home"></i>
                        Hostel Management
                    </div>
                    
                    <h1 class="hero-headline">
                        Streamline Your Hostel Operations
                    </h1>
                    
                    <p class="hero-subheadline">
                        Manage tenants, automate billing, track payments, and generate professional reports with Resido's comprehensive hostel management system.
                    </p>
                    
                    <div class="hero-actions">
                        <a href="/dashboard" class="btn btn-primary btn-large">
                            <i class="fas fa-arrow-right"></i>
                            Start Free Trial
                        </a>
                        <a href="#demo" class="btn btn-secondary btn-large">
                            <i class="fas fa-eye"></i>
                            Watch Demo
                        </a>
                    </div>
                </div>
                
                <div class="hero-visual">
                    <div class="hero-image">
                        <div class="dashboard-preview">
                            <div class="dashboard-card">
                                <div class="dashboard-header">
                                    <div class="dashboard-title">
                                        <i class="fas fa-chart-pie"></i>
                                        <span>Analytics Dashboard</span>
                                    </div>
                                    <div class="dashboard-status">
                                        <div class="status-dot"></div>
                                        <span>Live</span>
                                    </div>
                                </div>
                                
                                <div class="dashboard-content">
                                    <div class="main-stats">
                                        <div class="main-stat">
                                            <div class="main-stat-number">247</div>
                                            <div class="main-stat-label">Active Tenants</div>
                                        </div>
                                        <div class="main-stat">
                                            <div class="main-stat-number">₹2.4M</div>
                                            <div class="main-stat-label">Revenue</div>
                                        </div>
                                        <div class="main-stat">
                                            <div class="main-stat-number">156</div>
                                            <div class="main-stat-label">Invoices</div>
                                        </div>
                                    </div>
                                    
                                    <div class="quick-actions">
                                        <div class="action-item">
                                            <div class="action-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <span>Manage Tenants</span>
                                        </div>
                                        <div class="action-item">
                                            <div class="action-icon">
                                                <i class="fas fa-bed"></i>
                                            </div>
                                            <span>Room Status</span>
                                        </div>
                                        <div class="action-item">
                                            <div class="action-icon">
                                                <i class="fas fa-dollar-sign"></i>
                                            </div>
                                            <span>Payments</span>
                                        </div>
                                        <div class="action-item">
                                            <div class="action-icon">
                                                <i class="fas fa-file-invoice"></i>
                                            </div>
                                            <span>Billing</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="chart-area">
                                    <div class="chart-bars">
                                        <div class="chart-bar" style="height: 60%;"></div>
                                        <div class="chart-bar" style="height: 80%;"></div>
                                        <div class="chart-bar" style="height: 45%;"></div>
                                        <div class="chart-bar" style="height: 90%;"></div>
                                        <div class="chart-bar" style="height: 70%;"></div>
                                        <div class="chart-bar" style="height: 85%;"></div>
                                    </div>
                                    <div class="chart-label">Monthly Revenue Trend</div>
                                </div>
                            </div>
                            
                            <!-- Modern Dashboard Elements -->
                            
                            <!-- Top Right - Live Status -->
                            <div class="live-status-card">
                                <div class="live-status-header">
                                    <div class="live-status-dot"></div>
                                    <span class="live-status-text">Live</span>
                                </div>
                                <div class="live-status-metric">
                                    <span class="live-metric-value">247</span>
                                    <span class="live-metric-label">Active Users</span>
                                </div>
                            </div>
                            
                            <!-- Top Left - Quick Stats -->
                            <div class="quick-stats-card">
                                <div class="quick-stat">
                                    <div class="quick-stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="quick-stat-content">
                                        <div class="quick-stat-number">89%</div>
                                        <div class="quick-stat-label">Occupancy</div>
                                    </div>
                                </div>
                                <div class="quick-stat">
                                    <div class="quick-stat-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="quick-stat-content">
                                        <div class="quick-stat-number">₹2.4M</div>
                                        <div class="quick-stat-label">Revenue</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bottom Right - Performance Indicator -->
                            <div class="performance-card">
                                <div class="performance-header">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Performance</span>
                                </div>
                                <div class="performance-circle">
                                    <div class="performance-fill" style="--progress: 85%;"></div>
                                    <div class="performance-percentage">85%</div>
                                </div>
                            </div>
                            
                            <!-- Bottom Left - Recent Activity -->
                            <div class="activity-card">
                                <div class="activity-header">
                                    <i class="fas fa-clock"></i>
                                    <span>Recent Activity</span>
                                </div>
                                <div class="activity-list">
                                    <div class="activity-item">
                                        <div class="activity-dot"></div>
                                        <span class="activity-text">New tenant registered</span>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-dot"></div>
                                        <span class="activity-text">Payment received</span>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-dot"></div>
                                        <span class="activity-text">Invoice generated</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="statistics-section" id="statistics">
        <div class="container">
            <div class="statistics-content">
                <div class="statistics-header">
                    <div class="statistics-badge">
                        <i class="fas fa-chart-bar"></i>
                        Key Statistics
                    </div>
                    <h2 class="statistics-title">
                        Trusted by Hostels Worldwide
                    </h2>
                    <p class="statistics-subtitle">
                        Join thousands of hostels already using Resido to streamline their operations and boost efficiency.
                    </p>
                </div>

                <!-- Main Statistics Grid -->
                <div class="statistics-main-grid">
                    <div class="stat-card stat-card-primary">
                        <div class="stat-card-header">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon">
                                    <i class="fas fa-cubes"></i>
                                </div>
                                <div class="stat-icon-glow"></div>
                            </div>
                            <div class="stat-trend-indicator">
                                <i class="fas fa-arrow-up"></i>
                                <span>+5 this year</span>
                            </div>
                        </div>
                        <div class="stat-number" data-target="20">0</div>
                        <div class="stat-label">Core Modules</div>
                        <div class="stat-description">Complete feature coverage including advanced modules</div>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 85%;"></div>
                            </div>
                            <span class="progress-text">85% Complete</span>
                        </div>
                    </div>

                    <div class="stat-card stat-card-secondary">
                        <div class="stat-card-header">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon">
                                    <i class="fas fa-code"></i>
                                </div>
                                <div class="stat-icon-glow"></div>
                            </div>
                            <div class="stat-trend-indicator">
                                <i class="fas fa-check-circle"></i>
                                <span>Latest version</span>
                            </div>
                        </div>
                        <div class="stat-number" data-target="100">0</div>
                        <div class="stat-label">Laravel 12</div>
                        <div class="stat-description">Modern PHP framework with latest features</div>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%;"></div>
                            </div>
                            <span class="progress-text">100% Updated</span>
                        </div>
                    </div>

                    <div class="stat-card stat-card-tertiary">
                        <div class="stat-card-header">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="stat-icon-glow"></div>
                            </div>
                            <div class="stat-trend-indicator">
                                <i class="fas fa-star"></i>
                                <span>Enterprise grade</span>
                            </div>
                        </div>
                        <div class="stat-number">Professional</div>
                        <div class="stat-label">PDF System</div>
                        <div class="stat-description">Automated invoice generation and email delivery</div>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 95%;"></div>
                            </div>
                            <span class="progress-text">95% Automated</span>
                        </div>
                    </div>

                    <div class="stat-card stat-card-supporting">
                        <div class="stat-card-header">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon">
                                    <i class="fas fa-cloud"></i>
                                </div>
                                <div class="stat-icon-glow"></div>
                            </div>
                            <div class="stat-trend-indicator">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure & Reliable</span>
                            </div>
                        </div>
                        <div class="stat-number">Cloud</div>
                        <div class="stat-label">Hosted</div>
                        <div class="stat-description">Secure cloud infrastructure with 99.9% uptime guarantee</div>
                        <div class="stat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 99%;"></div>
                            </div>
                            <span class="progress-text">99.9% Uptime</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Stats Row -->
                <div class="additional-stats-container">
                    <div class="additional-stats-header">
                        <h3 class="additional-stats-title">Real-Time Metrics</h3>
                        <div class="live-indicator">
                            <div class="live-dot"></div>
                            <span>Live Data</span>
                        </div>
                    </div>
                    
                    <div class="additional-stats-grid">
                        <div class="additional-stat">
                            <div class="additional-stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="additional-stat-content">
                                <div class="additional-stat-number" data-target="5000">0</div>
                                <div class="additional-stat-label">Active Users</div>
                                <div class="additional-stat-change">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+12% this month</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="additional-stat">
                            <div class="additional-stat-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="additional-stat-content">
                                <div class="additional-stat-number" data-target="1200">0</div>
                                <div class="additional-stat-label">Hostels Managed</div>
                                <div class="additional-stat-change">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+8% this month</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="additional-stat">
                            <div class="additional-stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="additional-stat-content">
                                <div class="additional-stat-number" data-target="99">0</div>
                                <div class="additional-stat-label">Uptime %</div>
                                <div class="additional-stat-change">
                                    <i class="fas fa-check-circle"></i>
                                    <span>99.9% average</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="additional-stat">
                            <div class="additional-stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="additional-stat-content">
                                <div class="additional-stat-number" data-target="2">0</div>
                                <div class="additional-stat-label">Million Revenue</div>
                                <div class="additional-stat-change">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+25% this year</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="features-content">
                <div class="features-header">
                    <div class="features-badge">
                        <i class="fas fa-star"></i>
                        Core Features
                    </div>
                    <h2 class="features-title">
                        Everything You Need to Manage Your Hostel
                    </h2>
                    <p class="features-subtitle">
                        Comprehensive tools and features designed specifically for hostel management operations.
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feature-card feature-card-primary">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrapper">
                                <div class="feature-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="feature-icon-glow"></div>
                            </div>
                            <div class="feature-badge">Essential</div>
                        </div>
                        <h3 class="feature-title">Tenant Management</h3>
                        <p class="feature-description">Complete tenant profiles, document management, and communication tools for seamless tenant lifecycle management.</p>
                        <div class="feature-highlights">
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Profile Management</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Document Tracking</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Communication Hub</span>
                            </div>
                        </div>
                    </div>

                    <div class="feature-card feature-card-secondary">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrapper">
                                <div class="feature-icon">
                                    <i class="fas fa-bed"></i>
                                </div>
                                <div class="feature-icon-glow"></div>
                            </div>
                            <div class="feature-badge">Core</div>
                        </div>
                        <h3 class="feature-title">Room Management</h3>
                        <p class="feature-description">Track room availability, assign tenants, and manage room amenities with visual floor mapping and occupancy tracking.</p>
                        <div class="feature-highlights">
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Visual Mapping</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Occupancy Tracking</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Room Amenities</span>
                            </div>
                        </div>
                    </div>

                    <div class="feature-card feature-card-tertiary">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrapper">
                                <div class="feature-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="feature-icon-glow"></div>
                            </div>
                            <div class="feature-badge">Financial</div>
                        </div>
                        <h3 class="feature-title">Billing & Invoices</h3>
                        <p class="feature-description">Automated billing cycles, professional PDF invoice generation, and comprehensive payment tracking system.</p>
                        <div class="feature-highlights">
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Auto Billing</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>PDF Generation</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Payment Tracking</span>
                            </div>
                        </div>
                    </div>

                    <div class="feature-card feature-card-supporting">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrapper">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="feature-icon-glow"></div>
                            </div>
                            <div class="feature-badge">Analytics</div>
                        </div>
                        <h3 class="feature-title">Analytics & Reports</h3>
                        <p class="feature-description">Real-time analytics dashboard, occupancy reports, and financial insights to drive informed business decisions.</p>
                        <div class="feature-highlights">
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Real-time Data</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Visual Reports</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Financial Insights</span>
                            </div>
                        </div>
                    </div>

                    <div class="feature-card feature-card-accent">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrapper">
                                <div class="feature-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="feature-icon-glow"></div>
                            </div>
                            <div class="feature-badge">Smart</div>
                        </div>
                        <h3 class="feature-title">Smart Notifications</h3>
                        <p class="feature-description">Intelligent notification system for payments, maintenance alerts, and important updates with customizable preferences.</p>
                        <div class="feature-highlights">
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Payment Alerts</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Maintenance Alerts</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Custom Settings</span>
                            </div>
                        </div>
                    </div>

                    <div class="feature-card feature-card-mobile">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrapper">
                                <div class="feature-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="feature-icon-glow"></div>
                            </div>
                            <div class="feature-badge">Mobile</div>
                        </div>
                        <h3 class="feature-title">Mobile Responsive</h3>
                        <p class="feature-description">Fully responsive design that works seamlessly across all devices - desktop, tablet, and mobile for on-the-go management.</p>
                        <div class="feature-highlights">
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Cross-Platform</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Touch Optimized</span>
                            </div>
                            <div class="feature-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Offline Ready</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Technology Section -->
    <section class="technology-section" id="technology">
        <div class="container">
            <div class="technology-content">
                <div class="technology-header">
                    <div class="technology-badge">
                        <i class="fas fa-code"></i>
                        Technology Stack
                    </div>
                    <h2 class="technology-title">
                        Built with Modern Technologies
                    </h2>
                    <p class="technology-subtitle">
                        Leveraging the latest technologies for performance, security, and scalability.
                    </p>
                </div>

                <div class="technology-grid">
                    <div class="tech-item tech-item-primary">
                        <div class="tech-item-header">
                            <div class="tech-icon-wrapper">
                                <div class="tech-icon">
                                    <i class="fab fa-laravel"></i>
                                </div>
                                <div class="tech-icon-glow"></div>
                            </div>
                            <div class="tech-badge">Backend</div>
                        </div>
                        <h3 class="tech-title">Laravel 12</h3>
                        <p class="tech-description">Modern PHP framework with latest features, robust security, and elegant syntax for rapid development.</p>
                        <div class="tech-highlights">
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Latest Version</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Security First</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Rapid Development</span>
                            </div>
                        </div>
                        <div class="tech-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%;"></div>
                            </div>
                            <span class="progress-text">100% Updated</span>
                        </div>
                    </div>

                    <div class="tech-item tech-item-secondary">
                        <div class="tech-item-header">
                            <div class="tech-icon-wrapper">
                                <div class="tech-icon">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div class="tech-icon-glow"></div>
                            </div>
                            <div class="tech-badge">Database</div>
                        </div>
                        <h3 class="tech-title">MySQL</h3>
                        <p class="tech-description">Reliable database management system with high performance, scalability, and data integrity.</p>
                        <div class="tech-highlights">
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>High Performance</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Data Integrity</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Scalable</span>
                            </div>
                        </div>
                        <div class="tech-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 95%;"></div>
                            </div>
                            <span class="progress-text">95% Optimized</span>
                        </div>
                    </div>

                    <div class="tech-item tech-item-tertiary">
                        <div class="tech-item-header">
                            <div class="tech-icon-wrapper">
                                <div class="tech-icon">
                                    <i class="fab fa-js-square"></i>
                                </div>
                                <div class="tech-icon-glow"></div>
                            </div>
                            <div class="tech-badge">Frontend</div>
                        </div>
                        <h3 class="tech-title">JavaScript ES6+</h3>
                        <p class="tech-description">Modern JavaScript with ES6+ features for interactive user interfaces and dynamic functionality.</p>
                        <div class="tech-highlights">
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>ES6+ Features</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Interactive UI</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Modern Syntax</span>
                            </div>
                        </div>
                        <div class="tech-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 90%;"></div>
                            </div>
                            <span class="progress-text">90% Modern</span>
                        </div>
                    </div>

                    <div class="tech-item tech-item-supporting">
                        <div class="tech-item-header">
                            <div class="tech-icon-wrapper">
                                <div class="tech-icon">
                                    <i class="fab fa-css3-alt"></i>
                                </div>
                                <div class="tech-icon-glow"></div>
                            </div>
                            <div class="tech-badge">Styling</div>
                        </div>
                        <h3 class="tech-title">CSS3 & Design</h3>
                        <p class="tech-description">Modern CSS3 with advanced features, responsive design, and beautiful user interfaces.</p>
                        <div class="tech-highlights">
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Responsive Design</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Modern CSS3</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Beautiful UI</span>
                            </div>
                        </div>
                        <div class="tech-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 88%;"></div>
                            </div>
                            <span class="progress-text">88% Complete</span>
                        </div>
                    </div>

                    <div class="tech-item tech-item-accent">
                        <div class="tech-item-header">
                            <div class="tech-icon-wrapper">
                                <div class="tech-icon">
                                    <i class="fab fa-php"></i>
                                </div>
                                <div class="tech-icon-glow"></div>
                            </div>
                            <div class="tech-badge">Language</div>
                        </div>
                        <h3 class="tech-title">PHP 8.3</h3>
                        <p class="tech-description">Latest PHP version with enhanced performance, new features, and improved security for robust backend development.</p>
                        <div class="tech-highlights">
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Latest Version</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Enhanced Performance</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Improved Security</span>
                            </div>
                        </div>
                        <div class="tech-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 92%;"></div>
                            </div>
                            <span class="progress-text">92% Optimized</span>
                        </div>
                    </div>

                    <div class="tech-item tech-item-mobile">
                        <div class="tech-item-header">
                            <div class="tech-icon-wrapper">
                                <div class="tech-icon">
                                    <i class="fab fa-bootstrap"></i>
                                </div>
                                <div class="tech-icon-glow"></div>
                            </div>
                            <div class="tech-badge">Framework</div>
                        </div>
                        <h3 class="tech-title">Bootstrap 5</h3>
                        <p class="tech-description">Modern CSS framework with responsive grid system, components, and utilities for rapid UI development.</p>
                        <div class="tech-highlights">
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Responsive Grid</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Component Library</span>
                            </div>
                            <div class="tech-highlight">
                                <i class="fas fa-check-circle"></i>
                                <span>Rapid Development</span>
                            </div>
                        </div>
                        <div class="tech-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 85%;"></div>
                            </div>
                            <span class="progress-text">85% Complete</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Section - Hidden -->
    <section class="demo-section" id="demo" style="display: none;">
        <div class="container">
            <div class="demo-content">
                <div class="demo-header">
                    <div class="demo-badge">
                        <i class="fas fa-play"></i>
                        System Demo
                    </div>
                    <h2 class="demo-title">
                        See Resido in Action
                    </h2>
                    <p class="demo-subtitle">
                        Experience the power of our comprehensive hostel management system with live demo and system access.
                    </p>
                </div>

                <div class="demo-grid">
                    <div class="demo-card">
                        <div class="demo-card-header">
                            <div class="demo-icon">
                                <i class="fas fa-desktop"></i>
                            </div>
                            <h3 class="demo-card-title">Live System Access</h3>
                        </div>
                        <div class="demo-card-content">
                            <p class="demo-card-description">
                                Access our fully functional system with demo data to explore all features and capabilities.
                            </p>
                            <div class="demo-features">
                                <div class="demo-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Modern web interface with intuitive design</span>
                                </div>
                                <div class="demo-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Complete documentation and user guides</span>
                                </div>
                                <div class="demo-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Demo data included for immediate testing</span>
                                </div>
                                <div class="demo-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Responsive design works on all devices</span>
                                </div>
                            </div>
                            <div class="demo-actions">
                                <a href="/login" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Access System
                                </a>
                                <a href="/dashboard" class="btn btn-secondary">
                                    <i class="fas fa-tachometer-alt"></i>
                                    View Dashboard
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="demo-card">
                        <div class="demo-card-header">
                            <div class="demo-icon">
                                <i class="fas fa-code"></i>
                            </div>
                            <h3 class="demo-card-title">System Information</h3>
                        </div>
                        <div class="demo-card-content">
                            <div class="system-info">
                                <div class="system-info-item">
                                    <div class="system-info-label">System URL</div>
                                    <div class="system-info-value">{{ url('/') }}</div>
                                </div>
                                <div class="system-info-item">
                                    <div class="system-info-label">Login URL</div>
                                    <div class="system-info-value">{{ url('/login') }}</div>
                                </div>
                                <div class="system-info-item">
                                    <div class="system-info-label">Dashboard URL</div>
                                    <div class="system-info-value">{{ url('/dashboard') }}</div>
                                </div>
                                <div class="system-info-item">
                                    <div class="system-info-label">Status</div>
                                    <div class="system-info-value status-active">
                                        <i class="fas fa-circle"></i>
                                        Live & Ready
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Demo Section - Redesigned -->
    <section class="new-demo-section" id="new-demo">
        <div class="container">
            <div class="new-demo-content">
                <div class="new-demo-header">
                    <div class="new-demo-badge">
                        <i class="fas fa-rocket"></i>
                        Interactive Demo
                    </div>
                    <h2 class="new-demo-title">
                        Experience Resido Live
                    </h2>
                    <p class="new-demo-subtitle">
                        Dive into our fully functional hostel management system with interactive demos, live data, and real-time features.
                    </p>
                </div>

                <div class="new-demo-showcase">
                    <div class="demo-main-card">
                        <div class="demo-main-header">
                            <div class="demo-status-indicator">
                                <div class="status-pulse"></div>
                                <span class="status-text">Live System</span>
                            </div>
                            <div class="demo-version-badge">v2.0</div>
                        </div>
                        
                        <div class="demo-preview-area">
                            <div class="demo-screen-mockup">
                                <div class="screen-header">
                                    <div class="screen-controls">
                                        <div class="control-dot red"></div>
                                        <div class="control-dot yellow"></div>
                                        <div class="control-dot green"></div>
                                    </div>
                                    <div class="screen-title">Resido Dashboard</div>
                                    <div class="screen-status">
                                        <i class="fas fa-wifi"></i>
                                        <span>Live</span>
                                    </div>
                                </div>
                                <div class="screen-content">
                                    <div class="demo-widgets">
                                        <div class="demo-widget">
                                            <div class="widget-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="widget-content">
                                                <div class="widget-number">247</div>
                                                <div class="widget-label">Active Tenants</div>
                                                <div class="widget-trend up">
                                                    <i class="fas fa-arrow-up"></i>
                                                    <span>+12%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="demo-widget">
                                            <div class="widget-icon">
                                                <i class="fas fa-bed"></i>
                                            </div>
                                            <div class="widget-content">
                                                <div class="widget-number">89%</div>
                                                <div class="widget-label">Occupancy</div>
                                                <div class="widget-trend up">
                                                    <i class="fas fa-arrow-up"></i>
                                                    <span>+5%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="demo-widget">
                                            <div class="widget-icon">
                                                <i class="fas fa-dollar-sign"></i>
                                            </div>
                                            <div class="widget-content">
                                                <div class="widget-number">₹2.4M</div>
                                                <div class="widget-label">Revenue</div>
                                                <div class="widget-trend up">
                                                    <i class="fas fa-arrow-up"></i>
                                                    <span>+18%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="demo-chart-area">
                                        <div class="chart-header">
                                            <h4 class="chart-title">Monthly Performance</h4>
                                            <div class="chart-period">Last 6 Months</div>
                                        </div>
                                        <div class="chart-visual">
                                            <div class="chart-bars">
                                                <div class="chart-bar" style="height: 60%;">
                                                    <div class="bar-fill"></div>
                                                    <span class="bar-label">Jan</span>
                                                </div>
                                                <div class="chart-bar" style="height: 75%;">
                                                    <div class="bar-fill"></div>
                                                    <span class="bar-label">Feb</span>
                                                </div>
                                                <div class="chart-bar" style="height: 85%;">
                                                    <div class="bar-fill"></div>
                                                    <span class="bar-label">Mar</span>
                                                </div>
                                                <div class="chart-bar" style="height: 70%;">
                                                    <div class="bar-fill"></div>
                                                    <span class="bar-label">Apr</span>
                                                </div>
                                                <div class="chart-bar" style="height: 90%;">
                                                    <div class="bar-fill"></div>
                                                    <span class="bar-label">May</span>
                                                </div>
                                                <div class="chart-bar" style="height: 95%;">
                                                    <div class="bar-fill"></div>
                                                    <span class="bar-label">Jun</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="demo-recent-activity">
                                        <div class="activity-header">
                                            <h4 class="activity-title">Recent Activity</h4>
                                            <div class="activity-indicator">
                                                <div class="activity-dot"></div>
                                                <span>Live Updates</span>
                                            </div>
                                        </div>
                                        <div class="activity-list">
                                            <div class="activity-item">
                                                <div class="activity-icon">
                                                    <i class="fas fa-user-plus"></i>
                                                </div>
                                                <div class="activity-content">
                                                    <div class="activity-text">New tenant registered</div>
                                                    <div class="activity-time">2 minutes ago</div>
                                                </div>
                                            </div>
                                            <div class="activity-item">
                                                <div class="activity-icon">
                                                    <i class="fas fa-credit-card"></i>
                                                </div>
                                                <div class="activity-content">
                                                    <div class="activity-text">Payment received</div>
                                                    <div class="activity-time">5 minutes ago</div>
                                                </div>
                                            </div>
                                            <div class="activity-item">
                                                <div class="activity-icon">
                                                    <i class="fas fa-bed"></i>
                                                </div>
                                                <div class="activity-content">
                                                    <div class="activity-text">Room status updated</div>
                                                    <div class="activity-time">8 minutes ago</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="demo-cta-area">
                            <div class="demo-cta-primary">
                                <a href="/login" class="demo-btn demo-btn-primary">
                                    <i class="fas fa-play-circle"></i>
                                    <span>Launch Demo</span>
                                </a>
                            </div>
                            <div class="demo-cta-secondary">
                                <a href="/dashboard" class="demo-btn demo-btn-secondary">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>Open Dashboard</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="demo-features-grid">
                        <div class="demo-feature-card">
                            <div class="feature-card-icon">
                                <i class="fas fa-mouse-pointer"></i>
                            </div>
                            <h4 class="feature-card-title">Interactive Interface</h4>
                            <p class="feature-card-desc">Click, explore, and interact with all features in real-time</p>
                        </div>
                        
                        <div class="demo-feature-card">
                            <div class="feature-card-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <h4 class="feature-card-title">Live Data</h4>
                            <p class="feature-card-desc">Experience the system with realistic demo data and scenarios</p>
                        </div>
                        
                        <div class="demo-feature-card">
                            <div class="feature-card-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h4 class="feature-card-title">Mobile Ready</h4>
                            <p class="feature-card-desc">Test responsive design across all device sizes</p>
                        </div>
                        
                        <div class="demo-feature-card">
                            <div class="feature-card-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4 class="feature-card-title">Real-time Updates</h4>
                            <p class="feature-card-desc">See live notifications and data updates as they happen</p>
                        </div>
                        
                        <div class="demo-feature-card">
                            <div class="feature-card-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h4 class="feature-card-title">Secure Access</h4>
                            <p class="feature-card-desc">Enterprise-grade security with role-based permissions and data encryption</p>
                        </div>
                    </div>
                </div>

                <div class="demo-credentials-section">
                    <div class="credentials-header">
                        <div class="credentials-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <h3 class="credentials-title">Demo Credentials</h3>
                        <p class="credentials-subtitle">Use these credentials to explore the system with different user roles</p>
                    </div>
                    
                    <div class="credentials-grid">
                        <div class="credential-card admin-card">
                            <div class="credential-header">
                                <div class="credential-icon">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div class="credential-badge admin-badge">Admin</div>
                            </div>
                            <div class="credential-content">
                                <div class="credential-field">
                                    <label class="credential-label">Username:</label>
                                    <div class="credential-value">
                                        <span class="credential-text">admin@resido.com</span>
                                        <button class="copy-btn" onclick="copyToClipboard('admin@resido.com')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="credential-field">
                                    <label class="credential-label">Password:</label>
                                    <div class="credential-value">
                                        <span class="credential-text">admin123</span>
                                        <button class="copy-btn" onclick="copyToClipboard('admin123')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="credential-access">
                                    <a href="/login" class="credential-login-btn">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Login as Admin
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="credential-card manager-card">
                            <div class="credential-header">
                                <div class="credential-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="credential-badge manager-badge">Manager</div>
                            </div>
                            <div class="credential-content">
                                <div class="credential-field">
                                    <label class="credential-label">Username:</label>
                                    <div class="credential-value">
                                        <span class="credential-text">manager@resido.com</span>
                                        <button class="copy-btn" onclick="copyToClipboard('manager@resido.com')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="credential-field">
                                    <label class="credential-label">Password:</label>
                                    <div class="credential-value">
                                        <span class="credential-text">manager123</span>
                                        <button class="copy-btn" onclick="copyToClipboard('manager123')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="credential-access">
                                    <a href="/login" class="credential-login-btn">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Login as Manager
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="credential-card tenant-card">
                            <div class="credential-header">
                                <div class="credential-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="credential-badge tenant-badge">Tenant</div>
                            </div>
                            <div class="credential-content">
                                <div class="credential-field">
                                    <label class="credential-label">Username:</label>
                                    <div class="credential-value">
                                        <span class="credential-text">tenant@resido.com</span>
                                        <button class="copy-btn" onclick="copyToClipboard('tenant@resido.com')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="credential-field">
                                    <label class="credential-label">Password:</label>
                                    <div class="credential-value">
                                        <span class="credential-text">tenant123</span>
                                        <button class="copy-btn" onclick="copyToClipboard('tenant123')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="credential-access">
                                    <a href="/login" class="credential-login-btn">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Login as Tenant
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="credentials-note">
                        <div class="note-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="note-content">
                            <strong>Note:</strong> These are demo credentials for testing purposes. All data is reset daily to ensure a fresh experience for every visitor.
                        </div>
                    </div>
                </div>

                <div class="demo-stats-section">
                    <div class="demo-stats-grid">
                        <div class="demo-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">15+</div>
                                <div class="stat-label">Modules Available</div>
                            </div>
                        </div>
                        
                        <div class="demo-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">500+</div>
                                <div class="stat-label">Demo Users</div>
                            </div>
                        </div>
                        
                        <div class="demo-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">System Access</div>
                            </div>
                        </div>
                        
                        <div class="demo-stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">99.9%</div>
                                <div class="stat-label">Uptime</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section - Redesigned -->
    <section class="new-cta-section">
        <div class="container">
            <div class="cta-background">
                <div class="cta-pattern"></div>
                <div class="cta-glow"></div>
            </div>
            
            <div class="new-cta-content">
                <div class="cta-header">
                    <div class="cta-badge">
                        <i class="fas fa-rocket"></i>
                        <span>Get Started Today</span>
                    </div>
                    <h2 class="cta-title">
                        Ready to Transform Your 
                        <span class="cta-highlight">Hostel Management?</span>
                    </h2>
                    <p class="cta-subtitle">
                        Join thousands of hostels already using Resido to streamline their operations, 
                        increase efficiency, and provide better experiences for their tenants.
                    </p>
                </div>
                
                <div class="cta-main-grid">
                    <div class="cta-actions-section">
                        <div class="cta-primary-action">
                            <a href="/register" class="cta-btn cta-btn-primary">
                                <i class="fas fa-play-circle"></i>
                                <span>Start Free Trial</span>
                            </a>
                            <div class="cta-note">
                                <i class="fas fa-check-circle"></i>
                                <span>No credit card required • 14-day free trial</span>
                            </div>
                        </div>
                        
                        <div class="cta-secondary-actions">
                            <a href="/contact" class="cta-btn cta-btn-secondary">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Schedule Demo</span>
                            </a>
                            <a href="/pricing" class="cta-btn cta-btn-outline">
                                <i class="fas fa-tag"></i>
                                <span>View Pricing</span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="cta-features-section">
                        <div class="cta-features">
                            <div class="cta-feature">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Secure & Reliable</strong>
                                    <span>Enterprise-grade security</span>
                                </div>
                            </div>
                            
                            <div class="cta-feature">
                                <div class="feature-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>24/7 Support</strong>
                                    <span>Expert assistance always</span>
                                </div>
                            </div>
                            
                            <div class="cta-feature">
                                <div class="feature-icon">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Easy Migration</strong>
                                    <span>Seamless data transfer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cta-visual-section">
                        <div class="cta-card-stack">
                            <div class="cta-card card-1">
                                <div class="card-header">
                                    <div class="card-dots">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <div class="card-title">Dashboard</div>
                                </div>
                                <div class="card-content">
                                    <div class="card-metric">
                                        <div class="metric-value">247</div>
                                        <div class="metric-label">Active Tenants</div>
                                    </div>
                                    <div class="card-chart">
                                        <div class="chart-bar" style="height: 60%;"></div>
                                        <div class="chart-bar" style="height: 80%;"></div>
                                        <div class="chart-bar" style="height: 45%;"></div>
                                        <div class="chart-bar" style="height: 90%;"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="cta-card card-2">
                                <div class="card-header">
                                    <div class="card-dots">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <div class="card-title">Analytics</div>
                                </div>
                                <div class="card-content">
                                    <div class="card-metric">
                                        <div class="metric-value">89%</div>
                                        <div class="metric-label">Occupancy Rate</div>
                                    </div>
                                    <div class="card-progress">
                                        <div class="progress-circle">
                                            <div class="progress-fill"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="cta-card card-3">
                                <div class="card-header">
                                    <div class="card-dots">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <div class="card-title">Reports</div>
                                </div>
                                <div class="card-content">
                                    <div class="card-metric">
                                        <div class="metric-value">₹2.4M</div>
                                        <div class="metric-label">Monthly Revenue</div>
                                    </div>
                                    <div class="card-trend">
                                        <i class="fas fa-arrow-up"></i>
                                        <span>+18%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer - Redesigned -->
    <footer class="new-footer">
        <div class="container">
            <div class="footer-background">
                <div class="footer-pattern"></div>
            </div>
            
            <div class="new-footer-content">
                <div class="footer-main">
                    <div class="footer-brand">
                        <div class="footer-logo">
                            <i class="fas fa-home"></i>
                            <span>Resido</span>
                        </div>
                        <p class="footer-description">
                            The complete hostel management solution that streamlines operations, 
                            enhances tenant experiences, and drives business growth.
                        </p>
                        <div class="footer-social">
                            <a href="#" class="social-link">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="footer-links">
                        <div class="footer-column">
                            <h4 class="footer-title">Product</h4>
                            <ul class="footer-list">
                                <li><a href="#features">Features</a></li>
                                <li><a href="#demo">Live Demo</a></li>
                                <li><a href="/pricing">Pricing</a></li>
                                <li><a href="#technology">Technology</a></li>
                                <li><a href="/api">API Documentation</a></li>
                                <li><a href="/integrations">Integrations</a></li>
                            </ul>
                        </div>
                        
                        <div class="footer-column">
                            <h4 class="footer-title">Solutions</h4>
                            <ul class="footer-list">
                                <li><a href="/hostels">For Hostels</a></li>
                                <li><a href="/pg">For PG Accommodations</a></li>
                                <li><a href="/student-housing">Student Housing</a></li>
                                <li><a href="/corporate-housing">Corporate Housing</a></li>
                                <li><a href="/co-living">Co-living Spaces</a></li>
                                <li><a href="/guest-houses">Guest Houses</a></li>
                            </ul>
                        </div>
                        
                        <div class="footer-column">
                            <h4 class="footer-title">Support</h4>
                            <ul class="footer-list">
                                <li><a href="/help">Help Center</a></li>
                                <li><a href="/contact">Contact Support</a></li>
                                <li><a href="/documentation">Documentation</a></li>
                                <li><a href="/tutorials">Video Tutorials</a></li>
                                <li><a href="/community">Community</a></li>
                                <li><a href="/status">System Status</a></li>
                            </ul>
                        </div>
                        
                        <div class="footer-column">
                            <h4 class="footer-title">Company</h4>
                            <ul class="footer-list">
                                <li><a href="/about">About Us</a></li>
                                <li><a href="/careers">Careers</a></li>
                                <li><a href="/blog">Blog</a></li>
                                <li><a href="/press">Press Kit</a></li>
                                <li><a href="/partners">Partners</a></li>
                                <li><a href="/investors">Investors</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="footer-newsletter">
                    <div class="newsletter-content">
                        <div class="newsletter-text">
                            <h4 class="newsletter-title">Stay Updated</h4>
                            <p class="newsletter-description">
                                Get the latest updates, tips, and industry insights delivered to your inbox.
                            </p>
                        </div>
                        <div class="newsletter-form">
                            <form class="email-form">
                                <div class="form-group">
                                    <input type="email" placeholder="Enter your email address" class="email-input" required>
                                    <button type="submit" class="email-submit">
                                        <i class="fas fa-paper-plane"></i>
                                        Subscribe
                                    </button>
                                </div>
                                <p class="newsletter-note">
                                    <i class="fas fa-shield-alt"></i>
                                    We respect your privacy. Unsubscribe at any time.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <div class="footer-bottom-content">
                        <div class="footer-copyright">
                            <p>&copy; 2024 Resido. All rights reserved.</p>
                        </div>
                        <div class="footer-legal">
                            <a href="/privacy">Privacy Policy</a>
                            <a href="/terms">Terms of Service</a>
                            <a href="/cookies">Cookie Policy</a>
                            <a href="/gdpr">GDPR Compliance</a>
                        </div>
                        <div class="footer-badges">
                            <div class="security-badge">
                                <i class="fas fa-lock"></i>
                                <span>SSL Secured</span>
                            </div>
                            <div class="uptime-badge">
                                <i class="fas fa-heartbeat"></i>
                                <span>99.9% Uptime</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Landing Page JavaScript V2 -->
    <script src="{{ asset('js/landing-2.js') }}"></script>
</body>
</html>