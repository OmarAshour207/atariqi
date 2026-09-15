<?php

use App\Models\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'suggestions-drivers' => 'ride-booking',
        'sug-day-drivers' => 'day-ride-booking',
        'sug-week-drivers' => 'week-ride-booking',
    ];

    public function up(): void
    {
        $percentage = (float) (Subscription::generalDuesPercentage()?->cost ?? 0);

        foreach ($this->tables as $tripTable => $bookingTable) {
            if (!Schema::hasColumn($tripTable, 'trip_cost')) {
                Schema::table($tripTable, function (Blueprint $blueprint) {
                    $blueprint->decimal('trip_cost', 10, 2)->nullable()->after('atariqi_percentage');
                });
            }

            if (Schema::hasColumn($tripTable, 'atariqi_percentage')) {
                DB::table($tripTable)
                    ->whereNull('atariqi_percentage')
                    ->update(['atariqi_percentage' => $percentage]);
            }

            // Backfill trip_cost from the service cost linked through the booking.
            DB::statement("
                UPDATE `{$tripTable}` AS t
                INNER JOIN `{$bookingTable}` AS b ON b.id = t.`booking-id`
                INNER JOIN `services` AS s ON s.id = b.`service-id`
                SET t.trip_cost = s.cost
                WHERE t.trip_cost IS NULL
            ");
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->tables) as $tripTable) {
            if (Schema::hasColumn($tripTable, 'trip_cost')) {
                Schema::table($tripTable, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('trip_cost');
                });
            }
        }
    }
};
