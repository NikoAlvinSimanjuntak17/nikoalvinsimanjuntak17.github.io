<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\BookingList;
use App\Models\User;

use App\Jobs\SendEmail;

use DataTables;
use Carbon\Carbon;

class BookingListController extends Controller
{
    public function json(){
        $data = BookingList::with([
            'room', 'user'
        ]);

        return DataTables::of($data)
        ->addIndexColumn()
        ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.admin.booking-list.index');
    }

    public function update($id, $value)
    {
        $item   = BookingList::findOrFail($id);
        $today  = Carbon::today()->toDateString();
        $now    = Carbon::now()->toTimeString();
    
        // Admin details from authenticated user
        $admin_name  = Auth::user()->name;
        $admin_email = Auth::user()->email;
    
        // Set the status based on $value
        if ($value == 1) {
            $data['status'] = 'DISETUJUI';
        } elseif ($value == 0) {
            $data['status'] = 'DITOLAK';
        } else {
            session()->flash('alert-failed', 'Perintah tidak dimengerti');
            return redirect()->route('booking-list.index');
        }
    
        // Check if booking is valid (date and time)
        if ($item['date'] > $today || ($item['date'] == $today && $item['start_time'] > $now)) {
            if ($data['status'] == 'DISETUJUI') {
                // Check for conflicting bookings
                if (
                    BookingList::where([
                        ['date', '=', $item['date']],
                        ['room_id', '=', $item['room_id']],
                        ['status', '=', 'DISETUJUI'],
                    ])
                    ->whereBetween('start_time', [$item['start_time'], $item['end_time']])
                    ->count() <= 0 && 
                    BookingList::where([
                        ['date', '=', $item['date']],
                        ['room_id', '=', $item['room_id']],
                        ['status', '=', 'DISETUJUI'],
                    ])
                    ->whereBetween('end_time', [$item['start_time'], $item['end_time']])
                    ->count() <= 0 &&
                    BookingList::where([
                        ['date', '=', $item['date']],
                        ['room_id', '=', $item['room_id']],
                        ['start_time', '<=', $item['start_time']],
                        ['end_time', '>=', $item['end_time']],
                        ['status', '=', 'DISETUJUI'],
                    ])->count() <= 0
                ) {
                    if ($item->update($data)) {
                        session()->flash('alert-success', 'Booking Ruang '.$item->room->name.' sekarang '.$data['status']);
    
                        // Notify admin about the booking update
                        dispatch(new SendEmail($admin_email, $admin_name, $item->room->name, $item['date'], $item['start_time'], $item['end_time'], $item['purpose'], 'ADMIN', $admin_name, 'https://google.com', $data['status']));
    
                    } else {
                        session()->flash('alert-failed', 'Booking Ruang '.$item->room->name.' gagal diupdate');
                    }
                } else {
                    session()->flash('alert-failed', 'Ruangan '.$item->room->name.' di waktu itu sudah dibooking');
                }   
            } elseif ($data['status'] == 'DITOLAK') {
                if ($item->update($data)) {
                    session()->flash('alert-success', 'Booking Ruang '.$item->room->name.' sekarang '.$data['status']);
    
                    // Notify admin about the rejected booking
                    dispatch(new SendEmail($admin_email, $admin_name, $item->room->name, $item['date'], $item['start_time'], $item['end_time'], $item['purpose'], 'ADMIN', $admin_name, 'https://google.com', $data['status']));
    
                } else {
                    session()->flash('alert-failed', 'Booking Ruang '.$item->room->name.' gagal diupdate');
                }
            }
        } else {
            session()->flash('alert-failed', 'Permintaan booking itu tidak lagi bisa diupdate');
        }
        
        return redirect()->route('booking-list.index');
    }
    
}
