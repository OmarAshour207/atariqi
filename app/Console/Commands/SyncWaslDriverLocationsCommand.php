<?php

namespace App\Console\Commands;

use App\Services\WaslService;
use Illuminate\Console\Command;

class SyncWaslDriverLocationsCommand extends Command
{
    protected $signature = 'wasl:sync-driver-locations';

    protected $description = 'Send live driver locations to WASL (service 5.6) for active and on-service drivers.';

    public function handle(WaslService $waslService): int
    {
        if (!config('wasl.enabled')) {
            return self::SUCCESS;
        }

        try {
            $count = $waslService->syncActiveDriverLocations();
            $this->info("Sent {$count} driver location(s) to WASL.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to sync driver locations with WASL: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
