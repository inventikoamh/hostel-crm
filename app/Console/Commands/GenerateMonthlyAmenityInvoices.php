<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Carbon\Carbon;

class GenerateMonthlyAmenityInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-amenity {--month= : Month to generate invoices for (YYYY-MM format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly amenity usage invoices for all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monthInput = $this->option('month');

        if ($monthInput) {
            try {
                $month = Carbon::createFromFormat('Y-m', $monthInput);
            } catch (\Exception $e) {
                $this->error('Invalid month format. Please use YYYY-MM format (e.g., 2024-01)');
                return 1;
            }
        } else {
            $month = now()->subMonth();
        }

        $this->info("Generating amenity invoices for {$month->format('F Y')}...");

        try {
            $results = Invoice::generateMonthlyAmenityInvoices($month);

            $this->info("Invoice generation completed!");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Generated', $results['generated']],
                    ['Skipped', $results['skipped']],
                    ['Errors', count($results['errors'])]
                ]
            );

            if (!empty($results['errors'])) {
                $this->error("Errors encountered:");
                foreach ($results['errors'] as $error) {
                    $this->error("- {$error['tenant']}: {$error['error']}");
                }
            }

            if ($results['generated'] > 0) {
                $this->info("Successfully generated {$results['generated']} amenity invoices.");
            }

            if ($results['skipped'] > 0) {
                $this->warn("Skipped {$results['skipped']} tenants (no usage or already invoiced).");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to generate invoices: " . $e->getMessage());
            return 1;
        }
    }
}
