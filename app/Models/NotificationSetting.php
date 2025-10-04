<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_type',
        'name',
        'description',
        'enabled',
        'recipient_type',
        'recipient_email',
        'email_template',
        'conditions',
        'priority',
        'send_immediately',
        'delay_minutes'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'email_template' => 'array',
        'conditions' => 'array',
        'send_immediately' => 'boolean',
    ];

    // Recipient type constants
    const RECIPIENT_ADMIN = 'admin';
    const RECIPIENT_TENANT = 'tenant';
    const RECIPIENT_SPECIFIC_EMAIL = 'specific_email';

    // Priority constants
    const PRIORITY_HIGH = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_LOW = 3;

    /**
     * Scope for enabled settings
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope for specific notification type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Scope for recipient type
     */
    public function scopeByRecipientType($query, $recipientType)
    {
        return $query->where('recipient_type', $recipientType);
    }

    /**
     * Scope for high priority
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', self::PRIORITY_HIGH);
    }

    /**
     * Get recipient email for this setting
     */
    public function getRecipientEmail(): ?string
    {
        switch ($this->recipient_type) {
            case self::RECIPIENT_ADMIN:
                return config('mail.admin_email', 'admin@hostel.com');
            case self::RECIPIENT_SPECIFIC_EMAIL:
                return $this->recipient_email;
            default:
                return null;
        }
    }

    /**
     * Check if this setting should send immediately
     */
    public function shouldSendImmediately(): bool
    {
        return $this->send_immediately && $this->delay_minutes == 0;
    }

    /**
     * Get delay in minutes
     */
    public function getDelayMinutes(): int
    {
        return $this->delay_minutes ?? 0;
    }

    /**
     * Get formatted priority badge
     */
    public function getPriorityBadgeAttribute(): string
    {
        $badges = [
            self::PRIORITY_HIGH => 'bg-red-100 text-red-800',
            self::PRIORITY_MEDIUM => 'bg-yellow-100 text-yellow-800',
            self::PRIORITY_LOW => 'bg-green-100 text-green-800',
        ];

        $labels = [
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_LOW => 'Low',
        ];

        $class = $badges[$this->priority] ?? 'bg-gray-100 text-gray-800';
        $label = $labels[$this->priority] ?? 'Unknown';

        return "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$class}'>" . $label . "</span>";
    }

    /**
     * Get formatted recipient type display
     */
    public function getRecipientTypeDisplayAttribute(): string
    {
        $types = [
            self::RECIPIENT_ADMIN => 'Admin',
            self::RECIPIENT_TENANT => 'Tenant',
            self::RECIPIENT_SPECIFIC_EMAIL => 'Specific Email',
        ];

        return $types[$this->recipient_type] ?? ucwords(str_replace('_', ' ', $this->recipient_type));
    }

    /**
     * Get all available recipient types
     */
    public static function getRecipientTypes(): array
    {
        return [
            ['value' => self::RECIPIENT_ADMIN, 'label' => 'Admin'],
            ['value' => self::RECIPIENT_TENANT, 'label' => 'Tenant'],
            ['value' => self::RECIPIENT_SPECIFIC_EMAIL, 'label' => 'Specific Email'],
        ];
    }

    /**
     * Get all available priorities
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_LOW => 'Low',
        ];
    }

    /**
     * Get default email template for notification type
     */
    public function getDefaultTemplate(): array
    {
        $templates = [
            'tenant_added' => [
                'subject' => 'Welcome to {{hostel_name}} - Tenant Registration Confirmed',
                'greeting' => 'Dear {{tenant_name}},',
                'body' => 'Welcome to {{hostel_name}}! Your tenant registration has been successfully completed.',
                'footer' => 'Thank you for choosing {{hostel_name}}. If you have any questions, please contact us.'
            ],
            'enquiry_added' => [
                'subject' => 'New Enquiry Received - {{enquiry_subject}}',
                'greeting' => 'Hello Admin,',
                'body' => 'A new enquiry has been received from {{enquirer_name}} regarding {{enquiry_subject}}.',
                'footer' => 'Please review and respond to this enquiry as soon as possible.'
            ],
            'invoice_created' => [
                'subject' => 'Invoice Generated - {{invoice_number}}',
                'greeting' => 'Dear {{tenant_name}},',
                'body' => 'Your invoice {{invoice_number}} has been generated for the amount of {{invoice_amount}}.',
                'footer' => 'Please make payment by the due date to avoid any late fees.'
            ],
            'payment_received' => [
                'subject' => 'Payment Received - {{payment_number}}',
                'greeting' => 'Dear {{tenant_name}},',
                'body' => 'We have received your payment of {{payment_amount}} for invoice {{invoice_number}}.',
                'footer' => 'Thank you for your prompt payment.'
            ]
        ];

        return $templates[$this->notification_type] ?? [
            'subject' => 'Notification from {{hostel_name}}',
            'greeting' => 'Hello,',
            'body' => 'This is a notification from {{hostel_name}}.',
            'footer' => 'Thank you for using our services.'
        ];
    }

    /**
     * Get email template with fallback to default
     */
    public function getEmailTemplate(): array
    {
        return $this->email_template ?? $this->getDefaultTemplate();
    }

}
