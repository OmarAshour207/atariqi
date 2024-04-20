<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerRate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'passenger-rate';

    protected $fillable = [
        'user-id',
        'rate'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user-id');
    }
}
