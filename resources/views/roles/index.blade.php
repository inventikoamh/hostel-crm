@extends('layouts.app')

@section('title', 'Role Management')

@php
    $title = 'Role Management';
    $subtitle = 'Manage system roles and permissions';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Roles"
            value="{{ $roles->total() }}"
            subtitle="All system roles"
            icon="fas fa-user-tag"
            color="blue"
        />
        <x-stats-card
            title="System Roles"
            value="{{ $roles->where('is_system', true)->count() }}"
            subtitle="Built-in roles"
            icon="fas fa-cog"
            color="green"
        />
        <x-stats-card
            title="Custom Roles"
            value="{{ $roles->where('is_system', false)->count() }}"
            subtitle="User-created roles"
            icon="fas fa-user-plus"
            color="purple"
        />
        <x-stats-card
            title="Total Permissions"
            value="{{ $permissions->flatten()->count() }}"
            subtitle="Available permissions"
            icon="fas fa-key"
            color="orange"
        />
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Roles"
        add-button-text="Add Role"
        add-button-url="{{ route('roles.create') }}"
        :columns="$columns"
        :data="$data"
        :actions="true"
        :searchable="true"
        :exportable="true"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$pagination"
    />
@endsection
