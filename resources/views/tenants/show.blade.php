@extends('layouts.app')

@section('title', $tenant->user->name . ' - Tenant Details')

@php
    $title = $tenant->user->name;
    $subtitle = 'Tenant Profile and Information';
    $showBackButton = true;
    $backUrl = route('tenants.index');
    $profile = $tenant;
    $currentBed = $tenant->currentBed;
    $room = $currentBed ? $currentBed->room : null;
    $hostel = $room ? $room->hostel : null;
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Basic Information</h3>
                    @if($profile->is_verified)
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full flex items-center gap-1">
                            <i class="fas fa-check-circle"></i>
                            Verified
                        </span>
                    @else
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full flex items-center gap-1">
                            <i class="fas fa-clock"></i>
                            Unverified
                        </span>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Full Name</label>
                        <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $tenant->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Email</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $tenant->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Phone</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Date of Birth</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">
                            {{ $profile->date_of_birth ? $profile->date_of_birth->format('M j, Y') : 'Not provided' }}
                            @if($profile->age)
                                <span class="text-gray-500">({{ $profile->age }} years old)</span>
                            @endif
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Address</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->address ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Professional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Occupation</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->occupation ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Company</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->company ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- ID Proof Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">ID Proof Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">ID Proof Type</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->id_proof_type_display }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">ID Proof Number</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->id_proof_number ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Contact Name</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->emergency_contact_name ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Contact Phone</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->emergency_contact_phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Relationship</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->emergency_contact_relation ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Current Assignment -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Current Assignment</h3>
                @if($currentBed && $room && $hostel)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Hostel</label>
                            <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $hostel->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Room</label>
                            <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $room->room_number }} ({{ $room->floor }})</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Bed</label>
                            <p class="mt-1 text-sm" style="color: var(--text-primary);">Bed {{ $currentBed->bed_number }} ({{ $currentBed->bed_type_display }})</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Monthly Rent</label>
                            <p class="mt-1 text-sm font-semibold text-green-600">₹{{ number_format($currentBed->monthly_rent ?? $profile->monthly_rent, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('rooms.show', $room->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            View Room Details
                        </a>
                        <a href="{{ route('map.hostel', ['hostel' => $hostel->id, 'floor' => $room->floor]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            <i class="fas fa-map mr-1"></i>
                            View on Map
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-bed text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600" style="color: var(--text-secondary);">No bed assigned</p>
                        <p class="text-sm text-gray-500 mt-1">This tenant is not currently assigned to any bed.</p>
                    </div>
                @endif
            </div>

            <!-- Paid Services -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Paid Services</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('tenant-amenities.create', ['tenant_id' => $tenant->id]) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Add Service
                        </a>
                        <a href="{{ route('invoices.create', ['tenant_id' => $tenant->id, 'type' => 'rent']) }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-file-invoice"></i>
                            Generate Rent Invoice
                        </a>
                        <a href="{{ route('invoices.create', ['tenant_id' => $tenant->id, 'type' => 'amenities']) }}"
                           class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-concierge-bell"></i>
                            Generate Amenities Invoice
                        </a>
                    </div>
                </div>

                @if($profile->tenantAmenities && $profile->tenantAmenities->count() > 0)
                    <div class="space-y-4">
                        <!-- Services Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <i class="fas fa-list text-blue-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-900">Total Services</p>
                                        <p class="text-xl font-bold text-blue-600">{{ $profile->tenantAmenities->count() }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-900">Active Services</p>
                                        <p class="text-xl font-bold text-green-600">{{ $profile->tenantAmenities->where('status', 'active')->count() }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-purple-100 rounded-lg">
                                        <i class="fas fa-rupee-sign text-purple-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-purple-900">Monthly Cost</p>
                                        <p class="text-xl font-bold text-purple-600">
                                            ₹{{ number_format($profile->tenantAmenities->where('status', 'active')->filter(function($ta) { return $ta->paidAmenity->billing_type === 'monthly'; })->sum('effective_price'), 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Services List -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50" style="background-color: var(--bg-secondary);">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Service</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Category</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Billing</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Price</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Usage</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y" style="divide-color: var(--border-color);">
                                    @foreach($profile->tenantAmenities as $tenantAmenity)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200" style="hover:background-color: var(--bg-secondary);">
                                            <td class="px-4 py-4">
                                                <div class="flex items-center">
                                                    <i class="{{ $tenantAmenity->paidAmenity->icon_class }} mr-3 text-gray-600"></i>
                                                    <div>
                                                        <div class="text-sm font-medium" style="color: var(--text-primary);">
                                                            {{ $tenantAmenity->paidAmenity->name }}
                                                        </div>
                                                        @if($tenantAmenity->paidAmenity->description)
                                                            <div class="text-xs" style="color: var(--text-secondary);">
                                                                {{ Str::limit($tenantAmenity->paidAmenity->description, 40) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    {{ $tenantAmenity->paidAmenity->category_display }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $tenantAmenity->paidAmenity->billing_type === 'monthly' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $tenantAmenity->paidAmenity->billing_type_display }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="text-sm font-medium" style="color: var(--text-primary);">
                                                    {{ $tenantAmenity->formatted_effective_price }}
                                                </div>
                                                @if($tenantAmenity->custom_price)
                                                    <div class="text-xs text-orange-600">Custom Price</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $tenantAmenity->status_badge['class'] }}">
                                                    {{ $tenantAmenity->status_badge['text'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                @if($tenantAmenity->paidAmenity->billing_type === 'daily')
                                                    <div class="text-sm" style="color: var(--text-primary);">
                                                        {{ $tenantAmenity->usageRecords->count() }} records
                                                    </div>
                                                    <div class="text-xs" style="color: var(--text-secondary);">
                                                        ₹{{ number_format($tenantAmenity->usageRecords->sum('total_amount'), 2) }}
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-500">Monthly billing</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('tenant-amenities.show', $tenantAmenity) }}"
                                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('tenant-amenities.edit', $tenantAmenity) }}"
                                                       class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                       title="Edit Assignment">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($tenantAmenity->paidAmenity->billing_type === 'daily')
                                                        <button onclick="showUsageModal({{ $tenantAmenity->id }})"
                                                                class="text-purple-600 hover:text-purple-900 transition-colors duration-200"
                                                                title="Record Usage">
                                                            <i class="fas fa-plus-circle"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-concierge-bell text-4xl text-gray-300 mb-4"></i>
                        <h4 class="text-lg font-medium text-gray-500 mb-2">No Paid Services</h4>
                        <p class="text-gray-400 mb-4">This tenant hasn't been assigned any paid services yet.</p>
                        <a href="{{ route('tenant-amenities.create', ['tenant_id' => $tenant->id]) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Add First Service
                        </a>
                    </div>
                @endif
            </div>

            <!-- Billing Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Billing Summary</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('invoices.index', ['tenant_id' => $tenant->id]) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-file-invoice"></i>
                            View Invoices
                        </a>
                        <a href="{{ route('payments.index', ['tenant_id' => $tenant->id]) }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-credit-card"></i>
                            View Payments
                        </a>
                    </div>
                </div>

                @php
                    $invoices = $tenant->invoices ?? collect();
                    $totalInvoices = $invoices->count();
                    $totalAmount = $invoices->sum('total_amount');
                    $paidAmount = $invoices->sum('paid_amount');
                    $balanceAmount = $invoices->sum('balance_amount');
                    $overdueInvoices = $invoices->where('status', 'overdue')->count();
                @endphp

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg" style="background-color: var(--bg-secondary);">
                        <p class="text-2xl font-bold text-blue-600">{{ $totalInvoices }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">Total Invoices</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg" style="background-color: var(--bg-secondary);">
                        <p class="text-2xl font-bold text-purple-600">₹{{ number_format($totalAmount, 0) }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">Total Amount</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg" style="background-color: var(--bg-secondary);">
                        <p class="text-2xl font-bold text-green-600">₹{{ number_format($paidAmount, 0) }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">Paid Amount</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg" style="background-color: var(--bg-secondary);">
                        <p class="text-2xl font-bold text-red-600">₹{{ number_format($balanceAmount, 0) }}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">Balance Due</p>
                    </div>
                </div>

                @if($overdueInvoices > 0)
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            <span class="text-sm font-medium text-red-800">
                                {{ $overdueInvoices }} overdue invoice(s) require attention
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Documents</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.tenant-documents.create', ['tenant_id' => $tenant->id]) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Request Document
                        </a>
                        <a href="{{ route('admin.tenant-documents.index', ['tenant_id' => $tenant->id]) }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-list"></i>
                            View All
                        </a>
                    </div>
                </div>

                @if($tenant->tenantDocuments && $tenant->tenantDocuments->count() > 0)
                    <div class="space-y-3">
                        @foreach($tenant->tenantDocuments->take(5) as $document)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-file-alt text-blue-600 text-lg"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium" style="color: var(--text-primary);">
                                        {{ $document->document_type_display }}
                                    </div>
                                    <div class="text-xs" style="color: var(--text-secondary);">
                                        {{ $document->document_number }} • {{ $document->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $document->status_badge_color }}-100 text-{{ $document->status_badge_color }}-800 dark:bg-{{ $document->status_badge_color }}-900 dark:text-{{ $document->status_badge_color }}-200">
                                    {{ $document->status_display }}
                                </span>
                                @if($document->approval_status !== 'pending')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $document->approval_status_badge_color }}-100 text-{{ $document->approval_status_badge_color }}-800 dark:bg-{{ $document->approval_status_badge_color }}-900 dark:text-{{ $document->approval_status_badge_color }}-200">
                                    {{ $document->approval_status_display }}
                                </span>
                                @endif
                                <a href="{{ route('admin.tenant-documents.show', $document) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach

                        @if($tenant->tenantDocuments->count() > 5)
                        <div class="text-center pt-2">
                            <a href="{{ route('admin.tenant-documents.index', ['tenant_id' => $tenant->id]) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View {{ $tenant->tenantDocuments->count() - 5 }} more documents →
                            </a>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-file-alt text-4xl text-gray-400 dark:text-gray-500 mb-3"></i>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Documents</h4>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">This tenant doesn't have any document requests yet.</p>
                        <a href="{{ route('admin.tenant-documents.create', ['tenant_id' => $tenant->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-2"></i>
                            Request First Document
                        </a>
                    </div>
                @endif
            </div>

            <!-- Notes -->
            @if($profile->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Notes</h3>
                    <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                        <p class="text-sm whitespace-pre-wrap" style="color: var(--text-primary);">{{ $profile->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status & Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Status & Actions</h3>

                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Current Status</label>
                    <div class="mt-1">
                        <x-status-badge :status="$profile->status" />
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('tenants.edit', $tenant->id) }}"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-edit"></i>
                        Edit Tenant
                    </a>

                    @if(!$profile->is_verified)
                        <form action="{{ route('tenants.verify', $tenant->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                Mark as Verified
                            </button>
                        </form>
                    @endif

                    @if($profile->status === 'active')
                        <button onclick="showMoveOutModal()"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-sign-out-alt"></i>
                            Move Out
                        </button>
                    @endif

                    <button onclick="deleteTenant('{{ route('tenants.destroy', $tenant->id) }}')"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i>
                        Delete Tenant
                    </button>
                </div>
            </div>

            <!-- Lease Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Lease Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Move-in Date</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->move_in_date ? $profile->move_in_date->format('M j, Y') : 'Not set' }}</p>
                    </div>
                    @if($profile->move_out_date)
                        <div>
                            <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Move-out Date</label>
                            <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->move_out_date->format('M j, Y') }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Tenancy Duration</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->tenancy_duration_human }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Lease Period</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">
                            @if($profile->lease_start_date && $profile->lease_end_date)
                                {{ $profile->lease_start_date->format('M j, Y') }} - {{ $profile->lease_end_date->format('M j, Y') }}
                                @if($profile->is_lease_expired)
                                    <span class="text-red-600 font-medium">(Expired)</span>
                                @elseif($profile->days_until_lease_expiry !== null && $profile->days_until_lease_expiry <= 30)
                                    <span class="text-orange-600 font-medium">(Expires in {{ $profile->days_until_lease_expiry }} days)</span>
                                @endif
                            @else
                                Not set
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Security Deposit</label>
                        <p class="mt-1 text-sm font-semibold text-blue-600">₹{{ number_format($profile->security_deposit ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Monthly Rent</label>
                        <p class="mt-1 text-sm font-semibold text-green-600">₹{{ number_format($profile->monthly_rent ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Verification Info -->
            @if($profile->is_verified && $profile->verified_at)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Verification Info</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Verified At</label>
                            <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->verified_at->format('M j, Y g:i A') }}</p>
                        </div>
                        @if($profile->verifiedBy)
                            <div>
                                <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Verified By</label>
                                <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $profile->verifiedBy->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Usage Recording Modal -->
    <div id="usageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full" style="background-color: var(--card-bg);">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Record Usage</h3>
                        <button onclick="closeUsageModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="usageForm" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Usage Date</label>
                                <input type="date" name="usage_date" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                       max="{{ date('Y-m-d') }}">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Quantity</label>
                                <input type="number" name="quantity" min="1" value="1" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Notes (Optional)</label>
                                <textarea name="notes" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                          placeholder="Any additional notes..."></textarea>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="button" onclick="closeUsageModal()"
                                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                Record Usage
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Move Out Modal -->
    <div id="moveOutModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[1000] hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Move Out Tenant</h3>
            <form action="{{ route('tenants.move-out', $tenant->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="move_out_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Move-out Date *</label>
                        <input type="date" id="move_out_date" name="move_out_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label for="move_out_notes" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Notes</label>
                        <textarea id="move_out_notes" name="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="hideMoveOutModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200">Move Out</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function deleteTenant(url) {
            if (confirm('Are you sure you want to delete this tenant? This action cannot be undone and will also release any assigned bed.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function showMoveOutModal() {
            document.getElementById('moveOutModal').classList.remove('hidden');
            document.getElementById('move_out_date').value = new Date().toISOString().split('T')[0];
        }

        function hideMoveOutModal() {
            document.getElementById('moveOutModal').classList.add('hidden');
        }

        function showUsageModal(tenantAmenityId) {
            const modal = document.getElementById('usageModal');
            const form = document.getElementById('usageForm');

            form.action = `/tenant-amenities/${tenantAmenityId}/usage`;
            modal.classList.remove('hidden');
        }

        function closeUsageModal() {
            document.getElementById('usageModal').classList.add('hidden');
        }

        // Close usage modal when clicking outside
        document.getElementById('usageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUsageModal();
            }
        });
    </script>
@endsection
