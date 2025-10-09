# Hostel CRM API - Agentic AI Intents

## Overview
This document provides short, actionable intents for each API module in the Hostel CRM system. These intents are designed to be used by agentic AI systems for understanding, interacting with, and automating tasks across the entire API ecosystem.

## API Modules & Intents

### 1. Authentication API (`/api/v1/auth/`)

**Core Intents:**
- `authenticate_user` - Login user and obtain access token
- `logout_user` - Invalidate user session and token
- `get_user_profile` - Retrieve current authenticated user details
- `refresh_token` - Generate new access token using refresh token
- `validate_token` - Check if current token is valid and active

**Use Cases:**
- User login/logout flows
- Session management
- Token validation
- User profile access
- Security enforcement

---

### 2. Hostels API (`/api/v1/hostels/`)

**Core Intents:**
- `list_hostels` - Get all hostels with filtering and pagination
- `create_hostel` - Add new hostel with complete details
- `get_hostel_details` - Retrieve specific hostel information
- `update_hostel` - Modify existing hostel details
- `delete_hostel` - Remove hostel (with safety checks)
- `search_hostels` - Find hostels by name, location, or criteria
- `get_hostel_stats` - Retrieve occupancy and performance metrics

**Use Cases:**
- Property management
- Hostel registration
- Location-based searches
- Performance monitoring
- Multi-property operations

---

### 3. Tenants API (`/api/v1/tenants/`)

**Core Intents:**
- `list_tenants` - Get all tenants with filtering options
- `create_tenant` - Register new tenant with profile
- `get_tenant_details` - Retrieve complete tenant information
- `update_tenant` - Modify tenant profile and details
- `delete_tenant` - Remove tenant (with data cleanup)
- `search_tenants` - Find tenants by name, email, or criteria
- `assign_bed` - Assign tenant to specific bed
- `release_bed` - Remove tenant from bed assignment
- `verify_tenant` - Mark tenant as verified
- `get_tenant_stats` - Retrieve tenant analytics and metrics

**Use Cases:**
- Tenant registration and management
- Bed assignment workflows
- Tenant verification processes
- Search and filtering
- Analytics and reporting

---

### 4. Rooms & Beds API (`/api/v1/rooms-beds/`)

**Core Intents:**
- `list_rooms` - Get all rooms with occupancy status
- `create_room` - Add new room to hostel
- `get_room_details` - Retrieve room information and beds
- `update_room` - Modify room details and configuration
- `delete_room` - Remove room (with safety checks)
- `list_beds` - Get all beds with assignment status
- `create_bed` - Add new bed to room
- `get_bed_details` - Retrieve bed information
- `update_bed` - Modify bed details
- `delete_bed` - Remove bed (with safety checks)
- `assign_bed` - Assign tenant to bed
- `release_bed` - Remove tenant from bed
- `get_occupancy_stats` - Retrieve occupancy analytics

**Use Cases:**
- Room and bed management
- Occupancy tracking
- Assignment workflows
- Capacity planning
- Space optimization

---

### 5. Invoices API (`/api/v1/invoices/`)

**Core Intents:**
- `list_invoices` - Get all invoices with filtering
- `create_invoice` - Generate new invoice
- `get_invoice_details` - Retrieve invoice with items and payments
- `update_invoice` - Modify invoice details
- `delete_invoice` - Remove invoice (with safety checks)
- `search_invoices` - Find invoices by number, tenant, or criteria
- `add_payment` - Record payment against invoice
- `mark_overdue` - Mark invoice as overdue
- `generate_amenity_invoice` - Create invoice for amenity usage
- `add_invoice_item` - Add line item to invoice
- `update_invoice_item` - Modify invoice line item
- `remove_invoice_item` - Remove line item from invoice
- `get_invoice_stats` - Retrieve invoice analytics

**Use Cases:**
- Invoice generation and management
- Payment processing
- Billing automation
- Overdue tracking
- Financial reporting

---

### 6. Payments API (`/api/v1/payments/`)

**Core Intents:**
- `list_payments` - Get all payments with filtering
- `create_payment` - Record new payment
- `get_payment_details` - Retrieve payment information
- `update_payment` - Modify payment details
- `delete_payment` - Remove payment (with safety checks)
- `search_payments` - Find payments by tenant, invoice, or criteria
- `verify_payment` - Mark payment as verified
- `cancel_payment` - Cancel payment and reverse invoice updates
- `get_tenant_payment_summary` - Get payment summary for tenant
- `get_invoice_payment_summary` - Get payment summary for invoice
- `get_payment_stats` - Retrieve payment analytics

**Use Cases:**
- Payment recording and verification
- Payment cancellation workflows
- Payment analytics
- Tenant payment history
- Financial reconciliation

---

### 7. Amenities API (`/api/v1/amenities/`)

**Core Intents:**
- `list_basic_amenities` - Get all basic amenities
- `create_basic_amenity` - Add new basic amenity
- `get_basic_amenity_details` - Retrieve amenity information
- `update_basic_amenity` - Modify amenity details
- `delete_basic_amenity` - Remove amenity
- `list_paid_amenities` - Get all paid amenities
- `create_paid_amenity` - Add new paid amenity
- `get_paid_amenity_details` - Retrieve paid amenity information
- `update_paid_amenity` - Modify paid amenity details
- `delete_paid_amenity` - Remove paid amenity
- `list_tenant_subscriptions` - Get tenant amenity subscriptions
- `subscribe_tenant` - Subscribe tenant to amenity
- `update_subscription` - Modify subscription details
- `suspend_subscription` - Temporarily suspend subscription
- `reactivate_subscription` - Reactivate suspended subscription
- `terminate_subscription` - Permanently end subscription
- `list_usage_records` - Get amenity usage records
- `record_usage` - Record amenity usage
- `get_tenant_usage_summary` - Get usage summary for tenant
- `search_amenities` - Find amenities by name or criteria

**Use Cases:**
- Amenity management
- Subscription workflows
- Usage tracking
- Billing automation
- Service optimization

---

### 8. Users API (`/api/v1/users/`)

**Core Intents:**
- `list_users` - Get all users with filtering
- `create_user` - Add new user account
- `get_user_details` - Retrieve user information
- `update_user` - Modify user details
- `delete_user` - Remove user account
- `assign_role` - Assign role to user
- `remove_role` - Remove role from user
- `suspend_user` - Suspend user account
- `activate_user` - Activate suspended account
- `list_roles` - Get all available roles
- `create_role` - Add new role
- `get_role_details` - Retrieve role information
- `update_role` - Modify role details
- `delete_role` - Remove role
- `list_permissions` - Get all permissions
- `create_permission` - Add new permission
- `get_permission_details` - Retrieve permission information
- `update_permission` - Modify permission details
- `delete_permission` - Remove permission
- `get_user_stats` - Retrieve user analytics
- `get_available_modules` - Get system modules
- `search_users` - Find users by name, email, or criteria

**Use Cases:**
- User account management
- Role-based access control
- Permission management
- User analytics
- Security administration

---

### 9. Enquiries API (`/api/v1/enquiries/`)

**Core Intents:**
- `list_enquiries` - Get all enquiries with filtering
- `create_enquiry` - Add new enquiry
- `get_enquiry_details` - Retrieve enquiry information
- `update_enquiry` - Modify enquiry details
- `delete_enquiry` - Remove enquiry
- `search_enquiries` - Find enquiries by name, email, or criteria
- `assign_enquiry` - Assign enquiry to user
- `resolve_enquiry` - Mark enquiry as resolved
- `close_enquiry` - Close enquiry
- `convert_to_tenant` - Convert enquiry to tenant profile
- `get_enquiry_stats` - Retrieve enquiry analytics
- `get_enquiry_sources` - Get enquiry source breakdown

**Use Cases:**
- Lead management
- Customer inquiry handling
- Conversion tracking
- Response management
- Sales analytics

---

### 10. Notifications API (`/api/v1/notifications/`)

**Core Intents:**
- `list_notifications` - Get all notifications with filtering
- `create_notification` - Add new notification
- `get_notification_details` - Retrieve notification information
- `update_notification` - Modify notification details
- `delete_notification` - Remove notification
- `search_notifications` - Find notifications by content or criteria
- `mark_as_sent` - Mark notification as sent
- `mark_as_failed` - Mark notification as failed
- `retry_notification` - Retry failed notification
- `cancel_notification` - Cancel pending notification
- `send_now` - Send notification immediately
- `get_notification_stats` - Retrieve notification analytics
- `get_scheduled_notifications` - Get notifications ready to send

**Use Cases:**
- Communication management
- Notification delivery
- Retry mechanisms
- Delivery tracking
- Communication analytics

---

### 11. Dashboard API (`/api/v1/dashboard/`)

**Core Intents:**
- `get_dashboard_overview` - Retrieve comprehensive dashboard summary
- `get_financial_dashboard` - Get financial analytics and metrics
- `get_occupancy_dashboard` - Get occupancy analytics and trends
- `get_tenant_analytics` - Get tenant analytics and demographics
- `get_amenity_analytics` - Get amenity usage analytics
- `get_enquiry_analytics` - Get enquiry analytics and conversion
- `get_notification_analytics` - Get notification delivery analytics
- `get_system_health` - Get system health and performance metrics
- `get_dashboard_widgets` - Get available widgets and user preferences
- `customize_dashboard` - Configure user-specific dashboard layout

**Use Cases:**
- Executive reporting
- Performance monitoring
- Business intelligence
- System health monitoring
- Custom dashboard creation

---

## Common Patterns

### CRUD Operations
All modules follow standard CRUD patterns:
- `list_{entity}` - Retrieve multiple records
- `create_{entity}` - Add new record
- `get_{entity}_details` - Retrieve single record
- `update_{entity}` - Modify existing record
- `delete_{entity}` - Remove record

### Search & Filtering
Most modules support:
- `search_{entity}` - Find records by criteria
- Filtering by status, date ranges, and relationships
- Pagination for large datasets

### Analytics & Reporting
Many modules provide:
- `get_{entity}_stats` - Retrieve analytics
- Trend analysis and metrics
- Performance indicators

### Workflow Operations
Modules include business-specific operations:
- Assignment workflows (beds, enquiries)
- Status management (payments, notifications)
- Conversion processes (enquiries to tenants)

## Integration Guidelines

### Authentication
- Use `authenticate_user` to obtain tokens
- Include `Authorization: Bearer {token}` in requests
- Refresh tokens as needed with `refresh_token`

### Error Handling
- All APIs return consistent error formats
- Check `success` field in responses
- Handle validation errors (422) and server errors (500)

### Data Relationships
- Understand entity relationships for complex operations
- Use related data for comprehensive operations
- Leverage dashboard APIs for aggregated insights

### Performance
- Use pagination for large datasets
- Implement caching for frequently accessed data
- Use search endpoints for filtered results

## Agentic AI Usage Examples

### Automated Tenant Onboarding
1. `create_enquiry` - Capture initial inquiry
2. `assign_enquiry` - Route to appropriate staff
3. `resolve_enquiry` - Mark as handled
4. `convert_to_tenant` - Create tenant profile
5. `assign_bed` - Assign available bed
6. `create_invoice` - Generate initial invoice
7. `create_notification` - Send welcome message

### Payment Processing Workflow
1. `list_pending_payments` - Find overdue payments
2. `create_payment` - Record payment
3. `verify_payment` - Confirm payment
4. `update_invoice` - Update invoice status
5. `create_notification` - Send payment confirmation

### Occupancy Management
1. `get_occupancy_dashboard` - Check current occupancy
2. `list_available_beds` - Find vacant beds
3. `assign_bed` - Assign new tenant
4. `update_room_status` - Update room occupancy
5. `get_occupancy_stats` - Update analytics

### Maintenance Scheduling
1. `get_system_health` - Check system status
2. `get_scheduled_notifications` - Find maintenance alerts
3. `create_notification` - Send maintenance notices
4. `update_system_status` - Track maintenance progress

This intent mapping provides a comprehensive guide for any agentic AI system to understand and interact with the Hostel CRM API ecosystem effectively.
