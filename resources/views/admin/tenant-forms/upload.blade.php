@extends('layouts.app')

@section('title', 'Upload Signed Form')

@php
    $title = 'Upload Signed Form';
    $subtitle = 'Upload the signed tenant form';
    $showBackButton = true;
    $backUrl = route('admin.tenant-forms.show', $tenantForm);
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium" style="color: var(--text-primary);">Upload Signed Form</h3>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Form: {{ $tenantForm->form_number }} - {{ $tenantForm->tenantProfile->user->name }}</p>
        </div>

        <form method="POST" action="{{ route('admin.tenant-forms.store-signed', $tenantForm) }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- File Upload -->
                <div>
                    <label for="signed_form" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Signed Form File <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors" style="border-color: var(--border-color);">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600" style="color: var(--text-secondary);">
                                <label for="signed_form" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="signed_form" name="signed_form" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500" style="color: var(--text-secondary);">
                                PDF, PNG, JPG up to 10MB
                            </p>
                        </div>
                    </div>
                    @error('signed_form')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Preview -->
                <div id="file-preview" class="hidden">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file text-2xl text-gray-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium" id="file-name" style="color: var(--text-primary);"></p>
                                <p class="text-xs" id="file-size" style="color: var(--text-secondary);"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Additional Notes (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                              style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);"
                              placeholder="Any additional notes about the signed form...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Information -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Form Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-blue-700 dark:text-blue-300">Form Number:</span>
                            <span class="text-blue-600 dark:text-blue-400">{{ $tenantForm->form_number }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700 dark:text-blue-300">Tenant:</span>
                            <span class="text-blue-600 dark:text-blue-400">{{ $tenantForm->tenantProfile->user->name }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700 dark:text-blue-300">Form Type:</span>
                            <span class="text-blue-600 dark:text-blue-400">{{ $tenantForm->form_type_display }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700 dark:text-blue-300">Created:</span>
                            <span class="text-blue-600 dark:text-blue-400">{{ $tenantForm->created_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">Instructions</h4>
                    <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                        <li>• Upload the signed form as a PDF, JPG, or PNG file</li>
                        <li>• Maximum file size is 10MB</li>
                        <li>• Ensure the signature is clearly visible</li>
                        <li>• The form status will be updated to "Signed" after upload</li>
                    </ul>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.tenant-forms.show', $tenantForm) }}"
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Upload Signed Form
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('signed_form');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            filePreview.classList.remove('hidden');
        } else {
            filePreview.classList.add('hidden');
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection
