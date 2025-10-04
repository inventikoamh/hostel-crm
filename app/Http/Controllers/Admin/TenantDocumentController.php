<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantDocument;
use App\Models\TenantProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class TenantDocumentController extends Controller
{
    /**
     * Display a listing of tenant documents
     */
    public function index(Request $request)
    {
        $query = TenantDocument::with(['tenantProfile.user', 'printedByUser', 'uploadedByAdmin', 'approvedByUser']);

        // Filter by document type
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by approval status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Filter by request type
        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        // Search by tenant name or document number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('tenantProfile.user', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Prepare data for x-data-table component
        $columns = [
            [
                'key' => 'document_number',
                'label' => 'Document #',
                'width' => 'w-32'
            ],
            [
                'key' => 'tenant',
                'label' => 'Tenant',
                'width' => 'w-48',
                'component' => 'components.tenant-info'
            ],
            [
                'key' => 'document_type',
                'label' => 'Document Type',
                'width' => 'w-32'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'width' => 'w-24',
                'component' => 'components.status-badge'
            ],
            [
                'key' => 'approval_status',
                'label' => 'Approval',
                'width' => 'w-24',
                'component' => 'components.status-badge'
            ],
            [
                'key' => 'priority',
                'label' => 'Priority',
                'width' => 'w-20'
            ],
            [
                'key' => 'actions',
                'label' => 'Actions',
                'width' => 'w-32'
            ]
        ];

        $data = $documents->map(function ($document) {
            return [
                'id' => $document->id,
                'document_number' => $document->document_number,
                'tenant' => [
                    'name' => $document->tenantProfile->user->name,
                    'email' => $document->tenantProfile->user->email,
                    'avatar' => $document->tenantProfile->user->avatar
                ],
                'document_type' => $document->document_type_display,
                'status' => $document->status,
                'approval_status' => $document->approval_status,
                'priority' => $document->priority_display,
                'view_url' => route('admin.tenant-documents.show', $document),
                'print_url' => route('admin.tenant-documents.print', $document),
                'download_url' => route('admin.tenant-documents.download', $document),
                'delete_url' => route('admin.tenant-documents.destroy', $document)
            ];
        })->toArray();

        $filters = [
            [
                'key' => 'document_type',
                'label' => 'Document Type',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Types'],
                    ['value' => 'aadhar_card', 'label' => 'Aadhar Card'],
                    ['value' => 'pan_card', 'label' => 'PAN Card'],
                    ['value' => 'student_id', 'label' => 'Student ID'],
                    ['value' => 'tenant_agreement', 'label' => 'Tenant Agreement'],
                    ['value' => 'lease_agreement', 'label' => 'Lease Agreement'],
                    ['value' => 'rental_agreement', 'label' => 'Rental Agreement'],
                    ['value' => 'maintenance_form', 'label' => 'Maintenance Form'],
                    ['value' => 'identity_proof', 'label' => 'Identity Proof'],
                    ['value' => 'address_proof', 'label' => 'Address Proof'],
                    ['value' => 'income_proof', 'label' => 'Income Proof'],
                    ['value' => 'other', 'label' => 'Other']
                ],
                'value' => $request->document_type
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Status'],
                    ['value' => 'draft', 'label' => 'Draft'],
                    ['value' => 'requested', 'label' => 'Requested'],
                    ['value' => 'uploaded', 'label' => 'Uploaded'],
                    ['value' => 'approved', 'label' => 'Approved'],
                    ['value' => 'rejected', 'label' => 'Rejected'],
                    ['value' => 'expired', 'label' => 'Expired'],
                    ['value' => 'archived', 'label' => 'Archived']
                ],
                'value' => $request->status
            ],
            [
                'key' => 'approval_status',
                'label' => 'Approval Status',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Approval Status'],
                    ['value' => 'pending', 'label' => 'Pending'],
                    ['value' => 'approved', 'label' => 'Approved'],
                    ['value' => 'rejected', 'label' => 'Rejected']
                ],
                'value' => $request->approval_status
            ],
            [
                'key' => 'request_type',
                'label' => 'Request Type',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Request Types'],
                    ['value' => 'admin_upload', 'label' => 'Admin Upload'],
                    ['value' => 'tenant_upload', 'label' => 'Tenant Upload']
                ],
                'value' => $request->request_type
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
                'key' => 'archive',
                'label' => 'Archive Selected',
                'icon' => 'fas fa-archive'
            ],
            [
                'key' => 'delete',
                'label' => 'Delete Selected',
                'icon' => 'fas fa-trash'
            ]
        ];

        $stats = [
            [
                'title' => 'Total Documents',
                'value' => TenantDocument::count(),
                'icon' => 'fas fa-file-alt',
                'color' => 'blue'
            ],
            [
                'title' => 'Pending Approval',
                'value' => TenantDocument::where('approval_status', 'pending')->count(),
                'icon' => 'fas fa-clock',
                'color' => 'yellow'
            ],
            [
                'title' => 'Approved',
                'value' => TenantDocument::where('approval_status', 'approved')->count(),
                'icon' => 'fas fa-check',
                'color' => 'green'
            ],
            [
                'title' => 'Required',
                'value' => TenantDocument::where('is_required', true)->count(),
                'icon' => 'fas fa-exclamation',
                'color' => 'red'
            ]
        ];

        return view('admin.tenant-documents.index', compact('documents', 'stats', 'columns', 'data', 'filters', 'bulkActions'));
    }

    /**
     * Show the form for creating a new tenant document request
     */
    public function create(Request $request)
    {
        $tenantId = $request->get('tenant_id');
        $documentType = $request->get('document_type', 'aadhar_card');

        $tenants = TenantProfile::with('user')->get();
        $selectedTenant = $tenantId ? TenantProfile::with('user')->find($tenantId) : null;

        return view('admin.tenant-documents.create', compact('tenants', 'selectedTenant', 'documentType'));
    }

    /**
     * Store a newly created tenant document request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_profile_id' => 'required|exists:tenant_profiles,id',
            'document_type' => 'required|string|in:aadhar_card,pan_card,student_id,tenant_agreement,lease_agreement,rental_agreement,maintenance_form,identity_proof,address_proof,income_proof,other',
            'description' => 'nullable|string|max:500',
            'request_type' => 'required|in:admin_upload,tenant_upload',
            'is_required' => 'boolean',
            'priority' => 'required|integer|in:1,2,3',
            'expiry_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $document = TenantDocument::create([
            'tenant_profile_id' => $request->tenant_profile_id,
            'category' => $request->document_type, // Keep for backward compatibility
            'document_type' => $request->document_type,
            'description' => $request->description,
            'request_type' => $request->request_type,
            'is_required' => $request->boolean('is_required'),
            'priority' => $request->priority,
            'expiry_date' => $request->expiry_date,
            'notes' => $request->notes,
            'status' => $request->request_type === 'admin_upload' ? 'draft' : 'requested',
            'approval_status' => 'pending',
            'document_data' => null, // Initialize as null, will be populated when needed
        ]);

        return redirect()->route('admin.tenant-documents.show', $document)
            ->with('success', 'Document request created successfully.');
    }

    /**
     * Display the specified tenant document
     */
    public function show(TenantDocument $tenantDocument)
    {
        $tenantDocument->load(['tenantProfile.user', 'tenantProfile.currentBed.room.hostel', 'printedByUser', 'uploadedByAdmin', 'approvedByUser']);

        return view('admin.tenant-documents.show', compact('tenantDocument'));
    }

    /**
     * Print the tenant document
     */
    public function print(TenantDocument $tenantDocument)
    {
        $tenantDocument->load(['tenantProfile.user', 'tenantProfile.currentBed.room.hostel']);

        // Update document status to printed
        if ($tenantDocument->status === 'draft') {
            $tenantDocument->update([
                'status' => 'printed',
                'printed_by' => Auth::id(),
                'printed_at' => now(),
            ]);
        }

        $pdf = Pdf::loadView('admin.tenant-documents.print', compact('tenantDocument'));

        return $pdf->stream("tenant-document-{$tenantDocument->document_number}.pdf");
    }

    /**
     * Download the tenant document as PDF
     */
    public function download(TenantDocument $tenantDocument)
    {
        $tenantDocument->load(['tenantProfile.user', 'tenantProfile.currentBed.room.hostel']);

        $pdf = Pdf::loadView('admin.tenant-documents.print', compact('tenantDocument'));

        return $pdf->download("tenant-document-{$tenantDocument->document_number}.pdf");
    }

    /**
     * Show the form for uploading signed form
     */
    public function uploadForm(TenantDocument $tenantDocument)
    {
        return view('admin.tenant-documents.upload', compact('tenantDocument'));
    }

    /**
     * Store the uploaded signed form
     */
    public function storeSignedForm(Request $request, TenantDocument $tenantDocument)
    {
        $validator = Validator::make($request->all(), [
            'signed_form' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Delete old document if exists
        if ($tenantDocument->document_path && Storage::disk('public')->exists($tenantDocument->document_path)) {
            Storage::disk('public')->delete($tenantDocument->document_path);
        }

        // Store the new document
        $file = $request->file('signed_form');
        $filename = 'tenant-documents/' . $tenantDocument->document_number . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('', $filename, 'public');

        // Update the document
        $tenantDocument->update([
            'document_path' => $path,
            'uploaded_by_admin' => Auth::id(),
            'uploaded_at_admin' => now(),
            'status' => 'approved',
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes' => $request->notes ?: $tenantDocument->notes,
        ]);

        return redirect()->route('admin.tenant-documents.show', $tenantDocument)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * View the signed form
     */
    public function viewSignedForm(TenantDocument $tenantDocument)
    {
        if (!$tenantDocument->document_path || !Storage::disk('public')->exists($tenantDocument->document_path)) {
            abort(404, 'Document not found.');
        }

        $filePath = Storage::disk('public')->path($tenantDocument->document_path);
        $mimeType = Storage::disk('public')->mimeType($tenantDocument->document_path);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($tenantDocument->document_path) . '"'
        ]);
    }

    /**
     * Remove the specified tenant form
     */
    public function destroy(TenantDocument $tenantDocument)
    {
        // Delete document file if exists
        if ($tenantDocument->document_path && Storage::disk('public')->exists($tenantDocument->document_path)) {
            Storage::disk('public')->delete($tenantDocument->document_path);
        }

        $tenantDocument->delete();

        return redirect()->route('admin.tenant-documents.index')
            ->with('success', 'Tenant document deleted successfully.');
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject,archive,delete',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:tenant_documents,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $action = $request->action;
        $selectedIds = $request->selected_ids;

        if ($action === 'approve') {
            $count = TenantDocument::whereIn('id', $selectedIds)->update([
                'approval_status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'status' => 'approved'
            ]);
            return redirect()->back()->with('success', "Successfully approved {$count} documents.");
        }

        if ($action === 'reject') {
            $count = TenantDocument::whereIn('id', $selectedIds)->update([
                'approval_status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'status' => 'rejected'
            ]);
            return redirect()->back()->with('success', "Successfully rejected {$count} documents.");
        }

        if ($action === 'archive') {
            $count = TenantDocument::whereIn('id', $selectedIds)->update(['status' => 'archived']);
            return redirect()->back()->with('success', "Successfully archived {$count} documents.");
        }

        if ($action === 'delete') {
            $documents = TenantDocument::whereIn('id', $selectedIds)->get();
            $count = 0;

            foreach ($documents as $document) {
                // Delete document file if exists
                if ($document->document_path && Storage::disk('public')->exists($document->document_path)) {
                    Storage::disk('public')->delete($document->document_path);
                }
                $document->delete();
                $count++;
            }

            return redirect()->back()->with('success', "Successfully deleted {$count} documents.");
        }

        return redirect()->back()->with('error', 'Invalid action specified.');
    }
}
