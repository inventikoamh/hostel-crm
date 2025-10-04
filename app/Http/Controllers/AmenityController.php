<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $amenities = Amenity::ordered()->get()->map(function ($amenity) {
            return [
                'id' => $amenity->id,
                'name' => $amenity->name,
                'icon' => $amenity->icon,
                'description' => $amenity->description,
                'status' => $amenity->is_active ? 'active' : 'inactive', // Convert boolean to string for status-badge component
                'sort_order' => $amenity->sort_order,
                'created_at' => $amenity->created_at->format('M j, Y'),
                'view_url' => route('config.amenities.show', $amenity->id),
                'edit_url' => route('config.amenities.edit', $amenity->id),
                'delete_url' => route('config.amenities.destroy', $amenity->id)
            ];
        });

        $columns = [
            ['key' => 'name', 'label' => 'Name', 'width' => 'w-48'],
            ['key' => 'icon', 'label' => 'Icon', 'width' => 'w-24'],
            ['key' => 'description', 'label' => 'Description', 'width' => 'w-64'],
            ['key' => 'status', 'label' => 'Status', 'component' => 'components.status-badge', 'width' => 'w-24'],
            ['key' => 'sort_order', 'label' => 'Order', 'width' => 'w-20'],
            ['key' => 'created_at', 'label' => 'Created', 'width' => 'w-32']
        ];

        $filters = [
            [
                'key' => 'is_active',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => '1', 'label' => 'Active'],
                    ['value' => '0', 'label' => 'Inactive']
                ]
            ]
        ];

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
                'key' => 'delete',
                'label' => 'Delete',
                'icon' => 'fas fa-trash'
            ]
        ];

        $pagination = [
            'from' => 1,
            'to' => $amenities->count(),
            'total' => $amenities->count(),
            'current_page' => 1,
            'per_page' => 25
        ];

        return view('config.amenities.index', compact('amenities', 'columns', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('config.amenities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:amenities',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        Amenity::create($request->all());

        return redirect()->route('config.amenities.index')->with('success', 'Amenity created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $amenity = Amenity::findOrFail($id);

        return view('config.amenities.show', compact('amenity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $amenity = Amenity::findOrFail($id);

        return view('config.amenities.edit', compact('amenity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $amenity = Amenity::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name,' . $amenity->id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $amenity->update($request->all());

        return redirect()->route('config.amenities.index')->with('success', 'Amenity updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $amenity = Amenity::findOrFail($id);
        $amenity->delete();

        return redirect()->route('config.amenities.index')->with('success', 'Amenity deleted successfully!');
    }
}
