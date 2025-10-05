<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get hostels from database
        $hostels = Hostel::all()->map(function ($hostel) {
            return [
                'id' => $hostel->id,
                'name' => $hostel->name,
                'address' => $hostel->address,
                'city' => $hostel->city,
                'state' => $hostel->state,
                'total_rooms' => $hostel->total_rooms,
                'total_beds' => $hostel->total_beds,
                'rent_per_bed' => $hostel->rent_per_bed,
                'status' => $hostel->status,
                'manager_name' => $hostel->manager_name,
                'manager_phone' => $hostel->manager_phone,
                'manager_email' => $hostel->manager_email,
                'occupancy_rate' => $hostel->occupancy_rate, // This will be calculated from actual tenant data
                'view_url' => route('hostels.show', $hostel->id),
                'edit_url' => route('hostels.edit', $hostel->id),
                'delete_url' => route('hostels.destroy', $hostel->id)
            ];
        });

        // Define table columns
        $columns = [
            ['key' => 'name', 'label' => 'Hostel Name', 'width' => 'w-48'],
            ['key' => 'address', 'label' => 'Address', 'width' => 'w-64'],
            ['key' => 'city', 'label' => 'City', 'width' => 'w-24'],
            ['key' => 'total_rooms', 'label' => 'Rooms', 'width' => 'w-20'],
            ['key' => 'total_beds', 'label' => 'Beds', 'width' => 'w-20'],
            ['key' => 'rent_per_bed', 'label' => 'Rent/Bed', 'width' => 'w-24'],
            ['key' => 'status', 'label' => 'Status', 'component' => 'components.status-badge', 'width' => 'w-24'],
            ['key' => 'occupancy_rate', 'label' => 'Occupancy', 'width' => 'w-24']
        ];

        // Define filters
        $filters = [
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'inactive', 'label' => 'Inactive'],
                    ['value' => 'maintenance', 'label' => 'Maintenance']
                ]
            ],
            [
                'key' => 'city',
                'label' => 'City',
                'type' => 'select',
                'options' => [
                    ['value' => 'New York', 'label' => 'New York'],
                    ['value' => 'Los Angeles', 'label' => 'Los Angeles'],
                    ['value' => 'Chicago', 'label' => 'Chicago'],
                    ['value' => 'Houston', 'label' => 'Houston']
                ]
            ],
            [
                'key' => 'rent_range',
                'label' => 'Rent Range',
                'type' => 'range',
                'min' => 300,
                'max' => 600
            ]
        ];

        // Define bulk actions
        $bulkActions = [
            [
                'key' => 'activate',
                'label' => 'Activate',
                'icon' => 'fas fa-check'
            ],
            [
                'key' => 'deactivate',
                'label' => 'Deactivate',
                'icon' => 'fas fa-times'
            ],
            [
                'key' => 'maintenance',
                'label' => 'Set Maintenance',
                'icon' => 'fas fa-tools'
            ],
            [
                'key' => 'export',
                'label' => 'Export Data',
                'icon' => 'fas fa-download'
            ]
        ];

        // Define pagination
        $pagination = [
            'from' => 1,
            'to' => $hostels->count(),
            'total' => $hostels->count(),
            'current_page' => 1,
            'per_page' => 25
        ];

        return view('hostels.index', compact('hostels', 'columns', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hostels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'total_rooms' => 'required|integer|min:1',
            'total_beds' => 'required|integer|min:1',
            'rent_per_bed' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'manager_name' => 'required|string|max:255',
            'manager_phone' => 'required|string|max:20',
            'manager_email' => 'required|email|max:255',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'rules' => 'required|string|min:10',
        ]);

        Hostel::create($request->all());

        return redirect()->route('hostels.index')->with('success', 'Hostel created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hostel = Hostel::findOrFail($id);

        return view('hostels.show', compact('hostel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $hostel = Hostel::findOrFail($id);

        return view('hostels.edit', compact('hostel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'total_rooms' => 'required|integer|min:1',
            'total_beds' => 'required|integer|min:1',
            'rent_per_bed' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'manager_name' => 'required|string|max:255',
            'manager_phone' => 'required|string|max:20',
            'manager_email' => 'required|email|max:255',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'rules' => 'required|string|min:10',
        ]);

        $hostel = Hostel::findOrFail($id);
        $hostel->update($request->all());

        return redirect()->route('hostels.show', $id)->with('success', 'Hostel updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete logic would go here
        return redirect()->route('hostels.index')->with('success', 'Hostel deleted successfully!');
    }
}
