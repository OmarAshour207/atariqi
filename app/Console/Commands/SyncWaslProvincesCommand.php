<?php

namespace App\Console\Commands;

use App\Services\WaslService;
use Illuminate\Console\Command;

class SyncWaslProvincesCommand extends Command
{
    protected $signature = 'wasl:sync-provinces';

    protected $description = 'Fetch WASL province IDs (service 5.7) and store them locally for trip registration.';

    public function handle(WaslService $waslService): int
    {
        if (!config('wasl.enabled')) {
            $this->warn('WASL integration is disabled (WASL_ENABLED=false).');

            return self::FAILURE;
        }

        try {
            $count = $waslService->syncProvinces();
            $this->info("Synced {$count} WASL provinces successfully.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to sync WASL provinces: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
