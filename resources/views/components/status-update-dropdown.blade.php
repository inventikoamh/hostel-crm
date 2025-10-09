@props(['status', 'tenantAmenityId', 'class' => ''])

@php
    // Handle both array and string status values
    if (is_array($status)) {
        $statusValue = $status['status'] ?? $status;
        $tenantAmenityId = $status['tenantAmenityId'] ?? $tenantAmenityId;
    } else {
        $statusValue = $status;
    }
@endphp

<div class="flex items-center space-x-2 {{ $class }}" data-tenant-amenity-id="{{ $tenantAmenityId }}">
    <!-- Status Badge -->
    <span class="status-badge inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
        @if($statusValue === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
        @elseif($statusValue === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
        @elseif($statusValue === 'suspended') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
        {{ ucfirst($statusValue) }}
    </span>

    <!-- Status Update Dropdown -->
    <select class="status-update-select text-xs border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
            data-tenant-amenity-id="{{ $tenantAmenityId }}"
            data-original-value="{{ $statusValue }}">
        <option value="active" {{ $statusValue === 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ $statusValue === 'inactive' ? 'selected' : '' }}>Inactive</option>
        <option value="suspended" {{ $statusValue === 'suspended' ? 'selected' : '' }}>Suspended</option>
        <option value="pending" {{ $statusValue === 'pending' ? 'selected' : '' }}>Pending</option>
    </select>
</div>
