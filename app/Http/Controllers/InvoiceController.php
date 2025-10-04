<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\TenantProfile;
use App\Models\PaidAmenity;
use App\Models\Room;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['tenantProfile.user', 'items', 'payments']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_profile_id', $request->tenant_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('tenantProfile.user', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->get()->map(function ($invoice) {
            return [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'tenant_name' => $invoice->tenantProfile->user->name,
                'type' => $invoice->type,
                'status' => $invoice->status,
                'invoice_date' => $invoice->invoice_date->format('M j, Y'),
                'due_date' => $invoice->due_date->format('M j, Y'),
                'total_amount' => $invoice->formatted_total_amount,
                'paid_amount' => $invoice->formatted_paid_amount,
                'balance_amount' => $invoice->formatted_balance_amount,
                'payment_status' => $invoice->payment_status,
                'is_overdue' => $invoice->is_overdue,
                'days_overdue' => $invoice->days_overdue,
                'view_url' => route('invoices.show', $invoice->id),
                'edit_url' => route('invoices.edit', $invoice->id),
                'delete_url' => route('invoices.destroy', $invoice->id),
                'pdf_url' => route('invoices.pdf.view', $invoice->id),
                'pdf_download_url' => route('invoices.pdf.download', $invoice->id)
            ];
        });

        // Statistics
        $stats = [
            'total' => $invoices->count(),
            'draft' => $invoices->where('status', 'draft')->count(),
            'sent' => $invoices->where('status', 'sent')->count(),
            'paid' => $invoices->where('status', 'paid')->count(),
            'overdue' => $invoices->where('is_overdue', true)->count(),
            'total_amount' => $invoices->sum(function ($invoice) {
                return (float) str_replace(['₹', ','], '', $invoice['total_amount']);
            }),
            'paid_amount' => $invoices->sum(function ($invoice) {
                return (float) str_replace(['₹', ','], '', $invoice['paid_amount']);
            })
        ];

        // Table configuration
        $columns = [
            ['key' => 'invoice_number', 'label' => 'Invoice #', 'width' => 'w-32'],
            ['key' => 'tenant_name', 'label' => 'Tenant', 'width' => 'w-40'],
            ['key' => 'type', 'label' => 'Type', 'component' => 'components.status-badge', 'width' => 'w-24'],
            ['key' => 'invoice_date', 'label' => 'Date', 'width' => 'w-28'],
            ['key' => 'due_date', 'label' => 'Due Date', 'width' => 'w-28'],
            ['key' => 'total_amount', 'label' => 'Total', 'width' => 'w-24'],
            ['key' => 'balance_amount', 'label' => 'Balance', 'width' => 'w-24'],
            ['key' => 'status', 'label' => 'Status', 'component' => 'components.status-badge', 'width' => 'w-24'],
        ];

        // Filters
        $tenants = TenantProfile::with('user')->get();
        $filters = [
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => 'draft', 'label' => 'Draft'],
                    ['value' => 'sent', 'label' => 'Sent'],
                    ['value' => 'paid', 'label' => 'Paid'],
                    ['value' => 'overdue', 'label' => 'Overdue'],
                    ['value' => 'cancelled', 'label' => 'Cancelled']
                ]
            ],
            [
                'key' => 'type',
                'label' => 'Type',
                'type' => 'select',
                'options' => [
                    ['value' => 'rent', 'label' => 'Rent'],
                    ['value' => 'amenities', 'label' => 'Amenities'],
                    ['value' => 'damage', 'label' => 'Damage'],
                    ['value' => 'other', 'label' => 'Other']
                ]
            ],
            [
                'key' => 'tenant_id',
                'label' => 'Tenant',
                'type' => 'select',
                'options' => $tenants->map(fn($t) => ['value' => $t->id, 'label' => $t->user->name])->toArray()
            ]
        ];

        $bulkActions = [
            ['key' => 'send', 'label' => 'Mark as Sent', 'icon' => 'fas fa-paper-plane'],
            ['key' => 'cancel', 'label' => 'Cancel Selected', 'icon' => 'fas fa-times'],
            ['key' => 'delete', 'label' => 'Delete Selected', 'icon' => 'fas fa-trash']
        ];

        $pagination = [
            'from' => 1,
            'to' => $invoices->count(),
            'total' => $invoices->count(),
            'current_page' => 1,
            'per_page' => 25
        ];

        return view('invoices.index', compact('invoices', 'stats', 'columns', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create(Request $request)
    {
        $tenants = TenantProfile::with(['user', 'currentBed.room'])->get();
        $selectedTenantId = $request->query('tenant_id');
        $selectedType = $request->query('type', 'rent');

        return view('invoices.create', compact('tenants', 'selectedTenantId', 'selectedType'));
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_profile_id' => 'required|exists:tenant_profiles,id',
            'type' => 'required|in:rent,amenities,damage,other',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.item_type' => 'required|string',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'tenant_profile_id' => $request->tenant_profile_id,
                'type' => $request->type,
                'status' => 'draft',
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'tax_amount' => $request->tax_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            // Create invoice items
            foreach ($request->items as $itemData) {
                $invoice->items()->create([
                    'item_type' => $itemData['item_type'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'period_start' => $request->period_start,
                    'period_end' => $request->period_end,
                    'related_id' => $itemData['related_id'] ?? null,
                    'related_type' => $itemData['related_type'] ?? null
                ]);
            }

            // Calculate totals
            $invoice->calculateTotals();

            DB::commit();

            // Send invoice notification to tenant
            try {
                $tenant = $invoice->tenantProfile->user;
                $hostel = $invoice->tenantProfile->currentBed->room->hostel ?? null;

                $this->notificationService->sendNotification('invoice_created', $invoice->tenantProfile, [
                    'subject' => 'New Invoice from ' . ($hostel->name ?? 'Hostel') . ' - #' . $invoice->invoice_number,
                    'heading' => 'New Invoice Generated',
                    'body' => "Dear {$tenant->name},\n\n" .
                             "A new invoice has been generated for you. Please find the details below:\n\n" .
                             "Invoice Number: #{$invoice->invoice_number}\n" .
                             "Type: " . ucfirst($invoice->type) . "\n" .
                             "Amount Due: ₹{$invoice->total_amount}\n" .
                             "Due Date: " . $invoice->due_date->format('M j, Y') . "\n" .
                             "Invoice Date: " . $invoice->invoice_date->format('M j, Y') . "\n\n" .
                             "Please click the link below to view and pay your invoice.\n\n" .
                             "Thank you,\nThe " . ($hostel->name ?? 'Hostel') . " Team",
                    'greeting' => $tenant->name,
                    'action_url' => route('invoices.show', $invoice->id),
                    'action_text' => 'View Invoice',
                    'badge_text' => 'New Invoice',
                    'badge_type' => 'info',
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send invoice notification: ' . $e->getMessage());
            }

            return redirect()->route('invoices.show', $invoice->id)
                           ->with('success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to create invoice: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['tenantProfile.user', 'items', 'payments.recordedBy', 'createdBy']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice->id)
                           ->with('error', 'Cannot edit a paid invoice.');
        }

        $invoice->load(['tenantProfile.user', 'items']);
        $tenants = TenantProfile::with('user')->get();

        return view('invoices.edit', compact('invoice', 'tenants'));
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice->id)
                           ->with('error', 'Cannot update a paid invoice.');
        }

        $validator = Validator::make($request->all(), [
            'tenant_profile_id' => 'required|exists:tenant_profiles,id',
            'type' => 'required|in:rent,amenities,damage,other',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.item_type' => 'required|string',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update invoice
            $invoice->update([
                'tenant_profile_id' => $request->tenant_profile_id,
                'type' => $request->type,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'tax_amount' => $request->tax_amount ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'notes' => $request->notes
            ]);

            // Delete existing items and create new ones
            $invoice->items()->delete();
            foreach ($request->items as $itemData) {
                $invoice->items()->create([
                    'item_type' => $itemData['item_type'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'period_start' => $request->period_start,
                    'period_end' => $request->period_end,
                    'related_id' => $itemData['related_id'] ?? null,
                    'related_type' => $itemData['related_type'] ?? null
                ]);
            }

            // Recalculate totals
            $invoice->calculateTotals();

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)
                           ->with('success', 'Invoice updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to update invoice: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->back()
                           ->with('error', 'Cannot delete a paid invoice.');
        }

        if ($invoice->payments()->exists()) {
            return redirect()->back()
                           ->with('error', 'Cannot delete an invoice with payments.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
                       ->with('success', 'Invoice deleted successfully!');
    }

    /**
     * Send invoice to tenant
     */
    public function send(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return redirect()->back()
                           ->with('error', 'Only draft invoices can be sent.');
        }

        $invoice->update(['status' => 'sent']);

        // Here you would typically send email notification
        // Mail::to($invoice->tenantProfile->user->email)->send(new InvoiceNotification($invoice));

        return redirect()->back()
                       ->with('success', 'Invoice sent successfully!');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:send,cancel,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:invoices,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        $invoices = Invoice::whereIn('id', $request->ids)->get();
        $count = 0;

        foreach ($invoices as $invoice) {
            switch ($request->action) {
                case 'send':
                    if ($invoice->status === 'draft') {
                        $invoice->update(['status' => 'sent']);
                        $count++;
                    }
                    break;
                case 'cancel':
                    if ($invoice->status !== 'paid') {
                        $invoice->update(['status' => 'cancelled']);
                        $count++;
                    }
                    break;
                case 'delete':
                    if ($invoice->status !== 'paid' && !$invoice->payments()->exists()) {
                        $invoice->delete();
                        $count++;
                    }
                    break;
            }
        }

        return redirect()->back()
                       ->with('success', "Successfully processed {$count} invoices.");
    }

    /**
     * Generate rent invoice for a tenant
     */
    public function generateRentInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_profile_id' => 'required|exists:tenant_profiles,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tenant = TenantProfile::with(['user', 'currentBed.room'])->findOrFail($request->tenant_profile_id);

        if (!$tenant->currentBed) {
            return response()->json(['error' => 'Tenant is not assigned to any bed'], 400);
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'tenant_profile_id' => $tenant->id,
                'type' => 'rent',
                'status' => 'draft',
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'created_by' => auth()->id() ?? 1
            ]);

            // Add rent item
            $invoice->items()->create([
                'item_type' => 'rent',
                'description' => 'Room Rent - ' . $tenant->currentBed->room->room_number,
                'quantity' => 1,
                'unit_price' => $tenant->monthly_rent,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'related_id' => $tenant->currentBed->room->id,
                'related_type' => Room::class
            ]);

            $invoice->calculateTotals();

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice_id' => $invoice->id,
                'message' => 'Rent invoice generated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to generate invoice: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate amenities invoice for a tenant
     */
    public function generateAmenitiesInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_profile_id' => 'required|exists:tenant_profiles,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tenant = TenantProfile::with(['user', 'activeTenantAmenities.paidAmenity'])->findOrFail($request->tenant_profile_id);

        if ($tenant->activeTenantAmenities->isEmpty()) {
            return response()->json(['error' => 'Tenant has no active paid amenities'], 400);
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'tenant_profile_id' => $tenant->id,
                'type' => 'amenities',
                'status' => 'draft',
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'created_by' => auth()->id() ?? 1
            ]);

            // Add amenity items
            foreach ($tenant->activeTenantAmenities as $tenantAmenity) {
                $periodStart = Carbon::parse($request->period_start);
                $periodEnd = Carbon::parse($request->period_end);
                $days = $periodStart->diffInDays($periodEnd) + 1;

                if ($tenantAmenity->billing_cycle === 'monthly') {
                    $quantity = 1;
                    $unitPrice = $tenantAmenity->monthly_rate;
                } else {
                    $quantity = $days;
                    $unitPrice = $tenantAmenity->daily_rate;
                }

                $invoice->items()->create([
                    'item_type' => 'amenities',
                    'description' => $tenantAmenity->paidAmenity->name . ' - ' . $periodStart->format('M Y'),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'period_start' => $request->period_start,
                    'period_end' => $request->period_end,
                    'related_id' => $tenantAmenity->id,
                    'related_type' => 'App\Models\TenantAmenity'
                ]);
            }

            $invoice->calculateTotals();

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice_id' => $invoice->id,
                'message' => 'Amenities invoice generated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to generate invoice: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download invoice as PDF
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['tenantProfile.user', 'items', 'payments.recordedBy', 'createdBy']);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.invoice', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => public_path(),
            ]);

        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * View invoice PDF in browser
     */
    public function viewPdf(Invoice $invoice)
    {
        $invoice->load(['tenantProfile.user', 'items', 'payments.recordedBy', 'createdBy']);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.invoice', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => public_path(),
            ]);

        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Email invoice PDF to tenant
     */
    public function emailPdf(Invoice $invoice)
    {
        $invoice->load(['tenantProfile.user', 'items', 'payments.recordedBy', 'createdBy']);

        try {
            $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))
                      ->setPaper('a4', 'portrait')
                      ->setOptions([
                          'defaultFont' => 'DejaVu Sans',
                          'isRemoteEnabled' => true,
                          'isHtml5ParserEnabled' => true,
                          'chroot' => public_path(),
                      ]);

            // Here you would send the email with the PDF attachment
            // Mail::to($invoice->tenantProfile->user->email)
            //     ->send(new InvoiceMail($invoice, $pdf->output()));

            return response()->json([
                'success' => true,
                'message' => 'Invoice PDF sent successfully to ' . $invoice->tenantProfile->user->email
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate amenity usage invoice for a tenant
     */
    public function generateAmenityInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_profile_id' => 'required|exists:tenant_profiles,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $periodStart = Carbon::parse($request->period_start);
            $periodEnd = Carbon::parse($request->period_end);

            // Check if there's already an invoice for this period
            if (!Invoice::hasPendingAmenityCharges($request->tenant_profile_id, $periodStart, $periodEnd)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending amenity charges found for this period or invoice already exists.'
                ], 400);
            }

            $invoice = Invoice::createAmenityUsageInvoice(
                $request->tenant_profile_id,
                $periodStart,
                $periodEnd,
                [
                    'status' => $request->get('status', 'draft'),
                    'due_date' => $request->get('due_date') ? Carbon::parse($request->due_date) : now()->addDays(7),
                    'notes' => $request->get('notes')
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Amenity usage invoice generated successfully!',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get amenity usage summary for a tenant and period
     */
    public function getAmenityUsageSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_profile_id' => 'required|exists:tenant_profiles,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $periodStart = Carbon::parse($request->period_start);
            $periodEnd = Carbon::parse($request->period_end);

            $summary = Invoice::getAmenityUsageSummary(
                $request->tenant_profile_id,
                $periodStart,
                $periodEnd
            );

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get usage summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate monthly amenity invoices for all tenants
     */
    public function generateMonthlyAmenityInvoices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid month format. Please use YYYY-MM format.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $month = Carbon::createFromFormat('Y-m', $request->month);
            $results = Invoice::generateMonthlyAmenityInvoices($month);

            return response()->json([
                'success' => true,
                'message' => 'Monthly amenity invoices generation completed!',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate monthly invoices: ' . $e->getMessage()
            ], 500);
        }
    }
}
