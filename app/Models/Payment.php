<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'invoice_id',
        'tenant_profile_id',
        'amount',
        'payment_date',
        'payment_method',
        'status',
        'reference_number',
        'bank_name',
        'account_number',
        'notes',
        'metadata',
        'recorded_by',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'verified_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function tenantProfile(): BelongsTo
    {
        return $this->belongsTo(TenantProfile::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_profile_id', $tenantId);
    }

    public function scopeForInvoice($query, $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format((float) $this->amount, 2);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Pending'],
            'completed' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Completed'],
            'failed' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Failed'],
            'cancelled' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Cancelled'],
            default => ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($this->status)]
        };
    }

    public function getMethodBadgeAttribute(): array
    {
        return match($this->payment_method) {
            'cash' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Cash'],
            'bank_transfer' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Bank Transfer'],
            'upi' => ['class' => 'bg-purple-100 text-purple-800', 'text' => 'UPI'],
            'card' => ['class' => 'bg-indigo-100 text-indigo-800', 'text' => 'Card'],
            'cheque' => ['class' => 'bg-orange-100 text-orange-800', 'text' => 'Cheque'],
            'other' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Other'],
            default => ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($this->payment_method)]
        };
    }

    public function getIsVerifiedAttribute(): bool
    {
        return !is_null($this->verified_at);
    }

    // Methods
    public function verify(int $verifiedBy = null): void
    {
        $this->verified_at = now();
        $this->verified_by = $verifiedBy ?? auth()->id();
        $this->status = 'completed';
        $this->save();

        // Update invoice payment status
        $this->invoice->updatePaidAmount();
    }

    public function cancel(string $reason = null): void
    {
        $this->status = 'cancelled';
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Cancelled: " . $reason;
        }
        $this->save();

        // Update invoice payment status
        $this->invoice->updatePaidAmount();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            if ($payment->status === 'completed') {
                $payment->invoice->updatePaidAmount();
            }
        });

        static::updated(function ($payment) {
            $payment->invoice->updatePaidAmount();
        });

        static::deleted(function ($payment) {
            $payment->invoice->updatePaidAmount();
        });
    }
}
