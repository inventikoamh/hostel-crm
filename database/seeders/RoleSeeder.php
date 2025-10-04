<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin role
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'is_system' => true,
            ]
        );

        // Create Admin role
        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrative access to most system features',
                'is_system' => true,
            ]
        );

        // Create Manager role
        $manager = Role::firstOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Manager',
                'description' => 'Management access to hostel operations',
                'is_system' => true,
            ]
        );

        // Create Staff role
        $staff = Role::firstOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Staff',
                'description' => 'Limited access to daily operations',
                'is_system' => true,
            ]
        );

        // Create Viewer role
        $viewer = Role::firstOrCreate(
            ['slug' => 'viewer'],
            [
                'name' => 'Viewer',
                'description' => 'Read-only access to system data',
                'is_system' => true,
            ]
        );

        // Assign permissions to Super Admin (all permissions)
        $superAdmin->syncPermissions(Permission::pluck('id')->toArray());

        // Assign permissions to Admin (most permissions except user management)
        $adminPermissions = Permission::whereNotIn('module', ['User Management', 'Role Management', 'Permission Management'])
            ->pluck('id')->toArray();
        $admin->syncPermissions($adminPermissions);

        // Assign permissions to Manager (hostel, tenant, room, billing management)
        $managerPermissions = Permission::whereIn('module', [
            'Hostel Management',
            'Tenant Management',
            'Room Management',
            'Billing Management',
            'Enquiry Management',
            'Amenity Management',
            'Dashboard'
        ])->pluck('id')->toArray();
        $manager->syncPermissions($managerPermissions);

        // Assign permissions to Staff (limited operations)
        $staffPermissions = Permission::whereIn('slug', [
            'tenants.view',
            'tenants.create',
            'tenants.edit',
            'rooms.view',
            'enquiries.view',
            'enquiries.edit',
            'dashboard.view',
        ])->pluck('id')->toArray();
        $staff->syncPermissions($staffPermissions);

        // Assign permissions to Viewer (read-only)
        $viewerPermissions = Permission::where('slug', 'like', '%.view')
            ->pluck('id')->toArray();
        $viewer->syncPermissions($viewerPermissions);
    }
}
