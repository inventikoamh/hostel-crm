@extends('layouts.app')

@section('title', 'Add Usage Record')

@php
    $title = 'Add Usage Record';
    $subtitle = 'Record amenity usage for a specific tenant and date';
    $showBackButton = true;
    $backUrl = route('amenity-usage.index');
@endphp

@section('content')
    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <form method="POST" action="{{ route('amenity-usage.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label for="tenant_amenity_id" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                            Tenant & Amenity <span class="text-red-500">*</span>
                        </label>
                        <select id="tenant_amenity_id"
                                name="tenant_amenity_id"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tenant_amenity_id') border-red-500 @enderror"
                                style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                required>
                            <option value="">Select tenant and amenity...</option>
                            @foreach($tenantAmenities as $tenantAmenity)
                                <option value="{{ $tenantAmenity->id }}"
                                        data-price="{{ $tenantAmenity->price }}"
                                        {{ old('tenant_amenity_id') == $tenantAmenity->id ? 'selected' : '' }}>
                                    {{ $tenantAmenity->tenantProfile->user->name }} - {{ $tenantAmenity->paidAmenity->name }} ({{ $tenantAmenity->formatted_price }})
                                </option>
                            @endforeach
                        </select>
                        @error('tenant_amenity_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <div class="mb-4">
                        <label for="usage_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                            Usage Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="usage_date"
                               name="usage_date"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('usage_date') border-red-500 @enderror"
                               value="{{ old('usage_date', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                               required>
                        @error('usage_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <select id="quantity"
                                name="quantity"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('quantity') border-red-500 @enderror"
                                style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                required>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ old('quantity', 1) == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('quantity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Unit Price</label>
                        <div class="w-full px-3 py-2 border rounded-lg"
                             id="unit_price_display"
                             style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                            Select amenity first
                        </div>
                    </div>
                </div>

                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Total Amount</label>
                        <div class="w-full px-3 py-2 border rounded-lg"
                             id="total_amount_display"
                             style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                            ₹0.00
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Notes</label>
                <textarea id="notes"
                          name="notes"
                          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                          rows="3"
                          placeholder="Any additional notes about this usage..."
                          style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('amenity-usage.index') }}"
                   class="inline-flex items-center px-4 py-2 border font-medium rounded-lg transition-colors duration-200"
                   style="border-color: var(--border-color); color: var(--text-primary); background-color: var(--card-bg);"
                   onmouseover="this.style.backgroundColor='var(--bg-secondary)'"
                   onmouseout="this.style.backgroundColor='var(--card-bg)'">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Save Record
                </button>
            </div>
        </form>
    </div>

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

        tenantAmenitySelect.addEventListener('change', updatePriceCalculation);
        quantitySelect.addEventListener('change', updatePriceCalculation);

        // Initialize calculation if there's a pre-selected value
        updatePriceCalculation();
    });
    </script>
@endsection
