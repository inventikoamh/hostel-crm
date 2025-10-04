# Notification System Module

## Overview

The Notification System is a comprehensive email notification management system that provides automated email notifications for various events in the Hostel CRM system. It includes configurable templates, recipient management, and delivery tracking.

## Features

### Core Functionality
- **Automated Notifications**: Send emails automatically when specific events occur
- **Configurable Templates**: Customizable email templates with dynamic content
- **Recipient Management**: Support for admin, tenant, and specific email recipients
- **Delivery Tracking**: Track notification status (sent, failed, pending, scheduled)
- **Retry Mechanism**: Automatic retry for failed notifications
- **Scheduled Delivery**: Support for delayed notifications
- **Template Management**: CRUD operations for notification settings

### Notification Types
- **Tenant Added**: Welcome email when new tenant is registered
- **Enquiry Received (Admin)**: Notify admin of new enquiries
- **Enquiry Received (Tenant)**: Confirmation email to enquirers
- **Invoice Created**: Notify tenants of new invoices
- **Payment Received**: Confirm payment receipt to tenants
- **Payment Verified**: Confirm payment verification to tenants
- **Lease Expiring**: Remind tenants of upcoming lease expiry
- **Overdue Invoice**: Remind tenants of overdue payments

## Database Structure

### Tables

#### `notifications`
Stores all notification records and delivery status.

```sql
- id (bigint, primary key)
- type (varchar) - Notification type identifier
- title (varchar) - Email subject/title
- message (text) - Email body content
- data (json) - Additional notification data
- recipient_email (varchar) - Recipient email address
- recipient_name (varchar) - Recipient name
- status (varchar) - pending, sent, failed, scheduled, cancelled
- sent_at (timestamp) - When notification was sent
- error_message (text) - Error details if failed
- retry_count (int) - Number of retry attempts
- scheduled_at (timestamp) - When to send (for delayed notifications)
- notifiable_type (varchar) - Related model type
- notifiable_id (bigint) - Related model ID
- created_by (bigint) - User who triggered notification
- created_at (timestamp)
- updated_at (timestamp)
```

#### `notification_settings`
Stores configurable notification preferences and templates.

```sql
- id (bigint, primary key)
- notification_type (varchar, unique) - Type identifier
- name (varchar) - Human-readable name
- description (text) - Description of when notification is sent
- enabled (boolean) - Whether notification is active
- recipient_type (varchar) - admin, tenant, specific_email
- recipient_email (varchar) - For specific_email type
- email_template (json) - Template configuration
- conditions (json) - Conditional sending rules
- priority (int) - 1=high, 2=medium, 3=low
- send_immediately (boolean) - Send immediately or schedule
- delay_minutes (int) - Delay before sending
- created_at (timestamp)
- updated_at (timestamp)
```

## Models

### Notification Model
```php
class Notification extends Model
{
    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_CANCELLED = 'cancelled';

    // Type constants
    const TYPE_TENANT_ADDED = 'tenant_added';
    const TYPE_ENQUIRY_RECEIVED_ADMIN = 'enquiry_received_admin';
    const TYPE_ENQUIRY_RECEIVED_TENANT = 'enquiry_received_tenant';
    const TYPE_INVOICE_CREATED = 'invoice_created';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_PAYMENT_VERIFIED = 'payment_verified';
    const TYPE_LEASE_EXPIRING = 'lease_expiring';
    const TYPE_OVERDUE_INVOICE = 'overdue_invoice';

    // Relationships
    public function notifiable(): MorphTo
    public function createdBy(): BelongsTo

    // Scopes
    public function scopePending($query)
    public function scopeSent($query)
    public function scopeFailed($query)
    public function scopeByType($query, $type)
    public function scopeByRecipient($query, $email)
    public function scopeScheduled($query)

    // Methods
    public function markAsSent(): void
    public function markAsFailed(string $errorMessage = null): void
    public function canRetry(int $maxRetries = 3): bool
    public function getStatusBadgeAttribute(): string
    public function getTypeDisplayAttribute(): string
}
```

### NotificationSetting Model
```php
class NotificationSetting extends Model
{
    // Relationships
    public function notifications(): HasMany

    // Scopes
    public function scopeEnabled($query)
    public function scopeByType($query, $type)

    // Methods
    public function shouldSendImmediately(): bool
    public function getDelayMinutes(): int
    public function getEmailTemplate(): array
    public function getDefaultRecipientEmail(): string
}
```

## Services

### NotificationService
Core service for handling notification logic and email delivery.

```php
class NotificationService
{
    // Main methods
    public function sendNotification(string $type, $notifiable, array $data = []): bool
    public function sendEmail(Notification $notification, NotificationSetting $setting): bool
    public function processScheduledNotifications(): int
    public function retryFailedNotifications(int $maxRetries = 3): int

    // Specific notification methods
    public function sendTenantAddedNotification(TenantProfile $tenant): bool
    public function sendEnquiryAddedNotification($enquiry): bool
    public function sendInvoiceCreatedNotification($invoice): bool
    public function sendPaymentReceivedNotification($payment): bool

    // Utility methods
    public function getStatistics(): array
    private function createNotification(...): Notification
    private function processTemplateData(...): array
    private function scheduleNotification(...): void
}
```

## Controllers

### NotificationController
Handles notification management and settings.

```php
class NotificationController extends Controller
{
    // Notification management
    public function index(Request $request) // List all notifications
    public function show(Notification $notification) // View notification details
    public function statistics() // Get notification statistics
    public function processScheduled() // Process scheduled notifications
    public function retryFailed() // Retry failed notifications
    public function retry(Notification $notification) // Retry specific notification
    public function test(Request $request) // Send test notification

    // Settings management
    public function settings(Request $request) // List notification settings
    public function createSetting() // Show create form
    public function storeSetting(Request $request) // Store new setting
    public function editSetting(NotificationSetting $setting) // Show edit form
    public function updateSetting(Request $request, NotificationSetting $setting) // Update setting
    public function destroySetting(NotificationSetting $setting) // Delete setting
    public function toggleSetting(NotificationSetting $setting) // Toggle enabled status
}
```

## Views

### Notification Management
- **`notifications/index.blade.php`** - List all notifications with filters and actions
- **`notifications/show.blade.php`** - View individual notification details
- **`notifications/settings.blade.php`** - Manage notification settings
- **`notifications/create-setting.blade.php`** - Create new notification setting
- **`notifications/edit-setting.blade.php`** - Edit notification setting

### Email Templates
- **`emails/notification.blade.php`** - Main email template with responsive design

### Components
- **`components/status-badge.blade.php`** - Status display component

## Routes

```php
// Notification Routes (Authentication Required)
Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
    Route::get('/statistics', [NotificationController::class, 'statistics'])->name('statistics');
    Route::post('/process-scheduled', [NotificationController::class, 'processScheduled'])->name('process-scheduled');
    Route::post('/retry-failed', [NotificationController::class, 'retryFailed'])->name('retry-failed');
    Route::post('/test', [NotificationController::class, 'test'])->name('test');
    Route::post('/{notification}/retry', [NotificationController::class, 'retry'])->name('retry');
    
    // Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [NotificationController::class, 'settings'])->name('index');
        Route::get('/create', [NotificationController::class, 'createSetting'])->name('create');
        Route::post('/', [NotificationController::class, 'storeSetting'])->name('store');
        Route::get('/{notificationSetting}/edit', [NotificationController::class, 'editSetting'])->name('edit');
        Route::put('/{notificationSetting}', [NotificationController::class, 'updateSetting'])->name('update');
        Route::delete('/{notificationSetting}', [NotificationController::class, 'destroySetting'])->name('destroy');
        Route::post('/{notificationSetting}/toggle', [NotificationController::class, 'toggleSetting'])->name('toggle');
    });
});
```

## Integration

### Automatic Notifications
The notification system is integrated with existing modules:

#### TenantController
- Sends welcome email when new tenant is added
- Includes tenant details, room information, and rent details

#### EnquiryController
- Notifies admin when new enquiry is received
- Sends confirmation email to the enquirer

#### InvoiceController
- Notifies tenant when new invoice is created
- Includes invoice details and payment information

#### PaymentController
- Confirms payment receipt to tenant
- Notifies tenant when payment is verified

### Manual Notifications
- Test notifications can be sent through the admin interface
- Bulk operations for processing scheduled notifications
- Retry mechanism for failed notifications

## Configuration

### Default Settings
The system comes with pre-configured notification settings for common events:

1. **Tenant Added** - Welcome email to new tenants
2. **Enquiry Received (Admin)** - Admin notification for new enquiries
3. **Enquiry Received (Tenant)** - Confirmation to enquirers
4. **Invoice Created** - Invoice notification to tenants
5. **Payment Received** - Payment confirmation to tenants
6. **Payment Verified** - Payment verification confirmation
7. **Lease Expiring** - Lease expiry reminders
8. **Overdue Invoice** - Overdue payment reminders

### Email Templates
Templates support dynamic content with placeholders:
- `{tenant_name}` - Tenant's name
- `{hostel_name}` - Hostel name
- `{room_number}` - Room number
- `{bed_number}` - Bed number
- `{monthly_rent}` - Monthly rent amount
- `{invoice_number}` - Invoice number
- `{total_amount}` - Invoice total
- `{due_date}` - Payment due date
- `{payment_amount}` - Payment amount
- `{enquiry_subject}` - Enquiry subject
- `{enquiry_message}` - Enquiry message

## Usage Examples

### Sending a Notification
```php
// In a controller
$this->notificationService->sendNotification('tenant_added', $tenantProfile, [
    'subject' => 'Welcome to Our Hostel!',
    'heading' => 'Welcome to Our Hostel!',
    'body' => 'Dear {tenant_name}, welcome to {hostel_name}!',
    'greeting' => $tenant->name,
    'action_url' => route('tenants.show', $tenant->id),
    'action_text' => 'View Your Profile',
    'badge_text' => 'New Tenant',
    'badge_type' => 'success',
]);
```

### Creating a Custom Setting
```php
NotificationSetting::create([
    'notification_type' => 'custom_event',
    'name' => 'Custom Event Notification',
    'description' => 'Notification for custom events',
    'enabled' => true,
    'recipient_type' => 'admin',
    'send_immediately' => true,
    'email_template' => [
        'subject' => 'Custom Event Occurred',
        'body' => 'A custom event has occurred: {event_details}',
    ],
]);
```

## Testing

### Test Notifications
Use the test functionality in the admin interface:
1. Go to `/notifications`
2. Click "Test Notification"
3. Fill in test details
4. Send test email

### Manual Testing
```php
// Create test notification
$notification = Notification::create([
    'type' => 'test',
    'title' => 'Test Notification',
    'message' => 'This is a test',
    'data' => ['subject' => 'Test Subject'],
    'recipient_email' => 'test@example.com',
    'status' => 'sent',
    'sent_at' => now(),
    'notifiable_type' => 'App\Models\User',
    'notifiable_id' => 1,
]);
```

## Maintenance

### Processing Scheduled Notifications
Run the scheduled notification processor:
```bash
php artisan notifications:process-scheduled
```

### Retry Failed Notifications
```php
$notificationService = app(NotificationService::class);
$retried = $notificationService->retryFailedNotifications();
```

### Cleanup Old Notifications
```php
// Delete notifications older than 90 days
Notification::where('created_at', '<', now()->subDays(90))->delete();
```

## Security Considerations

- All notification routes require authentication
- Email templates are sanitized to prevent XSS
- Recipient emails are validated
- Failed notifications are logged for debugging
- Sensitive data is not included in notification logs

## Performance Considerations

- Notifications are queued for better performance
- Failed notifications have retry limits
- Old notifications can be archived or deleted
- Email sending is handled asynchronously when possible
- Template processing is optimized for speed

## Troubleshooting

### Common Issues

1. **Notifications not sending**
   - Check SMTP configuration
   - Verify notification settings are enabled
   - Check recipient email addresses

2. **Templates not rendering**
   - Verify template syntax
   - Check placeholder names
   - Ensure data is passed correctly

3. **Failed notifications**
   - Check error messages in notification details
   - Verify email addresses are valid
   - Check SMTP server status

### Debug Mode
Enable debug mode to see detailed error messages:
```php
// In .env
MAIL_DEBUG=true
LOG_LEVEL=debug
```

## Future Enhancements

- SMS notifications support
- Push notifications for mobile apps
- Advanced template editor with WYSIWYG
- Notification analytics and reporting
- Bulk notification sending
- Custom notification channels
- Integration with external services (Slack, Discord, etc.)
