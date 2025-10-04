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
                           class="px-4 py-2 rounded-lg transition-colors duration-200 {{ $floor == $selectedFloor ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
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
                    <p class="text-xl font-bold text-blue-600">{{ $occupiedBeds }}</p>
                    <p class="text-xs text-gray-500">Occupied</p>
                </div>
                <div class="text-center">
                    <p class="text-xl font-bold text-green-600">{{ $availableBeds }}</p>
                    <p class="text-xs text-gray-500">Available</p>
                </div>
                <div class="text-center">
                    <p class="text-xl font-bold text-purple-600">{{ $occupancyRate }}%</p>
                    <p class="text-xs text-gray-500">Occupancy</p>
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
                    <div class="w-4 h-4 bg-green-500 rounded border"></div>
                    <span>Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-blue-500 rounded border"></div>
                    <span>Occupied</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-yellow-500 rounded border"></div>
                    <span>Maintenance</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-purple-500 rounded border"></div>
                    <span>Reserved</span>
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
                            $statusColor = match($room->status) {
                                'available' => 'border-green-500 bg-green-50',
                                'occupied' => 'border-blue-500 bg-blue-50',
                                'maintenance' => 'border-yellow-500 bg-yellow-50',
                                'reserved' => 'border-purple-500 bg-purple-50',
                                default => 'border-gray-300 bg-gray-50'
                            };
                        @endphp
                        <div class="room-card border-2 rounded-lg p-3 cursor-pointer hover:shadow-md transition-all duration-200 {{ $statusColor }}"
                             onclick="showRoomDetails({{ $room->id }})"
                             data-room-id="{{ $room->id }}">
                            <!-- Room Header -->
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-sm">{{ $room->room_number }}</h4>
                                <span class="text-xs px-2 py-1 rounded {{ $room->status === 'available' ? 'bg-green-100 text-green-800' : ($room->status === 'occupied' ? 'bg-blue-100 text-blue-800' : ($room->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800')) }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </div>

                            <!-- Room Type -->
                            <p class="text-xs text-gray-600 mb-2">{{ $room->room_type_display }}</p>

                            <!-- Bed Status -->
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span>Beds: {{ $room->occupied_beds_count }}/{{ $room->capacity }}</span>
                                <span class="font-medium">{{ $occupancyRate }}%</span>
                            </div>

                            <!-- Bed Visual -->
                            <div class="grid grid-cols-4 gap-1 mb-2">
                                @foreach($room->beds as $bed)
                                    <div class="w-3 h-3 rounded-sm {{ $bed->status === 'available' ? 'bg-green-400' : ($bed->status === 'occupied' ? 'bg-blue-400' : ($bed->status === 'maintenance' ? 'bg-yellow-400' : 'bg-purple-400')) }}"
                                         title="Bed {{ $bed->bed_number }} - {{ ucfirst($bed->status) }}{{ $bed->tenant ? ' (' . $bed->tenant->name . ')' : '' }}">
                                    </div>
                                @endforeach
                            </div>

                            <!-- Rent -->
                            <p class="text-xs text-gray-500">₹{{ number_format($room->rent_per_bed, 0) }}/bed</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-door-open text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-500 mb-2">No Rooms on This Floor</h3>
                    <p class="text-gray-400">Add rooms to floor {{ $selectedFloor }} to see them here.</p>
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
                                    <span class="font-medium text-green-600">₹${room.rent_per_bed.toLocaleString()}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Status:</span>
                                    <span class="px-2 py-1 rounded text-xs ${getStatusBadgeClass(room.status)}">${room.status.charAt(0).toUpperCase() + room.status.slice(1)}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                            <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Features</h4>
                            <div class="space-y-2">
                                ${room.has_ac ? '<div class="flex items-center gap-2 text-sm"><i class="fas fa-snowflake text-purple-600"></i><span>Air Conditioning</span></div>' : ''}
                                ${room.has_attached_bathroom ? '<div class="flex items-center gap-2 text-sm"><i class="fas fa-bath text-blue-600"></i><span>Attached Bathroom</span></div>' : ''}
                                ${room.has_balcony ? '<div class="flex items-center gap-2 text-sm"><i class="fas fa-door-open text-green-600"></i><span>Balcony</span></div>' : ''}
                                ${!room.has_ac && !room.has_attached_bathroom && !room.has_balcony ? '<span class="text-sm text-gray-500">No special features</span>' : ''}
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                            <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Statistics</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Occupied:</span>
                                    <span class="font-medium text-blue-600">${room.occupied_beds_count}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Available:</span>
                                    <span class="font-medium text-green-600">${room.available_beds_count}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Occupancy:</span>
                                    <span class="font-medium text-purple-600">${room.occupancy_rate}%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Revenue:</span>
                                    <span class="font-medium text-green-600">₹${(room.occupied_beds_count * room.rent_per_bed).toLocaleString()}</span>
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
                const statusColors = {
                    'available': 'border-green-500 bg-green-100 text-green-800',
                    'occupied': 'border-blue-500 bg-blue-100 text-blue-800',
                    'maintenance': 'border-yellow-500 bg-yellow-100 text-yellow-800',
                    'reserved': 'border-purple-500 bg-purple-100 text-purple-800'
                };

                modalContent += `
                    <div class="absolute border-2 rounded-lg p-2 text-center cursor-pointer hover:shadow-lg transition-all duration-200 ${statusColors[bed.status] || 'border-gray-500 bg-gray-100 text-gray-800'}"
                         style="left: ${bedData.x}px; top: ${bedData.y}px; width: ${bedData.width}px; height: ${bedData.height}px;"
                         onclick="showBedDetails(${bed.id}, '${bed.bed_number}', '${bed.bed_type}', '${bed.status}', ${bed.tenant ? `'${bed.tenant.name}', ${bed.tenant.id}` : 'null, null'}, '${bed.occupied_from || ''}', '${bed.occupied_until || ''}', '${bed.monthly_rent || ''}')"
                         title="Bed ${bed.bed_number} - ${bed.status.charAt(0).toUpperCase() + bed.status.slice(1)}${bed.tenant ? ' (' + bed.tenant.name + ')' : ''}">
                        <div class="mb-1">
                            <i class="fas fa-bed text-lg"></i>
                        </div>
                        <div class="text-xs font-semibold">${bed.bed_number}</div>
                        ${bed.tenant ? `<div class="text-xs truncate mt-1" title="${bed.tenant.name}">${bed.tenant.name.length > 8 ? bed.tenant.name.substring(0, 8) + '...' : bed.tenant.name}</div>` : ''}
                    </div>
                `;
            });

            modalContent += `
                            </div>
                        </div>

                        <!-- Legend -->
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
        'available': 'bg-green-100 text-green-800',
        'occupied': 'bg-blue-100 text-blue-800',
        'maintenance': 'bg-yellow-100 text-yellow-800',
        'reserved': 'bg-purple-100 text-purple-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
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
