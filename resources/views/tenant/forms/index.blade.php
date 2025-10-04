@extends('tenant.layout')

@section('title', 'My Forms')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold" style="color: var(--text-primary);">My Forms</h1>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">View and download your signed forms</p>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-file-alt text-2xl" style="color: var(--text-secondary);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Forms List -->
    @if($forms->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($forms as $form)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg hover:shadow-lg transition-shadow duration-200" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="p-6">
                    <!-- Form Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                @if($form->status === 'draft') bg-gray-100 dark:bg-gray-700
                                @elseif($form->status === 'printed') bg-yellow-100 dark:bg-yellow-900
                                @elseif($form->status === 'signed') bg-green-100 dark:bg-green-900
                                @else bg-blue-100 dark:bg-blue-900 @endif">
                                <i class="fas fa-{{ $form->status === 'draft' ? 'edit' : ($form->status === 'printed' ? 'print' : ($form->status === 'signed' ? 'signature' : 'archive')) }}
                                    @if($form->status === 'draft') text-gray-600 dark:text-gray-400
                                    @elseif($form->status === 'printed') text-yellow-600 dark:text-yellow-400
                                    @elseif($form->status === 'signed') text-green-600 dark:text-green-400
                                    @else text-blue-600 dark:text-blue-400 @endif"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium" style="color: var(--text-primary);">{{ $form->form_type_display }}</h3>
                                <p class="text-sm" style="color: var(--text-secondary);">{{ $form->form_number }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($form->status === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                            @elseif($form->status === 'printed') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @elseif($form->status === 'signed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @endif">
                            {{ $form->status_display }}
                        </span>
                    </div>

                    <!-- Form Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span style="color: var(--text-secondary);">Created:</span>
                            <span style="color: var(--text-primary);">{{ $form->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($form->printed_at)
                        <div class="flex justify-between text-sm">
                            <span style="color: var(--text-secondary);">Printed:</span>
                            <span style="color: var(--text-primary);">{{ $form->printed_at->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($form->uploaded_at)
                        <div class="flex justify-between text-sm">
                            <span style="color: var(--text-secondary);">Signed:</span>
                            <span style="color: var(--text-primary);">{{ $form->uploaded_at->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t" style="border-color: var(--border-color);">
                        <a href="{{ route('tenant.forms.show', $form) }}"
                           class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            View Details
                        </a>
                        @if($form->isSigned())
                        <a href="{{ route('tenant.forms.signed', $form) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-file-alt text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium mb-2" style="color: var(--text-primary);">No Forms Found</h3>
                <p class="text-sm mb-6" style="color: var(--text-secondary);">
                    You don't have any forms yet. Forms will appear here once they are created by the administrator.
                </p>
                <div class="text-xs" style="color: var(--text-secondary);">
                    <p>Contact your administrator if you need forms to be created.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
