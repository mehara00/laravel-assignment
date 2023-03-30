<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PackageController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/rooms',[RoomController::class,'index']);
Route::get('/guests', [GuestController::class, 'index']);
Route::get('/bookings', [BookingController::class, 'index']);
Route::get('/charges', [CharegesController::class, 'index']);
Route::get('/available', [RoomController::class, 'availableRooms']);
Route::get('/roomsAndGuests', [RoomController::class, 'roomDetailsWithGuestDetails']);
Route::get('/details', [RoomController::class, 'Details']);