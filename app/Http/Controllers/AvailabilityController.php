<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Bed;
use App\Models\BedAssignment;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    /**
     * Display the availability checking page
     */
    public function index()
    {
        $hostels = Hostel::active()->get();

        return view('availability.index', compact('hostels'));
    }

    /**
     * Check availability for rooms and beds based on lease dates
     */
    public function check(Request $request)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'lease_start_date' => 'required|date',
            'lease_end_date' => 'nullable|date|after:lease_start_date',
        ]);

        $hostel = Hostel::findOrFail($request->hostel_id);
        $leaseStartDate = Carbon::parse($request->lease_start_date);
        $leaseEndDate = $request->lease_end_date ? Carbon::parse($request->lease_end_date) : null;

        // Get all rooms with their beds and assignments
        $rooms = $hostel->rooms()->with(['beds.assignments.tenant'])->get();

        $availabilityData = [];

        foreach ($rooms as $room) {
            $roomData = [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'room_name' => $room->name,
                'floor' => $room->floor,
                'room_type' => $room->room_type,
                'total_beds' => $room->beds->count(),
                'available_beds' => 0,
                'occupied_beds' => 0,
                'reserved_beds' => 0,
                'maintenance_beds' => 0,
                'beds' => []
            ];

            foreach ($room->beds as $bed) {
                $bedAvailability = $this->checkBedAvailability($bed, $leaseStartDate, $leaseEndDate);

                $bedData = [
                    'bed_id' => $bed->id,
                    'bed_number' => $bed->bed_number,
                    'bed_type' => $bed->bed_type,
                    'monthly_rent' => $bed->monthly_rent,
                    'status' => $bed->status,
                    'availability' => $bedAvailability['status'],
                    'availability_reason' => $bedAvailability['reason'],
                    'current_assignments' => $bed->assignments->map(function($assignment) {
                        return [
                            'id' => $assignment->id,
                            'tenant_name' => $assignment->tenant->name,
                            'status' => $assignment->status,
                            'assigned_from' => $assignment->assigned_from->format('M j, Y'),
                            'assigned_until' => $assignment->assigned_until ? $assignment->assigned_until->format('M j, Y') : 'No end date',
                            'monthly_rent' => $assignment->monthly_rent
                        ];
                    })
                ];

                $roomData['beds'][] = $bedData;

                // Count beds by availability status
                switch ($bedAvailability['status']) {
                    case 'available':
                        $roomData['available_beds']++;
                        break;
                    case 'occupied':
                        $roomData['occupied_beds']++;
                        break;
                    case 'reserved':
                        $roomData['reserved_beds']++;
                        break;
                    case 'maintenance':
                        $roomData['maintenance_beds']++;
                        break;
                }
            }

            $availabilityData[] = $roomData;
        }

        return response()->json([
            'hostel' => [
                'id' => $hostel->id,
                'name' => $hostel->name,
                'address' => $hostel->address
            ],
            'search_criteria' => [
                'lease_start_date' => $leaseStartDate->format('M j, Y'),
                'lease_end_date' => $leaseEndDate ? $leaseEndDate->format('M j, Y') : 'No end date'
            ],
            'summary' => [
                'total_rooms' => count($availabilityData),
                'total_beds' => collect($availabilityData)->sum('total_beds'),
                'available_beds' => collect($availabilityData)->sum('available_beds'),
                'occupied_beds' => collect($availabilityData)->sum('occupied_beds'),
                'reserved_beds' => collect($availabilityData)->sum('reserved_beds'),
                'maintenance_beds' => collect($availabilityData)->sum('maintenance_beds')
            ],
            'rooms' => $availabilityData
        ]);
    }

    /**
     * Check if a bed is available for the given lease period
     */
    private function checkBedAvailability($bed, $leaseStartDate, $leaseEndDate = null)
    {
        // If bed is in maintenance, it's not available
        if ($bed->status === 'maintenance') {
            return [
                'status' => 'maintenance',
                'reason' => 'Bed is under maintenance'
            ];
        }

        // If bed is available, it's available
        if ($bed->status === 'available') {
            return [
                'status' => 'available',
                'reason' => 'Bed is available for assignment'
            ];
        }

        // Check assignments for conflicts
        $conflictingAssignments = $bed->assignments->filter(function($assignment) use ($leaseStartDate, $leaseEndDate) {
            // Skip inactive assignments
            if ($assignment->status === 'inactive') {
                return false;
            }

            $assignmentStart = $assignment->assigned_from;
            $assignmentEnd = $assignment->assigned_until;

            // If no lease end date provided, just check if assignment ends before lease starts
            if (!$leaseEndDate) {
                return $assignmentEnd && $assignmentEnd->gte($leaseStartDate);
            }

            // Check for date overlap
            if ($assignmentEnd) {
                // Assignment has an end date - check for overlap
                return $assignmentStart->lt($leaseEndDate) && $assignmentEnd->gt($leaseStartDate);
            } else {
                // Assignment has no end date - check if it starts before lease ends
                return $assignmentStart->lt($leaseEndDate);
            }
        });

        if ($conflictingAssignments->count() > 0) {
            $conflictingAssignment = $conflictingAssignments->first();
            $status = $conflictingAssignment->status === 'active' ? 'occupied' : 'reserved';
            $reason = $conflictingAssignment->status === 'active'
                ? "Currently occupied by {$conflictingAssignment->tenant->name}"
                : "Reserved by {$conflictingAssignment->tenant->name} from {$conflictingAssignment->assigned_from->format('M j, Y')}";

            return [
                'status' => $status,
                'reason' => $reason
            ];
        }

        // No conflicts found
        return [
            'status' => 'available',
            'reason' => 'Bed is available for the requested period'
        ];
    }
}
