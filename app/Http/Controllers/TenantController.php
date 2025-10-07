<?php

namespace App\Http\Controllers;

use App\Models\TenantProfile;
use App\Models\Bed;
use App\Models\BedAssignment;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TenantProfile::with(['user', 'currentBed.room.hostel', 'verifiedBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by hostel
        if ($request->filled('hostel')) {
            $query->whereHas('currentBed.room', function ($q) use ($request) {
                $q->where('hostel_id', $request->hostel);
            });
        }

        // Filter by verification status
        if ($request->filled('verification_status')) {
            if ($request->verification_status === 'verified') {
                $query->verified();
            } elseif ($request->verification_status === 'unverified') {
                $query->unverified();
            }
        }

        $tenants = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $hostels = Hostel::all();

        // Prepare data for data-table component
        $columns = [
            [
                'key' => 'tenant',
                'label' => 'Tenant',
                'width' => 'w-48',
                'component' => 'components.tenant-info'
            ],
            [
                'key' => 'contact_info',
                'label' => 'Contact Info',
                'width' => 'w-40',
                'html' => true
            ],
            [
                'key' => 'current_bed',
                'label' => 'Current Bed',
                'width' => 'w-32'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'width' => 'w-24',
                'component' => 'components.status-badge'
            ],
            [
                'key' => 'move_in_date',
                'label' => 'Move In Date',
                'width' => 'w-32'
            ],
            [
                'key' => 'monthly_rent',
                'label' => 'Monthly Rent',
                'width' => 'w-24'
            ]
        ];

        $data = $tenants->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'tenant' => [
                    'name' => $tenant->user->name,
                    'email' => $tenant->user->email,
                    'avatar' => $tenant->user->avatar
                ],
                'contact_info' => $tenant->phone . '<br>' . $tenant->user->email,
                'current_bed' => $tenant->currentBed ?
                    $tenant->currentBed->room->hostel->name . ' - ' .
                    $tenant->currentBed->room->name . ' - ' .
                    $tenant->currentBed->bed_number : 'Not Assigned',
                'status' => $tenant->status,
                'move_in_date' => $tenant->move_in_date ? $tenant->move_in_date->format('M d, Y') : 'Not moved in',
                'monthly_rent' => $tenant->monthly_rent ? '₹' . number_format($tenant->monthly_rent, 2) : 'Not set',
                'view_url' => route('tenants.show', $tenant),
                'edit_url' => route('tenants.edit', $tenant),
                'delete_url' => route('tenants.destroy', $tenant)
            ];
        })->toArray();

        $filters = [
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Status'],
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'inactive', 'label' => 'Inactive'],
                    ['value' => 'pending', 'label' => 'Pending'],
                    ['value' => 'suspended', 'label' => 'Suspended'],
                    ['value' => 'moved_out', 'label' => 'Moved Out']
                ],
                'value' => $request->status
            ],
            [
                'key' => 'hostel',
                'label' => 'Hostel',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Hostels'],
                    ...$hostels->map(fn($hostel) => ['value' => $hostel->id, 'label' => $hostel->name])
                ],
                'value' => $request->hostel
            ],
            [
                'key' => 'verification_status',
                'label' => 'Verification',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All'],
                    ['value' => 'verified', 'label' => 'Verified'],
                    ['value' => 'unverified', 'label' => 'Unverified']
                ],
                'value' => $request->verification_status
            ]
        ];

        $bulkActions = [
            [
                'key' => 'verify',
                'label' => 'Verify Selected',
                'icon' => 'fas fa-check'
            ],
            [
                'key' => 'activate',
                'label' => 'Activate Selected',
                'icon' => 'fas fa-play'
            ],
            [
                'key' => 'deactivate',
                'label' => 'Deactivate Selected',
                'icon' => 'fas fa-pause'
            ],
            [
                'key' => 'move_out',
                'label' => 'Move Out Selected',
                'icon' => 'fas fa-sign-out-alt'
            ]
        ];

        $stats = [
            'total' => TenantProfile::count(),
            'active' => TenantProfile::active()->count(),
            'pending' => TenantProfile::where('status', 'pending')->count(),
            'verified' => TenantProfile::verified()->count(),
            'total_rent' => TenantProfile::where('status', 'active')->sum('monthly_rent')
        ];

        return view('tenants.index', compact(
            'tenants', 'columns', 'data', 'filters', 'bulkActions', 'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hostels = Hostel::with(['rooms.beds' => function ($query) {
            $query->where('status', 'available');
        }])->get();

        return view('tenants.create', compact('hostels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:500',
            'occupation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'id_proof_type' => 'required|string|in:aadhar,passport,driving_license,voter_id,pan_card,other',
            'id_proof_number' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relation' => 'required|string|max:100',
            'bed_id' => 'nullable|exists:beds,id',
            'move_in_date' => 'required|date',
            'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'lease_start_date' => 'required|date',
            'lease_end_date' => 'required|date|after:lease_start_date',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt('password123'), // Default password
                'status' => 'active',
                'is_tenant' => true
            ]);

            // Create tenant profile
            $tenantProfile = TenantProfile::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'occupation' => $request->occupation,
                'company' => $request->company,
                'id_proof_type' => $request->id_proof_type,
                'id_proof_number' => $request->id_proof_number,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relation' => $request->emergency_contact_relation,
                'status' => 'active',
                'move_in_date' => $request->move_in_date,
                'monthly_rent' => $request->monthly_rent,
                'security_deposit' => $request->security_deposit,
                'lease_start_date' => $request->lease_start_date,
                'lease_end_date' => $request->lease_end_date,
                'notes' => $request->notes,
                'is_verified' => false
            ]);

            // Assign to bed if selected using new BedAssignment system
            if ($request->bed_id) {
                $bed = Bed::findOrFail($request->bed_id);

                // Check if lease start date is in the future
                $leaseStartDate = Carbon::parse($request->lease_start_date);
                $today = Carbon::today();

                // Create bed assignment
                $assignmentStatus = $leaseStartDate->isFuture() ? 'reserved' : 'active';

                BedAssignment::create([
                    'bed_id' => $bed->id,
                    'tenant_id' => $user->id,
                    'assigned_from' => $request->lease_start_date,
                    'assigned_until' => $request->lease_end_date,
                    'status' => $assignmentStatus,
                    'monthly_rent' => $request->monthly_rent,
                    'notes' => 'Initial assignment via tenant creation'
                ]);

                // Update bed status based on assignment
                if ($assignmentStatus === 'active') {
                    $bed->update(['status' => 'occupied']);
                } else {
                    $bed->update(['status' => 'reserved']);
                }

                // Update room status
                $bed->room->updateStatus();
            }

            DB::commit();

            return redirect()->route('tenants.index')
                ->with('success', 'Tenant created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create tenant: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TenantProfile $tenant)
    {
        $tenant->load(['user', 'currentBed.room.hostel', 'verifiedBy', 'tenantAmenities.paidAmenity', 'invoices', 'payments', 'tenantDocuments', 'bedAssignments.bed.room.hostel']);

        return view('tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TenantProfile $tenant)
    {
        $tenant->load(['user', 'currentBed.room.hostel']);
        $hostels = Hostel::with(['rooms.beds'])->get();

        return view('tenants.edit', compact('tenant', 'hostels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TenantProfile $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $tenant->user_id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:500',
            'occupation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'id_proof_type' => 'required|string|in:aadhar,passport,driving_license,voter_id,pan_card,other',
            'id_proof_number' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relation' => 'required|string|max:100',
            'status' => 'required|string|in:active,inactive,pending,suspended,moved_out',
            'monthly_rent' => 'required|numeric|min:0',
            'lease_start_date' => 'required|date',
            'lease_end_date' => 'nullable|date|after:lease_start_date',
            'move_in_date' => 'nullable|date',
            'security_deposit' => 'nullable|numeric|min:0',
            'bed_id' => 'nullable|exists:beds,id',
            'hostel_id' => 'nullable|exists:hostels,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            // Update user
            $tenant->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // Update tenant profile
            $tenant->update([
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'occupation' => $request->occupation,
                'company' => $request->company,
                'id_proof_type' => $request->id_proof_type,
                'id_proof_number' => $request->id_proof_number,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relation' => $request->emergency_contact_relation,
                'status' => $request->status,
                'monthly_rent' => $request->monthly_rent,
                'lease_start_date' => $request->lease_start_date,
                'lease_end_date' => $request->lease_end_date,
                'move_in_date' => $request->move_in_date,
                'security_deposit' => $request->security_deposit,
                'notes' => $request->notes,
            ]);

            // Handle bed assignment update
            if ($request->bed_id) {
                $bed = Bed::findOrFail($request->bed_id);
                $leaseStartDate = Carbon::parse($request->lease_start_date);
                $leaseEndDate = $request->lease_end_date ? Carbon::parse($request->lease_end_date) : null;

                // Check if bed assignment has changed
                $currentAssignment = $tenant->currentBedAssignment;
                if (!$currentAssignment || $currentAssignment->bed_id != $bed->id) {
                    // Release current bed assignment if exists
                    if ($currentAssignment) {
                        $currentAssignment->update(['status' => 'inactive']);
                        $currentAssignment->bed->update(['status' => 'available']);
                    }

                    // Create new bed assignment
                    $assignment = BedAssignment::create([
                        'bed_id' => $bed->id,
                        'tenant_id' => $tenant->user_id,
                        'assigned_from' => $leaseStartDate,
                        'assigned_until' => $leaseEndDate,
                        'status' => $leaseStartDate->isFuture() ? 'reserved' : 'active',
                        'monthly_rent' => $request->monthly_rent,
                    ]);

                    // Update bed status
                    $bed->update([
                        'status' => $leaseStartDate->isFuture() ? 'reserved' : 'occupied'
                    ]);
                } else {
                    // Update existing assignment
                    $currentAssignment->update([
                        'assigned_from' => $leaseStartDate,
                        'assigned_until' => $leaseEndDate,
                        'status' => $leaseStartDate->isFuture() ? 'reserved' : 'active',
                        'monthly_rent' => $request->monthly_rent,
                    ]);

                    // Update bed status
                    $currentAssignment->bed->update([
                        'status' => $leaseStartDate->isFuture() ? 'reserved' : 'occupied'
                    ]);
                }
            } else {
                // Remove bed assignment if no bed selected
                $currentAssignment = $tenant->currentBedAssignment;
                if ($currentAssignment) {
                    $currentAssignment->update(['status' => 'inactive']);
                    $currentAssignment->bed->update(['status' => 'available']);
                }
            }

            DB::commit();

            return redirect()->route('tenants.index')
                ->with('success', 'Tenant updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update tenant: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TenantProfile $tenant)
    {
        DB::beginTransaction();
        try {
            // Release current bed if assigned
            $tenant->releaseCurrentBed();

            // Delete tenant profile
            $tenant->delete();

            // Delete user account
            $tenant->user->delete();

            DB::commit();

            return redirect()->route('tenants.index')
                ->with('success', 'Tenant deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to delete tenant: ' . $e->getMessage());
        }
    }

    /**
     * Get available beds for a specific hostel
     */
    public function getAvailableBeds(Request $request, Hostel $hostel)
    {
        $leaseStartDate = $request->get('lease_start_date');
        $leaseEndDate = $request->get('lease_end_date');

        // Get all beds for the hostel with their assignments
        $allBeds = $hostel->rooms()
            ->with(['beds.assignments' => function($query) {
                $query->whereIn('status', ['active', 'reserved']);
            }])
            ->get()
            ->pluck('beds')
            ->flatten();

        // Filter beds based on availability using the new BedAssignment system
        $availableBeds = $allBeds->filter(function ($bed) use ($leaseStartDate, $leaseEndDate) {
            // If bed is in maintenance, it's not available
            if ($bed->status === 'maintenance') {
                return false;
            }

            // Check assignments for conflicts
            $conflictingAssignments = $bed->assignments->filter(function($assignment) use ($leaseStartDate, $leaseEndDate) {
                // Skip inactive assignments
                if ($assignment->status === 'inactive') {
                    return false;
                }

                $assignmentStart = $assignment->assigned_from;
                $assignmentEnd = $assignment->assigned_until;

                // If no lease end date provided, just check if assignment ends before lease starts
                if (!$leaseEndDate) {
                    return $assignmentEnd && $assignmentEnd->gte(Carbon::parse($leaseStartDate));
                }

                // Check for date overlap
                if ($assignmentEnd) {
                    // Assignment has an end date - check for overlap
                    return $assignmentStart->lt(Carbon::parse($leaseEndDate)) && $assignmentEnd->gt(Carbon::parse($leaseStartDate));
                } else {
                    // Assignment has no end date - check if it starts before lease ends
                    return $assignmentStart->lt(Carbon::parse($leaseEndDate));
                }
            });

            // Bed is available if there are no conflicting assignments
            return $conflictingAssignments->count() === 0;
        })
        ->map(function ($bed) {
            return [
                'id' => $bed->id,
                'bed_number' => $bed->bed_number,
                'room_name' => $bed->room->name,
                'room_number' => $bed->room->room_number,
                'floor' => $bed->room->floor,
                'rent' => $bed->monthly_rent ?? $bed->room->rent_per_bed ?? 0,
                'label' => "Bed {$bed->bed_number} - {$bed->room->room_number}",
                'status' => $bed->status,
                'occupied_until' => $bed->occupied_until ? $bed->occupied_until->format('Y-m-d') : null
            ];
        });

        // Add debugging information
        \Log::info('Available beds query', [
            'hostel_id' => $hostel->id,
            'lease_start_date' => $leaseStartDate,
            'lease_end_date' => $leaseEndDate,
            'total_beds' => $allBeds->count(),
            'available_beds' => $availableBeds->count(),
            'beds_by_status' => $allBeds->groupBy('status')->map->count(),
            'reserved_beds_details' => $allBeds->where('status', 'reserved')->map(function($bed) use ($leaseStartDate, $leaseEndDate) {
                $requestedLeaseStart = $leaseStartDate ? Carbon::parse($leaseStartDate) : null;
                $requestedLeaseEnd = $leaseEndDate ? Carbon::parse($leaseEndDate) : null;
                $bedReservedFrom = $bed->occupied_from ? Carbon::parse($bed->occupied_from) : null;
                $bedReservedUntil = $bed->occupied_until ? Carbon::parse($bed->occupied_until) : null;

                $hasOverlap = false;
                if ($requestedLeaseStart && $requestedLeaseEnd && $bedReservedFrom && $bedReservedUntil) {
                    $hasOverlap = $bedReservedFrom < $requestedLeaseEnd && $bedReservedUntil > $requestedLeaseStart;
                }

                return [
                    'bed_id' => $bed->id,
                    'bed_number' => $bed->bed_number,
                    'reserved_from' => $bedReservedFrom ? $bedReservedFrom->format('Y-m-d') : null,
                    'reserved_until' => $bedReservedUntil ? $bedReservedUntil->format('Y-m-d') : null,
                    'requested_start' => $requestedLeaseStart ? $requestedLeaseStart->format('Y-m-d') : null,
                    'requested_end' => $requestedLeaseEnd ? $requestedLeaseEnd->format('Y-m-d') : null,
                    'has_overlap' => $hasOverlap,
                    'is_available' => !$hasOverlap
                ];
            })
        ]);

        return response()->json($availableBeds->values());
    }

    /**
     * Verify a tenant
     */
    public function verify(Request $request, TenantProfile $tenant)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $tenant->markAsVerified(Auth::id());

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant verified successfully.');
    }

    /**
     * Move out a tenant
     */
    public function moveOut(Request $request, TenantProfile $tenant)
    {
        $request->validate([
            'move_out_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        $tenant->moveOut($request->move_out_date);

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant moved out successfully.');
    }

    /**
     * Get available beds using the new BedAssignment system
     */
    public function getAvailableBedsNew(Request $request, Hostel $hostel)
    {
        $leaseStartDate = $request->get('lease_start_date');
        $leaseEndDate = $request->get('lease_end_date');

        // Get all beds for the hostel with their assignments
        $allBeds = $hostel->rooms()
            ->with(['beds.assignments' => function($query) {
                $query->whereIn('status', ['active', 'reserved']);
            }])
            ->get()
            ->pluck('beds')
            ->flatten();

        // Filter beds based on availability using the new assignment system
        $availableBeds = $allBeds->filter(function ($bed) use ($leaseStartDate, $leaseEndDate) {
            // Always show available beds (no active or reserved assignments)
            if ($bed->status === 'available' && $bed->assignments->isEmpty()) {
                return true;
            }

            // For maintenance beds, never show them
            if ($bed->status === 'maintenance') {
                return false;
            }

            // Check for overlapping assignments
            if ($leaseStartDate) {
                $hasOverlappingAssignment = $bed->assignments->contains(function ($assignment) use ($leaseStartDate, $leaseEndDate) {
                    return $assignment->overlapsWith($leaseStartDate, $leaseEndDate);
                });

                // If no overlapping assignments, bed is available
                return !$hasOverlappingAssignment;
            }

            // No lease start date provided, only show truly available beds
            return $bed->status === 'available' && $bed->assignments->isEmpty();
        });

        \Log::info('Available beds query (new system)', [
            'hostel_id' => $hostel->id,
            'lease_start_date' => $leaseStartDate,
            'lease_end_date' => $leaseEndDate,
            'total_beds' => $allBeds->count(),
            'available_beds' => $availableBeds->count(),
            'bed_breakdown' => $allBeds->groupBy('status')->map->count(),
            'assignment_details' => $allBeds->map(function($bed) {
                return [
                    'bed_id' => $bed->id,
                    'bed_number' => $bed->bed_number,
                    'status' => $bed->status,
                    'assignments' => $bed->assignments->map(function($assignment) {
                        return [
                            'id' => $assignment->id,
                            'tenant_name' => $assignment->tenant->name ?? 'Unknown',
                            'assigned_from' => $assignment->assigned_from,
                            'assigned_until' => $assignment->assigned_until,
                            'status' => $assignment->status
                        ];
                    })
                ];
            })
        ]);

        return response()->json($availableBeds->map(function ($bed) {
            return [
                'id' => $bed->id,
                'bed_number' => $bed->bed_number,
                'room_name' => $bed->room->name ?? null,
                'room_number' => $bed->room->room_number,
                'floor' => $bed->room->floor,
                'rent' => $bed->monthly_rent ?? $bed->room->rent_per_bed ?? 0,
                'label' => "Bed {$bed->bed_number} - {$bed->room->room_number}",
                'status' => $bed->status,
                'occupied_until' => null // This will be determined by assignments
            ];
        }));
    }
}
