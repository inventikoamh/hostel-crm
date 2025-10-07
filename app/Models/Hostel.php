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
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
    ];

    // Accessor for full address
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}";
    }

    // Accessor for total rooms (calculated from actual rooms)
    public function getTotalRoomsAttribute(): int
    {
        return $this->rooms()->count();
    }

    // Accessor for total beds (calculated from actual beds)
    public function getTotalBedsAttribute(): int
    {
        return $this->beds()->count();
    }

    // Accessor for average rent per bed (calculated from actual bed data)
    public function getRentPerBedAttribute(): float
    {
        $beds = $this->beds()->whereNotNull('monthly_rent')->get();
        if ($beds->isEmpty()) {
            // If no beds have monthly_rent, try to get from room rent_per_bed
            $rooms = $this->rooms()->whereNotNull('rent_per_bed')->get();
            if ($rooms->isEmpty()) return 0.0;
            return $rooms->avg('rent_per_bed');
        }

        return $beds->avg('monthly_rent');
    }

    // Accessor for formatted rent
    public function getFormattedRentAttribute(): string
    {
        return '$' . number_format($this->rent_per_bed, 2);
    }

    // Accessor for occupancy rate (calculated from BedAssignment system)
    public function getOccupancyRateAttribute(): float
    {
        $totalBeds = $this->beds()->count();
        if ($totalBeds == 0) return 0.0;

        // Count beds with active assignments
        $occupiedBeds = $this->beds()->whereHas('assignments', function($query) {
            $query->where('status', 'active');
        })->count();

        return round(($occupiedBeds / $totalBeds) * 100, 1);
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

    // Enhanced accessors with BedAssignment system
    public function getActualOccupancyRateAttribute(): float
    {
        $totalBeds = $this->beds()->count();
        if ($totalBeds == 0) return 0.0;

        $occupiedBeds = $this->beds()->whereHas('assignments', function($query) {
            $query->where('status', 'active');
        })->count();

        return round(($occupiedBeds / $totalBeds) * 100, 1);
    }

    public function getAvailableBedsCountAttribute(): int
    {
        return $this->beds()->whereDoesntHave('assignments', function($query) {
            $query->whereIn('status', ['active', 'reserved']);
        })->where('beds.status', 'available')->count();
    }

    public function getOccupiedBedsCountAttribute(): int
    {
        return $this->beds()->whereHas('assignments', function($query) {
            $query->where('status', 'active');
        })->count();
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
