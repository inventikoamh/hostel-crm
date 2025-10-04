# User Management Module

## Overview

The User Management module provides comprehensive user administration capabilities with role-based access control (RBAC). It includes user CRUD operations, role management, and permission management for granular access control throughout the system.

## Features

### User Management
- **User CRUD Operations**: Create, read, update, and delete users
- **User Status Management**: Active, suspended, and inactive user states
- **Avatar Upload**: Profile picture management
- **Role Assignment**: Assign multiple roles to users
- **Bulk Operations**: Mass user operations (activate, suspend, delete)
- **Search & Filtering**: Advanced search and filtering capabilities
- **User Statistics**: Dashboard with user metrics

### Role Management
- **Role CRUD Operations**: Create, read, update, and delete roles
- **Permission Assignment**: Assign permissions to roles
- **Role Cloning**: Duplicate existing roles with permissions
- **System vs Custom Roles**: Distinguish between system and custom roles
- **Role Statistics**: Track role usage and user assignments

### Permission Management
- **Permission CRUD Operations**: Create, read, update, and delete permissions
- **Module Organization**: Organize permissions by system modules
- **System vs Custom Permissions**: Protect system permissions from deletion
- **Permission Usage Tracking**: Monitor which roles use each permission
- **Bulk Operations**: Mass permission operations

## File Structure

```
app/Http/Controllers/
├── UserController.php           # User management logic
├── RoleController.php           # Role management logic
└── PermissionController.php     # Permission management logic

app/Models/
├── User.php                     # User model with role relationships
├── Role.php                     # Role model with permissions
└── Permission.php               # Permission model

app/Http/Middleware/
└── CheckPermission.php          # Permission checking middleware

database/migrations/
├── add_role_fields_to_users_table.php
├── create_roles_table.php
├── create_permissions_table.php
├── create_role_permissions_table.php
└── create_user_roles_table.php

database/seeders/
├── PermissionSeeder.php         # Default permissions
└── RoleSeeder.php              # Default roles with permissions

resources/views/
├── users/
│   ├── index.blade.php         # User listing
│   ├── create.blade.php        # User creation form
│   ├── edit.blade.php          # User edit form
│   └── show.blade.php          # User details
├── roles/
│   ├── index.blade.php         # Role listing
│   ├── create.blade.php        # Role creation form
│   ├── edit.blade.php          # Role edit form
│   └── show.blade.php          # Role details
├── permissions/
│   ├── index.blade.php         # Permission listing
│   ├── create.blade.php        # Permission creation form
│   ├── edit.blade.php          # Permission edit form
│   └── show.blade.php          # Permission details
└── components/
    ├── user-info.blade.php     # User display component
    ├── role-type-badge.blade.php # Role type indicator
    └── permission-type-badge.blade.php # Permission type indicator
```

## Database Schema

### Users Table (Extended)
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    status ENUM('active', 'suspended', 'inactive') DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    avatar VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Roles Table
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    is_system BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Permissions Table
```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    module VARCHAR(255) NOT NULL,
    is_system BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Pivot Tables
```sql
-- User-Role relationship
CREATE TABLE user_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_user_role (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Role-Permission relationship
CREATE TABLE role_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_role_permission (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);
```

## Controllers

### UserController

#### Methods

##### `index(Request $request)`
- **Purpose**: Display paginated list of users with search and filtering
- **Route**: `GET /users`
- **Features**: Search, status filtering, role filtering, bulk actions
- **Returns**: User listing view with data-table integration

##### `create()`
- **Purpose**: Show user creation form
- **Route**: `GET /users/create`
- **Returns**: User creation form with role selection

##### `store(Request $request)`
- **Purpose**: Create new user
- **Route**: `POST /users`
- **Validation**: Name, email, password, phone, status, roles
- **Features**: Avatar upload, role assignment, password hashing
- **Returns**: Redirect to user list with success message

##### `show(User $user)`
- **Purpose**: Display user details
- **Route**: `GET /users/{user}`
- **Returns**: User detail view with roles and permissions

##### `edit(User $user)`
- **Purpose**: Show user edit form
- **Route**: `GET /users/{user}/edit`
- **Returns**: Pre-filled user edit form

##### `update(Request $request, User $user)`
- **Purpose**: Update user information
- **Route**: `PUT/PATCH /users/{user}`
- **Features**: Role reassignment, avatar update, status change
- **Returns**: Redirect to user detail with success message

##### `destroy(User $user)`
- **Purpose**: Delete user
- **Route**: `DELETE /users/{user}`
- **Protection**: Prevent deletion of admin users
- **Returns**: Redirect to user list with success message

##### `toggleStatus(User $user)`
- **Purpose**: Toggle user status (active/suspended)
- **Route**: `POST /users/{user}/toggle-status`
- **Returns**: JSON response with new status

##### `bulkAction(Request $request)`
- **Purpose**: Perform bulk operations on users
- **Route**: `POST /users/bulk-action`
- **Actions**: Activate, suspend, delete
- **Returns**: Redirect with success/error message

### RoleController

#### Methods

##### `index(Request $request)`
- **Purpose**: Display paginated list of roles
- **Route**: `GET /roles`
- **Features**: Search, type filtering, permission count display
- **Returns**: Role listing view with data-table integration

##### `create()`
- **Purpose**: Show role creation form
- **Route**: `GET /roles/create`
- **Returns**: Role creation form with permission selection

##### `store(Request $request)`
- **Purpose**: Create new role
- **Route**: `POST /roles`
- **Validation**: Name, slug, description, permissions
- **Features**: Permission assignment, slug generation
- **Returns**: Redirect to role list with success message

##### `show(Role $role)`
- **Purpose**: Display role details
- **Route**: `GET /roles/{role}`
- **Returns**: Role detail view with permissions and users

##### `edit(Role $role)`
- **Purpose**: Show role edit form
- **Route**: `GET /roles/{role}/edit`
- **Returns**: Pre-filled role edit form

##### `update(Request $request, Role $role)`
- **Purpose**: Update role information
- **Route**: `PUT/PATCH /roles/{role}`
- **Features**: Permission reassignment, system role protection
- **Returns**: Redirect to role detail with success message

##### `destroy(Role $role)`
- **Purpose**: Delete role
- **Route**: `DELETE /roles/{role}`
- **Protection**: Prevent deletion of system roles and roles with users
- **Returns**: Redirect to role list with success message

##### `clone(Role $role)`
- **Purpose**: Clone existing role
- **Route**: `POST /roles/{role}/clone`
- **Features**: Copy role with all permissions
- **Returns**: Redirect to new role edit form

### PermissionController

#### Methods

##### `index(Request $request)`
- **Purpose**: Display paginated list of permissions
- **Route**: `GET /permissions`
- **Features**: Search, module filtering, type filtering
- **Returns**: Permission listing view with data-table integration

##### `create()`
- **Purpose**: Show permission creation form
- **Route**: `GET /permissions/create`
- **Returns**: Permission creation form with module selection

##### `store(Request $request)`
- **Purpose**: Create new permission
- **Route**: `POST /permissions`
- **Validation**: Name, slug, description, module
- **Features**: Auto-slug generation, module organization
- **Returns**: Redirect to permission list with success message

##### `show(Permission $permission)`
- **Purpose**: Display permission details
- **Route**: `GET /permissions/{permission}`
- **Returns**: Permission detail view with role usage

##### `edit(Permission $permission)`
- **Purpose**: Show permission edit form
- **Route**: `GET /permissions/{permission}/edit`
- **Returns**: Pre-filled permission edit form

##### `update(Request $request, Permission $permission)`
- **Purpose**: Update permission information
- **Route**: `PUT/PATCH /permissions/{permission}`
- **Features**: System permission protection
- **Returns**: Redirect to permission detail with success message

##### `destroy(Permission $permission)`
- **Purpose**: Delete permission
- **Route**: `DELETE /permissions/{permission}`
- **Protection**: Prevent deletion of system permissions and permissions assigned to roles
- **Returns**: Redirect to permission list with success message

##### `bulkAction(Request $request)`
- **Purpose**: Perform bulk operations on permissions
- **Route**: `POST /permissions/bulk-action`
- **Actions**: Delete
- **Returns**: Redirect with success/error message

## Models

### User Model

#### Relationships
```php
// Many-to-many relationship with roles
public function roles()
{
    return $this->belongsToMany(Role::class, 'user_roles');
}

// Get all permissions through roles
public function permissions()
{
    return $this->roles->flatMap->permissions->unique('id');
}
```

#### Helper Methods
```php
// Check if user has specific role
public function hasRole($role)
{
    return $this->roles->contains('slug', $role);
}

// Check if user has specific permission
public function hasPermission($permission)
{
    return $this->permissions->contains('slug', $permission);
}

// Check if user has any of the given roles
public function hasAnyRole($roles)
{
    return $this->roles->whereIn('slug', $roles)->isNotEmpty();
}

// Check if user has any of the given permissions
public function hasAnyPermission($permissions)
{
    return $this->permissions->whereIn('slug', $permissions)->isNotEmpty();
}

// Assign role to user
public function assignRole($role)
{
    return $this->roles()->syncWithoutDetaching([$role]);
}

// Remove role from user
public function removeRole($role)
{
    return $this->roles()->detach($role);
}

// Sync user roles
public function syncRoles($roles)
{
    return $this->roles()->sync($roles);
}

// Check if user is admin
public function isAdmin()
{
    return $this->hasRole('super-admin') || $this->hasRole('admin');
}

// Check if user is active
public function isActive()
{
    return $this->status === 'active';
}

// Check if user is suspended
public function isSuspended()
{
    return $this->status === 'suspended';
}
```

### Role Model

#### Relationships
```php
// Many-to-many relationship with users
public function users()
{
    return $this->belongsToMany(User::class, 'user_roles');
}

// Many-to-many relationship with permissions
public function permissions()
{
    return $this->belongsToMany(Permission::class, 'role_permissions');
}
```

#### Helper Methods
```php
// Check if role has specific permission
public function hasPermission($permission)
{
    return $this->permissions->contains('slug', $permission);
}

// Give permission to role
public function givePermission($permission)
{
    return $this->permissions()->syncWithoutDetaching([$permission]);
}

// Revoke permission from role
public function revokePermission($permission)
{
    return $this->permissions()->detach($permission);
}

// Sync role permissions
public function syncPermissions($permissions)
{
    return $this->permissions()->sync($permissions);
}

// Scope for non-system roles
public function scopeNonSystem($query)
{
    return $query->where('is_system', false);
}

// Scope for system roles
public function scopeSystem($query)
{
    return $query->where('is_system', true);
}
```

### Permission Model

#### Relationships
```php
// Many-to-many relationship with roles
public function roles()
{
    return $this->belongsToMany(Role::class, 'role_permissions');
}
```

#### Helper Methods
```php
// Scope for permissions by module
public function scopeByModule($query, $module)
{
    return $query->where('module', $module);
}

// Scope for non-system permissions
public function scopeNonSystem($query)
{
    return $query->where('is_system', false);
}

// Scope for system permissions
public function scopeSystem($query)
{
    return $query->where('is_system', true);
}

// Get all available modules
public static function getModules()
{
    return self::distinct()->pluck('module')->sort()->values()->toArray();
}
```

## Middleware

### CheckPermission Middleware

#### Purpose
Check if authenticated user has required permission before allowing access to routes.

#### Usage
```php
// In routes
Route::middleware(['auth', 'permission:users.create'])->group(function () {
    Route::get('/users/create', [UserController::class, 'create']);
});

// In controller
public function __construct()
{
    $this->middleware('permission:users.index')->only('index');
    $this->middleware('permission:users.create')->only(['create', 'store']);
    $this->middleware('permission:users.edit')->only(['edit', 'update']);
    $this->middleware('permission:users.delete')->only('destroy');
}
```

#### Implementation
```php
public function handle($request, Closure $next, $permission)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (!auth()->user()->hasPermission($permission)) {
        abort(403, 'Unauthorized access');
    }

    return $next($request);
}
```

## Seeders

### PermissionSeeder

#### Default Permissions
The seeder creates comprehensive permissions organized by modules:

- **User Management**: users.view, users.create, users.edit, users.delete
- **Role Management**: roles.view, roles.create, roles.edit, roles.delete
- **Permission Management**: permissions.view, permissions.create, permissions.edit, permissions.delete
- **Hostel Management**: hostels.view, hostels.create, hostels.edit, hostels.delete
- **Tenant Management**: tenants.view, tenants.create, tenants.edit, tenants.delete
- **Room Management**: rooms.view, rooms.create, rooms.edit, rooms.delete
- **Invoice Management**: invoices.view, invoices.create, invoices.edit, invoices.delete
- **Payment Management**: payments.view, payments.create, payments.edit, payments.delete
- **Dashboard**: dashboard.view
- **Reports**: reports.view, reports.export
- **Settings**: settings.view, settings.edit

### RoleSeeder

#### Default Roles
The seeder creates five default roles with appropriate permissions:

1. **Super Admin**: All permissions
2. **Admin**: Most permissions except system management
3. **Manager**: Hostel and tenant management permissions
4. **Staff**: Limited permissions for daily operations
5. **Viewer**: Read-only permissions

## Routes

### User Management Routes
```php
Route::prefix('users')->name('users.')->middleware('auth')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
});
```

### Role Management Routes
```php
Route::prefix('roles')->name('roles.')->middleware('auth')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('/create', [RoleController::class, 'create'])->name('create');
    Route::post('/', [RoleController::class, 'store'])->name('store');
    Route::get('/{role}', [RoleController::class, 'show'])->name('show');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('update');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    Route::post('/{role}/clone', [RoleController::class, 'clone'])->name('clone');
});
```

### Permission Management Routes
```php
Route::prefix('permissions')->name('permissions.')->middleware('auth')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('index');
    Route::get('/create', [PermissionController::class, 'create'])->name('create');
    Route::post('/', [PermissionController::class, 'store'])->name('store');
    Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
    Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
    Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
    Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-action', [PermissionController::class, 'bulkAction'])->name('bulk-action');
});
```

## Views

### Data Table Integration

All listing views use the `x-data-table` component for consistent UI:

#### User Index
- **Columns**: User info, email, roles, status, last login
- **Filters**: Status, role, search
- **Bulk Actions**: Activate, suspend, delete
- **Stats Cards**: Total users, active users, suspended users, new users

#### Role Index
- **Columns**: Role name, description, permissions count, users count, type
- **Filters**: Type (system/custom), search
- **Bulk Actions**: Delete
- **Stats Cards**: Total roles, system roles, custom roles, permissions

#### Permission Index
- **Columns**: Permission name, slug, module, description, type
- **Filters**: Module, type (system/custom), search
- **Bulk Actions**: Delete
- **Stats Cards**: Total permissions, system permissions, custom permissions, modules

### Form Features

#### User Forms
- **Avatar Upload**: File upload with preview
- **Role Selection**: Multi-select with search
- **Status Selection**: Active, suspended, inactive
- **Phone Validation**: International phone number format
- **Password Generation**: Auto-generate secure passwords

#### Role Forms
- **Permission Assignment**: Organized by modules with checkboxes
- **Slug Generation**: Auto-generate from name
- **Description**: Optional detailed description
- **System Protection**: Prevent editing system roles

#### Permission Forms
- **Module Selection**: Dropdown with existing modules
- **Custom Module**: Option to create new modules
- **Slug Generation**: Auto-generate from name
- **System Protection**: Prevent editing system permissions

## Security Features

### Access Control
- **Permission-based**: Granular permission checking
- **Role-based**: Role-based access control
- **Middleware Protection**: Route-level protection
- **System Protection**: Protect system roles and permissions

### Data Protection
- **Input Validation**: Comprehensive validation rules
- **CSRF Protection**: Laravel CSRF tokens
- **File Upload Security**: Secure avatar upload handling
- **SQL Injection Prevention**: Eloquent ORM usage

### User Protection
- **Admin Protection**: Prevent deletion of admin users
- **Self-protection**: Users cannot delete themselves
- **Status Management**: Safe user status changes
- **Bulk Operation Validation**: Validate bulk operations

## Testing

### Unit Tests
```php
// Test user role assignment
public function test_user_can_be_assigned_role()
{
    $user = User::factory()->create();
    $role = Role::factory()->create();
    
    $user->assignRole($role->id);
    
    $this->assertTrue($user->hasRole($role->slug));
}

// Test permission checking
public function test_user_has_permission_through_role()
{
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::factory()->create();
    
    $role->givePermission($permission->id);
    $user->assignRole($role->id);
    
    $this->assertTrue($user->hasPermission($permission->slug));
}
```

### Feature Tests
```php
// Test user creation
public function test_admin_can_create_user()
{
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'roles' => [1]
    ];
    
    $response = $this->actingAs($admin)->post('/users', $userData);
    $response->assertRedirect('/users');
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
}
```

## Performance Considerations

### Database Optimization
- **Eager Loading**: Load relationships efficiently
- **Indexing**: Proper database indexes
- **Query Optimization**: Optimize complex queries
- **Caching**: Cache frequently accessed data

### UI Performance
- **Pagination**: Limit data per page
- **Lazy Loading**: Load data on demand
- **Search Optimization**: Efficient search implementation
- **Bulk Operations**: Efficient bulk processing

## Troubleshooting

### Common Issues

#### Permission Not Working
1. Check middleware registration
2. Verify permission exists
3. Check user role assignment
4. Verify route protection
5. Check permission slug

#### Role Assignment Issues
1. Check role existence
2. Verify user-role relationship
3. Check pivot table data
4. Verify role permissions
5. Check system role protection

#### User Status Issues
1. Check status enum values
2. Verify status update logic
3. Check user protection rules
4. Verify bulk operation validation
5. Check status display logic

## Future Enhancements

### Planned Features
- **Two-Factor Authentication**: Enhanced security
- **User Groups**: Group-based permissions
- **Audit Logging**: Track user actions
- **API Authentication**: Token-based authentication
- **Social Login**: OAuth integration
- **Password Policies**: Enforce password rules
- **Account Lockout**: Brute force protection
- **Email Verification**: Email confirmation
- **Profile Management**: User profile pages
- **Notification Preferences**: User notification settings

### Performance Improvements
- **Redis Caching**: Cache user sessions and permissions
- **Database Optimization**: Query optimization
- **CDN Integration**: Static asset delivery
- **Lazy Loading**: Component lazy loading
- **Background Jobs**: Async user operations
