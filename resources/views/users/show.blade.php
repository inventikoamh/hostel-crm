@extends('layouts.app')

@section('title', 'User Details')

@php
    $title = 'User Details';
    $subtitle = 'View user information and permissions';
    $showBackButton = true;
    $backUrl = route('users.index');
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- User Profile Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if($user->avatar)
                            <img class="h-20 w-20 rounded-full object-cover" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                        @else
                            <div class="h-20 w-20 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                <i class="fas fa-user text-2xl text-gray-600 dark:text-gray-300"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">{{ $user->name }}</h1>
                        <p class="text-sm" style="color: var(--text-secondary);">{{ $user->email }}</p>
                        @if($user->phone)
                            <p class="text-sm" style="color: var(--text-secondary);">{{ $user->phone }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('users.edit', $user) }}"
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Edit User
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium mb-4" style="color: var(--text-primary);">Basic Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Full Name</dt>
                            <dd class="text-sm" style="color: var(--text-primary);">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Email Address</dt>
                            <dd class="text-sm" style="color: var(--text-primary);">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Phone Number</dt>
                            <dd class="text-sm" style="color: var(--text-primary);">{{ $user->phone ?? 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Status</dt>
                            <dd>
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Active
                                    </span>
                                @elseif($user->status === 'inactive')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        Inactive
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Suspended
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Last Login</dt>
                            <dd class="text-sm" style="color: var(--text-primary);">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Member Since</dt>
                            <dd class="text-sm" style="color: var(--text-primary);">{{ $user->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Roles and Permissions -->
                <div>
                    <h3 class="text-lg font-medium mb-4" style="color: var(--text-primary);">Roles & Permissions</h3>

                    <!-- Assigned Roles -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium mb-3" style="color: var(--text-secondary);">Assigned Roles</h4>
                        @if($user->roles->count() > 0)
                            <div class="space-y-2">
                                @foreach($user->roles as $role)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $role->name }}</span>
                                            @if($role->description)
                                                <p class="text-xs" style="color: var(--text-secondary);">{{ $role->description }}</p>
                                            @endif
                                        </div>
                                        @if($role->is_system)
                                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">System</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm" style="color: var(--text-secondary);">No roles assigned</p>
                        @endif
                    </div>

                    <!-- Permissions Summary -->
                    <div>
                        <h4 class="text-sm font-medium mb-3" style="color: var(--text-secondary);">Permissions Summary</h4>
                        @php
                            $permissions = $user->roles->flatMap->permissions->unique('id');
                            $permissionsByModule = $permissions->groupBy('module');
                        @endphp

                        @if($permissions->count() > 0)
                            <div class="space-y-3">
                                @foreach($permissionsByModule as $module => $modulePermissions)
                                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <h5 class="text-xs font-medium mb-2" style="color: var(--text-primary);">{{ $module }}</h5>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($modulePermissions as $permission)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ $permission->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm" style="color: var(--text-secondary);">No permissions assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Log (Placeholder) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <h3 class="text-lg font-medium" style="color: var(--text-primary);">Recent Activity</h3>
        </div>
        <div class="p-6">
            <div class="text-center py-8">
                <i class="fas fa-history text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-sm" style="color: var(--text-secondary);">No recent activity recorded</p>
                <p class="text-xs mt-1" style="color: var(--text-secondary);">User activity will be tracked here</p>
            </div>
        </div>
    </div>
</div>
@endsection
