@extends('layouts.app')

@section('title', 'Edit Role')

@php
    $title = 'Edit Role';
    $subtitle = 'Update role information and permissions';
    $showBackButton = true;
    $backUrl = route('roles.index');
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <h2 class="text-xl font-semibold" style="color: var(--text-primary);">Edit Role: {{ $role->name }}</h2>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Update role information and permissions.</p>
        </div>

        <form method="POST" action="{{ route('roles.update', $role) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Role Name -->
            <div>
                <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                    Role Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $role->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                       placeholder="Enter role name"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                          placeholder="Enter role description">{{ old('description', $role->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- System Role Warning -->
            @if($role->is_system)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">System Role</h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>This is a system role. Some permissions may be required for system functionality.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Permissions -->
            <div>
                <label class="block text-sm font-medium mb-3" style="color: var(--text-primary);">
                    Assign Permissions
                </label>

                @if($permissions->count() > 0)
                    <div class="space-y-4">
                        @foreach($permissions as $module => $modulePermissions)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4" style="border-color: var(--border-color);">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium" style="color: var(--text-primary);">{{ $module }}</h4>
                                    <button type="button"
                                            onclick="toggleModulePermissions('{{ $module }}')"
                                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <span id="{{ $module }}-toggle">Select All</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($modulePermissions as $permission)
                                        <label class="flex items-start space-x-2 p-2 rounded transition-colors duration-200 permission-card">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->id }}"
                                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500 module-{{ $module }}"
                                                   {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium" style="color: var(--text-primary);">{{ $permission->name }}</div>
                                                @if($permission->description)
                                                    <div class="text-xs" style="color: var(--text-secondary);">{{ $permission->description }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-key text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-sm" style="color: var(--text-secondary);">No permissions available</p>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">Please create permissions first</p>
                    </div>
                @endif

                @error('permissions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100" style="border-color: var(--border-color);">
                <a href="{{ route('roles.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                   style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.permission-card {
    background-color: var(--bg-primary);
}

.permission-card:hover {
    background-color: var(--hover-bg);
}
</style>

<script>
function toggleModulePermissions(module) {
    const checkboxes = document.querySelectorAll(`.module-${module}`);
    const toggle = document.getElementById(`${module}-toggle`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });

    toggle.textContent = allChecked ? 'Select All' : 'Deselect All';
}
</script>
@endsection
