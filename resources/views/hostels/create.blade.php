@extends('layouts.app')

@section('title', 'Create New Hostel')

@php
    $title = 'Create New Hostel';
    $subtitle = 'Add a new hostel to your system';
    $showBackButton = true;
    $backUrl = route('hostels.index');
@endphp

@section('content')

    <form action="{{ route('hostels.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Hostel Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter hostel name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                      style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                      placeholder="Enter hostel description">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Status *</label>
                            <select id="status" name="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">Select status</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Address Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Address *</label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter full address">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">City *</label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('city') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter city">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">State *</label>
                            <input type="text" id="state" name="state" value="{{ old('state') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('state') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter state">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Postal Code *</label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('postal_code') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter postal code">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Country *</label>
                            <input type="text" id="country" name="country" value="{{ old('country') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('country') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter country">
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Phone *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter phone number">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter email address">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="website" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Website</label>
                            <input type="url" id="website" name="website" value="{{ old('website') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('website') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter website URL (optional)">
                            @error('website')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Manager Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Manager Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="manager_name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Manager Name *</label>
                            <input type="text" id="manager_name" name="manager_name" value="{{ old('manager_name') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('manager_name') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter manager name">
                            @error('manager_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="manager_phone" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Manager Phone *</label>
                            <input type="tel" id="manager_phone" name="manager_phone" value="{{ old('manager_phone') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('manager_phone') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter manager phone">
                            @error('manager_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="manager_email" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Manager Email *</label>
                            <input type="email" id="manager_email" name="manager_email" value="{{ old('manager_email') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('manager_email') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter manager email">
                            @error('manager_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Timing & Rules -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Timing & Rules</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="check_in_time" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Check-in Time</label>
                            <input type="time" id="check_in_time" name="check_in_time" value="{{ old('check_in_time') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('check_in_time') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            @error('check_in_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="check_out_time" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Check-out Time</label>
                            <input type="time" id="check_out_time" name="check_out_time" value="{{ old('check_out_time') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('check_out_time') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            @error('check_out_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="rules" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Rules & Regulations</label>
                            <textarea id="rules" name="rules" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rules') border-red-500 @enderror"
                                      style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                      placeholder="Enter hostel rules and regulations...">{{ old('rules') }}</textarea>
                            @error('rules')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                            $oldAmenities = old('amenities', []);
                        @endphp
                        @foreach($availableAmenities as $amenity)
                            <label class="flex items-center">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                                       {{ in_array($amenity, $oldAmenities) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm" style="color: var(--text-primary);">{{ $amenity }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('amenities')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Actions</h3>
                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Create Hostel
                        </button>
                        <a href="{{ route('hostels.index') }}"
                           class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="bg-blue-50 rounded-xl p-6" style="background-color: rgba(59, 130, 246, 0.05);">
                    <h3 class="text-lg font-semibold mb-3 text-blue-800" style="color: var(--text-primary);">
                        <i class="fas fa-info-circle mr-2"></i>
                        Quick Tips
                    </h3>
                    <ul class="space-y-2 text-sm text-blue-700" style="color: var(--text-secondary);">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-600"></i>
                            <span>Fill in all required fields marked with *</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-600"></i>
                            <span>Select appropriate amenities for your hostel</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-600"></i>
                            <span>Rooms and beds will be added separately after creation</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle mr-2 mt-0.5 text-blue-600"></i>
                            <span>Provide accurate contact information</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
@endsection
