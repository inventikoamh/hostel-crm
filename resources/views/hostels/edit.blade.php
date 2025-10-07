@extends('layouts.app')

@section('title', 'Edit ' . $hostel->name)

@php
    $title = 'Edit Hostel';
    $subtitle = 'Update hostel information';
    $showBackButton = true;
    $backUrl = route('hostels.show', $hostel->id);
@endphp

@section('content')

    <form action="{{ route('hostels.update', $hostel->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Hostel Name *</label>
                            <input type="text" id="name" name="name" value="{{ $hostel->name }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">{{ $hostel->description }}</textarea>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Status *</label>
                            <select id="status" name="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="active" {{ $hostel->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $hostel->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ $hostel->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Address Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Address *</label>
                            <input type="text" id="address" name="address" value="{{ $hostel->address }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">City *</label>
                            <input type="text" id="city" name="city" value="{{ $hostel->city }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">State *</label>
                            <input type="text" id="state" name="state" value="{{ $hostel->state }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Postal Code *</label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ $hostel->postal_code }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Country *</label>
                            <input type="text" id="country" name="country" value="{{ $hostel->country }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Phone *</label>
                            <input type="tel" id="phone" name="phone" value="{{ $hostel->phone }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Email *</label>
                            <input type="email" id="email" name="email" value="{{ $hostel->email }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div class="md:col-span-2">
                            <label for="website" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Website</label>
                            <input type="url" id="website" name="website" value="{{ $hostel->website }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                    </div>
                </div>

                <!-- Manager Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Manager Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="manager_name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Manager Name *</label>
                            <input type="text" id="manager_name" name="manager_name" value="{{ $hostel->manager_name }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div>
                            <label for="manager_phone" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Manager Phone *</label>
                            <input type="tel" id="manager_phone" name="manager_phone" value="{{ $hostel->manager_phone }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div class="md:col-span-2">
                            <label for="manager_email" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Manager Email *</label>
                            <input type="email" id="manager_email" name="manager_email" value="{{ $hostel->manager_email }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                    </div>
                </div>

                <!-- Timing & Rules -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Timing & Rules</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="check_in_time" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Check-in Time </label>
                            <input type="time" id="check_in_time" name="check_in_time" value="{{ $hostel->check_in_time }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div>
                            <label for="check_out_time" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Check-out Time </label>
                            <input type="time" id="check_out_time" name="check_out_time" value="{{ $hostel->check_out_time }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div class="md:col-span-2">
                            <label for="rules" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Rules & Regulations </label>
                            <textarea id="rules" name="rules" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                      placeholder="Enter hostel rules and regulations...">{{ $hostel->rules }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Amenities -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Amenities</h3>
                    <div class="space-y-2">
                        @php
                            $availableAmenities = ['WiFi', 'Laundry', 'Kitchen', 'Common Room', 'Parking', 'Security', 'Gym', 'Study Room', 'Cafeteria', 'Library', 'Cleaning Service', 'Air Conditioning'];
                        @endphp
                        @foreach($availableAmenities as $amenity)
                            <label class="flex items-center">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                                       {{ in_array($amenity, $hostel->amenities) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm" style="color: var(--text-primary);">{{ $amenity }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Actions</h3>
                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Update Hostel
                        </button>
                        <a href="{{ route('hostels.show', $hostel->id) }}"
                           class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
