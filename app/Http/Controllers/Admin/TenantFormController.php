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
     * Display a listing of tenant forms
     */
    public function index(Request $request)
    {
        $query = TenantForm::with(['tenantProfile.user', 'printedByUser', 'uploadedByUser']);

        // Filter by form type
        if ($request->filled('form_type')) {
            $query->where('form_type', $request->form_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by tenant name or form number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('form_number', 'like', "%{$search}%")
                  ->orWhereHas('tenantProfile.user', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $forms = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Prepare data for x-data-table component
        $columns = [
            [
                'key' => 'form_number',
                'label' => 'Form Number',
                'width' => 'w-32'
            ],
            [
                'key' => 'tenant',
                'label' => 'Tenant',
                'width' => 'w-48',
                'component' => 'components.tenant-info'
            ],
            [
                'key' => 'form_type',
                'label' => 'Form Type',
                'width' => 'w-32'
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'width' => 'w-24',
                'component' => 'components.status-badge'
            ],
            [
                'key' => 'printed_at',
                'label' => 'Printed At',
                'width' => 'w-32'
            ],
            [
                'key' => 'uploaded_at',
                'label' => 'Signed At',
                'width' => 'w-32'
            ],
            [
                'key' => 'actions',
                'label' => 'Actions',
                'width' => 'w-32'
            ]
        ];

        $data = $forms->map(function ($form) {
            return [
                'id' => $form->id,
                'form_number' => $form->form_number,
                'tenant' => [
                    'name' => $form->tenantProfile->user->name,
                    'email' => $form->tenantProfile->user->email,
                    'avatar' => $form->tenantProfile->user->avatar
                ],
                'form_type' => $form->form_type_display,
                'status' => $form->status,
                'printed_at' => $form->printed_at ? $form->printed_at->format('M d, Y H:i') : 'Not printed',
                'uploaded_at' => $form->uploaded_at ? $form->uploaded_at->format('M d, Y H:i') : 'Not signed',
                'view_url' => route('admin.tenant-forms.show', $form),
                'print_url' => route('admin.tenant-forms.print', $form),
                'download_url' => route('admin.tenant-forms.download', $form),
                'delete_url' => route('admin.tenant-forms.destroy', $form)
            ];
        })->toArray();

        $filters = [
            [
                'key' => 'form_type',
                'label' => 'Form Type',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Types'],
                    ['value' => 'tenant_agreement', 'label' => 'Tenant Agreement'],
                    ['value' => 'lease_agreement', 'label' => 'Lease Agreement'],
                    ['value' => 'rental_agreement', 'label' => 'Rental Agreement'],
                    ['value' => 'maintenance_form', 'label' => 'Maintenance Form']
                ],
                'value' => $request->form_type
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Status'],
                    ['value' => 'draft', 'label' => 'Draft'],
                    ['value' => 'printed', 'label' => 'Printed'],
                    ['value' => 'signed', 'label' => 'Signed'],
                    ['value' => 'archived', 'label' => 'Archived']
                ],
                'value' => $request->status
            ]
        ];

        $bulkActions = [
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
                'title' => 'Total Forms',
                'value' => TenantForm::count(),
                'icon' => 'fas fa-file-alt',
                'color' => 'blue'
            ],
            [
                'title' => 'Draft',
                'value' => TenantForm::where('status', 'draft')->count(),
                'icon' => 'fas fa-edit',
                'color' => 'gray'
            ],
            [
                'title' => 'Printed',
                'value' => TenantForm::where('status', 'printed')->count(),
                'icon' => 'fas fa-print',
                'color' => 'yellow'
            ],
            [
                'title' => 'Signed',
                'value' => TenantForm::where('status', 'signed')->count(),
                'icon' => 'fas fa-signature',
                'color' => 'green'
            ]
        ];

        return view('admin.tenant-forms.index', compact('forms', 'stats', 'columns', 'data', 'filters', 'bulkActions'));
    }

    /**
     * Show the form for creating a new tenant form
     */
    public function create(Request $request)
    {
        $tenantId = $request->get('tenant_id');
        $formType = $request->get('form_type', 'tenant_agreement');

        $tenants = TenantProfile::with('user')->get();
        $selectedTenant = $tenantId ? TenantProfile::with('user')->find($tenantId) : null;

        return view('admin.tenant-forms.create', compact('tenants', 'selectedTenant', 'formType'));
    }

    /**
     * Store a newly created tenant form
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_profile_id' => 'required|exists:tenant_profiles,id',
            'form_type' => 'required|string|in:tenant_agreement,lease_agreement,rental_agreement,maintenance_form',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tenantProfile = TenantProfile::with('user')->find($request->tenant_profile_id);

        // Prepare form data
        $formData = [
            'tenant_info' => [
                'name' => $tenantProfile->user->name,
                'email' => $tenantProfile->user->email,
                'phone' => $tenantProfile->phone,
                'date_of_birth' => $tenantProfile->date_of_birth?->format('Y-m-d'),
                'address' => $tenantProfile->address,
                'occupation' => $tenantProfile->occupation,
                'company' => $tenantProfile->company,
                'id_proof_type' => $tenantProfile->id_proof_type,
                'id_proof_number' => $tenantProfile->id_proof_number,
                'avatar' => $tenantProfile->user->avatar,
            ],
            'emergency_contact' => [
                'name' => $tenantProfile->emergency_contact_name,
                'phone' => $tenantProfile->emergency_contact_phone,
                'relation' => $tenantProfile->emergency_contact_relation,
            ],
            'rental_info' => [
                'move_in_date' => $tenantProfile->move_in_date?->format('Y-m-d'),
                'move_out_date' => $tenantProfile->move_out_date?->format('Y-m-d'),
                'security_deposit' => $tenantProfile->security_deposit,
                'monthly_rent' => $tenantProfile->monthly_rent,
                'lease_start_date' => $tenantProfile->lease_start_date?->format('Y-m-d'),
                'lease_end_date' => $tenantProfile->lease_end_date?->format('Y-m-d'),
            ],
            'billing_info' => [
                'billing_cycle' => $tenantProfile->billing_cycle,
                'billing_day' => $tenantProfile->billing_day,
                'next_billing_date' => $tenantProfile->next_billing_date?->format('Y-m-d'),
            ],
            'current_bed' => $tenantProfile->currentBed ? [
                'room_number' => $tenantProfile->currentBed->room->room_number,
                'bed_number' => $tenantProfile->currentBed->bed_number,
                'hostel_name' => $tenantProfile->currentBed->room->hostel->name,
            ] : null,
            'form_metadata' => [
                'created_at' => now()->format('Y-m-d H:i:s'),
                'created_by' => Auth::user()->name,
            ]
        ];

        $form = TenantForm::create([
            'tenant_profile_id' => $request->tenant_profile_id,
            'form_type' => $request->form_type,
            'form_data' => $formData,
            'notes' => $request->notes,
            'status' => 'draft',
        ]);

        return redirect()->route('admin.tenant-forms.show', $form)
            ->with('success', 'Tenant form created successfully.');
    }

    /**
     * Display the specified tenant form
     */
    public function show(TenantForm $tenantForm)
    {
        $tenantForm->load(['tenantProfile.user', 'tenantProfile.currentBed.room.hostel', 'printedByUser', 'uploadedByUser']);

        return view('admin.tenant-forms.show', compact('tenantForm'));
    }

    /**
     * Print the tenant form
     */
    public function print(TenantForm $tenantForm)
    {
        $tenantForm->load(['tenantProfile.user', 'tenantProfile.currentBed.room.hostel']);

        // Update form status to printed
        if ($tenantForm->status === 'draft') {
            $tenantForm->update([
                'status' => 'printed',
                'printed_by' => Auth::id(),
                'printed_at' => now(),
            ]);
        }

        $pdf = Pdf::loadView('admin.tenant-forms.print', compact('tenantForm'));

        return $pdf->stream("tenant-form-{$tenantForm->form_number}.pdf");
    }

    /**
     * Download the tenant form as PDF
     */
    public function download(TenantForm $tenantForm)
    {
        $tenantForm->load(['tenantProfile.user', 'tenantProfile.currentBed.room.hostel']);

        $pdf = Pdf::loadView('admin.tenant-forms.print', compact('tenantForm'));

        return $pdf->download("tenant-form-{$tenantForm->form_number}.pdf");
    }

    /**
     * Show the form for uploading signed form
     */
    public function uploadForm(TenantForm $tenantForm)
    {
        return view('admin.tenant-forms.upload', compact('tenantForm'));
    }

    /**
     * Store the uploaded signed form
     */
    public function storeSignedForm(Request $request, TenantForm $tenantForm)
    {
        $validator = Validator::make($request->all(), [
            'signed_form' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Delete old signed form if exists
        if ($tenantForm->signed_form_path && Storage::disk('public')->exists($tenantForm->signed_form_path)) {
            Storage::disk('public')->delete($tenantForm->signed_form_path);
        }

        // Store the new signed form
        $file = $request->file('signed_form');
        $filename = 'signed-forms/' . $tenantForm->form_number . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('', $filename, 'public');

        // Update the form
        $tenantForm->update([
            'signed_form_path' => $path,
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
            'status' => 'signed',
            'notes' => $request->notes ?: $tenantForm->notes,
        ]);

        return redirect()->route('admin.tenant-forms.show', $tenantForm)
            ->with('success', 'Signed form uploaded successfully.');
    }

    /**
     * View the signed form
     */
    public function viewSignedForm(TenantForm $tenantForm)
    {
        if (!$tenantForm->signed_form_path || !Storage::disk('public')->exists($tenantForm->signed_form_path)) {
            abort(404, 'Signed form not found.');
        }

        $filePath = Storage::disk('public')->path($tenantForm->signed_form_path);
        $mimeType = Storage::disk('public')->mimeType($tenantForm->signed_form_path);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($tenantForm->signed_form_path) . '"'
        ]);
    }

    /**
     * Remove the specified tenant form
     */
    public function destroy(TenantForm $tenantForm)
    {
        // Delete signed form file if exists
        if ($tenantForm->signed_form_path && Storage::disk('public')->exists($tenantForm->signed_form_path)) {
            Storage::disk('public')->delete($tenantForm->signed_form_path);
        }

        $tenantForm->delete();

        return redirect()->route('admin.tenant-forms.index')
            ->with('success', 'Tenant form deleted successfully.');
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:archive,delete',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:tenant_forms,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $action = $request->action;
        $selectedIds = $request->selected_ids;

        if ($action === 'archive') {
            $count = TenantForm::whereIn('id', $selectedIds)->update(['status' => 'archived']);
            return redirect()->back()->with('success', "Successfully archived {$count} forms.");
        }

        if ($action === 'delete') {
            $forms = TenantForm::whereIn('id', $selectedIds)->get();
            $count = 0;

            foreach ($forms as $form) {
                // Delete signed form file if exists
                if ($form->signed_form_path && Storage::disk('public')->exists($form->signed_form_path)) {
                    Storage::disk('public')->delete($form->signed_form_path);
                }
                $form->delete();
                $count++;
            }

            return redirect()->back()->with('success', "Successfully deleted {$count} forms.");
        }

        return redirect()->back()->with('error', 'Invalid action specified.');
    }
}
