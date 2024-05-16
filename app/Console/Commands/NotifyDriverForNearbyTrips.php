<?php

namespace App\Console\Commands;

use App\Models\SugDayDriver;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotifyDriverForNearbyTrips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify-driver-nearby-trips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify drivers for nearby trips';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i:s');
        $afterFiveMin = Carbon::now()->addMinutes(5)->format('H:i:s');

        // Daily Drivers
        $dailyDrivers = SugDayDriver::with('driver')
            ->select('driver-id')
            ->whereHas('booking', function ($query) use ($today, $currentTime, $afterFiveMin) {
                $query->whereDate('day-ride-booking.date-of-ser', $today)
                    ->whereBetween('day-ride-booking.time-go', [$currentTime, $afterFiveMin])
                    ->orWhereBetween('day-ride-booking.time-back', [$currentTime, $afterFiveMin]);
            })
            ->get();

        foreach ($dailyDrivers as $dailyDriver) {
            $title = __('Be ready!');
            $message = __('You have a trip almost start.');
            sendNotification(['title' => $title, 'body' => $message, 'tokens' => $dailyDriver->driver->fcm_token]);
        }

        return Command::SUCCESS;
    }
}
