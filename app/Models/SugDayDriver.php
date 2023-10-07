<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SugDayDriver extends Model
{
    use HasFactory;

    protected $table = 'sug-day-drivers';

    public $timestamps = false;

    protected $fillable = [
        'booking-id',
        'driver-id',
        'passenger-id',
        'action',
        'date-of-add',
        'date-of-edit',
        'viewed'
    ];

    // relations
    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger-id');
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver-id');
    }

    public function driverinfo()
    {
        return $this->hasOne(DriverInfo::class, 'driver-id', 'driver-id');
    }
    public function booking()
    {
        return $this->belongsTo(DayRideBooking::class, 'booking-id');
    }
}
