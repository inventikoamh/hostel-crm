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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-bath mr-1"></i>
                            Attached Bathroom
                        </span>
                    @endif
                    @if($room->has_balcony)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-door-open mr-1"></i>
                            Balcony
                        </span>
                    @endif
                    @if($room->has_ac)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-snowflake mr-1"></i>
                            Air Conditioning
                        </span>
                    @endif
                    @if(!$room->has_attached_bathroom && !$room->has_balcony && !$room->has_ac)
                        <span class="text-sm text-gray-500">No special features</span>
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
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Bed Layout</h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($room->beds as $bed)
                    <div class="bed-card border-2 rounded-lg p-3 text-center cursor-pointer hover:shadow-md transition-all duration-200
                                {{ $bed->status === 'available' ? 'border-green-500 bg-green-50' :
                                   ($bed->status === 'occupied' ? 'border-blue-500 bg-blue-50' :
                                   ($bed->status === 'maintenance' ? 'border-yellow-500 bg-yellow-50' : 'border-purple-500 bg-purple-50')) }}"
                         onclick="showBedDetails({{ $bed->id }})"
                         data-bed-id="{{ $bed->id }}"
                         data-tenant-id="{{ $bed->tenant ? $bed->tenant->id : '' }}"
                         data-tenant-name="{{ $bed->tenant ? $bed->tenant->name : '' }}"
                         data-bed-type="{{ $bed->bed_type_display }}"
                         data-occupied-from="{{ $bed->occupied_from ? $bed->occupied_from->format('M j, Y') : '' }}"
                         data-occupied-until="{{ $bed->occupied_until ? $bed->occupied_until->format('M j, Y') : '' }}"
                         data-monthly-rent="{{ $bed->monthly_rent ? '₹' . number_format($bed->monthly_rent, 2) : '' }}">

                        <!-- Bed Icon -->
                        <div class="mb-2">
                            <i class="fas fa-bed text-2xl {{ $bed->status === 'available' ? 'text-green-600' :
                                                           ($bed->status === 'occupied' ? 'text-blue-600' :
                                                           ($bed->status === 'maintenance' ? 'text-yellow-600' : 'text-purple-600')) }}"></i>
                        </div>

                        <!-- Bed Number -->
                        <h4 class="font-semibold text-sm mb-1">Bed {{ $bed->bed_number }}</h4>

                        <!-- Status Badge -->
                        <span class="text-xs px-2 py-1 rounded {{ $bed->status === 'available' ? 'bg-green-100 text-green-800' :
                                                                 ($bed->status === 'occupied' ? 'bg-blue-100 text-blue-800' :
                                                                 ($bed->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800')) }}">
                            {{ ucfirst($bed->status) }}
                        </span>

                        <!-- Tenant Name (if occupied) -->
                        @if($bed->tenant)
                            <p class="text-xs text-gray-600 mt-1 truncate">{{ $bed->tenant->name }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Legend -->
            <div class="mt-6 flex items-center justify-center gap-6 text-sm">
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
function showBedDetails(bedId) {
    // Show modal
    document.getElementById('bedModal').classList.remove('hidden');

    // Get bed data from the bed card
    const bedCard = document.querySelector(`[data-bed-id="${bedId}"]`);
    const bedNumber = bedCard.querySelector('h4').textContent;
    const bedStatus = bedCard.querySelector('span').textContent;
    const tenantId = bedCard.dataset.tenantId;
    const tenantName = bedCard.dataset.tenantName;
    const bedType = bedCard.dataset.bedType;
    const occupiedFrom = bedCard.dataset.occupiedFrom;
    const occupiedUntil = bedCard.dataset.occupiedUntil;
    const monthlyRent = bedCard.dataset.monthlyRent;

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

    // Add tenant information if bed is occupied
    if (tenantId && tenantName) {
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

        if (occupiedFrom) {
            modalContent += `
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Occupied From:</label>
                    <p class="text-sm" style="color: var(--text-primary);">${occupiedFrom}</p>
                </div>
            `;
        }

        if (occupiedUntil) {
            modalContent += `
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Occupied Until:</label>
                    <p class="text-sm" style="color: var(--text-primary);">${occupiedUntil}</p>
                </div>
            `;
        }

        if (monthlyRent) {
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

function closeBedModal() {
    document.getElementById('bedModal').classList.add('hidden');
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
