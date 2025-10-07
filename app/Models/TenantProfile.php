<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TenantProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'date_of_birth',
        'address',
        'occupation',
        'company',
        'id_proof_type',
        'id_proof_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'status',
        'move_in_date',
        'move_out_date',
        'security_deposit',
        'monthly_rent',
        'lease_start_date',
        'lease_end_date',
        'notes',
        'documents',
        'is_verified',
        'verified_at',
        'verified_by',
        // Billing cycle fields
        'billing_cycle',
        'billing_day',
        'next_billing_date',
        'last_billing_date',
        'payment_status',
        'last_payment_date',
        'last_payment_amount',
        'outstanding_amount',
        'auto_billing_enabled',
        'notification_preferences',
        'reminder_days_before',
        'overdue_grace_days',
        'late_fee_amount',
        'late_fee_percentage',
        'compound_late_fees',
        'consecutive_on_time_payments',
        'total_late_payments',
        'last_reminder_sent',
        'reminder_count_current_cycle',
        'auto_payment_enabled',
        'payment_method',
        'payment_details',
    ];

    protected $casts = [
        'documents' => 'array',
        'date_of_birth' => 'date',
        'move_in_date' => 'date',
        'move_out_date' => 'date',
        'lease_start_date' => 'date',
        'lease_end_date' => 'date',
        'security_deposit' => 'decimal:2',
        'monthly_rent' => 'decimal:2',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        // Billing cycle casts
        'next_billing_date' => 'date',
        'last_billing_date' => 'date',
        'last_payment_date' => 'date',
        'last_payment_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'auto_billing_enabled' => 'boolean',
        'notification_preferences' => 'array',
        'late_fee_amount' => 'decimal:2',
        'late_fee_percentage' => 'decimal:2',
        'compound_late_fees' => 'boolean',
        'last_reminder_sent' => 'date',
        'auto_payment_enabled' => 'boolean',
        'payment_details' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function bedAssignments()
    {
        return $this->hasMany(BedAssignment::class, 'tenant_id', 'user_id');
    }

    public function currentBedAssignment()
    {
        return $this->hasOne(BedAssignment::class, 'tenant_id', 'user_id')->where('status', 'active');
    }

    public function currentBed()
    {
        return $this->hasOneThrough(Bed::class, BedAssignment::class, 'tenant_id', 'id', 'user_id', 'bed_id')
            ->where('bed_assignments.status', 'active');
    }

    public function beds()
    {
        return $this->hasManyThrough(Bed::class, BedAssignment::class, 'tenant_id', 'id', 'user_id', 'bed_id');
    }

    public function tenantAmenities()
    {
        return $this->hasMany(TenantAmenity::class);
    }

    public function activeTenantAmenities()
    {
        return $this->hasMany(TenantAmenity::class)->where('status', 'active');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function tenantDocuments()
    {
        return $this->hasMany(TenantDocument::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    // Billing-related scopes
    public function scopeDueForBilling($query)
    {
        return $query->where('next_billing_date', '<=', Carbon::now()->toDateString())
                    ->where('auto_billing_enabled', true)
                    ->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'overdue');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeWithOutstandingAmount($query)
    {
        return $query->where('outstanding_amount', '>', 0);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check-circle'],
            'inactive' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fas fa-pause-circle'],
            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
            'suspended' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-ban'],
            'moved_out' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fas fa-sign-out-alt']
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    public function getIdProofTypeDisplayAttribute()
    {
        $types = [
            'aadhar' => 'Aadhar Card',
            'passport' => 'Passport',
            'driving_license' => 'Driving License',
            'voter_id' => 'Voter ID',
            'pan_card' => 'PAN Card',
            'other' => 'Other'
        ];

        return $types[$this->id_proof_type] ?? 'Not Specified';
    }

    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) return null;
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function getTenancyDurationAttribute()
    {
        if (!$this->move_in_date) return null;

        $endDate = $this->move_out_date ?? Carbon::now();
        return Carbon::parse($this->move_in_date)->diffInDays($endDate);
    }

    public function getTenancyDurationHumanAttribute()
    {
        if (!$this->move_in_date) return 'Not moved in';

        $endDate = $this->move_out_date ?? Carbon::now();
        return Carbon::parse($this->move_in_date)->diffForHumans($endDate, true);
    }

    public function getIsLeaseExpiredAttribute()
    {
        if (!$this->lease_end_date) return false;
        return Carbon::now()->greaterThan($this->lease_end_date);
    }

    public function getDaysUntilLeaseExpiryAttribute()
    {
        if (!$this->lease_end_date) return null;
        return Carbon::now()->diffInDays($this->lease_end_date, false);
    }

    public function getCurrentRoomAttribute()
    {
        $bed = $this->currentBed;
        return $bed ? $bed->room : null;
    }

    public function getCurrentHostelAttribute()
    {
        $room = $this->current_room;
        return $room ? $room->hostel : null;
    }

    // Billing-related accessors
    public function getBillingCycleDisplayAttribute()
    {
        $cycles = [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'half_yearly' => 'Half Yearly',
            'yearly' => 'Yearly'
        ];

        return $cycles[$this->billing_cycle] ?? 'Monthly';
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'paid' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fas fa-check-circle'],
            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fas fa-clock'],
            'overdue' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fas fa-exclamation-triangle'],
            'partial' => ['class' => 'bg-orange-100 text-orange-800', 'icon' => 'fas fa-minus-circle']
        ];

        return $badges[$this->payment_status] ?? $badges['pending'];
    }

    public function getIsPaymentOverdueAttribute()
    {
        if (!$this->next_billing_date) return false;

        $overdueDate = Carbon::parse($this->next_billing_date)->addDays($this->overdue_grace_days);
        return Carbon::now()->greaterThan($overdueDate) && $this->payment_status !== 'paid';
    }

    public function getDaysUntilNextBillingAttribute()
    {
        if (!$this->next_billing_date) return null;

        return Carbon::now()->diffInDays($this->next_billing_date, false);
    }

    public function getNextBillingAmountAttribute()
    {
        $baseAmount = $this->monthly_rent ?? 0;

        // Calculate amount based on billing cycle
        switch ($this->billing_cycle) {
            case 'quarterly':
                $baseAmount *= 3;
                break;
            case 'half_yearly':
                $baseAmount *= 6;
                break;
            case 'yearly':
                $baseAmount *= 12;
                break;
            default:
                // monthly - no change
                break;
        }

        // Add outstanding amount
        $baseAmount += $this->outstanding_amount ?? 0;

        // Add late fees if overdue
        if ($this->is_payment_overdue) {
            if ($this->late_fee_amount) {
                $baseAmount += $this->late_fee_amount;
            } elseif ($this->late_fee_percentage) {
                $baseAmount += ($this->monthly_rent * $this->late_fee_percentage / 100);
            }
        }

        return $baseAmount;
    }

    public function getTotalOutstandingAttribute()
    {
        return ($this->outstanding_amount ?? 0) + ($this->is_payment_overdue ? $this->calculateLateFees() : 0);
    }

    public function getPaymentHistoryScoreAttribute()
    {
        $totalPayments = $this->consecutive_on_time_payments + $this->total_late_payments;
        if ($totalPayments === 0) return 100; // New tenant gets perfect score

        return round(($this->consecutive_on_time_payments / $totalPayments) * 100, 1);
    }

    // Helper methods
    public function markAsVerified($verifiedBy = null)
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => Carbon::now(),
            'verified_by' => $verifiedBy
        ]);

        return $this;
    }

    public function assignToBed($bedId, $moveInDate = null, $rent = null)
    {
        $bed = Bed::findOrFail($bedId);

        // Release any current bed
        $this->releaseCurrentBed();

        // Assign to new bed
        $bed->assignTenant(
            $this->user_id,
            $moveInDate ?? $this->move_in_date ?? Carbon::now(),
            $this->lease_end_date,
            $rent ?? $this->monthly_rent
        );

        // Update tenant status
        $this->update([
            'status' => 'active',
            'move_in_date' => $moveInDate ?? $this->move_in_date ?? Carbon::now(),
            'monthly_rent' => $rent ?? $this->monthly_rent
        ]);

        return $this;
    }

    public function releaseCurrentBed()
    {
        $currentBed = $this->currentBed;
        if ($currentBed) {
            $currentBed->releaseTenant();
        }

        return $this;
    }

    public function moveOut($moveOutDate = null)
    {
        $this->releaseCurrentBed();

        $this->update([
            'status' => 'moved_out',
            'move_out_date' => $moveOutDate ?? Carbon::now()
        ]);

        return $this;
    }

    // Billing helper methods
    public function calculateLateFees()
    {
        if (!$this->is_payment_overdue) return 0;

        $lateFee = 0;

        if ($this->late_fee_amount) {
            $lateFee = $this->late_fee_amount;
        } elseif ($this->late_fee_percentage) {
            $lateFee = ($this->monthly_rent * $this->late_fee_percentage / 100);
        }

        // Apply compounding if enabled
        if ($this->compound_late_fees && $this->total_late_payments > 0) {
            $lateFee *= (1 + ($this->total_late_payments * 0.1)); // 10% compound per late payment
        }

        return $lateFee;
    }

    public function calculateNextBillingDate($fromDate = null)
    {
        $baseDate = $fromDate ? Carbon::parse($fromDate) : Carbon::now();

        // Set to the billing day of current month
        $nextBilling = $baseDate->copy()->day($this->billing_day);

        // If billing day has passed this month, move to next cycle
        if ($nextBilling->lte($baseDate)) {
            switch ($this->billing_cycle) {
                case 'quarterly':
                    $nextBilling->addMonths(3);
                    break;
                case 'half_yearly':
                    $nextBilling->addMonths(6);
                    break;
                case 'yearly':
                    $nextBilling->addYear();
                    break;
                default:
                    $nextBilling->addMonth();
                    break;
            }
        }

        return $nextBilling;
    }

    public function initializeBillingCycle()
    {
        if (!$this->next_billing_date) {
            $this->update([
                'next_billing_date' => $this->calculateNextBillingDate($this->move_in_date),
                'billing_cycle' => $this->billing_cycle ?? 'monthly',
                'billing_day' => $this->billing_day ?? 1,
                'payment_status' => 'pending',
                'auto_billing_enabled' => true,
                'reminder_days_before' => 3,
                'overdue_grace_days' => 5,
            ]);
        }

        return $this;
    }

    public function recordPayment($amount, $paymentDate = null, $paymentMethod = null)
    {
        $paymentDate = $paymentDate ? Carbon::parse($paymentDate) : Carbon::now();
        $isOnTime = $paymentDate->lte(Carbon::parse($this->next_billing_date)->addDays($this->overdue_grace_days));

        // Update payment tracking
        $this->update([
            'last_payment_date' => $paymentDate,
            'last_payment_amount' => $amount,
            'payment_status' => $amount >= $this->next_billing_amount ? 'paid' : 'partial',
            'outstanding_amount' => max(0, $this->next_billing_amount - $amount),
            'consecutive_on_time_payments' => $isOnTime ? $this->consecutive_on_time_payments + 1 : 0,
            'total_late_payments' => $isOnTime ? $this->total_late_payments : $this->total_late_payments + 1,
            'reminder_count_current_cycle' => 0,
        ]);

        // Move to next billing cycle if fully paid
        if ($this->payment_status === 'paid') {
            $this->update([
                'next_billing_date' => $this->calculateNextBillingDate($this->next_billing_date),
                'last_billing_date' => $this->next_billing_date,
            ]);
        }

        return $this;
    }

    public function sendPaymentReminder($type = 'reminder')
    {
        $this->update([
            'last_reminder_sent' => Carbon::now(),
            'reminder_count_current_cycle' => $this->reminder_count_current_cycle + 1,
        ]);

        // Here you would integrate with notification service
        // NotificationService::send($this->user, $type, $this->next_billing_amount);

        return $this;
    }

    public function markAsOverdue()
    {
        $this->update([
            'payment_status' => 'overdue',
            'outstanding_amount' => $this->next_billing_amount + $this->calculateLateFees(),
        ]);

        return $this;
    }

    public function updateBillingSettings($settings)
    {
        $allowedSettings = [
            'billing_cycle',
            'billing_day',
            'auto_billing_enabled',
            'notification_preferences',
            'reminder_days_before',
            'overdue_grace_days',
            'late_fee_amount',
            'late_fee_percentage',
            'compound_late_fees',
            'auto_payment_enabled',
            'payment_method',
        ];

        $filteredSettings = array_intersect_key($settings, array_flip($allowedSettings));

        $this->update($filteredSettings);

        // Recalculate next billing date if cycle changed
        if (isset($settings['billing_cycle']) || isset($settings['billing_day'])) {
            $this->update([
                'next_billing_date' => $this->calculateNextBillingDate()
            ]);
        }

        return $this;
    }
}
