# Payment System Module

## Overview
The Payment System module handles all payment processing and tracking for the hostel CRM. It manages payments for room rent, amenity charges, damage fees, and other miscellaneous charges. The system provides comprehensive payment tracking, verification, reporting, and integration with the invoice system.

## Features

### 🎯 Core Functionality
- **Multi-method Support**: Cash, bank transfer, UPI, card, cheque payments
- **Payment Tracking**: Complete payment history and status tracking
- **Invoice Integration**: Automatic invoice balance updates
- **Verification System**: Payment verification and approval workflow
- **Receipt Generation**: Automated receipt creation and delivery
- **Reporting**: Comprehensive payment reports and analytics

### 📊 Key Components

#### 1. Payment Management
- **CRUD Operations**: Create, read, update, delete payments
- **Status Tracking**: Pending, completed, failed, verified
- **Bulk Operations**: Mass payment operations
- **Search & Filter**: Advanced filtering and search capabilities

#### 2. Payment Methods
- **Cash Payments**: Direct cash payment recording
- **Bank Transfers**: Bank transfer details and verification
- **UPI Payments**: UPI transaction tracking
- **Card Payments**: Credit/debit card payment processing
- **Cheque Payments**: Cheque details and clearance tracking

#### 3. Verification & Approval
- **Payment Verification**: Multi-level verification system
- **Approval Workflow**: Configurable approval process
- **Audit Trail**: Complete payment audit history
- **Reconciliation**: Payment matching and verification

## Database Structure

### Tables

#### `payments`
```sql
- id (Primary Key)
- payment_number (Unique payment identifier)
- invoice_id (Foreign Key to invoices)
- tenant_profile_id (Foreign Key to tenant_profiles)
- amount (Payment amount)
- payment_date (Date of payment)
- payment_method (cash, bank_transfer, upi, card, cheque)
- reference_number (Transaction/reference number)
- status (pending, completed, failed, verified)
- notes (Additional notes)
- is_verified (Verification status)
- verified_by (User who verified, nullable)
- verified_at (Verification timestamp, nullable)
- recorded_by (User who recorded the payment)
- created_at, updated_at (Timestamps)
```

### Relationships
- **Payment**: BelongsTo Invoice, BelongsTo TenantProfile, BelongsTo User (recorded_by), BelongsTo User (verified_by)
- **Invoice**: HasMany Payments
- **TenantProfile**: HasMany Payments

## Routes

### Payment Routes
```php
// Main CRUD Operations
GET    /payments                         → index (list all payments)
POST   /payments                         → store (create new payment)
GET    /payments/create                  → create (show create form)
GET    /payments/{id}                    → show (view payment details)
GET    /payments/{id}/edit               → edit (show edit form)
PUT    /payments/{id}                    → update (update payment)
DELETE /payments/{id}                    → destroy (delete payment)

// Special Operations
POST   /payments/bulk-action             → bulkAction (bulk operations)
POST   /payments/{id}/verify             → verify (verify payment)
```

## Controllers

### PaymentController

#### Key Methods

##### `index(Request $request)`
- **Purpose**: Display paginated list of payments
- **Features**: Search, filtering, sorting, bulk actions
- **Returns**: Data table with payment information
- **Filters**: Status, method, tenant, date range, amount range
- **Includes**: Invoice details, tenant information

##### `create(Request $request)`
- **Purpose**: Show payment creation form
- **Features**: Invoice pre-selection, method selection
- **Data**: Unpaid/partially paid invoices, tenants
- **Validation**: Amount validation, invoice selection

##### `store(Request $request)`
- **Purpose**: Create new payment record
- **Features**: Invoice balance update, validation
- **Integration**: Updates invoice paid amount
- **Response**: Redirect with success message

##### `verify(Payment $payment)`
- **Purpose**: Verify payment authenticity
- **Features**: Status update, verification tracking
- **Authorization**: Only authorized users can verify
- **Response**: JSON with verification status

##### `bulkAction(Request $request)`
- **Purpose**: Handle bulk operations on payments
- **Features**: Mass verification, status updates
- **Validation**: Action type and selected items
- **Transaction**: Database transaction for data integrity

## Models

### Payment

#### Fillable Fields
```php
[
    'payment_number',
    'invoice_id',
    'tenant_profile_id',
    'amount',
    'payment_date',
    'payment_method',
    'reference_number',
    'status',
    'notes',
    'is_verified',
    'verified_by',
    'verified_at',
    'recorded_by'
]
```

#### Casts
```php
[
    'amount' => 'decimal:2',
    'payment_date' => 'date',
    'is_verified' => 'boolean',
    'verified_at' => 'datetime'
]
```

#### Key Methods

##### `generatePaymentNumber()`
- **Purpose**: Generate unique payment number
- **Format**: PAY-YYYY-NNNNNN (e.g., PAY-2024-000001)
- **Logic**: Year-based sequential numbering
- **Uniqueness**: Database constraint ensures uniqueness

##### `markAsVerified($verifiedBy = null)`
- **Purpose**: Mark payment as verified
- **Features**: Status update, timestamp recording
- **Authorization**: Track who verified the payment
- **Response**: Updated payment instance

##### `updateInvoiceBalance()`
- **Purpose**: Update related invoice balance
- **Logic**: Recalculate invoice paid amount
- **Integration**: Calls invoice balance update
- **Triggers**: Called after payment changes

## Views

### File Structure
```
resources/views/payments/
├── index.blade.php      → Main payments listing
├── create.blade.php     → Create new payment form
├── edit.blade.php       → Edit payment form
└── show.blade.php       → View payment details
```

### Key Features

#### Payment Index
- **Comprehensive List**: All payments with key information
- **Advanced Filters**: Status, method, tenant, date range
- **Bulk Actions**: Mass operations on selected payments
- **Quick Actions**: Verify, edit, delete options
- **Status Indicators**: Visual status badges and icons

#### Payment Details
- **Complete Information**: All payment details
- **Invoice Integration**: Linked invoice information
- **Verification Status**: Verification details and history
- **Audit Trail**: Creation and modification history
- **Related Records**: Links to tenant and invoice

#### Payment Form
- **Smart Defaults**: Auto-populate from invoice
- **Method Selection**: Payment method specific fields
- **Validation**: Real-time form validation
- **Amount Calculation**: Automatic amount suggestions
- **Reference Tracking**: Transaction reference handling

## Payment Methods

### Cash Payments
- **Recording**: Direct cash payment entry
- **Verification**: Manual verification required
- **Receipt**: Automatic receipt generation
- **Tracking**: Cash flow tracking and reporting

### Bank Transfer
- **Details**: Bank account and transfer details
- **Reference**: Bank reference number tracking
- **Verification**: Bank statement verification
- **Reconciliation**: Automatic bank reconciliation

### UPI Payments
- **Transaction ID**: UPI transaction tracking
- **Instant Verification**: Real-time status updates
- **Integration**: UPI gateway integration
- **Reconciliation**: Automatic transaction matching

### Card Payments
- **Gateway Integration**: Payment gateway support
- **Security**: PCI DSS compliance
- **Processing**: Real-time payment processing
- **Reconciliation**: Automatic settlement matching

### Cheque Payments
- **Details**: Cheque number, bank, date tracking
- **Clearance**: Cheque clearance status
- **Bounce Handling**: Bounced cheque management
- **Bank Integration**: Bank clearance verification

## Integration Points

### With Invoice System
- **Balance Updates**: Automatic invoice balance updates
- **Status Changes**: Invoice status updates based on payments
- **Partial Payments**: Support for partial payment tracking
- **Overpayments**: Handle overpayment scenarios

### With Tenant Management
- **Payment History**: Complete tenant payment history
- **Outstanding Balances**: Real-time balance tracking
- **Communication**: Payment notifications and reminders
- **Credit Management**: Tenant credit and payment terms

### With Reporting System
- **Payment Reports**: Comprehensive payment analytics
- **Cash Flow**: Cash flow analysis and forecasting
- **Collection Reports**: Payment collection efficiency
- **Reconciliation**: Payment reconciliation reports

## Business Logic

### Payment Status Workflow
1. **Pending**: Payment recorded but not processed
2. **Completed**: Payment successfully processed
3. **Failed**: Payment processing failed
4. **Verified**: Payment verified by authorized user

### Verification Process
1. **Initial Recording**: Payment recorded by staff
2. **Verification Required**: Payments above threshold require verification
3. **Verification**: Authorized user verifies payment authenticity
4. **Final Status**: Payment marked as verified and complete

### Amount Validation
- **Positive Amounts**: Only positive payment amounts allowed
- **Maximum Limits**: Configurable maximum payment limits
- **Invoice Balance**: Cannot exceed outstanding invoice balance
- **Precision**: Decimal precision handling for currency

## Configuration

### Payment Settings
```php
// config/payment.php
'number_format' => 'PAY-{year}-{sequence}',
'verification_required' => true,
'verification_threshold' => 10000,
'methods' => [
    'cash' => 'Cash',
    'bank_transfer' => 'Bank Transfer',
    'upi' => 'UPI',
    'card' => 'Credit/Debit Card',
    'cheque' => 'Cheque'
],
'currency' => 'INR'
```

### Gateway Configuration
```php
'gateways' => [
    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET')
    ],
    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET')
    ]
]
```

## API Endpoints

### Payment Management
```php
GET    /api/payments                     → List payments
POST   /api/payments                     → Create payment
GET    /api/payments/{id}                → Get payment details
PUT    /api/payments/{id}                → Update payment
DELETE /api/payments/{id}                → Delete payment
```

### Verification Endpoints
```php
POST   /api/payments/{id}/verify         → Verify payment
GET    /api/payments/pending-verification → Get pending verifications
POST   /api/payments/bulk-verify         → Bulk verify payments
```

### Reporting Endpoints
```php
GET    /api/payments/summary             → Payment summary
GET    /api/payments/by-method           → Payments by method
GET    /api/payments/cash-flow           → Cash flow data
```

## Security & Validation

### Data Validation
- **Amount Validation**: Positive numbers, decimal precision
- **Date Validation**: Payment date validation
- **Method Validation**: Valid payment method selection
- **Reference Validation**: Unique reference numbers

### Access Control
- **Authentication**: All operations require login
- **Authorization**: Role-based access control
- **Verification Rights**: Only authorized users can verify
- **Audit Trail**: Track all changes and access

### Financial Security
- **Encryption**: Sensitive data encryption
- **PCI Compliance**: Payment card industry compliance
- **Fraud Detection**: Suspicious transaction detection
- **Secure Storage**: Secure payment data storage

## Performance Optimization

### Database Optimization
- **Indexes**: Optimized queries with proper indexing
- **Eager Loading**: Prevent N+1 query problems
- **Pagination**: Efficient data loading
- **Caching**: Cache frequently accessed data

### Payment Processing
- **Async Processing**: Background payment processing
- **Queue Management**: Payment queue handling
- **Retry Logic**: Failed payment retry mechanism
- **Rate Limiting**: API rate limiting for security

## Reporting & Analytics

### Payment Reports
- **Daily Collections**: Daily payment collection reports
- **Method Analysis**: Payment method usage analysis
- **Tenant Payments**: Individual tenant payment history
- **Outstanding Balances**: Pending payment reports

### Financial Analytics
- **Cash Flow**: Cash flow analysis and forecasting
- **Collection Efficiency**: Payment collection metrics
- **Payment Trends**: Historical payment trend analysis
- **Revenue Analysis**: Revenue breakdown by categories

### Export Options
- **CSV Export**: Export payment data to CSV
- **PDF Reports**: Generate PDF payment reports
- **Excel Integration**: Excel-compatible exports
- **API Access**: Programmatic data access

## Usage Examples

### Recording Cash Payment
1. Navigate to "Payments" section
2. Click "Add Payment"
3. Select invoice and tenant
4. Choose "Cash" as payment method
5. Enter amount and any notes
6. Save payment record

### Verifying Bank Transfer
1. View payment details
2. Check bank transfer reference
3. Verify amount and date
4. Click "Verify Payment"
5. Add verification notes
6. Confirm verification

### Bulk Payment Operations
1. Select multiple payments
2. Choose bulk action (verify, update status)
3. Confirm action
4. System processes all selected payments
5. View operation results

## Troubleshooting

### Common Issues

#### Payment Not Reflecting in Invoice
- **Check**: Payment invoice association
- **Verify**: Payment status is "completed"
- **Ensure**: Amount is correctly recorded
- **Debug**: Check invoice balance calculation

#### Verification Issues
- **Validate**: User has verification permissions
- **Check**: Payment is in correct status
- **Verify**: Verification workflow configuration
- **Debug**: Check verification logs

#### Gateway Integration Problems
- **Check**: Gateway configuration settings
- **Verify**: API credentials and endpoints
- **Ensure**: Network connectivity
- **Debug**: Check gateway logs and responses

## Future Enhancements

### Planned Features
- **Online Payments**: Tenant self-service payment portal
- **Recurring Payments**: Automated recurring payment setup
- **Payment Plans**: Installment payment options
- **Mobile Payments**: Mobile app payment integration
- **Cryptocurrency**: Digital currency payment support

### Advanced Features
- **AI Fraud Detection**: Machine learning fraud detection
- **Predictive Analytics**: Payment behavior prediction
- **Automated Reconciliation**: AI-powered reconciliation
- **Multi-currency**: International payment support

---

## Related Documentation
- [Invoice System](./invoice.md)
- [Tenant Management](./tenant.md)
- [Amenity Usage Tracking](./amenity-usage.md)
- [Reporting System](./reporting.md)
- [Table Standards](../table-standards.md)
