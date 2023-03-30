<?php

namespace App\Http\Controllers;
use App\Models\room;
use App\Models\booking;
use App\Models\guest;
use App\Models\package;
use App\Http\Requests\StoreroomRequest;
use App\Http\Requests\UpdateroomRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index()
    {
        return response()->json([
            //getting all records
            DB::table('rooms')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreroomRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateroomRequest $request, room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(room $room)
    {
        //
    }

    //display available rooms
    public function availableRooms()
    {
        return response()->json([
            //getting available rooms
           DB::table('rooms')->where('status', 'Available')->get()
        ]);
    }

        //display booked rooms with guest name, nic, and phone number
        public function roomDetailsWithGuestDetails(){
            $bookedRooms = DB::table('rooms') -> where ('status', 'Booked')->get('id');
                return response()-> json([
                    // DB::table('bookings')
                    // ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                    // ->join('guests', 'bookings.guest_id', '=', 'guests.id')
                    // ->join ('packages', 'bookings.package_type', '=', 'packages.package_type')
                    // ->select('rooms.id','rooms.status','rooms.suite','guests.name', 'bookings.check_in','bookings.check_out', 'packages.stay_type','guests.contact', 'guests.nic')
                    // ->get()
                    DB::table('bookings')
                        ->whereExists(function (Builder $query) {
                        $query->select(DB::raw(1))
                        ->from('rooms')
                        ->whereColumn('rooms.id', 'bookings.room_id');
                    })
                    ->get()
                ]);
        }       
}
