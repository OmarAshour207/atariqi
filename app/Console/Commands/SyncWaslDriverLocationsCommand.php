<?php

namespace App\Console\Commands;

use App\Services\WaslService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncWaslDriverLocationsCommand extends Command
{
    protected $signature = 'wasl:sync-driver-locations
                            {--dry-run : Collect locations and log payload without calling WASL}
                            {--show-details : Print collection details in the console}';

    protected $description = 'Send live driver locations to WASL (service 5.6) for active and on-service drivers.';

    public function handle(WaslService $waslService): int
    {
        if (!config('wasl.enabled')) {
            $this->warn('WASL is disabled (WASL_ENABLED=false).');

            return self::SUCCESS;
        }

        try {
            $diagnostics = $waslService->getLocationSyncDiagnostics();
            $locations = $diagnostics['collected_locations'];
            $count = count($locations);

            Log::channel('wasl')->info('Driver location sync run', [
                'dry_run' => (bool) $this->option('dry-run'),
                'ongoing_driver_ids' => $diagnostics['ongoing_driver_ids'],
                'stored_locations_count' => $diagnostics['stored_locations']->count(),
                'collected_count' => $count,
                'locations' => $locations,
            ]);

            if ($this->option('show-details') || $this->option('dry-run') || $count === 0) {
                $this->printDiagnostics($diagnostics);
            }

            if ($count === 0) {
                $this->warn('No driver locations collected. Nothing sent to WASL.');

                return self::SUCCESS;
            }

            if ($this->option('dry-run')) {
                $this->info("Dry run: would send {$count} driver location(s) to WASL.");

                return self::SUCCESS;
            }

            $waslService->updateCurrentLocations($locations);
            $this->info("Sent {$count} driver location(s) to WASL.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::channel('wasl')->error('Failed to sync driver locations with WASL', [
                'error' => $e->getMessage(),
            ]);

            $this->error('Failed to sync driver locations with WASL: ' . $e->getMessage());

            return self::FAILURE;
        } finally {
            $this->line('Log file: storage/logs/wasl/');
        }
    }

    private function printDiagnostics(array $diagnostics): void
    {
        $ongoingDriverIds = $diagnostics['ongoing_driver_ids'];
        $withLocation = $diagnostics['stored_locations'];
        $locations = $diagnostics['collected_locations'];

        $this->newLine();
        $this->info('Collection diagnostics:');
        $this->line('Ongoing trip driver IDs: ' . (empty($ongoingDriverIds) ? 'none' : implode(', ', $ongoingDriverIds)));
        $this->line('Drivers with stored location: ' . $withLocation->count());

        foreach ($withLocation as $info) {
            $missing = [];
            if (!$info->identity_number) {
                $missing[] = 'identity_number';
            }
            if (!$info->{'sequence-number'}) {
                $missing[] = 'sequence-number';
            }

            $note = $missing ? ' [missing: ' . implode(', ', $missing) . ']' : '';
            $this->line("  - driver {$info->{'driver-id'}}: lat={$info->{'current-lat'}}, lng={$info->{'current-lng'}}{$note}");
        }

        $this->line('Collected for WASL: ' . count($locations));

        if (!empty($locations)) {
            $this->line(json_encode($locations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $this->newLine();
    }
}
