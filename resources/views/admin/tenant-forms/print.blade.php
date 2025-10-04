<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenantForm->form_type_display }} - {{ $tenantForm->form_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .form-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .form-info h2 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 18px;
        }
        .form-info p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section h3 {
            background-color: #e9ecef;
            padding: 10px;
            margin: 0 0 15px 0;
            color: #2c3e50;
            border-left: 4px solid #007bff;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-item strong {
            color: #2c3e50;
            display: inline-block;
            width: 150px;
        }
        .photo-section {
            text-align: center;
            margin: 20px 0;
        }
        .photo-placeholder {
            width: 150px;
            height: 150px;
            border: 2px dashed #ccc;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 300px;
            margin: 20px 0 5px 0;
        }
        .signature-label {
            font-size: 12px;
            color: #666;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        @media print {
            body { margin: 0; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $tenantForm->form_type_display }}</h1>
        <p>Form Number: {{ $tenantForm->form_number }}</p>
        <p>Generated on: {{ now()->format('F d, Y \a\t H:i') }}</p>
    </div>

    <!-- Form Information -->
    <div class="form-info">
        <h2>Form Details</h2>
        <p><strong>Form Type:</strong> {{ $tenantForm->form_type_display }}</p>
        <p><strong>Form Number:</strong> {{ $tenantForm->form_number }}</p>
        <p><strong>Created:</strong> {{ $tenantForm->created_at->format('F d, Y \a\t H:i') }}</p>
        <p><strong>Created By:</strong> {{ $tenantForm->form_data['form_metadata']['created_by'] ?? 'System' }}</p>
    </div>

    <!-- Tenant Photo Section -->
    <div class="section">
        <h3>Tenant Photograph</h3>
        <div class="photo-section">
            @if($tenantForm->tenant_photo_url)
                <img src="{{ $tenantForm->tenant_photo_url }}" alt="Tenant Photo" style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #333;">
            @else
                <div class="photo-placeholder">
                    <span>Tenant Photo<br><small>(To be attached)</small></span>
                </div>
            @endif
        </div>
    </div>

    <!-- Personal Information -->
    <div class="section">
        <h3>Personal Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>Full Name:</strong> {{ $tenantForm->tenantProfile->user->name }}
            </div>
            <div class="info-item">
                <strong>Email:</strong> {{ $tenantForm->tenantProfile->user->email }}
            </div>
            <div class="info-item">
                <strong>Phone:</strong> {{ $tenantForm->tenantProfile->phone ?? 'Not provided' }}
            </div>
            <div class="info-item">
                <strong>Date of Birth:</strong> {{ $tenantForm->tenantProfile->date_of_birth?->format('F d, Y') ?? 'Not provided' }}
            </div>
            <div class="info-item">
                <strong>Occupation:</strong> {{ $tenantForm->tenantProfile->occupation ?? 'Not provided' }}
            </div>
            <div class="info-item">
                <strong>Company:</strong> {{ $tenantForm->tenantProfile->company ?? 'Not provided' }}
            </div>
            <div class="info-item">
                <strong>ID Proof Type:</strong> {{ ucfirst($tenantForm->tenantProfile->id_proof_type ?? 'Not provided') }}
            </div>
            <div class="info-item">
                <strong>ID Proof Number:</strong> {{ $tenantForm->tenantProfile->id_proof_number ?? 'Not provided' }}
            </div>
        </div>

        @if($tenantForm->tenantProfile->address)
        <div class="info-item" style="margin-top: 15px;">
            <strong>Address:</strong><br>
            {{ $tenantForm->tenantProfile->address }}
        </div>
        @endif
    </div>

    <!-- Emergency Contact -->
    @if($tenantForm->tenantProfile->emergency_contact_name)
    <div class="section">
        <h3>Emergency Contact Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>Contact Name:</strong> {{ $tenantForm->tenantProfile->emergency_contact_name }}
            </div>
            <div class="info-item">
                <strong>Phone:</strong> {{ $tenantForm->tenantProfile->emergency_contact_phone ?? 'Not provided' }}
            </div>
            <div class="info-item">
                <strong>Relation:</strong> {{ $tenantForm->tenantProfile->emergency_contact_relation ?? 'Not provided' }}
            </div>
        </div>
    </div>
    @endif

    <!-- Rental Information -->
    <div class="section">
        <h3>Rental Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>Move-in Date:</strong> {{ $tenantForm->tenantProfile->move_in_date?->format('F d, Y') ?? 'Not set' }}
            </div>
            <div class="info-item">
                <strong>Move-out Date:</strong> {{ $tenantForm->tenantProfile->move_out_date?->format('F d, Y') ?? 'Not set' }}
            </div>
            <div class="info-item">
                <strong>Monthly Rent:</strong> ₹{{ number_format($tenantForm->tenantProfile->monthly_rent ?? 0, 2) }}
            </div>
            <div class="info-item">
                <strong>Security Deposit:</strong> ₹{{ number_format($tenantForm->tenantProfile->security_deposit ?? 0, 2) }}
            </div>
            <div class="info-item">
                <strong>Lease Start:</strong> {{ $tenantForm->tenantProfile->lease_start_date?->format('F d, Y') ?? 'Not set' }}
            </div>
            <div class="info-item">
                <strong>Lease End:</strong> {{ $tenantForm->tenantProfile->lease_end_date?->format('F d, Y') ?? 'Not set' }}
            </div>
        </div>

        @if($tenantForm->tenantProfile->currentBed)
        <div class="info-item" style="margin-top: 15px;">
            <strong>Current Accommodation:</strong><br>
            <strong>Hostel:</strong> {{ $tenantForm->tenantProfile->currentBed->room->hostel->name }}<br>
            <strong>Room:</strong> {{ $tenantForm->tenantProfile->currentBed->room->room_number }}<br>
            <strong>Bed:</strong> {{ $tenantForm->tenantProfile->currentBed->bed_number }}
        </div>
        @endif
    </div>

    <!-- Billing Information -->
    <div class="section">
        <h3>Billing Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>Billing Cycle:</strong> {{ ucfirst($tenantForm->tenantProfile->billing_cycle ?? 'Monthly') }}
            </div>
            <div class="info-item">
                <strong>Billing Day:</strong> {{ $tenantForm->tenantProfile->billing_day ?? 1 }} of each month
            </div>
            <div class="info-item">
                <strong>Next Billing:</strong> {{ $tenantForm->tenantProfile->next_billing_date?->format('F d, Y') ?? 'Not set' }}
            </div>
            <div class="info-item">
                <strong>Payment Status:</strong> {{ ucfirst($tenantForm->tenantProfile->payment_status ?? 'Pending') }}
            </div>
        </div>
    </div>

    <!-- Terms and Conditions -->
    <div class="section">
        <h3>Terms and Conditions</h3>
        <p>By signing this form, the tenant agrees to:</p>
        <ul>
            <li>Pay rent on time as per the billing cycle</li>
            <li>Maintain the property in good condition</li>
            <li>Follow all hostel rules and regulations</li>
            <li>Provide accurate information as stated above</li>
            <li>Notify management of any changes in personal information</li>
        </ul>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <h3>Signatures</h3>

        <div style="margin-bottom: 40px;">
            <p><strong>Tenant Signature:</strong></p>
            <div class="signature-line"></div>
            <div class="signature-label">Date: _______________</div>
        </div>

        <div style="margin-bottom: 40px;">
            <p><strong>Witness Signature:</strong></p>
            <div class="signature-line"></div>
            <div class="signature-label">Date: _______________</div>
        </div>

        <div>
            <p><strong>Management Signature:</strong></p>
            <div class="signature-line"></div>
            <div class="signature-label">Date: _______________</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This form was generated on {{ now()->format('F d, Y \a\t H:i') }} by {{ $tenantForm->form_data['form_metadata']['created_by'] ?? 'System' }}</p>
        <p>Form Number: {{ $tenantForm->form_number }} | Page 1 of 1</p>
    </div>
</body>
</html>
