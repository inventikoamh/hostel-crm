<?php

namespace App\Http\Controllers\Api\V1\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\TenantProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Payment::with(['invoice', 'tenantProfile.user', 'recordedBy', 'verifiedBy']);

            // Apply filters
            if ($request->has('invoice_id')) {
                $query->where('invoice_id', $request->invoice_id);
            }

            if ($request->has('tenant_profile_id')) {
                $query->where('tenant_profile_id', $request->tenant_profile_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->has('payment_date_from')) {
                $query->where('payment_date', '>=', $request->payment_date_from);
            }

            if ($request->has('payment_date_to')) {
                $query->where('payment_date', '<=', $request->payment_date_to);
            }

            if ($request->has('is_verified')) {
                if ($request->boolean('is_verified')) {
                    $query->whereNotNull('verified_at');
                } else {
                    $query->whereNull('verified_at');
                }
            }

            if ($request->has('amount_min')) {
                $query->where('amount', '>=', $request->amount_min);
            }

            if ($request->has('amount_max')) {
                $query->where('amount', '<=', $request->amount_max);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('payment_number', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%")
                      ->orWhere('bank_name', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('tenantProfile.user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereHas('invoice', function ($invoiceQuery) use ($search) {
                          $invoiceQuery->where('invoice_number', 'like', "%{$search}%");
                      });
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Handle sorting by tenant name
            if ($sortBy === 'tenant_name') {
                $query->join('tenant_profiles', 'payments.tenant_profile_id', '=', 'tenant_profiles.id')
                      ->join('users', 'tenant_profiles.user_id', '=', 'users.id')
                      ->orderBy('users.name', $sortOrder)
                      ->select('payments.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $payments = $query->paginate($perPage);

            // Transform data
            $payments->getCollection()->transform(function ($payment) {
                return $this->transformPayment($payment);
            });

            return response()->json([
                'success' => true,
                'message' => 'Payments retrieved successfully',
                'data' => $payments->items(),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                    'from' => $payments->firstItem(),
                    'to' => $payments->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new payment (GET version for testing)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Payment creation form data',
                'data' => [
                    'required_fields' => [
                        'invoice_id' => 'Invoice ID (required)',
                        'tenant_profile_id' => 'Tenant Profile ID (required)',
                        'amount' => 'Payment amount (required)',
                        'payment_date' => 'Payment date (required)',
                        'payment_method' => 'Payment method: cash, bank_transfer, upi, card, cheque, other (required)',
                        'status' => 'Payment status: pending, completed, failed, cancelled (required)'
                    ],
                    'optional_fields' => [
                        'reference_number' => 'Transaction reference number',
                        'bank_name' => 'Bank name (for bank transfers)',
                        'account_number' => 'Account number (for bank transfers)',
                        'notes' => 'Payment notes',
                        'metadata' => 'Additional payment metadata'
                    ],
                    'example_request' => [
                        'invoice_id' => 1,
                        'tenant_profile_id' => 1,
                        'amount' => 500.00,
                        'payment_date' => '2024-01-15',
                        'payment_method' => 'bank_transfer',
                        'status' => 'completed',
                        'reference_number' => 'TXN123456789',
                        'bank_name' => 'State Bank',
                        'notes' => 'Partial payment received'
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/payments for actual creation.'
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
     * Store a newly created payment
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'invoice_id' => 'required|exists:invoices,id',
                'tenant_profile_id' => 'required|exists:tenant_profiles,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_date' => 'required|date',
                'payment_method' => ['required', Rule::in(['cash', 'bank_transfer', 'upi', 'card', 'cheque', 'other'])],
                'status' => ['required', Rule::in(['pending', 'completed', 'failed', 'cancelled'])],
                'reference_number' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:255',
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

            // Get the invoice to validate payment amount
            $invoice = Invoice::find($validated['invoice_id']);

            // Check if payment amount exceeds invoice balance
            if ($validated['amount'] > $invoice->balance_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed invoice balance'
                ], 422);
            }

            // Generate payment number
            $validated['payment_number'] = $this->generatePaymentNumber();

            // Set recorded_by
            $validated['recorded_by'] = auth()->id() ?? 1;

            $payment = Payment::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Payment created successfully',
                'data' => $this->transformPayment($payment->load(['invoice', 'tenantProfile.user', 'recordedBy', 'verifiedBy']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified payment
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $payment = Payment::with(['invoice', 'tenantProfile.user', 'recordedBy', 'verifiedBy'])
                ->find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment retrieved successfully',
                'data' => $this->transformPayment($payment, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $payment = Payment::find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'invoice_id' => 'sometimes|required|exists:invoices,id',
                'tenant_profile_id' => 'sometimes|required|exists:tenant_profiles,id',
                'amount' => 'sometimes|required|numeric|min:0.01',
                'payment_date' => 'sometimes|required|date',
                'payment_method' => ['sometimes', 'required', Rule::in(['cash', 'bank_transfer', 'upi', 'card', 'cheque', 'other'])],
                'status' => ['sometimes', 'required', Rule::in(['pending', 'completed', 'failed', 'cancelled'])],
                'reference_number' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:255',
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

            // If amount is being updated, validate against invoice balance
            if (isset($validated['amount'])) {
                $invoice = Invoice::find($validated['invoice_id'] ?? $payment->invoice_id);
                $otherPaymentsTotal = $invoice->payments()
                    ->where('id', '!=', $id)
                    ->where('status', 'completed')
                    ->sum('amount');
                $newBalance = $invoice->total_amount - $otherPaymentsTotal - $validated['amount'];

                if ($newBalance < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment amount would exceed invoice balance'
                    ], 422);
                }
            }

            $payment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' => $this->transformPayment($payment->load(['invoice', 'tenantProfile.user', 'recordedBy', 'verifiedBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified payment
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $payment = Payment::find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            // Check if payment is verified
            if ($payment->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete verified payment. Please cancel the payment instead.'
                ], 422);
            }

            $payment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function stats(Request $request, $id): JsonResponse
    {
        try {
            $payment = Payment::with(['invoice', 'tenantProfile.user', 'recordedBy', 'verifiedBy'])->find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            $stats = [
                'basic_info' => [
                    'payment_number' => $payment->payment_number,
                    'amount' => $payment->amount,
                    'formatted_amount' => $payment->formatted_amount,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'method_badge' => $payment->method_badge,
                    'status' => $payment->status,
                    'status_badge' => $payment->status_badge,
                    'is_verified' => $payment->is_verified,
                ],
                'invoice_info' => [
                    'invoice_id' => $payment->invoice->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                    'invoice_type' => $payment->invoice->type,
                    'invoice_status' => $payment->invoice->status,
                    'invoice_total' => $payment->invoice->total_amount,
                    'invoice_balance' => $payment->invoice->balance_amount,
                ],
                'tenant_info' => [
                    'tenant_profile_id' => $payment->tenant_profile_id,
                    'tenant_name' => $payment->tenantProfile->user->name,
                    'tenant_email' => $payment->tenantProfile->user->email,
                    'tenant_phone' => $payment->tenantProfile->phone,
                ],
                'payment_details' => [
                    'reference_number' => $payment->reference_number,
                    'bank_name' => $payment->bank_name,
                    'account_number' => $payment->account_number,
                    'notes' => $payment->notes,
                    'metadata' => $payment->metadata,
                ],
                'verification_info' => [
                    'verified_at' => $payment->verified_at,
                    'verified_by' => $payment->verifiedBy ? [
                        'id' => $payment->verifiedBy->id,
                        'name' => $payment->verifiedBy->name,
                    ] : null,
                ],
                'recorded_info' => [
                    'recorded_by' => $payment->recordedBy ? [
                        'id' => $payment->recordedBy->id,
                        'name' => $payment->recordedBy->name,
                    ] : null,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Payment statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a payment
     */
    public function verify(Request $request, $id): JsonResponse
    {
        try {
            $payment = Payment::find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            if ($payment->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment is already verified'
                ], 422);
            }

            $payment->verify();

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'data' => $this->transformPayment($payment->load(['invoice', 'tenantProfile.user', 'recordedBy', 'verifiedBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a payment
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            $payment = Payment::find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            if ($payment->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment is already cancelled'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $payment->cancel($request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Payment cancelled successfully',
                'data' => $this->transformPayment($payment->load(['invoice', 'tenantProfile.user', 'recordedBy', 'verifiedBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment summary for a tenant
     */
    public function tenantSummary(Request $request, $tenantId): JsonResponse
    {
        try {
            $tenantProfile = TenantProfile::find($tenantId);

            if (!$tenantProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            $payments = Payment::where('tenant_profile_id', $tenantId)
                ->with(['invoice'])
                ->get();

            $summary = [
                'tenant_info' => [
                    'id' => $tenantProfile->id,
                    'name' => $tenantProfile->user->name,
                    'email' => $tenantProfile->user->email,
                ],
                'payment_summary' => [
                    'total_payments' => $payments->count(),
                    'total_amount' => $payments->sum('amount'),
                    'formatted_total_amount' => '₹' . number_format($payments->sum('amount'), 2),
                    'completed_payments' => $payments->where('status', 'completed')->count(),
                    'pending_payments' => $payments->where('status', 'pending')->count(),
                    'failed_payments' => $payments->where('status', 'failed')->count(),
                    'cancelled_payments' => $payments->where('status', 'cancelled')->count(),
                ],
                'payment_methods' => $payments->groupBy('payment_method')->map(function ($methodPayments) {
                    return [
                        'count' => $methodPayments->count(),
                        'total_amount' => $methodPayments->sum('amount'),
                        'formatted_total_amount' => '₹' . number_format($methodPayments->sum('amount'), 2),
                    ];
                }),
                'recent_payments' => $payments->sortByDesc('created_at')->take(5)->map(function ($payment) {
                    return $this->transformPayment($payment);
                }),
                'monthly_summary' => $this->getMonthlyPaymentSummary($payments),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Tenant payment summary retrieved successfully',
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tenant payment summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment summary for an invoice
     */
    public function invoiceSummary(Request $request, $invoiceId): JsonResponse
    {
        try {
            $invoice = Invoice::find($invoiceId);

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }

            $payments = Payment::where('invoice_id', $invoiceId)->get();

            $summary = [
                'invoice_info' => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'total_amount' => $invoice->total_amount,
                    'formatted_total_amount' => $invoice->formatted_total_amount,
                    'balance_amount' => $invoice->balance_amount,
                    'formatted_balance_amount' => $invoice->formatted_balance_amount,
                ],
                'payment_summary' => [
                    'total_payments' => $payments->count(),
                    'total_paid' => $payments->where('status', 'completed')->sum('amount'),
                    'formatted_total_paid' => '₹' . number_format($payments->where('status', 'completed')->sum('amount'), 2),
                    'completed_payments' => $payments->where('status', 'completed')->count(),
                    'pending_payments' => $payments->where('status', 'pending')->count(),
                    'failed_payments' => $payments->where('status', 'failed')->count(),
                    'cancelled_payments' => $payments->where('status', 'cancelled')->count(),
                ],
                'payments' => $payments->map(function ($payment) {
                    return $this->transformPayment($payment);
                }),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Invoice payment summary retrieved successfully',
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoice payment summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search payments
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'status' => ['nullable', Rule::in(['pending', 'completed', 'failed', 'cancelled'])],
                'payment_method' => ['nullable', Rule::in(['cash', 'bank_transfer', 'upi', 'card', 'cheque', 'other'])],
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
            $status = $request->status;
            $paymentMethod = $request->payment_method;
            $limit = $request->get('limit', 10);

            $paymentsQuery = Payment::with(['invoice', 'tenantProfile.user'])
                ->where(function ($q) use ($query) {
                    $q->where('payment_number', 'like', "%{$query}%")
                      ->orWhere('reference_number', 'like', "%{$query}%")
                      ->orWhere('bank_name', 'like', "%{$query}%")
                      ->orWhere('notes', 'like', "%{$query}%")
                      ->orWhereHas('tenantProfile.user', function ($userQuery) use ($query) {
                          $userQuery->where('name', 'like', "%{$query}%")
                                   ->orWhere('email', 'like', "%{$query}%");
                      })
                      ->orWhereHas('invoice', function ($invoiceQuery) use ($query) {
                          $invoiceQuery->where('invoice_number', 'like', "%{$query}%");
                      });
                });

            if ($status) {
                $paymentsQuery->where('status', $status);
            }

            if ($paymentMethod) {
                $paymentsQuery->where('payment_method', $paymentMethod);
            }

            $payments = $paymentsQuery->limit($limit)->get()->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'payment_number' => $payment->payment_number,
                    'amount' => $payment->amount,
                    'formatted_amount' => $payment->formatted_amount,
                    'payment_date' => $payment->payment_date,
                    'payment_method' => $payment->payment_method,
                    'method_badge' => $payment->method_badge,
                    'status' => $payment->status,
                    'status_badge' => $payment->status_badge,
                    'reference_number' => $payment->reference_number,
                    'is_verified' => $payment->is_verified,
                    'invoice' => [
                        'id' => $payment->invoice->id,
                        'invoice_number' => $payment->invoice->invoice_number,
                        'type' => $payment->invoice->type,
                    ],
                    'tenant' => [
                        'id' => $payment->tenantProfile->user->id,
                        'name' => $payment->tenantProfile->user->name,
                        'email' => $payment->tenantProfile->user->email,
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $payments,
                'query' => $query,
                'count' => $payments->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Transform payment data for API response
     */
    private function transformPayment(Payment $payment, bool $detailed = false): array
    {
        $data = [
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
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'invoice' => [
                    'id' => $payment->invoice->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                    'type' => $payment->invoice->type,
                    'type_badge' => $payment->invoice->type_badge,
                    'status' => $payment->invoice->status,
                    'status_badge' => $payment->invoice->status_badge,
                    'total_amount' => $payment->invoice->total_amount,
                    'formatted_total_amount' => $payment->invoice->formatted_total_amount,
                    'balance_amount' => $payment->invoice->balance_amount,
                    'formatted_balance_amount' => $payment->invoice->formatted_balance_amount,
                ],
                'tenant' => [
                    'id' => $payment->tenantProfile->user->id,
                    'name' => $payment->tenantProfile->user->name,
                    'email' => $payment->tenantProfile->user->email,
                    'phone' => $payment->tenantProfile->phone,
                ],
                'recorded_by' => $payment->recordedBy ? [
                    'id' => $payment->recordedBy->id,
                    'name' => $payment->recordedBy->name,
                ] : null,
                'verified_by' => $payment->verifiedBy ? [
                    'id' => $payment->verifiedBy->id,
                    'name' => $payment->verifiedBy->name,
                ] : null,
            ]);
        }

        return $data;
    }

    /**
     * Generate unique payment number
     */
    private function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');

        $lastPayment = Payment::where('payment_number', 'like', "{$prefix}-{$year}{$month}-%")
                             ->orderBy('payment_number', 'desc')
                             ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }

    /**
     * Get monthly payment summary
     */
    private function getMonthlyPaymentSummary($payments)
    {
        $monthlyData = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('Y-m');
        })->map(function ($monthPayments) {
            return [
                'month' => $monthPayments->first()->payment_date->format('M Y'),
                'count' => $monthPayments->count(),
                'total_amount' => $monthPayments->sum('amount'),
                'formatted_total_amount' => '₹' . number_format($monthPayments->sum('amount'), 2),
                'completed_count' => $monthPayments->where('status', 'completed')->count(),
                'completed_amount' => $monthPayments->where('status', 'completed')->sum('amount'),
            ];
        });

        return $monthlyData->sortByDesc(function ($data, $key) {
            return $key;
        })->values();
    }
}
