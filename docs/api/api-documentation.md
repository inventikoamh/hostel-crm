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
