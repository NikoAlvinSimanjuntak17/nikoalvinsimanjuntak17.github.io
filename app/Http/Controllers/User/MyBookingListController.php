<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\BookingList;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\User;

use App\Jobs\SendEmail;

use App\Http\Requests\User\MyBookingListRequest;

use DataTables;

class MyBookingListController extends Controller
{
    public function json(){
        $data = BookingList::with([
            'room'
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
        return view('pages.user.my-booking-list.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rooms = Room::orderBy('name')->get();

        return view('pages.user.my-booking-list.create', [
            'rooms' => $rooms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MyBookingListRequest $request)
    {
        $data               = $request->all();
        $data['status']     = 'PENDING';
    
        $room               = Room::select('name')->where('id', $data['room_id'])->firstOrFail();
    
        if(
            BookingList::where([
                ['date', '=', $data['date']],
                ['room_id', '=', $data['room_id']],
                ['status', '=', 'DISETUJUI'],
            ])
            ->whereBetween('start_time', [$data['start_time'], $data['end_time']])
            ->count() <= 0 || 
            BookingList::where([
                ['date', '=', $data['date']],
                ['room_id', '=', $data['room_id']],
                ['status', '=', 'DISETUJUI'],
            ])
            ->whereBetween('end_time', [$data['start_time'], $data['end_time']])
            ->count() <= 0 ||
            BookingList::where([
                ['date', '=', $data['date']],
                ['room_id', '=', $data['room_id']],
                ['start_time', '<=', $data['start_time']],
                ['end_time', '>=', $data['end_time']],
                ['status', '=', 'DISETUJUI'],
            ])->count() <= 0
        ) {
            if(BookingList::create($data)) {
                $request->session()->flash('alert-success', 'Booking ruang '.$room->name.' berhasil ditambahkan');
                
                $admin  = $this->getAdminData(); // Get admin data
                $status = 'DIBUAT';
    
                // Notify the admin about the booking
                dispatch(new SendEmail($admin->email, $admin->name, $room->name, $data['date'], $data['start_time'], $data['end_time'], $data['purpose'], 'ADMIN', $admin->name, 'https://google.com', $status));
                
            } else {
                $request->session()->flash('alert-failed', 'Booking ruang '.$room->name.' gagal ditambahkan');
                return redirect()->route('my-booking-list.create');
            }
        } else {
            $request->session()->flash('alert-failed', 'Ruangan '.$room->name.' di waktu itu sudah dibooking');
            return redirect()->route('my-booking-list.create');
        }
    
        return redirect()->route('my-booking-list.index');
    }
    

    /**
     * Cancel the specified data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $item           = BookingList::findOrFail($id);
        $data['status'] = 'BATAL';
    
        $room = Room::select('name')->where('id', $item->room_id)->firstOrFail();
    
        if ($item->update($data)) {
            session()->flash('alert-success', 'Booking Ruang '.$room->name.' berhasil dibatalkan');
    
            $admin  = $this->getAdminData(); // Get the admin details
            $status = $data['status'];
    
            // Admin notification
            dispatch(new SendEmail($admin->email, $admin->name, $room->name, $item->date, $item->start_time, $item->end_time, $item->purpose, 'ADMIN', $admin->name, 'https://google.com', $status));
    
        } else {
            session()->flash('alert-failed', 'Booking Ruang '.$room->name.' gagal dibatalkan');
        }
    
        return redirect()->route('my-booking-list.index');
    }
    

    public function getAdminData() {
        return User::select('name','email')->where('role', 'ADMIN')->firstOrFail();
    }

}
