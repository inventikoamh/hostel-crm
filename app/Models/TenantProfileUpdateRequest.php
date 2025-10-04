<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantProfileUpdateRequest extends Model
{
    protected $fillable = [
        'tenant_profile_id',
        'requested_changes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'requested_changes' => 'array',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function tenantProfile(): BelongsTo
    {
        return $this->belongsTo(TenantProfile::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
            'approved' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check'],
            'rejected' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-times'],
            default => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-question']
        };
    }

    // Methods
    public function approve($adminId, $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);

        // Apply the changes to the tenant profile
        $this->applyChanges();
    }

    public function reject($adminId, $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    private function applyChanges(): void
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
}
