<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hostel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'website',
        'total_rooms',
        'total_beds',
        'rent_per_bed',
        'amenities',
        'images',
        'status',
        'manager_name',
        'manager_phone',
        'manager_email',
        'rules',
        'check_in_time',
        'check_out_time',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'rent_per_bed' => 'decimal:2',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
    ];

    // Accessor for full address
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}";
    }

    // Accessor for formatted rent
    public function getFormattedRentAttribute(): string
    {
        return '$' . number_format((float) $this->rent_per_bed, 2);
    }

    // Accessor for occupancy rate (placeholder for future implementation)
    public function getOccupancyRateAttribute(): float
    {
        // This would be calculated based on actual tenant data
        return 0.0;
    }

    // Scope for active hostels
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for inactive hostels
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Scope for maintenance hostels
    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // Relationships
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function beds()
    {
        return $this->hasManyThrough(Bed::class, Room::class);
    }

    // Enhanced accessors with room data
    public function getActualOccupancyRateAttribute(): float
    {
        $totalBeds = $this->beds()->count();
        if ($totalBeds == 0) return 0.0;

        $occupiedBeds = $this->beds()->where('beds.status', 'occupied')->count();
        return round(($occupiedBeds / $totalBeds) * 100, 1);
    }

    public function getAvailableBedsCountAttribute(): int
    {
        return $this->beds()->where('beds.status', 'available')->count();
    }

    public function getOccupiedBedsCountAttribute(): int
    {
        return $this->beds()->where('beds.status', 'occupied')->count();
    }

    public function getFloorsAttribute(): array
    {
        return $this->rooms()->distinct('floor')->pluck('floor')->sort()->values()->toArray();
    }

    public function getRoomsByFloorAttribute(): array
    {
        return $this->rooms()
            ->with(['beds.tenant'])
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get()
            ->groupBy('floor')
            ->toArray();
    }
}
