@extends('layouts.app')

@section('title', 'Edit Usage Record')

@php
    $title = 'Edit Usage Record';
    $subtitle = 'Update amenity usage record information';
    $showBackButton = true;
    $backUrl = route('amenity-usage.show', $amenityUsage);
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Edit Usage Record</h1>
            <p class="mt-1 text-sm" style="color: var(--text-secondary);">
                Update amenity usage record information
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('amenity-usage.show', $amenityUsage) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
               style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                <i class="fas fa-eye mr-2"></i>
                View Record
            </a>
            <a href="{{ route('amenity-usage.index') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Records
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="shadow-lg rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="px-6 py-6 sm:p-8">
            <form method="POST" action="{{ route('amenity-usage.update', $amenityUsage) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Usage Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tenant_amenity_id" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                                Tenant & Amenity <span class="text-red-500">*</span>
                            </label>
                            <select id="tenant_amenity_id"
                                    name="tenant_amenity_id"
                                    class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('tenant_amenity_id') border-red-500 @enderror"
                                    style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                    required>
                                <option value="">Select tenant and amenity...</option>
                                @foreach($tenantAmenities as $tenantAmenity)
                                    <option value="{{ $tenantAmenity->id }}"
                                            data-price="{{ $tenantAmenity->effective_price }}"
                                            {{ old('tenant_amenity_id', $amenityUsage->tenant_amenity_id) == $tenantAmenity->id ? 'selected' : '' }}>
                                        {{ $tenantAmenity->tenantProfile->user->name }} - {{ $tenantAmenity->paidAmenity->name }} (₹{{ number_format($tenantAmenity->effective_price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_amenity_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="usage_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                                Usage Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   id="usage_date"
                                   name="usage_date"
                                   class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('usage_date') border-red-500 @enderror"
                                   style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                   value="{{ old('usage_date', $amenityUsage->usage_date->format('Y-m-d')) }}"
                                   max="{{ now()->format('Y-m-d') }}"
                                   required>
                            @error('usage_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="quantity" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="quantity"
                                   name="quantity"
                                   min="1"
                                   max="10"
                                   class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('quantity') border-red-500 @enderror"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   value="{{ old('quantity', $amenityUsage->quantity) }}"
                                   required>
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Unit Price</label>
                            <div class="w-full px-3 py-2.5 border rounded-lg shadow-sm"
                                 style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                 id="unit_price_display">
                                {{ $amenityUsage->formatted_unit_price }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Total Amount</label>
                            <div class="w-full px-3 py-2.5 border rounded-lg font-semibold shadow-sm"
                                 style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                 id="total_amount_display">
                                {{ $amenityUsage->formatted_total_amount }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Notes</label>
                        <textarea id="notes"
                                  name="notes"
                                  class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm @error('notes') border-red-500 @enderror"
                                  style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                  rows="3"
                                  placeholder="Any additional notes about this usage...">{{ old('notes', $amenityUsage->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Current Record Info -->
                <div class="rounded-lg p-4 shadow-sm" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle mt-1 mr-3 flex-shrink-0" style="color: #3b82f6;"></i>
                        <div>
                            <h4 class="text-sm font-semibold" style="color: var(--text-primary);">Current Record</h4>
                            <p class="text-sm mt-1 leading-relaxed" style="color: var(--text-secondary);">
                                <strong style="color: var(--text-primary);">{{ $amenityUsage->tenantAmenity->tenantProfile->user->name }}</strong> used
                                <strong style="color: var(--text-primary);">{{ $amenityUsage->tenantAmenity->paidAmenity->name }}</strong>
                                <strong style="color: var(--text-primary);">{{ $amenityUsage->quantity }}x</strong> on
                                <strong style="color: var(--text-primary);">{{ $amenityUsage->usage_date->format('M j, Y') }}</strong>
                                (Total: <strong style="color: var(--text-primary);">{{ $amenityUsage->formatted_total_amount }}</strong>)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6" style="border-top: 1px solid var(--border-color);">
                    <a href="{{ route('amenity-usage.show', $amenityUsage) }}"
                       class="inline-flex items-center px-6 py-2.5 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                       style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm">
                        <i class="fas fa-save mr-2"></i>
                        Update Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tenantAmenitySelect = document.getElementById('tenant_amenity_id');
    const quantitySelect = document.getElementById('quantity');
    const unitPriceDisplay = document.getElementById('unit_price_display');
    const totalAmountDisplay = document.getElementById('total_amount_display');

    function updatePriceCalculation() {
        const selectedOption = tenantAmenitySelect.options[tenantAmenitySelect.selectedIndex];
        const quantity = parseInt(quantitySelect.value) || 1;

        if (selectedOption && selectedOption.dataset.price) {
            const unitPrice = parseFloat(selectedOption.dataset.price);
            const totalAmount = unitPrice * quantity;

            unitPriceDisplay.textContent = '₹' + unitPrice.toFixed(2);
            totalAmountDisplay.textContent = '₹' + totalAmount.toFixed(2);
        } else {
            unitPriceDisplay.textContent = 'Select amenity first';
            totalAmountDisplay.textContent = '₹0.00';
        }
    }

    // Add event listeners
    if (tenantAmenitySelect) {
        tenantAmenitySelect.addEventListener('change', updatePriceCalculation);
    }

    if (quantitySelect) {
        quantitySelect.addEventListener('change', updatePriceCalculation);
    }

    // Initialize calculation on page load
    updatePriceCalculation();
});
</script>
@endpush
@endsection
