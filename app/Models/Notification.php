<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'recipient_email',
        'recipient_name',
        'status',
        'sent_at',
        'error_message',
        'retry_count',
        'scheduled_at',
        'notifiable_type',
        'notifiable_id',
        'created_by'
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Type constants
    const TYPE_TENANT_ADDED = 'tenant_added';
    const TYPE_TENANT_UPDATED = 'tenant_updated';
    const TYPE_ENQUIRY_ADDED = 'enquiry_added';
    const TYPE_INVOICE_CREATED = 'invoice_created';
    const TYPE_INVOICE_SENT = 'invoice_sent';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_PAYMENT_VERIFIED = 'payment_verified';
    const TYPE_AMENITY_USAGE_RECORDED = 'amenity_usage_recorded';
    const TYPE_LEASE_EXPIRING = 'lease_expiring';
    const TYPE_OVERDUE_PAYMENT = 'overdue_payment';

    /**
     * Get the notifiable entity (tenant, enquiry, invoice, etc.)
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created this notification
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for pending notifications
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for sent notifications
     */
    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Scope for failed notifications
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for notifications by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for notifications by recipient
     */
    public function scopeByRecipient($query, $email)
    {
        return $query->where('recipient_email', $email);
    }

    /**
     * Scope for scheduled notifications
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
                    ->where('scheduled_at', '<=', now())
                    ->where('status', self::STATUS_PENDING);
    }

    /**
     * Mark notification as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark notification as failed
     */
    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * Check if notification can be retried
     */
    public function canRetry(int $maxRetries = 3): bool
    {
        return $this->retry_count < $maxRetries && $this->status === self::STATUS_FAILED;
    }

    /**
     * Get formatted status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_SENT => 'bg-green-100 text-green-800',
            self::STATUS_FAILED => 'bg-red-100 text-red-800',
            self::STATUS_CANCELLED => 'bg-gray-100 text-gray-800',
        ];

        $class = $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
        return "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$class}'>" . ucfirst($this->status) . "</span>";
    }

    /**
     * Get formatted type display
     */
    public function getTypeDisplayAttribute(): string
    {
        $types = [
            self::TYPE_TENANT_ADDED => 'Tenant Added',
            self::TYPE_TENANT_UPDATED => 'Tenant Updated',
            self::TYPE_ENQUIRY_ADDED => 'New Enquiry',
            self::TYPE_INVOICE_CREATED => 'Invoice Created',
            self::TYPE_INVOICE_SENT => 'Invoice Sent',
            self::TYPE_PAYMENT_RECEIVED => 'Payment Received',
            self::TYPE_PAYMENT_VERIFIED => 'Payment Verified',
            self::TYPE_AMENITY_USAGE_RECORDED => 'Amenity Usage Recorded',
            self::TYPE_LEASE_EXPIRING => 'Lease Expiring',
            self::TYPE_OVERDUE_PAYMENT => 'Overdue Payment',
        ];

        return $types[$this->type] ?? ucwords(str_replace('_', ' ', $this->type));
    }

    /**
     * Get all available notification types
     */
    public static function getAvailableTypes(): array
    {
        return [
            ['value' => self::TYPE_TENANT_ADDED, 'label' => 'Tenant Added'],
            ['value' => self::TYPE_TENANT_UPDATED, 'label' => 'Tenant Updated'],
            ['value' => self::TYPE_ENQUIRY_ADDED, 'label' => 'New Enquiry'],
            ['value' => self::TYPE_INVOICE_CREATED, 'label' => 'Invoice Created'],
            ['value' => self::TYPE_INVOICE_SENT, 'label' => 'Invoice Sent'],
            ['value' => self::TYPE_PAYMENT_RECEIVED, 'label' => 'Payment Received'],
            ['value' => self::TYPE_PAYMENT_VERIFIED, 'label' => 'Payment Verified'],
            ['value' => self::TYPE_AMENITY_USAGE_RECORDED, 'label' => 'Amenity Usage Recorded'],
            ['value' => self::TYPE_LEASE_EXPIRING, 'label' => 'Lease Expiring'],
            ['value' => self::TYPE_OVERDUE_PAYMENT, 'label' => 'Overdue Payment'],
        ];
    }
}
