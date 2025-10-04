<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template['subject'] ?? 'Notification' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #2d3748;
        }
        .message {
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 25px;
            color: #4a5568;
        }
        .details {
            background-color: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .details h3 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 16px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        .detail-label {
            font-weight: 600;
            color: #4a5568;
        }
        .detail-value {
            color: #2d3748;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0;
            font-size: 14px;
            color: #718096;
        }
        .footer .company-info {
            margin-top: 15px;
            font-size: 12px;
            color: #a0aec0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 15px 0;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-success {
            background-color: #c6f6d5;
            color: #22543d;
        }
        .status-info {
            background-color: #bee3f8;
            color: #2a4365;
        }
        .status-warning {
            background-color: #faf089;
            color: #744210;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header {
                padding: 20px 15px;
            }
            .content {
                padding: 20px 15px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 2px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name', 'Hostel CRM') }}</h1>
            <p>Professional Hostel Management System</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                {{ $template['greeting'] ?? 'Hello,' }}
            </div>

            <!-- Main Message -->
            <div class="message">
                {{ $template['body'] ?? 'This is a notification from our system.' }}
            </div>

            <!-- Notification Details -->
            @if(isset($data) && count($data) > 0)
                <div class="details">
                    <h3>📋 Details</h3>
                    @foreach($data as $key => $value)
                        @if(!in_array($key, ['subject', 'greeting', 'body', 'footer']))
                            <div class="detail-row">
                                <span class="detail-label">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                <span class="detail-value">{{ $value }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            <!-- Notification Type Badge -->
            <div style="text-align: center; margin: 20px 0;">
                <span class="status-badge status-info">
                    {{ $notification->type_display }}
                </span>
            </div>

            <!-- Footer Message -->
            @if(isset($template['footer']))
                <div class="divider"></div>
                <div class="message">
                    {{ $template['footer'] }}
                </div>
            @endif

            <!-- Action Button (if applicable) -->
            @if($notification->type === 'invoice_created' && isset($data['invoice_id']))
                <div style="text-align: center; margin: 25px 0;">
                    <a href="{{ url('/invoices/' . $data['invoice_id']) }}" class="button">
                        View Invoice
                    </a>
                </div>
            @elseif($notification->type === 'payment_received' && isset($data['payment_id']))
                <div style="text-align: center; margin: 25px 0;">
                    <a href="{{ url('/payments/' . $data['payment_id']) }}" class="button">
                        View Payment
                    </a>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ $template['footer'] ?? 'Thank you for using our services.' }}</p>

            <div class="company-info">
                <p><strong>{{ config('app.name', 'Hostel CRM') }}</strong></p>
                <p>Professional Hostel Management System</p>
                <p>Generated on {{ now()->format('M j, Y \a\t g:i A') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
