<?php

namespace App\Services;

use App\Models\ActionsLog;

class ActionsLogService
{
    public function logAdd(string $table, int|string $id, ?array $payload = null): void
    {
        $this->write('add', "{$table}:{$id}", null, null, $payload);
    }

    public function logEdit(string $table, int|string $id, ?array $oldData = null): void
    {
        $this->write('edit', "{$table}:{$id}", $oldData, null, null);
    }

    public function logDelete(string $table, int|string $id, array $oldData, string $reason): void
    {
        $this->write('delete', "{$table}:{$id}", $oldData, $reason, null);
    }

    private function write(
        string $actionType,
        string $tableRaw,
        ?array $oldData,
        ?string $reason,
        ?array $payload
    ): void {
        ActionsLog::create([
            'action_type' => $actionType,
            'table_raw' => $tableRaw,
            'old_data' => $payload ?? $oldData,
            'reason' => $reason,
            'decided_by_employee_id' => auth()->guard('admin')->id(),
            'created_at' => now(),
        ]);
    }
}
