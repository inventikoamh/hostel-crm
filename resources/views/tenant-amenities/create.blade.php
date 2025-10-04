@extends('layouts.app')

@section('title', 'Assign Service to Tenant')

@php
    $title = 'Assign Service';
    $subtitle = 'Assign a paid service to a tenant';
    $showBackButton = true;
    $backUrl = route('tenant-amenities.index');
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <form method="POST" action="{{ route('tenant-amenities.store') }}">
            @csrf

            <div class="space-y-6">
                <!-- Tenant Selection -->
                <div>
                    <label for="tenant_profile_id" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Select Tenant <span class="text-red-500">*</span>
                    </label>
                    <select name="tenant_profile_id" id="tenant_profile_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Choose a tenant...</option>
                        @foreach($tenants as $tenantOption)
                            <option value="{{ $tenantOption->id }}"
                                {{ (old('tenant_profile_id') == $tenantOption->id || $selectedTenantId == $tenantOption->user_id) ? 'selected' : '' }}>
                                {{ $tenantOption->user->name }} - {{ $tenantOption->user->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('tenant_profile_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @if($selectedTenantId)
                        <p class="mt-2 text-sm text-blue-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tenant pre-selected from previous page. You can change the selection if needed.
                        </p>
                    @endif
                </div>

                <!-- Service Selection -->
                <div>
                    <label for="paid_amenity_id" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Select Service <span class="text-red-500">*</span>
                    </label>
                    <select name="paid_amenity_id" id="paid_amenity_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                            onchange="updateServiceDetails()">
                        <option value="">Choose a service...</option>
                        @if($availableAmenities && count($availableAmenities) > 0)
                            @foreach(collect($availableAmenities)->groupBy('category') as $category => $categoryAmenities)
                                <optgroup label="{{ ucfirst(str_replace('_', ' ', $category)) }}">
                                    @foreach($categoryAmenities as $amenity)
                                        <option value="{{ $amenity->id }}"
                                                data-price="{{ $amenity->price }}"
                                                data-billing-type="{{ $amenity->billing_type }}"
                                                data-description="{{ $amenity->description }}"
                                                data-availability="{{ $amenity->getAvailabilityText() }}"
                                                data-max-usage="{{ $amenity->max_usage_per_day }}"
                                                {{ old('paid_amenity_id') == $amenity->id ? 'selected' : '' }}>
                                            {{ $amenity->name }} - {{ $amenity->formatted_price }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @else
                            <option value="" disabled>No services available</option>
                        @endif
                    </select>
                    @error('paid_amenity_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Details Display -->
                <div id="serviceDetails" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                    <h4 class="font-medium mb-2" style="color: var(--text-primary);">Service Details</h4>
                    <div id="serviceInfo" class="space-y-2 text-sm" style="color: var(--text-secondary);">
                        <!-- Service details will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" required
                               value="{{ old('start_date', date('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}"
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
                               value="{{ old('end_date') }}"
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
                               value="{{ old('custom_price') }}"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                               placeholder="Leave empty to use default price">
                    </div>
                    <p class="mt-1 text-xs" style="color: var(--text-secondary);">Override the default service price for this tenant</p>
                    @error('custom_price')
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
                              placeholder="Any additional notes or special instructions...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-4">
                    <a href="{{ route('tenant-amenities.index') }}"
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 text-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Assign Service
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function updateServiceDetails() {
    const select = document.getElementById('paid_amenity_id');
    const detailsDiv = document.getElementById('serviceDetails');
    const infoDiv = document.getElementById('serviceInfo');

    if (select.value) {
        const option = select.options[select.selectedIndex];
        const price = option.dataset.price;
        const billingType = option.dataset.billingType;
        const description = option.dataset.description;
        const availability = option.dataset.availability;
        const maxUsage = option.dataset.maxUsage;

        let infoHtml = `
            <div><strong>Price:</strong> ₹${parseFloat(price).toFixed(2)}/${billingType === 'daily' ? 'day' : 'month'}</div>
            <div><strong>Billing:</strong> ${billingType.charAt(0).toUpperCase() + billingType.slice(1)}</div>
        `;

        if (description) {
            infoHtml += `<div><strong>Description:</strong> ${description}</div>`;
        }

        if (availability) {
            infoHtml += `<div><strong>Availability:</strong> ${availability}</div>`;
        }

        if (maxUsage && billingType === 'daily') {
            infoHtml += `<div><strong>Max Usage:</strong> ${maxUsage} times per day</div>`;
        }

        infoDiv.innerHTML = infoHtml;
        detailsDiv.classList.remove('hidden');
    } else {
        detailsDiv.classList.add('hidden');
    }
}

// Update service details on page load if there's a selected value
document.addEventListener('DOMContentLoaded', function() {
    updateServiceDetails();

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
