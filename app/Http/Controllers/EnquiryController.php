<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnquiryController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of enquiries (Admin)
     */
    public function index(Request $request)
    {
        $query = Enquiry::with('assignedUser')->recent();

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('enquiry_type')) {
            $query->byType($request->enquiry_type);
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->unassigned();
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        $enquiries = $query->get()->map(function ($enquiry) {
            return [
                'id' => $enquiry->id,
                'name' => $enquiry->name,
                'email' => $enquiry->email,
                'phone' => $enquiry->phone,
                'enquiry_type' => $enquiry->enquiry_type_display,
                'subject' => $enquiry->subject,
                'status' => $enquiry->status,
                'priority' => $enquiry->priority,
                'assigned_to' => $enquiry->assignedUser ? $enquiry->assignedUser->name : 'Unassigned',
                'is_overdue' => $enquiry->is_overdue,
                'created_at' => $enquiry->created_at->format('M j, Y g:i A'),
                'view_url' => route('enquiries.show', $enquiry->id),
                'delete_url' => route('enquiries.destroy', $enquiry->id)
            ];
        });

        $columns = [
            ['key' => 'name', 'label' => 'Name', 'width' => 'w-40'],
            ['key' => 'email', 'label' => 'Email', 'width' => 'w-48'],
            ['key' => 'enquiry_type', 'label' => 'Type', 'width' => 'w-32'],
            ['key' => 'subject', 'label' => 'Subject', 'width' => 'w-64'],
            ['key' => 'status', 'label' => 'Status', 'component' => 'components.status-badge', 'width' => 'w-24'],
            ['key' => 'priority', 'label' => 'Priority', 'component' => 'components.priority-badge', 'width' => 'w-24'],
            ['key' => 'assigned_to', 'label' => 'Assigned To', 'width' => 'w-32'],
            ['key' => 'created_at', 'label' => 'Received', 'width' => 'w-40']
        ];

        $filters = [
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    ['value' => 'new', 'label' => 'New'],
                    ['value' => 'in_progress', 'label' => 'In Progress'],
                    ['value' => 'resolved', 'label' => 'Resolved'],
                    ['value' => 'closed', 'label' => 'Closed']
                ]
            ],
            [
                'key' => 'priority',
                'label' => 'Priority',
                'type' => 'select',
                'options' => [
                    ['value' => 'low', 'label' => 'Low'],
                    ['value' => 'medium', 'label' => 'Medium'],
                    ['value' => 'high', 'label' => 'High'],
                    ['value' => 'urgent', 'label' => 'Urgent']
                ]
            ],
            [
                'key' => 'enquiry_type',
                'label' => 'Type',
                'type' => 'select',
                'options' => [
                    ['value' => 'room_booking', 'label' => 'Room Booking'],
                    ['value' => 'general_info', 'label' => 'General Info'],
                    ['value' => 'pricing', 'label' => 'Pricing'],
                    ['value' => 'facilities', 'label' => 'Facilities'],
                    ['value' => 'other', 'label' => 'Other']
                ]
            ],
            [
                'key' => 'assigned_to',
                'label' => 'Assigned To',
                'type' => 'select',
                'options' => array_merge(
                    [['value' => 'unassigned', 'label' => 'Unassigned']],
                    User::all()->map(fn($user) => ['value' => $user->id, 'label' => $user->name])->toArray()
                )
            ]
        ];

        $bulkActions = [
            [
                'key' => 'assign',
                'label' => 'Assign to Me',
                'icon' => 'fas fa-user-plus'
            ],
            [
                'key' => 'mark_in_progress',
                'label' => 'Mark In Progress',
                'icon' => 'fas fa-clock'
            ],
            [
                'key' => 'mark_resolved',
                'label' => 'Mark Resolved',
                'icon' => 'fas fa-check'
            ],
            [
                'key' => 'delete',
                'label' => 'Delete',
                'icon' => 'fas fa-trash'
            ]
        ];

        $pagination = [
            'from' => 1,
            'to' => $enquiries->count(),
            'total' => $enquiries->count(),
            'current_page' => 1,
            'per_page' => 25
        ];

        return view('enquiries.index', compact('enquiries', 'columns', 'filters', 'bulkActions', 'pagination'));
    }

    /**
     * Show the form for creating a new enquiry (Public)
     */
    public function create()
    {
        return view('enquiries.create');
    }

    /**
     * Store a newly created enquiry (Public)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'enquiry_type' => 'required|in:room_booking,general_info,pricing,facilities,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'preferred_checkin' => 'nullable|date|after:today',
            'budget_range' => 'nullable|string|max:50',
            'duration' => 'nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Prepare metadata
        $metadata = [];
        if ($request->filled('preferred_checkin')) {
            $metadata['preferred_checkin'] = $request->preferred_checkin;
        }
        if ($request->filled('budget_range')) {
            $metadata['budget_range'] = $request->budget_range;
        }
        if ($request->filled('duration')) {
            $metadata['duration'] = $request->duration;
        }

        $enquiry = Enquiry::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'enquiry_type' => $request->enquiry_type,
            'subject' => $request->subject,
            'message' => $request->message,
            'metadata' => $metadata,
            'source' => 'website'
        ]);

        // Send notifications
        try {
            // Notify admin about new enquiry
            $this->notificationService->sendNotification('enquiry_received_admin', $enquiry, [
                'subject' => 'New Enquiry Received: ' . $enquiry->subject,
                'heading' => 'New Enquiry Received',
                'body' => "A new enquiry has been received from the website:\n\n" .
                         "Name: {$enquiry->name}\n" .
                         "Email: {$enquiry->email}\n" .
                         "Phone: {$enquiry->phone}\n" .
                         "Type: " . ucfirst(str_replace('_', ' ', $enquiry->enquiry_type)) . "\n" .
                         "Subject: {$enquiry->subject}\n" .
                         "Message: {$enquiry->message}\n\n" .
                         "Please log in to the CRM to view full details and respond.",
                'action_url' => route('enquiries.show', $enquiry->id),
                'action_text' => 'View Enquiry',
                'badge_text' => 'New',
                'badge_type' => 'info',
            ]);

            // Send confirmation to the enquirer
            $this->notificationService->sendNotification('enquiry_received_tenant', $enquiry, [
                'subject' => 'Your Enquiry Confirmation - ' . $enquiry->subject,
                'heading' => 'Thank You for Your Enquiry!',
                'body' => "Dear {$enquiry->name},\n\n" .
                         "Thank you for your enquiry regarding '{$enquiry->subject}'. We have received your message and will get back to you shortly.\n\n" .
                         "Your enquiry details:\n" .
                         "• Type: " . ucfirst(str_replace('_', ' ', $enquiry->enquiry_type)) . "\n" .
                         "• Subject: {$enquiry->subject}\n" .
                         "• Message: {$enquiry->message}\n\n" .
                         "We appreciate your interest in our hostel and look forward to assisting you.\n\n" .
                         "Best regards,\nThe Hostel Team",
                'greeting' => $enquiry->name,
                'badge_text' => 'Confirmation',
                'badge_type' => 'success',
            ], $enquiry->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send enquiry notifications: ' . $e->getMessage());
        }

        return redirect()->route('enquiry.success')->with('success', 'Thank you for your enquiry! We will get back to you soon.');
    }

    /**
     * Display the specified enquiry (Admin)
     */
    public function show(string $id)
    {
        $enquiry = Enquiry::with('assignedUser')->findOrFail($id);

        return view('enquiries.show', compact('enquiry'));
    }


    /**
     * Update the specified enquiry (Admin)
     */
    public function update(Request $request, string $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:new,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'admin_notes' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $updateData = [
            'status' => $request->status,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'admin_notes' => $request->admin_notes
        ];

        // Set responded_at if status is being changed to resolved/closed and it's not set yet
        if (in_array($request->status, ['resolved', 'closed']) && !$enquiry->responded_at) {
            $updateData['responded_at'] = now();
        }

        $enquiry->update($updateData);

        return redirect()->route('enquiries.show', $enquiry->id)->with('success', 'Enquiry updated successfully!');
    }

    /**
     * Remove the specified enquiry (Admin)
     */
    public function destroy(string $id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->delete();

        return redirect()->route('enquiries.index')->with('success', 'Enquiry deleted successfully!');
    }

    /**
     * Show enquiry success page (Public)
     */
    public function success()
    {
        return view('enquiries.success');
    }

    /**
     * Show public enquiry form (Public)
     */
    public function publicForm()
    {
        return view('enquiries.public-form');
    }
}
