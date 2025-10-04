@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid px-4">
    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <a href="{{ route('notifications.settings.index') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-cog mr-2"></i>
            Settings
        </a>
        <button onclick="processScheduled()"
                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-play mr-2"></i>
            Process Scheduled
        </button>
        <button onclick="retryFailed()"
                class="inline-flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-redo mr-2"></i>
            Retry Failed
        </button>
        <button onclick="showStatistics()"
                class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <i class="fas fa-chart-bar mr-2"></i>
            Statistics
        </button>
    </div>

    <!-- Data Table -->
    <x-data-table
        :data="$data"
        :columns="$columns"
        :filters="$filters"
        :pagination="$notifications"
        searchPlaceholder="Search notifications..."
        :showActions="true"
        :actionRoutes="[
            'show' => 'notifications.show'
        ]"
        title=""
        addButtonText=""
        addButtonUrl=""
    />
</div>

<!-- Statistics Modal -->
<div id="statisticsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" style="background-color: var(--card-bg);">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold" style="color: var(--text-primary);">Notification Statistics</h3>
                    <button onclick="closeStatistics()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div id="statisticsContent" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- Statistics will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function processScheduled() {
    if (confirm('Process all scheduled notifications?')) {
        fetch('{{ route("notifications.process-scheduled") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while processing scheduled notifications.');
        });
    }
}

function retryFailed() {
    if (confirm('Retry all failed notifications?')) {
        fetch('{{ route("notifications.retry-failed") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while retrying failed notifications.');
        });
    }
}

function showStatistics() {
    fetch('{{ route("notifications.statistics") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayStatistics(data.data);
                document.getElementById('statisticsModal').classList.remove('hidden');
            } else {
                alert('Failed to load statistics.');
            }
        })
        .catch(error => {
            alert('An error occurred while loading statistics.');
        });
}

function displayStatistics(stats) {
    const content = document.getElementById('statisticsContent');
    content.innerHTML = `
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-blue-600">${stats.total}</div>
            <div class="text-sm text-blue-800">Total Notifications</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-yellow-600">${stats.pending}</div>
            <div class="text-sm text-yellow-800">Pending</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-green-600">${stats.sent}</div>
            <div class="text-sm text-green-800">Sent</div>
        </div>
        <div class="bg-red-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-red-600">${stats.failed}</div>
            <div class="text-sm text-red-800">Failed</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-purple-600">${stats.today}</div>
            <div class="text-sm text-purple-800">Today</div>
        </div>
        <div class="bg-indigo-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-indigo-600">${stats.this_month}</div>
            <div class="text-sm text-indigo-800">This Month</div>
        </div>
    `;
}

function closeStatistics() {
    document.getElementById('statisticsModal').classList.add('hidden');
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
@endsection
