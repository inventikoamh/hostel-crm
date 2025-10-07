@extends('layouts.app')

@section('title', $hostel->name)

@php
    $title = $hostel->name;
    $subtitle = 'Hostel Details';
    $showBackButton = true;
    $backUrl = route('hostels.index');
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
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Description</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $hostel->description }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Status</label>
                        <div class="mt-1">
                            <x-status-badge :status="$hostel->status" />
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Total Rooms</label>
                        <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $hostel->total_rooms }} <span class="text-xs text-gray-500">(calculated from actual rooms)</span></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Total Beds</label>
                        <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">{{ $hostel->total_beds }} <span class="text-xs text-gray-500">(calculated from actual beds)</span></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Average Rent per Bed</label>
                        <p class="mt-1 text-sm font-medium" style="color: var(--text-primary);">${{ number_format($hostel->rent_per_bed, 2) }} <span class="text-xs text-gray-500">(calculated from actual bed data)</span></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Check-in Time</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ date('g:i A', strtotime($hostel->check_in_time)) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Check-out Time</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ date('g:i A', strtotime($hostel->check_out_time)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Phone</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $hostel->phone }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Email</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $hostel->email }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Website</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">
                            @if($hostel->website)
                                <a href="{{ $hostel->website }}" target="_blank" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                    {{ $hostel->website }}
                                </a>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Amenities</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($hostel->amenities as $amenity)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-check mr-1"></i>
                            {{ $amenity }}
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- Rules -->
            @if($hostel->rules)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Rules & Regulations</h3>
                <div class="prose prose-sm max-w-none" style="color: var(--text-primary);">
                    {!! nl2br(e($hostel->rules)) !!}
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Manager Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Manager Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Name</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $hostel->manager_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Phone</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $hostel->manager_phone }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Email</label>
                        <p class="mt-1 text-sm" style="color: var(--text-primary);">{{ $hostel->manager_email }}</p>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Address</h3>
                <div class="space-y-2">
                    <p class="text-sm" style="color: var(--text-primary);">{{ $hostel->address }}</p>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $hostel->city }}, {{ $hostel->state }} {{ $hostel->postal_code }}</p>
                    <p class="text-sm" style="color: var(--text-primary);">{{ $hostel->country }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('hostels.edit', $hostel->id) }}"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-edit"></i>
                        Edit Hostel
                    </a>
                    <a href="{{ route('map.hostel', $hostel->id) }}"
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-map"></i>
                        View Map
                    </a>
                    <button onclick="deleteHostel('{{ route('hostels.destroy', $hostel->id) }}')"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i>
                        Delete Hostel
                    </button>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Timestamps</h3>
                <div class="space-y-2">
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Created</label>
                        <p class="text-sm" style="color: var(--text-primary);">{{ date('M j, Y g:i A', strtotime($hostel->created_at)) }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600" style="color: var(--text-secondary);">Last Updated</label>
                        <p class="text-sm" style="color: var(--text-primary);">{{ date('M j, Y g:i A', strtotime($hostel->updated_at)) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteHostel(url) {
            if (confirm('Are you sure you want to delete this hostel? This action cannot be undone.')) {
                // Create a form and submit it
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
