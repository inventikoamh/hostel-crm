@extends('layouts.app')

@section('title', 'Edit Service Assignment')

@php
    $title = 'Edit Service Assignment';
    $subtitle = 'Update amenity subscription for ' . $tenantAmenity->tenantProfile->user->name;
    $showBackButton = true;
    $backUrl = route('tenant-amenities.show', $tenantAmenity);
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <form method="POST" action="{{ route('tenant-amenities.update', $tenantAmenity) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Tenant Information (Read-only) -->
                <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                    <h4 class="font-medium mb-3" style="color: var(--text-primary);">Tenant Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium" style="color: var(--text-secondary);">Name</label>
                            <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $tenantAmenity->tenantProfile->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium" style="color: var(--text-secondary);">Email</label>
                            <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $tenantAmenity->tenantProfile->user->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium" style="color: var(--text-secondary);">Phone</label>
                            <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $tenantAmenity->tenantProfile->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium" style="color: var(--text-secondary);">Status</label>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($tenantAmenity->tenantProfile->status === 'active') bg-green-100 text-green-800
                                @elseif($tenantAmenity->tenantProfile->status === 'inactive') bg-gray-100 text-gray-800
                                @else bg-orange-100 text-orange-800 @endif">
                                {{ ucfirst($tenantAmenity->tenantProfile->status) }}
                            </span>
                        </div>
                        @if($tenantAmenity->tenantProfile->currentBed)
                        <div>
                            <label class="text-sm font-medium" style="color: var(--text-secondary);">Room</label>
                            <p class="mt-1 text-sm" style="color: var(--text-primary);">
                                {{ $tenantAmenity->tenantProfile->currentBed->room->room_number }} - Bed {{ $tenantAmenity->tenantProfile->currentBed->bed_number }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium" style="color: var(--text-secondary);">Hostel</label>
                            <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $tenantAmenity->tenantProfile->currentBed->room->hostel->name }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('tenants.show', $tenantAmenity->tenantProfile) }}"
                           class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200 text-sm">
                            <i class="fas fa-user mr-2"></i>
                            View Full Tenant Profile
                        </a>
                    </div>
                </div>

                <!-- Service Information (Read-only) -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Service
                    </label>
                    <div class="px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        {{ $tenantAmenity->paidAmenity->name }} - {{ $tenantAmenity->paidAmenity->formatted_price }}
                    </div>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" required
                               value="{{ old('start_date', $tenantAmenity->start_date ? $tenantAmenity->start_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            End Date (Optional)
                        </label>
                        <input type="date" name="end_date" id="end_date"
                               value="{{ old('end_date', $tenantAmenity->end_date ? $tenantAmenity->end_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <p class="mt-1 text-xs" style="color: var(--text-secondary);">Leave empty for ongoing service</p>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Custom Price -->
                <div>
                    <label for="custom_price" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Custom Price (Optional)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                        <input type="number" name="custom_price" id="custom_price" step="0.01" min="0"
                               value="{{ old('custom_price', $tenantAmenity->custom_price) }}"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                               placeholder="Leave empty to use default price">
                    </div>
                    <p class="mt-1 text-xs" style="color: var(--text-secondary);">Override the default service price for this tenant</p>
                    @error('custom_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="active" {{ old('status', $tenantAmenity->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $tenantAmenity->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $tenantAmenity->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Notes (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                              placeholder="Any additional notes or special instructions...">{{ old('notes', $tenantAmenity->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-4">
                    <a href="{{ route('tenant-amenities.show', $tenantAmenity) }}"
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 text-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Update Service Assignment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum end date to start date
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = '';
        }
    });
});
</script>
@endsection
