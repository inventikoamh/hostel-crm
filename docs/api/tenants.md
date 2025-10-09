# Tenants API Module

## Overview
The Tenants API provides comprehensive endpoints for managing tenant profiles within the Hostel CRM system. This module handles tenant registration, profile management, bed assignments, billing cycles, and verification processes.

## Base Endpoints
All tenant endpoints are prefixed with `/api/v1/tenants/`

## Endpoints

### 1. List Tenants
Retrieve a paginated list of all tenants with optional filtering and sorting.

**GET Version (Testing):**
```
GET /api/v1/tenants?page=1&per_page=15&status=active&hostel_id=1&search=john&sort_by=name&sort_order=asc
```

**POST Version (Integration):**
```
POST /api/v1/tenants
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "status": "active",
    "hostel_id": 1,
    "search": "john",
    "sort_by": "name",
    "sort_order": "asc"
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `status` (optional): Filter by status (`active`, `inactive`, `pending`, `suspended`, `moved_out`)
- `verification_status` (optional): Filter by verification (`verified`, `unverified`)
- `hostel_id` (optional): Filter by hostel ID
- `payment_status` (optional): Filter by payment status (`paid`, `pending`, `overdue`, `partial`)
- `search` (optional): Search in name, email, phone, or ID proof number
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
```json
{
    "success": true,
    "message": "Tenants retrieved successfully",
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123",
            "date_of_birth": "1990-05-15",
            "age": 34,
            "address": "123 Main Street, City, State",
            "occupation": "Software Engineer",
            "company": "Tech Corp",
            "id_proof_type": "passport",
            "id_proof_type_display": "Passport",
            "id_proof_number": "P123456789",
            "emergency_contact_name": "Jane Doe",
            "emergency_contact_phone": "+1-555-0124",
            "emergency_contact_relation": "Sister",
            "status": "active",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "icon": "fas fa-check-circle"
            },
            "move_in_date": "2024-01-01",
            "move_out_date": null,
            "tenancy_duration": 365,
            "tenancy_duration_human": "1 year",
            "security_deposit": 500.00,
            "monthly_rent": 800.00,
            "lease_start_date": "2024-01-01",
            "lease_end_date": "2024-12-31",
            "is_lease_expired": false,
            "days_until_lease_expiry": 45,
            "notes": "Prefers ground floor room",
            "is_verified": true,
            "verified_at": "2024-01-02T10:00:00.000000Z",
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

### 2. Create Tenant
Create a new tenant profile with user account.

**GET Version (Testing):**
```
GET /api/v1/tenants/create
```

**POST Version (Integration):**
```
POST /api/v1/tenants
Content-Type: application/json

{
    "name": "Jane Smith",
    "email": "jane.smith@example.com",
    "password": "password123",
    "phone": "+1-555-0456",
    "date_of_birth": "1992-08-20",
    "address": "456 Oak Avenue, City, State",
    "occupation": "Marketing Manager",
    "company": "Marketing Inc",
    "id_proof_type": "driving_license",
    "id_proof_number": "DL987654321",
    "emergency_contact_name": "Bob Smith",
    "emergency_contact_phone": "+1-555-0457",
    "emergency_contact_relation": "Brother",
    "status": "pending",
    "move_in_date": "2024-02-01",
    "security_deposit": 600.00,
    "monthly_rent": 900.00,
    "lease_start_date": "2024-02-01",
    "lease_end_date": "2025-01-31",
    "notes": "Vegetarian, needs parking space",
    "billing_cycle": "monthly",
    "billing_day": 1,
    "auto_billing_enabled": true,
    "reminder_days_before": 3,
    "overdue_grace_days": 5,
    "late_fee_amount": 25.00,
    "auto_payment_enabled": false
}
```

**Required Fields:**
- `name`: Tenant full name
- `email`: Email address (must be unique)
- `password`: Password (minimum 6 characters)
- `phone`: Phone number
- `status`: Status (`active`, `inactive`, `pending`, `suspended`, `moved_out`)

**Optional Fields:**
- `date_of_birth`: Date of birth (YYYY-MM-DD format)
- `address`: Current address
- `occupation`: Job title/occupation
- `company`: Company name
- `id_proof_type`: ID type (`aadhar`, `passport`, `driving_license`, `voter_id`, `pan_card`, `other`)
- `id_proof_number`: ID proof number
- `emergency_contact_name`: Emergency contact name
- `emergency_contact_phone`: Emergency contact phone
- `emergency_contact_relation`: Relationship to emergency contact
- `move_in_date`: Move-in date (YYYY-MM-DD format)
- `move_out_date`: Move-out date (YYYY-MM-DD format)
- `security_deposit`: Security deposit amount
- `monthly_rent`: Monthly rent amount
- `lease_start_date`: Lease start date (YYYY-MM-DD format)
- `lease_end_date`: Lease end date (YYYY-MM-DD format)
- `notes`: Additional notes
- `documents`: Array of document file paths
- `billing_cycle`: Billing cycle (`monthly`, `quarterly`, `half_yearly`, `yearly`)
- `billing_day`: Day of month for billing (1-31)
- `auto_billing_enabled`: Enable automatic billing (boolean)
- `notification_preferences`: Array of notification preferences
- `reminder_days_before`: Days before billing to send reminder
- `overdue_grace_days`: Grace period for overdue payments
- `late_fee_amount`: Fixed late fee amount
- `late_fee_percentage`: Late fee percentage
- `compound_late_fees`: Enable compound late fees (boolean)
- `auto_payment_enabled`: Enable automatic payments (boolean)
- `payment_method`: Preferred payment method
- `payment_details`: Payment method details (array)

**Response (201):**
```json
{
    "success": true,
    "message": "Tenant created successfully",
    "data": {
        "id": 2,
        "user_id": 2,
        "name": "Jane Smith",
        "email": "jane.smith@example.com",
        "phone": "+1-555-0456",
        "date_of_birth": "1992-08-20",
        "age": 32,
        "address": "456 Oak Avenue, City, State",
        "occupation": "Marketing Manager",
        "company": "Marketing Inc",
        "id_proof_type": "driving_license",
        "id_proof_type_display": "Driving License",
        "id_proof_number": "DL987654321",
        "emergency_contact_name": "Bob Smith",
        "emergency_contact_phone": "+1-555-0457",
        "emergency_contact_relation": "Brother",
        "status": "pending",
        "status_badge": {
            "class": "bg-yellow-100 text-yellow-800",
            "icon": "fas fa-clock"
        },
        "move_in_date": "2024-02-01",
        "move_out_date": null,
        "tenancy_duration": null,
        "tenancy_duration_human": "Not moved in",
        "security_deposit": 600.00,
        "monthly_rent": 900.00,
        "lease_start_date": "2024-02-01",
        "lease_end_date": "2025-01-31",
        "is_lease_expired": false,
        "days_until_lease_expiry": null,
        "notes": "Vegetarian, needs parking space",
        "is_verified": false,
        "verified_at": null,
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### 3. Get Tenant Details
Retrieve detailed information about a specific tenant.

**GET Version (Testing):**
```
GET /api/v1/tenants/1
```

**POST Version (Integration):**
```
POST /api/v1/tenants/1
Content-Type: application/json
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant retrieved successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "phone": "+1-555-0123",
        "date_of_birth": "1990-05-15",
        "age": 34,
        "address": "123 Main Street, City, State",
        "occupation": "Software Engineer",
        "company": "Tech Corp",
        "id_proof_type": "passport",
        "id_proof_type_display": "Passport",
        "id_proof_number": "P123456789",
        "emergency_contact_name": "Jane Doe",
        "emergency_contact_phone": "+1-555-0124",
        "emergency_contact_relation": "Sister",
        "status": "active",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "icon": "fas fa-check-circle"
        },
        "move_in_date": "2024-01-01",
        "move_out_date": null,
        "tenancy_duration": 365,
        "tenancy_duration_human": "1 year",
        "security_deposit": 500.00,
        "monthly_rent": 800.00,
        "lease_start_date": "2024-01-01",
        "lease_end_date": "2024-12-31",
        "is_lease_expired": false,
        "days_until_lease_expiry": 45,
        "notes": "Prefers ground floor room",
        "is_verified": true,
        "verified_at": "2024-01-02T10:00:00.000000Z",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "documents": ["document1.pdf", "document2.pdf"],
        "current_bed": {
            "id": 1,
            "bed_number": "A1",
            "room": {
                "id": 1,
                "room_number": "101",
                "floor": 1,
                "hostel": {
                    "id": 1,
                    "name": "Downtown Hostel"
                }
            }
        },
        "current_hostel": {
            "id": 1,
            "name": "Downtown Hostel"
        },
        "verified_by": {
            "id": 2,
            "name": "Admin User"
        },
        "billing_info": {
            "billing_cycle": "monthly",
            "billing_cycle_display": "Monthly",
            "billing_day": 1,
            "next_billing_date": "2024-02-01",
            "last_billing_date": "2024-01-01",
            "next_billing_amount": 800.00,
            "outstanding_amount": 0.00,
            "payment_status": "paid",
            "payment_status_badge": {
                "class": "bg-green-100 text-green-800",
                "icon": "fas fa-check-circle"
            },
            "is_payment_overdue": false,
            "auto_billing_enabled": true,
            "auto_payment_enabled": false
        },
        "payment_history": {
            "consecutive_on_time_payments": 12,
            "total_late_payments": 1,
            "payment_history_score": 92.3,
            "last_payment_date": "2024-01-01",
            "last_payment_amount": 800.00
        },
        "amenities_count": {
            "total": 3,
            "active": 2
        },
        "documents_count": 2
    }
}
```

### 4. Update Tenant
Update an existing tenant's information.

**GET Version (Testing):**
```
GET /api/v1/tenants/1/edit
```

**POST Version (Integration):**
```
POST /api/v1/tenants/1
Content-Type: application/json

{
    "name": "John Updated Doe",
    "phone": "+1-555-0125",
    "status": "active",
    "monthly_rent": 850.00
}
```

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/tenants/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Updated Doe",
    "phone": "+1-555-0125",
    "status": "active",
    "monthly_rent": 850.00
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant updated successfully",
    "data": {
        "id": 1,
        "name": "John Updated Doe",
        "phone": "+1-555-0125",
        "status": "active",
        "monthly_rent": 850.00,
        "updated_at": "2024-01-15T15:30:00.000000Z"
    }
}
```

### 5. Delete Tenant
Remove a tenant from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/tenants/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant deleted successfully"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot delete tenant with active bed assignments. Please release bed assignments first."
}
```

### 6. Get Tenant Statistics
Retrieve statistical information about a tenant.

**GET Version (Testing):**
```
GET /api/v1/tenants/1/stats
```

**POST Version (Integration):**
```
POST /api/v1/tenants/1/stats
Content-Type: application/json
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant statistics retrieved successfully",
    "data": {
        "basic_info": {
            "age": 34,
            "tenancy_duration": 365,
            "tenancy_duration_human": "1 year",
            "is_lease_expired": false,
            "days_until_lease_expiry": 45
        },
        "billing_info": {
            "billing_cycle": "monthly",
            "billing_cycle_display": "Monthly",
            "next_billing_date": "2024-02-01",
            "last_billing_date": "2024-01-01",
            "next_billing_amount": 800.00,
            "outstanding_amount": 0.00,
            "total_outstanding": 0.00,
            "payment_status": "paid",
            "is_payment_overdue": false,
            "days_until_next_billing": 17
        },
        "payment_history": {
            "consecutive_on_time_payments": 12,
            "total_late_payments": 1,
            "payment_history_score": 92.3,
            "last_payment_date": "2024-01-01",
            "last_payment_amount": 800.00
        },
        "current_accommodation": {
            "current_bed": {
                "id": 1,
                "bed_number": "A1",
                "room": {
                    "id": 1,
                    "room_number": "101",
                    "floor": 1,
                    "hostel": {
                        "id": 1,
                        "name": "Downtown Hostel"
                    }
                }
            },
            "current_hostel": {
                "id": 1,
                "name": "Downtown Hostel"
            }
        },
        "amenities": {
            "total_amenities": 3,
            "active_amenities": 2
        },
        "documents": {
            "total_documents": 2,
            "is_verified": true,
            "verified_at": "2024-01-02T10:00:00.000000Z",
            "verified_by": {
                "id": 2,
                "name": "Admin User"
            }
        }
    }
}
```

### 7. Search Tenants
Search for tenants by various criteria.

**GET Version (Testing):**
```
GET /api/v1/tenants/search?query=john&limit=10
```

**POST Version (Integration):**
```
POST /api/v1/tenants/search
Content-Type: application/json

{
    "query": "john",
    "limit": 10
}
```

**Parameters:**
- `query` (required): Search term (minimum 2 characters)
- `limit` (optional): Maximum number of results (default: 10, max: 50)

**Response (200):**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123",
            "status": "active",
            "current_bed": {
                "bed_number": "A1",
                "room": {
                    "room_number": "101",
                    "hostel": {
                        "name": "Downtown Hostel"
                    }
                }
            }
        }
    ],
    "query": "john",
    "count": 1
}
```

### 8. Assign Tenant to Bed
Assign a tenant to a specific bed.

**POST Version (Authenticated):**
```
POST /api/v1/tenants/1/assign-bed
Authorization: Bearer {token}
Content-Type: application/json

{
    "bed_id": 5,
    "move_in_date": "2024-02-01",
    "rent": 850.00,
    "lease_end_date": "2025-01-31"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant assigned to bed successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "status": "active",
        "move_in_date": "2024-02-01",
        "monthly_rent": 850.00,
        "current_bed": {
            "id": 5,
            "bed_number": "B2",
            "room": {
                "id": 3,
                "room_number": "201",
                "floor": 2,
                "hostel": {
                    "id": 1,
                    "name": "Downtown Hostel"
                }
            }
        }
    }
}
```

### 9. Release Tenant from Bed
Release a tenant from their current bed assignment.

**POST Version (Authenticated):**
```
POST /api/v1/tenants/1/release-bed
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant released from bed successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "status": "active",
        "current_bed": null
    }
}
```

### 10. Verify Tenant
Mark a tenant as verified.

**POST Version (Authenticated):**
```
POST /api/v1/tenants/1/verify
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant verified successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "is_verified": true,
        "verified_at": "2024-01-15T16:00:00.000000Z",
        "verified_by": {
            "id": 2,
            "name": "Admin User"
        }
    }
}
```

## Data Models

### Tenant Object
```json
{
    "id": 1,
    "user_id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "phone": "+1-555-0123",
    "date_of_birth": "1990-05-15",
    "age": 34,
    "address": "123 Main Street, City, State",
    "occupation": "Software Engineer",
    "company": "Tech Corp",
    "id_proof_type": "passport",
    "id_proof_type_display": "Passport",
    "id_proof_number": "P123456789",
    "emergency_contact_name": "Jane Doe",
    "emergency_contact_phone": "+1-555-0124",
    "emergency_contact_relation": "Sister",
    "status": "active",
    "status_badge": {
        "class": "bg-green-100 text-green-800",
        "icon": "fas fa-check-circle"
    },
    "move_in_date": "2024-01-01",
    "move_out_date": null,
    "tenancy_duration": 365,
    "tenancy_duration_human": "1 year",
    "security_deposit": 500.00,
    "monthly_rent": 800.00,
    "lease_start_date": "2024-01-01",
    "lease_end_date": "2024-12-31",
    "is_lease_expired": false,
    "days_until_lease_expiry": 45,
    "notes": "Prefers ground floor room",
    "is_verified": true,
    "verified_at": "2024-01-02T10:00:00.000000Z",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

## Error Handling

### Validation Errors (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email must be a valid email address.", "The email has already been taken."],
        "password": ["The password must be at least 6 characters."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Tenant not found"
}
```

### Server Errors (500)
```json
{
    "success": false,
    "message": "Failed to retrieve tenants",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: List, Show, Search, Stats (read-only operations)
- **Authenticated Endpoints**: Create, Update, Delete, Assign Bed, Release Bed, Verify (write operations)
- **Authentication Method**: Bearer token via Laravel Sanctum

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/tenants
http://localhost/api/v1/tenants/1
http://localhost/api/v1/tenants/1/stats
http://localhost/api/v1/tenants/search?query=john
```

### cURL Examples
```bash
# List tenants
curl -X GET http://localhost/api/v1/tenants

# Get specific tenant
curl -X GET http://localhost/api/v1/tenants/1

# Create tenant
curl -X POST http://localhost/api/v1/tenants \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Tenant","email":"test@tenant.com","password":"password123","phone":"+1-555-TEST","status":"pending"}'

# Update tenant (authenticated)
curl -X PUT http://localhost/api/v1/tenants/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name":"Updated Tenant Name"}'

# Delete tenant (authenticated)
curl -X DELETE http://localhost/api/v1/tenants/1 \
  -H "Authorization: Bearer {token}"

# Search tenants
curl -X POST http://localhost/api/v1/tenants/search \
  -H "Content-Type: application/json" \
  -d '{"query":"john","limit":5}'

# Assign tenant to bed (authenticated)
curl -X POST http://localhost/api/v1/tenants/1/assign-bed \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"bed_id":5,"move_in_date":"2024-02-01","rent":850.00}'

# Release tenant from bed (authenticated)
curl -X POST http://localhost/api/v1/tenants/1/release-bed \
  -H "Authorization: Bearer {token}"

# Verify tenant (authenticated)
curl -X POST http://localhost/api/v1/tenants/1/verify \
  -H "Authorization: Bearer {token}"
```

## Business Rules

1. **Tenant Deletion**: Cannot delete tenants with active bed assignments
2. **Email Uniqueness**: Email addresses must be unique across all users
3. **Bed Assignment**: Only one tenant per bed at a time
4. **Status Values**: Only `active`, `inactive`, `pending`, `suspended`, or `moved_out` are allowed
5. **ID Proof Types**: Only specified types are allowed (`aadhar`, `passport`, `driving_license`, `voter_id`, `pan_card`, `other`)
6. **Billing Cycle**: Only `monthly`, `quarterly`, `half_yearly`, or `yearly` are allowed
7. **Date Validation**: Move-out date must be after move-in date, lease end date must be after lease start date
8. **Search Minimum**: Search queries must be at least 2 characters
9. **Pagination**: Maximum 100 items per page for performance

## Related Modules

- **Hostels API**: Manage hostel properties
- **Rooms & Beds API**: Manage room and bed assignments
- **Invoices API**: Generate invoices for tenant billing
- **Payments API**: Process payments from tenants
- **Amenities API**: Manage tenant amenity subscriptions

---

*Module: Tenants API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
