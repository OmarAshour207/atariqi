<?php

namespace App\Console\Commands;

use App\Models\DayRideBooking;
use App\Models\SugDayDriver;
use App\Models\SuggestionDriver;
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

        SuggestionDriver::whereDate('date-of-add', '<', $today)
            ->where('action', 1)
            ->chunk(100, function ($trips) {
                foreach ($trips as $trip) {
                    $trip->update([
                        'action' => 4
                    ]);
                }
        });

        SugDayDriver::whereHas('booking', function ($query) use ($today) {
                $query->whereDate('day-ride-booking.date-of-ser', '<', $today);
            })
            ->whereIn('action', [0, 1])
            ->chunk(100, function ($trips) {
                foreach ($trips as $trip) {
                    $trip->update([
                        'action' => 2
                    ]);
                }
            });

        DayRideBooking::whereDate('date-of-ser', '<', $today)
            ->whereIn('action', [0, 4])
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
            ->whereIn('action', [0, 1])
            ->chunk(100, function ($trips) {
                foreach ($trips as $trip) {
                    $trip->update([
                        'action' => 2
                    ]);
                }
            });

        $weekRides = WeekRideBooking::whereDate('date-of-ser', '<', $today)
            ->whereIn('action', [0, 4])
            ->get();

        foreach ($weekRides as $weekRide) {
            WeekRideBooking::where('group-id', $weekRide->{"group-id"})->chunk(10, function ($rides) {
                foreach ($rides as $ride) {
                    $ride->update([
                        'action' => 2
                    ]);
                }
            });
        }
//            ->chunk(100, function ($rides) {
//                foreach ($rides as $ride) {
//                    $ride->update([
//                        'action' => 3
//                    ]);
//                }
//            });

        return Command::SUCCESS;
    }
}
