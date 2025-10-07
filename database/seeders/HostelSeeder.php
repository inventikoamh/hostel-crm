<?php

namespace Database\Seeders;

use App\Models\Hostel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HostelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hostels = [
            [
                'name' => 'Test Property',
                'description' => 'A modern, comfortable hostel for testing the new BedAssignment system.',
                'address' => '123 Test Street',
                'city' => 'Test City',
                'state' => 'TS',
                'country' => 'Test Country',
                'postal_code' => '12345',
                'phone' => '+1-555-TEST',
                'email' => 'test@testhostel.com',
                'website' => 'https://testhostel.com',
                'amenities' => ['WiFi', 'Laundry', 'Kitchen', 'Common Room', 'Parking', 'Security'],
                'images' => ['test1.jpg', 'test2.jpg', 'test3.jpg'],
                'status' => 'active',
                'manager_name' => 'Test Manager',
                'manager_phone' => '+1-555-TEST',
                'manager_email' => 'manager@testhostel.com',
                'rules' => 'No smoking, No pets, Quiet hours 10 PM - 7 AM, Visitors must be registered',
                'check_in_time' => '14:00:00',
                'check_out_time' => '11:00:00',
            ]
        ];

        foreach ($hostels as $hostelData) {
            Hostel::create($hostelData);
        }
    }
}
