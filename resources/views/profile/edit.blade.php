@extends('layouts.app')

@section('title', 'Edit Profile')

@php
    $title = 'Edit Profile';
    $subtitle = 'Manage your account information and security settings';
@endphp

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold" style="color: var(--text-primary);">{{ $title }}</h1>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">{{ $subtitle }}</p>
        </div>

        <!-- Profile Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <!-- Personal Information Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Personal Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Security Settings</h3>
                    <p class="text-sm mb-4" style="color: var(--text-secondary);">Leave password fields empty if you don't want to change your password.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Current Password</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">New Password</label>
                            <input type="password" id="password" name="password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                    </div>
                </div>

                <!-- Account Information Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Account Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">User ID</label>
                            <input type="text" value="{{ $user->id }}" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-secondary);">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Account Created</label>
                            <input type="text" value="{{ $user->created_at->format('M j, Y') }}" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-secondary);">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Last Updated</label>
                            <input type="text" value="{{ $user->updated_at->format('M j, Y g:i A') }}" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-secondary);">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">User Type</label>
                            <input type="text" value="{{ $user->is_tenant ? 'Tenant' : 'Admin' }}" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-secondary);">
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t" style="border-color: var(--border-color);">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-200 transition-colors duration-200" style="background-color: var(--bg-secondary); color: var(--text-primary);">Cancel</a>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Password field validation
    document.getElementById('password').addEventListener('input', function() {
        const currentPassword = document.getElementById('current_password');
        if (this.value && !currentPassword.value) {
            currentPassword.required = true;
        } else if (!this.value) {
            currentPassword.required = false;
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const currentPassword = document.getElementById('current_password').value;
        
        if (password && !currentPassword) {
            e.preventDefault();
            alert('Please enter your current password to change your password.');
            document.getElementById('current_password').focus();
        }
    });
</script>
@endpush
