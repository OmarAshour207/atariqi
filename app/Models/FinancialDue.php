<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialDue extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'financial-dues';

    protected $fillable = [
        'driver-id',
        'amount'
    ];

    protected $casts = [
        'amount' => 'double'
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }
}
