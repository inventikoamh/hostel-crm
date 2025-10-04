@extends('tenant.layout')

@section('title', 'My Invoices')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold" style="color: var(--text-primary);">My Invoices</h2>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">View all your invoices and payment history</p>
            </div>
        </div>
    </div>

    <!-- Invoices List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6">
            @if($invoices->count() > 0)
                <div class="space-y-4">
                    @foreach($invoices as $invoice)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200" style="border-color: var(--border-color);">
                            <div class="flex items-center justify-between">
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
                                <div class="ml-4">
                                    <a href="{{ route('tenant.invoice.show', $invoice) }}"
                                       class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                       style="color: var(--primary-text);">
                                        View Details
                                        <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($invoices->hasPages())
                    <div class="mt-6">
                        {{ $invoices->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <i class="fas fa-file-invoice text-gray-400 text-3xl mb-3"></i>
                    <p class="text-sm" style="color: var(--text-secondary);">No invoices found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
