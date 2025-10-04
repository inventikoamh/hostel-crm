@extends('layouts.app')

@section('title', 'Dashboard - Hostel CRM')

@php
    $title = 'Dashboard';
    $subtitle = 'Welcome back! Here\'s what\'s happening at your hostel today.';
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <!-- Total Rooms Card -->
        <x-stats-card
            icon="fas fa-bed"
            icon-color="#2563eb"
            icon-bg="rgba(59, 130, 246, 0.1)"
            title="Total Rooms"
            :value="$stats['total_rooms']"
            subtitle="All available rooms"
            subtitle-icon="fas fa-home"
        />

        <!-- Occupied Rooms Card -->
        <x-stats-card
            icon="fas fa-user-check"
            icon-color="#16a34a"
            icon-bg="rgba(34, 197, 94, 0.1)"
            title="Occupied"
            :value="$stats['occupied_rooms']"
            subtitle="Currently occupied"
            subtitle-icon="fas fa-check-circle"
        />

        <!-- Available Rooms Card -->
        <x-stats-card
            icon="fas fa-door-open"
            icon-color="#ea580c"
            icon-bg="rgba(249, 115, 22, 0.1)"
            title="Available"
            :value="$stats['available_rooms']"
            subtitle="Ready to rent"
            subtitle-icon="fas fa-check-circle"
        />

        <!-- Tenants Card -->
        <x-stats-card
            icon="fas fa-users"
            icon-color="#9333ea"
            icon-bg="rgba(147, 51, 234, 0.1)"
            title="Tenants"
            :value="$stats['total_students']"
            subtitle="Total tenants"
            subtitle-icon="fas fa-graduation-cap"
        />

        <!-- Revenue Card -->
        <x-stats-card
            icon="fas fa-rupee-sign"
            icon-color="#059669"
            icon-bg="rgba(16, 185, 129, 0.1)"
            title="Revenue"
            :value="'₹' . number_format($stats['monthly_revenue'])"
            subtitle="This month"
            subtitle-icon="fas fa-calendar"
        />

        <!-- Pending Requests Card -->
        <x-stats-card
            icon="fas fa-clock"
            icon-color="#d97706"
            icon-bg="rgba(245, 158, 11, 0.1)"
            title="Pending"
            :value="$stats['pending_requests']"
            subtitle="Awaiting approval"
            subtitle-icon="fas fa-hourglass-half"
        />
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- Recent Activities -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                    <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Recent Activities</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg" style="background-color: var(--hover-bg);">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user-plus text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium" style="color: var(--text-primary);">New tenant registered</p>
                                <p class="text-sm" style="color: var(--text-secondary);">John Smith moved into Room A-101</p>
                            </div>
                            <span class="text-sm text-gray-500">2 hours ago</span>
                        </div>

                        <div class="flex items-center p-4 bg-gray-50 rounded-lg" style="background-color: var(--hover-bg);">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-credit-card text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium" style="color: var(--text-primary);">Payment received</p>
                                <p class="text-sm" style="color: var(--text-secondary);">₹1,200 from Sarah Johnson</p>
                            </div>
                            <span class="text-sm text-gray-500">4 hours ago</span>
                        </div>

                        <div class="flex items-center p-4 bg-gray-50 rounded-lg" style="background-color: var(--hover-bg);">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-tools text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium" style="color: var(--text-primary);">Maintenance request</p>
                                <p class="text-sm" style="color: var(--text-secondary);">Room B-205 - AC repair needed</p>
                            </div>
                            <span class="text-sm text-gray-500">6 hours ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                    <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Upcoming Events</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-calendar text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-medium" style="color: var(--text-primary);">Rent Due</p>
                                <p class="text-sm" style="color: var(--text-secondary);">Tomorrow - 15 tenants</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-home text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium" style="color: var(--text-primary);">Room Inspection</p>
                                <p class="text-sm" style="color: var(--text-secondary);">Friday - Block A</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-users text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium" style="color: var(--text-primary);">New Applications</p>
                                <p class="text-sm" style="color: var(--text-secondary);">3 pending review</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
