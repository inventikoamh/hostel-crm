@extends('layouts.app')

@section('title', 'Tenant Services')

@php
    $title = 'Tenant Services';
    $subtitle = 'Manage paid services assigned to tenants';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Assignments"
            value="{{ $stats['total'] }}"
            subtitle="All service assignments"
            icon="fas fa-users"
            color="blue"
        />
        <x-stats-card
            title="Active Services"
            value="{{ $stats['active'] }}"
            subtitle="Currently active"
            icon="fas fa-check-circle"
            color="green"
        />
        <x-stats-card
            title="Monthly Services"
            value="{{ $stats['monthly'] }}"
            subtitle="Monthly billing"
            icon="fas fa-calendar"
            color="yellow"
        />
        <x-stats-card
            title="Daily Services"
            value="{{ $stats['daily'] }}"
            subtitle="Daily billing"
            icon="fas fa-clock"
            color="purple"
        />
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Service Assignments"
        add-button-text="Assign Service"
        add-button-url="{{ route('tenant-amenities.create') }}"
        :columns="$columns"
        :data="$data"
        :actions="true"
        :searchable="true"
        :exportable="false"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$tenantAmenities"
        search-placeholder="Search by tenant name or service..."
    />
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
                    const statusBadge = document.querySelector(`[data-tenant-amenity-id="${tenantAmenityId}"] .status-badge`);
                    if (statusBadge) {
                        statusBadge.className = `status-badge ${getStatusClasses(newStatus)}`;
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
