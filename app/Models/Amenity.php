<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scope for active amenities
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered amenities
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Relationship with hostels (many-to-many)
    public function hostels()
    {
        return $this->belongsToMany(Hostel::class, 'hostel_amenities');
    }
}
