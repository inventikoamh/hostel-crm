<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\TenantProfile;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['invoice', 'tenantProfile.user', 'recordedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_profile_id', $request->tenant_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('tenantProfile.user', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->get()->map(function ($payment) {
            return [
                'id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'invoice_number' => $payment->invoice->invoice_number,
                'tenant_name' => $payment->tenantProfile->user->name,
                'amount' => $payment->formatted_amount,
                'payment_date' => $payment->payment_date->format('M j, Y'),
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'reference_number' => $payment->reference_number,
                'recorded_by' => $payment->recordedBy->name,
                'is_verified' => $payment->is_verified,
                'view_url' => route('payments.show', $payment->id),
                'edit_url' => route('payments.edit', $payment->id),
                'delete_url' => route('payments.destroy', $payment->id)
            ];
        });

        // Statistics
        $stats = [
            'total' => $payments->count(),
            'completed' => $payments->where('status', 'completed')->count(),
            'pending' => $payments->where('status', 'pending')->count(),
            'failed' => $payments->where('status', 'failed')->count(),
            'total_amount' => $payments->sum(function ($payment) {
                return (float) str_replace(['₹', ','], '', $payment['amount']);
            })
        ];

        // Table configuration
        $columns = [
            ['key' => 'payment_number', 'label' => 'Payment #', 'width' => 'w-32'],
            ['key' => 'invoice_number', 'label' => 'Invoice #', 'width' => 'w-32'],
            ['key' => 'tenant_name', 'label' => 'Tenant', 'width' => 'w-40'],
            ['key' => 'amount', 'label' => 'Amount', 'width' => 'w-24'],
            ['key' => 'payment_date', 'label' => 'Date', 'width' => 'w-28'],
            ['key' => 'payment_method', 'label' => 'Method', 'component' => 'components.status-badge', 'width' => 'w-24'],
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
                    ['value' => 'pending', 'label' => 'Pending'],
                    ['value' => 'completed', 'label' => 'Completed'],
                    ['value' => 'failed', 'label' => 'Failed'],
                    ['value' => 'cancelled', 'label' => 'Cancelled']
                ]
            ],
            [
                'key' => 'payment_method',
                'label' => 'Method',
                'type' => 'select',
                'options' => [
                    ['value' => 'cash', 'label' => 'Cash'],
                    ['value' => 'bank_transfer', 'label' => 'Bank Transfer'],
                    ['value' => 'upi', 'label' => 'UPI'],
                    ['value' => 'card', 'label' => 'Card'],
                    ['value' => 'cheque', 'label' => 'Cheque'],
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
            ['key' => 'verify', 'label' => 'Verify Selected', 'icon' => 'fas fa-check'],
            ['key' => 'cancel', 'label' => 'Cancel Selected', 'icon' => 'fas fa-times'],
            ['key' => 'delete', 'label' => 'Delete Selected', 'icon' => 'fas fa-trash']
        ];

        $pagination = [
            'from' => 1,
            'to' => $payments->count(),
            'total' => $payments->count(),
            'current_page' => 1,
            'per_page' => 25
        ];

        return view('payments.index', compact('payments', 'stats', 'columns', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(Request $request)
    {
        $invoiceId = $request->query('invoice_id');
        $invoice = null;

        if ($invoiceId) {
            $invoice = Invoice::with(['tenantProfile.user', 'items'])->findOrFail($invoiceId);
        }

        $invoices = Invoice::with(['tenantProfile.user'])
                          ->whereIn('status', ['sent', 'overdue'])
                          ->where('balance_amount', '>', 0)
                          ->get();

        return view('payments.create', compact('invoice', 'invoices'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,upi,card,cheque,other',
            'reference_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $invoice = Invoice::findOrFail($request->invoice_id);

        // Validate payment amount doesn't exceed balance
        if ($request->amount > $invoice->balance_amount) {
            return redirect()->back()
                           ->with('error', 'Payment amount cannot exceed invoice balance of ' . $invoice->formatted_balance_amount)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $payment = $invoice->addPayment($request->amount, [
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'notes' => $request->notes,
                'status' => 'completed'
            ]);

            DB::commit();

            // Send payment confirmation notification to tenant
            try {
                $tenant = $payment->tenantProfile->user;
                $hostel = $payment->tenantProfile->currentBed->room->hostel ?? null;
                $invoice = $payment->invoice;

                $this->notificationService->sendNotification('payment_received', $payment->tenantProfile, [
                    'subject' => 'Payment Confirmation for Invoice #' . $invoice->invoice_number,
                    'heading' => 'Payment Received Successfully',
                    'body' => "Dear {$tenant->name},\n\n" .
                             "We have successfully received your payment. Please find the details below:\n\n" .
                             "Payment Amount: ₹{$payment->amount}\n" .
                             "Payment Date: " . $payment->payment_date->format('M j, Y') . "\n" .
                             "Payment Method: " . ucfirst(str_replace('_', ' ', $payment->payment_method)) . "\n" .
                             "Invoice Number: #{$invoice->invoice_number}\n" .
                             "Reference Number: " . ($payment->reference_number ?? 'N/A') . "\n\n" .
                             "Your payment has been recorded and will be verified shortly.\n\n" .
                             "Thank you for your timely payment!\n\n" .
                             "Best regards,\nThe " . ($hostel->name ?? 'Hostel') . " Team",
                    'greeting' => $tenant->name,
                    'action_url' => route('payments.show', $payment->id),
                    'action_text' => 'View Payment',
                    'badge_text' => 'Payment Received',
                    'badge_type' => 'success',
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send payment notification: ' . $e->getMessage());
            }

            return redirect()->route('payments.show', $payment->id)
                           ->with('success', 'Payment recorded successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Failed to record payment: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load(['invoice.tenantProfile.user', 'recordedBy', 'verifiedBy']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit(Payment $payment)
    {
        if ($payment->status === 'completed' && $payment->is_verified) {
            return redirect()->route('payments.show', $payment->id)
                           ->with('error', 'Cannot edit a verified payment.');
        }

        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, Payment $payment)
    {
        if ($payment->status === 'completed' && $payment->is_verified) {
            return redirect()->route('payments.show', $payment->id)
                           ->with('error', 'Cannot update a verified payment.');
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,upi,card,cheque,other',
            'reference_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $payment->update($request->only([
            'amount', 'payment_date', 'payment_method', 'reference_number',
            'bank_name', 'account_number', 'notes'
        ]));

        return redirect()->route('payments.show', $payment->id)
                       ->with('success', 'Payment updated successfully!');
    }

    /**
     * Remove the specified payment
     */
    public function destroy(Payment $payment)
    {
        if ($payment->status === 'completed' && $payment->is_verified) {
            return redirect()->back()
                           ->with('error', 'Cannot delete a verified payment.');
        }

        $payment->delete();

        return redirect()->route('payments.index')
                       ->with('success', 'Payment deleted successfully!');
    }

    /**
     * Verify a payment
     */
    public function verify(Payment $payment)
    {
        if ($payment->is_verified) {
            return redirect()->back()
                           ->with('error', 'Payment is already verified.');
        }

        $payment->verify();

        // Send payment verification notification to tenant
        try {
            $tenant = $payment->tenantProfile->user;
            $hostel = $payment->tenantProfile->currentBed->room->hostel ?? null;
            $invoice = $payment->invoice;

            $this->notificationService->sendNotification('payment_verified', $payment->tenantProfile, [
                'subject' => 'Your Payment for Invoice #' . $invoice->invoice_number . ' has been Verified',
                'heading' => 'Payment Verified Successfully',
                'body' => "Dear {$tenant->name},\n\n" .
                         "Your payment of ₹{$payment->amount} for Invoice #{$invoice->invoice_number} has been successfully verified by our team.\n\n" .
                         "Payment Details:\n" .
                         "• Amount: ₹{$payment->amount}\n" .
                         "• Payment Date: " . $payment->payment_date->format('M j, Y') . "\n" .
                         "• Payment Method: " . ucfirst(str_replace('_', ' ', $payment->payment_method)) . "\n" .
                         "• Verified By: " . ($payment->verifiedBy->name ?? 'Admin') . "\n" .
                         "• Verified At: " . $payment->verified_at->format('M j, Y H:i') . "\n\n" .
                         "Your account balance has been updated accordingly.\n\n" .
                         "Thank you for your payment!\n\n" .
                         "Best regards,\nThe " . ($hostel->name ?? 'Hostel') . " Team",
                'greeting' => $tenant->name,
                'action_url' => route('payments.show', $payment->id),
                'action_text' => 'View Payment',
                'badge_text' => 'Verified',
                'badge_type' => 'success',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send payment verification notification: ' . $e->getMessage());
        }

        return redirect()->back()
                       ->with('success', 'Payment verified successfully!');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:verify,cancel,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:payments,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        $payments = Payment::whereIn('id', $request->ids)->get();
        $count = 0;

        foreach ($payments as $payment) {
            switch ($request->action) {
                case 'verify':
                    if (!$payment->is_verified && $payment->status === 'completed') {
                        $payment->verify();
                        $count++;
                    }
                    break;
                case 'cancel':
                    if (!$payment->is_verified) {
                        $payment->cancel('Bulk cancellation');
                        $count++;
                    }
                    break;
                case 'delete':
                    if (!$payment->is_verified) {
                        $payment->delete();
                        $count++;
                    }
                    break;
            }
        }

        return redirect()->back()
                       ->with('success', "Successfully processed {$count} payments.");
    }
}
