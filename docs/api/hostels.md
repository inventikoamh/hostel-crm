# Hostels API Module

## Overview
The Hostels API provides comprehensive endpoints for managing hostel properties within the Hostel CRM system. This module handles all CRUD operations for hostels, including search functionality, statistics, and detailed property information.

## Base Endpoints
All hostel endpoints are prefixed with `/api/v1/hostels/`

## Endpoints

### 1. List Hostels
Retrieve a paginated list of all hostels with optional filtering and sorting.

**GET Version (Testing):**
```
GET /api/v1/hostels?page=1&per_page=15&status=active&city=New York&search=hostel name
```

**POST Version (Integration):**
```
POST /api/v1/hostels
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "status": "active",
    "city": "New York",
    "search": "hostel name",
    "sort_by": "name",
    "sort_order": "asc"
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `status` (optional): Filter by status (`active`, `inactive`, `maintenance`)
- `city` (optional): Filter by city name
- `state` (optional): Filter by state name
- `search` (optional): Search in name, address, city, or manager name
- `sort_by` (optional): Sort field (default: `name`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
```json
{
    "success": true,
    "message": "Hostels retrieved successfully",
    "data": [
        {
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
            "status": "active",
            "manager_name": "John Smith",
            "manager_phone": "+1-555-0124",
            "manager_email": "john@downtownhostel.com",
            "check_in_time": "14:00",
            "check_out_time": "11:00",
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

### 2. Create Hostel
Create a new hostel property.

**GET Version (Testing):**
```
GET /api/v1/hostels/create
```

**POST Version (Integration):**
```
POST /api/v1/hostels
Content-Type: application/json

{
    "name": "New Hostel",
    "description": "A beautiful new hostel",
    "address": "456 Oak Avenue",
    "city": "Los Angeles",
    "state": "CA",
    "country": "USA",
    "postal_code": "90210",
    "phone": "+1-555-0456",
    "email": "info@newhostel.com",
    "website": "https://newhostel.com",
    "amenities": ["WiFi", "Laundry", "Kitchen"],
    "images": ["https://example.com/image1.jpg"],
    "status": "active",
    "manager_name": "Jane Doe",
    "manager_phone": "+1-555-0457",
    "manager_email": "jane@newhostel.com",
    "rules": "No smoking, Quiet hours 10pm-7am",
    "check_in_time": "15:00",
    "check_out_time": "12:00"
}
```

**Required Fields:**
- `name`: Hostel name
- `address`: Street address
- `city`: City name
- `state`: State/province
- `country`: Country name
- `postal_code`: Postal/ZIP code
- `phone`: Contact phone number
- `email`: Contact email address
- `status`: Status (`active`, `inactive`, `maintenance`)
- `manager_name`: Manager's full name
- `manager_phone`: Manager's phone number
- `manager_email`: Manager's email address

**Optional Fields:**
- `description`: Hostel description
- `website`: Website URL
- `amenities`: Array of amenities
- `images`: Array of image URLs
- `rules`: House rules
- `check_in_time`: Check-in time (HH:MM format)
- `check_out_time`: Check-out time (HH:MM format)

**Response (201):**
```json
{
    "success": true,
    "message": "Hostel created successfully",
    "data": {
        "id": 2,
        "name": "New Hostel",
        "description": "A beautiful new hostel",
        "address": "456 Oak Avenue",
        "city": "Los Angeles",
        "state": "CA",
        "country": "USA",
        "postal_code": "90210",
        "full_address": "456 Oak Avenue, Los Angeles, CA 90210, USA",
        "phone": "+1-555-0456",
        "email": "info@newhostel.com",
        "website": "https://newhostel.com",
        "status": "active",
        "manager_name": "Jane Doe",
        "manager_phone": "+1-555-0457",
        "manager_email": "jane@newhostel.com",
        "check_in_time": "15:00",
        "check_out_time": "12:00",
        "created_at": "2024-01-15T12:00:00.000000Z",
        "updated_at": "2024-01-15T12:00:00.000000Z"
    }
}
```

### 3. Get Hostel Details
Retrieve detailed information about a specific hostel.

**GET Version (Testing):**
```
GET /api/v1/hostels/1
```

**POST Version (Integration):**
```
POST /api/v1/hostels/1
Content-Type: application/json
```

**Response (200):**
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
        "amenities": ["WiFi", "Laundry", "Kitchen", "Parking"],
        "images": ["https://example.com/image1.jpg", "https://example.com/image2.jpg"],
        "status": "active",
        "manager_name": "John Smith",
        "manager_phone": "+1-555-0124",
        "manager_email": "john@downtownhostel.com",
        "rules": "No smoking, Quiet hours 10pm-7am, No pets",
        "check_in_time": "14:00",
        "check_out_time": "11:00",
        "total_rooms": 25,
        "total_beds": 50,
        "available_beds": 12,
        "occupied_beds": 38,
        "occupancy_rate": 76.0,
        "rent_per_bed": 45.00,
        "formatted_rent": "$45.00",
        "floors": [1, 2, 3],
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### 4. Update Hostel
Update an existing hostel's information.

**GET Version (Testing):**
```
GET /api/v1/hostels/1/edit
```

**POST Version (Integration):**
```
POST /api/v1/hostels/1
Content-Type: application/json

{
    "name": "Updated Hostel Name",
    "description": "Updated description",
    "status": "maintenance"
}
```

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/hostels/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Hostel Name",
    "description": "Updated description",
    "status": "maintenance"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Hostel updated successfully",
    "data": {
        "id": 1,
        "name": "Updated Hostel Name",
        "description": "Updated description",
        "status": "maintenance",
        "updated_at": "2024-01-15T15:30:00.000000Z"
    }
}
```

### 5. Delete Hostel
Remove a hostel from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/hostels/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Hostel deleted successfully"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot delete hostel with existing rooms. Please remove all rooms first."
}
```

### 6. Get Hostel Statistics
Retrieve statistical information about a hostel.

**GET Version (Testing):**
```
GET /api/v1/hostels/1/stats
```

**POST Version (Integration):**
```
POST /api/v1/hostels/1/stats
Content-Type: application/json
```

**Response (200):**
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
        "status": "active",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### 7. Search Hostels
Search for hostels by various criteria.

**GET Version (Testing):**
```
GET /api/v1/hostels/search?query=downtown&limit=10
```

**POST Version (Integration):**
```
POST /api/v1/hostels/search
Content-Type: application/json

{
    "query": "downtown",
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
            "name": "Downtown Hostel",
            "address": "123 Main Street",
            "city": "New York",
            "state": "NY",
            "status": "active",
            "occupancy_rate": 76.0
        }
    ],
    "query": "downtown",
    "count": 1
}
```

## Data Models

### Hostel Object
```json
{
    "id": 1,
    "name": "Hostel Name",
    "description": "Hostel description",
    "address": "Street address",
    "city": "City name",
    "state": "State/Province",
    "country": "Country name",
    "postal_code": "Postal code",
    "full_address": "Complete formatted address",
    "phone": "Contact phone",
    "email": "Contact email",
    "website": "Website URL",
    "amenities": ["WiFi", "Laundry", "Kitchen"],
    "images": ["https://example.com/image1.jpg"],
    "status": "active|inactive|maintenance",
    "manager_name": "Manager's name",
    "manager_phone": "Manager's phone",
    "manager_email": "Manager's email",
    "rules": "House rules",
    "check_in_time": "14:00",
    "check_out_time": "11:00",
    "total_rooms": 25,
    "total_beds": 50,
    "available_beds": 12,
    "occupied_beds": 38,
    "occupancy_rate": 76.0,
    "rent_per_bed": 45.00,
    "formatted_rent": "$45.00",
    "floors": [1, 2, 3],
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
        "email": ["The email must be a valid email address."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Hostel not found"
}
```

### Server Errors (500)
```json
{
    "success": false,
    "message": "Failed to retrieve hostels",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: List, Show, Search, Stats (read-only operations)
- **Authenticated Endpoints**: Create, Update, Delete (write operations)
- **Authentication Method**: Bearer token via Laravel Sanctum

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/hostels
http://localhost/api/v1/hostels/1
http://localhost/api/v1/hostels/1/stats
http://localhost/api/v1/hostels/search?query=downtown
```

### cURL Examples
```bash
# List hostels
curl -X GET http://localhost/api/v1/hostels

# Get specific hostel
curl -X GET http://localhost/api/v1/hostels/1

# Create hostel
curl -X POST http://localhost/api/v1/hostels \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Hostel","address":"123 Test St","city":"Test City","state":"TS","country":"Test Country","postal_code":"12345","phone":"555-0123","email":"test@hostel.com","status":"active","manager_name":"Test Manager","manager_phone":"555-0124","manager_email":"manager@hostel.com"}'

# Update hostel (authenticated)
curl -X PUT http://localhost/api/v1/hostels/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name":"Updated Hostel Name"}'

# Delete hostel (authenticated)
curl -X DELETE http://localhost/api/v1/hostels/1 \
  -H "Authorization: Bearer {token}"

# Search hostels
curl -X POST http://localhost/api/v1/hostels/search \
  -H "Content-Type: application/json" \
  -d '{"query":"downtown","limit":5}'
```

## Business Rules

1. **Hostel Deletion**: Cannot delete hostels that have existing rooms
2. **Status Values**: Only `active`, `inactive`, or `maintenance` are allowed
3. **Email Validation**: All email fields must be valid email addresses
4. **Phone Format**: Phone numbers should include country code
5. **Time Format**: Check-in/out times must be in HH:MM format
6. **Search Minimum**: Search queries must be at least 2 characters
7. **Pagination**: Maximum 100 items per page for performance

## Related Modules

- **Rooms API**: Manage rooms within hostels
- **Beds API**: Manage beds within rooms
- **Tenants API**: Manage tenant assignments
- **Invoices API**: Generate invoices for hostel services
- **Payments API**: Process payments for hostel fees

---

*Module: Hostels API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
