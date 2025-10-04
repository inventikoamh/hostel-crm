<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TenantDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_profile_id',
        'category',
        'document_type',
        'document_number',
        'description',
        'document_data',
        'request_type',
        'approval_status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'expiry_date',
        'is_required',
        'priority',
        'status',
        'printed_by',
        'printed_at',
        'document_path',
        'uploaded_by_admin',
        'uploaded_at_admin',
        'notes',
    ];

    protected $casts = [
        'document_data' => 'array',
        'printed_at' => 'datetime',
        'uploaded_at_admin' => 'datetime',
        'approved_at' => 'datetime',
        'expiry_date' => 'datetime',
        'is_required' => 'boolean',
    ];

    /**
     * Boot method to auto-generate document number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->document_number)) {
                $model->document_number = self::generateDocumentNumber();
            }
        });
    }

    /**
     * Generate unique document number
     */
    public static function generateDocumentNumber(): string
    {
        $prefix = 'TD';
        $year = date('Y');
        $month = date('m');

        // Get the last document number for this month
        $lastDocument = self::where('document_number', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('document_number', 'desc')
            ->first();

        if ($lastDocument) {
            $lastNumber = (int) substr($lastDocument->document_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the tenant profile that owns this document
     */
    public function tenantProfile(): BelongsTo
    {
        return $this->belongsTo(TenantProfile::class);
    }

    /**
     * Get the user who printed this document
     */
    public function printedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    /**
     * Get the admin user who uploaded the document
     */
    public function uploadedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_admin');
    }

    /**
     * Get the user who approved this document
     */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for different document types
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope for different statuses
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for different approval statuses
     */
    public function scopeWithApprovalStatus($query, $status)
    {
        return $query->where('approval_status', $status);
    }

    /**
     * Scope for required documents
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope for tenant upload requests
     */
    public function scopeTenantUpload($query)
    {
        return $query->where('request_type', 'tenant_upload');
    }

    /**
     * Scope for admin uploads
     */
    public function scopeAdminUpload($query)
    {
        return $query->where('request_type', 'admin_upload');
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'requested' => 'yellow',
            'uploaded' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'expired' => 'orange',
            'archived' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get the status display text
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'requested' => 'Requested',
            'uploaded' => 'Uploaded',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
            'archived' => 'Archived',
            default => 'Unknown'
        };
    }

    /**
     * Get the approval status badge color
     */
    public function getApprovalStatusBadgeColorAttribute(): string
    {
        return match($this->approval_status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the approval status display text
     */
    public function getApprovalStatusDisplayAttribute(): string
    {
        return match($this->approval_status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    /**
     * Get the document type display text
     */
    public function getDocumentTypeDisplayAttribute(): string
    {
        return match($this->document_type) {
            'aadhar_card' => 'Aadhar Card',
            'pan_card' => 'PAN Card',
            'student_id' => 'Student ID',
            'tenant_agreement' => 'Tenant Agreement',
            'lease_agreement' => 'Lease Agreement',
            'rental_agreement' => 'Rental Agreement',
            'maintenance_form' => 'Maintenance Form',
            'identity_proof' => 'Identity Proof',
            'address_proof' => 'Address Proof',
            'income_proof' => 'Income Proof',
            'other' => 'Other Document',
            default => ucwords(str_replace('_', ' ', $this->document_type))
        };
    }

    /**
     * Get the priority display text
     */
    public function getPriorityDisplayAttribute(): string
    {
        return match($this->priority) {
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            default => 'Low'
        };
    }

    /**
     * Get the priority badge color
     */
    public function getPriorityBadgeColorAttribute(): string
    {
        return match($this->priority) {
            1 => 'gray',
            2 => 'yellow',
            3 => 'red',
            default => 'gray'
        };
    }

    /**
     * Check if document is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved' && !empty($this->document_path);
    }

    /**
     * Check if document is uploaded
     */
    public function isUploaded(): bool
    {
        return $this->status === 'uploaded' || $this->status === 'approved';
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if document is required
     */
    public function isRequired(): bool
    {
        return $this->is_required;
    }

    /**
     * Get the document URL
     */
    public function getDocumentUrlAttribute(): ?string
    {
        if ($this->document_path) {
            return asset('storage/' . $this->document_path);
        }
        return null;
    }

    /**
     * Get the tenant's photo URL
     */
    public function getTenantPhotoUrlAttribute(): ?string
    {
        if ($this->tenantProfile && $this->tenantProfile->user && $this->tenantProfile->user->avatar) {
            return asset('storage/' . $this->tenantProfile->user->avatar);
        }
        return null;
    }
}
