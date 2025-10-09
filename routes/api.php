<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    
    // Authentication Routes (Public)
    Route::prefix('auth')->group(function () {
        // GET versions for testing
        Route::get('/login', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'login']);
        
        // POST versions for integration
        Route::post('/login', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'login']);
    });

    // Protected Authentication Routes
    Route::middleware(['auth:sanctum'])->prefix('auth')->group(function () {
        Route::get('/logout', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'logout']);
        Route::post('/logout', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'logout']);
        Route::get('/me', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'me']);
        Route::post('/me', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'me']);
        Route::get('/refresh', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'refresh']);
        Route::post('/refresh', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'refresh']);
    });

        // Hostels API Routes
        Route::prefix('hostels')->group(function () {
            // Specific routes first (before parameterized routes)
            Route::get('/search', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'search']);
            Route::post('/search', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'search']);
            
            // GET versions for testing
            Route::get('/', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'index']);
            Route::get('/create', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'create']);
            Route::get('/{id}', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'show']);
            Route::get('/{id}/stats', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'stats']);

            // POST versions for integration
            Route::post('/', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'store']);
            Route::post('/{id}', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'update']);
            Route::post('/{id}/stats', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'stats']);
        });

        // Protected Hostels API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('hostels')->group(function () {
            // PUT/PATCH/DELETE versions for authenticated users
            Route::put('/{id}', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'update']);
            Route::patch('/{id}', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\V1\Hostels\HostelController::class, 'destroy']);
        });

        // Tenants API Routes
        Route::prefix('tenants')->group(function () {
            // Specific routes first (before parameterized routes)
            Route::get('/search', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'search']);
            Route::post('/search', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'search']);
            
            // GET versions for testing
            Route::get('/', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'index']);
            Route::get('/create', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'create']);
            Route::get('/{id}', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'show']);
            Route::get('/{id}/stats', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'stats']);

            // POST versions for integration
            Route::post('/', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'store']);
            Route::post('/{id}', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'update']);
            Route::post('/{id}/stats', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'stats']);
        });

        // Protected Tenants API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('tenants')->group(function () {
            // PUT/PATCH/DELETE versions for authenticated users
            Route::put('/{id}', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'update']);
            Route::patch('/{id}', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'destroy']);
            
            // Special tenant operations
            Route::post('/{id}/assign-bed', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'assignBed']);
            Route::post('/{id}/release-bed', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'releaseBed']);
            Route::post('/{id}/verify', [App\Http\Controllers\Api\V1\Tenants\TenantController::class, 'verify']);
        });

        // Rooms & Beds API Routes
        Route::prefix('rooms-beds')->group(function () {
            // Specific routes first (before parameterized routes)
            Route::get('/search', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'search']);
            Route::post('/search', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'search']);
            
            // Rooms routes
            Route::prefix('rooms')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'createRoom']);
                Route::get('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'showRoom']);
                Route::get('/{id}/stats', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'roomStats']);
                Route::post('/', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'storeRoom']);
                Route::post('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'updateRoom']);
                Route::post('/{id}/stats', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'roomStats']);
            });
            
            // Beds routes
            Route::prefix('beds')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'indexBeds']);
                Route::get('/create', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'createBed']);
                Route::get('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'showBed']);
                Route::get('/{id}/stats', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'bedStats']);
                Route::post('/', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'storeBed']);
                Route::post('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'updateBed']);
                Route::post('/{id}/stats', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'bedStats']);
            });
            
            // Bed assignments routes
            Route::post('/assign-bed', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'assignBed']);
            Route::post('/release-bed/{assignmentId}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'releaseBed']);
        });

        // Protected Rooms & Beds API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('rooms-beds')->group(function () {
            // Rooms routes
            Route::prefix('rooms')->group(function () {
                Route::put('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'updateRoom']);
                Route::patch('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'updateRoom']);
                Route::delete('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'destroyRoom']);
            });
            
            // Beds routes
            Route::prefix('beds')->group(function () {
                Route::put('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'updateBed']);
                Route::patch('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'updateBed']);
                Route::delete('/{id}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'destroyBed']);
            });
            
            // Bed assignments routes
            Route::post('/assign-bed', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'assignBed']);
            Route::post('/release-bed/{assignmentId}', [App\Http\Controllers\Api\V1\RoomsBeds\RoomsBedsController::class, 'releaseBed']);
        });

        // Invoices API Routes
        Route::prefix('invoices')->group(function () {
            // Specific routes first (before parameterized routes)
            Route::get('/search', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'search']);
            Route::post('/search', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'search']);
            Route::post('/generate-amenity', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'generateAmenityInvoice']);

            // GET versions for testing
            Route::get('/', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'index']);
            Route::get('/create', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'create']);
            Route::get('/{id}', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'show']);
            Route::get('/{id}/stats', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'stats']);

            // POST versions for integration
            Route::post('/', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'store']);
            Route::post('/{id}', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'update']);
            Route::post('/{id}/stats', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'stats']);
            Route::post('/{id}/add-payment', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'addPayment']);
            Route::post('/{id}/mark-overdue', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'markOverdue']);
            Route::post('/{id}/add-item', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'addItem']);
            Route::post('/{id}/items/{itemId}', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'updateItem']);
            Route::post('/{id}/items/{itemId}/remove', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'removeItem']);
        });

        // Protected Invoices API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('invoices')->group(function () {
            // PUT/PATCH/DELETE versions for authenticated users
            Route::put('/{id}', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'update']);
            Route::patch('/{id}', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'destroy']);
            
            // Special invoice operations
            Route::post('/{id}/add-payment', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'addPayment']);
            Route::post('/{id}/mark-overdue', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'markOverdue']);
            Route::post('/generate-amenity', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'generateAmenityInvoice']);
            
            // Invoice items management
            Route::post('/{id}/add-item', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'addItem']);
            Route::put('/{id}/items/{itemId}', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'updateItem']);
            Route::patch('/{id}/items/{itemId}', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'updateItem']);
            Route::delete('/{id}/items/{itemId}', [App\Http\Controllers\Api\V1\Invoices\InvoiceController::class, 'removeItem']);
        });

        // Payments API Routes
        Route::prefix('payments')->group(function () {
            // Specific routes first (before parameterized routes)
            Route::get('/search', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'search']);
            Route::post('/search', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'search']);

            // GET versions for testing
            Route::get('/', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'index']);
            Route::get('/create', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'create']);
            Route::get('/{id}', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'show']);
            Route::get('/{id}/stats', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'stats']);
            Route::get('/tenant/{tenantId}/summary', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'tenantSummary']);
            Route::get('/invoice/{invoiceId}/summary', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'invoiceSummary']);

            // POST versions for integration
            Route::post('/', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'store']);
            Route::post('/{id}', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'update']);
            Route::post('/{id}/stats', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'stats']);
            Route::post('/{id}/verify', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'verify']);
            Route::post('/{id}/cancel', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'cancel']);
            Route::post('/tenant/{tenantId}/summary', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'tenantSummary']);
            Route::post('/invoice/{invoiceId}/summary', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'invoiceSummary']);
        });

        // Protected Payments API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('payments')->group(function () {
            // PUT/PATCH/DELETE versions for authenticated users
            Route::put('/{id}', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'update']);
            Route::patch('/{id}', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'destroy']);
            
            // Special payment operations
            Route::post('/{id}/verify', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'verify']);
            Route::post('/{id}/cancel', [App\Http\Controllers\Api\V1\Payments\PaymentController::class, 'cancel']);
        });

        // Amenities API Routes
        Route::prefix('amenities')->group(function () {
            // Specific routes first (before parameterized routes)
            Route::get('/search', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'search']);
            Route::post('/search', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'search']);

            // Paid Amenities routes (must come before /{id} route)
            Route::get('/paid', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'indexPaid']);
            Route::get('/paid/create', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'createPaid']);
            Route::get('/paid/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'showPaid']);
            Route::post('/paid', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'storePaid']);
            Route::post('/paid/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'updatePaid']);

            // Basic Amenities routes
            Route::get('/', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'index']);
            Route::get('/create', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'create']);
            Route::get('/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'show']);
            Route::post('/', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'store']);
            Route::post('/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'update']);

            // Tenant Amenity Subscriptions routes
            Route::get('/subscriptions', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'indexSubscriptions']);
            Route::post('/subscribe', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'subscribe']);
            Route::post('/subscriptions/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'updateSubscription']);
            Route::post('/subscriptions/{id}/suspend', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'suspendSubscription']);
            Route::post('/subscriptions/{id}/reactivate', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'reactivateSubscription']);
            Route::post('/subscriptions/{id}/terminate', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'terminateSubscription']);

            // Amenity Usage routes
            Route::get('/usage', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'getUsageRecords']);
            Route::post('/usage', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'recordUsage']);
            Route::get('/usage/tenant/{tenantId}/summary', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'getTenantUsageSummary']);
            Route::post('/usage/tenant/{tenantId}/summary', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'getTenantUsageSummary']);
        });

        // Protected Amenities API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('amenities')->group(function () {
            // PUT/PATCH/DELETE versions for authenticated users
            Route::put('/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'update']);
            Route::patch('/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'destroy']);
            
            // Paid Amenities routes
            Route::put('/paid/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'updatePaid']);
            Route::patch('/paid/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'updatePaid']);
            Route::delete('/paid/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'destroyPaid']);
            
            // Tenant Amenity Subscriptions routes
            Route::put('/subscriptions/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'updateSubscription']);
            Route::patch('/subscriptions/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'updateSubscription']);
            Route::delete('/subscriptions/{id}', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'terminateSubscription']);
            
            // Special subscription operations
            Route::post('/subscribe', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'subscribe']);
            Route::post('/subscriptions/{id}/suspend', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'suspendSubscription']);
            Route::post('/subscriptions/{id}/reactivate', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'reactivateSubscription']);
            Route::post('/subscriptions/{id}/terminate', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'terminateSubscription']);
            
            // Usage operations
            Route::post('/usage', [App\Http\Controllers\Api\V1\Amenities\AmenityController::class, 'recordUsage']);
        });

        // Users API Routes
        Route::prefix('users')->group(function () {
            // Specific routes first (before parameterized routes)
            Route::get('/search', [App\Http\Controllers\Api\V1\Users\UserController::class, 'search']);
            Route::post('/search', [App\Http\Controllers\Api\V1\Users\UserController::class, 'search']);
            Route::get('/stats', [App\Http\Controllers\Api\V1\Users\UserController::class, 'getUserStats']);
            Route::post('/stats', [App\Http\Controllers\Api\V1\Users\UserController::class, 'getUserStats']);
            Route::get('/modules', [App\Http\Controllers\Api\V1\Users\UserController::class, 'getModules']);
            Route::post('/modules', [App\Http\Controllers\Api\V1\Users\UserController::class, 'getModules']);

            // Users routes
            Route::get('/', [App\Http\Controllers\Api\V1\Users\UserController::class, 'index']);
            Route::get('/create', [App\Http\Controllers\Api\V1\Users\UserController::class, 'create']);
            Route::get('/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'show']);
            Route::post('/', [App\Http\Controllers\Api\V1\Users\UserController::class, 'store']);
            Route::post('/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'update']);
            Route::post('/{id}/assign-role', [App\Http\Controllers\Api\V1\Users\UserController::class, 'assignRole']);
            Route::post('/{id}/remove-role', [App\Http\Controllers\Api\V1\Users\UserController::class, 'removeRole']);
            Route::post('/{id}/suspend', [App\Http\Controllers\Api\V1\Users\UserController::class, 'suspend']);
            Route::post('/{id}/activate', [App\Http\Controllers\Api\V1\Users\UserController::class, 'activate']);

            // Roles routes
            Route::get('/roles', [App\Http\Controllers\Api\V1\Users\UserController::class, 'indexRoles']);
            Route::post('/roles', [App\Http\Controllers\Api\V1\Users\UserController::class, 'storeRole']);
            Route::post('/roles/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'updateRole']);

            // Permissions routes
            Route::get('/permissions', [App\Http\Controllers\Api\V1\Users\UserController::class, 'indexPermissions']);
            Route::post('/permissions', [App\Http\Controllers\Api\V1\Users\UserController::class, 'storePermission']);
            Route::post('/permissions/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'updatePermission']);
        });

        // Protected Users API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('users')->group(function () {
            // PUT/PATCH/DELETE versions for authenticated users
            Route::put('/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'update']);
            Route::patch('/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'destroy']);
            
            // Roles routes
            Route::put('/roles/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'updateRole']);
            Route::patch('/roles/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'updateRole']);
            Route::delete('/roles/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'destroyRole']);
            
            // Permissions routes
            Route::put('/permissions/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'updatePermission']);
            Route::patch('/permissions/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'updatePermission']);
            Route::delete('/permissions/{id}', [App\Http\Controllers\Api\V1\Users\UserController::class, 'destroyPermission']);
            
            // User management operations
            Route::post('/{id}/assign-role', [App\Http\Controllers\Api\V1\Users\UserController::class, 'assignRole']);
            Route::post('/{id}/remove-role', [App\Http\Controllers\Api\V1\Users\UserController::class, 'removeRole']);
            Route::post('/{id}/suspend', [App\Http\Controllers\Api\V1\Users\UserController::class, 'suspend']);
            Route::post('/{id}/activate', [App\Http\Controllers\Api\V1\Users\UserController::class, 'activate']);
        });

        // Enquiries API Routes
        Route::prefix('enquiries')->group(function () {
            // Specific routes first (before parameterized routes)
            Route::get('/search', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'search']);
            Route::post('/search', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'search']);
            Route::get('/stats', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'getStats']);
            Route::post('/stats', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'getStats']);
            Route::get('/sources', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'getSources']);
            Route::post('/sources', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'getSources']);

            // Enquiries routes
            Route::get('/', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'index']);
            Route::get('/create', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'create']);
            Route::get('/{id}', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'show']);
            Route::post('/', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'store']);
            Route::post('/{id}', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'update']);
            Route::post('/{id}/assign', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'assign']);
            Route::post('/{id}/resolve', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'resolve']);
            Route::post('/{id}/close', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'close']);
            Route::post('/{id}/convert-to-tenant', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'convertToTenant']);
        });

        // Protected Enquiries API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('enquiries')->group(function () {
            // PUT/PATCH/DELETE versions for authenticated users
            Route::put('/{id}', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'update']);
            Route::patch('/{id}', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'destroy']);
            
            // Enquiry management operations
            Route::post('/{id}/assign', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'assign']);
            Route::post('/{id}/resolve', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'resolve']);
            Route::post('/{id}/close', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'close']);
            Route::post('/{id}/convert-to-tenant', [App\Http\Controllers\Api\V1\Enquiries\EnquiryController::class, 'convertToTenant']);
        });

        // Notifications API Routes
        Route::prefix('notifications')->group(function () {
            // Specific routes first (before parameterized routes)
            Route::get('/search', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'search']);
            Route::post('/search', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'search']);
            Route::get('/stats', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'getStats']);
            Route::post('/stats', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'getStats']);
            Route::get('/scheduled', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'getScheduled']);
            Route::post('/scheduled', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'getScheduled']);

            // Notifications routes
            Route::get('/', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'index']);
            Route::get('/create', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'create']);
            Route::get('/{id}', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'show']);
            Route::post('/', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'store']);
            Route::post('/{id}', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'update']);
            Route::post('/{id}/mark-sent', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'markAsSent']);
            Route::post('/{id}/mark-failed', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'markAsFailed']);
            Route::post('/{id}/retry', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'retry']);
            Route::post('/{id}/cancel', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'cancel']);
            Route::post('/{id}/send-now', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'sendNow']);
        });

        // Protected Notifications API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('notifications')->group(function () {
            // PUT/PATCH/DELETE versions for authenticated users
            Route::put('/{id}', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'update']);
            Route::patch('/{id}', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'destroy']);
            
            // Notification management operations
            Route::post('/{id}/mark-sent', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'markAsSent']);
            Route::post('/{id}/mark-failed', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'markAsFailed']);
            Route::post('/{id}/retry', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'retry']);
            Route::post('/{id}/cancel', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'cancel']);
            Route::post('/{id}/send-now', [App\Http\Controllers\Api\V1\Notifications\NotificationController::class, 'sendNow']);
        });

        // Dashboard API Routes
        Route::prefix('dashboard')->group(function () {
            // Dashboard overview and analytics
            Route::get('/overview', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'overview']);
            Route::post('/overview', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'overview']);
            Route::get('/financial', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'financial']);
            Route::post('/financial', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'financial']);
            Route::get('/occupancy', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'occupancy']);
            Route::post('/occupancy', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'occupancy']);
            Route::get('/tenants', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'tenants']);
            Route::post('/tenants', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'tenants']);
            Route::get('/amenities', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'amenities']);
            Route::post('/amenities', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'amenities']);
            Route::get('/enquiries', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'enquiries']);
            Route::post('/enquiries', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'enquiries']);
            Route::get('/notifications', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'notifications']);
            Route::post('/notifications', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'notifications']);
            Route::get('/system-health', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'systemHealth']);
            Route::post('/system-health', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'systemHealth']);
            Route::get('/widgets', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'widgets']);
            Route::post('/widgets', [App\Http\Controllers\Api\V1\Dashboard\DashboardController::class, 'widgets']);
        });

        // Chat API Routes (require authentication)
        Route::middleware(['auth:sanctum'])->prefix('chat')->group(function () {
            Route::post('/send-message', [App\Http\Controllers\Api\V1\Chat\ChatController::class, 'sendMessage']);
            Route::get('/history', [App\Http\Controllers\Api\V1\Chat\ChatController::class, 'getHistory']);
            Route::get('/user-info', [App\Http\Controllers\Api\V1\Chat\ChatController::class, 'getUserInfo']);
        });

        // TODO: Add other API modules here as they are implemented
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});
