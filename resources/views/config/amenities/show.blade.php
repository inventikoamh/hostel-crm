@extends('layouts.app')

@section('title', $amenity->name)

@php
    $title = $amenity->name;
    $subtitle = 'Amenity Details';
    $showBackButton = true;
    $backUrl = route('config.amenities.index');
@endphp

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Name</label>
                    <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $amenity->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Status</label>
                    <div class="mt-1">
                        <x-status-badge :status="$amenity->is_active ? 'active' : 'inactive'" />
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Icon</label>
                    <div class="mt-1 flex items-center">
                        @if($amenity->icon)
                            <i class="{{ $amenity->icon }} mr-2" style="color: var(--text-primary);"></i>
                            <span class="text-sm" style="color: var(--text-primary);">{{ $amenity->icon }}</span>
                        @else
                            <span class="text-sm text-gray-400">No icon set</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Sort Order</label>
                    <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $amenity->sort_order }}</p>
                </div>
                @if($amenity->description)
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Description</label>
                    <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $amenity->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('config.amenities.edit', $amenity->id) }}"
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-edit"></i>
                    Edit Amenity
                </a>
                <button onclick="deleteAmenity('{{ route('config.amenities.destroy', $amenity->id) }}')"
                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-trash"></i>
                    Delete Amenity
                </button>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Timestamps</h3>
            <div class="space-y-2">
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Created</label>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $amenity->created_at->format('M j, Y g:i A') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Last Updated</label>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $amenity->updated_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteAmenity(url) {
    if (confirm('Are you sure you want to delete this amenity? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add method override for DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
