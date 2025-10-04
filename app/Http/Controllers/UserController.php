<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->systemUsers(); // Only show system users, not tenants

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        // Prepare data for data-table component
        $columns = [
            [
                'key' => 'user',
                'label' => 'User',
                'width' => 'w-64',
                'component' => 'components.user-info'
            ],
            [
                'key' => 'email',
                'label' => 'Email',
                'width' => 'w-48'
            ],
            [
                'key' => 'roles',
                'label' => 'Roles',
                'width' => 'w-32'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'width' => 'w-24',
                'component' => 'components.status-badge'
            ],
            [
                'key' => 'last_login_at',
                'label' => 'Last Login',
                'width' => 'w-32'
            ]
        ];

        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'user' => [
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar
                ],
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->join(', '),
                'status' => $user->status,
                'last_login_at' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never',
                'view_url' => route('users.show', $user),
                'edit_url' => route('users.edit', $user),
                'delete_url' => route('users.destroy', $user)
            ];
        })->toArray();

        $filters = [
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'inactive', 'label' => 'Inactive'],
                    ['value' => 'suspended', 'label' => 'Suspended']
                ]
            ],
            [
                'key' => 'role',
                'label' => 'Role',
                'type' => 'select',
                'options' => $roles->map(function ($role) {
                    return ['value' => $role->slug, 'label' => $role->name];
                })->toArray()
            ]
        ];

        $bulkActions = [
            [
                'key' => 'activate',
                'label' => 'Activate Users',
                'icon' => 'fas fa-check'
            ],
            [
                'key' => 'deactivate',
                'label' => 'Deactivate Users',
                'icon' => 'fas fa-times'
            ],
            [
                'key' => 'suspend',
                'label' => 'Suspend Users',
                'icon' => 'fas fa-ban'
            ],
            [
                'key' => 'delete',
                'label' => 'Delete Users',
                'icon' => 'fas fa-trash'
            ]
        ];

        $pagination = [
            'from' => $users->firstItem(),
            'to' => $users->lastItem(),
            'total' => $users->total(),
            'links' => $users->linkCollection()->map(function ($link) {
                return [
                    'url' => $link['url'],
                    'label' => $link['label'],
                    'active' => $link['active']
                ];
            })->toArray()
        ];

        return view('users.index', compact('users', 'roles', 'columns', 'data', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = $request->only(['name', 'email', 'phone', 'status']);
        $userData['password'] = Hash::make($request->password);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            $path = $avatar->storeAs('avatars', $filename, 'public');
            $userData['avatar'] = $path;
        }

        $user = User::create($userData);

        // Assign roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles.permissions');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive,suspended',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = $request->only(['name', 'email', 'phone', 'status']);

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();
            $path = $avatar->storeAs('avatars', $filename, 'public');
            $userData['avatar'] = $path;
        }

        $user->update($userData);

        // Update roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deletion of the current user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', "User status updated to {$newStatus}.");
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,suspend,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['status' => 'active']);
                $message = 'Selected users have been activated.';
                break;
            case 'deactivate':
                User::whereIn('id', $userIds)->update(['status' => 'inactive']);
                $message = 'Selected users have been deactivated.';
                break;
            case 'suspend':
                User::whereIn('id', $userIds)->update(['status' => 'suspended']);
                $message = 'Selected users have been suspended.';
                break;
            case 'delete':
                // Prevent deletion of current user
                $userIds = array_filter($userIds, function ($id) {
                    return $id != auth()->id();
                });

                if (empty($userIds)) {
                    return redirect()->back()
                        ->with('error', 'You cannot delete your own account.');
                }

                User::whereIn('id', $userIds)->delete();
                $message = 'Selected users have been deleted.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}
