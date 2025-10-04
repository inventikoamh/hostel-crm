<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaidAmenity;

class PaidAmenitySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $amenities = [
            // Food & Meals
            [
                'name' => 'Breakfast',
                'description' => 'Daily breakfast service with variety of options including Indian and Continental dishes',
                'billing_type' => 'daily',
                'price' => 80.00,
                'category' => 'food',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5, 6, 0] // All days
                ],
                'max_usage_per_day' => 1,
                'terms_conditions' => 'Breakfast served from 7:00 AM to 10:00 AM. Must be ordered by 9:00 PM previous day.',
                'icon' => 'fas fa-coffee'
            ],
            [
                'name' => 'Lunch',
                'description' => 'Nutritious lunch with rice, dal, vegetables, and roti',
                'billing_type' => 'daily',
                'price' => 120.00,
                'category' => 'food',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5, 6, 0] // All days
                ],
                'max_usage_per_day' => 1,
                'terms_conditions' => 'Lunch served from 12:00 PM to 3:00 PM. Must be ordered by 11:00 AM same day.',
                'icon' => 'fas fa-utensils'
            ],
            [
                'name' => 'Dinner',
                'description' => 'Complete dinner with rice, dal, vegetables, roti, and dessert',
                'billing_type' => 'daily',
                'price' => 150.00,
                'category' => 'food',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5, 6, 0] // All days
                ],
                'max_usage_per_day' => 1,
                'terms_conditions' => 'Dinner served from 7:00 PM to 10:00 PM. Must be ordered by 5:00 PM same day.',
                'icon' => 'fas fa-moon'
            ],
            [
                'name' => 'Monthly Meal Plan',
                'description' => 'Complete meal plan including breakfast, lunch, and dinner for the entire month',
                'billing_type' => 'monthly',
                'price' => 8500.00,
                'category' => 'food',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5, 6, 0] // All days
                ],
                'terms_conditions' => 'Includes all three meals daily. 10% discount compared to daily rates. Non-refundable.',
                'icon' => 'fas fa-calendar-alt'
            ],

            // Cleaning Services
            [
                'name' => 'Room Cleaning',
                'description' => 'Professional room cleaning service including bed making, floor mopping, and dusting',
                'billing_type' => 'daily',
                'price' => 50.00,
                'category' => 'cleaning',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5, 6] // Monday to Saturday
                ],
                'max_usage_per_day' => 1,
                'terms_conditions' => 'Service available from 9:00 AM to 5:00 PM. Must be requested 1 day in advance.',
                'icon' => 'fas fa-broom'
            ],
            [
                'name' => 'Deep Cleaning',
                'description' => 'Comprehensive deep cleaning including bathroom, windows, and detailed room cleaning',
                'billing_type' => 'daily',
                'price' => 200.00,
                'category' => 'cleaning',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [6, 0] // Saturday and Sunday
                ],
                'max_usage_per_day' => 1,
                'terms_conditions' => 'Available on weekends only. Must be booked 3 days in advance. Takes 2-3 hours.',
                'icon' => 'fas fa-spray-can'
            ],
            [
                'name' => 'Monthly Housekeeping',
                'description' => 'Regular housekeeping service 3 times a week including room cleaning and maintenance',
                'billing_type' => 'monthly',
                'price' => 1200.00,
                'category' => 'cleaning',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 3, 5] // Monday, Wednesday, Friday
                ],
                'terms_conditions' => 'Service provided 3 times per week. Includes basic room maintenance and cleaning supplies.',
                'icon' => 'fas fa-home'
            ],

            // Laundry Services
            [
                'name' => 'Laundry Service',
                'description' => 'Washing, drying, and folding service for clothes',
                'billing_type' => 'daily',
                'price' => 30.00,
                'category' => 'laundry',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5, 6] // Monday to Saturday
                ],
                'max_usage_per_day' => 2,
                'terms_conditions' => 'Per kg rate. Minimum 2kg. Delivery within 24 hours. Ironing charged separately.',
                'icon' => 'fas fa-tshirt'
            ],
            [
                'name' => 'Express Laundry',
                'description' => 'Same day laundry service with washing, drying, and basic ironing',
                'billing_type' => 'daily',
                'price' => 60.00,
                'category' => 'laundry',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5] // Monday to Friday
                ],
                'max_usage_per_day' => 1,
                'terms_conditions' => 'Same day service if submitted before 10:00 AM. Per kg rate. Minimum 1kg.',
                'icon' => 'fas fa-clock'
            ],
            [
                'name' => 'Monthly Laundry Package',
                'description' => 'Unlimited laundry service for the entire month including washing, drying, and folding',
                'billing_type' => 'monthly',
                'price' => 800.00,
                'category' => 'laundry',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5, 6] // Monday to Saturday
                ],
                'terms_conditions' => 'Unlimited usage within reasonable limits. Ironing and dry cleaning charged separately.',
                'icon' => 'fas fa-infinity'
            ],

            // Utilities
            [
                'name' => 'Extra Electricity',
                'description' => 'Additional electricity units beyond the basic allocation',
                'billing_type' => 'monthly',
                'price' => 8.00,
                'category' => 'utilities',
                'is_active' => true,
                'terms_conditions' => 'Per unit rate. Calculated based on actual consumption above free limit of 100 units.',
                'icon' => 'fas fa-bolt'
            ],
            [
                'name' => 'High Speed Internet',
                'description' => 'Premium internet connection with higher bandwidth and priority access',
                'billing_type' => 'monthly',
                'price' => 500.00,
                'category' => 'utilities',
                'is_active' => true,
                'terms_conditions' => 'Dedicated 50 Mbps connection. Installation charges may apply for new connections.',
                'icon' => 'fas fa-wifi'
            ],

            // General Services
            [
                'name' => 'Gym Access',
                'description' => 'Access to hostel gym facilities with basic equipment',
                'billing_type' => 'monthly',
                'price' => 300.00,
                'category' => 'services',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5, 6, 0] // All days
                ],
                'terms_conditions' => 'Access from 6:00 AM to 10:00 PM. Must follow gym rules and safety guidelines.',
                'icon' => 'fas fa-dumbbell'
            ],
            [
                'name' => 'Study Room Access',
                'description' => '24/7 access to dedicated study rooms with AC and Wi-Fi',
                'billing_type' => 'monthly',
                'price' => 200.00,
                'category' => 'services',
                'is_active' => true,
                'availability_schedule' => [
                    'days' => [1, 2, 3, 4, 5, 6, 0] // All days
                ],
                'terms_conditions' => '24/7 access with key card. Maintain silence and cleanliness. No food allowed.',
                'icon' => 'fas fa-book'
            ],
            [
                'name' => 'Parking Space',
                'description' => 'Reserved parking space for two-wheeler or four-wheeler',
                'billing_type' => 'monthly',
                'price' => 400.00,
                'category' => 'services',
                'is_active' => true,
                'terms_conditions' => 'Subject to availability. Security not guaranteed for vehicle contents. Register vehicle details.',
                'icon' => 'fas fa-car'
            ],
            [
                'name' => 'Guest Room',
                'description' => 'Guest room booking for visiting family and friends',
                'billing_type' => 'daily',
                'price' => 800.00,
                'category' => 'services',
                'is_active' => true,
                'max_usage_per_day' => 1,
                'terms_conditions' => 'Maximum 3 days per month. Must be booked 2 days in advance. Guest details required.',
                'icon' => 'fas fa-bed'
            ],

            // Other Services
            [
                'name' => 'Courier Service',
                'description' => 'Package receiving and delivery service',
                'billing_type' => 'daily',
                'price' => 25.00,
                'category' => 'other',
                'is_active' => true,
                'max_usage_per_day' => 5,
                'terms_conditions' => 'Per package rate. Storage for maximum 7 days. Tenant responsible for package contents.',
                'icon' => 'fas fa-box'
            ],
            [
                'name' => 'Maintenance Request',
                'description' => 'Priority maintenance and repair service for room issues',
                'billing_type' => 'daily',
                'price' => 100.00,
                'category' => 'other',
                'is_active' => true,
                'max_usage_per_day' => 2,
                'terms_conditions' => 'For non-emergency repairs. Emergency repairs are free. Material costs additional.',
                'icon' => 'fas fa-tools'
            ]
        ];

        foreach ($amenities as $amenity) {
            PaidAmenity::create($amenity);
        }

        $this->command->info('Created ' . count($amenities) . ' paid amenities');
    }
}
