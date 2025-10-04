@extends('layouts.app')

@section('title', 'Create User')

@php
    $title = 'Create User';
    $subtitle = 'Add a new user to the system';
    $showBackButton = true;
    $backUrl = route('users.index');
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <h2 class="text-xl font-semibold" style="color: var(--text-primary);">User Information</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Fill in the details to create a new user account.</p>
        </div>

        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                       placeholder="Enter full name"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                       placeholder="Enter email address"
                       required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Phone Number
                </label>
                <input type="tel"
                       id="phone"
                       name="phone"
                       value="{{ old('phone') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                       placeholder="Enter phone number">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                       placeholder="Enter password"
                       required>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                       placeholder="Confirm password"
                       required>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status"
                        name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                        style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                        required>
                    <option value="">Select status</option>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Avatar -->
            <div>
                <label for="avatar" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Profile Picture
                </label>
                <input type="file"
                       id="avatar"
                       name="avatar"
                       accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('avatar') border-red-500 @enderror"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                @error('avatar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs" style="color: var(--text-secondary);">Upload a profile picture (optional). Max size: 2MB</p>
            </div>

            <!-- Roles -->
            <div>
                <label class="block text-sm font-medium mb-3" style="color: var(--text-primary);">
                    Assign Roles
                </label>
                <div class="space-y-2">
                    @forelse($roles as $role)
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm" style="color: var(--text-primary);">{{ $role->name }}</span>
                            @if($role->description)
                                <span class="ml-2 text-xs" style="color: var(--text-secondary);">- {{ $role->description }}</span>
                            @endif
                        </label>
                    @empty
                        <p class="text-sm" style="color: var(--text-secondary);">No roles available. Please create roles first.</p>
                    @endforelse
                </div>
                @error('roles')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100" style="border-color: var(--border-color);">
                <a href="{{ route('users.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
