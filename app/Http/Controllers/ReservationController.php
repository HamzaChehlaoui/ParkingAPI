<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Store a newly created reservation.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the inputs
        $request->validate([
            'parking_id' => 'required|exists:parkings,id',
            'start_time' => 'required|date|after_or_equal:today', // Ensure start time is today or in the future
            'end_time' => 'required|date|after:start_time', // Ensure end time is after start time
        ]);

        $parking = Parking::find($request->parking_id);

        // Check if there are available spaces
        if ($parking->available_spaces <= 0) {
            return response()->json(['message' => 'No available spaces'], 400);
        }

        // Check if there's any existing reservation overlapping the requested time
        $existingReservation = Reservation::where('parking_id', $request->parking_id)
                                          ->where('start_time', '<', $request->end_time)
                                          ->where('end_time', '>', $request->start_time)
                                          ->first();

        if ($existingReservation) {
            return response()->json(['message' => 'Parking space already reserved for the selected time'], 400);
        }

        // Create the reservation
        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'parking_id' => $request->parking_id,
            'start_time' => Carbon::parse($request->start_time),
            'end_time' => Carbon::parse($request->end_time),
        ]);

        // Decrease the available spaces in the parking
        $parking->decrement('available_spaces');

        return response()->json($reservation, 201); // Return the created reservation
    }
}
