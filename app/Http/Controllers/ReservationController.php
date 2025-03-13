<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'parking_id' => 'required|exists:parkings,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        $parking = Parking::find($request->parking_id);

        if ($parking->available_spaces <= 0) {
            return response()->json(['message' => 'No available places in this parking.'], 400);
        }

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'parking_id' => $request->parking_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        $parking->decrement('available_spaces');

        return response()->json([
            'message' => 'Reservation successful',
            'reservation' => $reservation
        ], 201);
    }

    public function update(Request $request, $id)
{
    $reservation = Reservation::find($id);
    if (!$reservation) {
        return response()->json(['message' => 'Reservation not found'], 404);
    }

    $start_time = $request->input('start_time');
    $end_time = $request->input('end_time');

    $reservation->start_time = $start_time;
    $reservation->end_time = $end_time;
    $reservation->save();

    return response()->json([
        'message' => 'Reservation updated successfully',
        'reservation' => $reservation
    ]);
}

    public function destroy($id)
{
    $reservation = Reservation::find($id);
    if (!$reservation) {
        return response()->json(['message' => 'Reservation not found'], 404);
    }

    $reservation->delete();

    return response()->json(['message' => 'Reservation cancelled successfully']);
}

public function getUserReservations($userId)
{
    $reservations = Reservation::where('user_id', $userId)
                                ->orderBy('start_time', 'desc')
                                ->get();

    return response()->json([
        'reservations' => $reservations
    ]);
}



}
