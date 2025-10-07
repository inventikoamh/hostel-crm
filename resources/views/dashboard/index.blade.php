@extends('layouts.app')

@section('title', 'Dashboard - Hostel CRM')

@php
    $title = 'Dashboard';
    $subtitle = 'Welcome to Hostel CRM - Your comprehensive management system';
@endphp

@section('content')
    <!-- Quick Actions -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('hostels.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Add Hostel
            </a>
            <a href="{{ route('tenants.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-user-plus mr-2"></i>
                Add Tenant
            </a>
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                <i class="fas fa-file-invoice mr-2"></i>
                Create Invoice
            </a>
            <a href="{{ route('enquiries.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200">
                <i class="fas fa-envelope mr-2"></i>
                View Enquiries
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <!-- Total Hostels Card -->
        <x-stats-card
            icon="fas fa-building"
            icon-color="#2563eb"
            icon-bg="rgba(59, 130, 246, 0.1)"
            title="Total Hostels"
            :value="$stats['total_hostels']"
            subtitle="All registered hostels"
            subtitle-icon="fas fa-home"
        />

        <!-- Total Tenants Card -->
        <x-stats-card
            icon="fas fa-users"
            icon-color="#16a34a"
            icon-bg="rgba(34, 197, 94, 0.1)"
            title="Active Tenants"
            :value="$stats['total_tenants']"
            subtitle="Currently active"
            subtitle-icon="fas fa-user-check"
        />

        <!-- Total Rooms Card -->
        <x-stats-card
            icon="fas fa-bed"
            icon-color="#9333ea"
            icon-bg="rgba(147, 51, 234, 0.1)"
            title="Total Rooms"
            :value="$stats['total_rooms']"
            subtitle="All available rooms"
            subtitle-icon="fas fa-door-open"
        />

        <!-- Occupancy Rate Card -->
        <x-stats-card
            icon="fas fa-chart-line"
            icon-color="#ea580c"
            icon-bg="rgba(249, 115, 22, 0.1)"
            title="Occupancy Rate"
            :value="$stats['occupancy_rate'] . '%'"
            subtitle="Current occupancy"
            subtitle-icon="fas fa-percentage"
        />
    </div>

    <!-- Secondary Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <!-- Monthly Revenue Card -->
        <x-stats-card
            icon="fas fa-rupee-sign"
            icon-color="#059669"
            icon-bg="rgba(16, 185, 129, 0.1)"
            title="Monthly Revenue"
            :value="'₹' . number_format($stats['monthly_revenue'])"
            subtitle="This month"
            subtitle-icon="fas fa-calendar"
        />

        <!-- Pending Enquiries Card -->
        <x-stats-card
            icon="fas fa-envelope"
            icon-color="#d97706"
            icon-bg="rgba(245, 158, 11, 0.1)"
            title="Pending Enquiries"
            :value="$stats['pending_enquiries']"
            subtitle="Need attention"
            subtitle-icon="fas fa-clock"
        />

        <!-- Overdue Invoices Card -->
        <x-stats-card
            icon="fas fa-exclamation-triangle"
            icon-color="#dc2626"
            icon-bg="rgba(239, 68, 68, 0.1)"
            title="Overdue Invoices"
            :value="$stats['overdue_invoices']"
            subtitle="Require follow-up"
            subtitle-icon="fas fa-alert"
        />

        <!-- Total Payments Card -->
        <x-stats-card
            icon="fas fa-credit-card"
            icon-color="#7c3aed"
            icon-bg="rgba(124, 58, 237, 0.1)"
            title="Total Payments"
            :value="$stats['total_payments']"
            subtitle="Verified payments"
            subtitle-icon="fas fa-check-circle"
        />
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Recent Activities -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Recent Activities</h2>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                    </div>
                </div>
                <div class="p-6">
                    @if($recent_activities->count() > 0)
                        <div class="space-y-4">
                            @foreach($recent_activities as $activity)
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200" style="background-color: var(--hover-bg);">
                                    <div class="w-10 h-10 {{ $activity['icon_bg'] }} rounded-full flex items-center justify-center mr-4">
                                        <i class="{{ $activity['icon'] }} {{ $activity['icon_color'] }}"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium" style="color: var(--text-primary);">{{ $activity['title'] }}</p>
                                        <p class="text-sm" style="color: var(--text-secondary);">{{ $activity['description'] }}</p>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $activity['time'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No recent activities found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Events & Alerts -->
        <div class="space-y-6">
            <!-- Upcoming Events -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                    <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Upcoming Events</h2>
                </div>
                <div class="p-6">
                    @if($upcoming_events->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcoming_events as $event)
                                <div class="flex items-center p-3 rounded-lg {{ $event['priority'] === 'high' ? 'bg-red-50 border border-red-200' : 'bg-gray-50' }}" style="{{ $event['priority'] !== 'high' ? 'background-color: var(--hover-bg);' : '' }}">
                                    <div class="w-10 h-10 {{ $event['icon_bg'] }} rounded-lg flex items-center justify-center mr-3">
                                        <i class="{{ $event['icon'] }} {{ $event['icon_color'] }}"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-sm" style="color: var(--text-primary);">{{ $event['title'] }}</p>
                                        <p class="text-xs" style="color: var(--text-secondary);">{{ $event['description'] }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $event['date'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check text-2xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No upcoming events</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                    <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Quick Summary</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm" style="color: var(--text-secondary);">Available Beds</span>
                            <span class="font-semibold" style="color: var(--text-primary);">{{ $stats['available_beds'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm" style="color: var(--text-secondary);">Pending Payments</span>
                            <span class="font-semibold" style="color: var(--text-primary);">{{ $stats['pending_payments'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm" style="color: var(--text-secondary);">Total Invoices</span>
                            <span class="font-semibold" style="color: var(--text-primary);">{{ $stats['total_invoices'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm" style="color: var(--text-secondary);">Total Beds</span>
                            <span class="font-semibold" style="color: var(--text-primary);">{{ $stats['total_beds'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="mt-6 sm:mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                    <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Monthly Revenue Trend</h2>
                </div>
                <div class="p-6">
                    <div class="h-64 flex items-end justify-between space-x-2">
                        @foreach($chart_data['monthly_revenue'] as $data)
                            <div class="flex flex-col items-center flex-1">
                                <div class="w-full bg-blue-200 rounded-t" style="height: {{ $data['revenue'] > 0 ? max(20, ($data['revenue'] / max(array_column($chart_data['monthly_revenue'], 'revenue'))) * 200) : 20 }}px;"></div>
                                <span class="text-xs text-gray-500 mt-2">{{ $data['month'] }}</span>
                                <span class="text-xs font-medium" style="color: var(--text-primary);">₹{{ number_format($data['revenue'] / 1000, 0) }}k</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Occupancy Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                    <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Occupancy Rate Trend</h2>
                </div>
                <div class="p-6">
                    <div class="h-64 flex items-end justify-between space-x-2">
                        @foreach($chart_data['monthly_occupancy'] as $data)
                            <div class="flex flex-col items-center flex-1">
                                <div class="w-full bg-green-200 rounded-t" style="height: {{ max(20, ($data['occupancy'] / 100) * 200) }}px;"></div>
                                <span class="text-xs text-gray-500 mt-2">{{ $data['month'] }}</span>
                                <span class="text-xs font-medium" style="color: var(--text-primary);">{{ $data['occupancy'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
