# Invoices API Module

## Overview
The Invoices API provides comprehensive endpoints for managing billing and invoicing within the Hostel CRM system. This module handles invoice creation, payment processing, invoice items management, and automated billing for amenities usage.

## Base Endpoints
All invoice endpoints are prefixed with `/api/v1/invoices/`

## Endpoints

### 1. List Invoices
Retrieve a paginated list of all invoices with optional filtering and sorting.

**GET Version (Testing):**
```
GET /api/v1/invoices?page=1&per_page=15&tenant_profile_id=1&status=sent&type=rent&is_overdue=false
```

**POST Version (Integration):**
```
POST /api/v1/invoices
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "tenant_profile_id": 1,
    "status": "sent",
    "type": "rent",
    "is_overdue": false
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `tenant_profile_id` (optional): Filter by tenant profile ID
- `status` (optional): Filter by status (`draft`, `sent`, `paid`, `overdue`, `cancelled`)
- `type` (optional): Filter by type (`rent`, `amenities`, `damage`, `other`)
- `invoice_date_from` (optional): Filter invoices from this date
- `invoice_date_to` (optional): Filter invoices to this date
- `due_date_from` (optional): Filter due dates from this date
- `due_date_to` (optional): Filter due dates to this date
- `is_overdue` (optional): Filter overdue invoices (boolean)
- `search` (optional): Search in invoice number, notes, or tenant name/email
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
```json
{
    "success": true,
    "message": "Invoices retrieved successfully",
    "data": [
        {
            "id": 1,
            "invoice_number": "INV-202401-0001",
            "tenant_profile_id": 1,
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
            "invoice_date": "2024-01-01",
            "due_date": "2024-01-31",
            "period_start": "2024-01-01",
            "period_end": "2024-01-31",
            "subtotal": 1000.00,
            "tax_amount": 0.00,
            "discount_amount": 0.00,
            "total_amount": 1000.00,
            "formatted_total_amount": "₹1,000.00",
            "paid_amount": 0.00,
            "formatted_paid_amount": "₹0.00",
            "balance_amount": 1000.00,
            "formatted_balance_amount": "₹1,000.00",
            "payment_status": "Unpaid",
            "is_overdue": false,
            "days_overdue": 0,
            "notes": "Monthly rent for January 2024",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
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

### 2. Create Invoice
Create a new invoice for a tenant.

**GET Version (Testing):**
```
GET /api/v1/invoices/create
```

**POST Version (Integration):**
```
POST /api/v1/invoices
Content-Type: application/json

{
    "tenant_profile_id": 1,
    "type": "rent",
    "status": "draft",
    "invoice_date": "2024-01-01",
    "due_date": "2024-01-31",
    "period_start": "2024-01-01",
    "period_end": "2024-01-31",
    "subtotal": 1000.00,
    "tax_amount": 0.00,
    "discount_amount": 0.00,
    "total_amount": 1000.00,
    "paid_amount": 0.00,
    "balance_amount": 1000.00,
    "notes": "Monthly rent for January 2024",
    "terms_conditions": "Payment due within 30 days"
}
```

**Required Fields:**
- `tenant_profile_id`: Tenant Profile ID (must exist)
- `type`: Invoice type (`rent`, `amenities`, `damage`, `other`)
- `status`: Invoice status (`draft`, `sent`, `paid`, `overdue`, `cancelled`)
- `invoice_date`: Invoice date
- `due_date`: Due date (must be after or equal to invoice date)
- `total_amount`: Total amount

**Optional Fields:**
- `period_start`: Period start date (for recurring charges)
- `period_end`: Period end date (for recurring charges)
- `subtotal`: Subtotal amount
- `tax_amount`: Tax amount
- `discount_amount`: Discount amount
- `paid_amount`: Paid amount
- `balance_amount`: Balance amount
- `notes`: Invoice notes
- `terms_conditions`: Terms and conditions
- `metadata`: Additional metadata

**Response (201):**
```json
{
    "success": true,
    "message": "Invoice created successfully",
    "data": {
        "id": 2,
        "invoice_number": "INV-202401-0002",
        "tenant_profile_id": 1,
        "type": "rent",
        "type_badge": {
            "class": "bg-blue-100 text-blue-800",
            "text": "Rent"
        },
        "status": "draft",
        "status_badge": {
            "class": "bg-gray-100 text-gray-800",
            "text": "Draft"
        },
        "invoice_date": "2024-01-01",
        "due_date": "2024-01-31",
        "period_start": "2024-01-01",
        "period_end": "2024-01-31",
        "subtotal": 1000.00,
        "tax_amount": 0.00,
        "discount_amount": 0.00,
        "total_amount": 1000.00,
        "formatted_total_amount": "₹1,000.00",
        "paid_amount": 0.00,
        "formatted_paid_amount": "₹0.00",
        "balance_amount": 1000.00,
        "formatted_balance_amount": "₹1,000.00",
        "payment_status": "Unpaid",
        "is_overdue": false,
        "days_overdue": 0,
        "notes": "Monthly rent for January 2024",
        "terms_conditions": "Payment due within 30 days",
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### 3. Get Invoice Details
Retrieve detailed information about a specific invoice.

**GET Version (Testing):**
```
GET /api/v1/invoices/1
```

**Response (200):**
```json
{
    "success": true,
    "message": "Invoice retrieved successfully",
    "data": {
        "id": 1,
        "invoice_number": "INV-202401-0001",
        "tenant_profile_id": 1,
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
        "invoice_date": "2024-01-01",
        "due_date": "2024-01-31",
        "period_start": "2024-01-01",
        "period_end": "2024-01-31",
        "subtotal": 1000.00,
        "tax_amount": 0.00,
        "discount_amount": 0.00,
        "total_amount": 1000.00,
        "formatted_total_amount": "₹1,000.00",
        "paid_amount": 0.00,
        "formatted_paid_amount": "₹0.00",
        "balance_amount": 1000.00,
        "formatted_balance_amount": "₹1,000.00",
        "payment_status": "Unpaid",
        "is_overdue": false,
        "days_overdue": 0,
        "notes": "Monthly rent for January 2024",
        "terms_conditions": "Payment due within 30 days",
        "metadata": null,
        "paid_at": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "tenant": {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123"
        },
        "items": [
            {
                "id": 1,
                "invoice_id": 1,
                "item_type": "rent",
                "description": "Monthly rent for January 2024",
                "quantity": 1,
                "unit_price": 1000.00,
                "formatted_unit_price": "₹1,000.00",
                "total_price": 1000.00,
                "formatted_total_price": "₹1,000.00",
                "related_id": null,
                "related_type": null,
                "period_start": "2024-01-01",
                "period_end": "2024-01-31",
                "period_text": "Jan 1 - Jan 31, 2024",
                "metadata": null,
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "payments": [],
        "created_by": {
            "id": 1,
            "name": "Admin User"
        }
    }
}
```

### 4. Update Invoice
Update an existing invoice's information.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/invoices/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "sent",
    "notes": "Updated invoice notes",
    "due_date": "2024-02-15"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Invoice updated successfully",
    "data": {
        "id": 1,
        "status": "sent",
        "notes": "Updated invoice notes",
        "due_date": "2024-02-15",
        "updated_at": "2024-01-15T15:30:00.000000Z"
    }
}
```

### 5. Delete Invoice
Remove an invoice from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/invoices/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Invoice deleted successfully"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot delete invoice with payments. Please delete payments first or cancel the invoice."
}
```

### 6. Get Invoice Statistics
Retrieve statistical information about an invoice.

**GET Version (Testing):**
```
GET /api/v1/invoices/1/stats
```

**Response (200):**
```json
{
    "success": true,
    "message": "Invoice statistics retrieved successfully",
    "data": {
        "basic_info": {
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
            "invoice_date": "2024-01-01",
            "due_date": "2024-01-31",
            "period_start": "2024-01-01",
            "period_end": "2024-01-31"
        },
        "financial_info": {
            "subtotal": 1000.00,
            "tax_amount": 0.00,
            "discount_amount": 0.00,
            "total_amount": 1000.00,
            "formatted_total_amount": "₹1,000.00",
            "paid_amount": 0.00,
            "formatted_paid_amount": "₹0.00",
            "balance_amount": 1000.00,
            "formatted_balance_amount": "₹1,000.00",
            "payment_status": "Unpaid"
        },
        "overdue_info": {
            "is_overdue": false,
            "days_overdue": 0
        },
        "tenant_info": {
            "tenant_profile_id": 1,
            "tenant_name": "John Doe",
            "tenant_email": "john.doe@example.com"
        },
        "items_summary": {
            "total_items": 1,
            "items_by_type": {
                "rent": {
                    "count": 1,
                    "total_amount": 1000.00
                }
            }
        },
        "payments_summary": {
            "total_payments": 0,
            "completed_payments": 0,
            "pending_payments": 0,
            "total_paid_amount": 0.00
        },
        "created_info": {
            "created_by": {
                "id": 1,
                "name": "Admin User"
            },
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

### 7. Add Payment to Invoice
Add a payment to an existing invoice.

**POST Version (Authenticated):**
```
POST /api/v1/invoices/1/add-payment
Authorization: Bearer {token}
Content-Type: application/json

{
    "amount": 500.00,
    "payment_date": "2024-01-15",
    "payment_method": "bank_transfer",
    "status": "completed",
    "reference_number": "TXN123456789",
    "bank_name": "State Bank",
    "notes": "Partial payment received"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Payment added successfully",
    "data": {
        "payment": {
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
            "is_verified": false,
            "verified_at": null,
            "recorded_by": {
                "id": 1,
                "name": "Admin User"
            },
            "verified_by": null,
            "created_at": "2024-01-15T16:00:00.000000Z",
            "updated_at": "2024-01-15T16:00:00.000000Z"
        },
        "invoice": {
            "id": 1,
            "paid_amount": 500.00,
            "balance_amount": 500.00,
            "payment_status": "Partially Paid",
            "status": "sent"
        }
    }
}
```

### 8. Mark Invoice as Overdue
Mark an invoice as overdue if it's past due date.

**POST Version (Authenticated):**
```
POST /api/v1/invoices/1/mark-overdue
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Invoice marked as overdue successfully",
    "data": {
        "id": 1,
        "status": "overdue",
        "status_badge": {
            "class": "bg-red-100 text-red-800",
            "text": "Overdue"
        },
        "is_overdue": true,
        "days_overdue": 15
    }
}
```

### 9. Generate Amenity Usage Invoice
Automatically generate an invoice for amenity usage charges.

**POST Version (Authenticated):**
```
POST /api/v1/invoices/generate-amenity
Authorization: Bearer {token}
Content-Type: application/json

{
    "tenant_profile_id": 1,
    "period_start": "2024-01-01",
    "period_end": "2024-01-31",
    "status": "sent",
    "invoice_date": "2024-02-01",
    "due_date": "2024-02-15",
    "notes": "Amenity usage charges for January 2024"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Amenity usage invoice generated successfully",
    "data": {
        "id": 3,
        "invoice_number": "INV-202402-0001",
        "tenant_profile_id": 1,
        "type": "amenities",
        "type_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Amenities"
        },
        "status": "sent",
        "status_badge": {
            "class": "bg-blue-100 text-blue-800",
            "text": "Sent"
        },
        "invoice_date": "2024-02-01",
        "due_date": "2024-02-15",
        "period_start": "2024-01-01",
        "period_end": "2024-01-31",
        "subtotal": 150.00,
        "tax_amount": 0.00,
        "discount_amount": 0.00,
        "total_amount": 150.00,
        "formatted_total_amount": "₹150.00",
        "paid_amount": 0.00,
        "formatted_paid_amount": "₹0.00",
        "balance_amount": 150.00,
        "formatted_balance_amount": "₹150.00",
        "payment_status": "Unpaid",
        "is_overdue": false,
        "days_overdue": 0,
        "notes": "Amenity usage charges for January 2024",
        "items": [
            {
                "id": 2,
                "item_type": "amenity",
                "description": "Laundry Service (Usage: 5 days)",
                "quantity": 5,
                "unit_price": 30.00,
                "total_price": 150.00
            }
        ],
        "created_at": "2024-02-01T12:00:00.000000Z"
    }
}
```

### 10. Search Invoices
Search for invoices by various criteria.

**GET Version (Testing):**
```
GET /api/v1/invoices/search?query=INV-202401&type=rent&status=sent&limit=10
```

**POST Version (Integration):**
```
POST /api/v1/invoices/search
Content-Type: application/json

{
    "query": "INV-202401",
    "type": "rent",
    "status": "sent",
    "limit": 10
}
```

**Parameters:**
- `query` (required): Search term (minimum 2 characters)
- `type` (optional): Filter by invoice type
- `status` (optional): Filter by invoice status
- `limit` (optional): Maximum number of results (default: 10, max: 50)

**Response (200):**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
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
            "balance_amount": 1000.00,
            "invoice_date": "2024-01-01",
            "due_date": "2024-01-31",
            "tenant": {
                "id": 1,
                "name": "John Doe",
                "email": "john.doe@example.com"
            }
        }
    ],
    "query": "INV-202401",
    "count": 1
}
```

### 11. Add Item to Invoice
Add a line item to an existing invoice.

**POST Version (Authenticated):**
```
POST /api/v1/invoices/1/add-item
Authorization: Bearer {token}
Content-Type: application/json

{
    "item_type": "damage",
    "description": "Room damage repair",
    "quantity": 1,
    "unit_price": 200.00,
    "related_id": 1,
    "related_type": "room",
    "notes": "Damage to wall paint"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Item added successfully",
    "data": {
        "item": {
            "id": 2,
            "invoice_id": 1,
            "item_type": "damage",
            "description": "Room damage repair",
            "quantity": 1,
            "unit_price": 200.00,
            "formatted_unit_price": "₹200.00",
            "total_price": 200.00,
            "formatted_total_price": "₹200.00",
            "related_id": 1,
            "related_type": "room",
            "period_start": null,
            "period_end": null,
            "period_text": "",
            "metadata": null,
            "created_at": "2024-01-15T17:00:00.000000Z",
            "updated_at": "2024-01-15T17:00:00.000000Z"
        },
        "invoice": {
            "id": 1,
            "subtotal": 1200.00,
            "total_amount": 1200.00,
            "balance_amount": 1200.00
        }
    }
}
```

### 12. Update Invoice Item
Update an existing invoice item.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/invoices/1/items/2
Authorization: Bearer {token}
Content-Type: application/json

{
    "description": "Updated room damage repair",
    "unit_price": 250.00
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Item updated successfully",
    "data": {
        "item": {
            "id": 2,
            "description": "Updated room damage repair",
            "unit_price": 250.00,
            "total_price": 250.00,
            "updated_at": "2024-01-15T17:30:00.000000Z"
        },
        "invoice": {
            "id": 1,
            "subtotal": 1250.00,
            "total_amount": 1250.00,
            "balance_amount": 1250.00
        }
    }
}
```

### 13. Remove Invoice Item
Remove an item from an invoice.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/invoices/1/items/2
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Item removed successfully",
    "data": {
        "id": 1,
        "subtotal": 1000.00,
        "total_amount": 1000.00,
        "balance_amount": 1000.00
    }
}
```

## Data Models

### Invoice Object
```json
{
    "id": 1,
    "invoice_number": "INV-202401-0001",
    "tenant_profile_id": 1,
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
    "invoice_date": "2024-01-01",
    "due_date": "2024-01-31",
    "period_start": "2024-01-01",
    "period_end": "2024-01-31",
    "subtotal": 1000.00,
    "tax_amount": 0.00,
    "discount_amount": 0.00,
    "total_amount": 1000.00,
    "formatted_total_amount": "₹1,000.00",
    "paid_amount": 0.00,
    "formatted_paid_amount": "₹0.00",
    "balance_amount": 1000.00,
    "formatted_balance_amount": "₹1,000.00",
    "payment_status": "Unpaid",
    "is_overdue": false,
    "days_overdue": 0,
    "notes": "Monthly rent for January 2024",
    "terms_conditions": "Payment due within 30 days",
    "metadata": null,
    "paid_at": null,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

### Invoice Item Object
```json
{
    "id": 1,
    "invoice_id": 1,
    "item_type": "rent",
    "description": "Monthly rent for January 2024",
    "quantity": 1,
    "unit_price": 1000.00,
    "formatted_unit_price": "₹1,000.00",
    "total_price": 1000.00,
    "formatted_total_price": "₹1,000.00",
    "related_id": null,
    "related_type": null,
    "period_start": "2024-01-01",
    "period_end": "2024-01-31",
    "period_text": "Jan 1 - Jan 31, 2024",
    "metadata": null,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

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
    "is_verified": false,
    "verified_at": null,
    "recorded_by": {
        "id": 1,
        "name": "Admin User"
    },
    "verified_by": null,
    "created_at": "2024-01-15T16:00:00.000000Z",
    "updated_at": "2024-01-15T16:00:00.000000Z"
}
```

## Error Handling

### Validation Errors (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "tenant_profile_id": ["The tenant profile id field is required."],
        "type": ["The selected type is invalid."],
        "total_amount": ["The total amount must be at least 0."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Invoice not found"
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
    "message": "Failed to retrieve invoices",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: List, Show, Stats, Search (read-only operations)
- **Authenticated Endpoints**: Create, Update, Delete, Add Payment, Mark Overdue, Generate Amenity Invoice, Manage Items (write operations)
- **Authentication Method**: Bearer token via Laravel Sanctum

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/invoices
http://localhost/api/v1/invoices/1
http://localhost/api/v1/invoices/1/stats
http://localhost/api/v1/invoices/search?query=INV-202401
```

### cURL Examples
```bash
# List invoices
curl -X GET http://localhost/api/v1/invoices

# Get specific invoice
curl -X GET http://localhost/api/v1/invoices/1

# Create invoice
curl -X POST http://localhost/api/v1/invoices \
  -H "Content-Type: application/json" \
  -d '{"tenant_profile_id":1,"type":"rent","status":"draft","invoice_date":"2024-01-01","due_date":"2024-01-31","total_amount":1000.00}'

# Update invoice (authenticated)
curl -X PUT http://localhost/api/v1/invoices/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"status":"sent","notes":"Updated invoice"}'

# Delete invoice (authenticated)
curl -X DELETE http://localhost/api/v1/invoices/1 \
  -H "Authorization: Bearer {token}"

# Add payment (authenticated)
curl -X POST http://localhost/api/v1/invoices/1/add-payment \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"amount":500.00,"payment_method":"bank_transfer","status":"completed","reference_number":"TXN123456789"}'

# Generate amenity invoice (authenticated)
curl -X POST http://localhost/api/v1/invoices/generate-amenity \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"tenant_profile_id":1,"period_start":"2024-01-01","period_end":"2024-01-31","status":"sent"}'

# Add item to invoice (authenticated)
curl -X POST http://localhost/api/v1/invoices/1/add-item \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"item_type":"damage","description":"Room damage repair","quantity":1,"unit_price":200.00}'

# Search invoices
curl -X POST http://localhost/api/v1/invoices/search \
  -H "Content-Type: application/json" \
  -d '{"query":"INV-202401","type":"rent","limit":10}'
```

## Business Rules

1. **Invoice Deletion**: Cannot delete invoices with payments
2. **Payment Amount**: Payment amount cannot exceed invoice balance
3. **Invoice Number**: Auto-generated unique invoice numbers
4. **Status Values**: Only specified status values are allowed
5. **Type Values**: Only specified type values are allowed
6. **Date Validation**: Due date must be after or equal to invoice date
7. **Period Validation**: Period end date must be after or equal to period start date
8. **Search Minimum**: Search queries must be at least 2 characters
9. **Pagination**: Maximum 100 items per page for performance
10. **Auto-calculation**: Totals are automatically calculated when items are added/updated
11. **Payment Processing**: Invoice status updates automatically when payments are added
12. **Overdue Detection**: Invoices are automatically marked overdue when past due date
13. **Amenity Billing**: Automated amenity usage invoice generation based on usage records

## Related Modules

- **Tenants API**: Manage tenant profiles for invoice generation
- **Rooms & Beds API**: Reference room/bed information in invoice items
- **Payments API**: Process payments for invoices
- **Amenities API**: Track amenity usage for billing
- **Notifications API**: Send invoice notifications to tenants

---

*Module: Invoices API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
