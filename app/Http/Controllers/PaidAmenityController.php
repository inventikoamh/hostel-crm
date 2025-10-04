<?php

namespace App\Http\Controllers;

use App\Models\PaidAmenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaidAmenityController extends Controller
{
    /**
     * Display a listing of paid amenities
     */
    public function index(Request $request)
    {
        $query = PaidAmenity::with(['activeTenantAmenities']);

        // Apply filters
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('billing_type')) {
            $query->byBillingType($request->billing_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $amenities = $query->orderBy('category')->orderBy('name')->get()->map(function ($amenity) {
            return [
                'id' => $amenity->id,
                'name' => $amenity->name,
                'category' => $amenity->category_display,
                'billing_type' => $amenity->billing_type_display,
                'price' => $amenity->formatted_price,
                'status' => $amenity->is_active ? 'active' : 'inactive',
                'active_tenants' => $amenity->active_tenant_count,
                'availability' => $amenity->getAvailabilityText(),
                'view_url' => route('paid-amenities.show', $amenity->id),
                'edit_url' => route('paid-amenities.edit', $amenity->id),
                'delete_url' => route('paid-amenities.destroy', $amenity->id)
            ];
        });

        // Statistics
        $stats = [
            'total' => PaidAmenity::count(),
            'active' => PaidAmenity::active()->count(),
            'monthly' => PaidAmenity::monthly()->count(),
            'daily' => PaidAmenity::daily()->count(),
        ];

        // Table columns
        $columns = [
            ['key' => 'name', 'label' => 'Name', 'width' => 'w-48'],
            ['key' => 'category', 'label' => 'Category', 'width' => 'w-32'],
            ['key' => 'billing_type', 'label' => 'Billing', 'width' => 'w-24'],
            ['key' => 'price', 'label' => 'Price', 'width' => 'w-28'],
            ['key' => 'active_tenants', 'label' => 'Tenants', 'width' => 'w-20'],
            ['key' => 'status', 'label' => 'Status', 'component' => 'components.status-badge', 'width' => 'w-24'],
            ['key' => 'availability', 'label' => 'Availability', 'width' => 'w-40']
        ];

        // Filters
        $filters = [
            [
                'key' => 'category',
                'label' => 'Category',
                'type' => 'select',
                'options' => [
                    ['value' => 'food', 'label' => 'Food & Meals'],
                    ['value' => 'cleaning', 'label' => 'Cleaning Services'],
                    ['value' => 'laundry', 'label' => 'Laundry Services'],
                    ['value' => 'utilities', 'label' => 'Utilities'],
                    ['value' => 'services', 'label' => 'General Services'],
                    ['value' => 'other', 'label' => 'Other']
                ]
            ],
            [
                'key' => 'billing_type',
                'label' => 'Billing Type',
                'type' => 'select',
                'options' => [
                    ['value' => 'monthly', 'label' => 'Monthly'],
                    ['value' => 'daily', 'label' => 'Daily']
                ]
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'inactive', 'label' => 'Inactive']
                ]
            ]
        ];

        // Bulk actions
        $bulkActions = [
            ['key' => 'activate', 'label' => 'Activate Selected', 'icon' => 'fas fa-check'],
            ['key' => 'deactivate', 'label' => 'Deactivate Selected', 'icon' => 'fas fa-times'],
            ['key' => 'delete', 'label' => 'Delete Selected', 'icon' => 'fas fa-trash']
        ];

        // Pagination
        $pagination = [
            'current_page' => 1,
            'per_page' => 50,
            'total' => $amenities->count(),
            'last_page' => 1
        ];

        return view('paid-amenities.index', compact('amenities', 'stats', 'columns', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new paid amenity
     */
    public function create()
    {
        return view('paid-amenities.create');
    }

    /**
     * Store a newly created paid amenity
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:paid_amenities,name',
            'description' => 'nullable|string',
            'billing_type' => 'required|in:monthly,daily',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:food,cleaning,laundry,utilities,services,other',
            'is_active' => 'boolean',
            'availability_days' => 'nullable|array',
            'availability_days.*' => 'integer|between:0,6',
            'max_usage_per_day' => 'nullable|integer|min:1',
            'terms_conditions' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Prepare availability schedule
        $availabilitySchedule = null;
        if ($request->filled('availability_days')) {
            $availabilitySchedule = [
                'days' => $request->availability_days
            ];
        }

        PaidAmenity::create([
            'name' => $request->name,
            'description' => $request->description,
            'billing_type' => $request->billing_type,
            'price' => $request->price,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', true),
            'availability_schedule' => $availabilitySchedule,
            'max_usage_per_day' => $request->max_usage_per_day,
            'terms_conditions' => $request->terms_conditions,
            'icon' => $request->icon,
        ]);

        return redirect()->route('paid-amenities.index')
                        ->with('success', 'Paid amenity created successfully!');
    }

    /**
     * Display the specified paid amenity
     */
    public function show(PaidAmenity $paidAmenity)
    {
        $paidAmenity->load(['tenantAmenities.tenantProfile.user', 'tenantAmenities.usageRecords']);

        return view('paid-amenities.show', compact('paidAmenity'));
    }

    /**
     * Show the form for editing the specified paid amenity
     */
    public function edit(PaidAmenity $paidAmenity)
    {
        return view('paid-amenities.edit', compact('paidAmenity'));
    }

    /**
     * Update the specified paid amenity
     */
    public function update(Request $request, PaidAmenity $paidAmenity)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:paid_amenities,name,' . $paidAmenity->id,
            'description' => 'nullable|string',
            'billing_type' => 'required|in:monthly,daily',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:food,cleaning,laundry,utilities,services,other',
            'is_active' => 'boolean',
            'availability_days' => 'nullable|array',
            'availability_days.*' => 'integer|between:0,6',
            'max_usage_per_day' => 'nullable|integer|min:1',
            'terms_conditions' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Prepare availability schedule
        $availabilitySchedule = null;
        if ($request->filled('availability_days')) {
            $availabilitySchedule = [
                'days' => $request->availability_days
            ];
        }

        $paidAmenity->update([
            'name' => $request->name,
            'description' => $request->description,
            'billing_type' => $request->billing_type,
            'price' => $request->price,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', true),
            'availability_schedule' => $availabilitySchedule,
            'max_usage_per_day' => $request->max_usage_per_day,
            'terms_conditions' => $request->terms_conditions,
            'icon' => $request->icon,
        ]);

        return redirect()->route('paid-amenities.show', $paidAmenity)
                        ->with('success', 'Paid amenity updated successfully!');
    }

    /**
     * Remove the specified paid amenity
     */
    public function destroy(PaidAmenity $paidAmenity)
    {
        // Check if amenity is being used by any tenants
        if ($paidAmenity->tenantAmenities()->exists()) {
            return redirect()->back()
                           ->with('error', 'Cannot delete amenity that is being used by tenants.');
        }

        $paidAmenity->delete();

        return redirect()->route('paid-amenities.index')
                        ->with('success', 'Paid amenity deleted successfully!');
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:paid_amenities,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $amenities = PaidAmenity::whereIn('id', $request->selected_ids);
        $count = $amenities->count();

        switch ($request->action) {
            case 'activate':
                $amenities->update(['is_active' => true]);
                $message = "{$count} amenities activated successfully!";
                break;

            case 'deactivate':
                $amenities->update(['is_active' => false]);
                $message = "{$count} amenities deactivated successfully!";
                break;

            case 'delete':
                // Check if any selected amenities are being used
                $usedAmenities = $amenities->whereHas('tenantAmenities')->count();
                if ($usedAmenities > 0) {
                    return redirect()->back()
                                   ->with('error', "Cannot delete {$usedAmenities} amenities that are being used by tenants.");
                }

                $amenities->delete();
                $message = "{$count} amenities deleted successfully!";
                break;
        }

        return redirect()->route('paid-amenities.index')->with('success', $message);
    }
}
