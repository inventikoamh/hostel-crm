@extends('tenant.layout')

@section('title', 'Amenity Usage Tracking')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Amenity Usage Tracking</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Track and manage your amenity usage records
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="openMarkUsageModal()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Mark Usage
            </button>
            <a href="{{ route('tenant.amenities') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Amenities
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
                                Total Usage Records
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $usageRecords->count() }}
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
                        <i class="fas fa-calculator text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Total Amount
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                ₹{{ number_format($usageRecords->sum('total_amount'), 2) }}
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
                        <i class="fas fa-hashtag text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Total Quantity
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $usageRecords->sum('quantity') }}
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
                        <i class="fas fa-calendar-day text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Usage Days
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $usageRecords->unique('usage_date')->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Amenities -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                Your Active Amenities
            </h3>

            @if($tenantAmenities->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($tenantAmenities as $tenantAmenity)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-{{ $tenantAmenity->paidAmenity->icon ?? 'star' }} text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $tenantAmenity->paidAmenity->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        ₹{{ number_format($tenantAmenity->effective_price, 2) }} / {{ $tenantAmenity->paidAmenity->billing_type_display }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                <p>{{ Str::limit($tenantAmenity->paidAmenity->description, 80) }}</p>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    ID: {{ $tenantAmenity->id }}
                                </span>
                                <button onclick="markUsageForAmenity({{ $tenantAmenity->id }}, '{{ $tenantAmenity->paidAmenity->name }}')"
                                        class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-xs font-medium rounded text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-blue-600 dark:text-blue-400 dark:hover:bg-blue-900">
                                    <i class="fas fa-plus mr-1"></i>
                                    Mark Usage
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Active Amenities</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        You don't have any active amenities to track usage for.
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

    <!-- Usage Records -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                All Usage Records
            </h3>

            @if($usageRecords->count() > 0)
                <!-- Data Table Component -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <!-- Table Header with Search and Filters -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                                <div class="relative">
                                    <input type="text" id="searchInput" placeholder="Search records..."
                                           class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                                <select id="amenityFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">All Amenities</option>
                                    @foreach($tenantAmenities as $amenity)
                                        <option value="{{ $amenity->paidAmenity->name }}">{{ $amenity->paidAmenity->name }}</option>
                                    @endforeach
                                </select>
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <label class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">Date Range:</label>
                                        <input type="date" id="startDate"
                                               class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        <span class="text-gray-500 dark:text-gray-400">to</span>
                                        <input type="date" id="endDate"
                                               class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                        <button onclick="clearDateFilter()"
                                                class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                                title="Clear date filter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Quick filters:</span>
                                        <button onclick="setDateRange('today')"
                                                class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded">
                                            Today
                                        </button>
                                        <button onclick="setDateRange('week')"
                                                class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded">
                                            This Week
                                        </button>
                                        <button onclick="setDateRange('month')"
                                                class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded">
                                            This Month
                                        </button>
                                        <button onclick="setDateRange('last30')"
                                                class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded">
                                            Last 30 Days
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    Showing <span id="recordCount">{{ $usageRecords->count() }}</span> records
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Table Content -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="usageTable">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable(0)">
                                        Date <i class="fas fa-sort ml-1"></i>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable(1)">
                                        Amenity <i class="fas fa-sort ml-1"></i>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable(2)">
                                        Quantity <i class="fas fa-sort ml-1"></i>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable(3)">
                                        Unit Price <i class="fas fa-sort ml-1"></i>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" onclick="sortTable(4)">
                                        Total Amount <i class="fas fa-sort ml-1"></i>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Notes
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="tableBody">
                                @foreach($usageRecords as $usage)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-day text-gray-400 mr-2"></i>
                                                {{ $usage->usage_date->format('M j, Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <div class="flex items-center">
                                                <i class="fas fa-{{ $usage->tenantAmenity->paidAmenity->icon ?? 'star' }} text-blue-500 mr-2"></i>
                                                {{ $usage->tenantAmenity->paidAmenity->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $usage->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <span class="font-medium">₹{{ number_format($usage->unit_price, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                ₹{{ number_format($usage->total_amount, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs">
                                            @if($usage->notes)
                                                <div class="truncate" title="{{ $usage->notes }}">
                                                    {{ Str::limit($usage->notes, 50) }}
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">No notes</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button onclick="openCorrectionModal({{ $usage->id }}, '{{ $usage->tenantAmenity->paidAmenity->name }}', {{ $usage->quantity }}, '{{ $usage->notes }}')"
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Request Correction
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer with Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Show:</span>
                                <select id="pageSize" class="px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="text-sm text-gray-500 dark:text-gray-400">per page</span>
                            </div>
                            <div class="flex items-center space-x-2" id="pagination">
                                <!-- Pagination will be generated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-chart-line text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Usage Records</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        You haven't recorded any usage yet.
                    </p>
                    <button onclick="openMarkUsageModal()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Mark Usage
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Mark Usage Modal -->
<div id="markUsageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full">
                <i class="fas fa-plus text-blue-600 text-xl"></i>
            </div>
            <div class="mt-2 px-7 py-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center">Mark Usage</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                        Record your amenity usage for today or a specific date.
                    </p>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <form id="markUsageForm" method="POST" action="{{ route('tenant.amenities.usage.mark') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="tenant_amenity_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Amenity <span class="text-red-500">*</span>
                        </label>
                        <select id="tenant_amenity_id" name="tenant_amenity_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select an amenity...</option>
                            @foreach($tenantAmenities as $tenantAmenity)
                                <option value="{{ $tenantAmenity->id }}">{{ $tenantAmenity->paidAmenity->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="usage_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Usage Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="usage_date" name="usage_date" required
                               value="{{ now()->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="quantity" name="quantity" required min="1" max="10"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="Optional notes about this usage..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeMarkUsageModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Record Usage
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Correction Request Modal -->
<div id="correctionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-yellow-100 rounded-full">
                <i class="fas fa-edit text-yellow-600 text-xl"></i>
            </div>
            <div class="mt-2 px-7 py-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center">Request Usage Correction</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                        Request a correction for <span id="correctionAmenityName" class="font-medium"></span> usage.
                    </p>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <form id="correctionForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current Quantity: <span id="currentQuantity" class="font-medium"></span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label for="requested_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Requested Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="requested_quantity" name="requested_quantity" required min="1" max="10"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mb-4">
                        <label for="requested_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Requested Notes
                        </label>
                        <textarea id="requested_notes" name="requested_notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="Updated notes..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="correction_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reason for Correction <span class="text-red-500">*</span>
                        </label>
                        <textarea id="correction_reason" name="correction_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="Please explain why this correction is needed..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCorrectionModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openMarkUsageModal() {
    document.getElementById('markUsageModal').classList.remove('hidden');
}

function closeMarkUsageModal() {
    document.getElementById('markUsageModal').classList.add('hidden');
    document.getElementById('markUsageForm').reset();
}

function markUsageForAmenity(amenityId, amenityName) {
    document.getElementById('tenant_amenity_id').value = amenityId;
    openMarkUsageModal();
}

function openCorrectionModal(usageId, amenityName, currentQuantity, currentNotes) {
    document.getElementById('correctionAmenityName').textContent = amenityName;
    document.getElementById('currentQuantity').textContent = currentQuantity;
    document.getElementById('requested_quantity').value = currentQuantity;
    document.getElementById('requested_notes').value = currentNotes;
    document.getElementById('correctionForm').action = `/tenant/amenities/usage/${usageId}/correction`;
    document.getElementById('correctionModal').classList.remove('hidden');
}

function closeCorrectionModal() {
    document.getElementById('correctionModal').classList.add('hidden');
    document.getElementById('correctionForm').reset();
}

// Close modals when clicking outside
document.getElementById('markUsageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMarkUsageModal();
    }
});

document.getElementById('correctionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCorrectionModal();
    }
});


// Data Table Functionality
let currentPage = 1;
let pageSize = 25;
let sortColumn = -1;
let sortDirection = 'asc';
let allRows = [];
let filteredRows = [];

// Initialize data table
function initializeDataTable() {
    const table = document.getElementById('usageTable');
    if (!table) return;

    const tbody = document.getElementById('tableBody');
    allRows = Array.from(tbody.querySelectorAll('tr'));
    filteredRows = [...allRows];

    // Add event listeners
    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('amenityFilter').addEventListener('change', filterTable);
    document.getElementById('startDate').addEventListener('change', filterTable);
    document.getElementById('endDate').addEventListener('change', filterTable);
    document.getElementById('pageSize').addEventListener('change', function() {
        pageSize = parseInt(this.value);
        currentPage = 1;
        renderTable();
    });

    renderTable();
}

// Filter table based on search, amenity filter, and date range
function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const amenityFilter = document.getElementById('amenityFilter').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    filteredRows = allRows.filter(row => {
        const cells = row.querySelectorAll('td');
        const dateText = cells[0]?.textContent.trim() || '';
        const amenity = cells[1]?.textContent.toLowerCase() || '';
        const quantity = cells[2]?.textContent.toLowerCase() || '';
        const unitPrice = cells[3]?.textContent.toLowerCase() || '';
        const totalAmount = cells[4]?.textContent.toLowerCase() || '';
        const notes = cells[5]?.textContent.toLowerCase() || '';

        // Search filter
        const matchesSearch = !searchTerm ||
            dateText.toLowerCase().includes(searchTerm) ||
            amenity.includes(searchTerm) ||
            quantity.includes(searchTerm) ||
            unitPrice.includes(searchTerm) ||
            totalAmount.includes(searchTerm) ||
            notes.includes(searchTerm);

        // Amenity filter
        const matchesAmenity = !amenityFilter || amenity.includes(amenityFilter.toLowerCase());

        // Date range filter
        let matchesDateRange = true;
        if (startDate || endDate) {
            // Parse the date from the table (format: "Oct 4, 2025")
            const dateMatch = dateText.match(/(\w{3})\s+(\d{1,2}),\s+(\d{4})/);
            if (dateMatch) {
                const [, month, day, year] = dateMatch;
                const monthMap = {
                    'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
                    'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
                    'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
                };
                const formattedDate = `${year}-${monthMap[month]}-${day.padStart(2, '0')}`;

                if (startDate && formattedDate < startDate) {
                    matchesDateRange = false;
                }
                if (endDate && formattedDate > endDate) {
                    matchesDateRange = false;
                }
            } else {
                matchesDateRange = false;
            }
        }

        return matchesSearch && matchesAmenity && matchesDateRange;
    });

    currentPage = 1;
    renderTable();
}

// Sort table by column
function sortTable(columnIndex) {
    if (sortColumn === columnIndex) {
        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn = columnIndex;
        sortDirection = 'asc';
    }

    filteredRows.sort((a, b) => {
        const aValue = a.querySelectorAll('td')[columnIndex]?.textContent.trim() || '';
        const bValue = b.querySelectorAll('td')[columnIndex]?.textContent.trim() || '';

        // Handle numeric columns
        if (columnIndex === 2 || columnIndex === 3 || columnIndex === 4) {
            const aNum = parseFloat(aValue.replace(/[₹,]/g, '')) || 0;
            const bNum = parseFloat(bValue.replace(/[₹,]/g, '')) || 0;
            return sortDirection === 'asc' ? aNum - bNum : bNum - aNum;
        }

        // Handle date column
        if (columnIndex === 0) {
            const aDate = new Date(aValue);
            const bDate = new Date(bValue);
            return sortDirection === 'asc' ? aDate - bDate : bDate - aDate;
        }

        // Handle text columns
        return sortDirection === 'asc' ?
            aValue.localeCompare(bValue) :
            bValue.localeCompare(aValue);
    });

    renderTable();
}

// Render table with pagination
function renderTable() {
    const tbody = document.getElementById('tableBody');
    const recordCount = document.getElementById('recordCount');

    // Update record count
    recordCount.textContent = filteredRows.length;

    // Calculate pagination
    const totalPages = Math.ceil(filteredRows.length / pageSize);
    const startIndex = (currentPage - 1) * pageSize;
    const endIndex = startIndex + pageSize;
    const pageRows = filteredRows.slice(startIndex, endIndex);

    // Clear and populate table
    tbody.innerHTML = '';
    pageRows.forEach(row => tbody.appendChild(row.cloneNode(true)));

    // Update pagination
    updatePagination(totalPages);
}

// Update pagination controls
function updatePagination(totalPages) {
    const pagination = document.getElementById('pagination');
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';

    // Previous button
    html += `<button onclick="changePage(${currentPage - 1})"
                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}"
                ${currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
             </button>`;

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        html += `<button onclick="changePage(1)" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
        if (startPage > 2) {
            html += `<span class="px-2 py-1 text-gray-500">...</span>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        html += `<button onclick="changePage(${i})"
                    class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm ${i === currentPage ? 'bg-blue-500 text-white border-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}">
                    ${i}
                 </button>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<span class="px-2 py-1 text-gray-500">...</span>`;
        }
        html += `<button onclick="changePage(${totalPages})" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm hover:bg-gray-50 dark:hover:bg-gray-700">${totalPages}</button>`;
    }

    // Next button
    html += `<button onclick="changePage(${currentPage + 1})"
                class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}"
                ${currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
             </button>`;

    pagination.innerHTML = html;
}

// Change page
function changePage(page) {
    const totalPages = Math.ceil(filteredRows.length / pageSize);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderTable();
    }
}

// Clear date filter
function clearDateFilter() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    filterTable();
}

// Set date range for quick filters
function setDateRange(range) {
    const today = new Date();
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    let startDate, endDate;

    switch(range) {
        case 'today':
            startDate = endDate = today;
            break;
        case 'week':
            startDate = new Date(today);
            startDate.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)
            endDate = new Date(today);
            endDate.setDate(today.getDate() + (6 - today.getDay())); // End of week (Saturday)
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'last30':
            startDate = new Date(today);
            startDate.setDate(today.getDate() - 30);
            endDate = today;
            break;
        default:
            return;
    }

    // Format dates as YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    startDateInput.value = formatDate(startDate);
    endDateInput.value = formatDate(endDate);

    filterTable();
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDataTable();
});
</script>
@endsection
