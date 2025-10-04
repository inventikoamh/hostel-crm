@extends('layouts.app')

@section('title', 'Tenant Form Details')

@php
    $title = 'Tenant Form Details';
    $subtitle = 'View and manage tenant form';
    $showBackButton = true;
    $backUrl = route('admin.tenant-forms.index');
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Form Header -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">{{ $tenantForm->form_type_display }}</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">Form Number: {{ $tenantForm->form_number }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($tenantForm->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                        @elseif($tenantForm->status === 'printed') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($tenantForm->status === 'signed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @endif">
                        <i class="fas fa-{{ $tenantForm->status === 'draft' ? 'edit' : ($tenantForm->status === 'printed' ? 'print' : ($tenantForm->status === 'signed' ? 'signature' : 'archive')) }} mr-1"></i>
                        {{ $tenantForm->status_display }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tenant Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Tenant Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center" style="background-color: var(--bg-secondary);">
                            @if($tenantForm->tenant_photo_url)
                                <img src="{{ $tenantForm->tenant_photo_url }}" alt="Tenant Photo" class="w-20 h-20 rounded-full object-cover">
                            @else
                                <i class="fas fa-user text-3xl" style="color: var(--text-secondary);"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-xl font-medium" style="color: var(--text-primary);">{{ $tenantForm->tenantProfile->user->name }}</h4>
                            <p class="text-sm" style="color: var(--text-secondary);">{{ $tenantForm->tenantProfile->user->email }}</p>
                            <p class="text-sm" style="color: var(--text-secondary);">{{ $tenantForm->tenantProfile->phone ?? 'No phone' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h5 class="font-medium mb-3" style="color: var(--text-primary);">Personal Details</h5>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Date of Birth:</span>
                                    <span style="color: var(--text-primary);">{{ $tenantForm->tenantProfile->date_of_birth?->format('M d, Y') ?? 'Not provided' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Occupation:</span>
                                    <span style="color: var(--text-primary);">{{ $tenantForm->tenantProfile->occupation ?? 'Not provided' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Company:</span>
                                    <span style="color: var(--text-primary);">{{ $tenantForm->tenantProfile->company ?? 'Not provided' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">ID Proof:</span>
                                    <span style="color: var(--text-primary);">{{ ucfirst($tenantForm->tenantProfile->id_proof_type ?? 'Not provided') }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h5 class="font-medium mb-3" style="color: var(--text-primary);">Rental Details</h5>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Move-in Date:</span>
                                    <span style="color: var(--text-primary);">{{ $tenantForm->tenantProfile->move_in_date?->format('M d, Y') ?? 'Not set' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Monthly Rent:</span>
                                    <span style="color: var(--text-primary);">₹{{ number_format($tenantForm->tenantProfile->monthly_rent ?? 0, 2) }}</span>
                                </div>
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Security Deposit:</span>
                                    <span style="color: var(--text-primary);">₹{{ number_format($tenantForm->tenantProfile->security_deposit ?? 0, 2) }}</span>
                                </div>
                                @if($tenantForm->tenantProfile->currentBed)
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Current Bed:</span>
                                    <span style="color: var(--text-primary);">
                                        {{ $tenantForm->tenantProfile->currentBed->room->hostel->name }} -
                                        Room {{ $tenantForm->tenantProfile->currentBed->room->room_number }},
                                        Bed {{ $tenantForm->tenantProfile->currentBed->bed_number }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Data -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Form Data</h3>
                </div>
                <div class="p-6">
                    @php
                        $formData = $tenantForm->form_data;
                    @endphp

                    <!-- Tenant Information -->
                    @if(isset($formData['tenant_info']))
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-3" style="color: var(--text-primary);">Tenant Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Name:</span>
                                <span style="color: var(--text-primary);">{{ $formData['tenant_info']['name'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Email:</span>
                                <span style="color: var(--text-primary);">{{ $formData['tenant_info']['email'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Phone:</span>
                                <span style="color: var(--text-primary);">{{ $formData['tenant_info']['phone'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Date of Birth:</span>
                                <span style="color: var(--text-primary);">{{ $formData['tenant_info']['date_of_birth'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Occupation:</span>
                                <span style="color: var(--text-primary);">{{ $formData['tenant_info']['occupation'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Company:</span>
                                <span style="color: var(--text-primary);">{{ $formData['tenant_info']['company'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">ID Proof Type:</span>
                                <span style="color: var(--text-primary);">{{ ucfirst($formData['tenant_info']['id_proof_type'] ?? 'N/A') }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">ID Proof Number:</span>
                                <span style="color: var(--text-primary);">{{ $formData['tenant_info']['id_proof_number'] ?? 'N/A' }}</span>
                            </div>
                            @if(isset($formData['tenant_info']['address']))
                            <div class="md:col-span-2">
                                <span class="font-medium" style="color: var(--text-secondary);">Address:</span>
                                <span style="color: var(--text-primary);">{{ $formData['tenant_info']['address'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Emergency Contact -->
                    @if(isset($formData['emergency_contact']) && $formData['emergency_contact']['name'])
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-3" style="color: var(--text-primary);">Emergency Contact</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Name:</span>
                                <span style="color: var(--text-primary);">{{ $formData['emergency_contact']['name'] }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Phone:</span>
                                <span style="color: var(--text-primary);">{{ $formData['emergency_contact']['phone'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Relation:</span>
                                <span style="color: var(--text-primary);">{{ $formData['emergency_contact']['relation'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Rental Information -->
                    @if(isset($formData['rental_info']))
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-3" style="color: var(--text-primary);">Rental Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Move-in Date:</span>
                                <span style="color: var(--text-primary);">{{ $formData['rental_info']['move_in_date'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Move-out Date:</span>
                                <span style="color: var(--text-primary);">{{ $formData['rental_info']['move_out_date'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Security Deposit:</span>
                                <span style="color: var(--text-primary);">₹{{ number_format($formData['rental_info']['security_deposit'] ?? 0, 2) }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Monthly Rent:</span>
                                <span style="color: var(--text-primary);">₹{{ number_format($formData['rental_info']['monthly_rent'] ?? 0, 2) }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Lease Start:</span>
                                <span style="color: var(--text-primary);">{{ $formData['rental_info']['lease_start_date'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Lease End:</span>
                                <span style="color: var(--text-primary);">{{ $formData['rental_info']['lease_end_date'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @if(isset($formData['current_bed']) && $formData['current_bed'])
                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg" style="background-color: var(--bg-secondary);">
                            <h5 class="font-medium mb-2" style="color: var(--text-primary);">Current Bed Assignment</h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Hostel:</span>
                                    <span style="color: var(--text-primary);">{{ $formData['current_bed']['hostel_name'] }}</span>
                                </div>
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Room:</span>
                                    <span style="color: var(--text-primary);">{{ $formData['current_bed']['room_number'] }}</span>
                                </div>
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Bed:</span>
                                    <span style="color: var(--text-primary);">{{ $formData['current_bed']['bed_number'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Billing Information -->
                    @if(isset($formData['billing_info']))
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-3" style="color: var(--text-primary);">Billing Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Billing Cycle:</span>
                                <span style="color: var(--text-primary);">{{ ucfirst($formData['billing_info']['billing_cycle'] ?? 'Monthly') }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Billing Day:</span>
                                <span style="color: var(--text-primary);">{{ $formData['billing_info']['billing_day'] ?? 1 }} of each month</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Next Billing:</span>
                                <span style="color: var(--text-primary);">{{ $formData['billing_info']['next_billing_date'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Form Metadata -->
                    @if(isset($formData['form_metadata']))
                    <div class="border-t pt-4" style="border-color: var(--border-color);">
                        <h4 class="text-md font-medium mb-3" style="color: var(--text-primary);">Form Metadata</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Created At:</span>
                                <span style="color: var(--text-primary);">{{ $formData['form_metadata']['created_at'] ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium" style="color: var(--text-secondary);">Created By:</span>
                                <span style="color: var(--text-primary);">{{ $formData['form_metadata']['created_by'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            @if($tenantForm->notes)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Notes</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm" style="color: var(--text-primary);">{{ $tenantForm->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Form Status -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Form Status</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Current Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($tenantForm->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @elseif($tenantForm->status === 'printed') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($tenantForm->status === 'signed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @endif">
                                <i class="fas fa-{{ $tenantForm->status === 'draft' ? 'edit' : ($tenantForm->status === 'printed' ? 'print' : ($tenantForm->status === 'signed' ? 'signature' : 'archive')) }} mr-1"></i>
                                {{ $tenantForm->status_display }}
                            </span>
                        </div>

                        @if($tenantForm->printed_at)
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Printed At</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $tenantForm->printed_at->format('M d, Y H:i') }}</p>
                            @if($tenantForm->printedByUser)
                            <p class="text-xs" style="color: var(--text-secondary);">by {{ $tenantForm->printedByUser->name }}</p>
                            @endif
                        </div>
                        @endif

                        @if($tenantForm->uploaded_at)
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Signed At</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $tenantForm->uploaded_at->format('M d, Y H:i') }}</p>
                            @if($tenantForm->uploadedByUser)
                            <p class="text-xs" style="color: var(--text-secondary);">by {{ $tenantForm->uploadedByUser->name }}</p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Actions</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('admin.tenant-forms.print', $tenantForm) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-print mr-2"></i>
                            Print Form
                        </a>

                        <a href="{{ route('admin.tenant-forms.download', $tenantForm) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-download mr-2"></i>
                            Download PDF
                        </a>

                        @if($tenantForm->status === 'printed' || $tenantForm->status === 'draft')
                        <a href="{{ route('admin.tenant-forms.upload', $tenantForm) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Signed Form
                        </a>
                        @endif

                        @if($tenantForm->isSigned())
                        <a href="{{ route('admin.tenant-forms.view-signed', $tenantForm) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-eye mr-2"></i>
                            View Signed Form
                        </a>
                        @endif

                        <form method="POST" action="{{ route('admin.tenant-forms.destroy', $tenantForm) }}"
                              onsubmit="return confirm('Are you sure you want to delete this form?')" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Form
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
