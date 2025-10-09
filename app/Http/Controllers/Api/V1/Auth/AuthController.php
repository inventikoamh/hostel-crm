<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and return API token
     * 
     * GET /api/v1/auth/login?email=user@example.com&password=password
     * POST /api/v1/auth/login
     * Body: {"email": "user@example.com", "password": "password"}
     */
    public function login(Request $request)
    {
        try {
            // Handle both GET and POST requests
            $email = $request->input('email');
            $password = $request->input('password');

            // Validate input
            $validator = Validator::make([
                'email' => $email,
                'password' => $password
            ], [
                'email' => 'required|email',
                'password' => 'required|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Attempt authentication
            if (!Auth::attempt(['email' => $email, 'password' => $password])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = Auth::user();
            
            // Create API token
            $token = $user->createToken('API Token')->plainTextToken;

            // Update last login
            $user->update(['last_login_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'status' => $user->status,
                        'is_tenant' => $user->is_tenant,
                        'is_super_admin' => $user->is_super_admin,
                        'last_login_at' => $user->last_login_at,
                        'created_at' => $user->created_at
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user and revoke token
     * 
     * GET /api/v1/auth/logout
     * POST /api/v1/auth/logout
     * Headers: Authorization: Bearer {token}
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user) {
                // Revoke current token
                $user->currentAccessToken()->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No authenticated user found'
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user information
     * 
     * GET /api/v1/auth/me
     * POST /api/v1/auth/me
     * Headers: Authorization: Bearer {token}
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found'
                ], 401);
            }

            // Load relationships
            $user->load(['roles', 'permissions', 'tenantProfile']);

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'avatar' => $user->avatar,
                'is_tenant' => $user->is_tenant,
                'is_super_admin' => $user->is_super_admin,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'slug' => $role->slug,
                        'description' => $role->description
                    ];
                }),
                'permissions' => $user->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'slug' => $permission->slug,
                        'module' => $permission->module
                    ];
                })
            ];

            // Add tenant profile if user is a tenant
            if ($user->is_tenant && $user->tenantProfile) {
                $userData['tenant_profile'] = [
                    'id' => $user->tenantProfile->id,
                    'status' => $user->tenantProfile->status,
                    'move_in_date' => $user->tenantProfile->move_in_date,
                    'monthly_rent' => $user->tenantProfile->monthly_rent,
                    'lease_start_date' => $user->tenantProfile->lease_start_date,
                    'lease_end_date' => $user->tenantProfile->lease_end_date,
                    'is_verified' => $user->tenantProfile->is_verified,
                    'current_bed' => $user->currentBed ? [
                        'id' => $user->currentBed->id,
                        'bed_number' => $user->currentBed->bed_number,
                        'room' => [
                            'id' => $user->currentBed->room->id,
                            'room_number' => $user->currentBed->room->room_number,
                            'floor' => $user->currentBed->room->floor,
                            'hostel' => [
                                'id' => $user->currentBed->room->hostel->id,
                                'name' => $user->currentBed->room->hostel->name
                            ]
                        ]
                    ] : null
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'User information retrieved successfully',
                'data' => $userData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh user token
     * 
     * GET /api/v1/auth/refresh
     * POST /api/v1/auth/refresh
     * Headers: Authorization: Bearer {token}
     */
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found'
                ], 401);
            }

            // Revoke current token
            $user->currentAccessToken()->delete();
            
            // Create new token
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
