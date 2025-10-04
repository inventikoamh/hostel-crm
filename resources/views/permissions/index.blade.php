@extends('layouts.app')

@section('title', 'Permission Management')

@php
    $title = 'Permission Management';
    $subtitle = 'Manage system permissions and access controls';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Permissions"
            value="{{ $permissions->total() }}"
            subtitle="All system permissions"
            icon="fas fa-key"
            color="blue"
        />
        <x-stats-card
            title="System Permissions"
            value="{{ $permissions->where('is_system', true)->count() }}"
            subtitle="Built-in permissions"
            icon="fas fa-cog"
            color="green"
        />
        <x-stats-card
            title="Custom Permissions"
            value="{{ $permissions->where('is_system', false)->count() }}"
            subtitle="User-created permissions"
            icon="fas fa-plus-circle"
            color="purple"
        />
        <x-stats-card
            title="Permission Modules"
            value="{{ count($modules) }}"
            subtitle="Available modules"
            icon="fas fa-layer-group"
            color="orange"
        />
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Permissions"
        add-button-text="Add Permission"
        add-button-url="{{ route('permissions.create') }}"
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
