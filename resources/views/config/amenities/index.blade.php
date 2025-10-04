@extends('layouts.app')

@section('title', 'Amenities')

@php
    $title = 'Amenities';
    $subtitle = 'Manage hostel amenities and facilities';
    $showBackButton = false;
@endphp

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card title="Total Amenities" value="{{ count($amenities) }}" subtitle="All registered amenities" icon="fas fa-concierge-bell" color="blue"/>
        <x-stats-card title="Active Amenities" value="{{ collect($amenities)->where('status', 'active')->count() }}" subtitle="Currently available" icon="fas fa-check-circle" color="green"/>
        <x-stats-card title="Inactive Amenities" value="{{ collect($amenities)->where('status', 'inactive')->count() }}" subtitle="Not currently available" icon="fas fa-times-circle" color="red"/>
        <x-stats-card title="Highest Order" value="{{ collect($amenities)->max('sort_order') }}" subtitle="Max sort order" icon="fas fa-sort-numeric-up" color="purple"/>
    </div>

    <x-data-table
        title="All Amenities"
        add-button-text="Add Amenity"
        add-button-url="{{ route('config.amenities.create') }}"
        :columns="$columns"
        :data="$amenities"
        :actions="true"
        :searchable="true"
        :exportable="true"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$pagination"
    />
@endsection
