<?php

namespace App\Http\Controllers;

use App\Models\TenantProfile;
use App\Models\Bed;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'bed_id' => 'required|exists:beds,id',
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
                'user_type' => 'tenant'
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

            // Assign to bed
            $bed = Bed::findOrFail($request->bed_id);
            $bed->assignTenant($user->id, $request->move_in_date, $request->lease_end_date, $request->monthly_rent);

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
        $tenant->load(['user', 'currentBed.room.hostel', 'verifiedBy', 'tenantAmenities.paidAmenity', 'invoices', 'payments', 'tenantDocuments']);

        return view('tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TenantProfile $tenant)
    {
        $tenant->load(['user', 'currentBed.room.hostel']);
        $hostels = Hostel::with(['rooms.beds' => function ($query) {
            $query->where('status', 'available');
        }])->get();

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
            'security_deposit' => 'nullable|numeric|min:0',
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
                'security_deposit' => $request->security_deposit,
                'notes' => $request->notes,
            ]);

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
    public function getAvailableBeds(Hostel $hostel)
    {
        $beds = $hostel->rooms()
            ->with(['beds' => function ($query) {
                $query->where('status', 'available');
            }])
            ->get()
            ->pluck('beds')
            ->flatten()
            ->map(function ($bed) {
                return [
                    'id' => $bed->id,
                    'bed_number' => $bed->bed_number,
                    'room_name' => $bed->room->name,
                    'room_id' => $bed->room_id,
                    'rent' => $bed->rent
                ];
            });

        return response()->json($beds);
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
}
