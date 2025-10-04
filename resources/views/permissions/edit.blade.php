@extends('layouts.app')

@section('title', 'Edit Permission')

@php
    $title = 'Edit Permission';
    $subtitle = 'Update permission information';
    $showBackButton = true;
    $backUrl = route('permissions.index');
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Edit Permission</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Update the permission information below.</p>
        </div>

        <form method="POST" action="{{ route('permissions.update', $permission) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Permission Name -->
            <div>
                <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Permission Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $permission->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                       placeholder="e.g., View Users"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Permission Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Permission Slug <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="slug"
                       name="slug"
                       value="{{ old('slug', $permission->slug) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                       placeholder="e.g., users.view"
                       required>
                <p class="mt-1 text-xs" style="color: var(--text-secondary);">Use lowercase with dots (e.g., users.view, roles.create)</p>
                @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Module -->
            <div>
                <label for="module" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Module <span class="text-red-500">*</span>
                </label>
                <select id="module"
                        name="module"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('module') border-red-500 @enderror"
                        style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                        required>
                    <option value="">Select a module</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ old('module', $permission->module) === $module ? 'selected' : '' }}>{{ $module }}</option>
                    @endforeach
                    <option value="custom" {{ old('module', $permission->module) === 'custom' ? 'selected' : '' }}>Custom Module</option>
                </select>
                @error('module')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Custom Module Input (Hidden by default) -->
            <div id="customModuleDiv" class="hidden">
                <label for="custom_module" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Custom Module Name
                </label>
                <input type="text"
                       id="custom_module"
                       name="custom_module"
                       value="{{ old('custom_module') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                       placeholder="Enter custom module name">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Description
                </label>
                <textarea id="description"
                          name="description"
                          rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                          style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                          placeholder="Describe what this permission allows">{{ old('description', $permission->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- System Permission Warning -->
            @if($permission->is_system)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4" style="background-color: var(--warning-bg); border-color: var(--warning-border);">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800" style="color: var(--warning-text);">
                                System Permission
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700" style="color: var(--warning-text);">
                                <p>This is a system permission. Be careful when modifying it as it may affect system functionality.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100" style="border-color: var(--border-color);">
                <a href="{{ route('permissions.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Update Permission
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('module').addEventListener('change', function() {
    const customModuleDiv = document.getElementById('customModuleDiv');
    const customModuleInput = document.getElementById('custom_module');

    if (this.value === 'custom') {
        customModuleDiv.classList.remove('hidden');
        customModuleInput.required = true;
    } else {
        customModuleDiv.classList.add('hidden');
        customModuleInput.required = false;
        customModuleInput.value = '';
    }
});

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '.')
            .trim();
        slugInput.value = slug;
        slugInput.dataset.autoGenerated = 'true';
    }
});

// Reset auto-generation flag when user manually edits slug
document.getElementById('slug').addEventListener('input', function() {
    this.dataset.autoGenerated = 'false';
});
</script>
@endsection
