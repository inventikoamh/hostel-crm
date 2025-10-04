@extends('layouts.app')

@section('title', 'User Management')

@php
    $title = 'User Management';
    $subtitle = 'Manage system users, roles, and permissions';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Users"
            value="{{ $users->total() }}"
            subtitle="All registered users"
            icon="fas fa-users"
            color="blue"
        />
        <x-stats-card
            title="Active Users"
            value="{{ $users->where('status', 'active')->count() }}"
            subtitle="Currently active"
            icon="fas fa-user-check"
            color="green"
        />
        <x-stats-card
            title="Inactive Users"
            value="{{ $users->where('status', 'inactive')->count() }}"
            subtitle="Currently inactive"
            icon="fas fa-user-times"
            color="gray"
        />
        <x-stats-card
            title="Suspended Users"
            value="{{ $users->where('status', 'suspended')->count() }}"
            subtitle="Currently suspended"
            icon="fas fa-user-slash"
            color="red"
        />
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Users"
        add-button-text="Add User"
        add-button-url="{{ route('users.create') }}"
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
