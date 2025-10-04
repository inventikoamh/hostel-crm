<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
        }

        .company-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .invoice-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .company-tagline {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .invoice-number {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .billing-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .billing-from, .billing-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
            background-color: #f8f9ff;
            border: 1px solid #e5e7eb;
        }

        .billing-from {
            border-right: none;
        }

        .billing-label {
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .billing-details {
            color: #333;
        }

        .invoice-details {
            margin-bottom: 30px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .details-table th {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table th {
            background-color: #4F46E5;
            color: white;
            font-weight: bold;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .totals-section {
            width: 100%;
            margin-top: 20px;
        }

        .totals-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .totals-table .label {
            text-align: right;
            font-weight: bold;
            color: #666;
        }

        .totals-table .amount {
            text-align: right;
            font-weight: bold;
        }

        .total-row {
            background-color: #4F46E5;
            color: white;
            font-size: 16px;
        }

        .total-row td {
            border-bottom: none;
            padding: 15px;
        }

        .payment-info {
            margin-top: 30px;
            padding: 20px;
            background-color: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 5px;
        }

        .payment-status {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status-paid {
            color: #059669;
        }

        .status-pending {
            color: #dc2626;
        }

        .status-partial {
            color: #d97706;
        }

        .notes-section {
            margin-top: 30px;
        }

        .notes-title {
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .notes-content {
            color: #666;
            line-height: 1.6;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 4px solid #4F46E5;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .overdue-notice {
            background-color: #fef2f2;
            border: 2px solid #dc2626;
            color: #dc2626;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        @page {
            margin: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">Hostel CRM</div>
                <div class="company-tagline">Professional Hostel Management</div>
                <div>
                    Email: admin@hostelcrm.com<br>
                    Phone: +91 9876543210<br>
                    Website: www.hostelcrm.com
                </div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                <div>
                    <strong>Date:</strong> {{ $invoice->invoice_date->format('d M, Y') }}<br>
                    <strong>Due Date:</strong> {{ $invoice->due_date->format('d M, Y') }}
                </div>
            </div>
        </div>

        <!-- Overdue Notice -->
        @if($invoice->is_overdue)
            <div class="overdue-notice">
                ⚠️ OVERDUE NOTICE: This invoice is {{ $invoice->days_overdue }} days past due. Please settle immediately.
            </div>
        @endif

        <!-- Billing Information -->
        <div class="billing-info">
            <div class="billing-from">
                <div class="billing-label">FROM:</div>
                <div class="billing-details">
                    <strong>Hostel CRM</strong><br>
                    Professional Hostel Management<br>
                    123 Management Street<br>
                    City, State 12345<br>
                    GST: 12ABCDE3456F7GH
                </div>
            </div>
            <div class="billing-to">
                <div class="billing-label">BILL TO:</div>
                <div class="billing-details">
                    <strong>{{ $invoice->tenantProfile->user->name }}</strong><br>
                    {{ $invoice->tenantProfile->user->email }}<br>
                    @if($invoice->tenantProfile->currentBed)
                        Room {{ $invoice->tenantProfile->currentBed->room->room_number }},
                        Bed {{ $invoice->tenantProfile->currentBed->bed_number }}<br>
                    @endif
                    Phone: {{ $invoice->tenantProfile->phone ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <table class="details-table">
                <tr>
                    <th>Invoice Type</th>
                    <td>{{ ucfirst($invoice->type) }}</td>
                    <th>Status</th>
                    <td>{{ ucfirst($invoice->status) }}</td>
                </tr>
                @if($invoice->period_start && $invoice->period_end)
                    <tr>
                        <th>Billing Period</th>
                        <td colspan="3">{{ $invoice->period_start->format('d M, Y') }} to {{ $invoice->period_end->format('d M, Y') }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Invoice Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            {{ $item->description }}
                            @if($item->period_text)
                                <br><small style="color: #666;">{{ $item->period_text }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">₹{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="amount">₹{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->tax_amount > 0)
                    <tr>
                        <td class="label">Tax:</td>
                        <td class="amount">₹{{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                @endif
                @if($invoice->discount_amount > 0)
                    <tr>
                        <td class="label">Discount:</td>
                        <td class="amount" style="color: #dc2626;">-₹{{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td class="label">TOTAL:</td>
                    <td class="amount">₹{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <div class="payment-status
                @if($invoice->status === 'paid') status-paid
                @elseif($invoice->paid_amount > 0) status-partial
                @else status-pending
                @endif">
                Payment Status: {{ $invoice->payment_status }}
            </div>
            <div>
                <strong>Amount Paid:</strong> ₹{{ number_format($invoice->paid_amount, 2) }}<br>
                <strong>Balance Due:</strong> ₹{{ number_format($invoice->balance_amount, 2) }}
            </div>

            @if($invoice->payments->count() > 0)
                <div style="margin-top: 15px;">
                    <strong>Payment History:</strong><br>
                    @foreach($invoice->payments as $payment)
                        <div style="margin-top: 5px; font-size: 11px;">
                            {{ $payment->payment_date->format('d M, Y') }} -
                            ₹{{ number_format($payment->amount, 2) }} via
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            @if($payment->reference_number)
                                (Ref: {{ $payment->reference_number }})
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Notes -->
        @if($invoice->notes)
            <div class="notes-section">
                <div class="notes-title">Notes:</div>
                <div class="notes-content">
                    {{ $invoice->notes }}
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>Thank you for your business!</div>
            <div style="margin-top: 10px;">
                This is a computer-generated invoice. For queries, contact us at admin@hostelcrm.com
            </div>
            <div style="margin-top: 5px;">
                Generated on {{ now()->format('d M, Y H:i:s') }}
            </div>
        </div>
    </div>
</body>
</html>
