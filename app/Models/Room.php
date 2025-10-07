<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'hostel_id',
        'room_number',
        'room_type',
        'floor',
        'capacity',
        'rent_per_bed',
        'status',
        'description',
        'amenities',
        'area_sqft',
        'has_attached_bathroom',
        'has_balcony',
        'has_ac',
        'is_active',
        'coordinates',
    ];

    protected $casts = [
        'amenities' => 'array',
        'coordinates' => 'array',
        'rent_per_bed' => 'decimal:2',
        'area_sqft' => 'decimal:2',
        'has_attached_bathroom' => 'boolean',
        'has_balcony' => 'boolean',
        'has_ac' => 'boolean',
        'is_active' => 'boolean',
        'floor' => 'integer',
        'capacity' => 'integer',
    ];

    // Relationships
    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
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

    public function scopeByFloor($query, $floor)
    {
        return $query->where('floor', $floor);
    }

    public function scopeByHostel($query, $hostelId)
    {
        return $query->where('hostel_id', $hostelId);
    }

    // Accessors using BedAssignment system
    public function getOccupiedBedsCountAttribute()
    {
        return $this->beds()->whereHas('assignments', function($query) {
            $query->where('status', 'active');
        })->count();
    }

    public function getAvailableBedsCountAttribute()
    {
        return $this->beds()->whereDoesntHave('assignments', function($query) {
            $query->whereIn('status', ['active', 'reserved']);
        })->where('beds.status', 'available')->count();
    }

    public function getOccupancyRateAttribute()
    {
        if ($this->capacity == 0) return 0;
        return round(($this->occupied_beds_count / $this->capacity) * 100, 1);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'available' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check-circle'],
            'occupied' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-users'],
            'maintenance' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-tools'],
            'reserved' => ['class' => 'bg-purple-100 text-purple-800', 'icon' => 'fas fa-bookmark']
        ];

        return $badges[$this->status] ?? $badges['available'];
    }

    public function getRoomTypeDisplayAttribute()
    {
        $types = [
            'single' => 'Single Room',
            'double' => 'Double Room',
            'triple' => 'Triple Room',
            'dormitory' => 'Dormitory',
            'suite' => 'Suite',
            'studio' => 'Studio'
        ];

        return $types[$this->room_type] ?? ucfirst($this->room_type);
    }

    public function getFullRoomNumberAttribute()
    {
        return "Floor {$this->floor} - Room {$this->room_number}";
    }

    // Helper methods
    public function updateStatus()
    {
        $occupiedBeds = $this->occupied_beds_count;
        $totalBeds = $this->capacity;

        if ($occupiedBeds == 0) {
            $this->status = 'available';
        } elseif ($occupiedBeds == $totalBeds) {
            $this->status = 'occupied';
        } else {
            $this->status = 'available'; // Partially occupied rooms are still available
        }

        $this->save();
    }

    public function canAccommodate($bedsNeeded = 1)
    {
        return $this->available_beds_count >= $bedsNeeded && $this->status !== 'maintenance';
    }
}
