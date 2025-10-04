<?php

namespace Database\Seeders;

use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EnquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $assignedUser = $users->first(); // Assign some enquiries to the first user

        $enquiries = [
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul.sharma@gmail.com',
                'phone' => '+91 9876543210',
                'enquiry_type' => 'room_booking',
                'subject' => 'Room Booking for 3 Months',
                'message' => 'Hi, I am looking for a single occupancy room for 3 months starting from next month. I am a software engineer working remotely and need a quiet place with good internet connectivity. Please let me know about availability and pricing.',
                'status' => 'new',
                'priority' => 'medium',
                'source' => 'website',
                'metadata' => [
                    'preferred_checkin' => Carbon::now()->addDays(15)->format('Y-m-d'),
                    'duration' => '1-3 months',
                    'budget_range' => '₹10,000 - ₹20,000'
                ],
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2)
            ],
            [
                'name' => 'Priya Patel',
                'email' => 'priya.patel@yahoo.com',
                'phone' => '+91 8765432109',
                'enquiry_type' => 'general_info',
                'subject' => 'Hostel Facilities and Location',
                'message' => 'Hello, I am a college student looking for accommodation near the university. Can you please provide information about your facilities, meal options, and distance from the main campus?',
                'status' => 'in_progress',
                'priority' => 'low',
                'assigned_to' => $assignedUser->id,
                'source' => 'website',
                'admin_notes' => 'Sent facility brochure via email. Waiting for response.',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subHours(6)
            ],
            [
                'name' => 'Amit Kumar',
                'email' => 'amit.kumar@hotmail.com',
                'phone' => '+91 7654321098',
                'enquiry_type' => 'pricing',
                'subject' => 'Long-term Stay Pricing',
                'message' => 'I need accommodation for 6 months for my internship. What are your rates for long-term stays? Do you offer any discounts for extended bookings?',
                'status' => 'resolved',
                'priority' => 'medium',
                'assigned_to' => $assignedUser->id,
                'responded_at' => Carbon::now()->subHours(12),
                'source' => 'website',
                'metadata' => [
                    'duration' => '3-6 months',
                    'budget_range' => '₹15,000 - ₹25,000'
                ],
                'admin_notes' => 'Provided pricing details and discount information. Customer confirmed booking.',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subHours(12)
            ],
            [
                'name' => 'Sneha Reddy',
                'email' => 'sneha.reddy@gmail.com',
                'phone' => '+91 6543210987',
                'enquiry_type' => 'facilities',
                'subject' => 'WiFi and Study Area Availability',
                'message' => 'I am preparing for competitive exams and need a place with reliable high-speed internet and dedicated study areas. Do you have such facilities available?',
                'status' => 'new',
                'priority' => 'high',
                'source' => 'website',
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(30)
            ],
            [
                'name' => 'Vikash Singh',
                'email' => 'vikash.singh@outlook.com',
                'phone' => '+91 5432109876',
                'enquiry_type' => 'room_booking',
                'subject' => 'Urgent Room Requirement',
                'message' => 'I need a room urgently as my current accommodation lease is ending this week. Please let me know immediate availability and I can visit today for inspection.',
                'status' => 'new',
                'priority' => 'urgent',
                'source' => 'website',
                'metadata' => [
                    'preferred_checkin' => Carbon::now()->addDays(3)->format('Y-m-d'),
                    'duration' => '1-4 weeks',
                    'budget_range' => '₹5,000 - ₹10,000'
                ],
                'created_at' => Carbon::now()->subHours(1),
                'updated_at' => Carbon::now()->subHours(1)
            ],
            [
                'name' => 'Anita Joshi',
                'email' => 'anita.joshi@gmail.com',
                'phone' => '+91 4321098765',
                'enquiry_type' => 'other',
                'subject' => 'Corporate Accommodation for Team',
                'message' => 'We are a startup looking for accommodation for our team of 5 people for a 2-month project. Do you have group booking options and what would be the rates?',
                'status' => 'in_progress',
                'priority' => 'high',
                'assigned_to' => $assignedUser->id,
                'source' => 'website',
                'admin_notes' => 'Discussed group rates. Preparing custom quote for 5 rooms.',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subHours(4)
            ],
            [
                'name' => 'Rajesh Gupta',
                'email' => 'rajesh.gupta@company.com',
                'phone' => '+91 3210987654',
                'enquiry_type' => 'general_info',
                'subject' => 'Security and Safety Measures',
                'message' => 'My daughter will be staying at your hostel. Can you please provide information about security measures, visiting hours, and safety protocols you have in place?',
                'status' => 'closed',
                'priority' => 'medium',
                'assigned_to' => $assignedUser->id,
                'responded_at' => Carbon::now()->subDays(1),
                'source' => 'phone',
                'admin_notes' => 'Provided detailed security information. Parent satisfied with safety measures.',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(1)
            ],
            [
                'name' => 'Deepika Nair',
                'email' => 'deepika.nair@student.edu',
                'phone' => '+91 2109876543',
                'enquiry_type' => 'pricing',
                'subject' => 'Student Discount Inquiry',
                'message' => 'I am a final year engineering student. Do you offer any student discounts? Also, what are the payment terms for monthly stays?',
                'status' => 'new',
                'priority' => 'low',
                'source' => 'website',
                'created_at' => Carbon::now()->subHours(8),
                'updated_at' => Carbon::now()->subHours(8)
            ]
        ];

        foreach ($enquiries as $enquiryData) {
            Enquiry::create($enquiryData);
        }
    }
}
