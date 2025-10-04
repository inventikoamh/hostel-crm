<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mock data for the dashboard
        $stats = [
            'total_rooms' => 45,
            'occupied_rooms' => 32,
            'available_rooms' => 13,
            'total_students' => 128,
            'monthly_revenue' => 125000,
            'pending_requests' => 8
        ];

        $recent_activities = [
            [
                'type' => 'check_in',
                'student' => 'John Doe',
                'room' => 'A-101',
                'time' => '2 hours ago'
            ],
            [
                'type' => 'payment',
                'student' => 'Jane Smith',
                'amount' => 15000,
                'time' => '4 hours ago'
            ],
            [
                'type' => 'maintenance',
                'room' => 'B-205',
                'issue' => 'Water leak',
                'time' => '6 hours ago'
            ],
            [
                'type' => 'check_out',
                'student' => 'Mike Johnson',
                'room' => 'C-301',
                'time' => '1 day ago'
            ]
        ];

        $upcoming_events = [
            [
                'title' => 'Monthly Maintenance',
                'date' => '2024-01-15',
                'type' => 'maintenance'
            ],
            [
                'title' => 'Student Orientation',
                'date' => '2024-01-20',
                'type' => 'event'
            ],
            [
                'title' => 'Rent Collection',
                'date' => '2024-01-25',
                'type' => 'payment'
            ]
        ];

        return view('dashboard.index', compact('stats', 'recent_activities', 'upcoming_events'));
    }
}
