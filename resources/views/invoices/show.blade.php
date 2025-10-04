@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number)

@php
    $title = 'Invoice #' . $invoice->invoice_number;
    $subtitle = 'Invoice details and payment information';
    $showBackButton = true;
    $backUrl = route('invoices.index');
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Invoice Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Invoice #{{ $invoice->invoice_number }}</h1>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">
                    Created on {{ $invoice->created_at->format('M j, Y g:i A') }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $invoice->status_badge['class'] }}">
                    {{ $invoice->status_badge['text'] }}
                </span>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $invoice->type_badge['class'] }}">
                    {{ $invoice->type_badge['text'] }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <h3 class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Tenant Information</h3>
                <div class="space-y-1">
                    <p class="font-medium" style="color: var(--text-primary);">{{ $invoice->tenantProfile->user->name }}</p>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ $invoice->tenantProfile->user->email }}</p>
                    @if($invoice->tenantProfile->currentBed)
                        <p class="text-sm" style="color: var(--text-secondary);">
                            Room {{ $invoice->tenantProfile->currentBed->room->room_number }},
                            Bed {{ $invoice->tenantProfile->currentBed->bed_number }}
                        </p>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Invoice Details</h3>
                <div class="space-y-1">
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <span class="font-medium">Date:</span> {{ $invoice->invoice_date->format('M j, Y') }}
                    </p>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <span class="font-medium">Due Date:</span> {{ $invoice->due_date->format('M j, Y') }}
                    </p>
                    @if($invoice->period_start && $invoice->period_end)
                        <p class="text-sm" style="color: var(--text-secondary);">
                            <span class="font-medium">Period:</span>
                            {{ $invoice->period_start->format('M j') }} - {{ $invoice->period_end->format('M j, Y') }}
                        </p>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Payment Status</h3>
                <div class="space-y-1">
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <span class="font-medium">Status:</span> {{ $invoice->payment_status }}
                    </p>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <span class="font-medium">Paid:</span> {{ $invoice->formatted_paid_amount }}
                    </p>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <span class="font-medium">Balance:</span> {{ $invoice->formatted_balance_amount }}
                    </p>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-medium mb-2" style="color: var(--text-secondary);">Actions</h3>
                <div class="space-y-2">
                    @if($invoice->status === 'draft')
                        <form method="POST" action="{{ route('invoices.send', $invoice->id) }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200">
                                <i class="fas fa-paper-plane mr-1"></i>
                                Send Invoice
                            </button>
                        </form>
                    @endif

                    @if($invoice->status !== 'paid')
                        <a href="{{ route('invoices.edit', $invoice->id) }}"
                           class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 inline-block text-center">
                            <i class="fas fa-edit mr-1"></i>
                            Edit Invoice
                        </a>
                    @endif

                    @if($invoice->balance_amount > 0)
                        <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}"
                           class="w-full bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 inline-block text-center">
                            <i class="fas fa-plus mr-1"></i>
                            Add Payment
                        </a>
                    @endif

                    <!-- PDF Actions -->
                    <div class="border-t pt-3 mt-3" style="border-color: var(--border-color);">
                        <div class="space-y-2">
                            <a href="{{ route('invoices.pdf.view', $invoice->id) }}" target="_blank"
                               class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 inline-block text-center">
                                <i class="fas fa-eye mr-1"></i>
                                View PDF
                            </a>
                            <a href="{{ route('invoices.pdf.download', $invoice->id) }}"
                               class="w-full bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200 inline-block text-center">
                                <i class="fas fa-download mr-1"></i>
                                Download PDF
                            </a>
                            <button onclick="emailPdf({{ $invoice->id }})"
                                    class="w-full bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded-lg text-sm transition-colors duration-200">
                                <i class="fas fa-envelope mr-1"></i>
                                Email PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Invoice Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Invoice Items</h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50" style="background-color: var(--bg-secondary);">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Unit Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="divide-color: var(--border-color);">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium" style="color: var(--text-primary);">{{ $item->description }}</div>
                                        @if($item->period_text)
                                            <div class="text-xs" style="color: var(--text-secondary);">{{ $item->period_text }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm" style="color: var(--text-primary);">{{ $item->quantity }}</td>
                                    <td class="px-4 py-4 text-sm" style="color: var(--text-primary);">{{ $item->formatted_unit_price }}</td>
                                    <td class="px-4 py-4 text-sm font-medium" style="color: var(--text-primary);">{{ $item->formatted_total_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Invoice Totals -->
                <div class="mt-6 border-t pt-4" style="border-color: var(--border-color);">
                    <div class="flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between">
                                <span style="color: var(--text-secondary);">Subtotal:</span>
                                <span class="font-medium" style="color: var(--text-primary);">₹{{ number_format($invoice->subtotal, 2) }}</span>
                            </div>
                            @if($invoice->tax_amount > 0)
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Tax:</span>
                                    <span class="font-medium" style="color: var(--text-primary);">₹{{ number_format($invoice->tax_amount, 2) }}</span>
                                </div>
                            @endif
                            @if($invoice->discount_amount > 0)
                                <div class="flex justify-between">
                                    <span style="color: var(--text-secondary);">Discount:</span>
                                    <span class="font-medium text-red-600">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <hr class="border-gray-300" style="border-color: var(--border-color);">
                            <div class="flex justify-between text-lg font-bold">
                                <span style="color: var(--text-primary);">Total:</span>
                                <span class="text-blue-600">{{ $invoice->formatted_total_amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($invoice->notes)
                    <div class="mt-6 border-t pt-4" style="border-color: var(--border-color);">
                        <h4 class="font-medium mb-2" style="color: var(--text-primary);">Notes</h4>
                        <p class="text-sm whitespace-pre-wrap" style="color: var(--text-secondary);">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Payment Summary</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Total Amount:</span>
                        <span class="font-medium" style="color: var(--text-primary);">{{ $invoice->formatted_total_amount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Paid Amount:</span>
                        <span class="font-medium text-green-600">{{ $invoice->formatted_paid_amount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span style="color: var(--text-secondary);">Balance:</span>
                        <span class="font-medium text-red-600">{{ $invoice->formatted_balance_amount }}</span>
                    </div>
                </div>

                @if($invoice->is_overdue)
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            <span class="text-sm font-medium text-red-800">
                                Overdue by {{ $invoice->days_overdue }} days
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Payments -->
            @if($invoice->payments->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Payments</h3>

                    <div class="space-y-3">
                        @foreach($invoice->payments as $payment)
                            <div class="border border-gray-200 rounded-lg p-3" style="border-color: var(--border-color);">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium" style="color: var(--text-primary);">{{ $payment->formatted_amount }}</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->status_badge['class'] }}">
                                        {{ $payment->status_badge['text'] }}
                                    </span>
                                </div>
                                <div class="text-xs space-y-1" style="color: var(--text-secondary);">
                                    <p>{{ $payment->payment_date->format('M j, Y') }} - {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                    @if($payment->reference_number)
                                        <p>Ref: {{ $payment->reference_number }}</p>
                                    @endif
                                    <p>By: {{ $payment->recordedBy->name }}</p>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('payments.show', $payment->id) }}"
                                       class="text-xs text-blue-600 hover:text-blue-800">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Invoice Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
                <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Invoice Information</h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="font-medium" style="color: var(--text-secondary);">Created by:</span>
                        <p style="color: var(--text-primary);">{{ $invoice->createdBy->name }}</p>
                    </div>
                    <div>
                        <span class="font-medium" style="color: var(--text-secondary);">Created on:</span>
                        <p style="color: var(--text-primary);">{{ $invoice->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @if($invoice->updated_at != $invoice->created_at)
                        <div>
                            <span class="font-medium" style="color: var(--text-secondary);">Last updated:</span>
                            <p style="color: var(--text-primary);">{{ $invoice->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function emailPdf(invoiceId) {
    const button = event.target;
    const originalText = button.innerHTML;

    // Show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Sending...';

    try {
        const response = await fetch(`/invoices/${invoiceId}/pdf/email`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            // Show success message
            showMessage('success', data.message);
        } else {
            showMessage('error', data.message || 'Failed to send email');
        }
    } catch (error) {
        showMessage('error', 'Network error occurred while sending email');
    } finally {
        // Reset button
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

function showMessage(type, message) {
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success'
            ? 'bg-green-500 text-white'
            : 'bg-red-500 text-white'
    }`;
    messageDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            ${message}
        </div>
    `;

    document.body.appendChild(messageDiv);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}
</script>
@endsection
