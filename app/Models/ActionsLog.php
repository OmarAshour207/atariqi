<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionsLog extends Model
{
    public $timestamps = false;

    protected $table = 'actions_log';

    protected $fillable = [
        'action_type',
        'table_raw',
        'old_data',
        'reason',
        'decided_by_employee_id',
        'created_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'decided_by_employee_id');
    }
}
