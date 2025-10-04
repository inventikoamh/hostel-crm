# Paid Amenities Module

## Overview
The Paid Amenities module manages additional services that tenants can subscribe to beyond their basic room rent. These services are billed separately and can be charged on daily, weekly, or monthly cycles. Examples include meals (breakfast, lunch, dinner), cleaning services, laundry, Wi-Fi, parking, and other facility-based services.

## Features

### 🎯 Core Functionality
- **Service Management**: Create, edit, and manage paid amenity services
- **Flexible Pricing**: Support for different pricing models and billing cycles
- **Tenant Subscriptions**: Assign amenities to specific tenants
- **Usage Tracking**: Integration with usage tracking system
- **Billing Integration**: Automatic invoice generation based on usage

### 📊 Key Components

#### 1. Amenity Management
- **Service Catalog**: Comprehensive list of available paid services
- **Pricing Configuration**: Flexible pricing with different billing cycles
- **Category Organization**: Group services by type (meals, utilities, etc.)
- **Status Management**: Active/inactive service control

#### 2. Tenant Subscriptions
- **Individual Assignment**: Assign specific amenities to tenants
- **Bulk Operations**: Mass assignment and management
- **Custom Pricing**: Override default pricing for specific tenants
- **Billing Preferences**: Set individual billing cycles per tenant

#### 3. Usage Integration
- **Daily Tracking**: Record actual usage of subscribed services
- **Automatic Billing**: Generate invoices based on actual usage
- **Usage Reports**: Detailed usage analytics and reporting

## Database Structure

### Tables

#### `paid_amenities`
```sql
- id (Primary Key)
- name (Service name, e.g., "Breakfast", "Cleaning")
- description (Detailed service description)
- price (Default price per unit)
- billing_cycle (daily, weekly, monthly)
- category (meals, utilities, services, etc.)
- is_active (Service availability status)
- created_at, updated_at (Timestamps)
```

#### `tenant_amenities`
```sql
- id (Primary Key)
- tenant_profile_id (Foreign Key to tenant_profiles)
- paid_amenity_id (Foreign Key to paid_amenities)
- price (Custom price for this tenant, overrides default)
- billing_cycle (Custom billing cycle for this tenant)
- start_date (When subscription starts)
- end_date (When subscription ends, nullable)
- status (active, inactive, suspended)
- notes (Additional notes)
- created_at, updated_at (Timestamps)
```

### Relationships
- **PaidAmenity**: HasMany TenantAmenities, HasMany TenantAmenityUsage
- **TenantAmenity**: BelongsTo PaidAmenity, BelongsTo TenantProfile, HasMany TenantAmenityUsage
- **TenantProfile**: HasMany TenantAmenities

## Routes

### Paid Amenities Routes
```php
// Main CRUD Operations
GET    /paid-amenities                   → index (list all amenities)
POST   /paid-amenities                   → store (create new amenity)
GET    /paid-amenities/create            → create (show create form)
GET    /paid-amenities/{id}              → show (view amenity details)
GET    /paid-amenities/{id}/edit         → edit (show edit form)
PUT    /paid-amenities/{id}              → update (update amenity)
DELETE /paid-amenities/{id}              → destroy (delete amenity)

// Bulk Operations
POST   /paid-amenities/bulk-action       → bulkAction (bulk operations)
```

### Tenant Amenities Routes
```php
// Subscription Management
GET    /tenant-amenities                 → index (list subscriptions)
POST   /tenant-amenities                 → store (create subscription)
GET    /tenant-amenities/create          → create (show create form)
GET    /tenant-amenities/{id}            → show (view subscription)
GET    /tenant-amenities/{id}/edit       → edit (show edit form)
PUT    /tenant-amenities/{id}            → update (update subscription)
DELETE /tenant-amenities/{id}            → destroy (delete subscription)

// Special Features
GET    /tenant-amenities/billing-summary/{tenant} → getBillingSummary
POST   /tenant-amenities/{id}/usage      → recordUsage
PUT    /tenant-amenities/usage/{usage}   → updateUsage
DELETE /tenant-amenities/usage/{usage}  → deleteUsage
```

## Controllers

### PaidAmenityController

#### Key Methods

##### `index(Request $request)`
- **Purpose**: Display list of all paid amenities
- **Features**: Search, filtering, pagination, bulk actions
- **Returns**: Data table with amenity information
- **Filters**: Category, status, price range

##### `store(Request $request)`
- **Purpose**: Create new paid amenity
- **Validation**: Name, price, billing cycle, category
- **Features**: Duplicate name prevention, price validation
- **Response**: Redirect with success message

##### `bulkAction(Request $request)`
- **Purpose**: Handle bulk operations (activate, deactivate, delete)
- **Features**: Mass operations on selected amenities
- **Validation**: Action type and selected items
- **Transaction**: Database transaction for data integrity

### TenantAmenityController

#### Key Methods

##### `index(Request $request)`
- **Purpose**: Display tenant amenity subscriptions
- **Features**: Tenant filtering, status filtering, search
- **Returns**: Data table with subscription details
- **Includes**: Tenant info, amenity details, usage statistics

##### `create(Request $request)`
- **Purpose**: Show subscription creation form
- **Features**: Tenant pre-selection, amenity dropdown
- **Data**: Active tenants, available amenities
- **Validation**: Prevent duplicate subscriptions

##### `getBillingSummary($tenant)`
- **Purpose**: Get billing summary for specific tenant
- **Returns**: JSON with usage and billing data
- **Features**: Current month summary, pending charges
- **Usage**: AJAX endpoint for dynamic loading

## Models

### PaidAmenity

#### Fillable Fields
```php
[
    'name',
    'description',
    'price',
    'billing_cycle',
    'category',
    'is_active'
]
```

#### Casts
```php
[
    'price' => 'decimal:2',
    'is_active' => 'boolean'
]
```

#### Scopes
- `active()`: Only active amenities
- `byCategory($category)`: Filter by category
- `priceRange($min, $max)`: Filter by price range

#### Accessors
- `getFormattedPriceAttribute()`: Returns formatted currency
- `getBillingCycleTextAttribute()`: Human-readable billing cycle

### TenantAmenity

#### Fillable Fields
```php
[
    'tenant_profile_id',
    'paid_amenity_id',
    'price',
    'billing_cycle',
    'start_date',
    'end_date',
    'status',
    'notes'
]
```

#### Casts
```php
[
    'price' => 'decimal:2',
    'start_date' => 'date',
    'end_date' => 'date'
]
```

#### Scopes
- `active()`: Only active subscriptions
- `forTenant($tenantId)`: Filter by tenant
- `forAmenity($amenityId)`: Filter by amenity
- `currentMonth()`: Current month subscriptions

## Views

### File Structure
```
resources/views/paid-amenities/
├── index.blade.php      → Main amenities listing
├── create.blade.php     → Create new amenity form
├── edit.blade.php       → Edit amenity form
└── show.blade.php       → View amenity details

resources/views/tenant-amenities/
├── index.blade.php      → Tenant subscriptions listing
├── create.blade.php     → Create subscription form
├── edit.blade.php       → Edit subscription form
└── show.blade.php       → View subscription details
```

### Key Features

#### Paid Amenities Index
- **Service Catalog**: Grid/table view of all services
- **Category Filters**: Filter by service category
- **Status Toggle**: Quick activate/deactivate
- **Bulk Actions**: Mass operations on selected items
- **Search**: Find services by name or description

#### Tenant Amenities Management
- **Subscription Overview**: All tenant subscriptions
- **Quick Assignment**: Fast amenity assignment to tenants
- **Usage Summary**: Current usage and billing status
- **Billing Integration**: Link to invoices and payments

## Integration Points

### With Tenant Management
- **Profile Integration**: Links to tenant profiles
- **Bed Assignment**: Considers room and bed location
- **Billing Address**: Uses tenant billing information
- **Communication**: Notification preferences

### With Usage Tracking
- **Daily Recording**: Track actual service usage
- **Billing Calculation**: Usage-based billing
- **Reports**: Usage analytics and trends
- **Attendance**: Daily attendance marking

### With Invoice System
- **Automatic Generation**: Monthly invoice creation
- **Usage-based Items**: Invoice items from usage records
- **Proration**: Partial month calculations
- **Tax Calculation**: Service tax application

### With Payment System
- **Payment Tracking**: Link payments to amenity charges
- **Outstanding Balances**: Track unpaid amenity charges
- **Payment Plans**: Installment options for large amounts
- **Refunds**: Handle service refunds and adjustments

## Business Logic

### Pricing Models
1. **Fixed Monthly**: Same price every month regardless of usage
2. **Daily Usage**: Charged only for days actually used
3. **Per Unit**: Charged per quantity used (e.g., meals)
4. **Tiered Pricing**: Different rates based on usage volume

### Billing Cycles
- **Daily**: Billed daily based on usage
- **Weekly**: Weekly billing cycles
- **Monthly**: Standard monthly billing
- **Custom**: Flexible billing periods

### Subscription Management
- **Start/End Dates**: Flexible subscription periods
- **Proration**: Partial period calculations
- **Suspension**: Temporary service suspension
- **Cancellation**: Proper cancellation handling

## Configuration

### Service Categories
```php
// config/amenities.php
'categories' => [
    'meals' => 'Meals & Food Services',
    'utilities' => 'Utilities & Internet',
    'cleaning' => 'Cleaning Services',
    'laundry' => 'Laundry Services',
    'parking' => 'Parking & Transportation',
    'entertainment' => 'Entertainment & Recreation',
    'other' => 'Other Services'
]
```

### Billing Cycles
```php
'billing_cycles' => [
    'daily' => 'Daily',
    'weekly' => 'Weekly',
    'monthly' => 'Monthly'
]
```

## API Endpoints

### Service Management
```php
GET    /api/paid-amenities              → List all amenities
POST   /api/paid-amenities              → Create amenity
GET    /api/paid-amenities/{id}         → Get amenity details
PUT    /api/paid-amenities/{id}         → Update amenity
DELETE /api/paid-amenities/{id}         → Delete amenity
```

### Subscription Management
```php
GET    /api/tenant-amenities            → List subscriptions
POST   /api/tenant-amenities            → Create subscription
GET    /api/tenant-amenities/{id}       → Get subscription details
PUT    /api/tenant-amenities/{id}       → Update subscription
DELETE /api/tenant-amenities/{id}       → Cancel subscription
```

## Security & Validation

### Data Validation
- **Price Validation**: Positive numbers, decimal precision
- **Date Validation**: Start date before end date
- **Duplicate Prevention**: Unique tenant-amenity combinations
- **Status Validation**: Valid status transitions

### Access Control
- **Authentication**: All operations require login
- **Authorization**: Role-based access control
- **Audit Trail**: Track all changes and modifications
- **Data Privacy**: Tenant data protection

## Performance Optimization

### Database Optimization
- **Indexes**: Optimized queries with proper indexing
- **Eager Loading**: Prevent N+1 query problems
- **Caching**: Cache frequently accessed amenity data
- **Pagination**: Efficient data loading

### Frontend Optimization
- **AJAX Loading**: Dynamic content loading
- **Search Debouncing**: Optimized search performance
- **Lazy Loading**: Load data as needed
- **Caching**: Browser caching for static data

## Usage Examples

### Creating a New Service
1. Navigate to "Paid Services" → "Manage Services"
2. Click "Add New Service"
3. Fill in service details (name, price, category)
4. Set billing cycle and status
5. Save the service

### Assigning Service to Tenant
1. Go to "Paid Services" → "Tenant Services"
2. Click "Add Service"
3. Select tenant and amenity
4. Set custom pricing if needed
5. Configure start/end dates
6. Save subscription

### Managing Subscriptions
1. View all subscriptions in tenant services list
2. Use filters to find specific subscriptions
3. Edit pricing or dates as needed
4. Suspend or cancel services when required
5. Track usage and billing status

## Troubleshooting

### Common Issues

#### Service Not Appearing for Tenant
- **Check**: Service is marked as active
- **Verify**: Tenant has active profile
- **Ensure**: No duplicate subscription exists

#### Billing Not Working
- **Validate**: Billing cycle is properly set
- **Check**: Usage records exist for billing period
- **Verify**: Invoice generation is enabled

#### Price Calculations Wrong
- **Check**: Custom pricing overrides
- **Verify**: Proration calculations
- **Ensure**: Tax settings are correct

## Future Enhancements

### Planned Features
- **Service Packages**: Bundle multiple services
- **Dynamic Pricing**: Time-based pricing variations
- **Loyalty Programs**: Discounts for long-term tenants
- **Service Ratings**: Tenant feedback system
- **Inventory Integration**: Link to inventory management

### Advanced Features
- **API Integration**: Third-party service providers
- **Mobile Ordering**: Mobile app for service requests
- **Automated Billing**: Smart billing based on usage patterns
- **Predictive Analytics**: Usage prediction and optimization

---

## Related Documentation
- [Amenity Usage Tracking](./amenity-usage.md)
- [Tenant Management](./tenant.md)
- [Invoice System](./invoice.md)
- [Payment System](./payment.md)
- [Table Standards](../table-standards.md)
