@extends('layouts.app')

@section('title', 'Payment #' . $payment->payment_number)

@php
    $title = 'Payment #' . $payment->payment_number;
    $subtitle = 'Payment details and transaction information';
    $showBackButton = true;
    $backUrl = route('payments.index');
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Payment Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Payment #{{ $payment->payment_number }}</h1>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">
                    Recorded on {{ $payment->created_at->format('M j, Y g:i A') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $payment->status_badge['class'] }}">
                    {{ $payment->status_badge['text'] }}
                </span>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $payment->method_badge['class'] }}">
                    {{ $payment->method_badge['text'] }}
                </span>
                @if($payment->is_verified)
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        Verified
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <h3 class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Payment Information</h3>
                <div class="space-y-1">
                    <p class="text-2xl font-bold text-green-600">{{ $payment->formatted_amount }}</p>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <span class="font-medium">Date:</span> {{ $payment->payment_date->format('M j, Y') }}
                    </p>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <span class="font-medium">Method:</span> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                    </p>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Invoice Details</h3>
                <div class="space-y-1">
                    <p class="font-medium" style="color: var(--text-primary);">
                        <a href="{{ route('invoices.show', $payment->invoice->id) }}"
                           class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                            {{ $payment->invoice->invoice_number }}
                        </a>
                    </p>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <span class="font-medium">Type:</span> {{ ucfirst($payment->invoice->type) }}
                    </p>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <span class="font-medium">Total:</span> {{ $payment->invoice->formatted_total_amount }}
                    </p>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Tenant Information</h3>
                <div class="space-y-1">
                    <p class="font-medium" style="color: var(--text-primary);">{{ $payment->tenantProfile->user->name }}</p>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ $payment->tenantProfile->user->email }}</p>
                    @if($payment->tenantProfile->currentBed)
                        <p class="text-sm" style="color: var(--text-secondary);">
                            Room {{ $payment->tenantProfile->currentBed->room->room_number }},
                            Bed {{ $payment->tenantProfile->currentBed->bed_number }}
                        </p>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Actions</h3>
                <div class="space-y-2">
                    @if(!$payment->is_verified && $payment->status === 'completed')
                        <form method="POST" action="{{ route('payments.verify', $payment->id) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200">
                                <i class="fas fa-check mr-1"></i>
                                Verify Payment
                            </button>
                        </form>
                    @endif

                    @if(!$payment->is_verified)
                        <a href="{{ route('payments.edit', $payment->id) }}"
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 inline-block text-center">
                            <i class="fas fa-edit mr-1"></i>
                            Edit Payment
                        </a>
                    @endif

                    <a href="{{ route('invoices.show', $payment->invoice->id) }}"
                       class="w-full bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 inline-block text-center">
                        <i class="fas fa-file-invoice mr-1"></i>
                        View Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Payment Details</h3>

                <div class="space-y-4">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                            <h4 class="font-medium mb-2" style="color: var(--text-primary);">Amount</h4>
                            <p class="text-2xl font-bold text-green-600">{{ $payment->formatted_amount }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary);">
                            <h4 class="font-medium mb-2" style="color: var(--text-primary);">Payment Date</h4>
                            <p class="text-lg font-medium" style="color: var(--text-primary);">{{ $payment->payment_date->format('M j, Y') }}</p>
                        </div>
                    </div>

                    <!-- Payment Method Details -->
                    <div class="border border-gray-200 rounded-lg p-4" style="border-color: var(--border-color);">
                        <h4 class="font-medium mb-3" style="color: var(--text-primary);">Payment Method Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium" style="color: var(--text-secondary);">Method</p>
                                <p class="text-sm" style="color: var(--text-primary);">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            </div>

                            @if($payment->reference_number)
                                <div>
                                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Reference Number</p>
                                    <p class="text-sm font-mono" style="color: var(--text-primary);">{{ $payment->reference_number }}</p>
                                </div>
                            @endif

                            @if($payment->bank_name)
                                <div>
                                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Bank Name</p>
                                    <p class="text-sm" style="color: var(--text-primary);">{{ $payment->bank_name }}</p>
                                </div>
                            @endif

                            @if($payment->account_number)
                                <div>
                                    <p class="text-sm font-medium" style="color: var(--text-secondary);">Account Number</p>
                                    <p class="text-sm font-mono" style="color: var(--text-primary);">{{ $payment->account_number }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($payment->notes)
                        <div class="border border-gray-200 rounded-lg p-4" style="border-color: var(--border-color);">
                            <h4 class="font-medium mb-2" style="color: var(--text-primary);">Notes</h4>
                            <p class="text-sm whitespace-pre-wrap" style="color: var(--text-secondary);">{{ $payment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Status Information</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Status:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->status_badge['class'] }}">
                            {{ $payment->status_badge['text'] }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Verified:</span>
                        @if($payment->is_verified)
                            <span class="text-green-600 font-medium">
                                <i class="fas fa-check-circle mr-1"></i>
                                Yes
                            </span>
                        @else
                            <span class="text-yellow-600 font-medium">
                                <i class="fas fa-clock mr-1"></i>
                                Pending
                            </span>
                        @endif
                    </div>

                    @if($payment->is_verified)
                        <div class="pt-2 border-t" style="border-color: var(--border-color);">
                            <p class="text-sm" style="color: var(--text-secondary);">
                                <span class="font-medium">Verified by:</span> {{ $payment->verifiedBy->name ?? 'Unknown' }}
                            </p>
                            <p class="text-sm" style="color: var(--text-secondary);">
                                <span class="font-medium">Verified on:</span> {{ $payment->verified_at->format('M j, Y g:i A') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invoice Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Invoice Summary</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Invoice Total:</span>
                        <span class="font-medium" style="color: var(--text-primary);">{{ $payment->invoice->formatted_total_amount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Total Paid:</span>
                        <span class="font-medium text-green-600">{{ $payment->invoice->formatted_paid_amount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Balance:</span>
                        <span class="font-medium text-red-600">{{ $payment->invoice->formatted_balance_amount }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('invoices.show', $payment->invoice->id) }}"
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200 inline-block text-center">
                        <i class="fas fa-file-invoice mr-1"></i>
                        View Full Invoice
                    </a>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Payment Information</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="font-medium" style="color: var(--text-secondary);">Recorded by:</span>
                        <p style="color: var(--text-primary);">{{ $payment->recordedBy->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <span class="font-medium" style="color: var(--text-secondary);">Recorded on:</span>
                        <p style="color: var(--text-primary);">{{ $payment->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @if($payment->updated_at != $payment->created_at)
                        <div>
                            <span class="font-medium" style="color: var(--text-secondary);">Last updated:</span>
                            <p style="color: var(--text-primary);">{{ $payment->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
