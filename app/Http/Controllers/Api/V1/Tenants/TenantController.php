<?php

namespace App\Http\Controllers\Api\V1\Tenants;

use App\Http\Controllers\Controller;
use App\Models\TenantProfile;
use App\Models\User;
use App\Models\Bed;
use App\Models\BedAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = TenantProfile::with(['user', 'currentBed.room.hostel', 'verifiedBy']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('verification_status')) {
                if ($request->verification_status === 'verified') {
                    $query->verified();
                } elseif ($request->verification_status === 'unverified') {
                    $query->unverified();
                }
            }

            if ($request->has('hostel_id')) {
                $query->whereHas('currentBed.room', function ($q) use ($request) {
                    $q->where('hostel_id', $request->hostel_id);
                });
            }

            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_proof_number', 'like', "%{$search}%");
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Handle sorting by user fields
            if (in_array($sortBy, ['name', 'email'])) {
                $query->join('users', 'tenant_profiles.user_id', '=', 'users.id')
                      ->orderBy("users.{$sortBy}", $sortOrder)
                      ->select('tenant_profiles.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $tenants = $query->paginate($perPage);

            // Transform data
            $tenants->getCollection()->transform(function ($tenant) {
                return $this->transformTenant($tenant);
            });

            return response()->json([
                'success' => true,
                'message' => 'Tenants retrieved successfully',
                'data' => $tenants->items(),
                'pagination' => [
                    'current_page' => $tenants->currentPage(),
                    'last_page' => $tenants->lastPage(),
                    'per_page' => $tenants->perPage(),
                    'total' => $tenants->total(),
                    'from' => $tenants->firstItem(),
                    'to' => $tenants->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenants',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new tenant (GET version for testing)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Tenant creation form data',
                'data' => [
                    'required_fields' => [
                        'name' => 'Tenant full name (required)',
                        'email' => 'Email address (required)',
                        'password' => 'Password (required, min 6 characters)',
                        'phone' => 'Phone number (required)',
                        'status' => 'Status: active, inactive, pending, suspended, moved_out (required)'
                    ],
                    'optional_fields' => [
                        'date_of_birth' => 'Date of birth (YYYY-MM-DD format)',
                        'address' => 'Current address',
                        'occupation' => 'Job title/occupation',
                        'company' => 'Company name',
                        'id_proof_type' => 'ID type: aadhar, passport, driving_license, voter_id, pan_card, other',
                        'id_proof_number' => 'ID proof number',
                        'emergency_contact_name' => 'Emergency contact name',
                        'emergency_contact_phone' => 'Emergency contact phone',
                        'emergency_contact_relation' => 'Relationship to emergency contact',
                        'move_in_date' => 'Move-in date (YYYY-MM-DD format)',
                        'move_out_date' => 'Move-out date (YYYY-MM-DD format)',
                        'security_deposit' => 'Security deposit amount',
                        'monthly_rent' => 'Monthly rent amount',
                        'lease_start_date' => 'Lease start date (YYYY-MM-DD format)',
                        'lease_end_date' => 'Lease end date (YYYY-MM-DD format)',
                        'notes' => 'Additional notes',
                        'documents' => 'Array of document file paths',
                        'billing_cycle' => 'Billing cycle: monthly, quarterly, half_yearly, yearly',
                        'billing_day' => 'Day of month for billing (1-31)',
                        'auto_billing_enabled' => 'Enable automatic billing (boolean)',
                        'notification_preferences' => 'Array of notification preferences',
                        'reminder_days_before' => 'Days before billing to send reminder',
                        'overdue_grace_days' => 'Grace period for overdue payments',
                        'late_fee_amount' => 'Fixed late fee amount',
                        'late_fee_percentage' => 'Late fee percentage',
                        'compound_late_fees' => 'Enable compound late fees (boolean)',
                        'auto_payment_enabled' => 'Enable automatic payments (boolean)',
                        'payment_method' => 'Preferred payment method',
                        'payment_details' => 'Payment method details (array)'
                    ],
                    'example_request' => [
                        'name' => 'John Doe',
                        'email' => 'john.doe@example.com',
                        'password' => 'password123',
                        'phone' => '+1-555-0123',
                        'date_of_birth' => '1990-05-15',
                        'address' => '123 Main Street, City, State',
                        'occupation' => 'Software Engineer',
                        'company' => 'Tech Corp',
                        'id_proof_type' => 'passport',
                        'id_proof_number' => 'P123456789',
                        'emergency_contact_name' => 'Jane Doe',
                        'emergency_contact_phone' => '+1-555-0124',
                        'emergency_contact_relation' => 'Sister',
                        'status' => 'pending',
                        'move_in_date' => '2024-01-01',
                        'security_deposit' => 500.00,
                        'monthly_rent' => 800.00,
                        'lease_start_date' => '2024-01-01',
                        'lease_end_date' => '2024-12-31',
                        'notes' => 'Prefers ground floor room',
                        'billing_cycle' => 'monthly',
                        'billing_day' => 1,
                        'auto_billing_enabled' => true,
                        'reminder_days_before' => 3,
                        'overdue_grace_days' => 5,
                        'late_fee_amount' => 25.00,
                        'auto_payment_enabled' => false
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/tenants for actual creation.'
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
     * Store a newly created tenant
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'phone' => 'required|string|max:20',
                'date_of_birth' => 'nullable|date|before:today',
                'address' => 'nullable|string',
                'occupation' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'id_proof_type' => ['nullable', Rule::in(['aadhar', 'passport', 'driving_license', 'voter_id', 'pan_card', 'other'])],
                'id_proof_number' => 'nullable|string|max:255',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:20',
                'emergency_contact_relation' => 'nullable|string|max:100',
                'status' => ['required', Rule::in(['active', 'inactive', 'pending', 'suspended', 'moved_out'])],
                'move_in_date' => 'nullable|date',
                'move_out_date' => 'nullable|date|after:move_in_date',
                'security_deposit' => 'nullable|numeric|min:0',
                'monthly_rent' => 'nullable|numeric|min:0',
                'lease_start_date' => 'nullable|date',
                'lease_end_date' => 'nullable|date|after:lease_start_date',
                'notes' => 'nullable|string',
                'documents' => 'nullable|array',
                'billing_cycle' => ['nullable', Rule::in(['monthly', 'quarterly', 'half_yearly', 'yearly'])],
                'billing_day' => 'nullable|integer|min:1|max:31',
                'auto_billing_enabled' => 'nullable|boolean',
                'notification_preferences' => 'nullable|array',
                'reminder_days_before' => 'nullable|integer|min:1|max:30',
                'overdue_grace_days' => 'nullable|integer|min:0|max:30',
                'late_fee_amount' => 'nullable|numeric|min:0',
                'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
                'compound_late_fees' => 'nullable|boolean',
                'auto_payment_enabled' => 'nullable|boolean',
                'payment_method' => 'nullable|string|max:100',
                'payment_details' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Create user first
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'is_tenant' => true,
                'status' => 'active',
            ]);

            // Remove user-specific fields from tenant profile data
            $tenantData = collect($validated)->except(['name', 'email', 'password'])->toArray();
            $tenantData['user_id'] = $user->id;

            // Set default values
            $tenantData['is_verified'] = false;
            $tenantData['billing_cycle'] = $tenantData['billing_cycle'] ?? 'monthly';
            $tenantData['billing_day'] = $tenantData['billing_day'] ?? 1;
            $tenantData['auto_billing_enabled'] = $tenantData['auto_billing_enabled'] ?? true;
            $tenantData['reminder_days_before'] = $tenantData['reminder_days_before'] ?? 3;
            $tenantData['overdue_grace_days'] = $tenantData['overdue_grace_days'] ?? 5;

            $tenant = TenantProfile::create($tenantData);

            // Initialize billing cycle if move_in_date is set
            if ($tenant->move_in_date) {
                $tenant->initializeBillingCycle();
            }

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'data' => $this->transformTenant($tenant->load('user'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified tenant
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $tenant = TenantProfile::with(['user', 'currentBed.room.hostel', 'verifiedBy', 'bedAssignments.bed.room.hostel'])
                ->find($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tenant retrieved successfully',
                'data' => $this->transformTenant($tenant, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified tenant
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $tenant = TenantProfile::find($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $tenant->user_id,
                'phone' => 'sometimes|required|string|max:20',
                'date_of_birth' => 'nullable|date|before:today',
                'address' => 'nullable|string',
                'occupation' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'id_proof_type' => ['nullable', Rule::in(['aadhar', 'passport', 'driving_license', 'voter_id', 'pan_card', 'other'])],
                'id_proof_number' => 'nullable|string|max:255',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:20',
                'emergency_contact_relation' => 'nullable|string|max:100',
                'status' => ['sometimes', 'required', Rule::in(['active', 'inactive', 'pending', 'suspended', 'moved_out'])],
                'move_in_date' => 'nullable|date',
                'move_out_date' => 'nullable|date|after:move_in_date',
                'security_deposit' => 'nullable|numeric|min:0',
                'monthly_rent' => 'nullable|numeric|min:0',
                'lease_start_date' => 'nullable|date',
                'lease_end_date' => 'nullable|date|after:lease_start_date',
                'notes' => 'nullable|string',
                'documents' => 'nullable|array',
                'is_verified' => 'nullable|boolean',
                'billing_cycle' => ['nullable', Rule::in(['monthly', 'quarterly', 'half_yearly', 'yearly'])],
                'billing_day' => 'nullable|integer|min:1|max:31',
                'auto_billing_enabled' => 'nullable|boolean',
                'notification_preferences' => 'nullable|array',
                'reminder_days_before' => 'nullable|integer|min:1|max:30',
                'overdue_grace_days' => 'nullable|integer|min:0|max:30',
                'late_fee_amount' => 'nullable|numeric|min:0',
                'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
                'compound_late_fees' => 'nullable|boolean',
                'auto_payment_enabled' => 'nullable|boolean',
                'payment_method' => 'nullable|string|max:100',
                'payment_details' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Update user if name or email changed
            if (isset($validated['name']) || isset($validated['email'])) {
                $userData = [];
                if (isset($validated['name'])) $userData['name'] = $validated['name'];
                if (isset($validated['email'])) $userData['email'] = $validated['email'];
                if (isset($validated['phone'])) $userData['phone'] = $validated['phone'];
                
                $tenant->user->update($userData);
            }

            // Remove user-specific fields from tenant profile data
            $tenantData = collect($validated)->except(['name', 'email', 'phone'])->toArray();

            $tenant->update($tenantData);

            return response()->json([
                'success' => true,
                'message' => 'Tenant updated successfully',
                'data' => $this->transformTenant($tenant->load('user'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified tenant
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $tenant = TenantProfile::find($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            // Check if tenant has active bed assignments
            if ($tenant->bedAssignments()->where('status', 'active')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete tenant with active bed assignments. Please release bed assignments first.'
                ], 422);
            }

            // Delete tenant profile (user will be deleted via cascade)
            $tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tenant deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tenant statistics
     */
    public function stats(Request $request, $id): JsonResponse
    {
        try {
            $tenant = TenantProfile::find($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $stats = [
                'basic_info' => [
                    'age' => $tenant->age,
                    'tenancy_duration' => $tenant->tenancy_duration,
                    'tenancy_duration_human' => $tenant->tenancy_duration_human,
                    'is_lease_expired' => $tenant->is_lease_expired,
                    'days_until_lease_expiry' => $tenant->days_until_lease_expiry,
                ],
                'billing_info' => [
                    'billing_cycle' => $tenant->billing_cycle,
                    'billing_cycle_display' => $tenant->billing_cycle_display,
                    'next_billing_date' => $tenant->next_billing_date,
                    'last_billing_date' => $tenant->last_billing_date,
                    'next_billing_amount' => $tenant->next_billing_amount,
                    'outstanding_amount' => $tenant->outstanding_amount,
                    'total_outstanding' => $tenant->total_outstanding,
                    'payment_status' => $tenant->payment_status,
                    'is_payment_overdue' => $tenant->is_payment_overdue,
                    'days_until_next_billing' => $tenant->days_until_next_billing,
                ],
                'payment_history' => [
                    'consecutive_on_time_payments' => $tenant->consecutive_on_time_payments,
                    'total_late_payments' => $tenant->total_late_payments,
                    'payment_history_score' => $tenant->payment_history_score,
                    'last_payment_date' => $tenant->last_payment_date,
                    'last_payment_amount' => $tenant->last_payment_amount,
                ],
                'current_accommodation' => [
                    'current_bed' => $tenant->currentBed ? [
                        'id' => $tenant->currentBed->id,
                        'bed_number' => $tenant->currentBed->bed_number,
                        'room' => [
                            'id' => $tenant->currentBed->room->id,
                            'room_number' => $tenant->currentBed->room->room_number,
                            'floor' => $tenant->currentBed->room->floor,
                            'hostel' => [
                                'id' => $tenant->currentBed->room->hostel->id,
                                'name' => $tenant->currentBed->room->hostel->name,
                            ]
                        ]
                    ] : null,
                    'current_hostel' => $tenant->current_hostel ? [
                        'id' => $tenant->current_hostel->id,
                        'name' => $tenant->current_hostel->name,
                    ] : null,
                ],
                'amenities' => [
                    'total_amenities' => $tenant->tenantAmenities()->count(),
                    'active_amenities' => $tenant->activeTenantAmenities()->count(),
                ],
                'documents' => [
                    'total_documents' => $tenant->tenantDocuments()->count(),
                    'is_verified' => $tenant->is_verified,
                    'verified_at' => $tenant->verified_at,
                    'verified_by' => $tenant->verifiedBy ? [
                        'id' => $tenant->verifiedBy->id,
                        'name' => $tenant->verifiedBy->name,
                    ] : null,
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Tenant statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search tenants
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

            $tenants = TenantProfile::with(['user', 'currentBed.room.hostel'])
                ->whereHas('user', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('phone', 'like', "%{$query}%");
                })
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('id_proof_number', 'like', "%{$query}%")
                ->limit($limit)
                ->get()
                ->map(function ($tenant) {
                    return $this->transformTenant($tenant);
                });

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $tenants,
                'query' => $query,
                'count' => $tenants->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search tenants',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign tenant to bed
     */
    public function assignBed(Request $request, $id): JsonResponse
    {
        try {
            $tenant = TenantProfile::find($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'bed_id' => 'required|exists:beds,id',
                'move_in_date' => 'nullable|date',
                'rent' => 'nullable|numeric|min:0',
                'lease_end_date' => 'nullable|date|after:move_in_date'
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
            if ($bed->isOccupied()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bed is already occupied'
                ], 422);
            }

            $tenant->assignToBed(
                $request->bed_id,
                $request->move_in_date,
                $request->rent
            );

            if ($request->lease_end_date) {
                $tenant->update(['lease_end_date' => $request->lease_end_date]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tenant assigned to bed successfully',
                'data' => $this->transformTenant($tenant->load('user', 'currentBed.room.hostel'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign tenant to bed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Release tenant from current bed
     */
    public function releaseBed(Request $request, $id): JsonResponse
    {
        try {
            $tenant = TenantProfile::find($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $tenant->releaseCurrentBed();

            return response()->json([
                'success' => true,
                'message' => 'Tenant released from bed successfully',
                'data' => $this->transformTenant($tenant->load('user'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to release tenant from bed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark tenant as verified
     */
    public function verify(Request $request, $id): JsonResponse
    {
        try {
            $tenant = TenantProfile::find($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $verifiedBy = $request->user() ? $request->user()->id : null;
            $tenant->markAsVerified($verifiedBy);

            return response()->json([
                'success' => true,
                'message' => 'Tenant verified successfully',
                'data' => $this->transformTenant($tenant->load('user', 'verifiedBy'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform tenant data for API response
     */
    private function transformTenant(TenantProfile $tenant, bool $detailed = false): array
    {
        $data = [
            'id' => $tenant->id,
            'user_id' => $tenant->user_id,
            'name' => $tenant->user->name,
            'email' => $tenant->user->email,
            'phone' => $tenant->phone,
            'date_of_birth' => $tenant->date_of_birth,
            'age' => $tenant->age,
            'address' => $tenant->address,
            'occupation' => $tenant->occupation,
            'company' => $tenant->company,
            'id_proof_type' => $tenant->id_proof_type,
            'id_proof_type_display' => $tenant->id_proof_type_display,
            'id_proof_number' => $tenant->id_proof_number,
            'emergency_contact_name' => $tenant->emergency_contact_name,
            'emergency_contact_phone' => $tenant->emergency_contact_phone,
            'emergency_contact_relation' => $tenant->emergency_contact_relation,
            'status' => $tenant->status,
            'status_badge' => $tenant->status_badge,
            'move_in_date' => $tenant->move_in_date,
            'move_out_date' => $tenant->move_out_date,
            'tenancy_duration' => $tenant->tenancy_duration,
            'tenancy_duration_human' => $tenant->tenancy_duration_human,
            'security_deposit' => $tenant->security_deposit,
            'monthly_rent' => $tenant->monthly_rent,
            'lease_start_date' => $tenant->lease_start_date,
            'lease_end_date' => $tenant->lease_end_date,
            'is_lease_expired' => $tenant->is_lease_expired,
            'days_until_lease_expiry' => $tenant->days_until_lease_expiry,
            'notes' => $tenant->notes,
            'is_verified' => $tenant->is_verified,
            'verified_at' => $tenant->verified_at,
            'created_at' => $tenant->created_at,
            'updated_at' => $tenant->updated_at,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'documents' => $tenant->documents,
                'current_bed' => $tenant->currentBed ? [
                    'id' => $tenant->currentBed->id,
                    'bed_number' => $tenant->currentBed->bed_number,
                    'room' => [
                        'id' => $tenant->currentBed->room->id,
                        'room_number' => $tenant->currentBed->room->room_number,
                        'floor' => $tenant->currentBed->room->floor,
                        'hostel' => [
                            'id' => $tenant->currentBed->room->hostel->id,
                            'name' => $tenant->currentBed->room->hostel->name,
                        ]
                    ]
                ] : null,
                'current_hostel' => $tenant->current_hostel ? [
                    'id' => $tenant->current_hostel->id,
                    'name' => $tenant->current_hostel->name,
                ] : null,
                'verified_by' => $tenant->verifiedBy ? [
                    'id' => $tenant->verifiedBy->id,
                    'name' => $tenant->verifiedBy->name,
                ] : null,
                'billing_info' => [
                    'billing_cycle' => $tenant->billing_cycle,
                    'billing_cycle_display' => $tenant->billing_cycle_display,
                    'billing_day' => $tenant->billing_day,
                    'next_billing_date' => $tenant->next_billing_date,
                    'last_billing_date' => $tenant->last_billing_date,
                    'next_billing_amount' => $tenant->next_billing_amount,
                    'outstanding_amount' => $tenant->outstanding_amount,
                    'payment_status' => $tenant->payment_status,
                    'payment_status_badge' => $tenant->payment_status_badge,
                    'is_payment_overdue' => $tenant->is_payment_overdue,
                    'auto_billing_enabled' => $tenant->auto_billing_enabled,
                    'auto_payment_enabled' => $tenant->auto_payment_enabled,
                ],
                'payment_history' => [
                    'consecutive_on_time_payments' => $tenant->consecutive_on_time_payments,
                    'total_late_payments' => $tenant->total_late_payments,
                    'payment_history_score' => $tenant->payment_history_score,
                    'last_payment_date' => $tenant->last_payment_date,
                    'last_payment_amount' => $tenant->last_payment_amount,
                ],
                'amenities_count' => [
                    'total' => $tenant->tenantAmenities()->count(),
                    'active' => $tenant->activeTenantAmenities()->count(),
                ],
                'documents_count' => $tenant->tenantDocuments()->count(),
            ]);
        }

        return $data;
    }
}
