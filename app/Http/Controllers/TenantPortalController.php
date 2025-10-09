<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TenantProfile;
use App\Models\TenantAmenity;
use App\Models\TenantAmenityUsage;
use App\Models\PaidAmenity;
use App\Models\UsageCorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TenantPortalController extends Controller
{
    /**
     * Show tenant login form
     */
    public function showLogin()
    {
        return view('tenant.auth.login');
    }

    /**
     * Process tenant login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Only allow tenants to login through this route
        $user = User::where('email', $credentials['email'])
                   ->where('is_tenant', true)
                   ->where('status', 'active')
                   ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);

            // Update last login time
            $user->update(['last_login_at' => now()]);

            return redirect()->route('tenant.dashboard')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or account not found.',
        ])->withInput($request->only('email'));
    }

    /**
     * Show tenant dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        // Get tenant's invoices
        $invoices = Invoice::where('tenant_profile_id', $tenantProfile->id)
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        // Get tenant's payments
        $payments = Payment::where('tenant_profile_id', $tenantProfile->id)
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        // Calculate outstanding amount
        $totalInvoiced = Invoice::where('tenant_profile_id', $tenantProfile->id)
                               ->where('status', '!=', 'cancelled')
                               ->sum('total_amount');

        $totalPaid = Payment::where('tenant_profile_id', $tenantProfile->id)
                           ->where('status', 'verified')
                           ->sum('amount');

        $outstandingAmount = $totalInvoiced - $totalPaid;

        // Get recent invoices with status
        $recentInvoices = Invoice::where('tenant_profile_id', $tenantProfile->id)
                                ->with(['invoiceItems'])
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();

        return view('tenant.dashboard', compact(
            'user',
            'tenantProfile',
            'invoices',
            'payments',
            'outstandingAmount',
            'recentInvoices'
        ));
    }

    /**
     * Show tenant's invoices
     */
    public function invoices()
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $invoices = Invoice::where('tenant_profile_id', $tenantProfile->id)
                          ->with(['invoiceItems'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('tenant.invoices', compact('user', 'tenantProfile', 'invoices'));
    }

    /**
     * Show specific invoice
     */
    public function showInvoice(Invoice $invoice)
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        // Ensure the invoice belongs to this tenant
        if (!$tenantProfile || $invoice->tenant_profile_id !== $tenantProfile->id) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $invoice->load(['invoiceItems', 'tenantProfile.currentBed.room.hostel']);

        return view('tenant.invoice-detail', compact('user', 'tenantProfile', 'invoice'));
    }

    /**
     * Show tenant's payments
     */
    public function payments()
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $payments = Payment::where('tenant_profile_id', $tenantProfile->id)
                          ->with(['invoice'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('tenant.payments', compact('user', 'tenantProfile', 'payments'));
    }

    /**
     * Show tenant's profile
     */
    public function profile()
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $tenantProfile->load(['currentBed.room.hostel', 'user']);

        return view('tenant.profile', compact('user', 'tenantProfile'));
    }

    /**
     * Update tenant profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'occupation' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if there are any actual changes
        $hasChanges = false;
        $requestedChanges = [];

        // Check user changes
        $userChanges = [];
        if ($request->name !== $user->name) {
            $userChanges['name'] = $request->name;
            $hasChanges = true;
        }
        if ($request->phone !== $user->phone) {
            $userChanges['phone'] = $request->phone;
            $hasChanges = true;
        }

        // Check tenant profile changes
        $tenantProfileChanges = [];
        if ($request->date_of_birth !== ($tenantProfile->date_of_birth?->format('Y-m-d') ?? '')) {
            $tenantProfileChanges['date_of_birth'] = $request->date_of_birth;
            $hasChanges = true;
        }
        if ($request->address !== ($tenantProfile->address ?? '')) {
            $tenantProfileChanges['address'] = $request->address;
            $hasChanges = true;
        }
        if ($request->occupation !== ($tenantProfile->occupation ?? '')) {
            $tenantProfileChanges['occupation'] = $request->occupation;
            $hasChanges = true;
        }
        if ($request->company !== ($tenantProfile->company ?? '')) {
            $tenantProfileChanges['company'] = $request->company;
            $hasChanges = true;
        }
        if ($request->emergency_contact_name !== ($tenantProfile->emergency_contact_name ?? '')) {
            $tenantProfileChanges['emergency_contact_name'] = $request->emergency_contact_name;
            $hasChanges = true;
        }
        if ($request->emergency_contact_phone !== ($tenantProfile->emergency_contact_phone ?? '')) {
            $tenantProfileChanges['emergency_contact_phone'] = $request->emergency_contact_phone;
            $hasChanges = true;
        }
        if ($request->emergency_contact_relation !== ($tenantProfile->emergency_contact_relation ?? '')) {
            $tenantProfileChanges['emergency_contact_relation'] = $request->emergency_contact_relation;
            $hasChanges = true;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userChanges['avatar'] = $avatarPath;
            $hasChanges = true;
        }

        // Only create update request if there are actual changes
        if ($hasChanges) {
            $requestedChanges = [];

            if (!empty($userChanges)) {
                $requestedChanges['user'] = $userChanges;
            }

            if (!empty($tenantProfileChanges)) {
                $requestedChanges['tenant_profile'] = $tenantProfileChanges;
            }

            // Create profile update request
            \App\Models\TenantProfileUpdateRequest::create([
                'tenant_profile_id' => $tenantProfile->id,
                'requested_changes' => $requestedChanges,
                'status' => 'pending',
            ]);

            return redirect()->route('tenant.profile')
                ->with('success', 'Your profile update request has been submitted for admin approval. You will be notified once it\'s reviewed.');
        } else {
            return redirect()->route('tenant.profile')
                ->with('info', 'No changes were detected. Your profile remains unchanged.');
        }
    }


    /**
     * Show tenant's bed information
     */
    public function bedInfo()
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $tenantProfile->load(['currentBed.room.hostel', 'currentBed.room']);

        return view('tenant.bed-info', compact('user', 'tenantProfile'));
    }

    /**
     * Show tenant's amenities
     */
    public function amenities()
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $tenantAmenities = TenantAmenity::with(['paidAmenity', 'usageRecords' => function($query) {
            $query->where('usage_date', '>=', now()->startOfMonth());
        }])
        ->where('tenant_profile_id', $tenantProfile->id)
        ->orderBy('created_at', 'desc')
        ->get();

        // Get available amenities that tenant doesn't have
        $subscribedAmenityIds = $tenantAmenities->pluck('paid_amenity_id')->toArray();
        $availableAmenities = PaidAmenity::active()
            ->whereNotIn('id', $subscribedAmenityIds)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('tenant.amenities.index', compact('user', 'tenantProfile', 'tenantAmenities', 'availableAmenities'));
    }

    /**
     * Show form to request new amenity
     */
    public function requestAmenity()
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        // Get available amenities that tenant doesn't have
        $subscribedAmenityIds = TenantAmenity::where('tenant_profile_id', $tenantProfile->id)
            ->pluck('paid_amenity_id')
            ->toArray();

        $availableAmenities = PaidAmenity::active()
            ->whereNotIn('id', $subscribedAmenityIds)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('tenant.amenities.request', compact('user', 'tenantProfile', 'availableAmenities'));
    }

    /**
     * Store amenity request
     */
    public function storeAmenityRequest(Request $request)
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $validator = Validator::make($request->all(), [
            'paid_amenity_id' => 'required|exists:paid_amenities,id',
            'start_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if tenant already has this amenity
        $existingAmenity = TenantAmenity::where('tenant_profile_id', $tenantProfile->id)
            ->where('paid_amenity_id', $request->paid_amenity_id)
            ->where('status', 'active')
            ->first();

        if ($existingAmenity) {
            return redirect()->back()
                ->with('error', 'You already have this amenity subscribed.')
                ->withInput();
        }

        // Create amenity request (this will be pending admin approval)
        TenantAmenity::create([
            'tenant_profile_id' => $tenantProfile->id,
            'paid_amenity_id' => $request->paid_amenity_id,
            'start_date' => $request->start_date,
            'status' => 'pending', // Will be approved by admin
            'notes' => $request->notes,
        ]);

        return redirect()->route('tenant.amenities')
            ->with('success', 'Your amenity request has been submitted for admin approval.');
    }

    /**
     * Cancel amenity subscription
     */
    public function cancelAmenity(Request $request, TenantAmenity $tenantAmenity)
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile || $tenantAmenity->tenant_profile_id !== $tenantProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:500',
            'end_date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $tenantAmenity->update([
            'status' => 'cancelled',
            'end_date' => $request->end_date,
            'notes' => $tenantAmenity->notes . "\n\nCancellation Reason: " . $request->cancellation_reason,
        ]);

        return redirect()->route('tenant.amenities')
            ->with('success', 'Your amenity subscription has been cancelled successfully.');
    }

    /**
     * Show amenity usage tracking
     */
    public function amenityUsage()
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $tenantAmenities = TenantAmenity::with(['paidAmenity'])
            ->where('tenant_profile_id', $tenantProfile->id)
            ->where('status', 'active')
            ->get();

        // Get all usage records (not just current month)
        $currentMonth = now();
        $usageRecords = TenantAmenityUsage::with(['tenantAmenity.paidAmenity'])
            ->whereHas('tenantAmenity', function($query) use ($tenantProfile) {
                $query->where('tenant_profile_id', $tenantProfile->id);
            })
            ->orderBy('usage_date', 'desc')
            ->get();

        return view('tenant.amenities.usage', compact('user', 'tenantProfile', 'tenantAmenities', 'usageRecords', 'currentMonth'));
    }

    /**
     * Mark amenity usage
     */
    public function markUsage(Request $request)
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $validator = Validator::make($request->all(), [
            'tenant_amenity_id' => 'required|exists:tenant_amenities,id',
            'usage_date' => 'required|date|before_or_equal:today',
            'quantity' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tenantAmenity = TenantAmenity::with('paidAmenity')
            ->where('id', $request->tenant_amenity_id)
            ->where('tenant_profile_id', $tenantProfile->id)
            ->where('status', 'active')
            ->first();

        if (!$tenantAmenity) {
            return redirect()->back()
                ->with('error', 'Invalid amenity or amenity not active.')
                ->withInput();
        }

        // Check if usage already exists for this date
        $existingUsage = TenantAmenityUsage::where('tenant_amenity_id', $tenantAmenity->id)
            ->where('usage_date', $request->usage_date)
            ->first();

        if ($existingUsage) {
            return redirect()->back()
                ->with('error', 'Usage already recorded for this date. Please contact admin to modify.')
                ->withInput();
        }

        // Create usage record
        $unitPrice = $tenantAmenity->effective_price ?? $tenantAmenity->paidAmenity->price;
        $totalAmount = $unitPrice * $request->quantity;

        // Ensure we have a valid price
        if (!$unitPrice || $unitPrice <= 0) {
            return redirect()->back()
                ->with('error', 'Invalid pricing for this amenity. Please contact administrator.')
                ->withInput();
        }

        TenantAmenityUsage::create([
            'tenant_amenity_id' => $tenantAmenity->id,
            'usage_date' => $request->usage_date,
            'quantity' => $request->quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'notes' => $request->notes,
            'recorded_by' => $user->id,
        ]);

        return redirect()->route('tenant.amenities.usage')
            ->with('success', 'Usage recorded successfully!');
    }

    /**
     * Request usage correction
     */
    public function requestUsageCorrection(Request $request, TenantAmenityUsage $usage)
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile || $usage->tenantAmenity->tenant_profile_id !== $tenantProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $validator = Validator::make($request->all(), [
            'correction_reason' => 'required|string|max:500',
            'requested_quantity' => 'required|integer|min:1|max:10',
            'requested_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Create a correction request
        UsageCorrectionRequest::create([
            'tenant_amenity_usage_id' => $usage->id,
            'requested_by' => $user->id,
            'original_quantity' => $usage->quantity,
            'requested_quantity' => $request->requested_quantity,
            'original_notes' => $usage->notes,
            'requested_notes' => $request->requested_notes,
            'correction_reason' => $request->correction_reason,
            'status' => 'pending',
        ]);

        return redirect()->route('tenant.amenities.usage')
            ->with('success', 'Your usage correction request has been submitted for admin review.');
    }


    /**
     * View tenant documents
     */
    public function documents()
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile) {
            return redirect()->route('tenant.login')
                ->with('error', 'Tenant profile not found. Please contact administrator.');
        }

        $documents = \App\Models\TenantDocument::where('tenant_profile_id', $tenantProfile->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tenant.documents.index', compact('user', 'tenantProfile', 'documents'));
    }

    /**
     * View specific tenant document
     */
    public function showDocument(\App\Models\TenantDocument $tenantDocument)
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile || $tenantDocument->tenant_profile_id !== $tenantProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $tenantDocument->load(['tenantProfile.user', 'tenantProfile.currentBed.room.hostel', 'printedByUser', 'uploadedByAdmin', 'approvedByUser']);

        return view('tenant.documents.show', compact('user', 'tenantProfile', 'tenantDocument'));
    }

    /**
     * View/download document
     */
    public function viewDocument(\App\Models\TenantDocument $tenantDocument)
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile || $tenantDocument->tenant_profile_id !== $tenantProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$tenantDocument->document_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($tenantDocument->document_path)) {
            abort(404, 'Document not found.');
        }

        $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($tenantDocument->document_path);
        $mimeType = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($tenantDocument->document_path);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($tenantDocument->document_path) . '"'
        ]);
    }

    /**
     * Upload document (for tenant upload requests)
     */
    public function uploadDocument(Request $request, \App\Models\TenantDocument $tenantDocument)
    {
        $user = Auth::user();
        $tenantProfile = $user->tenantProfile;

        if (!$tenantProfile || $tenantDocument->tenant_profile_id !== $tenantProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($tenantDocument->request_type !== 'tenant_upload') {
            abort(403, 'This document cannot be uploaded by tenant.');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Store the document
        $file = $request->file('document');
        $filename = 'tenant-documents/' . $tenantDocument->document_number . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('', $filename, 'public');

        // Update the document
        $tenantDocument->update([
            'document_path' => $path,
            'status' => 'uploaded',
            'notes' => $request->notes ?: $tenantDocument->notes,
        ]);

        return redirect()->route('tenant.documents.show', $tenantDocument)
            ->with('success', 'Document uploaded successfully. Waiting for admin approval.');
    }

    /**
     * Logout tenant
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('tenant.login')
            ->with('success', 'You have been logged out successfully.');
    }
}
