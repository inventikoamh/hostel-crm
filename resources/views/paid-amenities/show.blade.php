@extends('layouts.app')

@section('title', $paidAmenity->name)

@php
    $title = $paidAmenity->name;
    $subtitle = 'Service details and tenant assignments';
    $showBackButton = true;
    $backUrl = route('paid-amenities.index');
@endphp

@section('content')
<div class="space-y-6">
    <!-- Service Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="{{ $paidAmenity->icon_class }} text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold" style="color: var(--text-primary);">{{ $paidAmenity->name }}</h1>
                    <div class="flex items-center gap-4 mt-2">
                        <span class="px-3 py-1 text-sm font-medium rounded-full {{ $paidAmenity->status_badge['class'] }}">
                            {{ $paidAmenity->status_badge['text'] }}
                        </span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">
                            {{ $paidAmenity->category_display }}
                        </span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full {{ $paidAmenity->billing_type === 'monthly' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $paidAmenity->billing_type_display }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('paid-amenities.edit', $paidAmenity) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Edit Service
                </a>
                <button onclick="confirmDelete('{{ route('paid-amenities.destroy', $paidAmenity) }}')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Service Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Service Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h2 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Service Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Service Name</label>
                        <p class="text-base" style="color: var(--text-primary);">{{ $paidAmenity->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Category</label>
                        <p class="text-base" style="color: var(--text-primary);">{{ $paidAmenity->category_display }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Billing Type</label>
                        <p class="text-base" style="color: var(--text-primary);">{{ $paidAmenity->billing_type_display }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Price</label>
                        <p class="text-base font-semibold text-green-600">{{ $paidAmenity->formatted_price }}</p>
                    </div>

                    @if($paidAmenity->max_usage_per_day)
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Max Usage Per Day</label>
                            <p class="text-base" style="color: var(--text-primary);">{{ $paidAmenity->max_usage_per_day }} times</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Availability</label>
                        <p class="text-base" style="color: var(--text-primary);">{{ $paidAmenity->getAvailabilityText() }}</p>
                    </div>
                </div>

                @if($paidAmenity->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Description</label>
                        <p class="text-base leading-relaxed" style="color: var(--text-primary);">{{ $paidAmenity->description }}</p>
                    </div>
                @endif

                @if($paidAmenity->terms_conditions)
                    <div class="mt-6">
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Terms & Conditions</label>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-yellow-800">{{ $paidAmenity->terms_conditions }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tenant Assignments -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold" style="color: var(--text-primary);">Tenant Assignments</h2>
                    <a href="{{ route('tenant-amenities.create', ['paid_amenity_id' => $paidAmenity->id]) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Assign to Tenant
                    </a>
                </div>

                @if($paidAmenity->tenantAmenities->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50" style="background-color: var(--bg-secondary);">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Tenant</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Price</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Duration</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Usage</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="divide-color: var(--border-color);">
                                @foreach($paidAmenity->tenantAmenities as $tenantAmenity)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200" style="hover:background-color: var(--bg-secondary);">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-gray-600 text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium" style="color: var(--text-primary);">
                                                        {{ $tenantAmenity->tenantProfile->user->name }}
                                                    </div>
                                                    <div class="text-xs" style="color: var(--text-secondary);">
                                                        {{ $tenantAmenity->tenantProfile->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $tenantAmenity->status_badge['class'] }}">
                                                {{ $tenantAmenity->status_badge['text'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium" style="color: var(--text-primary);">
                                                {{ $tenantAmenity->formatted_effective_price }}
                                            </div>
                                            @if($tenantAmenity->custom_price)
                                                <div class="text-xs text-orange-600">Custom Price</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm" style="color: var(--text-primary);">
                                                {{ $tenantAmenity->duration_text }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if($paidAmenity->billing_type === 'daily')
                                                <div class="text-sm" style="color: var(--text-primary);">
                                                    {{ $tenantAmenity->usageRecords->count() }} records
                                                </div>
                                                <div class="text-xs" style="color: var(--text-secondary);">
                                                    Total: ₹{{ number_format($tenantAmenity->usageRecords->sum('total_amount'), 2) }}
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-500">Monthly billing</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('tenant-amenities.show', $tenantAmenity) }}"
                                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('tenant-amenities.edit', $tenantAmenity) }}"
                                                   class="text-green-600 hover:text-green-900 transition-colors duration-200">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-500 mb-2">No Tenant Assignments</h3>
                        <p class="text-gray-400 mb-4">This service hasn't been assigned to any tenants yet.</p>
                        <a href="{{ route('tenant-amenities.create', ['paid_amenity_id' => $paidAmenity->id]) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Assign to First Tenant
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Statistics</h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm" style="color: var(--text-secondary);">Total Assignments</span>
                        <span class="text-lg font-semibold" style="color: var(--text-primary);">{{ $paidAmenity->tenantAmenities->count() }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm" style="color: var(--text-secondary);">Active Assignments</span>
                        <span class="text-lg font-semibold text-green-600">{{ $paidAmenity->tenantAmenities->where('status', 'active')->count() }}</span>
                    </div>

                    @if($paidAmenity->billing_type === 'daily')
                        <div class="flex items-center justify-between">
                            <span class="text-sm" style="color: var(--text-secondary);">Total Usage Records</span>
                            <span class="text-lg font-semibold text-blue-600">{{ $paidAmenity->tenantAmenities->sum(function($ta) { return $ta->usageRecords->count(); }) }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm" style="color: var(--text-secondary);">Total Revenue</span>
                            <span class="text-lg font-semibold text-purple-600">₹{{ number_format($paidAmenity->tenantAmenities->sum(function($ta) { return $ta->usageRecords->sum('total_amount'); }), 2) }}</span>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <span class="text-sm" style="color: var(--text-secondary);">Monthly Revenue</span>
                            <span class="text-lg font-semibold text-purple-600">₹{{ number_format($paidAmenity->tenantAmenities->where('status', 'active')->sum('effective_price'), 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Quick Actions</h3>

                <div class="space-y-3">
                    <a href="{{ route('tenant-amenities.create', ['paid_amenity_id' => $paidAmenity->id]) }}"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Assign to Tenant
                    </a>

                    <a href="{{ route('paid-amenities.edit', $paidAmenity) }}"
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        Edit Service
                    </a>

                    @if($paidAmenity->is_active)
                        <button onclick="toggleStatus(false)"
                                class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-pause"></i>
                            Deactivate Service
                        </button>
                    @else
                        <button onclick="toggleStatus(true)"
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-play"></i>
                            Activate Service
                        </button>
                    @endif

                    <button onclick="confirmDelete('{{ route('paid-amenities.destroy', $paidAmenity) }}')"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        Delete Service
                    </button>
                </div>
            </div>

            <!-- Service Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Service Details</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Created:</span>
                        <span style="color: var(--text-primary);">{{ $paidAmenity->created_at->format('M j, Y') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Last Updated:</span>
                        <span style="color: var(--text-primary);">{{ $paidAmenity->updated_at->format('M j, Y') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Service ID:</span>
                        <span class="font-mono text-xs" style="color: var(--text-primary);">#{{ $paidAmenity->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(url) {
    if (confirm('Are you sure you want to delete this service? This action cannot be undone and will remove all tenant assignments.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';

        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleStatus(activate) {
    const action = activate ? 'activate' : 'deactivate';
    const message = activate ? 'activate' : 'deactivate';

    if (confirm(`Are you sure you want to ${message} this service?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("paid-amenities.bulk-action") }}';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;

        const idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'selected_ids[]';
        idsInput.value = '{{ $paidAmenity->id }}';

        form.appendChild(tokenInput);
        form.appendChild(actionInput);
        form.appendChild(idsInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
