<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentUpdateLog extends Model
{
    public $timestamps = false;

    protected $table = 'document_updates_log';

    protected $fillable = [
        'assigned_from_employee_id',
        'document_id',
        'document_link_old',
        'document_link_new',
        'status',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_from_employee_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}
