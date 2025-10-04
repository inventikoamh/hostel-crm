@extends('layouts.app')

@section('title', 'Rooms')

@php
    $title = 'Rooms';
    $subtitle = 'Manage rooms and bed allocations';
    $showBackButton = false;
@endphp

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <x-stats-card
        title="Total Rooms"
        value="{{ count($rooms) }}"
        subtitle="All registered rooms"
        icon="fas fa-door-open"
        color="blue"
    />
    <x-stats-card
        title="Available Rooms"
        value="{{ collect($rooms)->where('status', 'available')->count() }}"
        subtitle="Ready for occupancy"
        icon="fas fa-check-circle"
        color="green"
    />
    <x-stats-card
        title="Occupied Rooms"
        value="{{ collect($rooms)->where('status', 'occupied')->count() }}"
        subtitle="Currently occupied"
        icon="fas fa-users"
        color="orange"
    />
    <x-stats-card
        title="Total Beds"
        value="{{ collect($rooms)->sum('capacity') }}"
        subtitle="Across all rooms"
        icon="fas fa-bed"
        color="purple"
    />
</div>

<!-- Data Table -->
<x-data-table
    title="All Rooms"
    add-button-text="Add Room"
    add-button-url="{{ route('rooms.create') }}"
    :columns="$columns"
    :data="$rooms"
    :actions="true"
    :searchable="true"
    :exportable="true"
    :filters="$filters"
    :bulk-actions="$bulkActions"
    :pagination="$pagination"
/>
@endsection
