@extends('layouts.app')

@section('title', 'Record Payment')

@php
    $title = 'Record Payment';
    $subtitle = 'Add a new payment for an invoice';
    $showBackButton = true;
    $backUrl = route('payments.index');
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" style="background-color: var(--card-bg); border-color: var(--border-color);">
        <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
            @csrf

            <div class="space-y-6">
                <!-- Invoice Selection -->
                <div>
                    <label for="invoice_id" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Select Invoice <span class="text-red-500">*</span>
                    </label>
                    <select name="invoice_id" id="invoice_id" required onchange="handleInvoiceChange()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Choose an invoice...</option>
                        @foreach($invoices as $invoiceOption)
                            <option value="{{ $invoiceOption->id }}"
                                data-balance="{{ $invoiceOption->balance_amount }}"
                                data-tenant="{{ $invoiceOption->tenantProfile->user->name }}"
                                data-total="{{ $invoiceOption->total_amount }}"
                                data-paid="{{ $invoiceOption->paid_amount }}"
                                {{ ($invoice && $invoice->id == $invoiceOption->id) || old('invoice_id') == $invoiceOption->id ? 'selected' : '' }}>
                                {{ $invoiceOption->invoice_number }} - {{ $invoiceOption->tenantProfile->user->name }}
                                (Balance: ₹{{ number_format($invoiceOption->balance_amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Invoice Details (shown when invoice is selected) -->
                <div id="invoiceDetails" class="bg-gray-50 rounded-lg p-4" style="background-color: var(--bg-secondary); {{ $invoice ? 'display: block;' : 'display: none;' }}">
                    <h3 class="text-lg font-semibold mb-3" style="color: var(--text-primary);">Invoice Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm font-medium" style="color: var(--text-secondary);">Tenant</p>
                            <p id="invoiceTenant" class="text-sm" style="color: var(--text-primary);">
                                {{ $invoice ? $invoice->tenantProfile->user->name : '' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Amount</p>
                            <p id="invoiceTotal" class="text-sm font-medium text-blue-600">
                                {{ $invoice ? $invoice->formatted_total_amount : '' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium" style="color: var(--text-secondary);">Paid Amount</p>
                            <p id="invoicePaid" class="text-sm font-medium text-green-600">
                                {{ $invoice ? $invoice->formatted_paid_amount : '' }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                            <span class="text-sm font-medium text-yellow-800">
                                Outstanding Balance: <span id="invoiceBalance" class="font-bold">
                                    {{ $invoice ? $invoice->formatted_balance_amount : '' }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Payment Amount (₹) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="amount" id="amount"
                                   value="{{ old('amount', $invoice ? $invoice->balance_amount : '') }}"
                                   min="0.01" step="0.01" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Enter payment amount">
                            <button type="button" onclick="setFullBalance()"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded hover:bg-blue-200 transition-colors duration-200">
                                Full
                            </button>
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Payment Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="payment_date" id="payment_date"
                               value="{{ old('payment_date', date('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @error('payment_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method" id="payment_method" required onchange="handlePaymentMethodChange()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select payment method...</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="upi" {{ old('payment_method') == 'upi' ? 'selected' : '' }}>UPI</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method Specific Fields -->
                <div id="paymentMethodFields" class="space-y-4" style="display: none;">
                    <!-- Reference Number -->
                    <div id="referenceNumberField">
                        <label for="reference_number" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                            Reference Number
                        </label>
                        <input type="text" name="reference_number" id="reference_number"
                               value="{{ old('reference_number') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                               placeholder="Transaction ID, UTR, Cheque number, etc.">
                        @error('reference_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bank Details -->
                    <div id="bankFields" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                                Bank Name
                            </label>
                            <input type="text" name="bank_name" id="bank_name"
                                   value="{{ old('bank_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Bank name">
                            @error('bank_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_number" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                                Account Number
                            </label>
                            <input type="text" name="account_number" id="account_number"
                                   value="{{ old('account_number') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                                   placeholder="Account number">
                            @error('account_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
                        Notes
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              style="background-color: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);"
                              placeholder="Additional notes about the payment...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('payments.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function handleInvoiceChange() {
    const select = document.getElementById('invoice_id');
    const option = select.options[select.selectedIndex];
    const detailsDiv = document.getElementById('invoiceDetails');

    if (option.value) {
        // Show invoice details
        detailsDiv.style.display = 'block';

        // Update details
        document.getElementById('invoiceTenant').textContent = option.dataset.tenant;
        document.getElementById('invoiceTotal').textContent = `₹${parseFloat(option.dataset.total).toFixed(2)}`;
        document.getElementById('invoicePaid').textContent = `₹${parseFloat(option.dataset.paid).toFixed(2)}`;
        document.getElementById('invoiceBalance').textContent = `₹${parseFloat(option.dataset.balance).toFixed(2)}`;

        // Set default payment amount to balance
        document.getElementById('amount').value = parseFloat(option.dataset.balance).toFixed(2);
    } else {
        detailsDiv.style.display = 'none';
        document.getElementById('amount').value = '';
    }
}

function handlePaymentMethodChange() {
    const method = document.getElementById('payment_method').value;
    const fieldsDiv = document.getElementById('paymentMethodFields');
    const bankFields = document.getElementById('bankFields');
    const referenceField = document.getElementById('referenceNumberField');

    if (method && method !== 'cash') {
        fieldsDiv.style.display = 'block';
        referenceField.style.display = 'block';

        // Show bank fields for bank transfer and cheque
        if (method === 'bank_transfer' || method === 'cheque') {
            bankFields.style.display = 'grid';
        } else {
            bankFields.style.display = 'none';
        }

        // Update placeholder text based on method
        const referenceInput = document.getElementById('reference_number');
        switch(method) {
            case 'bank_transfer':
                referenceInput.placeholder = 'UTR Number or Transaction ID';
                break;
            case 'upi':
                referenceInput.placeholder = 'UPI Transaction ID';
                break;
            case 'card':
                referenceInput.placeholder = 'Card Transaction ID';
                break;
            case 'cheque':
                referenceInput.placeholder = 'Cheque Number';
                break;
            default:
                referenceInput.placeholder = 'Reference Number';
        }
    } else {
        fieldsDiv.style.display = 'none';
    }
}

function setFullBalance() {
    const select = document.getElementById('invoice_id');
    const option = select.options[select.selectedIndex];

    if (option.value && option.dataset.balance) {
        document.getElementById('amount').value = parseFloat(option.dataset.balance).toFixed(2);
    }
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    const invoiceSelect = document.getElementById('invoice_id');
    const methodSelect = document.getElementById('payment_method');

    if (invoiceSelect.value) {
        handleInvoiceChange();
    }

    if (methodSelect.value) {
        handlePaymentMethodChange();
    }
});
</script>
@endsection
