<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class UpdateParkingAvailability implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function handle()
    {
        $expiredReservations = Reservation::where('end_time', '<', now())->get();

        foreach ($expiredReservations as $reservation) {
            $parking = $reservation->parking;
            $parking->increment('available_spaces');
            $reservation->delete();
        }
    }

}
