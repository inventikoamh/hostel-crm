# Hostel CRM API Documentation

## Overview

This document provides comprehensive API documentation for the Hostel CRM system. The API follows RESTful principles and provides both GET and POST versions of endpoints for testing and integration purposes.

## Base URL

```
http://your-domain.com/api/v1
```

## Authentication

The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Response Format

All API responses follow this standard format:

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        // Validation errors (if applicable)
    }
}
```

## HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting

API requests are rate limited to 60 requests per minute per user.

---

## Authentication API

### Login

**GET Version (for testing):**
```
GET /api/v1/auth/login?email=user@example.com&password=password
```

**POST Version (for integration):**
```
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "phone": "+1234567890",
            "status": "active",
            "is_tenant": false,
            "is_super_admin": false,
            "last_login_at": "2024-01-15T10:30:00.000000Z",
            "created_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abcdef123456789",
        "token_type": "Bearer"
    }
}
```

### Logout

**GET Version:**
```
GET /api/v1/auth/logout
Authorization: Bearer {token}
```

**POST Version:**
```
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Logout successful"
}
```

### Get User Information

**GET Version:**
```
GET /api/v1/auth/me
Authorization: Bearer {token}
```

**POST Version:**
```
POST /api/v1/auth/me
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "User information retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "phone": "+1234567890",
        "status": "active",
        "avatar": null,
        "is_tenant": false,
        "is_super_admin": false,
        "last_login_at": "2024-01-15T10:30:00.000000Z",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "roles": [
            {
                "id": 1,
                "name": "Admin",
                "slug": "admin",
                "description": "Administrator role"
            }
        ],
        "permissions": [
            {
                "id": 1,
                "name": "Manage Hostels",
                "slug": "hostels.manage",
                "module": "hostels"
            }
        ],
        "tenant_profile": {
            "id": 1,
            "status": "active",
            "move_in_date": "2024-01-01",
            "monthly_rent": "500.00",
            "lease_start_date": "2024-01-01",
            "lease_end_date": "2024-12-31",
            "is_verified": true,
            "current_bed": {
                "id": 1,
                "bed_number": "A1",
                "room": {
                    "id": 1,
                    "room_number": "101",
                    "floor": 1,
                    "hostel": {
                        "id": 1,
                        "name": "Main Hostel"
                    }
                }
            }
        }
    }
}
```

### Refresh Token

**GET Version:**
```
GET /api/v1/auth/refresh
Authorization: Bearer {token}
```

**POST Version:**
```
POST /api/v1/auth/refresh
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Token refreshed successfully",
    "data": {
        "token": "2|xyz789abcdef",
        "token_type": "Bearer"
    }
}
```

## Error Examples

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Authentication Error (401)
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Server Error (500)
```json
{
    "success": false,
    "message": "Login failed",
    "error": "Database connection failed"
}
```

---

## Hostels API

### List Hostels

**GET Version (for testing):**
```
GET /api/v1/hostels?page=1&per_page=15&status=active&search=hostel name
```

**POST Version (for integration):**
```
POST /api/v1/hostels
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "status": "active",
    "search": "hostel name"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Hostels retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Downtown Hostel",
            "address": "123 Main Street",
            "city": "New York",
            "state": "NY",
            "status": "active",
            "manager_name": "John Smith",
            "occupancy_rate": 76.0
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create Hostel

**POST Version:**
```
POST /api/v1/hostels
Content-Type: application/json

{
    "name": "New Hostel",
    "address": "456 Oak Avenue",
    "city": "Los Angeles",
    "state": "CA",
    "country": "USA",
    "postal_code": "90210",
    "phone": "+1-555-0456",
    "email": "info@newhostel.com",
    "status": "active",
    "manager_name": "Jane Doe",
    "manager_phone": "+1-555-0457",
    "manager_email": "jane@newhostel.com"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Hostel created successfully",
    "data": {
        "id": 2,
        "name": "New Hostel",
        "address": "456 Oak Avenue",
        "city": "Los Angeles",
        "state": "CA",
        "status": "active",
        "created_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### Get Hostel Details

**GET Version:**
```
GET /api/v1/hostels/1
```

**Response:**
```json
{
    "success": true,
    "message": "Hostel retrieved successfully",
    "data": {
        "id": 1,
        "name": "Downtown Hostel",
        "description": "Modern hostel in city center",
        "address": "123 Main Street",
        "city": "New York",
        "state": "NY",
        "country": "USA",
        "postal_code": "10001",
        "full_address": "123 Main Street, New York, NY 10001, USA",
        "phone": "+1-555-0123",
        "email": "info@downtownhostel.com",
        "website": "https://downtownhostel.com",
        "amenities": ["WiFi", "Laundry", "Kitchen"],
        "status": "active",
        "manager_name": "John Smith",
        "manager_phone": "+1-555-0124",
        "manager_email": "john@downtownhostel.com",
        "total_rooms": 25,
        "total_beds": 50,
        "occupancy_rate": 76.0,
        "rent_per_bed": 45.00,
        "created_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### Update Hostel

**PUT Version (authenticated):**
```
PUT /api/v1/hostels/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Hostel Name",
    "status": "maintenance"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Hostel updated successfully",
    "data": {
        "id": 1,
        "name": "Updated Hostel Name",
        "status": "maintenance",
        "updated_at": "2024-01-15T15:30:00.000000Z"
    }
}
```

### Delete Hostel

**DELETE Version (authenticated):**
```
DELETE /api/v1/hostels/1
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Hostel deleted successfully"
}
```

### Search Hostels

**POST Version:**
```
POST /api/v1/hostels/search
Content-Type: application/json

{
    "query": "downtown",
    "limit": 10
}
```

**Response:**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
            "id": 1,
            "name": "Downtown Hostel",
            "address": "123 Main Street",
            "city": "New York",
            "status": "active"
        }
    ],
    "query": "downtown",
    "count": 1
}
```

### Get Hostel Statistics

**GET Version:**
```
GET /api/v1/hostels/1/stats
```

**Response:**
```json
{
    "success": true,
    "message": "Hostel statistics retrieved successfully",
    "data": {
        "total_rooms": 25,
        "total_beds": 50,
        "available_beds": 12,
        "occupied_beds": 38,
        "occupancy_rate": 76.0,
        "rent_per_bed": 45.00,
        "formatted_rent": "$45.00",
        "floors": [1, 2, 3],
        "status": "active"
    }
}
```

**For complete Hostels API documentation, see: [Hostels API Documentation](./hostels.md)**

---

## Tenants API

### List Tenants

**GET Version (for testing):**
```
GET /api/v1/tenants?page=1&per_page=15&status=active&search=john
```

**POST Version (for integration):**
```
POST /api/v1/tenants
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "status": "active",
    "search": "john"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Tenants retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123",
            "status": "active",
            "age": 34,
            "tenancy_duration_human": "1 year",
            "monthly_rent": 800.00,
            "is_verified": true
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create Tenant

**POST Version:**
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
    "status": "pending",
    "monthly_rent": 900.00,
    "billing_cycle": "monthly"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Tenant created successfully",
    "data": {
        "id": 2,
        "name": "Jane Smith",
        "email": "jane.smith@example.com",
        "phone": "+1-555-0456",
        "status": "pending",
        "age": 32,
        "monthly_rent": 900.00,
        "is_verified": false,
        "created_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### Get Tenant Details

**GET Version:**
```
GET /api/v1/tenants/1
```

**Response:**
```json
{
    "success": true,
    "message": "Tenant retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "phone": "+1-555-0123",
        "date_of_birth": "1990-05-15",
        "age": 34,
        "address": "123 Main Street, City, State",
        "occupation": "Software Engineer",
        "status": "active",
        "move_in_date": "2024-01-01",
        "tenancy_duration_human": "1 year",
        "monthly_rent": 800.00,
        "lease_end_date": "2024-12-31",
        "is_verified": true,
        "current_bed": {
            "bed_number": "A1",
            "room": {
                "room_number": "101",
                "hostel": {
                    "name": "Downtown Hostel"
                }
            }
        },
        "billing_info": {
            "billing_cycle": "monthly",
            "next_billing_date": "2024-02-01",
            "payment_status": "paid",
            "outstanding_amount": 0.00
        }
    }
}
```

### Update Tenant

**PUT Version (authenticated):**
```
PUT /api/v1/tenants/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Updated Doe",
    "monthly_rent": 850.00
}
```

**Response:**
```json
{
    "success": true,
    "message": "Tenant updated successfully",
    "data": {
        "id": 1,
        "name": "John Updated Doe",
        "monthly_rent": 850.00,
        "updated_at": "2024-01-15T15:30:00.000000Z"
    }
}
```

### Delete Tenant

**DELETE Version (authenticated):**
```
DELETE /api/v1/tenants/1
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Tenant deleted successfully"
}
```

### Search Tenants

**POST Version:**
```
POST /api/v1/tenants/search
Content-Type: application/json

{
    "query": "john",
    "limit": 10
}
```

**Response:**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "status": "active"
        }
    ],
    "query": "john",
    "count": 1
}
```

### Get Tenant Statistics

**GET Version:**
```
GET /api/v1/tenants/1/stats
```

**Response:**
```json
{
    "success": true,
    "message": "Tenant statistics retrieved successfully",
    "data": {
        "basic_info": {
            "age": 34,
            "tenancy_duration_human": "1 year",
            "is_lease_expired": false,
            "days_until_lease_expiry": 45
        },
        "billing_info": {
            "billing_cycle": "monthly",
            "next_billing_date": "2024-02-01",
            "payment_status": "paid",
            "outstanding_amount": 0.00
        },
        "payment_history": {
            "consecutive_on_time_payments": 12,
            "payment_history_score": 92.3
        },
        "current_accommodation": {
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
    }
}
```

### Assign Tenant to Bed

**POST Version (authenticated):**
```
POST /api/v1/tenants/1/assign-bed
Authorization: Bearer {token}
Content-Type: application/json

{
    "bed_id": 5,
    "move_in_date": "2024-02-01",
    "rent": 850.00
}
```

**Response:**
```json
{
    "success": true,
    "message": "Tenant assigned to bed successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "status": "active",
        "current_bed": {
            "bed_number": "B2",
            "room": {
                "room_number": "201",
                "hostel": {
                    "name": "Downtown Hostel"
                }
            }
        }
    }
}
```

**For complete Tenants API documentation, see: [Tenants API Documentation](./tenants.md)**

---

## Rooms & Beds API

### List Rooms

**GET Version (for testing):**
```
GET /api/v1/rooms-beds/rooms?page=1&per_page=15&hostel_id=1&status=available&room_type=double
```

**POST Version (for integration):**
```
POST /api/v1/rooms-beds/rooms
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "hostel_id": 1,
    "status": "available",
    "room_type": "double"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Rooms retrieved successfully",
    "data": [
        {
            "id": 1,
            "hostel_id": 1,
            "room_number": "101",
            "room_type": "double",
            "room_type_display": "Double Room",
            "floor": 1,
            "capacity": 2,
            "rent_per_bed": 500.00,
            "status": "available",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "icon": "fas fa-check-circle"
            },
            "area_sqft": 200.00,
            "has_attached_bathroom": true,
            "has_ac": true,
            "is_active": true
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create Room

**POST Version:**
```
POST /api/v1/rooms-beds/rooms
Content-Type: application/json

{
    "hostel_id": 1,
    "room_number": "102",
    "room_type": "triple",
    "floor": 1,
    "capacity": 3,
    "rent_per_bed": 450.00,
    "status": "available",
    "description": "Triple room with shared bathroom",
    "amenities": ["WiFi", "TV", "Air Conditioning"],
    "area_sqft": 250.00,
    "has_attached_bathroom": false,
    "has_balcony": true,
    "has_ac": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Room created successfully",
    "data": {
        "id": 2,
        "hostel_id": 1,
        "room_number": "102",
        "room_type": "triple",
        "room_type_display": "Triple Room",
        "floor": 1,
        "capacity": 3,
        "rent_per_bed": 450.00,
        "status": "available",
        "area_sqft": 250.00,
        "has_attached_bathroom": false,
        "has_balcony": true,
        "has_ac": true,
        "created_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### Get Room Details

**GET Version:**
```
GET /api/v1/rooms-beds/rooms/1
```

**Response:**
```json
{
    "success": true,
    "message": "Room retrieved successfully",
    "data": {
        "id": 1,
        "hostel_id": 1,
        "room_number": "101",
        "room_type": "double",
        "room_type_display": "Double Room",
        "floor": 1,
        "capacity": 2,
        "rent_per_bed": 500.00,
        "status": "available",
        "description": "Spacious double room with modern amenities",
        "area_sqft": 200.00,
        "has_attached_bathroom": true,
        "has_ac": true,
        "amenities": ["WiFi", "TV", "Air Conditioning"],
        "hostel": {
            "id": 1,
            "name": "Downtown Hostel",
            "address": "123 Main Street, City, State"
        },
        "occupancy_info": {
            "occupied_beds_count": 1,
            "available_beds_count": 1,
            "occupancy_rate": 50.0,
            "can_accommodate": true
        },
        "beds": [
            {
                "id": 1,
                "bed_number": "A1",
                "bed_type": "single",
                "status": "occupied",
                "monthly_rent": 500.00
            }
        ]
    }
}
```

### List Beds

**GET Version:**
```
GET /api/v1/rooms-beds/beds?room_id=1&status=available
```

**Response:**
```json
{
    "success": true,
    "message": "Beds retrieved successfully",
    "data": [
        {
            "id": 1,
            "room_id": 1,
            "bed_number": "A1",
            "bed_type": "single",
            "bed_type_display": "Single Bed",
            "status": "occupied",
            "status_badge": {
                "class": "bg-blue-100 text-blue-800",
                "icon": "fas fa-user"
            },
            "monthly_rent": 500.00,
            "current_rent": 500.00,
            "is_active": true
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 2
    }
}
```

### Create Bed

**POST Version:**
```
POST /api/v1/rooms-beds/beds
Content-Type: application/json

{
    "room_id": 1,
    "bed_number": "A2",
    "bed_type": "single",
    "status": "available",
    "monthly_rent": 500.00,
    "notes": "Near window, good ventilation"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Bed created successfully",
    "data": {
        "id": 2,
        "room_id": 1,
        "bed_number": "A2",
        "bed_type": "single",
        "bed_type_display": "Single Bed",
        "status": "available",
        "monthly_rent": 500.00,
        "current_rent": 500.00,
        "notes": "Near window, good ventilation",
        "created_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### Assign Tenant to Bed

**POST Version (authenticated):**
```
POST /api/v1/rooms-beds/assign-bed
Authorization: Bearer {token}
Content-Type: application/json

{
    "bed_id": 1,
    "tenant_id": 1,
    "assigned_from": "2024-02-01",
    "assigned_until": "2025-01-31",
    "monthly_rent": 500.00,
    "notes": "Long-term assignment"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Bed assigned successfully",
    "data": {
        "assignment": {
            "id": 1,
            "bed_id": 1,
            "tenant_id": 1,
            "assigned_from": "2024-02-01",
            "assigned_until": "2025-01-31",
            "status": "active",
            "monthly_rent": 500.00,
            "notes": "Long-term assignment"
        },
        "bed": {
            "id": 1,
            "bed_number": "A1",
            "status": "occupied",
            "room": {
                "room_number": "101",
                "hostel": {
                    "name": "Downtown Hostel"
                }
            }
        }
    }
}
```

### Release Bed Assignment

**POST Version (authenticated):**
```
POST /api/v1/rooms-beds/release-bed/1
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Bed released successfully",
    "data": {
        "assignment": {
            "id": 1,
            "status": "completed"
        },
        "bed": {
            "id": 1,
            "bed_number": "A1",
            "status": "available"
        }
    }
}
```

### Search Rooms and Beds

**POST Version:**
```
POST /api/v1/rooms-beds/search
Content-Type: application/json

{
    "query": "101",
    "type": "both",
    "hostel_id": 1,
    "limit": 10
}
```

**Response:**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
            "type": "room",
            "id": 1,
            "room_number": "101",
            "room_type": "double",
            "floor": 1,
            "status": "available",
            "hostel": {
                "id": 1,
                "name": "Downtown Hostel"
            }
        },
        {
            "type": "bed",
            "id": 1,
            "bed_number": "A1",
            "bed_type": "single",
            "status": "occupied",
            "room": {
                "id": 1,
                "room_number": "101",
                "floor": 1
            },
            "hostel": {
                "id": 1,
                "name": "Downtown Hostel"
            }
        }
    ],
    "query": "101",
    "count": 2
}
```

**For complete Rooms & Beds API documentation, see: [Rooms & Beds API Documentation](./rooms-beds.md)**

---

## Invoices API

### List Invoices

**GET Version (for testing):**
```
GET /api/v1/invoices?page=1&per_page=15&tenant_profile_id=1&status=sent&type=rent
```

**POST Version (for integration):**
```
POST /api/v1/invoices
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "tenant_profile_id": 1,
    "status": "sent",
    "type": "rent"
}
```

**Response:**
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
            "total_amount": 1000.00,
            "formatted_total_amount": "₹1,000.00",
            "paid_amount": 0.00,
            "balance_amount": 1000.00,
            "payment_status": "Unpaid",
            "is_overdue": false
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create Invoice

**POST Version:**
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
    "notes": "Monthly rent for January 2024"
}
```

**Response:**
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
        "total_amount": 1000.00,
        "formatted_total_amount": "₹1,000.00",
        "balance_amount": 1000.00,
        "created_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### Get Invoice Details

**GET Version:**
```
GET /api/v1/invoices/1
```

**Response:**
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
        "total_amount": 1000.00,
        "formatted_total_amount": "₹1,000.00",
        "balance_amount": 1000.00,
        "payment_status": "Unpaid",
        "is_overdue": false,
        "tenant": {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123"
        },
        "items": [
            {
                "id": 1,
                "item_type": "rent",
                "description": "Monthly rent for January 2024",
                "quantity": 1,
                "unit_price": 1000.00,
                "total_price": 1000.00
            }
        ],
        "payments": []
    }
}
```

### Add Payment to Invoice

**POST Version (authenticated):**
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
    "notes": "Partial payment received"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Payment added successfully",
    "data": {
        "payment": {
            "id": 1,
            "payment_number": "PAY-202401-0001",
            "amount": 500.00,
            "formatted_amount": "₹500.00",
            "payment_method": "bank_transfer",
            "status": "completed",
            "reference_number": "TXN123456789",
            "notes": "Partial payment received"
        },
        "invoice": {
            "id": 1,
            "paid_amount": 500.00,
            "balance_amount": 500.00,
            "payment_status": "Partially Paid"
        }
    }
}
```

### Generate Amenity Usage Invoice

**POST Version (authenticated):**
```
POST /api/v1/invoices/generate-amenity
Authorization: Bearer {token}
Content-Type: application/json

{
    "tenant_profile_id": 1,
    "period_start": "2024-01-01",
    "period_end": "2024-01-31",
    "status": "sent",
    "notes": "Amenity usage charges for January 2024"
}
```

**Response:**
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
        "total_amount": 150.00,
        "formatted_total_amount": "₹150.00",
        "items": [
            {
                "id": 2,
                "item_type": "amenity",
                "description": "Laundry Service (Usage: 5 days)",
                "quantity": 5,
                "unit_price": 30.00,
                "total_price": 150.00
            }
        ]
    }
}
```

### Add Item to Invoice

**POST Version (authenticated):**
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
    "related_type": "room"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Item added successfully",
    "data": {
        "item": {
            "id": 2,
            "item_type": "damage",
            "description": "Room damage repair",
            "quantity": 1,
            "unit_price": 200.00,
            "total_price": 200.00
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

### Search Invoices

**POST Version:**
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

**Response:**
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

**For complete Invoices API documentation, see: [Invoices API Documentation](./invoices.md)**

---

## Payments API

### List Payments

**GET Version (for testing):**
```
GET /api/v1/payments?page=1&per_page=15&invoice_id=1&status=completed&payment_method=bank_transfer
```

**POST Version (for integration):**
```
POST /api/v1/payments
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "invoice_id": 1,
    "status": "completed",
    "payment_method": "bank_transfer"
}
```

**Response:**
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
            "is_verified": true
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create Payment

**POST Version:**
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

**Response:**
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
        "is_verified": false,
        "created_at": "2024-01-15T17:00:00.000000Z"
    }
}
```

### Get Payment Details

**GET Version:**
```
GET /api/v1/payments/1
```

**Response:**
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
        "is_verified": true,
        "verified_at": "2024-01-15T16:30:00.000000Z",
        "invoice": {
            "id": 1,
            "invoice_number": "INV-202401-0001",
            "type": "rent",
            "total_amount": 1000.00,
            "balance_amount": 500.00
        },
        "tenant": {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123"
        }
    }
}
```

### Verify Payment

**POST Version (authenticated):**
```
POST /api/v1/payments/1/verify
Authorization: Bearer {token}
```

**Response:**
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

### Cancel Payment

**POST Version (authenticated):**
```
POST /api/v1/payments/1/cancel
Authorization: Bearer {token}
Content-Type: application/json

{
    "reason": "Payment was made in error"
}
```

**Response:**
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

### Get Tenant Payment Summary

**GET Version:**
```
GET /api/v1/payments/tenant/1/summary
```

**Response:**
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

### Get Invoice Payment Summary

**GET Version:**
```
GET /api/v1/payments/invoice/1/summary
```

**Response:**
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
            }
        ]
    }
}
```

### Search Payments

**POST Version:**
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

**Response:**
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

**For complete Payments API documentation, see: [Payments API Documentation](./payments.md)**

---

## Amenities API

### List Basic Amenities

**GET Version (for testing):**
```
GET /api/v1/amenities?page=1&per_page=15&is_active=true&search=WiFi
```

**POST Version (for integration):**
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

**Response:**
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
        "total": 42
    }
}
```

### List Paid Amenities

**GET Version (for testing):**
```
GET /api/v1/amenities/paid?page=1&per_page=15&category=laundry&billing_type=daily&is_active=true
```

**POST Version (for integration):**
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

**Response:**
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
            "active_tenant_count": 5
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create Paid Amenity

**POST Version:**
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

**Response:**
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

### Subscribe Tenant to Amenity

**POST Version:**
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

**Response:**
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

### Record Amenity Usage

**POST Version (authenticated):**
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

**Response:**
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

### Suspend Subscription

**POST Version (authenticated):**
```
POST /api/v1/amenities/subscriptions/1/suspend
Authorization: Bearer {token}
Content-Type: application/json

{
    "reason": "Tenant requested temporary suspension"
}
```

**Response:**
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

### Get Tenant Usage Summary

**GET Version:**
```
GET /api/v1/amenities/usage/tenant/1/summary?month=1&year=2024
```

**Response:**
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

### Search Amenities

**POST Version:**
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

**Response:**
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

**For complete Amenities API documentation, see: [Amenities API Documentation](./amenities.md)**

---

## Users API

### List Users

**GET Version (for testing):**
```
GET /api/v1/users?page=1&per_page=15&status=active&is_tenant=false&role=admin
```

**POST Version (for integration):**
```
POST /api/v1/users
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "status": "active",
    "is_tenant": false,
    "role": "admin"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Users retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123",
            "status": "active",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "text": "Active"
            },
            "avatar": "http://localhost/storage/avatars/avatar.jpg",
            "is_tenant": false,
            "is_super_admin": false,
            "last_login_at": "2024-01-15T10:30:00.000000Z",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create User

**POST Version:**
```
POST /api/v1/users
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "password123",
    "phone": "+1-555-0123",
    "status": "active",
    "is_tenant": false,
    "is_super_admin": false,
    "roles": [1, 2],
    "permissions": [1, 2, 3]
}
```

**Response:**
```json
{
    "success": true,
    "message": "User created successfully",
    "data": {
        "id": 2,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "phone": "+1-555-0123",
        "status": "active",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Active"
        },
        "avatar": null,
        "is_tenant": false,
        "is_super_admin": false,
        "last_login_at": null,
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### Get User Details

**GET Version:**
```
GET /api/v1/users/1
```

**Response:**
```json
{
    "success": true,
    "message": "User retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "phone": "+1-555-0123",
        "status": "active",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Active"
        },
        "avatar": "http://localhost/storage/avatars/avatar.jpg",
        "is_tenant": false,
        "is_super_admin": false,
        "last_login_at": "2024-01-15T10:30:00.000000Z",
        "roles": [
            {
                "id": 1,
                "name": "Admin",
                "slug": "admin",
                "description": "Administrator role",
                "is_system": false
            }
        ],
        "permissions": [
            {
                "id": 1,
                "name": "Create Users",
                "slug": "create-users",
                "description": "Create new users",
                "module": "users",
                "is_system": false
            }
        ],
        "tenant_profile": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "phone": "+1-555-0123",
            "status": "active"
        }
    }
}
```

### Assign Role to User

**POST Version (authenticated):**
```
POST /api/v1/users/1/assign-role
Authorization: Bearer {token}
Content-Type: application/json

{
    "role_id": 2
}
```

**Response:**
```json
{
    "success": true,
    "message": "Role assigned successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "roles": [
            {
                "id": 1,
                "name": "Admin",
                "slug": "admin"
            },
            {
                "id": 2,
                "name": "Manager",
                "slug": "manager"
            }
        ]
    }
}
```

### Suspend User

**POST Version (authenticated):**
```
POST /api/v1/users/1/suspend
Authorization: Bearer {token}
Content-Type: application/json

{
    "reason": "Violation of terms of service"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User suspended successfully",
    "data": {
        "id": 1,
        "status": "suspended",
        "status_badge": {
            "class": "bg-red-100 text-red-800",
            "text": "Suspended"
        }
    }
}
```

### List Roles

**GET Version:**
```
GET /api/v1/users/roles?page=1&per_page=15&is_system=false&search=admin
```

**Response:**
```json
{
    "success": true,
    "message": "Roles retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Admin",
            "slug": "admin",
            "description": "Administrator role with full access",
            "is_system": false,
            "permissions_count": 15,
            "users_count": 3,
            "permissions": [
                {
                    "id": 1,
                    "name": "Create Users",
                    "slug": "create-users",
                    "module": "users"
                }
            ],
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create Role

**POST Version:**
```
POST /api/v1/users/roles
Content-Type: application/json

{
    "name": "Manager",
    "slug": "manager",
    "description": "Manager role with limited access",
    "is_system": false,
    "permissions": [1, 2, 3]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Role created successfully",
    "data": {
        "id": 2,
        "name": "Manager",
        "slug": "manager",
        "description": "Manager role with limited access",
        "is_system": false,
        "permissions_count": 3,
        "users_count": 0,
        "permissions": [
            {
                "id": 1,
                "name": "Create Users",
                "slug": "create-users",
                "module": "users"
            }
        ],
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### List Permissions

**GET Version:**
```
GET /api/v1/users/permissions?page=1&per_page=15&module=users&is_system=false
```

**Response:**
```json
{
    "success": true,
    "message": "Permissions retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Create Users",
            "slug": "create-users",
            "description": "Create new users",
            "module": "users",
            "is_system": false,
            "roles_count": 2,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create Permission

**POST Version:**
```
POST /api/v1/users/permissions
Content-Type: application/json

{
    "name": "Delete Users",
    "slug": "delete-users",
    "description": "Delete users from the system",
    "module": "users",
    "is_system": false
}
```

**Response:**
```json
{
    "success": true,
    "message": "Permission created successfully",
    "data": {
        "id": 2,
        "name": "Delete Users",
        "slug": "delete-users",
        "description": "Delete users from the system",
        "module": "users",
        "is_system": false,
        "roles_count": 0,
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### Get User Statistics

**GET Version:**
```
GET /api/v1/users/stats
```

**Response:**
```json
{
    "success": true,
    "message": "User statistics retrieved successfully",
    "data": {
        "total_users": 150,
        "active_users": 120,
        "inactive_users": 20,
        "suspended_users": 10,
        "tenant_users": 100,
        "system_users": 50,
        "super_admins": 2,
        "users_with_roles": 140,
        "users_without_roles": 10,
        "recent_logins": 45
    }
}
```

### Get Available Modules

**GET Version:**
```
GET /api/v1/users/modules
```

**Response:**
```json
{
    "success": true,
    "message": "Modules retrieved successfully",
    "data": [
        "users",
        "hostels",
        "tenants",
        "invoices",
        "payments",
        "amenities",
        "rooms",
        "beds",
        "enquiries",
        "notifications"
    ]
}
```

### Search Users, Roles, and Permissions

**POST Version:**
```
POST /api/v1/users/search
Content-Type: application/json

{
    "query": "john",
    "type": "users",
    "status": "active",
    "limit": 10
}
```

**Response:**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
            "type": "user",
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-0123",
            "status": "active",
            "is_tenant": false,
            "is_super_admin": false,
            "roles_count": 2,
            "last_login_at": "2024-01-15T10:30:00.000000Z"
        },
        {
            "type": "role",
            "id": 1,
            "name": "Admin",
            "slug": "admin",
            "description": "Administrator role",
            "is_system": false,
            "permissions_count": 15,
            "users_count": 3
        },
        {
            "type": "permission",
            "id": 1,
            "name": "Create Users",
            "slug": "create-users",
            "description": "Create new users",
            "module": "users",
            "is_system": false,
            "roles_count": 2
        }
    ],
    "query": "john",
    "count": 3
}
```

**For complete Users API documentation, see: [Users API Documentation](./users.md)**

---

## Enquiries API

### List Enquiries

**GET Version (for testing):**
```
GET /api/v1/enquiries?page=1&per_page=15&status=new&priority=high&enquiry_type=room_booking&overdue=true
```

**POST Version (for integration):**
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

**Response:**
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
        "total": 42
    }
}
```

### Create Enquiry

**POST Version:**
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

**Response:**
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

### Get Enquiry Details

**GET Version:**
```
GET /api/v1/enquiries/1
```

**Response:**
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

### Assign Enquiry to User

**POST Version (authenticated):**
```
POST /api/v1/enquiries/1/assign
Authorization: Bearer {token}
Content-Type: application/json

{
    "assigned_to": 2,
    "admin_notes": "Assigned to sales team"
}
```

**Response:**
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

### Resolve Enquiry

**POST Version (authenticated):**
```
POST /api/v1/enquiries/1/resolve
Authorization: Bearer {token}
Content-Type: application/json

{
    "admin_notes": "Customer satisfied with response",
    "resolution_notes": "Provided room availability and pricing information"
}
```

**Response:**
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

### Convert Enquiry to Tenant

**POST Version (authenticated):**
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

**Response:**
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

### Get Enquiry Statistics

**GET Version:**
```
GET /api/v1/enquiries/stats
```

**Response:**
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

### Get Enquiry Sources

**GET Version:**
```
GET /api/v1/enquiries/sources
```

**Response:**
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

### Search Enquiries

**POST Version:**
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

**Response:**
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

**For complete Enquiries API documentation, see: [Enquiries API Documentation](./enquiries.md)**

---

## Notifications API

### List Notifications

**GET Version (for testing):**
```
GET /api/v1/notifications?page=1&per_page=15&status=pending&type=tenant_added&scheduled=true
```

**POST Version (for integration):**
```
POST /api/v1/notifications
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "status": "pending",
    "type": "tenant_added",
    "scheduled": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Notifications retrieved successfully",
    "data": [
        {
            "id": 1,
            "type": "tenant_added",
            "type_display": "Tenant Added",
            "title": "Welcome to Hostel CRM",
            "message": "Your tenant registration has been completed successfully.",
            "recipient_email": "tenant@example.com",
            "recipient_name": "John Doe",
            "status": "pending",
            "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'>Pending</span>",
            "retry_count": 0,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

### Create Notification

**POST Version:**
```
POST /api/v1/notifications
Content-Type: application/json

{
    "type": "tenant_added",
    "title": "Welcome to Hostel CRM",
    "message": "Your tenant registration has been completed successfully.",
    "recipient_email": "tenant@example.com",
    "recipient_name": "John Doe",
    "status": "pending",
    "data": {
        "hostel_name": "Sunrise Hostel",
        "tenant_id": 1
    },
    "scheduled_at": "2024-01-15T12:00:00.000000Z",
    "notifiable_type": "App\\Models\\TenantProfile",
    "notifiable_id": 1,
    "created_by": 1
}
```

**Response:**
```json
{
    "success": true,
    "message": "Notification created successfully",
    "data": {
        "id": 2,
        "type": "tenant_added",
        "type_display": "Tenant Added",
        "title": "Welcome to Hostel CRM",
        "message": "Your tenant registration has been completed successfully.",
        "recipient_email": "tenant@example.com",
        "recipient_name": "John Doe",
        "status": "pending",
        "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'>Pending</span>",
        "retry_count": 0,
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### Get Notification Details

**GET Version:**
```
GET /api/v1/notifications/1
```

**Response:**
```json
{
    "success": true,
    "message": "Notification retrieved successfully",
    "data": {
        "id": 1,
        "type": "tenant_added",
        "type_display": "Tenant Added",
        "title": "Welcome to Hostel CRM",
        "message": "Your tenant registration has been completed successfully.",
        "recipient_email": "tenant@example.com",
        "recipient_name": "John Doe",
        "status": "pending",
        "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'>Pending</span>",
        "retry_count": 0,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "data": {
            "hostel_name": "Sunrise Hostel",
            "tenant_id": 1
        },
        "scheduled_at": null,
        "sent_at": null,
        "error_message": null,
        "notifiable_type": "App\\Models\\TenantProfile",
        "notifiable_id": 1,
        "created_by": 1,
        "created_by_user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@hostel.com"
        },
        "notifiable_entity": {
            "type": "App\\Models\\TenantProfile",
            "id": 1,
            "data": {
                "id": 1,
                "name": "John Doe",
                "email": "tenant@example.com",
                "phone": "+1-555-0123"
            }
        }
    }
}
```

### Mark Notification as Sent

**POST Version (authenticated):**
```
POST /api/v1/notifications/1/mark-sent
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Notification marked as sent successfully",
    "data": {
        "id": 1,
        "status": "sent",
        "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'>Sent</span>",
        "sent_at": "2024-01-15T16:00:00.000000Z"
    }
}
```

### Mark Notification as Failed

**POST Version (authenticated):**
```
POST /api/v1/notifications/1/mark-failed
Authorization: Bearer {token}
Content-Type: application/json

{
    "error_message": "SMTP server unavailable"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Notification marked as failed successfully",
    "data": {
        "id": 1,
        "status": "failed",
        "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800'>Failed</span>",
        "error_message": "SMTP server unavailable",
        "retry_count": 1
    }
}
```

### Retry Failed Notification

**POST Version (authenticated):**
```
POST /api/v1/notifications/1/retry
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Notification queued for retry successfully",
    "data": {
        "id": 1,
        "status": "pending",
        "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'>Pending</span>",
        "error_message": null
    }
}
```

### Cancel Notification

**POST Version (authenticated):**
```
POST /api/v1/notifications/1/cancel
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Notification cancelled successfully",
    "data": {
        "id": 1,
        "status": "cancelled",
        "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800'>Cancelled</span>"
    }
}
```

### Send Notification Now

**POST Version (authenticated):**
```
POST /api/v1/notifications/1/send-now
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Notification sent successfully",
    "data": {
        "id": 1,
        "status": "sent",
        "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'>Sent</span>",
        "sent_at": "2024-01-15T16:00:00.000000Z"
    }
}
```

### Get Notification Statistics

**GET Version:**
```
GET /api/v1/notifications/stats
```

**Response:**
```json
{
    "success": true,
    "message": "Notification statistics retrieved successfully",
    "data": {
        "total_notifications": 150,
        "pending_notifications": 25,
        "sent_notifications": 100,
        "failed_notifications": 20,
        "cancelled_notifications": 5,
        "scheduled_notifications": 15,
        "retryable_notifications": 15,
        "today_notifications": 10,
        "this_week_notifications": 45,
        "this_month_notifications": 120,
        "success_rate": 66.67,
        "average_retry_count": 1.2,
        "tenant_added_notifications": 30,
        "tenant_updated_notifications": 15,
        "enquiry_added_notifications": 25,
        "invoice_created_notifications": 20,
        "invoice_sent_notifications": 18,
        "payment_received_notifications": 22,
        "payment_verified_notifications": 20,
        "amenity_usage_recorded_notifications": 10,
        "lease_expiring_notifications": 5,
        "overdue_payment_notifications": 8
    }
}
```

### Get Scheduled Notifications

**GET Version:**
```
GET /api/v1/notifications/scheduled?limit=50
```

**Response:**
```json
{
    "success": true,
    "message": "Scheduled notifications retrieved successfully",
    "data": [
        {
            "id": 1,
            "type": "lease_expiring",
            "type_display": "Lease Expiring",
            "title": "Lease Expiration Reminder",
            "message": "Your lease will expire in 7 days. Please contact us to renew.",
            "recipient_email": "tenant@example.com",
            "recipient_name": "John Doe",
            "status": "pending",
            "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'>Pending</span>",
            "retry_count": 0,
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "count": 1
}
```

### Search Notifications

**POST Version:**
```
POST /api/v1/notifications/search
Content-Type: application/json

{
    "query": "welcome",
    "status": "pending",
    "type": "tenant_added",
    "limit": 10
}
```

**Response:**
```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": [
        {
            "id": 1,
            "type": "tenant_added",
            "type_display": "Tenant Added",
            "title": "Welcome to Hostel CRM",
            "message": "Your tenant registration has been completed successfully.",
            "recipient_email": "tenant@example.com",
            "recipient_name": "John Doe",
            "status": "pending",
            "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800'>Pending</span>",
            "scheduled_at": null,
            "sent_at": null,
            "retry_count": 0,
            "created_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "query": "welcome",
    "count": 1
}
```

**For complete Notifications API documentation, see: [Notifications API Documentation](./notifications.md)**

---

## Dashboard API

### Get Dashboard Overview

**GET Version (for testing):**
```
GET /api/v1/dashboard/overview
```

**Response:**
```json
{
    "success": true,
    "message": "Dashboard overview retrieved successfully",
    "data": {
        "summary": {
            "total_hostels": 2,
            "total_rooms": 15,
            "total_beds": 45,
            "total_tenants": 25,
            "total_users": 30,
            "total_invoices": 120,
            "total_payments": 100,
            "total_enquiries": 50,
            "total_notifications": 200,
            "active_tenants": 20,
            "occupied_beds": 35,
            "pending_enquiries": 5
        },
        "recent_activity": [
            {
                "type": "tenant_registered",
                "message": "New tenant registered: John Doe",
                "timestamp": "2024-01-15T10:30:00.000000Z",
                "data": {"tenant_id": 1}
            }
        ],
        "quick_stats": {
            "today": {
                "new_enquiries": 3,
                "new_tenants": 1,
                "payments_received": 2500,
                "notifications_sent": 15
            },
            "this_month": {
                "new_enquiries": 25,
                "new_tenants": 8,
                "payments_received": 45000,
                "notifications_sent": 150
            },
            "occupancy_rate": 77.78,
            "revenue_growth": 12.5
        },
        "alerts": [
            {
                "type": "warning",
                "title": "Overdue Payments",
                "message": "3 payments are overdue",
                "action": "view_overdue_payments"
            }
        ],
        "charts": {
            "revenue_chart": [
                {"month": "Jan", "revenue": 15000},
                {"month": "Feb", "revenue": 18000}
            ],
            "occupancy_chart": [
                {"month": "Jan", "occupancy_rate": 75.5},
                {"month": "Feb", "occupancy_rate": 78.2}
            ]
        },
        "performance_metrics": {
            "enquiry_response_rate": 85.5,
            "payment_collection_rate": 92.3,
            "tenant_satisfaction_score": 4.2,
            "system_uptime": 99.9
        }
    }
}
```

### Get Financial Dashboard

**GET Version (for testing):**
```
GET /api/v1/dashboard/financial
```

**Response:**
```json
{
    "success": true,
    "message": "Financial dashboard data retrieved successfully",
    "data": {
        "revenue_summary": {
            "this_month": 25000,
            "last_month": 22000,
            "growth_rate": 13.64,
            "total_revenue": 150000,
            "average_monthly_revenue": 12500
        },
        "payment_analytics": {
            "total_payments": 100,
            "verified_payments": 95,
            "pending_payments": 3,
            "failed_payments": 2,
            "cancelled_payments": 0,
            "total_amount": 150000,
            "average_payment": 1578.95,
            "payment_methods": {
                "cash": 25,
                "bank_transfer": 40,
                "upi": 30,
                "card": 5
            }
        },
        "invoice_analytics": {
            "total_invoices": 120,
            "paid_invoices": 95,
            "pending_invoices": 20,
            "overdue_invoices": 5,
            "total_amount": 180000,
            "paid_amount": 150000,
            "outstanding_amount": 30000,
            "average_invoice_amount": 1500
        },
        "outstanding_amounts": {
            "total_outstanding": 30000,
            "overdue_amount": 5000,
            "pending_amount": 25000,
            "by_tenant": [
                {
                    "tenant_name": "John Doe",
                    "outstanding_amount": 1500
                }
            ]
        },
        "monthly_trends": [
            {
                "month": "Jan 2024",
                "revenue": 15000,
                "invoices": 25
            }
        ],
        "payment_methods": [
            {
                "method": "bank_transfer",
                "total_amount": 60000,
                "count": 40,
                "percentage": 40
            }
        ]
    }
}
```

### Get Occupancy Dashboard

**GET Version (for testing):**
```
GET /api/v1/dashboard/occupancy
```

**Response:**
```json
{
    "success": true,
    "message": "Occupancy dashboard data retrieved successfully",
    "data": {
        "occupancy_summary": {
            "total_beds": 45,
            "occupied_beds": 35,
            "available_beds": 10,
            "occupancy_rate": 77.78,
            "total_rooms": 15,
            "occupied_rooms": 12
        },
        "hostel_occupancy": [
            {
                "hostel_id": 1,
                "hostel_name": "Sunrise Hostel",
                "total_beds": 25,
                "occupied_beds": 20,
                "occupancy_rate": 80.0
            }
        ],
        "room_occupancy": [
            {
                "room_id": 1,
                "room_number": "101",
                "hostel_name": "Sunrise Hostel",
                "total_beds": 3,
                "occupied_beds": 3,
                "occupancy_rate": 100.0
            }
        ],
        "bed_assignments": {
            "total_assignments": 50,
            "active_assignments": 35,
            "completed_assignments": 10,
            "cancelled_assignments": 5,
            "average_duration": 180.5
        },
        "move_ins_outs": {
            "this_month": {
                "move_ins": 5,
                "move_outs": 2
            },
            "last_month": {
                "move_ins": 3,
                "move_outs": 1
            }
        },
        "lease_expirations": {
            "expiring_in_7_days": 2,
            "expiring_in_30_days": 5,
            "expired_leases": 1
        }
    }
}
```

### Get Tenant Analytics Dashboard

**GET Version (for testing):**
```
GET /api/v1/dashboard/tenants
```

**Response:**
```json
{
    "success": true,
    "message": "Tenant analytics dashboard data retrieved successfully",
    "data": {
        "tenant_summary": {
            "total_tenants": 25,
            "active_tenants": 20,
            "pending_tenants": 3,
            "inactive_tenants": 2,
            "verified_tenants": 22,
            "unverified_tenants": 3
        },
        "tenant_status": {
            "active": 20,
            "pending": 3,
            "inactive": 2
        },
        "tenant_demographics": {
            "age_groups": {
                "18-25": 15,
                "26-35": 8,
                "36-45": 2
            },
            "occupations": {
                "Software Engineer": 8,
                "Student": 12,
                "Business Analyst": 3,
                "Designer": 2
            },
            "companies": {
                "Tech Corp": 5,
                "University": 12,
                "Design Studio": 2,
                "Consulting Firm": 3
            }
        },
        "tenant_satisfaction": {
            "overall_satisfaction": 4.2,
            "response_rate": 75.5,
            "satisfaction_trend": "increasing",
            "common_concerns": [
                "WiFi connectivity",
                "Room maintenance",
                "Noise levels"
            ]
        },
        "tenant_retention": {
            "retention_rate": 85.5,
            "average_tenancy_duration": 180.5,
            "churn_rate": 14.5
        },
        "tenant_communication": {
            "total_notifications": 150,
            "sent_notifications": 140,
            "failed_notifications": 10,
            "average_response_time": 2.5
        }
    }
}
```

### Get Amenity Usage Dashboard

**GET Version (for testing):**
```
GET /api/v1/dashboard/amenities
```

**Response:**
```json
{
    "success": true,
    "message": "Amenity usage dashboard data retrieved successfully",
    "data": {
        "amenity_summary": {
            "total_amenities": 10,
            "total_paid_amenities": 5,
            "active_subscriptions": 25,
            "total_usage_records": 500,
            "amenity_revenue": 15000
        },
        "usage_analytics": {
            "daily_usage": 25,
            "monthly_usage": 500,
            "average_usage_per_tenant": 20,
            "peak_usage_hours": ["18:00", "19:00", "20:00"]
        },
        "revenue_breakdown": [
            {
                "amenity_name": "Gym Access",
                "revenue": 5000,
                "subscribers": 15
            },
            {
                "amenity_name": "Laundry Service",
                "revenue": 3000,
                "subscribers": 20
            }
        ],
        "popular_amenities": [
            {
                "amenity_name": "Laundry Service",
                "subscribers": 20,
                "revenue": 3000
            },
            {
                "amenity_name": "Gym Access",
                "subscribers": 15,
                "revenue": 5000
            }
        ],
        "subscription_trends": [
            {
                "month": "Jan 2024",
                "new_subscriptions": 5,
                "cancelled_subscriptions": 2
            }
        ],
        "usage_patterns": {
            "hourly_usage": [
                {"hour": "06:00", "usage": 5},
                {"hour": "18:00", "usage": 25}
            ],
            "daily_usage": [
                {"day": "Monday", "usage": 120},
                {"day": "Tuesday", "usage": 135}
            ],
            "weekly_usage": [
                {"week": "Week 1", "usage": 500},
                {"week": "Week 2", "usage": 520}
            ]
        }
    }
}
```

### Get Enquiry Analytics Dashboard

**GET Version (for testing):**
```
GET /api/v1/dashboard/enquiries
```

**Response:**
```json
{
    "success": true,
    "message": "Enquiry analytics dashboard data retrieved successfully",
    "data": {
        "enquiry_summary": {
            "total_enquiries": 50,
            "new_enquiries": 5,
            "in_progress_enquiries": 8,
            "resolved_enquiries": 30,
            "closed_enquiries": 7,
            "overdue_enquiries": 2
        },
        "conversion_analytics": {
            "conversion_rate": 60.0,
            "total_conversions": 30,
            "average_conversion_time": 24.5
        },
        "source_analytics": {
            "website": 25,
            "phone": 15,
            "walk-in": 8,
            "referral": 2
        },
        "response_analytics": {
            "response_rate": 85.5,
            "average_response_time": 12.5,
            "response_time_trend": "decreasing"
        },
        "priority_breakdown": {
            "high": 10,
            "medium": 25,
            "low": 15
        },
        "trend_analysis": [
            {
                "month": "Jan 2024",
                "total_enquiries": 15,
                "resolved_enquiries": 12
            }
        ]
    }
}
```

### Get Notification Analytics Dashboard

**GET Version (for testing):**
```
GET /api/v1/dashboard/notifications
```

**Response:**
```json
{
    "success": true,
    "message": "Notification analytics dashboard data retrieved successfully",
    "data": {
        "notification_summary": {
            "total_notifications": 200,
            "pending_notifications": 10,
            "sent_notifications": 180,
            "failed_notifications": 8,
            "cancelled_notifications": 2,
            "scheduled_notifications": 5
        },
        "delivery_analytics": {
            "delivery_rate": 90.0,
            "average_delivery_time": 5.5,
            "retry_rate": 15.0
        },
        "type_breakdown": {
            "tenant_added": 25,
            "tenant_updated": 15,
            "enquiry_added": 30,
            "invoice_created": 40,
            "payment_received": 35,
            "lease_expiring": 10
        },
        "success_rates": {
            "tenant_added": 95.0,
            "tenant_updated": 90.0,
            "enquiry_added": 85.0,
            "invoice_created": 92.0,
            "payment_received": 88.0,
            "lease_expiring": 100.0
        },
        "retry_analytics": {
            "total_retries": 45,
            "average_retries": 1.2,
            "max_retries_reached": 5,
            "retryable_notifications": 3
        },
        "trend_analysis": [
            {
                "month": "Jan 2024",
                "total_notifications": 50,
                "sent_notifications": 45
            }
        ]
    }
}
```

### Get System Health Dashboard

**GET Version (for testing):**
```
GET /api/v1/dashboard/system-health
```

**Response:**
```json
{
    "success": true,
    "message": "System health dashboard data retrieved successfully",
    "data": {
        "system_summary": {
            "total_users": 30,
            "active_users": 25,
            "system_users": 5,
            "tenant_users": 25,
            "super_admins": 1,
            "last_login": "2024-01-15T10:30:00.000000Z"
        },
        "user_activity": {
            "today_logins": 15,
            "this_week_logins": 45,
            "active_sessions": 8,
            "user_growth": 12.5
        },
        "database_stats": {
            "total_records": 1500,
            "database_size": "25.5 MB",
            "table_counts": {
                "users": 30,
                "tenant_profiles": 25,
                "invoices": 120,
                "payments": 100
            },
            "index_usage": {
                "primary_keys": 100,
                "foreign_keys": 200,
                "custom_indexes": 50
            }
        },
        "performance_metrics": {
            "average_response_time": 150.5,
            "memory_usage": 75.2,
            "cpu_usage": 45.8,
            "disk_usage": 60.3
        },
        "error_logs": {
            "total_errors": 25,
            "errors_today": 2,
            "error_types": {
                "database": 10,
                "validation": 8,
                "authentication": 5,
                "other": 2
            },
            "critical_errors": 1
        },
        "maintenance_alerts": {
            "scheduled_maintenance": [
                {
                    "date": "2024-01-20",
                    "description": "Database optimization",
                    "duration": "2 hours"
                }
            ],
            "system_updates": [
                {
                    "version": "v1.2.0",
                    "description": "Security patches",
                    "status": "pending"
                }
            ],
            "backup_status": {
                "last_backup": "2024-01-15T02:00:00.000000Z",
                "status": "success",
                "size": "25.5 MB"
            },
            "security_alerts": [
                {
                    "type": "failed_login",
                    "count": 3,
                    "severity": "medium"
                }
            ]
        }
    }
}
```

### Get Dashboard Widgets

**GET Version (for testing):**
```
GET /api/v1/dashboard/widgets
```

**POST Version (for integration):**
```
POST /api/v1/dashboard/widgets
Content-Type: application/json

{
    "widgets": ["revenue_chart", "occupancy_gauge", "recent_activity", "quick_stats", "alerts"]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Dashboard widgets retrieved successfully",
    "data": {
        "available_widgets": [
            {
                "revenue_chart": {
                    "name": "Revenue Chart",
                    "type": "chart"
                },
                "occupancy_gauge": {
                    "name": "Occupancy Gauge",
                    "type": "gauge"
                },
                "recent_activity": {
                    "name": "Recent Activity",
                    "type": "list"
                },
                "quick_stats": {
                    "name": "Quick Stats",
                    "type": "stats"
                },
                "alerts": {
                    "name": "Alerts",
                    "type": "alerts"
                }
            }
        ],
        "user_widgets": [
            "revenue_chart",
            "occupancy_gauge",
            "recent_activity",
            "quick_stats",
            "alerts"
        ],
        "widget_data": {
            "revenue_chart": [
                {"month": "Jan", "revenue": 15000},
                {"month": "Feb", "revenue": 18000}
            ],
            "occupancy_gauge": 77.78,
            "recent_activity": [
                {
                    "type": "tenant_registered",
                    "message": "New tenant registered: John Doe",
                    "timestamp": "2024-01-15T10:30:00.000000Z"
                }
            ],
            "quick_stats": {
                "today": {
                    "new_enquiries": 3,
                    "new_tenants": 1,
                    "payments_received": 2500,
                    "notifications_sent": 15
                }
            },
            "alerts": [
                {
                    "type": "warning",
                    "title": "Overdue Payments",
                    "message": "3 payments are overdue"
                }
            ]
        }
    }
}
```

**For complete Dashboard API documentation, see: [Dashboard API Documentation](./dashboard.md)**

---

## Testing Instructions

### Using Browser (GET requests)
1. Open browser and navigate to: `http://your-domain.com/api/v1/auth/login?email=admin@hostel.com&password=password`
2. Copy the token from response
3. Use token in subsequent requests: `http://your-domain.com/api/v1/auth/me` (with Authorization header)

### Using Postman/API Client (POST requests)
1. Create POST request to `/api/v1/auth/login`
2. Set Content-Type to `application/json`
3. Add JSON body with email and password
4. Copy token from response
5. Add Authorization header: `Bearer {token}` to subsequent requests

### Using cURL
```bash
# Login
curl -X POST http://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@hostel.com","password":"password"}'

# Get user info
curl -X GET http://your-domain.com/api/v1/auth/me \
  -H "Authorization: Bearer {token}"
```

---

*Last updated: January 15, 2024*
*API Version: 1.0.0*
