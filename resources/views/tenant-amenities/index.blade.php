@extends('layouts.app')

@section('title', 'Tenant Services')

@php
    $title = 'Tenant Services';
    $subtitle = 'Manage paid services assigned to tenants';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Assignments"
            value="{{ $stats['total'] }}"
            subtitle="All service assignments"
            icon="fas fa-users"
            color="blue"
        />
        <x-stats-card
            title="Active Services"
            value="{{ $stats['active'] }}"
            subtitle="Currently active"
            icon="fas fa-check-circle"
            color="green"
        />
        <x-stats-card
            title="Monthly Services"
            value="{{ $stats['monthly'] }}"
            subtitle="Monthly billing"
            icon="fas fa-calendar"
            color="yellow"
        />
        <x-stats-card
            title="Daily Services"
            value="{{ $stats['daily'] }}"
            subtitle="Daily billing"
            icon="fas fa-clock"
            color="purple"
        />
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Service Assignments"
        add-button-text="Assign Service"
        add-button-url="{{ route('tenant-amenities.create') }}"
        :columns="$columns"
        :data="$data"
        :actions="true"
        :searchable="true"
        :exportable="false"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$tenantAmenities"
        search-placeholder="Search by tenant name or service..."
    />
@endsection
