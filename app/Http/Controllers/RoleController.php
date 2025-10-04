<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions', 'users');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by system roles
        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->where('is_system', true);
            } elseif ($request->type === 'custom') {
                $query->where('is_system', false);
            }
        }

        $roles = $query->paginate(15)->withQueryString();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        // Prepare data for data-table component
        $columns = [
            [
                'key' => 'name',
                'label' => 'Role Name',
                'width' => 'w-48'
            ],
            [
                'key' => 'description',
                'label' => 'Description',
                'width' => 'w-64'
            ],
            [
                'key' => 'permissions_count',
                'label' => 'Permissions',
                'width' => 'w-24'
            ],
            [
                'key' => 'users_count',
                'label' => 'Users',
                'width' => 'w-20'
            ],
            [
                'key' => 'is_system',
                'label' => 'Type',
                'width' => 'w-24',
                'component' => 'components.role-type-badge'
            ]
        ];

        $data = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description ?: 'No description',
                'permissions_count' => $role->permissions->count(),
                'users_count' => $role->users->count(),
                'is_system' => [
                    'is_system' => $role->is_system
                ],
                'view_url' => route('roles.show', $role),
                'edit_url' => route('roles.edit', $role),
                'delete_url' => route('roles.destroy', $role)
            ];
        })->toArray();

        $filters = [
            [
                'key' => 'type',
                'label' => 'Type',
                'type' => 'select',
                'options' => [
                    ['value' => 'system', 'label' => 'System Roles'],
                    ['value' => 'custom', 'label' => 'Custom Roles']
                ]
            ]
        ];

        $bulkActions = [
            [
                'key' => 'delete',
                'label' => 'Delete Roles',
                'icon' => 'fas fa-trash'
            ]
        ];

        $pagination = [
            'from' => $roles->firstItem(),
            'to' => $roles->lastItem(),
            'total' => $roles->total(),
            'links' => $roles->linkCollection()->map(function ($link) {
                return [
                    'url' => $link['url'],
                    'label' => $link['label'],
                    'active' => $link['active']
                ];
            })->toArray()
        ];

        return view('roles.index', compact('roles', 'permissions', 'columns', 'data', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_system' => false,
        ]);

        // Assign permissions
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        $role->load('permissions');
        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        // Update permissions
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        if ($role->is_system) {
            return redirect()->route('roles.index')
                ->with('error', 'System roles cannot be deleted.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role that is assigned to users.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Clone a role
     */
    public function clone(Role $role)
    {
        $newRole = Role::create([
            'name' => $role->name . ' (Copy)',
            'slug' => Str::slug($role->name . ' copy'),
            'description' => $role->description,
            'is_system' => false,
        ]);

        // Copy permissions
        $newRole->syncPermissions($role->permissions->pluck('id')->toArray());

        return redirect()->route('roles.index')
            ->with('success', 'Role cloned successfully.');
    }
}
