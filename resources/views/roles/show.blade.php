@extends('layouts.app')

@section('title', 'Role Details')

@php
    $title = 'Role Details';
    $subtitle = 'View role information and permissions';
    $showBackButton = true;
    $backUrl = route('roles.index');
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Role Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <i class="fas fa-user-tag text-2xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">{{ $role->name }}</h1>
                        <p class="text-sm" style="color: var(--text-secondary);">{{ $role->description ?: 'No description provided' }}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            @if($role->is_system)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <i class="fas fa-cog mr-1"></i>
                                    System Role
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-user-plus mr-1"></i>
                                    Custom Role
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @if(!$role->is_system)
                        <form method="POST" action="{{ route('roles.clone', $role) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <i class="fas fa-copy mr-2"></i>Clone Role
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('roles.edit', $role) }}"
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Edit Role
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium mb-4" style="color: var(--text-primary);">Basic Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Role Name</dt>
                            <dd class="text-sm" style="color: var(--text-primary);">{{ $role->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Slug</dt>
                            <dd class="text-sm font-mono" style="color: var(--text-primary);">{{ $role->slug }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Type</dt>
                            <dd>
                                @if($role->is_system)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        System Role
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Custom Role
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Created</dt>
                            <dd class="text-sm" style="color: var(--text-primary);">{{ $role->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Last Updated</dt>
                            <dd class="text-sm" style="color: var(--text-primary);">{{ $role->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Statistics -->
                <div>
                    <h3 class="text-lg font-medium mb-4" style="color: var(--text-primary);">Statistics</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Total Permissions</dt>
                            <dd class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $role->permissions->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Users with this Role</dt>
                            <dd class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $role->users->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Permission Modules</dt>
                            <dd class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $role->permissions->groupBy('module')->count() }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h3 class="text-lg font-medium mb-4" style="color: var(--text-primary);">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('roles.edit', $role) }}"
                           class="block w-full px-4 py-2 text-sm font-medium text-center text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i>Edit Role
                        </a>
                        @if(!$role->is_system)
                            <form method="POST" action="{{ route('roles.clone', $role) }}" class="block">
                                @csrf
                                <button type="submit"
                                        class="w-full px-4 py-2 text-sm font-medium text-center text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                        style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                    <i class="fas fa-copy mr-2"></i>Clone Role
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('roles.index') }}"
                           class="block w-full px-4 py-2 text-sm font-medium text-center text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Roles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions by Module -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <h3 class="text-lg font-medium" style="color: var(--text-primary);">Assigned Permissions</h3>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Permissions organized by module</p>
        </div>
        <div class="p-6">
            @php
                $permissionsByModule = $role->permissions->groupBy('module');
            @endphp

            @if($permissionsByModule->count() > 0)
                <div class="space-y-6">
                    @foreach($permissionsByModule as $module => $modulePermissions)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4" style="border-color: var(--border-color);">
                            <h4 class="text-sm font-medium mb-3" style="color: var(--text-primary);">{{ $module }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($modulePermissions as $permission)
                                    <div class="flex items-center space-x-2 p-2 rounded permission-card">
                                        <i class="fas fa-key text-xs text-blue-600 dark:text-blue-400"></i>
                                        <span class="text-sm" style="color: var(--text-primary);">{{ $permission->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-key text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-sm" style="color: var(--text-secondary);">No permissions assigned to this role</p>
                    <p class="text-xs mt-1" style="color: var(--text-secondary);">Edit the role to assign permissions</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Users with this Role -->
    @if($role->users->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">Users with this Role</h3>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $role->users->count() }} user(s) have this role</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($role->users as $user)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($user->avatar)
                                    <img class="h-8 w-8 rounded-full" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <i class="fas fa-user text-xs text-gray-600 dark:text-gray-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium" style="color: var(--text-primary);">{{ $user->name }}</p>
                                <p class="text-xs" style="color: var(--text-secondary);">{{ $user->email }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.permission-card {
    background-color: var(--bg-primary);
}

.permission-card:hover {
    background-color: var(--hover-bg);
}
</style>
@endsection
