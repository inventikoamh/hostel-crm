@extends('layouts.app')

@section('title', 'Request Document')

@php
    $title = 'Request Document';
    $subtitle = 'Create a new document request for a tenant';
    $showBackButton = true;
    $backUrl = route('admin.tenant-documents.index');
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium" style="color: var(--text-primary);">Document Request Details</h3>
        </div>

        <form method="POST" action="{{ route('admin.tenant-documents.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tenant Selection -->
                <div class="md:col-span-2">
                    <label for="tenant_profile_id" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Select Tenant <span class="text-red-500">*</span>
                    </label>
                    <select name="tenant_profile_id" id="tenant_profile_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select a tenant...</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}" {{ (old('tenant_profile_id') == $tenant->id || $selectedTenant?->id == $tenant->id) ? 'selected' : '' }}>
                                {{ $tenant->user->name }} ({{ $tenant->user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('tenant_profile_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Type -->
                <div>
                    <label for="document_type" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Document Type <span class="text-red-500">*</span>
                    </label>
                    <select name="document_type" id="document_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select document type...</option>
                        <option value="aadhar_card" {{ (old('document_type') == 'aadhar_card' || $documentType == 'aadhar_card') ? 'selected' : '' }}>Aadhar Card</option>
                        <option value="pan_card" {{ (old('document_type') == 'pan_card' || $documentType == 'pan_card') ? 'selected' : '' }}>PAN Card</option>
                        <option value="student_id" {{ (old('document_type') == 'student_id' || $documentType == 'student_id') ? 'selected' : '' }}>Student ID</option>
                        <option value="tenant_agreement" {{ (old('document_type') == 'tenant_agreement' || $documentType == 'tenant_agreement') ? 'selected' : '' }}>Tenant Agreement</option>
                        <option value="lease_agreement" {{ (old('document_type') == 'lease_agreement' || $documentType == 'lease_agreement') ? 'selected' : '' }}>Lease Agreement</option>
                        <option value="rental_agreement" {{ (old('document_type') == 'rental_agreement' || $documentType == 'rental_agreement') ? 'selected' : '' }}>Rental Agreement</option>
                        <option value="maintenance_form" {{ (old('document_type') == 'maintenance_form' || $documentType == 'maintenance_form') ? 'selected' : '' }}>Maintenance Form</option>
                        <option value="identity_proof" {{ (old('document_type') == 'identity_proof' || $documentType == 'identity_proof') ? 'selected' : '' }}>Identity Proof</option>
                        <option value="address_proof" {{ (old('document_type') == 'address_proof' || $documentType == 'address_proof') ? 'selected' : '' }}>Address Proof</option>
                        <option value="income_proof" {{ (old('document_type') == 'income_proof' || $documentType == 'income_proof') ? 'selected' : '' }}>Income Proof</option>
                        <option value="other" {{ (old('document_type') == 'other' || $documentType == 'other') ? 'selected' : '' }}>Other Document</option>
                    </select>
                    @error('document_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Request Type -->
                <div>
                    <label for="request_type" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Request Type <span class="text-red-500">*</span>
                    </label>
                    <select name="request_type" id="request_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select request type...</option>
                        <option value="tenant_upload" {{ old('request_type') == 'tenant_upload' ? 'selected' : '' }}>Tenant Upload (Request tenant to upload)</option>
                        <option value="admin_upload" {{ old('request_type') == 'admin_upload' ? 'selected' : '' }}>Admin Upload (Admin will upload)</option>
                    </select>
                    @error('request_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" id="priority" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select priority...</option>
                        <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Low</option>
                        <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>Medium</option>
                        <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expiry Date -->
                <div>
                    <label for="expiry_date" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Expiry Date
                    </label>
                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" min="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    @error('expiry_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Required Document -->
                <div class="flex items-center">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="is_required" id="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_required" class="font-medium" style="color: var(--text-primary);">
                            Required Document
                        </label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Description
                </label>
                <textarea name="description" id="description" rows="3" placeholder="Describe what this document is for..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                          style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Internal Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Internal Notes
                </label>
                <textarea name="notes" id="notes" rows="2" placeholder="Internal notes (not visible to tenant)..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                          style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tenant Preview -->
            @if($selectedTenant)
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg" style="background-color: var(--bg-secondary);">
                <h4 class="text-md font-medium mb-3" style="color: var(--text-primary);">Selected Tenant Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium" style="color: var(--text-secondary);">Name:</span>
                        <span style="color: var(--text-primary);">{{ $selectedTenant->user->name }}</span>
                    </div>
                    <div>
                        <span class="font-medium" style="color: var(--text-secondary);">Email:</span>
                        <span style="color: var(--text-primary);">{{ $selectedTenant->user->email }}</span>
                    </div>
                    <div>
                        <span class="font-medium" style="color: var(--text-secondary);">Phone:</span>
                        <span style="color: var(--text-primary);">{{ $selectedTenant->phone ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="font-medium" style="color: var(--text-secondary);">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($selectedTenant->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($selectedTenant->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                            {{ ucfirst($selectedTenant->status) }}
                        </span>
                    </div>
                    @if($selectedTenant->currentBed)
                    <div class="md:col-span-2">
                        <span class="font-medium" style="color: var(--text-secondary);">Current Bed:</span>
                        <span style="color: var(--text-primary);">
                            {{ $selectedTenant->currentBed->room->hostel->name }} -
                            Room {{ $selectedTenant->currentBed->room->room_number }},
                            Bed {{ $selectedTenant->currentBed->bed_number }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.tenant-documents.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Document Request
                </button>
            </div>
                    </form>
                </div>
            </div>
    </div>
</div>

<script>
// Auto-select tenant if provided in URL
document.addEventListener('DOMContentLoaded', function() {
    const tenantSelect = document.getElementById('tenant_profile_id');
    const urlParams = new URLSearchParams(window.location.search);
    const tenantId = urlParams.get('tenant_id');

    if (tenantId) {
        tenantSelect.value = tenantId;
    }
});
</script>
@endsection
