@extends('layouts.app')

@section('title', 'Tenants')

@php
    $title = 'Tenants';
    $subtitle = 'Manage all your hostel tenants and their information.';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
        <x-stats-card title="Total Tenants" value="{{ $stats['total'] }}" subtitle="All registered tenants" icon="fas fa-users" color="blue"/>
        <x-stats-card title="Active Tenants" value="{{ $stats['active'] }}" subtitle="Currently residing" icon="fas fa-user-check" color="green"/>
        <x-stats-card title="Pending Tenants" value="{{ $stats['pending'] }}" subtitle="Awaiting approval" icon="fas fa-user-clock" color="yellow"/>
        <x-stats-card title="Verified Tenants" value="{{ $stats['verified'] }}" subtitle="Document verified" icon="fas fa-user-shield" color="purple"/>
        <x-stats-card title="Total Monthly Rent" value="₹{{ number_format($stats['total_rent'], 2) }}" subtitle="Combined rent collection" icon="fas fa-rupee-sign" color="indigo"/>
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Tenants"
        add-button-text="Add Tenant"
        add-button-url="{{ route('tenants.create') }}"
        :columns="$columns"
        :data="$data"
        :actions="true"
        :searchable="true"
        :exportable="true"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$tenants"
    />
@endsection
