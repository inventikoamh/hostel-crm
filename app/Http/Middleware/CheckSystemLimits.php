<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Bed;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limitType): Response
    {
        // Skip limit checks for super admins
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        switch ($limitType) {
            case 'hostels':
                $maxHostels = SystemSetting::getValue('max_hostels', 10);
                $currentHostels = Hostel::count();

                if ($currentHostels >= $maxHostels) {
                    return redirect()->back()->with('error', "Maximum number of hostels ({$maxHostels}) has been reached. Contact your administrator to increase the limit.");
                }
                break;

            case 'floors':
                $maxFloors = SystemSetting::getValue('max_floors_per_hostel', 5);
                $hostelId = $request->route('hostel') ?? $request->input('hostel_id');

                if ($hostelId) {
                    $hostel = Hostel::find($hostelId);
                    if ($hostel) {
                        $currentFloors = $hostel->rooms()->distinct('floor')->count('floor');
                        if ($currentFloors >= $maxFloors) {
                            return redirect()->back()->with('error', "Maximum number of floors per hostel ({$maxFloors}) has been reached. Contact your administrator to increase the limit.");
                        }
                    }
                }
                break;

            case 'rooms':
                $maxRooms = SystemSetting::getValue('max_rooms_per_floor', 20);
                $hostelId = $request->route('hostel') ?? $request->input('hostel_id');
                $floor = $request->input('floor');

                if ($hostelId && $floor !== null) {
                    $hostel = Hostel::find($hostelId);
                    if ($hostel) {
                        $currentRooms = $hostel->rooms()->where('floor', $floor)->count();
                        if ($currentRooms >= $maxRooms) {
                            return redirect()->back()->with('error', "Maximum number of rooms per floor ({$maxRooms}) has been reached. Contact your administrator to increase the limit.");
                        }
                    }
                }
                break;

            case 'beds':
                $maxBeds = SystemSetting::getValue('max_beds_per_room', 10);
                $roomId = $request->route('room') ?? $request->input('room_id');

                if ($roomId) {
                    $room = Room::find($roomId);
                    if ($room) {
                        $currentBeds = $room->beds()->count();
                        if ($currentBeds >= $maxBeds) {
                            return redirect()->back()->with('error', "Maximum number of beds per room ({$maxBeds}) has been reached. Contact your administrator to increase the limit.");
                        }
                    }
                }
                break;
        }

        return $next($request);
    }
}
