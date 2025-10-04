# Hostel Module

## Overview

The Hostel module is the core component of the Hostel CRM system, providing comprehensive management capabilities for hostel properties. It handles hostel registration, information management, and operational oversight.

## Features

- **Complete CRUD Operations**: Create, read, update, and delete hostels
- **Comprehensive Information Management**: Detailed hostel profiles
- **Status Management**: Active, inactive, and maintenance states
- **Amenities Tracking**: Facility and service management
- **Manager Information**: Contact and management details
- **Advanced Search & Filtering**: Find hostels quickly
- **Bulk Operations**: Manage multiple hostels simultaneously
- **Responsive Design**: Mobile-friendly interface
- **Data Export**: Export hostel information

## File Structure

```
app/
├── Http/Controllers/
│   └── HostelController.php       # Hostel management logic
├── Models/
│   └── Hostel.php                 # Hostel model and relationships

database/
├── migrations/
│   └── create_hostels_table.php   # Database schema
└── seeders/
    ├── HostelSeeder.php           # Demo data seeder
    └── DatabaseSeeder.php         # Main seeder (includes HostelSeeder)

resources/views/hostels/
├── index.blade.php                # Hostel listing page
├── show.blade.php                 # Hostel detail page
├── create.blade.php               # Create hostel form
└── edit.blade.php                 # Edit hostel form
```

## Database Schema

### Hostels Table
```sql
CREATE TABLE hostels (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    state VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    postal_code VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    website VARCHAR(255) NULL,
    total_rooms INT NOT NULL,
    total_beds INT NOT NULL,
    rent_per_bed DECIMAL(10,2) NOT NULL,
    amenities JSON NULL,
    images JSON NULL,
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    manager_name VARCHAR(255) NOT NULL,
    manager_phone VARCHAR(255) NOT NULL,
    manager_email VARCHAR(255) NOT NULL,
    rules TEXT NULL,
    check_in_time TIME DEFAULT '14:00:00',
    check_out_time TIME DEFAULT '11:00:00',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Field Descriptions

#### Basic Information
- **name**: Hostel name (required)
- **description**: Detailed description of the hostel
- **address**: Street address
- **city**: City name
- **state**: State or province
- **country**: Country name
- **postal_code**: Postal or ZIP code

#### Contact Information
- **phone**: Primary contact phone number
- **email**: Primary contact email
- **website**: Hostel website URL (optional)

#### Capacity & Pricing
- **total_rooms**: Total number of rooms
- **total_beds**: Total number of beds
- **rent_per_bed**: Monthly rent per bed

#### Features & Status
- **amenities**: JSON array of available amenities
- **images**: JSON array of image URLs
- **status**: Current operational status

#### Management
- **manager_name**: Property manager name
- **manager_phone**: Manager contact phone
- **manager_email**: Manager contact email
- **rules**: House rules and regulations
- **check_in_time**: Standard check-in time
- **check_out_time**: Standard check-out time

## Model: Hostel

### Fillable Fields
```php
protected $fillable = [
    'name', 'description', 'address', 'city', 'state', 'country',
    'postal_code', 'phone', 'email', 'website', 'total_rooms',
    'total_beds', 'rent_per_bed', 'amenities', 'images', 'status',
    'manager_name', 'manager_phone', 'manager_email', 'rules',
    'check_in_time', 'check_out_time'
];
```

### Casts
```php
protected $casts = [
    'amenities' => 'array',
    'images' => 'array',
    'rent_per_bed' => 'decimal:2',
    'check_in_time' => 'datetime:H:i',
    'check_out_time' => 'datetime:H:i',
];
```

### Accessors
```php
// Full address accessor
public function getFullAddressAttribute(): string
{
    return "{$this->address}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}";
}

// Formatted rent accessor
public function getFormattedRentAttribute(): string
{
    return '$' . number_format($this->rent_per_bed, 2);
}

// Occupancy rate accessor
public function getOccupancyRateAttribute(): float
{
    // This would be calculated based on actual tenant data
    return 0.0;
}
```

### Scopes
```php
// Active hostels scope
public function scopeActive($query)
{
    return $query->where('status', 'active');
}

// Inactive hostels scope
public function scopeInactive($query)
{
    return $query->where('status', 'inactive');
}

// Maintenance hostels scope
public function scopeMaintenance($query)
{
    return $query->where('status', 'maintenance');
}
```

## Database Integration

### Real Database Connection
The hostel module is fully integrated with the database using Laravel's Eloquent ORM. All data is stored and retrieved from the actual database instead of using hardcoded demo data.

### Data Flow
1. **Controller**: `HostelController` uses `Hostel::all()` and `Hostel::findOrFail($id)` to fetch data
2. **Model**: `Hostel` model handles database interactions and data casting
3. **Views**: Blade templates use Eloquent model properties (`$hostel->name`) instead of array syntax
4. **Database**: SQLite database stores all hostel records with proper relationships

### Seeder: HostelSeeder

#### Purpose
The `HostelSeeder` provides comprehensive demo data for testing and development purposes.

#### Demo Data Includes
- **6 Hostels** across different US cities
- **Varied Statuses**: Active, inactive, and maintenance states
- **Rich Information**: Descriptions, amenities, manager details, rules
- **Realistic Data**: Phone numbers, emails, addresses, pricing

#### Seeder Data
```php
$hostels = [
    [
        'name' => 'Sunrise Hostel',
        'description' => 'A modern, comfortable hostel located in the heart of the city...',
        'address' => '123 Main Street',
        'city' => 'New York',
        'state' => 'NY',
        'country' => 'USA',
        'postal_code' => '10001',
        'phone' => '+1-555-0123',
        'email' => 'info@sunrisehostel.com',
        'website' => 'https://sunrisehostel.com',
        'total_rooms' => 50,
        'total_beds' => 200,
        'rent_per_bed' => 450.00,
        'amenities' => ['WiFi', 'Laundry', 'Kitchen', 'Common Room', 'Parking', 'Security'],
        'images' => ['hostel1.jpg', 'hostel2.jpg', 'hostel3.jpg'],
        'status' => 'active',
        'manager_name' => 'John Smith',
        'manager_phone' => '+1-555-0123',
        'manager_email' => 'john@sunrisehostel.com',
        'rules' => 'No smoking, No pets, Quiet hours 10 PM - 7 AM, Visitors must be registered',
        'check_in_time' => '14:00:00',
        'check_out_time' => '11:00:00',
    ],
    // ... 5 more hostels
];
```

#### Running the Seeder
```bash
# Run specific seeder
php artisan db:seed --class=HostelSeeder

# Run all seeders (includes HostelSeeder)
php artisan db:seed
```

#### Database Seeder Integration
The `HostelSeeder` is automatically included in the main `DatabaseSeeder`:

```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    $this->call([
        UserSeeder::class,
        HostelSeeder::class,  // Added for hostel demo data
    ]);
}
```

## Controller: HostelController

### Methods

#### `index()`
- **Purpose**: Display list of all hostels from database
- **Route**: `GET /hostels`
- **Features**: Search, filtering, pagination, bulk actions
- **Data Source**: Real database records via Eloquent ORM
- **Returns**: Hostel listing view with data table

#### `create()`
- **Purpose**: Show form to create new hostel
- **Route**: `GET /hostels/create`
- **Returns**: Create hostel form view

#### `store(Request $request)`
- **Purpose**: Store new hostel in database
- **Route**: `POST /hostels`
- **Validation**: Required fields and data validation
- **Returns**: Redirect to hostel list with success message

#### `show(string $id)`
- **Purpose**: Display detailed hostel information from database
- **Route**: `GET /hostels/{id}`
- **Data Source**: Single hostel record via `Hostel::findOrFail($id)`
- **Returns**: Hostel detail view with real data

#### `edit(string $id)`
- **Purpose**: Show form to edit existing hostel from database
- **Route**: `GET /hostels/{id}/edit`
- **Data Source**: Single hostel record via `Hostel::findOrFail($id)`
- **Returns**: Edit hostel form view with pre-populated data

#### `update(Request $request, string $id)`
- **Purpose**: Update existing hostel information
- **Route**: `PUT/PATCH /hostels/{id}`
- **Validation**: Required fields and data validation
- **Returns**: Redirect to hostel detail with success message

#### `destroy(string $id)`
- **Purpose**: Delete hostel from database
- **Route**: `DELETE /hostels/{id}`
- **Returns**: Redirect to hostel list with success message

## Views

### Index Page (`hostels/index.blade.php`)

#### Features
- **Statistics Cards**: Overview of hostel metrics
- **Data Table**: Comprehensive hostel listing
- **Search Functionality**: Find hostels by name, city, etc.
- **Filtering**: Filter by status, city, rent range
- **Bulk Actions**: Activate, deactivate, set maintenance, export
- **Pagination**: Navigate through large datasets
- **Responsive Design**: Mobile-friendly interface

#### Statistics Display
```php
// Statistics cards - Real database data
<x-stats-card title="Total Hostels" value="{{ count($hostels) }}" />
<x-stats-card title="Active Hostels" value="{{ collect($hostels)->where('status', 'active')->count() }}" />
<x-stats-card title="Total Rooms" value="{{ collect($hostels)->sum('total_rooms') }}" />
<x-stats-card title="Total Beds" value="{{ collect($hostels)->sum('total_beds') }}" />
```

#### Table Columns
- **Hostel Name**: Primary identifier
- **Address**: Location information
- **City**: Geographic location
- **Rooms**: Total room count
- **Beds**: Total bed count
- **Rent/Bed**: Pricing information
- **Status**: Current operational status
- **Occupancy**: Occupancy rate percentage

### Show Page (`hostels/show.blade.php`)

#### Layout Structure
- **Header**: Hostel name and back navigation
- **Main Content**: Detailed information sections
- **Sidebar**: Manager info, address, actions, timestamps

#### Information Sections

##### Basic Information
- Description and status
- Room and bed counts
- Rent per bed
- Check-in/out times

##### Contact Information
- Phone, email, website
- Clickable website links

##### Amenities
- Visual amenity badges
- Icon-based display

##### Rules & Regulations
- Formatted text display
- Line break preservation

##### Manager Information
- Manager contact details
- Dedicated sidebar section

##### Address
- Complete address display
- Formatted layout

##### Actions
- Edit hostel button
- Delete hostel button
- Confirmation dialogs

### Edit Page (`hostels/edit.blade.php`)

#### Form Structure
- **Basic Information**: Name, description, capacity, pricing
- **Address Information**: Complete address fields
- **Contact Information**: Phone, email, website
- **Manager Information**: Manager contact details
- **Timing & Rules**: Check-in/out times, house rules
- **Amenities**: Checkbox selection of available amenities

#### Form Features
- **Validation**: Client and server-side validation
- **Required Fields**: Clearly marked required fields
- **Data Binding**: Pre-populated with existing data
- **Responsive Layout**: Mobile-friendly form design
- **Action Buttons**: Save and cancel options

#### Amenities Selection
```php
$availableAmenities = [
    'WiFi', 'Laundry', 'Kitchen', 'Common Room', 
    'Parking', 'Security', 'Gym', 'Study Room', 
    'Cafeteria', 'Library', 'Cleaning Service', 'Air Conditioning'
];
```

## Routes

### Resource Routes
```php
// Hostel resource routes
Route::resource('hostels', HostelController::class)->middleware('auth');
```

### Generated Routes
- `GET /hostels` - List hostels
- `GET /hostels/create` - Show create form
- `POST /hostels` - Store new hostel
- `GET /hostels/{id}` - Show hostel details
- `GET /hostels/{id}/edit` - Show edit form
- `PUT/PATCH /hostels/{id}` - Update hostel
- `DELETE /hostels/{id}` - Delete hostel

## Data Table Configuration

### Columns Definition
```php
$columns = [
    ['key' => 'name', 'label' => 'Hostel Name', 'width' => 'w-48'],
    ['key' => 'address', 'label' => 'Address', 'width' => 'w-64'],
    ['key' => 'city', 'label' => 'City', 'width' => 'w-24'],
    ['key' => 'total_rooms', 'label' => 'Rooms', 'width' => 'w-20'],
    ['key' => 'total_beds', 'label' => 'Beds', 'width' => 'w-20'],
    ['key' => 'rent_per_bed', 'label' => 'Rent/Bed', 'width' => 'w-24'],
    ['key' => 'status', 'label' => 'Status', 'component' => 'components.status-badge', 'width' => 'w-24'],
    ['key' => 'occupancy_rate', 'label' => 'Occupancy', 'width' => 'w-24']
];
```

### Filters Configuration
```php
$filters = [
    [
        'key' => 'status',
        'label' => 'Status',
        'type' => 'select',
        'options' => [
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
            ['value' => 'maintenance', 'label' => 'Maintenance']
        ]
    ],
    [
        'key' => 'city',
        'label' => 'City',
        'type' => 'select',
        'options' => [
            ['value' => 'New York', 'label' => 'New York'],
            ['value' => 'Los Angeles', 'label' => 'Los Angeles'],
            // ... more cities
        ]
    ],
    [
        'key' => 'rent_range',
        'label' => 'Rent Range',
        'type' => 'range',
        'min' => 300,
        'max' => 600
    ]
];
```

### Bulk Actions Configuration
```php
$bulkActions = [
    [
        'key' => 'activate',
        'label' => 'Activate',
        'icon' => 'fas fa-check'
    ],
    [
        'key' => 'deactivate',
        'label' => 'Deactivate',
        'icon' => 'fas fa-times'
    ],
    [
        'key' => 'maintenance',
        'label' => 'Set Maintenance',
        'icon' => 'fas fa-tools'
    ],
    [
        'key' => 'export',
        'label' => 'Export Data',
        'icon' => 'fas fa-download'
    ]
];
```

## Validation Rules

### Create/Update Validation
```php
$request->validate([
    'name' => 'required|string|max:255',
    'description' => 'nullable|string',
    'address' => 'required|string|max:255',
    'city' => 'required|string|max:255',
    'state' => 'required|string|max:255',
    'country' => 'required|string|max:255',
    'postal_code' => 'required|string|max:255',
    'phone' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'website' => 'nullable|url|max:255',
    'total_rooms' => 'required|integer|min:1',
    'total_beds' => 'required|integer|min:1',
    'rent_per_bed' => 'required|numeric|min:0',
    'amenities' => 'nullable|array',
    'status' => 'required|in:active,inactive,maintenance',
    'manager_name' => 'required|string|max:255',
    'manager_phone' => 'required|string|max:255',
    'manager_email' => 'required|email|max:255',
    'rules' => 'nullable|string',
    'check_in_time' => 'required|date_format:H:i',
    'check_out_time' => 'required|date_format:H:i'
]);
```

## JavaScript Functionality

### Delete Confirmation
```javascript
function deleteHostel(url) {
    if (confirm('Are you sure you want to delete this hostel? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add method override for DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
```

### Form Validation
```javascript
// Client-side validation
function validateHostelForm() {
    const requiredFields = ['name', 'address', 'city', 'state', 'country'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}
```

## Status Management

### Status Types
- **Active**: Hostel is operational and accepting tenants
- **Inactive**: Hostel is not operational
- **Maintenance**: Hostel is under maintenance

### Status Badge Component
```html
<x-status-badge :status="$hostel->status" />
```

### Status Colors
- **Active**: Green background with check icon
- **Inactive**: Red background with X icon
- **Maintenance**: Yellow background with tools icon

## Amenities System

### Available Amenities
```php
$availableAmenities = [
    'WiFi' => 'fas fa-wifi',
    'Laundry' => 'fas fa-tshirt',
    'Kitchen' => 'fas fa-utensils',
    'Common Room' => 'fas fa-couch',
    'Parking' => 'fas fa-parking',
    'Security' => 'fas fa-shield-alt',
    'Gym' => 'fas fa-dumbbell',
    'Study Room' => 'fas fa-book',
    'Cafeteria' => 'fas fa-coffee',
    'Library' => 'fas fa-book-open',
    'Cleaning Service' => 'fas fa-broom',
    'Air Conditioning' => 'fas fa-snowflake'
];
```

### Amenities Display
```html
@foreach($hostel->amenities as $amenity)
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
        <i class="fas fa-check mr-1"></i>
        {{ $amenity }}
    </span>
@endforeach
```

## Performance Optimization

### Database Optimization
- **Indexing**: Proper indexes on frequently queried fields
- **Eager Loading**: Load related data efficiently
- **Query Optimization**: Optimize database queries
- **Caching**: Cache frequently accessed data

### Frontend Optimization
- **Lazy Loading**: Load images and content as needed
- **Asset Minification**: Compress CSS and JavaScript
- **CDN**: Use content delivery network for assets
- **Caching**: Browser caching strategies

## Security Considerations

### Access Control
- **Authentication**: Require user authentication
- **Authorization**: Role-based access control
- **Input Validation**: Validate all user inputs
- **CSRF Protection**: Protect against CSRF attacks

### Data Protection
- **Sensitive Data**: Protect sensitive information
- **Audit Logging**: Log all hostel operations
- **Backup**: Regular data backups
- **Encryption**: Encrypt sensitive data

## Testing

### Unit Tests
```php
// Test hostel creation
public function test_can_create_hostel()
{
    $hostelData = [
        'name' => 'Test Hostel',
        'address' => '123 Test Street',
        'city' => 'Test City',
        'state' => 'TS',
        'country' => 'Test Country',
        'postal_code' => '12345',
        'phone' => '123-456-7890',
        'email' => 'test@hostel.com',
        'total_rooms' => 10,
        'total_beds' => 20,
        'rent_per_bed' => 500.00,
        'status' => 'active',
        'manager_name' => 'Test Manager',
        'manager_phone' => '123-456-7890',
        'manager_email' => 'manager@hostel.com',
        'check_in_time' => '14:00:00',
        'check_out_time' => '11:00:00'
    ];
    
    $response = $this->post('/hostels', $hostelData);
    $response->assertRedirect('/hostels');
    $this->assertDatabaseHas('hostels', ['name' => 'Test Hostel']);
}
```

### Feature Tests
```php
// Test hostel listing
public function test_can_view_hostel_list()
{
    $user = User::factory()->create();
    $hostel = Hostel::factory()->create();
    
    $response = $this->actingAs($user)->get('/hostels');
    $response->assertStatus(200);
    $response->assertSee($hostel->name);
}
```

## Troubleshooting

### Common Issues

#### Hostel Not Saving
1. Check validation rules
2. Verify required fields
3. Check database connection
4. Verify form data
5. Check for JavaScript errors

#### Images Not Loading
1. Check file permissions
2. Verify image paths
3. Check storage configuration
4. Verify image URLs
5. Check browser console

#### Search Not Working
1. Check search implementation
2. Verify database queries
3. Check JavaScript functionality
4. Verify data format
5. Check network requests

## Future Enhancements

### Planned Features
- **Image Upload**: File upload for hostel images
- **Location Services**: GPS coordinates and maps
- **Reviews System**: Tenant reviews and ratings
- **Booking System**: Online booking functionality
- **Analytics**: Detailed hostel analytics
- **API Integration**: Third-party integrations
- **Mobile App**: Native mobile application

### Performance Improvements
- **Real-time Updates**: WebSocket integration
- **Advanced Caching**: Redis caching
- **Database Optimization**: Query optimization
- **CDN Integration**: Content delivery network
- **Progressive Web App**: PWA capabilities
