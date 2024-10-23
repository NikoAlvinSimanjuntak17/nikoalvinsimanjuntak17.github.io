<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\BookingList;
use DataTables;
use Carbon\Carbon;

class RoomListController extends Controller
{
    public function json()
    {
        $now = Carbon::now(); // Get the current time
        $data = Room::with(['bookings' => function ($query) use ($now) {
            $query->whereDate('date', '=', $now->toDateString())
                  ->where('status', '=', 'DISETUJUI')
                  ->where(function ($query) use ($now) {
                      $query->where(function ($query) use ($now) {
                          $query->where('start_time', '<=', $now->toTimeString())
                                ->where('end_time', '>=', $now->toTimeString());
                      })
                      ->orWhere(function ($query) use ($now) {
                          $query->where('start_time', '>', $now->toTimeString());
                      });
                  });
        }])->get();

        $roomsWithStatus = $data->map(function ($room) use ($now) {
            $bookings = $room->bookings;

            if ($bookings->isEmpty()) {
                // No bookings, room is available
                $room->status = 'AVAILABLE';
            } else {
                // There are bookings
                $currentBooking = $bookings->first();

                if ($currentBooking->start_time <= $now->toTimeString() && $currentBooking->end_time >= $now->toTimeString()) {
                    // Current time is within the booking time
                    $room->status = 'IN USE';
                } else {
                    // Room is booked but not currently in use
                    $room->status = 'BOOKED';
                    $room->booking_time = "{$currentBooking->start_time} - {$currentBooking->end_time}";
                }
            }

            return $room;
        });

        return DataTables::of($roomsWithStatus)
            ->addIndexColumn()
            ->make(true);
    }

    public function index()
    {
        return view('pages.user.room.index');
    }
}
