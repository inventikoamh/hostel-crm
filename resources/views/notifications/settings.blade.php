@extends('layouts.app')

@section('title', 'Notification Settings')

@php
    $title = 'Notification Settings';
    $subtitle = 'Configure email notification preferences and recipients';
    $showBackButton = false;
@endphp

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-stats-card
            title="Total Settings"
            value="{{ $stats['total'] }}"
            subtitle="All notification settings"
            icon="fas fa-cog"
            color="blue"
        />
        <x-stats-card
            title="Enabled"
            value="{{ $stats['enabled'] }}"
            subtitle="Active notifications"
            icon="fas fa-check-circle"
            color="green"
        />
        <x-stats-card
            title="Disabled"
            value="{{ $stats['disabled'] }}"
            subtitle="Inactive notifications"
            icon="fas fa-times-circle"
            color="red"
        />
        <x-stats-card
            title="High Priority"
            value="{{ $stats['high_priority'] }}"
            subtitle="Urgent notifications"
            icon="fas fa-exclamation-triangle"
            color="yellow"
        />
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <a href="{{ route('notifications.settings.create') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            Add New Setting
        </a>
        <a href="{{ route('notifications.index') }}"
           class="inline-flex items-center justify-center px-4 py-2 border font-medium rounded-lg transition-colors duration-200"
           style="border-color: var(--border-color); color: var(--text-primary); background-color: var(--card-bg);"
           onmouseover="this.style.backgroundColor='var(--bg-secondary)'"
           onmouseout="this.style.backgroundColor='var(--card-bg)'">
            <i class="fas fa-list mr-2"></i>
            View Notifications
        </a>
        <button onclick="testAllNotifications()"
                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-paper-plane mr-2"></i>
            Test All Notifications
        </button>
    </div>

    <!-- Data Table -->
    <x-data-table
        title="Notification Settings"
        add-button-text="Add Setting"
        add-button-url="{{ route('notifications.settings.create') }}"
        :columns="$columns"
        :data="$data"
        :actions="true"
        :searchable="true"
        :exportable="false"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$settings"
        search-placeholder="Search notification settings..."
    />
@endsection

@push('scripts')
<script>
// Add custom action buttons for toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add toggle buttons to each row
    const tableBody = document.querySelector('tbody');
    if (tableBody) {
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const enabledCell = row.querySelector('td:nth-child(5)'); // Enabled column
            if (enabledCell) {
                const enabled = enabledCell.textContent.trim() === 'Enabled';
                const toggleButton = document.createElement('button');
                toggleButton.className = `inline-flex items-center px-2 py-1 rounded text-xs font-medium ${
                    enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                }`;
                toggleButton.textContent = enabled ? 'Enabled' : 'Disabled';
                toggleButton.onclick = function() {
                    toggleSetting(this, row.dataset.id);
                };
                enabledCell.innerHTML = '';
                enabledCell.appendChild(toggleButton);
            }
        });
    }
});

function toggleSetting(button, settingId) {
    fetch(`/notifications/settings/${settingId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button appearance
            if (data.enabled) {
                button.className = 'inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800';
                button.textContent = 'Enabled';
            } else {
                button.className = 'inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800';
                button.textContent = 'Disabled';
            }
            showToast('success', data.message);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'An error occurred while updating the setting.');
    });
}

function testAllNotifications() {
    if (confirm('This will send test notifications for all enabled settings. Continue?')) {
        fetch('/notifications/test-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => {
            showToast('error', 'An error occurred while testing notifications.');
        });
    }
}

function showToast(type, message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.textContent = message;

    document.body.appendChild(toast);

    // Remove toast after 3 seconds
    setTimeout(() => {
        document.body.removeChild(toast);
    }, 3000);
}
</script>
@endpush
