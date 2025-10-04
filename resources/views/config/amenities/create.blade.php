@extends('layouts.app')

@section('title', 'Create Amenity')

@php
    $title = 'Create Amenity';
    $subtitle = 'Add a new amenity to the system';
    $showBackButton = true;
    $backUrl = route('config.amenities.index');
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('config.amenities.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Amenity Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                           placeholder="Enter amenity name">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div>
                    <label for="icon" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Icon (FontAwesome)</label>
                    <input type="text" id="icon" name="icon" value="{{ old('icon') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                           placeholder="e.g., fas fa-wifi">
                    @error('icon')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">FontAwesome icon class (optional)</p>
                </div>

                <!-- Sort Order -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                           placeholder="0">
                    @error('sort_order')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                              placeholder="Enter amenity description (optional)">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                        <span class="text-sm font-medium" style="color: var(--text-primary);">Active</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Uncheck to disable this amenity</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('config.amenities.index') }}"
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                Create Amenity
            </button>
        </div>
    </form>
</div>
@endsection
