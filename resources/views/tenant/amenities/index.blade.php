@extends('tenant.layout')

@section('title', 'My Amenities')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Amenities</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage your subscribed amenities and request new ones
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('tenant.amenities.request') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Request New Amenity
            </a>
            <a href="{{ route('tenant.amenities.usage') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-chart-line mr-2"></i>
                Usage Tracking
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-list text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Total Amenities
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $tenantAmenities ? $tenantAmenities->count() : 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Active Amenities
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $tenantAmenities->where('status', 'active')->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Pending Requests
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $tenantAmenities->where('status', 'pending')->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-plus-circle text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Available Amenities
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $availableAmenities ? $availableAmenities->count() : 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Amenities -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                My Subscribed Amenities
            </h3>

            @if($tenantAmenities && $tenantAmenities->count() > 0)
                <div class="space-y-4">
                    @foreach($tenantAmenities as $tenantAmenity)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-{{ $tenantAmenity->paidAmenity->icon ?? 'star' }} text-blue-600 text-xl"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ $tenantAmenity->paidAmenity->name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $tenantAmenity->paidAmenity->description }}
                                            </p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    {{ $tenantAmenity->paidAmenity->category_display }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    {{ $tenantAmenity->paidAmenity->billing_type_display }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-rupee-sign mr-1"></i>
                                                    ₹{{ number_format($tenantAmenity->effective_price, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($tenantAmenity->status === 'active') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                        @elseif($tenantAmenity->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                        @elseif($tenantAmenity->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100 @endif">
                                        {{ ucfirst($tenantAmenity->status) }}
                                    </span>

                                    <!-- Action Buttons -->
                                    @if($tenantAmenity->status === 'active')
                                        <button onclick="openCancelModal({{ $tenantAmenity->id }}, '{{ $tenantAmenity->paidAmenity->name }}')"
                                                class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-gray-800 dark:border-red-600 dark:text-red-400 dark:hover:bg-red-900">
                                            <i class="fas fa-times mr-1"></i>
                                            Cancel
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Additional Info -->
                            <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <span class="font-medium">Start Date:</span>
                                        {{ $tenantAmenity->start_date->format('M j, Y') }}
                                    </div>
                                    @if($tenantAmenity->end_date)
                                        <div>
                                            <span class="font-medium">End Date:</span>
                                            {{ $tenantAmenity->end_date->format('M j, Y') }}
                                        </div>
                                    @endif
                                    @if($tenantAmenity->notes)
                                        <div class="md:col-span-3">
                                            <span class="font-medium">Notes:</span>
                                            {{ $tenantAmenity->notes }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Current Month Usage Summary -->
                            @if($tenantAmenity->status === 'active' && $tenantAmenity->usageRecords->count() > 0)
                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        This Month's Usage
                                    </h5>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">{{ $tenantAmenity->usageRecords->sum('quantity') }}</span>
                                        {{ Str::plural('usage', $tenantAmenity->usageRecords->sum('quantity')) }}
                                        (Total: ₹{{ number_format($tenantAmenity->usageRecords->sum('total_amount'), 2) }})
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-list text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Amenities Subscribed</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        You haven't subscribed to any amenities yet. Browse available amenities and request new ones.
                    </p>
                    <a href="{{ route('tenant.amenities.request') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Request New Amenity
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Available Amenities -->
    @if($availableAmenities && $availableAmenities->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                    Available Amenities
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    These amenities are available for subscription. Click "Request" to subscribe.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($availableAmenities as $amenity)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-{{ $amenity->icon ?? 'star' }} text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $amenity->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $amenity->category_display }}
                                    </p>
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                {{ Str::limit($amenity->description, 100) }}
                            </p>

                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-medium">₹{{ number_format($amenity->price, 2) }}</span>
                                    <span class="text-xs">/ {{ $amenity->billing_type_display }}</span>
                                </div>
                            </div>

                            <a href="{{ route('tenant.amenities.request') }}?amenity={{ $amenity->id }}"
                               class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i>
                                Request
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Cancel Amenity Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div class="mt-2 px-7 py-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center">Cancel Amenity</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                        Are you sure you want to cancel <span id="amenityName" class="font-medium"></span>?
                    </p>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <form id="cancelForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cancellation Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea id="cancellation_reason" name="cancellation_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="Please provide a reason for cancellation..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCancelModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openCancelModal(amenityId, amenityName) {
    document.getElementById('amenityName').textContent = amenityName;
    document.getElementById('cancelForm').action = `/tenant/amenities/${amenityId}/cancel`;
    document.getElementById('end_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelForm').reset();
}

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
@endsection
