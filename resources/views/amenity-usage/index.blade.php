@extends('layouts.app')

@section('title', 'Amenity Usage Records')

@php
    $title = 'Amenity Usage Records';
    $subtitle = 'Track daily usage of paid amenities by tenants';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <a href="{{ route('amenity-usage.attendance') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-calendar-check mr-2"></i>
            Mark Attendance
        </a>
        <a href="{{ route('amenity-usage.reports') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-chart-bar mr-2"></i>
            Reports
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Records"
            value="{{ $stats['total'] }}"
            subtitle="All usage records"
            icon="fas fa-list"
            color="blue"
        />
        <x-stats-card
            title="Today's Usage"
            value="{{ $stats['today'] }}"
            subtitle="Records for today"
            icon="fas fa-calendar-day"
            color="green"
        />
        <x-stats-card
            title="This Month"
            value="{{ $stats['this_month'] }}"
            subtitle="Monthly records"
            icon="fas fa-calendar-alt"
            color="yellow"
        />
        <x-stats-card
            title="Total Amount"
            value="₹{{ number_format($stats['total_amount'], 2) }}"
            subtitle="Total revenue"
            icon="fas fa-rupee-sign"
            color="purple"
        />
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Usage Records"
        add-button-text="Add Record"
        add-button-url="{{ route('amenity-usage.create') }}"
        :columns="$columns"
        :data="$data"
        :actions="true"
        :searchable="true"
        :exportable="false"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$usageRecords"
        search-placeholder="Search by tenant name or amenity..."
    />
@endsection
