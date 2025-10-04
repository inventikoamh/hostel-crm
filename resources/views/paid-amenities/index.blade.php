@extends('layouts.app')

@section('title', 'Paid Services')

@php
    $title = 'Paid Services';
    $subtitle = 'Manage available paid services and amenities';
    $showBackButton = false;
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-concierge-bell text-2xl text-blue-600"></i>
                <div>
                    <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Paid Services</h1>
                    <p class="text-sm" style="color: var(--text-secondary);">Manage available paid services and amenities</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('paid-amenities.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Add Service</span>
                <span class="sm:hidden">Add</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-list text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Services</p>
                    <p class="text-2xl font-bold" style="color: var(--text-primary);">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Active Services</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-calendar text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Monthly Services</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['monthly'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-clock text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Daily Services</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['daily'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Table -->
    @include('components.data-table', [
        'title' => 'Available Services',
        'data' => $amenities,
        'columns' => $columns,
        'filters' => $filters,
        'bulkActions' => $bulkActions,
        'pagination' => $pagination,
        'searchPlaceholder' => 'Search services...',
        'emptyState' => [
            'icon' => 'fas fa-concierge-bell',
            'title' => 'No Services Available',
            'description' => 'Create your first paid service to get started.',
            'action' => [
                'text' => 'Add First Service',
                'url' => route('paid-amenities.create'),
                'icon' => 'fas fa-plus'
            ]
        ]
    ])
</div>
@endsection
