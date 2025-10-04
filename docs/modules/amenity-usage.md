# Amenity Usage Tracking Module

## Overview
The Amenity Usage Tracking module provides comprehensive functionality for recording, managing, and reporting on daily usage of paid amenities by tenants. This system enables hostels to track services like meals, cleaning, laundry, and other paid facilities with detailed attendance-style recording.

## Features

### 🎯 Core Functionality
- **Daily Attendance Recording**: Mark amenity usage for multiple tenants on a specific date
- **Individual Record Management**: Add, edit, view, and delete individual usage records
- **Comprehensive Reporting**: Generate usage reports with various filters and export capabilities
- **Billing Integration**: Automatic calculation and invoice generation for amenity usage
- **Real-time Statistics**: Usage analytics and summary data
- **Correction Request System**: Tenants can request corrections to their usage records with admin approval workflow

### 📊 Key Components

#### 1. Usage Records Management
- **List View**: Paginated table with search, filter, and bulk actions
- **Create/Edit Forms**: User-friendly forms with validation and auto-calculations
- **Detail View**: Complete record information with related data
- **Bulk Operations**: Mass actions for efficient management

#### 2. Attendance System
- **Date-based Interface**: Select any date to mark attendance
- **Tenant Cards**: Visual cards showing each tenant's amenity subscriptions
- **Quantity Selection**: Support for multiple quantities per usage
- **Notes Support**: Optional notes for each usage record
- **Real-time Calculations**: Automatic price calculations based on quantity

#### 3. Reporting & Analytics
- **Multiple Report Types**: Monthly, daily, tenant-wise, and amenity-wise reports
- **Interactive Charts**: Visual representation using Chart.js
- **Export Functionality**: CSV export for external analysis
- **Date Range Filtering**: Flexible date range selection
- **Summary Statistics**: Key metrics and totals

#### 4. Tenant Portal Integration
- **Usage Tracking**: Tenants can view their usage records
- **Correction Requests**: Tenants can request corrections to their usage records
- **Data Table Interface**: Advanced search, filtering, and pagination
- **Request Management**: Submit correction requests with reason and new values

## Database Structure

### Tables

#### `tenant_amenity_usage`
```sql
- id (Primary Key)
- tenant_amenity_id (Foreign Key to tenant_amenities)
- usage_date (Date of usage)
- quantity (Number of units used)
- unit_price (Price per unit at time of usage)
- total_amount (Calculated total: quantity × unit_price)
- notes (Optional notes)
- recorded_by (User who recorded the usage)
- created_at, updated_at (Timestamps)
```

### Relationships
- **BelongsTo**: `TenantAmenity` (tenant's amenity subscription)
- **BelongsTo**: `User` (recorded_by - who recorded the usage)
- **HasMany**: Through TenantAmenity to `TenantProfile`, `PaidAmenity`

## Routes

### Web Routes
```php
// Main CRUD Operations
GET    /amenity-usage                    → index (list all records)
POST   /amenity-usage                    → store (create new record)
GET    /amenity-usage/create             → create (show create form)
GET    /amenity-usage/{id}               → show (view record details)
GET    /amenity-usage/{id}/edit          → edit (show edit form)
PUT    /amenity-usage/{id}               → update (update record)
DELETE /amenity-usage/{id}               → destroy (delete record)

// Special Features
GET    /amenity-usage/attendance         → attendance (daily marking interface)
POST   /amenity-usage/attendance         → storeAttendance (save attendance)
GET    /amenity-usage/reports            → reports (reporting interface)
GET    /amenity-usage/export             → exportReport (CSV export)
GET    /amenity-usage/stats              → getUsageStats (API endpoint)
```

## Controllers

### AmenityUsageController

#### Key Methods

##### `index(Request $request)`
- **Purpose**: Display paginated list of usage records
- **Features**: Search, filtering, sorting, pagination
- **Returns**: View with data table component
- **Filters**: Date, tenant, amenity, month
- **Search**: Tenant name, amenity name

##### `attendance(Request $request)`
- **Purpose**: Show daily attendance marking interface
- **Features**: Date selection, tenant cards, existing usage display
- **Returns**: Grouped amenities by type with tenant cards
- **Logic**: Groups tenant amenities by paid amenity name for organization

##### `storeAttendance(Request $request)`
- **Purpose**: Process bulk attendance submission
- **Features**: Validation, duplicate prevention, bulk creation
- **Transaction**: Uses database transactions for data integrity
- **Response**: JSON with success/error status

##### `reports(Request $request)`
- **Purpose**: Generate usage reports and analytics
- **Features**: Multiple report types, date filtering, export
- **AJAX Support**: Returns JSON for dynamic report generation
- **Export**: CSV download functionality

## Models

### TenantAmenityUsage

#### Fillable Fields
```php
[
    'tenant_amenity_id',
    'usage_date',
    'quantity',
    'unit_price',
    'total_amount',
    'notes',
    'recorded_by'
]
```

#### Casts
```php
[
    'usage_date' => 'date',
    'quantity' => 'integer',
    'unit_price' => 'decimal:2',
    'total_amount' => 'decimal:2'
]
```

#### Scopes
- `forDate($date)`: Filter by specific date
- `forMonth($year, $month)`: Filter by month and year
- `forDateRange($start, $end)`: Filter by date range
- `forTenant($tenantId)`: Filter by tenant
- `forAmenity($amenityId)`: Filter by amenity

#### Accessors
- `getFormattedTotalAttribute()`: Returns formatted currency
- `getFormattedDateAttribute()`: Returns human-readable date

## Views

### File Structure
```
resources/views/amenity-usage/
├── index.blade.php      → Main listing page
├── create.blade.php     → Create new record form
├── edit.blade.php       → Edit existing record form
├── show.blade.php       → View record details
├── attendance.blade.php → Daily attendance interface
└── reports.blade.php    → Reporting and analytics
```

### Key Features

#### Index Page
- **Header**: Title, subtitle, action buttons
- **Action Buttons**: Mark Attendance, Reports, Add Record
- **Data Table**: Responsive table with search, filters, pagination
- **Mobile Responsive**: Optimized for all screen sizes

#### Attendance Page
- **Date Selection**: Calendar input with validation
- **Amenity Groups**: Organized by amenity type
- **Tenant Cards**: Visual cards with usage controls
- **Real-time Calculations**: Dynamic price updates
- **Form Validation**: Client and server-side validation

#### Reports Page
- **Filter Controls**: Report type, date range selection
- **Chart Display**: Interactive charts using Chart.js
- **Export Options**: CSV download functionality
- **Summary Cards**: Key statistics display

## Styling & UI/UX

### Design System
- **Framework**: Tailwind CSS
- **Theme Support**: Light/Dark mode compatibility
- **Responsive**: Mobile-first design approach
- **Icons**: Font Awesome integration
- **Colors**: CSS custom properties for theming

### Component Usage
- **Data Table**: Reusable data-table component
- **Forms**: Consistent form styling and validation
- **Cards**: Modern card-based layouts
- **Buttons**: Standardized button styles and states
- **Modals**: Interactive modal components

## Integration

### Billing System Integration
- **Invoice Generation**: Automatic monthly invoice creation
- **Usage Summary**: Integration with invoice items
- **Pending Charges**: Detection of unbilled usage
- **Billing Cycles**: Respect tenant billing preferences

### Related Modules
- **Tenant Management**: Links to tenant profiles
- **Paid Amenities**: Uses amenity definitions and pricing
- **Invoice System**: Generates usage-based invoices
- **Payment System**: Tracks payments for amenity usage

## API Endpoints

### Usage Statistics
```php
GET /amenity-usage/stats
```
**Response**: JSON with usage statistics and trends

### Report Data
```php
GET /amenity-usage/reports?type={type}&start_date={date}&end_date={date}
```
**Response**: JSON with report data for charts

### Export
```php
GET /amenity-usage/export?{filters}
```
**Response**: CSV file download

## Security & Permissions

### Authentication
- All routes require authentication (`auth` middleware)
- User session validation on all operations
- CSRF protection on form submissions

### Authorization
- Users can only record usage for active tenants
- Historical data modification tracking
- Audit trail for all usage records

### Data Validation
- **Server-side**: Laravel validation rules
- **Client-side**: JavaScript validation for UX
- **Database**: Foreign key constraints and data integrity

## Performance Considerations

### Database Optimization
- **Indexes**: Optimized queries with proper indexing
- **Eager Loading**: Prevents N+1 query problems
- **Pagination**: Efficient data loading for large datasets
- **Caching**: Strategic caching for frequently accessed data

### Frontend Optimization
- **Lazy Loading**: Components loaded as needed
- **AJAX**: Dynamic content loading without page refresh
- **Debouncing**: Search input optimization
- **Responsive Images**: Optimized for different screen sizes

## Usage Examples

### Recording Daily Attendance
1. Navigate to "Usage Tracking" in sidebar
2. Click "Mark Attendance" button
3. Select date using date picker
4. Check amenities used by each tenant
5. Adjust quantities as needed
6. Add optional notes
7. Submit attendance form

### Generating Reports
1. Go to amenity usage reports page
2. Select report type (Monthly/Daily/Tenant/Amenity)
3. Choose date range
4. Click "Generate Report"
5. View interactive charts
6. Export to CSV if needed

### Managing Individual Records
1. Access main amenity usage page
2. Use search and filters to find records
3. Click on record to view details
4. Edit or delete as needed
5. Add new records using "Add Record" button

## Troubleshooting

### Common Issues

#### No Data Showing
- **Check**: Tenant amenity subscriptions exist
- **Verify**: Date filters are not too restrictive
- **Ensure**: Database seeding has been run

#### Attendance Not Saving
- **Validate**: All required fields are filled
- **Check**: Network connectivity for AJAX requests
- **Verify**: User has proper permissions

#### Reports Not Loading
- **Check**: Date range is valid
- **Verify**: JavaScript is enabled
- **Ensure**: Chart.js library is loaded

## Future Enhancements

### Planned Features
- **Mobile App**: Native mobile app for attendance marking
- **Notifications**: Automated reminders for usage recording
- **Advanced Analytics**: Machine learning for usage predictions
- **Integration**: Third-party service integrations
- **Automation**: Automatic usage detection for certain amenities

### Scalability Considerations
- **Database Partitioning**: For large-scale deployments
- **Caching Strategy**: Redis integration for performance
- **API Rate Limiting**: Prevent abuse of API endpoints
- **Background Jobs**: Queue processing for heavy operations

---

## Related Documentation
- [Paid Amenities Module](./paid-amenities.md)
- [Usage Correction Requests](./usage-correction-requests.md)
- [Tenant Management](./tenant.md)
- [Invoice System](./invoice.md)
- [Table Standards](../table-standards.md)
- [Component Standards](../component-standards.md)
