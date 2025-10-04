@extends('layouts.app')

@section('title', 'Hostel Map')

@php
    $title = 'Hostel Map';
    $subtitle = 'Visual overview of all hostels and their occupancy';
    $showBackButton = false;
@endphp

@section('content')
<div class="space-y-6">
    @foreach($hostels as $hostel)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <!-- Hostel Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold" style="color: var(--text-primary);">{{ $hostel->name }}</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ $hostel->address }}, {{ $hostel->city }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $hostel->occupied_beds_count }}</p>
                        <p class="text-xs text-gray-500">Occupied</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $hostel->available_beds_count }}</p>
                        <p class="text-xs text-gray-500">Available</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600">{{ $hostel->actual_occupancy_rate }}%</p>
                        <p class="text-xs text-gray-500">Occupancy</p>
                    </div>
                    <a href="{{ route('map.hostel', $hostel->id) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-map"></i>
                        View Map
                    </a>
                </div>
            </div>

            <!-- Floor Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($hostel->floors as $floor)
                    @php
                        $floorRooms = $hostel->rooms->where('floor', $floor);
                        $totalBeds = $floorRooms->sum('capacity');
                        $occupiedBeds = $floorRooms->sum(function($room) {
                            return $room->beds->where('status', 'occupied')->count();
                        });
                        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
                    @endphp
                    <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium" style="color: var(--text-primary);">Floor {{ $floor }}</h4>
                            <span class="text-sm font-medium {{ $occupancyRate > 80 ? 'text-red-600' : ($occupancyRate > 50 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $occupancyRate }}%
                            </span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between text-sm">
                                <span style="color: var(--text-secondary);">Rooms:</span>
                                <span style="color: var(--text-primary);">{{ $floorRooms->count() }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span style="color: var(--text-secondary);">Beds:</span>
                                <span style="color: var(--text-primary);">{{ $occupiedBeds }}/{{ $totalBeds }}</span>
                            </div>
                        </div>
                        <!-- Occupancy Bar -->
                        <div class="mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $occupancyRate > 80 ? 'bg-red-500' : ($occupancyRate > 50 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                     style="width: {{ $occupancyRate }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Room Status Legend -->
            <div class="mt-4 flex items-center justify-center gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-500 rounded"></div>
                    <span style="color: var(--text-secondary);">Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-500 rounded"></div>
                    <span style="color: var(--text-secondary);">Occupied</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-yellow-500 rounded"></div>
                    <span style="color: var(--text-secondary);">Maintenance</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-purple-500 rounded"></div>
                    <span style="color: var(--text-secondary);">Reserved</span>
                </div>
            </div>
        </div>
    @endforeach

    @if($hostels->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-500 mb-2">No Hostels Found</h3>
            <p class="text-gray-400 mb-4">Create your first hostel to start managing rooms and beds.</p>
            <a href="{{ route('hostels.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                Add Hostel
            </a>
        </div>
    @endif
</div>
@endsection
