<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'enquiry_type',
        'subject',
        'message',
        'status',
        'priority',
        'admin_notes',
        'assigned_to',
        'responded_at',
        'source',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with User (assigned_to)
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes for filtering
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('enquiry_type', $type);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'new' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-star'],
            'in_progress' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
            'resolved' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check-circle'],
            'closed' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-times-circle']
        ];

        return $badges[$this->status] ?? $badges['new'];
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-arrow-down'],
            'medium' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-minus'],
            'high' => ['class' => 'bg-orange-100 text-orange-800', 'icon' => 'fas fa-arrow-up'],
            'urgent' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-exclamation-triangle']
        ];

        return $badges[$this->priority] ?? $badges['medium'];
    }

    public function getEnquiryTypeDisplayAttribute()
    {
        $types = [
            'room_booking' => 'Room Booking',
            'general_info' => 'General Information',
            'pricing' => 'Pricing Inquiry',
            'facilities' => 'Facilities & Amenities',
            'other' => 'Other'
        ];

        return $types[$this->enquiry_type] ?? 'Unknown';
    }

    // Check if enquiry is overdue (more than 24 hours old and still new/in_progress)
    public function getIsOverdueAttribute()
    {
        if (in_array($this->status, ['resolved', 'closed'])) {
            return false;
        }

        return $this->created_at->diffInHours(now()) > 24;
    }
}
