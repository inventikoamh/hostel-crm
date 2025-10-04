<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaidAmenity extends Model
{
    protected $fillable = [
        'name',
        'description',
        'billing_type',
        'price',
        'category',
        'is_active',
        'availability_schedule',
        'max_usage_per_day',
        'terms_conditions',
        'icon',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'availability_schedule' => 'array',
        'max_usage_per_day' => 'integer',
    ];

    // Relationships
    public function tenantAmenities(): HasMany
    {
        return $this->hasMany(TenantAmenity::class);
    }

    public function activeTenantAmenities(): HasMany
    {
        return $this->hasMany(TenantAmenity::class)->where('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByBillingType($query, $billingType)
    {
        return $query->where('billing_type', $billingType);
    }

    public function scopeMonthly($query)
    {
        return $query->where('billing_type', 'monthly');
    }

    public function scopeDaily($query)
    {
        return $query->where('billing_type', 'daily');
    }

    // Accessors
    public function getCategoryDisplayAttribute(): string
    {
        $categories = [
            'food' => 'Food & Meals',
            'cleaning' => 'Cleaning Services',
            'laundry' => 'Laundry Services',
            'utilities' => 'Utilities',
            'services' => 'General Services',
            'other' => 'Other'
        ];

        return $categories[$this->category] ?? 'Other';
    }

    public function getBillingTypeDisplayAttribute(): string
    {
        return ucfirst($this->billing_type);
    }

    public function getFormattedPriceAttribute(): string
    {
        $suffix = $this->billing_type === 'daily' ? '/day' : '/month';
        return '₹' . number_format((float) $this->price, 2) . $suffix;
    }

    public function getIconClassAttribute(): string
    {
        if ($this->icon) {
            return $this->icon;
        }

        // Default icons based on category
        $defaultIcons = [
            'food' => 'fas fa-utensils',
            'cleaning' => 'fas fa-broom',
            'laundry' => 'fas fa-tshirt',
            'utilities' => 'fas fa-plug',
            'services' => 'fas fa-concierge-bell',
            'other' => 'fas fa-star'
        ];

        return $defaultIcons[$this->category] ?? 'fas fa-star';
    }

    public function getStatusBadgeAttribute(): array
    {
        return $this->is_active
            ? ['class' => 'bg-green-100 text-green-800', 'text' => 'Active']
            : ['class' => 'bg-red-100 text-red-800', 'text' => 'Inactive'];
    }

    public function getActiveTenantCountAttribute(): int
    {
        return $this->activeTenantAmenities()->count();
    }

    // Helper methods
    public function isAvailableOnDay($dayOfWeek): bool
    {
        if (!$this->availability_schedule) {
            return true; // Available all days if no schedule set
        }

        return in_array($dayOfWeek, $this->availability_schedule['days'] ?? []);
    }

    public function getAvailabilityText(): string
    {
        if (!$this->availability_schedule) {
            return 'Available daily';
        }

        $days = $this->availability_schedule['days'] ?? [];
        if (empty($days)) {
            return 'No availability set';
        }

        $dayNames = [
            0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
            4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'
        ];

        $availableDays = array_map(fn($day) => $dayNames[$day] ?? $day, $days);

        if (count($availableDays) === 7) {
            return 'Available daily';
        }

        return 'Available on: ' . implode(', ', $availableDays);
    }

    public function canBeUsedToday(): bool
    {
        return $this->is_active && $this->isAvailableOnDay(now()->dayOfWeek);
    }
}
