<?php

namespace App\Console\Commands;

use App\Models\Bed;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateReservedBeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beds:update-reserved';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update reserved beds to occupied when their lease start date arrives';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Find all reserved beds where the lease start date is today or in the past
        $reservedBeds = Bed::where('status', 'reserved')
            ->where('occupied_from', '<=', $today)
            ->with(['tenant', 'room'])
            ->get();

        if ($reservedBeds->isEmpty()) {
            $this->info('No reserved beds need to be updated today.');
            return;
        }

        $updatedCount = 0;

        foreach ($reservedBeds as $bed) {
            // Update bed status to occupied
            $bed->update(['status' => 'occupied']);

            // Update room status
            $bed->room->updateStatus();

            $updatedCount++;

            $this->info("Updated bed {$bed->bed_number} in room {$bed->room->room_number} to occupied for tenant {$bed->tenant->name}");
        }

        $this->info("Successfully updated {$updatedCount} reserved beds to occupied status.");
    }
}
