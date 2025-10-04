<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\Bed;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Display hostel map view
     */
    public function index()
    {
        $hostels = Hostel::active()->with(['rooms.beds.tenant'])->get();

        return view('map.index', compact('hostels'));
    }

    /**
     * Display specific hostel floor map
     */
    public function hostel(Request $request, $hostelId)
    {
        $hostel = Hostel::with(['rooms.beds.tenant'])->findOrFail($hostelId);

        $selectedFloor = $request->get('floor', $hostel->floors[0] ?? 1);

        $floors = $hostel->floors;
        $roomsByFloor = $hostel->rooms()
            ->with(['beds.tenant'])
            ->where('floor', $selectedFloor)
            ->orderBy('room_number')
            ->get();

        // Generate layout data for the floor
        $floorLayout = $this->generateFloorLayout($roomsByFloor);

        return view('map.hostel', compact('hostel', 'floors', 'selectedFloor', 'roomsByFloor', 'floorLayout'));
    }

    /**
     * Get room details for modal display
     */
    public function roomDetails($roomId)
    {
        $room = Room::with(['hostel', 'beds.tenant'])->findOrFail($roomId);

        // Generate bed layout for the room
        $bedLayout = $this->generateBedLayout($room->beds);

        return response()->json([
            'room' => [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'room_type' => $room->room_type_display,
                'capacity' => $room->capacity,
                'status' => $room->status,
                'rent_per_bed' => $room->rent_per_bed,
                'has_ac' => $room->has_ac,
                'has_attached_bathroom' => $room->has_attached_bathroom,
                'has_balcony' => $room->has_balcony,
                'description' => $room->description,
                'area_sqft' => $room->area_sqft,
                'occupied_beds_count' => $room->occupied_beds_count,
                'available_beds_count' => $room->available_beds_count,
                'occupancy_rate' => $room->occupancy_rate,
                'hostel' => [
                    'id' => $room->hostel->id,
                    'name' => $room->hostel->name
                ]
            ],
            'beds' => $room->beds->map(function ($bed) {
                return [
                    'id' => $bed->id,
                    'bed_number' => $bed->bed_number,
                    'bed_type' => $bed->bed_type_display,
                    'status' => $bed->status,
                    'tenant' => $bed->tenant ? [
                        'id' => $bed->tenant->id,
                        'name' => $bed->tenant->name
                    ] : null,
                    'occupied_from' => $bed->occupied_from ? $bed->occupied_from->format('M j, Y') : null,
                    'occupied_until' => $bed->occupied_until ? $bed->occupied_until->format('M j, Y') : null,
                    'monthly_rent' => $bed->monthly_rent ? '₹' . number_format($bed->monthly_rent, 2) : null,
                ];
            }),
            'bedLayout' => $bedLayout
        ]);
    }

    /**
     * Get real-time occupancy data for AJAX requests
     */
    public function occupancyData($hostelId, $floor = null)
    {
        $query = Room::with(['beds.tenant'])->where('hostel_id', $hostelId);

        if ($floor) {
            $query->where('floor', $floor);
        }

        $rooms = $query->get();

        $data = [
            'total_rooms' => $rooms->count(),
            'total_beds' => $rooms->sum('capacity'),
            'occupied_beds' => $rooms->sum(function($room) {
                return $room->beds->where('status', 'occupied')->count();
            }),
            'available_beds' => $rooms->sum(function($room) {
                return $room->beds->where('status', 'available')->count();
            }),
            'maintenance_beds' => $rooms->sum(function($room) {
                return $room->beds->where('status', 'maintenance')->count();
            }),
            'rooms' => $rooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'floor' => $room->floor,
                    'status' => $room->status,
                    'capacity' => $room->capacity,
                    'occupied_beds' => $room->beds->where('status', 'occupied')->count(),
                    'available_beds' => $room->beds->where('status', 'available')->count(),
                    'occupancy_rate' => $room->occupancy_rate,
                    'beds' => $room->beds->map(function($bed) {
                        return [
                            'id' => $bed->id,
                            'bed_number' => $bed->bed_number,
                            'status' => $bed->status,
                            'tenant_name' => $bed->tenant ? $bed->tenant->name : null,
                            'occupied_from' => $bed->occupied_from ? $bed->occupied_from->format('Y-m-d') : null,
                            'occupied_until' => $bed->occupied_until ? $bed->occupied_until->format('Y-m-d') : null,
                            'is_overdue' => $bed->is_overdue
                        ];
                    })
                ];
            })
        ];

        return response()->json($data);
    }

    /**
     * Generate floor layout with room positioning
     */
    private function generateFloorLayout($rooms)
    {
        $layout = [];
        $roomsPerRow = 6; // Configurable
        $roomWidth = 120;
        $roomHeight = 80;
        $spacing = 20;

        foreach ($rooms as $index => $room) {
            $row = intval($index / $roomsPerRow);
            $col = $index % $roomsPerRow;

            $layout[] = [
                'room' => $room,
                'x' => $col * ($roomWidth + $spacing) + $spacing,
                'y' => $row * ($roomHeight + $spacing) + $spacing,
                'width' => $roomWidth,
                'height' => $roomHeight
            ];
        }

        return $layout;
    }

    /**
     * Generate bed layout within a room
     */
    private function generateBedLayout($beds)
    {
        $layout = [];
        $bedsPerRow = 2; // Configurable based on room type
        $bedWidth = 60;
        $bedHeight = 40;
        $spacing = 10;

        foreach ($beds as $index => $bed) {
            $row = intval($index / $bedsPerRow);
            $col = $index % $bedsPerRow;

            $layout[] = [
                'bed' => $bed,
                'x' => $col * ($bedWidth + $spacing) + $spacing,
                'y' => $row * ($bedHeight + $spacing) + $spacing,
                'width' => $bedWidth,
                'height' => $bedHeight
            ];
        }

        return $layout;
    }

    /**
     * Update bed status via AJAX
     */
    public function updateBedStatus(Request $request, $bedId)
    {
        $bed = Bed::findOrFail($bedId);
        $status = $request->input('status');

        if (!in_array($status, ['available', 'occupied', 'maintenance', 'reserved'])) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        if ($status === 'available') {
            $bed->releaseTenant();
        } elseif ($status === 'maintenance') {
            $bed->setMaintenance($request->input('notes'));
        }

        $bed->update(['status' => $status]);
        $bed->room->updateStatus();

        return response()->json([
            'success' => true,
            'bed' => [
                'id' => $bed->id,
                'status' => $bed->status,
                'tenant_name' => $bed->tenant ? $bed->tenant->name : null
            ],
            'room' => [
                'id' => $bed->room->id,
                'status' => $bed->room->status,
                'occupancy_rate' => $bed->room->occupancy_rate
            ]
        ]);
    }
}
