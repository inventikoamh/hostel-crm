@extends('layouts.app')

@section('title', 'Create Tenant Form')

@php
    $title = 'Create Tenant Form';
    $subtitle = 'Create a new tenant form for printing and signing';
    $showBackButton = true;
    $backUrl = route('admin.tenant-forms.index');
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium" style="color: var(--text-primary);">Create New Tenant Form</h3>
        </div>

        <form method="POST" action="{{ route('admin.tenant-forms.store') }}" class="p-6">
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
                            <option value="{{ $tenant->id }}" {{ $selectedTenant && $selectedTenant->id == $tenant->id ? 'selected' : '' }}>
                                {{ $tenant->user->name }} ({{ $tenant->user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('tenant_profile_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Type -->
                <div>
                    <label for="form_type" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Form Type <span class="text-red-500">*</span>
                    </label>
                    <select name="form_type" id="form_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="tenant_agreement" {{ $formType === 'tenant_agreement' ? 'selected' : '' }}>Tenant Agreement</option>
                        <option value="lease_agreement" {{ $formType === 'lease_agreement' ? 'selected' : '' }}>Lease Agreement</option>
                        <option value="rental_agreement" {{ $formType === 'rental_agreement' ? 'selected' : '' }}>Rental Agreement</option>
                        <option value="maintenance_form" {{ $formType === 'maintenance_form' ? 'selected' : '' }}>Maintenance Form</option>
                    </select>
                    @error('form_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Notes (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                              style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                              placeholder="Additional notes for this form...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
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
                <a href="{{ route('admin.tenant-forms.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Form
                </button>
            </div>
        </form>
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
