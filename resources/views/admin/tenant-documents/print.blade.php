<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenantDocument->document_type_display }} - {{ $tenantDocument->document_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }

        .document-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .document-info h3 {
            margin: 0 0 10px 0;
            color: #007bff;
            font-size: 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
        }

        .info-value {
            color: #212529;
        }

        .tenant-section {
            margin-bottom: 30px;
        }

        .section-title {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .tenant-photo {
            text-align: center;
            margin-bottom: 20px;
        }

        .tenant-photo img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #007bff;
            object-fit: cover;
        }

        .tenant-photo .placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #007bff;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #6c757d;
            font-size: 14px;
        }

        .form-data {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
        }

        .data-section {
            margin-bottom: 25px;
        }

        .data-section:last-child {
            margin-bottom: 0;
        }

        .data-section h4 {
            color: #007bff;
            margin: 0 0 10px 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .data-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #dee2e6;
        }

        .data-item:last-child {
            border-bottom: none;
        }

        .data-label {
            font-weight: 500;
            color: #495057;
            font-size: 13px;
        }

        .data-value {
            color: #212529;
            font-size: 13px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            color: #6c757d;
            font-size: 12px;
        }

        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .signature-box {
            text-align: center;
            border: 1px solid #dee2e6;
            padding: 20px;
            min-height: 100px;
        }

        .signature-box h5 {
            margin: 0 0 40px 0;
            color: #495057;
            font-size: 14px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
        }

        .signature-label {
            font-size: 12px;
            color: #6c757d;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $tenantDocument->document_type_display }}</h1>
        <p>Document Number: {{ $tenantDocument->document_number }}</p>
        <p>Generated on: {{ now()->format('F d, Y \a\t H:i') }}</p>
    </div>

    <!-- Document Information -->
    <div class="document-info">
        <h3>Document Details</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Document Type:</span>
                <span class="info-value">{{ $tenantDocument->document_type_display }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Priority:</span>
                <span class="info-value">{{ $tenantDocument->priority_display }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ $tenantDocument->status_display }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Required:</span>
                <span class="info-value">{{ $tenantDocument->is_required ? 'Yes' : 'No' }}</span>
            </div>
            @if($tenantDocument->expiry_date)
            <div class="info-item">
                <span class="info-label">Expiry Date:</span>
                <span class="info-value">{{ $tenantDocument->expiry_date->format('M d, Y') }}</span>
            </div>
            @endif
            <div class="info-item">
                <span class="info-label">Created:</span>
                <span class="info-value">{{ $tenantDocument->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Tenant Information -->
    <div class="tenant-section">
        <div class="section-title">Tenant Information</div>

        <div class="tenant-photo">
            @if($tenantDocument->tenant_photo_url)
                <img src="{{ $tenantDocument->tenant_photo_url }}" alt="Tenant Photo">
            @else
                <div class="placeholder">
                    No Photo Available
                </div>
            @endif
        </div>

        <div class="form-data">
            @if($tenantDocument->document_data && is_array($tenantDocument->document_data))
                @php
                    $data = $tenantDocument->document_data;
                @endphp

                <!-- Personal Information -->
                @if(isset($data['tenant_info']))
                <div class="data-section">
                    <h4>Personal Information</h4>
                    <div class="data-grid">
                        <div class="data-item">
                            <span class="data-label">Full Name:</span>
                            <span class="data-value">{{ $data['tenant_info']['name'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Email:</span>
                            <span class="data-value">{{ $data['tenant_info']['email'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Phone:</span>
                            <span class="data-value">{{ $data['tenant_info']['phone'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Date of Birth:</span>
                            <span class="data-value">{{ $data['tenant_info']['date_of_birth'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Occupation:</span>
                            <span class="data-value">{{ $data['tenant_info']['occupation'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Company:</span>
                            <span class="data-value">{{ $data['tenant_info']['company'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Emergency Contact -->
                @if(isset($data['emergency_contact']))
                <div class="data-section">
                    <h4>Emergency Contact</h4>
                    <div class="data-grid">
                        <div class="data-item">
                            <span class="data-label">Contact Name:</span>
                            <span class="data-value">{{ $data['emergency_contact']['name'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Phone:</span>
                            <span class="data-value">{{ $data['emergency_contact']['phone'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Relation:</span>
                            <span class="data-value">{{ $data['emergency_contact']['relation'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Rental Information -->
                @if(isset($data['rental_info']))
                <div class="data-section">
                    <h4>Rental Information</h4>
                    <div class="data-grid">
                        <div class="data-item">
                            <span class="data-label">Move-in Date:</span>
                            <span class="data-value">{{ $data['rental_info']['move_in_date'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Security Deposit:</span>
                            <span class="data-value">₹{{ number_format($data['rental_info']['security_deposit'] ?? 0) }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Monthly Rent:</span>
                            <span class="data-value">₹{{ number_format($data['rental_info']['monthly_rent'] ?? 0) }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Lease Start:</span>
                            <span class="data-value">{{ $data['rental_info']['lease_start_date'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Lease End:</span>
                            <span class="data-value">{{ $data['rental_info']['lease_end_date'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Current Bed Assignment -->
                @if(isset($data['current_bed']) && $data['current_bed'])
                <div class="data-section">
                    <h4>Current Bed Assignment</h4>
                    <div class="data-grid">
                        <div class="data-item">
                            <span class="data-label">Hostel:</span>
                            <span class="data-value">{{ $data['current_bed']['hostel_name'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Room Number:</span>
                            <span class="data-value">{{ $data['current_bed']['room_number'] ?? 'N/A' }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Bed Number:</span>
                            <span class="data-value">{{ $data['current_bed']['bed_number'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="data-section">
                    <h4>Tenant Information</h4>
                    <div class="data-grid">
                        <div class="data-item">
                            <span class="data-label">Name:</span>
                            <span class="data-value">{{ $tenantDocument->tenantProfile->user->name }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Email:</span>
                            <span class="data-value">{{ $tenantDocument->tenantProfile->user->email }}</span>
                        </div>
                        <div class="data-item">
                            <span class="data-label">Phone:</span>
                            <span class="data-value">{{ $tenantDocument->tenantProfile->phone ?? 'N/A' }}</span>
                        </div>
                        @if($tenantDocument->tenantProfile->currentBed)
                        <div class="data-item">
                            <span class="data-label">Current Bed:</span>
                            <span class="data-value">
                                {{ $tenantDocument->tenantProfile->currentBed->room->hostel->name }} -
                                Room {{ $tenantDocument->tenantProfile->currentBed->room->room_number }},
                                Bed {{ $tenantDocument->tenantProfile->currentBed->bed_number }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <h5>Tenant Signature</h5>
            <div class="signature-line" style="height: 40px;"></div>
            <div class="signature-label">Date: _______________</div>
        </div>
        <div class="signature-box">
            <h5>Admin Signature</h5>
            <div class="signature-line" style="height: 40px;"></div>
            <div class="signature-label">Date: _______________</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This document was generated on {{ now()->format('F d, Y \a\t H:i') }} by {{ Auth::user()->name ?? 'System' }}</p>
        <p>Document Number: {{ $tenantDocument->document_number }}</p>
    </div>
</body>
</html>
