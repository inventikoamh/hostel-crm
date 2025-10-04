@extends('tenant.layout')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold" style="color: var(--text-primary);">Welcome back, {{ $user->name }}!</h2>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">Here's an overview of your hostel stay</p>
            </div>
            <div class="text-right">
                <p class="text-sm" style="color: var(--text-secondary);">Current Status</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    <i class="fas fa-check-circle mr-1"></i>
                    Active
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Outstanding Amount -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center" style="background-color: var(--danger-bg);">
                        <i class="fas fa-exclamation-triangle text-red-600" style="color: var(--danger-text);"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Outstanding Amount</p>
                    <p class="text-2xl font-semibold" style="color: var(--text-primary);">₹{{ number_format($outstandingAmount, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Total Invoices -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center" style="background-color: var(--primary-bg);">
                        <i class="fas fa-file-invoice text-blue-600" style="color: var(--primary-text);"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Invoices</p>
                    <p class="text-2xl font-semibold" style="color: var(--text-primary);">{{ $invoices->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total Payments -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center" style="background-color: var(--success-bg);">
                        <i class="fas fa-credit-card text-green-600" style="color: var(--success-text);"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Payments</p>
                    <p class="text-2xl font-semibold" style="color: var(--text-primary);">{{ $payments->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Bed Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center" style="background-color: var(--info-bg);">
                        <i class="fas fa-bed text-purple-600" style="color: var(--info-text);"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Bed Number</p>
                    <p class="text-2xl font-semibold" style="color: var(--text-primary);">{{ $tenantProfile->bed->bed_number ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">Recent Invoices</h3>
                <a href="{{ route('tenant.invoices') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200" style="color: var(--primary-text);">
                    View all
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="p-6">
            @if($recentInvoices->count() > 0)
                <div class="space-y-4">
                    @foreach($recentInvoices->take(5) as $invoice)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200" style="border-color: var(--border-color);">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center" style="background-color: var(--primary-bg);">
                                        <i class="fas fa-file-invoice text-blue-600" style="color: var(--primary-text);"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium" style="color: var(--text-primary);">Invoice #{{ $invoice->invoice_number }}</p>
                                    <p class="text-xs" style="color: var(--text-secondary);">{{ $invoice->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium" style="color: var(--text-primary);">₹{{ number_format($invoice->total_amount, 2) }}</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($invoice->status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($invoice->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($invoice->status === 'overdue') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-file-invoice text-gray-400 text-3xl mb-3"></i>
                    <p class="text-sm" style="color: var(--text-secondary);">No invoices found</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Bed Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">My Bed Information</h3>
            </div>
            <div class="p-6">
                @if($tenantProfile->bed)
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm" style="color: var(--text-secondary);">Bed Number:</span>
                            <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenantProfile->bed->bed_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm" style="color: var(--text-secondary);">Room:</span>
                            <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->room_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm" style="color: var(--text-secondary);">Hostel:</span>
                            <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->hostel->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm" style="color: var(--text-secondary);">Floor:</span>
                            <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenantProfile->bed->room->floor }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('tenant.bed-info') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200" style="color: var(--primary-text);">
                            View detailed information
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-bed text-gray-400 text-2xl mb-2"></i>
                        <p class="text-sm" style="color: var(--text-secondary);">No bed assigned</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Profile Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
            <div class="p-6 border-b border-gray-100" style="border-color: var(--border-color);">
                <h3 class="text-lg font-medium" style="color: var(--text-primary);">Profile Information</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm" style="color: var(--text-secondary);">Name:</span>
                        <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm" style="color: var(--text-secondary);">Email:</span>
                        <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm" style="color: var(--text-secondary);">Phone:</span>
                        <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $user->phone ?: 'Not provided' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm" style="color: var(--text-secondary);">Move-in Date:</span>
                        <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $tenantProfile->move_in_date ? $tenantProfile->move_in_date->format('M d, Y') : 'Not set' }}</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('tenant.profile') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200" style="color: var(--primary-text);">
                        Edit profile
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
