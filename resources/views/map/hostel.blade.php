@extends('layouts.app')

@section('title', $hostel->name . ' - Floor Map')

@php
    $title = $hostel->name;
    $subtitle = 'Floor ' . $selectedFloor . ' - Room and bed layout';
    $showBackButton = true;
    $backUrl = route('map.index');
@endphp

@section('content')
<div class="space-y-6">
    <!-- Floor Navigation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Select Floor</h3>
                <div class="flex gap-2">
                    @foreach($floors as $floor)
                        <a href="{{ route('map.hostel', ['hostel' => $hostel->id, 'floor' => $floor]) }}"
                           class="px-4 py-2 rounded-lg transition-colors duration-200 {{ $floor == $selectedFloor ? 'bg-blue-600 text-white' : '' }}"
                           style="{{ $floor != $selectedFloor ? 'background-color: var(--bg-secondary); color: var(--text-primary);' : '' }}"
                           onmouseover="{{ $floor != $selectedFloor ? 'this.style.backgroundColor = \'var(--hover-bg)\'' : '' }}"
                           onmouseout="{{ $floor != $selectedFloor ? 'this.style.backgroundColor = \'var(--bg-secondary)\'' : '' }}">
                            Floor {{ $floor }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Floor Stats -->
            <div class="flex items-center gap-6">
                @php
                    $totalBeds = $roomsByFloor->sum('capacity');
                    $occupiedBeds = $roomsByFloor->sum(function($room) {
                        return $room->beds->where('status', 'occupied')->count();
                    });
                    $availableBeds = $roomsByFloor->sum(function($room) {
                        return $room->beds->where('status', 'available')->count();
                    });
                    $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
                @endphp
                <div class="text-center">
                    <p class="text-xl font-bold" style="color: var(--bed-occupied-color);">{{ $occupiedBeds }}</p>
                    <p class="text-xs" style="color: var(--text-secondary);">Occupied</p>
                </div>
                <div class="text-center">
                    <p class="text-xl font-bold" style="color: var(--bed-available-color);">{{ $availableBeds }}</p>
                    <p class="text-xs" style="color: var(--text-secondary);">Available</p>
                </div>
                <div class="text-center">
                    <p class="text-xl font-bold" style="color: var(--bed-reserved-color);">{{ $occupancyRate }}%</p>
                    <p class="text-xs" style="color: var(--text-secondary);">Occupancy</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Floor Map -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Floor {{ $selectedFloor }} Layout</h3>

            <!-- Legend -->
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded border" style="background-color: var(--bed-available-color);"></div>
                    <span style="color: var(--text-secondary);">Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded border" style="background-color: var(--bed-occupied-color);"></div>
                    <span style="color: var(--text-secondary);">Occupied</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded border" style="background-color: var(--bed-maintenance-color);"></div>
                    <span style="color: var(--text-secondary);">Maintenance</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded border" style="background-color: var(--bed-reserved-color);"></div>
                    <span style="color: var(--text-secondary);">Reserved</span>
                </div>
            </div>
        </div>

        <!-- Room Grid -->
        <div class="relative bg-gray-50 rounded-lg p-8 min-h-96" style="background-color: var(--bg-secondary);">
            @if($roomsByFloor->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @foreach($floorLayout as $roomLayout)
                        @php
                            $room = $roomLayout['room'];
                            $occupancyRate = $room->occupancy_rate;
                            $statusBorder = match($room->status) {
                                'available' => 'border-green-500',
                                'occupied' => 'border-blue-500',
                                'maintenance' => 'border-yellow-500',
                                'reserved' => 'border-purple-500',
                                default => 'border-gray-300'
                            };
                            $statusBg = match($room->status) {
                                'available' => 'var(--bed-available-bg)',
                                'occupied' => 'var(--bed-occupied-bg)',
                                'maintenance' => 'var(--bed-maintenance-bg)',
                                'reserved' => 'var(--bed-reserved-bg)',
                                default => 'var(--bg-secondary)'
                            };
                        @endphp
                        <div class="room-card border-2 rounded-lg p-3 cursor-pointer hover:shadow-md transition-all duration-200 {{ $statusBorder }}"
                             style="background-color: {{ $statusBg }};"
                             onclick="showRoomDetails({{ $room->id }})"
                             data-room-id="{{ $room->id }}">
                            <!-- Room Header -->
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-sm" style="color: var(--text-primary);">{{ $room->room_number }}</h4>
                                <span class="text-xs px-2 py-1 rounded" style="background-color: {{ $room->status === 'available' ? 'var(--bed-available-badge-bg)' : ($room->status === 'occupied' ? 'var(--bed-occupied-badge-bg)' : ($room->status === 'maintenance' ? 'var(--bed-maintenance-badge-bg)' : 'var(--bed-reserved-badge-bg)')) }}; color: {{ $room->status === 'available' ? 'var(--bed-available-badge-text)' : ($room->status === 'occupied' ? 'var(--bed-occupied-badge-text)' : ($room->status === 'maintenance' ? 'var(--bed-maintenance-badge-text)' : 'var(--bed-reserved-badge-text)')) }};">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </div>

                            <!-- Room Type -->
                            <p class="text-xs mb-2" style="color: var(--text-secondary);">{{ $room->room_type_display }}</p>

                            <!-- Bed Status -->
                            <div class="flex items-center justify-between text-xs mb-2" style="color: var(--text-primary);">
                                <span>Beds: {{ $room->occupied_beds_count }}/{{ $room->capacity }}</span>
                                <span class="font-medium">{{ $occupancyRate }}%</span>
                            </div>

                            <!-- Bed Visual -->
                            <div class="grid grid-cols-4 gap-1 mb-2">
                                @foreach($room->beds as $bed)
                                    <div class="w-3 h-3 rounded-sm" style="background-color: {{ $bed->status === 'available' ? 'var(--bed-available-color)' : ($bed->status === 'occupied' ? 'var(--bed-occupied-color)' : ($bed->status === 'maintenance' ? 'var(--bed-maintenance-color)' : 'var(--bed-reserved-color)')) }};"
                                         title="Bed {{ $bed->bed_number }} - {{ ucfirst($bed->status) }}{{ $bed->tenant ? ' (' . $bed->tenant->name . ')' : '' }}">
                                    </div>
                                @endforeach
                            </div>

                            <!-- Rent -->
                            <p class="text-xs" style="color: var(--text-secondary);">₹{{ number_format($room->rent_per_bed, 0) }}/bed</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-door-open text-4xl mb-4" style="color: var(--text-secondary);"></i>
                    <h3 class="text-lg font-medium mb-2" style="color: var(--text-primary);">No Rooms on This Floor</h3>
                    <p style="color: var(--text-secondary);">Add rooms to floor {{ $selectedFloor }} to see them here.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Room Details Modal -->
<div id="roomModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" style="background-color: var(--card-bg);">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);" id="modalRoomTitle">Room Details</h3>
                    <button onclick="closeRoomModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="modalRoomContent">
                    <div class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                        <p class="text-gray-500">Loading room details...</p>
                    </div>
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
function showRoomDetails(roomId) {
    // Show modal with loading state
    document.getElementById('roomModal').classList.remove('hidden');
    document.getElementById('modalRoomContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
            <p class="text-gray-500">Loading room details...</p>
        </div>
    `;

    // Load room details via AJAX
    fetch(`/map/room-details/${roomId}`)
        .then(response => response.json())
        .then(data => {
            const room = data.room;
            const beds = data.beds;
            const bedLayout = data.bedLayout;

            document.getElementById('modalRoomTitle').textContent = `Room ${room.room_number}`;

            let modalContent = `
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Room Information -->
                    <div class="lg:col-span-1 space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                            <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Room Information</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Type:</span>
                                    <span style="color: var(--text-primary);">${room.room_type}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Floor:</span>
                                    <span style="color: var(--text-primary);">${room.floor}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Capacity:</span>
                                    <span style="color: var(--text-primary);">${room.capacity} beds</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Rent/Bed:</span>
                                    <span class="font-medium" style="color: var(--bed-available-color);">₹${room.rent_per_bed.toLocaleString()}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Status:</span>
                                    <span class="px-2 py-1 rounded text-xs" style="background-color: ${getStatusBadgeClass(room.status).split(' ')[0]}; color: ${getStatusBadgeClass(room.status).split(' ')[1]};">${room.status.charAt(0).toUpperCase() + room.status.slice(1)}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                            <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Features</h4>
                            <div class="space-y-2">
                                ${room.has_ac ? '<div class="flex items-center gap-2 text-sm"><i class="fas fa-snowflake" style="color: var(--bed-reserved-color);"></i><span style="color: var(--text-primary);">Air Conditioning</span></div>' : ''}
                                ${room.has_attached_bathroom ? '<div class="flex items-center gap-2 text-sm"><i class="fas fa-bath" style="color: var(--bed-occupied-color);"></i><span style="color: var(--text-primary);">Attached Bathroom</span></div>' : ''}
                                ${room.has_balcony ? '<div class="flex items-center gap-2 text-sm"><i class="fas fa-door-open" style="color: var(--bed-available-color);"></i><span style="color: var(--text-primary);">Balcony</span></div>' : ''}
                                ${!room.has_ac && !room.has_attached_bathroom && !room.has_balcony ? '<span class="text-sm" style="color: var(--text-secondary);">No special features</span>' : ''}
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                            <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Statistics</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Occupied:</span>
                                    <span class="font-medium" style="color: var(--bed-occupied-color);">${room.occupied_beds_count}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Available:</span>
                                    <span class="font-medium" style="color: var(--bed-available-color);">${room.available_beds_count}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Occupancy:</span>
                                    <span class="font-medium" style="color: var(--bed-reserved-color);">${room.occupancy_rate}%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Revenue:</span>
                                    <span class="font-medium" style="color: var(--bed-available-color);">₹${(room.occupied_beds_count * room.rent_per_bed).toLocaleString()}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-2">
                            <a href="/rooms/${room.id}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-eye"></i>
                                View Full Details
                            </a>
                            <a href="/rooms/${room.id}/edit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-edit"></i>
                                Edit Room
                            </a>
                        </div>
                    </div>

                    <!-- Bed Layout -->
                    <div class="lg:col-span-2">
                        <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Bed Layout</h4>
                        <div class="relative bg-gray-100 rounded-lg p-6 min-h-64" style="background-color: var(--bg-secondary);">
                            <!-- Room outline -->
                            <div class="absolute inset-4 border-2 border-dashed border-gray-300 rounded-lg">
                                <div class="absolute -top-6 left-4 bg-white px-2 py-1 rounded text-sm font-medium" style="background-color: var(--card-bg); color: var(--text-primary);">
                                    Room ${room.room_number}
                                </div>
                            </div>

                            <!-- Beds -->
                            <div class="relative h-full">
            `;

            // Add beds to the layout
            bedLayout.forEach(bedData => {
                const bed = beds.find(b => b.id === bedData.bed.id);
                const statusBorders = {
                    'available': 'border-green-500',
                    'occupied': 'border-blue-500',
                    'maintenance': 'border-yellow-500',
                    'reserved': 'border-purple-500'
                };
                const statusBgColors = {
                    'available': 'var(--bed-available-bg)',
                    'occupied': 'var(--bed-occupied-bg)',
                    'maintenance': 'var(--bed-maintenance-bg)',
                    'reserved': 'var(--bed-reserved-bg)'
                };
                const statusTextColors = {
                    'available': 'var(--bed-available-badge-text)',
                    'occupied': 'var(--bed-occupied-badge-text)',
                    'maintenance': 'var(--bed-maintenance-badge-text)',
                    'reserved': 'var(--bed-reserved-badge-text)'
                };

                // Get current active assignment
                const currentAssignment = bed.assignments && bed.assignments.length > 0
                    ? bed.assignments.find(assignment => assignment.status === 'active')
                    : null;
                const currentTenant = currentAssignment ? currentAssignment.tenant : null;

                modalContent += `
                    <div class="absolute border-2 rounded-lg p-2 text-center cursor-pointer hover:shadow-lg transition-all duration-200 ${statusBorders[bed.status] || 'border-gray-500'}"
                         style="left: ${bedData.x}px; top: ${bedData.y}px; width: ${bedData.width}px; height: ${bedData.height}px; background-color: ${statusBgColors[bed.status] || 'var(--bg-secondary)'}; color: ${statusTextColors[bed.status] || 'var(--text-primary)'};"
                         onclick="showBedDetails(${bed.id}, '${bed.bed_number}', '${bed.bed_type}', '${bed.status}', ${currentTenant ? `'${currentTenant.name}', ${currentTenant.id}` : 'null, null'}, '${currentAssignment ? currentAssignment.assigned_from : ''}', '${currentAssignment ? currentAssignment.assigned_until : ''}', '${bed.monthly_rent || ''}')"
                         title="Bed ${bed.bed_number} - ${bed.status.charAt(0).toUpperCase() + bed.status.slice(1)}${currentTenant ? ' (' + currentTenant.name + ')' : ''}">
                        <div class="mb-1">
                            <i class="fas fa-bed text-lg"></i>
                        </div>
                        <div class="text-xs font-semibold">${bed.bed_number}</div>
                        ${currentTenant ? `<div class="text-xs truncate mt-1" title="${currentTenant.name}">${currentTenant.name.length > 8 ? currentTenant.name.substring(0, 8) + '...' : currentTenant.name}</div>` : ''}
                    </div>
                `;
            });

            modalContent += `
                            </div>
                        </div>

                        <!-- Legend -->
                        <div class="mt-4 flex items-center justify-center gap-6 text-sm">
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
            `;

            document.getElementById('modalRoomContent').innerHTML = modalContent;
        })
        .catch(error => {
            console.error('Error loading room details:', error);
            document.getElementById('modalRoomContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-400 mb-2"></i>
                    <p class="text-red-500">Error loading room details.</p>
                    <button onclick="showRoomDetails(${roomId})" class="mt-2 text-blue-600 hover:text-blue-700 text-sm">Try Again</button>
                </div>
            `;
        });
}

function showBedDetails(bedId, bedNumber, bedType, bedStatus, tenantName, tenantId, occupiedFrom, occupiedUntil, monthlyRent) {
    // Show bed modal
    document.getElementById('bedModal').classList.remove('hidden');
    document.getElementById('modalBedTitle').textContent = `Bed ${bedNumber}`;

    let modalContent = `
        <div class="space-y-4">
            <div>
                <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Status:</label>
                <p class="text-sm font-medium" style="color: var(--text-primary);">${bedStatus.charAt(0).toUpperCase() + bedStatus.slice(1)}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Bed Type:</label>
                <p class="text-sm" style="color: var(--text-primary);">${bedType}</p>
            </div>
    `;

    // Add tenant information if bed is occupied
    if (tenantId && tenantName && tenantName !== 'null') {
        modalContent += `
            <div>
                <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Tenant:</label>
                <div class="flex items-center justify-between mt-1">
                    <p class="text-sm" style="color: var(--text-primary);">${tenantName}</p>
                    <a href="/tenants/${tenantId}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition-colors duration-200 flex items-center gap-1">
                        <i class="fas fa-user"></i>
                        View Tenant
                    </a>
                </div>
            </div>
        `;

        if (occupiedFrom && occupiedFrom !== '') {
            modalContent += `
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Occupied From:</label>
                    <p class="text-sm" style="color: var(--text-primary);">${occupiedFrom}</p>
                </div>
            `;
        }

        if (occupiedUntil && occupiedUntil !== '') {
            modalContent += `
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Occupied Until:</label>
                    <p class="text-sm" style="color: var(--text-primary);">${occupiedUntil}</p>
                </div>
            `;
        }

        if (monthlyRent && monthlyRent !== '') {
            modalContent += `
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Monthly Rent:</label>
                    <p class="text-sm font-medium text-green-600">${monthlyRent}</p>
                </div>
            `;
        }
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

function getStatusBadgeClass(status) {
    const classes = {
        'available': 'var(--bed-available-badge-bg) var(--bed-available-badge-text)',
        'occupied': 'var(--bed-occupied-badge-bg) var(--bed-occupied-badge-text)',
        'maintenance': 'var(--bed-maintenance-badge-bg) var(--bed-maintenance-badge-text)',
        'reserved': 'var(--bed-reserved-badge-bg) var(--bed-reserved-badge-text)'
    };
    return classes[status] || 'var(--bg-secondary) var(--text-primary)';
}

function closeRoomModal() {
    document.getElementById('roomModal').classList.add('hidden');
}

function closeBedModal() {
    document.getElementById('bedModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('roomModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRoomModal();
    }
});

document.getElementById('bedModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBedModal();
    }
});
</script>
@endsection
