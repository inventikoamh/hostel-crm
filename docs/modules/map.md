# Map Visualization Module

## Overview
The Map Visualization module provides interactive visual representation of hostels, rooms, and beds with real-time occupancy status. It offers floor-wise navigation and detailed bed information for efficient hostel management.

## Features

### Visual Mapping
- **Hostel Overview**: Grid-based hostel listing with occupancy statistics
- **Floor Navigation**: Interactive floor-wise room visualization
- **Room Layout**: Visual room arrangement with bed status indicators
- **Real-time Status**: Live occupancy and availability updates

### Interactive Elements
- **Clickable Beds**: Detailed bed information on click
- **Status Colors**: Color-coded bed status (available, occupied, maintenance, reserved)
- **Hover Effects**: Quick status preview on hover
- **Responsive Design**: Works on all device sizes

### Data Integration
- **Live Data**: Real-time synchronization with database
- **AJAX Updates**: Dynamic content loading without page refresh
- **Status Tracking**: Automatic status updates from room/bed changes

## Routes

### Map Routes
```php
Route::prefix('map')->name('map.')->middleware('auth')->group(function () {
    Route::get('/', [MapController::class, 'index'])->name('index');
    Route::get('/hostel/{hostel}', [MapController::class, 'hostel'])->name('hostel');
    Route::get('/room/{room}', [MapController::class, 'room'])->name('room');
    Route::get('/occupancy/{hostel}/{floor?}', [MapController::class, 'occupancyData'])->name('occupancy');
    Route::post('/bed/{bed}/status', [MapController::class, 'updateBedStatus'])->name('bed.status');
});
```

## Controllers

### MapController

#### index()
Displays overview of all hostels with occupancy statistics.

```php
public function index()
{
    $hostels = Hostel::with('rooms.beds')->get()->map(function ($hostel) {
        return [
            'id' => $hostel->id,
            'name' => $hostel->name,
            'address' => $hostel->address,
            'total_rooms' => $hostel->rooms->count(),
            'total_beds' => $hostel->beds->count(),
            'available_beds' => $hostel->available_beds_count,
            'occupied_beds' => $hostel->occupied_beds_count,
            'occupancy_rate' => $hostel->actual_occupancy_rate,
            'view_url' => route('map.hostel', $hostel->id),
        ];
    });

    return view('map.index', compact('hostels'));
}
```

#### hostel()
Shows detailed floor-wise view of a specific hostel.

```php
public function hostel(Hostel $hostel, Request $request)
{
    $selectedFloor = $request->query('floor', $hostel->floors[0] ?? null);
    return view('map.hostel', compact('hostel', 'selectedFloor'));
}
```

#### occupancyData()
Returns JSON data for room and bed occupancy information.

```php
public function occupancyData(Hostel $hostel, $floor = null)
{
    $roomsQuery = $hostel->rooms()->with(['beds.tenant']);
    
    if ($floor) {
        $roomsQuery->where('floor', $floor);
    }
    
    $rooms = $roomsQuery->orderBy('room_number')->get()->map(function ($room) {
        return [
            'id' => $room->id,
            'room_number' => $room->room_number,
            'floor' => $room->floor,
            'room_type' => $room->room_type_display,
            'capacity' => $room->capacity,
            'status' => $room->status,
            'occupancy_rate' => $room->occupancy_rate,
            'beds' => $room->beds->map(function ($bed) {
                return [
                    'id' => $bed->id,
                    'bed_number' => $bed->bed_number,
                    'status' => $bed->status,
                    'bed_type' => $bed->bed_type_display,
                    'tenant_name' => $bed->tenant ? $bed->tenant->name : null,
                    'tenant_id' => $bed->tenant ? $bed->tenant->id : null,
                    'occupied_from' => $bed->occupied_from ? $bed->occupied_from->format('M j, Y') : null,
                    'occupied_until' => $bed->occupied_until ? $bed->occupied_until->format('M j, Y') : null,
                    'monthly_rent' => $bed->monthly_rent ? '₹' . number_format($bed->monthly_rent, 2) : null,
                    'notes' => $bed->notes,
                ];
            }),
            'is_fully_occupied' => $room->is_fully_occupied,
            'is_empty' => $room->is_empty,
        ];
    });

    return response()->json([
        'rooms' => $rooms,
        'floors' => $hostel->floors,
        'selected_floor' => $floor,
    ]);
}
```

#### updateBedStatus()
Updates bed status via AJAX requests.

```php
public function updateBedStatus(Request $request, Bed $bed)
{
    $validator = Validator::make($request->all(), [
        'status' => 'required|in:available,occupied,maintenance,reserved',
        'tenant_id' => 'nullable|exists:users,id',
        'occupied_from' => 'nullable|date',
        'occupied_until' => 'nullable|date|after_or_equal:occupied_from',
        'monthly_rent' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $bed->update($request->only([
        'status', 'tenant_id', 'occupied_from', 'occupied_until', 'monthly_rent', 'notes'
    ]));

    // Update room status based on bed occupancy
    $room = $bed->room;
    if ($room->is_fully_occupied) {
        $room->update(['status' => 'occupied']);
    } elseif ($room->is_empty) {
        $room->update(['status' => 'available']);
    } else {
        $room->update(['status' => 'available']); // Mixed occupancy
    }

    return response()->json(['message' => 'Bed status updated successfully!', 'bed' => $bed->load('tenant')]);
}
```

## Views

### Map Index (`map/index.blade.php`)
Displays grid of hostels with occupancy statistics and navigation links.

```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($hostels as $hostel)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-semibold mb-2">{{ $hostel['name'] }}</h3>
            <p class="text-sm text-gray-600 mb-4">{{ $hostel['address'] }}</p>
            
            <!-- Occupancy Statistics -->
            <div class="space-y-2 mb-4">
                <div class="flex justify-between">
                    <span>Total Rooms:</span>
                    <span>{{ $hostel['total_rooms'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Available Beds:</span>
                    <span class="text-green-600">{{ $hostel['available_beds'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Occupied Beds:</span>
                    <span class="text-blue-600">{{ $hostel['occupied_beds'] }}</span>
                </div>
            </div>
            
            <!-- Occupancy Rate Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                <div class="h-2.5 rounded-full bg-blue-500" style="width: {{ $hostel['occupancy_rate'] }}%;"></div>
            </div>
            
            <a href="{{ $hostel['view_url'] }}" class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-map-marked-alt"></i> View Map
            </a>
        </div>
    @empty
        <p class="text-gray-600">No hostels found to display on the map.</p>
    @endforelse
</div>
```

### Hostel Map (`map/hostel.blade.php`)
Interactive floor-wise room and bed visualization.

```blade
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Floor Navigation Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border p-6 sticky top-20">
            <h3 class="text-lg font-semibold mb-4">Floors</h3>
            <nav class="space-y-2">
                @forelse($hostel->floors as $floor)
                    <a href="{{ route('map.hostel', ['hostel' => $hostel->id, 'floor' => $floor]) }}"
                       class="flex items-center px-4 py-2 rounded-lg transition-colors
                       {{ $selectedFloor == $floor ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-layer-group mr-3"></i>
                        {{ $floor }}
                    </a>
                @empty
                    <p class="text-gray-600 text-sm">No floors found for this hostel.</p>
                @endforelse
            </nav>
        </div>
    </div>

    <!-- Room Layout -->
    <div class="lg:col-span-3 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold mb-4">
                Rooms on {{ $selectedFloor ?? 'All Floors' }}
                <span id="floorOccupancyRate" class="ml-3 text-sm font-normal text-gray-500"></span>
            </h3>

            <div id="roomLayout" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <!-- Rooms loaded via JavaScript -->
                <p id="loadingMessage" class="text-gray-600 col-span-full">Loading rooms...</p>
                <p id="noRoomsMessage" class="text-gray-600 col-span-full hidden">No rooms found for this floor.</p>
            </div>
        </div>
    </div>
</div>
```

## JavaScript Integration

### Dynamic Room Loading
```javascript
function loadRoomData(hostelId, floor) {
    fetch(`/map/occupancy/${hostelId}/${floor}`)
        .then(response => response.json())
        .then(data => {
            renderRooms(data.rooms);
            updateFloorStats(data.rooms);
        })
        .catch(error => {
            console.error('Error loading room data:', error);
        });
}

function renderRooms(rooms) {
    const container = document.getElementById('roomLayout');
    container.innerHTML = '';
    
    rooms.forEach(room => {
        const roomElement = createRoomElement(room);
        container.appendChild(roomElement);
    });
}

function createRoomElement(room) {
    const roomDiv = document.createElement('div');
    roomDiv.className = 'bg-white border rounded-lg p-4 hover:shadow-md transition-shadow';
    
    roomDiv.innerHTML = `
        <h4 class="font-semibold text-lg mb-2">Room ${room.room_number}</h4>
        <p class="text-sm text-gray-600 mb-3">${room.room_type} • ${room.capacity} beds</p>
        <div class="grid grid-cols-2 gap-2">
            ${room.beds.map(bed => createBedElement(bed)).join('')}
        </div>
        <div class="mt-3 text-xs text-gray-500">
            Occupancy: ${room.occupancy_rate}%
        </div>
    `;
    
    return roomDiv;
}

function createBedElement(bed) {
    const statusColors = {
        'available': 'bg-green-100 text-green-800',
        'occupied': 'bg-blue-100 text-blue-800',
        'maintenance': 'bg-yellow-100 text-yellow-800',
        'reserved': 'bg-purple-100 text-purple-800'
    };
    
    return `
        <div class="bed-card p-2 rounded text-center cursor-pointer transition-all ${statusColors[bed.status]}"
             onclick="showBedDetails(${JSON.stringify(bed).replace(/"/g, '&quot;')})">
            <i class="fas fa-bed text-sm mb-1"></i>
            <p class="text-xs font-semibold">Bed ${bed.bed_number}</p>
            <p class="text-xs">${bed.status}</p>
        </div>
    `;
}
```

### Bed Details Modal
```javascript
function showBedDetails(bed) {
    document.getElementById('modalBedNumber').textContent = bed.bed_number;
    document.getElementById('modalBedStatus').textContent = bed.status;
    document.getElementById('modalBedType').textContent = bed.bed_type;
    
    // Show/hide tenant information
    const tenantInfo = document.getElementById('modalTenantInfo');
    if (bed.tenant_name) {
        tenantInfo.classList.remove('hidden');
        document.getElementById('modalTenantName').textContent = bed.tenant_name;
    } else {
        tenantInfo.classList.add('hidden');
    }
    
    // Show modal
    document.getElementById('bedDetailsModal').classList.remove('hidden');
}

function closeBedDetailsModal() {
    document.getElementById('bedDetailsModal').classList.add('hidden');
}
```

## Status Color Coding

### Bed Status Colors
- **Available** (Green): `bg-green-100 text-green-800`
- **Occupied** (Blue): `bg-blue-100 text-blue-800`
- **Maintenance** (Yellow): `bg-yellow-100 text-yellow-800`
- **Reserved** (Purple): `bg-purple-100 text-purple-800`

### Room Status Indicators
- **Empty Room**: Light gray border
- **Partially Occupied**: Orange accent
- **Fully Occupied**: Red accent
- **Under Maintenance**: Yellow accent

## Integration Points

### With Room Module
- Real-time room and bed data
- Status synchronization
- Capacity and occupancy calculations

### With Tenant Module
- Tenant assignment visualization
- Occupancy period display
- Rent information

### With Dashboard
- Quick navigation to map views
- Occupancy statistics integration
- Alert notifications for issues

## Performance Considerations

### Optimization Strategies
- **AJAX Loading**: Prevents full page reloads
- **Selective Data**: Only loads necessary floor data
- **Caching**: Browser caching for static elements
- **Lazy Loading**: Loads room details on demand

### Database Queries
- **Eager Loading**: Loads related models efficiently
- **Selective Fields**: Only fetches required data
- **Indexing**: Optimized database indexes for quick queries

## Future Enhancements

### Planned Features
- **Drag & Drop**: Bed assignment via drag and drop
- **Real-time Updates**: WebSocket integration for live updates
- **Floor Plan Upload**: Custom floor plan images
- **3D Visualization**: 3D room and bed visualization
- **Mobile App**: Native mobile map interface

### Advanced Features
- **Heatmaps**: Occupancy density visualization
- **Historical Data**: Time-based occupancy trends
- **Predictive Analytics**: Occupancy forecasting
- **Integration APIs**: Third-party system integration

## Best Practices

### User Experience
- Intuitive color coding
- Responsive design for all devices
- Fast loading times
- Clear visual hierarchy

### Data Management
- Real-time data synchronization
- Efficient query optimization
- Error handling for failed requests
- Graceful degradation for slow connections

This map visualization module provides an intuitive and powerful interface for managing hostel occupancy with real-time visual feedback and seamless integration with the core management system.
