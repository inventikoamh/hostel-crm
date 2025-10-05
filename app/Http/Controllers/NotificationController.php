<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of notifications
     */
    public function index(Request $request)
    {
        $query = Notification::with(['notifiable', 'createdBy']);

        // Apply filters
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('recipient_email')) {
            $query->byRecipient($request->recipient_email);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $notifications = $query->paginate(15);

        // Prepare data for data table
        $data = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type_display,
                'title' => $notification->title ?? $notification->data['subject'] ?? 'No Title',
                'recipient_email' => $notification->recipient_email,
                'status' => $notification->status,
                'created_at' => $notification->created_at->format('M j, Y g:i A'),
                'sent_at' => $notification->sent_at ? $notification->sent_at->format('M j, Y g:i A') : 'Not sent',
                'retry_count' => $notification->retry_count ?? 0,
            ];
        })->toArray();

        $columns = [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'type', 'label' => 'Type', 'sortable' => true],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true],
            ['key' => 'recipient_email', 'label' => 'Recipient', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true, 'component' => 'components.status-badge'],
            ['key' => 'created_at', 'label' => 'Created', 'sortable' => true],
            ['key' => 'sent_at', 'label' => 'Sent At', 'sortable' => true],
            ['key' => 'retry_count', 'label' => 'Retries', 'sortable' => true],
        ];

        $filters = [
            [
                'key' => 'type',
                'label' => 'Type',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All'],
                    ['value' => 'tenant_added', 'label' => 'Tenant Added'],
                    ['value' => 'enquiry_received_admin', 'label' => 'Enquiry Received (Admin)'],
                    ['value' => 'enquiry_received_tenant', 'label' => 'Enquiry Received (Tenant)'],
                    ['value' => 'invoice_created', 'label' => 'Invoice Created'],
                    ['value' => 'payment_received', 'label' => 'Payment Received'],
                    ['value' => 'payment_verified', 'label' => 'Payment Verified']
                ],
                'value' => $request->type
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All'],
                    ['value' => 'pending', 'label' => 'Pending'],
                    ['value' => 'sent', 'label' => 'Sent'],
                    ['value' => 'failed', 'label' => 'Failed'],
                    ['value' => 'scheduled', 'label' => 'Scheduled']
                ],
                'value' => $request->status
            ],
            [
                'key' => 'recipient_email',
                'label' => 'Recipient Email',
                'type' => 'text',
                'value' => $request->recipient_email
            ],
            [
                'key' => 'date_from',
                'label' => 'From Date',
                'type' => 'date',
                'value' => $request->date_from
            ],
            [
                'key' => 'date_to',
                'label' => 'To Date',
                'type' => 'date',
                'value' => $request->date_to
            ]
        ];

        return view('notifications.index', compact('data', 'columns', 'filters', 'notifications'))
            ->with('title', 'Notifications')
            ->with('subtitle', 'Manage email notifications and delivery status');
    }

    /**
     * Display the specified notification.
     */
    public function show(Notification $notification)
    {
        $notification->load('notifiable');

        return view('notifications.show', compact('notification'))
            ->with('title', 'Notification Details')
            ->with('subtitle', 'View notification information and status');
    }

    /**
     * Display notification settings
     */
    public function settings(Request $request)
    {
        $query = NotificationSetting::query();

        // Apply filters
        if ($request->filled('notification_type')) {
            $query->byType($request->notification_type);
        }

        if ($request->filled('recipient_type')) {
            $query->byRecipientType($request->recipient_type);
        }

        if ($request->filled('enabled')) {
            $query->where('enabled', $request->enabled === '1');
        }

        // Sort
        $query->orderBy('notification_type')->orderBy('priority');

        $settings = $query->paginate(15)->withQueryString();

        // Prepare data for data table
        $data = $settings->map(function ($setting) {
            return [
                'id' => $setting->id,
                'notification_type' => $setting->name,
                'recipient_type' => $setting->recipient_type_display,
                'recipient_email' => $setting->getRecipientEmail() ?? 'N/A',
                'enabled' => $setting->enabled ? 'enabled' : 'disabled',
                'priority' => $setting->priority,
                'send_immediately' => $setting->send_immediately ? 'enabled' : 'disabled',
                'delay_minutes' => $setting->delay_minutes,
                'view_url' => route('notifications.settings.edit', $setting),
                'edit_url' => route('notifications.settings.edit', $setting),
                'delete_url' => route('notifications.settings.destroy', $setting)
            ];
        })->toArray();

        $columns = [
            [
                'key' => 'id',
                'label' => 'ID',
                'width' => 'w-16'
            ],
            [
                'key' => 'notification_type',
                'label' => 'Notification Type',
                'width' => 'w-48'
            ],
            [
                'key' => 'recipient_type',
                'label' => 'Recipient Type',
                'width' => 'w-32'
            ],
            [
                'key' => 'recipient_email',
                'label' => 'Recipient Email',
                'width' => 'w-48'
            ],
            [
                'key' => 'enabled',
                'label' => 'Enabled',
                'width' => 'w-24',
                'component' => 'components.status-badge'
            ],
            [
                'key' => 'priority',
                'label' => 'Priority',
                'width' => 'w-24',
                'component' => 'components.priority-badge'
            ],
            [
                'key' => 'send_immediately',
                'label' => 'Immediate',
                'width' => 'w-24',
                'component' => 'components.status-badge'
            ],
            [
                'key' => 'delay_minutes',
                'label' => 'Delay (min)',
                'width' => 'w-24'
            ]
        ];

        $filters = [
            [
                'key' => 'notification_type',
                'label' => 'Notification Type',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Types'],
                    ...Notification::getAvailableTypes()
                ],
                'value' => $request->notification_type
            ],
            [
                'key' => 'recipient_type',
                'label' => 'Recipient Type',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All Types'],
                    ...NotificationSetting::getRecipientTypes()
                ],
                'value' => $request->recipient_type
            ],
            [
                'key' => 'enabled',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => '', 'label' => 'All'],
                    ['value' => '1', 'label' => 'Enabled'],
                    ['value' => '0', 'label' => 'Disabled']
                ],
                'value' => $request->enabled
            ]
        ];

        $bulkActions = [
            [
                'key' => 'enable',
                'label' => 'Enable Selected',
                'icon' => 'fas fa-check'
            ],
            [
                'key' => 'disable',
                'label' => 'Disable Selected',
                'icon' => 'fas fa-times'
            ],
            [
                'key' => 'delete',
                'label' => 'Delete Selected',
                'icon' => 'fas fa-trash'
            ]
        ];

        $stats = [
            'total' => NotificationSetting::count(),
            'enabled' => NotificationSetting::where('enabled', true)->count(),
            'disabled' => NotificationSetting::where('enabled', false)->count(),
            'high_priority' => NotificationSetting::where('priority', 1)->count()
        ];

        return view('notifications.settings', compact('data', 'columns', 'filters', 'bulkActions', 'stats', 'settings'))
            ->with('title', 'Notification Settings')
            ->with('subtitle', 'Configure email notification preferences and recipients');
    }

    /**
     * Show the form for creating a new notification setting
     */
    public function createSetting()
    {
        $notificationTypes = Notification::getAvailableTypes();
        $recipientTypes = NotificationSetting::getRecipientTypes();
        $priorities = NotificationSetting::getPriorities();

        return view('notifications.create-setting', compact('notificationTypes', 'recipientTypes', 'priorities'))
            ->with('title', 'Create Notification Setting')
            ->with('subtitle', 'Add new email notification configuration');
    }

    /**
     * Store a newly created notification setting
     */
    public function storeSetting(Request $request)
    {
        $request->validate([
            'notification_type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'enabled' => 'boolean',
            'recipient_type' => 'required|string|in:admin,tenant,specific_email',
            'recipient_email' => 'required_if:recipient_type,specific_email|nullable|email|max:255',
            'priority' => 'required|integer|in:1,2,3',
            'send_immediately' => 'boolean',
            'delay_minutes' => 'integer|min:0|max:1440',
        ]);

        try {
            NotificationSetting::create($request->all());
            return redirect()->route('notifications.settings.index')
                ->with('success', 'Notification setting created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create notification setting: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified notification setting
     */
    public function editSetting(NotificationSetting $notificationSetting)
    {
        $notificationTypes = Notification::getAvailableTypes();
        $recipientTypes = NotificationSetting::getRecipientTypes();
        $priorities = NotificationSetting::getPriorities();

        return view('notifications.edit-setting', compact('notificationSetting', 'notificationTypes', 'recipientTypes', 'priorities'))
            ->with('title', 'Edit Notification Setting')
            ->with('subtitle', 'Update email notification configuration');
    }

    /**
     * Update the specified notification setting
     */
    public function updateSetting(Request $request, NotificationSetting $notificationSetting)
    {
        $request->validate([
            'notification_type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'enabled' => 'boolean',
            'recipient_type' => 'required|string|in:admin,tenant,specific_email',
            'recipient_email' => 'required_if:recipient_type,specific_email|nullable|email|max:255',
            'priority' => 'required|integer|in:1,2,3',
            'send_immediately' => 'boolean',
            'delay_minutes' => 'integer|min:0|max:1440',
        ]);

        $notificationSetting->update($request->all());

        return redirect()->route('notifications.settings.index')
            ->with('success', 'Notification setting updated successfully.');
    }

    /**
     * Toggle notification setting enabled status
     */
    public function toggleSetting(NotificationSetting $notificationSetting)
    {
        $notificationSetting->update(['enabled' => !$notificationSetting->enabled]);

        $status = $notificationSetting->enabled ? 'enabled' : 'disabled';

        return response()->json([
            'success' => true,
            'message' => "Notification setting {$status} successfully.",
            'enabled' => $notificationSetting->enabled
        ]);
    }

    /**
     * Delete notification setting
     */
    public function destroySetting(NotificationSetting $notificationSetting)
    {
        $notificationSetting->delete();

        return redirect()->route('notifications.settings.index')
            ->with('success', 'Notification setting deleted successfully.');
    }

    /**
     * Handle bulk actions for notification settings
     */
    public function bulkActionSettings(Request $request)
    {
        $request->validate([
            'bulk_action' => 'required|in:enable,disable,delete',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:notification_settings,id'
        ]);

        $count = 0;

        if ($request->bulk_action === 'delete') {
            $settings = NotificationSetting::whereIn('id', $request->selected_ids)->get();
            foreach ($settings as $setting) {
                $setting->delete();
                $count++;
            }
            return redirect()->back()->with('success', "Successfully deleted {$count} notification setting(s).");
        } else {
            $enabled = $request->bulk_action === 'enable';
            $settings = NotificationSetting::whereIn('id', $request->selected_ids)->get();
            foreach ($settings as $setting) {
                $setting->update(['enabled' => $enabled]);
                $count++;
            }
            $action = $enabled ? 'enabled' : 'disabled';
            return redirect()->back()->with('success', "Successfully {$action} {$count} notification setting(s).");
        }
    }

    /**
     * Retry failed notification
     */
    public function retry(Notification $notification)
    {
        if (!$notification->canRetry()) {
            return response()->json([
                'success' => false,
                'message' => 'Notification cannot be retried.'
            ], 400);
        }

        $setting = NotificationSetting::byType($notification->type)->first();

        if (!$setting || !$setting->enabled) {
            return response()->json([
                'success' => false,
                'message' => 'No enabled setting found for this notification type.'
            ], 400);
        }

        $notification->update(['status' => Notification::STATUS_PENDING]);

        $success = $this->notificationService->sendEmail($notification, $setting);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification sent successfully.' : 'Failed to send notification.'
        ]);
    }

    /**
     * Process scheduled notifications
     */
    public function processScheduled()
    {
        $this->notificationService->processScheduledNotifications();

        return response()->json([
            'success' => true,
            'message' => "Scheduled notifications processed successfully.",
        ]);
    }

    /**
     * Retry failed notifications
     */
    public function retryFailed()
    {
        $retried = $this->notificationService->retryFailedNotifications();

        return response()->json([
            'success' => true,
            'message' => "Retried {$retried} failed notifications.",
        ]);
    }

    /**
     * Get notification statistics
     */
    public function statistics()
    {
        $stats = $this->notificationService->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Test notification
     */
    public function test(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'email' => 'required|email',
            'data' => 'nullable|array'
        ]);

        // Create a test notification
        $notification = Notification::create([
            'type' => $request->type,
            'title' => 'Test Notification',
            'message' => 'This is a test notification to verify email delivery.',
            'data' => $request->data ?? [],
            'recipient_email' => $request->email,
            'recipient_name' => 'Test User',
            'status' => Notification::STATUS_PENDING,
            'notifiable_type' => 'test',
            'notifiable_id' => 0,
            'created_by' => auth()->id(),
        ]);

        $setting = NotificationSetting::byType($request->type)->first();

        if ($setting && $setting->enabled) {
            $success = $this->notificationService->sendEmail($notification, $setting);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Test notification sent successfully.' : 'Failed to send test notification.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No enabled setting found for this notification type.'
        ], 400);
    }

    /**
     * Test all enabled notifications
     */
    public function testAll(Request $request)
    {
        $enabledSettings = NotificationSetting::enabled()->get();
        $testEmail = $request->get('email', auth()->user()->email);
        $successCount = 0;
        $totalCount = $enabledSettings->count();

        foreach ($enabledSettings as $setting) {
            // Create a test notification
            $notification = Notification::create([
                'type' => $setting->notification_type,
                'title' => 'Test Notification - ' . $setting->name,
                'message' => 'This is a test notification to verify email delivery for: ' . $setting->name,
                'data' => ['test' => true],
                'recipient_email' => $testEmail,
                'recipient_name' => 'Test User',
                'status' => Notification::STATUS_PENDING,
                'notifiable_type' => 'test',
                'notifiable_id' => 0,
                'created_by' => auth()->id(),
            ]);

            $success = $this->notificationService->sendEmail($notification, $setting);
            if ($success) {
                $successCount++;
            }
        }

        return response()->json([
            'success' => $successCount > 0,
            'message' => "Test notifications sent: {$successCount}/{$totalCount} successful."
        ]);
    }
}
