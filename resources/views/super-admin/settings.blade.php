@extends('layouts.app')

@section('title', 'System Settings')

@php
    $title = 'System Settings';
    $subtitle = 'Configure system preferences and limits';
@endphp

@section('content')
<div class="container mx-auto p-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">{{ $title }}</h1>
        <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $subtitle }}</p>
    </div>

    <form method="POST" action="{{ route('super-admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- General Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">General Settings</h3>

                <div class="space-y-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="demo_mode" value="1" {{ ($settingsArray['demo_mode'] ?? false) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium" style="color: var(--text-primary);">Demo Mode</span>
                        </label>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">Enable demo mode to restrict certain features</p>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="tenant_login_maintenance" value="1" {{ ($settingsArray['tenant_login_maintenance'] ?? false) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium" style="color: var(--text-primary);">Tenant Login Maintenance</span>
                        </label>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">Enable maintenance mode to disable tenant login</p>
                    </div>

                    <div>
                        <label for="app_name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Application Name</label>
                        <input type="text" id="app_name" name="app_name" value="{{ $settingsArray['app_name'] ?? 'Hostel CRM' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>

                    <div>
                        <label for="app_logo_file" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Application Logo</label>
                        <input type="file" id="app_logo_file" name="app_logo_file" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @if($settingsArray['app_logo'] ?? false)
                        <div class="mt-2">
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Current Logo</label>
                            <img src="{{ $settingsArray['app_logo'] }}" alt="Current Logo" class="h-12 w-auto border rounded" style="border-color: var(--border-color);">
                        </div>
                        @endif
                    </div>

                    <div>
                        <label for="favicon_file" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Favicon</label>
                        <input type="file" id="favicon_file" name="favicon_file" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        @if($settingsArray['favicon'] ?? false)
                        <div class="mt-2">
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Current Favicon</label>
                            <img src="{{ $settingsArray['favicon'] }}" alt="Current Favicon" class="h-8 w-8 border rounded" style="border-color: var(--border-color);">
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Theme Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Theme Settings</h3>

                <div class="space-y-4">
                    <div>
                        <label for="primary_color" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Primary Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="primary_color" name="primary_color" value="{{ $settingsArray['primary_color'] ?? '#3B82F6' }}"
                                   class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                            <input type="text" value="{{ $settingsArray['primary_color'] ?? '#3B82F6' }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   onchange="document.getElementById('primary_color').value = this.value">
                        </div>
                    </div>

                    <div>
                        <label for="secondary_color" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Secondary Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="secondary_color" name="secondary_color" value="{{ $settingsArray['secondary_color'] ?? '#6B7280' }}"
                                   class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                            <input type="text" value="{{ $settingsArray['secondary_color'] ?? '#6B7280' }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                                   onchange="document.getElementById('secondary_color').value = this.value">
                        </div>
                    </div>

                </div>
            </div>

            <!-- System Limits -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">System Limits</h3>

                <div class="space-y-4">
                    <div>
                        <label for="max_hostels" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Maximum Hostels</label>
                        <input type="number" id="max_hostels" name="max_hostels" value="{{ $settingsArray['max_hostels'] ?? 10 }}" min="1" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>

                    <div>
                        <label for="max_floors_per_hostel" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Maximum Floors per Hostel</label>
                        <input type="number" id="max_floors_per_hostel" name="max_floors_per_hostel" value="{{ $settingsArray['max_floors_per_hostel'] ?? 5 }}" min="1" max="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>

                    <div>
                        <label for="max_rooms_per_floor" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Maximum Rooms per Floor</label>
                        <input type="number" id="max_rooms_per_floor" name="max_rooms_per_floor" value="{{ $settingsArray['max_rooms_per_floor'] ?? 20 }}" min="1" max="50"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>

                    <div>
                        <label for="max_beds_per_room" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Maximum Beds per Room</label>
                        <input type="number" id="max_beds_per_room" name="max_beds_per_room" value="{{ $settingsArray['max_beds_per_room'] ?? 10 }}" min="1" max="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    </div>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('super-admin.dashboard') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-200 transition-colors duration-200" style="background-color: var(--bg-secondary); color: var(--text-primary);">Cancel</a>
            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200">Save Settings</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Toggle SMTP settings based on demo mode
    document.querySelector('input[name="demo_mode"]').addEventListener('change', function() {
        const smtpSettings = document.getElementById('smtp-settings');
        const smtpInputs = smtpSettings.querySelectorAll('input, select');

        if (this.checked) {
            smtpInputs.forEach(input => {
                input.disabled = true;
                input.classList.add('bg-gray-100');
            });
            smtpSettings.querySelector('.bg-yellow-50').style.display = 'block';
        } else {
            smtpInputs.forEach(input => {
                input.disabled = false;
                input.classList.remove('bg-gray-100');
            });
            smtpSettings.querySelector('.bg-yellow-50').style.display = 'none';
        }
    });

    // Sync color inputs
    document.getElementById('primary_color').addEventListener('input', function() {
        this.nextElementSibling.value = this.value;
    });

    document.getElementById('secondary_color').addEventListener('input', function() {
        this.nextElementSibling.value = this.value;
    });
</script>
@endpush
