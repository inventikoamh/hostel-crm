<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Send message to AI webhook
     */
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:1000',
                'conversation_id' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $message = $request->input('message');
            $conversationId = $request->input('conversation_id', uniqid('conv_', true));

            // Prepare data to send to n8n webhook
            $webhookData = [
                'message' => $message,
                'conversation_id' => $conversationId,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'is_tenant' => $user->is_tenant,
                    'is_super_admin' => $user->is_super_admin,
                    'status' => $user->status,
                    'last_login_at' => $user->last_login_at,
                    'created_at' => $user->created_at,
                ],
                'timestamp' => now()->toISOString(),
                'session_id' => session()->getId(),
            ];

            // Add tenant profile if user is a tenant
            if ($user->is_tenant && $user->tenantProfile) {
                $webhookData['user']['tenant_profile'] = [
                    'id' => $user->tenantProfile->id,
                    'phone' => $user->tenantProfile->phone,
                    'date_of_birth' => $user->tenantProfile->date_of_birth,
                    'address' => $user->tenantProfile->address,
                    'occupation' => $user->tenantProfile->occupation,
                    'company' => $user->tenantProfile->company,
                    'status' => $user->tenantProfile->status,
                    'move_in_date' => $user->tenantProfile->move_in_date,
                    'move_out_date' => $user->tenantProfile->move_out_date,
                    'monthly_rent' => $user->tenantProfile->monthly_rent,
                    'lease_start_date' => $user->tenantProfile->lease_start_date,
                    'lease_end_date' => $user->tenantProfile->lease_end_date,
                    'is_verified' => $user->tenantProfile->is_verified,
                ];
            }

            // Send to n8n webhook
            $response = Http::timeout(30)->post('https://n8n.admin.inventiko.com/webhook/crm', $webhookData);

            if ($response->successful()) {
                $aiResponse = $response->json();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => [
                        'user_message' => $message,
                        'conversation_id' => $conversationId,
                        'ai_response' => $aiResponse,
                        'timestamp' => now()->toISOString(),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get AI response',
                    'error' => 'Webhook request failed with status: ' . $response->status(),
                    'data' => [
                        'user_message' => $message,
                        'conversation_id' => $conversationId,
                        'timestamp' => now()->toISOString(),
                    ]
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage(),
                'data' => [
                    'user_message' => $request->input('message'),
                    'timestamp' => now()->toISOString(),
                ]
            ], 500);
        }
    }

    /**
     * Get chat history (placeholder for future implementation)
     */
    public function getHistory(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // For now, return empty history
            // In future, you can implement chat history storage
            return response()->json([
                'success' => true,
                'message' => 'Chat history retrieved successfully',
                'data' => [
                    'messages' => [],
                    'conversation_id' => $request->input('conversation_id', uniqid('conv_', true)),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve chat history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user info for chat context
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $userInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_tenant' => $user->is_tenant,
                'is_super_admin' => $user->is_super_admin,
                'status' => $user->status,
                'avatar' => $user->avatar,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
            ];

            // Add tenant profile if user is a tenant
            if ($user->is_tenant && $user->tenantProfile) {
                $userInfo['tenant_profile'] = [
                    'id' => $user->tenantProfile->id,
                    'phone' => $user->tenantProfile->phone,
                    'date_of_birth' => $user->tenantProfile->date_of_birth,
                    'address' => $user->tenantProfile->address,
                    'occupation' => $user->tenantProfile->occupation,
                    'company' => $user->tenantProfile->company,
                    'status' => $user->tenantProfile->status,
                    'move_in_date' => $user->tenantProfile->move_in_date,
                    'move_out_date' => $user->tenantProfile->move_out_date,
                    'monthly_rent' => $user->tenantProfile->monthly_rent,
                    'lease_start_date' => $user->tenantProfile->lease_start_date,
                    'lease_end_date' => $user->tenantProfile->lease_end_date,
                    'is_verified' => $user->tenantProfile->is_verified,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'User info retrieved successfully',
                'data' => $userInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user info',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
