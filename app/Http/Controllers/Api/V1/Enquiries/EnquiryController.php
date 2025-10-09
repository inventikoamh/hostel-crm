<?php

namespace App\Http\Controllers\Api\V1\Enquiries;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\User;
use App\Models\TenantProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EnquiryController extends Controller
{
    /**
     * Display a listing of enquiries
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Enquiry::with(['assignedUser']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->has('enquiry_type')) {
                $query->where('enquiry_type', $request->enquiry_type);
            }

            if ($request->has('assigned_to')) {
                if ($request->assigned_to === 'unassigned') {
                    $query->whereNull('assigned_to');
                } else {
                    $query->where('assigned_to', $request->assigned_to);
                }
            }

            if ($request->has('source')) {
                $query->where('source', $request->source);
            }

            if ($request->has('overdue')) {
                if ($request->boolean('overdue')) {
                    $query->where(function ($q) {
                        $q->whereIn('status', ['new', 'in_progress'])
                          ->where('created_at', '<', Carbon::now()->subHours(24));
                    });
                }
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            if ($request->has('responded_from')) {
                $query->where('responded_at', '>=', $request->responded_from);
            }

            if ($request->has('responded_to')) {
                $query->where('responded_at', '<=', $request->responded_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Handle priority sorting
            if ($sortBy === 'priority') {
                $query->orderByRaw("CASE 
                    WHEN priority = 'urgent' THEN 1 
                    WHEN priority = 'high' THEN 2 
                    WHEN priority = 'medium' THEN 3 
                    WHEN priority = 'low' THEN 4 
                    ELSE 5 
                END {$sortOrder}");
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $enquiries = $query->paginate($perPage);

            // Transform data
            $enquiries->getCollection()->transform(function ($enquiry) {
                return $this->transformEnquiry($enquiry);
            });

            return response()->json([
                'success' => true,
                'message' => 'Enquiries retrieved successfully',
                'data' => $enquiries->items(),
                'pagination' => [
                    'current_page' => $enquiries->currentPage(),
                    'last_page' => $enquiries->lastPage(),
                    'per_page' => $enquiries->perPage(),
                    'total' => $enquiries->total(),
                    'from' => $enquiries->firstItem(),
                    'to' => $enquiries->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve enquiries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new enquiry (GET version for testing)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $users = User::where('is_tenant', false)->get(['id', 'name', 'email']);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry creation form data',
                'data' => [
                    'required_fields' => [
                        'name' => 'Enquirer name (required)',
                        'email' => 'Email address (required)',
                        'phone' => 'Phone number (required)',
                        'enquiry_type' => 'Enquiry type: room_booking, general_info, pricing, facilities, other (required)',
                        'subject' => 'Enquiry subject (required)',
                        'message' => 'Enquiry message (required)'
                    ],
                    'optional_fields' => [
                        'status' => 'Status: new, in_progress, resolved, closed (default: new)',
                        'priority' => 'Priority: low, medium, high, urgent (default: medium)',
                        'admin_notes' => 'Admin notes',
                        'assigned_to' => 'Assigned user ID',
                        'source' => 'Enquiry source (website, phone, walk-in, referral, etc.)',
                        'metadata' => 'Additional metadata (JSON)'
                    ],
                    'available_users' => $users,
                    'example_request' => [
                        'name' => 'John Doe',
                        'email' => 'john.doe@example.com',
                        'phone' => '+1-555-0123',
                        'enquiry_type' => 'room_booking',
                        'subject' => 'Room availability inquiry',
                        'message' => 'I am interested in booking a room for next month. Please let me know about availability and pricing.',
                        'priority' => 'medium',
                        'source' => 'website',
                        'metadata' => [
                            'referrer' => 'google',
                            'utm_source' => 'search'
                        ]
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/enquiries for actual creation.'
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
     * Store a newly created enquiry
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'enquiry_type' => ['required', Rule::in(['room_booking', 'general_info', 'pricing', 'facilities', 'other'])],
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'status' => ['nullable', Rule::in(['new', 'in_progress', 'resolved', 'closed'])],
                'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
                'admin_notes' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'source' => 'nullable|string|max:255',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Set defaults
            $validated['status'] = $validated['status'] ?? 'new';
            $validated['priority'] = $validated['priority'] ?? 'medium';

            $enquiry = Enquiry::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry created successfully',
                'data' => $this->transformEnquiry($enquiry->load(['assignedUser']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create enquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified enquiry
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $enquiry = Enquiry::with(['assignedUser'])->find($id);

            if (!$enquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Enquiry retrieved successfully',
                'data' => $this->transformEnquiry($enquiry, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve enquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified enquiry
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $enquiry = Enquiry::find($id);

            if (!$enquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'enquiry_type' => ['sometimes', 'required', Rule::in(['room_booking', 'general_info', 'pricing', 'facilities', 'other'])],
                'subject' => 'sometimes|required|string|max:255',
                'message' => 'sometimes|required|string',
                'status' => ['nullable', Rule::in(['new', 'in_progress', 'resolved', 'closed'])],
                'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
                'admin_notes' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'source' => 'nullable|string|max:255',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Set responded_at if status is being changed to resolved/closed
            if (isset($validated['status']) && in_array($validated['status'], ['resolved', 'closed']) && !$enquiry->responded_at) {
                $validated['responded_at'] = now();
            }

            $enquiry->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry updated successfully',
                'data' => $this->transformEnquiry($enquiry->load(['assignedUser']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update enquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified enquiry
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $enquiry = Enquiry::find($id);

            if (!$enquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry not found'
                ], 404);
            }

            $enquiry->delete();

            return response()->json([
                'success' => true,
                'message' => 'Enquiry deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete enquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign enquiry to a user
     */
    public function assign(Request $request, $id): JsonResponse
    {
        try {
            $enquiry = Enquiry::find($id);

            if (!$enquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'assigned_to' => 'required|exists:users,id',
                'admin_notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Update assignment and status
            $updateData = [
                'assigned_to' => $validated['assigned_to'],
                'status' => 'in_progress'
            ];

            if (isset($validated['admin_notes'])) {
                $updateData['admin_notes'] = $validated['admin_notes'];
            }

            $enquiry->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry assigned successfully',
                'data' => $this->transformEnquiry($enquiry->load(['assignedUser']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign enquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark enquiry as resolved
     */
    public function resolve(Request $request, $id): JsonResponse
    {
        try {
            $enquiry = Enquiry::find($id);

            if (!$enquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'admin_notes' => 'nullable|string',
                'resolution_notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            $updateData = [
                'status' => 'resolved',
                'responded_at' => now()
            ];

            if (isset($validated['admin_notes'])) {
                $updateData['admin_notes'] = $validated['admin_notes'];
            }

            if (isset($validated['resolution_notes'])) {
                $updateData['admin_notes'] = ($enquiry->admin_notes ? $enquiry->admin_notes . "\n" : '') . "Resolution: " . $validated['resolution_notes'];
            }

            $enquiry->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry resolved successfully',
                'data' => $this->transformEnquiry($enquiry->load(['assignedUser']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve enquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close enquiry
     */
    public function close(Request $request, $id): JsonResponse
    {
        try {
            $enquiry = Enquiry::find($id);

            if (!$enquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'admin_notes' => 'nullable|string',
                'closure_reason' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            $updateData = [
                'status' => 'closed',
                'responded_at' => now()
            ];

            if (isset($validated['admin_notes'])) {
                $updateData['admin_notes'] = $validated['admin_notes'];
            }

            if (isset($validated['closure_reason'])) {
                $updateData['admin_notes'] = ($enquiry->admin_notes ? $enquiry->admin_notes . "\n" : '') . "Closure Reason: " . $validated['closure_reason'];
            }

            $enquiry->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry closed successfully',
                'data' => $this->transformEnquiry($enquiry->load(['assignedUser']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close enquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert enquiry to tenant
     */
    public function convertToTenant(Request $request, $id): JsonResponse
    {
        try {
            $enquiry = Enquiry::find($id);

            if (!$enquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:6',
                'date_of_birth' => 'nullable|date',
                'address' => 'nullable|string',
                'occupation' => 'nullable|string',
                'company' => 'nullable|string',
                'id_proof_type' => ['nullable', Rule::in(['aadhar', 'passport', 'driving_license', 'voter_id', 'pan_card', 'other'])],
                'id_proof_number' => 'nullable|string',
                'emergency_contact_name' => 'nullable|string',
                'emergency_contact_phone' => 'nullable|string',
                'emergency_contact_relation' => 'nullable|string',
                'move_in_date' => 'nullable|date',
                'security_deposit' => 'nullable|numeric|min:0',
                'monthly_rent' => 'nullable|numeric|min:0',
                'lease_start_date' => 'nullable|date',
                'lease_end_date' => 'nullable|date',
                'notes' => 'nullable|string',
                'billing_cycle' => ['nullable', Rule::in(['monthly', 'quarterly', 'half_yearly', 'yearly'])],
                'billing_day' => 'nullable|integer|min:1|max:31',
                'auto_billing_enabled' => 'nullable|boolean',
                'reminder_days_before' => 'nullable|integer|min:1|max:30',
                'overdue_grace_days' => 'nullable|integer|min:1|max:30',
                'late_fee_amount' => 'nullable|numeric|min:0',
                'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
                'auto_payment_enabled' => 'nullable|boolean',
                'payment_method' => ['nullable', Rule::in(['cash', 'bank_transfer', 'upi', 'card', 'cheque', 'other'])],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Check if user already exists with this email
            $existingUser = User::where('email', $enquiry->email)->first();
            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'A user with this email already exists'
                ], 422);
            }

            // Create user
            $user = User::create([
                'name' => $enquiry->name,
                'email' => $enquiry->email,
                'phone' => $enquiry->phone,
                'password' => bcrypt($validated['password']),
                'is_tenant' => true,
                'status' => 'active'
            ]);

            // Create tenant profile
            $tenantData = [
                'user_id' => $user->id,
                'first_name' => explode(' ', $enquiry->name)[0],
                'last_name' => implode(' ', array_slice(explode(' ', $enquiry->name), 1)),
                'phone' => $enquiry->phone,
                'status' => 'pending',
                'notes' => "Converted from enquiry #{$enquiry->id}: " . ($validated['notes'] ?? ''),
            ];

            // Add optional fields
            $optionalFields = [
                'date_of_birth', 'address', 'occupation', 'company', 'id_proof_type',
                'id_proof_number', 'emergency_contact_name', 'emergency_contact_phone',
                'emergency_contact_relation', 'move_in_date', 'security_deposit',
                'monthly_rent', 'lease_start_date', 'lease_end_date', 'billing_cycle',
                'billing_day', 'auto_billing_enabled', 'reminder_days_before',
                'overdue_grace_days', 'late_fee_amount', 'late_fee_percentage',
                'auto_payment_enabled', 'payment_method'
            ];

            foreach ($optionalFields as $field) {
                if (isset($validated[$field])) {
                    $tenantData[$field] = $validated[$field];
                }
            }

            $tenantProfile = TenantProfile::create($tenantData);

            // Update enquiry status
            $enquiry->update([
                'status' => 'resolved',
                'responded_at' => now(),
                'admin_notes' => ($enquiry->admin_notes ? $enquiry->admin_notes . "\n" : '') . "Converted to tenant profile #{$tenantProfile->id}"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Enquiry converted to tenant successfully',
                'data' => [
                    'enquiry' => $this->transformEnquiry($enquiry->load(['assignedUser'])),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'is_tenant' => $user->is_tenant,
                        'status' => $user->status
                    ],
                    'tenant_profile' => [
                        'id' => $tenantProfile->id,
                        'user_id' => $tenantProfile->user_id,
                        'first_name' => $tenantProfile->first_name,
                        'last_name' => $tenantProfile->last_name,
                        'phone' => $tenantProfile->phone,
                        'status' => $tenantProfile->status,
                        'notes' => $tenantProfile->notes
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert enquiry to tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get enquiry statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $stats = [
                'total_enquiries' => Enquiry::count(),
                'new_enquiries' => Enquiry::where('status', 'new')->count(),
                'in_progress_enquiries' => Enquiry::where('status', 'in_progress')->count(),
                'resolved_enquiries' => Enquiry::where('status', 'resolved')->count(),
                'closed_enquiries' => Enquiry::where('status', 'closed')->count(),
                'unassigned_enquiries' => Enquiry::whereNull('assigned_to')->count(),
                'overdue_enquiries' => Enquiry::whereIn('status', ['new', 'in_progress'])
                    ->where('created_at', '<', Carbon::now()->subHours(24))
                    ->count(),
                'urgent_enquiries' => Enquiry::where('priority', 'urgent')->count(),
                'high_priority_enquiries' => Enquiry::where('priority', 'high')->count(),
                'room_booking_enquiries' => Enquiry::where('enquiry_type', 'room_booking')->count(),
                'general_info_enquiries' => Enquiry::where('enquiry_type', 'general_info')->count(),
                'pricing_enquiries' => Enquiry::where('enquiry_type', 'pricing')->count(),
                'facilities_enquiries' => Enquiry::where('enquiry_type', 'facilities')->count(),
                'other_enquiries' => Enquiry::where('enquiry_type', 'other')->count(),
                'today_enquiries' => Enquiry::whereDate('created_at', Carbon::today())->count(),
                'this_week_enquiries' => Enquiry::whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])->count(),
                'this_month_enquiries' => Enquiry::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'response_rate' => $this->calculateResponseRate(),
                'average_response_time' => $this->calculateAverageResponseTime(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Enquiry statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve enquiry statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get enquiry sources
     */
    public function getSources(Request $request): JsonResponse
    {
        try {
            $sources = Enquiry::selectRaw('source, COUNT(*) as count')
                ->whereNotNull('source')
                ->groupBy('source')
                ->orderBy('count', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Enquiry sources retrieved successfully',
                'data' => $sources
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve enquiry sources',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search enquiries
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'status' => ['nullable', Rule::in(['new', 'in_progress', 'resolved', 'closed'])],
                'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
                'enquiry_type' => ['nullable', Rule::in(['room_booking', 'general_info', 'pricing', 'facilities', 'other'])],
                'limit' => 'nullable|integer|min:1|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = $request->get('query');
            $status = $request->get('status');
            $priority = $request->get('priority');
            $enquiryType = $request->get('enquiry_type');
            $limit = $request->get('limit', 10);

            $enquiriesQuery = Enquiry::with(['assignedUser'])
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('phone', 'like', "%{$query}%")
                      ->orWhere('subject', 'like', "%{$query}%")
                      ->orWhere('message', 'like', "%{$query}%");
                });

            if ($status) {
                $enquiriesQuery->where('status', $status);
            }

            if ($priority) {
                $enquiriesQuery->where('priority', $priority);
            }

            if ($enquiryType) {
                $enquiriesQuery->where('enquiry_type', $enquiryType);
            }

            $enquiries = $enquiriesQuery->limit($limit)->get()->map(function ($enquiry) {
                return [
                    'id' => $enquiry->id,
                    'name' => $enquiry->name,
                    'email' => $enquiry->email,
                    'phone' => $enquiry->phone,
                    'enquiry_type' => $enquiry->enquiry_type,
                    'enquiry_type_display' => $enquiry->enquiry_type_display,
                    'subject' => $enquiry->subject,
                    'status' => $enquiry->status,
                    'status_badge' => $enquiry->status_badge,
                    'priority' => $enquiry->priority,
                    'priority_badge' => $enquiry->priority_badge,
                    'source' => $enquiry->source,
                    'assigned_to' => $enquiry->assignedUser ? [
                        'id' => $enquiry->assignedUser->id,
                        'name' => $enquiry->assignedUser->name
                    ] : null,
                    'is_overdue' => $enquiry->is_overdue,
                    'created_at' => $enquiry->created_at,
                    'responded_at' => $enquiry->responded_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $enquiries->toArray(),
                'query' => $query,
                'count' => $enquiries->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search enquiries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform enquiry data for API response
     */
    private function transformEnquiry(Enquiry $enquiry, bool $detailed = false): array
    {
        $data = [
            'id' => $enquiry->id,
            'name' => $enquiry->name,
            'email' => $enquiry->email,
            'phone' => $enquiry->phone,
            'enquiry_type' => $enquiry->enquiry_type,
            'enquiry_type_display' => $enquiry->enquiry_type_display,
            'subject' => $enquiry->subject,
            'message' => $enquiry->message,
            'status' => $enquiry->status,
            'status_badge' => $enquiry->status_badge,
            'priority' => $enquiry->priority,
            'priority_badge' => $enquiry->priority_badge,
            'source' => $enquiry->source,
            'is_overdue' => $enquiry->is_overdue,
            'created_at' => $enquiry->created_at,
            'updated_at' => $enquiry->updated_at,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'admin_notes' => $enquiry->admin_notes,
                'assigned_to' => $enquiry->assigned_to,
                'assigned_user' => $enquiry->assignedUser ? [
                    'id' => $enquiry->assignedUser->id,
                    'name' => $enquiry->assignedUser->name,
                    'email' => $enquiry->assignedUser->email
                ] : null,
                'responded_at' => $enquiry->responded_at,
                'metadata' => $enquiry->metadata,
            ]);
        }

        return $data;
    }

    /**
     * Calculate response rate percentage
     */
    private function calculateResponseRate(): float
    {
        $total = Enquiry::count();
        if ($total === 0) return 0;

        $responded = Enquiry::whereNotNull('responded_at')->count();
        return round(($responded / $total) * 100, 2);
    }

    /**
     * Calculate average response time in hours
     */
    private function calculateAverageResponseTime(): float
    {
        $respondedEnquiries = Enquiry::whereNotNull('responded_at')->get();
        
        if ($respondedEnquiries->isEmpty()) return 0;

        $totalHours = $respondedEnquiries->sum(function ($enquiry) {
            return $enquiry->created_at->diffInHours($enquiry->responded_at);
        });

        return round($totalHours / $respondedEnquiries->count(), 2);
    }
}
