@extends('layouts.app')

@section('title', 'Tenant Forms')

@php
    $title = 'Tenant Forms';
    $subtitle = 'Manage tenant forms, print and upload signed documents';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Forms"
            value="{{ $stats[0]['value'] }}"
            subtitle="All tenant forms"
            icon="fas fa-file-alt"
            color="blue"
        />
        <x-stats-card
            title="Draft"
            value="{{ $stats[1]['value'] }}"
            subtitle="Forms in draft"
            icon="fas fa-edit"
            color="gray"
        />
        <x-stats-card
            title="Printed"
            value="{{ $stats[2]['value'] }}"
            subtitle="Forms printed"
            icon="fas fa-print"
            color="yellow"
        />
        <x-stats-card
            title="Signed"
            value="{{ $stats[3]['value'] }}"
            subtitle="Forms signed"
            icon="fas fa-signature"
            color="green"
        />
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Tenant Forms"
        add-button-text="Create Form"
        add-button-url="{{ route('admin.tenant-forms.create') }}"
        :columns="$columns"
        :data="$data"
        :actions="true"
        :searchable="true"
        :exportable="false"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$forms"
        search-placeholder="Search by form number or tenant name..."
    />
@endsection
