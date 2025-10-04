<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TenantAmenity;
use App\Models\TenantAmenityUsage;
use App\Models\User;
use Carbon\Carbon;

class AmenityUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Creating sample amenity usage records...\n";

        // Get active tenant amenities
        $tenantAmenities = TenantAmenity::where('status', 'active')->get();

        if ($tenantAmenities->isEmpty()) {
            echo "No active tenant amenities found. Please run TenantAmenitySeeder first.\n";
            return;
        }

        // Get a user to record the usage (admin user)
        $recordedBy = User::first()->id ?? 1;

        // Create usage records for the past 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        foreach ($tenantAmenities as $tenantAmenity) {
            // Create random usage records for this tenant amenity
            $usageDays = rand(5, 15); // Random number of days they used the amenity

            for ($i = 0; $i < $usageDays; $i++) {
                // Random date within the past 30 days
                $usageDate = Carbon::createFromTimestamp(
                    rand($startDate->timestamp, $endDate->timestamp)
                )->startOfDay();

                // Check if usage already exists for this date
                $existingUsage = TenantAmenityUsage::where('tenant_amenity_id', $tenantAmenity->id)
                    ->where('usage_date', $usageDate)
                    ->first();

                if (!$existingUsage) {
                    $quantity = rand(1, 3); // Random quantity between 1-3
                    $unitPrice = $tenantAmenity->price;
                    $totalAmount = $unitPrice * $quantity;

                    // Random notes (sometimes empty)
                    $notes = rand(1, 100) > 70 ? $this->getRandomNote() : null;

                    TenantAmenityUsage::create([
                        'tenant_amenity_id' => $tenantAmenity->id,
                        'usage_date' => $usageDate,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_amount' => $totalAmount,
                        'notes' => $notes,
                        'recorded_by' => $recordedBy,
                    ]);
                }
            }
        }

        // Create some recent usage records (last 7 days) for better demo
        $recentStartDate = Carbon::now()->subDays(7);

        foreach ($tenantAmenities->take(5) as $tenantAmenity) {
            for ($i = 0; $i < 7; $i++) {
                $usageDate = $recentStartDate->copy()->addDays($i);

                // 70% chance of usage each day
                if (rand(1, 100) <= 70) {
                    $existingUsage = TenantAmenityUsage::where('tenant_amenity_id', $tenantAmenity->id)
                        ->where('usage_date', $usageDate)
                        ->first();

                    if (!$existingUsage) {
                        $quantity = rand(1, 2);
                        $unitPrice = $tenantAmenity->price;
                        $totalAmount = $unitPrice * $quantity;

                        TenantAmenityUsage::create([
                            'tenant_amenity_id' => $tenantAmenity->id,
                            'usage_date' => $usageDate,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_amount' => $totalAmount,
                            'notes' => rand(1, 100) > 80 ? $this->getRandomNote() : null,
                            'recorded_by' => $recordedBy,
                        ]);
                    }
                }
            }
        }

        $totalRecords = TenantAmenityUsage::count();
        echo "Created {$totalRecords} amenity usage records successfully!\n";
    }

    /**
     * Get a random note for usage record
     */
    private function getRandomNote(): string
    {
        $notes = [
            'Regular usage',
            'Extra portion requested',
            'Late usage',
            'Special request',
            'Holiday usage',
            'Guest usage',
            'Makeup for missed day',
            'Double portion',
            'Early usage',
            'Weekend usage',
        ];

        return $notes[array_rand($notes)];
    }
}
