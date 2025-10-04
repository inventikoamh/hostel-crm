<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\TenantProfile;
use App\Models\User;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tenant profiles
        $tenantProfiles = TenantProfile::with('user')->get();

        if ($tenantProfiles->isEmpty()) {
            $this->command->info('No tenant profiles found. Please seed tenants first.');
            return;
        }

        $this->command->info('Creating sample invoices...');

        foreach ($tenantProfiles->take(5) as $index => $tenant) {
            // Create rent invoice
            $rentInvoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'tenant_profile_id' => $tenant->id,
                'type' => 'rent',
                'status' => $index % 3 == 0 ? 'paid' : ($index % 2 == 0 ? 'sent' : 'draft'),
                'invoice_date' => now()->subDays(rand(1, 30)),
                'due_date' => now()->addDays(rand(1, 15)),
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'tax_amount' => 0,
                'discount_amount' => 0,
                'notes' => 'Monthly rent invoice for ' . now()->format('F Y'),
                'created_by' => 1
            ]);

            // Add rent item
            $rentInvoice->items()->create([
                'item_type' => 'rent',
                'description' => 'Room Rent - ' . now()->format('F Y'),
                'quantity' => 1,
                'unit_price' => $tenant->monthly_rent ?? 5000,
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth()
            ]);

            $rentInvoice->calculateTotals();

            // Create payment for paid invoices
            if ($rentInvoice->status === 'paid') {
                $payment = $rentInvoice->addPayment($rentInvoice->total_amount, [
                    'payment_date' => now()->subDays(rand(1, 10)),
                    'payment_method' => collect(['cash', 'bank_transfer', 'upi'])->random(),
                    'reference_number' => 'REF' . rand(100000, 999999),
                    'notes' => 'Rent payment for ' . now()->format('F Y'),
                    'status' => 'completed'
                ]);
                $payment->verify();
            }

            // Create amenities invoice (every other tenant)
            if ($index % 2 == 0) {
                $amenitiesInvoice = Invoice::create([
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'tenant_profile_id' => $tenant->id,
                    'type' => 'amenities',
                    'status' => 'sent',
                    'invoice_date' => now()->subDays(rand(5, 20)),
                    'due_date' => now()->addDays(rand(5, 10)),
                    'period_start' => now()->startOfMonth(),
                    'period_end' => now()->endOfMonth(),
                    'tax_amount' => 50,
                    'discount_amount' => 0,
                    'notes' => 'Paid amenities for ' . now()->format('F Y'),
                    'created_by' => 1
                ]);

                // Add amenity items
                $amenitiesInvoice->items()->create([
                    'item_type' => 'amenities',
                    'description' => 'Lunch Service - ' . now()->format('F Y'),
                    'quantity' => 30,
                    'unit_price' => 80,
                    'period_start' => now()->startOfMonth(),
                    'period_end' => now()->endOfMonth()
                ]);

                $amenitiesInvoice->items()->create([
                    'item_type' => 'amenities',
                    'description' => 'Laundry Service - ' . now()->format('F Y'),
                    'quantity' => 8,
                    'unit_price' => 25,
                    'period_start' => now()->startOfMonth(),
                    'period_end' => now()->endOfMonth()
                ]);

                $amenitiesInvoice->calculateTotals();
            }

            // Create damage invoice (every third tenant)
            if ($index % 3 == 0) {
                $damageInvoice = Invoice::create([
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'tenant_profile_id' => $tenant->id,
                    'type' => 'damage',
                    'status' => 'overdue',
                    'invoice_date' => now()->subDays(rand(20, 40)),
                    'due_date' => now()->subDays(rand(1, 10)),
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'notes' => 'Damage charges as per inspection report',
                    'created_by' => 1
                ]);

                // Add damage items
                $damageInvoice->items()->create([
                    'item_type' => 'damage',
                    'description' => 'Broken window glass replacement',
                    'quantity' => 1,
                    'unit_price' => 800
                ]);

                $damageInvoice->items()->create([
                    'item_type' => 'damage',
                    'description' => 'Wall paint touch-up',
                    'quantity' => 1,
                    'unit_price' => 500
                ]);

                $damageInvoice->calculateTotals();
            }
        }

        // Create some additional payments
        $unpaidInvoices = Invoice::where('status', 'sent')->where('balance_amount', '>', 0)->get();

        foreach ($unpaidInvoices->take(3) as $invoice) {
            $partialAmount = $invoice->total_amount * 0.5; // 50% payment

            $payment = $invoice->addPayment($partialAmount, [
                'payment_date' => now()->subDays(rand(1, 5)),
                'payment_method' => collect(['cash', 'bank_transfer', 'upi', 'card'])->random(),
                'reference_number' => 'PAY' . rand(100000, 999999),
                'notes' => 'Partial payment',
                'status' => 'completed'
            ]);

            // Verify some payments
            if (rand(0, 1)) {
                $payment->verify();
            }
        }

        $this->command->info('Sample invoices and payments created successfully!');
    }
}
