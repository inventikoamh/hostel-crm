<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
        'avatar',
        'is_tenant',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_tenant' => 'boolean',
        ];
    }

    // Relationships
    public function tenantProfile(): HasOne
    {
        return $this->hasOne(TenantProfile::class);
    }

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class, 'tenant_id');
    }

    public function currentBed()
    {
        return $this->hasOne(Bed::class, 'tenant_id')->where('status', 'occupied');
    }

    // Helper methods
    public function isTenant(): bool
    {
        return $this->is_tenant;
    }

    public function isActiveTenant(): bool
    {
        return $this->is_tenant && $this->tenantProfile && $this->tenantProfile->status === 'active';
    }

    public function getCurrentRoom()
    {
        $bed = $this->currentBed;
        return $bed ? $bed->room : null;
    }

    public function getCurrentHostel()
    {
        $room = $this->getCurrentRoom();
        return $room ? $room->hostel : null;
    }

    // Role and Permission Relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_roles', 'user_id', 'role_id')
            ->join('role_permissions', 'user_roles.role_id', '=', 'role_permissions.role_id')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->select('permissions.*');
    }

    // Role and Permission Helper Methods
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists() ||
               $this->roles()->whereHas('permissions', function ($query) use ($permission) {
                   $query->where('slug', $permission);
               })->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return $this->permissions()->whereIn('slug', $permissions)->exists() ||
               $this->roles()->whereHas('permissions', function ($query) use ($permissions) {
                   $query->whereIn('slug', $permissions);
               })->exists();
    }

    public function assignRole(Role $role): void
    {
        if (!$this->hasRole($role->slug)) {
            $this->roles()->attach($role);
        }
    }

    public function removeRole(Role $role): void
    {
        $this->roles()->detach($role);
    }

    public function syncRoles(array $roles): void
    {
        $this->roles()->sync($roles);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super-admin');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    // Check if user is a system user (not a tenant)
    public function isSystemUser(): bool
    {
        return !$this->is_tenant;
    }

    // Scope for tenants only
    public function scopeTenants($query)
    {
        return $query->where('is_tenant', true);
    }

    // Scope for system users only
    public function scopeSystemUsers($query)
    {
        return $query->where('is_tenant', false);
    }
}
