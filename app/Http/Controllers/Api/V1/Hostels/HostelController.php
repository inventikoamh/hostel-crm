<?php

namespace App\Http\Controllers\Api\V1\Hostels;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HostelController extends Controller
{
    /**
     * Display a listing of hostels
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Hostel::query();

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('city')) {
                $query->where('city', 'like', '%' . $request->city . '%');
            }

            if ($request->has('state')) {
                $query->where('state', 'like', '%' . $request->state . '%');
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('address', 'like', '%' . $search . '%')
                      ->orWhere('city', 'like', '%' . $search . '%')
                      ->orWhere('manager_name', 'like', '%' . $search . '%');
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $hostels = $query->paginate($perPage);

            // Transform data
            $hostels->getCollection()->transform(function ($hostel) {
                return $this->transformHostel($hostel);
            });

            return response()->json([
                'success' => true,
                'message' => 'Hostels retrieved successfully',
                'data' => $hostels->items(),
                'pagination' => [
                    'current_page' => $hostels->currentPage(),
                    'last_page' => $hostels->lastPage(),
                    'per_page' => $hostels->perPage(),
                    'total' => $hostels->total(),
                    'from' => $hostels->firstItem(),
                    'to' => $hostels->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve hostels',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new hostel (GET version for testing)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Hostel creation form data',
                'data' => [
                    'required_fields' => [
                        'name' => 'Hostel name (required)',
                        'address' => 'Street address (required)',
                        'city' => 'City name (required)',
                        'state' => 'State/province (required)',
                        'country' => 'Country name (required)',
                        'postal_code' => 'Postal/ZIP code (required)',
                        'phone' => 'Contact phone number (required)',
                        'email' => 'Contact email address (required)',
                        'status' => 'Status: active, inactive, or maintenance (required)',
                        'manager_name' => 'Manager\'s full name (required)',
                        'manager_phone' => 'Manager\'s phone number (required)',
                        'manager_email' => 'Manager\'s email address (required)'
                    ],
                    'optional_fields' => [
                        'description' => 'Hostel description',
                        'website' => 'Website URL',
                        'amenities' => 'Array of amenities',
                        'images' => 'Array of image URLs',
                        'rules' => 'House rules',
                        'check_in_time' => 'Check-in time (HH:MM format)',
                        'check_out_time' => 'Check-out time (HH:MM format)'
                    ],
                    'example_request' => [
                        'name' => 'New Hostel',
                        'description' => 'A beautiful new hostel',
                        'address' => '456 Oak Avenue',
                        'city' => 'Los Angeles',
                        'state' => 'CA',
                        'country' => 'USA',
                        'postal_code' => '90210',
                        'phone' => '+1-555-0456',
                        'email' => 'info@newhostel.com',
                        'website' => 'https://newhostel.com',
                        'amenities' => ['WiFi', 'Laundry', 'Kitchen'],
                        'images' => ['https://example.com/image1.jpg'],
                        'status' => 'active',
                        'manager_name' => 'Jane Doe',
                        'manager_phone' => '+1-555-0457',
                        'manager_email' => 'jane@newhostel.com',
                        'rules' => 'No smoking, Quiet hours 10pm-7am',
                        'check_in_time' => '15:00',
                        'check_out_time' => '12:00'
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/hostels for actual creation.'
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
     * Store a newly created hostel
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'country' => 'required|string|max:100',
                'postal_code' => 'required|string|max:20',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'website' => 'nullable|url|max:255',
                'amenities' => 'nullable|array',
                'images' => 'nullable|array',
                'status' => ['required', Rule::in(['active', 'inactive', 'maintenance'])],
                'manager_name' => 'required|string|max:255',
                'manager_phone' => 'required|string|max:20',
                'manager_email' => 'required|email|max:255',
                'rules' => 'nullable|string',
                'check_in_time' => 'nullable|date_format:H:i',
                'check_out_time' => 'nullable|date_format:H:i',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $hostel = Hostel::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Hostel created successfully',
                'data' => $this->transformHostel($hostel)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create hostel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified hostel
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $hostel = Hostel::find($id);

            if (!$hostel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hostel not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Hostel retrieved successfully',
                'data' => $this->transformHostel($hostel, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve hostel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified hostel
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $hostel = Hostel::find($id);

            if (!$hostel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hostel not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'address' => 'sometimes|required|string|max:255',
                'city' => 'sometimes|required|string|max:100',
                'state' => 'sometimes|required|string|max:100',
                'country' => 'sometimes|required|string|max:100',
                'postal_code' => 'sometimes|required|string|max:20',
                'phone' => 'sometimes|required|string|max:20',
                'email' => 'sometimes|required|email|max:255',
                'website' => 'nullable|url|max:255',
                'amenities' => 'nullable|array',
                'images' => 'nullable|array',
                'status' => ['sometimes', 'required', Rule::in(['active', 'inactive', 'maintenance'])],
                'manager_name' => 'sometimes|required|string|max:255',
                'manager_phone' => 'sometimes|required|string|max:20',
                'manager_email' => 'sometimes|required|email|max:255',
                'rules' => 'nullable|string',
                'check_in_time' => 'nullable|date_format:H:i',
                'check_out_time' => 'nullable|date_format:H:i',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $hostel->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Hostel updated successfully',
                'data' => $this->transformHostel($hostel)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update hostel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified hostel
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $hostel = Hostel::find($id);

            if (!$hostel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hostel not found'
                ], 404);
            }

            // Check if hostel has rooms or tenants
            if ($hostel->rooms()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete hostel with existing rooms. Please remove all rooms first.'
                ], 422);
            }

            $hostel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hostel deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete hostel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get hostel statistics
     */
    public function stats(Request $request, $id): JsonResponse
    {
        try {
            $hostel = Hostel::find($id);

            if (!$hostel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hostel not found'
                ], 404);
            }

            $stats = [
                'total_rooms' => $hostel->total_rooms,
                'total_beds' => $hostel->total_beds,
                'available_beds' => $hostel->available_beds_count,
                'occupied_beds' => $hostel->occupied_beds_count,
                'occupancy_rate' => $hostel->occupancy_rate,
                'rent_per_bed' => $hostel->rent_per_bed,
                'formatted_rent' => $hostel->formatted_rent,
                'floors' => $hostel->floors,
                'status' => $hostel->status,
                'created_at' => $hostel->created_at,
                'updated_at' => $hostel->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Hostel statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve hostel statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search hostels
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
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
            $limit = $request->get('limit', 10);

            $hostels = Hostel::where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('address', 'like', '%' . $query . '%')
                  ->orWhere('city', 'like', '%' . $query . '%')
                  ->orWhere('state', 'like', '%' . $query . '%')
                  ->orWhere('manager_name', 'like', '%' . $query . '%');
            })
            ->limit($limit)
            ->get()
            ->map(function ($hostel) {
                return $this->transformHostel($hostel);
            });

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $hostels,
                'query' => $query,
                'count' => $hostels->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search hostels',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform hostel data for API response
     */
    private function transformHostel(Hostel $hostel, bool $detailed = false): array
    {
        $data = [
            'id' => $hostel->id,
            'name' => $hostel->name,
            'description' => $hostel->description,
            'address' => $hostel->address,
            'city' => $hostel->city,
            'state' => $hostel->state,
            'country' => $hostel->country,
            'postal_code' => $hostel->postal_code,
            'full_address' => $hostel->full_address,
            'phone' => $hostel->phone,
            'email' => $hostel->email,
            'website' => $hostel->website,
            'status' => $hostel->status,
            'manager_name' => $hostel->manager_name,
            'manager_phone' => $hostel->manager_phone,
            'manager_email' => $hostel->manager_email,
            'check_in_time' => $hostel->check_in_time ? $hostel->check_in_time->format('H:i') : null,
            'check_out_time' => $hostel->check_out_time ? $hostel->check_out_time->format('H:i') : null,
            'created_at' => $hostel->created_at,
            'updated_at' => $hostel->updated_at,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'amenities' => $hostel->amenities,
                'images' => $hostel->images,
                'rules' => $hostel->rules,
                'total_rooms' => $hostel->total_rooms,
                'total_beds' => $hostel->total_beds,
                'available_beds' => $hostel->available_beds_count,
                'occupied_beds' => $hostel->occupied_beds_count,
                'occupancy_rate' => $hostel->occupancy_rate,
                'rent_per_bed' => $hostel->rent_per_bed,
                'formatted_rent' => $hostel->formatted_rent,
                'floors' => $hostel->floors,
            ]);
        }

        return $data;
    }
}
