@extends('layouts.app')

@section('title', 'Mark Amenity Attendance')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold mb-1" style="color: var(--text-primary);">Mark Amenity Attendance</h1>
            <p class="text-sm" style="color: var(--text-secondary);">Record daily usage of paid amenities</p>
        </div>
        <a href="{{ route('amenity-usage.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Records
        </a>
    </div>

    <!-- Date Selection -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <form method="GET" action="{{ route('amenity-usage.attendance') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label for="date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Select Date</label>
                <input type="date"
                       id="date"
                       name="date"
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ $selectedDate->format('Y-m-d') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-calendar mr-2"></i>
                    Load Date
                </button>
            </div>
            <div class="md:col-span-6 text-right">
                <div class="text-sm" style="color: var(--text-secondary);">
                    <i class="fas fa-info-circle mr-2"></i>
                    Selected: <strong style="color: var(--text-primary);">{{ $selectedDate->format('l, M j, Y') }}</strong>
                </div>
            </div>
        </form>
    </div>

    <!-- Attendance Form -->
    <form id="attendanceForm">
        @csrf
        <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">

        @if($amenityGroups->count() > 0)
            @foreach($amenityGroups as $amenityName => $tenantAmenities)
                <div class="bg-white rounded-xl shadow-sm border mb-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <div class="p-6 border-b" style="border-color: var(--border-color);">
                        <h5 class="text-lg font-semibold flex items-center mb-0" style="color: var(--text-primary);">
                            <i class="fas fa-concierge-bell mr-3 text-blue-600"></i>
                            {{ $amenityName }}
                            <span class="ml-3 px-2 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">{{ $tenantAmenities->count() }} tenants</span>
                        </h5>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($tenantAmenities as $tenantAmenity)
                                @php
                                    $existingRecord = $existingUsage->get($tenantAmenity->id);
                                    $isMarked = $existingRecord !== null;
                                    $quantity = $isMarked ? $existingRecord->quantity : 1;
                                    $notes = $isMarked ? $existingRecord->notes : '';
                                @endphp

                                <div class="mb-4">
                                    <div class="bg-white border rounded-lg p-4 h-full {{ $isMarked ? 'border-green-500 bg-green-50' : 'border-gray-200' }}" style="border-color: var(--border-color);">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex-1">
                                                    <h6 class="font-medium text-sm mb-1" style="color: var(--text-primary);">
                                                        {{ $tenantAmenity->tenantProfile->user->name }}
                                                    </h6>
                                                    <p class="text-xs" style="color: var(--text-secondary);">
                                                        {{ $tenantAmenity->formatted_price }} per use
                                                    </p>
                                                </div>
                                                <div class="flex items-center">
                                                    <input class="attendance-checkbox mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                           type="checkbox"
                                                           id="attendance_{{ $tenantAmenity->id }}"
                                                           data-tenant-amenity-id="{{ $tenantAmenity->id }}"
                                                           {{ $isMarked ? 'checked' : '' }}>
                                                    <label class="text-xs" for="attendance_{{ $tenantAmenity->id }}">
                                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $isMarked ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                            {{ $isMarked ? 'Used' : 'Not Used' }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="usage-details" style="{{ $isMarked ? '' : 'display: none;' }}">
                                                <div class="grid grid-cols-2 gap-3 mb-3">
                                                    <div>
                                                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Quantity</label>
                                                        <select class="w-full px-2 py-1 text-sm border rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 quantity-select"
                                                                name="usage[{{ $tenantAmenity->id }}][quantity]"
                                                                data-tenant-amenity-id="{{ $tenantAmenity->id }}"
                                                                style="background-color: var(--input-bg); border-color: var(--border-color);">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <option value="{{ $i }}" {{ $quantity == $i ? 'selected' : '' }}>
                                                                    {{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Total</label>
                                                        <div class="w-full px-2 py-1 text-sm border rounded total-amount"
                                                             data-unit-price="{{ $tenantAmenity->price }}"
                                                             style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                                                            ₹{{ number_format($tenantAmenity->price * $quantity, 2) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Notes (optional)</label>
                                                    <textarea class="w-full px-2 py-1 text-sm border rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                              name="usage[{{ $tenantAmenity->id }}][notes]"
                                                              rows="2"
                                                              placeholder="Any additional notes..."
                                                              style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">{{ $notes }}</textarea>
                                                </div>
                                                <input type="hidden"
                                                       name="usage[{{ $tenantAmenity->id }}][tenant_amenity_id]"
                                                       value="{{ $tenantAmenity->id }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Submit Button -->
            <div class="text-center mb-6">
                <button type="submit" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-lg transition-colors duration-200" id="submitBtn">
                    <i class="fas fa-save mr-2"></i>
                    Save Attendance Records
                </button>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border p-8 text-center" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <i class="fas fa-exclamation-circle text-yellow-500 text-5xl mb-4"></i>
                <h5 class="text-lg font-semibold mb-2" style="color: var(--text-primary);">No Active Amenity Subscriptions</h5>
                <p class="text-sm mb-4" style="color: var(--text-secondary);">There are no active paid amenity subscriptions to mark attendance for.</p>
                <a href="{{ route('tenant-amenities.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Manage Tenant Amenities
                </a>
            </div>
        @endif
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle checkbox changes
    document.querySelectorAll('.attendance-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const tenantAmenityId = this.dataset.tenantAmenityId;
            const usageDetails = this.closest('.card-body').querySelector('.usage-details');
            const badge = this.closest('.card-body').querySelector('.badge');

            if (this.checked) {
                usageDetails.style.display = 'block';
                badge.textContent = 'Used';
                badge.className = 'badge bg-success';
                this.closest('.card').classList.add('border-success');
            } else {
                usageDetails.style.display = 'none';
                badge.textContent = 'Not Used';
                badge.className = 'badge bg-secondary';
                this.closest('.card').classList.remove('border-success');
            }
        });
    });

    // Handle quantity changes
    document.querySelectorAll('.quantity-select').forEach(select => {
        select.addEventListener('change', function() {
            const unitPrice = parseFloat(this.closest('.usage-details').querySelector('.total-amount').dataset.unitPrice);
            const quantity = parseInt(this.value);
            const totalAmount = unitPrice * quantity;

            this.closest('.usage-details').querySelector('.total-amount').textContent =
                '₹' + totalAmount.toFixed(2);
        });
    });

    // Handle form submission
    document.getElementById('attendanceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

        // Collect only checked items
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('date', document.querySelector('input[name="date"]').value);

        const usageData = [];
        document.querySelectorAll('.attendance-checkbox:checked').forEach(checkbox => {
            const tenantAmenityId = checkbox.dataset.tenantAmenityId;
            const usageDetails = checkbox.closest('.card-body').querySelector('.usage-details');

            const quantity = usageDetails.querySelector('.quantity-select').value;
            const notes = usageDetails.querySelector('textarea').value;

            usageData.push({
                tenant_amenity_id: tenantAmenityId,
                quantity: quantity,
                notes: notes
            });
        });

        formData.append('usage', JSON.stringify(usageData));

        // Send AJAX request
        fetch('{{ route("amenity-usage.store-attendance") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                date: document.querySelector('input[name="date"]').value,
                usage: usageData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('success', data.message);
                // Optionally reload the page or update UI
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showMessage('error', data.message || 'Failed to save attendance');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('error', 'Network error occurred while saving attendance');
        })
        .finally(() => {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    function showMessage(type, message) {
        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        messageDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        messageDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(messageDiv);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endsection
