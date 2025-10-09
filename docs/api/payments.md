# Payments API Module

## Overview
The Payments API provides comprehensive endpoints for managing payment processing and tracking within the Hostel CRM system. This module handles payment creation, verification, cancellation, and provides detailed analytics for payment tracking.

## Base Endpoints
All payment endpoints are prefixed with `/api/v1/payments/`

## Endpoints

### 1. List Payments
Retrieve a paginated list of all payments with optional filtering and sorting.

**GET Version (Testing):**
```
GET /api/v1/payments?page=1&per_page=15&invoice_id=1&status=completed&payment_method=bank_transfer&is_verified=true
```

**POST Version (Integration):**
```
POST /api/v1/payments
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "invoice_id": 1,
    "status": "completed",
    "payment_method": "bank_transfer",
    "is_verified": true
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `invoice_id` (optional): Filter by invoice ID
- `tenant_profile_id` (optional): Filter by tenant profile ID
- `status` (optional): Filter by status (`pending`, `completed`, `failed`, `cancelled`)
- `payment_method` (optional): Filter by payment method (`cash`, `bank_transfer`, `upi`, `card`, `cheque`, `other`)
- `payment_date_from` (optional): Filter payments from this date
- `payment_date_to` (optional): Filter payments to this date
- `is_verified` (optional): Filter by verification status (boolean)
- `amount_min` (optional): Filter by minimum amount
- `amount_max` (optional): Filter by maximum amount
- `search` (optional): Search in payment number, reference number, bank name, notes, tenant name/email, or invoice number
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
```json
{
    "success": true,
    "message": "Payments retrieved successfully",
    "data": [
        {
            "id": 1,
            "payment_number": "PAY-202401-0001",
            "invoice_id": 1,
            "tenant_profile_id": 1,
            "amount": 500.00,
            "formatted_amount": "₹500.00",
            "payment_date": "2024-01-15",
            "payment_method": "bank_transfer",
            "method_badge": {
                "class": "bg-blue-100 text-blue-800",
                "text": "Bank Transfer"
            },
            "status": "completed",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "text": "Completed"
            },
            "reference_number": "TXN123456789",
            "bank_name": "State Bank",
            "account_number": null,
            "notes": "Partial payment received",
            "metadata": null,
            "is_verified": true,
            "verified_at": "2024-01-15T16:30:00.000000Z",
            "created_at": "2024-01-15T16:00:00.000000Z",
            "updated_at": "2024-01-15T16:30:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42,
        "from": 1,
        "to": 15
    }
}
```

### 2. Create Payment
Create a new payment for an invoice.

**GET Version (Testing):**
```
GET /api/v1/payments/create
```

**POST Version (Integration):**
```
POST /api/v1/payments
Content-Type: application/json

{
    "invoice_id": 1,
    "tenant_profile_id": 1,
    "amount": 500.00,
    "payment_date": "2024-01-15",
    "payment_method": "bank_transfer",
    "status": "completed",
    "reference_number": "TXN123456789",
    "bank_name": "State Bank",
    "notes": "Partial payment received"
}
```

**Required Fields:**
- `invoice_id`: Invoice ID (must exist)
- `tenant_profile_id`: Tenant Profile ID (must exist)
- `amount`: Payment amount (must be positive)
- `payment_date`: Payment date
- `payment_method`: Payment method (`cash`, `bank_transfer`, `upi`, `card`, `cheque`, `other`)
- `status`: Payment status (`pending`, `completed`, `failed`, `cancelled`)

**Optional Fields:**
- `reference_number`: Transaction reference number
- `bank_name`: Bank name (for bank transfers)
- `account_number`: Account number (for bank transfers)
- `notes`: Payment notes
- `metadata`: Additional payment metadata

**Response (201):**
```json
{
    "success": true,
    "message": "Payment created successfully",
    "data": {
        "id": 2,
        "payment_number": "PAY-202401-0002",
        "invoice_id": 1,
        "tenant_profile_id": 1,
        "amount": 500.00,
        "formatted_amount": "₹500.00",
        "payment_date": "2024-01-15",
        "payment_method": "bank_transfer",
        "method_badge": {
            "class": "bg-blue-100 text-blue-800",
            "text": "Bank Transfer"
        },
        "status": "completed",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Completed"
        },
        "reference_number": "TXN123456789",
        "bank_name": "State Bank",
        "account_number": null,
        "notes": "Partial payment received",
        "metadata": null,
        "is_verified": false,
        "verified_at": null,
        "created_at": "2024-01-15T17:00:00.000000Z",
        "updated_at": "2024-01-15T17:00:00.000000Z"
    }
}
```

### 3. Get Payment Details
Retrieve detailed information about a specific payment.

**GET Version (Testing):**
```
GET /api/v1/payments/1
```

**Response (200):**
```json
{
    "success": true,
    "message": "Payment retrieved successfully",
    "data": {
        "id": 1,
        "payment_number": "PAY-202401-0001",
        "invoice_id": 1,
        "tenant_profile_id": 1,
        "amount": 500.00,
        "formatted_amount": "₹500.00",
        "payment_date": "2024-01-15",
        "payment_method": "bank_transfer",
        "method_badge": {
            "class": "bg-blue-100 text-blue-800",
            "text": "Bank Transfer"
        },
        "status": "completed",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Completed"
        },
        "reference_number": "TXN123456789",
        "bank_name": "State Bank",
        "account_number": null,
        "notes": "Partial payment received",
        "metadata": null,
        "is_verified": true,
        "verified_at": "2024-01-15T16:30:00.000000Z",
        "created_at": "2024-01-15T16:00:00.000000Z",
        "updated_at": "2024-01-15T16:30:00.000000Z",
        "invoice": {
            "id": 1,
            "invoice_number": "INV-202401-0001",
            "type": "rent",
            "type_badge": {
                "class": "bg-blue-100 text-blue-800",
                "text": "Rent"
            },
            "status": "sent",
            "status_badge": {
                "class": "bg-blue-100 text-blue-800",
                "text": "Sent"
            },
            "total_amount": 1000.00,
            "formatted_total_amount": "₹1,000.00",
            "balance_amount": 500.00,
            "formatted_balance_amount": "₹500.00"
        },
        "tenant": {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123"
        },
        "recorded_by": {
            "id": 1,
            "name": "Admin User"
        },
        "verified_by": {
            "id": 1,
            "name": "Admin User"
        }
    }
}
```

### 4. Update Payment
Update an existing payment's information.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/payments/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "amount": 600.00,
    "status": "completed",
    "notes": "Updated payment amount"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Payment updated successfully",
    "data": {
        "id": 1,
        "amount": 600.00,
        "formatted_amount": "₹600.00",
        "status": "completed",
        "notes": "Updated payment amount",
        "updated_at": "2024-01-15T18:00:00.000000Z"
    }
}
```

### 5. Delete Payment
Remove a payment from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/payments/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Payment deleted successfully"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot delete verified payment. Please cancel the payment instead."
}
```

### 6. Get Payment Statistics
Retrieve statistical information about a payment.

**GET Version (Testing):**
```
GET /api/v1/payments/1/stats
```

**Response (200):**
```json
{
    "success": true,
    "message": "Payment statistics retrieved successfully",
    "data": {
        "basic_info": {
            "payment_number": "PAY-202401-0001",
            "amount": 500.00,
            "formatted_amount": "₹500.00",
            "payment_date": "2024-01-15",
            "payment_method": "bank_transfer",
            "method_badge": {
                "class": "bg-blue-100 text-blue-800",
                "text": "Bank Transfer"
            },
            "status": "completed",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "text": "Completed"
            },
            "is_verified": true
        },
        "invoice_info": {
            "invoice_id": 1,
            "invoice_number": "INV-202401-0001",
            "invoice_type": "rent",
            "invoice_status": "sent",
            "invoice_total": 1000.00,
            "invoice_balance": 500.00
        },
        "tenant_info": {
            "tenant_profile_id": 1,
            "tenant_name": "John Doe",
            "tenant_email": "john.doe@example.com",
            "tenant_phone": "+1-555-0123"
        },
        "payment_details": {
            "reference_number": "TXN123456789",
            "bank_name": "State Bank",
            "account_number": null,
            "notes": "Partial payment received",
            "metadata": null
        },
        "verification_info": {
            "verified_at": "2024-01-15T16:30:00.000000Z",
            "verified_by": {
                "id": 1,
                "name": "Admin User"
            }
        },
        "recorded_info": {
            "recorded_by": {
                "id": 1,
                "name": "Admin User"
            },
            "created_at": "2024-01-15T16:00:00.000000Z",
            "updated_at": "2024-01-15T16:30:00.000000Z"
        }
    }
}
```

### 7. Verify Payment
Mark a payment as verified.

**POST Version (Authenticated):**
```
POST /api/v1/payments/1/verify
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Payment verified successfully",
    "data": {
        "id": 1,
        "status": "completed",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Completed"
        },
        "is_verified": true,
        "verified_at": "2024-01-15T18:30:00.000000Z",
        "verified_by": {
            "id": 1,
            "name": "Admin User"
        }
    }
}
```

### 8. Cancel Payment
Cancel a payment with optional reason.

**POST Version (Authenticated):**
```
POST /api/v1/payments/1/cancel
Authorization: Bearer {token}
Content-Type: application/json

{
    "reason": "Payment was made in error"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Payment cancelled successfully",
    "data": {
        "id": 1,
        "status": "cancelled",
        "status_badge": {
            "class": "bg-gray-100 text-gray-800",
            "text": "Cancelled"
        },
        "notes": "Partial payment received\nCancelled: Payment was made in error"
    }
}
```

### 9. Get Tenant Payment Summary
Get payment summary for a specific tenant.

**GET Version (Testing):**
```
GET /api/v1/payments/tenant/1/summary
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant payment summary retrieved successfully",
    "data": {
        "tenant_info": {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com"
        },
        "payment_summary": {
            "total_payments": 5,
            "total_amount": 2500.00,
            "formatted_total_amount": "₹2,500.00",
            "completed_payments": 4,
            "pending_payments": 1,
            "failed_payments": 0,
            "cancelled_payments": 0
        },
        "payment_methods": {
            "bank_transfer": {
                "count": 3,
                "total_amount": 1500.00,
                "formatted_total_amount": "₹1,500.00"
            },
            "cash": {
                "count": 2,
                "total_amount": 1000.00,
                "formatted_total_amount": "₹1,000.00"
            }
        },
        "recent_payments": [
            {
                "id": 5,
                "payment_number": "PAY-202401-0005",
                "amount": 500.00,
                "formatted_amount": "₹500.00",
                "payment_date": "2024-01-20",
                "payment_method": "bank_transfer",
                "status": "completed"
            }
        ],
        "monthly_summary": [
            {
                "month": "Jan 2024",
                "count": 5,
                "total_amount": 2500.00,
                "formatted_total_amount": "₹2,500.00",
                "completed_count": 4,
                "completed_amount": 2000.00
            }
        ]
    }
}
```

### 10. Get Invoice Payment Summary
Get payment summary for a specific invoice.

**GET Version (Testing):**
```
GET /api/v1/payments/invoice/1/summary
```

**Response (200):**
```json
{
    "success": true,
    "message": "Invoice payment summary retrieved successfully",
    "data": {
        "invoice_info": {
            "id": 1,
            "invoice_number": "INV-202401-0001",
            "total_amount": 1000.00,
            "formatted_total_amount": "₹1,000.00",
            "balance_amount": 500.00,
            "formatted_balance_amount": "₹500.00"
        },
        "payment_summary": {
            "total_payments": 2,
            "total_paid": 500.00,
            "formatted_total_paid": "₹500.00",
            "completed_payments": 1,
            "pending_payments": 1,
            "failed_payments": 0,
            "cancelled_payments": 0
        },
        "payments": [
            {
                "id": 1,
                "payment_number": "PAY-202401-0001",
                "amount": 500.00,
                "formatted_amount": "₹500.00",
                "payment_date": "2024-01-15",
                "payment_method": "bank_transfer",
                "status": "completed",
                "is_verified": true
            },
            {
                "id": 2,
                "payment_number": "PAY-202401-0002",
                "amount": 300.00,
                "formatted_amount": "₹300.00",
                "payment_date": "2024-01-20",
                "payment_method": "cash",
                "status": "pending",
                "is_verified": false
            }
        ]
    }
}
```

### 11. Search Payments
Search for payments by various criteria.

**GET Version (Testing):**
```
GET /api/v1/payments/search?query=PAY-202401&status=completed&payment_method=bank_transfer&limit=10
```

**POST Version (Integration):**
```
POST /api/v1/payments/search
Content-Type: application/json

{
    "query": "PAY-202401",
    "status": "completed",
    "payment_method": "bank_transfer",
    "limit": 10
}
```

**Parameters:**
- `query` (required): Search term (minimum 2 characters)
- `status` (optional): Filter by payment status
- `payment_method` (optional): Filter by payment method
- `limit` (optional): Maximum number of results (default: 10, max: 50)

**Response (200):**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
            "id": 1,
            "payment_number": "PAY-202401-0001",
            "amount": 500.00,
            "formatted_amount": "₹500.00",
            "payment_date": "2024-01-15",
            "payment_method": "bank_transfer",
            "method_badge": {
                "class": "bg-blue-100 text-blue-800",
                "text": "Bank Transfer"
            },
            "status": "completed",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "text": "Completed"
            },
            "reference_number": "TXN123456789",
            "is_verified": true,
            "invoice": {
                "id": 1,
                "invoice_number": "INV-202401-0001",
                "type": "rent"
            },
            "tenant": {
                "id": 1,
                "name": "John Doe",
                "email": "john.doe@example.com"
            }
        }
    ],
    "query": "PAY-202401",
    "count": 1
}
```

## Data Models

### Payment Object
```json
{
    "id": 1,
    "payment_number": "PAY-202401-0001",
    "invoice_id": 1,
    "tenant_profile_id": 1,
    "amount": 500.00,
    "formatted_amount": "₹500.00",
    "payment_date": "2024-01-15",
    "payment_method": "bank_transfer",
    "method_badge": {
        "class": "bg-blue-100 text-blue-800",
        "text": "Bank Transfer"
    },
    "status": "completed",
    "status_badge": {
        "class": "bg-green-100 text-green-800",
        "text": "Completed"
    },
    "reference_number": "TXN123456789",
    "bank_name": "State Bank",
    "account_number": null,
    "notes": "Partial payment received",
    "metadata": null,
    "is_verified": true,
    "verified_at": "2024-01-15T16:30:00.000000Z",
    "created_at": "2024-01-15T16:00:00.000000Z",
    "updated_at": "2024-01-15T16:30:00.000000Z"
}
```

## Error Handling

### Validation Errors (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "invoice_id": ["The invoice id field is required."],
        "amount": ["The amount must be at least 0.01."],
        "payment_method": ["The selected payment method is invalid."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Payment not found"
}
```

### Conflict Errors (422)
```json
{
    "success": false,
    "message": "Payment amount cannot exceed invoice balance"
}
```

### Server Errors (500)
```json
{
    "success": false,
    "message": "Failed to retrieve payments",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: List, Show, Stats, Search, Tenant Summary, Invoice Summary (read-only operations)
- **Authenticated Endpoints**: Create, Update, Delete, Verify, Cancel (write operations)
- **Authentication Method**: Bearer token via Laravel Sanctum

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/payments
http://localhost/api/v1/payments/1
http://localhost/api/v1/payments/1/stats
http://localhost/api/v1/payments/tenant/1/summary
http://localhost/api/v1/payments/invoice/1/summary
http://localhost/api/v1/payments/search?query=PAY-202401
```

### cURL Examples
```bash
# List payments
curl -X GET http://localhost/api/v1/payments

# Get specific payment
curl -X GET http://localhost/api/v1/payments/1

# Create payment
curl -X POST http://localhost/api/v1/payments \
  -H "Content-Type: application/json" \
  -d '{"invoice_id":1,"tenant_profile_id":1,"amount":500.00,"payment_date":"2024-01-15","payment_method":"bank_transfer","status":"completed","reference_number":"TXN123456789"}'

# Update payment (authenticated)
curl -X PUT http://localhost/api/v1/payments/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"amount":600.00,"status":"completed"}'

# Delete payment (authenticated)
curl -X DELETE http://localhost/api/v1/payments/1 \
  -H "Authorization: Bearer {token}"

# Verify payment (authenticated)
curl -X POST http://localhost/api/v1/payments/1/verify \
  -H "Authorization: Bearer {token}"

# Cancel payment (authenticated)
curl -X POST http://localhost/api/v1/payments/1/cancel \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"reason":"Payment was made in error"}'

# Get tenant payment summary
curl -X GET http://localhost/api/v1/payments/tenant/1/summary

# Get invoice payment summary
curl -X GET http://localhost/api/v1/payments/invoice/1/summary

# Search payments
curl -X POST http://localhost/api/v1/payments/search \
  -H "Content-Type: application/json" \
  -d '{"query":"PAY-202401","status":"completed","limit":10}'
```

## Business Rules

1. **Payment Deletion**: Cannot delete verified payments
2. **Payment Amount**: Payment amount cannot exceed invoice balance
3. **Payment Number**: Auto-generated unique payment numbers
4. **Status Values**: Only specified status values are allowed
5. **Payment Methods**: Only specified payment methods are allowed
6. **Verification**: Only unverified payments can be verified
7. **Cancellation**: Only non-cancelled payments can be cancelled
8. **Search Minimum**: Search queries must be at least 2 characters
9. **Pagination**: Maximum 100 items per page for performance
10. **Invoice Integration**: Payment status changes automatically update invoice payment status
11. **Amount Validation**: Payment amounts must be positive
12. **Date Validation**: Payment date must be valid
13. **Reference Numbers**: Reference numbers are optional but should be unique when provided

## Related Modules

- **Invoices API**: Manage invoices for payment processing
- **Tenants API**: Manage tenant profiles for payment tracking
- **Authentication API**: User authentication for payment operations
- **Notifications API**: Send payment notifications to tenants

---

*Module: Payments API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
