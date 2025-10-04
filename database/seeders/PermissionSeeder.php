<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            [
                'name' => 'View Users',
                'slug' => 'users.view',
                'description' => 'View user list and details',
                'module' => 'User Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Users',
                'slug' => 'users.create',
                'description' => 'Create new users',
                'module' => 'User Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'users.edit',
                'description' => 'Edit user information',
                'module' => 'User Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'users.delete',
                'description' => 'Delete users',
                'module' => 'User Management',
                'is_system' => true,
            ],

            // Role Management
            [
                'name' => 'View Roles',
                'slug' => 'roles.view',
                'description' => 'View role list and details',
                'module' => 'Role Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Roles',
                'slug' => 'roles.create',
                'description' => 'Create new roles',
                'module' => 'Role Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Roles',
                'slug' => 'roles.edit',
                'description' => 'Edit role information and permissions',
                'module' => 'Role Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Roles',
                'slug' => 'roles.delete',
                'description' => 'Delete roles',
                'module' => 'Role Management',
                'is_system' => true,
            ],

            // Permission Management
            [
                'name' => 'View Permissions',
                'slug' => 'permissions.view',
                'description' => 'View permission list and details',
                'module' => 'Permission Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Permissions',
                'slug' => 'permissions.create',
                'description' => 'Create new permissions',
                'module' => 'Permission Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Permissions',
                'slug' => 'permissions.edit',
                'description' => 'Edit permission information',
                'module' => 'Permission Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Permissions',
                'slug' => 'permissions.delete',
                'description' => 'Delete permissions',
                'module' => 'Permission Management',
                'is_system' => true,
            ],

            // Hostel Management
            [
                'name' => 'View Hostels',
                'slug' => 'hostels.view',
                'description' => 'View hostel list and details',
                'module' => 'Hostel Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Hostels',
                'slug' => 'hostels.create',
                'description' => 'Create new hostels',
                'module' => 'Hostel Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Hostels',
                'slug' => 'hostels.edit',
                'description' => 'Edit hostel information',
                'module' => 'Hostel Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Hostels',
                'slug' => 'hostels.delete',
                'description' => 'Delete hostels',
                'module' => 'Hostel Management',
                'is_system' => true,
            ],

            // Tenant Management
            [
                'name' => 'View Tenants',
                'slug' => 'tenants.view',
                'description' => 'View tenant list and details',
                'module' => 'Tenant Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Tenants',
                'slug' => 'tenants.create',
                'description' => 'Create new tenants',
                'module' => 'Tenant Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Tenants',
                'slug' => 'tenants.edit',
                'description' => 'Edit tenant information',
                'module' => 'Tenant Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Tenants',
                'slug' => 'tenants.delete',
                'description' => 'Delete tenants',
                'module' => 'Tenant Management',
                'is_system' => true,
            ],

            // Room Management
            [
                'name' => 'View Rooms',
                'slug' => 'rooms.view',
                'description' => 'View room list and details',
                'module' => 'Room Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Rooms',
                'slug' => 'rooms.create',
                'description' => 'Create new rooms',
                'module' => 'Room Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Rooms',
                'slug' => 'rooms.edit',
                'description' => 'Edit room information',
                'module' => 'Room Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Rooms',
                'slug' => 'rooms.delete',
                'description' => 'Delete rooms',
                'module' => 'Room Management',
                'is_system' => true,
            ],

            // Billing Management
            [
                'name' => 'View Invoices',
                'slug' => 'invoices.view',
                'description' => 'View invoice list and details',
                'module' => 'Billing Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Invoices',
                'slug' => 'invoices.create',
                'description' => 'Create new invoices',
                'module' => 'Billing Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Invoices',
                'slug' => 'invoices.edit',
                'description' => 'Edit invoice information',
                'module' => 'Billing Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Invoices',
                'slug' => 'invoices.delete',
                'description' => 'Delete invoices',
                'module' => 'Billing Management',
                'is_system' => true,
            ],
            [
                'name' => 'View Payments',
                'slug' => 'payments.view',
                'description' => 'View payment list and details',
                'module' => 'Billing Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Payments',
                'slug' => 'payments.create',
                'description' => 'Create new payments',
                'module' => 'Billing Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Payments',
                'slug' => 'payments.edit',
                'description' => 'Edit payment information',
                'module' => 'Billing Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Payments',
                'slug' => 'payments.delete',
                'description' => 'Delete payments',
                'module' => 'Billing Management',
                'is_system' => true,
            ],

            // Enquiry Management
            [
                'name' => 'View Enquiries',
                'slug' => 'enquiries.view',
                'description' => 'View enquiry list and details',
                'module' => 'Enquiry Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Enquiries',
                'slug' => 'enquiries.edit',
                'description' => 'Edit enquiry information',
                'module' => 'Enquiry Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Enquiries',
                'slug' => 'enquiries.delete',
                'description' => 'Delete enquiries',
                'module' => 'Enquiry Management',
                'is_system' => true,
            ],

            // Amenity Management
            [
                'name' => 'View Amenities',
                'slug' => 'amenities.view',
                'description' => 'View amenity list and details',
                'module' => 'Amenity Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Amenities',
                'slug' => 'amenities.create',
                'description' => 'Create new amenities',
                'module' => 'Amenity Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Amenities',
                'slug' => 'amenities.edit',
                'description' => 'Edit amenity information',
                'module' => 'Amenity Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Amenities',
                'slug' => 'amenities.delete',
                'description' => 'Delete amenities',
                'module' => 'Amenity Management',
                'is_system' => true,
            ],

            // Notification Management
            [
                'name' => 'View Notifications',
                'slug' => 'notifications.view',
                'description' => 'View notification list and details',
                'module' => 'Notification Management',
                'is_system' => true,
            ],
            [
                'name' => 'Create Notifications',
                'slug' => 'notifications.create',
                'description' => 'Create new notifications',
                'module' => 'Notification Management',
                'is_system' => true,
            ],
            [
                'name' => 'Edit Notifications',
                'slug' => 'notifications.edit',
                'description' => 'Edit notification information',
                'module' => 'Notification Management',
                'is_system' => true,
            ],
            [
                'name' => 'Delete Notifications',
                'slug' => 'notifications.delete',
                'description' => 'Delete notifications',
                'module' => 'Notification Management',
                'is_system' => true,
            ],

            // System Configuration
            [
                'name' => 'View System Settings',
                'slug' => 'system.view',
                'description' => 'View system configuration',
                'module' => 'System Configuration',
                'is_system' => true,
            ],
            [
                'name' => 'Edit System Settings',
                'slug' => 'system.edit',
                'description' => 'Edit system configuration',
                'module' => 'System Configuration',
                'is_system' => true,
            ],

            // Dashboard
            [
                'name' => 'View Dashboard',
                'slug' => 'dashboard.view',
                'description' => 'View dashboard and statistics',
                'module' => 'Dashboard',
                'is_system' => true,
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
