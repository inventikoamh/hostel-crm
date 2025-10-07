@extends('layouts.app')

@section('title', 'Bed Assignments Debug')

@php
    $title = 'Bed Assignments Debug';
    $subtitle = "Debug view for {$hostel->name} - All bed assignments and reservations";
    $showBackButton = true;
    $backUrl = route('hostels.index');
@endphp

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="text-2xl font-bold text-blue-600">{{ $allBeds->count() }}</div>
            <div class="text-sm" style="color: var(--text-secondary);">Total Beds</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="text-2xl font-bold text-green-600">{{ $tenantProfiles->count() }}</div>
            <div class="text-sm" style="color: var(--text-secondary);">Total Tenants</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="text-2xl font-bold text-purple-600">{{ $allBeds->where('status', 'available')->count() }}</div>
            <div class="text-sm" style="color: var(--text-secondary);">Available Beds</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="text-2xl font-bold text-orange-600">{{ $allBeds->where('status', 'reserved')->count() }}</div>
            <div class="text-sm" style="color: var(--text-secondary);">Reserved Beds</div>
        </div>
    </div>

    <!-- Bed Status Breakdown -->
    <div class="bg-white rounded-lg shadow p-6 mb-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Bed Status Breakdown</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($allBeds->groupBy('status') as $status => $beds)
                <div class="text-center">
                    <div class="text-2xl font-bold
                        {{ $status === 'available' ? 'text-green-600' :
                           ($status === 'occupied' ? 'text-blue-600' :
                           ($status === 'reserved' ? 'text-purple-600' : 'text-yellow-600')) }}">
                        {{ $beds->count() }}
                    </div>
                    <div class="text-sm capitalize" style="color: var(--text-secondary);">{{ $status }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- All Beds with Assignments -->
    <div class="bg-white rounded-lg shadow p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">All Beds with Assignments</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50" style="background-color: var(--bg-secondary);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Bed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Current Tenant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Reservation Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Rent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">All Assignments</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" style="background-color: var(--card-bg);">
                    @foreach($allBeds as $bed)
                        @php
                            $allTenantsForThisBed = $tenantProfiles->where('bed_id', $bed->id);
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: var(--text-primary);">
                                Bed {{ $bed->bed_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-primary);">
                                {{ $bed->room->room_number }} (Floor {{ $bed->room->floor }})
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $bed->status === 'available' ? 'bg-green-100 text-green-800' :
                                       ($bed->status === 'occupied' ? 'bg-blue-100 text-blue-800' :
                                       ($bed->status === 'reserved' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ ucfirst($bed->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-primary);">
                                @if($bed->tenant)
                                    <div>
                                        <div class="font-medium">{{ $bed->tenant->name }}</div>
                                        <div class="text-xs" style="color: var(--text-secondary);">{{ $bed->tenant->email }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400">No tenant</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-primary);">
                                @if($bed->occupied_from || $bed->occupied_until)
                                    <div>
                                        @if($bed->occupied_from)
                                            <div><strong>From:</strong> {{ $bed->occupied_from->format('M j, Y') }}</div>
                                        @endif
                                        @if($bed->occupied_until)
                                            <div><strong>Until:</strong> {{ $bed->occupied_until->format('M j, Y') }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">No dates</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-primary);">
                                @if($bed->monthly_rent)
                                    ₹{{ number_format($bed->monthly_rent, 2) }}
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: var(--text-primary);">
                                @if($allTenantsForThisBed->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($allTenantsForThisBed as $tenant)
                                            <div class="border-l-2 border-blue-400 pl-2">
                                                <div class="font-medium">{{ $tenant->user->name }}</div>
                                                <div class="text-xs" style="color: var(--text-secondary);">
                                                    @if($tenant->lease_start_date)
                                                        {{ $tenant->lease_start_date->format('M j, Y') }}
                                                    @endif
                                                    @if($tenant->lease_start_date && $tenant->lease_end_date)
                                                        -
                                                    @endif
                                                    @if($tenant->lease_end_date)
                                                        {{ $tenant->lease_end_date->format('M j, Y') }}
                                                    @endif
                                                </div>
                                                <div class="text-xs" style="color: var(--text-secondary);">
                                                    Status: {{ $tenant->status }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">No assignments</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Tenants -->
    <div class="bg-white rounded-lg shadow p-6 mt-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">All Tenants</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50" style="background-color: var(--bg-secondary);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Tenant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Assigned Bed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Lease Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Move-in Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Monthly Rent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" style="background-color: var(--card-bg);">
                    @foreach($tenantProfiles as $tenant)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenant->user->name }}</div>
                                    <div class="text-sm" style="color: var(--text-secondary);">{{ $tenant->user->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-primary);">
                                @if($tenant->bed)
                                    Bed {{ $tenant->bed->bed_number }} (Room {{ $tenant->bed->room->room_number }})
                                @else
                                    <span class="text-gray-400">No bed assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-primary);">
                                @if($tenant->lease_start_date || $tenant->lease_end_date)
                                    <div>
                                        @if($tenant->lease_start_date)
                                            <div><strong>Start:</strong> {{ $tenant->lease_start_date->format('M j, Y') }}</div>
                                        @endif
                                        @if($tenant->lease_end_date)
                                            <div><strong>End:</strong> {{ $tenant->lease_end_date->format('M j, Y') }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">No lease dates</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-primary);">
                                @if($tenant->move_in_date)
                                    {{ $tenant->move_in_date->format('M j, Y') }}
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-primary);">
                                @if($tenant->monthly_rent)
                                    ₹{{ number_format($tenant->monthly_rent, 2) }}
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' :
                                       ($tenant->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
