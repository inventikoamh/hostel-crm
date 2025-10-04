@extends('layouts.app')

@section('title', 'Edit Room ' . $room->room_number)

@php
    $title = 'Edit Room ' . $room->room_number;
    $subtitle = 'Update room information and settings';
    $showBackButton = true;
    $backUrl = route('rooms.show', $room->id);
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('rooms.update', $room->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Basic Information</h3>

                <div class="space-y-4">
                    <div>
                        <label for="hostel_id" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Hostel *</label>
                        <select id="hostel_id" name="hostel_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">Select Hostel</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}" {{ old('hostel_id', $room->hostel_id) == $hostel->id ? 'selected' : '' }}>
                                    {{ $hostel->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('hostel_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="room_number" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Room Number *</label>
                            <input type="text" id="room_number" name="room_number" value="{{ old('room_number', $room->room_number) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="e.g., 101, A-201">
                            @error('room_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="floor" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Floor *</label>
                            <input type="number" id="floor" name="floor" value="{{ old('floor', $room->floor) }}" required min="0" max="50"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            @error('floor')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="room_type" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Room Type *</label>
                            <select id="room_type" name="room_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">Select Room Type</option>
                                <option value="single" {{ old('room_type', $room->room_type) == 'single' ? 'selected' : '' }}>Single Room</option>
                                <option value="double" {{ old('room_type', $room->room_type) == 'double' ? 'selected' : '' }}>Double Room</option>
                                <option value="triple" {{ old('room_type', $room->room_type) == 'triple' ? 'selected' : '' }}>Triple Room</option>
                                <option value="dormitory" {{ old('room_type', $room->room_type) == 'dormitory' ? 'selected' : '' }}>Dormitory</option>
                                <option value="suite" {{ old('room_type', $room->room_type) == 'suite' ? 'selected' : '' }}>Suite</option>
                                <option value="studio" {{ old('room_type', $room->room_type) == 'studio' ? 'selected' : '' }}>Studio</option>
                            </select>
                            @error('room_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="capacity" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Capacity (Beds) *</label>
                            <input type="number" id="capacity" name="capacity" value="{{ old('capacity', $room->capacity) }}" required min="1" max="20"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            @error('capacity')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Current: {{ $room->capacity }} beds. Changing this will add/remove beds automatically.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="rent_per_bed" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Rent per Bed (₹) *</label>
                            <input type="number" id="rent_per_bed" name="rent_per_bed" value="{{ old('rent_per_bed', $room->rent_per_bed) }}" required min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="5000.00">
                            @error('rent_per_bed')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="area_sqft" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Area (sq ft)</label>
                            <input type="number" id="area_sqft" name="area_sqft" value="{{ old('area_sqft', $room->area_sqft) }}" min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="120.50">
                            @error('area_sqft')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Room Status *</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="reserved" {{ old('status', $room->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Room Features -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Room Features</h3>

                <div class="space-y-4">
                    <!-- Amenities Checkboxes -->
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="has_attached_bathroom" name="has_attached_bathroom" value="1"
                                   {{ old('has_attached_bathroom', $room->has_attached_bathroom) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <label for="has_attached_bathroom" class="ml-2 text-sm" style="color: var(--text-primary);">
                                Attached Bathroom
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="has_balcony" name="has_balcony" value="1"
                                   {{ old('has_balcony', $room->has_balcony) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <label for="has_balcony" class="ml-2 text-sm" style="color: var(--text-primary);">
                                Balcony
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="has_ac" name="has_ac" value="1"
                                   {{ old('has_ac', $room->has_ac) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <label for="has_ac" class="ml-2 text-sm" style="color: var(--text-primary);">
                                Air Conditioning
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                  placeholder="Describe the room features, location, or any special notes...">{{ old('description', $room->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Bed Status Warning -->
        @if($room->occupied_beds_count > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Room Currently Occupied</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>This room has {{ $room->occupied_beds_count }} occupied bed(s). Reducing capacity below {{ $room->occupied_beds_count }} beds is not allowed while beds are occupied.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('rooms.show', $room->id) }}"
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-save"></i>
                Update Room
            </button>
        </div>
    </form>
</div>

<script>
// Validate capacity changes
document.getElementById('capacity').addEventListener('change', function() {
    const newCapacity = parseInt(this.value);
    const currentOccupied = {{ $room->occupied_beds_count }};

    if (newCapacity < currentOccupied) {
        alert(`Cannot reduce capacity below ${currentOccupied} beds while they are occupied.`);
        this.value = Math.max(newCapacity, currentOccupied);
    }
});
</script>
@endsection
