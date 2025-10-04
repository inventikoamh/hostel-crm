# Room Management Module

## Overview
The Room Management module provides comprehensive functionality for managing hostel rooms and beds, including occupancy tracking, bed assignment, and visual mapping integration.

## Features

### Room Management
- **Room Creation**: Add rooms with detailed specifications
- **Floor Organization**: Organize rooms by floors
- **Room Types**: Support for single, double, triple, dormitory, suite, and studio rooms
- **Amenity Tracking**: Track room-specific amenities (AC, attached bathroom, balcony)
- **Capacity Management**: Define and track bed capacity per room

### Bed Management
- **Individual Bed Tracking**: Each bed is tracked separately
- **Bed Types**: Single, double, bunk (top/bottom) bed support
- **Occupancy Status**: Available, occupied, maintenance, reserved
- **Tenant Assignment**: Direct bed-to-tenant assignment
- **Rent Management**: Individual bed rent or room-based pricing

### Visual Integration
- **Map Integration**: Seamless integration with visual map module
- **Floor Plans**: Visual representation of room layouts
- **Occupancy Visualization**: Color-coded bed status indicators

## Database Schema

### Rooms Table
```sql
CREATE TABLE rooms (
    id BIGINT PRIMARY KEY,
    hostel_id BIGINT FOREIGN KEY,
    room_number VARCHAR(255),
    floor VARCHAR(255) DEFAULT 'Ground Floor',
    room_type ENUM('single', 'double', 'triple', 'dormitory', 'suite', 'studio'),
    capacity INTEGER DEFAULT 1,
    rent_per_bed DECIMAL(8,2),
    status ENUM('available', 'occupied', 'maintenance', 'reserved'),
    has_ac BOOLEAN DEFAULT FALSE,
    has_attached_bathroom BOOLEAN DEFAULT FALSE,
    has_balcony BOOLEAN DEFAULT FALSE,
    notes TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    coordinates JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(hostel_id, room_number)
);
```

### Beds Table
```sql
CREATE TABLE beds (
    id BIGINT PRIMARY KEY,
    room_id BIGINT FOREIGN KEY,
    bed_number VARCHAR(255),
    bed_type ENUM('single', 'double', 'bunk_top', 'bunk_bottom'),
    status ENUM('available', 'occupied', 'maintenance', 'reserved'),
    tenant_id BIGINT FOREIGN KEY NULLABLE,
    occupied_from DATE,
    occupied_until DATE,
    monthly_rent DECIMAL(8,2),
    notes TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    coordinates JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(room_id, bed_number)
);
```

## Models

### Room Model
```php
class Room extends Model
{
    // Relationships
    public function hostel(): BelongsTo
    public function beds(): HasMany
    
    // Scopes
    public function scopeActive($query)
    public function scopeStatus($query, $status)
    public function scopeFloor($query, $floor)
    public function scopeForHostel($query, $hostelId)
    
    // Accessors
    public function getRoomTypeDisplayAttribute(): string
    public function getOccupiedBedsCountAttribute(): int
    public function getAvailableBedsCountAttribute(): int
    public function getOccupancyRateAttribute(): float
    public function getStatusBadgeAttribute(): string
    public function getIsFullyOccupiedAttribute(): bool
    public function getIsEmptyAttribute(): bool
}
```

### Bed Model
```php
class Bed extends Model
{
    // Relationships
    public function room(): BelongsTo
    public function tenant(): BelongsTo
    
    // Scopes
    public function scopeActive($query)
    public function scopeStatus($query, $status)
    public function scopeForRoom($query, $roomId)
    public function scopeForHostel($query, $hostelId)
    
    // Helper Methods
    public function assignTenant($tenantId, $checkInDate, $checkOutDate, $rent)
    public function releaseTenant()
    public function extendLease($newEndDate, $newRent)
    
    // Accessors
    public function getStatusBadgeAttribute(): string
    public function getBedTypeDisplayAttribute(): string
    public function getIsOccupiedAttribute(): bool
    public function getIsAvailableAttribute(): bool
}
```

## Controllers

### RoomController
- **index()**: List all rooms with filtering and search
- **create()**: Show room creation form
- **store()**: Create new room with beds
- **show()**: Display room details and bed layout
- **edit()**: Show room edit form
- **update()**: Update room and manage bed capacity
- **destroy()**: Delete room and associated beds

### Key Features
- **Dynamic Bed Creation**: Automatically creates beds based on room capacity
- **Capacity Management**: Handles bed creation/deletion when capacity changes
- **Validation**: Prevents capacity reduction below occupied beds
- **Bulk Operations**: Support for bulk room operations

## Views

### Room List (`rooms/index.blade.php`)
- **Statistics Cards**: Total, available, occupied, maintenance rooms
- **Advanced Data Table**: Search, filters, pagination, bulk actions
- **Quick Actions**: View, edit, delete rooms

### Room Creation (`rooms/create.blade.php`)
- **Multi-section Form**: Room details, amenities, notes
- **Dynamic Suggestions**: Room number and capacity suggestions
- **Validation**: Real-time form validation

### Room Details (`rooms/show.blade.php`)
- **Room Overview**: Complete room information
- **Bed Layout**: Visual bed arrangement with status colors
- **Statistics**: Occupancy rates and availability
- **Actions**: Edit, map view, delete options

### Room Edit (`rooms/edit.blade.php`)
- **Pre-populated Form**: Current room data
- **Capacity Constraints**: Prevents invalid capacity changes
- **Bed Management**: Automatic bed adjustment

## Routes

### Resource Routes
```php
Route::resource('rooms', RoomController::class);
```

### Additional Routes
```php
Route::get('/rooms/{room}/beds', [RoomController::class, 'beds']);
Route::post('/rooms/{room}/beds', [RoomController::class, 'createBed']);
Route::put('/beds/{bed}', [RoomController::class, 'updateBed']);
Route::delete('/beds/{bed}', [RoomController::class, 'deleteBed']);
```

## Integration Points

### With Hostel Module
- Rooms belong to hostels
- Hostel capacity calculated from rooms
- Amenity inheritance from hostel

### With Tenant Module
- Bed assignment to tenants
- Rent calculation from bed/room rates
- Occupancy tracking

### With Map Module
- Visual room representation
- Floor-wise organization
- Interactive bed status

### With Billing Module
- Rent amount from room/bed rates
- Occupancy-based billing
- Payment tracking per bed

## Seeding

### Room Seeder
- Creates realistic room layouts
- Generates beds for each room
- Sets appropriate statuses
- Assigns some beds to tenants

```php
// Sample room data
$rooms = [
    [
        'room_number' => '101',
        'floor' => 'Ground Floor',
        'room_type' => 'double',
        'capacity' => 2,
        'rent_per_bed' => 7500,
        'has_ac' => true,
        'has_attached_bathroom' => true,
    ],
    // ... more rooms
];
```

## Usage Examples

### Create Room with Beds
```php
$room = Room::create([
    'hostel_id' => 1,
    'room_number' => '101',
    'capacity' => 2,
    'room_type' => 'double',
]);

// Beds are automatically created based on capacity
```

### Assign Bed to Tenant
```php
$bed = Bed::find(1);
$bed->assignTenant($tenantId, $checkInDate, $checkOutDate, $rent);
```

### Get Room Occupancy
```php
$room = Room::with('beds')->find(1);
$occupancyRate = $room->occupancy_rate; // Percentage
$availableBeds = $room->available_beds_count;
```

### Filter Rooms
```php
$availableRooms = Room::status('available')->get();
$groundFloorRooms = Room::floor('Ground Floor')->get();
$hostelRooms = Room::forHostel(1)->get();
```

## Future Enhancements

### Planned Features
- **Room Templates**: Predefined room configurations
- **Bulk Import**: CSV import for multiple rooms
- **Room Photos**: Image gallery for rooms
- **Maintenance Scheduling**: Room maintenance tracking
- **Booking System**: Advance room reservations

### Integration Opportunities
- **IoT Integration**: Smart room monitoring
- **Mobile App**: Room management on mobile
- **QR Codes**: Room identification and access
- **Analytics**: Room utilization reports

## Best Practices

### Room Management
- Use consistent room numbering schemes
- Maintain accurate capacity information
- Regular status updates for beds
- Document room-specific notes

### Performance
- Use eager loading for room-bed relationships
- Index frequently queried fields
- Implement caching for occupancy calculations
- Optimize bulk operations

### Data Integrity
- Validate bed assignments
- Prevent over-capacity assignments
- Maintain audit trails
- Regular data consistency checks

This module provides a solid foundation for comprehensive room and bed management with seamless integration across the entire hostel management system.
