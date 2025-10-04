@extends('tenant.layout')

@section('title', 'My Bed Information')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold" style="color: var(--text-primary);">My Bed Information</h2>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">Details about your assigned bed and room</p>
            </div>
        </div>
    </div>

    @if($tenantProfile->bed)
        <!-- Bed Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">Bed Details</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Bed Number:</span>
                            <span class="text-sm" style="color: var(--text-primary);">{{ $tenantProfile->bed->bed_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Room Number:</span>
                            <span class="text-sm" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->room_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Floor:</span>
                            <span class="text-sm" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->floor }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Bed Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <i class="fas fa-check-circle mr-1"></i>
                                Occupied
                            </span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Hostel:</span>
                            <span class="text-sm" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->hostel->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Room Capacity:</span>
                            <span class="text-sm" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->bed_capacity }} beds</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Rent per Bed:</span>
                            <span class="text-sm" style="color: var(--text-primary);">₹{{ number_format($tenantProfile->bed->room->rent_per_bed, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Room Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <i class="fas fa-home mr-1"></i>
                                {{ ucfirst($tenantProfile->bed->room->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hostel Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">Hostel Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Hostel Name:</span>
                            <p class="text-sm mt-1" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->hostel->name }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Address:</span>
                            <p class="text-sm mt-1" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->hostel->address }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Phone:</span>
                            <p class="text-sm mt-1" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->hostel->phone }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Email:</span>
                            <p class="text-sm mt-1" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->hostel->email }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ ucfirst($tenantProfile->bed->room->hostel->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($tenantProfile->bed->room->hostel->amenities)
                    <div class="mt-6">
                        <span class="text-sm font-medium" style="color: var(--text-secondary);">Available Amenities:</span>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($tenantProfile->bed->room->hostel->amenities as $amenity)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <i class="fas fa-check mr-1"></i>
                                    {{ $amenity }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tenancy Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">Tenancy Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Move-in Date:</span>
                            <span class="text-sm" style="color: var(--text-primary);">{{ $tenantProfile->move_in_date ? $tenantProfile->move_in_date->format('M d, Y') : 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Billing Cycle:</span>
                            <span class="text-sm" style="color: var(--text-primary);">{{ ucfirst($tenantProfile->billing_cycle) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Rent Amount:</span>
                            <span class="text-sm" style="color: var(--text-primary);">₹{{ number_format($tenantProfile->rent_amount, 2) }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Security Deposit:</span>
                            <span class="text-sm" style="color: var(--text-primary);">₹{{ number_format($tenantProfile->security_deposit, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium" style="color: var(--text-secondary);">Tenant Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ ucfirst($tenantProfile->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Bed Assigned -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="p-12 text-center">
                <i class="fas fa-bed text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium mb-2" style="color: var(--text-primary);">No Bed Assigned</h3>
                <p class="text-sm" style="color: var(--text-secondary);">You don't have a bed assigned yet. Please contact the hostel administrator.</p>
            </div>
        </div>
    @endif
</div>
@endsection
