<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'tenant_profile_id',
        'type',
        'status',
        'invoice_date',
        'due_date',
        'period_start',
        'period_end',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'notes',
        'terms_conditions',
        'metadata',
        'paid_at',
        'created_by'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    // Relationships
    public function tenantProfile(): BelongsTo
    {
        return $this->belongsTo(TenantProfile::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'sent']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'sent')
                    ->where('due_date', '<', now());
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_profile_id', $tenantId);
    }

    // Accessors
    public function getFormattedTotalAmountAttribute(): string
    {
        return '₹' . number_format((float) $this->total_amount, 2);
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return '₹' . number_format((float) $this->paid_amount, 2);
    }

    public function getFormattedBalanceAmountAttribute(): string
    {
        return '₹' . number_format((float) $this->balance_amount, 2);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Draft'],
            'sent' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Sent'],
            'paid' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Paid'],
            'overdue' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Overdue'],
            'cancelled' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Cancelled'],
            default => ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($this->status)]
        };
    }

    public function getTypeBadgeAttribute(): array
    {
        return match($this->type) {
            'rent' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Rent'],
            'amenities' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Amenities'],
            'damage' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Damage'],
            'other' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Other'],
            default => ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($this->type)]
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'sent' && $this->due_date < now();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return $this->due_date->diffInDays(now());
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->status === 'paid') {
            return 'Fully Paid';
        }

        if ($this->paid_amount > 0) {
            return 'Partially Paid';
        }

        return 'Unpaid';
    }

    // Methods
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->balance_amount = $this->total_amount - $this->paid_amount;
        $this->save();
    }

    public function addPayment(float $amount, array $paymentData = []): Payment
    {
        $payment = $this->payments()->create(array_merge([
            'payment_number' => $this->generatePaymentNumber(),
            'tenant_profile_id' => $this->tenant_profile_id,
            'amount' => $amount,
            'payment_date' => now(),
            'recorded_by' => auth()->id() ?? 1 // Default to user ID 1 if not authenticated
        ], $paymentData));

        $this->updatePaidAmount();

        return $payment;
    }

    public function updatePaidAmount(): void
    {
        $this->paid_amount = $this->payments()->where('status', 'completed')->sum('amount');
        $this->balance_amount = $this->total_amount - $this->paid_amount;

        // Update status based on payment
        if ($this->balance_amount <= 0) {
            $this->status = 'paid';
            $this->paid_at = now();
        } elseif ($this->paid_amount > 0) {
            $this->status = 'sent'; // Partially paid
        }

        $this->save();
    }

    public function markAsOverdue(): void
    {
        if ($this->status === 'sent' && $this->due_date < now()) {
            $this->status = 'overdue';
            $this->save();
        }
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');

        $lastInvoice = static::where('invoice_number', 'like', "{$prefix}-{$year}{$month}-%")
                           ->orderBy('invoice_number', 'desc')
                           ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }

    private function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');

        $lastPayment = Payment::where('payment_number', 'like', "{$prefix}-{$year}{$month}-%")
                             ->orderBy('payment_number', 'desc')
                             ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }

    /**
     * Create amenity usage invoice for a tenant
     */
    public static function createAmenityUsageInvoice(
        int $tenantProfileId,
        Carbon $periodStart,
        Carbon $periodEnd,
        array $options = []
    ): self {
        $tenantProfile = TenantProfile::findOrFail($tenantProfileId);

        // Get all usage records for the period
        $usageRecords = TenantAmenityUsage::with(['tenantAmenity.paidAmenity'])
            ->whereHas('tenantAmenity', function ($query) use ($tenantProfileId) {
                $query->where('tenant_profile_id', $tenantProfileId);
            })
            ->forDateRange($periodStart, $periodEnd)
            ->get();

        if ($usageRecords->isEmpty()) {
            throw new \Exception('No amenity usage records found for the specified period.');
        }

        // Create the invoice
        $invoice = self::create([
            'invoice_number' => self::generateInvoiceNumber(),
            'tenant_profile_id' => $tenantProfileId,
            'type' => 'amenities',
            'status' => $options['status'] ?? 'draft',
            'invoice_date' => $options['invoice_date'] ?? now(),
            'due_date' => $options['due_date'] ?? now()->addDays(7),
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'notes' => $options['notes'] ?? "Amenity usage charges for " . $periodStart->format('M Y'),
            'created_by' => auth()->id() ?? 1,
        ]);

        // Group usage records by amenity
        $amenityGroups = $usageRecords->groupBy('tenantAmenity.paidAmenity.name');

        foreach ($amenityGroups as $amenityName => $usageGroup) {
            $totalQuantity = $usageGroup->sum('quantity');
            $totalAmount = $usageGroup->sum('total_amount');
            $avgUnitPrice = $totalAmount / $totalQuantity;

            $invoice->items()->create([
                'item_type' => 'amenity',
                'description' => $amenityName . " (Usage: {$usageGroup->count()} days)",
                'quantity' => $totalQuantity,
                'unit_price' => $avgUnitPrice,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
            ]);
        }

        $invoice->calculateTotals();

        return $invoice;
    }

    /**
     * Get amenity usage summary for a tenant and period
     */
    public static function getAmenityUsageSummary(
        int $tenantProfileId,
        Carbon $periodStart,
        Carbon $periodEnd
    ): array {
        $usageRecords = TenantAmenityUsage::with(['tenantAmenity.paidAmenity'])
            ->whereHas('tenantAmenity', function ($query) use ($tenantProfileId) {
                $query->where('tenant_profile_id', $tenantProfileId);
            })
            ->forDateRange($periodStart, $periodEnd)
            ->get();

        if ($usageRecords->isEmpty()) {
            return [
                'total_amount' => 0,
                'total_usage_days' => 0,
                'amenities' => [],
                'has_usage' => false
            ];
        }

        $amenityGroups = $usageRecords->groupBy('tenantAmenity.paidAmenity.name');
        $amenitySummary = [];

        foreach ($amenityGroups as $amenityName => $usageGroup) {
            $amenitySummary[] = [
                'name' => $amenityName,
                'usage_days' => $usageGroup->count(),
                'total_quantity' => $usageGroup->sum('quantity'),
                'total_amount' => $usageGroup->sum('total_amount'),
                'avg_daily_amount' => $usageGroup->sum('total_amount') / $usageGroup->count()
            ];
        }

        return [
            'total_amount' => $usageRecords->sum('total_amount'),
            'total_usage_days' => $usageRecords->count(),
            'amenities' => $amenitySummary,
            'has_usage' => true
        ];
    }

    /**
     * Check if tenant has pending amenity usage charges
     */
    public static function hasPendingAmenityCharges(int $tenantProfileId, Carbon $periodStart, Carbon $periodEnd): bool
    {
        // Check if there are usage records without corresponding invoices
        $usageRecords = TenantAmenityUsage::whereHas('tenantAmenity', function ($query) use ($tenantProfileId) {
            $query->where('tenant_profile_id', $tenantProfileId);
        })
        ->forDateRange($periodStart, $periodEnd)
        ->count();

        if ($usageRecords === 0) {
            return false;
        }

        // Check if there's already an amenity invoice for this period
        $existingInvoice = self::where('tenant_profile_id', $tenantProfileId)
            ->where('type', 'amenities')
            ->where('period_start', '>=', $periodStart)
            ->where('period_end', '<=', $periodEnd)
            ->exists();

        return !$existingInvoice;
    }

    /**
     * Auto-generate monthly amenity invoices for all tenants
     */
    public static function generateMonthlyAmenityInvoices(Carbon $month = null): array
    {
        $month = $month ?? now()->subMonth();
        $periodStart = $month->copy()->startOfMonth();
        $periodEnd = $month->copy()->endOfMonth();

        $results = [
            'generated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        // Get all tenant profiles with amenity usage in the period
        $tenantProfiles = TenantProfile::whereHas('tenantAmenities.usageRecords', function ($query) use ($periodStart, $periodEnd) {
            $query->forDateRange($periodStart, $periodEnd);
        })->get();

        foreach ($tenantProfiles as $tenantProfile) {
            try {
                if (self::hasPendingAmenityCharges($tenantProfile->id, $periodStart, $periodEnd)) {
                    self::createAmenityUsageInvoice(
                        $tenantProfile->id,
                        $periodStart,
                        $periodEnd,
                        [
                            'status' => 'sent',
                            'due_date' => now()->addDays(7)
                        ]
                    );
                    $results['generated']++;
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'tenant' => $tenantProfile->user->name,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }
}
