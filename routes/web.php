<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PaidAmenityController;
use App\Http\Controllers\TenantAmenityController;
use App\Http\Controllers\SmtpSettingsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AmenityUsageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TenantPortalController;

Route::get('/', function () {
    return view('landing');
})->name('home');




// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Dashboard Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Tenant Routes
Route::get('/tenants/available-beds/{hostel}', [TenantController::class, 'getAvailableBeds'])->name('tenants.available-beds')->middleware('auth');
Route::post('/tenants/{tenant}/verify', [TenantController::class, 'verify'])->name('tenants.verify')->middleware('auth');
Route::post('/tenants/{tenant}/move-out', [TenantController::class, 'moveOut'])->name('tenants.move-out')->middleware('auth');
Route::resource('tenants', TenantController::class)->middleware('auth');

// Hostel Routes
Route::resource('hostels', HostelController::class)->middleware('auth');

// Configuration Routes
Route::prefix('config')->name('config.')->middleware('auth')->group(function () {
    Route::resource('amenities', AmenityController::class);

    // SMTP Settings
    Route::get('/smtp-settings', [SmtpSettingsController::class, 'index'])->name('smtp-settings');
    Route::put('/smtp-settings', [SmtpSettingsController::class, 'update'])->name('smtp-settings.update');
    Route::post('/smtp-settings/test', [SmtpSettingsController::class, 'test'])->name('smtp-settings.test');
});

// Paid Amenities Routes (Authentication Required)
Route::resource('paid-amenities', PaidAmenityController::class)->middleware('auth');
Route::post('/paid-amenities/bulk-action', [PaidAmenityController::class, 'bulkAction'])->name('paid-amenities.bulk-action')->middleware('auth');

// Tenant Amenities Routes (Authentication Required)
Route::prefix('tenant-amenities')->name('tenant-amenities.')->middleware('auth')->group(function () {
    Route::get('/', [TenantAmenityController::class, 'index'])->name('index');
    Route::get('/create', [TenantAmenityController::class, 'create'])->name('create');
    Route::post('/', [TenantAmenityController::class, 'store'])->name('store');

    // Billing summary - specific route before parameterized routes
    Route::get('/billing-summary/{tenant}', [TenantAmenityController::class, 'getBillingSummary'])->name('billing-summary');

    // Usage management - specific routes before parameterized routes
    Route::put('/usage/{usage}', [TenantAmenityController::class, 'updateUsage'])->name('update-usage');
    Route::delete('/usage/{usage}', [TenantAmenityController::class, 'deleteUsage'])->name('delete-usage');

    // Parameterized routes - these should come last
    Route::get('/{tenantAmenity}', [TenantAmenityController::class, 'show'])->name('show');
    Route::get('/{tenantAmenity}/edit', [TenantAmenityController::class, 'edit'])->name('edit');
    Route::put('/{tenantAmenity}', [TenantAmenityController::class, 'update'])->name('update');
    Route::delete('/{tenantAmenity}', [TenantAmenityController::class, 'destroy'])->name('destroy');
    Route::post('/{tenantAmenity}/usage', [TenantAmenityController::class, 'recordUsage'])->name('record-usage');
});

// Public Enquiry Routes (No Authentication Required)
Route::get('/contact', [EnquiryController::class, 'publicForm'])->name('enquiry.form');
Route::post('/contact', [EnquiryController::class, 'store'])->name('enquiry.store');
Route::get('/contact/success', [EnquiryController::class, 'success'])->name('enquiry.success');

// Admin Enquiry Routes (Authentication Required)
Route::resource('enquiries', EnquiryController::class)->middleware('auth');

// Invoice and Payment Routes (Authentication Required)
Route::resource('invoices', InvoiceController::class)->middleware('auth');
Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send')->middleware('auth');
Route::post('invoices/bulk-action', [InvoiceController::class, 'bulkAction'])->name('invoices.bulk-action')->middleware('auth');
Route::post('invoices/generate-rent', [InvoiceController::class, 'generateRentInvoice'])->name('invoices.generate-rent')->middleware('auth');
Route::post('invoices/generate-amenities', [InvoiceController::class, 'generateAmenitiesInvoice'])->name('invoices.generate-amenities')->middleware('auth');
Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'viewPdf'])->name('invoices.pdf.view')->middleware('auth');
Route::get('invoices/{invoice}/pdf/download', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf.download')->middleware('auth');
Route::post('invoices/{invoice}/pdf/email', [InvoiceController::class, 'emailPdf'])->name('invoices.pdf.email')->middleware('auth');
Route::post('invoices/generate-amenity', [InvoiceController::class, 'generateAmenityInvoice'])->name('invoices.generate-amenity')->middleware('auth');
Route::get('invoices/amenity-usage-summary', [InvoiceController::class, 'getAmenityUsageSummary'])->name('invoices.amenity-usage-summary')->middleware('auth');
Route::post('invoices/generate-monthly-amenity', [InvoiceController::class, 'generateMonthlyAmenityInvoices'])->name('invoices.generate-monthly-amenity')->middleware('auth');

Route::resource('payments', PaymentController::class)->middleware('auth');
Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify')->middleware('auth');
Route::post('payments/bulk-action', [PaymentController::class, 'bulkAction'])->name('payments.bulk-action')->middleware('auth');

// Room Routes (Authentication Required)
Route::resource('rooms', RoomController::class)->middleware('auth');

// Map Routes (Authentication Required)
Route::prefix('map')->name('map.')->middleware('auth')->group(function () {
    Route::get('/', [MapController::class, 'index'])->name('index');
    Route::get('/hostel/{hostel}', [MapController::class, 'hostel'])->name('hostel');
    Route::get('/room-details/{room}', [MapController::class, 'roomDetails'])->name('room-details');
    Route::get('/occupancy/{hostel}/{floor?}', [MapController::class, 'occupancyData'])->name('occupancy');
    Route::post('/bed/{bed}/status', [MapController::class, 'updateBedStatus'])->name('bed.status');
});

// Amenity Usage Routes (Authentication Required)
Route::get('amenity-usage', [AmenityUsageController::class, 'index'])->name('amenity-usage.index')->middleware('auth');
Route::get('amenity-usage/create', [AmenityUsageController::class, 'create'])->name('amenity-usage.create')->middleware('auth');
Route::get('amenity-usage/attendance', [AmenityUsageController::class, 'attendance'])->name('amenity-usage.attendance')->middleware('auth');
Route::post('amenity-usage/attendance', [AmenityUsageController::class, 'storeAttendance'])->name('amenity-usage.store-attendance')->middleware('auth');
Route::get('amenity-usage/reports', [AmenityUsageController::class, 'reports'])->name('amenity-usage.reports')->middleware('auth');
Route::get('amenity-usage/export', [AmenityUsageController::class, 'exportReport'])->name('amenity-usage.export')->middleware('auth');
Route::get('amenity-usage/stats', [AmenityUsageController::class, 'getUsageStats'])->name('amenity-usage.stats')->middleware('auth');
Route::post('amenity-usage', [AmenityUsageController::class, 'store'])->name('amenity-usage.store')->middleware('auth');
Route::get('amenity-usage/{amenityUsage}', [AmenityUsageController::class, 'show'])->name('amenity-usage.show')->middleware('auth');
Route::get('amenity-usage/{amenityUsage}/edit', [AmenityUsageController::class, 'edit'])->name('amenity-usage.edit')->middleware('auth');
Route::put('amenity-usage/{amenityUsage}', [AmenityUsageController::class, 'update'])->name('amenity-usage.update')->middleware('auth');
Route::delete('amenity-usage/{amenityUsage}', [AmenityUsageController::class, 'destroy'])->name('amenity-usage.destroy')->middleware('auth');

// Notification Routes (Authentication Required)
Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/statistics', [NotificationController::class, 'statistics'])->name('statistics');
    Route::post('/process-scheduled', [NotificationController::class, 'processScheduled'])->name('process-scheduled');
    Route::post('/retry-failed', [NotificationController::class, 'retryFailed'])->name('retry-failed');
    Route::post('/test', [NotificationController::class, 'test'])->name('test');
    Route::post('/test-all', [NotificationController::class, 'testAll'])->name('test-all');

    // Settings routes (must come before parameterized routes)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [NotificationController::class, 'settings'])->name('index');
        Route::get('/test', function() { return 'Test route works!'; })->name('test');
        Route::get('/test-auth', function() { return 'Auth test: ' . (auth()->check() ? 'Logged in as ' . auth()->user()->email : 'Not logged in'); })->name('test-auth');
        Route::get('/test-simple', function() {
            $controller = new App\Http\Controllers\NotificationController(new App\Services\NotificationService());
            $request = new Illuminate\Http\Request();
            $result = $controller->settings($request);
            return view('notifications.settings-simple', $result->getData());
        })->name('test-simple');
        Route::get('/test-view', function() {
            $controller = new App\Http\Controllers\NotificationController(new App\Services\NotificationService());
            $request = new Illuminate\Http\Request();
            return $controller->settings($request);
        })->name('test-view');
        Route::post('/bulk-action', [NotificationController::class, 'bulkActionSettings'])->name('bulk-action');
        Route::get('/create', [NotificationController::class, 'createSetting'])->name('create');
        Route::post('/', [NotificationController::class, 'storeSetting'])->name('store');
        Route::get('/{notificationSetting}/edit', [NotificationController::class, 'editSetting'])->name('edit');
        Route::put('/{notificationSetting}', [NotificationController::class, 'updateSetting'])->name('update');
        Route::delete('/{notificationSetting}', [NotificationController::class, 'destroySetting'])->name('destroy');
        Route::post('/{notificationSetting}/toggle', [NotificationController::class, 'toggleSetting'])->name('toggle');
    });

    // Parameterized routes (must come after specific routes)
    Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
    Route::post('/{notification}/retry', [NotificationController::class, 'retry'])->name('retry');
});

// User Management Routes (Authentication Required)
Route::prefix('users')->name('users.')->middleware('auth')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
});

// Role Management Routes (Authentication Required)
Route::prefix('roles')->name('roles.')->middleware('auth')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('/create', [RoleController::class, 'create'])->name('create');
    Route::post('/', [RoleController::class, 'store'])->name('store');
    Route::get('/{role}', [RoleController::class, 'show'])->name('show');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('update');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    Route::post('/{role}/clone', [RoleController::class, 'clone'])->name('clone');
});

// Permission Management Routes (Authentication Required)
Route::prefix('permissions')->name('permissions.')->middleware('auth')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('index');
    Route::get('/create', [PermissionController::class, 'create'])->name('create');
    Route::post('/', [PermissionController::class, 'store'])->name('store');
    Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
    Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
    Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
    Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-action', [PermissionController::class, 'bulkAction'])->name('bulk-action');
});

// Admin Tenant Profile Update Requests Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('tenant-profile-requests', \App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class)->except(['create', 'edit', 'store', 'update']);
    Route::post('/tenant-profile-requests/{tenantProfileRequest}/approve', [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'approve'])->name('tenant-profile-requests.approve');
    Route::post('/tenant-profile-requests/{tenantProfileRequest}/reject', [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'reject'])->name('tenant-profile-requests.reject');
    Route::post('/tenant-profile-requests/bulk-action', [\App\Http\Controllers\Admin\TenantProfileUpdateRequestController::class, 'bulkAction'])->name('tenant-profile-requests.bulk-action');

    // Usage Correction Requests
    Route::resource('usage-correction-requests', \App\Http\Controllers\Admin\UsageCorrectionRequestController::class)->except(['create', 'edit', 'store', 'update']);
    Route::post('/usage-correction-requests/{usageCorrectionRequest}/approve', [\App\Http\Controllers\Admin\UsageCorrectionRequestController::class, 'approve'])->name('usage-correction-requests.approve');
    Route::post('/usage-correction-requests/{usageCorrectionRequest}/reject', [\App\Http\Controllers\Admin\UsageCorrectionRequestController::class, 'reject'])->name('usage-correction-requests.reject');
    Route::post('/usage-correction-requests/bulk-approve', [\App\Http\Controllers\Admin\UsageCorrectionRequestController::class, 'bulkApprove'])->name('usage-correction-requests.bulk-approve');
    Route::post('/usage-correction-requests/bulk-reject', [\App\Http\Controllers\Admin\UsageCorrectionRequestController::class, 'bulkReject'])->name('usage-correction-requests.bulk-reject');
    Route::post('/usage-correction-requests/bulk-action', [\App\Http\Controllers\Admin\UsageCorrectionRequestController::class, 'bulkAction'])->name('usage-correction-requests.bulk-action');

    // Tenant Documents
    Route::resource('tenant-documents', \App\Http\Controllers\Admin\TenantDocumentController::class)->except(['edit', 'update']);
    Route::get('/tenant-documents/{tenantDocument}/print', [\App\Http\Controllers\Admin\TenantDocumentController::class, 'print'])->name('tenant-documents.print');
    Route::get('/tenant-documents/{tenantDocument}/download', [\App\Http\Controllers\Admin\TenantDocumentController::class, 'download'])->name('tenant-documents.download');
    Route::get('/tenant-documents/{tenantDocument}/upload', [\App\Http\Controllers\Admin\TenantDocumentController::class, 'uploadForm'])->name('tenant-documents.upload');
    Route::post('/tenant-documents/{tenantDocument}/upload', [\App\Http\Controllers\Admin\TenantDocumentController::class, 'storeSignedForm'])->name('tenant-documents.store-signed');
    Route::get('/tenant-documents/{tenantDocument}/signed', [\App\Http\Controllers\Admin\TenantDocumentController::class, 'viewSignedForm'])->name('tenant-documents.view-signed');
    Route::post('/tenant-documents/{tenantDocument}/approve', [\App\Http\Controllers\Admin\TenantDocumentController::class, 'approve'])->name('tenant-documents.approve');
    Route::post('/tenant-documents/{tenantDocument}/reject', [\App\Http\Controllers\Admin\TenantDocumentController::class, 'reject'])->name('tenant-documents.reject');
    Route::post('/tenant-documents/bulk-action', [\App\Http\Controllers\Admin\TenantDocumentController::class, 'bulkAction'])->name('tenant-documents.bulk-action');
});

// Tenant Portal Routes
Route::prefix('tenant')->name('tenant.')->group(function () {
    // Public tenant login routes
    Route::get('/login', [TenantPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [TenantPortalController::class, 'login'])->name('login.post');

    // Protected tenant routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [TenantPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/invoices', [TenantPortalController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}', [TenantPortalController::class, 'showInvoice'])->name('invoice.show');
        Route::get('/payments', [TenantPortalController::class, 'payments'])->name('payments');
        Route::get('/profile', [TenantPortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [TenantPortalController::class, 'updateProfile'])->name('profile.update');
        Route::get('/bed-info', [TenantPortalController::class, 'bedInfo'])->name('bed-info');

        // Amenity management routes
        Route::get('/amenities', [TenantPortalController::class, 'amenities'])->name('amenities');
        Route::get('/amenities/request', [TenantPortalController::class, 'requestAmenity'])->name('amenities.request');
        Route::post('/amenities/request', [TenantPortalController::class, 'storeAmenityRequest'])->name('amenities.request.store');
        Route::post('/amenities/{tenantAmenity}/cancel', [TenantPortalController::class, 'cancelAmenity'])->name('amenities.cancel');
        Route::get('/amenities/usage', [TenantPortalController::class, 'amenityUsage'])->name('amenities.usage');
        Route::post('/amenities/usage', [TenantPortalController::class, 'markUsage'])->name('amenities.usage.mark');
        Route::post('/amenities/usage/{usage}/correction', [TenantPortalController::class, 'requestUsageCorrection'])->name('amenities.usage.correction');

        // Documents
        Route::get('/documents', [TenantPortalController::class, 'documents'])->name('documents');
        Route::get('/documents/{tenantDocument}', [TenantPortalController::class, 'showDocument'])->name('documents.show');
        Route::get('/documents/{tenantDocument}/download', [TenantPortalController::class, 'viewDocument'])->name('documents.download');
        Route::post('/documents/{tenantDocument}/upload', [TenantPortalController::class, 'uploadDocument'])->name('documents.upload');

        Route::post('/logout', [TenantPortalController::class, 'logout'])->name('logout');
    });
});
