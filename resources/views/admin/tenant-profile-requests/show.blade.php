@extends('layouts.app')

@section('title', 'Profile Update Request Details')

@php
    $title = 'Profile Update Request Details';
    $subtitle = 'Review tenant profile changes';
    $showBackButton = true;
    $backUrl = route('admin.tenant-profile-requests.index');
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Request Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tenant Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Tenant Information</h2>
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center" style="background-color: var(--bg-secondary);">
                                @if($tenantProfileRequest->tenantProfile->user->avatar)
                                    <img src="{{ asset('storage/' . $tenantProfileRequest->tenantProfile->user->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <i class="fas fa-user text-2xl" style="color: var(--text-secondary);"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">{{ $tenantProfileRequest->tenantProfile->user->name }}</h3>
                                <p class="text-sm" style="color: var(--text-secondary);">{{ $tenantProfileRequest->tenantProfile->user->email }}</p>
                                <p class="text-sm" style="color: var(--text-secondary);">Requested: {{ $tenantProfileRequest->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Changes Comparison -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4" style="color: var(--text-primary);">Profile Changes</h2>

                        @php
                            $changes = $tenantProfileRequest->requested_changes;
                            $hasChanges = false;
                        @endphp

                        <!-- Profile Image Changes -->
                        @if(isset($changes['user']['avatar']))
                            @php $hasChanges = true; @endphp
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-3" style="color: var(--text-primary);">Profile Picture</h3>
                                <div class="flex items-center space-x-6">
                                    <div class="text-center">
                                        <p class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Current</p>
                                        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center" style="background-color: var(--bg-secondary);">
                                            @if($tenantProfileRequest->tenantProfile->user->avatar)
                                                <img src="{{ asset('storage/' . $tenantProfileRequest->tenantProfile->user->avatar) }}" alt="Current Avatar" class="w-20 h-20 rounded-full object-cover">
                                            @else
                                                <i class="fas fa-user text-xl" style="color: var(--text-secondary);"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Requested</p>
                                        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center" style="background-color: var(--bg-secondary);">
                                            <img src="{{ asset('storage/' . $changes['user']['avatar']) }}" alt="New Avatar" class="w-20 h-20 rounded-full object-cover">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- User Information Changes -->
                        @if(isset($changes['user']) && count(array_filter($changes['user'], function($v, $k) { return $k !== 'avatar' && !empty($v); }, ARRAY_FILTER_USE_BOTH)) > 0)
                            @php $hasChanges = true; @endphp
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-3" style="color: var(--text-primary);">User Information</h3>
                                <div class="space-y-4">
                                    @foreach($changes['user'] as $field => $newValue)
                                        @if($field !== 'avatar' && !empty($newValue))
                                            @php
                                                $currentValue = '';
                                                switch($field) {
                                                    case 'name':
                                                        $currentValue = $tenantProfileRequest->tenantProfile->user->name;
                                                        break;
                                                    case 'phone':
                                                        $currentValue = $tenantProfileRequest->tenantProfile->user->phone ?? '';
                                                        break;
                                                }
                                            @endphp
                                            <div class="p-4 rounded-lg border" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                                                <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <p class="text-xs font-medium mb-1" style="color: var(--text-secondary);">Current:</p>
                                                        <p class="text-sm" style="color: var(--text-primary);">{{ $currentValue ?: 'Not set' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs font-medium mb-1" style="color: var(--text-secondary);">Requested:</p>
                                                        <p class="text-sm font-medium" style="color: var(--primary-color, #3b82f6);">{{ $newValue }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Tenant Profile Changes -->
                        @if(isset($changes['tenant_profile']) && count(array_filter($changes['tenant_profile'])) > 0)
                            @php $hasChanges = true; @endphp
                            <div class="mb-6">
                                <h3 class="text-lg font-medium mb-3" style="color: var(--text-primary);">Profile Information</h3>
                                <div class="space-y-4">
                                    @foreach($changes['tenant_profile'] as $field => $newValue)
                                        @if(!empty($newValue))
                                            @php
                                                $currentValue = '';
                                                switch($field) {
                                                    case 'date_of_birth':
                                                        $currentValue = $tenantProfileRequest->tenantProfile->date_of_birth ? $tenantProfileRequest->tenantProfile->date_of_birth->format('Y-m-d') : '';
                                                        break;
                                                    case 'address':
                                                        $currentValue = $tenantProfileRequest->tenantProfile->address ?? '';
                                                        break;
                                                    case 'occupation':
                                                        $currentValue = $tenantProfileRequest->tenantProfile->occupation ?? '';
                                                        break;
                                                    case 'company':
                                                        $currentValue = $tenantProfileRequest->tenantProfile->company ?? '';
                                                        break;
                                                    case 'emergency_contact_name':
                                                        $currentValue = $tenantProfileRequest->tenantProfile->emergency_contact_name ?? '';
                                                        break;
                                                    case 'emergency_contact_phone':
                                                        $currentValue = $tenantProfileRequest->tenantProfile->emergency_contact_phone ?? '';
                                                        break;
                                                    case 'emergency_contact_relation':
                                                        $currentValue = $tenantProfileRequest->tenantProfile->emergency_contact_relation ?? '';
                                                        break;
                                                }
                                            @endphp
                                            <div class="p-4 rounded-lg border" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                                                <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <p class="text-xs font-medium mb-1" style="color: var(--text-secondary);">Current:</p>
                                                        <p class="text-sm" style="color: var(--text-primary);">{{ $currentValue ?: 'Not set' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs font-medium mb-1" style="color: var(--text-secondary);">Requested:</p>
                                                        <p class="text-sm font-medium" style="color: var(--primary-color, #3b82f6);">{{ $newValue }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!$hasChanges)
                            <div class="text-center py-8">
                                <i class="fas fa-info-circle text-4xl mb-4" style="color: var(--text-secondary);"></i>
                                <p class="text-lg font-medium" style="color: var(--text-primary);">No changes detected</p>
                                <p class="text-sm" style="color: var(--text-secondary);">This request doesn't contain any profile changes.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Request Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Request Status</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm" style="color: var(--text-secondary);">Status:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $tenantProfileRequest->status_badge['class'] }}">
                                    <i class="{{ $tenantProfileRequest->status_badge['icon'] }} mr-1"></i>
                                    {{ ucfirst($tenantProfileRequest->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm" style="color: var(--text-secondary);">Requested:</span>
                                <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenantProfileRequest->created_at->format('M d, Y') }}</span>
                            </div>
                            @if($tenantProfileRequest->reviewed_at)
                                <div class="flex justify-between">
                                    <span class="text-sm" style="color: var(--text-secondary);">Reviewed:</span>
                                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenantProfileRequest->reviewed_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm" style="color: var(--text-secondary);">Reviewed By:</span>
                                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenantProfileRequest->reviewedBy->name ?? 'Unknown' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Admin Actions -->
                @if($tenantProfileRequest->status === 'pending')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Admin Actions</h3>

                            <!-- Approve Form -->
                            <form method="POST" action="{{ route('admin.tenant-profile-requests.approve', $tenantProfileRequest) }}" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Notes (Optional)</label>
                                    <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" placeholder="Add notes for approval..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <i class="fas fa-check mr-2"></i>
                                    Approve Request
                                </button>
                            </form>

                            <!-- Reject Form -->
                            <form method="POST" action="{{ route('admin.tenant-profile-requests.reject', $tenantProfileRequest) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Rejection Reason <span class="text-red-500">*</span></label>
                                    <textarea name="admin_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" placeholder="Please provide a reason for rejection..." required></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                    <i class="fas fa-times mr-2"></i>
                                    Reject Request
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Admin Notes -->
                @if($tenantProfileRequest->admin_notes)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Admin Notes</h3>
                            <p class="text-sm" style="color: var(--text-primary);">{{ $tenantProfileRequest->admin_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
    </div>
</div>
@endsection
