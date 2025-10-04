@extends('layouts.app')

@section('title', 'Create Room')

@php
    $title = 'Create Room';
    $subtitle = 'Add a new room to your hostel';
    $showBackButton = true;
    $backUrl = route('rooms.index');
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('rooms.store') }}" method="POST" class="space-y-6">
        @csrf

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
                                <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>
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
                            <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="e.g., 101, A-201">
                            @error('room_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="floor" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Floor *</label>
                            <input type="number" id="floor" name="floor" value="{{ old('floor', 1) }}" required min="0" max="50"
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
                                <option value="single" {{ old('room_type') == 'single' ? 'selected' : '' }}>Single Room</option>
                                <option value="double" {{ old('room_type') == 'double' ? 'selected' : '' }}>Double Room</option>
                                <option value="triple" {{ old('room_type') == 'triple' ? 'selected' : '' }}>Triple Room</option>
                                <option value="dormitory" {{ old('room_type') == 'dormitory' ? 'selected' : '' }}>Dormitory</option>
                                <option value="suite" {{ old('room_type') == 'suite' ? 'selected' : '' }}>Suite</option>
                                <option value="studio" {{ old('room_type') == 'studio' ? 'selected' : '' }}>Studio</option>
                            </select>
                            @error('room_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="capacity" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Capacity (Beds) *</label>
                            <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 2) }}" required min="1" max="20"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            @error('capacity')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="rent_per_bed" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Rent per Bed (₹) *</label>
                            <input type="number" id="rent_per_bed" name="rent_per_bed" value="{{ old('rent_per_bed') }}" required min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="5000.00">
                            @error('rent_per_bed')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="area_sqft" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Area (sq ft)</label>
                            <input type="number" id="area_sqft" name="area_sqft" value="{{ old('area_sqft') }}" min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="120.50">
                            @error('area_sqft')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
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
                                   {{ old('has_attached_bathroom') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <label for="has_attached_bathroom" class="ml-2 text-sm" style="color: var(--text-primary);">
                                Attached Bathroom
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="has_balcony" name="has_balcony" value="1"
                                   {{ old('has_balcony') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <label for="has_balcony" class="ml-2 text-sm" style="color: var(--text-primary);">
                                Balcony
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="has_ac" name="has_ac" value="1"
                                   {{ old('has_ac') ? 'checked' : '' }}
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
                                  placeholder="Describe the room features, location, or any special notes...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('rooms.index') }}"
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Create Room
            </button>
        </div>
    </form>
</div>

<script>
// Auto-suggest room number based on floor
document.getElementById('floor').addEventListener('change', function() {
    const floor = this.value;
    const roomNumberInput = document.getElementById('room_number');

    if (floor && !roomNumberInput.value) {
        // Suggest room number format: Floor + 01 (e.g., 101, 201, 301)
        roomNumberInput.value = floor + '01';
    }
});

// Auto-set capacity based on room type
document.getElementById('room_type').addEventListener('change', function() {
    const roomType = this.value;
    const capacityInput = document.getElementById('capacity');

    if (roomType && !capacityInput.value || capacityInput.value == 2) {
        const capacities = {
            'single': 1,
            'double': 2,
            'triple': 3,
            'dormitory': 6,
            'suite': 4,
            'studio': 1
        };

        if (capacities[roomType]) {
            capacityInput.value = capacities[roomType];
        }
    }
});
</script>
@endsection
