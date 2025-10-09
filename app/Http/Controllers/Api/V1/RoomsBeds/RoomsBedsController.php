<?php

namespace App\Http\Controllers\Api\V1\RoomsBeds;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Bed;
use App\Models\BedAssignment;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class RoomsBedsController extends Controller
{
    // ==================== ROOMS API ====================

    /**
     * Display a listing of rooms
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Room::with(['hostel', 'beds.assignments.tenant']);

            // Apply filters
            if ($request->has('hostel_id')) {
                $query->where('hostel_id', $request->hostel_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('room_type')) {
                $query->where('room_type', $request->room_type);
            }

            if ($request->has('floor')) {
                $query->where('floor', $request->floor);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('room_number', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('hostel', function ($hostelQuery) use ($search) {
                          $hostelQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Handle sorting by hostel name
            if ($sortBy === 'hostel_name') {
                $query->join('hostels', 'rooms.hostel_id', '=', 'hostels.id')
                      ->orderBy('hostels.name', $sortOrder)
                      ->select('rooms.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $rooms = $query->paginate($perPage);

            // Transform data
            $rooms->getCollection()->transform(function ($room) {
                return $this->transformRoom($room);
            });

            return response()->json([
                'success' => true,
                'message' => 'Rooms retrieved successfully',
                'data' => $rooms->items(),
                'pagination' => [
                    'current_page' => $rooms->currentPage(),
                    'last_page' => $rooms->lastPage(),
                    'per_page' => $rooms->perPage(),
                    'total' => $rooms->total(),
                    'from' => $rooms->firstItem(),
                    'to' => $rooms->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve rooms',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new room (GET version for testing)
     */
    public function createRoom(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Room creation form data',
                'data' => [
                    'required_fields' => [
                        'hostel_id' => 'Hostel ID (required)',
                        'room_number' => 'Room number (required)',
                        'room_type' => 'Room type: single, double, triple, dormitory, suite, studio (required)',
                        'floor' => 'Floor number (required)',
                        'capacity' => 'Total number of beds (required)',
                        'rent_per_bed' => 'Rent per bed amount (required)',
                        'status' => 'Status: available, occupied, maintenance, reserved (required)'
                    ],
                    'optional_fields' => [
                        'description' => 'Room description',
                        'amenities' => 'Array of room amenities',
                        'area_sqft' => 'Room area in square feet',
                        'has_attached_bathroom' => 'Has attached bathroom (boolean)',
                        'has_balcony' => 'Has balcony (boolean)',
                        'has_ac' => 'Has air conditioning (boolean)',
                        'is_active' => 'Is room active (boolean)',
                        'coordinates' => 'Room coordinates for map positioning'
                    ],
                    'example_request' => [
                        'hostel_id' => 1,
                        'room_number' => '101',
                        'room_type' => 'double',
                        'floor' => 1,
                        'capacity' => 2,
                        'rent_per_bed' => 500.00,
                        'status' => 'available',
                        'description' => 'Spacious double room with modern amenities',
                        'amenities' => ['WiFi', 'TV', 'Air Conditioning'],
                        'area_sqft' => 200.00,
                        'has_attached_bathroom' => true,
                        'has_balcony' => false,
                        'has_ac' => true,
                        'is_active' => true
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/rooms-beds/rooms for actual creation.'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve creation form data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created room
     */
    public function storeRoom(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'hostel_id' => 'required|exists:hostels,id',
                'room_number' => 'required|string|max:50',
                'room_type' => ['required', Rule::in(['single', 'double', 'triple', 'dormitory', 'suite', 'studio'])],
                'floor' => 'required|integer|min:0',
                'capacity' => 'required|integer|min:1',
                'rent_per_bed' => 'required|numeric|min:0',
                'status' => ['required', Rule::in(['available', 'occupied', 'maintenance', 'reserved'])],
                'description' => 'nullable|string',
                'amenities' => 'nullable|array',
                'area_sqft' => 'nullable|numeric|min:0',
                'has_attached_bathroom' => 'nullable|boolean',
                'has_balcony' => 'nullable|boolean',
                'has_ac' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'coordinates' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Check for unique room number within hostel
            $existingRoom = Room::where('hostel_id', $validated['hostel_id'])
                ->where('room_number', $validated['room_number'])
                ->first();

            if ($existingRoom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room number already exists in this hostel'
                ], 422);
            }

            $room = Room::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Room created successfully',
                'data' => $this->transformRoom($room->load('hostel'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified room
     */
    public function showRoom(Request $request, $id): JsonResponse
    {
        try {
            $room = Room::with(['hostel', 'beds.assignments.tenant'])
                ->find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Room retrieved successfully',
                'data' => $this->transformRoom($room, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified room
     */
    public function updateRoom(Request $request, $id): JsonResponse
    {
        try {
            $room = Room::find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'hostel_id' => 'sometimes|required|exists:hostels,id',
                'room_number' => 'sometimes|required|string|max:50',
                'room_type' => ['sometimes', 'required', Rule::in(['single', 'double', 'triple', 'dormitory', 'suite', 'studio'])],
                'floor' => 'sometimes|required|integer|min:0',
                'capacity' => 'sometimes|required|integer|min:1',
                'rent_per_bed' => 'sometimes|required|numeric|min:0',
                'status' => ['sometimes', 'required', Rule::in(['available', 'occupied', 'maintenance', 'reserved'])],
                'description' => 'nullable|string',
                'amenities' => 'nullable|array',
                'area_sqft' => 'nullable|numeric|min:0',
                'has_attached_bathroom' => 'nullable|boolean',
                'has_balcony' => 'nullable|boolean',
                'has_ac' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
                'coordinates' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Check for unique room number within hostel if room_number is being updated
            if (isset($validated['room_number']) || isset($validated['hostel_id'])) {
                $hostelId = $validated['hostel_id'] ?? $room->hostel_id;
                $roomNumber = $validated['room_number'] ?? $room->room_number;
                
                $existingRoom = Room::where('hostel_id', $hostelId)
                    ->where('room_number', $roomNumber)
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingRoom) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Room number already exists in this hostel'
                    ], 422);
                }
            }

            $room->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Room updated successfully',
                'data' => $this->transformRoom($room->load('hostel'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified room
     */
    public function destroyRoom(Request $request, $id): JsonResponse
    {
        try {
            $room = Room::find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }

            // Check if room has active bed assignments
            $activeAssignments = BedAssignment::whereHas('bed', function ($query) use ($id) {
                $query->where('room_id', $id);
            })->where('status', 'active')->exists();

            if ($activeAssignments) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete room with active bed assignments. Please release all bed assignments first.'
                ], 422);
            }

            $room->delete();

            return response()->json([
                'success' => true,
                'message' => 'Room deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room statistics
     */
    public function roomStats(Request $request, $id): JsonResponse
    {
        try {
            $room = Room::with(['beds.assignments.tenant'])->find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }

            $stats = [
                'basic_info' => [
                    'room_number' => $room->room_number,
                    'room_type' => $room->room_type,
                    'room_type_display' => $room->room_type_display,
                    'floor' => $room->floor,
                    'capacity' => $room->capacity,
                    'area_sqft' => $room->area_sqft,
                    'rent_per_bed' => $room->rent_per_bed,
                    'status' => $room->status,
                    'status_badge' => $room->status_badge,
                ],
                'occupancy_info' => [
                    'occupied_beds_count' => $room->occupied_beds_count,
                    'available_beds_count' => $room->available_beds_count,
                    'occupancy_rate' => $room->occupancy_rate,
                    'can_accommodate' => $room->canAccommodate(),
                ],
                'amenities' => [
                    'room_amenities' => $room->amenities,
                    'has_attached_bathroom' => $room->has_attached_bathroom,
                    'has_balcony' => $room->has_balcony,
                    'has_ac' => $room->has_ac,
                ],
                'hostel_info' => [
                    'hostel_id' => $room->hostel->id,
                    'hostel_name' => $room->hostel->name,
                    'hostel_address' => $room->hostel->full_address,
                ],
                'beds_summary' => [
                    'total_beds' => $room->beds->count(),
                    'available_beds' => $room->beds->where('status', 'available')->count(),
                    'occupied_beds' => $room->beds->where('status', 'occupied')->count(),
                    'maintenance_beds' => $room->beds->where('status', 'maintenance')->count(),
                    'reserved_beds' => $room->beds->where('status', 'reserved')->count(),
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Room statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve room statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== BEDS API ====================

    /**
     * Display a listing of beds
     */
    public function indexBeds(Request $request): JsonResponse
    {
        try {
            $query = Bed::with(['room.hostel', 'assignments.tenant']);

            // Apply filters
            if ($request->has('room_id')) {
                $query->where('room_id', $request->room_id);
            }

            if ($request->has('hostel_id')) {
                $query->whereHas('room', function ($q) use ($request) {
                    $q->where('hostel_id', $request->hostel_id);
                });
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('bed_type')) {
                $query->where('bed_type', $request->bed_type);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('bed_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('room', function ($roomQuery) use ($search) {
                          $roomQuery->where('room_number', 'like', "%{$search}%");
                      })
                      ->orWhereHas('room.hostel', function ($hostelQuery) use ($search) {
                          $hostelQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $beds = $query->paginate($perPage);

            // Transform data
            $beds->getCollection()->transform(function ($bed) {
                return $this->transformBed($bed);
            });

            return response()->json([
                'success' => true,
                'message' => 'Beds retrieved successfully',
                'data' => $beds->items(),
                'pagination' => [
                    'current_page' => $beds->currentPage(),
                    'last_page' => $beds->lastPage(),
                    'per_page' => $beds->perPage(),
                    'total' => $beds->total(),
                    'from' => $beds->firstItem(),
                    'to' => $beds->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve beds',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new bed (GET version for testing)
     */
    public function createBed(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Bed creation form data',
                'data' => [
                    'required_fields' => [
                        'room_id' => 'Room ID (required)',
                        'bed_number' => 'Bed number (required)',
                        'bed_type' => 'Bed type: single, double, bunk_top, bunk_bottom (required)',
                        'status' => 'Status: available, occupied, maintenance, reserved (required)'
                    ],
                    'optional_fields' => [
                        'monthly_rent' => 'Individual bed rent amount',
                        'notes' => 'Bed notes',
                        'is_active' => 'Is bed active (boolean)',
                        'coordinates' => 'Bed coordinates for room layout'
                    ],
                    'example_request' => [
                        'room_id' => 1,
                        'bed_number' => 'A1',
                        'bed_type' => 'single',
                        'status' => 'available',
                        'monthly_rent' => 500.00,
                        'notes' => 'Near window, good ventilation',
                        'is_active' => true
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/rooms-beds/beds for actual creation.'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve creation form data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created bed
     */
    public function storeBed(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:rooms,id',
                'bed_number' => 'required|string|max:50',
                'bed_type' => ['required', Rule::in(['single', 'double', 'bunk_top', 'bunk_bottom'])],
                'status' => ['required', Rule::in(['available', 'occupied', 'maintenance', 'reserved'])],
                'monthly_rent' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'coordinates' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Check for unique bed number within room
            $existingBed = Bed::where('room_id', $validated['room_id'])
                ->where('bed_number', $validated['bed_number'])
                ->first();

            if ($existingBed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bed number already exists in this room'
                ], 422);
            }

            $bed = Bed::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Bed created successfully',
                'data' => $this->transformBed($bed->load('room.hostel'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified bed
     */
    public function showBed(Request $request, $id): JsonResponse
    {
        try {
            $bed = Bed::with(['room.hostel', 'assignments.tenant'])
                ->find($id);

            if (!$bed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bed not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bed retrieved successfully',
                'data' => $this->transformBed($bed, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified bed
     */
    public function updateBed(Request $request, $id): JsonResponse
    {
        try {
            $bed = Bed::find($id);

            if (!$bed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bed not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'room_id' => 'sometimes|required|exists:rooms,id',
                'bed_number' => 'sometimes|required|string|max:50',
                'bed_type' => ['sometimes', 'required', Rule::in(['single', 'double', 'bunk_top', 'bunk_bottom'])],
                'status' => ['sometimes', 'required', Rule::in(['available', 'occupied', 'maintenance', 'reserved'])],
                'monthly_rent' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'coordinates' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Check for unique bed number within room if bed_number or room_id is being updated
            if (isset($validated['bed_number']) || isset($validated['room_id'])) {
                $roomId = $validated['room_id'] ?? $bed->room_id;
                $bedNumber = $validated['bed_number'] ?? $bed->bed_number;
                
                $existingBed = Bed::where('room_id', $roomId)
                    ->where('bed_number', $bedNumber)
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingBed) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bed number already exists in this room'
                    ], 422);
                }
            }

            $bed->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Bed updated successfully',
                'data' => $this->transformBed($bed->load('room.hostel'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified bed
     */
    public function destroyBed(Request $request, $id): JsonResponse
    {
        try {
            $bed = Bed::find($id);

            if (!$bed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bed not found'
                ], 404);
            }

            // Check if bed has active assignments
            if ($bed->assignments()->where('status', 'active')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete bed with active assignments. Please release bed assignments first.'
                ], 422);
            }

            $bed->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bed deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bed statistics
     */
    public function bedStats(Request $request, $id): JsonResponse
    {
        try {
            $bed = Bed::with(['room.hostel', 'assignments.tenant'])->find($id);

            if (!$bed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bed not found'
                ], 404);
            }

            $currentAssignment = $bed->getCurrentAssignment();
            $currentTenant = $bed->getCurrentTenant();

            $stats = [
                'basic_info' => [
                    'bed_number' => $bed->bed_number,
                    'bed_type' => $bed->bed_type,
                    'bed_type_display' => $bed->bed_type_display,
                    'status' => $bed->status,
                    'status_badge' => $bed->status_badge,
                    'monthly_rent' => $bed->monthly_rent,
                    'current_rent' => $bed->current_rent,
                ],
                'room_info' => [
                    'room_id' => $bed->room->id,
                    'room_number' => $bed->room->room_number,
                    'room_type' => $bed->room->room_type,
                    'floor' => $bed->room->floor,
                    'hostel' => [
                        'id' => $bed->room->hostel->id,
                        'name' => $bed->room->hostel->name,
                        'address' => $bed->room->hostel->full_address,
                    ]
                ],
                'current_assignment' => $currentAssignment ? [
                    'id' => $currentAssignment->id,
                    'tenant' => $currentTenant ? [
                        'id' => $currentTenant->id,
                        'name' => $currentTenant->name,
                        'email' => $currentTenant->email,
                    ] : null,
                    'assigned_from' => $currentAssignment->assigned_from,
                    'assigned_until' => $currentAssignment->assigned_until,
                    'status' => $currentAssignment->status,
                    'monthly_rent' => $currentAssignment->monthly_rent,
                ] : null,
                'assignment_history' => [
                    'total_assignments' => $bed->assignments->count(),
                    'active_assignments' => $bed->assignments->where('status', 'active')->count(),
                    'completed_assignments' => $bed->assignments->where('status', 'completed')->count(),
                    'cancelled_assignments' => $bed->assignments->where('status', 'cancelled')->count(),
                ],
                'availability' => [
                    'has_active_assignment' => $bed->hasActiveAssignment(),
                    'has_reserved_assignment' => $bed->hasReservedAssignment(),
                    'is_available' => $bed->status === 'available' && !$bed->hasActiveAssignment(),
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Bed statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bed statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== BED ASSIGNMENTS API ====================

    /**
     * Assign tenant to bed
     */
    public function assignBed(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'bed_id' => 'required|exists:beds,id',
                'tenant_id' => 'required|exists:users,id',
                'assigned_from' => 'nullable|date',
                'assigned_until' => 'nullable|date|after:assigned_from',
                'monthly_rent' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $bed = Bed::find($request->bed_id);

            // Check if bed is available
            if ($bed->hasActiveAssignment()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bed is already assigned to another tenant'
                ], 422);
            }

            // Check for overlapping assignments
            $overlappingAssignment = BedAssignment::where('bed_id', $request->bed_id)
                ->where('status', 'active')
                ->overlappingWith($request->assigned_from ?? Carbon::now(), $request->assigned_until)
                ->first();

            if ($overlappingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bed has overlapping assignment during the specified period'
                ], 422);
            }

            $assignment = BedAssignment::create([
                'bed_id' => $request->bed_id,
                'tenant_id' => $request->tenant_id,
                'assigned_from' => $request->assigned_from ?? Carbon::now(),
                'assigned_until' => $request->assigned_until,
                'status' => 'active',
                'monthly_rent' => $request->monthly_rent ?? $bed->current_rent,
                'notes' => $request->notes,
            ]);

            // Update bed status
            $bed->update(['status' => 'occupied']);

            return response()->json([
                'success' => true,
                'message' => 'Bed assigned successfully',
                'data' => [
                    'assignment' => $assignment,
                    'bed' => $this->transformBed($bed->load('room.hostel')),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign bed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Release bed assignment
     */
    public function releaseBed(Request $request, $assignmentId): JsonResponse
    {
        try {
            $assignment = BedAssignment::find($assignmentId);

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found'
                ], 404);
            }

            if ($assignment->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment is not active'
                ], 422);
            }

            $assignment->update(['status' => 'completed']);

            // Update bed status
            $bed = $assignment->bed;
            $bed->update(['status' => 'available']);

            return response()->json([
                'success' => true,
                'message' => 'Bed released successfully',
                'data' => [
                    'assignment' => $assignment,
                    'bed' => $this->transformBed($bed->load('room.hostel')),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to release bed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search rooms and beds
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'type' => ['nullable', Rule::in(['rooms', 'beds', 'both'])],
                'hostel_id' => 'nullable|exists:hostels,id',
                'limit' => 'nullable|integer|min:1|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = $request->query;
            $type = $request->get('type', 'both');
            $hostelId = $request->hostel_id;
            $limit = $request->get('limit', 10);

            $results = [];

            if ($type === 'rooms' || $type === 'both') {
                $roomsQuery = Room::with(['hostel'])
                    ->where(function ($q) use ($query) {
                        $q->where('room_number', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%");
                    });

                if ($hostelId) {
                    $roomsQuery->where('hostel_id', $hostelId);
                }

                $rooms = $roomsQuery->limit($limit)->get()->map(function ($room) {
                    return [
                        'type' => 'room',
                        'id' => $room->id,
                        'room_number' => $room->room_number,
                        'room_type' => $room->room_type,
                        'floor' => $room->floor,
                        'status' => $room->status,
                        'hostel' => [
                            'id' => $room->hostel->id,
                            'name' => $room->hostel->name,
                        ]
                    ];
                });

                $results = array_merge($results, $rooms->toArray());
            }

            if ($type === 'beds' || $type === 'both') {
                $bedsQuery = Bed::with(['room.hostel'])
                    ->where(function ($q) use ($query) {
                        $q->where('bed_number', 'like', "%{$query}%")
                          ->orWhere('notes', 'like', "%{$query}%")
                          ->orWhereHas('room', function ($roomQuery) use ($query) {
                              $roomQuery->where('room_number', 'like', "%{$query}%");
                          });
                    });

                if ($hostelId) {
                    $bedsQuery->whereHas('room', function ($q) use ($hostelId) {
                        $q->where('hostel_id', $hostelId);
                    });
                }

                $beds = $bedsQuery->limit($limit)->get()->map(function ($bed) {
                    return [
                        'type' => 'bed',
                        'id' => $bed->id,
                        'bed_number' => $bed->bed_number,
                        'bed_type' => $bed->bed_type,
                        'status' => $bed->status,
                        'room' => [
                            'id' => $bed->room->id,
                            'room_number' => $bed->room->room_number,
                            'floor' => $bed->room->floor,
                        ],
                        'hostel' => [
                            'id' => $bed->room->hostel->id,
                            'name' => $bed->room->hostel->name,
                        ]
                    ];
                });

                $results = array_merge($results, $beds->toArray());
            }

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $results,
                'query' => $query,
                'count' => count($results)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search rooms and beds',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Transform room data for API response
     */
    private function transformRoom(Room $room, bool $detailed = false): array
    {
        $data = [
            'id' => $room->id,
            'hostel_id' => $room->hostel_id,
            'room_number' => $room->room_number,
            'room_type' => $room->room_type,
            'room_type_display' => $room->room_type_display,
            'floor' => $room->floor,
            'capacity' => $room->capacity,
            'rent_per_bed' => $room->rent_per_bed,
            'status' => $room->status,
            'status_badge' => $room->status_badge,
            'description' => $room->description,
            'area_sqft' => $room->area_sqft,
            'has_attached_bathroom' => $room->has_attached_bathroom,
            'has_balcony' => $room->has_balcony,
            'has_ac' => $room->has_ac,
            'is_active' => $room->is_active,
            'coordinates' => $room->coordinates,
            'created_at' => $room->created_at,
            'updated_at' => $room->updated_at,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'amenities' => $room->amenities,
                'hostel' => [
                    'id' => $room->hostel->id,
                    'name' => $room->hostel->name,
                    'address' => $room->hostel->full_address,
                ],
                'occupancy_info' => [
                    'occupied_beds_count' => $room->occupied_beds_count,
                    'available_beds_count' => $room->available_beds_count,
                    'occupancy_rate' => $room->occupancy_rate,
                    'can_accommodate' => $room->canAccommodate(),
                ],
                'beds' => $room->beds->map(function ($bed) {
                    return $this->transformBed($bed);
                }),
            ]);
        }

        return $data;
    }

    /**
     * Transform bed data for API response
     */
    private function transformBed(Bed $bed, bool $detailed = false): array
    {
        $data = [
            'id' => $bed->id,
            'room_id' => $bed->room_id,
            'bed_number' => $bed->bed_number,
            'bed_type' => $bed->bed_type,
            'bed_type_display' => $bed->bed_type_display,
            'status' => $bed->status,
            'status_badge' => $bed->status_badge,
            'monthly_rent' => $bed->monthly_rent,
            'current_rent' => $bed->current_rent,
            'notes' => $bed->notes,
            'is_active' => $bed->is_active,
            'coordinates' => $bed->coordinates,
            'created_at' => $bed->created_at,
            'updated_at' => $bed->updated_at,
        ];

        if ($detailed) {
            $currentAssignment = $bed->getCurrentAssignment();
            $currentTenant = $bed->getCurrentTenant();

            $data = array_merge($data, [
                'room' => [
                    'id' => $bed->room->id,
                    'room_number' => $bed->room->room_number,
                    'room_type' => $bed->room->room_type,
                    'floor' => $bed->room->floor,
                    'hostel' => [
                        'id' => $bed->room->hostel->id,
                        'name' => $bed->room->hostel->name,
                    ]
                ],
                'current_assignment' => $currentAssignment ? [
                    'id' => $currentAssignment->id,
                    'tenant' => $currentTenant ? [
                        'id' => $currentTenant->id,
                        'name' => $currentTenant->name,
                        'email' => $currentTenant->email,
                    ] : null,
                    'assigned_from' => $currentAssignment->assigned_from,
                    'assigned_until' => $currentAssignment->assigned_until,
                    'status' => $currentAssignment->status,
                    'monthly_rent' => $currentAssignment->monthly_rent,
                ] : null,
                'assignment_history' => $bed->assignments->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'tenant' => $assignment->tenant ? [
                            'id' => $assignment->tenant->id,
                            'name' => $assignment->tenant->name,
                        ] : null,
                        'assigned_from' => $assignment->assigned_from,
                        'assigned_until' => $assignment->assigned_until,
                        'status' => $assignment->status,
                        'monthly_rent' => $assignment->monthly_rent,
                    ];
                }),
                'availability' => [
                    'has_active_assignment' => $bed->hasActiveAssignment(),
                    'has_reserved_assignment' => $bed->hasReservedAssignment(),
                    'is_available' => $bed->status === 'available' && !$bed->hasActiveAssignment(),
                ]
            ]);
        }

        return $data;
    }
}
