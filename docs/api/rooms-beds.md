# Rooms & Beds API Module

## Overview
The Rooms & Beds API provides comprehensive endpoints for managing accommodation facilities within the Hostel CRM system. This module handles room management, bed assignments, occupancy tracking, and tenant accommodation management.

## Base Endpoints
All rooms and beds endpoints are prefixed with `/api/v1/rooms-beds/`

## Endpoints

### ROOMS API

#### 1. List Rooms
Retrieve a paginated list of all rooms with optional filtering and sorting.

**GET Version (Testing):**
```
GET /api/v1/rooms-beds/rooms?page=1&per_page=15&hostel_id=1&status=available&room_type=double&floor=1&search=101
```

**POST Version (Integration):**
```
POST /api/v1/rooms-beds/rooms
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "hostel_id": 1,
    "status": "available",
    "room_type": "double",
    "floor": 1,
    "search": "101"
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `hostel_id` (optional): Filter by hostel ID
- `status` (optional): Filter by status (`available`, `occupied`, `maintenance`, `reserved`)
- `room_type` (optional): Filter by room type (`single`, `double`, `triple`, `dormitory`, `suite`, `studio`)
- `floor` (optional): Filter by floor number
- `is_active` (optional): Filter by active status (boolean)
- `search` (optional): Search in room number, description, or hostel name
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
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
            "description": "Spacious double room with modern amenities",
            "area_sqft": 200.00,
            "has_attached_bathroom": true,
            "has_balcony": false,
            "has_ac": true,
            "is_active": true,
            "coordinates": null,
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

#### 2. Create Room
Create a new room in a hostel.

**GET Version (Testing):**
```
GET /api/v1/rooms-beds/rooms/create
```

**POST Version (Integration):**
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
    "has_ac": true,
    "is_active": true
}
```

**Required Fields:**
- `hostel_id`: Hostel ID (must exist)
- `room_number`: Room number (unique within hostel)
- `room_type`: Room type (`single`, `double`, `triple`, `dormitory`, `suite`, `studio`)
- `floor`: Floor number
- `capacity`: Total number of beds
- `rent_per_bed`: Rent per bed amount
- `status`: Status (`available`, `occupied`, `maintenance`, `reserved`)

**Optional Fields:**
- `description`: Room description
- `amenities`: Array of room amenities
- `area_sqft`: Room area in square feet
- `has_attached_bathroom`: Has attached bathroom (boolean)
- `has_balcony`: Has balcony (boolean)
- `has_ac`: Has air conditioning (boolean)
- `is_active`: Is room active (boolean)
- `coordinates`: Room coordinates for map positioning

**Response (201):**
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
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "icon": "fas fa-check-circle"
        },
        "description": "Triple room with shared bathroom",
        "area_sqft": 250.00,
        "has_attached_bathroom": false,
        "has_balcony": true,
        "has_ac": true,
        "is_active": true,
        "coordinates": null,
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

#### 3. Get Room Details
Retrieve detailed information about a specific room.

**GET Version (Testing):**
```
GET /api/v1/rooms-beds/rooms/1
```

**POST Version (Integration):**
```
POST /api/v1/rooms-beds/rooms/1
Content-Type: application/json
```

**Response (200):**
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
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "icon": "fas fa-check-circle"
        },
        "description": "Spacious double room with modern amenities",
        "area_sqft": 200.00,
        "has_attached_bathroom": true,
        "has_balcony": false,
        "has_ac": true,
        "is_active": true,
        "coordinates": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
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
                "notes": null,
                "is_active": true,
                "coordinates": null,
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-15T10:30:00.000000Z"
            }
        ]
    }
}
```

#### 4. Update Room
Update an existing room's information.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/rooms-beds/rooms/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "room_number": "101A",
    "rent_per_bed": 550.00,
    "description": "Updated room description"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Room updated successfully",
    "data": {
        "id": 1,
        "room_number": "101A",
        "rent_per_bed": 550.00,
        "description": "Updated room description",
        "updated_at": "2024-01-15T15:30:00.000000Z"
    }
}
```

#### 5. Delete Room
Remove a room from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/rooms-beds/rooms/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Room deleted successfully"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot delete room with active bed assignments. Please release all bed assignments first."
}
```

#### 6. Get Room Statistics
Retrieve statistical information about a room.

**GET Version (Testing):**
```
GET /api/v1/rooms-beds/rooms/1/stats
```

**Response (200):**
```json
{
    "success": true,
    "message": "Room statistics retrieved successfully",
    "data": {
        "basic_info": {
            "room_number": "101",
            "room_type": "double",
            "room_type_display": "Double Room",
            "floor": 1,
            "capacity": 2,
            "area_sqft": 200.00,
            "rent_per_bed": 500.00,
            "status": "available",
            "status_badge": {
                "class": "bg-green-100 text-green-800",
                "icon": "fas fa-check-circle"
            }
        },
        "occupancy_info": {
            "occupied_beds_count": 1,
            "available_beds_count": 1,
            "occupancy_rate": 50.0,
            "can_accommodate": true
        },
        "amenities": {
            "room_amenities": ["WiFi", "TV", "Air Conditioning"],
            "has_attached_bathroom": true,
            "has_balcony": false,
            "has_ac": true
        },
        "hostel_info": {
            "hostel_id": 1,
            "hostel_name": "Downtown Hostel",
            "hostel_address": "123 Main Street, City, State"
        },
        "beds_summary": {
            "total_beds": 2,
            "available_beds": 1,
            "occupied_beds": 1,
            "maintenance_beds": 0,
            "reserved_beds": 0
        }
    }
}
```

### BEDS API

#### 7. List Beds
Retrieve a paginated list of all beds with optional filtering and sorting.

**GET Version (Testing):**
```
GET /api/v1/rooms-beds/beds?page=1&per_page=15&room_id=1&status=available&bed_type=single&hostel_id=1
```

**POST Version (Integration):**
```
POST /api/v1/rooms-beds/beds
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "room_id": 1,
    "status": "available",
    "bed_type": "single",
    "hostel_id": 1
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `room_id` (optional): Filter by room ID
- `hostel_id` (optional): Filter by hostel ID
- `status` (optional): Filter by status (`available`, `occupied`, `maintenance`, `reserved`)
- `bed_type` (optional): Filter by bed type (`single`, `double`, `bunk_top`, `bunk_bottom`)
- `is_active` (optional): Filter by active status (boolean)
- `search` (optional): Search in bed number, notes, room number, or hostel name
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
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
            "notes": null,
            "is_active": true,
            "coordinates": null,
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

#### 8. Create Bed
Create a new bed in a room.

**GET Version (Testing):**
```
GET /api/v1/rooms-beds/beds/create
```

**POST Version (Integration):**
```
POST /api/v1/rooms-beds/beds
Content-Type: application/json

{
    "room_id": 1,
    "bed_number": "A2",
    "bed_type": "single",
    "status": "available",
    "monthly_rent": 500.00,
    "notes": "Near window, good ventilation",
    "is_active": true
}
```

**Required Fields:**
- `room_id`: Room ID (must exist)
- `bed_number`: Bed number (unique within room)
- `bed_type`: Bed type (`single`, `double`, `bunk_top`, `bunk_bottom`)
- `status`: Status (`available`, `occupied`, `maintenance`, `reserved`)

**Optional Fields:**
- `monthly_rent`: Individual bed rent amount
- `notes`: Bed notes
- `is_active`: Is bed active (boolean)
- `coordinates`: Bed coordinates for room layout

**Response (201):**
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
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "icon": "fas fa-bed"
        },
        "monthly_rent": 500.00,
        "current_rent": 500.00,
        "notes": "Near window, good ventilation",
        "is_active": true,
        "coordinates": null,
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

#### 9. Get Bed Details
Retrieve detailed information about a specific bed.

**GET Version (Testing):**
```
GET /api/v1/rooms-beds/beds/1
```

**Response (200):**
```json
{
    "success": true,
    "message": "Bed retrieved successfully",
    "data": {
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
        "notes": null,
        "is_active": true,
        "coordinates": null,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
        "room": {
            "id": 1,
            "room_number": "101",
            "room_type": "double",
            "floor": 1,
            "hostel": {
                "id": 1,
                "name": "Downtown Hostel"
            }
        },
        "current_assignment": {
            "id": 1,
            "tenant": {
                "id": 1,
                "name": "John Doe",
                "email": "john.doe@example.com"
            },
            "assigned_from": "2024-01-01",
            "assigned_until": "2024-12-31",
            "status": "active",
            "monthly_rent": 500.00
        },
        "assignment_history": [
            {
                "id": 1,
                "tenant": {
                    "id": 1,
                    "name": "John Doe"
                },
                "assigned_from": "2024-01-01",
                "assigned_until": "2024-12-31",
                "status": "active",
                "monthly_rent": 500.00
            }
        ],
        "availability": {
            "has_active_assignment": true,
            "has_reserved_assignment": false,
            "is_available": false
        }
    }
}
```

#### 10. Update Bed
Update an existing bed's information.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/rooms-beds/beds/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "bed_number": "A1A",
    "monthly_rent": 550.00,
    "notes": "Updated bed notes"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Bed updated successfully",
    "data": {
        "id": 1,
        "bed_number": "A1A",
        "monthly_rent": 550.00,
        "notes": "Updated bed notes",
        "updated_at": "2024-01-15T15:30:00.000000Z"
    }
}
```

#### 11. Delete Bed
Remove a bed from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/rooms-beds/beds/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Bed deleted successfully"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot delete bed with active assignments. Please release bed assignments first."
}
```

#### 12. Get Bed Statistics
Retrieve statistical information about a bed.

**GET Version (Testing):**
```
GET /api/v1/rooms-beds/beds/1/stats
```

**Response (200):**
```json
{
    "success": true,
    "message": "Bed statistics retrieved successfully",
    "data": {
        "basic_info": {
            "bed_number": "A1",
            "bed_type": "single",
            "bed_type_display": "Single Bed",
            "status": "occupied",
            "status_badge": {
                "class": "bg-blue-100 text-blue-800",
                "icon": "fas fa-user"
            },
            "monthly_rent": 500.00,
            "current_rent": 500.00
        },
        "room_info": {
            "room_id": 1,
            "room_number": "101",
            "room_type": "double",
            "floor": 1,
            "hostel": {
                "id": 1,
                "name": "Downtown Hostel",
                "address": "123 Main Street, City, State"
            }
        },
        "current_assignment": {
            "id": 1,
            "tenant": {
                "id": 1,
                "name": "John Doe",
                "email": "john.doe@example.com"
            },
            "assigned_from": "2024-01-01",
            "assigned_until": "2024-12-31",
            "status": "active",
            "monthly_rent": 500.00
        },
        "assignment_history": {
            "total_assignments": 1,
            "active_assignments": 1,
            "completed_assignments": 0,
            "cancelled_assignments": 0
        },
        "availability": {
            "has_active_assignment": true,
            "has_reserved_assignment": false,
            "is_available": false
        }
    }
}
```

### BED ASSIGNMENTS API

#### 13. Assign Tenant to Bed
Assign a tenant to a specific bed.

**POST Version (Authenticated):**
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

**Response (200):**
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
            "notes": "Long-term assignment",
            "created_at": "2024-01-15T16:00:00.000000Z",
            "updated_at": "2024-01-15T16:00:00.000000Z"
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

#### 14. Release Bed Assignment
Release a tenant from their bed assignment.

**POST Version (Authenticated):**
```
POST /api/v1/rooms-beds/release-bed/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Bed released successfully",
    "data": {
        "assignment": {
            "id": 1,
            "status": "completed",
            "updated_at": "2024-01-15T17:00:00.000000Z"
        },
        "bed": {
            "id": 1,
            "bed_number": "A1",
            "status": "available",
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

### SEARCH API

#### 15. Search Rooms and Beds
Search for rooms and beds by various criteria.

**GET Version (Testing):**
```
GET /api/v1/rooms-beds/search?query=101&type=both&hostel_id=1&limit=10
```

**POST Version (Integration):**
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

**Parameters:**
- `query` (required): Search term (minimum 2 characters)
- `type` (optional): Search type (`rooms`, `beds`, `both`) - default: `both`
- `hostel_id` (optional): Filter by hostel ID
- `limit` (optional): Maximum number of results (default: 10, max: 50)

**Response (200):**
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

## Data Models

### Room Object
```json
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
    "description": "Spacious double room with modern amenities",
    "area_sqft": 200.00,
    "has_attached_bathroom": true,
    "has_balcony": false,
    "has_ac": true,
    "is_active": true,
    "coordinates": null,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

### Bed Object
```json
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
    "notes": null,
    "is_active": true,
    "coordinates": null,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
}
```

### Bed Assignment Object
```json
{
    "id": 1,
    "bed_id": 1,
    "tenant_id": 1,
    "assigned_from": "2024-01-01",
    "assigned_until": "2024-12-31",
    "status": "active",
    "monthly_rent": 500.00,
    "notes": "Long-term assignment",
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
        "room_number": ["The room number field is required."],
        "room_type": ["The selected room type is invalid."],
        "capacity": ["The capacity must be at least 1."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Room not found"
}
```

### Conflict Errors (422)
```json
{
    "success": false,
    "message": "Room number already exists in this hostel"
}
```

### Server Errors (500)
```json
{
    "success": false,
    "message": "Failed to retrieve rooms",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: List, Show, Stats, Search (read-only operations)
- **Authenticated Endpoints**: Create, Update, Delete, Assign Bed, Release Bed (write operations)
- **Authentication Method**: Bearer token via Laravel Sanctum

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/rooms-beds/rooms
http://localhost/api/v1/rooms-beds/rooms/1
http://localhost/api/v1/rooms-beds/rooms/1/stats
http://localhost/api/v1/rooms-beds/beds
http://localhost/api/v1/rooms-beds/beds/1
http://localhost/api/v1/rooms-beds/beds/1/stats
http://localhost/api/v1/rooms-beds/search?query=101
```

### cURL Examples
```bash
# List rooms
curl -X GET http://localhost/api/v1/rooms-beds/rooms

# Get specific room
curl -X GET http://localhost/api/v1/rooms-beds/rooms/1

# Create room
curl -X POST http://localhost/api/v1/rooms-beds/rooms \
  -H "Content-Type: application/json" \
  -d '{"hostel_id":1,"room_number":"102","room_type":"double","floor":1,"capacity":2,"rent_per_bed":500.00,"status":"available"}'

# Update room (authenticated)
curl -X PUT http://localhost/api/v1/rooms-beds/rooms/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"rent_per_bed":550.00}'

# Delete room (authenticated)
curl -X DELETE http://localhost/api/v1/rooms-beds/rooms/1 \
  -H "Authorization: Bearer {token}"

# List beds
curl -X GET http://localhost/api/v1/rooms-beds/beds

# Create bed
curl -X POST http://localhost/api/v1/rooms-beds/beds \
  -H "Content-Type: application/json" \
  -d '{"room_id":1,"bed_number":"A2","bed_type":"single","status":"available"}'

# Assign bed (authenticated)
curl -X POST http://localhost/api/v1/rooms-beds/assign-bed \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"bed_id":1,"tenant_id":1,"assigned_from":"2024-02-01","monthly_rent":500.00}'

# Release bed (authenticated)
curl -X POST http://localhost/api/v1/rooms-beds/release-bed/1 \
  -H "Authorization: Bearer {token}"

# Search rooms and beds
curl -X POST http://localhost/api/v1/rooms-beds/search \
  -H "Content-Type: application/json" \
  -d '{"query":"101","type":"both","limit":10}'
```

## Business Rules

1. **Room Deletion**: Cannot delete rooms with active bed assignments
2. **Bed Deletion**: Cannot delete beds with active assignments
3. **Room Number Uniqueness**: Room numbers must be unique within each hostel
4. **Bed Number Uniqueness**: Bed numbers must be unique within each room
5. **Bed Assignment**: Only one active assignment per bed at a time
6. **Status Values**: Only specified status values are allowed
7. **Room Types**: Only specified room types are allowed
8. **Bed Types**: Only specified bed types are allowed
9. **Date Validation**: Assignment end date must be after start date
10. **Search Minimum**: Search queries must be at least 2 characters
11. **Pagination**: Maximum 100 items per page for performance
12. **Overlapping Assignments**: Cannot assign bed if there are overlapping active assignments

## Related Modules

- **Hostels API**: Manage hostel properties
- **Tenants API**: Manage tenant profiles and assignments
- **Invoices API**: Generate invoices for room/bed billing
- **Payments API**: Process payments from tenants
- **Amenities API**: Manage room and bed amenities

---

*Module: Rooms & Beds API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
