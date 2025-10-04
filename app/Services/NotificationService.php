<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Models\TenantProfile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Send notification for a specific event
     */
    public function sendNotification(string $type, $notifiable, array $data = []): bool
    {
        try {
            // Get notification settings for this type
            $settings = NotificationSetting::enabled()
                ->byType($type)
                ->get();

            if ($settings->isEmpty()) {
                Log::info("No notification settings found for type: {$type}");
                return false;
            }

            $sent = false;

            foreach ($settings as $setting) {
                // Determine recipient email
                $recipientEmail = $this->getRecipientEmail($setting, $notifiable);

                if (!$recipientEmail) {
                    continue;
                }

                // Create notification record
                $notification = $this->createNotification($type, $notifiable, $setting, $data, $recipientEmail);

                // Send immediately or schedule
                if ($setting->shouldSendImmediately()) {
                    $this->sendEmail($notification, $setting);
                    $sent = true;
                } else {
                    $this->scheduleNotification($notification, $setting);
                }
            }

            return $sent;

        } catch (\Exception $e) {
            Log::error("Failed to send notification for type {$type}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recipient email based on setting and notifiable
     */
    private function getRecipientEmail(NotificationSetting $setting, $notifiable): ?string
    {
        switch ($setting->recipient_type) {
            case NotificationSetting::RECIPIENT_ADMIN:
                return config('mail.admin_email', 'admin@hostel.com');

            case NotificationSetting::RECIPIENT_TENANT:
                if ($notifiable instanceof TenantProfile) {
                    return $notifiable->user->email;
                }
                if (isset($notifiable->tenantProfile) && $notifiable->tenantProfile) {
                    return $notifiable->tenantProfile->user->email;
                }
                return null;

            case NotificationSetting::RECIPIENT_SPECIFIC_EMAIL:
                return $setting->recipient_email;

            default:
                return null;
        }
    }

    /**
     * Create notification record
     */
    private function createNotification(string $type, $notifiable, NotificationSetting $setting, array $data, string $recipientEmail): Notification
    {
        $template = $setting->getEmailTemplate();
        $processedData = $this->processTemplateData($template, $notifiable, $data);

        return Notification::create([
            'type' => $type,
            'title' => $processedData['subject'],
            'message' => $processedData['body'],
            'data' => array_merge($data, $processedData),
            'recipient_email' => $recipientEmail,
            'recipient_name' => $this->getRecipientName($notifiable),
            'status' => Notification::STATUS_PENDING,
            'scheduled_at' => $setting->shouldSendImmediately() ? null : now()->addMinutes($setting->getDelayMinutes()),
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Process template data with actual values
     */
    private function processTemplateData(array $template, $notifiable, array $data): array
    {
        $processed = [];

        foreach ($template as $key => $value) {
            $processed[$key] = $this->replacePlaceholders($value, $notifiable, $data);
        }

        return $processed;
    }

    /**
     * Replace placeholders in template
     */
    private function replacePlaceholders(string $text, $notifiable, array $data): string
    {
        $placeholders = [
            '{{hostel_name}}' => config('app.name', 'Hostel CRM'),
            '{{app_name}}' => config('app.name', 'Hostel CRM'),
            '{{current_date}}' => now()->format('M j, Y'),
            '{{current_time}}' => now()->format('g:i A'),
        ];

        // Add notifiable-specific placeholders
        if ($notifiable instanceof TenantProfile) {
            $placeholders['{{tenant_name}}'] = $notifiable->user->name;
            $placeholders['{{tenant_email}}'] = $notifiable->user->email;
            $placeholders['{{room_number}}'] = $notifiable->bed->room->room_number ?? 'N/A';
            $placeholders['{{bed_number}}'] = $notifiable->bed->bed_number ?? 'N/A';
        }

        // Add data-specific placeholders
        foreach ($data as $key => $value) {
            $placeholders["{{$key}}"] = $value;
        }

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }

    /**
     * Get recipient name
     */
    private function getRecipientName($notifiable): ?string
    {
        if ($notifiable instanceof TenantProfile) {
            return $notifiable->user->name;
        }

        if (isset($notifiable->tenantProfile) && $notifiable->tenantProfile) {
            return $notifiable->tenantProfile->user->name;
        }

        return null;
    }

    /**
     * Send email notification
     */
    public function sendEmail(Notification $notification, NotificationSetting $setting): bool
    {
        try {
            $template = $setting->getEmailTemplate();

            Mail::send('emails.notification', [
                'notification' => $notification,
                'template' => $template,
                'data' => $notification->data,
            ], function ($message) use ($notification, $template) {
                $message->to($notification->recipient_email, $notification->recipient_name)
                       ->subject($template['subject']);
            });

            $notification->markAsSent();
            Log::info("Notification sent successfully: {$notification->id}");
            return true;

        } catch (\Exception $e) {
            $notification->markAsFailed($e->getMessage());
            Log::error("Failed to send notification {$notification->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Schedule notification for later
     */
    private function scheduleNotification(Notification $notification, NotificationSetting $setting): void
    {
        $notification->update([
            'scheduled_at' => now()->addMinutes($setting->getDelayMinutes()),
        ]);

        Log::info("Notification scheduled: {$notification->id} for {$notification->scheduled_at}");
    }

    /**
     * Process scheduled notifications
     */
    public function processScheduledNotifications(): int
    {
        $notifications = Notification::scheduled()->get();
        $processed = 0;

        foreach ($notifications as $notification) {
            $setting = NotificationSetting::byType($notification->type)->first();

            if ($setting && $setting->enabled) {
                if ($this->sendEmail($notification, $setting)) {
                    $processed++;
                }
            }
        }

        return $processed;
    }

    /**
     * Retry failed notifications
     */
    public function retryFailedNotifications(int $maxRetries = 3): int
    {
        $notifications = Notification::failed()
            ->where('retry_count', '<', $maxRetries)
            ->get();

        $retried = 0;

        foreach ($notifications as $notification) {
            $setting = NotificationSetting::byType($notification->type)->first();

            if ($setting && $setting->enabled) {
                $notification->update(['status' => Notification::STATUS_PENDING]);

                if ($this->sendEmail($notification, $setting)) {
                    $retried++;
                }
            }
        }

        return $retried;
    }

    /**
     * Send tenant added notification
     */
    public function sendTenantAddedNotification(TenantProfile $tenant): bool
    {
        return $this->sendNotification(Notification::TYPE_TENANT_ADDED, $tenant, [
            'tenant_name' => $tenant->user->name,
            'room_number' => $tenant->bed->room->room_number ?? 'N/A',
            'bed_number' => $tenant->bed->bed_number ?? 'N/A',
            'lease_start' => $tenant->lease_start->format('M j, Y'),
            'lease_end' => $tenant->lease_end->format('M j, Y'),
        ]);
    }

    /**
     * Send enquiry added notification
     */
    public function sendEnquiryAddedNotification($enquiry): bool
    {
        return $this->sendNotification(Notification::TYPE_ENQUIRY_ADDED, $enquiry, [
            'enquirer_name' => $enquiry->name,
            'enquiry_subject' => $enquiry->subject,
            'enquiry_message' => $enquiry->message,
            'enquiry_email' => $enquiry->email,
            'enquiry_phone' => $enquiry->phone,
        ]);
    }

    /**
     * Send invoice created notification
     */
    public function sendInvoiceCreatedNotification($invoice): bool
    {
        return $this->sendNotification(Notification::TYPE_INVOICE_CREATED, $invoice, [
            'invoice_number' => $invoice->invoice_number,
            'invoice_amount' => '₹' . number_format($invoice->total_amount, 2),
            'due_date' => $invoice->due_date->format('M j, Y'),
            'invoice_type' => ucfirst($invoice->type),
        ]);
    }

    /**
     * Send payment received notification
     */
    public function sendPaymentReceivedNotification($payment): bool
    {
        return $this->sendNotification(Notification::TYPE_PAYMENT_RECEIVED, $payment, [
            'payment_number' => $payment->payment_number,
            'payment_amount' => '₹' . number_format($payment->amount, 2),
            'payment_method' => ucfirst(str_replace('_', ' ', $payment->payment_method)),
            'payment_date' => $payment->payment_date->format('M j, Y'),
            'invoice_number' => $payment->invoice->invoice_number ?? 'N/A',
        ]);
    }

    /**
     * Get notification statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Notification::count(),
            'pending' => Notification::pending()->count(),
            'sent' => Notification::sent()->count(),
            'failed' => Notification::failed()->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
            'this_month' => Notification::whereMonth('created_at', now()->month)->count(),
        ];
    }
}
