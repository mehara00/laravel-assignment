<?php

namespace App\Http\Controllers;

use App\Models\booking;
use App\Http\Requests\StorebookingRequest;
use App\Http\Requests\UpdatebookingRequest;
use App\Models\room;
use App\Models\guest;
use App\Models\package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{

    //getting all booking records
    public function index()
    {
        return response()->json([
            DB::table('bookings')->get()
        ]);
    }

    //Show the form for creating a new resource
    public function create()
    {
        //
    }

    //Store a newly created resource in storage.
    public function store(StorebookingRequest $request)
    {
        $name = $request -> input ('name');
        $contact = $request -> input ('contact');
        $nic = $request -> input ('nic');
        $check_in = $request -> input ('check_in');
        $check_out = $request -> input ('check_out');
        $start_date = \Carbon\Carbon::parse($check_in);
        $end_date = \Carbon\Carbon::parse($check_out);
        $stay_type = $request -> input ('stay_type');
        $suite = $request -> input ('suite');
        $room_id = $request -> input ('room_id');
        $paymentDetail_id = $request -> input ('paymentDetail_id');
        $nights = $end_date -> diffInDays ($start_date);
        
        //$tax= 0.1 * $subtotal;
        //$total = $tax + $subtotal;

        DB::transaction(function () use ($name, $contact, $nic, $check_in, $check_out, $stay_type, $suite, $room_id) {
            if($suite == 'Standard'){
                if ($stay_type == 'FB'){
                    $package_type =1;
                }else if( $stay_type == 'BB'){
                    $package_type = 2;
                }
            }else if ($suite == 'Deluxe'){
                if ($stay_type == 'FB'){
                    $package_type =3;
                }else if( $stay_type == 'BB'){
                    $package_type = 4;
                }   
            }

            //declared variables for the second time
            $nights = $end_date -> diffInDays ($start_date);
            $start_date = \Carbon\Carbon::parse($check_in);
            $end_date = \Carbon\Carbon::parse($check_out);

            if($package_type==1){
                $subtotal = $nights*25000;
            }else if ($package_type==2){
                $subtotal = $nights*15000;
            }else if ($package_type==3){
                $subtotal = $nights*40000;
            }else if ($package_type==4){
                $subtotal = $nights*25000;
            }

            //declared variables for the second time
            $tax= 0.1 * $subtotal;
            $total = $tax + $subtotal;
            
            $id = DB::table('guests')->insertGetId([
                'name' => $name,
                'contact' => $contact,
                'nic' => $nic
            ]);

            $booking_id = DB::table('bookings')->insert([
                "guest_id" => $id,
                "room_id" => $room_id,
                "package_type" => $package_type,
                "check_in" => $check_in,
                "check_out" => $check_out,
            ]);

            DB:: table ('payment_details') -> insertId([
                'package_type' => $package_type,
                'booking_id' => $booking_id,
                'subtotal' => $subtotal,
                'tax' => 0.1*$subtotal,
                'total' => $total
            ]);

        });
    }

    //Display the specified resource.
    public function show(booking $booking)
    {
        //
    }

    //Show the form for editing the specified resource
    public function edit(booking $booking)
    {
        //
    }

    //Update the specified resource in storage
    public function update(UpdatebookingRequest $request, booking $booking)
    {
        //
    }

    //Remove the specified resource from storage
    public function destroy(booking $booking)
    {
        //
    }

    //display all booked rooms with guest name, nic, and phone number
    public function bookedRoomDetails(){
        return response() -> json ([
                        DB::table('bookings')
                        ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                        ->join('guests', 'bookings.guest_id', '=', 'guests.id')
                        ->join ('packages', 'bookings.package_type', '=', 'packages.package_type')
                        ->select('rooms.id','rooms.status','rooms.suite','guests.name', 'bookings.check_in','bookings.check_out', 'packages.stay_type','guests.contact', 'guests.nic')
                        ->where('bookings.check_out' , '>', now())
                        ->get()  
        ]);
    }

    //display details of available rooms
    public function availableRoomDetails(){
        return response() -> json ([
            DB::table('bookings')
                ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                ->leftJoin('guests', 'bookings.guest_id', '=', 'guests.id')
                ->leftJoin('packages', 'bookings.package_type', '=', 'packages.package_type')
                ->select(
                    'rooms.id',
                    'rooms.status',
                    'rooms.suite',
                    DB::raw("CASE WHEN bookings.check_out < NOW() THEN NULL ELSE guests.name END AS name"),
                    DB::raw("CASE WHEN rooms.status = 'Available' THEN NULL ELSE bookings.check_in END AS check_in"),
                    DB::raw("CASE WHEN bookings.check_out < NOW() THEN NULL ELSE bookings.check_out END AS check_out"),
                    DB::raw("CASE WHEN rooms.status = 'Available' THEN NULL ELSE packages.stay_type END AS stay_type"),
                    DB::raw("CASE WHEN bookings.check_out < NOW() THEN NULL ELSE guests.contact END AS contact"),
                    DB::raw("CASE WHEN bookings.check_out < NOW() THEN NULL ELSE guests.nic END AS nic")
                )
                ->where('rooms.status', 'Available')
                ->get()  
        ]);
    }
    
    //display current room details
    public function currentStatus(){
        $bookedRooms = $this->bookedRoomDetails();
        $availableRooms = $this->availableRoomDetails();
        return response() -> json([
        'booked' => $bookedRooms -> original,
        'available' => $availableRooms -> original,
        ]);
    }      
}