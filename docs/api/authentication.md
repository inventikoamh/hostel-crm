# Authentication API Module

## Overview

The Authentication API provides endpoints for user login, logout, token management, and user information retrieval. This module handles all authentication-related operations for the Hostel CRM system.

## Base Endpoints

All authentication endpoints are prefixed with `/api/v1/auth/`

## Endpoints

### 1. Login

Authenticate a user and receive an API token.

**GET Version (Testing):**
```
GET /api/v1/auth/login?email={email}&password={password}
```

**POST Version (Integration):**
```
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

**Parameters:**
- `email` (required): User's email address
- `password` (required): User's password (minimum 6 characters)

**Response (200):**
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

**Error Responses:**
- `422` - Validation Error
- `401` - Invalid Credentials
- `500` - Server Error

### 2. Logout

Logout the authenticated user and revoke their token.

**GET Version (Testing):**
```
GET /api/v1/auth/logout
Authorization: Bearer {token}
```

**POST Version (Integration):**
```
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

**Headers:**
- `Authorization: Bearer {token}` (required)

**Response (200):**
```json
{
    "success": true,
    "message": "Logout successful"
}
```

**Error Responses:**
- `401` - Unauthorized
- `500` - Server Error

### 3. Get User Information

Retrieve detailed information about the authenticated user.

**GET Version (Testing):**
```
GET /api/v1/auth/me
Authorization: Bearer {token}
```

**POST Version (Integration):**
```
POST /api/v1/auth/me
Authorization: Bearer {token}
```

**Headers:**
- `Authorization: Bearer {token}` (required)

**Response (200):**
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

**Note:** The `tenant_profile` field is only included if the user is a tenant.

**Error Responses:**
- `401` - Unauthorized
- `500` - Server Error

### 4. Refresh Token

Generate a new API token and revoke the current one.

**GET Version (Testing):**
```
GET /api/v1/auth/refresh
Authorization: Bearer {token}
```

**POST Version (Integration):**
```
POST /api/v1/auth/refresh
Authorization: Bearer {token}
```

**Headers:**
- `Authorization: Bearer {token}` (required)

**Response (200):**
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

**Error Responses:**
- `401` - Unauthorized
- `500` - Server Error

## Authentication Flow

1. **Login:** Send credentials to `/auth/login` endpoint
2. **Store Token:** Save the returned token for future requests
3. **Use Token:** Include token in Authorization header for protected endpoints
4. **Refresh:** Use `/auth/refresh` to get a new token when needed
5. **Logout:** Use `/auth/logout` to revoke the token

## Security Considerations

- Tokens are automatically revoked on logout
- Tokens expire based on Laravel Sanctum configuration
- Rate limiting is applied to prevent brute force attacks
- All sensitive operations require valid authentication
- Passwords are hashed and never returned in responses

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/auth/login?email=admin@hostel.com&password=password
http://localhost/api/v1/auth/me (with Authorization header)
```

### Postman Testing (POST requests)
1. Create POST request to `/api/v1/auth/login`
2. Set body to JSON with email and password
3. Copy token from response
4. Add Authorization header to subsequent requests

### cURL Examples
```bash
# Login
curl -X POST http://localhost/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@hostel.com","password":"password"}'

# Get user info
curl -X GET http://localhost/api/v1/auth/me \
  -H "Authorization: Bearer {token}"

# Logout
curl -X POST http://localhost/api/v1/auth/logout \
  -H "Authorization: Bearer {token}"
```

## Error Handling

All endpoints return consistent error responses:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

Common error scenarios:
- Invalid credentials (401)
- Missing or invalid token (401)
- Validation errors (422)
- Server errors (500)

---

*Module: Authentication API*
*Version: 1.0.0*
*Last Updated: January 15, 2024*
