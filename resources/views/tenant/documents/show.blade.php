@extends('tenant.layout')

@section('title', 'Document Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Document Details</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $tenantDocument->document_number }} - {{ $tenantDocument->document_type_display }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('tenant.documents') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Documents
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Document Information -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Document Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Document Number:</span>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tenantDocument->document_number }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Document Type:</span>
                                <p class="text-lg text-gray-900 dark:text-white">{{ $tenantDocument->document_type_display }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $tenantDocument->status_badge_color }}-100 text-{{ $tenantDocument->status_badge_color }}-800 dark:bg-{{ $tenantDocument->status_badge_color }}-900 dark:text-{{ $tenantDocument->status_badge_color }}-200">
                                    {{ $tenantDocument->status_display }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Approval Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $tenantDocument->approval_status_badge_color }}-100 text-{{ $tenantDocument->approval_status_badge_color }}-800 dark:bg-{{ $tenantDocument->approval_status_badge_color }}-900 dark:text-{{ $tenantDocument->approval_status_badge_color }}-200">
                                    {{ $tenantDocument->approval_status_display }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Request Type:</span>
                                <p class="text-lg text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $tenantDocument->request_type)) }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Priority:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $tenantDocument->priority_badge_color }}-100 text-{{ $tenantDocument->priority_badge_color }}-800 dark:bg-{{ $tenantDocument->priority_badge_color }}-900 dark:text-{{ $tenantDocument->priority_badge_color }}-200">
                                    {{ $tenantDocument->priority_display }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Required:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tenantDocument->is_required ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ $tenantDocument->is_required ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            @if($tenantDocument->expiry_date)
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Expiry Date:</span>
                                <p class="text-lg text-gray-900 dark:text-white">{{ $tenantDocument->expiry_date->format('M d, Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($tenantDocument->description)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Description:</span>
                        <p class="mt-2 text-lg text-gray-900 dark:text-white">{{ $tenantDocument->description }}</p>
                    </div>
                    @endif

                    @if($tenantDocument->rejection_reason)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejection Reason:</span>
                        <div class="mt-2 p-4 bg-red-50 border border-red-200 rounded-lg dark:bg-red-900/20 dark:border-red-800">
                            <p class="text-red-800 dark:text-red-200">{{ $tenantDocument->rejection_reason }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Upload Section -->
            @if($tenantDocument->request_type === 'tenant_upload' && $tenantDocument->status === 'requested')
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Upload Document</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('tenant.documents.upload', $tenantDocument) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label for="document" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Document File <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="document" id="document" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-200 dark:hover:file:bg-blue-800 @error('document') border-red-300 @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Supported formats: PDF, JPG, JPEG, PNG (Max size: 10MB)</p>
                            @error('document')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes</label>
                            <textarea name="notes" id="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('notes') border-red-300 @enderror" rows="3" placeholder="Add any additional notes about this document...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-upload mr-2"></i>
                                Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Document Preview -->
            @if($tenantDocument->document_path)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Document Preview</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('tenant.documents.download', $tenantDocument) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-eye mr-2"></i>
                            View Document
                        </a>
                        <a href="{{ route('tenant.documents.download', $tenantDocument) }}" download class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-download mr-2"></i>
                            Download Document
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Document Timeline -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Document Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Document Requested</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tenantDocument->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($tenantDocument->uploaded_at_admin)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Document Uploaded by Admin</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tenantDocument->uploaded_at_admin->format('M d, Y H:i') }}</p>
                                @if($tenantDocument->uploadedByAdmin)
                                <p class="text-xs text-gray-500 dark:text-gray-500">by {{ $tenantDocument->uploadedByAdmin->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($tenantDocument->approved_at)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Document {{ ucfirst($tenantDocument->approval_status) }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tenantDocument->approved_at->format('M d, Y H:i') }}</p>
                                @if($tenantDocument->approvedByUser)
                                <p class="text-xs text-gray-500 dark:text-gray-500">by {{ $tenantDocument->approvedByUser->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
