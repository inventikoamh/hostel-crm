# Enquiry Management Module

## Overview
The Enquiry Management module handles public enquiry submissions and provides administrative tools for managing and responding to potential tenant inquiries. It includes both public-facing forms and admin management interfaces.

## Features

### Public Enquiry System
- **Public Form**: Accessible enquiry form for visitors
- **No Authentication**: Public access without login requirements
- **Hostel Selection**: Dynamic hostel selection with details
- **Form Validation**: Comprehensive client and server-side validation
- **Success Confirmation**: User-friendly confirmation pages

### Admin Management
- **Enquiry Listing**: Comprehensive enquiry management interface
- **Status Tracking**: Track enquiry status (new, contacted, converted, closed)
- **Response Management**: Record responses and follow-ups
- **Search & Filter**: Advanced filtering and search capabilities
- **Bulk Operations**: Mass status updates and actions

### Integration Features
- **Tenant Conversion**: Convert enquiries to tenants
- **Hostel Integration**: Link enquiries to specific hostels
- **Email Notifications**: Automated email responses (future)
- **Analytics**: Enquiry source and conversion tracking

## Database Schema

### Enquiries Table
```sql
CREATE TABLE enquiries (
    id BIGINT PRIMARY KEY,
    hostel_id BIGINT FOREIGN KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    preferred_move_in_date DATE,
    budget_min DECIMAL(8,2),
    budget_max DECIMAL(8,2),
    room_type_preference ENUM('single', 'double', 'triple', 'dormitory', 'suite', 'studio'),
    duration_months INTEGER,
    message TEXT,
    status ENUM('new', 'contacted', 'converted', 'closed') DEFAULT 'new',
    source VARCHAR(100) DEFAULT 'website',
    admin_notes TEXT,
    contacted_at TIMESTAMP NULL,
    contacted_by BIGINT FOREIGN KEY NULL,
    converted_to_tenant_id BIGINT FOREIGN KEY NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX(status),
    INDEX(hostel_id),
    INDEX(created_at),
    INDEX(preferred_move_in_date)
);
```

## Models

### Enquiry Model
```php
class Enquiry extends Model
{
    protected $fillable = [
        'hostel_id', 'name', 'email', 'phone',
        'preferred_move_in_date', 'budget_min', 'budget_max',
        'room_type_preference', 'duration_months', 'message',
        'status', 'source', 'admin_notes'
    ];

    protected $casts = [
        'preferred_move_in_date' => 'date',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'contacted_at' => 'datetime',
    ];

    // Relationships
    public function hostel(): BelongsTo
    public function contactedBy(): BelongsTo // User who contacted
    public function convertedToTenant(): BelongsTo // Converted tenant

    // Scopes
    public function scopeNew($query)
    public function scopeContacted($query)
    public function scopeConverted($query)
    public function scopeClosed($query)
    public function scopeForHostel($query, $hostelId)
    public function scopeWithinBudget($query, $min, $max)

    // Accessors
    public function getStatusBadgeAttribute()
    public function getRoomTypeDisplayAttribute()
    public function getBudgetRangeAttribute()
    public function getDaysOldAttribute()
    public function getIsNewAttribute()
    public function getIsOverdueAttribute() // No contact after 3 days
}
```

## Controllers

### EnquiryController

#### Public Methods

##### publicForm()
Displays the public enquiry form.

```php
public function publicForm()
{
    $hostels = Hostel::active()
                    ->select('id', 'name', 'address', 'city', 'rent_per_bed')
                    ->orderBy('name')
                    ->get();
    
    return view('enquiries.public-form', compact('hostels'));
}
```

##### store()
Handles enquiry form submission.

```php
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'hostel_id' => 'required|exists:hostels,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'preferred_move_in_date' => 'nullable|date|after_or_equal:today',
        'budget_min' => 'nullable|numeric|min:0',
        'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
        'room_type_preference' => 'nullable|in:single,double,triple,dormitory,suite,studio',
        'duration_months' => 'nullable|integer|min:1|max:60',
        'message' => 'nullable|string|max:1000',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
                       ->withErrors($validator)
                       ->withInput();
    }

    $enquiry = Enquiry::create($request->all());

    // Send confirmation email (future implementation)
    // Mail::to($enquiry->email)->send(new EnquiryConfirmation($enquiry));

    return redirect()->route('enquiry.success')
                   ->with('enquiry_id', $enquiry->id);
}
```

##### success()
Shows enquiry submission success page.

```php
public function success()
{
    if (!session('enquiry_id')) {
        return redirect()->route('enquiry.form');
    }

    $enquiry = Enquiry::with('hostel')->find(session('enquiry_id'));
    
    return view('enquiries.success', compact('enquiry'));
}
```

#### Admin Methods

##### index()
Lists all enquiries with filtering and search.

```php
public function index(Request $request)
{
    $query = Enquiry::with(['hostel', 'contactedBy', 'convertedToTenant'])
                   ->orderBy('created_at', 'desc');

    // Apply filters
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('hostel_id')) {
        $query->where('hostel_id', $request->hostel_id);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    $enquiries = $query->get()->map(function ($enquiry) {
        return [
            'id' => $enquiry->id,
            'name' => $enquiry->name,
            'email' => $enquiry->email,
            'phone' => $enquiry->phone ?? 'N/A',
            'hostel' => $enquiry->hostel->name,
            'move_in_date' => $enquiry->preferred_move_in_date ? $enquiry->preferred_move_in_date->format('M j, Y') : 'N/A',
            'budget_range' => $enquiry->budget_range,
            'room_type' => $enquiry->room_type_display,
            'status' => $enquiry->status,
            'days_old' => $enquiry->days_old,
            'view_url' => route('enquiries.show', $enquiry->id),
        ];
    });

    // Statistics and filters setup...
    
    return view('enquiries.index', compact('enquiries', 'stats', 'columns', 'filters', 'bulkActions', 'pagination'));
}
```

##### show()
Displays detailed enquiry information with response options.

```php
public function show(Enquiry $enquiry)
{
    $enquiry->load(['hostel', 'contactedBy', 'convertedToTenant']);
    
    // Get available rooms in the enquired hostel
    $availableRooms = Room::where('hostel_id', $enquiry->hostel_id)
                         ->where('status', 'available')
                         ->with('beds')
                         ->get();
    
    return view('enquiries.show', compact('enquiry', 'availableRooms'));
}
```

##### update()
Updates enquiry status and admin notes.

```php
public function update(Request $request, Enquiry $enquiry)
{
    $validator = Validator::make($request->all(), [
        'status' => 'required|in:new,contacted,converted,closed',
        'admin_notes' => 'nullable|string',
        'response_message' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $updateData = [
        'status' => $request->status,
        'admin_notes' => $request->admin_notes,
    ];

    // Track contact information
    if ($request->status === 'contacted' && $enquiry->status !== 'contacted') {
        $updateData['contacted_at'] = now();
        $updateData['contacted_by'] = auth()->id();
    }

    $enquiry->update($updateData);

    // Send response email if message provided
    if ($request->response_message) {
        // Mail::to($enquiry->email)->send(new EnquiryResponse($enquiry, $request->response_message));
    }

    return redirect()->route('enquiries.show', $enquiry)
                   ->with('success', 'Enquiry updated successfully!');
}
```

## Routes

### Public Routes (No Authentication)
```php
Route::get('/contact', [EnquiryController::class, 'publicForm'])->name('enquiry.form');
Route::post('/contact', [EnquiryController::class, 'store'])->name('enquiry.store');
Route::get('/contact/success', [EnquiryController::class, 'success'])->name('enquiry.success');
```

### Admin Routes (Authentication Required)
```php
Route::resource('enquiries', EnquiryController::class)->middleware('auth');
Route::post('/enquiries/{enquiry}/convert', [EnquiryController::class, 'convertToTenant'])->name('enquiries.convert')->middleware('auth');
Route::post('/enquiries/bulk-update', [EnquiryController::class, 'bulkUpdate'])->name('enquiries.bulk-update')->middleware('auth');
```

## Views

### Public Form (`enquiries/public-form.blade.php`)
User-friendly enquiry submission form.

```blade
<form action="{{ route('enquiry.store') }}" method="POST" class="space-y-6">
    @csrf
    
    <!-- Hostel Selection -->
    <div>
        <label for="hostel_id" class="block text-sm font-medium mb-2">Interested Hostel *</label>
        <select id="hostel_id" name="hostel_id" required class="w-full px-3 py-2 border rounded-lg">
            <option value="">Select a hostel</option>
            @foreach($hostels as $hostel)
                <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>
                    {{ $hostel->name }} - {{ $hostel->address }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Personal Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="name" class="block text-sm font-medium mb-2">Full Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                   class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium mb-2">Email Address *</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                   class="w-full px-3 py-2 border rounded-lg">
        </div>
    </div>

    <!-- Preferences -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="budget_min" class="block text-sm font-medium mb-2">Budget Range (₹)</label>
            <div class="flex gap-2">
                <input type="number" id="budget_min" name="budget_min" placeholder="Min" 
                       value="{{ old('budget_min') }}" class="w-full px-3 py-2 border rounded-lg">
                <input type="number" id="budget_max" name="budget_max" placeholder="Max" 
                       value="{{ old('budget_max') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
        </div>
        <div>
            <label for="room_type_preference" class="block text-sm font-medium mb-2">Room Type Preference</label>
            <select id="room_type_preference" name="room_type_preference" class="w-full px-3 py-2 border rounded-lg">
                <option value="">No preference</option>
                <option value="single" {{ old('room_type_preference') == 'single' ? 'selected' : '' }}>Single</option>
                <option value="double" {{ old('room_type_preference') == 'double' ? 'selected' : '' }}>Double</option>
                <option value="triple" {{ old('room_type_preference') == 'triple' ? 'selected' : '' }}>Triple</option>
                <option value="dormitory" {{ old('room_type_preference') == 'dormitory' ? 'selected' : '' }}>Dormitory</option>
            </select>
        </div>
    </div>

    <!-- Message -->
    <div>
        <label for="message" class="block text-sm font-medium mb-2">Additional Message</label>
        <textarea id="message" name="message" rows="4" 
                  class="w-full px-3 py-2 border rounded-lg">{{ old('message') }}</textarea>
    </div>

    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
        Submit Enquiry
    </button>
</form>
```

### Admin List (`enquiries/index.blade.php`)
Administrative enquiry management interface.

```blade
@extends('layouts.app')

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stats-card title="Total Enquiries" value="{{ $stats['total'] }}" icon="fas fa-envelope" color="blue"/>
        <x-stats-card title="New Enquiries" value="{{ $stats['new'] }}" icon="fas fa-star" color="green"/>
        <x-stats-card title="Contacted" value="{{ $stats['contacted'] }}" icon="fas fa-phone" color="orange"/>
        <x-stats-card title="Converted" value="{{ $stats['converted'] }}" icon="fas fa-check-circle" color="purple"/>
    </div>

    <!-- Data Table -->
    <x-data-table
        title="All Enquiries"
        :columns="$columns"
        :data="$enquiries"
        :actions="true"
        :searchable="true"
        :exportable="true"
        :filters="$filters"
        :bulk-actions="$bulkActions"
        :pagination="$pagination"
    />
@endsection
```

### Enquiry Details (`enquiries/show.blade.php`)
Detailed enquiry view with response options.

```blade
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Enquiry Details -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold mb-4">Enquiry Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-600">Name</label>
                    <p class="mt-1 font-medium">{{ $enquiry->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Email</label>
                    <p class="mt-1">{{ $enquiry->email }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Phone</label>
                    <p class="mt-1">{{ $enquiry->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600">Preferred Move-in</label>
                    <p class="mt-1">{{ $enquiry->preferred_move_in_date ? $enquiry->preferred_move_in_date->format('M j, Y') : 'Flexible' }}</p>
                </div>
            </div>
        </div>

        <!-- Response Form -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold mb-4">Update Status & Respond</h3>
            <form action="{{ route('enquiries.update', $enquiry) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="status" class="block text-sm font-medium mb-2">Status</label>
                        <select id="status" name="status" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="new" {{ $enquiry->status == 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ $enquiry->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="converted" {{ $enquiry->status == 'converted' ? 'selected' : '' }}>Converted</option>
                            <option value="closed" {{ $enquiry->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="response_message" class="block text-sm font-medium mb-2">Response Message (Optional)</label>
                    <textarea id="response_message" name="response_message" rows="4" 
                              class="w-full px-3 py-2 border rounded-lg"
                              placeholder="This message will be sent to the enquirer via email..."></textarea>
                </div>

                <div class="mb-4">
                    <label for="admin_notes" class="block text-sm font-medium mb-2">Admin Notes</label>
                    <textarea id="admin_notes" name="admin_notes" rows="3" 
                              class="w-full px-3 py-2 border rounded-lg">{{ $enquiry->admin_notes }}</textarea>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Update Enquiry
                </button>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-3">
                @if($enquiry->status !== 'converted')
                    <button onclick="convertToTenant()" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fas fa-user-plus mr-2"></i>Convert to Tenant
                    </button>
                @endif
                
                <a href="mailto:{{ $enquiry->email }}" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center justify-center">
                    <i class="fas fa-envelope mr-2"></i>Send Email
                </a>
                
                @if($enquiry->phone)
                    <a href="tel:{{ $enquiry->phone }}" class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 flex items-center justify-center">
                        <i class="fas fa-phone mr-2"></i>Call Now
                    </a>
                @endif
            </div>
        </div>

        <!-- Available Rooms -->
        @if($availableRooms->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold mb-4">Available Rooms</h3>
                <div class="space-y-3">
                    @foreach($availableRooms as $room)
                        <div class="border rounded-lg p-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium">Room {{ $room->room_number }}</h4>
                                    <p class="text-sm text-gray-600">{{ $room->room_type_display }} • {{ $room->floor }}</p>
                                    <p class="text-sm font-medium text-green-600">₹{{ number_format($room->rent_per_bed, 2) }}/bed</p>
                                </div>
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                    {{ $room->available_beds_count }} available
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
```

## Seeding

### Enquiry Seeder
```php
class EnquirySeeder extends Seeder
{
    public function run()
    {
        $hostels = Hostel::all();
        $statuses = ['new', 'contacted', 'converted', 'closed'];
        $roomTypes = ['single', 'double', 'triple', 'dormitory'];
        
        foreach (range(1, 50) as $i) {
            Enquiry::create([
                'hostel_id' => $hostels->random()->id,
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'preferred_move_in_date' => fake()->dateTimeBetween('now', '+3 months'),
                'budget_min' => fake()->numberBetween(5000, 8000),
                'budget_max' => fake()->numberBetween(8000, 15000),
                'room_type_preference' => fake()->randomElement($roomTypes),
                'duration_months' => fake()->numberBetween(6, 24),
                'message' => fake()->paragraph(),
                'status' => fake()->randomElement($statuses),
                'source' => 'website',
                'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            ]);
        }
    }
}
```

## Integration Points

### With Hostel Module
- Enquiries linked to specific hostels
- Hostel information displayed in forms
- Available room suggestions

### With Tenant Module
- Convert enquiries to tenants
- Pre-fill tenant forms with enquiry data
- Track conversion rates

### With Dashboard
- Enquiry statistics and metrics
- Recent enquiry notifications
- Conversion tracking

## Future Enhancements

### Planned Features
- **Email Automation**: Automated email responses and follow-ups
- **SMS Integration**: SMS notifications and responses
- **Lead Scoring**: Automatic lead qualification scoring
- **CRM Integration**: Integration with external CRM systems
- **Analytics Dashboard**: Detailed enquiry analytics and reporting

### Advanced Features
- **Chatbot Integration**: AI-powered initial response system
- **WhatsApp Integration**: WhatsApp-based enquiry handling
- **Multi-language Support**: Support for multiple languages
- **Advanced Filtering**: Complex enquiry filtering and segmentation

This enquiry management module provides a complete solution for handling potential tenant inquiries from initial contact through conversion, with comprehensive administrative tools and seamless integration with the core hostel management system.
