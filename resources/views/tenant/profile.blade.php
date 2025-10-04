@extends('tenant.layout')

@section('title', 'My Profile')

@section('content')
<div class="min-h-screen" style="background-color: var(--bg-primary);">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold" style="color: var(--text-primary);">My Profile</h1>
            <p class="text-lg mt-2" style="color: var(--text-secondary);">Manage your personal information and account settings</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Personal Information</h2>
                            <button onclick="toggleEditMode()" id="editBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Profile
                            </button>
                        </div>

                        <!-- Approval Notice -->
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-yellow-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-yellow-800" style="color: var(--text-primary);">Profile Update Process</h4>
                                    <p class="text-sm text-yellow-700 mt-1" style="color: var(--text-secondary);">
                                        Any changes to your profile will be submitted for admin approval. You'll be notified once your changes are reviewed and approved.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form id="profileForm" method="POST" action="{{ route('tenant.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                           required>
                                    @error('name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Email Address</label>
                                    <input type="email" value="{{ $user->email }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100"
                                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-secondary);"
                                           disabled>
                                    <p class="text-xs mt-1" style="color: var(--text-secondary);">Email cannot be changed</p>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Phone Number</label>
                                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                    @error('phone')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date of Birth -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Date of Birth</label>
                                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $tenantProfile->date_of_birth?->format('Y-m-d')) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                    @error('date_of_birth')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Address</label>
                                    <textarea name="address" rows="3"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                              style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">{{ old('address', $tenantProfile->address) }}</textarea>
                                    @error('address')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Occupation -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Occupation</label>
                                    <input type="text" name="occupation" value="{{ old('occupation', $tenantProfile->occupation) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                    @error('occupation')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Company -->
                                <div>
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Company</label>
                                    <input type="text" name="company" value="{{ old('company', $tenantProfile->company) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                    @error('company')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Emergency Contact -->
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Emergency Contact</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Contact Name</label>
                                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $tenantProfile->emergency_contact_name) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                        @error('emergency_contact_name')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Contact Phone</label>
                                        <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $tenantProfile->emergency_contact_phone) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                        @error('emergency_contact_phone')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Relationship</label>
                                        <input type="text" name="emergency_contact_relation" value="{{ old('emergency_contact_relation', $tenantProfile->emergency_contact_relation) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                                        @error('emergency_contact_relation')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Avatar Upload (inside form) -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Profile Picture</label>
                                <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                                <label for="avatar" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 cursor-pointer">
                                    <i class="fas fa-camera mr-2"></i>
                                    Choose Photo
                                </label>
                                <p class="text-xs mt-2" style="color: var(--text-secondary);">Upload a new profile picture (optional)</p>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-4 mt-8" id="formActions" style="display: none;">
                                <button type="button" onclick="cancelEdit()" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200" style="border-color: var(--border-color); color: var(--text-primary);">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Submit for Approval
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Profile Picture -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <div class="p-6 text-center">
                        <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-200 flex items-center justify-center" style="background-color: var(--bg-secondary);">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile Picture" class="w-24 h-24 rounded-full object-cover">
                            @else
                                <i class="fas fa-user text-3xl" style="color: var(--text-secondary);"></i>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold mb-2" style="color: var(--text-primary);">{{ $user->name }}</h3>
                        <p class="text-sm" style="color: var(--text-secondary);">{{ $user->email }}</p>

                        <!-- Profile Picture Info -->
                        <div class="mt-4 p-3 rounded-lg" style="background-color: var(--bg-secondary);">
                            <p class="text-xs" style="color: var(--text-secondary);">
                                <i class="fas fa-info-circle mr-1"></i>
                                Profile pictures require admin approval
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Account Status</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm" style="color: var(--text-secondary);">Status:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $tenantProfile->status_badge['class'] }}">
                                    <i class="{{ $tenantProfile->status_badge['icon'] }} mr-1"></i>
                                    {{ ucfirst($tenantProfile->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm" style="color: var(--text-secondary);">Move-in Date:</span>
                                <span class="text-sm font-medium" style="color: var(--text-primary);">
                                    {{ $tenantProfile->move_in_date ? $tenantProfile->move_in_date->format('M d, Y') : 'Not set' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm" style="color: var(--text-secondary);">Tenancy Duration:</span>
                                <span class="text-sm font-medium" style="color: var(--text-primary);">
                                    {{ $tenantProfile->tenancy_duration_human ?? 'Not available' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Update Requests -->
                @php
                    $pendingRequests = \App\Models\TenantProfileUpdateRequest::where('tenant_profile_id', $tenantProfile->id)
                        ->where('status', 'pending')
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();
                @endphp

                @if($pendingRequests->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Pending Requests</h3>
                            <div class="space-y-3">
                                @foreach($pendingRequests as $request)
                                    <div class="p-3 rounded-lg border" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium" style="color: var(--text-primary);">Profile Update</p>
                                                <p class="text-xs" style="color: var(--text-secondary);">{{ $request->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pending
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('tenant.dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200" style="color: var(--text-primary);">
                                <i class="fas fa-tachometer-alt mr-3" style="color: var(--text-secondary);"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('tenant.invoices') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200" style="color: var(--text-primary);">
                                <i class="fas fa-file-invoice mr-3" style="color: var(--text-secondary);"></i>
                                My Invoices
                            </a>
                            <a href="{{ route('tenant.payments') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200" style="color: var(--text-primary);">
                                <i class="fas fa-credit-card mr-3" style="color: var(--text-secondary);"></i>
                                Payment History
                            </a>
                            <a href="{{ route('tenant.bed-info') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200" style="color: var(--text-primary);">
                                <i class="fas fa-bed mr-3" style="color: var(--text-secondary);"></i>
                                Bed Information
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let isEditMode = false;
let originalValues = {};

function toggleEditMode() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, textarea');
    const editBtn = document.getElementById('editBtn');
    const formActions = document.getElementById('formActions');

    if (!isEditMode) {
        // Enter edit mode
        inputs.forEach(input => {
            if (input.name !== 'email') { // Keep email disabled
                originalValues[input.name] = input.value;
                input.disabled = false;
            }
        });

        editBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Cancel Edit';
        editBtn.onclick = cancelEdit;
        formActions.style.display = 'flex';
        isEditMode = true;
    }
}

function cancelEdit() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, textarea');
    const editBtn = document.getElementById('editBtn');
    const formActions = document.getElementById('formActions');

    // Restore original values
    inputs.forEach(input => {
        if (input.name !== 'email') {
            input.disabled = true;
            if (originalValues[input.name] !== undefined) {
                input.value = originalValues[input.name];
            }
        }
    });

    editBtn.innerHTML = '<i class="fas fa-edit mr-2"></i>Edit Profile';
    editBtn.onclick = toggleEditMode;
    formActions.style.display = 'none';
    isEditMode = false;

    // Clear file input and reset avatar preview
    const fileInput = document.getElementById('avatar');
    if (fileInput) {
        fileInput.value = '';
    }

    // Reset avatar preview to original
    const sidebarImg = document.querySelector('.w-24.h-24 img');
    const sidebarIcon = document.querySelector('.w-24.h-24 i');
    if (sidebarImg && sidebarIcon) {
        sidebarImg.remove();
        sidebarIcon.style.display = 'block';
    }
}

function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Update the sidebar avatar preview
            const sidebarImg = document.querySelector('.w-24.h-24 img');
            const sidebarIcon = document.querySelector('.w-24.h-24 i');

            if (sidebarImg) {
                sidebarImg.src = e.target.result;
            } else if (sidebarIcon) {
                sidebarIcon.style.display = 'none';
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.className = 'w-24 h-24 rounded-full object-cover';
                newImg.alt = 'Profile Picture Preview';
                sidebarIcon.parentNode.appendChild(newImg);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}


// Initialize form as read-only
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, textarea');

    inputs.forEach(input => {
        if (input.name !== 'email') {
            input.disabled = true;
        }
    });
});
</script>
@endsection
