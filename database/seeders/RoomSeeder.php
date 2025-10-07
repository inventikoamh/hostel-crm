<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Bed;
use App\Models\Hostel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hostels = Hostel::all();

        foreach ($hostels as $hostel) {
            // Create 2 floors with 4 rooms each
            $floors = [0, 1]; // Ground floor and 1st floor

            foreach ($floors as $floor) {
                $roomsPerFloor = 4; // 4 rooms per floor

                for ($roomNum = 1; $roomNum <= $roomsPerFloor; $roomNum++) {
                    $roomNumber = 'G' . $roomNum; // G1, G2, G3, G4 for ground floor, 11, 12, 13, 14 for 1st floor
                    if ($floor > 0) {
                        $roomNumber = $floor . $roomNum;
                    }

                    // Create a simple room structure
                    $room = Room::create([
                        'hostel_id' => $hostel->id,
                        'room_number' => $roomNumber,
                        'room_type' => 'dormitory',
                        'floor' => $floor,
                        'capacity' => 4, // 4 beds per room
                        'rent_per_bed' => 5000, // ₹5000 per bed
                        'status' => 'available',
                        'description' => "A comfortable dormitory room on floor {$floor}",
                        'area_sqft' => 120,
                        'has_attached_bathroom' => true,
                        'has_balcony' => false,
                        'has_ac' => true,
                        'is_active' => true
                    ]);

                    // Create 4 beds for each room
                    for ($bedNum = 1; $bedNum <= 4; $bedNum++) {
                        $bedNumber = str_pad($bedNum, 2, '0', STR_PAD_LEFT);

                        // Create beds with different statuses for testing
                        $bedStatus = 'available';
                        if ($roomNumber === 'G1' && $bedNum === 1) {
                            $bedStatus = 'occupied'; // Bed 01 in G1 is occupied
                        } elseif ($roomNumber === 'G1' && $bedNum === 4) {
                            $bedStatus = 'maintenance'; // Bed 04 in G1 is under maintenance
                        }

                        Bed::create([
                            'room_id' => $room->id,
                            'bed_number' => $bedNumber,
                            'bed_type' => 'single',
                            'status' => $bedStatus,
                            'monthly_rent' => 5000,
                            'notes' => $bedStatus === 'maintenance' ? 'Under maintenance - AC repair' : null,
                            'is_active' => true
                        ]);
                    }

                    // Update room status based on bed occupancy
                    $room->updateStatus();
                }
            }
        }
    }
}
