<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permission::with('roles');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");
            });
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by system permissions
        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->where('is_system', true);
            } elseif ($request->type === 'custom') {
                $query->where('is_system', false);
            }
        }

        $permissions = $query->orderBy('module')->orderBy('name')->paginate(15)->withQueryString();
        $modules = Permission::getModules();

        // Prepare data for data-table component
        $columns = [
            [
                'key' => 'name',
                'label' => 'Permission Name',
                'width' => 'w-48'
            ],
            [
                'key' => 'slug',
                'label' => 'Slug',
                'width' => 'w-40'
            ],
            [
                'key' => 'module',
                'label' => 'Module',
                'width' => 'w-32'
            ],
            [
                'key' => 'description',
                'label' => 'Description',
                'width' => 'w-64'
            ],
            [
                'key' => 'is_system',
                'label' => 'Type',
                'width' => 'w-24',
                'component' => 'components.permission-type-badge'
            ]
        ];

        $data = $permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'slug' => $permission->slug,
                'module' => $permission->module,
                'description' => $permission->description ?: 'No description',
                'is_system' => [
                    'is_system' => $permission->is_system
                ],
                'view_url' => route('permissions.show', $permission),
                'edit_url' => route('permissions.edit', $permission),
                'delete_url' => route('permissions.destroy', $permission)
            ];
        })->toArray();

        $filters = [
            [
                'key' => 'module',
                'label' => 'Module',
                'type' => 'select',
                'options' => collect($modules)->map(function ($module) {
                    return ['value' => $module, 'label' => $module];
                })->toArray()
            ],
            [
                'key' => 'type',
                'label' => 'Type',
                'type' => 'select',
                'options' => [
                    ['value' => 'system', 'label' => 'System Permissions'],
                    ['value' => 'custom', 'label' => 'Custom Permissions']
                ]
            ]
        ];

        $bulkActions = [
            [
                'key' => 'delete',
                'label' => 'Delete Permissions',
                'icon' => 'fas fa-trash'
            ]
        ];

        $pagination = [
            'from' => $permissions->firstItem(),
            'to' => $permissions->lastItem(),
            'total' => $permissions->total(),
            'links' => $permissions->linkCollection()->map(function ($link) {
                return [
                    'url' => $link['url'],
                    'label' => $link['label'],
                    'active' => $link['active']
                ];
            })->toArray()
        ];

        return view('permissions.index', compact('permissions', 'modules', 'columns', 'data', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = Permission::getModules();
        return view('permissions.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions',
            'description' => 'nullable|string',
            'module' => 'required|string|max:255',
        ]);

        Permission::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'module' => $request->module,
            'is_system' => false,
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles.users');
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        $modules = Permission::getModules();
        return view('permissions.edit', compact('permission', 'modules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string',
            'module' => 'required|string|max:255',
        ]);

        $permission->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'module' => $request->module,
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // Prevent deletion of system permissions
        if ($permission->is_system) {
            return redirect()->route('permissions.index')
                ->with('error', 'System permissions cannot be deleted.');
        }

        // Check if permission is assigned to roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete permission that is assigned to roles.');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Handle bulk actions for permissions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:delete',
            'permission_ids' => 'required|array|min:1',
            'permission_ids.*' => 'exists:permissions,id'
        ]);

        $permissions = Permission::whereIn('id', $request->permission_ids);

        switch ($request->action) {
            case 'delete':
                // Check for system permissions
                $systemPermissions = $permissions->where('is_system', true)->count();
                if ($systemPermissions > 0) {
                    return redirect()->route('permissions.index')
                        ->with('error', 'Cannot delete system permissions.');
                }

                // Check for permissions assigned to roles
                $assignedPermissions = $permissions->whereHas('roles')->count();
                if ($assignedPermissions > 0) {
                    return redirect()->route('permissions.index')
                        ->with('error', 'Cannot delete permissions that are assigned to roles.');
                }

                $deletedCount = $permissions->delete();
                return redirect()->route('permissions.index')
                    ->with('success', "Successfully deleted {$deletedCount} permissions.");
                break;

            default:
                return redirect()->route('permissions.index')
                    ->with('error', 'Invalid action selected.');
        }
    }
}
