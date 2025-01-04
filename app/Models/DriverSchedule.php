<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverSchedule extends Model
{
    use HasFactory;

    protected $table = 'drivers-schedule';

    public $timestamps = false;

    protected $fillable = [
        'driver-id',
        'Saturday-to',
        'Saturday-from',
        'Sunday-to',
        'Sunday-from',
        'Monday-to',
        'Monday-from',
        'Tuesday-to',
        'Tuesday-from',
        'Wednesday-to',
        'Wednesday-from',
        'Thursday-to',
        'Thursday-from',
        'Friday-to',
        'Friday-from',
        'date-of-add',
        'date-of-edit'
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }
}
