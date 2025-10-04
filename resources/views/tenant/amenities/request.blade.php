@extends('tenant.layout')

@section('title', 'Request New Amenity')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Request New Amenity</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Select an amenity you'd like to subscribe to
            </p>
        </div>
        <a href="{{ route('tenant.amenities') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Amenities
        </a>
    </div>

    @if($availableAmenities && $availableAmenities->count() > 0)
        <form method="POST" action="{{ route('tenant.amenities.request.store') }}" class="space-y-6">
            @csrf

            <!-- Amenity Selection -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Select Amenity
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availableAmenities as $amenity)
                            <div class="amenity-option border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:shadow-md transition-all duration-200"
                                 onclick="selectAmenity({{ $amenity->id }}, '{{ $amenity->name }}', {{ $amenity->price }}, '{{ $amenity->billing_type }}', '{{ $amenity->description }}')">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <input type="radio" name="paid_amenity_id" value="{{ $amenity->id }}"
                                               id="amenity_{{ $amenity->id }}" class="amenity-radio hidden">
                                        <label for="amenity_{{ $amenity->id }}" class="cursor-pointer">
                                            <i class="fas fa-{{ $amenity->icon ?? 'star' }} text-blue-600 text-xl"></i>
                                        </label>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ $amenity->name }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $amenity->category_display }}
                                        </p>
                                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <i class="fas fa-rupee-sign mr-1"></i>
                                                ₹{{ number_format($amenity->price, 2) }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $amenity->billing_type_display }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $amenity->description }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    @error('paid_amenity_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Selected Amenity Details -->
            <div id="selectedAmenityDetails" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 hidden">
                <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">
                    Selected Amenity Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-800 dark:text-blue-200">Amenity Name</label>
                        <p id="selectedName" class="mt-1 text-sm text-blue-700 dark:text-blue-300"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-800 dark:text-blue-200">Price</label>
                        <p id="selectedPrice" class="mt-1 text-sm text-blue-700 dark:text-blue-300"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-800 dark:text-blue-200">Billing Type</label>
                        <p id="selectedBilling" class="mt-1 text-sm text-blue-700 dark:text-blue-300"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-800 dark:text-blue-200">Description</label>
                        <p id="selectedDescription" class="mt-1 text-sm text-blue-700 dark:text-blue-300"></p>
                    </div>
                </div>
            </div>

            <!-- Request Details -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Request Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="start_date" name="start_date" required
                                   value="{{ old('start_date', now()->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Additional Notes
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                      placeholder="Any special requirements or notes...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Important Information
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Your amenity request will be reviewed by the administrator</li>
                                <li>You will be notified once your request is approved or rejected</li>
                                <li>Billing will start from the approved start date</li>
                                <li>You can cancel your subscription at any time</li>
                                <li>Usage tracking is available for daily billing amenities</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('tenant.amenities') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-300 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit" id="submitButton" disabled
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Request
                </button>
            </div>
        </form>
    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6 text-center">
                <i class="fas fa-check-circle text-green-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">All Amenities Subscribed</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    You have already subscribed to all available amenities. No new amenities are available for subscription.
                </p>
                <a href="{{ route('tenant.amenities') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Amenities
                </a>
            </div>
        </div>
    @endif
</div>

<script>
function selectAmenity(id, name, price, billingType, description) {
    // Remove previous selection
    document.querySelectorAll('.amenity-option').forEach(option => {
        option.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        option.classList.add('border-gray-200', 'dark:border-gray-700');
    });

    // Select current option
    const selectedOption = document.querySelector(`input[value="${id}"]`).closest('.amenity-option');
    selectedOption.classList.remove('border-gray-200', 'dark:border-gray-700');
    selectedOption.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');

    // Check the radio button
    document.querySelector(`input[value="${id}"]`).checked = true;

    // Update selected amenity details
    document.getElementById('selectedName').textContent = name;
    document.getElementById('selectedPrice').textContent = '₹' + price.toFixed(2);
    document.getElementById('selectedBilling').textContent = billingType.charAt(0).toUpperCase() + billingType.slice(1);
    document.getElementById('selectedDescription').textContent = description;

    // Show details section
    document.getElementById('selectedAmenityDetails').classList.remove('hidden');

    // Enable submit button
    document.getElementById('submitButton').disabled = false;
}

// Auto-select amenity if passed in URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const amenityId = urlParams.get('amenity');

    if (amenityId) {
        const amenityElement = document.querySelector(`input[value="${amenityId}"]`);
        if (amenityElement) {
            const option = amenityElement.closest('.amenity-option');
            const name = option.querySelector('h4').textContent;
            const price = parseFloat(option.querySelector('.fa-rupee-sign').parentElement.textContent.replace('₹', '').replace(',', ''));
            const billingType = option.querySelector('.fa-calendar').parentElement.textContent.trim();
            const description = option.querySelector('p:last-child').textContent.trim();

            selectAmenity(amenityId, name, price, billingType, description);
        }
    }
});
</script>

<style>
.amenity-option {
    transition: all 0.2s ease-in-out;
}

.amenity-option:hover {
    transform: translateY(-1px);
}

.amenity-option.selected {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

.dark .amenity-option.selected {
    background-color: rgba(59, 130, 246, 0.1);
}
</style>
@endsection
