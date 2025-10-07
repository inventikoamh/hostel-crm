@extends('layouts.app')

@section('title', 'Room ' . $room->room_number)

@php
    $title = 'Room ' . $room->room_number;
    $subtitle = $room->hostel->name . ' - Floor ' . $room->floor;
    $showBackButton = true;
    $backUrl = route('rooms.index');
@endphp

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Room Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-start justify-between mb-4">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Room Information</h3>
                <div class="flex items-center gap-2">
                    <x-status-badge :status="$room->status" />
                    @if($room->occupancy_rate > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: var(--bed-occupied-badge-bg); color: var(--bed-occupied-badge-text);">
                            {{ $room->occupancy_rate }}% Occupied
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Room Number</label>
                    <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $room->room_number }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Room Type</label>
                    <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $room->room_type_display }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Floor</label>
                    <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $room->floor }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Capacity</label>
                    <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $room->capacity }} beds</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Rent per Bed</label>
                    <p class="mt-1 text-sm font-medium text-green-600">₹{{ number_format($room->rent_per_bed, 2) }}</p>
                </div>
                @if($room->area_sqft)
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Area</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $room->area_sqft }} sq ft</p>
                    </div>
                @endif
            </div>

            <!-- Room Features -->
            <div class="mb-6">
                <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Features</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @if($room->has_attached_bathroom)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: var(--bed-available-badge-bg); color: var(--bed-available-badge-text);">
                            <i class="fas fa-bath mr-1"></i>
                            Attached Bathroom
                        </span>
                    @endif
                    @if($room->has_balcony)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: var(--bed-occupied-badge-bg); color: var(--bed-occupied-badge-text);">
                            <i class="fas fa-door-open mr-1"></i>
                            Balcony
                        </span>
                    @endif
                    @if($room->has_ac)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: var(--bed-reserved-badge-bg); color: var(--bed-reserved-badge-text);">
                            <i class="fas fa-snowflake mr-1"></i>
                            Air Conditioning
                        </span>
                    @endif
                    @if(!$room->has_attached_bathroom && !$room->has_balcony && !$room->has_ac)
                        <span class="text-sm" style="color: var(--text-secondary);">No special features</span>
                    @endif
                </div>
            </div>

            @if($room->description)
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Description</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg" style="background-color: var(--bg-secondary);">
                        <p class="text-sm" style="color: var(--text-primary);">{{ $room->description }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Bed Layout -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Bed Layout</h3>
                <div class="text-xs" style="color: var(--text-secondary);">
                    <i class="fas fa-info-circle mr-1"></i>
                    Click on beds to view details and change status
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($room->beds as $bed)
                    <div class="bed-card border-2 rounded-lg p-3 text-center cursor-pointer hover:shadow-md transition-all duration-200
                                {{ $bed->status === 'available' ? 'border-green-500' :
                                   ($bed->status === 'occupied' ? 'border-blue-500' :
                                   ($bed->status === 'maintenance' ? 'border-yellow-500' : 'border-purple-500')) }}"
                         style="background-color: {{ $bed->status === 'available' ? 'var(--bed-available-bg)' :
                                                   ($bed->status === 'occupied' ? 'var(--bed-occupied-bg)' :
                                                   ($bed->status === 'maintenance' ? 'var(--bed-maintenance-bg)' : 'var(--bed-reserved-bg)')) }};"
                         onclick="showBedDetails({{ $bed->id }})"
                         data-bed-id="{{ $bed->id }}"
                         data-tenant-id="{{ $bed->getCurrentTenant() ? $bed->getCurrentTenant()->id : '' }}"
                         data-tenant-name="{{ $bed->getCurrentTenant() ? $bed->getCurrentTenant()->name : '' }}"
                         data-bed-type="{{ $bed->bed_type_display }}"
                         data-occupied-from="{{ $bed->getCurrentAssignment() ? $bed->getCurrentAssignment()->assigned_from->format('M j, Y') : '' }}"
                         data-occupied-until="{{ $bed->getCurrentAssignment() ? ($bed->getCurrentAssignment()->assigned_until ? $bed->getCurrentAssignment()->assigned_until->format('M j, Y') : '') : '' }}"
                         data-monthly-rent="{{ $bed->monthly_rent ? '₹' . number_format($bed->monthly_rent, 2) : '' }}"
                         data-bed-status="{{ $bed->status }}"
                         data-assignments="{{ json_encode($bed->assignments->map(function($assignment) {
                             return [
                                 'id' => $assignment->id,
                                 'tenant_id' => $assignment->tenant->id,
                                 'tenant_name' => $assignment->tenant->name,
                                 'status' => $assignment->status,
                                 'assigned_from' => $assignment->assigned_from->format('M j, Y'),
                                 'assigned_until' => $assignment->assigned_until ? $assignment->assigned_until->format('M j, Y') : null,
                                 'monthly_rent' => $assignment->monthly_rent ? '₹' . number_format($assignment->monthly_rent, 2) : null
                             ];
                         })) }}">

                        <!-- Bed Icon -->
                        <div class="mb-2">
                            <i class="fas fa-bed text-2xl" style="color: {{ $bed->status === 'available' ? 'var(--bed-available-color)' :
                                                           ($bed->status === 'occupied' ? 'var(--bed-occupied-color)' :
                                                           ($bed->status === 'maintenance' ? 'var(--bed-maintenance-color)' : 'var(--bed-reserved-color)')) }};"></i>
                        </div>

                        <!-- Bed Number -->
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-semibold text-sm" style="color: var(--text-primary);">Bed {{ $bed->bed_number }}</h4>
                            @if(!$bed->getCurrentTenant())
                                <i class="fas fa-edit text-xs opacity-50" style="color: var(--text-secondary);" title="Click to change status"></i>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        <span class="text-xs px-2 py-1 rounded" style="background-color: {{ $bed->status === 'available' ? 'var(--bed-available-badge-bg)' :
                                                                 ($bed->status === 'occupied' ? 'var(--bed-occupied-badge-bg)' :
                                                                 ($bed->status === 'maintenance' ? 'var(--bed-maintenance-badge-bg)' : 'var(--bed-reserved-badge-bg)')) }}; color: {{ $bed->status === 'available' ? 'var(--bed-available-badge-text)' :
                                                                 ($bed->status === 'occupied' ? 'var(--bed-occupied-badge-text)' :
                                                                 ($bed->status === 'maintenance' ? 'var(--bed-maintenance-badge-text)' : 'var(--bed-reserved-badge-text)')) }};">
                            {{ ucfirst($bed->status) }}
                        </span>

                        <!-- Tenant Name (if occupied) -->
                        @if($bed->getCurrentTenant())
                            <p class="text-xs mt-1 truncate" style="color: var(--text-secondary);">{{ $bed->getCurrentTenant()->name }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Legend -->
            <div class="mt-6 flex items-center justify-center gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded" style="background-color: var(--bed-available-color);"></div>
                    <span style="color: var(--text-secondary);">Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded" style="background-color: var(--bed-occupied-color);"></div>
                    <span style="color: var(--text-secondary);">Occupied</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded" style="background-color: var(--bed-maintenance-color);"></div>
                    <span style="color: var(--text-secondary);">Maintenance</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded" style="background-color: var(--bed-reserved-color);"></div>
                    <span style="color: var(--text-secondary);">Reserved</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('rooms.edit', $room->id) }}"
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-edit"></i>
                    Edit Room
                </a>
                <a href="{{ route('map.hostel', $room->hostel->id) }}"
                   class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-map"></i>
                    View Hostel Map
                </a>
                <button onclick="deleteRoom('{{ route('rooms.destroy', $room->id) }}')"
                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-trash"></i>
                    Delete Room
                </button>
            </div>
        </div>

        <!-- Room Statistics -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Statistics</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm" style="color: var(--text-secondary);">Total Beds:</span>
                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $room->capacity }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm" style="color: var(--text-secondary);">Occupied:</span>
                    <span class="text-sm font-medium text-blue-600">{{ $room->occupied_beds_count }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm" style="color: var(--text-secondary);">Available:</span>
                    <span class="text-sm font-medium text-green-600">{{ $room->available_beds_count }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm" style="color: var(--text-secondary);">Reserved:</span>
                    <span class="text-sm font-medium text-purple-600">{{ $room->beds->where('status', 'reserved')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm" style="color: var(--text-secondary);">Occupancy Rate:</span>
                    <span class="text-sm font-medium text-purple-600">{{ $room->occupancy_rate }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm" style="color: var(--text-secondary);">Monthly Revenue:</span>
                    <span class="text-sm font-medium text-green-600">₹{{ number_format($room->occupied_beds_count * $room->rent_per_bed, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Room Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Room Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Hostel</label>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $room->hostel->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Created</label>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $room->created_at->format('M j, Y') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Last Updated</label>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $room->updated_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bed Details Modal -->
<div id="bedModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full" style="background-color: var(--card-bg);">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);" id="modalBedTitle">Bed Details</h3>
                    <button onclick="closeBedModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="modalBedContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Define the bed status update URL
const BED_STATUS_UPDATE_URL = '{{ route("map.bed.status", ":bedId") }}';

function showBedDetails(bedId) {
    // Show modal
    document.getElementById('bedModal').classList.remove('hidden');

    // Get bed data from the bed card
    const bedCard = document.querySelector(`[data-bed-id="${bedId}"]`);
    const bedNumber = bedCard.querySelector('h4').textContent;
    const bedStatus = bedCard.querySelector('span').textContent;
    const bedStatusValue = bedCard.dataset.bedStatus;
    const bedType = bedCard.dataset.bedType;
    const assignments = JSON.parse(bedCard.dataset.assignments || '[]');

    document.getElementById('modalBedTitle').textContent = bedNumber;

    let modalContent = `
        <div class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Status:</label>
                <p class="text-sm font-medium" style="color: var(--text-primary);">${bedStatus}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Bed Type:</label>
                <p class="text-sm" style="color: var(--text-primary);">${bedType}</p>
            </div>
    `;

    // Show all assignments (current and historical)
    if (assignments && assignments.length > 0) {
        modalContent += `
            <div class="mt-4 pt-4 border-t" style="border-color: var(--border-color);">
                <h4 class="text-sm font-medium mb-3" style="color: var(--text-primary);">Tenant Assignments</h4>
                <div class="space-y-3">
        `;

        assignments.forEach((assignment, index) => {
            const statusColor = assignment.status === 'active' ? 'text-green-600' :
                               assignment.status === 'reserved' ? 'text-purple-600' : 'text-gray-500';
            const statusIcon = assignment.status === 'active' ? 'fas fa-user-check' :
                              assignment.status === 'reserved' ? 'fas fa-user-clock' : 'fas fa-user-times';

            modalContent += `
                <div class="p-3 rounded-lg border" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <i class="${statusIcon} ${statusColor}"></i>
                            <span class="text-sm font-medium capitalize ${statusColor}">${assignment.status}</span>
                        </div>
                        <a href="/tenants/${assignment.tenant_id}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors duration-200 flex items-center gap-1">
                            <i class="fas fa-user"></i>
                            View
                        </a>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user text-xs" style="color: var(--text-secondary);"></i>
                            <a href="/tenants/${assignment.tenant_id}" class="text-sm font-medium hover:underline" style="color: var(--text-primary);">
                                ${assignment.tenant_name}
                            </a>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-xs" style="color: var(--text-secondary);"></i>
                            <span class="text-xs" style="color: var(--text-secondary);">
                                From: ${assignment.assigned_from}
                                ${assignment.assigned_until ? ` - Until: ${assignment.assigned_until}` : ''}
                            </span>
                        </div>
                        ${assignment.monthly_rent ? `
                        <div class="flex items-center gap-2">
                            <i class="fas fa-rupee-sign text-xs" style="color: var(--text-secondary);"></i>
                            <span class="text-xs font-medium text-green-600">${assignment.monthly_rent}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });

        modalContent += `
                </div>
            </div>
        `;
    } else {
        modalContent += `
            <div class="mt-4 pt-4 border-t" style="border-color: var(--border-color);">
                <div class="text-center py-4">
                    <i class="fas fa-user-slash text-2xl mb-2" style="color: var(--text-secondary);"></i>
                    <p class="text-sm" style="color: var(--text-secondary);">No tenant assignments</p>
                </div>
            </div>
        `;
    }

    // Add status change section if bed is not occupied
    if (bedStatusValue !== 'occupied') {
        modalContent += `
            <div class="mt-4 pt-4 border-t" style="border-color: var(--border-color);">
                <h4 class="text-sm font-medium mb-3" style="color: var(--text-primary);">Change Status</h4>
                <div class="flex gap-2 flex-wrap">
                    <button onclick="changeBedStatus(${bedId}, 'available')"
                            class="px-3 py-1 rounded text-xs transition-colors duration-200 ${bedStatus.toLowerCase() === 'available' ? 'opacity-50 cursor-not-allowed' : 'hover:opacity-80'}"
                            style="background-color: var(--bed-available-badge-bg); color: var(--bed-available-badge-text);"
                            ${bedStatus.toLowerCase() === 'available' ? 'disabled' : ''}>
                        <i class="fas fa-check mr-1"></i>Available
                    </button>
                    <button onclick="changeBedStatus(${bedId}, 'maintenance')"
                            class="px-3 py-1 rounded text-xs transition-colors duration-200 ${bedStatus.toLowerCase() === 'maintenance' ? 'opacity-50 cursor-not-allowed' : 'hover:opacity-80'}"
                            style="background-color: var(--bed-maintenance-badge-bg); color: var(--bed-maintenance-badge-text);"
                            ${bedStatus.toLowerCase() === 'maintenance' ? 'disabled' : ''}>
                        <i class="fas fa-tools mr-1"></i>Maintenance
                    </button>
                </div>
                <p class="text-xs mt-2" style="color: var(--text-secondary);">
                    <i class="fas fa-info-circle mr-1"></i>
                    Note: Occupied beds cannot be changed to available or maintenance status.
                </p>
            </div>
        `;
    }

    modalContent += `
            <div class="flex gap-2 mt-6 pt-4 border-t" style="border-color: var(--border-color);">
                <button onclick="closeBedModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    `;

    document.getElementById('modalBedContent').innerHTML = modalContent;
}

function closeBedModal() {
    document.getElementById('bedModal').classList.add('hidden');
}

function changeBedStatus(bedId, newStatus) {
    // Show confirmation dialog
    const statusText = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
    if (!confirm(`Are you sure you want to change this bed status to "${statusText}"?`)) {
        return;
    }

    // Show loading state
    const modalContent = document.getElementById('modalBedContent');
    const originalContent = modalContent.innerHTML;
    modalContent.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl mb-2" style="color: var(--text-secondary);"></i>
            <p style="color: var(--text-secondary);">Updating bed status...</p>
        </div>
    `;

    // Make AJAX request
    const url = BED_STATUS_UPDATE_URL.replace(':bedId', bedId);
    console.log('Updating bed status:', { bedId, newStatus, url });
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: newStatus,
            notes: newStatus === 'maintenance' ? 'Status changed manually' : null
        })
    })
    .then(response => {
        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response. Please check if you are logged in.');
        }

        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update the bed card in the grid
            updateBedCard(bedId, newStatus);

            // Show success message
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-2xl mb-2" style="color: var(--bed-available-color);"></i>
                    <p style="color: var(--text-primary);">Bed status updated successfully!</p>
                    <button onclick="closeBedModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                        Close
                    </button>
                </div>
            `;

            // Auto-close modal after 2 seconds
            setTimeout(() => {
                closeBedModal();
            }, 2000);
        } else {
            throw new Error(data.error || 'Failed to update bed status');
        }
    })
    .catch(error => {
        console.error('Error updating bed status:', error);

        let errorMessage = 'An unexpected error occurred';
        if (error.message.includes('HTTP error! status: 401')) {
            errorMessage = 'You are not logged in. Please refresh the page and try again.';
        } else if (error.message.includes('HTTP error! status: 403')) {
            errorMessage = 'You do not have permission to perform this action.';
        } else if (error.message.includes('HTTP error! status: 404')) {
            errorMessage = 'Bed not found. Please refresh the page and try again.';
        } else if (error.message.includes('HTTP error! status: 500')) {
            errorMessage = 'Server error. Please try again later.';
        } else if (error.message.includes('non-JSON response')) {
            errorMessage = 'Server error. Please check if you are logged in and try again.';
        } else {
            errorMessage = error.message;
        }

        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-2xl mb-2" style="color: #ef4444;"></i>
                <p style="color: var(--text-primary);">Error updating bed status</p>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">${errorMessage}</p>
                <div class="flex gap-2 mt-4 justify-center">
                    <button onclick="showBedDetails(${bedId})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                        Try Again
                    </button>
                    <button onclick="closeBedModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        `;
    });
}

function updateBedCard(bedId, newStatus) {
    const bedCard = document.querySelector(`[data-bed-id="${bedId}"]`);
    if (!bedCard) return;

    // Update border color
    const statusBorders = {
        'available': 'border-green-500',
        'occupied': 'border-blue-500',
        'maintenance': 'border-yellow-500',
        'reserved': 'border-purple-500'
    };

    // Update background color
    const statusBgColors = {
        'available': 'var(--bed-available-bg)',
        'occupied': 'var(--bed-occupied-bg)',
        'maintenance': 'var(--bed-maintenance-bg)',
        'reserved': 'var(--bed-reserved-bg)'
    };

    // Update icon color
    const statusIconColors = {
        'available': 'var(--bed-available-color)',
        'occupied': 'var(--bed-occupied-color)',
        'maintenance': 'var(--bed-maintenance-color)',
        'reserved': 'var(--bed-reserved-color)'
    };

    // Update badge colors
    const statusBadgeBgColors = {
        'available': 'var(--bed-available-badge-bg)',
        'occupied': 'var(--bed-occupied-badge-bg)',
        'maintenance': 'var(--bed-maintenance-badge-bg)',
        'reserved': 'var(--bed-reserved-badge-bg)'
    };

    const statusBadgeTextColors = {
        'available': 'var(--bed-available-badge-text)',
        'occupied': 'var(--bed-occupied-badge-text)',
        'maintenance': 'var(--bed-maintenance-badge-text)',
        'reserved': 'var(--bed-reserved-badge-text)'
    };

    // Remove old border classes and add new one
    bedCard.className = bedCard.className.replace(/border-(green|blue|yellow|purple)-500/g, '');
    bedCard.classList.add(statusBorders[newStatus]);

    // Update background color
    bedCard.style.backgroundColor = statusBgColors[newStatus];

    // Update icon color
    const icon = bedCard.querySelector('i');
    if (icon) {
        icon.style.color = statusIconColors[newStatus];
    }

    // Update status badge
    const statusBadge = bedCard.querySelector('span');
    if (statusBadge) {
        statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
        statusBadge.style.backgroundColor = statusBadgeBgColors[newStatus];
        statusBadge.style.color = statusBadgeTextColors[newStatus];
    }

    // Clear tenant name if status changed to available or maintenance
    if (newStatus === 'available' || newStatus === 'maintenance') {
        const tenantName = bedCard.querySelector('p');
        if (tenantName) {
            tenantName.remove();
        }
    }
}

function deleteRoom(url) {
    if (confirm('Are you sure you want to delete this room? This will also delete all associated beds. This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('bedModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBedModal();
    }
});
</script>
@endsection
