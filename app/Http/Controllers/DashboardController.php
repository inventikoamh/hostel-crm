<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\TenantProfile;
use App\Models\Room;
use App\Models\Bed;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Enquiry;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate real statistics from database
        $stats = [
            'total_hostels' => Hostel::count(),
            'total_tenants' => TenantProfile::where('status', 'active')->count(),
            'total_rooms' => Room::count(),
            'total_beds' => Bed::count(),
            'occupied_beds' => Bed::where('status', 'occupied')->count(),
            'available_beds' => Bed::where('status', 'available')->count(),
            'occupancy_rate' => $this->calculateOccupancyRate(),
            'monthly_revenue' => $this->calculateMonthlyRevenue(),
            'pending_enquiries' => Enquiry::where('status', 'pending')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            'total_invoices' => Invoice::count(),
            'total_payments' => Payment::where('status', 'verified')->count()
        ];

        // Get recent activities from database
        $recent_activities = $this->getRecentActivities();

        // Get upcoming events and important dates
        $upcoming_events = $this->getUpcomingEvents();

        // Get quick stats for charts
        $chart_data = $this->getChartData();

        return view('dashboard.index', compact('stats', 'recent_activities', 'upcoming_events', 'chart_data'));
    }

    private function calculateOccupancyRate()
    {
        $totalBeds = Bed::count();
        $occupiedBeds = Bed::where('status', 'occupied')->count();

        return $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
    }

    private function calculateMonthlyRevenue()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return Payment::where('status', 'verified')
                     ->whereBetween('created_at', [$currentMonth, $endOfMonth])
                     ->sum('amount');
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Recent tenant registrations
        $recentTenants = TenantProfile::with('user')
                              ->where('created_at', '>=', Carbon::now()->subDays(7))
                              ->orderBy('created_at', 'desc')
                              ->limit(3)
                              ->get();

        foreach ($recentTenants as $tenant) {
            $activities->push([
                'type' => 'tenant_registration',
                'icon' => 'fas fa-user-plus',
                'icon_color' => 'text-green-600',
                'icon_bg' => 'bg-green-100',
                'title' => 'New Tenant Registered',
                'description' => $tenant->user->name . ' joined the hostel',
                'time' => $tenant->created_at->diffForHumans(),
                'timestamp' => $tenant->created_at
            ]);
        }

        // Recent payments
        $recentPayments = Payment::with('tenantProfile.user')
                                ->where('status', 'verified')
                                ->where('created_at', '>=', Carbon::now()->subDays(7))
                                ->orderBy('created_at', 'desc')
                                ->limit(3)
                                ->get();

        foreach ($recentPayments as $payment) {
            $activities->push([
                'type' => 'payment_received',
                'icon' => 'fas fa-credit-card',
                'icon_color' => 'text-blue-600',
                'icon_bg' => 'bg-blue-100',
                'title' => 'Payment Received',
                'description' => '₹' . number_format((float)$payment->amount) . ' from ' . ($payment->tenantProfile->user->name ?? 'Unknown Tenant'),
                'time' => $payment->created_at->diffForHumans(),
                'timestamp' => $payment->created_at
            ]);
        }

        // Recent enquiries
        $recentEnquiries = Enquiry::where('created_at', '>=', Carbon::now()->subDays(7))
                                 ->orderBy('created_at', 'desc')
                                 ->limit(3)
                                 ->get();

        foreach ($recentEnquiries as $enquiry) {
            $activities->push([
                'type' => 'enquiry_received',
                'icon' => 'fas fa-envelope',
                'icon_color' => 'text-purple-600',
                'icon_bg' => 'bg-purple-100',
                'title' => 'New Enquiry',
                'description' => 'From ' . ($enquiry->name ?? 'Unknown') . ' - ' . ($enquiry->subject ?? 'General Enquiry'),
                'time' => $enquiry->created_at->diffForHumans(),
                'timestamp' => $enquiry->created_at
            ]);
        }

        return $activities->sortByDesc('timestamp')->take(10)->values();
    }

    private function getUpcomingEvents()
    {
        $events = collect();

        // Upcoming rent due dates
        $upcomingRentDue = Invoice::where('status', 'pending')
                                 ->where('due_date', '>=', Carbon::now())
                                 ->where('due_date', '<=', Carbon::now()->addDays(7))
                                 ->count();

        if ($upcomingRentDue > 0) {
            $events->push([
                'type' => 'rent_due',
                'icon' => 'fas fa-calendar-alt',
                'icon_color' => 'text-red-600',
                'icon_bg' => 'bg-red-100',
                'title' => 'Rent Due Soon',
                'description' => $upcomingRentDue . ' invoices due within 7 days',
                'date' => Carbon::now()->addDays(3)->format('M d'),
                'priority' => 'high'
            ]);
        }

        // Pending enquiries
        $pendingEnquiries = Enquiry::where('status', 'pending')->count();
        if ($pendingEnquiries > 0) {
            $events->push([
                'type' => 'pending_enquiries',
                'icon' => 'fas fa-question-circle',
                'icon_color' => 'text-yellow-600',
                'icon_bg' => 'bg-yellow-100',
                'title' => 'Pending Enquiries',
                'description' => $pendingEnquiries . ' enquiries need attention',
                'date' => 'Today',
                'priority' => 'medium'
            ]);
        }

        // Overdue payments
        $overduePayments = Invoice::where('status', 'overdue')->count();
        if ($overduePayments > 0) {
            $events->push([
                'type' => 'overdue_payments',
                'icon' => 'fas fa-exclamation-triangle',
                'icon_color' => 'text-red-600',
                'icon_bg' => 'bg-red-100',
                'title' => 'Overdue Payments',
                'description' => $overduePayments . ' payments are overdue',
                'date' => 'Urgent',
                'priority' => 'high'
            ]);
        }

        return $events->sortBy('priority')->values();
    }

    private function getChartData()
    {
        // Monthly revenue for the last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Payment::where('status', 'verified')
                             ->whereYear('created_at', $month->year)
                             ->whereMonth('created_at', $month->month)
                             ->sum('amount');

            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Occupancy rate for the last 6 months
        $monthlyOccupancy = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $totalBeds = Bed::count();
            $occupiedBeds = Bed::where('status', 'occupied')
                              ->where('updated_at', '<=', $month->endOfMonth())
                              ->count();

            $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;

            $monthlyOccupancy[] = [
                'month' => $month->format('M Y'),
                'occupancy' => $occupancyRate
            ];
        }

        return [
            'monthly_revenue' => $monthlyRevenue,
            'monthly_occupancy' => $monthlyOccupancy
        ];
    }
}
