<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Models\UserPackage;
use App\Models\UserPackageHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckFinishedSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-finished-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check finished subscriptions and update';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        $freePackage = Package::where('status', Package::FREE)->first();

        UserPackage::where('end_date', '<', $today)
            ->where('status', UserPackage::STATUS_ACTIVE)
            ->chunk(100, function ($userPackages) use ($freePackage) {
                foreach ($userPackages as $userPackage) {
                    UserPackageHistory::create([
                        'user_id' => $userPackage->user_id,
                        'package_id' => $userPackage->package_id,
                        'status' => UserPackage::STATUS_EXPIRED,
                        'start_date' => $userPackage->start_date,
                        'end_date' => $userPackage->end_date,
                        'canceled_date' => now(),
                        'interval' => $userPackage->interval,
                    ]);

                    UserPackage::create([
                        'user_id' => $userPackage->user_id,
                        'package_id' => $freePackage->id,
                        'status' => UserPackage::STATUS_ACTIVE,
                        'start_date' => now(),
                        'end_date' => now()->addYear(),
                        'interval' => 'yearly',
                    ]);

                    $userPackage->delete();
                }
            });

        return Command::SUCCESS;
    }
}
