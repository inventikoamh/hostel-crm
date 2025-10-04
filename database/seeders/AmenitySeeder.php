<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            [
                'name' => 'WiFi',
                'icon' => 'fas fa-wifi',
                'description' => 'High-speed wireless internet access',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Laundry',
                'icon' => 'fas fa-tshirt',
                'description' => 'Washing and drying facilities',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Kitchen',
                'icon' => 'fas fa-utensils',
                'description' => 'Shared kitchen facilities for cooking',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Common Room',
                'icon' => 'fas fa-couch',
                'description' => 'Shared living space for relaxation',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Parking',
                'icon' => 'fas fa-parking',
                'description' => 'Vehicle parking facilities',
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Security',
                'icon' => 'fas fa-shield-alt',
                'description' => '24/7 security and surveillance',
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Gym',
                'icon' => 'fas fa-dumbbell',
                'description' => 'Fitness center and exercise equipment',
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'name' => 'Study Room',
                'icon' => 'fas fa-book',
                'description' => 'Quiet study areas and workspaces',
                'is_active' => true,
                'sort_order' => 8
            ],
            [
                'name' => 'Cafeteria',
                'icon' => 'fas fa-coffee',
                'description' => 'On-site dining and food services',
                'is_active' => true,
                'sort_order' => 9
            ],
            [
                'name' => 'Library',
                'icon' => 'fas fa-book-open',
                'description' => 'Reading room and book collection',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'name' => 'Cleaning Service',
                'icon' => 'fas fa-broom',
                'description' => 'Regular housekeeping services',
                'is_active' => true,
                'sort_order' => 11
            ],
            [
                'name' => 'Air Conditioning',
                'icon' => 'fas fa-snowflake',
                'description' => 'Climate control and cooling',
                'is_active' => true,
                'sort_order' => 12
            ],
            [
                'name' => 'Garden',
                'icon' => 'fas fa-leaf',
                'description' => 'Outdoor garden and green spaces',
                'is_active' => true,
                'sort_order' => 13
            ],
            [
                'name' => 'Co-working Space',
                'icon' => 'fas fa-laptop',
                'description' => 'Professional workspace for remote work',
                'is_active' => true,
                'sort_order' => 14
            ],
            [
                'name' => 'High-speed Internet',
                'icon' => 'fas fa-ethernet',
                'description' => 'Dedicated high-speed internet connection',
                'is_active' => true,
                'sort_order' => 15
            ]
        ];

        foreach ($amenities as $amenityData) {
            Amenity::create($amenityData);
        }
    }
}
