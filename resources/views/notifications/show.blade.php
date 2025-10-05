@extends('layouts.app')

@section('title', 'Notification Details')

@php
    $title = 'Notification Details';
    $subtitle = 'View notification information and status';
    $showBackButton = true;
    $backUrl = route('notifications.index');
@endphp

@section('content')
    <div class="bg-white rounded-xl shadow-sm border p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <!-- Notification ID -->
        <div class="mb-6">
            <p class="text-sm" style="color: var(--text-secondary);">ID: #{{ $notification->id }}</p>
        </div>

        <!-- Action Buttons -->
        @if($notification->status === 'failed')
            <div class="mb-6">
                <form method="POST" action="{{ route('notifications.retry', $notification) }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-redo mr-2"></i>
                        Retry
                    </button>
                </form>
            </div>
        @endif

        <!-- Status Badge -->
        <div class="mb-6">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($notification->status === 'sent') bg-green-100 text-green-800
                @elseif($notification->status === 'failed') bg-red-100 text-red-800
                @elseif($notification->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($notification->status === 'scheduled') bg-blue-100 text-blue-800
                @else bg-gray-100 text-gray-800 @endif">
                <i class="fas fa-circle mr-2 text-xs"></i>
                {{ ucfirst($notification->status) }}
            </span>
        </div>

        <!-- Notification Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Type</label>
                        <p class="text-sm" style="color: var(--text-primary);">{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Recipient Email</label>
                        <p class="text-sm" style="color: var(--text-primary);">{{ $notification->recipient_email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Created At</label>
                        <p class="text-sm" style="color: var(--text-primary);">{{ $notification->created_at->format('M j, Y H:i:s') }}</p>
                    </div>
                    @if($notification->sent_at)
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Sent At</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $notification->sent_at->format('M j, Y H:i:s') }}</p>
                        </div>
                    @endif
                    @if($notification->scheduled_at)
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Scheduled At</label>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $notification->scheduled_at->format('M j, Y H:i:s') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Related Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Related Model</label>
                        <p class="text-sm" style="color: var(--text-primary);">{{ class_basename($notification->notifiable_type) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Related ID</label>
                        <p class="text-sm" style="color: var(--text-primary);">{{ $notification->notifiable_id }}</p>
                    </div>
                    @if($notification->notifiable)
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Related Object</label>
                            <p class="text-sm" style="color: var(--text-primary);">
                                @if(method_exists($notification->notifiable, 'name'))
                                    {{ $notification->notifiable->name }}
                                @elseif(method_exists($notification->notifiable, 'title'))
                                    {{ $notification->notifiable->title }}
                                @else
                                    {{ $notification->notifiable_type }} #{{ $notification->notifiable_id }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Email Content -->
        @if($notification->data)
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Email Content</h3>
                <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--input-bg);">
                    <div class="space-y-3">
                        @if(isset($notification->data['subject']))
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Subject</label>
                                <p class="text-sm" style="color: var(--text-primary);">{{ $notification->data['subject'] }}</p>
                            </div>
                        @endif
                        @if(isset($notification->data['heading']))
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Heading</label>
                                <p class="text-sm" style="color: var(--text-primary);">{{ $notification->data['heading'] }}</p>
                            </div>
                        @endif
                        @if(isset($notification->data['body']))
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Body</label>
                                <div class="text-sm whitespace-pre-wrap" style="color: var(--text-primary);">{{ $notification->data['body'] }}</div>
                            </div>
                        @endif
                        @if(isset($notification->data['action_url']))
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color: var(--text-secondary);">Action URL</label>
                                <a href="{{ $notification->data['action_url'] }}" class="text-sm text-blue-600 hover:text-blue-800 break-all" target="_blank">
                                    {{ $notification->data['action_url'] }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if($notification->message)
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Error Message</h3>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-800">{{ $notification->message }}</p>
                </div>
            </div>
        @endif

        <!-- Raw Data -->
        <div>
            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Raw Data</h3>
            <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--input-bg);">
                <pre class="text-xs overflow-x-auto" style="color: var(--text-primary);">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
@endsection
