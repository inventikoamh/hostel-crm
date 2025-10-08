@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@php
    $title = 'Super Admin Dashboard';
    $subtitle = 'System administration and management';
@endphp

@section('content')
<div class="container mx-auto p-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">{{ $title }}</h1>
        <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $subtitle }}</p>
    </div>

    <!-- System Status Alert -->
    @if($stats['demo_mode'])
    <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 rounded-lg" style="background-color: #fef3c7; border-color: #f59e0b;">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-yellow-800">Demo Mode Active</h3>
                <p class="text-sm text-yellow-700 mt-1">The system is currently running in demo mode. Some features may be restricted.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stats-card
            title="Total Users"
            value="{{ number_format($stats['total_users']) }}"
            icon="fas fa-users"
            color="blue"
            :trend="null"
        />
        <x-stats-card
            title="Super Admins"
            value="{{ number_format($stats['total_super_admins']) }}"
            icon="fas fa-crown"
            color="purple"
            :trend="null"
        />
        <x-stats-card
            title="Total Hostels"
            value="{{ number_format($stats['total_hostels']) }}"
            icon="fas fa-building"
            color="green"
            :trend="null"
        />
        <x-stats-card
            title="Total Rooms"
            value="{{ number_format($stats['total_rooms']) }}"
            icon="fas fa-door-open"
            color="indigo"
            :trend="null"
        />
    </div>

    <!-- System Limits -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- System Limits Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">System Limits</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Max Hostels</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $stats['max_hostels'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Max Floors per Hostel</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $stats['max_floors_per_hostel'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Current Hostels</span>
                    <span class="text-sm font-medium {{ $stats['total_hostels'] >= $stats['max_hostels'] ? 'text-red-600' : 'text-green-600' }}" style="color: var(--text-primary);">
                        {{ $stats['total_hostels'] }}/{{ $stats['max_hostels'] }}
                    </span>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('super-admin.settings') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="fas fa-cog mr-1"></i>
                    Manage Settings
                </a>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('super-admin.settings') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200" style="background-color: var(--hover-bg);">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-cog text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-primary);">System Settings</p>
                        <p class="text-xs" style="color: var(--text-secondary);">Configure system preferences</p>
                    </div>
                </a>
                <a href="{{ route('super-admin.users') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200" style="background-color: var(--hover-bg);">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users text-purple-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-primary);">User Management</p>
                        <p class="text-xs" style="color: var(--text-secondary);">Manage users and permissions</p>
                    </div>
                </a>
                <a href="{{ route('super-admin.system-health') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200" style="background-color: var(--hover-bg);">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-heartbeat text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-primary);">System Health</p>
                        <p class="text-xs" style="color: var(--text-secondary);">Check system status</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Recent Users</h3>
            <a href="{{ route('super-admin.users') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                View All
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b" style="border-color: var(--border-color);">
                        <th class="text-left py-2 text-sm font-medium" style="color: var(--text-secondary);">Name</th>
                        <th class="text-left py-2 text-sm font-medium" style="color: var(--text-secondary);">Email</th>
                        <th class="text-left py-2 text-sm font-medium" style="color: var(--text-secondary);">Type</th>
                        <th class="text-left py-2 text-sm font-medium" style="color: var(--text-secondary);">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                    <tr class="border-b" style="border-color: var(--border-color);">
                        <td class="py-2 text-sm" style="color: var(--text-primary);">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-white text-xs"></i>
                                </div>
                                {{ $user->name }}
                                @if($user->is_super_admin)
                                    <i class="fas fa-crown text-yellow-500 ml-2 text-xs" title="Super Admin"></i>
                                @endif
                            </div>
                        </td>
                        <td class="py-2 text-sm" style="color: var(--text-secondary);">{{ $user->email }}</td>
                        <td class="py-2 text-sm">
                            @if($user->is_super_admin)
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Super Admin</span>
                            @elseif($user->is_tenant)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Tenant</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Admin</span>
                            @endif
                        </td>
                        <td class="py-2 text-sm" style="color: var(--text-secondary);">{{ $user->created_at->format('M j, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm" style="color: var(--text-secondary);">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
