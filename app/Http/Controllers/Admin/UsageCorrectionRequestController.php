<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UsageCorrectionRequest;
use App\Models\TenantAmenityUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsageCorrectionRequestController extends Controller
{
    /**
     * Display a listing of usage correction requests
     */
    public function index(Request $request)
    {
        $query = UsageCorrectionRequest::with([
            'tenantAmenityUsage.tenantAmenity.tenantProfile.user',
            'tenantAmenityUsage.tenantAmenity.paidAmenity',
            'requestedBy',
            'reviewedBy'
        ]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by tenant name or amenity
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('requestedBy', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })->orWhereHas('tenantAmenityUsage.tenantAmenity.paidAmenity', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

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
                'label' => 'Usage Date',
                'width' => 'w-32'
            ],
            [
                'key' => 'quantity_change',
                'label' => 'Quantity Change',
                'width' => 'w-32'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'width' => 'w-24',
                'component' => 'components.status-badge'
            ],
            [
                'key' => 'created_at',
                'label' => 'Requested At',
                'width' => 'w-32'
            ],
            [
                'key' => 'reviewed_by',
                'label' => 'Reviewed By',
                'width' => 'w-32'
            ]
        ];

        $data = $requests->map(function ($request) {
            return [
                'id' => $request->id,
                'tenant' => [
                    'name' => $request->requestedBy->name,
                    'email' => $request->requestedBy->email,
                    'avatar' => $request->requestedBy->avatar
                ],
                'amenity' => $request->tenantAmenityUsage->tenantAmenity->paidAmenity->name,
                'usage_date' => $request->tenantAmenityUsage->usage_date->format('M d, Y'),
                'quantity_change' => $request->original_quantity . ' → ' . $request->requested_quantity,
                'status' => $request->status,
                'created_at' => $request->created_at->format('M d, Y H:i'),
                'reviewed_by' => $request->reviewedBy ? $request->reviewedBy->name : 'Pending',
                'view_url' => route('admin.usage-correction-requests.show', $request),
                'delete_url' => route('admin.usage-correction-requests.destroy', $request)
            ];
        })->toArray();

        $filters = [
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Status'],
                    ['value' => 'pending', 'label' => 'Pending'],
                    ['value' => 'approved', 'label' => 'Approved'],
                    ['value' => 'rejected', 'label' => 'Rejected']
                ],
                'value' => $request->status
            ]
        ];

        $bulkActions = [
            [
                'key' => 'approve',
                'label' => 'Approve Selected',
                'icon' => 'fas fa-check'
            ],
            [
                'key' => 'reject',
                'label' => 'Reject Selected',
                'icon' => 'fas fa-times'
            ]
        ];

        $stats = [
            [
                'title' => 'Total Requests',
                'value' => UsageCorrectionRequest::count(),
                'icon' => 'fas fa-list',
                'color' => 'blue'
            ],
            [
                'title' => 'Pending',
                'value' => UsageCorrectionRequest::pending()->count(),
                'icon' => 'fas fa-clock',
                'color' => 'yellow'
            ],
            [
                'title' => 'Approved',
                'value' => UsageCorrectionRequest::approved()->count(),
                'icon' => 'fas fa-check',
                'color' => 'green'
            ],
            [
                'title' => 'Rejected',
                'value' => UsageCorrectionRequest::rejected()->count(),
                'icon' => 'fas fa-times',
                'color' => 'red'
            ]
        ];

        return view('admin.usage-correction-requests.index', compact('requests', 'stats', 'columns', 'data', 'filters', 'bulkActions'));
    }

    /**
     * Display the specified usage correction request
     */
    public function show(UsageCorrectionRequest $usageCorrectionRequest)
    {
        $usageCorrectionRequest->load([
            'tenantAmenityUsage.tenantAmenity.tenantProfile.user',
            'tenantAmenityUsage.tenantAmenity.paidAmenity',
            'requestedBy',
            'reviewedBy'
        ]);

        return view('admin.usage-correction-requests.show', compact('usageCorrectionRequest'));
    }

    /**
     * Approve a usage correction request
     */
    public function approve(Request $request, UsageCorrectionRequest $usageCorrectionRequest)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Update the usage record
        $usage = $usageCorrectionRequest->tenantAmenityUsage;
        $usage->update([
            'quantity' => $usageCorrectionRequest->requested_quantity,
            'notes' => $usageCorrectionRequest->requested_notes,
            'total_amount' => $usage->unit_price * $usageCorrectionRequest->requested_quantity,
        ]);

        // Update the correction request
        $usageCorrectionRequest->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.usage-correction-requests.index')
            ->with('success', 'Usage correction request approved successfully.');
    }

    /**
     * Reject a usage correction request
     */
    public function reject(Request $request, UsageCorrectionRequest $usageCorrectionRequest)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Update the correction request
        $usageCorrectionRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.usage-correction-requests.index')
            ->with('success', 'Usage correction request rejected successfully.');
    }

    /**
     * Bulk approve multiple requests
     */
    public function bulkApprove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:usage_correction_requests,id',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $requests = UsageCorrectionRequest::whereIn('id', $request->request_ids)
            ->where('status', 'pending')
            ->get();

        $approvedCount = 0;

        foreach ($requests as $correctionRequest) {
            // Update the usage record
            $usage = $correctionRequest->tenantAmenityUsage;
            $usage->update([
                'quantity' => $correctionRequest->requested_quantity,
                'notes' => $correctionRequest->requested_notes,
                'total_amount' => $usage->unit_price * $correctionRequest->requested_quantity,
            ]);

            // Update the correction request
            $correctionRequest->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $approvedCount++;
        }

        return redirect()->route('admin.usage-correction-requests.index')
            ->with('success', "Successfully approved {$approvedCount} usage correction requests.");
    }

    /**
     * Bulk reject multiple requests
     */
    public function bulkReject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:usage_correction_requests,id',
            'admin_notes' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $rejectedCount = UsageCorrectionRequest::whereIn('id', $request->request_ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

        return redirect()->route('admin.usage-correction-requests.index')
            ->with('success', "Successfully rejected {$rejectedCount} usage correction requests.");
    }

    /**
     * Handle bulk actions from data table
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:usage_correction_requests,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $action = $request->action;
        $selectedIds = $request->selected_ids;

        if ($action === 'approve') {
            $requests = UsageCorrectionRequest::whereIn('id', $selectedIds)
                ->where('status', 'pending')
                ->get();

            $approvedCount = 0;
            foreach ($requests as $correctionRequest) {
                // Update the usage record
                $usage = $correctionRequest->tenantAmenityUsage;
                $usage->update([
                    'quantity' => $correctionRequest->requested_quantity,
                    'notes' => $correctionRequest->requested_notes,
                    'total_amount' => $usage->unit_price * $correctionRequest->requested_quantity,
                ]);

                // Update the correction request
                $correctionRequest->update([
                    'status' => 'approved',
                    'admin_notes' => $request->admin_notes ?? 'Bulk approved',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

                $approvedCount++;
            }

            return redirect()->route('admin.usage-correction-requests.index')
                ->with('success', "Successfully approved {$approvedCount} usage correction requests.");
        }

        if ($action === 'reject') {
            $validator = Validator::make($request->all(), [
                'admin_notes' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }

            $rejectedCount = UsageCorrectionRequest::whereIn('id', $selectedIds)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'admin_notes' => $request->admin_notes,
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

            return redirect()->route('admin.usage-correction-requests.index')
                ->with('success', "Successfully rejected {$rejectedCount} usage correction requests.");
        }

        return redirect()->back()->with('error', 'Invalid action specified.');
    }
}
