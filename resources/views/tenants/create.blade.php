@extends('layouts.app')

@section('title', 'Create Tenant')

@php
    $title = 'Create Tenant';
    $subtitle = 'Add a new tenant to the hostel management system.';
    $showBackButton = true;
    $backUrl = route('tenants.index');
@endphp

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('tenants.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Email Address *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Password *</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
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
                                  style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">{{ old('address') }}</textarea>
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
                        <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('occupation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="company" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Company/Organization</label>
                        <input type="text" id="company" name="company" value="{{ old('company') }}"
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
                            <option value="aadhar" {{ old('id_proof_type') == 'aadhar' ? 'selected' : '' }}>Aadhar Card</option>
                            <option value="passport" {{ old('id_proof_type') == 'passport' ? 'selected' : '' }}>Passport</option>
                            <option value="driving_license" {{ old('id_proof_type') == 'driving_license' ? 'selected' : '' }}>Driving License</option>
                            <option value="voter_id" {{ old('id_proof_type') == 'voter_id' ? 'selected' : '' }}>Voter ID</option>
                            <option value="pan_card" {{ old('id_proof_type') == 'pan_card' ? 'selected' : '' }}>PAN Card</option>
                            <option value="other" {{ old('id_proof_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('id_proof_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="id_proof_number" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">ID Proof Number</label>
                        <input type="text" id="id_proof_number" name="id_proof_number" value="{{ old('id_proof_number') }}"
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
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('emergency_contact_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Contact Phone</label>
                        <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('emergency_contact_phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="emergency_contact_relation" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Relationship</label>
                        <input type="text" id="emergency_contact_relation" name="emergency_contact_relation" value="{{ old('emergency_contact_relation') }}"
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
                        <label for="move_in_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Move-in Date</label>
                        <input type="date" id="move_in_date" name="move_in_date" value="{{ old('move_in_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('move_in_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="security_deposit" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Security Deposit (₹)</label>
                        <input type="number" id="security_deposit" name="security_deposit" value="{{ old('security_deposit') }}" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('security_deposit')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="monthly_rent" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                            Monthly Rent (₹)
                            <span class="text-xs text-gray-500" id="rent_source"></span>
                        </label>
                        <input type="number" id="monthly_rent" name="monthly_rent" value="{{ old('monthly_rent') }}" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('monthly_rent')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Will be auto-filled when bed is selected</p>
                    </div>
                    <div>
                        <label for="lease_start_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Lease Start Date *</label>
                        <input type="date" id="lease_start_date" name="lease_start_date" value="{{ old('lease_start_date') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @error('lease_start_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">When the tenant will actually move in</p>
                    </div>
                    <div>
                        <label for="lease_end_date" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Lease End Date</label>
                        <input type="date" id="lease_end_date" name="lease_end_date" value="{{ old('lease_end_date') }}"
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
                                <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                            @endforeach
                        </select>
                        @error('hostel_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="bed_id" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Available Bed</label>
                        <select id="bed_id" name="bed_id" disabled
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent opacity-50"
                                style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">Please enter lease dates first</option>
                        </select>
                        @error('bed_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1" id="bed-assignment-help">Enter lease start and end dates to enable bed selection</p>
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
                              style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('tenants.index') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-200 transition-colors duration-200" style="background-color: var(--bg-secondary); color: var(--text-primary);">Cancel</a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200">Create Tenant</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hostelSelect = document.getElementById('hostel_id');
        const bedSelect = document.getElementById('bed_id');
        const leaseStartDateInput = document.getElementById('lease_start_date');
        const leaseEndDateInput = document.getElementById('lease_end_date');
        const bedAssignmentHelp = document.getElementById('bed-assignment-help');

        // Function to check if bed assignment should be enabled
        function updateBedAssignmentAvailability() {
            const leaseStartDate = leaseStartDateInput.value;
            const leaseEndDate = leaseEndDateInput.value;
            const hostelId = hostelSelect.value;

            if (leaseStartDate && hostelId) {
                // Enable bed selection
                bedSelect.disabled = false;
                bedSelect.classList.remove('opacity-50');
                bedAssignmentHelp.textContent = 'Select a bed to assign to this tenant';
                bedAssignmentHelp.style.color = 'var(--text-secondary)';

                // Fetch available beds
                fetchBeds(hostelId, leaseStartDate, leaseEndDate);
            } else {
                // Disable bed selection
                bedSelect.disabled = true;
                bedSelect.classList.add('opacity-50');
                bedSelect.innerHTML = '<option value="">Please enter lease dates first</option>';

                if (!leaseStartDate) {
                    bedAssignmentHelp.textContent = 'Enter lease start date to enable bed selection';
                } else if (!hostelId) {
                    bedAssignmentHelp.textContent = 'Select a hostel to enable bed selection';
                }
                bedAssignmentHelp.style.color = 'var(--text-secondary)';
            }
        }

        function fetchBeds(hostelId, leaseStartDate, leaseEndDate) {
            console.log('=== FETCHING BEDS ===');
            console.log('Fetching beds for hostel:', hostelId, 'with lease start date:', leaseStartDate, 'lease end date:', leaseEndDate);

            // Clear existing options first
            bedSelect.innerHTML = '<option value="">Loading available beds...</option>';

            // Build URL with lease dates parameters
            let url = `/tenants/available-beds/${hostelId}`;
            const params = new URLSearchParams();
            if (leaseStartDate) {
                params.append('lease_start_date', leaseStartDate);
            }
            if (leaseEndDate) {
                params.append('lease_end_date', leaseEndDate);
            }
            if (params.toString()) {
                url += `?${params.toString()}`;
            }

            console.log('Fetching URL:', url);

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
                    console.log('Beds type:', typeof beds);
                    console.log('Beds length:', beds ? beds.length : 'undefined');
                    console.log('Beds is array:', Array.isArray(beds));
                    console.log('Bed details:', beds.map(bed => ({
                        id: bed.id,
                        label: bed.label,
                        status: bed.status,
                        occupied_until: bed.occupied_until
                    })));

                    if (beds && Array.isArray(beds) && beds.length > 0) {
                        console.log('Processing', beds.length, 'beds for dropdown');
                        // Clear loading message
                        bedSelect.innerHTML = '';

                        // Add default option
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = 'Select a bed';
                        bedSelect.appendChild(defaultOption);

                        beds.forEach((bed, index) => {
                            console.log(`Processing bed ${index + 1}:`, bed);
                            const option = document.createElement('option');
                            option.value = bed.id;
                            option.textContent = `${bed.label} (Floor ${bed.floor}) - ₹${bed.rent || 0}`;
                            option.dataset.rent = bed.rent || 0;
                            option.dataset.roomNumber = bed.room_number;
                            bedSelect.appendChild(option);
                            console.log(`Added option: ${option.textContent}`);
                        });
                        console.log('Added', beds.length, 'bed options');
                        console.log('Final bed select options:', Array.from(bedSelect.options).map(opt => opt.textContent));
                    } else {
                        console.log('No beds available - showing empty state');
                        bedSelect.innerHTML = '';
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No available beds found';
                        bedSelect.appendChild(option);
                        console.log('No available beds found for this hostel');
                    }
                })
                .catch(error => {
                    console.error('Error fetching beds:', error);
                    // Show user-friendly error
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Error loading beds - Please try again';
                    bedSelect.appendChild(option);

                    // Show a temporary notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    notification.textContent = 'Failed to load available beds. Please refresh and try again.';
                    document.body.appendChild(notification);

                    // Remove notification after 5 seconds
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 5000);
                });
        }

        hostelSelect.addEventListener('change', function() {
            updateBedAssignmentAvailability();
        });

        // Auto-refresh beds when lease dates change
        leaseStartDateInput.addEventListener('change', function() {
            updateBedAssignmentAvailability();
        });

        leaseEndDateInput.addEventListener('change', function() {
            updateBedAssignmentAvailability();
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

        // Auto-fill lease start date when move-in date is selected
        const moveInDate = document.getElementById('move_in_date');
        const leaseStartDate = document.getElementById('lease_start_date');

        moveInDate.addEventListener('change', function() {
            if (this.value && !leaseStartDate.value) {
                leaseStartDate.value = this.value;
            }
        });

        // Initialize bed assignment availability on page load
        updateBedAssignmentAvailability();
    });
</script>
@endpush
