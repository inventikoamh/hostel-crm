<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_type',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'related_id',
        'related_type',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'metadata' => 'array'
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    // Accessors
    public function getFormattedUnitPriceAttribute(): string
    {
        return '₹' . number_format((float) $this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return '₹' . number_format((float) $this->total_price, 2);
    }

    public function getPeriodTextAttribute(): string
    {
        if ($this->period_start && $this->period_end) {
            return $this->period_start->format('M j') . ' - ' . $this->period_end->format('M j, Y');
        }
        return '';
    }

    // Methods
    public function calculateTotal(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });

        static::saved(function ($item) {
            $item->invoice->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->invoice->calculateTotals();
        });
    }
}
