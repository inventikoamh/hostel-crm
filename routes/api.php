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

        // TODO: Add other API modules here as they are implemented
        // - Invoices API
        // - Payments API
        // - Amenities API
        // - Users API
        // - Enquiries API
        // - Notifications API
        // - Dashboard API
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});
