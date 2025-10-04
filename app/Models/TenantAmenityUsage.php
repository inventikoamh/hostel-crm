<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantAmenityUsage extends Model
{
    protected $table = 'tenant_amenity_usage';

    protected $fillable = [
        'tenant_amenity_id',
        'usage_date',
        'quantity',
        'unit_price',
        'total_amount',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function tenantAmenity(): BelongsTo
    {
        return $this->belongsTo(TenantAmenity::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('usage_date', $date);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('usage_date', $year)
                    ->whereMonth('usage_date', $month);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('usage_date', [$startDate, $endDate]);
    }

    public function scopeForTenant($query, $tenantProfileId)
    {
        return $query->whereHas('tenantAmenity', function ($q) use ($tenantProfileId) {
            $q->where('tenant_profile_id', $tenantProfileId);
        });
    }

    public function scopeForAmenity($query, $amenityId)
    {
        return $query->whereHas('tenantAmenity', function ($q) use ($amenityId) {
            $q->where('paid_amenity_id', $amenityId);
        });
    }

    // Accessors
    public function getFormattedTotalAmountAttribute(): string
    {
        return '₹' . number_format((float) $this->total_amount, 2);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '₹' . number_format((float) $this->unit_price, 2);
    }

    public function getUsageSummaryAttribute(): string
    {
        $quantity = $this->quantity > 1 ? "{$this->quantity}x " : '';
        return $quantity . $this->tenantAmenity->paidAmenity->name;
    }

    // Helper methods
    public function updateQuantity($newQuantity): bool
    {
        $newTotal = $this->unit_price * $newQuantity;

        return $this->update([
            'quantity' => $newQuantity,
            'total_amount' => $newTotal
        ]);
    }

    public function updatePrice($newUnitPrice): bool
    {
        $newTotal = $newUnitPrice * $this->quantity;

        return $this->update([
            'unit_price' => $newUnitPrice,
            'total_amount' => $newTotal
        ]);
    }

    public function recalculateTotal(): bool
    {
        $newTotal = $this->unit_price * $this->quantity;

        return $this->update(['total_amount' => $newTotal]);
    }
}
