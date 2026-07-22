<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 30)->unique();
            $table->enum('ticket_type', ['inquiry', 'complaint', 'technical']);
            $table->string('customer_name', 150)->nullable();
            $table->string('customer_email', 150);
            $table->string('customer_phone', 30)->nullable();
            $table->string('title', 255);
            $table->text('description');
            $table->string('complaint_type', 100)->nullable();
            $table->string('related_service_reference', 100)->nullable();
            $table->enum('device_type', ['android', 'ios', 'web'])->nullable();
            $table->enum('status', ['new', 'in_progress', 'closed'])->default('new');
            $table->unsignedBigInteger('assigned_employee_id')->nullable()->index();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->enum('uploaded_by', ['customer', 'employee']);
            $table->string('file_path', 255);
            $table->string('file_type', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->enum('sender_type', ['customer', 'employee']);
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->text('message');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('ticket_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30);
            $table->unsignedBigInteger('changed_by_employee_id')->nullable()->index();
            $table->timestamp('created_at')->useCurrent();
        });

        if (Schema::hasTable('support_tickets')) {
            $this->migrateLegacySupportTickets();
            Schema::dropIfExists('support_tickets');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_status_logs');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('tickets');
    }

    private function migrateLegacySupportTickets(): void
    {
        $legacyTickets = DB::table('support_tickets')->orderBy('id')->get();

        foreach ($legacyTickets as $legacy) {
            $status = $legacy->status === 'resolved' ? 'closed' : $legacy->status;
            if (!in_array($status, ['new', 'in_progress', 'closed'], true)) {
                $status = 'new';
            }

            $title = $legacy->subject
                ?: ($legacy->complaint_type ?: 'Support Ticket');

            $deviceType = in_array($legacy->device_type, ['android', 'ios', 'web'], true)
                ? $legacy->device_type
                : null;

            $ticketId = DB::table('tickets')->insertGetId([
                'ticket_number' => $legacy->ticket_number,
                'ticket_type' => $legacy->type,
                'customer_name' => $legacy->name,
                'customer_email' => $legacy->email,
                'customer_phone' => $legacy->phone,
                'title' => $title,
                'description' => $legacy->description,
                'complaint_type' => $legacy->complaint_type,
                'related_service_reference' => $legacy->trip_reference,
                'device_type' => $deviceType,
                'status' => $status,
                'assigned_employee_id' => null,
                'closed_at' => $status === 'closed' ? ($legacy->updated_at ?? now()) : null,
                'created_at' => $legacy->created_at,
                'updated_at' => $legacy->updated_at,
            ]);

            DB::table('ticket_status_logs')->insert([
                'ticket_id' => $ticketId,
                'old_status' => null,
                'new_status' => $status,
                'changed_by_employee_id' => null,
                'created_at' => $legacy->created_at ?? now(),
            ]);

            if (!empty($legacy->admin_response)) {
                DB::table('ticket_replies')->insert([
                    'ticket_id' => $ticketId,
                    'sender_type' => 'employee',
                    'employee_id' => null,
                    'message' => $legacy->admin_response,
                    'created_at' => $legacy->updated_at ?? now(),
                ]);
            }

            $attachments = json_decode($legacy->attachments ?? '[]', true);
            if (is_array($attachments)) {
                foreach ($attachments as $path) {
                    if (!is_string($path) || $path === '') {
                        continue;
                    }

                    DB::table('ticket_attachments')->insert([
                        'ticket_id' => $ticketId,
                        'uploaded_by' => 'customer',
                        'file_path' => $path,
                        'file_type' => pathinfo($path, PATHINFO_EXTENSION) ?: null,
                        'created_at' => $legacy->created_at ?? now(),
                    ]);
                }
            }
        }
    }
};
