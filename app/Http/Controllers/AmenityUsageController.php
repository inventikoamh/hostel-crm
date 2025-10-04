<?php

namespace App\Http\Controllers;

use App\Models\TenantAmenityUsage;
use App\Models\TenantAmenity;
use App\Models\TenantProfile;
use App\Models\PaidAmenity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AmenityUsageController extends Controller
{
    /**
     * Display a listing of amenity usage records
     */
    public function index(Request $request)
    {
        $query = TenantAmenityUsage::with([
            'tenantAmenity.tenantProfile.user',
            'tenantAmenity.paidAmenity',
            'recordedBy'
        ]);

        // Apply filters
        if ($request->filled('date')) {
            $query->forDate($request->date);
        }

        if ($request->filled('tenant_id')) {
            $query->forTenant($request->tenant_id);
        }

        if ($request->filled('amenity_id')) {
            $query->forAmenity($request->amenity_id);
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->forMonth($date->year, $date->month);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenantAmenity.tenantProfile.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('tenantAmenity.paidAmenity', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'usage_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $usageRecords = $query->paginate(15)->withQueryString();

        // Prepare data for x-data-table component
        $columns = [
            [
                'key' => 'tenant',
                'label' => 'Tenant',
                'width' => 'w-48',
                'component' => 'components.tenant-info'
            ],
            [
                'key' => 'amenity',
                'label' => 'Amenity',
                'width' => 'w-32'
            ],
            [
                'key' => 'usage_date',
                'label' => 'Date',
                'width' => 'w-24'
            ],
            [
                'key' => 'quantity',
                'label' => 'Qty',
                'width' => 'w-16'
            ],
            [
                'key' => 'unit_price',
                'label' => 'Unit Price',
                'width' => 'w-20'
            ],
            [
                'key' => 'total_amount',
                'label' => 'Total',
                'width' => 'w-20'
            ],
            [
                'key' => 'recorded_by',
                'label' => 'Recorded By',
                'width' => 'w-24'
            ]
        ];

        $data = $usageRecords->map(function ($usage) {
            return [
                'id' => $usage->id,
                'tenant' => [
                    'name' => $usage->tenantAmenity->tenantProfile->user->name,
                    'email' => $usage->tenantAmenity->tenantProfile->user->email,
                    'avatar' => $usage->tenantAmenity->tenantProfile->user->avatar
                ],
                'amenity' => $usage->tenantAmenity->paidAmenity->name,
                'usage_date' => $usage->usage_date->format('M j, Y'),
                'quantity' => $usage->quantity,
                'unit_price' => $usage->formatted_unit_price,
                'total_amount' => $usage->formatted_total_amount,
                'recorded_by' => $usage->recordedBy->name ?? 'Unknown',
                'view_url' => route('amenity-usage.show', $usage),
                'edit_url' => route('amenity-usage.edit', $usage),
                'delete_url' => route('amenity-usage.destroy', $usage)
            ];
        })->toArray();

        $filters = [
            [
                'key' => 'date',
                'label' => 'Date',
                'type' => 'date',
                'value' => $request->date
            ],
            [
                'key' => 'month',
                'label' => 'Month',
                'type' => 'month',
                'value' => $request->month
            ],
            [
                'key' => 'tenant_id',
                'label' => 'Tenant',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Tenants'],
                    ...TenantProfile::with('user')->get()->map(function ($tenant) {
                        return [
                            'value' => $tenant->id,
                            'label' => $tenant->user->name
                        ];
                    })->toArray()
                ],
                'value' => $request->tenant_id
            ],
            [
                'key' => 'amenity_id',
                'label' => 'Amenity',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Amenities'],
                    ...PaidAmenity::all()->map(function ($amenity) {
                        return [
                            'value' => $amenity->id,
                            'label' => $amenity->name
                        ];
                    })->toArray()
                ],
                'value' => $request->amenity_id
            ]
        ];

        $bulkActions = [
            [
                'key' => 'delete',
                'label' => 'Delete Selected',
                'icon' => 'fas fa-trash'
            ]
        ];

        $stats = [
            'total' => TenantAmenityUsage::count(),
            'today' => TenantAmenityUsage::forDate(now())->count(),
            'this_month' => TenantAmenityUsage::forMonth(now()->year, now()->month)->count(),
            'total_amount' => TenantAmenityUsage::sum('total_amount')
        ];

        return view('amenity-usage.index', compact(
            'usageRecords', 'columns', 'data', 'filters', 'bulkActions', 'stats'
        ));
    }

    /**
     * Show daily attendance marking interface
     */
    public function attendance(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        // Get all active tenant amenities
        $tenantAmenities = TenantAmenity::with([
            'tenantProfile.user',
            'paidAmenity'
        ])->where('status', 'active')->get();

        // Get existing usage records for the selected date
        $existingUsage = TenantAmenityUsage::with('tenantAmenity')
            ->forDate($selectedDate)
            ->get()
            ->keyBy('tenant_amenity_id');

        // Group by amenity for better organization
        $amenityGroups = $tenantAmenities->groupBy('paidAmenity.name');

        return view('amenity-usage.attendance', compact(
            'amenityGroups',
            'selectedDate',
            'existingUsage'
        ))->with('title', 'Mark Attendance')
          ->with('subtitle', 'Record daily amenity usage by tenants');
    }

    /**
     * Store attendance records
     */
    public function storeAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'usage' => 'required|array',
            'usage.*.tenant_amenity_id' => 'required|exists:tenant_amenities,id',
            'usage.*.quantity' => 'required|integer|min:1|max:10',
            'usage.*.notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $date = Carbon::parse($request->date);
                $recordedBy = auth()->id();

                foreach ($request->usage as $usageData) {
                    $tenantAmenity = TenantAmenity::findOrFail($usageData['tenant_amenity_id']);

                    // Check if usage already exists for this date
                    $existingUsage = TenantAmenityUsage::where('tenant_amenity_id', $tenantAmenity->id)
                        ->forDate($date)
                        ->first();

                    $quantity = (int) $usageData['quantity'];
                    $unitPrice = $tenantAmenity->price;
                    $totalAmount = $unitPrice * $quantity;

                    if ($existingUsage) {
                        // Update existing record
                        $existingUsage->update([
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_amount' => $totalAmount,
                            'notes' => $usageData['notes'] ?? null,
                            'recorded_by' => $recordedBy,
                        ]);
                    } else {
                        // Create new record
                        TenantAmenityUsage::create([
                            'tenant_amenity_id' => $tenantAmenity->id,
                            'usage_date' => $date,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_amount' => $totalAmount,
                            'notes' => $usageData['notes'] ?? null,
                            'recorded_by' => $recordedBy,
                        ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new usage record
     */
    public function create()
    {
        $tenantAmenities = TenantAmenity::with([
            'tenantProfile.user',
            'paidAmenity'
        ])->where('status', 'active')->get();

        return view('amenity-usage.create', compact('tenantAmenities'))
            ->with('title', 'Add Usage Record')
            ->with('subtitle', 'Record individual amenity usage');
    }

    /**
     * Store a newly created usage record
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_amenity_id' => 'required|exists:tenant_amenities,id',
            'usage_date' => 'required|date|before_or_equal:today',
            'quantity' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $tenantAmenity = TenantAmenity::findOrFail($request->tenant_amenity_id);
            $quantity = (int) $request->quantity;
            $unitPrice = $tenantAmenity->price;
            $totalAmount = $unitPrice * $quantity;

            // Check if usage already exists for this date
            $existingUsage = TenantAmenityUsage::where('tenant_amenity_id', $tenantAmenity->id)
                ->forDate($request->usage_date)
                ->first();

            if ($existingUsage) {
                return redirect()->back()
                    ->withErrors(['usage_date' => 'Usage record already exists for this date. Please edit the existing record.'])
                    ->withInput();
            }

            TenantAmenityUsage::create([
                'tenant_amenity_id' => $tenantAmenity->id,
                'usage_date' => $request->usage_date,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'recorded_by' => auth()->id(),
            ]);

            return redirect()->route('amenity-usage.index')
                ->with('success', 'Usage record created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create usage record: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified usage record
     */
    public function show(TenantAmenityUsage $amenityUsage)
    {
        $amenityUsage->load([
            'tenantAmenity.tenantProfile.user',
            'tenantAmenity.paidAmenity',
            'recordedBy'
        ]);

        return view('amenity-usage.show', compact('amenityUsage'))
            ->with('title', 'Usage Record Details')
            ->with('subtitle', 'View amenity usage record information');
    }

    /**
     * Show the form for editing the specified usage record
     */
    public function edit(TenantAmenityUsage $amenityUsage)
    {
        $amenityUsage->load([
            'tenantAmenity.tenantProfile.user',
            'tenantAmenity.paidAmenity'
        ]);

        $tenantAmenities = TenantAmenity::with([
            'tenantProfile.user',
            'paidAmenity'
        ])->where('status', 'active')->get();

        return view('amenity-usage.edit', compact('amenityUsage', 'tenantAmenities'))
            ->with('title', 'Edit Usage Record')
            ->with('subtitle', 'Update amenity usage record information');
    }

    /**
     * Update the specified usage record
     */
    public function update(Request $request, TenantAmenityUsage $amenityUsage)
    {
        $validator = Validator::make($request->all(), [
            'tenant_amenity_id' => 'required|exists:tenant_amenities,id',
            'usage_date' => 'required|date|before_or_equal:today',
            'quantity' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $tenantAmenity = TenantAmenity::findOrFail($request->tenant_amenity_id);
            $quantity = (int) $request->quantity;
            $unitPrice = $tenantAmenity->price;
            $totalAmount = $unitPrice * $quantity;

            // Check if another usage record exists for this date (excluding current record)
            $existingUsage = TenantAmenityUsage::where('tenant_amenity_id', $tenantAmenity->id)
                ->forDate($request->usage_date)
                ->where('id', '!=', $amenityUsage->id)
                ->first();

            if ($existingUsage) {
                return redirect()->back()
                    ->withErrors(['usage_date' => 'Another usage record already exists for this date.'])
                    ->withInput();
            }

            $amenityUsage->update([
                'tenant_amenity_id' => $tenantAmenity->id,
                'usage_date' => $request->usage_date,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'recorded_by' => auth()->id(),
            ]);

            return redirect()->route('amenity-usage.show', $amenityUsage)
                ->with('success', 'Usage record updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update usage record: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified usage record
     */
    public function destroy(TenantAmenityUsage $amenityUsage)
    {
        try {
            $amenityUsage->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usage record deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete usage record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get usage statistics for dashboard
     */
    public function getUsageStats(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $stats = [
            'total_usage_records' => TenantAmenityUsage::forDateRange($startDate, $endDate)->count(),
            'total_amount' => TenantAmenityUsage::forDateRange($startDate, $endDate)->sum('total_amount'),
            'unique_tenants' => TenantAmenityUsage::forDateRange($startDate, $endDate)
                ->distinct('tenant_amenity_id')
                ->count(),
            'top_amenities' => TenantAmenityUsage::with('tenantAmenity.paidAmenity')
                ->forDateRange($startDate, $endDate)
                ->select('tenant_amenity_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_amount) as total_amount'))
                ->groupBy('tenant_amenity_id')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Show reports page
     */
    public function reports(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->generateReportData($request);
        }

        return view('amenity-usage.reports')
            ->with('title', 'Usage Reports')
            ->with('subtitle', 'Analyze amenity usage patterns and generate reports');
    }

    /**
     * Generate report data
     */
    private function generateReportData(Request $request)
    {
        $reportType = $request->get('report_type', 'monthly');
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));


        try {
            switch($reportType) {
                case 'monthly':
                    $data = $this->getMonthlyReport($startDate, $endDate);
                    break;
                case 'daily':
                    $data = $this->getDailyReport($startDate, $endDate);
                    break;
                case 'tenant':
                    $data = $this->getTenantReport($startDate, $endDate);
                    break;
                case 'amenity':
                    $data = $this->getAmenityReport($startDate, $endDate);
                    break;
                default:
                    $data = $this->getMonthlyReport($startDate, $endDate);
                    break;
            }


            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly report data
     */
    private function getMonthlyReport($startDate, $endDate)
    {
        $records = TenantAmenityUsage::with(['tenantAmenity.tenantProfile.user', 'tenantAmenity.paidAmenity'])
            ->forDateRange($startDate, $endDate)
            ->get()
            ->groupBy(function ($usage) {
                return $usage->usage_date->format('Y-m');
            })
            ->map(function ($monthUsage, $month) {
                return [
                    'period' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                    'usage_days' => $monthUsage->count(),
                    'total_amount' => $monthUsage->sum('total_amount'),
                    'active_tenants' => $monthUsage->unique('tenant_amenity_id')->count(),
                    'avg_daily' => $monthUsage->sum('total_amount') / max($monthUsage->count(), 1)
                ];
            })
            ->values();

        $summary = $this->calculateSummary($startDate, $endDate);
        $chartData = $this->getChartData($records, 'monthly');

        return [
            'records' => $records,
            'summary' => $summary,
            'chart_data' => $chartData
        ];
    }

    /**
     * Get daily report data
     */
    private function getDailyReport($startDate, $endDate)
    {
        $records = TenantAmenityUsage::with(['tenantAmenity.tenantProfile.user', 'tenantAmenity.paidAmenity'])
            ->forDateRange($startDate, $endDate)
            ->get()
            ->groupBy(function ($usage) {
                return $usage->usage_date->format('Y-m-d');
            })
            ->map(function ($dayUsage, $date) {
                $topAmenity = $dayUsage->groupBy('tenantAmenity.paidAmenity.name')
                    ->map(function ($amenityUsage) {
                        return $amenityUsage->sum('total_amount');
                    })
                    ->sortDesc()
                    ->keys()
                    ->first() ?? 'N/A';

                return [
                    'date' => Carbon::createFromFormat('Y-m-d', $date)->format('M j, Y'),
                    'usage_count' => $dayUsage->count(),
                    'total_amount' => $dayUsage->sum('total_amount'),
                    'active_tenants' => $dayUsage->unique('tenant_amenity_id')->count(),
                    'top_amenity' => $topAmenity
                ];
            })
            ->values();

        $summary = $this->calculateSummary($startDate, $endDate);
        $chartData = $this->getChartData($records, 'daily');

        return [
            'records' => $records,
            'summary' => $summary,
            'chart_data' => $chartData
        ];
    }

    /**
     * Get tenant report data
     */
    private function getTenantReport($startDate, $endDate)
    {
        $records = TenantAmenityUsage::with(['tenantAmenity.tenantProfile.user', 'tenantAmenity.paidAmenity'])
            ->forDateRange($startDate, $endDate)
            ->get()
            ->groupBy('tenantAmenity.tenantProfile.user.name')
            ->map(function ($tenantUsage, $tenantName) {
                $topAmenity = $tenantUsage->groupBy('tenantAmenity.paidAmenity.name')
                    ->map(function ($amenityUsage) {
                        return $amenityUsage->sum('total_amount');
                    })
                    ->sortDesc()
                    ->keys()
                    ->first() ?? 'N/A';

                $usageDays = $tenantUsage->count();
                $totalAmount = $tenantUsage->sum('total_amount');

                return [
                    'tenant_name' => $tenantName,
                    'usage_days' => $usageDays,
                    'total_amount' => $totalAmount,
                    'avg_per_day' => $usageDays > 0 ? $totalAmount / $usageDays : 0,
                    'top_amenity' => $topAmenity
                ];
            })
            ->sortByDesc('total_amount')
            ->values();

        $summary = $this->calculateSummary($startDate, $endDate);
        $chartData = $this->getChartData($records, 'tenant');

        return [
            'records' => $records,
            'summary' => $summary,
            'chart_data' => $chartData
        ];
    }

    /**
     * Get amenity report data
     */
    private function getAmenityReport($startDate, $endDate)
    {
        $records = TenantAmenityUsage::with(['tenantAmenity.tenantProfile.user', 'tenantAmenity.paidAmenity'])
            ->forDateRange($startDate, $endDate)
            ->get()
            ->groupBy('tenantAmenity.paidAmenity.name')
            ->map(function ($amenityUsage, $amenityName) {
                $usageDays = $amenityUsage->count();
                $totalAmount = $amenityUsage->sum('total_amount');

                return [
                    'amenity_name' => $amenityName,
                    'usage_days' => $usageDays,
                    'total_amount' => $totalAmount,
                    'active_tenants' => $amenityUsage->unique('tenantAmenity.tenantProfile.user.id')->count(),
                    'avg_per_day' => $usageDays > 0 ? $totalAmount / $usageDays : 0
                ];
            })
            ->sortByDesc('total_amount')
            ->values();

        $summary = $this->calculateSummary($startDate, $endDate);
        $chartData = $this->getChartData($records, 'amenity');

        return [
            'records' => $records,
            'summary' => $summary,
            'chart_data' => $chartData
        ];
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($startDate, $endDate)
    {
        $totalUsage = TenantAmenityUsage::forDateRange($startDate, $endDate)->count();
        $totalAmount = TenantAmenityUsage::forDateRange($startDate, $endDate)->sum('total_amount');
        $activeTenants = TenantAmenityUsage::forDateRange($startDate, $endDate)
            ->distinct('tenant_amenity_id')
            ->count();

        $daysDiff = $startDate->diffInDays($endDate) + 1;
        $avgDailyUsage = $daysDiff > 0 ? $totalUsage / $daysDiff : 0;

        return [
            'total_usage_days' => $totalUsage,
            'total_amount' => $totalAmount,
            'active_tenants' => $activeTenants,
            'avg_daily_usage' => $avgDailyUsage
        ];
    }

    /**
     * Get chart data for visualization
     */
    private function getChartData($records, $type)
    {
        $labels = [];
        $amounts = [];
        $counts = [];

        foreach ($records as $record) {
            switch ($type) {
                case 'monthly':
                    $labels[] = $record['period'];
                    break;
                case 'daily':
                    $labels[] = $record['date'];
                    break;
                case 'tenant':
                    $labels[] = $record['tenant_name'];
                    break;
                case 'amenity':
                    $labels[] = $record['amenity_name'];
                    break;
            }

            $amounts[] = $record['total_amount'];
            $counts[] = $record['usage_days'] ?? $record['usage_count'] ?? 0;
        }

        return [
            'labels' => $labels,
            'amounts' => $amounts,
            'counts' => $counts
        ];
    }

    /**
     * Export report as CSV
     */
    public function exportReport(Request $request)
    {
        $reportType = $request->get('report_type', 'monthly');
        $startDate = Carbon::parse($request->get('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->get('end_date', now()->endOfMonth()));

        $data = match($reportType) {
            'monthly' => $this->getMonthlyReport($startDate, $endDate),
            'daily' => $this->getDailyReport($startDate, $endDate),
            'tenant' => $this->getTenantReport($startDate, $endDate),
            'amenity' => $this->getAmenityReport($startDate, $endDate),
            default => $this->getMonthlyReport($startDate, $endDate)
        };

        $filename = "amenity_usage_{$reportType}_report_" . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data, $reportType) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            $csvHeaders = $this->getCsvHeaders($reportType);
            fputcsv($file, $csvHeaders);

            // Add data rows
            foreach ($data['records'] as $record) {
                $row = $this->getCsvRow($record, $reportType);
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get CSV headers for different report types
     */
    private function getCsvHeaders($reportType)
    {
        return match($reportType) {
            'monthly' => ['Month', 'Usage Days', 'Total Amount', 'Active Tenants', 'Avg Daily'],
            'daily' => ['Date', 'Usage Count', 'Total Amount', 'Active Tenants', 'Top Amenity'],
            'tenant' => ['Tenant', 'Usage Days', 'Total Amount', 'Avg Per Day', 'Top Amenity'],
            'amenity' => ['Amenity', 'Usage Days', 'Total Amount', 'Active Tenants', 'Avg Per Day'],
            default => ['Data']
        };
    }

    /**
     * Get CSV row data for different report types
     */
    private function getCsvRow($record, $reportType)
    {
        return match($reportType) {
            'monthly' => [
                $record['period'],
                $record['usage_days'],
                $record['total_amount'],
                $record['active_tenants'],
                $record['avg_daily']
            ],
            'daily' => [
                $record['date'],
                $record['usage_count'],
                $record['total_amount'],
                $record['active_tenants'],
                $record['top_amenity']
            ],
            'tenant' => [
                $record['tenant_name'],
                $record['usage_days'],
                $record['total_amount'],
                $record['avg_per_day'],
                $record['top_amenity']
            ],
            'amenity' => [
                $record['amenity_name'],
                $record['usage_days'],
                $record['total_amount'],
                $record['active_tenants'],
                $record['avg_per_day']
            ],
            default => [json_encode($record)]
        };
    }

}
