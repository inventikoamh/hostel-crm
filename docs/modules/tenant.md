# Tenant Management Module

## Overview

The Tenant Management module provides comprehensive functionality for managing tenant lifecycle in the Hostel CRM system. It handles tenant registration, profile management, bed assignment, billing cycles, payment tracking, and verification workflows with full database integration.

## Features

### Core Tenant Management
- **Complete CRUD Operations**: Create, read, update, and delete tenant records
- **Database Integration**: Full Eloquent ORM integration with relationships
- **Advanced Data Table**: Search, filtering, pagination, and bulk actions
- **Status Tracking**: Active, inactive, pending, suspended, moved out statuses
- **Verification System**: Tenant profile verification workflow
- **Profile Update Requests**: Admin approval workflow for tenant profile changes
- **Profile Image Management**: Avatar upload and approval system

### Billing & Payment Management
- **Billing Cycle Management**: Automated billing cycles (monthly, quarterly, half-yearly, yearly)
- **Payment Tracking**: Comprehensive payment history and status tracking
- **Late Fee Management**: Configurable late fees and penalty calculations
- **Outstanding Balance**: Track overdue amounts and payment history
- **Payment History Score**: Reliability scoring based on payment behavior
- **Notification System**: Payment reminders and overdue notifications (ready for implementation)

### Bed Assignment & Integration
- **Advanced Bed Assignment System**: Multi-tenant bed assignment with date-based availability
- **BedAssignment Model**: Separate tracking of bed assignments with status (active, reserved, inactive)
- **Date-based Availability**: Check bed availability based on lease start/end dates
- **Conditional Bed Selection**: Bed assignment only enabled when lease dates are provided
- **Dynamic Bed Loading**: AJAX-based bed selection by hostel with real-time availability
- **Automatic Rent Population**: Auto-fill rent from selected bed/room
- **Occupancy Tracking**: Track bed occupancy periods, reservations, and status changes
- **Future Reservation Support**: Reserve beds for future tenants with automatic status updates

### Profile Update Request System
- **Change Detection**: Automatic detection of profile changes to prevent empty requests
- **Admin Approval Workflow**: All profile changes require admin approval
- **Profile Image Upload**: Avatar upload with approval process
- **Bulk Actions**: Approve, reject, or delete multiple requests
- **Change Comparison**: Side-by-side comparison of current vs requested values
- **File Management**: Automatic cleanup of uploaded files when requests are deleted

### Amenity Management
- **Amenity Subscriptions**: View subscribed paid amenities and their details
- **Available Amenities**: Browse and request new amenity subscriptions
- **Usage Tracking**: View and manage amenity usage records
- **Correction Requests**: Request corrections to usage records with admin approval
- **Data Table Interface**: Advanced search, filtering, and pagination for usage records
- **Request Management**: Submit amenity requests and usage corrections

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Tenant Profiles Table (with Billing Cycle Fields)
```sql
CREATE TABLE tenant_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- Personal Information
    phone VARCHAR(255) NULL,
    date_of_birth DATE NULL,
    address TEXT NULL,
    occupation VARCHAR(255) NULL,
    company VARCHAR(255) NULL,
    
    -- ID Proof Information
    id_proof_type ENUM('aadhar', 'passport', 'driving_license', 'voter_id', 'pan_card', 'other') NULL,
    id_proof_number VARCHAR(255) NULL,
    
    -- Emergency Contact
    emergency_contact_name VARCHAR(255) NULL,
    emergency_contact_phone VARCHAR(255) NULL,
    emergency_contact_relation VARCHAR(255) NULL,
    
    -- Status & Verification
    status ENUM('active', 'inactive', 'pending', 'suspended', 'moved_out') DEFAULT 'pending',
    is_verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    verified_by BIGINT UNSIGNED NULL,
    
    -- Lease Information
    move_in_date DATE NULL,
    move_out_date DATE NULL,
    security_deposit DECIMAL(10,2) NULL,
    monthly_rent DECIMAL(8,2) NULL,
    lease_start_date DATE NULL,
    lease_end_date DATE NULL,
    notes TEXT NULL,
    
    -- Billing Cycle Configuration
    billing_cycle ENUM('monthly', 'quarterly', 'half_yearly', 'yearly') DEFAULT 'monthly',
    billing_day INTEGER DEFAULT 1,
    next_billing_date DATE NULL,
    last_billing_date DATE NULL,
    
    -- Payment Tracking
    payment_status ENUM('paid', 'pending', 'overdue', 'partial') DEFAULT 'pending',
    last_payment_date DATE NULL,
    last_payment_amount DECIMAL(10,2) NULL,
    outstanding_amount DECIMAL(10,2) DEFAULT 0,
    
    -- Notification Settings
    auto_billing_enabled BOOLEAN DEFAULT TRUE,
    notification_preferences JSON NULL,
    reminder_days_before INTEGER DEFAULT 3,
    overdue_grace_days INTEGER DEFAULT 5,
    
    -- Late Fees & Penalties
    late_fee_amount DECIMAL(8,2) NULL,
    late_fee_percentage DECIMAL(5,2) NULL,
    compound_late_fees BOOLEAN DEFAULT FALSE,
    
    -- Payment History
    consecutive_on_time_payments INTEGER DEFAULT 0,
    total_late_payments INTEGER DEFAULT 0,
    last_reminder_sent DATE NULL,
    reminder_count_current_cycle INTEGER DEFAULT 0,
    
    -- Auto-Payment (Future)
    auto_payment_enabled BOOLEAN DEFAULT FALSE,
    payment_method VARCHAR(255) NULL,
    payment_details JSON NULL,
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_next_billing_date (next_billing_date),
    INDEX idx_payment_status (payment_status),
    INDEX idx_billing_cycle (billing_cycle),
    INDEX idx_auto_billing_enabled (auto_billing_enabled)
);
```

### Bed Assignments Table
```sql
CREATE TABLE bed_assignments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bed_id BIGINT UNSIGNED NOT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    assigned_from DATE NOT NULL,
    assigned_until DATE NULL,
    status ENUM('active', 'reserved', 'inactive') DEFAULT 'active',
    monthly_rent DECIMAL(8,2) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (bed_id) REFERENCES beds(id) ON DELETE CASCADE,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_bed_id (bed_id),
    INDEX idx_tenant_id (tenant_id),
    INDEX idx_status (status),
    INDEX idx_assigned_from (assigned_from),
    INDEX idx_assigned_until (assigned_until),
    INDEX idx_date_range (assigned_from, assigned_until)
);
```

### Tenant Profile Update Requests Table
```sql
CREATE TABLE tenant_profile_update_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_profile_id BIGINT UNSIGNED NOT NULL,
    requested_changes JSON NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at TIMESTAMP NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (tenant_profile_id) REFERENCES tenant_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_tenant_profile_id (tenant_profile_id),
    INDEX idx_reviewed_by (reviewed_by),
    INDEX idx_created_at (created_at)
);
```

## Key Features

### Billing Cycles Supported
- **Monthly** (Default) - Standard monthly rent
- **Quarterly** - Every 3 months (rent × 3)
- **Half Yearly** - Every 6 months (rent × 6)
- **Yearly** - Every 12 months (rent × 12)

### Payment Status Tracking
- **Paid** - Full payment received on time
- **Pending** - Payment due but not yet received
- **Overdue** - Payment past due date + grace period
- **Partial** - Partial payment received

### Smart Calculations
- **Next Billing Amount** - Includes base rent + outstanding + late fees
- **Late Fee Calculation** - Fixed amount or percentage-based
- **Payment History Score** - 0-100 reliability score
- **Days Until Billing** - Countdown to next payment due

### Bed Assignment System Features
- **Multi-Tenant Support** - Multiple tenants can be assigned to the same bed at different times
- **Date Overlap Detection** - Prevents double-booking by checking for date conflicts
- **Status Management** - Active (current), Reserved (future), Inactive (past) assignments
- **Automatic Status Updates** - Reserved beds automatically become active on lease start date
- **Conditional UI** - Bed selection only enabled when lease dates are provided
- **Real-time Availability** - Dynamic bed loading based on current assignments and dates
- **Assignment History** - Complete history of all bed assignments for each tenant

## Routes

### Resource Routes
```php
Route::resource('tenants', TenantController::class)->middleware('auth');
```

### Additional Routes
```php
Route::get('/tenants/available-beds/{hostel}', [TenantController::class, 'getAvailableBeds'])
     ->name('tenants.available-beds')->middleware('auth');
Route::get('/tenants/available-beds-new/{hostel}', [TenantController::class, 'getAvailableBedsNew'])
     ->name('tenants.available-beds-new')->middleware('auth');
Route::post('/tenants/{tenant}/verify', [TenantController::class, 'verify'])
     ->name('tenants.verify')->middleware('auth');
Route::post('/tenants/{tenant}/move-out', [TenantController::class, 'moveOut'])
     ->name('tenants.move-out')->middleware('auth');
```

### Availability Routes
```php
Route::get('/availability', [AvailabilityController::class, 'index'])
     ->name('availability.index')->middleware('auth');
Route::post('/availability/check', [AvailabilityController::class, 'check'])
     ->name('availability.check')->middleware('auth');
```

### Tenant Profile Update Request Routes
```php
// Admin routes for managing profile update requests
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('tenant-profile-requests', \App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class)
        ->except(['create', 'edit', 'store', 'update']);
    Route::post('/tenant-profile-requests/{tenantProfileRequest}/approve', 
        [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'approve'])
        ->name('tenant-profile-requests.approve');
    Route::post('/tenant-profile-requests/{tenantProfileRequest}/reject', 
        [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'reject'])
        ->name('tenant-profile-requests.reject');
    Route::post('/tenant-profile-requests/bulk-action', 
        [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'bulkAction'])
        ->name('tenant-profile-requests.bulk-action');
});
```

### Tenant Portal Routes
```php
// Tenant portal routes for profile management
Route::prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/login', [TenantPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [TenantPortalController::class, 'login'])->name('login.post');
    
    // Protected tenant routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [TenantPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [TenantPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [TenantPortalController::class, 'updateProfile'])->name('profile.update');
        Route::post('/logout', [TenantPortalController::class, 'logout'])->name('logout');
    });
});
```

## Integration Points

### With Hostel Module
- Tenant assignment to hostel beds
- Rent calculation from room rates
- Occupancy statistics

### With Room Module
- Bed assignment and release
- Room occupancy tracking
- Rent synchronization

### With Billing System
- Automated billing cycle management
- Payment tracking and history
- Late fee calculations

### With Map Module
- Visual tenant location display
- Bed occupancy visualization
- Quick tenant information access

### With Availability Module
- Real-time bed availability checking
- Date-based availability filtering
- Consistent availability logic across tenant creation and availability pages

### With Amenity Management
- Amenity subscription management
- Usage tracking and correction requests
- Billing integration for paid amenities

## Future Enhancements

### Planned Features
- **Payment Gateway Integration**: Online rent payment processing
- **Automated Notifications**: Email/SMS payment reminders
- **Mobile App**: Tenant mobile application
- **Document Management**: Enhanced document storage and verification
- **Analytics Dashboard**: Tenant behavior and payment analytics

## Related Documentation
- [Availability Module](./availability.md)
- [Paid Amenities Module](./paid-amenities.md)
- [Usage Correction Requests](./usage-correction-requests.md)
- [Amenity Usage Tracking](./amenity-usage.md)
- [Invoice System](./invoice.md)
- [Payment System](./payment.md)
- [User Management](./user-management.md)
- [Billing Cycle System](../billing-cycle-system.md)
- [Table Standards](../table-standards.md)
- [Component Standards](../component-standards.md)

For detailed implementation examples and code samples, refer to the main codebase and the billing cycle system documentation.
