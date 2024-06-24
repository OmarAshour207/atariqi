<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver-id',
        'type',
        'amount'
    ];

    // Relations
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver-id');
    }
}
