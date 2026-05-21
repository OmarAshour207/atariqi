<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverBanned extends Model
{
    use HasFactory;

    protected $table = 'drivers-banned';

    protected $fillable = [
        'driver-id',
        'reason',
        'banned-by',
    ];


    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }
}
