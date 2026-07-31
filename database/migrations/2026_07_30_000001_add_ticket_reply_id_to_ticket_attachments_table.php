<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->foreignId('ticket_reply_id')
                ->nullable()
                ->after('ticket_id')
                ->constrained('ticket_replies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ticket_reply_id');
        });
    }
};
