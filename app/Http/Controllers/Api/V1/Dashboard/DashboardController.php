<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hostel;
use App\Models\TenantProfile;
use App\Models\Room;
use App\Models\Bed;
use App\Models\BedAssignment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Amenity;
use App\Models\PaidAmenity;
use App\Models\TenantAmenity;
use App\Models\TenantAmenityUsage;
use App\Models\Enquiry;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get comprehensive dashboard overview
     */
    public function overview(Request $request): JsonResponse
    {
        try {
            $overview = [
                'summary' => $this->getSummaryStats(),
                'recent_activity' => $this->getRecentActivity(),
                'quick_stats' => $this->getQuickStats(),
                'alerts' => $this->getAlerts(),
                'charts' => $this->getChartData(),
                'performance_metrics' => $this->getPerformanceMetrics(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Dashboard overview retrieved successfully',
                'data' => $overview
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard overview',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get financial dashboard data
     */
    public function financial(Request $request): JsonResponse
    {
        try {
            $financial = [
                'revenue_summary' => $this->getRevenueSummary(),
                'payment_analytics' => $this->getPaymentAnalytics(),
                'invoice_analytics' => $this->getInvoiceAnalytics(),
                'outstanding_amounts' => $this->getOutstandingAmounts(),
                'monthly_trends' => $this->getMonthlyFinancialTrends(),
                'payment_methods' => $this->getPaymentMethodBreakdown(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Financial dashboard data retrieved successfully',
                'data' => $financial
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve financial dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get occupancy dashboard data
     */
    public function occupancy(Request $request): JsonResponse
    {
        try {
            $occupancy = [
                'occupancy_summary' => $this->getOccupancySummary(),
                'hostel_occupancy' => $this->getHostelOccupancy(),
                'room_occupancy' => $this->getRoomOccupancy(),
                'bed_assignments' => $this->getBedAssignmentStats(),
                'move_ins_outs' => $this->getMoveInOutStats(),
                'lease_expirations' => $this->getLeaseExpirationStats(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Occupancy dashboard data retrieved successfully',
                'data' => $occupancy
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve occupancy dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tenant analytics dashboard
     */
    public function tenants(Request $request): JsonResponse
    {
        try {
            $tenants = [
                'tenant_summary' => $this->getTenantSummary(),
                'tenant_status' => $this->getTenantStatusBreakdown(),
                'tenant_demographics' => $this->getTenantDemographics(),
                'tenant_satisfaction' => $this->getTenantSatisfactionMetrics(),
                'tenant_retention' => $this->getTenantRetentionMetrics(),
                'tenant_communication' => $this->getTenantCommunicationStats(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Tenant analytics dashboard data retrieved successfully',
                'data' => $tenants
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant analytics dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get amenity usage dashboard
     */
    public function amenities(Request $request): JsonResponse
    {
        try {
            $amenities = [
                'amenity_summary' => $this->getAmenitySummary(),
                'usage_analytics' => $this->getAmenityUsageAnalytics(),
                'revenue_breakdown' => $this->getAmenityRevenueBreakdown(),
                'popular_amenities' => $this->getPopularAmenities(),
                'subscription_trends' => $this->getSubscriptionTrends(),
                'usage_patterns' => $this->getUsagePatterns(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Amenity usage dashboard data retrieved successfully',
                'data' => $amenities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve amenity usage dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get enquiry analytics dashboard
     */
    public function enquiries(Request $request): JsonResponse
    {
        try {
            $enquiries = [
                'enquiry_summary' => $this->getEnquirySummary(),
                'conversion_analytics' => $this->getEnquiryConversionAnalytics(),
                'source_analytics' => $this->getEnquirySourceAnalytics(),
                'response_analytics' => $this->getEnquiryResponseAnalytics(),
                'priority_breakdown' => $this->getEnquiryPriorityBreakdown(),
                'trend_analysis' => $this->getEnquiryTrendAnalysis(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Enquiry analytics dashboard data retrieved successfully',
                'data' => $enquiries
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve enquiry analytics dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification analytics dashboard
     */
    public function notifications(Request $request): JsonResponse
    {
        try {
            $notifications = [
                'notification_summary' => $this->getNotificationSummary(),
                'delivery_analytics' => $this->getNotificationDeliveryAnalytics(),
                'type_breakdown' => $this->getNotificationTypeBreakdown(),
                'success_rates' => $this->getNotificationSuccessRates(),
                'retry_analytics' => $this->getNotificationRetryAnalytics(),
                'trend_analysis' => $this->getNotificationTrendAnalysis(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Notification analytics dashboard data retrieved successfully',
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notification analytics dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system health dashboard
     */
    public function systemHealth(Request $request): JsonResponse
    {
        try {
            $systemHealth = [
                'system_summary' => $this->getSystemSummary(),
                'user_activity' => $this->getUserActivityStats(),
                'database_stats' => $this->getDatabaseStats(),
                'performance_metrics' => $this->getSystemPerformanceMetrics(),
                'error_logs' => $this->getErrorLogStats(),
                'maintenance_alerts' => $this->getMaintenanceAlerts(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'System health dashboard data retrieved successfully',
                'data' => $systemHealth
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve system health dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get custom dashboard widgets
     */
    public function widgets(Request $request): JsonResponse
    {
        try {
            $widgets = [
                'available_widgets' => $this->getAvailableWidgets(),
                'user_widgets' => $this->getUserWidgets($request),
                'widget_data' => $this->getWidgetData($request),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Dashboard widgets retrieved successfully',
                'data' => $widgets
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard widgets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary statistics
     */
    private function getSummaryStats(): array
    {
        return [
            'total_hostels' => Hostel::count(),
            'total_rooms' => Room::count(),
            'total_beds' => Bed::count(),
            'total_tenants' => TenantProfile::count(),
            'total_users' => User::count(),
            'total_invoices' => Invoice::count(),
            'total_payments' => Payment::count(),
            'total_enquiries' => Enquiry::count(),
            'total_notifications' => Notification::count(),
            'active_tenants' => TenantProfile::where('status', 'active')->count(),
            'occupied_beds' => BedAssignment::where('status', 'active')->count(),
            'pending_enquiries' => Enquiry::where('status', 'new')->count(),
        ];
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity(): array
    {
        $activities = [];

        // Recent tenant registrations
        $recentTenants = TenantProfile::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($tenant) {
                return [
                    'type' => 'tenant_registered',
                    'message' => "New tenant registered: {$tenant->user->name}",
                    'timestamp' => $tenant->created_at,
                    'data' => ['tenant_id' => $tenant->id]
                ];
            });

        // Recent enquiries
        $recentEnquiries = Enquiry::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($enquiry) {
                return [
                    'type' => 'enquiry_received',
                    'message' => "New enquiry from {$enquiry->name}: {$enquiry->subject}",
                    'timestamp' => $enquiry->created_at,
                    'data' => ['enquiry_id' => $enquiry->id]
                ];
            });

        // Recent payments
        $recentPayments = Payment::with('tenantProfile.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'type' => 'payment_received',
                    'message' => "Payment received from {$payment->tenantProfile->user->name}: ₹{$payment->amount}",
                    'timestamp' => $payment->created_at,
                    'data' => ['payment_id' => $payment->id]
                ];
            });

        $activities = collect()
            ->merge($recentTenants)
            ->merge($recentEnquiries)
            ->merge($recentPayments)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values()
            ->toArray();

        return $activities;
    }

    /**
     * Get quick statistics
     */
    private function getQuickStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today' => [
                'new_enquiries' => Enquiry::whereDate('created_at', $today)->count(),
                'new_tenants' => TenantProfile::whereDate('created_at', $today)->count(),
                'payments_received' => Payment::whereDate('created_at', $today)->sum('amount'),
                'notifications_sent' => Notification::whereDate('sent_at', $today)->count(),
            ],
            'this_month' => [
                'new_enquiries' => Enquiry::where('created_at', '>=', $thisMonth)->count(),
                'new_tenants' => TenantProfile::where('created_at', '>=', $thisMonth)->count(),
                'payments_received' => Payment::where('created_at', '>=', $thisMonth)->sum('amount'),
                'notifications_sent' => Notification::where('sent_at', '>=', $thisMonth)->count(),
            ],
            'occupancy_rate' => $this->calculateOverallOccupancyRate(),
            'revenue_growth' => $this->calculateRevenueGrowth(),
        ];
    }

    /**
     * Get alerts and notifications
     */
    private function getAlerts(): array
    {
        $alerts = [];

        // Overdue payments (based on invoice due dates)
        $overduePayments = Payment::where('status', 'pending')
            ->whereHas('invoice', function ($query) {
                $query->where('due_date', '<', now());
            })
            ->count();

        if ($overduePayments > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Overdue Payments',
                'message' => "{$overduePayments} payments are overdue",
                'action' => 'view_overdue_payments'
            ];
        }

        // Pending enquiries
        $pendingEnquiries = Enquiry::where('status', 'new')
            ->where('created_at', '<', now()->subHours(24))
            ->count();

        if ($pendingEnquiries > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Pending Enquiries',
                'message' => "{$pendingEnquiries} enquiries need attention",
                'action' => 'view_pending_enquiries'
            ];
        }

        // Failed notifications
        $failedNotifications = Notification::where('status', 'failed')
            ->where('retry_count', '>=', 3)
            ->count();

        if ($failedNotifications > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Failed Notifications',
                'message' => "{$failedNotifications} notifications failed to send",
                'action' => 'view_failed_notifications'
            ];
        }

        return $alerts;
    }

    /**
     * Get chart data
     */
    private function getChartData(): array
    {
        return [
            'revenue_chart' => $this->getRevenueChartData(),
            'occupancy_chart' => $this->getOccupancyChartData(),
            'enquiry_chart' => $this->getEnquiryChartData(),
            'payment_chart' => $this->getPaymentChartData(),
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(): array
    {
        return [
            'enquiry_response_rate' => $this->calculateEnquiryResponseRate(),
            'payment_collection_rate' => $this->calculatePaymentCollectionRate(),
            'tenant_satisfaction_score' => $this->calculateTenantSatisfactionScore(),
            'system_uptime' => $this->calculateSystemUptime(),
        ];
    }

    /**
     * Get revenue summary
     */
    private function getRevenueSummary(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $thisMonthRevenue = Payment::where('status', 'verified')
            ->where('created_at', '>=', $thisMonth)
            ->sum('amount');

        $lastMonthRevenue = Payment::where('status', 'verified')
            ->whereBetween('created_at', [$lastMonth, $thisMonth])
            ->sum('amount');

        $growthRate = $lastMonthRevenue > 0 
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        return [
            'this_month' => $thisMonthRevenue,
            'last_month' => $lastMonthRevenue,
            'growth_rate' => round($growthRate, 2),
            'total_revenue' => Payment::where('status', 'verified')->sum('amount'),
            'average_monthly_revenue' => $this->calculateAverageMonthlyRevenue(),
        ];
    }

    /**
     * Get payment analytics
     */
    private function getPaymentAnalytics(): array
    {
        return [
            'total_payments' => Payment::count(),
            'verified_payments' => Payment::where('status', 'verified')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'cancelled_payments' => Payment::where('status', 'cancelled')->count(),
            'total_amount' => Payment::where('status', 'verified')->sum('amount'),
            'average_payment' => Payment::where('status', 'verified')->avg('amount'),
            'payment_methods' => Payment::select('payment_method', DB::raw('count(*) as count'))
                ->groupBy('payment_method')
                ->get()
                ->pluck('count', 'payment_method')
                ->toArray(),
        ];
    }

    /**
     * Get invoice analytics
     */
    private function getInvoiceAnalytics(): array
    {
        return [
            'total_invoices' => Invoice::count(),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            'total_amount' => Invoice::sum('total_amount'),
            'paid_amount' => Invoice::sum('paid_amount'),
            'outstanding_amount' => Invoice::sum('total_amount') - Invoice::sum('paid_amount'),
            'average_invoice_amount' => Invoice::avg('total_amount'),
        ];
    }

    /**
     * Get outstanding amounts
     */
    private function getOutstandingAmounts(): array
    {
        return [
            'total_outstanding' => Invoice::sum('total_amount') - Invoice::sum('paid_amount'),
            'overdue_amount' => Invoice::where('status', 'overdue')
                ->sum(DB::raw('total_amount - paid_amount')),
            'pending_amount' => Invoice::where('status', 'pending')
                ->sum(DB::raw('total_amount - paid_amount')),
            'by_tenant' => Invoice::with('tenantProfile.user')
                ->select('tenant_profile_id', DB::raw('SUM(total_amount - paid_amount) as outstanding'))
                ->groupBy('tenant_profile_id')
                ->having('outstanding', '>', 0)
                ->get()
                ->map(function ($invoice) {
                    return [
                        'tenant_name' => $invoice->tenantProfile->user->name ?? 'Unknown',
                        'outstanding_amount' => $invoice->outstanding
                    ];
                }),
        ];
    }

    /**
     * Get monthly financial trends
     */
    private function getMonthlyFinancialTrends(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $month->format('M Y'),
                'revenue' => Payment::where('status', 'verified')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount'),
                'invoices' => Invoice::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        return $months;
    }

    /**
     * Get payment method breakdown
     */
    private function getPaymentMethodBreakdown(): array
    {
        return Payment::where('status', 'verified')
            ->select('payment_method', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->map(function ($payment) {
                return [
                    'method' => $payment->payment_method,
                    'total_amount' => $payment->total_amount,
                    'count' => $payment->count,
                    'percentage' => 0 // Will be calculated on frontend
                ];
            })
            ->toArray();
    }

    /**
     * Get occupancy summary
     */
    private function getOccupancySummary(): array
    {
        $totalBeds = Bed::count();
        $occupiedBeds = BedAssignment::where('status', 'active')->count();
        $occupancyRate = $totalBeds > 0 ? ($occupiedBeds / $totalBeds) * 100 : 0;

        return [
            'total_beds' => $totalBeds,
            'occupied_beds' => $occupiedBeds,
            'available_beds' => $totalBeds - $occupiedBeds,
            'occupancy_rate' => round($occupancyRate, 2),
            'total_rooms' => Room::count(),
            'occupied_rooms' => Room::whereHas('beds.assignments', function ($query) {
                $query->where('status', 'active');
            })->count(),
        ];
    }

    /**
     * Get hostel occupancy
     */
    private function getHostelOccupancy(): array
    {
        return Hostel::with(['rooms.beds.assignments' => function ($query) {
            $query->where('status', 'active');
        }])
        ->get()
        ->map(function ($hostel) {
            $totalBeds = $hostel->rooms->sum(function ($room) {
                return $room->beds->count();
            });
            $occupiedBeds = $hostel->rooms->sum(function ($room) {
                return $room->beds->sum(function ($bed) {
                    return $bed->assignments->count();
                });
            });
            $occupancyRate = $totalBeds > 0 ? ($occupiedBeds / $totalBeds) * 100 : 0;

            return [
                'hostel_id' => $hostel->id,
                'hostel_name' => $hostel->name,
                'total_beds' => $totalBeds,
                'occupied_beds' => $occupiedBeds,
                'occupancy_rate' => round($occupancyRate, 2),
            ];
        })
        ->toArray();
    }

    /**
     * Get room occupancy
     */
    private function getRoomOccupancy(): array
    {
        return Room::with(['beds.assignments' => function ($query) {
            $query->where('status', 'active');
        }])
        ->get()
        ->map(function ($room) {
            $totalBeds = $room->beds->count();
            $occupiedBeds = $room->beds->sum(function ($bed) {
                return $bed->assignments->count();
            });
            $occupancyRate = $totalBeds > 0 ? ($occupiedBeds / $totalBeds) * 100 : 0;

            return [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'hostel_name' => $room->hostel->name,
                'total_beds' => $totalBeds,
                'occupied_beds' => $occupiedBeds,
                'occupancy_rate' => round($occupancyRate, 2),
            ];
        })
        ->toArray();
    }

    /**
     * Get bed assignment statistics
     */
    private function getBedAssignmentStats(): array
    {
        return [
            'total_assignments' => BedAssignment::count(),
            'active_assignments' => BedAssignment::where('status', 'active')->count(),
            'completed_assignments' => BedAssignment::where('status', 'completed')->count(),
            'cancelled_assignments' => BedAssignment::where('status', 'cancelled')->count(),
            'average_duration' => $this->calculateAverageAssignmentDuration(),
        ];
    }

    /**
     * Get move in/out statistics
     */
    private function getMoveInOutStats(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'this_month' => [
                'move_ins' => TenantProfile::where('move_in_date', '>=', $thisMonth)->count(),
                'move_outs' => TenantProfile::where('move_out_date', '>=', $thisMonth)->count(),
            ],
            'last_month' => [
                'move_ins' => TenantProfile::whereBetween('move_in_date', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    $thisMonth
                ])->count(),
                'move_outs' => TenantProfile::whereBetween('move_out_date', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    $thisMonth
                ])->count(),
            ],
        ];
    }

    /**
     * Get lease expiration statistics
     */
    private function getLeaseExpirationStats(): array
    {
        $next30Days = Carbon::now()->addDays(30);
        $next7Days = Carbon::now()->addDays(7);

        return [
            'expiring_in_7_days' => TenantProfile::where('lease_end_date', '<=', $next7Days)
                ->where('lease_end_date', '>', now())
                ->count(),
            'expiring_in_30_days' => TenantProfile::where('lease_end_date', '<=', $next30Days)
                ->where('lease_end_date', '>', now())
                ->count(),
            'expired_leases' => TenantProfile::where('lease_end_date', '<', now())
                ->whereNull('move_out_date')
                ->count(),
        ];
    }

    /**
     * Get tenant summary
     */
    private function getTenantSummary(): array
    {
        return [
            'total_tenants' => TenantProfile::count(),
            'active_tenants' => TenantProfile::where('status', 'active')->count(),
            'pending_tenants' => TenantProfile::where('status', 'pending')->count(),
            'inactive_tenants' => TenantProfile::where('status', 'inactive')->count(),
            'verified_tenants' => TenantProfile::where('is_verified', true)->count(),
            'unverified_tenants' => TenantProfile::where('is_verified', false)->count(),
        ];
    }

    /**
     * Get tenant status breakdown
     */
    private function getTenantStatusBreakdown(): array
    {
        return TenantProfile::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get tenant demographics
     */
    private function getTenantDemographics(): array
    {
        return [
            'age_groups' => $this->getTenantAgeGroups(),
            'occupations' => $this->getTenantOccupations(),
            'companies' => $this->getTenantCompanies(),
        ];
    }

    /**
     * Get tenant satisfaction metrics
     */
    private function getTenantSatisfactionMetrics(): array
    {
        // This would typically come from surveys or feedback systems
        // For now, we'll use placeholder data
        return [
            'overall_satisfaction' => 4.2,
            'response_rate' => 75.5,
            'satisfaction_trend' => 'increasing',
            'common_concerns' => [
                'WiFi connectivity',
                'Room maintenance',
                'Noise levels',
            ],
        ];
    }

    /**
     * Get tenant retention metrics
     */
    private function getTenantRetentionMetrics(): array
    {
        $totalTenants = TenantProfile::count();
        $retainedTenants = TenantProfile::where('status', 'active')
            ->where('created_at', '<', Carbon::now()->subMonths(6))
            ->count();

        return [
            'retention_rate' => $totalTenants > 0 ? ($retainedTenants / $totalTenants) * 100 : 0,
            'average_tenancy_duration' => $this->calculateAverageTenancyDuration(),
            'churn_rate' => $this->calculateChurnRate(),
        ];
    }

    /**
     * Get tenant communication statistics
     */
    private function getTenantCommunicationStats(): array
    {
        return [
            'total_notifications' => Notification::where('notifiable_type', 'App\\Models\\TenantProfile')->count(),
            'sent_notifications' => Notification::where('notifiable_type', 'App\\Models\\TenantProfile')
                ->where('status', 'sent')
                ->count(),
            'failed_notifications' => Notification::where('notifiable_type', 'App\\Models\\TenantProfile')
                ->where('status', 'failed')
                ->count(),
            'average_response_time' => $this->calculateAverageNotificationResponseTime(),
        ];
    }

    /**
     * Get amenity summary
     */
    private function getAmenitySummary(): array
    {
        return [
            'total_amenities' => Amenity::count(),
            'total_paid_amenities' => PaidAmenity::count(),
            'active_subscriptions' => TenantAmenity::where('status', 'active')->count(),
            'total_usage_records' => TenantAmenityUsage::count(),
            'amenity_revenue' => TenantAmenityUsage::sum('total_amount'),
        ];
    }

    /**
     * Get amenity usage analytics
     */
    private function getAmenityUsageAnalytics(): array
    {
        return [
            'daily_usage' => TenantAmenityUsage::whereDate('usage_date', Carbon::today())->count(),
            'monthly_usage' => TenantAmenityUsage::whereMonth('usage_date', Carbon::now()->month)->count(),
            'average_usage_per_tenant' => $this->calculateAverageUsagePerTenant(),
            'peak_usage_hours' => $this->getPeakUsageHours(),
        ];
    }

    /**
     * Get amenity revenue breakdown
     */
    private function getAmenityRevenueBreakdown(): array
    {
        return PaidAmenity::with('tenantAmenities')
            ->get()
            ->map(function ($amenity) {
                $revenue = $amenity->tenantAmenities->sum(function ($subscription) {
                    return $subscription->usageRecords->sum('total_amount');
                });

                return [
                    'amenity_name' => $amenity->name,
                    'revenue' => $revenue,
                    'subscribers' => $amenity->tenantAmenities->count(),
                ];
            })
            ->sortByDesc('revenue')
            ->values()
            ->toArray();
    }

    /**
     * Get popular amenities
     */
    private function getPopularAmenities(): array
    {
        return PaidAmenity::withCount('tenantAmenities')
            ->orderBy('tenant_amenities_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($amenity) {
                return [
                    'amenity_name' => $amenity->name,
                    'subscribers' => $amenity->tenant_amenities_count,
                    'revenue' => $amenity->tenantAmenities->sum(function ($subscription) {
                        return $subscription->usageRecords->sum('total_amount');
                    }),
                ];
            })
            ->toArray();
    }

    /**
     * Get subscription trends
     */
    private function getSubscriptionTrends(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $month->format('M Y'),
                'new_subscriptions' => TenantAmenity::whereYear('start_date', $month->year)
                    ->whereMonth('start_date', $month->month)
                    ->count(),
                'cancelled_subscriptions' => TenantAmenity::whereYear('end_date', $month->year)
                    ->whereMonth('end_date', $month->month)
                    ->count(),
            ];
        }

        return $months;
    }

    /**
     * Get usage patterns
     */
    private function getUsagePatterns(): array
    {
        return [
            'hourly_usage' => $this->getHourlyUsagePattern(),
            'daily_usage' => $this->getDailyUsagePattern(),
            'weekly_usage' => $this->getWeeklyUsagePattern(),
        ];
    }

    /**
     * Get enquiry summary
     */
    private function getEnquirySummary(): array
    {
        return [
            'total_enquiries' => Enquiry::count(),
            'new_enquiries' => Enquiry::where('status', 'new')->count(),
            'in_progress_enquiries' => Enquiry::where('status', 'in_progress')->count(),
            'resolved_enquiries' => Enquiry::where('status', 'resolved')->count(),
            'closed_enquiries' => Enquiry::where('status', 'closed')->count(),
            'overdue_enquiries' => Enquiry::whereIn('status', ['new', 'in_progress'])
                ->where('created_at', '<', Carbon::now()->subHours(24))
                ->count(),
        ];
    }

    /**
     * Get enquiry conversion analytics
     */
    private function getEnquiryConversionAnalytics(): array
    {
        $totalEnquiries = Enquiry::count();
        $convertedEnquiries = Enquiry::where('status', 'resolved')
            ->whereNotNull('admin_notes')
            ->where('admin_notes', 'like', '%Converted to tenant%')
            ->count();

        return [
            'conversion_rate' => $totalEnquiries > 0 ? ($convertedEnquiries / $totalEnquiries) * 100 : 0,
            'total_conversions' => $convertedEnquiries,
            'average_conversion_time' => $this->calculateAverageConversionTime(),
        ];
    }

    /**
     * Get enquiry source analytics
     */
    private function getEnquirySourceAnalytics(): array
    {
        return Enquiry::select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->get()
            ->pluck('count', 'source')
            ->toArray();
    }

    /**
     * Get enquiry response analytics
     */
    private function getEnquiryResponseAnalytics(): array
    {
        return [
            'response_rate' => $this->calculateEnquiryResponseRate(),
            'average_response_time' => $this->calculateAverageEnquiryResponseTime(),
            'response_time_trend' => $this->getEnquiryResponseTimeTrend(),
        ];
    }

    /**
     * Get enquiry priority breakdown
     */
    private function getEnquiryPriorityBreakdown(): array
    {
        return Enquiry::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority')
            ->toArray();
    }

    /**
     * Get enquiry trend analysis
     */
    private function getEnquiryTrendAnalysis(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $month->format('M Y'),
                'total_enquiries' => Enquiry::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'resolved_enquiries' => Enquiry::where('status', 'resolved')
                    ->whereYear('responded_at', $month->year)
                    ->whereMonth('responded_at', $month->month)
                    ->count(),
            ];
        }

        return $months;
    }

    /**
     * Get notification summary
     */
    private function getNotificationSummary(): array
    {
        return [
            'total_notifications' => Notification::count(),
            'pending_notifications' => Notification::where('status', 'pending')->count(),
            'sent_notifications' => Notification::where('status', 'sent')->count(),
            'failed_notifications' => Notification::where('status', 'failed')->count(),
            'cancelled_notifications' => Notification::where('status', 'cancelled')->count(),
            'scheduled_notifications' => Notification::whereNotNull('scheduled_at')->count(),
        ];
    }

    /**
     * Get notification delivery analytics
     */
    private function getNotificationDeliveryAnalytics(): array
    {
        return [
            'delivery_rate' => $this->calculateNotificationDeliveryRate(),
            'average_delivery_time' => $this->calculateAverageNotificationDeliveryTime(),
            'retry_rate' => $this->calculateNotificationRetryRate(),
        ];
    }

    /**
     * Get notification type breakdown
     */
    private function getNotificationTypeBreakdown(): array
    {
        return Notification::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Get notification success rates
     */
    private function getNotificationSuccessRates(): array
    {
        $types = Notification::distinct()->pluck('type');
        $successRates = [];

        foreach ($types as $type) {
            $total = Notification::where('type', $type)->count();
            $sent = Notification::where('type', $type)->where('status', 'sent')->count();
            $successRates[$type] = $total > 0 ? ($sent / $total) * 100 : 0;
        }

        return $successRates;
    }

    /**
     * Get notification retry analytics
     */
    private function getNotificationRetryAnalytics(): array
    {
        return [
            'total_retries' => Notification::sum('retry_count'),
            'average_retries' => Notification::avg('retry_count'),
            'max_retries_reached' => Notification::where('retry_count', '>=', 3)->count(),
            'retryable_notifications' => Notification::where('status', 'failed')
                ->where('retry_count', '<', 3)
                ->count(),
        ];
    }

    /**
     * Get notification trend analysis
     */
    private function getNotificationTrendAnalysis(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $month->format('M Y'),
                'total_notifications' => Notification::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'sent_notifications' => Notification::where('status', 'sent')
                    ->whereYear('sent_at', $month->year)
                    ->whereMonth('sent_at', $month->month)
                    ->count(),
            ];
        }

        return $months;
    }

    /**
     * Get system summary
     */
    private function getSystemSummary(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'system_users' => User::where('is_tenant', false)->count(),
            'tenant_users' => User::where('is_tenant', true)->count(),
            'super_admins' => User::where('is_super_admin', true)->count(),
            'last_login' => User::whereNotNull('last_login_at')
                ->orderBy('last_login_at', 'desc')
                ->first()
                ->last_login_at ?? null,
        ];
    }

    /**
     * Get user activity statistics
     */
    private function getUserActivityStats(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();

        return [
            'today_logins' => User::whereDate('last_login_at', $today)->count(),
            'this_week_logins' => User::where('last_login_at', '>=', $thisWeek)->count(),
            'active_sessions' => $this->getActiveSessionCount(),
            'user_growth' => $this->getUserGrowthRate(),
        ];
    }

    /**
     * Get database statistics
     */
    private function getDatabaseStats(): array
    {
        return [
            'total_records' => $this->getTotalDatabaseRecords(),
            'database_size' => $this->getDatabaseSize(),
            'table_counts' => $this->getTableCounts(),
            'index_usage' => $this->getIndexUsageStats(),
        ];
    }

    /**
     * Get system performance metrics
     */
    private function getSystemPerformanceMetrics(): array
    {
        return [
            'average_response_time' => $this->getAverageResponseTime(),
            'memory_usage' => $this->getMemoryUsage(),
            'cpu_usage' => $this->getCpuUsage(),
            'disk_usage' => $this->getDiskUsage(),
        ];
    }

    /**
     * Get error log statistics
     */
    private function getErrorLogStats(): array
    {
        return [
            'total_errors' => $this->getTotalErrorCount(),
            'errors_today' => $this->getTodayErrorCount(),
            'error_types' => $this->getErrorTypeBreakdown(),
            'critical_errors' => $this->getCriticalErrorCount(),
        ];
    }

    /**
     * Get maintenance alerts
     */
    private function getMaintenanceAlerts(): array
    {
        return [
            'scheduled_maintenance' => $this->getScheduledMaintenance(),
            'system_updates' => $this->getSystemUpdates(),
            'backup_status' => $this->getBackupStatus(),
            'security_alerts' => $this->getSecurityAlerts(),
        ];
    }

    /**
     * Get available widgets
     */
    private function getAvailableWidgets(): array
    {
        return [
            'revenue_chart' => ['name' => 'Revenue Chart', 'type' => 'chart'],
            'occupancy_gauge' => ['name' => 'Occupancy Gauge', 'type' => 'gauge'],
            'recent_activity' => ['name' => 'Recent Activity', 'type' => 'list'],
            'quick_stats' => ['name' => 'Quick Stats', 'type' => 'stats'],
            'alerts' => ['name' => 'Alerts', 'type' => 'alerts'],
            'enquiry_trend' => ['name' => 'Enquiry Trend', 'type' => 'chart'],
            'payment_summary' => ['name' => 'Payment Summary', 'type' => 'summary'],
            'tenant_satisfaction' => ['name' => 'Tenant Satisfaction', 'type' => 'score'],
        ];
    }

    /**
     * Get user widgets
     */
    private function getUserWidgets(Request $request): array
    {
        // This would typically be stored in user preferences
        // For now, return default widgets
        return [
            'revenue_chart',
            'occupancy_gauge',
            'recent_activity',
            'quick_stats',
            'alerts',
        ];
    }

    /**
     * Get widget data
     */
    private function getWidgetData(Request $request): array
    {
        $widgets = $request->get('widgets', []);
        $data = [];

        foreach ($widgets as $widget) {
            switch ($widget) {
                case 'revenue_chart':
                    $data['revenue_chart'] = $this->getRevenueChartData();
                    break;
                case 'occupancy_gauge':
                    $data['occupancy_gauge'] = $this->calculateOverallOccupancyRate();
                    break;
                case 'recent_activity':
                    $data['recent_activity'] = $this->getRecentActivity();
                    break;
                case 'quick_stats':
                    $data['quick_stats'] = $this->getQuickStats();
                    break;
                case 'alerts':
                    $data['alerts'] = $this->getAlerts();
                    break;
            }
        }

        return $data;
    }

    // Helper methods for calculations

    private function calculateOverallOccupancyRate(): float
    {
        $totalBeds = Bed::count();
        $occupiedBeds = BedAssignment::where('status', 'active')->count();
        return $totalBeds > 0 ? ($occupiedBeds / $totalBeds) * 100 : 0;
    }

    private function calculateRevenueGrowth(): float
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $thisMonthRevenue = Payment::where('status', 'verified')
            ->where('created_at', '>=', $thisMonth)
            ->sum('amount');

        $lastMonthRevenue = Payment::where('status', 'verified')
            ->whereBetween('created_at', [$lastMonth, $thisMonth])
            ->sum('amount');

        return $lastMonthRevenue > 0 
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;
    }

    private function calculateEnquiryResponseRate(): float
    {
        $totalEnquiries = Enquiry::count();
        $respondedEnquiries = Enquiry::whereNotNull('responded_at')->count();
        return $totalEnquiries > 0 ? ($respondedEnquiries / $totalEnquiries) * 100 : 0;
    }

    private function calculatePaymentCollectionRate(): float
    {
        $totalInvoices = Invoice::sum('total_amount');
        $collectedAmount = Payment::where('status', 'verified')->sum('amount');
        return $totalInvoices > 0 ? ($collectedAmount / $totalInvoices) * 100 : 0;
    }

    private function calculateTenantSatisfactionScore(): float
    {
        // This would typically come from surveys or feedback systems
        // For now, return a placeholder value
        return 4.2;
    }

    private function calculateSystemUptime(): float
    {
        // This would typically be calculated from system monitoring
        // For now, return a placeholder value
        return 99.9;
    }

    private function calculateAverageMonthlyRevenue(): float
    {
        $months = 12;
        $totalRevenue = Payment::where('status', 'verified')->sum('amount');
        return $months > 0 ? $totalRevenue / $months : 0;
    }

    private function calculateAverageAssignmentDuration(): float
    {
        $assignments = BedAssignment::whereNotNull('assigned_until')
            ->whereNotNull('assigned_from')
            ->get();

        if ($assignments->isEmpty()) {
            return 0;
        }

        $totalDays = $assignments->sum(function ($assignment) {
            return Carbon::parse($assignment->assigned_from)
                ->diffInDays(Carbon::parse($assignment->assigned_until));
        });

        return $totalDays / $assignments->count();
    }

    private function calculateAverageTenancyDuration(): float
    {
        $tenants = TenantProfile::whereNotNull('move_in_date')
            ->whereNotNull('move_out_date')
            ->get();

        if ($tenants->isEmpty()) {
            return 0;
        }

        $totalDays = $tenants->sum(function ($tenant) {
            return Carbon::parse($tenant->move_in_date)
                ->diffInDays(Carbon::parse($tenant->move_out_date));
        });

        return $totalDays / $tenants->count();
    }

    private function calculateChurnRate(): float
    {
        $totalTenants = TenantProfile::count();
        $churnedTenants = TenantProfile::whereNotNull('move_out_date')->count();
        return $totalTenants > 0 ? ($churnedTenants / $totalTenants) * 100 : 0;
    }

    private function calculateAverageNotificationResponseTime(): float
    {
        $notifications = Notification::whereNotNull('sent_at')
            ->whereNotNull('created_at')
            ->get();

        if ($notifications->isEmpty()) {
            return 0;
        }

        $totalHours = $notifications->sum(function ($notification) {
            return Carbon::parse($notification->created_at)
                ->diffInHours(Carbon::parse($notification->sent_at));
        });

        return $totalHours / $notifications->count();
    }

    private function calculateAverageUsagePerTenant(): float
    {
        $totalUsage = TenantAmenityUsage::count();
        $totalTenants = TenantProfile::count();
        return $totalTenants > 0 ? $totalUsage / $totalTenants : 0;
    }

    private function calculateAverageConversionTime(): float
    {
        $convertedEnquiries = Enquiry::where('status', 'resolved')
            ->whereNotNull('responded_at')
            ->get();

        if ($convertedEnquiries->isEmpty()) {
            return 0;
        }

        $totalHours = $convertedEnquiries->sum(function ($enquiry) {
            return Carbon::parse($enquiry->created_at)
                ->diffInHours(Carbon::parse($enquiry->responded_at));
        });

        return $totalHours / $convertedEnquiries->count();
    }

    private function calculateAverageEnquiryResponseTime(): float
    {
        $respondedEnquiries = Enquiry::whereNotNull('responded_at')->get();

        if ($respondedEnquiries->isEmpty()) {
            return 0;
        }

        $totalHours = $respondedEnquiries->sum(function ($enquiry) {
            return Carbon::parse($enquiry->created_at)
                ->diffInHours(Carbon::parse($enquiry->responded_at));
        });

        return $totalHours / $respondedEnquiries->count();
    }

    private function calculateNotificationDeliveryRate(): float
    {
        $totalNotifications = Notification::count();
        $sentNotifications = Notification::where('status', 'sent')->count();
        return $totalNotifications > 0 ? ($sentNotifications / $totalNotifications) * 100 : 0;
    }

    private function calculateAverageNotificationDeliveryTime(): float
    {
        $sentNotifications = Notification::where('status', 'sent')
            ->whereNotNull('sent_at')
            ->get();

        if ($sentNotifications->isEmpty()) {
            return 0;
        }

        $totalMinutes = $sentNotifications->sum(function ($notification) {
            return Carbon::parse($notification->created_at)
                ->diffInMinutes(Carbon::parse($notification->sent_at));
        });

        return $totalMinutes / $sentNotifications->count();
    }

    private function calculateNotificationRetryRate(): float
    {
        $totalNotifications = Notification::count();
        $retriedNotifications = Notification::where('retry_count', '>', 0)->count();
        return $totalNotifications > 0 ? ($retriedNotifications / $totalNotifications) * 100 : 0;
    }

    // Placeholder methods for system metrics
    private function getActiveSessionCount(): int
    {
        return 0; // Would be implemented with session tracking
    }

    private function getUserGrowthRate(): float
    {
        return 0; // Would be calculated from historical data
    }

    private function getTotalDatabaseRecords(): int
    {
        return 0; // Would be calculated from all tables
    }

    private function getDatabaseSize(): string
    {
        return '0 MB'; // Would be calculated from database
    }

    private function getTableCounts(): array
    {
        return []; // Would be calculated from all tables
    }

    private function getIndexUsageStats(): array
    {
        return []; // Would be calculated from database
    }

    private function getAverageResponseTime(): float
    {
        return 0; // Would be calculated from request logs
    }

    private function getMemoryUsage(): float
    {
        return 0; // Would be calculated from system metrics
    }

    private function getCpuUsage(): float
    {
        return 0; // Would be calculated from system metrics
    }

    private function getDiskUsage(): float
    {
        return 0; // Would be calculated from system metrics
    }

    private function getTotalErrorCount(): int
    {
        return 0; // Would be calculated from error logs
    }

    private function getTodayErrorCount(): int
    {
        return 0; // Would be calculated from error logs
    }

    private function getErrorTypeBreakdown(): array
    {
        return []; // Would be calculated from error logs
    }

    private function getCriticalErrorCount(): int
    {
        return 0; // Would be calculated from error logs
    }

    private function getScheduledMaintenance(): array
    {
        return []; // Would be retrieved from maintenance schedule
    }

    private function getSystemUpdates(): array
    {
        return []; // Would be retrieved from update system
    }

    private function getBackupStatus(): array
    {
        return []; // Would be retrieved from backup system
    }

    private function getSecurityAlerts(): array
    {
        return []; // Would be retrieved from security system
    }

    // Chart data methods
    private function getRevenueChartData(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $month->format('M'),
                'revenue' => Payment::where('status', 'verified')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount'),
            ];
        }
        return $months;
    }

    private function getOccupancyChartData(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $totalBeds = Bed::whereYear('created_at', '<=', $month->year)
                ->whereMonth('created_at', '<=', $month->month)
                ->count();
            $occupiedBeds = BedAssignment::where('status', 'active')
                ->whereYear('assigned_from', '<=', $month->year)
                ->whereMonth('assigned_from', '<=', $month->month)
                ->count();
            $occupancyRate = $totalBeds > 0 ? ($occupiedBeds / $totalBeds) * 100 : 0;

            $months[] = [
                'month' => $month->format('M'),
                'occupancy_rate' => round($occupancyRate, 2),
            ];
        }
        return $months;
    }

    private function getEnquiryChartData(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $month->format('M'),
                'enquiries' => Enquiry::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }
        return $months;
    }

    private function getPaymentChartData(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $month->format('M'),
                'payments' => Payment::where('status', 'verified')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount'),
            ];
        }
        return $months;
    }

    // Additional helper methods
    private function getTenantAgeGroups(): array
    {
        return []; // Would be calculated from date_of_birth
    }

    private function getTenantOccupations(): array
    {
        return TenantProfile::select('occupation', DB::raw('count(*) as count'))
            ->whereNotNull('occupation')
            ->groupBy('occupation')
            ->get()
            ->pluck('count', 'occupation')
            ->toArray();
    }

    private function getTenantCompanies(): array
    {
        return TenantProfile::select('company', DB::raw('count(*) as count'))
            ->whereNotNull('company')
            ->groupBy('company')
            ->get()
            ->pluck('count', 'company')
            ->toArray();
    }

    private function getPeakUsageHours(): array
    {
        return []; // Would be calculated from usage records
    }

    private function getHourlyUsagePattern(): array
    {
        return []; // Would be calculated from usage records
    }

    private function getDailyUsagePattern(): array
    {
        return []; // Would be calculated from usage records
    }

    private function getWeeklyUsagePattern(): array
    {
        return []; // Would be calculated from usage records
    }

    private function getEnquiryResponseTimeTrend(): array
    {
        return []; // Would be calculated from enquiry data
    }
}
