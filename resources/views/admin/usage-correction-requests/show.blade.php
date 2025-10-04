@extends('layouts.app')

@section('title', 'Usage Correction Request Details')

@php
    $title = 'Usage Correction Request Details';
    $subtitle = 'Review usage record correction request';
    $showBackButton = true;
    $backUrl = route('admin.usage-correction-requests.index');
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Request Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tenant Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Tenant Information</h2>
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center" style="background-color: var(--bg-secondary);">
                            @if($usageCorrectionRequest->requestedBy->avatar)
                                <img src="{{ asset('storage/' . $usageCorrectionRequest->requestedBy->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover">
                            @else
                                <i class="fas fa-user text-2xl" style="color: var(--text-secondary);"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-medium" style="color: var(--text-primary);">{{ $usageCorrectionRequest->requestedBy->name }}</h3>
                            <p class="text-sm" style="color: var(--text-secondary);">{{ $usageCorrectionRequest->requestedBy->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Record Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Usage Record Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Amenity</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->tenantAmenityUsage->tenantAmenity->paidAmenity->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Usage Date</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->tenantAmenityUsage->usage_date->format('M j, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Unit Price</label>
                            <p class="text-sm" style="color: var(--text-primary);">₹{{ number_format($usageCorrectionRequest->tenantAmenityUsage->unit_price, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Recorded By</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->requestedBy->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Correction Request -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Correction Request</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Current Values -->
                        <div>
                            <h4 class="text-md font-medium mb-4" style="color: var(--text-primary);">Current Values</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Quantity</label>
                                    <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->original_quantity }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Notes</label>
                                    <p class="text-sm" style="color: var(--text-primary);">
                                        @if($usageCorrectionRequest->original_notes)
                                            {{ $usageCorrectionRequest->original_notes }}
                                        @else
                                            <span class="italic" style="color: var(--text-secondary);">No notes</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Total Amount</label>
                                    <p class="text-sm font-medium" style="color: var(--text-primary);">₹{{ number_format($usageCorrectionRequest->tenantAmenityUsage->unit_price * $usageCorrectionRequest->original_quantity, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Requested Values -->
                        <div>
                            <h4 class="text-md font-medium mb-4" style="color: var(--text-primary);">Requested Values</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Quantity</label>
                                    <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->requested_quantity }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Notes</label>
                                    <p class="text-sm" style="color: var(--text-primary);">
                                        @if($usageCorrectionRequest->requested_notes)
                                            {{ $usageCorrectionRequest->requested_notes }}
                                        @else
                                            <span class="italic" style="color: var(--text-secondary);">No notes</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Total Amount</label>
                                    <p class="text-sm font-medium" style="color: var(--text-primary);">₹{{ number_format($usageCorrectionRequest->tenantAmenityUsage->unit_price * $usageCorrectionRequest->requested_quantity, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reason for Correction -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Reason for Correction</h2>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->correction_reason }}</p>
                </div>
            </div>

            <!-- Admin Notes -->
            @if($usageCorrectionRequest->admin_notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Admin Notes</h2>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->admin_notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Request Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Request Status</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($usageCorrectionRequest->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($usageCorrectionRequest->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                <i class="fas fa-{{ $usageCorrectionRequest->status === 'pending' ? 'clock' : ($usageCorrectionRequest->status === 'approved' ? 'check' : 'times') }} mr-1"></i>
                                {{ $usageCorrectionRequest->status_display }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Requested At</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                        @if($usageCorrectionRequest->reviewed_at)
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Reviewed At</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->reviewed_at->format('M j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Reviewed By</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $usageCorrectionRequest->reviewedBy->name ?? 'Unknown' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($usageCorrectionRequest->status === 'pending')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Actions</h3>
                    <div class="space-y-3">
                        <button onclick="approveRequest()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-check mr-2"></i>
                            Approve Request
                        </button>
                        <button onclick="rejectRequest()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-times mr-2"></i>
                            Reject Request
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <div class="mt-2 px-7 py-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center">Approve Request</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                        Are you sure you want to approve this usage correction request?
                    </p>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <form id="approveForm" method="POST" action="{{ route('admin.usage-correction-requests.approve', $usageCorrectionRequest) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Admin Notes (Optional)
                        </label>
                        <textarea id="admin_notes" name="admin_notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="Optional notes about this approval..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeApproveModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Approve Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <div class="mt-2 px-7 py-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center">Reject Request</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                        Are you sure you want to reject this usage correction request?
                    </p>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <form id="rejectForm" method="POST" action="{{ route('admin.usage-correction-requests.reject', $usageCorrectionRequest) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="reject_admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rejection Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea id="reject_admin_notes" name="admin_notes" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Reject Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function approveRequest() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('approveForm').reset();
}

function rejectRequest() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectForm').reset();
}

// Close modals when clicking outside
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection
