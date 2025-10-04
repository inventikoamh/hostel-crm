@extends('layouts.app')

@section('title', 'SMTP Settings')

@php
    $title = 'SMTP Settings';
    $subtitle = 'Configure email server settings';
    $showBackButton = true;
    $backUrl = route('dashboard');
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center" style="background-color: var(--success-bg, #f0fdf4); border-color: var(--success-border, #bbf7d0);">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800" style="color: var(--success-text, #166534);">
                    {{ session('success') }}
                </p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="closeMessage(this.parentElement.parentElement)" class="text-green-600 hover:text-green-800 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center" style="background-color: var(--error-bg, #fef2f2); border-color: var(--error-border, #fecaca);">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800" style="color: var(--error-text, #991b1b);">
                    {{ session('error') }}
                </p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="closeMessage(this.parentElement.parentElement)" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4" style="background-color: var(--error-bg, #fef2f2); border-color: var(--error-border, #fecaca);">
            <div class="flex items-center mb-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800" style="color: var(--error-text, #991b1b);">
                        Please correct the following errors:
                    </h3>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="closeMessage(this.parentElement.parentElement.parentElement)" class="text-red-600 hover:text-red-800 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1" style="color: var(--error-text, #b91c1c);">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- SMTP Configuration -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Email Server Configuration</h3>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">Configure SMTP settings for sending emails</p>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-envelope text-blue-600"></i>
                <span class="text-sm font-medium text-blue-600">Mail Settings</span>
            </div>
        </div>

        <form method="POST" action="{{ route('config.smtp-settings.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mail Driver -->
                <div class="md:col-span-2">
                    <label for="mail_mailer" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Mail Driver <span class="text-red-500">*</span>
                    </label>
                    <select name="mail_mailer" id="mail_mailer" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="smtp" {{ old('mail_mailer', $settings['mail_mailer']) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ old('mail_mailer', $settings['mail_mailer']) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="mailgun" {{ old('mail_mailer', $settings['mail_mailer']) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        <option value="ses" {{ old('mail_mailer', $settings['mail_mailer']) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                        <option value="postmark" {{ old('mail_mailer', $settings['mail_mailer']) == 'postmark' ? 'selected' : '' }}>Postmark</option>
                        <option value="log" {{ old('mail_mailer', $settings['mail_mailer']) == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                    </select>
                    @error('mail_mailer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SMTP Host -->
                <div>
                    <label for="mail_host" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        SMTP Host <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mail_host" id="mail_host"
                           value="{{ old('mail_host', $settings['mail_host']) }}" required
                           placeholder="smtp.gmail.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('mail_host')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SMTP Port -->
                <div>
                    <label for="mail_port" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        SMTP Port <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="mail_port" id="mail_port"
                           value="{{ old('mail_port', $settings['mail_port']) }}" required
                           placeholder="587"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('mail_port')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SMTP Username -->
                <div>
                    <label for="mail_username" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        SMTP Username
                    </label>
                    <input type="text" name="mail_username" id="mail_username"
                           value="{{ old('mail_username', $settings['mail_username']) }}"
                           placeholder="your-email@gmail.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('mail_username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SMTP Password -->
                <div>
                    <label for="mail_password" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        SMTP Password
                    </label>
                    <div class="relative">
                        <input type="password" name="mail_password" id="mail_password"
                               value="{{ old('mail_password', $settings['mail_password']) }}"
                               placeholder="Your app password"
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <button type="button" onclick="togglePassword('mail_password')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="mail_password_icon"></i>
                        </button>
                    </div>
                    @error('mail_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Encryption -->
                <div>
                    <label for="mail_encryption" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Encryption
                    </label>
                    <select name="mail_encryption" id="mail_encryption"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">None</option>
                        <option value="tls" {{ old('mail_encryption', $settings['mail_encryption']) == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                    @error('mail_encryption')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- From Address -->
                <div>
                    <label for="mail_from_address" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        From Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="mail_from_address" id="mail_from_address"
                           value="{{ old('mail_from_address', $settings['mail_from_address']) }}" required
                           placeholder="noreply@yourapp.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('mail_from_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- From Name -->
                <div>
                    <label for="mail_from_name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        From Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mail_from_name" id="mail_from_name"
                           value="{{ old('mail_from_name', $settings['mail_from_name']) }}" required
                           placeholder="Hostel CRM"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('mail_from_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Test Email -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Test Email Configuration</h3>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">Send a test email to verify your SMTP settings</p>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-paper-plane text-green-600"></i>
                <span class="text-sm font-medium text-green-600">Test Email</span>
            </div>
        </div>

        <form method="POST" action="{{ route('config.smtp-settings.test') }}">
            @csrf
            <div class="flex gap-4">
                <div class="flex-1">
                    <label for="test_email" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Test Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="test_email" id="test_email"
                           value="{{ old('test_email') }}" required
                           placeholder="test@example.com"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('test_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-end">
                    <button type="submit" id="testEmailBtn"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane" id="testEmailIcon"></i>
                        <span id="testEmailText">Send Test Email</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Common SMTP Providers -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Common SMTP Providers</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Gmail -->
            <div class="border border-gray-200 rounded-lg p-4" style="border-color: var(--border-color);">
                <div class="flex items-center mb-3">
                    <i class="fab fa-google text-red-500 mr-2"></i>
                    <h4 class="font-medium" style="color: var(--text-primary);">Gmail</h4>
                </div>
                <div class="text-sm space-y-1" style="color: var(--text-secondary);">
                    <p><strong>Host:</strong> smtp.gmail.com</p>
                    <p><strong>Port:</strong> 587</p>
                    <p><strong>Encryption:</strong> TLS</p>
                    <p class="text-xs text-orange-600 mt-2">Note: Use App Password, not regular password</p>
                </div>
            </div>

            <!-- Outlook -->
            <div class="border border-gray-200 rounded-lg p-4" style="border-color: var(--border-color);">
                <div class="flex items-center mb-3">
                    <i class="fab fa-microsoft text-blue-500 mr-2"></i>
                    <h4 class="font-medium" style="color: var(--text-primary);">Outlook</h4>
                </div>
                <div class="text-sm space-y-1" style="color: var(--text-secondary);">
                    <p><strong>Host:</strong> smtp-mail.outlook.com</p>
                    <p><strong>Port:</strong> 587</p>
                    <p><strong>Encryption:</strong> TLS</p>
                </div>
            </div>

            <!-- Yahoo -->
            <div class="border border-gray-200 rounded-lg p-4" style="border-color: var(--border-color);">
                <div class="flex items-center mb-3">
                    <i class="fab fa-yahoo text-purple-500 mr-2"></i>
                    <h4 class="font-medium" style="color: var(--text-primary);">Yahoo</h4>
                </div>
                <div class="text-sm space-y-1" style="color: var(--text-secondary);">
                    <p><strong>Host:</strong> smtp.mail.yahoo.com</p>
                    <p><strong>Port:</strong> 587</p>
                    <p><strong>Encryption:</strong> TLS</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Show/hide SMTP fields based on mail driver
    document.getElementById('mail_mailer').addEventListener('change', function() {
        const smtpFields = ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption'];
        const isSmtp = this.value === 'smtp';

        smtpFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const container = field.closest('div');
            if (isSmtp) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });
    });

    // Trigger on page load
    document.getElementById('mail_mailer').dispatchEvent(new Event('change'));

    // Auto-hide success/error messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const messages = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
        messages.forEach(function(message) {
            // Add fade-in animation
            message.style.opacity = '0';
            message.style.transform = 'translateY(-10px)';
            message.style.transition = 'all 0.3s ease-in-out';

            // Fade in
            setTimeout(() => {
                message.style.opacity = '1';
                message.style.transform = 'translateY(0)';
            }, 100);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (message.parentElement) {
                        message.remove();
                    }
                }, 300);
            }, 5000);
        });
    });

    // Manual close function with animation
    function closeMessage(element) {
        element.style.opacity = '0';
        element.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            element.remove();
        }, 300);
    }

    // Test email loading state
    document.querySelector('form[action*="test"]').addEventListener('submit', function() {
        const btn = document.getElementById('testEmailBtn');
        const icon = document.getElementById('testEmailIcon');
        const text = document.getElementById('testEmailText');

        // Disable button and show loading state
        btn.disabled = true;
        icon.className = 'fas fa-spinner fa-spin';
        text.textContent = 'Sending...';

        // Re-enable after 10 seconds (in case of timeout)
        setTimeout(() => {
            btn.disabled = false;
            icon.className = 'fas fa-paper-plane';
            text.textContent = 'Send Test Email';
        }, 10000);
    });
</script>
@endsection
