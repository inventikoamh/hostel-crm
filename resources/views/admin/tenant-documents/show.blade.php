@extends('layouts.app')

@section('title', 'Document Details')

@php
    $title = 'Document Details';
    $subtitle = $tenantDocument->document_number . ' - ' . $tenantDocument->document_type_display;
    $showBackButton = true;
    $backUrl = route('admin.tenant-documents.index');
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Document Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Document Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-medium" style="color: var(--text-secondary);">Document Number:</span>
                                <p class="text-lg font-semibold" style="color: var(--text-primary);">{{ $tenantDocument->document_number }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium" style="color: var(--text-secondary);">Document Type:</span>
                                <p class="text-lg" style="color: var(--text-primary);">{{ $tenantDocument->document_type_display }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium" style="color: var(--text-secondary);">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $tenantDocument->status_badge_color }}-100 text-{{ $tenantDocument->status_badge_color }}-800 dark:bg-{{ $tenantDocument->status_badge_color }}-900 dark:text-{{ $tenantDocument->status_badge_color }}-200">
                                    {{ $tenantDocument->status_display }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium" style="color: var(--text-secondary);">Approval Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $tenantDocument->approval_status_badge_color }}-100 text-{{ $tenantDocument->approval_status_badge_color }}-800 dark:bg-{{ $tenantDocument->approval_status_badge_color }}-900 dark:text-{{ $tenantDocument->approval_status_badge_color }}-200">
                                    {{ $tenantDocument->approval_status_display }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-medium" style="color: var(--text-secondary);">Request Type:</span>
                                <p class="text-lg" style="color: var(--text-primary);">{{ ucfirst(str_replace('_', ' ', $tenantDocument->request_type)) }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium" style="color: var(--text-secondary);">Priority:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $tenantDocument->priority_badge_color }}-100 text-{{ $tenantDocument->priority_badge_color }}-800 dark:bg-{{ $tenantDocument->priority_badge_color }}-900 dark:text-{{ $tenantDocument->priority_badge_color }}-200">
                                    {{ $tenantDocument->priority_display }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium" style="color: var(--text-secondary);">Required:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tenantDocument->is_required ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ $tenantDocument->is_required ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            @if($tenantDocument->expiry_date)
                            <div>
                                <span class="text-sm font-medium" style="color: var(--text-secondary);">Expiry Date:</span>
                                <p class="text-lg" style="color: var(--text-primary);">{{ $tenantDocument->expiry_date->format('M d, Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($tenantDocument->description)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium" style="color: var(--text-secondary);">Description:</span>
                        <p class="mt-2 text-lg" style="color: var(--text-primary);">{{ $tenantDocument->description }}</p>
                    </div>
                    @endif

                    @if($tenantDocument->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium" style="color: var(--text-secondary);">Internal Notes:</span>
                        <p class="mt-2 text-lg" style="color: var(--text-primary);">{{ $tenantDocument->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tenant Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Tenant Information</h2>
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center" style="background-color: var(--bg-secondary);">
                            @if($tenantDocument->tenantProfile->user->avatar)
                                <img src="{{ asset('storage/' . $tenantDocument->tenantProfile->user->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover">
                            @else
                                <i class="fas fa-user text-2xl" style="color: var(--text-secondary);"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold" style="color: var(--text-primary);">{{ $tenantDocument->tenantProfile->user->name }}</h3>
                            <p class="text-sm" style="color: var(--text-secondary);">{{ $tenantDocument->tenantProfile->user->email }}</p>
                            <p class="text-sm" style="color: var(--text-secondary);">{{ $tenantDocument->tenantProfile->phone ?? 'No phone provided' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($tenantDocument->tenantProfile->currentBed)
                        <div>
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Current Bed:</span>
                            <p class="text-lg" style="color: var(--text-primary);">
                                {{ $tenantDocument->tenantProfile->currentBed->room->hostel->name }} -
                                Room {{ $tenantDocument->tenantProfile->currentBed->room->room_number }},
                                Bed {{ $tenantDocument->tenantProfile->currentBed->bed_number }}
                            </p>
                        </div>
                        @endif
                        <div>
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Move-in Date:</span>
                            <p class="text-lg" style="color: var(--text-primary);">
                                {{ $tenantDocument->tenantProfile->move_in_date ? $tenantDocument->tenantProfile->move_in_date->format('M d, Y') : 'Not set' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Actions -->
            @if($tenantDocument->request_type === 'tenant_upload' && $tenantDocument->status === 'uploaded' && $tenantDocument->approval_status === 'pending')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Document Review</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">This document has been uploaded by the tenant and is waiting for your approval.</p>
                    <div class="flex space-x-3">
                        <form action="{{ route('admin.tenant-documents.approve', $tenantDocument) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-check mr-2"></i>
                                Approve Document
                            </button>
                        </form>
                        <button type="button" onclick="showRejectModal()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-times mr-2"></i>
                            Reject Document
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Document Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Document Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium" style="color: var(--text-primary);">Document Created</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tenantDocument->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($tenantDocument->printed_at)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-3 h-3 bg-cyan-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium" style="color: var(--text-primary);">Document Printed</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tenantDocument->printed_at->format('M d, Y H:i') }}</p>
                                @if($tenantDocument->printedByUser)
                                <p class="text-xs text-gray-500 dark:text-gray-500">by {{ $tenantDocument->printedByUser->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($tenantDocument->uploaded_at_admin)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium" style="color: var(--text-primary);">Document Uploaded</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tenantDocument->uploaded_at_admin->format('M d, Y H:i') }}</p>
                                @if($tenantDocument->uploadedByAdmin)
                                <p class="text-xs text-gray-500 dark:text-gray-500">by {{ $tenantDocument->uploadedByAdmin->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($tenantDocument->approved_at)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium" style="color: var(--text-primary);">Document {{ ucfirst($tenantDocument->approval_status) }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tenantDocument->approved_at->format('M d, Y H:i') }}</p>
                                @if($tenantDocument->approvedByUser)
                                <p class="text-xs text-gray-500 dark:text-gray-500">by {{ $tenantDocument->approvedByUser->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($tenantDocument->status === 'draft')
                            <a href="{{ route('admin.tenant-documents.print', $tenantDocument) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-print mr-2"></i>
                                Print Document
                            </a>
                        @endif

                        @if($tenantDocument->request_type === 'admin_upload' && !$tenantDocument->document_path)
                            <a href="{{ route('admin.tenant-documents.upload', $tenantDocument) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-upload mr-2"></i>
                                Upload Document
                            </a>
                        @endif

                        @if($tenantDocument->document_path)
                            <a href="{{ route('admin.tenant-documents.view-signed', $tenantDocument) }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-700 focus:bg-cyan-700 active:bg-cyan-900 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-eye mr-2"></i>
                                View Document
                            </a>
                            <a href="{{ route('admin.tenant-documents.download', $tenantDocument) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-download mr-2"></i>
                                Download PDF
                            </a>
                        @endif

                        <button type="button" onclick="deleteDocument()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Document
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">Reject Document</h3>
                <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.tenant-documents.reject', $tenantDocument) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Rejection Reason</label>
                    <textarea name="rejection_reason" id="rejection_reason" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" rows="3" placeholder="Please provide a reason for rejecting this document..." required></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Reject Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">Delete Document</h3>
                <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <p style="color: var(--text-primary);">Are you sure you want to delete this document? This action cannot be undone.</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
                <form action="{{ route('admin.tenant-documents.destroy', $tenantDocument) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete Document
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function deleteDocument() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const rejectModal = document.getElementById('rejectModal');
    const deleteModal = document.getElementById('deleteModal');

    if (event.target === rejectModal) {
        closeRejectModal();
    }

    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});
</script>
@endsection
