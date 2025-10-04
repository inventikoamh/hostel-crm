# Usage Correction Request System

## Overview

The Usage Correction Request System provides a comprehensive workflow for managing tenant requests to correct amenity usage records. This system ensures data accuracy and proper oversight of usage record modifications while providing a smooth user experience for both tenants and administrators.

## Features

### Core Functionality
- **Request Submission**: Tenants can request corrections to their usage records
- **Admin Review Workflow**: All correction requests require admin approval
- **Bulk Operations**: Approve, reject, or manage multiple requests simultaneously
- **Change Comparison**: Side-by-side comparison of current vs requested values
- **Audit Trail**: Complete tracking of who requested, reviewed, and when

### User Experience
- **Tenant Portal**: Self-service correction request interface
- **Admin Dashboard**: Comprehensive management interface for all requests
- **Real-time Updates**: Immediate feedback on request status
- **Mobile Responsive**: Works seamlessly on all devices

## Database Schema

### Usage Correction Requests Table
```sql
CREATE TABLE usage_correction_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_amenity_usage_id BIGINT UNSIGNED NOT NULL,
    requested_by BIGINT UNSIGNED NOT NULL,
    original_quantity INT NOT NULL,
    requested_quantity INT NOT NULL,
    original_notes TEXT NULL,
    requested_notes TEXT NULL,
    correction_reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (tenant_amenity_usage_id) REFERENCES tenant_amenity_usage(id) ON DELETE CASCADE,
    FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status_created (status, created_at),
    INDEX idx_requested_by (requested_by),
    INDEX idx_reviewed_by (reviewed_by)
);
```

## Models

### UsageCorrectionRequest Model
```php
class UsageCorrectionRequest extends Model
{
    protected $fillable = [
        'tenant_amenity_usage_id',
        'requested_by',
        'original_quantity',
        'requested_quantity',
        'original_notes',
        'requested_notes',
        'correction_reason',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    // Relationships
    public function tenantAmenityUsage(): BelongsTo
    public function requestedBy(): BelongsTo
    public function reviewedBy(): BelongsTo

    // Scopes
    public function scopePending($query)
    public function scopeApproved($query)
    public function scopeRejected($query)

    // Accessors
    public function getStatusBadgeColorAttribute(): string
    public function getStatusDisplayAttribute(): string
}
```

## Controllers

### TenantPortalController
Handles tenant-side correction request submission:

```php
public function requestUsageCorrection(Request $request, TenantAmenityUsage $usage)
{
    // Validation
    // Create correction request
    // Return success response
}
```

### Admin\UsageCorrectionRequestController
Handles admin-side request management:

```php
public function index(Request $request)           // List all requests
public function show(UsageCorrectionRequest $request) // View request details
public function approve(Request $request, UsageCorrectionRequest $correctionRequest) // Approve request
public function reject(Request $request, UsageCorrectionRequest $correctionRequest)  // Reject request
public function bulkApprove(Request $request)     // Bulk approve
public function bulkReject(Request $request)      // Bulk reject
public function bulkAction(Request $request)      // Handle bulk actions from data table
```

## Routes

### Tenant Routes
```php
Route::prefix('tenant')->name('tenant.')->group(function () {
    Route::post('/amenities/usage/{usage}/correction', [TenantPortalController::class, 'requestUsageCorrection'])
        ->name('amenities.usage.correction');
});
```

### Admin Routes
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('usage-correction-requests', Admin\UsageCorrectionRequestController::class)
        ->except(['create', 'edit', 'store', 'update']);
    Route::post('/usage-correction-requests/{usageCorrectionRequest}/approve', [Admin\UsageCorrectionRequestController::class, 'approve'])
        ->name('usage-correction-requests.approve');
    Route::post('/usage-correction-requests/{usageCorrectionRequest}/reject', [Admin\UsageCorrectionRequestController::class, 'reject'])
        ->name('usage-correction-requests.reject');
    Route::post('/usage-correction-requests/bulk-action', [Admin\UsageCorrectionRequestController::class, 'bulkAction'])
        ->name('usage-correction-requests.bulk-action');
});
```

## Views

### Tenant Views
- **Usage Tracking Page**: `/tenant/amenities/usage`
  - Data table with usage records
  - "Request Correction" button for each record
  - Modal form for correction request submission

### Admin Views
- **Index Page**: `/admin/usage-correction-requests`
  - Statistics dashboard
  - Data table with search and filters
  - Bulk action capabilities
- **Show Page**: `/admin/usage-correction-requests/{id}`
  - Detailed request information
  - Side-by-side comparison
  - Approve/Reject actions

## Workflow

### Tenant Workflow
1. **View Usage Records**: Tenant accesses usage tracking page
2. **Identify Error**: Tenant notices incorrect usage record
3. **Request Correction**: Clicks "Request Correction" button
4. **Fill Form**: Provides new quantity, notes, and reason
5. **Submit Request**: Request is created with 'pending' status
6. **Wait for Review**: Admin reviews and decides on request

### Admin Workflow
1. **View Requests**: Admin accesses correction requests dashboard
2. **Review Details**: Examines original vs requested values
3. **Make Decision**: Approve or reject the request
4. **Add Notes**: Provide admin notes for the decision
5. **Update Records**: If approved, usage record is updated
6. **Notify Tenant**: Status change is reflected in tenant portal

## Features

### Statistics Dashboard
- **Total Requests**: Count of all correction requests
- **Pending**: Requests awaiting admin review
- **Approved**: Successfully approved requests
- **Rejected**: Requests that were rejected

### Data Table Features
- **Search**: By tenant name or amenity name
- **Filter**: By status (All, Pending, Approved, Rejected)
- **Sort**: By any column (date, tenant, status, etc.)
- **Pagination**: 15 items per page
- **Bulk Actions**: Select multiple requests for batch processing

### Request Details
- **Tenant Information**: Name, email, avatar
- **Usage Record**: Amenity, date, original values
- **Request Details**: Requested changes and reason
- **Status Tracking**: Current status, dates, reviewer info
- **Admin Actions**: Approve/Reject with notes

## Validation Rules

### Tenant Request Validation
```php
'correction_reason' => 'required|string|max:500',
'requested_quantity' => 'required|integer|min:1|max:10',
'requested_notes' => 'nullable|string|max:500',
```

### Admin Action Validation
```php
// Approve
'admin_notes' => 'nullable|string|max:500',

// Reject
'admin_notes' => 'required|string|max:500',
```

## Security

### Authorization
- **Tenant Access**: Users can only request corrections for their own usage records
- **Admin Access**: Only admin users can review and approve/reject requests
- **Data Integrity**: Foreign key constraints ensure data consistency

### Validation
- **Input Validation**: All inputs are validated on both client and server side
- **CSRF Protection**: All forms include CSRF tokens
- **Permission Checks**: Middleware ensures proper access control

## Integration

### Related Modules
- **Amenity Usage Tracking**: Source of usage records that can be corrected
- **Paid Amenities**: Amenities that tenants can request corrections for
- **Tenant Management**: Tenant information and access control
- **User Management**: Admin users who can review requests

### Data Flow
1. **Usage Record Creation**: Via amenity usage tracking
2. **Correction Request**: Tenant submits correction request
3. **Admin Review**: Admin reviews and makes decision
4. **Record Update**: If approved, original usage record is updated
5. **Audit Trail**: All actions are logged with timestamps

## Troubleshooting

### Common Issues

#### Request Not Submitting
- **Check**: User is logged in as tenant
- **Verify**: Usage record belongs to the tenant
- **Validate**: All required fields are filled
- **Ensure**: CSRF token is present

#### Admin Cannot See Requests
- **Verify**: User has admin role
- **Check**: Database has correction requests
- **Ensure**: Proper relationships are loaded

#### Bulk Actions Not Working
- **Validate**: At least one request is selected
- **Check**: Selected requests are in 'pending' status
- **Verify**: Admin has proper permissions

### Performance Considerations
- **Database Indexing**: Proper indexes on status and date columns
- **Eager Loading**: Load relationships to avoid N+1 queries
- **Pagination**: Limit results to prevent memory issues
- **Caching**: Consider caching for frequently accessed data

## Future Enhancements

### Planned Features
- **Email Notifications**: Notify tenants when requests are approved/rejected
- **Mobile App**: Native mobile interface for request submission
- **Advanced Analytics**: Track correction patterns and trends
- **Automated Rules**: Auto-approve certain types of corrections
- **Integration**: Connect with external systems for validation

### Scalability Considerations
- **Database Partitioning**: For large-scale deployments
- **Queue Processing**: Background processing for bulk operations
- **API Rate Limiting**: Prevent abuse of correction endpoints
- **Audit Logging**: Comprehensive logging for compliance

---

## Related Documentation
- [Amenity Usage Tracking](./amenity-usage.md)
- [Paid Amenities Module](./paid-amenities.md)
- [Tenant Management](./tenant.md)
- [User Management](./user-management.md)
- [Table Standards](../table-standards.md)
- [Component Standards](../component-standards.md)
