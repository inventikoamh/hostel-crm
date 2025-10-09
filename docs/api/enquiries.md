# Enquiries API Module

## Overview
The Enquiries API provides comprehensive endpoints for managing customer enquiries, leads, and support requests within the Hostel CRM system. This module handles enquiry lifecycle management, assignment, resolution, and conversion to tenant profiles.

## Base Endpoints
All enquiry endpoints are prefixed with `/api/v1/enquiries/`

## Endpoints

### 1. Enquiries Management

#### List Enquiries
Retrieve a paginated list of all enquiries with optional filtering.

**GET Version (Testing):**
```
GET /api/v1/enquiries?page=1&per_page=15&status=new&priority=high&enquiry_type=room_booking&overdue=true
```

**POST Version (Integration):**
```
POST /api/v1/enquiries
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "status": "new",
    "priority": "high",
    "enquiry_type": "room_booking",
    "overdue": true
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `status` (optional): Filter by status (`new`, `in_progress`, `resolved`, `closed`)
- `priority` (optional): Filter by priority (`low`, `medium`, `high`, `urgent`)
- `enquiry_type` (optional): Filter by enquiry type (`room_booking`, `general_info`, `pricing`, `facilities`, `other`)
- `assigned_to` (optional): Filter by assigned user ID or `unassigned`
- `source` (optional): Filter by enquiry source
- `overdue` (optional): Filter overdue enquiries (boolean)
- `date_from` (optional): Filter enquiries from this date
- `date_to` (optional): Filter enquiries to this date
- `responded_from` (optional): Filter by response date from
- `responded_to` (optional): Filter by response date to
- `search` (optional): Search in name, email, phone, subject, or message
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiries retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123",
            "enquiry_type": "room_booking",
            "enquiry_type_display": "Room Booking",
            "subject": "Room availability inquiry",
            "message": "I am interested in booking a room for next month.",
            "status": "new",
            "status_badge": {
                "class": "bg-blue-100 text-blue-800",
                "icon": "fas fa-star"
            },
            "priority": "high",
            "priority_badge": {
                "class": "bg-orange-100 text-orange-800",
                "icon": "fas fa-arrow-up"
            },
            "source": "website",
            "is_overdue": false,
            "created_at": "2024-01-15T10:30:00.000000Z",
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

#### Create Enquiry
Create a new enquiry.

**GET Version (Testing):**
```
GET /api/v1/enquiries/create
```

**POST Version (Integration):**
```
POST /api/v1/enquiries
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "phone": "+1-555-0123",
    "enquiry_type": "room_booking",
    "subject": "Room availability inquiry",
    "message": "I am interested in booking a room for next month. Please let me know about availability and pricing.",
    "priority": "medium",
    "source": "website",
    "metadata": {
        "referrer": "google",
        "utm_source": "search"
    }
}
```

**Required Fields:**
- `name`: Enquirer name
- `email`: Email address
- `phone`: Phone number
- `enquiry_type`: Enquiry type (`room_booking`, `general_info`, `pricing`, `facilities`, `other`)
- `subject`: Enquiry subject
- `message`: Enquiry message

**Optional Fields:**
- `status`: Status (`new`, `in_progress`, `resolved`, `closed`)
- `priority`: Priority (`low`, `medium`, `high`, `urgent`)
- `admin_notes`: Admin notes
- `assigned_to`: Assigned user ID
- `source`: Enquiry source
- `metadata`: Additional metadata (JSON)

**Response (201):**
```json
{
    "success": true,
    "message": "Enquiry created successfully",
    "data": {
        "id": 2,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "phone": "+1-555-0123",
        "enquiry_type": "room_booking",
        "enquiry_type_display": "Room Booking",
        "subject": "Room availability inquiry",
        "message": "I am interested in booking a room for next month.",
        "status": "new",
        "status_badge": {
            "class": "bg-blue-100 text-blue-800",
            "icon": "fas fa-star"
        },
        "priority": "medium",
        "priority_badge": {
            "class": "bg-blue-100 text-blue-800",
            "icon": "fas fa-minus"
        },
        "source": "website",
        "is_overdue": false,
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

#### Get Enquiry Details
Retrieve detailed information about a specific enquiry.

**GET Version (Testing):**
```
GET /api/v1/enquiries/1
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "phone": "+1-555-0123",
        "enquiry_type": "room_booking",
        "enquiry_type_display": "Room Booking",
        "subject": "Room availability inquiry",
        "message": "I am interested in booking a room for next month.",
        "status": "new",
        "status_badge": {
            "class": "bg-blue-100 text-blue-800",
            "icon": "fas fa-star"
        },
        "priority": "high",
        "priority_badge": {
            "class": "bg-orange-100 text-orange-800",
            "icon": "fas fa-arrow-up"
        },
        "source": "website",
        "is_overdue": false,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "admin_notes": null,
        "assigned_to": null,
        "assigned_user": null,
        "responded_at": null,
        "metadata": {
            "referrer": "google",
            "utm_source": "search"
        }
    }
}
```

#### Update Enquiry
Update an existing enquiry's information.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/enquiries/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "in_progress",
    "priority": "high",
    "admin_notes": "Customer called for urgent booking"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry updated successfully",
    "data": {
        "id": 1,
        "status": "in_progress",
        "status_badge": {
            "class": "bg-yellow-100 text-yellow-800",
            "icon": "fas fa-clock"
        },
        "priority": "high",
        "priority_badge": {
            "class": "bg-orange-100 text-orange-800",
            "icon": "fas fa-arrow-up"
        },
        "admin_notes": "Customer called for urgent booking",
        "updated_at": "2024-01-15T15:00:00.000000Z"
    }
}
```

#### Delete Enquiry
Remove an enquiry from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/enquiries/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry deleted successfully"
}
```

### 2. Enquiry Management Operations

#### Assign Enquiry to User
Assign an enquiry to a specific user.

**POST Version (Authenticated):**
```
POST /api/v1/enquiries/1/assign
Authorization: Bearer {token}
Content-Type: application/json

{
    "assigned_to": 2,
    "admin_notes": "Assigned to sales team"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry assigned successfully",
    "data": {
        "id": 1,
        "status": "in_progress",
        "status_badge": {
            "class": "bg-yellow-100 text-yellow-800",
            "icon": "fas fa-clock"
        },
        "assigned_to": 2,
        "assigned_user": {
            "id": 2,
            "name": "Jane Smith",
            "email": "jane.smith@hostel.com"
        },
        "admin_notes": "Assigned to sales team"
    }
}
```

#### Resolve Enquiry
Mark an enquiry as resolved.

**POST Version (Authenticated):**
```
POST /api/v1/enquiries/1/resolve
Authorization: Bearer {token}
Content-Type: application/json

{
    "admin_notes": "Customer satisfied with response",
    "resolution_notes": "Provided room availability and pricing information"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry resolved successfully",
    "data": {
        "id": 1,
        "status": "resolved",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "icon": "fas fa-check-circle"
        },
        "responded_at": "2024-01-15T16:00:00.000000Z",
        "admin_notes": "Customer satisfied with response\nResolution: Provided room availability and pricing information"
    }
}
```

#### Close Enquiry
Close an enquiry without resolution.

**POST Version (Authenticated):**
```
POST /api/v1/enquiries/1/close
Authorization: Bearer {token}
Content-Type: application/json

{
    "admin_notes": "Customer no longer interested",
    "closure_reason": "Customer found alternative accommodation"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry closed successfully",
    "data": {
        "id": 1,
        "status": "closed",
        "status_badge": {
            "class": "bg-gray-100 text-gray-800",
            "icon": "fas fa-times-circle"
        },
        "responded_at": "2024-01-15T16:00:00.000000Z",
        "admin_notes": "Customer no longer interested\nClosure Reason: Customer found alternative accommodation"
    }
}
```

#### Convert Enquiry to Tenant
Convert an enquiry into a tenant profile.

**POST Version (Authenticated):**
```
POST /api/v1/enquiries/1/convert-to-tenant
Authorization: Bearer {token}
Content-Type: application/json

{
    "password": "password123",
    "date_of_birth": "1990-05-15",
    "address": "123 Main Street, City, State",
    "occupation": "Software Engineer",
    "company": "Tech Corp",
    "id_proof_type": "passport",
    "id_proof_number": "P123456789",
    "emergency_contact_name": "Jane Doe",
    "emergency_contact_phone": "+1-555-0124",
    "emergency_contact_relation": "Sister",
    "move_in_date": "2024-02-01",
    "security_deposit": 500,
    "monthly_rent": 800,
    "lease_start_date": "2024-02-01",
    "lease_end_date": "2024-12-31",
    "notes": "Converted from enquiry",
    "billing_cycle": "monthly",
    "billing_day": 1,
    "auto_billing_enabled": true,
    "reminder_days_before": 3,
    "overdue_grace_days": 5,
    "late_fee_amount": 25,
    "auto_payment_enabled": false
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry converted to tenant successfully",
    "data": {
        "enquiry": {
            "id": 1,
            "status": "resolved",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "icon": "fas fa-check-circle"
            },
            "responded_at": "2024-01-15T16:00:00.000000Z",
            "admin_notes": "Converted to tenant profile #2"
        },
        "user": {
            "id": 3,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123",
            "is_tenant": true,
            "status": "active"
        },
        "tenant_profile": {
            "id": 2,
            "user_id": 3,
            "first_name": "John",
            "last_name": "Doe",
            "phone": "+1-555-0123",
            "status": "pending",
            "notes": "Converted from enquiry #1: Converted from enquiry"
        }
    }
}
```

### 3. Statistics and Analytics

#### Get Enquiry Statistics
Retrieve comprehensive enquiry statistics.

**GET Version (Testing):**
```
GET /api/v1/enquiries/stats
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry statistics retrieved successfully",
    "data": {
        "total_enquiries": 150,
        "new_enquiries": 25,
        "in_progress_enquiries": 30,
        "resolved_enquiries": 80,
        "closed_enquiries": 15,
        "unassigned_enquiries": 10,
        "overdue_enquiries": 5,
        "urgent_enquiries": 8,
        "high_priority_enquiries": 20,
        "room_booking_enquiries": 100,
        "general_info_enquiries": 25,
        "pricing_enquiries": 15,
        "facilities_enquiries": 8,
        "other_enquiries": 2,
        "today_enquiries": 5,
        "this_week_enquiries": 25,
        "this_month_enquiries": 80,
        "response_rate": 85.5,
        "average_response_time": 12.5
    }
}
```

#### Get Enquiry Sources
Retrieve enquiry sources and their counts.

**GET Version (Testing):**
```
GET /api/v1/enquiries/sources
```

**Response (200):**
```json
{
    "success": true,
    "message": "Enquiry sources retrieved successfully",
    "data": [
        {
            "source": "website",
            "count": 80
        },
        {
            "source": "phone",
            "count": 35
        },
        {
            "source": "walk-in",
            "count": 20
        },
        {
            "source": "referral",
            "count": 15
        }
    ]
}
```

### 4. Search API
Search for enquiries with advanced filtering.

**GET Version (Testing):**
```
GET /api/v1/enquiries/search?query=john&status=new&priority=high&limit=10
```

**POST Version (Integration):**
```
POST /api/v1/enquiries/search
Content-Type: application/json

{
    "query": "john",
    "status": "new",
    "priority": "high",
    "enquiry_type": "room_booking",
    "limit": 10
}
```

**Parameters:**
- `query` (required): Search term (minimum 2 characters)
- `status` (optional): Filter by status
- `priority` (optional): Filter by priority
- `enquiry_type` (optional): Filter by enquiry type
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
            "enquiry_type": "room_booking",
            "enquiry_type_display": "Room Booking",
            "subject": "Room availability inquiry",
            "status": "new",
            "status_badge": {
                "class": "bg-blue-100 text-blue-800",
                "icon": "fas fa-star"
            },
            "priority": "high",
            "priority_badge": {
                "class": "bg-orange-100 text-orange-800",
                "icon": "fas fa-arrow-up"
            },
            "source": "website",
            "assigned_to": null,
            "is_overdue": false,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "responded_at": null
        }
    ],
    "query": "john",
    "count": 1
}
```

## Data Models

### Enquiry Object
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "phone": "+1-555-0123",
    "enquiry_type": "room_booking",
    "enquiry_type_display": "Room Booking",
    "subject": "Room availability inquiry",
    "message": "I am interested in booking a room for next month.",
    "status": "new",
    "status_badge": {
        "class": "bg-blue-100 text-blue-800",
        "icon": "fas fa-star"
    },
    "priority": "high",
    "priority_badge": {
        "class": "bg-orange-100 text-orange-800",
        "icon": "fas fa-arrow-up"
    },
    "source": "website",
    "is_overdue": false,
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

### Detailed Enquiry Object
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "phone": "+1-555-0123",
    "enquiry_type": "room_booking",
    "enquiry_type_display": "Room Booking",
    "subject": "Room availability inquiry",
    "message": "I am interested in booking a room for next month.",
    "status": "new",
    "status_badge": {
        "class": "bg-blue-100 text-blue-800",
        "icon": "fas fa-star"
    },
    "priority": "high",
    "priority_badge": {
        "class": "bg-orange-100 text-orange-800",
        "icon": "fas fa-arrow-up"
    },
    "source": "website",
    "is_overdue": false,
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z",
    "admin_notes": null,
    "assigned_to": null,
    "assigned_user": null,
    "responded_at": null,
    "metadata": {
        "referrer": "google",
        "utm_source": "search"
    }
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
        "email": ["The email field is required."],
        "phone": ["The phone field is required."],
        "enquiry_type": ["The enquiry type field is required."],
        "subject": ["The subject field is required."],
        "message": ["The message field is required."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Enquiry not found"
}
```

### Conflict Errors (422)
```json
{
    "success": false,
    "message": "A user with this email already exists"
}
```

### Server Errors (500)
```json
{
    "success": false,
    "message": "Failed to retrieve enquiries",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: List, Show, Search, Stats, Sources (read-only operations)
- **Authenticated Endpoints**: Create, Update, Delete, Assign, Resolve, Close, Convert to Tenant (write operations)
- **Authentication Method**: Bearer token via Laravel Sanctum

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/enquiries
http://localhost/api/v1/enquiries/create
http://localhost/api/v1/enquiries/stats
http://localhost/api/v1/enquiries/sources
http://localhost/api/v1/enquiries/search?query=room
```

### cURL Examples
```bash
# List enquiries
curl -X GET http://localhost/api/v1/enquiries

# Create enquiry
curl -X POST http://localhost/api/v1/enquiries \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john.doe@example.com","phone":"+1-555-0123","enquiry_type":"room_booking","subject":"Room inquiry","message":"Interested in booking a room","priority":"medium","source":"website"}'

# Update enquiry
curl -X PUT http://localhost/api/v1/enquiries/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"status":"in_progress","priority":"high","admin_notes":"Customer called"}'

# Delete enquiry
curl -X DELETE http://localhost/api/v1/enquiries/1 \
  -H "Authorization: Bearer {token}"

# Assign enquiry
curl -X POST http://localhost/api/v1/enquiries/1/assign \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"assigned_to":2,"admin_notes":"Assigned to sales team"}'

# Resolve enquiry
curl -X POST http://localhost/api/v1/enquiries/1/resolve \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"admin_notes":"Customer satisfied","resolution_notes":"Provided information"}'

# Close enquiry
curl -X POST http://localhost/api/v1/enquiries/1/close \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"admin_notes":"Customer not interested","closure_reason":"Found alternative"}'

# Convert to tenant
curl -X POST http://localhost/api/v1/enquiries/1/convert-to-tenant \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"password":"password123","date_of_birth":"1990-05-15","address":"123 Main St","occupation":"Engineer","move_in_date":"2024-02-01","security_deposit":500,"monthly_rent":800}'

# Get statistics
curl -X GET http://localhost/api/v1/enquiries/stats

# Get sources
curl -X GET http://localhost/api/v1/enquiries/sources

# Search enquiries
curl -X POST http://localhost/api/v1/enquiries/search \
  -H "Content-Type: application/json" \
  -d '{"query":"john","status":"new","priority":"high","limit":10}'
```

## Business Rules

1. **Enquiry Types**: Only specified enquiry types are allowed
2. **Status Values**: Only specified status values are allowed
3. **Priority Values**: Only specified priority values are allowed
4. **Auto Response Time**: Automatically sets `responded_at` when status changes to resolved/closed
5. **Overdue Detection**: Enquiries are considered overdue if older than 24 hours and still new/in_progress
6. **Assignment Logic**: Assigning an enquiry automatically changes status to in_progress
7. **Conversion Logic**: Converting enquiry to tenant creates both User and TenantProfile records
8. **Email Uniqueness**: Prevents conversion if user with same email already exists
9. **Search Minimum**: Search queries must be at least 2 characters
10. **Pagination**: Maximum 100 items per page for performance
11. **Priority Sorting**: Custom priority sorting (urgent > high > medium > low)
12. **Response Tracking**: Automatic response time calculation and tracking
13. **Metadata Support**: Flexible metadata storage for tracking sources and UTM parameters
14. **Admin Notes**: Append-only admin notes for audit trail
15. **Source Tracking**: Track enquiry sources for analytics

## Related Modules

- **Authentication API**: User authentication for admin operations
- **Users API**: User management for assignment and conversion
- **Tenants API**: Tenant profile creation during conversion
- **Hostels API**: Hostel information for room booking enquiries
- **Rooms API**: Room availability for booking enquiries
- **Notifications API**: Notification system for enquiry updates
- **Dashboard API**: Dashboard analytics and summaries

---

*Module: Enquiries API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
