@extends('layouts.app')

@section('title', 'Tenant Profile Update Requests')

@php
    $title = 'Tenant Profile Update Requests';
    $subtitle = 'Review and manage tenant profile update requests';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Requests"
            value="{{ $stats[0]['value'] }}"
            subtitle="All profile requests"
            icon="fas fa-file-alt"
            color="blue"
        />
        <x-stats-card
            title="Pending"
            value="{{ $stats[1]['value'] }}"
            subtitle="Awaiting review"
            icon="fas fa-clock"
            color="yellow"
        />
        <x-stats-card
            title="Approved"
            value="{{ $stats[2]['value'] }}"
            subtitle="Successfully approved"
            icon="fas fa-check"
            color="green"
        />
        <x-stats-card
            title="Rejected"
            value="{{ $stats[3]['value'] }}"
            subtitle="Requests rejected"
            icon="fas fa-times"
            color="red"
        />
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Profile Update Requests"
        add-button-text=""
        add-button-url=""
        :columns="$columns"
        :data="$data"
        :actions="true"
        :searchable="true"
        :exportable="false"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$requests"
        search-placeholder="Search by tenant name or email..."
    />
@endsection
