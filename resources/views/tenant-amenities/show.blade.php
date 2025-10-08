@extends('layouts.app')

@section('title', 'Amenity Details')

@php
    $title = 'Amenity Details';
    $subtitle = $tenantAmenity->paidAmenity->name ?? 'Amenity';
    $showBackButton = true;
    $backUrl = route('tenant-amenities.index');
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Amenity Information</h3>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($tenantAmenity->status === 'active') bg-green-100 text-green-800
                            @elseif($tenantAmenity->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($tenantAmenity->status === 'suspended') bg-orange-100 text-orange-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($tenantAmenity->status) }}
                        </span>
                        <select class="status-update-select text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                data-tenant-amenity-id="{{ $tenantAmenity->id }}"
                                data-original-value="{{ $tenantAmenity->status }}">
                            <option value="active" {{ $tenantAmenity->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $tenantAmenity->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ $tenantAmenity->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="pending" {{ $tenantAmenity->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium" style="color: var(--text-secondary);">Amenity</label>
                        <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $tenantAmenity->paidAmenity->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium" style="color: var(--text-secondary);">Category</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $tenantAmenity->paidAmenity->category ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium" style="color: var(--text-secondary);">Start Date</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{
                            $tenantAmenity->start_date ?
                            \Carbon\Carbon::parse($tenantAmenity->start_date)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    @if($tenantAmenity->end_date)
                    <div>
                        <label class="text-sm font-medium" style="color: var(--text-secondary);">End Date</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ \Carbon\Carbon::parse($tenantAmenity->end_date)->format('M d, Y') }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium" style="color: var(--text-secondary);">Price</label>
                        <p class="mt-1 text-sm font-semibold text-green-700">₹{{ number_format($tenantAmenity->effective_price ?? ($tenantAmenity->paidAmenity->price ?? 0), 2) }}</p>
                    </div>
                </div>

                @if(!empty($tenantAmenity->notes))
                    <div class="mt-4">
                        <label class="text-sm font-medium" style="color: var(--text-secondary);">Notes</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $tenantAmenity->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Tenant Information -->
            <div class="bg-white rounded-xl shadow-sm border p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Tenant Information</h3>
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

                <div class="mt-4 flex gap-2">
                    <a href="{{ route('tenants.show', $tenantAmenity->tenantProfile) }}"
                       class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200 text-sm">
                        <i class="fas fa-user mr-2"></i>
                        View Tenant Profile
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Current Month Usage</h3>
                @if(!empty($usageSummary) && isset($usageSummary['total_quantity']))
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-lg border" style="border-color: var(--border-color);">
                            <p class="text-xs" style="color: var(--text-secondary);">Total Quantity</p>
                            <p class="text-xl font-semibold" style="color: var(--text-primary);">{{ $usageSummary['total_quantity'] }}</p>
                        </div>
                        <div class="p-4 rounded-lg border" style="border-color: var(--border-color);">
                            <p class="text-xs" style="color: var(--text-secondary);">Unit Price</p>
                            <p class="text-xl font-semibold text-green-700">₹{{ number_format($usageSummary['unit_price'] ?? ($tenantAmenity->effective_price ?? ($tenantAmenity->paidAmenity->price ?? 0)), 2) }}</p>
                        </div>
                        <div class="p-4 rounded-lg border" style="border-color: var(--border-color);">
                            <p class="text-xs" style="color: var(--text-secondary);">Total Amount</p>
                            <p class="text-xl font-semibold" style="color: var(--text-primary);">₹{{ number_format($usageSummary['total_amount'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm" style="color: var(--text-secondary);">No usage recorded for this month.</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('tenant-amenities.edit', $tenantAmenity) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Subscription
                    </a>
                    <a href="{{ route('tenant-amenities.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border hover:bg-gray-50 transition-colors duration-200" style="border-color: var(--border-color); color: var(--text-primary);">
                        <i class="fas fa-list mr-2"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status updates
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('status-update-select')) {
            const select = e.target;
            const tenantAmenityId = select.dataset.tenantAmenityId;
            const newStatus = select.value;
            const originalValue = select.dataset.originalValue;

            // Show loading state
            select.disabled = true;
            const originalText = select.innerHTML;
            select.innerHTML = '<option>Updating...</option>';

            // Make AJAX request
            fetch(`/tenant-amenities/${tenantAmenityId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the status badge
                    const statusBadge = document.querySelector(`[data-tenant-amenity-id="${tenantAmenityId}"] + span`);
                    if (statusBadge) {
                        statusBadge.className = `px-3 py-1 rounded-full text-xs font-medium ${getStatusClasses(newStatus)}`;
                        statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    }

                    // Show success message
                    showToast('Status updated successfully', 'success');

                    // Update original value
                    select.dataset.originalValue = newStatus;
                } else {
                    // Revert to original value
                    select.value = originalValue;
                    showToast(data.message || 'Failed to update status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                select.value = originalValue;
                showToast('An error occurred while updating status', 'error');
            })
            .finally(() => {
                select.disabled = false;
                select.innerHTML = originalText;
            });
        }
    });

    function getStatusClasses(status) {
        const classes = {
            'active': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'inactive': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
            'suspended': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
        };
        return classes[status] || classes['inactive'];
    }

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>


