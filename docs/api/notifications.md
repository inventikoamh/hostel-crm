# Notifications API Module

## Overview
The Notifications API provides comprehensive endpoints for managing notifications, alerts, and communication within the Hostel CRM system. This module handles notification lifecycle management, delivery tracking, retry mechanisms, and integration with all other modules.

## Base Endpoints
All notification endpoints are prefixed with `/api/v1/notifications/`

## Endpoints

### 1. Notifications Management

#### List Notifications
Retrieve a paginated list of all notifications with optional filtering.

**GET Version (Testing):**
```
GET /api/v1/notifications?page=1&per_page=15&status=pending&type=tenant_added&scheduled=true
```

**POST Version (Integration):**
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

**Query Parameters:**
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 15, max: 100)
- `status` (optional): Filter by status (`pending`, `sent`, `failed`, `cancelled`)
- `type` (optional): Filter by notification type
- `recipient_email` (optional): Filter by recipient email
- `created_by` (optional): Filter by creator user ID
- `notifiable_type` (optional): Filter by related entity type
- `notifiable_id` (optional): Filter by related entity ID
- `scheduled` (optional): Filter scheduled notifications (boolean)
- `date_from` (optional): Filter notifications from this date
- `date_to` (optional): Filter notifications to this date
- `sent_from` (optional): Filter by sent date from
- `sent_to` (optional): Filter by sent date to
- `search` (optional): Search in title, message, recipient email, or recipient name
- `sort_by` (optional): Sort field (default: `created_at`)
- `sort_order` (optional): Sort direction (`asc` or `desc`)

**Response (200):**
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
        "total": 42,
        "from": 1,
        "to": 15
    }
}
```

#### Create Notification
Create a new notification.

**GET Version (Testing):**
```
GET /api/v1/notifications/create
```

**POST Version (Integration):**
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

**Required Fields:**
- `type`: Notification type
- `title`: Notification title
- `message`: Notification message
- `recipient_email`: Recipient email address
- `recipient_name`: Recipient name

**Optional Fields:**
- `status`: Status (`pending`, `sent`, `failed`, `cancelled`)
- `data`: Additional data (JSON)
- `scheduled_at`: Schedule notification for later (datetime)
- `notifiable_type`: Related entity type
- `notifiable_id`: Related entity ID
- `created_by`: User ID who created this notification

**Available Types:**
- `tenant_added`: Tenant Added
- `tenant_updated`: Tenant Updated
- `enquiry_added`: New Enquiry
- `invoice_created`: Invoice Created
- `invoice_sent`: Invoice Sent
- `payment_received`: Payment Received
- `payment_verified`: Payment Verified
- `amenity_usage_recorded`: Amenity Usage Recorded
- `lease_expiring`: Lease Expiring
- `overdue_payment`: Overdue Payment

**Response (201):**
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

#### Get Notification Details
Retrieve detailed information about a specific notification.

**GET Version (Testing):**
```
GET /api/v1/notifications/1
```

**Response (200):**
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

#### Update Notification
Update an existing notification's information.

**PUT/PATCH Version (Authenticated):**
```
PUT /api/v1/notifications/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "sent",
    "title": "Updated Welcome Message",
    "message": "Your tenant registration has been completed successfully. Welcome!"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Notification updated successfully",
    "data": {
        "id": 1,
        "status": "sent",
        "status_badge": "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'>Sent</span>",
        "title": "Updated Welcome Message",
        "message": "Your tenant registration has been completed successfully. Welcome!",
        "updated_at": "2024-01-15T15:00:00.000000Z"
    }
}
```

#### Delete Notification
Remove a notification from the system.

**DELETE Version (Authenticated):**
```
DELETE /api/v1/notifications/1
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Notification deleted successfully"
}
```

### 2. Notification Management Operations

#### Mark Notification as Sent
Mark a notification as successfully sent.

**POST Version (Authenticated):**
```
POST /api/v1/notifications/1/mark-sent
Authorization: Bearer {token}
```

**Response (200):**
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

#### Mark Notification as Failed
Mark a notification as failed to send.

**POST Version (Authenticated):**
```
POST /api/v1/notifications/1/mark-failed
Authorization: Bearer {token}
Content-Type: application/json

{
    "error_message": "SMTP server unavailable"
}
```

**Response (200):**
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

#### Retry Failed Notification
Retry a failed notification.

**POST Version (Authenticated):**
```
POST /api/v1/notifications/1/retry
Authorization: Bearer {token}
```

**Response (200):**
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

#### Cancel Notification
Cancel a pending notification.

**POST Version (Authenticated):**
```
POST /api/v1/notifications/1/cancel
Authorization: Bearer {token}
```

**Response (200):**
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

#### Send Notification Now
Send a notification immediately.

**POST Version (Authenticated):**
```
POST /api/v1/notifications/1/send-now
Authorization: Bearer {token}
```

**Response (200):**
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

### 3. Statistics and Analytics

#### Get Notification Statistics
Retrieve comprehensive notification statistics.

**GET Version (Testing):**
```
GET /api/v1/notifications/stats
```

**Response (200):**
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

#### Get Scheduled Notifications
Retrieve notifications that are scheduled and ready to send.

**GET Version (Testing):**
```
GET /api/v1/notifications/scheduled?limit=50
```

**Response (200):**
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

### 4. Search API
Search for notifications with advanced filtering.

**GET Version (Testing):**
```
GET /api/v1/notifications/search?query=welcome&status=pending&type=tenant_added&limit=10
```

**POST Version (Integration):**
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

**Parameters:**
- `query` (required): Search term (minimum 2 characters)
- `status` (optional): Filter by status
- `type` (optional): Filter by notification type
- `limit` (optional): Maximum number of results (default: 10, max: 50)

**Response (200):**
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

## Data Models

### Notification Object
```json
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
```

### Detailed Notification Object
```json
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
```

## Error Handling

### Validation Errors (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "type": ["The type field is required."],
        "title": ["The title field is required."],
        "message": ["The message field is required."],
        "recipient_email": ["The recipient email field is required."],
        "recipient_name": ["The recipient name field is required."]
    }
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Notification not found"
}
```

### Business Logic Errors (422)
```json
{
    "success": false,
    "message": "Cannot cancel already sent notification"
}
```

```json
{
    "success": false,
    "message": "Notification cannot be retried (max retries exceeded or not failed)"
}
```

### Server Errors (500)
```json
{
    "success": false,
    "message": "Failed to retrieve notifications",
    "error": "Database connection error"
}
```

## Authentication

- **Public Endpoints**: List, Show, Search, Stats, Scheduled (read-only operations)
- **Authenticated Endpoints**: Create, Update, Delete, Mark as Sent/Failed, Retry, Cancel, Send Now (write operations)
- **Authentication Method**: Bearer token via Laravel Sanctum

## Rate Limiting

- **Public Endpoints**: 100 requests per minute per IP
- **Authenticated Endpoints**: 1000 requests per minute per user

## Testing Examples

### Browser Testing (GET requests)
```
http://localhost/api/v1/notifications
http://localhost/api/v1/notifications/create
http://localhost/api/v1/notifications/stats
http://localhost/api/v1/notifications/scheduled
http://localhost/api/v1/notifications/search?query=welcome
```

### cURL Examples
```bash
# List notifications
curl -X GET http://localhost/api/v1/notifications

# Create notification
curl -X POST http://localhost/api/v1/notifications \
  -H "Content-Type: application/json" \
  -d '{"type":"tenant_added","title":"Welcome to Hostel CRM","message":"Your tenant registration has been completed successfully.","recipient_email":"tenant@example.com","recipient_name":"John Doe","status":"pending","data":{"hostel_name":"Sunrise Hostel","tenant_id":1}}'

# Update notification
curl -X PUT http://localhost/api/v1/notifications/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"status":"sent","title":"Updated Welcome Message","message":"Your tenant registration has been completed successfully. Welcome!"}'

# Delete notification
curl -X DELETE http://localhost/api/v1/notifications/1 \
  -H "Authorization: Bearer {token}"

# Mark as sent
curl -X POST http://localhost/api/v1/notifications/1/mark-sent \
  -H "Authorization: Bearer {token}"

# Mark as failed
curl -X POST http://localhost/api/v1/notifications/1/mark-failed \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"error_message":"SMTP server unavailable"}'

# Retry notification
curl -X POST http://localhost/api/v1/notifications/1/retry \
  -H "Authorization: Bearer {token}"

# Cancel notification
curl -X POST http://localhost/api/v1/notifications/1/cancel \
  -H "Authorization: Bearer {token}"

# Send now
curl -X POST http://localhost/api/v1/notifications/1/send-now \
  -H "Authorization: Bearer {token}"

# Get statistics
curl -X GET http://localhost/api/v1/notifications/stats

# Get scheduled notifications
curl -X GET http://localhost/api/v1/notifications/scheduled

# Search notifications
curl -X POST http://localhost/api/v1/notifications/search \
  -H "Content-Type: application/json" \
  -d '{"query":"welcome","status":"pending","type":"tenant_added","limit":10}'
```

## Business Rules

1. **Notification Types**: Only specified notification types are allowed
2. **Status Values**: Only specified status values are allowed
3. **Retry Logic**: Failed notifications can be retried up to 3 times
4. **Scheduling**: Notifications can be scheduled for future delivery
5. **Cancellation**: Only pending notifications can be cancelled
6. **Immediate Sending**: Notifications can be sent immediately regardless of schedule
7. **Status Transitions**: Proper status transitions (pending → sent/failed/cancelled)
8. **Error Tracking**: Failed notifications track error messages and retry counts
9. **Entity Relationships**: Notifications can be linked to any entity via polymorphic relationships
10. **Creator Tracking**: Track who created each notification
11. **Search Minimum**: Search queries must be at least 2 characters
12. **Pagination**: Maximum 100 items per page for performance
13. **Success Rate**: Calculate delivery success rates
14. **Retry Analytics**: Track average retry counts for failed notifications
15. **Scheduled Processing**: Retrieve notifications ready for immediate sending

## Related Modules

- **Authentication API**: User authentication for admin operations
- **Users API**: User management for notification creators
- **Tenants API**: Tenant-related notifications
- **Enquiries API**: Enquiry-related notifications
- **Invoices API**: Invoice-related notifications
- **Payments API**: Payment-related notifications
- **Amenities API**: Amenity usage notifications
- **Dashboard API**: Notification analytics and summaries

---

*Module: Notifications API*  
*Version: 1.0.0*  
*Last Updated: January 15, 2024*
