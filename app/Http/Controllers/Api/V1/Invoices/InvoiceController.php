<?php

namespace App\Http\Controllers\Api\V1\Invoices;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\TenantProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Invoice::with(['tenantProfile.user', 'items', 'payments', 'createdBy']);

            // Apply filters
            if ($request->has('tenant_profile_id')) {
                $query->where('tenant_profile_id', $request->tenant_profile_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('invoice_date_from')) {
                $query->where('invoice_date', '>=', $request->invoice_date_from);
            }

            if ($request->has('invoice_date_to')) {
                $query->where('invoice_date', '<=', $request->invoice_date_to);
            }

            if ($request->has('due_date_from')) {
                $query->where('due_date', '>=', $request->due_date_from);
            }

            if ($request->has('due_date_to')) {
                $query->where('due_date', '<=', $request->due_date_to);
            }

            if ($request->has('is_overdue')) {
                if ($request->boolean('is_overdue')) {
                    $query->overdue();
                } else {
                    $query->where(function ($q) {
                        $q->where('status', '!=', 'overdue')
                          ->orWhere('due_date', '>=', now());
                    });
                }
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('tenantProfile.user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Handle sorting by tenant name
            if ($sortBy === 'tenant_name') {
                $query->join('tenant_profiles', 'invoices.tenant_profile_id', '=', 'tenant_profiles.id')
                      ->join('users', 'tenant_profiles.user_id', '=', 'users.id')
                      ->orderBy('users.name', $sortOrder)
                      ->select('invoices.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $invoices = $query->paginate($perPage);

            // Transform data
            $invoices->getCollection()->transform(function ($invoice) {
                return $this->transformInvoice($invoice);
            });

            return response()->json([
                'success' => true,
                'message' => 'Invoices retrieved successfully',
                'data' => $invoices->items(),
                'pagination' => [
                    'current_page' => $invoices->currentPage(),
                    'last_page' => $invoices->lastPage(),
                    'per_page' => $invoices->perPage(),
                    'total' => $invoices->total(),
                    'from' => $invoices->firstItem(),
                    'to' => $invoices->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new invoice (GET version for testing)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Invoice creation form data',
                'data' => [
                    'required_fields' => [
                        'tenant_profile_id' => 'Tenant Profile ID (required)',
                        'type' => 'Invoice type: rent, amenities, damage, other (required)',
                        'invoice_date' => 'Invoice date (required)',
                        'due_date' => 'Due date (required)',
                        'total_amount' => 'Total amount (required)'
                    ],
                    'optional_fields' => [
                        'period_start' => 'Period start date (for recurring charges)',
                        'period_end' => 'Period end date (for recurring charges)',
                        'subtotal' => 'Subtotal amount',
                        'tax_amount' => 'Tax amount',
                        'discount_amount' => 'Discount amount',
                        'paid_amount' => 'Paid amount',
                        'balance_amount' => 'Balance amount',
                        'notes' => 'Invoice notes',
                        'terms_conditions' => 'Terms and conditions',
                        'metadata' => 'Additional metadata',
                        'status' => 'Invoice status: draft, sent, paid, overdue, cancelled'
                    ],
                    'example_request' => [
                        'tenant_profile_id' => 1,
                        'type' => 'rent',
                        'invoice_date' => '2024-01-01',
                        'due_date' => '2024-01-31',
                        'period_start' => '2024-01-01',
                        'period_end' => '2024-01-31',
                        'subtotal' => 1000.00,
                        'tax_amount' => 0.00,
                        'discount_amount' => 0.00,
                        'total_amount' => 1000.00,
                        'paid_amount' => 0.00,
                        'balance_amount' => 1000.00,
                        'notes' => 'Monthly rent for January 2024',
                        'status' => 'draft'
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/invoices for actual creation.'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve creation form data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'tenant_profile_id' => 'required|exists:tenant_profiles,id',
                'type' => ['required', Rule::in(['rent', 'amenities', 'damage', 'other'])],
                'status' => ['required', Rule::in(['draft', 'sent', 'paid', 'overdue', 'cancelled'])],
                'invoice_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:invoice_date',
                'period_start' => 'nullable|date',
                'period_end' => 'nullable|date|after_or_equal:period_start',
                'subtotal' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'balance_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Generate invoice number
            $validated['invoice_number'] = Invoice::generateInvoiceNumber();

            // Set created_by
            $validated['created_by'] = auth()->id() ?? 1;

            // Calculate balance if not provided
            if (!isset($validated['balance_amount'])) {
                $validated['balance_amount'] = $validated['total_amount'] - ($validated['paid_amount'] ?? 0);
            }

            $invoice = Invoice::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $this->transformInvoice($invoice->load(['tenantProfile.user', 'items', 'payments', 'createdBy']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $invoice = Invoice::with(['tenantProfile.user', 'items', 'payments.recordedBy', 'payments.verifiedBy', 'createdBy'])
                ->find($id);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice retrieved successfully',
                'data' => $this->transformInvoice($invoice, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'tenant_profile_id' => 'sometimes|required|exists:tenant_profiles,id',
                'type' => ['sometimes', 'required', Rule::in(['rent', 'amenities', 'damage', 'other'])],
                'status' => ['sometimes', 'required', Rule::in(['draft', 'sent', 'paid', 'overdue', 'cancelled'])],
                'invoice_date' => 'sometimes|required|date',
                'due_date' => 'sometimes|required|date|after_or_equal:invoice_date',
                'period_start' => 'nullable|date',
                'period_end' => 'nullable|date|after_or_equal:period_start',
                'subtotal' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'sometimes|required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'balance_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Recalculate totals if needed
            if (isset($validated['subtotal']) || isset($validated['tax_amount']) || isset($validated['discount_amount'])) {
                $subtotal = $validated['subtotal'] ?? $invoice->subtotal;
                $taxAmount = $validated['tax_amount'] ?? $invoice->tax_amount;
                $discountAmount = $validated['discount_amount'] ?? $invoice->discount_amount;
                $validated['total_amount'] = $subtotal + $taxAmount - $discountAmount;
            }

            // Recalculate balance if needed
            if (isset($validated['total_amount']) || isset($validated['paid_amount'])) {
                $totalAmount = $validated['total_amount'] ?? $invoice->total_amount;
                $paidAmount = $validated['paid_amount'] ?? $invoice->paid_amount;
                $validated['balance_amount'] = $totalAmount - $paidAmount;
            }

            $invoice->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully',
                'data' => $this->transformInvoice($invoice->load(['tenantProfile.user', 'items', 'payments', 'createdBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            // Check if invoice has payments
            if ($invoice->payments()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete invoice with payments. Please delete payments first or cancel the invoice.'
                ], 422);
            }

            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice statistics
     */
    public function stats(Request $request, $id): JsonResponse
    {
        try {
            $invoice = Invoice::with(['tenantProfile.user', 'items', 'payments'])->find($id);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            $stats = [
                'basic_info' => [
                    'invoice_number' => $invoice->invoice_number,
                    'type' => $invoice->type,
                    'type_badge' => $invoice->type_badge,
                    'status' => $invoice->status,
                    'status_badge' => $invoice->status_badge,
                    'invoice_date' => $invoice->invoice_date,
                    'due_date' => $invoice->due_date,
                    'period_start' => $invoice->period_start,
                    'period_end' => $invoice->period_end,
                ],
                'financial_info' => [
                    'subtotal' => $invoice->subtotal,
                    'tax_amount' => $invoice->tax_amount,
                    'discount_amount' => $invoice->discount_amount,
                    'total_amount' => $invoice->total_amount,
                    'formatted_total_amount' => $invoice->formatted_total_amount,
                    'paid_amount' => $invoice->paid_amount,
                    'formatted_paid_amount' => $invoice->formatted_paid_amount,
                    'balance_amount' => $invoice->balance_amount,
                    'formatted_balance_amount' => $invoice->formatted_balance_amount,
                    'payment_status' => $invoice->payment_status,
                ],
                'overdue_info' => [
                    'is_overdue' => $invoice->is_overdue,
                    'days_overdue' => $invoice->days_overdue,
                ],
                'tenant_info' => [
                    'tenant_profile_id' => $invoice->tenant_profile_id,
                    'tenant_name' => $invoice->tenantProfile->user->name,
                    'tenant_email' => $invoice->tenantProfile->user->email,
                ],
                'items_summary' => [
                    'total_items' => $invoice->items->count(),
                    'items_by_type' => $invoice->items->groupBy('item_type')->map(function ($items) {
                        return [
                            'count' => $items->count(),
                            'total_amount' => $items->sum('total_price')
                        ];
                    }),
                ],
                'payments_summary' => [
                    'total_payments' => $invoice->payments->count(),
                    'completed_payments' => $invoice->payments->where('status', 'completed')->count(),
                    'pending_payments' => $invoice->payments->where('status', 'pending')->count(),
                    'total_paid_amount' => $invoice->payments->where('status', 'completed')->sum('amount'),
                ],
                'created_info' => [
                    'created_by' => $invoice->createdBy ? [
                        'id' => $invoice->createdBy->id,
                        'name' => $invoice->createdBy->name,
                    ] : null,
                    'created_at' => $invoice->created_at,
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Invoice statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoice statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add payment to invoice
     */
    public function addPayment(Request $request, $id): JsonResponse
    {
        try {
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0.01',
                'payment_date' => 'nullable|date',
                'payment_method' => ['required', Rule::in(['cash', 'bank_transfer', 'upi', 'card', 'cheque', 'other'])],
                'status' => ['nullable', Rule::in(['pending', 'completed', 'failed', 'cancelled'])],
                'reference_number' => 'nullable|string',
                'bank_name' => 'nullable|string',
                'account_number' => 'nullable|string',
                'notes' => 'nullable|string',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Check if payment amount exceeds balance
            if ($validated['amount'] > $invoice->balance_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed invoice balance'
                ], 422);
            }

            $payment = $invoice->addPayment($validated['amount'], $validated);

            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully',
                'data' => [
                    'payment' => $this->transformPayment($payment),
                    'invoice' => $this->transformInvoice($invoice->fresh(['tenantProfile.user', 'items', 'payments']))
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark invoice as overdue
     */
    public function markOverdue(Request $request, $id): JsonResponse
    {
        try {
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            $invoice->markAsOverdue();

            return response()->json([
                'success' => true,
                'message' => 'Invoice marked as overdue successfully',
                'data' => $this->transformInvoice($invoice->load(['tenantProfile.user', 'items', 'payments']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark invoice as overdue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate amenity usage invoice
     */
    public function generateAmenityInvoice(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'tenant_profile_id' => 'required|exists:tenant_profiles,id',
                'period_start' => 'required|date',
                'period_end' => 'required|date|after_or_equal:period_start',
                'status' => ['nullable', Rule::in(['draft', 'sent', 'paid', 'overdue', 'cancelled'])],
                'invoice_date' => 'nullable|date',
                'due_date' => 'nullable|date|after_or_equal:invoice_date',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            $invoice = Invoice::createAmenityUsageInvoice(
                $validated['tenant_profile_id'],
                Carbon::parse($validated['period_start']),
                Carbon::parse($validated['period_end']),
                [
                    'status' => $validated['status'] ?? 'sent',
                    'invoice_date' => $validated['invoice_date'] ?? now(),
                    'due_date' => $validated['due_date'] ?? now()->addDays(7),
                    'notes' => $validated['notes'] ?? "Amenity usage charges for " . Carbon::parse($validated['period_start'])->format('M Y'),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Amenity usage invoice generated successfully',
                'data' => $this->transformInvoice($invoice->load(['tenantProfile.user', 'items', 'payments']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate amenity usage invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search invoices
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'type' => ['nullable', Rule::in(['rent', 'amenities', 'damage', 'other'])],
                'status' => ['nullable', Rule::in(['draft', 'sent', 'paid', 'overdue', 'cancelled'])],
                'limit' => 'nullable|integer|min:1|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = $request->query;
            $type = $request->type;
            $status = $request->status;
            $limit = $request->get('limit', 10);

            $invoicesQuery = Invoice::with(['tenantProfile.user'])
                ->where(function ($q) use ($query) {
                    $q->where('invoice_number', 'like', "%{$query}%")
                      ->orWhere('notes', 'like', "%{$query}%")
                      ->orWhereHas('tenantProfile.user', function ($userQuery) use ($query) {
                          $userQuery->where('name', 'like', "%{$query}%")
                                   ->orWhere('email', 'like', "%{$query}%");
                      });
                });

            if ($type) {
                $invoicesQuery->where('type', $type);
            }

            if ($status) {
                $invoicesQuery->where('status', $status);
            }

            $invoices = $invoicesQuery->limit($limit)->get()->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'type' => $invoice->type,
                    'type_badge' => $invoice->type_badge,
                    'status' => $invoice->status,
                    'status_badge' => $invoice->status_badge,
                    'total_amount' => $invoice->total_amount,
                    'formatted_total_amount' => $invoice->formatted_total_amount,
                    'balance_amount' => $invoice->balance_amount,
                    'invoice_date' => $invoice->invoice_date,
                    'due_date' => $invoice->due_date,
                    'tenant' => [
                        'id' => $invoice->tenantProfile->user->id,
                        'name' => $invoice->tenantProfile->user->name,
                        'email' => $invoice->tenantProfile->user->email,
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $invoices,
                'query' => $query,
                'count' => $invoices->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search invoices',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== INVOICE ITEMS API ====================

    /**
     * Add item to invoice
     */
    public function addItem(Request $request, $id): JsonResponse
    {
        try {
            $invoice = Invoice::find($id);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'item_type' => 'required|string',
                'description' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0',
                'related_id' => 'nullable|integer',
                'related_type' => 'nullable|string',
                'period_start' => 'nullable|date',
                'period_end' => 'nullable|date|after_or_equal:period_start',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            $item = $invoice->items()->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data' => [
                    'item' => $this->transformInvoiceItem($item),
                    'invoice' => $this->transformInvoice($invoice->fresh(['tenantProfile.user', 'items', 'payments']))
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update invoice item
     */
    public function updateItem(Request $request, $invoiceId, $itemId): JsonResponse
    {
        try {
            $invoice = Invoice::find($invoiceId);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            $item = $invoice->items()->find($itemId);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice item not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'item_type' => 'sometimes|required|string',
                'description' => 'sometimes|required|string',
                'quantity' => 'sometimes|required|integer|min:1',
                'unit_price' => 'sometimes|required|numeric|min:0',
                'related_id' => 'nullable|integer',
                'related_type' => 'nullable|string',
                'period_start' => 'nullable|date',
                'period_end' => 'nullable|date|after_or_equal:period_start',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            $item->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => [
                    'item' => $this->transformInvoiceItem($item),
                    'invoice' => $this->transformInvoice($invoice->fresh(['tenantProfile.user', 'items', 'payments']))
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove invoice item
     */
    public function removeItem(Request $request, $invoiceId, $itemId): JsonResponse
    {
        try {
            $invoice = Invoice::find($invoiceId);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            $item = $invoice->items()->find($itemId);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice item not found'
                ], 404);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed successfully',
                'data' => $this->transformInvoice($invoice->fresh(['tenantProfile.user', 'items', 'payments']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Transform invoice data for API response
     */
    private function transformInvoice(Invoice $invoice, bool $detailed = false): array
    {
        $data = [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'tenant_profile_id' => $invoice->tenant_profile_id,
            'type' => $invoice->type,
            'type_badge' => $invoice->type_badge,
            'status' => $invoice->status,
            'status_badge' => $invoice->status_badge,
            'invoice_date' => $invoice->invoice_date,
            'due_date' => $invoice->due_date,
            'period_start' => $invoice->period_start,
            'period_end' => $invoice->period_end,
            'subtotal' => $invoice->subtotal,
            'tax_amount' => $invoice->tax_amount,
            'discount_amount' => $invoice->discount_amount,
            'total_amount' => $invoice->total_amount,
            'formatted_total_amount' => $invoice->formatted_total_amount,
            'paid_amount' => $invoice->paid_amount,
            'formatted_paid_amount' => $invoice->formatted_paid_amount,
            'balance_amount' => $invoice->balance_amount,
            'formatted_balance_amount' => $invoice->formatted_balance_amount,
            'payment_status' => $invoice->payment_status,
            'is_overdue' => $invoice->is_overdue,
            'days_overdue' => $invoice->days_overdue,
            'notes' => $invoice->notes,
            'terms_conditions' => $invoice->terms_conditions,
            'metadata' => $invoice->metadata,
            'paid_at' => $invoice->paid_at,
            'created_at' => $invoice->created_at,
            'updated_at' => $invoice->updated_at,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'tenant' => [
                    'id' => $invoice->tenantProfile->user->id,
                    'name' => $invoice->tenantProfile->user->name,
                    'email' => $invoice->tenantProfile->user->email,
                    'phone' => $invoice->tenantProfile->phone,
                ],
                'items' => $invoice->items->map(function ($item) {
                    return $this->transformInvoiceItem($item);
                }),
                'payments' => $invoice->payments->map(function ($payment) {
                    return $this->transformPayment($payment);
                }),
                'created_by' => $invoice->createdBy ? [
                    'id' => $invoice->createdBy->id,
                    'name' => $invoice->createdBy->name,
                ] : null,
            ]);
        }

        return $data;
    }

    /**
     * Transform invoice item data for API response
     */
    private function transformInvoiceItem(InvoiceItem $item): array
    {
        return [
            'id' => $item->id,
            'invoice_id' => $item->invoice_id,
            'item_type' => $item->item_type,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'formatted_unit_price' => $item->formatted_unit_price,
            'total_price' => $item->total_price,
            'formatted_total_price' => $item->formatted_total_price,
            'related_id' => $item->related_id,
            'related_type' => $item->related_type,
            'period_start' => $item->period_start,
            'period_end' => $item->period_end,
            'period_text' => $item->period_text,
            'metadata' => $item->metadata,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    }

    /**
     * Transform payment data for API response
     */
    private function transformPayment(Payment $payment): array
    {
        return [
            'id' => $payment->id,
            'payment_number' => $payment->payment_number,
            'invoice_id' => $payment->invoice_id,
            'tenant_profile_id' => $payment->tenant_profile_id,
            'amount' => $payment->amount,
            'formatted_amount' => $payment->formatted_amount,
            'payment_date' => $payment->payment_date,
            'payment_method' => $payment->payment_method,
            'method_badge' => $payment->method_badge,
            'status' => $payment->status,
            'status_badge' => $payment->status_badge,
            'reference_number' => $payment->reference_number,
            'bank_name' => $payment->bank_name,
            'account_number' => $payment->account_number,
            'notes' => $payment->notes,
            'metadata' => $payment->metadata,
            'is_verified' => $payment->is_verified,
            'verified_at' => $payment->verified_at,
            'recorded_by' => $payment->recordedBy ? [
                'id' => $payment->recordedBy->id,
                'name' => $payment->recordedBy->name,
            ] : null,
            'verified_by' => $payment->verifiedBy ? [
                'id' => $payment->verifiedBy->id,
                'name' => $payment->verifiedBy->name,
            ] : null,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at,
        ];
    }
}
