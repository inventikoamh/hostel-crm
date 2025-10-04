<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Bed extends Model
{
    protected $fillable = [
        'room_id',
        'bed_number',
        'bed_type',
        'status',
        'tenant_id',
        'occupied_from',
        'occupied_until',
        'monthly_rent',
        'notes',
        'is_active',
        'coordinates',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'monthly_rent' => 'decimal:2',
        'occupied_from' => 'date',
        'occupied_until' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id'); // Assuming tenants are users
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeByRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'available' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-bed'],
            'occupied' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-user'],
            'maintenance' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-tools'],
            'reserved' => ['class' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-bookmark']
        ];

        return $badges[$this->status] ?? $badges['available'];
    }

    public function getBedTypeDisplayAttribute()
    {
        $types = [
            'single' => 'Single Bed',
            'double' => 'Double Bed',
            'bunk_top' => 'Bunk Bed (Top)',
            'bunk_bottom' => 'Bunk Bed (Bottom)'
        ];

        return $types[$this->bed_type] ?? ucfirst($this->bed_type);
    }

    public function getFullBedNumberAttribute()
    {
        return "{$this->room->full_room_number} - Bed {$this->bed_number}";
    }

    public function getCurrentRentAttribute()
    {
        return $this->monthly_rent ?? $this->room->rent_per_bed;
    }

    public function getIsOverdueAttribute()
    {
        if (!$this->occupied_until || $this->status !== 'occupied') {
            return false;
        }

        return Carbon::now()->greaterThan($this->occupied_until);
    }

    public function getDaysUntilCheckoutAttribute()
    {
        if (!$this->occupied_until || $this->status !== 'occupied') {
            return null;
        }

        return Carbon::now()->diffInDays($this->occupied_until, false);
    }

    public function getOccupancyDurationAttribute()
    {
        if (!$this->occupied_from || $this->status !== 'occupied') {
            return null;
        }

        return Carbon::parse($this->occupied_from)->diffInDays(Carbon::now());
    }

    // Helper methods
    public function assignTenant($tenantId, $checkInDate = null, $checkOutDate = null, $rent = null)
    {
        $this->update([
            'tenant_id' => $tenantId,
            'status' => 'occupied',
            'occupied_from' => $checkInDate ?? Carbon::now(),
            'occupied_until' => $checkOutDate,
            'monthly_rent' => $rent
        ]);

        // Update room status
        $this->room->updateStatus();

        return $this;
    }

    public function releaseTenant()
    {
        $this->update([
            'tenant_id' => null,
            'status' => 'available',
            'occupied_from' => null,
            'occupied_until' => null,
            'monthly_rent' => null
        ]);

        // Update room status
        $this->room->updateStatus();

        return $this;
    }

    public function reserve($checkInDate = null, $checkOutDate = null)
    {
        $this->update([
            'status' => 'reserved',
            'occupied_from' => $checkInDate,
            'occupied_until' => $checkOutDate
        ]);

        // Update room status
        $this->room->updateStatus();

        return $this;
    }

    public function setMaintenance($notes = null)
    {
        $this->update([
            'status' => 'maintenance',
            'notes' => $notes
        ]);

        // Update room status
        $this->room->updateStatus();

        return $this;
    }

}
