@extends('layouts.app')

@section('title', 'Tenant Documents')

@php
    $title = 'Tenant Documents';
    $subtitle = 'Manage tenant document requests and approvals';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Documents"
            value="{{ $stats[0]['value'] }}"
            subtitle="All document requests"
            icon="fas fa-file-alt"
            color="blue"
        />
        <x-stats-card
            title="Pending Approval"
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
            title="Required"
            value="{{ $stats[3]['value'] }}"
            subtitle="Mandatory documents"
            icon="fas fa-exclamation"
            color="red"
        />
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Documents"
        add-button-text="Request Document"
        add-button-url="{{ route('admin.tenant-documents.create') }}"
        :columns="$columns"
        :data="$data"
        :actions="true"
        :searchable="true"
        :exportable="true"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$documents"
        search-placeholder="Search documents, tenants..."
    />
@endsection
