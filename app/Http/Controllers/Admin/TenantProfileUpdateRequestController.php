<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantProfileUpdateRequestController extends Controller
{
    /**
     * Display a listing of profile update requests
     */
    public function index(Request $request)
    {
        $query = TenantProfileUpdateRequest::with(['tenantProfile.user', 'reviewedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by tenant name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenantProfile.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
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
                'key' => 'requested_changes',
                'label' => 'Changes Requested',
                'width' => 'w-64'
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
            $changes = $request->requested_changes;
            $changesText = [];

            if (isset($changes['user'])) {
                foreach ($changes['user'] as $field => $value) {
                    if ($value) {
                        $changesText[] = ucfirst(str_replace('_', ' ', $field));
                    }
                }
            }

            if (isset($changes['tenant_profile'])) {
                foreach ($changes['tenant_profile'] as $field => $value) {
                    if ($value) {
                        $changesText[] = ucfirst(str_replace('_', ' ', $field));
                    }
                }
            }

            return [
                'id' => $request->id,
                'tenant' => [
                    'name' => $request->tenantProfile->user->name,
                    'email' => $request->tenantProfile->user->email,
                    'avatar' => $request->tenantProfile->user->avatar
                ],
                'requested_changes' => implode(', ', $changesText) ?: 'No changes',
                'status' => $request->status,
                'created_at' => $request->created_at->format('M d, Y H:i'),
                'reviewed_by' => $request->reviewedBy ? $request->reviewedBy->name : 'Pending',
                'view_url' => route('admin.tenant-profile-requests.show', $request),
                'delete_url' => route('admin.tenant-profile-requests.destroy', $request)
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
            ],
            [
                'key' => 'delete',
                'label' => 'Delete Selected',
                'icon' => 'fas fa-trash'
            ]
        ];

        $stats = [
            [
                'title' => 'Total Requests',
                'value' => TenantProfileUpdateRequest::count(),
                'icon' => 'fas fa-file-alt',
                'color' => 'blue'
            ],
            [
                'title' => 'Pending',
                'value' => TenantProfileUpdateRequest::where('status', 'pending')->count(),
                'icon' => 'fas fa-clock',
                'color' => 'yellow'
            ],
            [
                'title' => 'Approved',
                'value' => TenantProfileUpdateRequest::where('status', 'approved')->count(),
                'icon' => 'fas fa-check',
                'color' => 'green'
            ],
            [
                'title' => 'Rejected',
                'value' => TenantProfileUpdateRequest::where('status', 'rejected')->count(),
                'icon' => 'fas fa-times',
                'color' => 'red'
            ]
        ];

        return view('admin.tenant-profile-requests.index', compact(
            'requests', 'columns', 'data', 'filters', 'bulkActions', 'stats'
        ));
    }

    /**
     * Display the specified profile update request
     */
    public function show(TenantProfileUpdateRequest $tenantProfileRequest)
    {
        $tenantProfileRequest->load(['tenantProfile.user', 'reviewedBy']);

        return view('admin.tenant-profile-requests.show', compact('tenantProfileRequest'));
    }

    /**
     * Approve a profile update request
     */
    public function approve(Request $request, TenantProfileUpdateRequest $tenantProfileRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $tenantProfileRequest->approve(Auth::id(), $request->admin_notes);

        return redirect()->route('admin.tenant-profile-requests.index')
            ->with('success', 'Profile update request approved successfully.');
    }

    /**
     * Reject a profile update request
     */
    public function reject(Request $request, TenantProfileUpdateRequest $tenantProfileRequest)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        $tenantProfileRequest->reject(Auth::id(), $request->admin_notes);

        return redirect()->route('admin.tenant-profile-requests.index')
            ->with('success', 'Profile update request rejected.');
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'bulk_action' => 'required|in:approve,reject,delete',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:tenant_profile_update_requests,id'
        ]);

        $count = 0;

        if ($request->bulk_action === 'delete') {
            // For delete, we can delete any status
            $requests = TenantProfileUpdateRequest::whereIn('id', $request->selected_ids)->get();

            if ($requests->isEmpty()) {
                return redirect()->back()->with('error', 'No requests found to delete.');
            }

            foreach ($requests as $profileRequest) {
                // Clean up any uploaded files before deleting
                if (isset($profileRequest->requested_changes['user']['avatar'])) {
                    $avatarPath = $profileRequest->requested_changes['user']['avatar'];
                    if (\Storage::disk('public')->exists($avatarPath)) {
                        \Storage::disk('public')->delete($avatarPath);
                    }
                }
                $profileRequest->delete();
                $count++;
            }

            return redirect()->back()->with('success', "Successfully deleted {$count} profile update request(s).");
        } else {
            // For approve/reject, only process pending requests
            $requests = TenantProfileUpdateRequest::whereIn('id', $request->selected_ids)
                ->where('status', 'pending')
                ->get();

            if ($requests->isEmpty()) {
                return redirect()->back()->with('error', 'No pending requests found to process.');
            }

            foreach ($requests as $profileRequest) {
                if ($request->bulk_action === 'approve') {
                    $profileRequest->approve(Auth::id(), 'Bulk approved');
                } else {
                    $profileRequest->reject(Auth::id(), 'Bulk rejected');
                }
                $count++;
            }

            $action = $request->bulk_action === 'approve' ? 'approved' : 'rejected';
            return redirect()->back()->with('success', "Successfully {$action} {$count} profile update request(s).");
        }
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(TenantProfileUpdateRequest $tenantProfileRequest)
    {
        $tenantProfileRequest->delete();

        return redirect()->route('admin.tenant-profile-requests.index')
            ->with('success', 'Profile update request deleted successfully.');
    }
}
