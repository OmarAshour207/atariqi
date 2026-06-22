<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\WaslService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDriverWaslEligibilityCommand extends Command
{
    protected $signature = 'drivers:check-wasl-eligibility';

    protected $description = 'Check approved drivers eligibility with the ministry (WASL) and update approval status.';

    public function handle(WaslService $waslService): int
    {
        $drivers = User::with(['driverInfo', 'callingKey'])
            ->where('user-type', 'driver')
            ->whereIn('approval', [0, 1, 4])
            ->whereHas('driverInfo', function ($query) {
                $query->whereNotNull('identity_number');
            })
            ->get();

        $updated = 0;

        foreach ($drivers as $driver) {
            try {
                $waslService->applyDailyEligibilityToDriver($driver);
                $updated++;
            } catch (\Throwable $e) {
                Log::channel('wasl')->error('Daily WASL eligibility check failed', [
                    'driver_id' => $driver->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Driver {$driver->id}: {$e->getMessage()}");
            }
        }

        $this->info("Processed {$updated} driver(s) for WASL eligibility.");

        return self::SUCCESS;
    }
}
