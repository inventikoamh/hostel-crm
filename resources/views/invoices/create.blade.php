@extends('layouts.app')

@section('title', 'Create Invoice')

@php
    $title = 'Create Invoice';
    $subtitle = 'Generate a new invoice for tenant';
    $showBackButton = true;
    $backUrl = route('invoices.index');
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
            @csrf

            <div class="space-y-6">
                <!-- Invoice Type Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Invoice Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required onchange="handleTypeChange()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">Select invoice type...</option>
                            <option value="rent" {{ old('type', $selectedType) == 'rent' ? 'selected' : '' }}>Room Rent</option>
                            <option value="amenities" {{ old('type', $selectedType) == 'amenities' ? 'selected' : '' }}>Paid Amenities</option>
                            <option value="damage" {{ old('type', $selectedType) == 'damage' ? 'selected' : '' }}>Damage Charges</option>
                            <option value="other" {{ old('type', $selectedType) == 'other' ? 'selected' : '' }}>Other Charges</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tenant_profile_id" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Select Tenant <span class="text-red-500">*</span>
                        </label>
                        <select name="tenant_profile_id" id="tenant_profile_id" required onchange="handleTenantChange()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">Choose a tenant...</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}"
                                    data-rent="{{ $tenant->monthly_rent }}"
                                    data-room="{{ $tenant->currentBed ? $tenant->currentBed->room->room_number : '' }}"
                                    {{ old('tenant_profile_id', $selectedTenantId) == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->user->name }} - {{ $tenant->user->email }}
                                    @if($tenant->currentBed)
                                        (Room {{ $tenant->currentBed->room->room_number }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('tenant_profile_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="invoice_date" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Invoice Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="invoice_date" id="invoice_date"
                               value="{{ old('invoice_date', date('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('invoice_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Due Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="due_date" id="due_date"
                               value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Billing Period (for rent) -->
                <div id="billingPeriod" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                    <div>
                        <label for="period_start" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Period Start
                        </label>
                        <input type="date" name="period_start" id="period_start"
                               value="{{ old('period_start') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('period_start')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="period_end" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Period End
                        </label>
                        <input type="date" name="period_end" id="period_end"
                               value="{{ old('period_end') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('period_end')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Invoice Items -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Invoice Items</h3>
                        <button type="button" onclick="addInvoiceItem()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Add Item
                        </button>
                    </div>

                    <div id="invoiceItems" class="space-y-4">
                        <!-- Items will be added here dynamically -->
                    </div>
                </div>

                <!-- Tax and Discount -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tax_amount" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Tax Amount (₹)
                        </label>
                        <input type="number" name="tax_amount" id="tax_amount"
                               value="{{ old('tax_amount', 0) }}" min="0" step="0.01"
                               onchange="calculateTotal()"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('tax_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="discount_amount" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Discount Amount (₹)
                        </label>
                        <input type="number" name="discount_amount" id="discount_amount"
                               value="{{ old('discount_amount', 0) }}" min="0" step="0.01"
                               onchange="calculateTotal()"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('discount_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Notes
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                              placeholder="Additional notes or terms...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Summary -->
                <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span style="color: var(--text-secondary);">Subtotal:</span>
                            <span id="subtotalDisplay" class="font-medium" style="color: var(--text-primary);">₹0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: var(--text-secondary);">Tax:</span>
                            <span id="taxDisplay" class="font-medium" style="color: var(--text-primary);">₹0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: var(--text-secondary);">Discount:</span>
                            <span id="discountDisplay" class="font-medium text-red-600">-₹0.00</span>
                        </div>
                        <hr class="border-gray-300" style="border-color: var(--border-color);">
                        <div class="flex justify-between text-lg font-bold">
                            <span style="color: var(--text-primary);">Total:</span>
                            <span id="totalDisplay" class="text-blue-600">₹0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('invoices.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Create Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemCounter = 0;

function handleTypeChange() {
    const type = document.getElementById('type').value;
    const billingPeriod = document.getElementById('billingPeriod');

    if (type === 'rent') {
        billingPeriod.style.display = 'grid';
        // Auto-fill current month period
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        document.getElementById('period_start').value = firstDay.toISOString().split('T')[0];
        document.getElementById('period_end').value = lastDay.toISOString().split('T')[0];
    } else {
        billingPeriod.style.display = 'none';
    }

    // Clear existing items and add default based on type
    clearItems();
    if (type) {
        addDefaultItem(type);
    }
}

function handleTenantChange() {
    const select = document.getElementById('tenant_profile_id');
    const option = select.options[select.selectedIndex];
    const type = document.getElementById('type').value;

    if (type === 'rent' && option.dataset.rent) {
        // Update rent item if exists
        const rentInput = document.querySelector('input[name="items[0][unit_price]"]');
        if (rentInput) {
            rentInput.value = option.dataset.rent;
            calculateItemTotal(0);
        }
    }
}

function addDefaultItem(type) {
    const select = document.getElementById('tenant_profile_id');
    const option = select.options[select.selectedIndex];

    let description = '';
    let price = 0;

    switch(type) {
        case 'rent':
            description = option.dataset.room ? `Room Rent - ${option.dataset.room}` : 'Room Rent';
            price = option.dataset.rent || 0;
            break;
        case 'amenities':
            description = 'Paid Amenity Charges';
            break;
        case 'damage':
            description = 'Damage Charges';
            break;
        case 'other':
            description = 'Other Charges';
            break;
    }

    addInvoiceItem(description, 1, price, type);
}

function addInvoiceItem(description = '', quantity = 1, unitPrice = 0, itemType = '') {
    const container = document.getElementById('invoiceItems');
    const type = document.getElementById('type').value || itemType;

    const itemHtml = `
        <div class="border border-gray-200 rounded-lg p-4" style="border-color: var(--border-color);" id="item_${itemCounter}">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium" style="color: var(--text-primary);">Item ${itemCounter + 1}</h4>
                <button type="button" onclick="removeItem(${itemCounter})"
                        class="text-red-600 hover:text-red-800 transition-colors duration-200">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1" style="color: var(--text-primary);">Description</label>
                    <input type="text" name="items[${itemCounter}][description]"
                           value="${description}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    <input type="hidden" name="items[${itemCounter}][item_type]" value="${type}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--text-primary);">Quantity</label>
                    <input type="number" name="items[${itemCounter}][quantity]"
                           value="${quantity}" min="1" required
                           onchange="calculateItemTotal(${itemCounter})"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--text-primary);">Unit Price (₹)</label>
                    <input type="number" name="items[${itemCounter}][unit_price]"
                           value="${unitPrice}" min="0" step="0.01" required
                           onchange="calculateItemTotal(${itemCounter})"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>
            </div>

            <div class="mt-3 text-right">
                <span class="text-sm" style="color: var(--text-secondary);">Total: </span>
                <span id="itemTotal_${itemCounter}" class="font-medium text-blue-600">₹${(quantity * unitPrice).toFixed(2)}</span>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', itemHtml);
    itemCounter++;
    calculateTotal();
}

function removeItem(index) {
    document.getElementById(`item_${index}`).remove();
    calculateTotal();
}

function clearItems() {
    document.getElementById('invoiceItems').innerHTML = '';
    itemCounter = 0;
}

function calculateItemTotal(index) {
    const quantityInput = document.querySelector(`input[name="items[${index}][quantity]"]`);
    const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
    const totalSpan = document.getElementById(`itemTotal_${index}`);

    if (quantityInput && priceInput && totalSpan) {
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;

        totalSpan.textContent = `₹${total.toFixed(2)}`;
        calculateTotal();
    }
}

function calculateTotal() {
    let subtotal = 0;

    // Calculate subtotal from all items
    document.querySelectorAll('[id^="itemTotal_"]').forEach(span => {
        const amount = parseFloat(span.textContent.replace('₹', '')) || 0;
        subtotal += amount;
    });

    const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const total = subtotal + tax - discount;

    document.getElementById('subtotalDisplay').textContent = `₹${subtotal.toFixed(2)}`;
    document.getElementById('taxDisplay').textContent = `₹${tax.toFixed(2)}`;
    document.getElementById('discountDisplay').textContent = `-₹${discount.toFixed(2)}`;
    document.getElementById('totalDisplay').textContent = `₹${total.toFixed(2)}`;
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    const type = document.getElementById('type').value;
    if (type) {
        handleTypeChange();
    } else {
        addInvoiceItem(); // Add one empty item by default
    }
});
</script>
@endsection
