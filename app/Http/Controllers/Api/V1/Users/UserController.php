<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\TenantProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserController extends Controller
{
    // ==================== USERS API ====================

    /**
     * Display a listing of users
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::with(['roles', 'permissions', 'tenantProfile']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('is_tenant')) {
                $query->where('is_tenant', $request->boolean('is_tenant'));
            }

            if ($request->has('is_super_admin')) {
                $query->where('is_super_admin', $request->boolean('is_super_admin'));
            }

            if ($request->has('role')) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('slug', $request->role);
                });
            }

            if ($request->has('permission')) {
                $query->whereHas('permissions', function ($q) use ($request) {
                    $q->where('slug', $request->permission);
                });
            }

            if ($request->has('module')) {
                $query->whereHas('permissions', function ($q) use ($request) {
                    $q->where('module', $request->module);
                });
            }

            if ($request->has('last_login_from')) {
                $query->where('last_login_at', '>=', $request->last_login_from);
            }

            if ($request->has('last_login_to')) {
                $query->where('last_login_at', '<=', $request->last_login_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Apply visibility scope based on authenticated user
            if (auth()->check()) {
                $query->visibleTo(auth()->user());
            } else {
                $query->where('is_super_admin', false);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Handle sorting by tenant name
            if ($sortBy === 'tenant_name') {
                $query->leftJoin('tenant_profiles', 'users.id', '=', 'tenant_profiles.user_id')
                      ->orderBy('tenant_profiles.first_name', $sortOrder)
                      ->select('users.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $users = $query->paginate($perPage);

            // Transform data
            $users->getCollection()->transform(function ($user) {
                return $this->transformUser($user);
            });

            return response()->json([
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new user (GET version for testing)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $roles = Role::nonSystem()->get(['id', 'name', 'slug']);
            $permissions = Permission::nonSystem()->get(['id', 'name', 'slug', 'module']);

            return response()->json([
                'success' => true,
                'message' => 'User creation form data',
                'data' => [
                    'required_fields' => [
                        'name' => 'User full name (required)',
                        'email' => 'User email address (required)',
                        'password' => 'User password (required)'
                    ],
                    'optional_fields' => [
                        'phone' => 'User phone number',
                        'status' => 'User status: active, inactive, suspended (default: active)',
                        'avatar' => 'User avatar image',
                        'is_tenant' => 'Is tenant user (boolean, default: false)',
                        'is_super_admin' => 'Is super admin (boolean, default: false)',
                        'roles' => 'Array of role IDs',
                        'permissions' => 'Array of permission IDs'
                    ],
                    'available_roles' => $roles,
                    'available_permissions' => $permissions,
                    'example_request' => [
                        'name' => 'John Doe',
                        'email' => 'john.doe@example.com',
                        'password' => 'password123',
                        'phone' => '+1-555-0123',
                        'status' => 'active',
                        'is_tenant' => false,
                        'is_super_admin' => false,
                        'roles' => [1, 2],
                        'permissions' => [1, 2, 3]
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/users for actual creation.'
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
     * Store a newly created user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string|max:20',
                'status' => ['nullable', Rule::in(['active', 'inactive', 'suspended'])],
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_tenant' => 'nullable|boolean',
                'is_super_admin' => 'nullable|boolean',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,id',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $validated['avatar'] = $avatarPath;
            }

            // Hash password
            $validated['password'] = Hash::make($validated['password']);

            // Set defaults
            $validated['status'] = $validated['status'] ?? 'active';
            $validated['is_tenant'] = $validated['is_tenant'] ?? false;
            $validated['is_super_admin'] = $validated['is_super_admin'] ?? false;

            // Extract roles and permissions
            $roles = $validated['roles'] ?? [];
            $permissions = $validated['permissions'] ?? [];
            unset($validated['roles'], $validated['permissions']);

            $user = User::create($validated);

            // Assign roles and permissions
            if (!empty($roles)) {
                $user->syncRoles($roles);
            }

            if (!empty($permissions)) {
                $user->permissions()->sync($permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $this->transformUser($user->load(['roles', 'permissions', 'tenantProfile']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $query = User::with(['roles', 'permissions', 'tenantProfile']);
            
            // Apply visibility scope
            if (auth()->check()) {
                $query->visibleTo(auth()->user());
            } else {
                $query->where('is_super_admin', false);
            }

            $user = $query->find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $this->transformUser($user, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
                'phone' => 'nullable|string|max:20',
                'status' => ['sometimes', 'required', Rule::in(['active', 'inactive', 'suspended'])],
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_tenant' => 'nullable|boolean',
                'is_super_admin' => 'nullable|boolean',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,id',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $validated['avatar'] = $avatarPath;
            }

            // Hash password if provided
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            // Extract roles and permissions
            $roles = $validated['roles'] ?? null;
            $permissions = $validated['permissions'] ?? null;
            unset($validated['roles'], $validated['permissions']);

            $user->update($validated);

            // Update roles and permissions if provided
            if ($roles !== null) {
                $user->syncRoles($roles);
            }

            if ($permissions !== null) {
                $user->permissions()->sync($permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $this->transformUser($user->load(['roles', 'permissions', 'tenantProfile']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Prevent deletion of super admin users
            if ($user->is_super_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete super admin users'
                ], 422);
            }

            // Prevent deletion of users with tenant profiles
            if ($user->tenantProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete users with tenant profiles. Please delete the tenant profile first.'
                ], 422);
            }

            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== ROLES API ====================

    /**
     * Display a listing of roles
     */
    public function indexRoles(Request $request): JsonResponse
    {
        try {
            $query = Role::with(['permissions', 'users']);

            // Apply filters
            if ($request->has('is_system')) {
                $query->where('is_system', $request->boolean('is_system'));
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $roles = $query->paginate($perPage);

            // Transform data
            $roles->getCollection()->transform(function ($role) {
                return $this->transformRole($role);
            });

            return response()->json([
                'success' => true,
                'message' => 'Roles retrieved successfully',
                'data' => $roles->items(),
                'pagination' => [
                    'current_page' => $roles->currentPage(),
                    'last_page' => $roles->lastPage(),
                    'per_page' => $roles->perPage(),
                    'total' => $roles->total(),
                    'from' => $roles->firstItem(),
                    'to' => $roles->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created role
     */
    public function storeRole(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:roles,slug',
                'description' => 'nullable|string',
                'is_system' => 'nullable|boolean',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Extract permissions
            $permissions = $validated['permissions'] ?? [];
            unset($validated['permissions']);

            $role = Role::create($validated);

            // Assign permissions
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => $this->transformRole($role->load(['permissions', 'users']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified role
     */
    public function updateRole(Request $request, $id): JsonResponse
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'slug' => 'sometimes|required|string|max:255|unique:roles,slug,' . $id,
                'description' => 'nullable|string',
                'is_system' => 'nullable|boolean',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Extract permissions
            $permissions = $validated['permissions'] ?? null;
            unset($validated['permissions']);

            $role->update($validated);

            // Update permissions if provided
            if ($permissions !== null) {
                $role->syncPermissions($permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $this->transformRole($role->load(['permissions', 'users']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroyRole(Request $request, $id): JsonResponse
    {
        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            // Prevent deletion of system roles
            if ($role->is_system) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete system roles'
                ], 422);
            }

            // Check if role has users
            if ($role->users()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role with assigned users. Please reassign users first.'
                ], 422);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== PERMISSIONS API ====================

    /**
     * Display a listing of permissions
     */
    public function indexPermissions(Request $request): JsonResponse
    {
        try {
            $query = Permission::with(['roles']);

            // Apply filters
            if ($request->has('is_system')) {
                $query->where('is_system', $request->boolean('is_system'));
            }

            if ($request->has('module')) {
                $query->where('module', $request->module);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('module', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'module');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder)->orderBy('name', 'asc');

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $permissions = $query->paginate($perPage);

            // Transform data
            $permissions->getCollection()->transform(function ($permission) {
                return $this->transformPermission($permission);
            });

            return response()->json([
                'success' => true,
                'message' => 'Permissions retrieved successfully',
                'data' => $permissions->items(),
                'pagination' => [
                    'current_page' => $permissions->currentPage(),
                    'last_page' => $permissions->lastPage(),
                    'per_page' => $permissions->perPage(),
                    'total' => $permissions->total(),
                    'from' => $permissions->firstItem(),
                    'to' => $permissions->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created permission
     */
    public function storePermission(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:permissions,slug',
                'description' => 'nullable|string',
                'module' => 'required|string|max:255',
                'is_system' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $permission = Permission::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
                'data' => $this->transformPermission($permission->load(['roles']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified permission
     */
    public function updatePermission(Request $request, $id): JsonResponse
    {
        try {
            $permission = Permission::find($id);

            if (!$permission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'slug' => 'sometimes|required|string|max:255|unique:permissions,slug,' . $id,
                'description' => 'nullable|string',
                'module' => 'sometimes|required|string|max:255',
                'is_system' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $permission->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'data' => $this->transformPermission($permission->load(['roles']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified permission
     */
    public function destroyPermission(Request $request, $id): JsonResponse
    {
        try {
            $permission = Permission::find($id);

            if (!$permission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission not found'
                ], 404);
            }

            // Prevent deletion of system permissions
            if ($permission->is_system) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete system permissions'
                ], 422);
            }

            // Check if permission has roles
            if ($permission->roles()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete permission assigned to roles. Please remove from roles first.'
                ], 422);
            }

            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== USER MANAGEMENT OPERATIONS ====================

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'role_id' => 'required|exists:roles,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $role = Role::find($request->role_id);
            $user->assignRole($role);

            return response()->json([
                'success' => true,
                'message' => 'Role assigned successfully',
                'data' => $this->transformUser($user->load(['roles', 'permissions']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request, $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'role_id' => 'required|exists:roles,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $role = Role::find($request->role_id);
            $user->removeRole($role);

            return response()->json([
                'success' => true,
                'message' => 'Role removed successfully',
                'data' => $this->transformUser($user->load(['roles', 'permissions']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suspend user
     */
    public function suspend(Request $request, $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update(['status' => 'suspended']);

            return response()->json([
                'success' => true,
                'message' => 'User suspended successfully',
                'data' => $this->transformUser($user)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate user
     */
    public function activate(Request $request, $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $user->update(['status' => 'active']);

            return response()->json([
                'success' => true,
                'message' => 'User activated successfully',
                'data' => $this->transformUser($user)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStats(Request $request): JsonResponse
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'inactive_users' => User::where('status', 'inactive')->count(),
                'suspended_users' => User::where('status', 'suspended')->count(),
                'tenant_users' => User::where('is_tenant', true)->count(),
                'system_users' => User::where('is_tenant', false)->count(),
                'super_admins' => User::where('is_super_admin', true)->count(),
                'users_with_roles' => User::whereHas('roles')->count(),
                'users_without_roles' => User::whereDoesntHave('roles')->count(),
                'recent_logins' => User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'User statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available modules for permissions
     */
    public function getModules(Request $request): JsonResponse
    {
        try {
            $modules = Permission::getModules();

            return response()->json([
                'success' => true,
                'message' => 'Modules retrieved successfully',
                'data' => $modules
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== SEARCH API ====================

    /**
     * Search users, roles, and permissions
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'type' => ['nullable', Rule::in(['users', 'roles', 'permissions'])],
                'status' => ['nullable', Rule::in(['active', 'inactive', 'suspended'])],
                'is_tenant' => 'nullable|boolean',
                'module' => 'nullable|string',
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
            $type = $request->type;
            $status = $request->status;
            $isTenant = $request->is_tenant;
            $module = $request->module;
            $limit = $request->get('limit', 10);

            $results = [];

            // Search users
            if (!$type || $type === 'users') {
                $usersQuery = User::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('phone', 'like', "%{$query}%");
                });

                if ($status) {
                    $usersQuery->where('status', $status);
                }

                if ($isTenant !== null) {
                    $usersQuery->where('is_tenant', $isTenant);
                }

                // Apply visibility scope
                if (auth()->check()) {
                    $usersQuery->visibleTo(auth()->user());
                } else {
                    $usersQuery->where('is_super_admin', false);
                }

                $users = $usersQuery->limit($limit)->get()->map(function ($user) {
                    return [
                        'type' => 'user',
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'status' => $user->status,
                        'is_tenant' => $user->is_tenant,
                        'is_super_admin' => $user->is_super_admin,
                        'roles_count' => $user->roles->count(),
                        'last_login_at' => $user->last_login_at,
                    ];
                });

                $results = array_merge($results, $users->toArray());
            }

            // Search roles
            if (!$type || $type === 'roles') {
                $rolesQuery = Role::where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");

                $roles = $rolesQuery->limit($limit)->get()->map(function ($role) {
                    return [
                        'type' => 'role',
                        'id' => $role->id,
                        'name' => $role->name,
                        'slug' => $role->slug,
                        'description' => $role->description,
                        'is_system' => $role->is_system,
                        'permissions_count' => $role->permissions->count(),
                        'users_count' => $role->users->count(),
                    ];
                });

                $results = array_merge($results, $roles->toArray());
            }

            // Search permissions
            if (!$type || $type === 'permissions') {
                $permissionsQuery = Permission::where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('module', 'like', "%{$query}%");

                if ($module) {
                    $permissionsQuery->where('module', $module);
                }

                $permissions = $permissionsQuery->limit($limit)->get()->map(function ($permission) {
                    return [
                        'type' => 'permission',
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'description' => $permission->description,
                        'module' => $permission->module,
                        'is_system' => $permission->is_system,
                        'roles_count' => $permission->roles->count(),
                    ];
                });

                $results = array_merge($results, $permissions->toArray());
            }

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $results,
                'query' => $query,
                'count' => count($results)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Transform user data for API response
     */
    private function transformUser(User $user, bool $detailed = false): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => $user->status,
            'status_badge' => $this->getStatusBadge($user->status),
            'avatar' => $user->avatar ? Storage::url($user->avatar) : null,
            'is_tenant' => $user->is_tenant,
            'is_super_admin' => $user->is_super_admin,
            'last_login_at' => $user->last_login_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'slug' => $role->slug,
                        'description' => $role->description,
                        'is_system' => $role->is_system,
                    ];
                }),
                'permissions' => $user->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'description' => $permission->description,
                        'module' => $permission->module,
                        'is_system' => $permission->is_system,
                    ];
                }),
                'tenant_profile' => $user->tenantProfile ? [
                    'id' => $user->tenantProfile->id,
                    'first_name' => $user->tenantProfile->first_name,
                    'last_name' => $user->tenantProfile->last_name,
                    'phone' => $user->tenantProfile->phone,
                    'status' => $user->tenantProfile->status,
                ] : null,
            ]);
        }

        return $data;
    }

    /**
     * Transform role data for API response
     */
    private function transformRole(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'is_system' => $role->is_system,
            'permissions_count' => $role->permissions->count(),
            'users_count' => $role->users->count(),
            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'slug' => $permission->slug,
                    'module' => $permission->module,
                ];
            }),
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
        ];
    }

    /**
     * Transform permission data for API response
     */
    private function transformPermission(Permission $permission): array
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'slug' => $permission->slug,
            'description' => $permission->description,
            'module' => $permission->module,
            'is_system' => $permission->is_system,
            'roles_count' => $permission->roles->count(),
            'created_at' => $permission->created_at,
            'updated_at' => $permission->updated_at,
        ];
    }

    /**
     * Get status badge for user status
     */
    private function getStatusBadge(string $status): array
    {
        return match($status) {
            'active' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Active'],
            'inactive' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Inactive'],
            'suspended' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Suspended'],
            default => ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($status)]
        };
    }
}
