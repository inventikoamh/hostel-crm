@extends('layouts.app')

@section('title', 'Permission Details')

@php
    $title = 'Permission Details';
    $subtitle = 'View permission information and usage';
    $showBackButton = true;
    $backUrl = route('permissions.index');
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Permission Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold" style="color: var(--text-primary);">{{ $permission->name }}</h2>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $permission->slug }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <x-permission-type-badge :is_system="$permission->is_system" />
                    <a href="{{ route('permissions.edit', $permission) }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                       style="background-color: var(--primary-bg); border-color: var(--primary-border); color: var(--primary-text);">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
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
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Permission Name</dt>
                            <dd class="text-sm mt-1" style="color: var(--text-primary);">{{ $permission->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Slug</dt>
                            <dd class="text-sm mt-1 font-mono" style="color: var(--text-primary);">{{ $permission->slug }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Module</dt>
                            <dd class="text-sm mt-1" style="color: var(--text-primary);">{{ $permission->module }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium" style="color: var(--text-secondary);">Type</dt>
                            <dd class="text-sm mt-1">
                                <x-permission-type-badge :is_system="$permission->is_system" />
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Description -->
                <div>
                    <h3 class="text-lg font-medium mb-4" style="color: var(--text-primary);">Description</h3>
                    <div class="text-sm" style="color: var(--text-primary);">
                        @if($permission->description)
                            {{ $permission->description }}
                        @else
                            <span class="italic" style="color: var(--text-secondary);">No description provided</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Using This Permission -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <h3 class="text-lg font-medium" style="color: var(--text-primary);">Roles Using This Permission</h3>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Roles that have been assigned this permission</p>
        </div>

        <div class="p-6">
            @if($permission->roles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permission->roles as $role)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200" style="border-color: var(--border-color);">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium" style="color: var(--text-primary);">{{ $role->name }}</h4>
                                    <p class="text-xs mt-1" style="color: var(--text-secondary);">{{ $role->users->count() }} users</p>
                                </div>
                                <a href="{{ route('roles.show', $role) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm" style="color: var(--primary-text);">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-3xl mb-3"></i>
                    <p class="text-sm" style="color: var(--text-secondary);">No roles are using this permission</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Permission Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center" style="background-color: var(--primary-bg);">
                        <i class="fas fa-users text-blue-600" style="color: var(--primary-text);"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Roles</p>
                    <p class="text-2xl font-semibold" style="color: var(--text-primary);">{{ $permission->roles->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center" style="background-color: var(--success-bg);">
                        <i class="fas fa-user text-green-600" style="color: var(--success-text);"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Users</p>
                    <p class="text-2xl font-semibold" style="color: var(--text-primary);">{{ $permission->roles->sum('users_count') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center" style="background-color: var(--info-bg);">
                        <i class="fas fa-layer-group text-purple-600" style="color: var(--info-text);"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Module</p>
                    <p class="text-lg font-semibold" style="color: var(--text-primary);">{{ $permission->module }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
