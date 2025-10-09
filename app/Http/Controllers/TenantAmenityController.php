<?php

namespace App\Http\Controllers;

use App\Models\TenantAmenity;
use App\Models\TenantAmenityUsage;
use App\Models\PaidAmenity;
use App\Models\TenantProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TenantAmenityController extends Controller
{
    /**
     * Display tenant amenities for a specific tenant
     */
    public function index(Request $request, $tenantId = null)
    {
        $query = TenantAmenity::with(['tenantProfile.user', 'paidAmenity', 'usageRecords']);

        if ($tenantId) {
            $query->forTenant($tenantId);
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('billing_type')) {
            if ($request->billing_type === 'monthly') {
                $query->monthly();
            } else {
                $query->daily();
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('paidAmenity', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('tenantProfile.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $tenantAmenities = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Prepare data for x-data-table component
        $columns = [
            [
                'key' => 'tenant',
                'label' => 'Tenant',
                'width' => 'w-48',
                'component' => 'components.tenant-info'
            ],
            [
                'key' => 'service',
                'label' => 'Service',
                'width' => 'w-48'
            ],
            [
                'key' => 'category',
                'label' => 'Category',
                'width' => 'w-32'
            ],
            [
                'key' => 'billing_type',
                'label' => 'Billing',
                'width' => 'w-24'
            ],
            [
                'key' => 'price',
                'label' => 'Price',
                'width' => 'w-24'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'width' => 'w-32',
                'component' => 'components.status-update-dropdown'
            ],
            [
                'key' => 'duration',
                'label' => 'Duration',
                'width' => 'w-32'
            ]
        ];

        $data = $tenantAmenities->map(function ($tenantAmenity) {
            return [
                'id' => $tenantAmenity->id,
                'tenant' => [
                    'name' => $tenantAmenity->tenantProfile->user->name,
                    'email' => $tenantAmenity->tenantProfile->user->email,
                    'avatar' => $tenantAmenity->tenantProfile->user->avatar
                ],
                'service' => $tenantAmenity->paidAmenity->name,
                'category' => $tenantAmenity->paidAmenity->category_display,
                'billing_type' => $tenantAmenity->paidAmenity->billing_type_display,
                'price' => $tenantAmenity->formatted_effective_price,
                'status' => [
                    'status' => $tenantAmenity->status,
                    'tenantAmenityId' => $tenantAmenity->id
                ],
                'duration' => $tenantAmenity->duration_text,
                'view_url' => route('tenant-amenities.show', $tenantAmenity),
                'edit_url' => route('tenant-amenities.edit', $tenantAmenity),
                'delete_url' => route('tenant-amenities.destroy', $tenantAmenity)
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
                    ['value' => 'suspended', 'label' => 'Suspended']
                ],
                'value' => $request->status
            ],
            [
                'key' => 'billing_type',
                'label' => 'Billing Type',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Types'],
                    ['value' => 'monthly', 'label' => 'Monthly'],
                    ['value' => 'daily', 'label' => 'Daily']
                ],
                'value' => $request->billing_type
            ]
        ];

        $bulkActions = [
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
                'key' => 'delete',
                'label' => 'Delete Selected',
                'icon' => 'fas fa-trash'
            ]
        ];

        $stats = [
            'total' => TenantAmenity::count(),
            'active' => TenantAmenity::where('status', 'active')->count(),
            'monthly' => TenantAmenity::whereHas('paidAmenity', function($q) {
                $q->where('billing_type', 'monthly');
            })->count(),
            'daily' => TenantAmenity::whereHas('paidAmenity', function($q) {
                $q->where('billing_type', 'daily');
            })->count()
        ];

        return view('tenant-amenities.index', compact(
            'tenantAmenities', 'tenantId', 'columns', 'data', 'filters', 'bulkActions', 'stats'
        ));
    }

    /**
     * Show form to assign amenity to tenant
     */
    public function create(Request $request)
    {
        $selectedTenantId = $request->query('tenant_id');

        // Get all available amenities (we'll filter them dynamically on the frontend)
        $availableAmenities = PaidAmenity::active()
                                       ->orderBy('category')
                                       ->orderBy('name')
                                       ->get();

        $tenants = TenantProfile::with('user')->get();

        return view('tenant-amenities.create', compact('selectedTenantId', 'availableAmenities', 'tenants'));
    }

    /**
     * Store tenant amenity assignment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_profile_id' => 'required|exists:tenant_profiles,id',
            'paid_amenity_id' => 'required|exists:paid_amenities,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'custom_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if tenant already has this amenity active
        $existingAssignment = TenantAmenity::where('tenant_profile_id', $request->tenant_profile_id)
                                         ->where('paid_amenity_id', $request->paid_amenity_id)
                                         ->where('status', 'active')
                                         ->first();

        if ($existingAssignment) {
            return redirect()->back()
                           ->with('error', 'This amenity is already assigned to the tenant.')
                           ->withInput();
        }

        TenantAmenity::create([
            'tenant_profile_id' => $request->tenant_profile_id,
            'paid_amenity_id' => $request->paid_amenity_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'custom_price' => $request->custom_price,
            'notes' => $request->notes,
            'status' => 'active'
        ]);

        $redirectRoute = $request->has('tenant_id')
            ? 'tenant-amenities.index'
            : 'tenant-amenities.index';

        return redirect()->route($redirectRoute, ['tenant' => $request->tenant_profile_id])
                        ->with('success', 'Amenity assigned to tenant successfully!');
    }

    /**
     * Display specific tenant amenity
     */
    public function show(TenantAmenity $tenantAmenity)
    {
        $tenantAmenity->load([
            'tenantProfile.user',
            'tenantProfile.currentBed.room.hostel',
            'paidAmenity',
            'usageRecords.recordedBy'
        ]);

        // Get usage summary for current month
        $currentMonth = Carbon::now();
        $usageSummary = $tenantAmenity->getMonthlyBillingSummary($currentMonth->year, $currentMonth->month);

        return view('tenant-amenities.show', compact('tenantAmenity', 'usageSummary'));
    }

    /**
     * Show form to edit tenant amenity
     */
    public function edit(TenantAmenity $tenantAmenity)
    {
        $tenantAmenity->load([
            'tenantProfile.user',
            'tenantProfile.currentBed.room.hostel',
            'paidAmenity'
        ]);

        return view('tenant-amenities.edit', compact('tenantAmenity'));
    }

    /**
     * Update tenant amenity
     */
    public function update(Request $request, TenantAmenity $tenantAmenity)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'custom_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tenantAmenity->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'custom_price' => $request->custom_price,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('tenant-amenities.show', $tenantAmenity)
                        ->with('success', 'Tenant amenity updated successfully!');
    }

    /**
     * Update status of tenant amenity (AJAX)
     */
    public function updateStatus(Request $request, TenantAmenity $tenantAmenity)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,suspended,pending'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value'
            ], 400);
        }

        $oldStatus = $tenantAmenity->status;
        $tenantAmenity->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => [
                'id' => $tenantAmenity->id,
                'status' => $tenantAmenity->status,
                'old_status' => $oldStatus
            ]
        ]);
    }

    /**
     * Remove tenant amenity assignment
     */
    public function destroy(TenantAmenity $tenantAmenity)
    {
        $tenantAmenity->delete();

        return redirect()->route('tenant-amenities.index')
                        ->with('success', 'Amenity assignment removed successfully!');
    }

    /**
     * Record daily usage for an amenity
     */
    public function recordUsage(Request $request, TenantAmenity $tenantAmenity)
    {
        $validator = Validator::make($request->all(), [
            'usage_date' => 'required|date|before_or_equal:today',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if amenity is daily billing type
        if ($tenantAmenity->paidAmenity->billing_type !== 'daily') {
            return redirect()->back()
                           ->with('error', 'Usage can only be recorded for daily billing amenities.');
        }

        // Check if usage date is within amenity active period
        $usageDate = Carbon::parse($request->usage_date);
        if ($usageDate < $tenantAmenity->start_date ||
            ($tenantAmenity->end_date && $usageDate > $tenantAmenity->end_date)) {
            return redirect()->back()
                           ->with('error', 'Usage date is outside the amenity active period.');
        }

        // Check max usage per day limit
        $maxUsage = $tenantAmenity->paidAmenity->max_usage_per_day;
        if ($maxUsage && $request->quantity > $maxUsage) {
            return redirect()->back()
                           ->with('error', "Maximum {$maxUsage} usage(s) allowed per day for this amenity.");
        }

        $tenantAmenity->recordUsage(
            $request->usage_date,
            $request->quantity,
            $request->notes,
            auth()->id()
        );

        return redirect()->back()
                        ->with('success', 'Usage recorded successfully!');
    }

    /**
     * Update existing usage record
     */
    public function updateUsage(Request $request, TenantAmenityUsage $usage)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check max usage per day limit
        $maxUsage = $usage->tenantAmenity->paidAmenity->max_usage_per_day;
        if ($maxUsage && $request->quantity > $maxUsage) {
            return redirect()->back()
                           ->with('error', "Maximum {$maxUsage} usage(s) allowed per day for this amenity.");
        }

        $usage->update([
            'quantity' => $request->quantity,
            'total_amount' => $usage->unit_price * $request->quantity,
            'notes' => $request->notes
        ]);

        return redirect()->back()
                        ->with('success', 'Usage updated successfully!');
    }

    /**
     * Delete usage record
     */
    public function deleteUsage(TenantAmenityUsage $usage)
    {
        $usage->delete();

        return redirect()->back()
                        ->with('success', 'Usage record deleted successfully!');
    }

    /**
     * Get monthly billing summary for a tenant
     */
    public function getBillingSummary(Request $request, $tenantId)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tenantAmenities = TenantAmenity::with(['paidAmenity', 'usageRecords'])
                                      ->forTenant($tenantId)
                                      ->active()
                                      ->get();

        $billingSummary = [];
        $totalAmount = 0;

        foreach ($tenantAmenities as $tenantAmenity) {
            $summary = $tenantAmenity->getMonthlyBillingSummary($request->year, $request->month);
            if ($summary['total_amount'] > 0) {
                $billingSummary[] = $summary;
                $totalAmount += $summary['total_amount'];
            }
        }

        return response()->json([
            'billing_summary' => $billingSummary,
            'total_amount' => $totalAmount,
            'formatted_total' => '₹' . number_format($totalAmount, 2)
        ]);
    }
}
