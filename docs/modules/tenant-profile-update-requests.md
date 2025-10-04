# Tenant Profile Update Request System

## Overview

The Tenant Profile Update Request System provides a comprehensive workflow for managing tenant profile changes with admin approval. This system ensures data integrity and proper oversight of tenant information modifications while providing a smooth user experience.

## Features

### Core Functionality
- **Change Detection**: Automatic detection of actual changes to prevent empty requests
- **Admin Approval Workflow**: All profile changes require admin approval
- **Profile Image Management**: Avatar upload with approval process
- **Bulk Operations**: Approve, reject, or delete multiple requests
- **Change Comparison**: Side-by-side comparison of current vs requested values
- **File Management**: Automatic cleanup of uploaded files when requests are deleted

### User Experience
- **Tenant Portal**: Self-service profile management interface
- **Pending Request Tracking**: Tenants can see their pending requests
- **Real-time Updates**: Immediate feedback on request status
- **Mobile Responsive**: Works seamlessly on all devices

## Database Schema

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

### Requested Changes JSON Structure
```json
{
    "user": {
        "name": "New Name",
        "phone": "New Phone",
        "avatar": "avatars/filename.jpg"
    },
    "tenant_profile": {
        "date_of_birth": "1990-01-01",
        "address": "New Address",
        "occupation": "New Occupation",
        "company": "New Company",
        "emergency_contact_name": "New Contact",
        "emergency_contact_phone": "New Phone",
        "emergency_contact_relation": "New Relation"
    }
}
```

## Workflow

### 1. Tenant Profile Update Process

#### Step 1: Tenant Initiates Update
1. Tenant logs into the tenant portal
2. Navigates to profile page
3. Clicks "Edit Profile" to enable editing mode
4. Makes desired changes to profile information
5. Optionally uploads new profile picture
6. Clicks "Submit for Approval"

#### Step 2: Change Detection
The system automatically:
- Compares current values with submitted values
- Only creates requests for fields that have actually changed
- Handles file uploads for profile pictures
- Shows appropriate message if no changes are detected

#### Step 3: Request Creation
- Creates a new `TenantProfileUpdateRequest` record
- Stores the requested changes in JSON format
- Sets status to 'pending'
- Redirects tenant with success message

### 2. Admin Approval Process

#### Step 1: Admin Review
1. Admin navigates to `/admin/tenant-profile-requests`
2. Views list of pending requests with tenant information
3. Clicks on a request to view details

#### Step 2: Change Comparison
The admin sees:
- Side-by-side comparison of current vs requested values
- Profile image comparison (current vs requested)
- Only the fields that are being changed
- Clear visual indicators for changes

#### Step 3: Approval Decision
Admin can:
- **Approve**: Apply all changes to tenant profile
- **Reject**: Deny the request with reason
- **Delete**: Remove the request entirely

#### Step 4: Bulk Operations
Admin can:
- Select multiple requests
- Bulk approve, reject, or delete
- Process multiple requests efficiently

## Routes

### Admin Routes
```php
// Admin routes for managing profile update requests
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Resource routes (index, show, destroy only)
    Route::resource('tenant-profile-requests', \App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class)
        ->except(['create', 'edit', 'store', 'update']);
    
    // Approval actions
    Route::post('/tenant-profile-requests/{tenantProfileRequest}/approve', 
        [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'approve'])
        ->name('tenant-profile-requests.approve');
    
    Route::post('/tenant-profile-requests/{tenantProfileRequest}/reject', 
        [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'reject'])
        ->name('tenant-profile-requests.reject');
    
    // Bulk actions
    Route::post('/tenant-profile-requests/bulk-action', 
        [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'bulkAction'])
        ->name('tenant-profile-requests.bulk-action');
});
```

### Tenant Portal Routes
```php
// Tenant portal routes for profile management
Route::prefix('tenant')->name('tenant.')->group(function () {
    // Protected tenant routes
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [TenantPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [TenantPortalController::class, 'updateProfile'])->name('profile.update');
    });
});
```

## Controllers

### Admin\TenantProfileUpdateRequestController

#### Methods
- `index()` - List all profile update requests with filtering
- `show()` - Display request details with change comparison
- `approve()` - Approve a profile update request
- `reject()` - Reject a profile update request
- `bulkAction()` - Handle bulk operations (approve/reject/delete)
- `destroy()` - Delete a profile update request

#### Key Features
- Advanced filtering and search
- Bulk action support
- File cleanup on deletion
- Change comparison display
- Statistics and metrics

### TenantPortalController

#### Methods
- `profile()` - Display tenant profile page
- `updateProfile()` - Process profile update requests

#### Key Features
- Change detection logic
- File upload handling
- Request creation
- User feedback

## Models

### TenantProfileUpdateRequest Model

#### Relationships
```php
public function tenantProfile()
{
    return $this->belongsTo(TenantProfile::class);
}

public function reviewedBy()
{
    return $this->belongsTo(User::class, 'reviewed_by');
}
```

#### Methods
```php
public function approve($adminId, $notes = null)
{
    $this->update([
        'status' => 'approved',
        'reviewed_by' => $adminId,
        'reviewed_at' => now(),
        'admin_notes' => $notes,
    ]);
    
    $this->applyChanges();
}

public function reject($adminId, $notes = null)
{
    $this->update([
        'status' => 'rejected',
        'reviewed_by' => $adminId,
        'reviewed_at' => now(),
        'admin_notes' => $notes,
    ]);
}

private function applyChanges()
{
    $tenantProfile = $this->tenantProfile;
    $changes = $this->requested_changes;

    // Update user information
    if (isset($changes['user'])) {
        $tenantProfile->user->update($changes['user']);
    }

    // Update tenant profile information
    if (isset($changes['tenant_profile'])) {
        $tenantProfile->update($changes['tenant_profile']);
    }
}
```

## Views

### Admin Views

#### Index View (`admin/tenant-profile-requests/index.blade.php`)
- Data table with tenant information
- Status badges and filtering
- Bulk action controls
- Statistics cards

#### Show View (`admin/tenant-profile-requests/show.blade.php`)
- Side-by-side change comparison
- Profile image comparison
- Approval/rejection forms
- Admin notes section

### Tenant Views

#### Profile View (`tenant/profile.blade.php`)
- Profile information display
- Edit mode toggle
- Avatar upload
- Pending requests widget
- Form validation and feedback

## Components

### Tenant Info Component (`components/tenant-info.blade.php`)
```php
@props(['name', 'email', 'avatar'])

<div class="flex items-center">
    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
        @if(isset($avatar) && $avatar)
            <img src="{{ asset('storage/' . $avatar) }}" alt="{{ $name }}" class="w-10 h-10 rounded-full object-cover">
        @else
            <i class="fas fa-user text-gray-600"></i>
        @endif
    </div>
    <div>
        <div class="text-sm font-medium" style="color: var(--text-primary);">{{ $name }}</div>
        <div class="text-sm" style="color: var(--text-secondary);">{{ $email }}</div>
    </div>
</div>
```

## File Management

### Avatar Upload
- Files stored in `storage/app/public/avatars/`
- Automatic file cleanup on request deletion
- Proper asset URL generation
- File validation and size limits

### Storage Configuration
```php
// config/filesystems.php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

## Security Considerations

### File Upload Security
- File type validation (images only)
- File size limits (2MB max)
- Secure file storage
- Automatic cleanup

### Data Validation
- Server-side validation for all inputs
- CSRF protection on all forms
- Proper authorization checks
- SQL injection prevention

### Access Control
- Admin-only access to approval functions
- Tenant-only access to profile updates
- Proper authentication middleware
- Role-based permissions

## Performance Optimization

### Database Optimization
- Proper indexing on frequently queried fields
- Eager loading of relationships
- Efficient query patterns
- Pagination for large datasets

### File Handling
- Efficient file storage
- Automatic cleanup of orphaned files
- Optimized image handling
- CDN-ready asset URLs

## Testing

### Unit Tests
- Model relationship testing
- Validation testing
- File upload testing
- Change detection testing

### Feature Tests
- Complete workflow testing
- Admin approval process
- Tenant update process
- Bulk operations testing

### Integration Tests
- File storage integration
- Database transaction testing
- Email notification testing
- API endpoint testing

## Future Enhancements

### Planned Features
- **Email Notifications**: Automatic notifications for status changes
- **Mobile App Integration**: Native mobile app support
- **Advanced File Management**: Document upload and management
- **Audit Trail**: Complete change history tracking
- **API Endpoints**: RESTful API for external integrations

### Performance Improvements
- **Caching**: Redis caching for frequently accessed data
- **Queue Processing**: Background job processing for file operations
- **CDN Integration**: Content delivery network for file assets
- **Database Optimization**: Advanced query optimization

## Troubleshooting

### Common Issues

#### Profile Images Not Loading
1. Check storage link: `php artisan storage:link`
2. Verify file permissions
3. Check asset URL generation
4. Verify file exists in storage

#### Empty Requests Being Created
1. Check change detection logic
2. Verify form data comparison
3. Check for hidden form fields
4. Verify validation rules

#### Bulk Actions Not Working
1. Check JavaScript console for errors
2. Verify CSRF token
3. Check route definitions
4. Verify middleware configuration

### Debug Commands
```bash
# Check storage link
php artisan storage:link

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Check routes
php artisan route:list --name=admin.tenant-profile-requests

# Check file permissions
ls -la storage/app/public/avatars/
```

## Maintenance

### Regular Tasks
- Monitor file storage usage
- Clean up orphaned files
- Review pending requests
- Update documentation

### Backup Considerations
- Database backup including JSON fields
- File storage backup
- Configuration backup
- Log file management

This system provides a robust, secure, and user-friendly approach to managing tenant profile updates with proper admin oversight and approval workflows.

