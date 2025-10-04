@extends('layouts.app')

@section('title', 'Create Paid Service')

@php
    $title = 'Create Service';
    $subtitle = 'Add a new paid service or amenity';
    $showBackButton = true;
    $backUrl = route('paid-amenities.index');
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <form method="POST" action="{{ route('paid-amenities.store') }}">
            @csrf

            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Basic Information</h3>

                    <div>
                        <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Service Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                               placeholder="e.g., Breakfast, Room Cleaning, Laundry Service">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                  placeholder="Detailed description of the service...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">Select a category...</option>
                            <option value="food" {{ old('category') == 'food' ? 'selected' : '' }}>Food & Meals</option>
                            <option value="cleaning" {{ old('category') == 'cleaning' ? 'selected' : '' }}>Cleaning Services</option>
                            <option value="laundry" {{ old('category') == 'laundry' ? 'selected' : '' }}>Laundry Services</option>
                            <option value="utilities" {{ old('category') == 'utilities' ? 'selected' : '' }}>Utilities</option>
                            <option value="services" {{ old('category') == 'services' ? 'selected' : '' }}>General Services</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Pricing & Billing -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Pricing & Billing</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="billing_type" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                                Billing Type <span class="text-red-500">*</span>
                            </label>
                            <select name="billing_type" id="billing_type" required onchange="updateBillingInfo()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">Select billing type...</option>
                                <option value="monthly" {{ old('billing_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="daily" {{ old('billing_type') == 'daily' ? 'selected' : '' }}>Daily (Pay per use)</option>
                            </select>
                            @error('billing_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                                Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                                <input type="number" name="price" id="price" step="0.01" min="0" required
                                       value="{{ old('price') }}"
                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                       placeholder="0.00">
                            </div>
                            <p id="priceHelp" class="mt-1 text-xs" style="color: var(--text-secondary);">Price per service</p>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Daily Billing Options -->
                    <div id="dailyOptions" class="hidden">
                        <label for="max_usage_per_day" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Maximum Usage Per Day
                        </label>
                        <input type="number" name="max_usage_per_day" id="max_usage_per_day" min="1"
                               value="{{ old('max_usage_per_day') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                               placeholder="e.g., 1 for meals, 2 for laundry">
                        <p class="mt-1 text-xs" style="color: var(--text-secondary);">Leave empty for unlimited usage</p>
                        @error('max_usage_per_day')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Availability -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Availability</h3>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Available Days
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            @php
                                $days = [
                                    1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
                                    5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'
                                ];
                                $oldDays = old('availability_days', []);
                            @endphp
                            @foreach($days as $value => $label)
                                <label class="flex items-center">
                                    <input type="checkbox" name="availability_days[]" value="{{ $value }}"
                                           {{ in_array($value, $oldDays) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm" style="color: var(--text-primary);">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs" style="color: var(--text-secondary);">Leave unchecked for all days availability</p>
                        @error('availability_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium" style="color: var(--text-primary);">Additional Settings</h3>

                    <div>
                        <label for="icon" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Icon (FontAwesome class)
                        </label>
                        <input type="text" name="icon" id="icon"
                               value="{{ old('icon') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                               placeholder="e.g., fas fa-utensils, fas fa-broom">
                        <p class="mt-1 text-xs" style="color: var(--text-secondary);">Leave empty to use default category icon</p>
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="terms_conditions" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Terms & Conditions
                        </label>
                        <textarea name="terms_conditions" id="terms_conditions" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                  placeholder="Service terms, conditions, and important notes...">{{ old('terms_conditions') }}</textarea>
                        @error('terms_conditions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_active" class="ml-2 text-sm" style="color: var(--text-primary);">
                            Service is active and available for assignment
                        </label>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-4">
                    <a href="{{ route('paid-amenities.index') }}"
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 text-center">
                        Cancel
                    </a>
                    <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Create Service
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function updateBillingInfo() {
    const billingType = document.getElementById('billing_type').value;
    const priceHelp = document.getElementById('priceHelp');
    const dailyOptions = document.getElementById('dailyOptions');

    if (billingType === 'monthly') {
        priceHelp.textContent = 'Price per month';
        dailyOptions.classList.add('hidden');
    } else if (billingType === 'daily') {
        priceHelp.textContent = 'Price per usage/day';
        dailyOptions.classList.remove('hidden');
    } else {
        priceHelp.textContent = 'Price per service';
        dailyOptions.classList.add('hidden');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateBillingInfo();
});
</script>
@endsection
