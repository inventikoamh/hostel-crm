<!-- Modern Sidebar -->
<div id="sidebar" class="modern-sidebar w-64 h-screen fixed left-0 top-0 flex flex-col z-50 sidebar-expanded">
    <!-- Logo Section -->
    <div class="sidebar-header flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="sidebar-logo w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-building text-white text-lg font-bold"></i>
            </div>
            <div class="flex flex-col">
                <h1 class="text-lg font-bold text-gray-900 dark:text-white">Hostel CRM</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Management System</p>
            </div>
        </div>
        <button onclick="toggleSidebar()" class="sidebar-toggle w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200" title="Toggle sidebar">
            <i class="fas fa-chevron-left text-sm text-gray-600 dark:text-gray-300"></i>
        </button>
    </div>

    <!-- Navigation - Scrollable -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 custom-scrollbar">
        <div class="space-y-6">
            <!-- Dashboard -->
            <div class="nav-section">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} group flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                    <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                        <i class="fas fa-tachometer-alt text-lg"></i>
                    </div>
                    <span class="nav-text font-medium">Dashboard</span>
                </a>
            </div>

            <!-- Management Section -->
            <div class="nav-section">
                <div class="nav-section-header flex items-center px-4 mb-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Management</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('hostels.index') }}" class="nav-item {{ request()->routeIs('hostels.*') ? 'active' : '' }} group flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                            <i class="fas fa-building text-lg"></i>
                        </div>
                        <span class="nav-text font-medium">Hostels</span>
                    </a>
                    <a href="{{ route('tenants.index') }}" class="nav-item {{ request()->routeIs('tenants.*') ? 'active' : '' }} group flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                            <i class="fas fa-users text-lg"></i>
                        </div>
                        <span class="nav-text font-medium">Tenants</span>
                    </a>
                    <a href="{{ route('rooms.index') }}" class="nav-item {{ request()->routeIs('rooms.*') ? 'active' : '' }} group flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                            <i class="fas fa-bed text-lg"></i>
                        </div>
                        <span class="nav-text font-medium">Rooms</span>
                    </a>
                    <a href="{{ route('map.index') }}" class="nav-item {{ request()->routeIs('map.*') ? 'active' : '' }} group flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                            <i class="fas fa-map text-lg"></i>
                        </div>
                        <span class="nav-text font-medium">Floor Map</span>
                    </a>
                    <a href="{{ route('availability.index') }}" class="nav-item {{ request()->routeIs('availability.*') ? 'active' : '' }} group flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                            <i class="fas fa-search text-lg"></i>
                        </div>
                        <span class="nav-text font-medium">Availability</span>
                    </a>
                </div>
            </div>

            <!-- Operations Section -->
            <div class="nav-section">
                <div class="nav-section-header flex items-center px-4 mb-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operations</h3>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('enquiries.index') }}" class="nav-item {{ request()->routeIs('enquiries.*') ? 'active' : '' }} group flex items-center px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                            <i class="fas fa-envelope text-lg"></i>
                        </div>
                        <span class="nav-text font-medium">Enquiries</span>
                </a>

                <!-- Billing Dropdown -->
                    <div class="nav-dropdown">
                        <button onclick="toggleBillingDropdown()" class="nav-item nav-dropdown-toggle {{ request()->routeIs('invoices.*') || request()->routeIs('payments.*') ? 'active' : '' }} group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="flex items-center">
                                <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-file-invoice-dollar text-lg"></i>
                                </div>
                                <span class="nav-text font-medium">Billing</span>
                        </div>
                            <i class="fas fa-chevron-down text-sm transition-transform duration-200 nav-dropdown-icon" id="billingDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                        <div id="billingDropdown" class="nav-dropdown-content ml-6 mt-1 space-y-1 {{ request()->routeIs('invoices.*') || request()->routeIs('payments.*') ? '' : 'hidden' }}">
                            <a href="{{ route('invoices.index') }}" class="nav-sub-item {{ request()->routeIs('invoices.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-file-invoice text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Invoices</span>
                            </a>
                            <a href="{{ route('payments.index') }}" class="nav-sub-item {{ request()->routeIs('payments.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-credit-card text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Payments</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="nav-section">
                <div class="nav-section-header flex items-center px-4 mb-3">
                    <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Services</h3>
                </div>
                <div class="space-y-1">
                <!-- Paid Services Dropdown -->
                    <div class="nav-dropdown">
                        <button onclick="toggleServicesDropdown()" class="nav-item nav-dropdown-toggle {{ request()->routeIs('paid-amenities.*') || request()->routeIs('tenant-amenities.*') || request()->routeIs('amenity-usage.*') ? 'active' : '' }} group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="flex items-center">
                                <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-concierge-bell text-lg"></i>
                                </div>
                                <span class="nav-text font-medium">Paid Services</span>
                        </div>
                            <i class="fas fa-chevron-down text-sm transition-transform duration-200 nav-dropdown-icon" id="servicesDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                        <div id="servicesDropdown" class="nav-dropdown-content ml-6 mt-1 space-y-1 {{ request()->routeIs('paid-amenities.*') || request()->routeIs('tenant-amenities.*') || request()->routeIs('amenity-usage.*') ? '' : 'hidden' }}">
                            <a href="{{ route('paid-amenities.index') }}" class="nav-sub-item {{ request()->routeIs('paid-amenities.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-list text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Manage Services</span>
                            </a>
                            <a href="{{ route('tenant-amenities.index') }}" class="nav-sub-item {{ request()->routeIs('tenant-amenities.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-users-cog text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Tenant Services</span>
                            </a>
                            <a href="{{ route('amenity-usage.index') }}" class="nav-sub-item {{ request()->routeIs('amenity-usage.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-calendar-check text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Usage Tracking</span>
                        </a>
                    </div>
                </div>

                <!-- Notifications Dropdown -->
                    <div class="nav-dropdown">
                        <button onclick="toggleNotificationsDropdown()" class="nav-item nav-dropdown-toggle {{ request()->routeIs('notifications.*') ? 'active' : '' }} group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="flex items-center">
                                <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-bell text-lg"></i>
                                </div>
                                <span class="nav-text font-medium">Notifications</span>
                        </div>
                            <i class="fas fa-chevron-down text-sm transition-transform duration-200 nav-dropdown-icon" id="notificationsDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                        <div id="notificationsDropdown" class="nav-dropdown-content ml-6 mt-1 space-y-1 {{ request()->routeIs('notifications.*') ? '' : 'hidden' }}">
                            <a href="{{ route('notifications.index') }}" class="nav-sub-item {{ request()->routeIs('notifications.index') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-list text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">All Notifications</span>
                            </a>
                            <a href="{{ route('notifications.settings.index') }}" class="nav-sub-item {{ request()->routeIs('notifications.settings.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-cog text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Settings</span>
                            </a>
                        </div>
                    </div>
                    </div>
                </div>

            <!-- User Management Section -->
            <div class="nav-section">
                <div class="nav-section-header flex items-center px-4 mb-3">
                    <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Administration</h3>
                </div>
                <div class="space-y-1">
                <!-- User Management Dropdown -->
                    <div class="nav-dropdown">
                        <button onclick="toggleUserManagementDropdown()" class="nav-item nav-dropdown-toggle {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('admin.tenant-profile-requests.*') || request()->routeIs('admin.usage-correction-requests.*') || request()->routeIs('admin.tenant-documents.*') ? 'active' : '' }} group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="flex items-center">
                                <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-users-cog text-lg"></i>
                                </div>
                                <span class="nav-text font-medium">Users & Forms</span>
                        </div>
                            <i class="fas fa-chevron-down text-sm transition-transform duration-200 nav-dropdown-icon" id="userManagementDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                        <div id="userManagementDropdown" class="nav-dropdown-content ml-6 mt-1 space-y-1 {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('admin.tenant-profile-requests.*') || request()->routeIs('admin.usage-correction-requests.*') || request()->routeIs('admin.tenant-documents.*') ? '' : 'hidden' }}">
                            <a href="{{ route('users.index') }}" class="nav-sub-item {{ request()->routeIs('users.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-users text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Users</span>
                            </a>
                            <a href="{{ route('roles.index') }}" class="nav-sub-item {{ request()->routeIs('roles.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-user-tag text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Roles</span>
                            </a>
                            <a href="{{ route('permissions.index') }}" class="nav-sub-item {{ request()->routeIs('permissions.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-key text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Permissions</span>
                            </a>
                            <a href="{{ route('admin.tenant-profile-requests.index') }}" class="nav-sub-item {{ request()->routeIs('admin.tenant-profile-requests.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-user-edit text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Profile Requests</span>
                            </a>
                            <a href="{{ route('admin.usage-correction-requests.index') }}" class="nav-sub-item {{ request()->routeIs('admin.usage-correction-requests.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-edit text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Usage Corrections</span>
                            </a>
                            <a href="{{ route('admin.tenant-documents.index') }}" class="nav-sub-item {{ request()->routeIs('admin.tenant-documents.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                                <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                    <i class="fas fa-file-alt text-sm"></i>
                                </div>
                                <span class="nav-text font-medium">Tenant Documents</span>
                </a>
            </div>
        </div>

        @if(auth()->user() && auth()->user()->isSuperAdmin())
        <!-- Super Admin Section -->
        <div class="nav-section">
            <div class="nav-section-header flex items-center px-4 mb-3">
                <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Super Admin</h3>
            </div>
            <div class="space-y-1">
                <!-- Super Admin Dropdown -->
                <div class="nav-dropdown">
                    <button onclick="toggleSuperAdminDropdown()" class="nav-item nav-dropdown-toggle {{ request()->routeIs('super-admin.*') ? 'active' : '' }} group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="flex items-center">
                            <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                <i class="fas fa-crown text-lg"></i>
                            </div>
                            <span class="nav-text font-medium">Super Admin</span>
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform duration-200 nav-dropdown-icon" id="superAdminDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="superAdminDropdown" class="nav-dropdown-content ml-6 mt-1 space-y-1 {{ request()->routeIs('super-admin.*') ? '' : 'hidden' }}">
                        <a href="{{ route('super-admin.dashboard') }}" class="nav-sub-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                            <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                <i class="fas fa-tachometer-alt text-sm"></i>
                            </div>
                            <span class="nav-text font-medium">Dashboard</span>
                        </a>
                        <a href="{{ route('super-admin.settings') }}" class="nav-sub-item {{ request()->routeIs('super-admin.settings') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                            <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                <i class="fas fa-cog text-sm"></i>
                            </div>
                            <span class="nav-text font-medium">System Settings</span>
                        </a>
                        <a href="{{ route('super-admin.users') }}" class="nav-sub-item {{ request()->routeIs('super-admin.users') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                            <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                <i class="fas fa-users-cog text-sm"></i>
                            </div>
                            <span class="nav-text font-medium">User Management</span>
                        </a>
                        <a href="{{ route('super-admin.system-health') }}" class="nav-sub-item {{ request()->routeIs('super-admin.system-health') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                            <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                <i class="fas fa-heartbeat text-sm"></i>
                            </div>
                            <span class="nav-text font-medium">System Health</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Configuration Section -->
        <div class="nav-section">
            <div class="nav-section-header flex items-center px-4 mb-3">
                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Configuration</h3>
            </div>
            <div class="space-y-1">
                <!-- Configuration Dropdown -->
                <div class="nav-dropdown">
                    <button onclick="toggleConfigDropdown()" class="nav-item nav-dropdown-toggle {{ request()->routeIs('config.*') ? 'active' : '' }} group flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200">
                        <div class="flex items-center">
                            <div class="nav-icon w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                <i class="fas fa-cog text-lg"></i>
                            </div>
                            <span class="nav-text font-medium">System Config</span>
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform duration-200 nav-dropdown-icon" id="configDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="configDropdown" class="nav-dropdown-content ml-6 mt-1 space-y-1 {{ request()->routeIs('config.*') ? '' : 'hidden' }}">
                        <a href="{{ route('config.amenities.index') }}" class="nav-sub-item {{ request()->routeIs('config.amenities.*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                            <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                <i class="fas fa-star text-sm"></i>
                            </div>
                            <span class="nav-text font-medium">Hostel Amenities</span>
                        </a>
                        <a href="{{ route('config.smtp-settings') }}" class="nav-sub-item {{ request()->routeIs('config.smtp-settings*') ? 'active' : '' }} group flex items-center px-4 py-2 rounded-lg text-sm transition-all duration-200">
                            <div class="nav-sub-icon w-8 h-8 flex items-center justify-center rounded-lg mr-3 transition-all duration-200">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <span class="nav-text font-medium">SMTP Settings</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- User Profile - Fixed at Bottom -->
    <div class="sidebar-footer p-4" style="border-top: 1px solid var(--border-color);">
        <div class="relative">
            <button onclick="toggleUserDropdown()" class="user-profile-card w-full flex items-center p-3 rounded-xl transition-all duration-200 group hover:opacity-80"
                    style="background-color: var(--bg-secondary);">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-3 shadow-sm">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
                <div class="flex-1 text-left">
                    <p class="font-semibold text-sm" style="color: var(--text-primary);">Admin User</p>
                    <p class="text-xs" style="color: var(--text-secondary);">admin@hostel.com</p>
                </div>
                <i class="fas fa-chevron-down transition-transform duration-200 group-hover:opacity-80"
                   style="color: var(--text-secondary);" id="userDropdownIcon"></i>
            </button>

            <!-- User Dropdown -->
            <div id="userDropdown" class="absolute bottom-full left-0 right-0 mb-2 rounded-xl shadow-xl border hidden overflow-hidden"
                 style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="py-2">
                    <a href="{{ route('profile.edit') }}" class="user-dropdown-item flex items-center px-4 py-3 transition-colors duration-200"
                       style="color: var(--text-primary);">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3"
                             style="background-color: var(--bg-secondary);">
                            <i class="fas fa-user-circle text-sm" style="color: var(--text-secondary);"></i>
                        </div>
                        <span class="text-sm font-medium">Profile</span>
                    </a>
                    <a href="#" class="user-dropdown-item flex items-center px-4 py-3 transition-colors duration-200"
                       style="color: var(--text-primary);">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3"
                             style="background-color: var(--bg-secondary);">
                            <i class="fas fa-cog text-sm" style="color: var(--text-secondary);"></i>
                        </div>
                        <span class="text-sm font-medium">Account Settings</span>
                    </a>
                    <hr class="my-1" style="border-color: var(--border-color);">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 transition-colors duration-200 hover:opacity-80"
                                style="color: #dc2626; background-color: rgba(220, 38, 38, 0.1);">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3"
                                 style="background-color: rgba(220, 38, 38, 0.2);">
                                <i class="fas fa-sign-out-alt text-sm" style="color: #dc2626;"></i>
                            </div>
                            <span class="text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
