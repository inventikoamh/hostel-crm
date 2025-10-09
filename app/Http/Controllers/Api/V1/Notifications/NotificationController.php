<?php

namespace App\Http\Controllers\Api\V1\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Models\TenantProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Notification::with(['createdBy', 'notifiable']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('recipient_email')) {
                $query->where('recipient_email', $request->recipient_email);
            }

            if ($request->has('created_by')) {
                $query->where('created_by', $request->created_by);
            }

            if ($request->has('notifiable_type')) {
                $query->where('notifiable_type', $request->notifiable_type);
            }

            if ($request->has('notifiable_id')) {
                $query->where('notifiable_id', $request->notifiable_id);
            }

            if ($request->has('scheduled')) {
                if ($request->boolean('scheduled')) {
                    $query->whereNotNull('scheduled_at');
                } else {
                    $query->whereNull('scheduled_at');
                }
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            if ($request->has('sent_from')) {
                $query->where('sent_at', '>=', $request->sent_from);
            }

            if ($request->has('sent_to')) {
                $query->where('sent_at', '<=', $request->sent_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%")
                      ->orWhere('recipient_email', 'like', "%{$search}%")
                      ->orWhere('recipient_name', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $notifications = $query->paginate($perPage);

            // Transform data
            $notifications->getCollection()->transform(function ($notification) {
                return $this->transformNotification($notification);
            });

            return response()->json([
                'success' => true,
                'message' => 'Notifications retrieved successfully',
                'data' => $notifications->items(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'from' => $notifications->firstItem(),
                    'to' => $notifications->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new notification (GET version for testing)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $users = User::where('is_tenant', false)->get(['id', 'name', 'email']);
            $tenants = TenantProfile::with('user')->get(['id', 'user_id']);

            return response()->json([
                'success' => true,
                'message' => 'Notification creation form data',
                'data' => [
                    'required_fields' => [
                        'type' => 'Notification type (required)',
                        'title' => 'Notification title (required)',
                        'message' => 'Notification message (required)',
                        'recipient_email' => 'Recipient email address (required)',
                        'recipient_name' => 'Recipient name (required)'
                    ],
                    'optional_fields' => [
                        'status' => 'Status: pending, sent, failed, cancelled (default: pending)',
                        'data' => 'Additional data (JSON)',
                        'scheduled_at' => 'Schedule notification for later (datetime)',
                        'notifiable_type' => 'Related entity type (e.g., App\\Models\\TenantProfile)',
                        'notifiable_id' => 'Related entity ID',
                        'created_by' => 'User ID who created this notification'
                    ],
                    'available_types' => Notification::getAvailableTypes(),
                    'available_users' => $users,
                    'available_tenants' => $tenants->map(function ($tenant) {
                        return [
                            'id' => $tenant->id,
                            'name' => $tenant->user->name ?? 'Unknown',
                            'email' => $tenant->user->email ?? null
                        ];
                    }),
                    'example_request' => [
                        'type' => 'tenant_added',
                        'title' => 'Welcome to Hostel CRM',
                        'message' => 'Your tenant registration has been completed successfully.',
                        'recipient_email' => 'tenant@example.com',
                        'recipient_name' => 'John Doe',
                        'status' => 'pending',
                        'data' => [
                            'hostel_name' => 'Sunrise Hostel',
                            'tenant_id' => 1
                        ]
                    ],
                    'note' => 'This is a GET endpoint for testing. Use POST /api/v1/notifications for actual creation.'
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
     * Store a newly created notification
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => ['required', Rule::in(array_column(Notification::getAvailableTypes(), 'value'))],
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'recipient_email' => 'required|email|max:255',
                'recipient_name' => 'required|string|max:255',
                'status' => ['nullable', Rule::in(['pending', 'sent', 'failed', 'cancelled'])],
                'data' => 'nullable|array',
                'scheduled_at' => 'nullable|date|after:now',
                'notifiable_type' => 'nullable|string|max:255',
                'notifiable_id' => 'nullable|integer',
                'created_by' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Set defaults
            $validated['status'] = $validated['status'] ?? 'pending';
            $validated['retry_count'] = 0;

            $notification = Notification::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Notification created successfully',
                'data' => $this->transformNotification($notification->load(['createdBy', 'notifiable']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified notification
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $notification = Notification::with(['createdBy', 'notifiable'])->find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification retrieved successfully',
                'data' => $this->transformNotification($notification, true)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified notification
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'type' => ['sometimes', 'required', Rule::in(array_column(Notification::getAvailableTypes(), 'value'))],
                'title' => 'sometimes|required|string|max:255',
                'message' => 'sometimes|required|string',
                'recipient_email' => 'sometimes|required|email|max:255',
                'recipient_name' => 'sometimes|required|string|max:255',
                'status' => ['nullable', Rule::in(['pending', 'sent', 'failed', 'cancelled'])],
                'data' => 'nullable|array',
                'scheduled_at' => 'nullable|date',
                'notifiable_type' => 'nullable|string|max:255',
                'notifiable_id' => 'nullable|integer',
                'created_by' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            $notification->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Notification updated successfully',
                'data' => $this->transformNotification($notification->load(['createdBy', 'notifiable']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified notification
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as sent
     */
    public function markAsSent(Request $request, $id): JsonResponse
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsSent();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as sent successfully',
                'data' => $this->transformNotification($notification->load(['createdBy', 'notifiable']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as sent',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as failed
     */
    public function markAsFailed(Request $request, $id): JsonResponse
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'error_message' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $errorMessage = $request->get('error_message');
            $notification->markAsFailed($errorMessage);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as failed successfully',
                'data' => $this->transformNotification($notification->load(['createdBy', 'notifiable']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry failed notification
     */
    public function retry(Request $request, $id): JsonResponse
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            if (!$notification->canRetry()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification cannot be retried (max retries exceeded or not failed)'
                ], 422);
            }

            $notification->update([
                'status' => 'pending',
                'error_message' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification queued for retry successfully',
                'data' => $this->transformNotification($notification->load(['createdBy', 'notifiable']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retry notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel notification
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            if ($notification->status === 'sent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel already sent notification'
                ], 422);
            }

            $notification->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Notification cancelled successfully',
                'data' => $this->transformNotification($notification->load(['createdBy', 'notifiable']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification immediately
     */
    public function sendNow(Request $request, $id): JsonResponse
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            if ($notification->status === 'sent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification already sent'
                ], 422);
            }

            // Simulate sending notification
            // In a real implementation, this would send email/SMS/push notification
            $notification->markAsSent();

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
                'data' => $this->transformNotification($notification->load(['createdBy', 'notifiable']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $stats = [
                'total_notifications' => Notification::count(),
                'pending_notifications' => Notification::where('status', 'pending')->count(),
                'sent_notifications' => Notification::where('status', 'sent')->count(),
                'failed_notifications' => Notification::where('status', 'failed')->count(),
                'cancelled_notifications' => Notification::where('status', 'cancelled')->count(),
                'scheduled_notifications' => Notification::whereNotNull('scheduled_at')->count(),
                'retryable_notifications' => Notification::where('status', 'failed')->where('retry_count', '<', 3)->count(),
                'today_notifications' => Notification::whereDate('created_at', Carbon::today())->count(),
                'this_week_notifications' => Notification::whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])->count(),
                'this_month_notifications' => Notification::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'success_rate' => $this->calculateSuccessRate(),
                'average_retry_count' => $this->calculateAverageRetryCount(),
            ];

            // Add type-specific statistics
            foreach (Notification::getAvailableTypes() as $type) {
                $typeValue = $type['value'];
                $stats["{$typeValue}_notifications"] = Notification::where('type', $typeValue)->count();
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notification statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get scheduled notifications ready to send
     */
    public function getScheduled(Request $request): JsonResponse
    {
        try {
            $notifications = Notification::scheduled()
                ->with(['createdBy', 'notifiable'])
                ->orderBy('scheduled_at', 'asc')
                ->limit($request->get('limit', 50))
                ->get();

            $notifications->transform(function ($notification) {
                return $this->transformNotification($notification);
            });

            return response()->json([
                'success' => true,
                'message' => 'Scheduled notifications retrieved successfully',
                'data' => $notifications->toArray(),
                'count' => $notifications->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve scheduled notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search notifications
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'status' => ['nullable', Rule::in(['pending', 'sent', 'failed', 'cancelled'])],
                'type' => ['nullable', Rule::in(array_column(Notification::getAvailableTypes(), 'value'))],
                'limit' => 'nullable|integer|min:1|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = $request->get('query');
            $status = $request->get('status');
            $type = $request->get('type');
            $limit = $request->get('limit', 10);

            $notificationsQuery = Notification::with(['createdBy', 'notifiable'])
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('message', 'like', "%{$query}%")
                      ->orWhere('recipient_email', 'like', "%{$query}%")
                      ->orWhere('recipient_name', 'like', "%{$query}%");
                });

            if ($status) {
                $notificationsQuery->where('status', $status);
            }

            if ($type) {
                $notificationsQuery->where('type', $type);
            }

            $notifications = $notificationsQuery->limit($limit)->get()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'type_display' => $notification->type_display,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'recipient_email' => $notification->recipient_email,
                    'recipient_name' => $notification->recipient_name,
                    'status' => $notification->status,
                    'status_badge' => $notification->status_badge,
                    'scheduled_at' => $notification->scheduled_at,
                    'sent_at' => $notification->sent_at,
                    'retry_count' => $notification->retry_count,
                    'created_at' => $notification->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $notifications->toArray(),
                'query' => $query,
                'count' => $notifications->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform notification data for API response
     */
    private function transformNotification(Notification $notification, bool $detailed = false): array
    {
        $data = [
            'id' => $notification->id,
            'type' => $notification->type,
            'type_display' => $notification->type_display,
            'title' => $notification->title,
            'message' => $notification->message,
            'recipient_email' => $notification->recipient_email,
            'recipient_name' => $notification->recipient_name,
            'status' => $notification->status,
            'status_badge' => $notification->status_badge,
            'retry_count' => $notification->retry_count,
            'created_at' => $notification->created_at,
            'updated_at' => $notification->updated_at,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'data' => $notification->data,
                'scheduled_at' => $notification->scheduled_at,
                'sent_at' => $notification->sent_at,
                'error_message' => $notification->error_message,
                'notifiable_type' => $notification->notifiable_type,
                'notifiable_id' => $notification->notifiable_id,
                'created_by' => $notification->created_by,
                'created_by_user' => $notification->createdBy ? [
                    'id' => $notification->createdBy->id,
                    'name' => $notification->createdBy->name,
                    'email' => $notification->createdBy->email
                ] : null,
                'notifiable_entity' => $notification->notifiable ? [
                    'type' => $notification->notifiable_type,
                    'id' => $notification->notifiable_id,
                    'data' => $this->getNotifiableData($notification->notifiable)
                ] : null,
            ]);
        }

        return $data;
    }

    /**
     * Get notifiable entity data
     */
    private function getNotifiableData($notifiable): array
    {
        if (!$notifiable) {
            return [];
        }

        $data = ['id' => $notifiable->id];

        switch (get_class($notifiable)) {
            case TenantProfile::class:
                $data['name'] = $notifiable->first_name . ' ' . $notifiable->last_name;
                $data['email'] = $notifiable->user->email ?? null;
                $data['phone'] = $notifiable->phone;
                break;
            case User::class:
                $data['name'] = $notifiable->name;
                $data['email'] = $notifiable->email;
                $data['phone'] = $notifiable->phone;
                break;
            default:
                // For other models, try to get common fields
                if (isset($notifiable->name)) {
                    $data['name'] = $notifiable->name;
                }
                if (isset($notifiable->email)) {
                    $data['email'] = $notifiable->email;
                }
        }

        return $data;
    }

    /**
     * Calculate success rate percentage
     */
    private function calculateSuccessRate(): float
    {
        $total = Notification::count();
        if ($total === 0) return 0;

        $sent = Notification::where('status', 'sent')->count();
        return round(($sent / $total) * 100, 2);
    }

    /**
     * Calculate average retry count
     */
    private function calculateAverageRetryCount(): float
    {
        $failedNotifications = Notification::where('status', 'failed')->get();
        
        if ($failedNotifications->isEmpty()) return 0;

        $totalRetries = $failedNotifications->sum('retry_count');
        return round($totalRetries / $failedNotifications->count(), 2);
    }
}
