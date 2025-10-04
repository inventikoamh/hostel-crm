@extends('layouts.app')

@section('title', 'Usage Record Details')

@php
    $title = 'Usage Record Details';
    $subtitle = 'View amenity usage record information';
    $showBackButton = true;
    $backUrl = route('amenity-usage.index');
@endphp

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Usage Record Details</h1>
            <p class="mt-1 text-sm" style="color: var(--text-secondary);">
                View amenity usage record information
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('amenity-usage.edit', $amenityUsage) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm">
                <i class="fas fa-edit mr-2"></i>
                Edit Record
            </a>
            <a href="{{ route('amenity-usage.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
               style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Records
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Usage Information -->
        <div class="lg:col-span-2">
            <div class="rounded-lg shadow-lg p-6 mb-6" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <h3 class="text-lg font-semibold mb-4 flex items-center" style="color: var(--text-primary);">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Usage Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Tenant</label>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">
                                {{ substr($amenityUsage->tenantAmenity->tenantProfile->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-medium" style="color: var(--text-primary);">
                                    {{ $amenityUsage->tenantAmenity->tenantProfile->user->name }}
                                </div>
                                <small style="color: var(--text-secondary);">{{ $amenityUsage->tenantAmenity->tenantProfile->user->email }}</small>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Amenity</label>
                        <div class="flex items-center">
                            <i class="fas fa-concierge-bell text-blue-600 mr-2"></i>
                            <div>
                                <div class="font-medium" style="color: var(--text-primary);">
                                    {{ $amenityUsage->tenantAmenity->paidAmenity->name }}
                                </div>
                                <small style="color: var(--text-secondary);">{{ $amenityUsage->tenantAmenity->paidAmenity->description }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Usage Date</label>
                        <div class="font-medium flex items-center" style="color: var(--text-primary);">
                            <i class="fas fa-calendar mr-2 text-blue-600"></i>
                            {{ $amenityUsage->usage_date->format('l, M j, Y') }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Quantity</label>
                        <div class="font-medium flex items-center" style="color: var(--text-primary);">
                            <i class="fas fa-hashtag mr-2 text-blue-600"></i>
                            {{ $amenityUsage->quantity }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Unit Price</label>
                        <div class="font-medium flex items-center" style="color: var(--text-primary);">
                            <i class="fas fa-rupee-sign mr-2 text-blue-600"></i>
                            {{ $amenityUsage->formatted_unit_price }}
                        </div>
                    </div>
                </div>

                @if($amenityUsage->notes)
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Notes</label>
                        <div class="p-4 rounded-lg" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color);">
                            <p class="mb-0" style="color: var(--text-primary);">{{ $amenityUsage->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary & Actions -->
        <div class="space-y-6">
            <!-- Amount Summary -->
            <div class="rounded-lg shadow-lg p-6" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Amount Summary</h3>

                <div class="flex justify-between items-center mb-3">
                    <span style="color: var(--text-secondary);">Unit Price:</span>
                    <span class="font-medium" style="color: var(--text-primary);">{{ $amenityUsage->formatted_unit_price }}</span>
                </div>

                <div class="flex justify-between items-center mb-3">
                    <span style="color: var(--text-secondary);">Quantity:</span>
                    <span class="font-medium" style="color: var(--text-primary);">{{ $amenityUsage->quantity }}</span>
                </div>

                <hr style="border-color: var(--border-color); margin: 1rem 0;">

                <div class="flex justify-between items-center">
                    <span class="font-medium" style="color: var(--text-primary);">Total Amount:</span>
                    <span class="font-bold text-green-600 text-lg">{{ $amenityUsage->formatted_total_amount }}</span>
                </div>
            </div>

            <!-- Record Information -->
            <div class="rounded-lg shadow-lg p-6" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Record Information</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Recorded by</label>
                    <div class="font-medium" style="color: var(--text-primary);">
                        {{ $amenityUsage->recordedBy->name ?? 'Unknown' }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Recorded on</label>
                    <div class="font-medium" style="color: var(--text-primary);">
                        {{ $amenityUsage->created_at->format('M j, Y g:i A') }}
                    </div>
                </div>

                @if($amenityUsage->updated_at != $amenityUsage->created_at)
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Last updated</label>
                        <div class="font-medium" style="color: var(--text-primary);">
                            {{ $amenityUsage->updated_at->format('M j, Y g:i A') }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="rounded-lg shadow-lg p-6" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Actions</h3>

                <div class="space-y-3">
                    <a href="{{ route('amenity-usage.edit', $amenityUsage) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Record
                    </a>

                    <button type="button"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-red-700 hover:bg-red-50 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            onclick="deleteRecord({{ $amenityUsage->id }})">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Record
                    </button>

                    <a href="{{ route('amenity-usage.create') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 text-blue-700 hover:bg-blue-50 rounded-lg font-medium text-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Record
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function deleteRecord(id) {
    if (!confirm('Are you sure you want to delete this usage record? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/amenity-usage/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            showMessage('success', data.message);
            setTimeout(() => {
                window.location.href = '{{ route("amenity-usage.index") }}';
            }, 1500);
        } else {
            showMessage('error', data.message || 'Failed to delete record');
        }
    } catch (error) {
        showMessage('error', 'Network error occurred while deleting record');
    }
}

function showMessage(type, message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm`;
    messageDiv.style.cssText = `background-color: ${type === 'success' ? '#10b981' : '#ef4444'}; color: white;`;
    messageDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span class="flex-1">${message}</span>
            <button type="button" class="ml-2 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(messageDiv);

    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}
</script>

@endsection
