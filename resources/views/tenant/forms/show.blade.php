@extends('tenant.layout')

@section('title', 'Form Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold" style="color: var(--text-primary);">{{ $tenantForm->form_type_display }}</h1>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">Form Number: {{ $tenantForm->form_number }}</p>
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
            <!-- Form Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Form Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Form Type</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $tenantForm->form_type_display }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Form Number</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $tenantForm->form_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Created Date</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $tenantForm->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Current Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($tenantForm->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @elseif($tenantForm->status === 'printed') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($tenantForm->status === 'signed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @endif">
                                {{ $tenantForm->status_display }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Your Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Your Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center" style="background-color: var(--bg-secondary);">
                            @if($tenantForm->tenant_photo_url)
                                <img src="{{ $tenantForm->tenant_photo_url }}" alt="Your Photo" class="w-16 h-16 rounded-full object-cover">
                            @else
                                <i class="fas fa-user text-2xl" style="color: var(--text-secondary);"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-lg font-medium" style="color: var(--text-primary);">{{ $tenantForm->tenantProfile->user->name }}</h4>
                            <p class="text-sm" style="color: var(--text-secondary);">{{ $tenantForm->tenantProfile->user->email }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h5 class="font-medium mb-3" style="color: var(--text-primary);">Personal Details</h5>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="font-medium" style="color: var(--text-secondary);">Phone:</span>
                                    <span style="color: var(--text-primary);">{{ $tenantForm->tenantProfile->phone ?? 'Not provided' }}</span>
                                </div>
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
            <!-- Status Timeline -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Status Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <!-- Created -->
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <i class="fas fa-plus text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm" style="color: var(--text-primary);">Form Created</p>
                                                <p class="text-xs" style="color: var(--text-secondary);">{{ $tenantForm->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- Printed -->
                            @if($tenantForm->printed_at)
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <i class="fas fa-print text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm" style="color: var(--text-primary);">Form Printed</p>
                                                <p class="text-xs" style="color: var(--text-secondary);">{{ $tenantForm->printed_at->format('M d, Y H:i') }}</p>
                                                @if($tenantForm->printedByUser)
                                                <p class="text-xs" style="color: var(--text-secondary);">by {{ $tenantForm->printedByUser->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif

                            <!-- Signed -->
                            @if($tenantForm->uploaded_at)
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <i class="fas fa-signature text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm" style="color: var(--text-primary);">Form Signed</p>
                                                <p class="text-xs" style="color: var(--text-secondary);">{{ $tenantForm->uploaded_at->format('M d, Y H:i') }}</p>
                                                @if($tenantForm->uploadedByUser)
                                                <p class="text-xs" style="color: var(--text-secondary);">by {{ $tenantForm->uploadedByUser->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
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
                        @if($tenantForm->isSigned())
                        <a href="{{ route('tenant.forms.signed', $tenantForm) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-download mr-2"></i>
                            Download Signed Form
                        </a>
                        @else
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg" style="background-color: var(--bg-secondary);">
                            <i class="fas fa-clock text-2xl text-gray-400 mb-2"></i>
                            <p class="text-sm" style="color: var(--text-secondary);">
                                @if($tenantForm->status === 'draft')
                                    Form is being prepared
                                @elseif($tenantForm->status === 'printed')
                                    Form is ready for signing
                                @else
                                    Form is being processed
                                @endif
                            </p>
                        </div>
                        @endif

                        <a href="{{ route('tenant.forms') }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Forms
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
