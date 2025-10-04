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
            // Create rooms for each floor
            $floors = [1, 2, 3]; // 3 floors per hostel

            foreach ($floors as $floor) {
                $roomsPerFloor = 8; // 8 rooms per floor

                for ($roomNum = 1; $roomNum <= $roomsPerFloor; $roomNum++) {
                    $roomNumber = str_pad($floor . str_pad($roomNum, 2, '0', STR_PAD_LEFT), 3, '0', STR_PAD_LEFT);

                    // Vary room types and capacities
                    $roomTypes = ['single', 'double', 'triple', 'dormitory'];
                    $roomType = $roomTypes[array_rand($roomTypes)];

                    $capacity = match($roomType) {
                        'single' => 1,
                        'double' => 2,
                        'triple' => 3,
                        'dormitory' => rand(4, 6),
                        default => 2
                    };

                    $room = Room::create([
                        'hostel_id' => $hostel->id,
                        'room_number' => $roomNumber,
                        'room_type' => $roomType,
                        'floor' => $floor,
                        'capacity' => $capacity,
                        'rent_per_bed' => rand(3000, 8000),
                        'status' => ['available', 'occupied', 'maintenance'][array_rand(['available', 'occupied', 'maintenance'])],
                        'description' => "A comfortable {$roomType} room on floor {$floor}",
                        'area_sqft' => rand(80, 200),
                        'has_attached_bathroom' => rand(0, 1),
                        'has_balcony' => rand(0, 1),
                        'has_ac' => rand(0, 1),
                        'is_active' => true
                    ]);

                    // Create beds for the room
                    for ($bedNum = 1; $bedNum <= $capacity; $bedNum++) {
                        $bedNumber = str_pad($bedNum, 2, '0', STR_PAD_LEFT);

                        // Determine bed type
                        $bedType = 'single';
                        if ($roomType === 'dormitory' && $capacity > 4) {
                            $bedType = ($bedNum % 2 === 1) ? 'bunk_bottom' : 'bunk_top';
                        } elseif ($roomType === 'double' && $capacity === 2) {
                            $bedType = 'single';
                        }

                        // Random bed status
                        $bedStatuses = ['available', 'occupied', 'maintenance', 'reserved'];
                        $bedStatus = $bedStatuses[array_rand($bedStatuses)];

                        // If room is maintenance, all beds should be maintenance
                        if ($room->status === 'maintenance') {
                            $bedStatus = 'maintenance';
                        }

                        Bed::create([
                            'room_id' => $room->id,
                            'bed_number' => $bedNumber,
                            'bed_type' => $bedType,
                            'status' => $bedStatus,
                            'tenant_id' => null, // We'll assign tenants later if needed
                            'occupied_from' => $bedStatus === 'occupied' ? now()->subDays(rand(1, 90)) : null,
                            'occupied_until' => $bedStatus === 'occupied' ? now()->addDays(rand(30, 365)) : null,
                            'monthly_rent' => $bedStatus === 'occupied' ? $room->rent_per_bed : null,
                            'notes' => $bedStatus === 'maintenance' ? 'Under maintenance - ' . ['AC repair', 'Plumbing issue', 'Painting work', 'Furniture repair'][array_rand(['AC repair', 'Plumbing issue', 'Painting work', 'Furniture repair'])] : null,
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
