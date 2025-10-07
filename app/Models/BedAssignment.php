<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BedAssignment extends Model
{
    protected $fillable = [
        'bed_id',
        'tenant_id',
        'assigned_from',
        'assigned_until',
        'status',
        'monthly_rent',
        'notes',
    ];

    protected $casts = [
        'assigned_from' => 'date',
        'assigned_until' => 'date',
        'monthly_rent' => 'decimal:2',
    ];

    // Relationships
    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeReserved($query)
    {
        return $query->where('status', 'reserved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->where(function ($subQ) use ($startDate, $endDate) {
                // Assignment starts before or on the end date and ends after or on the start date
                $subQ->where('assigned_from', '<=', $endDate)
                     ->where(function ($endQ) use ($startDate) {
                         $endQ->whereNull('assigned_until')
                              ->orWhere('assigned_until', '>=', $startDate);
                     });
            });
        });
    }

    public function scopeOverlappingWith($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->where('assigned_from', '<=', $endDate)
              ->where(function ($endQ) use ($startDate) {
                  $endQ->whereNull('assigned_until')
                       ->orWhere('assigned_until', '>=', $startDate);
              });
        });
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isReserved()
    {
        return $this->status === 'reserved';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isCurrentlyActive()
    {
        $today = Carbon::today();
        return $this->isActive() &&
               $this->assigned_from <= $today &&
               ($this->assigned_until === null || $this->assigned_until >= $today);
    }

    public function overlapsWith($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $assignmentStart = Carbon::parse($this->assigned_from);
        $assignmentEnd = $this->assigned_until ? Carbon::parse($this->assigned_until) : null;

        // Check for overlap
        if ($assignmentEnd === null) {
            // Assignment has no end date, so it overlaps if it starts before or on the requested end date
            return $assignmentStart <= $endDate;
        } else {
            // Assignment has an end date, check for overlap
            return $assignmentStart <= $endDate && $assignmentEnd >= $startDate;
        }
    }

    public function getDurationInDays()
    {
        if ($this->assigned_until) {
            return Carbon::parse($this->assigned_from)->diffInDays(Carbon::parse($this->assigned_until));
        }
        return Carbon::parse($this->assigned_from)->diffInDays(Carbon::now());
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Active'],
            'reserved' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Reserved'],
            'completed' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Completed'],
            'cancelled' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Cancelled'],
        ];

        $badge = $badges[$this->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Unknown'];

        return "<span class='px-2 py-1 text-xs font-medium rounded-full {$badge['class']}'>{$badge['text']}</span>";
    }
}
