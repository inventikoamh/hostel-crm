@extends('layouts.app')

@section('title', 'Create Notification Setting')

@section('content')
<div class="container-fluid px-4">
    <div class="bg-white rounded-xl shadow-sm border p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <form method="POST" action="{{ route('notifications.settings.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Notification Type -->
                <div>
                    <label for="notification_type" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                        Notification Type <span class="text-red-500">*</span>
                    </label>
                    <select id="notification_type"
                            name="notification_type"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notification_type') border-red-500 @enderror"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                            required>
                        <option value="">Select notification type...</option>
                        @foreach($notificationTypes as $type)
                            <option value="{{ $type['value'] }}" {{ old('notification_type') == $type['value'] ? 'selected' : '' }}>
                                {{ $type['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('notification_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           value="{{ old('name') }}"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Description</label>
                <textarea id="description"
                          name="description"
                          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                          rows="3"
                          style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Recipient Type -->
                <div>
                    <label for="recipient_type" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                        Recipient Type <span class="text-red-500">*</span>
                    </label>
                    <select id="recipient_type"
                            name="recipient_type"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('recipient_type') border-red-500 @enderror"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                            required
                            onchange="toggleRecipientEmail()">
                        <option value="">Select recipient type...</option>
                        @foreach($recipientTypes as $type)
                            <option value="{{ $type['value'] }}" {{ old('recipient_type') == $type['value'] ? 'selected' : '' }}>
                                {{ $type['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('recipient_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recipient Email (conditional) -->
                <div id="recipient_email_field" style="display: none;">
                    <label for="recipient_email" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                        Recipient Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                           id="recipient_email"
                           name="recipient_email"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('recipient_email') border-red-500 @enderror"
                           value="{{ old('recipient_email') }}"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('recipient_email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <select id="priority"
                            name="priority"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('priority') border-red-500 @enderror"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                            required>
                        @foreach($priorities as $key => $label)
                            <option value="{{ $key }}" {{ old('priority', 2) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Send Immediately -->
                <div>
                    <input type="hidden" name="send_immediately" value="0">
                    <label class="flex items-center">
                        <input type="checkbox"
                               id="send_immediately"
                               name="send_immediately"
                               value="1"
                               {{ old('send_immediately', true) ? 'checked' : '' }}
                               class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                               onchange="toggleDelayField()">
                        <span class="text-sm font-medium" style="color: var(--text-secondary);">Send Immediately</span>
                    </label>
                </div>

                <!-- Delay Minutes -->
                <div id="delay_field">
                    <label for="delay_minutes" class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Delay (minutes)</label>
                    <input type="number"
                           id="delay_minutes"
                           name="delay_minutes"
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('delay_minutes') border-red-500 @enderror"
                           value="{{ old('delay_minutes', 0) }}"
                           min="0"
                           max="1440"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('delay_minutes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Enabled -->
            <div class="mt-6">
                <input type="hidden" name="enabled" value="0">
                <label class="flex items-center">
                    <input type="checkbox"
                           id="enabled"
                           name="enabled"
                           value="1"
                           {{ old('enabled', true) ? 'checked' : '' }}
                           class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <span class="text-sm font-medium" style="color: var(--text-secondary);">Enabled</span>
                </label>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('notifications.settings.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Create Setting
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRecipientEmail() {
    const recipientType = document.getElementById('recipient_type').value;
    const emailField = document.getElementById('recipient_email_field');
    const emailInput = document.getElementById('recipient_email');

    if (recipientType === 'specific_email') {
        emailField.style.display = 'block';
        emailInput.required = true;
    } else {
        emailField.style.display = 'none';
        emailInput.required = false;
        emailInput.value = '';
    }
}

function toggleDelayField() {
    const sendImmediately = document.getElementById('send_immediately').checked;
    const delayField = document.getElementById('delay_field');
    const delayInput = document.getElementById('delay_minutes');

    if (sendImmediately) {
        delayField.style.display = 'none';
        delayInput.value = 0;
    } else {
        delayField.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRecipientEmail();
    toggleDelayField();
});
</script>
@endsection
