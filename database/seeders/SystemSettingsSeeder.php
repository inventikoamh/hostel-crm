<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default system settings
        $settings = [
            // Demo Mode
            [
                'key' => 'demo_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable demo mode to restrict certain features',
                'is_public' => false,
            ],
            [
                'key' => 'tenant_login_maintenance',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable tenant login maintenance mode',
                'is_public' => false,
            ],

            // System Limits
            [
                'key' => 'max_hostels',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Maximum number of hostels allowed',
                'is_public' => false,
            ],
            [
                'key' => 'max_floors_per_hostel',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Maximum floors per hostel',
                'is_public' => false,
            ],
            [
                'key' => 'max_rooms_per_floor',
                'value' => '20',
                'type' => 'integer',
                'description' => 'Maximum rooms per floor',
                'is_public' => false,
            ],
            [
                'key' => 'max_beds_per_room',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Maximum beds per room',
                'is_public' => false,
            ],

            // Application Settings
            [
                'key' => 'app_name',
                'value' => 'Hostel CRM',
                'type' => 'string',
                'description' => 'Application name',
                'is_public' => true,
            ],
            [
                'key' => 'app_logo',
                'value' => '',
                'type' => 'string',
                'description' => 'Application logo URL',
                'is_public' => true,
            ],
            [
                'key' => 'favicon',
                'value' => '',
                'type' => 'string',
                'description' => 'Favicon URL',
                'is_public' => true,
            ],

            // Theme Settings
            [
                'key' => 'primary_color',
                'value' => '#3B82F6',
                'type' => 'string',
                'description' => 'Primary theme color',
                'is_public' => true,
            ],
            [
                'key' => 'secondary_color',
                'value' => '#6B7280',
                'type' => 'string',
                'description' => 'Secondary theme color',
                'is_public' => true,
            ],

        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // Create a default super admin user if none exists
        if (!User::where('is_super_admin', true)->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@hostelcrm.com',
                'password' => Hash::make('password'),
                'is_super_admin' => true,
                'is_tenant' => false,
            ]);
        }

        $this->command->info('System settings seeded successfully!');
        $this->command->info('Default super admin created: superadmin@hostelcrm.com / password');
    }
}
