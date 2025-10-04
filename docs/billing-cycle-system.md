# Billing Cycle System Documentation

## Overview
The billing cycle system provides automated rent payment notifications and tracking for tenants. While the notification functionality is not yet implemented, all necessary database fields and model methods are in place for future development.

## Database Schema

### New Fields in `tenant_profiles` Table

#### Billing Configuration
- `billing_cycle` - enum('monthly', 'quarterly', 'half_yearly', 'yearly') - Default: 'monthly'
- `billing_day` - integer(1-31) - Day of month for billing - Default: 1
- `next_billing_date` - date - Next scheduled billing date
- `last_billing_date` - date - Last billing date

#### Payment Tracking
- `payment_status` - enum('paid', 'pending', 'overdue', 'partial') - Default: 'pending'
- `last_payment_date` - date - Date of last payment received
- `last_payment_amount` - decimal(10,2) - Amount of last payment
- `outstanding_amount` - decimal(10,2) - Outstanding/overdue amount - Default: 0

#### Notification Settings
- `auto_billing_enabled` - boolean - Enable automatic billing notifications - Default: true
- `notification_preferences` - json - Notification settings (email, sms, days_before)
- `reminder_days_before` - integer - Days before billing to send reminder - Default: 3
- `overdue_grace_days` - integer - Grace period before marking overdue - Default: 5

#### Late Fees & Penalties
- `late_fee_amount` - decimal(8,2) - Fixed late fee amount
- `late_fee_percentage` - decimal(5,2) - Late fee as percentage of rent
- `compound_late_fees` - boolean - Whether to compound late fees - Default: false

#### Payment History
- `consecutive_on_time_payments` - integer - Count of consecutive on-time payments - Default: 0
- `total_late_payments` - integer - Total count of late payments - Default: 0
- `last_reminder_sent` - date - Date when last reminder was sent
- `reminder_count_current_cycle` - integer - Number of reminders sent for current billing cycle - Default: 0

#### Auto-Payment (Future)
- `auto_payment_enabled` - boolean - Enable automatic payment processing - Default: false
- `payment_method` - string - Preferred payment method
- `payment_details` - json - Encrypted payment method details

## Model Methods

### TenantProfile Model

#### Scopes
- `dueForBilling()` - Tenants due for billing
- `overdue()` - Tenants with overdue payments
- `pendingPayment()` - Tenants with pending payments
- `withOutstandingAmount()` - Tenants with outstanding amounts

#### Accessors
- `billing_cycle_display` - Human-readable billing cycle
- `payment_status_badge` - Status badge configuration
- `is_payment_overdue` - Boolean check for overdue status
- `days_until_next_billing` - Days until next billing date
- `next_billing_amount` - Calculated next billing amount
- `total_outstanding` - Total outstanding including late fees
- `payment_history_score` - Payment reliability score (0-100)

#### Helper Methods
- `calculateLateFees()` - Calculate late fee amount
- `calculateNextBillingDate($fromDate)` - Calculate next billing date
- `initializeBillingCycle()` - Set up billing cycle for new tenant
- `recordPayment($amount, $paymentDate, $paymentMethod)` - Record payment
- `sendPaymentReminder($type)` - Send payment reminder
- `markAsOverdue()` - Mark payment as overdue
- `updateBillingSettings($settings)` - Update billing configuration

## Usage Examples

### Initialize Billing for New Tenant
```php
$tenant = TenantProfile::find(1);
$tenant->initializeBillingCycle();
```

### Record Payment
```php
$tenant->recordPayment(7500, '2024-10-01', 'bank_transfer');
```

### Check Overdue Tenants
```php
$overdueTenants = TenantProfile::overdue()->get();
```

### Get Tenants Due for Billing
```php
$dueForBilling = TenantProfile::dueForBilling()->get();
```

### Update Billing Settings
```php
$tenant->updateBillingSettings([
    'billing_cycle' => 'quarterly',
    'billing_day' => 15,
    'reminder_days_before' => 5,
    'late_fee_percentage' => 5.0
]);
```

## Billing Cycles

### Monthly (Default)
- Bills generated every month on the specified billing day
- Most common for hostel rentals

### Quarterly
- Bills generated every 3 months
- Amount = monthly_rent × 3

### Half Yearly
- Bills generated every 6 months
- Amount = monthly_rent × 6

### Yearly
- Bills generated every 12 months
- Amount = monthly_rent × 12

## Payment Status Flow

1. **Pending** - Initial status when bill is generated
2. **Paid** - Full payment received on time
3. **Partial** - Partial payment received
4. **Overdue** - Payment not received within grace period

## Late Fee Calculation

### Fixed Amount
```php
$lateFee = $tenant->late_fee_amount; // e.g., ₹500
```

### Percentage Based
```php
$lateFee = $tenant->monthly_rent * ($tenant->late_fee_percentage / 100); // e.g., 5% of rent
```

### Compounding (Optional)
```php
if ($tenant->compound_late_fees) {
    $lateFee *= (1 + ($tenant->total_late_payments * 0.1)); // 10% compound per late payment
}
```

## Notification Preferences Structure

```json
{
    "email": true,
    "sms": false,
    "push": true,
    "reminder_types": ["before_due", "overdue"],
    "custom_message": "Please pay your rent by the due date."
}
```

## Future Implementation Tasks

### Phase 1: Basic Notifications
- [ ] Email notification service integration
- [ ] SMS notification service integration
- [ ] Automated reminder scheduling (Laravel Scheduler)
- [ ] Overdue payment detection job

### Phase 2: Advanced Features
- [ ] Payment gateway integration
- [ ] Auto-payment processing
- [ ] Payment history dashboard
- [ ] Tenant payment portal

### Phase 3: Analytics & Reporting
- [ ] Payment analytics dashboard
- [ ] Late payment reports
- [ ] Revenue forecasting
- [ ] Tenant payment behavior analysis

## Database Indexes

The following indexes are created for optimal performance:
- `next_billing_date` - For finding due payments
- `payment_status` - For filtering by payment status
- `billing_cycle` - For grouping by billing frequency
- `auto_billing_enabled` - For filtering enabled tenants

## Security Considerations

- Payment details are stored as encrypted JSON
- Sensitive financial data should be handled with appropriate encryption
- Access to billing information should be role-based
- Audit logs should be maintained for all payment transactions

## Integration Points

### With Existing Modules
- **Tenant Management** - Billing settings in tenant profiles
- **Room Assignment** - Rent amount from assigned bed/room
- **Dashboard** - Payment status overview
- **Reports** - Financial reporting integration

### External Services (Future)
- **Payment Gateways** - Razorpay, Stripe, PayU
- **Notification Services** - Email (SMTP), SMS (Twilio)
- **Accounting Software** - Tally, QuickBooks integration
- **Banking APIs** - Auto-reconciliation of payments

This system provides a solid foundation for automated billing and payment management while maintaining flexibility for future enhancements.
