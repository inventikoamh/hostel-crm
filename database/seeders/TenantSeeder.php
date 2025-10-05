<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TenantProfile;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Bed;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo tenant user
        $tenant = User::firstOrCreate(
            ['email' => 'tenant@hostel.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'phone' => '+1234567890',
                'status' => 'active',
                'is_tenant' => true,
                'email_verified_at' => now(),
            ]
        );

        // Get or create a hostel, room, and bed for the tenant
        $hostel = Hostel::first();
        if (!$hostel) {
            $hostel = Hostel::create([
                'name' => 'Demo Hostel',
                'address' => '123 Demo Street, Demo City',
                'phone' => '+1234567890',
                'email' => 'demo@hostel.com',
                'description' => 'A demo hostel for testing purposes',
                'amenities' => json_encode(['WiFi', 'Laundry', 'Kitchen', 'Parking']),
                'status' => 'active',
            ]);
        }

        $room = Room::where('hostel_id', $hostel->id)->first();
        if (!$room) {
            $room = Room::create([
                'hostel_id' => $hostel->id,
                'room_number' => '101',
                'floor' => 1,
                'bed_capacity' => 2,
                'rent_per_bed' => 5000.00,
                'status' => 'active',
            ]);
        }

        $bed = Bed::where('room_id', $room->id)->first();
        if (!$bed) {
            $bed = Bed::create([
                'room_id' => $room->id,
                'bed_number' => 'A',
                'status' => 'available',
            ]);
        }

        // Create tenant profile
        TenantProfile::firstOrCreate(
            ['user_id' => $tenant->id],
            [
                'phone' => '+1234567890',
                'date_of_birth' => '1995-01-15',
                'address' => '123 Demo Street, Demo City',
                'occupation' => 'Software Developer',
                'company' => 'Demo Company',
                'id_proof_type' => 'aadhar',
                'id_proof_number' => '123456789012',
                'emergency_contact_name' => 'Jane Doe',
                'emergency_contact_phone' => '+1234567891',
                'emergency_contact_relation' => 'Sister',
                'status' => 'active',
                'move_in_date' => now()->subDays(30),
                'security_deposit' => 10000.00,
                'monthly_rent' => 5000.00,
                'billing_cycle' => 'monthly',
                'billing_day' => 1,
                'next_billing_date' => now()->addMonth()->startOfMonth(),
                'payment_status' => 'paid',
                'lease_start_date' => now()->subDays(30),
                'lease_end_date' => now()->addMonths(11),
                'notes' => 'Demo tenant for testing purposes',
                'is_verified' => true,
                'verified_at' => now(),
            ]
        );

        // Update bed status to occupied
        $bed->update(['status' => 'occupied']);

        $this->command->info('Demo tenant created successfully!');
        $this->command->info('Email: tenant@hostel.com');
        $this->command->info('Password: password123');
    }
}
