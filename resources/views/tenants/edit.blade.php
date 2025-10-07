@extends('layouts.app')

@section('title', 'Edit ' . $tenant->user->name)

@php
    $title = 'Edit Tenant';
    $subtitle = 'Update tenant information for ' . $tenant->user->name;
    $showBackButton = true;
    $backUrl = route('tenants.show', $tenant->id);
    $profile = $tenant;
    $currentBed = $tenant->currentBed;
@endphp

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('tenants.update', $tenant->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $tenant->user->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $tenant->user->email) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('date_of_birth')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Address</label>
                        <textarea id="address" name="address" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">{{ old('address', $profile->address) }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Professional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="occupation" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Occupation</label>
                        <input type="text" id="occupation" name="occupation" value="{{ old('occupation', $profile->occupation) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('occupation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="company" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Company/Organization</label>
                        <input type="text" id="company" name="company" value="{{ old('company', $profile->company) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('company')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ID Proof Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">ID Proof Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="id_proof_type" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">ID Proof Type</label>
                        <select id="id_proof_type" name="id_proof_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">Select ID Proof Type</option>
                            <option value="aadhar" {{ old('id_proof_type', $profile->id_proof_type) == 'aadhar' ? 'selected' : '' }}>Aadhar Card</option>
                            <option value="passport" {{ old('id_proof_type', $profile->id_proof_type) == 'passport' ? 'selected' : '' }}>Passport</option>
                            <option value="driving_license" {{ old('id_proof_type', $profile->id_proof_type) == 'driving_license' ? 'selected' : '' }}>Driving License</option>
                            <option value="voter_id" {{ old('id_proof_type', $profile->id_proof_type) == 'voter_id' ? 'selected' : '' }}>Voter ID</option>
                            <option value="pan_card" {{ old('id_proof_type', $profile->id_proof_type) == 'pan_card' ? 'selected' : '' }}>PAN Card</option>
                            <option value="other" {{ old('id_proof_type', $profile->id_proof_type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('id_proof_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="id_proof_number" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">ID Proof Number</label>
                        <input type="text" id="id_proof_number" name="id_proof_number" value="{{ old('id_proof_number', $profile->id_proof_number) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('id_proof_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Contact Name</label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $profile->emergency_contact_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('emergency_contact_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Contact Phone</label>
                        <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $profile->emergency_contact_phone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('emergency_contact_phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_contact_relation" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Relationship</label>
                        <input type="text" id="emergency_contact_relation" name="emergency_contact_relation" value="{{ old('emergency_contact_relation', $profile->emergency_contact_relation) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('emergency_contact_relation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Lease Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Lease Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="monthly_rent" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                            Monthly Rent (₹)
                            <span class="text-xs text-gray-500" id="rent_source"></span>
                        </label>
                        <input type="number" id="monthly_rent" name="monthly_rent" value="{{ old('monthly_rent', $profile->monthly_rent) }}" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('monthly_rent')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Will be auto-filled when bed is selected</p>
                    </div>
                    <div>
                        <label for="lease_start_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Lease Start Date *</label>
                        <input type="date" id="lease_start_date" name="lease_start_date" value="{{ old('lease_start_date', $profile->lease_start_date ? $profile->lease_start_date->format('Y-m-d') : '') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('lease_start_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">When the tenant will actually move in</p>
                    </div>
                    <div>
                        <label for="lease_end_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Lease End Date</label>
                        <input type="date" id="lease_end_date" name="lease_end_date" value="{{ old('lease_end_date', $profile->lease_end_date ? $profile->lease_end_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('lease_end_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Bed Assignment -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Bed Assignment (Optional)</h3>
                <div class="mb-4 p-3 bg-blue-50 rounded-lg" style="background-color: rgba(59, 130, 246, 0.05);">
                    <p class="text-sm text-blue-700" style="color: var(--text-secondary);">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> If you assign a bed with a future lease start date, the bed will be marked as "reserved" until the lease begins.
                        This prevents other tenants from being assigned to the same bed during the reservation period.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="hostel_id" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Hostel</label>
                        <select id="hostel_id" name="hostel_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">Select Hostel</option>
                            @foreach($hostels as $hostel)
                                @php
                                    $currentHostelId = $currentBed && $currentBed->room ? $currentBed->room->hostel_id : null;
                                @endphp
                                <option value="{{ $hostel->id }}" {{ old('hostel_id', $currentHostelId) == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                            @endforeach
                        </select>
                        @error('hostel_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="bed_id" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Available Bed</label>
                        <select id="bed_id" name="bed_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">Select Bed</option>
                            @if($currentBed)
                                <option value="{{ $currentBed->id }}" selected>{{ $currentBed->room->room_number }} - Bed {{ $currentBed->bed_number }} (Current)</option>
                            @endif
                        </select>
                        @error('bed_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @if($currentBed)
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg" style="background-color: var(--bg-secondary);">
                        <p class="text-sm" style="color: var(--text-primary);">
                            <strong>Currently assigned to:</strong> {{ $currentBed->room->hostel->name }} - {{ $currentBed->room->room_number }} - Bed {{ $currentBed->bed_number }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Status & Lease Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Status & Lease Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Status *</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="active" {{ old('status', $profile->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $profile->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="pending" {{ old('status', $profile->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="suspended" {{ old('status', $profile->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="moved_out" {{ old('status', $profile->status) == 'moved_out' ? 'selected' : '' }}>Moved Out</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="move_in_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Move-in Date</label>
                        <input type="date" id="move_in_date" name="move_in_date" value="{{ old('move_in_date', $profile->move_in_date ? $profile->move_in_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('move_in_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="security_deposit" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Security Deposit (₹)</label>
                        <input type="number" id="security_deposit" name="security_deposit" value="{{ old('security_deposit', $profile->security_deposit) }}" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('security_deposit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Additional Notes</h3>
                <div>
                    <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">{{ old('notes', $profile->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('tenants.show', $tenant->id) }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-200 transition-colors duration-200" style="background-color: var(--bg-secondary); color: var(--text-primary);">Cancel</a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200">Update Tenant</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hostelSelect = document.getElementById('hostel_id');
        const bedSelect = document.getElementById('bed_id');
        const currentBedId = {{ $currentBed ? $currentBed->id : 'null' }};

        hostelSelect.addEventListener('change', function() {
            const hostelId = this.value;
            const currentBedOption = bedSelect.querySelector('option[value="' + currentBedId + '"]');

            // Clear all options except the current bed
            bedSelect.innerHTML = '<option value="">Select Bed</option>';
            if (currentBedOption && currentBedId) {
                bedSelect.appendChild(currentBedOption.cloneNode(true));
            }

            if (hostelId) {
                const leaseStartDate = document.getElementById('lease_start_date').value;
                const leaseEndDate = document.getElementById('lease_end_date').value;

                let url = `/tenants/available-beds/${hostelId}`;
                const params = new URLSearchParams();
                if (leaseStartDate) params.append('lease_start_date', leaseStartDate);
                if (leaseEndDate) params.append('lease_end_date', leaseEndDate);
                if (params.toString()) url += '?' + params.toString();

                console.log('Fetching beds for hostel:', hostelId, 'with lease dates:', leaseStartDate, 'to', leaseEndDate);
                fetch(url)
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(beds => {
                        console.log('Received beds:', beds);
                        beds.forEach(bed => {
                            // Don't add the current bed again
                            if (bed.id != currentBedId) {
                                const option = document.createElement('option');
                                option.value = bed.id;
                                option.textContent = `${bed.label} (${bed.floor}) - ₹${bed.rent}`;
                                option.dataset.rent = bed.rent;
                                option.dataset.roomNumber = bed.room_number;
                                bedSelect.appendChild(option);
                            }
                        });
                        console.log('Added', beds.length, 'bed options');
                    })
                    .catch(error => {
                        console.error('Error fetching beds:', error);
                        // Show user-friendly error
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Error loading beds';
                        bedSelect.appendChild(option);
                    });
            }
        });

        // Auto-update rent when bed is selected
        bedSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const monthlyRentInput = document.getElementById('monthly_rent');
            const rentSourceSpan = document.getElementById('rent_source');

            if (selectedOption && selectedOption.dataset.rent) {
                const rent = parseFloat(selectedOption.dataset.rent);
                monthlyRentInput.value = rent.toFixed(2);
                rentSourceSpan.textContent = `(from ${selectedOption.dataset.roomNumber})`;
                rentSourceSpan.style.color = 'var(--text-success, #10b981)';
                console.log('Updated rent to:', rent, 'from bed:', selectedOption.textContent);
            } else if (this.value === '') {
                // Clear rent source indicator when no bed is selected
                rentSourceSpan.textContent = '';
            }
        });

        // Trigger change event on page load if hostel is already selected
        if (hostelSelect.value) {
            hostelSelect.dispatchEvent(new Event('change'));
        }

        // Auto-fill lease start date when move-in date is selected
        const moveInDate = document.getElementById('move_in_date');
        const leaseStartDate = document.getElementById('lease_start_date');
        const leaseEndDate = document.getElementById('lease_end_date');

        moveInDate.addEventListener('change', function() {
            if (this.value && !leaseStartDate.value) {
                leaseStartDate.value = this.value;
            }
        });

        // Refresh available beds when lease dates change
        leaseStartDate.addEventListener('change', function() {
            if (hostelSelect.value) {
                console.log('Lease start date changed, refreshing beds...');
                hostelSelect.dispatchEvent(new Event('change'));
            }
        });

        leaseEndDate.addEventListener('change', function() {
            if (hostelSelect.value) {
                console.log('Lease end date changed, refreshing beds...');
                hostelSelect.dispatchEvent(new Event('change'));
            }
        });
    });
</script>
@endpush
