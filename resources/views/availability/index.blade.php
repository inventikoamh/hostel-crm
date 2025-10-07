@extends('layouts.app')

@section('title', 'Room & Bed Availability')

@php
    $title = 'Room & Bed Availability';
    $subtitle = 'Check availability of rooms and beds based on lease dates';
    $showBackButton = true;
    $backUrl = route('dashboard');
@endphp

@section('content')
<div class="space-y-6">
    <!-- Search Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Search Availability</h3>

        <form id="availabilityForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="hostel_id" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Hostel *</label>
                    <select id="hostel_id" name="hostel_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select a hostel</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="lease_start_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Lease Start Date *</label>
                    <input type="date" id="lease_start_date" name="lease_start_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                </div>

                <div>
                    <label for="lease_end_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Lease End Date</label>
                    <input type="date" id="lease_end_date" name="lease_end_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" id="searchBtn"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-search"></i>
                    Check Availability
                </button>
            </div>
        </form>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-sm" style="color: var(--text-secondary);">Checking availability...</span>
        </div>
    </div>

    <!-- Results -->
    <div id="resultsContainer" class="hidden space-y-6">
        <!-- Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Availability Summary</h3>
            <div id="summaryContent"></div>
        </div>

        <!-- Rooms -->
        <div id="roomsContainer"></div>
    </div>

    <!-- Error State -->
    <div id="errorState" class="hidden bg-red-50 border border-red-200 rounded-xl p-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
            <div>
                <h3 class="text-sm font-medium text-red-800">Error</h3>
                <p id="errorMessage" class="text-sm text-red-700 mt-1"></p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('availabilityForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const searchBtn = document.getElementById('searchBtn');
    const loadingState = document.getElementById('loadingState');
    const resultsContainer = document.getElementById('resultsContainer');
    const errorState = document.getElementById('errorState');

    // Show loading state
    loadingState.classList.remove('hidden');
    resultsContainer.classList.add('hidden');
    errorState.classList.add('hidden');
    searchBtn.disabled = true;
    searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';

    // Make API request
    fetch('{{ route("availability.check") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingState.classList.add('hidden');

        if (data.error) {
            showError(data.error);
        } else {
            displayResults(data);
        }
    })
    .catch(error => {
        loadingState.classList.add('hidden');
        showError('An error occurred while checking availability. Please try again.');
        console.error('Error:', error);
    })
    .finally(() => {
        searchBtn.disabled = false;
        searchBtn.innerHTML = '<i class="fas fa-search"></i> Check Availability';
    });
});

function displayResults(data) {
    const resultsContainer = document.getElementById('resultsContainer');
    const summaryContent = document.getElementById('summaryContent');
    const roomsContainer = document.getElementById('roomsContainer');

    // Display summary
    summaryContent.innerHTML = `
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="text-center p-4 rounded-lg" style="background-color: var(--bg-secondary);">
                <div class="text-2xl font-bold text-blue-600">${data.summary.total_rooms}</div>
                <div class="text-sm" style="color: var(--text-secondary);">Total Rooms</div>
            </div>
            <div class="text-center p-4 rounded-lg" style="background-color: var(--bg-secondary);">
                <div class="text-2xl font-bold text-green-600">${data.summary.available_beds}</div>
                <div class="text-sm" style="color: var(--text-secondary);">Available Beds</div>
            </div>
            <div class="text-center p-4 rounded-lg" style="background-color: var(--bg-secondary);">
                <div class="text-2xl font-bold text-red-600">${data.summary.occupied_beds}</div>
                <div class="text-sm" style="color: var(--text-secondary);">Occupied Beds</div>
            </div>
            <div class="text-center p-4 rounded-lg" style="background-color: var(--bg-secondary);">
                <div class="text-2xl font-bold text-purple-600">${data.summary.reserved_beds}</div>
                <div class="text-sm" style="color: var(--text-secondary);">Reserved Beds</div>
            </div>
            <div class="text-center p-4 rounded-lg" style="background-color: var(--bg-secondary);">
                <div class="text-2xl font-bold text-yellow-600">${data.summary.maintenance_beds}</div>
                <div class="text-sm" style="color: var(--text-secondary);">Maintenance</div>
            </div>
        </div>
        <div class="mt-4 p-4 rounded-lg" style="background-color: var(--bg-secondary);">
            <h4 class="font-medium mb-2" style="color: var(--text-primary);">Search Criteria</h4>
            <p class="text-sm" style="color: var(--text-secondary);">
                <strong>Hostel:</strong> ${data.hostel.name}<br>
                <strong>Lease Period:</strong> ${data.search_criteria.lease_start_date} to ${data.search_criteria.lease_end_date}
            </p>
        </div>
    `;

    // Display rooms
    roomsContainer.innerHTML = data.rooms.map(room => `
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">${room.room_name || room.room_number}</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">Floor ${room.floor} • ${room.room_type}</p>
                </div>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">${room.available_beds} Available</span>
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">${room.occupied_beds} Occupied</span>
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">${room.reserved_beds} Reserved</span>
                    ${room.maintenance_beds > 0 ? `<span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">${room.maintenance_beds} Maintenance</span>` : ''}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                ${room.beds.map(bed => `
                    <div class="p-4 rounded-lg border ${getBedCardClass(bed.availability)}" style="border-color: var(--border-color);">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium" style="color: var(--text-primary);">Bed ${bed.bed_number}</h4>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${getBedStatusClass(bed.availability)}">${bed.availability}</span>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm" style="color: var(--text-secondary);">${bed.bed_type}</p>
                            <p class="text-sm font-medium text-green-600">₹${bed.monthly_rent}</p>
                            <p class="text-xs" style="color: var(--text-secondary);">${bed.availability_reason}</p>
                        </div>
                        ${bed.current_assignments.length > 0 ? `
                            <div class="mt-3 pt-3 border-t" style="border-color: var(--border-color);">
                                <p class="text-xs font-medium mb-2" style="color: var(--text-secondary);">Current Assignments:</p>
                                ${bed.current_assignments.map(assignment => `
                                    <div class="text-xs" style="color: var(--text-secondary);">
                                        <div class="font-medium">${assignment.tenant_name}</div>
                                        <div>${assignment.assigned_from} - ${assignment.assigned_until}</div>
                                        <div class="text-green-600">₹${assignment.monthly_rent}</div>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                `).join('')}
            </div>
        </div>
    `).join('');

    resultsContainer.classList.remove('hidden');
}

function getBedCardClass(availability) {
    switch(availability) {
        case 'available': return 'bg-green-50';
        case 'occupied': return 'bg-red-50';
        case 'reserved': return 'bg-purple-50';
        case 'maintenance': return 'bg-yellow-50';
        default: return 'bg-gray-50';
    }
}

function getBedStatusClass(availability) {
    switch(availability) {
        case 'available': return 'bg-green-100 text-green-800';
        case 'occupied': return 'bg-red-100 text-red-800';
        case 'reserved': return 'bg-purple-100 text-purple-800';
        case 'maintenance': return 'bg-yellow-100 text-yellow-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function showError(message) {
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    errorMessage.textContent = message;
    errorState.classList.remove('hidden');
}
</script>
@endsection
