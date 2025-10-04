<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Hostel;
use App\Models\Bed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms
     */
    public function index(Request $request)
    {
        $query = Room::with(['hostel', 'beds']);

        // Apply filters
        if ($request->filled('hostel_id')) {
            $query->byHostel($request->hostel_id);
        }

        if ($request->filled('floor')) {
            $query->byFloor($request->floor);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('room_type')) {
            $query->where('room_type', $request->room_type);
        }

        $rooms = $query->orderBy('hostel_id')->orderBy('floor')->orderBy('room_number')->get()->map(function ($room) {
            return [
                'id' => $room->id,
                'hostel_name' => $room->hostel->name,
                'room_number' => $room->room_number,
                'floor' => $room->floor,
                'room_type' => $room->room_type_display,
                'capacity' => $room->capacity,
                'occupied_beds' => $room->occupied_beds_count,
                'available_beds' => $room->available_beds_count,
                'occupancy_rate' => $room->occupancy_rate . '%',
                'rent_per_bed' => '₹' . number_format((float) $room->rent_per_bed, 2),
                'status' => $room->status,
                'has_ac' => $room->has_ac ? 'Yes' : 'No',
                'view_url' => route('rooms.show', $room->id),
                'edit_url' => route('rooms.edit', $room->id),
                'delete_url' => route('rooms.destroy', $room->id)
            ];
        });

        $columns = [
            ['key' => 'hostel_name', 'label' => 'Hostel', 'width' => 'w-40'],
            ['key' => 'room_number', 'label' => 'Room', 'width' => 'w-24'],
            ['key' => 'floor', 'label' => 'Floor', 'width' => 'w-20'],
            ['key' => 'room_type', 'label' => 'Type', 'width' => 'w-32'],
            ['key' => 'capacity', 'label' => 'Capacity', 'width' => 'w-20'],
            ['key' => 'occupied_beds', 'label' => 'Occupied', 'width' => 'w-24'],
            ['key' => 'available_beds', 'label' => 'Available', 'width' => 'w-24'],
            ['key' => 'occupancy_rate', 'label' => 'Occupancy', 'width' => 'w-24'],
            ['key' => 'rent_per_bed', 'label' => 'Rent/Bed', 'width' => 'w-28'],
            ['key' => 'status', 'label' => 'Status', 'component' => 'components.status-badge', 'width' => 'w-24'],
            ['key' => 'has_ac', 'label' => 'AC', 'width' => 'w-16']
        ];

        $hostels = Hostel::active()->get();
        $floors = Room::distinct('floor')->pluck('floor')->sort()->values();
        $roomTypes = ['single', 'double', 'triple', 'dormitory', 'suite', 'studio'];

        $filters = [
            [
                'key' => 'hostel_id',
                'label' => 'Hostel',
                'type' => 'select',
                'options' => $hostels->map(fn($hostel) => ['value' => $hostel->id, 'label' => $hostel->name])->toArray()
            ],
            [
                'key' => 'floor',
                'label' => 'Floor',
                'type' => 'select',
                'options' => $floors->map(fn($floor) => ['value' => $floor, 'label' => "Floor {$floor}"])->toArray()
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => 'available', 'label' => 'Available'],
                    ['value' => 'occupied', 'label' => 'Occupied'],
                    ['value' => 'maintenance', 'label' => 'Maintenance'],
                    ['value' => 'reserved', 'label' => 'Reserved']
                ]
            ],
            [
                'key' => 'room_type',
                'label' => 'Room Type',
                'type' => 'select',
                'options' => collect($roomTypes)->map(fn($type) => ['value' => $type, 'label' => ucfirst($type)])->toArray()
            ]
        ];

        $bulkActions = [
            [
                'key' => 'set_maintenance',
                'label' => 'Set Maintenance',
                'icon' => 'fas fa-tools'
            ],
            [
                'key' => 'set_available',
                'label' => 'Set Available',
                'icon' => 'fas fa-check'
            ],
            [
                'key' => 'delete',
                'label' => 'Delete',
                'icon' => 'fas fa-trash'
            ]
        ];

        $pagination = [
            'from' => 1,
            'to' => $rooms->count(),
            'total' => $rooms->count(),
            'current_page' => 1,
            'per_page' => 25
        ];

        return view('rooms.index', compact('rooms', 'columns', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new room
     */
    public function create()
    {
        $hostels = Hostel::active()->get();
        return view('rooms.create', compact('hostels'));
    }

    /**
     * Store a newly created room
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hostel_id' => 'required|exists:hostels,id',
            'room_number' => 'required|string|max:20',
            'room_type' => 'required|in:single,double,triple,dormitory,suite,studio',
            'floor' => 'required|integer|min:0|max:50',
            'capacity' => 'required|integer|min:1|max:20',
            'rent_per_bed' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'area_sqft' => 'nullable|numeric|min:0',
            'has_attached_bathroom' => 'boolean',
            'has_balcony' => 'boolean',
            'has_ac' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check for unique room number within hostel
        $existingRoom = Room::where('hostel_id', $request->hostel_id)
            ->where('room_number', $request->room_number)
            ->first();

        if ($existingRoom) {
            return back()->withErrors(['room_number' => 'Room number already exists in this hostel.'])->withInput();
        }

        $room = Room::create($request->all());

        // Create beds for the room
        for ($i = 1; $i <= $room->capacity; $i++) {
            Bed::create([
                'room_id' => $room->id,
                'bed_number' => str_pad($i, 2, '0', STR_PAD_LEFT),
                'bed_type' => 'single', // Default bed type
                'status' => 'available'
            ]);
        }

        return redirect()->route('rooms.show', $room->id)->with('success', 'Room created successfully with ' . $room->capacity . ' beds!');
    }

    /**
     * Display the specified room
     */
    public function show(string $id)
    {
        $room = Room::with(['hostel', 'beds.tenant'])->findOrFail($id);

        return view('rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified room
     */
    public function edit(string $id)
    {
        $room = Room::findOrFail($id);
        $hostels = Hostel::active()->get();

        return view('rooms.edit', compact('room', 'hostels'));
    }

    /**
     * Update the specified room
     */
    public function update(Request $request, string $id)
    {
        $room = Room::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'hostel_id' => 'required|exists:hostels,id',
            'room_number' => 'required|string|max:20',
            'room_type' => 'required|in:single,double,triple,dormitory,suite,studio',
            'floor' => 'required|integer|min:0|max:50',
            'capacity' => 'required|integer|min:1|max:20',
            'rent_per_bed' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance,reserved',
            'description' => 'nullable|string|max:1000',
            'area_sqft' => 'nullable|numeric|min:0',
            'has_attached_bathroom' => 'boolean',
            'has_balcony' => 'boolean',
            'has_ac' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check for unique room number within hostel (excluding current room)
        $existingRoom = Room::where('hostel_id', $request->hostel_id)
            ->where('room_number', $request->room_number)
            ->where('id', '!=', $room->id)
            ->first();

        if ($existingRoom) {
            return back()->withErrors(['room_number' => 'Room number already exists in this hostel.'])->withInput();
        }

        // Handle capacity changes
        $oldCapacity = $room->capacity;
        $newCapacity = $request->capacity;

        $room->update($request->all());

        if ($newCapacity > $oldCapacity) {
            // Add new beds
            for ($i = $oldCapacity + 1; $i <= $newCapacity; $i++) {
                Bed::create([
                    'room_id' => $room->id,
                    'bed_number' => str_pad($i, 2, '0', STR_PAD_LEFT),
                    'bed_type' => 'single',
                    'status' => 'available'
                ]);
            }
        } elseif ($newCapacity < $oldCapacity) {
            // Remove excess beds (only if they're not occupied)
            $bedsToRemove = $room->beds()
                ->where('bed_number', '>', str_pad($newCapacity, 2, '0', STR_PAD_LEFT))
                ->where('status', '!=', 'occupied')
                ->get();

            foreach ($bedsToRemove as $bed) {
                $bed->delete();
            }
        }

        return redirect()->route('rooms.show', $room->id)->with('success', 'Room updated successfully!');
    }

    /**
     * Remove the specified room
     */
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);

        // Check if any beds are occupied
        $occupiedBeds = $room->beds()->where('status', 'occupied')->count();
        if ($occupiedBeds > 0) {
            return redirect()->route('rooms.index')->with('error', 'Cannot delete room with occupied beds.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully!');
    }
}
