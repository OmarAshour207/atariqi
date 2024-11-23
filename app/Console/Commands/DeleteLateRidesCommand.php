<?php

namespace App\Console\Commands;

use App\Models\DayRideBooking;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use App\Models\WeekRideBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteLateRidesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-late-trips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get late trips and change their action to rejected from driver side';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        SugDayDriver::whereHas('booking', function ($query) use ($today) {
                $query->whereDate('day-ride-booking.date-of-ser', '<', $today);
            })
            ->where('action', 0)
            ->chunk(100, function ($trips) {
                foreach ($trips as $trip) {
                    $trip->update([
                        'action' => 2
                    ]);
                }
            });

        DayRideBooking::whereDate('date-of-ser', '<', $today)
            ->where('action', 0)
            ->chunk(100, function ($rides) {
                foreach ($rides as $ride) {
                    $ride->update([
                        'action' => 3
                    ]);
                }
            });

        SugWeekDriver::whereHas('booking', function ($query) use ($today) {
            $query->whereDate('week-ride-booking.date-of-ser', '<', $today);
        })
            ->where('action', 0)
            ->chunk(100, function ($trips) {
                foreach ($trips as $trip) {
                    $trip->update([
                        'action' => 2
                    ]);
                }
            });

        WeekRideBooking::whereDate('date-of-ser', '<', $today)
            ->where('action', 0)
            ->chunk(100, function ($rides) {
                foreach ($rides as $ride) {
                    $ride->update([
                        'action' => 3
                    ]);
                }
            });

        return Command::SUCCESS;
    }
}
