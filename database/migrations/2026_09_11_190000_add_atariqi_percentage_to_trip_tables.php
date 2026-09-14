<?php

use App\Models\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'suggestions-drivers',
        'sug-day-drivers',
        'sug-week-drivers',
    ];

    public function up(): void
    {
        $percentage = (float) (Subscription::generalDuesPercentage()?->cost ?? 0);

        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->decimal('atariqi_percentage', 8, 2)->nullable()->after('action');
            });

            DB::table($table)
                ->whereNull('atariqi_percentage')
                ->update(['atariqi_percentage' => $percentage]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('atariqi_percentage');
            });
        }
    }
};
