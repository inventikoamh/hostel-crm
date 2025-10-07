@extends('layouts.app')

@section('title', 'Hostels')

@php
    $title = 'Hostels';
    $subtitle = 'Manage all hostels in your system';
    $showBackButton = false;
@endphp

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <x-stats-card
        title="Total Hostels"
        value="{{ count($hostels) }}"
        subtitle="All registered hostels"
        icon="fas fa-building"
        color="blue"
    />
    <x-stats-card
        title="Active Hostels"
        value="{{ collect($hostels)->where('status', 'active')->count() }}"
        subtitle="Currently operational"
        icon="fas fa-check-circle"
        color="green"
    />
    <x-stats-card
        title="Total Rooms"
        value="{{ collect($hostels)->sum('total_rooms') }}"
        subtitle="Calculated from actual rooms"
        icon="fas fa-bed"
        color="purple"
    />
    <x-stats-card
        title="Total Beds"
        value="{{ collect($hostels)->sum('total_beds') }}"
        subtitle="Calculated from actual beds"
        icon="fas fa-users"
        color="orange"
    />
</div>

<!-- Data Table -->
<x-data-table
    title="All Hostels"
    add-button-text="Add Hostel"
    add-button-url="{{ route('hostels.create') }}"
    :columns="$columns"
    :data="$hostels"
    :actions="true"
    :searchable="true"
    :exportable="true"
    :filters="$filters"
    :bulk-actions="$bulkActions"
    :pagination="$pagination"
/>
@endsection
