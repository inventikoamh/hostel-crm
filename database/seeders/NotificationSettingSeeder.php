<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationSetting;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Tenant Management Notifications
            [
                'notification_type' => 'tenant_added',
                'name' => 'Tenant Registration Notification (Admin)',
                'description' => 'Notify admin when a new tenant is registered',
                'enabled' => true,
                'recipient_type' => 'admin',
                'recipient_email' => null,
                'priority' => 2,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],
            [
                'notification_type' => 'tenant_added',
                'name' => 'Welcome Email to Tenant',
                'description' => 'Send welcome email to newly registered tenant',
                'enabled' => true,
                'recipient_type' => 'tenant',
                'recipient_email' => null,
                'priority' => 2,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],
            [
                'notification_type' => 'tenant_updated',
                'name' => 'Tenant Profile Update Notification',
                'description' => 'Notify admin when tenant profile is updated',
                'enabled' => true,
                'recipient_type' => 'admin',
                'recipient_email' => null,
                'priority' => 3,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],

            // Enquiry Management Notifications
            [
                'notification_type' => 'enquiry_added',
                'name' => 'New Enquiry Notification (Admin)',
                'description' => 'Notify admin when a new enquiry is received',
                'enabled' => true,
                'recipient_type' => 'admin',
                'recipient_email' => null,
                'priority' => 1,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],
            [
                'notification_type' => 'enquiry_added',
                'name' => 'Enquiry Confirmation (Tenant)',
                'description' => 'Confirm enquiry submission to tenant',
                'enabled' => true,
                'recipient_type' => 'tenant',
                'recipient_email' => null,
                'priority' => 2,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],

            // Invoice Management Notifications
            [
                'notification_type' => 'invoice_created',
                'name' => 'Invoice Created Notification',
                'description' => 'Notify tenant when an invoice is created',
                'enabled' => true,
                'recipient_type' => 'tenant',
                'recipient_email' => null,
                'priority' => 2,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],
            [
                'notification_type' => 'invoice_sent',
                'name' => 'Invoice Sent Confirmation',
                'description' => 'Confirm invoice has been sent to tenant',
                'enabled' => true,
                'recipient_type' => 'tenant',
                'recipient_email' => null,
                'priority' => 2,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],

            // Payment Management Notifications
            [
                'notification_type' => 'payment_received',
                'name' => 'Payment Confirmation (Tenant)',
                'description' => 'Confirm payment receipt to tenant',
                'enabled' => true,
                'recipient_type' => 'tenant',
                'recipient_email' => null,
                'priority' => 2,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],
            [
                'notification_type' => 'payment_verified',
                'name' => 'Payment Verification Notification (Admin)',
                'description' => 'Notify admin when payment is verified',
                'enabled' => true,
                'recipient_type' => 'admin',
                'recipient_email' => null,
                'priority' => 1,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],

            // Amenity Usage Notifications
            [
                'notification_type' => 'amenity_usage_recorded',
                'name' => 'Amenity Usage Notification (Admin)',
                'description' => 'Notify admin when amenity usage is recorded',
                'enabled' => true,
                'recipient_type' => 'admin',
                'recipient_email' => null,
                'priority' => 3,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],

            // Lease Management Notifications
            [
                'notification_type' => 'lease_expiring',
                'name' => 'Lease Expiring Reminder',
                'description' => 'Remind tenant about expiring lease',
                'enabled' => true,
                'recipient_type' => 'tenant',
                'recipient_email' => null,
                'priority' => 1,
                'send_immediately' => false,
                'delay_minutes' => 0,
            ],

            // Overdue Payment Notifications
            [
                'notification_type' => 'overdue_payment',
                'name' => 'Overdue Payment Alert (Tenant)',
                'description' => 'Alert tenant about overdue payments',
                'enabled' => true,
                'recipient_type' => 'tenant',
                'recipient_email' => null,
                'priority' => 1,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],
            [
                'notification_type' => 'overdue_payment',
                'name' => 'Overdue Payment Alert (Admin)',
                'description' => 'Alert admin about overdue payments',
                'enabled' => true,
                'recipient_type' => 'admin',
                'recipient_email' => null,
                'priority' => 1,
                'send_immediately' => true,
                'delay_minutes' => 0,
            ],
        ];

        foreach ($settings as $setting) {
            NotificationSetting::updateOrCreate(
                [
                    'notification_type' => $setting['notification_type'],
                    'recipient_type' => $setting['recipient_type'],
                    'recipient_email' => $setting['recipient_email'],
                ],
                $setting
            );
        }
    }
}
