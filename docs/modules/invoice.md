# Invoice System Module

## Overview
The Invoice System module handles all billing operations for the hostel CRM, including room rent, amenity charges, damage fees, and other miscellaneous charges. It provides comprehensive invoicing capabilities with PDF generation, email delivery, payment tracking, and integration with the usage tracking system.

## Features

### 🎯 Core Functionality
- **Multi-type Invoicing**: Room rent, amenities, damage, and other charges
- **Automated Generation**: Scheduled invoice creation based on billing cycles
- **PDF Generation**: Professional PDF invoices with company branding
- **Email Delivery**: Automated invoice delivery to tenants
- **Payment Integration**: Track payments against invoices
- **Usage-based Billing**: Integration with amenity usage tracking

### 📊 Key Components

#### 1. Invoice Management
- **CRUD Operations**: Create, read, update, delete invoices
- **Status Tracking**: Draft, sent, paid, overdue, cancelled
- **Bulk Operations**: Mass invoice operations
- **Search & Filter**: Advanced filtering and search capabilities

#### 2. Invoice Items
- **Itemized Billing**: Detailed line items for each charge
- **Flexible Pricing**: Support for different pricing models
- **Tax Calculations**: Automatic tax computation
- **Discounts**: Apply discounts at item or invoice level

#### 3. PDF & Communication
- **Professional PDFs**: Branded invoice templates
- **Email Integration**: Automated email delivery
- **Download Options**: Multiple download formats
- **Print-friendly**: Optimized for printing

## Database Structure

### Tables

#### `invoices`
```sql
- id (Primary Key)
- invoice_number (Unique invoice identifier)
- tenant_profile_id (Foreign Key to tenant_profiles)
- type (rent, amenities, damage, other)
- status (draft, sent, paid, overdue, cancelled)
- invoice_date (Date of invoice creation)
- due_date (Payment due date)
- period_start (Billing period start)
- period_end (Billing period end)
- subtotal (Sum of all items before tax/discount)
- tax_amount (Total tax amount)
- discount_amount (Total discount amount)
- total_amount (Final amount after tax/discount)
- paid_amount (Amount already paid)
- balance_amount (Remaining balance)
- notes (Additional notes)
- created_by (User who created the invoice)
- created_at, updated_at (Timestamps)
```

#### `invoice_items`
```sql
- id (Primary Key)
- invoice_id (Foreign Key to invoices)
- item_type (rent, amenity, damage, other)
- description (Item description)
- quantity (Number of units)
- unit_price (Price per unit)
- total_price (quantity × unit_price)
- period_start (Item period start)
- period_end (Item period end)
- tenant_amenity_id (Foreign Key, nullable)
- created_at, updated_at (Timestamps)
```

### Relationships
- **Invoice**: HasMany InvoiceItems, HasMany Payments, BelongsTo TenantProfile, BelongsTo User (created_by)
- **InvoiceItem**: BelongsTo Invoice, BelongsTo TenantAmenity (optional)
- **Payment**: BelongsTo Invoice, BelongsTo TenantProfile

## Routes

### Invoice Routes
```php
// Main CRUD Operations
GET    /invoices                         → index (list all invoices)
POST   /invoices                         → store (create new invoice)
GET    /invoices/create                  → create (show create form)
GET    /invoices/{id}                    → show (view invoice details)
GET    /invoices/{id}/edit               → edit (show edit form)
PUT    /invoices/{id}                    → update (update invoice)
DELETE /invoices/{id}                    → destroy (delete invoice)

// Special Operations
POST   /invoices/bulk-action             → bulkAction (bulk operations)
POST   /invoices/{id}/send               → send (email invoice to tenant)
GET    /invoices/{id}/pdf                → viewPdf (view PDF in browser)
GET    /invoices/{id}/pdf/download       → downloadPdf (download PDF)
POST   /invoices/{id}/pdf/email          → emailPdf (email PDF to tenant)

// Generation Endpoints
POST   /invoices/generate-rent           → generateRentInvoice
POST   /invoices/generate-amenities      → generateAmenitiesInvoice
POST   /invoices/generate-amenity        → generateAmenityInvoice
POST   /invoices/generate-monthly-amenity → generateMonthlyAmenityInvoices
GET    /invoices/amenity-usage-summary   → getAmenityUsageSummary
```

## Controllers

### InvoiceController

#### Key Methods

##### `index(Request $request)`
- **Purpose**: Display paginated list of invoices
- **Features**: Search, filtering, sorting, bulk actions
- **Returns**: Data table with invoice information
- **Filters**: Status, type, tenant, date range
- **Includes**: PDF download links, payment status

##### `create(Request $request)`
- **Purpose**: Show invoice creation form
- **Features**: Tenant selection, type selection, item management
- **Data**: Active tenants, amenity subscriptions
- **Validation**: Required fields, date validation

##### `generateRentInvoice(Request $request)`
- **Purpose**: Generate monthly rent invoice for tenant
- **Features**: Automatic calculation, proration support
- **Validation**: Tenant exists, no duplicate for period
- **Response**: JSON with success/error status

##### `generateAmenityInvoice(Request $request)`
- **Purpose**: Generate invoice for specific amenity usage
- **Features**: Usage-based calculation, period validation
- **Integration**: Links with usage tracking system
- **Response**: JSON with invoice details

##### `generateMonthlyAmenityInvoices(Request $request)`
- **Purpose**: Bulk generate amenity invoices for all tenants
- **Features**: Batch processing, usage aggregation
- **Performance**: Optimized for large datasets
- **Response**: JSON with generation summary

##### `downloadPdf(Invoice $invoice)`
- **Purpose**: Generate and download PDF invoice
- **Features**: Professional template, company branding
- **Security**: Access control, tenant data protection
- **Response**: PDF file download

##### `emailPdf(Invoice $invoice)`
- **Purpose**: Email PDF invoice to tenant
- **Features**: Email template, attachment handling
- **Validation**: Valid email address, invoice status
- **Response**: JSON with email status

## Models

### Invoice

#### Fillable Fields
```php
[
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
    'created_by'
]
```

#### Casts
```php
[
    'invoice_date' => 'date',
    'due_date' => 'date',
    'period_start' => 'date',
    'period_end' => 'date',
    'subtotal' => 'decimal:2',
    'tax_amount' => 'decimal:2',
    'discount_amount' => 'decimal:2',
    'total_amount' => 'decimal:2',
    'paid_amount' => 'decimal:2',
    'balance_amount' => 'decimal:2'
]
```

#### Key Methods

##### `generateInvoiceNumber()`
- **Purpose**: Generate unique invoice number
- **Format**: INV-YYYY-NNNNNN (e.g., INV-2024-000001)
- **Logic**: Year-based sequential numbering
- **Uniqueness**: Database constraint ensures uniqueness

##### `calculateTotals()`
- **Purpose**: Recalculate invoice totals from items
- **Logic**: Sum items, apply tax and discounts
- **Updates**: Subtotal, tax, discount, total amounts
- **Triggers**: Called after item changes

##### `addPayment($amount, $paymentData = [])`
- **Purpose**: Add payment to invoice
- **Features**: Payment validation, balance update
- **Integration**: Creates payment record
- **Response**: Payment model instance

##### `createAmenityUsageInvoice($tenantProfile, $usageData, $period)`
- **Purpose**: Create invoice from usage data
- **Features**: Usage aggregation, item creation
- **Validation**: Usage data validation
- **Response**: Invoice model instance

### InvoiceItem

#### Fillable Fields
```php
[
    'invoice_id',
    'item_type',
    'description',
    'quantity',
    'unit_price',
    'total_price',
    'period_start',
    'period_end',
    'tenant_amenity_id'
]
```

#### Casts
```php
[
    'quantity' => 'integer',
    'unit_price' => 'decimal:2',
    'total_price' => 'decimal:2',
    'period_start' => 'date',
    'period_end' => 'date'
]
```

## Views

### File Structure
```
resources/views/invoices/
├── index.blade.php      → Main invoices listing
├── create.blade.php     → Create new invoice form
├── edit.blade.php       → Edit invoice form
├── show.blade.php       → View invoice details

resources/views/pdf/
└── invoice.blade.php    → PDF invoice template
```

### Key Features

#### Invoice Index
- **Comprehensive List**: All invoices with key information
- **Advanced Filters**: Status, type, tenant, date range
- **Bulk Actions**: Mass operations on selected invoices
- **Quick Actions**: PDF download, email, payment links
- **Status Indicators**: Visual status badges

#### Invoice Details
- **Complete Information**: All invoice and item details
- **Payment History**: Linked payments and balance
- **PDF Actions**: View, download, email options
- **Edit Controls**: Modify invoice if not paid
- **Communication Log**: Email history and status

#### PDF Template
- **Professional Design**: Company branding and layout
- **Complete Details**: All invoice information
- **Itemized List**: Detailed line items
- **Payment Summary**: Payment history and balance
- **Terms & Conditions**: Legal terms and payment info

## PDF Generation

### Template Features
- **Company Header**: Logo, name, address, contact info
- **Invoice Details**: Number, date, due date, period
- **Tenant Information**: Name, room, contact details
- **Itemized List**: Description, quantity, price, total
- **Summary Section**: Subtotal, tax, discount, total
- **Payment Info**: Payment methods, terms, notes

### Technical Implementation
- **Library**: DomPDF for PDF generation
- **Template**: Blade template with CSS styling
- **Fonts**: DejaVu Sans for Unicode support
- **Options**: A4 paper, portrait orientation
- **Security**: Access control and data validation

## Integration Points

### With Tenant Management
- **Profile Integration**: Links to tenant profiles
- **Billing Address**: Uses tenant billing information
- **Communication**: Email preferences and history
- **Room Details**: Room and bed information

### With Usage Tracking
- **Automatic Generation**: Usage-based invoice creation
- **Item Details**: Detailed usage breakdown
- **Period Calculation**: Accurate billing periods
- **Real-time Updates**: Live usage integration

### With Payment System
- **Payment Tracking**: Link payments to invoices
- **Balance Updates**: Automatic balance calculations
- **Payment Status**: Real-time payment status
- **Reconciliation**: Payment matching and verification

### With Amenity System
- **Service Integration**: Amenity subscription details
- **Usage Calculation**: Usage-based billing
- **Proration**: Partial period calculations
- **Custom Pricing**: Tenant-specific pricing

## Automation Features

### Scheduled Generation
- **Monthly Rent**: Automatic monthly rent invoices
- **Amenity Usage**: Monthly usage-based invoices
- **Due Date Reminders**: Automated reminder emails
- **Overdue Processing**: Automatic status updates

### Artisan Commands
```php
// Generate monthly rent invoices
php artisan invoices:generate-rent {month?}

// Generate amenity usage invoices
php artisan invoices:generate-amenity-usage {month?}

// Send due date reminders
php artisan invoices:send-reminders

// Update overdue invoices
php artisan invoices:update-overdue
```

## Business Logic

### Invoice Types
1. **Rent**: Monthly room rent charges
2. **Amenities**: Usage-based amenity charges
3. **Damage**: Damage and repair charges
4. **Other**: Miscellaneous charges and fees

### Status Workflow
1. **Draft**: Invoice created but not finalized
2. **Sent**: Invoice sent to tenant
3. **Paid**: Invoice fully paid
4. **Overdue**: Past due date with outstanding balance
5. **Cancelled**: Invoice cancelled or voided

### Calculation Logic
- **Subtotal**: Sum of all item totals
- **Tax**: Applied based on configuration
- **Discount**: Item or invoice level discounts
- **Total**: Subtotal + tax - discount
- **Balance**: Total - paid amount

## Configuration

### Invoice Settings
```php
// config/invoice.php
'number_format' => 'INV-{year}-{sequence}',
'due_days' => 7,
'tax_rate' => 0.18,
'currency' => 'INR',
'company' => [
    'name' => 'Hostel Management',
    'address' => '...',
    'phone' => '...',
    'email' => '...'
]
```

### PDF Settings
```php
'pdf' => [
    'paper' => 'a4',
    'orientation' => 'portrait',
    'font' => 'DejaVu Sans',
    'options' => [
        'isRemoteEnabled' => true,
        'isHtml5ParserEnabled' => true
    ]
]
```

## API Endpoints

### Invoice Management
```php
GET    /api/invoices                     → List invoices
POST   /api/invoices                     → Create invoice
GET    /api/invoices/{id}                → Get invoice details
PUT    /api/invoices/{id}                → Update invoice
DELETE /api/invoices/{id}                → Delete invoice
```

### Generation Endpoints
```php
POST   /api/invoices/generate-rent       → Generate rent invoice
POST   /api/invoices/generate-amenity    → Generate amenity invoice
GET    /api/invoices/usage-summary       → Get usage summary
```

## Security & Validation

### Data Validation
- **Amount Validation**: Positive numbers, decimal precision
- **Date Validation**: Logical date relationships
- **Status Validation**: Valid status transitions
- **Tenant Validation**: Active tenant profiles

### Access Control
- **Authentication**: All operations require login
- **Authorization**: Role-based access control
- **Data Privacy**: Tenant data protection
- **Audit Trail**: Track all changes

### PDF Security
- **Access Control**: Verify user permissions
- **Data Sanitization**: Clean input data
- **File Security**: Secure file generation
- **Download Limits**: Prevent abuse

## Performance Optimization

### Database Optimization
- **Indexes**: Optimized queries with proper indexing
- **Eager Loading**: Prevent N+1 query problems
- **Pagination**: Efficient data loading
- **Caching**: Cache frequently accessed data

### PDF Generation
- **Async Processing**: Background PDF generation
- **Caching**: Cache generated PDFs
- **Optimization**: Optimized templates
- **Memory Management**: Efficient memory usage

## Usage Examples

### Creating Manual Invoice
1. Navigate to "Invoices" section
2. Click "Create Invoice"
3. Select tenant and invoice type
4. Add invoice items with details
5. Review totals and save
6. Send to tenant via email

### Generating Rent Invoices
1. Use bulk generation feature
2. Select billing month
3. Choose tenants (or all)
4. Review generated invoices
5. Send batch emails to tenants

### Processing Payments
1. View invoice details
2. Record payment received
3. System updates balance automatically
4. Invoice status changes to "Paid"
5. Generate payment receipt

## Troubleshooting

### Common Issues

#### PDF Generation Fails
- **Check**: DomPDF library installation
- **Verify**: Template syntax and CSS
- **Ensure**: Sufficient memory allocation
- **Debug**: Check error logs

#### Email Delivery Issues
- **Validate**: SMTP configuration
- **Check**: Recipient email addresses
- **Verify**: Email template syntax
- **Monitor**: Email queue status

#### Calculation Errors
- **Check**: Item quantities and prices
- **Verify**: Tax and discount settings
- **Ensure**: Proper decimal handling
- **Debug**: Calculation logic

## Future Enhancements

### Planned Features
- **Multi-currency Support**: Handle different currencies
- **Advanced Templates**: Customizable PDF templates
- **Payment Gateway**: Online payment integration
- **Recurring Invoices**: Automated recurring billing
- **Invoice Approval**: Multi-level approval workflow

### Advanced Features
- **API Integration**: Third-party accounting systems
- **Mobile App**: Mobile invoice management
- **Analytics**: Advanced billing analytics
- **AI Integration**: Smart billing predictions

---

## Related Documentation
- [Payment System](./payment.md)
- [Amenity Usage Tracking](./amenity-usage.md)
- [Tenant Management](./tenant.md)
- [Paid Amenities](./paid-amenities.md)
- [Table Standards](../table-standards.md)
