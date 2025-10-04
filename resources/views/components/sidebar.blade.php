<!-- Sidebar -->
<div id="sidebar" class="sidebar-gradient w-64 h-screen fixed left-0 top-0 flex flex-col text-white z-50 sidebar-expanded">
    <!-- Logo -->
    <div class="flex items-center p-4 border-b border-white border-opacity-10 h-16">
        <button onclick="toggleSidebar()" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-30 hover:bg-opacity-40 transition-all duration-200 mr-3" title="Toggle sidebar">
            <i class="fas fa-chevron-left text-sm text-gray-800 font-bold"></i>
        </button>
        <div class="w-10 h-10 bg-white bg-opacity-40 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-building text-xl text-gray-800 font-bold"></i>
        </div>
        <h1 class="text-xl font-bold text-white">Hostel CRM</h1>
    </div>

    <!-- Navigation - Scrollable -->
    <nav class="flex-1 overflow-y-auto py-4 px-6 custom-scrollbar">
        <div class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>

            <!-- Management Section -->
            <div class="pt-4">
                <h3 class="text-xs font-semibold text-white text-opacity-60 uppercase tracking-wider mb-2 px-4">Management</h3>
                <a href="{{ route('hostels.index') }}" class="sidebar-item {{ request()->routeIs('hostels.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                    <i class="fas fa-building mr-3"></i>
                    Hostels
                </a>
                <a href="{{ route('tenants.index') }}" class="sidebar-item {{ request()->routeIs('tenants.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                    <i class="fas fa-users mr-3"></i>
                    Tenants
                </a>
                <a href="{{ route('rooms.index') }}" class="sidebar-item {{ request()->routeIs('rooms.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                    <i class="fas fa-bed mr-3"></i>
                    Rooms
                </a>
                <a href="{{ route('map.index') }}" class="sidebar-item {{ request()->routeIs('map.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                    <i class="fas fa-map mr-3"></i>
                    Floor Map
                </a>
            </div>

            <!-- Operations Section -->
            <div class="pt-4">
                <h3 class="text-xs font-semibold text-white text-opacity-60 uppercase tracking-wider mb-2 px-4">Operations</h3>
                <a href="{{ route('enquiries.index') }}" class="sidebar-item {{ request()->routeIs('enquiries.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-lg">
                    <i class="fas fa-envelope mr-3"></i>
                    Enquiries
                </a>

                <!-- Billing Dropdown -->
                <div class="sidebar-dropdown mb-4">
                    <button onclick="toggleBillingDropdown()" class="sidebar-item {{ request()->routeIs('invoices.*') || request()->routeIs('payments.*') ? 'active' : '' }} flex items-center justify-between w-full px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-file-invoice-dollar mr-3"></i>
                            Billing
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform duration-200" id="billingDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="billingDropdown" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('invoices.*') || request()->routeIs('payments.*') ? '' : 'hidden' }}">
                        <a href="{{ route('invoices.index') }}" class="sidebar-sub-item {{ request()->routeIs('invoices.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-file-invoice mr-3 text-xs"></i>
                            Invoices
                        </a>
                        <a href="{{ route('payments.index') }}" class="sidebar-sub-item {{ request()->routeIs('payments.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-credit-card mr-3 text-xs"></i>
                            Payments
                        </a>
                    </div>
                </div>
            </div>

            <!-- Configuration Section -->
            <div class="pt-4">
                <h3 class="text-xs font-semibold text-white text-opacity-60 uppercase tracking-wider mb-2 px-4">Services</h3>

                <!-- Paid Services Dropdown -->
                <div class="sidebar-dropdown mb-4">
                    <button onclick="toggleServicesDropdown()" class="sidebar-item {{ request()->routeIs('paid-amenities.*') || request()->routeIs('tenant-amenities.*') || request()->routeIs('amenity-usage.*') ? 'active' : '' }} flex items-center justify-between w-full px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-concierge-bell mr-3"></i>
                            Paid Services
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform duration-200" id="servicesDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="servicesDropdown" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('paid-amenities.*') || request()->routeIs('tenant-amenities.*') || request()->routeIs('amenity-usage.*') ? '' : 'hidden' }}">
                        <a href="{{ route('paid-amenities.index') }}" class="sidebar-sub-item {{ request()->routeIs('paid-amenities.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-list mr-3 text-xs"></i>
                            Manage Services
                        </a>
                        <a href="{{ route('tenant-amenities.index') }}" class="sidebar-sub-item {{ request()->routeIs('tenant-amenities.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-users-cog mr-3 text-xs"></i>
                            Tenant Services
                        </a>
                        <a href="{{ route('amenity-usage.index') }}" class="sidebar-sub-item {{ request()->routeIs('amenity-usage.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-calendar-check mr-3 text-xs"></i>
                            Usage Tracking
                        </a>
                    </div>
                </div>

                <!-- Notifications Dropdown -->
                <div class="sidebar-dropdown mb-4">
                    <button onclick="toggleNotificationsDropdown()" class="sidebar-item {{ request()->routeIs('notifications.*') ? 'active' : '' }} flex items-center justify-between w-full px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-bell mr-3"></i>
                            Notifications
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform duration-200" id="notificationsDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="notificationsDropdown" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('notifications.*') ? '' : 'hidden' }}">
                        <a href="{{ route('notifications.index') }}" class="sidebar-sub-item {{ request()->routeIs('notifications.index') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-list mr-3 text-xs"></i>
                            All Notifications
                        </a>
                        <a href="{{ route('notifications.settings.index') }}" class="sidebar-sub-item {{ request()->routeIs('notifications.settings.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-cog mr-3 text-xs"></i>
                            Settings
                        </a>
                    </div>
                </div>

                <!-- User Management Dropdown -->
                <div class="sidebar-dropdown mb-4">
                    <button onclick="toggleUserManagementDropdown()" class="sidebar-item {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('admin.tenant-profile-requests.*') || request()->routeIs('admin.usage-correction-requests.*') || request()->routeIs('admin.tenant-documents.*') ? 'active' : '' }} flex items-center justify-between w-full px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-users-cog mr-3"></i>
                            Users & Forms
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform duration-200" id="userManagementDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="userManagementDropdown" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('admin.tenant-profile-requests.*') || request()->routeIs('admin.usage-correction-requests.*') || request()->routeIs('admin.tenant-documents.*') ? '' : 'hidden' }}">
                        <a href="{{ route('users.index') }}" class="sidebar-sub-item {{ request()->routeIs('users.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-users mr-3 text-xs"></i>
                            Users
                        </a>
                        <a href="{{ route('roles.index') }}" class="sidebar-sub-item {{ request()->routeIs('roles.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-user-tag mr-3 text-xs"></i>
                            Roles
                        </a>
                        <a href="{{ route('permissions.index') }}" class="sidebar-sub-item {{ request()->routeIs('permissions.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-key mr-3 text-xs"></i>
                            Permissions
                        </a>
                        <a href="{{ route('admin.tenant-profile-requests.index') }}" class="sidebar-sub-item {{ request()->routeIs('admin.tenant-profile-requests.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-user-edit mr-3 text-xs"></i>
                            Profile Requests
                        </a>
                <a href="{{ route('admin.usage-correction-requests.index') }}" class="sidebar-sub-item {{ request()->routeIs('admin.usage-correction-requests.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-edit mr-3 text-xs"></i>
                    Usage Corrections
                </a>
                <a href="{{ route('admin.tenant-documents.index') }}" class="sidebar-sub-item {{ request()->routeIs('admin.tenant-documents.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-file-alt mr-3 text-xs"></i>
                    Tenant Documents
                </a>
            </div>
        </div>

                <h3 class="text-xs font-semibold text-white text-opacity-60 uppercase tracking-wider mb-2 px-4">Configuration</h3>

                <!-- Configuration Dropdown -->
                <div class="sidebar-dropdown">
                    <button onclick="toggleConfigDropdown()" class="sidebar-item {{ request()->routeIs('config.*') ? 'active' : '' }} flex items-center justify-between w-full px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-cog mr-3"></i>
                            System Config
                        </div>
                        <i class="fas fa-chevron-down text-sm transition-transform duration-200" id="configDropdownIcon"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="configDropdown" class="ml-4 mt-1 space-y-1 {{ request()->routeIs('config.*') ? '' : 'hidden' }}">
                        <a href="{{ route('config.amenities.index') }}" class="sidebar-sub-item {{ request()->routeIs('config.amenities.*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-star mr-3 text-xs"></i>
                            Hostel Amenities
                        </a>
                        <a href="{{ route('config.smtp-settings') }}" class="sidebar-sub-item {{ request()->routeIs('config.smtp-settings*') ? 'active' : '' }} flex items-center px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-envelope mr-3 text-xs"></i>
                            SMTP Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- User Profile - Fixed at Bottom -->
    <div class="border-t border-white border-opacity-10 p-4">
        <div class="relative">
            <button onclick="toggleUserDropdown()" class="user-profile-card w-full flex items-center p-3 bg-white bg-opacity-90 rounded-lg hover:bg-opacity-95 transition-all duration-200">
                <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div class="flex-1 text-left">
                    <p class="font-medium text-sm text-gray-800">Admin User</p>
                    <p class="text-xs text-gray-600">admin@hostel.com</p>
                </div>
                <i class="fas fa-chevron-down text-gray-600 transition-transform duration-200" id="userDropdownIcon"></i>
            </button>

            <!-- User Dropdown -->
            <div id="userDropdown" class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-lg border border-gray-200 hidden">
                <div class="py-2">
                    <a href="#" class="flex items-center px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-user-circle mr-3 text-gray-600"></i>
                        <span class="text-sm">Profile</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-gray-800 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-cog mr-3 text-gray-600"></i>
                        <span class="text-sm">Account Settings</span>
                    </a>
                    <hr class="my-1 border-gray-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-red-600 hover:bg-red-50 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            <span class="text-sm">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
