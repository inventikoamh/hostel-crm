# API & Routes Documentation

## Overview

The Hostel CRM system uses Laravel's routing system to define all application endpoints. This document provides comprehensive information about all routes, their purposes, middleware, and usage patterns.

## Route Structure

### Route Files
- **`routes/web.php`**: Web routes for the application
- **`routes/api.php`**: API routes (future implementation)
- **`routes/console.php`**: Artisan command routes

### Route Groups
Routes are organized into logical groups based on functionality and middleware requirements.

## Web Routes

### Authentication Routes

#### Login Routes
```php
// Display login form
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Process login credentials
Route::post('/login', [AuthController::class, 'login']);

// Logout user
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
```

**Purpose**: Handle user authentication and session management.

**Middleware**: None (public routes).

**Parameters**:
- `email` (required): User email address
- `password` (required): User password
- `remember` (optional): Remember me checkbox

**Responses**:
- Success: Redirect to dashboard
- Failure: Redirect back with validation errors

### Dashboard Routes

#### Main Dashboard
```php
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
```

**Purpose**: Display the main dashboard with statistics and overview.

**Middleware**: `auth` (requires authentication).

**Returns**: Dashboard view with statistics and recent activities.

### Hostel Routes

#### Resource Routes
```php
Route::resource('hostels', HostelController::class)->middleware('auth');
```

**Purpose**: Complete CRUD operations for hostel management.

**Middleware**: `auth` (requires authentication).

**Generated Routes**:
- `GET /hostels` - List all hostels
- `GET /hostels/create` - Show create form
- `POST /hostels` - Store new hostel
- `GET /hostels/{id}` - Show hostel details
- `GET /hostels/{id}/edit` - Show edit form
- `PUT/PATCH /hostels/{id}` - Update hostel
- `DELETE /hostels/{id}` - Delete hostel

#### Route Details

##### List Hostels
```php
GET /hostels
```
- **Controller**: `HostelController@index`
- **Purpose**: Display paginated list of hostels
- **Features**: Search, filtering, bulk actions
- **Returns**: Hostel listing view

##### Create Hostel
```php
GET /hostels/create
POST /hostels
```
- **Controller**: `HostelController@create`, `HostelController@store`
- **Purpose**: Create new hostel
- **Validation**: Required fields validation
- **Returns**: Create form view, redirect to list on success

##### Show Hostel
```php
GET /hostels/{id}
```
- **Controller**: `HostelController@show`
- **Purpose**: Display detailed hostel information
- **Returns**: Hostel detail view

##### Edit Hostel
```php
GET /hostels/{id}/edit
PUT/PATCH /hostels/{id}
```
- **Controller**: `HostelController@edit`, `HostelController@update`
- **Purpose**: Edit existing hostel
- **Validation**: Required fields validation
- **Returns**: Edit form view, redirect to detail on success

##### Delete Hostel
```php
DELETE /hostels/{id}
```
- **Controller**: `HostelController@destroy`
- **Purpose**: Delete hostel
- **Returns**: Redirect to list with success message

### Tenant Routes

#### Resource Routes
```php
Route::resource('tenants', TenantController::class)->middleware('auth');
```

**Purpose**: Complete CRUD operations for tenant management with billing cycles.

**Middleware**: `auth` (requires authentication).

#### Additional Tenant Routes
```php
// AJAX endpoint for dynamic bed loading
Route::get('/tenants/available-beds/{hostel}', [TenantController::class, 'getAvailableBeds'])
     ->name('tenants.available-beds')->middleware('auth');

// Tenant verification
Route::post('/tenants/{tenant}/verify', [TenantController::class, 'verify'])
     ->name('tenants.verify')->middleware('auth');

// Tenant move-out processing
Route::post('/tenants/{tenant}/move-out', [TenantController::class, 'moveOut'])
     ->name('tenants.move-out')->middleware('auth');
```

**Generated Resource Routes**:
- `GET /tenants` - List all tenants with filtering and search
- `GET /tenants/create` - Show tenant creation form
- `POST /tenants` - Store new tenant with profile and bed assignment
- `GET /tenants/{id}` - Show tenant details with billing information
- `GET /tenants/{id}/edit` - Show tenant edit form
- `PUT/PATCH /tenants/{id}` - Update tenant with bed reassignment
- `DELETE /tenants/{id}` - Delete tenant and release bed

### Room Routes

#### Resource Routes
```php
Route::resource('rooms', RoomController::class)->middleware('auth');
```

**Purpose**: Complete CRUD operations for room and bed management.

**Middleware**: `auth` (requires authentication).

**Generated Resource Routes**:
- `GET /rooms` - List all rooms with occupancy statistics
- `GET /rooms/create` - Show room creation form
- `POST /rooms` - Store new room with automatic bed creation
- `GET /rooms/{id}` - Show room details with bed layout
- `GET /rooms/{id}/edit` - Show room edit form
- `PUT/PATCH /rooms/{id}` - Update room and manage bed capacity
- `DELETE /rooms/{id}` - Delete room and associated beds

### Map Routes

#### Map Visualization Routes
```php
Route::prefix('map')->name('map.')->middleware('auth')->group(function () {
    Route::get('/', [MapController::class, 'index'])->name('index');
    Route::get('/hostel/{hostel}', [MapController::class, 'hostel'])->name('hostel');
    Route::get('/occupancy/{hostel}/{floor?}', [MapController::class, 'occupancyData'])->name('occupancy');
    Route::post('/bed/{bed}/status', [MapController::class, 'updateBedStatus'])->name('bed.status');
});
```

**Purpose**: Visual hostel mapping with interactive floor plans and bed status.

**Middleware**: `auth` (requires authentication).

**Route Details**:
- `GET /map` - Overview of all hostels with occupancy statistics
- `GET /map/hostel/{hostel}` - Floor-wise room and bed visualization
- `GET /map/occupancy/{hostel}/{floor?}` - AJAX endpoint for occupancy data
- `POST /map/bed/{bed}/status` - Update bed status via AJAX

### Enquiry Routes

#### Public Routes (No Authentication)
```php
Route::get('/contact', [EnquiryController::class, 'publicForm'])->name('enquiry.form');
Route::post('/contact', [EnquiryController::class, 'store'])->name('enquiry.store');
Route::get('/contact/success', [EnquiryController::class, 'success'])->name('enquiry.success');
```

**Purpose**: Public enquiry form for potential tenants.

**Middleware**: None (public access).

#### Admin Routes (Authentication Required)
```php
Route::resource('enquiries', EnquiryController::class)->middleware('auth');
Route::post('/enquiries/{enquiry}/convert', [EnquiryController::class, 'convertToTenant'])
     ->name('enquiries.convert')->middleware('auth');
Route::post('/enquiries/bulk-update', [EnquiryController::class, 'bulkUpdate'])
     ->name('enquiries.bulk-update')->middleware('auth');
```

**Purpose**: Administrative enquiry management and conversion.

**Middleware**: `auth` (requires authentication).

#### Admin Usage Correction Request Routes
```php
// Usage correction request management
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('usage-correction-requests', Admin\UsageCorrectionRequestController::class)
        ->except(['create', 'edit', 'store', 'update']);
    Route::post('/usage-correction-requests/{usageCorrectionRequest}/approve', 
        [Admin\UsageCorrectionRequestController::class, 'approve'])
        ->name('usage-correction-requests.approve');
    Route::post('/usage-correction-requests/{usageCorrectionRequest}/reject', 
        [Admin\UsageCorrectionRequestController::class, 'reject'])
        ->name('usage-correction-requests.reject');
    Route::post('/usage-correction-requests/bulk-action', 
        [Admin\UsageCorrectionRequestController::class, 'bulkAction'])
        ->name('usage-correction-requests.bulk-action');
});
```

**Purpose**: Administrative management of tenant usage correction requests.

**Middleware**: `auth` (authentication required).

**Features**:
- List all correction requests with filtering and search
- View detailed request information
- Approve or reject individual requests
- Bulk approve/reject multiple requests
- Admin notes for approval/rejection decisions

### Configuration Routes

#### Amenities Management
```php
Route::prefix('config')->name('config.')->middleware('auth')->group(function () {
    Route::resource('amenities', AmenityController::class);
});
```

**Purpose**: Manage configurable amenities for hostels.

**Middleware**: `auth` (requires authentication).

**Generated Routes**:
- `GET /config/amenities` - List all amenities
- `GET /config/amenities/create` - Show amenity creation form
- `POST /config/amenities` - Store new amenity
- `GET /config/amenities/{id}` - Show amenity details
- `GET /config/amenities/{id}/edit` - Show amenity edit form
- `PUT/PATCH /config/amenities/{id}` - Update amenity
- `DELETE /config/amenities/{id}` - Delete amenity

## Route Parameters

### Model Binding
Laravel automatically resolves route parameters to model instances:

```php
// Automatic model binding
Route::get('/hostels/{hostel}', [HostelController::class, 'show']);

// In controller
public function show(Hostel $hostel)
{
    // $hostel is automatically resolved
    return view('hostels.show', compact('hostel'));
}
```

### Parameter Constraints
```php
// Constrain parameters to specific patterns
Route::get('/hostels/{id}', [HostelController::class, 'show'])
    ->where('id', '[0-9]+');

// Multiple constraints
Route::get('/hostels/{hostel}/tenants/{tenant}', [TenantController::class, 'show'])
    ->where(['hostel' => '[0-9]+', 'tenant' => '[0-9]+']);
```

## Middleware

### Authentication Middleware
```php
// Single route
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');

// Route group
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('hostels', HostelController::class);
});
```

### Custom Middleware
```php
// Role-based middleware (future implementation)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// Rate limiting
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/api/login', [AuthController::class, 'apiLogin']);
});
```

## Route Naming

### Named Routes
All routes have descriptive names for easy reference:

```php
// Named routes
Route::get('/hostels', [HostelController::class, 'index'])->name('hostels.index');
Route::get('/hostels/create', [HostelController::class, 'create'])->name('hostels.create');
Route::post('/hostels', [HostelController::class, 'store'])->name('hostels.store');
Route::get('/hostels/{id}', [HostelController::class, 'show'])->name('hostels.show');
Route::get('/hostels/{id}/edit', [HostelController::class, 'edit'])->name('hostels.edit');
Route::put('/hostels/{id}', [HostelController::class, 'update'])->name('hostels.update');
Route::delete('/hostels/{id}', [HostelController::class, 'destroy'])->name('hostels.destroy');
```

### Route Helper Usage
```php
// Generate URLs
route('hostels.index'); // /hostels
route('hostels.show', 1); // /hostels/1
route('hostels.edit', ['hostel' => 1]); // /hostels/1/edit

// In Blade templates
<a href="{{ route('hostels.create') }}">Create Hostel</a>
<a href="{{ route('hostels.show', $hostel->id) }}">View Hostel</a>
```

## Route Model Binding

### Implicit Binding
```php
// Automatic model resolution
Route::get('/hostels/{hostel}', [HostelController::class, 'show']);

// In controller
public function show(Hostel $hostel)
{
    // $hostel is automatically resolved from database
    return view('hostels.show', compact('hostel'));
}
```

### Explicit Binding
```php
// Custom binding logic
Route::model('hostel', Hostel::class);

// In RouteServiceProvider
public function boot()
{
    parent::boot();
    
    Route::model('hostel', Hostel::class, function ($value) {
        throw new ModelNotFoundException("Hostel with ID {$value} not found.");
    });
}
```

## API Routes (Future Implementation)

### Planned API Structure
```php
// API routes with versioning
Route::prefix('api/v1')->group(function () {
    // Authentication
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::post('/refresh', [AuthController::class, 'apiRefresh']);
    
    // Protected routes
    Route::middleware('auth:api')->group(function () {
        // Hostels API
        Route::apiResource('hostels', HostelApiController::class);
        
        // Tenants API
        Route::apiResource('tenants', TenantApiController::class);
        
        // Dashboard API
        Route::get('/dashboard/stats', [DashboardController::class, 'apiStats']);
    });
});
```

### API Response Format
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Sunrise Hostel",
        "status": "active",
        "created_at": "2024-01-15T10:30:00Z"
    },
    "message": "Hostel retrieved successfully"
}
```

## Route Caching

### Production Optimization
```bash
# Cache routes for production
php artisan route:cache

# Clear route cache
php artisan route:clear

# List all routes
php artisan route:list
```

### Route List Output
```
+--------+---------------------------+------------------+------------------+----------------------------------------------------+
| Method | URI                       | Name             | Action           | Middleware                                        |
+--------+---------------------------+------------------+------------------+----------------------------------------------------+
| GET    | /                         |                  | Closure          | web                                               |
| GET    | /login                    | login            | AuthController   | web                                               |
| POST   | /login                    |                  | AuthController   | web                                               |
| POST   | /logout                   | logout           | AuthController   | web                                               |
| GET    | /dashboard                | dashboard        | DashboardController | web,auth                                        |
| GET    | /hostels                  | hostels.index    | HostelController | web,auth                                        |
| GET    | /hostels/create           | hostels.create   | HostelController | web,auth                                        |
| POST   | /hostels                  | hostels.store    | HostelController | web,auth                                        |
| GET    | /hostels/{id}             | hostels.show     | HostelController | web,auth                                        |
| GET    | /hostels/{id}/edit        | hostels.edit     | HostelController | web,auth                                        |
| PUT    | /hostels/{id}             | hostels.update   | HostelController | web,auth                                        |
| DELETE | /hostels/{id}             | hostels.destroy  | HostelController | web,auth                                        |
| GET    | /tenants                  | tenants.index    | TenantController | web,auth                                        |
| GET    | /tenants/create           | tenants.create   | TenantController | web,auth                                        |
| POST   | /tenants                  | tenants.store    | TenantController | web,auth                                        |
| GET    | /tenants/{id}             | tenants.show     | TenantController | web,auth                                        |
| GET    | /tenants/{id}/edit        | tenants.edit     | TenantController | web,auth                                        |
| PUT    | /tenants/{id}             | tenants.update   | TenantController | web,auth                                        |
| DELETE | /tenants/{id}             | tenants.destroy  | TenantController | web,auth                                        |
| GET    | /tenants/available-beds/{hostel} | tenants.available-beds | TenantController | web,auth                                        |
| POST   | /tenants/{tenant}/verify  | tenants.verify   | TenantController | web,auth                                        |
| POST   | /tenants/{tenant}/move-out| tenants.move-out | TenantController | web,auth                                        |
| GET    | /rooms                    | rooms.index      | RoomController   | web,auth                                        |
| GET    | /rooms/create             | rooms.create     | RoomController   | web,auth                                        |
| POST   | /rooms                    | rooms.store      | RoomController   | web,auth                                        |
| GET    | /rooms/{id}               | rooms.show       | RoomController   | web,auth                                        |
| GET    | /rooms/{id}/edit          | rooms.edit       | RoomController   | web,auth                                        |
| PUT    | /rooms/{id}               | rooms.update     | RoomController   | web,auth                                        |
| DELETE | /rooms/{id}               | rooms.destroy    | RoomController   | web,auth                                        |
| GET    | /map                      | map.index        | MapController    | web,auth                                        |
| GET    | /map/hostel/{hostel}      | map.hostel       | MapController    | web,auth                                        |
| GET    | /map/occupancy/{hostel}/{floor?} | map.occupancy | MapController    | web,auth                                        |
| POST   | /map/bed/{bed}/status     | map.bed.status   | MapController    | web,auth                                        |
| GET    | /contact                  | enquiry.form     | EnquiryController| web                                               |
| POST   | /contact                  | enquiry.store    | EnquiryController| web                                               |
| GET    | /contact/success          | enquiry.success  | EnquiryController| web                                               |
| GET    | /enquiries                | enquiries.index  | EnquiryController| web,auth                                        |
| GET    | /enquiries/create         | enquiries.create | EnquiryController| web,auth                                        |
| POST   | /enquiries                | enquiries.store  | EnquiryController| web,auth                                        |
| GET    | /enquiries/{id}           | enquiries.show   | EnquiryController| web,auth                                        |
| GET    | /enquiries/{id}/edit      | enquiries.edit   | EnquiryController| web,auth                                        |
| PUT    | /enquiries/{id}           | enquiries.update | EnquiryController| web,auth                                        |
| DELETE | /enquiries/{id}           | enquiries.destroy| EnquiryController| web,auth                                        |
| POST   | /enquiries/{enquiry}/convert | enquiries.convert | EnquiryController| web,auth                                        |
| POST   | /enquiries/bulk-update    | enquiries.bulk-update | EnquiryController| web,auth                                        |
| GET    | /config/amenities         | config.amenities.index | AmenityController| web,auth                                        |
| GET    | /config/amenities/create  | config.amenities.create| AmenityController| web,auth                                        |
| POST   | /config/amenities         | config.amenities.store | AmenityController| web,auth                                        |
| GET    | /config/amenities/{id}    | config.amenities.show  | AmenityController| web,auth                                        |
| GET    | /config/amenities/{id}/edit| config.amenities.edit | AmenityController| web,auth                                        |
| PUT    | /config/amenities/{id}    | config.amenities.update| AmenityController| web,auth                                        |
| DELETE | /config/amenities/{id}    | config.amenities.destroy| AmenityController| web,auth                                        |
+--------+---------------------------+------------------+------------------+----------------------------------------------------+
```

## Route Testing

### Feature Tests
```php
// Test route accessibility
public function test_dashboard_requires_authentication()
{
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
}

public function test_authenticated_user_can_access_dashboard()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);
}

// Test resource routes
public function test_hostel_resource_routes()
{
    $user = User::factory()->create();
    
    // Test index
    $response = $this->actingAs($user)->get('/hostels');
    $response->assertStatus(200);
    
    // Test create
    $response = $this->actingAs($user)->get('/hostels/create');
    $response->assertStatus(200);
    
    // Test store
    $hostelData = Hostel::factory()->make()->toArray();
    $response = $this->actingAs($user)->post('/hostels', $hostelData);
    $response->assertRedirect('/hostels');
}
```

### Route Testing Helpers
```php
// Test route exists
$this->assertRouteExists('hostels.index');

// Test route has middleware
$this->assertRouteHasMiddleware('hostels.index', ['web', 'auth']);

// Test route parameters
$this->assertRouteHasParameter('hostels.show', 'id');
```

## Security Considerations

### CSRF Protection
```php
// CSRF protection is enabled by default for web routes
Route::post('/hostels', [HostelController::class, 'store']);

// In forms
<form method="POST" action="{{ route('hostels.store') }}">
    @csrf
    <!-- form fields -->
</form>
```

### Rate Limiting
```php
// Apply rate limiting to sensitive routes
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});
```

### Input Validation
```php
// Validate route parameters
Route::get('/hostels/{id}', [HostelController::class, 'show'])
    ->where('id', '[0-9]+');

// In controller
public function show(Request $request, $id)
{
    $request->validate([
        'id' => 'required|integer|exists:hostels,id'
    ]);
}
```

## Error Handling

### 404 Errors
```php
// Custom 404 handling
Route::fallback(function () {
    return view('errors.404');
});
```

### Route Not Found
```php
// Handle missing routes
Route::any('{any}', function () {
    return redirect()->route('dashboard');
})->where('any', '.*');
```

## Performance Optimization

### Route Caching
```bash
# Cache routes for production
php artisan route:cache
```

### Route Model Binding Optimization
```php
// Eager load relationships
Route::get('/hostels/{hostel}', [HostelController::class, 'show']);

// In controller
public function show(Hostel $hostel)
{
    $hostel->load(['manager', 'rooms', 'tenants']);
    return view('hostels.show', compact('hostel'));
}
```

## Future Enhancements

### Notification Routes

#### Notification Management Routes
```php
// Notification Routes (Authentication Required)
Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {
    // Main notification routes
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
    Route::get('/statistics', [NotificationController::class, 'statistics'])->name('statistics');
    
    // Notification actions
    Route::post('/process-scheduled', [NotificationController::class, 'processScheduled'])->name('process-scheduled');
    Route::post('/retry-failed', [NotificationController::class, 'retryFailed'])->name('retry-failed');
    Route::post('/test', [NotificationController::class, 'test'])->name('test');
    Route::post('/{notification}/retry', [NotificationController::class, 'retry'])->name('retry');
    
    // Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [NotificationController::class, 'settings'])->name('index');
        Route::get('/create', [NotificationController::class, 'createSetting'])->name('create');
        Route::post('/', [NotificationController::class, 'storeSetting'])->name('store');
        Route::get('/{notificationSetting}/edit', [NotificationController::class, 'editSetting'])->name('edit');
        Route::put('/{notificationSetting}', [NotificationController::class, 'updateSetting'])->name('update');
        Route::delete('/{notificationSetting}', [NotificationController::class, 'destroySetting'])->name('destroy');
        Route::post('/{notificationSetting}/toggle', [NotificationController::class, 'toggleSetting'])->name('toggle');
    });
});
```

**Purpose**: Manage email notifications, settings, and delivery tracking.

**Middleware**: `auth` (authentication required).

**Key Features**:
- View all notifications with filtering and search
- Manage notification settings and templates
- Process scheduled notifications
- Retry failed notifications
- Send test notifications
- Toggle notification settings on/off

### User Management Routes

#### User Management Routes
```php
// User Management Routes (Authentication Required)
Route::prefix('users')->name('users.')->middleware('auth')->group(function () {
    // Main user routes
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    
    // User actions
    Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
});
```

**Purpose**: Complete user management with role-based access control.

**Middleware**: `auth` (authentication required).

**Key Features**:
- User CRUD operations with avatar uploads
- User status management (active, suspended, inactive)
- Role assignment and management
- Bulk user operations
- Advanced search and filtering
- User statistics and analytics

#### Role Management Routes
```php
// Role Management Routes (Authentication Required)
Route::prefix('roles')->name('roles.')->middleware('auth')->group(function () {
    // Main role routes
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('/create', [RoleController::class, 'create'])->name('create');
    Route::post('/', [RoleController::class, 'store'])->name('store');
    Route::get('/{role}', [RoleController::class, 'show'])->name('show');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('update');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    
    // Role actions
    Route::post('/{role}/clone', [RoleController::class, 'clone'])->name('clone');
});
```

**Purpose**: Role management with permission assignment.

**Middleware**: `auth` (authentication required).

**Key Features**:
- Role CRUD operations
- Permission assignment to roles
- Role cloning functionality
- System vs custom role distinction
- Role usage tracking
- Bulk role operations

#### Permission Management Routes
```php
// Permission Management Routes (Authentication Required)
Route::prefix('permissions')->name('permissions.')->middleware('auth')->group(function () {
    // Main permission routes
    Route::get('/', [PermissionController::class, 'index'])->name('index');
    Route::get('/create', [PermissionController::class, 'create'])->name('create');
    Route::post('/', [PermissionController::class, 'store'])->name('store');
    Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
    Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
    Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
    Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    
    // Permission actions
    Route::post('/bulk-action', [PermissionController::class, 'bulkAction'])->name('bulk-action');
});
```

**Purpose**: Granular permission management with module organization.

**Middleware**: `auth` (authentication required).

**Key Features**:
- Permission CRUD operations
- Module-based organization
- System vs custom permission distinction
- Permission usage tracking
- Bulk permission operations
- Auto-slug generation

### Tenant Profile Update Request Routes

#### Admin Profile Update Request Management
```php
// Admin routes for managing tenant profile update requests
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Resource routes (index, show, destroy only)
    Route::resource('tenant-profile-requests', \App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class)
        ->except(['create', 'edit', 'store', 'update']);
    
    // Approval actions
    Route::post('/tenant-profile-requests/{tenantProfileRequest}/approve', 
        [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'approve'])
        ->name('tenant-profile-requests.approve');
    
    Route::post('/tenant-profile-requests/{tenantProfileRequest}/reject', 
        [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'reject'])
        ->name('tenant-profile-requests.reject');
    
    // Bulk actions
    Route::post('/tenant-profile-requests/bulk-action', 
        [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'bulkAction'])
        ->name('tenant-profile-requests.bulk-action');
});
```

**Purpose**: Manage tenant profile update requests with admin approval workflow.

**Middleware**: `auth` (authentication required).

**Generated Routes**:
- `GET /admin/tenant-profile-requests` - List all profile update requests
- `GET /admin/tenant-profile-requests/{id}` - Show request details with change comparison
- `DELETE /admin/tenant-profile-requests/{id}` - Delete request
- `POST /admin/tenant-profile-requests/{id}/approve` - Approve request
- `POST /admin/tenant-profile-requests/{id}/reject` - Reject request
- `POST /admin/tenant-profile-requests/bulk-action` - Bulk approve/reject/delete

**Key Features**:
- Change detection to prevent empty requests
- Side-by-side comparison of current vs requested values
- Profile image upload and approval
- Bulk actions for multiple requests
- File cleanup when requests are deleted
- Admin notes for approval/rejection

#### Tenant Portal Routes
```php
// Tenant portal routes for profile management
Route::prefix('tenant')->name('tenant.')->group(function () {
    // Public login routes
    Route::get('/login', [TenantPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [TenantPortalController::class, 'login'])->name('login');
    
    // Protected tenant routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [TenantPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/invoices', [TenantPortalController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}', [TenantPortalController::class, 'showInvoice'])->name('invoice.show');
        Route::get('/payments', [TenantPortalController::class, 'payments'])->name('payments');
        Route::get('/profile', [TenantPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [TenantPortalController::class, 'updateProfile'])->name('profile.update');
        Route::get('/bed-info', [TenantPortalController::class, 'bedInfo'])->name('bed-info');
        
        // Amenity management routes
        Route::get('/amenities', [TenantPortalController::class, 'amenities'])->name('amenities');
        Route::get('/amenities/request', [TenantPortalController::class, 'requestAmenity'])->name('amenities.request');
        Route::post('/amenities/request', [TenantPortalController::class, 'storeAmenityRequest'])->name('amenities.request.store');
        Route::post('/amenities/{tenantAmenity}/cancel', [TenantPortalController::class, 'cancelAmenity'])->name('amenities.cancel');
        Route::get('/amenities/usage', [TenantPortalController::class, 'amenityUsage'])->name('amenities.usage');
        Route::post('/amenities/usage', [TenantPortalController::class, 'markUsage'])->name('amenities.usage.mark');
        Route::post('/amenities/usage/{usage}/correction', [TenantPortalController::class, 'requestUsageCorrection'])->name('amenities.usage.correction');
        
        Route::post('/logout', [TenantPortalController::class, 'logout'])->name('logout');
    });
});
```

**Purpose**: Tenant self-service portal for profile management and information access.

**Middleware**: `auth` (authentication required for protected routes).

**Key Features**:
- Profile update with approval workflow
- Invoice and payment history viewing
- Bed information display
- Pending request tracking
- Secure tenant authentication

### Planned Routes
- **Payment Gateway Integration**: Online payment processing routes
- **Maintenance**: Maintenance request routes
- **Reports**: Reporting and analytics routes
- **Settings**: Advanced system configuration routes
- **API Endpoints**: RESTful API routes with authentication

### Advanced Features
- **Route Versioning**: API versioning
- **Route Groups**: Advanced route organization
- **Custom Middleware**: Role-based access control
- **Route Model Binding**: Advanced binding logic
- **Route Caching**: Performance optimization
- **API Documentation**: Automated API documentation

## Troubleshooting

### Common Issues

#### Route Not Found
1. Check route definition
2. Verify route caching
3. Check middleware
4. Verify URL parameters
5. Check route names

#### Middleware Issues
1. Check middleware registration
2. Verify middleware parameters
3. Check authentication
4. Verify permissions
5. Check middleware order

#### Parameter Binding Issues
1. Check model binding
2. Verify parameter constraints
3. Check model existence
4. Verify parameter names
5. Check route patterns

### Debugging Tools
```bash
# List all routes
php artisan route:list

# List routes with middleware
php artisan route:list --columns=method,uri,name,middleware

# Clear route cache
php artisan route:clear

# Check route exists
php artisan route:list | grep hostels
```
