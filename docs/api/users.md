# Users API Module

## Overview
The Users API provides comprehensive endpoints for managing users, roles, and permissions within the Hostel CRM system. This module handles user management, role-based access control, and permission management for system security.

## Base Endpoints
All user endpoints are prefixed with `/api/v1/users/`

## Endpoints

### 1. Users Management

#### List Users
Retrieve a paginated list of all users with optional filtering.

**GET Version (Testing):**
```
GET /api/v1/users?page=1&per_page=15&status=active&is_tenant=false&role=admin
```

**POST Version (Integration):**
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

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `status` (optional): Filter by status (`active`, `inactive`, `suspended`)
- `is_tenant` (optional): Filter by tenant status (boolean)
- `is_super_admin` (optional): Filter by super admin status (boolean)
- `role` (optional): Filter by role slug
- `permission` (optional): Filter by permission slug
- `module` (optional): Filter by permission module
- `last_login_from` (optional): Filter users logged in from this date
- `last_login_to` (optional): Filter users logged in to this date
- `search` (optional): Search in name, email, or phone
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
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
        "total": 42,
        "from": 1,
        "to": 15
    }
}
```

#### Create User
Create a new user with roles and permissions.

**GET Version (Testing):**
```
GET /api/v1/users/create
```

**POST Version (Integration):**
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

**Required Fields:**
- `name`: User full name
- `email`: User email address (must be unique)
- `password`: User password (minimum 8 characters)

**Optional Fields:**
- `phone`: User phone number
- `status`: User status (`active`, `inactive`, `suspended`)
- `avatar`: User avatar image file
- `is_tenant`: Is tenant user (boolean)
- `is_super_admin`: Is super admin (boolean)
- `roles`: Array of role IDs
- `permissions`: Array of permission IDs

**Response (201):**
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

#### Get User Details
Retrieve detailed information about a specific user.

**GET Version (Testing):**
```
GET /api/v1/users/1
```

**Response (200):**
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
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z",
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

#### Update User
Update an existing user's information.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/users/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Smith",
    "phone": "+1-555-0124",
    "status": "active",
    "roles": [1, 3]
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "User updated successfully",
    "data": {
        "id": 1,
        "name": "John Smith",
        "email": "john.doe@example.com",
        "phone": "+1-555-0124",
        "status": "active",
        "status_badge": {
            "class": "bg-green-100 text-green-800",
            "text": "Active"
        },
        "updated_at": "2024-01-15T15:00:00.000000Z"
    }
}
```

#### Delete User
Remove a user from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/users/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "User deleted successfully"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot delete super admin users"
}
```

### 2. User Management Operations

#### Assign Role to User
Assign a role to a user.

**POST Version (Authenticated):**
```
POST /api/v1/users/1/assign-role
Authorization: Bearer {token}
Content-Type: application/json

{
    "role_id": 2
}
```

**Response (200):**
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

#### Remove Role from User
Remove a role from a user.

**POST Version (Authenticated):**
```
POST /api/v1/users/1/remove-role
Authorization: Bearer {token}
Content-Type: application/json

{
    "role_id": 2
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Role removed successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "roles": [
            {
                "id": 1,
                "name": "Admin",
                "slug": "admin"
            }
        ]
    }
}
```

#### Suspend User
Suspend a user account.

**POST Version (Authenticated):**
```
POST /api/v1/users/1/suspend
Authorization: Bearer {token}
Content-Type: application/json

{
    "reason": "Violation of terms of service"
}
```

**Response (200):**
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

#### Activate User
Activate a user account.

**POST Version (Authenticated):**
```
POST /api/v1/users/1/activate
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "User activated successfully",
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

### 3. Roles Management

#### List Roles
Retrieve a paginated list of all roles.

**GET Version (Testing):**
```
GET /api/v1/users/roles?page=1&per_page=15&is_system=false&search=admin
```

**POST Version (Integration):**
```
POST /api/v1/users/roles
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "is_system": false,
    "search": "admin"
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `is_system` (optional): Filter by system role status (boolean)
- `search` (optional): Search in name or description
- `sort_by` (optional): Sort field (default: `name`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
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
        "total": 42,
        "from": 1,
        "to": 15
    }
}
```

#### Create Role
Create a new role with permissions.

**POST Version (Integration):**
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

**Required Fields:**
- `name`: Role name
- `slug`: Role slug (must be unique)

**Optional Fields:**
- `description`: Role description
- `is_system`: Is system role (boolean)
- `permissions`: Array of permission IDs

**Response (201):**
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

#### Update Role
Update an existing role.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/users/roles/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Senior Admin",
    "description": "Senior administrator role",
    "permissions": [1, 2, 3, 4, 5]
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Role updated successfully",
    "data": {
        "id": 1,
        "name": "Senior Admin",
        "slug": "admin",
        "description": "Senior administrator role",
        "is_system": false,
        "permissions_count": 5,
        "users_count": 3,
        "permissions": [
            {
                "id": 1,
                "name": "Create Users",
                "slug": "create-users",
                "module": "users"
            }
        ],
        "updated_at": "2024-01-15T15:00:00.000000Z"
    }
}
```

#### Delete Role
Remove a role from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/users/roles/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Role deleted successfully"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot delete system roles"
}
```

### 4. Permissions Management

#### List Permissions
Retrieve a paginated list of all permissions.

**GET Version (Testing):**
```
GET /api/v1/users/permissions?page=1&per_page=15&module=users&is_system=false
```

**POST Version (Integration):**
```
POST /api/v1/users/permissions
Content-Type: application/json

{
    "page": 1,
    "per_page": 15,
    "module": "users",
    "is_system": false
}
```

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `is_system` (optional): Filter by system permission status (boolean)
- `module` (optional): Filter by permission module
- `search` (optional): Search in name, description, or module
- `sort_by` (optional): Sort field (default: `module`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
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
        "total": 42,
        "from": 1,
        "to": 15
    }
}
```

#### Create Permission
Create a new permission.

**POST Version (Integration):**
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

**Required Fields:**
- `name`: Permission name
- `slug`: Permission slug (must be unique)
- `module`: Permission module

**Optional Fields:**
- `description`: Permission description
- `is_system`: Is system permission (boolean)

**Response (201):**
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

#### Update Permission
Update an existing permission.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/users/permissions/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Create and Manage Users",
    "description": "Create and manage users in the system",
    "module": "users"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Permission updated successfully",
    "data": {
        "id": 1,
        "name": "Create and Manage Users",
        "slug": "create-users",
        "description": "Create and manage users in the system",
        "module": "users",
        "is_system": false,
        "roles_count": 2,
        "updated_at": "2024-01-15T15:00:00.000000Z"
    }
}
```

#### Delete Permission
Remove a permission from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/users/permissions/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Permission deleted successfully"
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot delete system permissions"
}
```

### 5. Statistics and Utilities

#### Get User Statistics
Retrieve user statistics and counts.

**GET Version (Testing):**
```
GET /api/v1/users/stats
```

**Response (200):**
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

#### Get Available Modules
Retrieve all available permission modules.

**GET Version (Testing):**
```
GET /api/v1/users/modules
```

**Response (200):**
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

### 6. Search API
Search for users, roles, and permissions.

**GET Version (Testing):**
```
GET /api/v1/users/search?query=john&type=users&status=active&limit=10
```

**POST Version (Integration):**
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

**Parameters:**
- `query` (required): Search term (minimum 2 characters)
- `type` (optional): Filter by type (`users`, `roles`, `permissions`)
- `status` (optional): Filter by user status (for user search)
- `is_tenant` (optional): Filter by tenant status (for user search)
- `module` (optional): Filter by module (for permission search)
- `limit` (optional): Maximum number of results (default: 10, max: 50)

**Response (200):**
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

## Data Models

### User Object
```json
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
```

### Role Object
```json
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
```

### Permission Object
```json
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
        "password": ["The password must be at least 8 characters."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "User not found"
}
```

### Conflict Errors (422)
```json
{
    "success": false,
    "message": "Cannot delete super admin users"
}
```

### Server Errors (500)
```json
{
    "success": false,
    "message": "Failed to retrieve users",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: List, Show, Search, Stats, Modules (read-only operations)
- **Authenticated Endpoints**: Create, Update, Delete, Assign Role, Remove Role, Suspend, Activate (write operations)
- **Authentication Method**: Bearer token via Laravel Sanctum

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/users
http://localhost/api/v1/users/roles
http://localhost/api/v1/users/permissions
http://localhost/api/v1/users/stats
http://localhost/api/v1/users/modules
http://localhost/api/v1/users/search?query=admin
```

### cURL Examples
```bash
# List users
curl -X GET http://localhost/api/v1/users

# Create user
curl -X POST http://localhost/api/v1/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john.doe@example.com","password":"password123","phone":"+1-555-0123","status":"active"}'

# Update user
curl -X PUT http://localhost/api/v1/users/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name":"John Smith","phone":"+1-555-0124"}'

# Delete user
curl -X DELETE http://localhost/api/v1/users/1 \
  -H "Authorization: Bearer {token}"

# Assign role
curl -X POST http://localhost/api/v1/users/1/assign-role \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"role_id":2}'

# Suspend user
curl -X POST http://localhost/api/v1/users/1/suspend \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"reason":"Violation of terms"}'

# Create role
curl -X POST http://localhost/api/v1/users/roles \
  -H "Content-Type: application/json" \
  -d '{"name":"Manager","slug":"manager","description":"Manager role","permissions":[1,2,3]}'

# Create permission
curl -X POST http://localhost/api/v1/users/permissions \
  -H "Content-Type: application/json" \
  -d '{"name":"Delete Users","slug":"delete-users","description":"Delete users","module":"users"}'

# Get user statistics
curl -X GET http://localhost/api/v1/users/stats

# Get modules
curl -X GET http://localhost/api/v1/users/modules

# Search users
curl -X POST http://localhost/api/v1/users/search \
  -H "Content-Type: application/json" \
  -d '{"query":"john","type":"users","status":"active","limit":10}'
```

## Business Rules

1. **Super Admin Protection**: Cannot delete super admin users
2. **System Role Protection**: Cannot delete system roles
3. **System Permission Protection**: Cannot delete system permissions
3. **Tenant Profile Protection**: Cannot delete users with tenant profiles
4. **Role Assignment**: Users can have multiple roles
5. **Permission Assignment**: Users can have direct permissions and role-based permissions
6. **Avatar Management**: Automatic avatar file management with cleanup
7. **Password Security**: Passwords are automatically hashed
8. **Email Uniqueness**: Email addresses must be unique
9. **Status Values**: Only specified status values are allowed
10. **Search Minimum**: Search queries must be at least 2 characters
11. **Pagination**: Maximum 100 items per page for performance
12. **Visibility Control**: Super admins are hidden from regular users
13. **Role Dependencies**: Cannot delete roles with assigned users
14. **Permission Dependencies**: Cannot delete permissions assigned to roles
15. **File Upload**: Avatar images are validated and stored securely

## Related Modules

- **Authentication API**: User authentication and token management
- **Tenants API**: Tenant profile management for tenant users
- **Hostels API**: Hostel management permissions
- **Invoices API**: Invoice management permissions
- **Payments API**: Payment processing permissions
- **Amenities API**: Amenity management permissions
- **Rooms API**: Room management permissions
- **Enquiries API**: Enquiry management permissions
- **Notifications API**: Notification system permissions

---

*Module: Users API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
