<?php

namespace App\Http\Controllers\Api\V1\Amenities;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\PaidAmenity;
use App\Models\TenantAmenity;
use App\Models\TenantAmenityUsage;
use App\Models\TenantProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AmenityController extends Controller
{
    // ==================== BASIC AMENITIES API ====================

    /**
     * Display a listing of basic amenities
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Amenity::query();

            // Apply filters
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $amenities = $query->paginate($perPage);

            // Transform data
            $amenities->getCollection()->transform(function ($amenity) {
                return $this->transformBasicAmenity($amenity);
            });

            return response()->json([
                'success' => true,
                'message' => 'Amenities retrieved successfully',
                'data' => $amenities->items(),
                'pagination' => [
                    'current_page' => $amenities->currentPage(),
                    'last_page' => $amenities->lastPage(),
                    'per_page' => $amenities->perPage(),
                    'total' => $amenities->total(),
                    'from' => $amenities->firstItem(),
                    'to' => $amenities->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve amenities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new basic amenity (GET version for testing)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Basic amenity creation form data',
                'data' => [
                    'required_fields' => [
                        'name' => 'Amenity name (required)'
                    ],
                    'optional_fields' => [
                        'icon' => 'FontAwesome icon class',
                        'description' => 'Amenity description',
                        'is_active' => 'Active status (boolean)',
                        'sort_order' => 'Sort order (integer)'
                    ],
                    'example_request' => [
                        'name' => 'WiFi',
                        'icon' => 'fas fa-wifi',
                        'description' => 'High-speed internet access',
                        'is_active' => true,
                        'sort_order' => 1
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/amenities for actual creation.'
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
     * Store a newly created basic amenity
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:amenities,name',
                'icon' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $amenity = Amenity::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Amenity created successfully',
                'data' => $this->transformBasicAmenity($amenity)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create amenity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified basic amenity
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $amenity = Amenity::find($id);

            if (!$amenity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amenity not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Amenity retrieved successfully',
                'data' => $this->transformBasicAmenity($amenity, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve amenity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified basic amenity
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $amenity = Amenity::find($id);

            if (!$amenity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amenity not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255|unique:amenities,name,' . $id,
                'icon' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $amenity->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Amenity updated successfully',
                'data' => $this->transformBasicAmenity($amenity)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update amenity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified basic amenity
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $amenity = Amenity::find($id);

            if (!$amenity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amenity not found'
                ], 404);
            }

            $amenity->delete();

            return response()->json([
                'success' => true,
                'message' => 'Amenity deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete amenity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== PAID AMENITIES API ====================

    /**
     * Display a listing of paid amenities
     */
    public function indexPaid(Request $request): JsonResponse
    {
        try {
            $query = PaidAmenity::with(['tenantAmenities.tenantProfile.user']);

            // Apply filters
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            if ($request->has('billing_type')) {
                $query->where('billing_type', $request->billing_type);
            }

            if ($request->has('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }

            if ($request->has('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $amenities = $query->paginate($perPage);

            // Transform data
            $amenities->getCollection()->transform(function ($amenity) {
                return $this->transformPaidAmenity($amenity);
            });

            return response()->json([
                'success' => true,
                'message' => 'Paid amenities retrieved successfully',
                'data' => $amenities->items(),
                'pagination' => [
                    'current_page' => $amenities->currentPage(),
                    'last_page' => $amenities->lastPage(),
                    'per_page' => $amenities->perPage(),
                    'total' => $amenities->total(),
                    'from' => $amenities->firstItem(),
                    'to' => $amenities->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve paid amenities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new paid amenity (GET version for testing)
     */
    public function createPaid(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Paid amenity creation form data',
                'data' => [
                    'required_fields' => [
                        'name' => 'Amenity name (required)',
                        'billing_type' => 'Billing type: monthly, daily (required)',
                        'price' => 'Price amount (required)',
                        'category' => 'Category: food, cleaning, laundry, utilities, services, other (required)'
                    ],
                    'optional_fields' => [
                        'description' => 'Amenity description',
                        'is_active' => 'Active status (boolean)',
                        'availability_schedule' => 'Availability schedule (JSON)',
                        'max_usage_per_day' => 'Maximum usage per day (integer)',
                        'terms_conditions' => 'Terms and conditions',
                        'icon' => 'FontAwesome icon class'
                    ],
                    'example_request' => [
                        'name' => 'Laundry Service',
                        'description' => 'Professional laundry service',
                        'billing_type' => 'daily',
                        'price' => 50.00,
                        'category' => 'laundry',
                        'is_active' => true,
                        'availability_schedule' => [
                            'days' => [1, 2, 3, 4, 5],
                            'hours' => ['09:00', '18:00']
                        ],
                        'max_usage_per_day' => 2,
                        'terms_conditions' => 'Service available Monday to Friday',
                        'icon' => 'fas fa-tshirt'
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/amenities/paid for actual creation.'
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
     * Store a newly created paid amenity
     */
    public function storePaid(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'billing_type' => ['required', Rule::in(['monthly', 'daily'])],
                'price' => 'required|numeric|min:0',
                'category' => ['required', Rule::in(['food', 'cleaning', 'laundry', 'utilities', 'services', 'other'])],
                'is_active' => 'nullable|boolean',
                'availability_schedule' => 'nullable|array',
                'max_usage_per_day' => 'nullable|integer|min:1',
                'terms_conditions' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $amenity = PaidAmenity::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Paid amenity created successfully',
                'data' => $this->transformPaidAmenity($amenity)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create paid amenity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified paid amenity
     */
    public function showPaid(Request $request, $id): JsonResponse
    {
        try {
            $amenity = PaidAmenity::with(['tenantAmenities.tenantProfile.user'])->find($id);

            if (!$amenity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paid amenity not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paid amenity retrieved successfully',
                'data' => $this->transformPaidAmenity($amenity, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve paid amenity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified paid amenity
     */
    public function updatePaid(Request $request, $id): JsonResponse
    {
        try {
            $amenity = PaidAmenity::find($id);

            if (!$amenity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paid amenity not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'billing_type' => ['sometimes', 'required', Rule::in(['monthly', 'daily'])],
                'price' => 'sometimes|required|numeric|min:0',
                'category' => ['sometimes', 'required', Rule::in(['food', 'cleaning', 'laundry', 'utilities', 'services', 'other'])],
                'is_active' => 'nullable|boolean',
                'availability_schedule' => 'nullable|array',
                'max_usage_per_day' => 'nullable|integer|min:1',
                'terms_conditions' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $amenity->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Paid amenity updated successfully',
                'data' => $this->transformPaidAmenity($amenity)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update paid amenity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified paid amenity
     */
    public function destroyPaid(Request $request, $id): JsonResponse
    {
        try {
            $amenity = PaidAmenity::find($id);

            if (!$amenity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paid amenity not found'
                ], 404);
            }

            // Check if amenity has active tenant subscriptions
            if ($amenity->activeTenantAmenities()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete amenity with active tenant subscriptions. Please terminate all subscriptions first.'
                ], 422);
            }

            $amenity->delete();

            return response()->json([
                'success' => true,
                'message' => 'Paid amenity deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete paid amenity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== TENANT AMENITY SUBSCRIPTIONS API ====================

    /**
     * Display a listing of tenant amenity subscriptions
     */
    public function indexSubscriptions(Request $request): JsonResponse
    {
        try {
            $query = TenantAmenity::with(['tenantProfile.user', 'paidAmenity', 'usageRecords']);

            // Apply filters
            if ($request->has('tenant_profile_id')) {
                $query->where('tenant_profile_id', $request->tenant_profile_id);
            }

            if ($request->has('paid_amenity_id')) {
                $query->where('paid_amenity_id', $request->paid_amenity_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('billing_type')) {
                $query->whereHas('paidAmenity', function ($q) use ($request) {
                    $q->where('billing_type', $request->billing_type);
                });
            }

            if ($request->has('is_current')) {
                if ($request->boolean('is_current')) {
                    $query->current();
                } else {
                    $query->expired();
                }
            }

            if ($request->has('start_date_from')) {
                $query->where('start_date', '>=', $request->start_date_from);
            }

            if ($request->has('start_date_to')) {
                $query->where('start_date', '<=', $request->start_date_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('tenantProfile.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('paidAmenity', function ($amenityQuery) use ($search) {
                        $amenityQuery->where('name', 'like', "%{$search}%");
                    });
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $subscriptions = $query->paginate($perPage);

            // Transform data
            $subscriptions->getCollection()->transform(function ($subscription) {
                return $this->transformTenantAmenity($subscription);
            });

            return response()->json([
                'success' => true,
                'message' => 'Tenant amenity subscriptions retrieved successfully',
                'data' => $subscriptions->items(),
                'pagination' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'per_page' => $subscriptions->perPage(),
                    'total' => $subscriptions->total(),
                    'from' => $subscriptions->firstItem(),
                    'to' => $subscriptions->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant amenity subscriptions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Subscribe a tenant to a paid amenity
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'tenant_profile_id' => 'required|exists:tenant_profiles,id',
                'paid_amenity_id' => 'required|exists:paid_amenities,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'custom_price' => 'nullable|numeric|min:0',
                'custom_schedule' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Check if tenant already has an active subscription to this amenity
            $existingSubscription = TenantAmenity::where('tenant_profile_id', $validated['tenant_profile_id'])
                ->where('paid_amenity_id', $validated['paid_amenity_id'])
                ->where('status', 'active')
                ->current()
                ->first();

            if ($existingSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant already has an active subscription to this amenity'
                ], 422);
            }

            $validated['status'] = 'active';
            $subscription = TenantAmenity::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tenant subscribed to amenity successfully',
                'data' => $this->transformTenantAmenity($subscription->load(['tenantProfile.user', 'paidAmenity']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe tenant to amenity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update tenant amenity subscription
     */
    public function updateSubscription(Request $request, $id): JsonResponse
    {
        try {
            $subscription = TenantAmenity::find($id);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant amenity subscription not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'status' => ['sometimes', 'required', Rule::in(['active', 'inactive', 'suspended'])],
                'start_date' => 'sometimes|required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'custom_price' => 'nullable|numeric|min:0',
                'custom_schedule' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $subscription->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Tenant amenity subscription updated successfully',
                'data' => $this->transformTenantAmenity($subscription->load(['tenantProfile.user', 'paidAmenity']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant amenity subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suspend tenant amenity subscription
     */
    public function suspendSubscription(Request $request, $id): JsonResponse
    {
        try {
            $subscription = TenantAmenity::find($id);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant amenity subscription not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $subscription->suspend($request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Tenant amenity subscription suspended successfully',
                'data' => $this->transformTenantAmenity($subscription->load(['tenantProfile.user', 'paidAmenity']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend tenant amenity subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reactivate tenant amenity subscription
     */
    public function reactivateSubscription(Request $request, $id): JsonResponse
    {
        try {
            $subscription = TenantAmenity::find($id);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant amenity subscription not found'
                ], 404);
            }

            $subscription->reactivate();

            return response()->json([
                'success' => true,
                'message' => 'Tenant amenity subscription reactivated successfully',
                'data' => $this->transformTenantAmenity($subscription->load(['tenantProfile.user', 'paidAmenity']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reactivate tenant amenity subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Terminate tenant amenity subscription
     */
    public function terminateSubscription(Request $request, $id): JsonResponse
    {
        try {
            $subscription = TenantAmenity::find($id);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant amenity subscription not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'end_date' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $subscription->terminate($request->end_date);

            return response()->json([
                'success' => true,
                'message' => 'Tenant amenity subscription terminated successfully',
                'data' => $this->transformTenantAmenity($subscription->load(['tenantProfile.user', 'paidAmenity']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to terminate tenant amenity subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== AMENITY USAGE API ====================

    /**
     * Record amenity usage
     */
    public function recordUsage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'tenant_amenity_id' => 'required|exists:tenant_amenities,id',
                'usage_date' => 'required|date',
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            $subscription = TenantAmenity::find($validated['tenant_amenity_id']);

            // Check if subscription is active
            if ($subscription->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot record usage for inactive subscription'
                ], 422);
            }

            // Check if subscription is current
            if (!$subscription->is_current) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot record usage for expired subscription'
                ], 422);
            }

            $usage = $subscription->recordUsage(
                $validated['usage_date'],
                $validated['quantity'],
                $validated['notes'] ?? null,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Amenity usage recorded successfully',
                'data' => $this->transformUsageRecord($usage->load(['tenantAmenity.paidAmenity', 'recordedBy']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record amenity usage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get amenity usage records
     */
    public function getUsageRecords(Request $request): JsonResponse
    {
        try {
            $query = TenantAmenityUsage::with(['tenantAmenity.paidAmenity', 'tenantAmenity.tenantProfile.user', 'recordedBy']);

            // Apply filters
            if ($request->has('tenant_profile_id')) {
                $query->forTenant($request->tenant_profile_id);
            }

            if ($request->has('paid_amenity_id')) {
                $query->forAmenity($request->paid_amenity_id);
            }

            if ($request->has('tenant_amenity_id')) {
                $query->where('tenant_amenity_id', $request->tenant_amenity_id);
            }

            if ($request->has('usage_date_from')) {
                $query->where('usage_date', '>=', $request->usage_date_from);
            }

            if ($request->has('usage_date_to')) {
                $query->where('usage_date', '<=', $request->usage_date_to);
            }

            if ($request->has('month')) {
                $year = $request->get('year', date('Y'));
                $query->forMonth($year, $request->month);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'usage_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $usageRecords = $query->paginate($perPage);

            // Transform data
            $usageRecords->getCollection()->transform(function ($record) {
                return $this->transformUsageRecord($record);
            });

            return response()->json([
                'success' => true,
                'message' => 'Amenity usage records retrieved successfully',
                'data' => $usageRecords->items(),
                'pagination' => [
                    'current_page' => $usageRecords->currentPage(),
                    'last_page' => $usageRecords->lastPage(),
                    'per_page' => $usageRecords->perPage(),
                    'total' => $usageRecords->total(),
                    'from' => $usageRecords->firstItem(),
                    'to' => $usageRecords->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve amenity usage records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get amenity usage summary for a tenant
     */
    public function getTenantUsageSummary(Request $request, $tenantId): JsonResponse
    {
        try {
            $tenantProfile = TenantProfile::find($tenantId);

            if (!$tenantProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));

            $subscriptions = TenantAmenity::with(['paidAmenity', 'usageRecords'])
                ->where('tenant_profile_id', $tenantId)
                ->where('status', 'active')
                ->get();

            $summary = [
                'tenant_info' => [
                    'id' => $tenantProfile->id,
                    'name' => $tenantProfile->user->name,
                    'email' => $tenantProfile->user->email,
                ],
                'period' => [
                    'month' => $month,
                    'year' => $year,
                    'month_name' => Carbon::create($year, $month, 1)->format('F Y'),
                ],
                'subscriptions' => $subscriptions->map(function ($subscription) use ($year, $month) {
                    return $subscription->getMonthlyBillingSummary($year, $month);
                }),
                'total_amount' => $subscriptions->sum(function ($subscription) use ($year, $month) {
                    return $subscription->calculateMonthlyCharge($year, $month);
                }),
                'formatted_total_amount' => '₹' . number_format($subscriptions->sum(function ($subscription) use ($year, $month) {
                    return $subscription->calculateMonthlyCharge($year, $month);
                }), 2),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Tenant amenity usage summary retrieved successfully',
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant amenity usage summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== SEARCH API ====================

    /**
     * Search amenities
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'type' => ['nullable', Rule::in(['basic', 'paid', 'subscriptions'])],
                'category' => ['nullable', Rule::in(['food', 'cleaning', 'laundry', 'utilities', 'services', 'other'])],
                'billing_type' => ['nullable', Rule::in(['monthly', 'daily'])],
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
            $type = $request->type;
            $category = $request->category;
            $billingType = $request->billing_type;
            $limit = $request->get('limit', 10);

            $results = [];

            // Search basic amenities
            if (!$type || $type === 'basic') {
                $basicAmenities = Amenity::where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->limit($limit)
                    ->get()
                    ->map(function ($amenity) {
                        return [
                            'type' => 'basic',
                            'id' => $amenity->id,
                            'name' => $amenity->name,
                            'description' => $amenity->description,
                            'icon' => $amenity->icon,
                            'is_active' => $amenity->is_active,
                        ];
                    });

                $results = array_merge($results, $basicAmenities->toArray());
            }

            // Search paid amenities
            if (!$type || $type === 'paid') {
                $paidAmenitiesQuery = PaidAmenity::where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");

                if ($category) {
                    $paidAmenitiesQuery->where('category', $category);
                }

                if ($billingType) {
                    $paidAmenitiesQuery->where('billing_type', $billingType);
                }

                $paidAmenities = $paidAmenitiesQuery->limit($limit)->get()->map(function ($amenity) {
                    return [
                        'type' => 'paid',
                        'id' => $amenity->id,
                        'name' => $amenity->name,
                        'description' => $amenity->description,
                        'category' => $amenity->category,
                        'category_display' => $amenity->category_display,
                        'billing_type' => $amenity->billing_type,
                        'billing_type_display' => $amenity->billing_type_display,
                        'price' => $amenity->price,
                        'formatted_price' => $amenity->formatted_price,
                        'is_active' => $amenity->is_active,
                        'active_tenant_count' => $amenity->active_tenant_count,
                    ];
                });

                $results = array_merge($results, $paidAmenities->toArray());
            }

            // Search subscriptions
            if (!$type || $type === 'subscriptions') {
                $subscriptionsQuery = TenantAmenity::with(['tenantProfile.user', 'paidAmenity'])
                    ->whereHas('tenantProfile.user', function ($userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%")
                                 ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->orWhereHas('paidAmenity', function ($amenityQuery) use ($query) {
                        $amenityQuery->where('name', 'like', "%{$query}%");
                    });

                $subscriptions = $subscriptionsQuery->limit($limit)->get()->map(function ($subscription) {
                    return [
                        'type' => 'subscription',
                        'id' => $subscription->id,
                        'tenant_name' => $subscription->tenantProfile->user->name,
                        'tenant_email' => $subscription->tenantProfile->user->email,
                        'amenity_name' => $subscription->paidAmenity->name,
                        'status' => $subscription->status,
                        'status_badge' => $subscription->status_badge,
                        'start_date' => $subscription->start_date,
                        'end_date' => $subscription->end_date,
                        'effective_price' => $subscription->effective_price,
                        'formatted_effective_price' => $subscription->formatted_effective_price,
                        'is_current' => $subscription->is_current,
                    ];
                });

                $results = array_merge($results, $subscriptions->toArray());
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
                'message' => 'Failed to search amenities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Transform basic amenity data for API response
     */
    private function transformBasicAmenity(Amenity $amenity, bool $detailed = false): array
    {
        $data = [
            'id' => $amenity->id,
            'name' => $amenity->name,
            'icon' => $amenity->icon,
            'description' => $amenity->description,
            'is_active' => $amenity->is_active,
            'sort_order' => $amenity->sort_order,
            'created_at' => $amenity->created_at,
            'updated_at' => $amenity->updated_at,
        ];

        if ($detailed) {
            $data['hostels_count'] = $amenity->hostels()->count();
        }

        return $data;
    }

    /**
     * Transform paid amenity data for API response
     */
    private function transformPaidAmenity(PaidAmenity $amenity, bool $detailed = false): array
    {
        $data = [
            'id' => $amenity->id,
            'name' => $amenity->name,
            'description' => $amenity->description,
            'billing_type' => $amenity->billing_type,
            'billing_type_display' => $amenity->billing_type_display,
            'price' => $amenity->price,
            'formatted_price' => $amenity->formatted_price,
            'category' => $amenity->category,
            'category_display' => $amenity->category_display,
            'is_active' => $amenity->is_active,
            'status_badge' => $amenity->status_badge,
            'availability_schedule' => $amenity->availability_schedule,
            'max_usage_per_day' => $amenity->max_usage_per_day,
            'terms_conditions' => $amenity->terms_conditions,
            'icon' => $amenity->icon,
            'icon_class' => $amenity->icon_class,
            'active_tenant_count' => $amenity->active_tenant_count,
            'created_at' => $amenity->created_at,
            'updated_at' => $amenity->updated_at,
        ];

        if ($detailed) {
            $data['tenant_subscriptions'] = $amenity->tenantAmenities->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'tenant_name' => $subscription->tenantProfile->user->name,
                    'tenant_email' => $subscription->tenantProfile->user->email,
                    'status' => $subscription->status,
                    'start_date' => $subscription->start_date,
                    'end_date' => $subscription->end_date,
                    'effective_price' => $subscription->effective_price,
                ];
            });
        }

        return $data;
    }

    /**
     * Transform tenant amenity subscription data for API response
     */
    private function transformTenantAmenity(TenantAmenity $subscription, bool $detailed = false): array
    {
        $data = [
            'id' => $subscription->id,
            'tenant_profile_id' => $subscription->tenant_profile_id,
            'paid_amenity_id' => $subscription->paid_amenity_id,
            'status' => $subscription->status,
            'status_badge' => $subscription->status_badge,
            'start_date' => $subscription->start_date,
            'end_date' => $subscription->end_date,
            'custom_price' => $subscription->custom_price,
            'custom_schedule' => $subscription->custom_schedule,
            'notes' => $subscription->notes,
            'effective_price' => $subscription->effective_price,
            'formatted_effective_price' => $subscription->formatted_effective_price,
            'is_current' => $subscription->is_current,
            'is_expired' => $subscription->is_expired,
            'duration_days' => $subscription->duration_days,
            'duration_text' => $subscription->duration_text,
            'created_at' => $subscription->created_at,
            'updated_at' => $subscription->updated_at,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'tenant' => [
                    'id' => $subscription->tenantProfile->user->id,
                    'name' => $subscription->tenantProfile->user->name,
                    'email' => $subscription->tenantProfile->user->email,
                    'phone' => $subscription->tenantProfile->phone,
                ],
                'paid_amenity' => [
                    'id' => $subscription->paidAmenity->id,
                    'name' => $subscription->paidAmenity->name,
                    'description' => $subscription->paidAmenity->description,
                    'billing_type' => $subscription->paidAmenity->billing_type,
                    'category' => $subscription->paidAmenity->category,
                    'price' => $subscription->paidAmenity->price,
                    'formatted_price' => $subscription->paidAmenity->formatted_price,
                ],
                'usage_records' => $subscription->usageRecords->map(function ($record) {
                    return $this->transformUsageRecord($record);
                }),
            ]);
        }

        return $data;
    }

    /**
     * Transform usage record data for API response
     */
    private function transformUsageRecord(TenantAmenityUsage $record): array
    {
        return [
            'id' => $record->id,
            'tenant_amenity_id' => $record->tenant_amenity_id,
            'usage_date' => $record->usage_date,
            'quantity' => $record->quantity,
            'unit_price' => $record->unit_price,
            'formatted_unit_price' => $record->formatted_unit_price,
            'total_amount' => $record->total_amount,
            'formatted_total_amount' => $record->formatted_total_amount,
            'notes' => $record->notes,
            'usage_summary' => $record->usage_summary,
            'recorded_by' => $record->recordedBy ? [
                'id' => $record->recordedBy->id,
                'name' => $record->recordedBy->name,
            ] : null,
            'created_at' => $record->created_at,
            'updated_at' => $record->updated_at,
        ];
    }
}
