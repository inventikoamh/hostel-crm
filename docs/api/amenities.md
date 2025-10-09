# Amenities API Module

## Overview
The Amenities API provides comprehensive endpoints for managing amenities within the Hostel CRM system. This module handles basic amenities, paid amenities, tenant subscriptions, and usage tracking for billing purposes.

## Base Endpoints
All amenity endpoints are prefixed with `/api/v1/amenities/`

## Endpoints

### 1. Basic Amenities Management

#### List Basic Amenities
Retrieve a paginated list of all basic amenities.

**GET Version (Testing):**
```
GET /api/v1/amenities?page=1&per_page=15&is_active=true&search=WiFi
```

**POST Version (Integration):**
```
POST /api/v1/amenities
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "is_active": true,
    "search": "WiFi"
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `is_active` (optional): Filter by active status (boolean)
- `search` (optional): Search in name or description
- `sort_by` (optional): Sort field (default: `sort_order`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
```json
{
    "success": true,
    "message": "Amenities retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "WiFi",
            "icon": "fas fa-wifi",
            "description": "High-speed internet access",
            "is_active": true,
            "sort_order": 1,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
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

#### Create Basic Amenity
Create a new basic amenity.

**GET Version (Testing):**
```
GET /api/v1/amenities/create
```

**POST Version (Integration):**
```
POST /api/v1/amenities
Content-Type: application/json

{
    "name": "WiFi",
    "icon": "fas fa-wifi",
    "description": "High-speed internet access",
    "is_active": true,
    "sort_order": 1
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Amenity created successfully",
    "data": {
        "id": 2,
        "name": "WiFi",
        "icon": "fas fa-wifi",
        "description": "High-speed internet access",
        "is_active": true,
        "sort_order": 1,
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### 2. Paid Amenities Management

#### List Paid Amenities
Retrieve a paginated list of all paid amenities.

**GET Version (Testing):**
```
GET /api/v1/amenities/paid?page=1&per_page=15&category=laundry&billing_type=daily&is_active=true
```

**POST Version (Integration):**
```
POST /api/v1/amenities/paid
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "category": "laundry",
    "billing_type": "daily",
    "is_active": true
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `is_active` (optional): Filter by active status (boolean)
- `category` (optional): Filter by category (`food`, `cleaning`, `laundry`, `utilities`, `services`, `other`)
- `billing_type` (optional): Filter by billing type (`monthly`, `daily`)
- `price_min` (optional): Filter by minimum price
- `price_max` (optional): Filter by maximum price
- `search` (optional): Search in name or description
- `sort_by` (optional): Sort field (default: `name`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
```json
{
    "success": true,
    "message": "Paid amenities retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Laundry Service",
            "description": "Professional laundry service",
            "billing_type": "daily",
            "billing_type_display": "Daily",
            "price": 50.00,
            "formatted_price": "₹50.00/day",
            "category": "laundry",
            "category_display": "Laundry Services",
            "is_active": true,
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "text": "Active"
            },
            "availability_schedule": {
                "days": [1, 2, 3, 4, 5],
                "hours": ["09:00", "18:00"]
            },
            "max_usage_per_day": 2,
            "terms_conditions": "Service available Monday to Friday",
            "icon": "fas fa-tshirt",
            "icon_class": "fas fa-tshirt",
            "active_tenant_count": 5,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
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

#### Create Paid Amenity
Create a new paid amenity.

**GET Version (Testing):**
```
GET /api/v1/amenities/paid/create
```

**POST Version (Integration):**
```
POST /api/v1/amenities/paid
Content-Type: application/json

{
    "name": "Laundry Service",
    "description": "Professional laundry service",
    "billing_type": "daily",
    "price": 50.00,
    "category": "laundry",
    "is_active": true,
    "availability_schedule": {
        "days": [1, 2, 3, 4, 5],
        "hours": ["09:00", "18:00"]
    },
    "max_usage_per_day": 2,
    "terms_conditions": "Service available Monday to Friday",
    "icon": "fas fa-tshirt"
}
```

**Required Fields:**
- `name`: Amenity name
- `billing_type`: Billing type (`monthly`, `daily`)
- `price`: Price amount
- `category`: Category (`food`, `cleaning`, `laundry`, `utilities`, `services`, `other`)

**Optional Fields:**
- `description`: Amenity description
- `is_active`: Active status (boolean)
- `availability_schedule`: Availability schedule (JSON)
- `max_usage_per_day`: Maximum usage per day (integer)
- `terms_conditions`: Terms and conditions
- `icon`: FontAwesome icon class

**Response (201):**
```json
{
    "success": true,
    "message": "Paid amenity created successfully",
    "data": {
        "id": 2,
        "name": "Laundry Service",
        "description": "Professional laundry service",
        "billing_type": "daily",
        "billing_type_display": "Daily",
        "price": 50.00,
        "formatted_price": "₹50.00/day",
        "category": "laundry",
        "category_display": "Laundry Services",
        "is_active": true,
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Active"
        },
        "availability_schedule": {
            "days": [1, 2, 3, 4, 5],
            "hours": ["09:00", "18:00"]
        },
        "max_usage_per_day": 2,
        "terms_conditions": "Service available Monday to Friday",
        "icon": "fas fa-tshirt",
        "icon_class": "fas fa-tshirt",
        "active_tenant_count": 0,
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### 3. Tenant Amenity Subscriptions

#### List Tenant Amenity Subscriptions
Retrieve a paginated list of tenant amenity subscriptions.

**GET Version (Testing):**
```
GET /api/v1/amenities/subscriptions?page=1&per_page=15&tenant_profile_id=1&status=active&billing_type=daily
```

**POST Version (Integration):**
```
POST /api/v1/amenities/subscriptions
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "tenant_profile_id": 1,
    "status": "active",
    "billing_type": "daily"
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `tenant_profile_id` (optional): Filter by tenant profile ID
- `paid_amenity_id` (optional): Filter by paid amenity ID
- `status` (optional): Filter by status (`active`, `inactive`, `suspended`)
- `billing_type` (optional): Filter by billing type
- `is_current` (optional): Filter by current subscriptions (boolean)
- `start_date_from` (optional): Filter subscriptions from this date
- `start_date_to` (optional): Filter subscriptions to this date
- `search` (optional): Search in tenant name/email or amenity name
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant amenity subscriptions retrieved successfully",
    "data": [
        {
            "id": 1,
            "tenant_profile_id": 1,
            "paid_amenity_id": 1,
            "status": "active",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "text": "Active"
            },
            "start_date": "2024-01-01",
            "end_date": null,
            "custom_price": null,
            "custom_schedule": null,
            "notes": null,
            "effective_price": 50.00,
            "formatted_effective_price": "₹50.00/day",
            "is_current": true,
            "is_expired": false,
            "duration_days": 15,
            "duration_text": "Ongoing since Jan 1, 2024",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
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

#### Subscribe Tenant to Amenity
Subscribe a tenant to a paid amenity.

**POST Version (Integration):**
```
POST /api/v1/amenities/subscribe
Content-Type: application/json

{
    "tenant_profile_id": 1,
    "paid_amenity_id": 1,
    "start_date": "2024-01-01",
    "end_date": "2024-12-31",
    "custom_price": 45.00,
    "notes": "Special pricing for long-term subscription"
}
```

**Required Fields:**
- `tenant_profile_id`: Tenant Profile ID (must exist)
- `paid_amenity_id`: Paid Amenity ID (must exist)
- `start_date`: Subscription start date

**Optional Fields:**
- `end_date`: Subscription end date
- `custom_price`: Custom price override
- `custom_schedule`: Custom availability schedule
- `notes`: Subscription notes

**Response (201):**
```json
{
    "success": true,
    "message": "Tenant subscribed to amenity successfully",
    "data": {
        "id": 2,
        "tenant_profile_id": 1,
        "paid_amenity_id": 1,
        "status": "active",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Active"
        },
        "start_date": "2024-01-01",
        "end_date": "2024-12-31",
        "custom_price": 45.00,
        "custom_schedule": null,
        "notes": "Special pricing for long-term subscription",
        "effective_price": 45.00,
        "formatted_effective_price": "₹45.00/day",
        "is_current": true,
        "is_expired": false,
        "duration_days": 365,
        "duration_text": "Jan 1, 2024 - Dec 31, 2024",
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

#### Suspend Subscription
Suspend a tenant amenity subscription.

**POST Version (Authenticated):**
```
POST /api/v1/amenities/subscriptions/1/suspend
Authorization: Bearer {token}
Content-Type: application/json

{
    "reason": "Tenant requested temporary suspension"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant amenity subscription suspended successfully",
    "data": {
        "id": 1,
        "status": "suspended",
        "status_badge": {
            "class": "bg-red-100 text-red-800",
            "text": "Suspended"
        },
        "notes": "Suspended: Tenant requested temporary suspension"
    }
}
```

#### Reactivate Subscription
Reactivate a suspended tenant amenity subscription.

**POST Version (Authenticated):**
```
POST /api/v1/amenities/subscriptions/1/reactivate
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant amenity subscription reactivated successfully",
    "data": {
        "id": 1,
        "status": "active",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Active"
        }
    }
}
```

#### Terminate Subscription
Terminate a tenant amenity subscription.

**POST Version (Authenticated):**
```
POST /api/v1/amenities/subscriptions/1/terminate
Authorization: Bearer {token}
Content-Type: application/json

{
    "end_date": "2024-01-31"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant amenity subscription terminated successfully",
    "data": {
        "id": 1,
        "status": "inactive",
        "status_badge": {
            "class": "bg-gray-100 text-gray-800",
            "text": "Inactive"
        },
        "end_date": "2024-01-31",
        "is_current": false,
        "is_expired": true,
        "duration_text": "Expired on Jan 31, 2024"
    }
}
```

### 4. Amenity Usage Tracking

#### Record Amenity Usage
Record usage of an amenity by a tenant.

**POST Version (Authenticated):**
```
POST /api/v1/amenities/usage
Authorization: Bearer {token}
Content-Type: application/json

{
    "tenant_amenity_id": 1,
    "usage_date": "2024-01-15",
    "quantity": 2,
    "notes": "Laundry service used twice today"
}
```

**Required Fields:**
- `tenant_amenity_id`: Tenant Amenity Subscription ID (must exist)
- `usage_date`: Date of usage
- `quantity`: Quantity used (minimum 1)

**Optional Fields:**
- `notes`: Usage notes

**Response (201):**
```json
{
    "success": true,
    "message": "Amenity usage recorded successfully",
    "data": {
        "id": 1,
        "tenant_amenity_id": 1,
        "usage_date": "2024-01-15",
        "quantity": 2,
        "unit_price": 50.00,
        "formatted_unit_price": "₹50.00",
        "total_amount": 100.00,
        "formatted_total_amount": "₹100.00",
        "notes": "Laundry service used twice today",
        "usage_summary": "2x Laundry Service",
        "recorded_by": {
            "id": 1,
            "name": "Admin User"
        },
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

#### Get Usage Records
Retrieve amenity usage records with filtering.

**GET Version (Testing):**
```
GET /api/v1/amenities/usage?tenant_profile_id=1&usage_date_from=2024-01-01&usage_date_to=2024-01-31&month=1&year=2024
```

**POST Version (Integration):**
```
POST /api/v1/amenities/usage
Content-Type: application/json

{
    "tenant_profile_id": 1,
    "usage_date_from": "2024-01-01",
    "usage_date_to": "2024-01-31",
    "month": 1,
    "year": 2024
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `tenant_profile_id` (optional): Filter by tenant profile ID
- `paid_amenity_id` (optional): Filter by paid amenity ID
- `tenant_amenity_id` (optional): Filter by tenant amenity subscription ID
- `usage_date_from` (optional): Filter usage records from this date
- `usage_date_to` (optional): Filter usage records to this date
- `month` (optional): Filter by month (1-12)
- `year` (optional): Filter by year (default: current year)
- `sort_by` (optional): Sort field (default: `usage_date`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
```json
{
    "success": true,
    "message": "Amenity usage records retrieved successfully",
    "data": [
        {
            "id": 1,
            "tenant_amenity_id": 1,
            "usage_date": "2024-01-15",
            "quantity": 2,
            "unit_price": 50.00,
            "formatted_unit_price": "₹50.00",
            "total_amount": 100.00,
            "formatted_total_amount": "₹100.00",
            "notes": "Laundry service used twice today",
            "usage_summary": "2x Laundry Service",
            "recorded_by": {
                "id": 1,
                "name": "Admin User"
            },
            "created_at": "2024-01-15T12:00:00.000000Z",
            "updated_at": "2024-01-15T12:00:00.000000Z"
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

#### Get Tenant Usage Summary
Get amenity usage summary for a specific tenant.

**GET Version (Testing):**
```
GET /api/v1/amenities/usage/tenant/1/summary?month=1&year=2024
```

**POST Version (Integration):**
```
POST /api/v1/amenities/usage/tenant/1/summary
Content-Type: application/json

{
    "month": 1,
    "year": 2024
}
```

**Query Parameters:**
- `month` (optional): Month for summary (default: current month)
- `year` (optional): Year for summary (default: current year)

**Response (200):**
```json
{
    "success": true,
    "message": "Tenant amenity usage summary retrieved successfully",
    "data": {
        "tenant_info": {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com"
        },
        "period": {
            "month": 1,
            "year": 2024,
            "month_name": "January 2024"
        },
        "subscriptions": [
            {
                "amenity_name": "Laundry Service",
                "billing_type": "daily",
                "unit_price": 50.00,
                "total_amount": 500.00,
                "usage_details": {
                    "type": "daily",
                    "total_days": 10,
                    "total_quantity": 15,
                    "records": [
                        {
                            "date": "Jan 15, 2024",
                            "quantity": 2,
                            "amount": 100.00
                        }
                    ]
                }
            }
        ],
        "total_amount": 500.00,
        "formatted_total_amount": "₹500.00"
    }
}
```

### 5. Search Amenities
Search for amenities across all types.

**GET Version (Testing):**
```
GET /api/v1/amenities/search?query=laundry&type=paid&category=laundry&billing_type=daily&limit=10
```

**POST Version (Integration):**
```
POST /api/v1/amenities/search
Content-Type: application/json

{
    "query": "laundry",
    "type": "paid",
    "category": "laundry",
    "billing_type": "daily",
    "limit": 10
}
```

**Parameters:**
- `query` (required): Search term (minimum 2 characters)
- `type` (optional): Filter by type (`basic`, `paid`, `subscriptions`)
- `category` (optional): Filter by category (for paid amenities)
- `billing_type` (optional): Filter by billing type (for paid amenities)
- `limit` (optional): Maximum number of results (default: 10, max: 50)

**Response (200):**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
            "type": "paid",
            "id": 1,
            "name": "Laundry Service",
            "description": "Professional laundry service",
            "category": "laundry",
            "category_display": "Laundry Services",
            "billing_type": "daily",
            "billing_type_display": "Daily",
            "price": 50.00,
            "formatted_price": "₹50.00/day",
            "is_active": true,
            "active_tenant_count": 5
        },
        {
            "type": "subscription",
            "id": 1,
            "tenant_name": "John Doe",
            "tenant_email": "john.doe@example.com",
            "amenity_name": "Laundry Service",
            "status": "active",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "text": "Active"
            },
            "start_date": "2024-01-01",
            "end_date": null,
            "effective_price": 50.00,
            "formatted_effective_price": "₹50.00/day",
            "is_current": true
        }
    ],
    "query": "laundry",
    "count": 2
}
```

## Data Models

### Basic Amenity Object
```json
{
    "id": 1,
    "name": "WiFi",
    "icon": "fas fa-wifi",
    "description": "High-speed internet access",
    "is_active": true,
    "sort_order": 1,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Paid Amenity Object
```json
{
    "id": 1,
    "name": "Laundry Service",
    "description": "Professional laundry service",
    "billing_type": "daily",
    "billing_type_display": "Daily",
    "price": 50.00,
    "formatted_price": "₹50.00/day",
    "category": "laundry",
    "category_display": "Laundry Services",
    "is_active": true,
    "status_badge": {
        "class": "bg-green-100 text-green-800",
        "text": "Active"
    },
    "availability_schedule": {
        "days": [1, 2, 3, 4, 5],
        "hours": ["09:00", "18:00"]
    },
    "max_usage_per_day": 2,
    "terms_conditions": "Service available Monday to Friday",
    "icon": "fas fa-tshirt",
    "icon_class": "fas fa-tshirt",
    "active_tenant_count": 5,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Tenant Amenity Subscription Object
```json
{
    "id": 1,
    "tenant_profile_id": 1,
    "paid_amenity_id": 1,
    "status": "active",
    "status_badge": {
        "class": "bg-green-100 text-green-800",
        "text": "Active"
    },
    "start_date": "2024-01-01",
    "end_date": null,
    "custom_price": null,
    "custom_schedule": null,
    "notes": null,
    "effective_price": 50.00,
    "formatted_effective_price": "₹50.00/day",
    "is_current": true,
    "is_expired": false,
    "duration_days": 15,
    "duration_text": "Ongoing since Jan 1, 2024",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Usage Record Object
```json
{
    "id": 1,
    "tenant_amenity_id": 1,
    "usage_date": "2024-01-15",
    "quantity": 2,
    "unit_price": 50.00,
    "formatted_unit_price": "₹50.00",
    "total_amount": 100.00,
    "formatted_total_amount": "₹100.00",
    "notes": "Laundry service used twice today",
    "usage_summary": "2x Laundry Service",
    "recorded_by": {
        "id": 1,
        "name": "Admin User"
    },
    "created_at": "2024-01-15T12:00:00.000000Z",
    "updated_at": "2024-01-15T12:00:00.000000Z"
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
        "billing_type": ["The selected billing type is invalid."],
        "price": ["The price must be at least 0."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Paid amenity not found"
}
```

### Conflict Errors (422)
```json
{
    "success": false,
    "message": "Tenant already has an active subscription to this amenity"
}
```

### Server Errors (500)
```json
{
    "success": false,
    "message": "Failed to retrieve paid amenities",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: List, Show, Search, Usage Records, Tenant Summary (read-only operations)
- **Authenticated Endpoints**: Create, Update, Delete, Subscribe, Suspend, Reactivate, Terminate, Record Usage (write operations)
- **Authentication Method**: Bearer token via Laravel Sanctum

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/amenities
http://localhost/api/v1/amenities/paid
http://localhost/api/v1/amenities/subscriptions
http://localhost/api/v1/amenities/usage
http://localhost/api/v1/amenities/search?query=laundry
```

### cURL Examples
```bash
# List basic amenities
curl -X GET http://localhost/api/v1/amenities

# List paid amenities
curl -X GET http://localhost/api/v1/amenities/paid

# Create paid amenity
curl -X POST http://localhost/api/v1/amenities/paid \
  -H "Content-Type: application/json" \
  -d '{"name":"Laundry Service","description":"Professional laundry service","billing_type":"daily","price":50.00,"category":"laundry"}'

# Subscribe tenant to amenity
curl -X POST http://localhost/api/v1/amenities/subscribe \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"tenant_profile_id":1,"paid_amenity_id":1,"start_date":"2024-01-01"}'

# Record usage
curl -X POST http://localhost/api/v1/amenities/usage \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"tenant_amenity_id":1,"usage_date":"2024-01-15","quantity":2,"notes":"Laundry service used twice today"}'

# Suspend subscription
curl -X POST http://localhost/api/v1/amenities/subscriptions/1/suspend \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"reason":"Tenant requested temporary suspension"}'

# Get tenant usage summary
curl -X GET http://localhost/api/v1/amenities/usage/tenant/1/summary?month=1&year=2024

# Search amenities
curl -X POST http://localhost/api/v1/amenities/search \
  -H "Content-Type: application/json" \
  -d '{"query":"laundry","type":"paid","category":"laundry","limit":10}'
```

## Business Rules

1. **Subscription Uniqueness**: Tenant cannot have multiple active subscriptions to the same amenity
2. **Usage Recording**: Can only record usage for active, current subscriptions
3. **Amenity Deletion**: Cannot delete paid amenities with active tenant subscriptions
4. **Price Validation**: Prices must be positive numbers
5. **Date Validation**: End dates must be after or equal to start dates
6. **Status Values**: Only specified status values are allowed
7. **Category Values**: Only specified category values are allowed
8. **Billing Type Values**: Only specified billing type values are allowed
9. **Search Minimum**: Search queries must be at least 2 characters
10. **Pagination**: Maximum 100 items per page for performance
11. **Usage Quantity**: Usage quantity must be at least 1
12. **Subscription Management**: Only active subscriptions can be suspended, only suspended subscriptions can be reactivated
13. **Billing Integration**: Usage records are used for generating amenity invoices
14. **Availability Schedule**: Amenities can have custom availability schedules
15. **Custom Pricing**: Subscriptions can override default amenity pricing

## Related Modules

- **Invoices API**: Generate amenity usage invoices
- **Payments API**: Process payments for amenity charges
- **Tenants API**: Manage tenant profiles for subscriptions
- **Authentication API**: User authentication for amenity operations
- **Notifications API**: Send amenity-related notifications

---

*Module: Amenities API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
