<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class TenantAmenity extends Model
{
    protected $fillable = [
        'tenant_profile_id',
        'paid_amenity_id',
        'status',
        'start_date',
        'end_date',
        'custom_price',
        'custom_schedule',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'custom_price' => 'decimal:2',
        'custom_schedule' => 'array',
    ];

    // Relationships
    public function tenantProfile(): BelongsTo
    {
        return $this->belongsTo(TenantProfile::class);
    }

    public function paidAmenity(): BelongsTo
    {
        return $this->belongsTo(PaidAmenity::class);
    }

    public function usageRecords(): HasMany
    {
        return $this->hasMany(TenantAmenityUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForTenant($query, $tenantProfileId)
    {
        return $query->where('tenant_profile_id', $tenantProfileId);
    }

    public function scopeForAmenity($query, $amenityId)
    {
        return $query->where('paid_amenity_id', $amenityId);
    }

    public function scopeCurrent($query)
    {
        $today = Carbon::today();
        return $query->where('start_date', '<=', $today)
                    ->where(function ($q) use ($today) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $today);
                    });
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', Carbon::today());
    }

    public function scopeMonthly($query)
    {
        return $query->whereHas('paidAmenity', function ($q) {
            $q->where('billing_type', 'monthly');
        });
    }

    public function scopeDaily($query)
    {
        return $query->whereHas('paidAmenity', function ($q) {
            $q->where('billing_type', 'daily');
        });
    }

    // Accessors
    public function getStatusBadgeAttribute(): array
    {
        $badges = [
            'active' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Active'],
            'inactive' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Inactive'],
            'suspended' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Suspended'],
        ];

        return $badges[$this->status] ?? $badges['inactive'];
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->custom_price ?? $this->paidAmenity->price;
    }

    public function getFormattedEffectivePriceAttribute(): string
    {
        $suffix = $this->paidAmenity->billing_type === 'daily' ? '/day' : '/month';
        return '₹' . number_format($this->effective_price, 2) . $suffix;
    }

    public function getIsCurrentAttribute(): bool
    {
        $today = Carbon::today();
        return $this->start_date <= $today &&
               ($this->end_date === null || $this->end_date >= $today);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date && $this->end_date < Carbon::today();
    }

    public function getDurationDaysAttribute(): int
    {
        $endDate = $this->end_date ?? Carbon::today();
        return $this->start_date->diffInDays($endDate) + 1;
    }

    public function getDurationTextAttribute(): string
    {
        if (!$this->end_date) {
            return 'Ongoing since ' . $this->start_date->format('M j, Y');
        }

        if ($this->is_expired) {
            return 'Expired on ' . $this->end_date->format('M j, Y');
        }

        return $this->start_date->format('M j, Y') . ' - ' . $this->end_date->format('M j, Y');
    }

    // Usage tracking methods
    public function getTotalUsageForMonth($year, $month): int
    {
        return $this->usageRecords()
                   ->whereYear('usage_date', $year)
                   ->whereMonth('usage_date', $month)
                   ->sum('quantity');
    }

    public function getTotalAmountForMonth($year, $month): float
    {
        return $this->usageRecords()
                   ->whereYear('usage_date', $year)
                   ->whereMonth('usage_date', $month)
                   ->sum('total_amount');
    }

    public function getUsageForDate($date): ?TenantAmenityUsage
    {
        return $this->usageRecords()
                   ->where('usage_date', $date)
                   ->first();
    }

    public function hasUsageForDate($date): bool
    {
        return $this->usageRecords()
                   ->where('usage_date', $date)
                   ->exists();
    }

    // Billing calculation methods
    public function calculateMonthlyCharge($year, $month): float
    {
        if ($this->paidAmenity->billing_type === 'monthly') {
            // For monthly billing, charge full amount if active during the month
            $monthStart = Carbon::create($year, $month, 1);
            $monthEnd = $monthStart->copy()->endOfMonth();

            // Check if the amenity was active during any part of the month
            if ($this->start_date <= $monthEnd &&
                ($this->end_date === null || $this->end_date >= $monthStart)) {
                return $this->effective_price;
            }

            return 0;
        } else {
            // For daily billing, sum up all usage for the month
            return $this->getTotalAmountForMonth($year, $month);
        }
    }

    public function getMonthlyBillingSummary($year, $month): array
    {
        $summary = [
            'amenity_name' => $this->paidAmenity->name,
            'billing_type' => $this->paidAmenity->billing_type,
            'unit_price' => $this->effective_price,
            'total_amount' => 0,
            'usage_details' => []
        ];

        if ($this->paidAmenity->billing_type === 'monthly') {
            $summary['total_amount'] = $this->calculateMonthlyCharge($year, $month);
            $summary['usage_details'] = ['type' => 'monthly', 'amount' => $summary['total_amount']];
        } else {
            $usageRecords = $this->usageRecords()
                               ->whereYear('usage_date', $year)
                               ->whereMonth('usage_date', $month)
                               ->orderBy('usage_date')
                               ->get();

            $summary['total_amount'] = $usageRecords->sum('total_amount');
            $summary['usage_details'] = [
                'type' => 'daily',
                'total_days' => $usageRecords->count(),
                'total_quantity' => $usageRecords->sum('quantity'),
                'records' => $usageRecords->map(function ($record) {
                    return [
                        'date' => $record->usage_date->format('M j, Y'),
                        'quantity' => $record->quantity,
                        'amount' => $record->total_amount
                    ];
                })->toArray()
            ];
        }

        return $summary;
    }

    // Helper methods
    public function recordUsage($date, $quantity = 1, $notes = null, $recordedBy = null): TenantAmenityUsage
    {
        $unitPrice = $this->effective_price;
        $totalAmount = $unitPrice * $quantity;

        return $this->usageRecords()->updateOrCreate(
            ['usage_date' => $date],
            [
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'notes' => $notes,
                'recorded_by' => $recordedBy ?? auth()->id(),
            ]
        );
    }

    public function suspend($reason = null): bool
    {
        return $this->update([
            'status' => 'suspended',
            'notes' => $reason ? "Suspended: {$reason}" : $this->notes
        ]);
    }

    public function reactivate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    public function terminate($endDate = null): bool
    {
        return $this->update([
            'status' => 'inactive',
            'end_date' => $endDate ?? Carbon::today()
        ]);
    }
}
