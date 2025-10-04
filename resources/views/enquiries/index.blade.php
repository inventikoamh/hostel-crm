@extends('layouts.app')

@section('title', 'Enquiries')

@php
    $title = 'Enquiries';
    $subtitle = 'Manage customer enquiries and support requests';
    $showBackButton = false;
@endphp

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <x-stats-card
        title="Total Enquiries"
        value="{{ count($enquiries) }}"
        subtitle="All received enquiries"
        icon="fas fa-envelope"
        color="blue"
    />
    <x-stats-card
        title="New Enquiries"
        value="{{ collect($enquiries)->where('status', 'new')->count() }}"
        subtitle="Awaiting response"
        icon="fas fa-star"
        color="yellow"
    />
    <x-stats-card
        title="In Progress"
        value="{{ collect($enquiries)->where('status', 'in_progress')->count() }}"
        subtitle="Being handled"
        icon="fas fa-clock"
        color="orange"
    />
    <x-stats-card
        title="Resolved"
        value="{{ collect($enquiries)->where('status', 'resolved')->count() }}"
        subtitle="Successfully resolved"
        icon="fas fa-check-circle"
        color="green"
    />
</div>

<!-- Data Table -->
<x-data-table
    title="All Enquiries"
    add-button-text="View Public Form"
    add-button-url="{{ route('enquiry.form') }}"
    :columns="$columns"
    :data="$enquiries"
    :actions="true"
    :searchable="true"
    :exportable="true"
    :filters="$filters"
    :bulk-actions="$bulkActions"
    :pagination="$pagination"
/>
@endsection
